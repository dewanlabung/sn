<!doctype html>
<html data-lang="{$system['language']['code']}" {if $system['language']['dir'] == "RTL"} dir="RTL" {/if}>

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{$page_title|truncate:70}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <link rel="stylesheet" href="{$system['system_url']}/content/themes/{$system['theme']}/css/embed.css?v={$system['system_version']}" />
    <style>
      :root {
        --plyr-color-main: {if $system['css_btn_primary']}{$system['css_btn_primary']}{elseif $system['css_link_color']}{$system['css_link_color']}{else}#5e72e4{/if};
      }
    </style>
  </head>

  <body>
    <div class="embed-video-wrapper">
      {include file='__embed_video.tpl'}
    </div>
    <script src="https://cdn.plyr.io/3.8.4/plyr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var video = document.querySelector('video.js_video-plyr');
        if (!video) {
          return;
        }
        var ratio = {if $system['fluid_videos_enabled']}null{else}'16:9'{/if};
        var controls = ['play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'];
        if (window.innerWidth <= 768) {
          controls.splice(controls.indexOf('volume'), 1);
        }
        var player = new Plyr(video, {
          ratio: ratio,
          fullscreen: {
            iosNative: true
          },
          controls: controls,
          settings: ['captions', 'quality', 'speed', 'loop'],
          hideControls: true,
          storage: true
        });
        var source = video.querySelector('source');
        if (source && source.getAttribute('src') && source.getAttribute('src').endsWith('.m3u8')) {
          if (typeof Hls !== 'undefined' && Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(source.getAttribute('src'));
            hls.attachMedia(video);
          } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = source.getAttribute('src');
          }
        }
      });
    </script>
  </body>

</html>