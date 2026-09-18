/**
 * sockets -> nodejs -> libs -> templates -> feeds_message.media
 *
 * @package Sngine
 * @author Zamblek
 */

const { __ } = require('../core/i18n');
const { is_empty } = require('../core/database');

/**
 * render_message_media
 *
 * @param {Object} params
 * @param {Object} params.message
 * @param {Object} params.system
 * @return {string}
 */
function render_message_media({ message, system }) {
  const uploads = system.system_uploads;
  const mt = !is_empty(message.message) ? 'mt5' : '';
  let html = '';

  if (message.media && message.media.photos && message.media.photos.length) {
    html += `<div class="chat-message-media ${mt}">`;
    message.media.photos.forEach((photo, i) => {
      html += `<span class="text-link js_lightbox-nodata d-inline-block ${i > 0 ? 'mt5' : ''}" data-image="${uploads}/${photo.source}">`;
      html += `<img class="img-fluid img-wrapper" src="${uploads}/${photo.source}"></span>`;
    });
    html += '</div>';
  }
  if (message.media && message.media.video) {
    const video = message.media.video;
    html += `<div class="chat-message-media ${mt}">`;
    html += `<video class="video-wrapper js_video-plyr" controls preload="metadata">`;
    html += `<source src="${uploads}/${video.source}" type="video/mp4">`;
    html += '</video></div>';
  }
  if (message.media && message.media.audio) {
    html += `<div class="chat-message-media ${mt}">`;
    html += `<audio class="js_audio" id="audio-${message.message_id}" controls preload="auto" style="width: 100%; min-width: 120px;">`;
    html += `<source src="${uploads}/${message.media.audio.source}" type="audio/mpeg">`;
    html += `<source src="${uploads}/${message.media.audio.source}" type="audio/mp3">`;
    html += __('Your browser does not support HTML5 audio');
    html += '</audio></div>';
  }
  if (message.media && message.media.file) {
    html += `<div class="chat-message-media ${mt}">`;
    html += `<a class="btn btn-sm btn-light" href="${uploads}/${message.media.file.source}" target="_blank" download>`;
    html += '<i class="fa fa-file-download mr5"></i>' + __('Download File') + '</a></div>';
  }

  return html;
}

module.exports = { render_message_media };
