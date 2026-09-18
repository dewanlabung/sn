/**
 * sockets -> nodejs -> libs -> traits -> calls
 *
 * @package Sngine
 * @author Zamblek
 */

const crypto = require('crypto');
const { query, secure } = require('../core/database');
const { get_picture } = require('../functions');
const { __ } = require('../core/i18n');
const { init_system_datetime } = require('../core/datetime');

/**
 * get_hash_token
 * @return {string}
 */
function get_hash_token() {
  return crypto.randomBytes(24).toString('hex');
}

module.exports = {
  /**
   * create_call
   * @param {string} type  'audio' | 'video'
   * @param {number} to_user_id
   * @return {Promise<Object|string|false>}
   */
  async create_call(type, to_user_id) {
    const date = init_system_datetime();
    let is_video_call;
    switch (type) {
      case 'audio':
        is_video_call = 0;
        if (this.system.audio_call_enabled != 1) {
          throw new Error(__('The audio call feature has been disabled by the admin'));
        }
        if (!this._data.can_start_audio_call) {
          throw new Error(__("You don't have the permission to do this"));
        }
        break;
      case 'video':
        is_video_call = 1;
        if (this.system.video_call_enabled != 1) {
          throw new Error(__('The video call feature has been disabled by the admin'));
        }
        if (!this._data.can_start_video_call) {
          throw new Error(__("You don't have the permission to do this"));
        }
        break;
      default:
        throw new Error(__('Bad Request'));
    }
    /* check if target user exist */
    const check_target = await query(
      `SELECT COUNT(*) as count FROM users WHERE user_id = ${secure(to_user_id, 'int')}`
    );
    if (check_target.rows[0].count == 0) return false;
    /* check blocking */
    if (await this.blocked(to_user_id)) return false;
    /* check recipients chat privacy */
    this.check_recipients_chat_privacy([await this.get_user(to_user_id)]);
    /* check if target offline */
    const target_offline = await query(
      `SELECT COUNT(*) as count FROM users WHERE user_id = ${secure(to_user_id, 'int')} AND user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(${secure(this.system.offline_time, 'int', false)}))`
    );
    if (target_offline.rows[0].count == 0) return 'recipient_offline';
    /* check if target busy */
    const target_busy = await query(
      `SELECT COUNT(*) as count FROM conversations_calls WHERE (from_user_id = ${secure(to_user_id, 'int')} OR to_user_id = ${secure(to_user_id, 'int')}) AND declined = "0" AND updated_time >= SUBTIME(NOW(), SEC_TO_TIME(40))`
    );
    if (target_busy.rows[0].count > 0) return 'recipient_busy';
    /* check if viewer busy */
    const viewer_busy = await query(
      `SELECT COUNT(*) as count FROM conversations_calls WHERE (from_user_id = ${secure(this._data.user_id, 'int')} OR to_user_id = ${secure(this._data.user_id, 'int')}) AND declined = "0" AND updated_time >= SUBTIME(NOW(), SEC_TO_TIME(40))`
    );
    if (viewer_busy.rows[0].count > 0) return 'caller_busy';
    /* check paid call (wallet balance) */
    if (
      this.system.monetization_enabled == 1 &&
      (await this.check_user_permission(to_user_id, 'monetization_permission'))
    ) {
      const to_user_info = await this.get_user(to_user_id);
      if (to_user_info.user_monetization_enabled == 1 && to_user_info.user_monetization_call_price > 0) {
        if (Number(this._data.user_wallet_balance) < Number(to_user_info.user_monetization_call_price)) {
          throw new Error(__('There is not enough credit in your wallet. Recharge your wallet to continue.'));
        }
      }
    }
    /* create a room + provider tokens */
    const room = get_hash_token();
    const caller_token = this.generate_call_token(room, this._data.user_id, true);
    const receiver_token = this.generate_call_token(room, to_user_id, false);
    /* insert the call */
    const insert = await query(
      `INSERT INTO conversations_calls (is_video_call, from_user_id, from_user_token, to_user_id, to_user_token, room, created_time, updated_time) VALUES (${secure(is_video_call)}, ${secure(this._data.user_id, 'int')}, ${secure(caller_token)}, ${secure(to_user_id, 'int')}, ${secure(receiver_token)}, ${secure(room)}, ${secure(date)}, ${secure(date)})`
    );
    return {
      call_id: insert.insertId,
      type,
      is_video: type === 'video',
      is_audio: type === 'audio',
      caller_name: this._data.user_fullname,
      caller_picture: this._data.user_picture,
    };
  },

  /**
   * generate_call_token
   *
   * Loads the configured provider's SDK on demand. Returns '' when the SDK is
   * not installed (call signalling still works; media connect needs the SDK).
   *
   * @param {string} room
   * @param {number} identity_user_id
   * @param {boolean} is_caller
   * @return {string}
   */
  generate_call_token(room, identity_user_id, is_caller) {
    const provider = this.system.audio_video_provider;
    try {
      if (provider === 'livekit') {
        const { AccessToken } = require('livekit-server-sdk');
        const at = new AccessToken(this.system.livekit_api_key, this.system.livekit_api_secret, {
          identity: String(identity_user_id),
        });
        at.addGrant({ roomJoin: true, room });
        return at.toJwt();
      }
      // Twilio & Agora token builders can be wired here the same way.
    } catch (e) {
      console.log(`⚠️ Call token provider "${provider}" SDK unavailable: ${e.message}`);
    }
    return '';
  },

  /**
   * decline_call
   * @param {number} call_id
   * @return {Promise<Object>}
   */
  async decline_call(call_id) {
    const date = init_system_datetime();
    const check_call = await query(
      `SELECT * FROM conversations_calls WHERE call_id = ${secure(call_id, 'int')} AND (from_user_id = ${secure(this._data.user_id, 'int')} OR to_user_id = ${secure(this._data.user_id, 'int')})`
    );
    if (check_call.num_rows === 0) throw new Error(__('You are not authorized to do this'));
    const call = check_call.rows[0];
    await query(
      `UPDATE conversations_calls SET declined = '1', updated_time = ${secure(date)} WHERE call_id = ${secure(call_id, 'int')}`
    );
    return call;
  },

  /**
   * answer_call
   * @param {number} call_id
   * @return {Promise<Object>}
   */
  async answer_call(call_id) {
    const date = init_system_datetime();
    const check_call = await query(
      `SELECT * FROM conversations_calls WHERE call_id = ${secure(call_id, 'int')} AND to_user_id = ${secure(this._data.user_id, 'int')}`
    );
    if (check_call.num_rows === 0) throw new Error(__('You are not authorized to do this'));
    const call = check_call.rows[0];
    /* paid call */
    if (
      this._data.can_monetize_content &&
      this._data.user_monetization_enabled == 1 &&
      this._data.user_monetization_call_price > 0
    ) {
      throw new Error(
        __('Paid calls are not supported on the NodeJS socket server yet. Use the PHP socket server for monetized calls.')
      );
    }
    await query(
      `UPDATE conversations_calls SET answered = '1', updated_time = ${secure(date)} WHERE call_id = ${secure(call_id, 'int')}`
    );
    return call;
  },

  /**
   * update_call
   * @param {number} call_id
   * @return {Promise<void>}
   */
  async update_call(call_id) {
    const date = init_system_datetime();
    const check_call = await query(
      `SELECT COUNT(*) as count FROM conversations_calls WHERE call_id = ${secure(call_id, 'int')} AND (from_user_id = ${secure(this._data.user_id, 'int')} OR to_user_id = ${secure(this._data.user_id, 'int')})`
    );
    if (check_call.rows[0].count == 0) throw new Error(__('You are not authorized to do this'));
    await query(
      `UPDATE conversations_calls SET updated_time = ${secure(date)} WHERE call_id = ${secure(call_id, 'int')}`
    );
  },

  /**
   * get_call_caller_picture (helper used when broadcasting)
   * @param {string} picture
   * @param {string} gender
   * @return {string}
   */
  _caller_picture(picture, gender) {
    return get_picture(picture, gender, this.system);
  },
};
