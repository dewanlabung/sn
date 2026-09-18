/**
 * sockets -> nodejs -> libs -> templates -> feeds_message
 *
 * @package Sngine
 * @author Zamblek
 */

const { __ } = require('../core/i18n');
const { is_empty } = require('../core/database');
const { print_money, build_paid_message_payment_options } = require('../functions');
const { render_message_media } = require('./feeds_message.media');

/**
 * render_need_payment_message
 *
 * @param {Object} params
 * @param {Object} params.message
 * @param {Object} params.system
 * @param {number|null} params.vat_percentage
 * @return {string}
 */
function render_need_payment_message({ message, system, vat_percentage = null }) {
  const uploads = system.system_uploads;
  const price = message.message_price;
  const discounted = message.message_price_discounted;
  let html = '<div class="chat-paid-message ptb15 plr15';
  if (message.paid_image) {
    html += ' chat-paid-message-bg" style="background-image: url(\'' + uploads + '/' + message.paid_image + '\');"';
  } else {
    html += '"';
  }
  html += '><div class="chat-paid-message-inner text-center text-muted';
  if (message.paid_image) {
    html += ' chat-paid-message-overlay';
  }
  html += '">';
  html += '<i class="fa fa-lock main-icon chat-paid-icon mb10"></i>';
  html += '<div><span class="chat-paid-label">' + __('PAID MESSAGE') + '</span></div>';
  html += '<div class="d-grid">';
  if (discounted) {
    const payment_options = build_paid_message_payment_options(
      message.message_id,
      discounted,
      system,
      vat_percentage
    );
    html += `<button class="btn btn-info rounded rounded-pill mt10 chat-paid-btn" data-toggle="modal" data-url="#payment" data-options='${payment_options}'>`;
    html += '<i class="fa fa-money-check-alt mr5"></i>' + __('PAY TO UNLOCK') + ' ' + print_money(discounted, system);
    html += `<span class="ml5 chat-paid-old-price">${print_money(price, system)}</span>`;
    html += '</button>';
  } else {
    const payment_options = build_paid_message_payment_options(
      message.message_id,
      price,
      system,
      vat_percentage
    );
    html += `<button class="btn btn-info rounded rounded-pill mt10 chat-paid-btn" data-toggle="modal" data-url="#payment" data-options='${payment_options}'>`;
    html += '<i class="fa fa-money-check-alt mr5"></i>' + __('PAY TO UNLOCK') + ' ' + print_money(price, system);
    html += '</button>';
  }
  if (message.paid_text) {
    html += `<div class="post-paid-description rounded mt10${message.paid_image ? ' post-paid-description-on-image' : ''}">${message.paid_text}</div>`;
  }
  html += '</div></div></div>';
  return html;
}

/**
 * render_message
 *
 * @param {Object} params
 * @param {Object} params.message
 * @param {boolean} params.is_me
 * @param {Object} params.conversation
 * @param {Object} params.user
 * @param {Object} params.system
 * @param {number|null} params.vat_percentage
 * @return {string}
 */
function render_message({ message, is_me, conversation, user, system, vat_percentage = null }) {
  const uploads = system.system_uploads;
  const url = system.system_url;
  const viewer_id = user && user._data ? user._data.user_id : null;
  const mine = is_me === true || (is_me === undefined && message.user_id == viewer_id);

  let html = '<li>';
  html += `<div class="conversation clearfix ${mine ? 'right' : ''}" id="${message.message_id}">`;

  if (!mine) {
    html += '<div class="conversation-user">';
    html += `<a href="${url}/${message.user_name}"><img src="${message.user_picture}" alt=""></a>`;
    html += '</div>';
  }

  html += `<div class="conversation-body ${system.chat_translation_enabled == 1 ? 'js_chat-translator' : ''}">`;

  if (!is_empty(message.message)) {
    html += '<div class="clearfix">';
    html += `<span class="text ${mine ? 'js_chat-color-me' : ''}">${message.message}</span>`;
    html += '</div>';
  }

  if (message.image) {
    html += `<div class="chat-message-media ${message.message != '' ? 'mt5' : ''}">`;
    html += `<span class="text-link js_lightbox-nodata d-inline-block" data-image="${uploads}/${message.image}">`;
    html += `<img class="img-fluid img-wrapper" src="${uploads}/${message.image}"></span>`;
    html += '</div>';
  }

  if (message.video) {
    html += `<div class="chat-message-media ${message.message != '' ? 'mt5' : ''}">`;
    html += `<video class="video-wrapper" src="${uploads}/${message.video}" controls></video>`;
    html += '</div>';
  }

  if (message.voice_note) {
    html += `<div class="chat-message-media ${message.message != '' ? 'mt5' : ''}">`;
    html += `<audio class="js_audio" id="audio-${message.message_id}" controls preload="auto" style="width: 100%; min-width: 120px;">`;
    html += `<source src="${uploads}/${message.voice_note}" type="audio/mpeg">`;
    html += `<source src="${uploads}/${message.voice_note}" type="audio/mp3">`;
    html += __('Your browser does not support HTML5 audio');
    html += '</audio></div>';
  }

  if (message.post) {
    html += '<div class="chat-message-media">';
    html += `<a class="chat-product" href="${url}/posts/${message.post.post_id}">`;
    html += '<div class="chat-product-image">';
    html += `<img src="${uploads}/${message.post.photos[0].source}"></div>`;
    html += '<div class="chat-product-info">';
    html += `<div class="chat-product-title">${message.post.product.name}</div>`;
    html += `<div class="chat-product-price">${message.post.product.price_formatted}</div>`;
    html += '</div></a></div>';
  }

  if (message.is_paid == '1') {
    if (message.can_get_details) {
      html += render_message_media({ message, system });
    } else if (message.needs_payment) {
      html += render_need_payment_message({ message, system, vat_percentage });
    }
  }

  html += '<div class="conversation-meta">';
  html += `<div class="time js_moment" data-time="${message.time}">${message.time}</div>`;
  if (message.is_paid == '1' && message.can_get_details) {
    html += '<span class="chat-paid-badge">' + __('PAID') + ' &middot; ' + print_money(message.message_price, system) + '</span>';
  }
  html += '</div>';

  if (system.chat_translation_enabled == 1) {
    html += `<div class="translate">${__('Tap to translate')}</div>`;
  }

  if (conversation && conversation.last_seen_message_id == message.message_id) {
    html += `<div class="seen">${__('Seen by')} <span class="js_seen-name-list">${conversation.seen_name_list || ''}</span></div>`;
  }

  html += '</div></div></li>';
  return html;
}

module.exports = { render_message };
