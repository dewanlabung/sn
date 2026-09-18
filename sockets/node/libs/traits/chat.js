/**
 * sockets -> nodejs -> libs -> traits -> chat
 *
 * @package Sngine
 * @author Zamblek
 */

const { query, secure, is_empty } = require('../core/database');
const { get_picture, html_entity_decode, is_valid_upload_source, get_user_age } = require('../functions');
const { __ } = require('../core/i18n');
const { init_system_datetime } = require('../core/datetime');
const {
  BadRequestException,
  ValidationException,
  AuthorizationException,
  PrivacyException,
} = require('../core/exceptions');

/**
 * nl2br
 * @param {string} text
 * @return {string}
 */
function nl2br(text) {
  return String(text === null || text === undefined ? '' : text).replace(/\r\n|\n\r|\n|\r/g, '<br />$&');
}

/**
 * popover
 * Minimal port — the profile name anchor used in conversation.name_html.
 * @param {number} user_id
 * @param {string} user_name
 * @param {string} fullname
 * @return {string}
 */
function popover(user_id, user_name, fullname) {
  return `<a href="#" data-uid="${user_id}" data-hovercard="${user_name}">${html_entity_decode(fullname)}</a>`;
}

module.exports = {
  /**
   * user_online
   * @param {number} user_id
   * @return {Promise<boolean>}
   */
  async user_online(user_id) {
    if (!this.friendship_approved(user_id)) return false;
    const get_user_status = await query(
      `SELECT COUNT(*) as count FROM users WHERE user_id = ${secure(user_id, 'int')} AND user_chat_enabled = '1' AND user_privacy_chat != 'me' AND user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(${secure(this.system.offline_time, 'int', false)}))`
    );
    return get_user_status.rows[0].count != 0;
  },

  /**
   * get_conversation
   * @param {number} conversation_id
   * @return {Promise<Object|false>}
   */
  async get_conversation(conversation_id) {
    const get_conversation = await query(
      `SELECT conversations.*, conversations_users.seen FROM conversations INNER JOIN conversations_users ON conversations.conversation_id = conversations_users.conversation_id WHERE conversations_users.user_id = ${secure(this._data.user_id, 'int')} AND conversations.conversation_id = ${secure(conversation_id, 'int')}`
    );
    if (get_conversation.num_rows === 0) return false;
    const conversation = get_conversation.rows[0];

    /* get recipients */
    const get_recipients = await query(
      `SELECT conversations_users.seen, conversations_users.deleted, conversations_users.typing, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified, users.user_monetization_enabled, users.user_monetization_chat_price, users.user_monetization_call_price, users.user_privacy_chat, users.user_last_seen FROM conversations_users INNER JOIN users ON conversations_users.user_id = users.user_id WHERE conversations_users.conversation_id = ${secure(conversation.conversation_id, 'int')} AND conversations_users.user_id != ${secure(this._data.user_id, 'int')}`
    );
    const recipients = get_recipients.rows;
    const recipients_num = recipients.length;
    if (!conversation.node_id && recipients_num === 0) return false;

    conversation.recipients = [];
    conversation.chat_price = 0;
    conversation.call_price = 0;
    conversation.paid_recipients = [];
    conversation.name_list = conversation.name_list || '';
    conversation.typing_name_list = conversation.typing_name_list || '';
    conversation.seen_name_list = conversation.seen_name_list || '';

    let i = 1;
    for (const recipient of recipients) {
      /* check if paid chat */
      if (
        this.system.monetization_enabled == 1 &&
        (await this.check_user_permission(recipient.user_id, 'monetization_permission')) &&
        recipient.user_monetization_enabled == 1 &&
        (recipient.user_monetization_chat_price > 0 || recipient.user_monetization_call_price > 0)
      ) {
        conversation.chat_price += Number(recipient.user_monetization_chat_price);
        conversation.call_price += Number(recipient.user_monetization_call_price);
        conversation.paid_recipients.push(recipient);
      }
      /* get recipient picture */
      recipient.user_picture = get_picture(recipient.user_picture, recipient.user_gender, this.system);
      conversation.recipients.push(recipient);

      /* typing recipients */
      if (this.system.chat_typing_enabled == 1 && recipient.typing == 1) {
        const status = await query(
          `SELECT COUNT(*) as count FROM users WHERE user_id = ${secure(recipient.user_id, 'int')} AND user_last_seen >= SUBTIME(NOW(), SEC_TO_TIME(${secure(this.system.offline_time, 'int', false)}))`
        );
        if (status.rows[0].count == 0) {
          await query(
            `UPDATE conversations_users SET typing = '0' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(recipient.user_id, 'int')}`
          );
        } else {
          if (conversation.typing_name_list) conversation.typing_name_list += ', ';
          conversation.typing_name_list += html_entity_decode(
            this.system.show_usernames_enabled == 1 ? recipient.user_name : recipient.user_firstname
          );
        }
      }
      /* seen recipients */
      if (this.system.chat_seen_enabled == 1 && recipient.seen == 1) {
        if (conversation.seen_name_list) conversation.seen_name_list += ', ';
        conversation.seen_name_list += html_entity_decode(
          this.system.show_usernames_enabled == 1 ? recipient.user_name : recipient.user_firstname
        );
      }
      /* name_list */
      conversation.name_list += html_entity_decode(
        this.system.show_usernames_enabled == 1 ? recipient.user_name : recipient.user_firstname
      );
      if (i < recipients_num) conversation.name_list += ', ';
      i++;
    }

    /* prepare conversation with multiple_recipients */
    const show = this.system.show_usernames_enabled == 1;
    switch (conversation.node_type) {
      case 'group': {
        const group = await this.get_group(conversation.node_id);
        conversation.multiple_recipients = true;
        conversation.link = 'groups/' + (group ? group.group_name : conversation.node_id);
        conversation.picture = get_picture(group ? group.group_picture : '', 'group', this.system);
        conversation.name = __('Group') + ': ' + (group ? group.group_title : '');
        break;
      }
      case 'event': {
        const event = await this.get_event(conversation.node_id);
        conversation.multiple_recipients = true;
        conversation.link = 'events/' + conversation.node_id;
        conversation.picture = get_picture(event ? event.event_cover : '', 'event', this.system);
        conversation.name = __('Event') + ': ' + (event ? event.event_title : '');
        break;
      }
      default:
        if (recipients_num > 1) {
          conversation.multiple_recipients = true;
          conversation.link = 'messages/' + conversation.conversation_id;
          conversation.picture_left = conversation.recipients[0].user_picture;
          conversation.picture_right = conversation.recipients[1].user_picture;
          const n0 = html_entity_decode(show ? conversation.recipients[0].user_name : conversation.recipients[0].user_firstname);
          const n1 = html_entity_decode(show ? conversation.recipients[1].user_name : conversation.recipients[1].user_firstname);
          if (recipients_num > 2) {
            conversation.name = `${n0}, ${n1} & ${recipients_num - 2} ${__('more')}`;
          } else {
            conversation.name = `${n0} & ${n1}`;
          }
        } else {
          conversation.multiple_recipients = false;
          conversation.user_id = conversation.recipients[0].user_id;
          conversation.link = conversation.recipients[0].user_name;
          conversation.picture = conversation.recipients[0].user_picture;
          conversation.name = html_entity_decode(
            show
              ? conversation.recipients[0].user_name
              : conversation.recipients[0].user_firstname + ' ' + conversation.recipients[0].user_lastname
          );
          conversation.name_html = popover(
            conversation.recipients[0].user_id,
            conversation.recipients[0].user_name,
            show
              ? conversation.recipients[0].user_name
              : conversation.recipients[0].user_firstname + ' ' + conversation.recipients[0].user_lastname
          );
          conversation.user_is_online = await this.user_online(conversation.recipients[0].user_id);
          conversation.user_last_seen = conversation.recipients[0].user_last_seen;
        }
        break;
    }

    conversation.is_chatbox = conversation.node_id ? true : false;
    conversation.total_messages = await this.get_conversation_total_messages(conversation_id);
    conversation.last_message = await this.get_conversation_message_by_id(conversation.last_message_id);
    return conversation;
  },

  /**
   * get_mutual_conversation_id
   * @param {Array} recipients
   * @param {boolean} check_deleted
   * @return {Promise<number|false>}
   */
  async get_mutual_conversation_id(recipients, check_deleted = false) {
    let recipients_array = (Array.isArray(recipients) ? recipients : [recipients])
      .map((v) => this.filter_ids(v))
      .filter((v) => v !== undefined);
    if (recipients_array.length === 0) {
      throw new BadRequestException(__('Conversation recipients is required'));
    }
    recipients_array.push(this._data.user_id);
    const recipients_list = this.spread_ids(recipients_array);
    const get_mutual = await query(
      `SELECT conversations.conversation_id FROM conversations_users INNER JOIN conversations ON conversations_users.conversation_id = conversations.conversation_id WHERE conversations.node_id IS NULL AND conversations_users.user_id IN (${recipients_list}) GROUP BY conversations.conversation_id HAVING COUNT(conversations.conversation_id) = ${recipients_array.length}`
    );
    if (get_mutual.num_rows === 0) return false;
    for (const mutual of get_mutual.rows) {
      const where_statement = check_deleted ? " AND deleted != '1' " : '';
      const get_recipients = await query(
        `SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = ${secure(mutual.conversation_id, 'int')}${where_statement}`
      );
      if (get_recipients.rows[0].count == recipients_array.length) {
        return mutual.conversation_id;
      }
    }
    return false;
  },

  /**
   * check_recipients_chat_privacy
   * @param {Array} recipients
   * @return {void}
   */
  check_recipients_chat_privacy(recipients) {
    const show = this.system.show_usernames_enabled == 1;
    if (this._data.user_privacy_chat == 'me') {
      throw new PrivacyException(
        __('You set your chat privacy to no one, Go to your privacy settings to change this')
      );
    }
    for (const recipient of recipients) {
      const fullname = show
        ? recipient.user_name
        : recipient.user_firstname + ' ' + recipient.user_lastname;
      if (
        recipient.user_privacy_chat == 'me' ||
        (recipient.user_privacy_chat == 'friends' && !this.friendship_approved(recipient.user_id))
      ) {
        throw new PrivacyException(__("You can't chat with this") + ' ' + fullname);
      }
      if (
        this._data.user_privacy_chat == 'friends' &&
        !this.friendship_approved(recipient.user_id)
      ) {
        throw new PrivacyException(
          __("You can't chat with this") +
          ' ' +
          fullname +
          ' ' +
          __('because your chat privacy is set to friends only')
        );
      }
    }
  },

  /**
   * post_conversation_message
   * @param {Object} args
   * @param {boolean} from_web
   * @return {Promise<Object>}
   */
  async post_conversation_message(args = {}, from_web = false) {
    const date = init_system_datetime();
    let message = args.message ?? '';
    let photo = args.photo ?? '';
    let video = args.video ?? '';
    let voice_note = args.voice_note ?? '';
    const product_post_id = args.product_post_id ?? null;
    let conversation_id = args.conversation_id ?? null;
    let recipients = args.recipients ?? null;

    /* filter web-encoded uploads (they arrive JSON-encoded) */
    if (photo && from_web) photo = JSON.parse(photo);
    if (video && from_web) video = JSON.parse(video);
    if (voice_note && from_web) voice_note = JSON.parse(voice_note);

    if (is_empty(message) && !photo && !video && !voice_note && !product_post_id) {
      throw new BadRequestException(__('Message or Image or Video or Voice Note is required'));
    }
    if (product_post_id) {
      const post = await this.get_post(product_post_id);
      if (!post) throw new BadRequestException(__('Product is not exists'));
      if (post.post_type != 'product') throw new BadRequestException(__('Product post is not valid'));
    }
    if (!conversation_id && !recipients) {
      throw new BadRequestException(__('Conversation ID or Recipients is required'));
    }
    if (conversation_id && isNaN(conversation_id)) {
      throw new BadRequestException(__('Conversation ID must be numeric'));
    }
    if (recipients) {
      recipients = JSON.parse(recipients);
      if (!Array.isArray(recipients)) throw new BadRequestException(__('Recipients must be an array'));
      recipients = recipients.filter((v) => !isNaN(v));
      for (const recipient of recipients) {
        if (await this.blocked(recipient)) {
          throw new AuthorizationException(__('You are not authorized to do this'));
        }
      }
    }

    let conversation;
    if (conversation_id == null) {
      /* new or existing mutual conversation */
      const mutual_conversation = await this.get_mutual_conversation_id(recipients);
      if (!mutual_conversation) {
        /* [1] start a new conversation */
        let chat_price = 0;
        const paid_recipients = [];
        const recipient_infos = [];
        for (const recipient of recipients) {
          const recipient_info = await this.get_user(recipient);
          if (
            this.system.monetization_enabled == 1 &&
            (await this.check_user_permission(recipient, 'monetization_permission'))
          ) {
            if (recipient_info.user_monetization_enabled == 1 && recipient_info.user_monetization_chat_price > 0) {
              chat_price += Number(recipient_info.user_monetization_chat_price);
              paid_recipients.push(recipient_info);
            }
          }
          recipient_infos.push(recipient_info);
        }
        this.check_recipients_chat_privacy(recipient_infos);
        if (!product_post_id && chat_price > 0) this.wallet_chat_payment(chat_price, paid_recipients);
        /* insert conversation */
        const insert_conversation = await query("INSERT INTO conversations (last_message_id) VALUES ('0')");
        conversation_id = insert_conversation.insertId;
        /* insert the sender (viewer) */
        await query(
          `INSERT INTO conversations_users (conversation_id, user_id, seen) VALUES (${secure(conversation_id, 'int')}, ${secure(this._data.user_id, 'int')}, '1')`
        );
        /* insert recipients */
        for (const recipient_info of recipient_infos) {
          await query(
            `INSERT INTO conversations_users (conversation_id, user_id) VALUES (${secure(conversation_id, 'int')}, ${secure(recipient_info.user_id, 'int')})`
          );
        }
      } else {
        /* [2] existing conversation */
        conversation_id = mutual_conversation;
        conversation = await this.get_conversation(conversation_id);
        this.check_recipients_chat_privacy(conversation.recipients);
        if (!conversation.node_id && !product_post_id && conversation.chat_price > 0) {
          this.wallet_chat_payment(conversation.chat_price, conversation.paid_recipients);
        }
      }
    } else {
      /* [3] post to an existing conversation */
      conversation = await this.get_conversation(conversation_id);
      if (!conversation) throw new AuthorizationException(__('You are not authorized to do this'));
      if (!conversation.node_type) this.check_recipients_chat_privacy(conversation.recipients);
      if (!conversation.node_id && conversation.chat_price > 0) {
        this.wallet_chat_payment(conversation.chat_price, conversation.paid_recipients);
      }
      if (conversation.node_id) {
        if (conversation.node_type === 'group') {
          const membership = await this.check_group_membership(this._data.user_id, conversation.node_id);
          if (membership != 'approved') throw new Error(__('You are not authorized to do this'));
        } else if (conversation.node_type === 'event') {
          const membership = await this.check_event_membership(this._data.user_id, conversation.node_id);
          if (!membership) throw new Error(__('You are not authorized to do this'));
        }
      }
      for (const recipient of conversation.recipients) {
        if (!conversation.node_id && (await this.blocked(recipient.user_id))) {
          const recipient_name = this.system.show_usernames_enabled == 1
            ? recipient.user_name
            : recipient.user_firstname + ' ' + recipient.user_lastname;
          throw new Error(__('You aren\'t allowed to message') + ' ' + recipient_name);
        }
      }
      /* update sender as seen & not deleted */
      await query(
        `UPDATE conversations_users SET seen = '1', deleted = '0' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
      );
      if (!conversation.node_id) {
        await query(
          `UPDATE conversations_users SET seen = '0', deleted = '0' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id != ${secure(this._data.user_id, 'int')}`
        );
      }
    }

    /* prevent sharing the same product post twice in a row */
    if (product_post_id && conversation && conversation.last_message && conversation.last_message.product_post_id) {
      return;
    }

    /* insert message */
    const insert_message = await query(
      `INSERT INTO conversations_messages (conversation_id, user_id, message, image, video, voice_note, product_post_id, time) VALUES (${secure(conversation_id, 'int')}, ${secure(this._data.user_id, 'int')}, ${secure(message)}, ${secure(photo)}, ${secure(video)}, ${secure(voice_note)}, ${product_post_id ? secure(product_post_id, 'int') : 'null'}, ${secure(date)})`
    );
    const message_id = insert_message.insertId;
    /* update conversation with last message id */
    await query(
      `UPDATE conversations SET last_message_id = ${secure(message_id, 'int')} WHERE conversation_id = ${secure(conversation_id, 'int')}`
    );
    /* update sender with last message id */
    await query(
      `UPDATE users SET user_live_messages_lastid = ${secure(message_id, 'int')} WHERE user_id = ${secure(this._data.user_id, 'int')}`
    );
    /* re-fetch conversation */
    conversation = await this.get_conversation(conversation_id);
    /* update recipients + notify */
    for (const recipient of conversation.recipients) {
      if (!recipient.deleted) {
        await query(
          `UPDATE users SET user_live_messages_lastid = ${secure(message_id, 'int')}, user_live_messages_counter = user_live_messages_counter + 1 WHERE user_id = ${secure(recipient.user_id, 'int')}`
        );
      }
      let notify_message = message;
      if (!notify_message) {
        if (photo) notify_message = __('Sent a photo');
        if (video) notify_message = __('Sent a video');
        if (voice_note) notify_message = __('Sent a voice message');
        if (product_post_id) notify_message = __('Shared a product');
      }
      await this.post_notification({
        to_user_id: recipient.user_id,
        from_user_id: this._data.user_id,
        action: 'chat_message',
        node_url: conversation_id,
        message: notify_message,
      });
    }
    /* clear the viewer's typing status */
    await query(
      `UPDATE conversations_users SET typing = '0' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
    );
    return conversation;
  },

  /**
   * post_conversation_paid_message
   * @param {Object} args
   * @param {boolean} from_web
   * @return {Promise<Object>}
   */
  async post_conversation_paid_message(args = {}, from_web = false) {
    const date = init_system_datetime();
    /* check monetization permission */
    if (!this._data.can_monetize_content || this._data.user_monetization_enabled != 1) {
      throw new AuthorizationException(__("You don't have the permission to do this"));
    }
    /* prepare */
    let message = args.message ?? '';
    let photos = args.photos ?? [];
    let video = args.video ?? null;
    let audio = args.audio ?? null;
    let file = args.file ?? null;
    const conversation_id = args.conversation_id ?? null;
    const message_price = args.message_price ?? 0;
    const paid_text = args.paid_text ?? '';
    const paid_image = args.paid_image ?? '';
    const for_adult =
      args.for_adult === '1' || args.for_adult === true || args.for_adult === 'true' ? '1' : '0';
    /* filter photos */
    if (photos && from_web && typeof photos === 'string') {
      photos = JSON.parse(photos);
    }
    if (!photos || typeof photos !== 'object') {
      photos = [];
    }
    if (!Array.isArray(photos)) {
      photos = Object.values(photos);
    }
    for (const photo of photos) {
      const source = photo && (photo.source ?? photo['source']);
      if (!is_valid_upload_source(source)) {
        throw new BadRequestException(__('Bad Request'));
      }
    }
    /* filter video */
    if (video && from_web && typeof video === 'string') {
      video = JSON.parse(video);
    }
    if (video && typeof video === 'object' && !video.source) {
      video = null;
    }
    if (video) {
      if (typeof video !== 'object' || !is_valid_upload_source(video.source)) {
        throw new BadRequestException(__('Bad Request'));
      }
    }
    /* filter audio */
    if (audio && from_web && typeof audio === 'string') {
      audio = JSON.parse(audio);
    }
    if (audio && typeof audio === 'object' && !audio.source) {
      audio = null;
    }
    if (audio) {
      if (typeof audio !== 'object' || !is_valid_upload_source(audio.source)) {
        throw new BadRequestException(__('Bad Request'));
      }
    }
    /* filter file */
    if (file && from_web && typeof file === 'string') {
      file = JSON.parse(file);
    }
    if (file && typeof file === 'object' && !file.source) {
      file = null;
    }
    if (file) {
      if (typeof file !== 'object' || !is_valid_upload_source(file.source)) {
        throw new BadRequestException(__('Bad Request'));
      }
    }
    /* filter paid image */
    if (paid_image && !is_valid_upload_source(paid_image)) {
      throw new BadRequestException(__('Bad Request'));
    }
    /* at least one media attachment required */
    const has_photos = photos.length > 0;
    const has_video = !!(video && typeof video === 'object');
    const has_audio = !!(audio && typeof audio === 'object');
    const has_file = !!(file && typeof file === 'object');
    const media_count =
      (has_photos ? 1 : 0) + (has_video ? 1 : 0) + (has_audio ? 1 : 0) + (has_file ? 1 : 0);
    if (media_count === 0) {
      throw new ValidationException(__('Please attach at least one media file'));
    }
    /* validate price */
    if (isNaN(message_price) || Number(message_price) <= 0) {
      throw new ValidationException(__('Please enter a valid price'));
    }
    if (
      this.system.monetization_max_paid_post_price > 0 &&
      Number(message_price) > Number(this.system.monetization_max_paid_post_price)
    ) {
      throw new ValidationException(
        __('The price must be less than or equal to') +
        ' ' +
        this.system.system_currency_symbol +
        Number(this.system.monetization_max_paid_post_price)
      );
    }
    /* validate paid text */
    if (String(paid_text).length > 1000) {
      throw new ValidationException(__('Paid content description is more than 1000 characters'));
    }
    /* validate conversation */
    if (!conversation_id || isNaN(conversation_id)) {
      throw new BadRequestException(__('Conversation ID must be numeric'));
    }
    let conversation = await this.get_conversation(conversation_id);
    if (!conversation) {
      throw new AuthorizationException(__('You are not authorized to do this'));
    }
    /* disable in group/event chatboxes for v1 */
    if (conversation.node_id) {
      throw new AuthorizationException(
        __('Paid media messages are not supported in group or event chats yet')
      );
    }
    /* check recipients chat privacy */
    this.check_recipients_chat_privacy(conversation.recipients);
    /* check paid conversation */
    if (conversation.chat_price > 0) {
      this.wallet_chat_payment(conversation.chat_price, conversation.paid_recipients);
    }
    for (const recipient of conversation.recipients) {
      if (await this.blocked(recipient.user_id)) {
        const recipient_name =
          this.system.show_usernames_enabled == 1
            ? recipient.user_name
            : recipient.user_firstname + ' ' + recipient.user_lastname;
        throw new Error(__("You aren't allowed to message") + ' ' + recipient_name);
      }
    }
    /* update sender (viewer) as seen and not deleted */
    await query(
      `UPDATE conversations_users SET seen = '1', deleted = '0' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
    );
    /* update recipients as not seen and not deleted */
    await query(
      `UPDATE conversations_users SET seen = '0', deleted = '0' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id != ${secure(this._data.user_id, 'int')}`
    );
    /* insert message */
    const insert_message = await query(
      `INSERT INTO conversations_messages (conversation_id, user_id, message, image, video, voice_note, is_paid, message_price, paid_text, paid_image, for_adult, time) VALUES (${secure(conversation_id, 'int')}, ${secure(this._data.user_id, 'int')}, ${secure(message)}, '', '', '', '1', ${secure(message_price, 'float')}, ${secure(paid_text)}, ${secure(paid_image)}, ${secure(for_adult)}, ${secure(date)})`
    );
    const message_id = insert_message.insertId;
    const pending_uploads = [];
    if (paid_image) {
      pending_uploads.push(paid_image);
    }
    /* insert media rows */
    if (has_photos) {
      for (const photo of photos) {
        const source = photo && (photo.source ?? photo['source']);
        if (!is_valid_upload_source(source)) {
          throw new BadRequestException(__('Bad Request'));
        }
        await query(
          `INSERT INTO conversations_messages_media (message_id, media_type, source) VALUES (${secure(message_id, 'int')}, 'photo', ${secure(source)})`
        );
        pending_uploads.push(source);
      }
    }
    if (has_video) {
      await query(
        `INSERT INTO conversations_messages_media (message_id, media_type, source) VALUES (${secure(message_id, 'int')}, 'video', ${secure(video.source)})`
      );
      pending_uploads.push(video.source);
    }
    if (has_audio) {
      await query(
        `INSERT INTO conversations_messages_media (message_id, media_type, source) VALUES (${secure(message_id, 'int')}, 'audio', ${secure(audio.source)})`
      );
      pending_uploads.push(audio.source);
    }
    if (has_file) {
      await query(
        `INSERT INTO conversations_messages_media (message_id, media_type, source) VALUES (${secure(message_id, 'int')}, 'file', ${secure(file.source)})`
      );
      pending_uploads.push(file.source);
    }
    /* update the conversation with last message id */
    await query(
      `UPDATE conversations SET last_message_id = ${secure(message_id, 'int')} WHERE conversation_id = ${secure(conversation_id, 'int')}`
    );
    /* update sender (viewer) with last message id */
    await query(
      `UPDATE users SET user_live_messages_lastid = ${secure(message_id, 'int')} WHERE user_id = ${secure(this._data.user_id, 'int')}`
    );
    /* re-fetch conversation */
    conversation = await this.get_conversation(conversation_id);
    /* update all recipients with last message id & only offline recipient messages counter */
    let notify_message = message;
    if (!notify_message) {
      notify_message = __('Sent paid content');
    }
    for (const recipient of conversation.recipients) {
      if (!recipient.deleted) {
        await query(
          `UPDATE users SET user_live_messages_lastid = ${secure(message_id, 'int')}, user_live_messages_counter = user_live_messages_counter + 1 WHERE user_id = ${secure(recipient.user_id, 'int')}`
        );
      }
      await this.post_notification({
        to_user_id: recipient.user_id,
        from_user_id: this._data.user_id,
        action: 'chat_message',
        node_url: conversation_id,
        message: notify_message,
      });
    }
    /* update typing status of the viewer for this conversation */
    await query(
      `UPDATE conversations_users SET typing = '0' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
    );
    /* remove pending uploads */
    for (const file_name of pending_uploads) {
      if (!file_name) continue;
      await query(
        `DELETE FROM users_uploads_pending WHERE user_id = ${secure(this._data.user_id, 'int')} AND file_name = ${secure(file_name)}`
      );
    }
    /* return with conversation */
    return conversation;
  },

  /**
   * get_conversation_message_by_id
   * @param {number} message_id
   * @param {number|null} viewer_id
   * @return {Promise<Object|null>}
   */
  async get_conversation_message_by_id(message_id, viewer_id = null) {
    const get_message = await query(
      `SELECT conversations_messages.*, users.user_id, users.user_name, users.user_firstname, users.user_lastname, users.user_gender, users.user_picture, users.user_subscribed, users.user_verified FROM conversations_messages INNER JOIN users ON conversations_messages.user_id = users.user_id WHERE conversations_messages.message_id = ${secure(message_id, 'int')}`
    );
    if (get_message.num_rows === 0) return null;
    return this.parse_message(get_message.rows[0], viewer_id);
  },

  /**
   * get_conversation_total_messages
   * @param {number} conversation_id
   * @return {Promise<number>}
   */
  async get_conversation_total_messages(conversation_id) {
    const res = await query(
      `SELECT COUNT(*) as count FROM conversations_messages WHERE conversation_id = ${secure(conversation_id, 'int')}`
    );
    return res.rows[0].count;
  },

  /**
   * parse_message
   * @param {Object} message
   * @param {number|null} viewer_id
   * @return {Promise<Object>}
   */
  async parse_message(message, viewer_id = null) {
    let viewer = this._data;
    let viewer_logged_in = this._logged_in;
    if (viewer_id !== null && viewer_id != (this._data.user_id ?? null)) {
      const fetched_viewer = await this.get_user(viewer_id);
      if (fetched_viewer) {
        fetched_viewer.user_adult = get_user_age(fetched_viewer.user_birthdate) >= 18;
        viewer = fetched_viewer;
        viewer_logged_in = true;
      }
    }

    message.user_picture = get_picture(message.user_picture, message.user_gender, this.system);
    message.user_fullname =
      this.system.show_usernames_enabled == 1
        ? message.user_name
        : message.user_firstname + ' ' + message.user_lastname;
    /* reactions */
    message.reactions = {
      like: message.reaction_like_count || 0,
      love: message.reaction_love_count || 0,
      haha: message.reaction_haha_count || 0,
      yay: message.reaction_yay_count || 0,
      wow: message.reaction_wow_count || 0,
      sad: message.reaction_sad_count || 0,
      angry: message.reaction_angry_count || 0,
    };
    message.reactions_total_count = Object.values(message.reactions).reduce((a, b) => a + Number(b), 0);
    message.i_react = false;
    if (message.reactions_total_count > 0) {
      const get_reaction = await query(
        `SELECT reaction FROM conversations_messages_reactions WHERE user_id = ${secure(viewer.user_id, 'int')} AND message_id = ${secure(message.message_id, 'int')}`
      );
      if (get_reaction.num_rows > 0) {
        message.i_react = true;
        message.i_reaction = get_reaction.rows[0].reaction;
      }
    }
    /* message text */
    message.message_orginal = message.message;
    message.message_orginal_decoded = html_entity_decode(message.message);
    message.message = nl2br(message.message);
    message.message_decoded = html_entity_decode(message.message);
    /* product post */
    if (message.product_post_id) {
      const post = await this.get_post(message.product_post_id);
      if (post) message.post = post;
    }
    /* paid media message */
    message.needs_payment = false;
    message.can_get_details = true;
    message.message_price_discounted = 0;
    if (message.is_paid == '1') {
      message.message_price = message.message_price ?? 0;
      message.media = { photos: [], video: null, audio: null, file: null };
      const get_media = await query(
        `SELECT * FROM conversations_messages_media WHERE message_id = ${secure(message.message_id, 'int')}`
      );
      if (get_media.num_rows > 0) {
        for (const media of get_media.rows) {
          if (media.media_type === 'photo') {
            message.media.photos.push(media);
          } else if (media.media_type === 'video') {
            message.media.video = media;
          } else if (media.media_type === 'audio') {
            message.media.audio = media;
          } else if (media.media_type === 'file') {
            message.media.file = media;
          }
        }
      }
      if (message.is_paid == '1' && this.system.monetization_enabled == 1) {
        const author = await this.get_user(message.user_id);
        const discount_enabled = author.user_monetization_discount_enabled == 1;
        const discount_percent = discount_enabled ? Number(author.user_monetization_discount_percent) : 0;
        if (viewer_logged_in) {
          if (viewer.user_group == 3 && message.user_id != viewer.user_id) {
            if (!(await this.is_user_paid_for_message(message.message_id, viewer.user_id))) {
              message.needs_payment = true;
              message.message_price_discounted = discount_enabled
                ? message.message_price * (1 - discount_percent / 100)
                : 0;
            }
          }
        } else {
          message.needs_payment = true;
          message.message_price_discounted = discount_enabled
            ? message.message_price * (1 - discount_percent / 100)
            : 0;
        }
      }
      message.needs_age_verification = false;
      if (this.system.adult_mode == 1 && message.for_adult == '1') {
        if (viewer_logged_in) {
          if (viewer.user_group == 3 && message.user_id != viewer.user_id) {
            if (!viewer.user_adult) {
              message.needs_age_verification = true;
            } else if (this.system.verification_for_adult_content == 1 && viewer.user_verified != 1) {
              message.needs_age_verification = true;
            }
          }
        } else {
          message.needs_age_verification = true;
        }
      }
      message.can_get_details = true;
      if (message.needs_age_verification) {
        message.can_get_details = false;
      } else if (message.needs_payment) {
        message.can_get_details = false;
      }
      if (viewer_logged_in && (message.user_id == viewer.user_id || viewer.user_group < 3)) {
        message.can_get_details = true;
        message.needs_payment = false;
      }
      if (!message.can_get_details) {
        message.media = { photos: [], video: null, audio: null, file: null };
      }
    }
    return message;
  },

  /**
   * delete_conversation
   * @param {number} conversation_id
   * @return {Promise<void>}
   */
  async delete_conversation(conversation_id) {
    const check = await query(
      `SELECT COUNT(*) as count FROM conversations_users INNER JOIN conversations ON conversations_users.conversation_id = conversations.conversation_id WHERE conversations.node_id IS NULL AND conversations.conversation_id = ${secure(conversation_id, 'int')} AND conversations_users.user_id = ${secure(this._data.user_id, 'int')}`
    );
    if (check.rows[0].count == 0) {
      throw new AuthorizationException(__('You are not authorized to do this'));
    }
    if (this.system.chat_permanently_delete_enabled == 1) {
      await query(`DELETE FROM conversations WHERE conversation_id = ${secure(conversation_id, 'int')}`);
      await query(`DELETE FROM conversations_users WHERE conversation_id = ${secure(conversation_id, 'int')}`);
      await query(`DELETE FROM conversations_messages WHERE conversation_id = ${secure(conversation_id, 'int')}`);
    } else {
      await query(
        `UPDATE conversations_users SET typing = '0' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
      );
      await query(
        `UPDATE conversations_users SET deleted = '1' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
      );
    }
  },

  /**
   * leave_conversation
   * @param {number} conversation_id
   * @return {Promise<void>}
   */
  async leave_conversation(conversation_id) {
    const check = await query(
      `SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
    );
    if (check.rows[0].count == 0) {
      throw new AuthorizationException(__('You are not authorized to do this'));
    }
    await query(
      `UPDATE conversations_users SET typing = '0' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
    );
    await query(
      `UPDATE conversations_users SET deleted = '1' WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
    );
  },

  /**
   * set_conversation_color
   * @param {number} conversation_id
   * @param {string} color
   * @return {Promise<void>}
   */
  async set_conversation_color(conversation_id, color) {
    const check = await query(
      `SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
    );
    if (check.rows[0].count == 0) {
      throw new AuthorizationException(__('You are not authorized to do this'));
    }
    await query(
      `UPDATE conversations SET color = ${secure(color)} WHERE conversation_id = ${secure(conversation_id, 'int')}`
    );
  },

  /**
   * update_conversation_typing_status
   * @param {number} conversation_id
   * @param {boolean} is_typing
   * @return {Promise<void>}
   */
  async update_conversation_typing_status(conversation_id, is_typing) {
    if (this.system.chat_typing_enabled != 1) return;
    if (conversation_id == null || is_typing == null) return;
    const check = await query(
      `SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
    );
    if (check.rows[0].count == 0) return;
    const typing = is_typing ? '1' : '0';
    await query(
      `UPDATE conversations_users SET typing = ${secure(typing)} WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
    );
  },

  /**
   * update_conversation_seen_status
   * @param {Array|number} conversation_ids
   * @return {Promise<void>}
   */
  async update_conversation_seen_status(conversation_ids) {
    if (this.system.chat_seen_enabled != 1) return;
    if (conversation_ids == null) return;
    const conversations_array = [];
    for (const conversation_id of Array.isArray(conversation_ids) ? conversation_ids : [conversation_ids]) {
      const check = await query(
        `SELECT COUNT(*) as count FROM conversations_users WHERE conversation_id = ${secure(conversation_id, 'int')} AND user_id = ${secure(this._data.user_id, 'int')}`
      );
      if (check.rows[0].count > 0) conversations_array.push(conversation_id);
    }
    if (conversations_array.length === 0) return;
    await query(
      `UPDATE conversations_users SET seen = '1' WHERE conversation_id IN (${this.spread_ids(conversations_array)}) AND user_id = ${secure(this._data.user_id, 'int')}`
    );
  },
};
