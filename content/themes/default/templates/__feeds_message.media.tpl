{if $message['media']['photos']}
  <!-- photos -->
  <div class="chat-message-media {if !is_empty($message['message'])}mt5{/if}">
    {foreach $message['media']['photos'] as $photo}
      <span class="text-link js_lightbox-nodata d-inline-block {if !$photo@first}mt5{/if}" data-image="{$system['system_uploads']}/{$photo['source']}">
        <img class="img-fluid img-wrapper" src="{$system['system_uploads']}/{$photo['source']}">
      </span>
    {/foreach}
  </div>
  <!-- photos -->
{/if}
{if $message['media']['video']}
  <!-- video -->
  <div class="chat-message-media {if !is_empty($message['message'])}mt5{/if}">
    {assign var="video" value=$message['media']['video']}
    <video class="video-wrapper js_video-plyr" controls preload="metadata">
      <source src="{$system['system_uploads']}/{$video['source']}" type="video/mp4">
    </video>
  </div>
  <!-- video -->
{/if}
{if $message['media']['audio']}
  <!-- audio -->
  <div class="chat-message-media {if !is_empty($message['message'])}mt5{/if}">
    <audio class="js_audio" id="audio-{$message['message_id']}" controls preload="auto" style="width: 100%; min-width: 120px;">
      <source src="{$system['system_uploads']}/{$message['media']['audio']['source']}" type="audio/mpeg">
      <source src="{$system['system_uploads']}/{$message['media']['audio']['source']}" type="audio/mp3">
      {__("Your browser does not support HTML5 audio")}
    </audio>
  </div>
  <!-- audio -->
{/if}
{if $message['media']['file']}
  <!-- file -->
  <div class="chat-message-media {if !is_empty($message['message'])}mt5{/if}">
    <a class="btn btn-sm btn-light" href="{$system['system_uploads']}/{$message['media']['file']['source']}" target="_blank" download>
      <i class="fa fa-file-download mr5"></i>{__("Download File")}
    </a>
  </div>
  <!-- file -->
{/if}
