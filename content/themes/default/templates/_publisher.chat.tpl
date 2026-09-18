  <div id="publisher-wapper-modal">
    <div class="publisher-overlay"></div>

    <div class="x-form publisher" data-id="{$cid}" data-modal-mode="true" style=" margin-bottom: 0; border-radius: 8px;">

      <!-- publisher close -->
      <div class="publisher-close">
        <button type="button" class="btn-close js_close-publisher-modal"></button>
      </div>
      <!-- publisher close -->

      <!-- publisher loader -->
      <div class="publisher-loader">
        <div class="loader loader_small"></div>
      </div>
      <!-- publisher loader -->

      <!-- publisher-message -->
      <div class="publisher-message">
        <img class="publisher-avatar" src="{$user->_data['user_picture']}">
        <div class="colored-text-wrapper">
          <textarea autofocus dir="auto" class="js_autosize" data-init-placeholder='{__("Write a message")}' placeholder='{__("Write a message")}'></textarea>
        </div>
      </div>
      <!-- publisher-message -->

      <!-- publisher-slider -->
      <div class="publisher-slider">

        <!-- post attachments (photos) -->
        <div class="publisher-attachments attachments clearfix x-hidden js_attachments-photos"></div>
        <!-- post attachments -->

        <!-- post attachments (videos) -->
        <div class="publisher-attachments attachments clearfix x-hidden js_attachments-video"></div>
        <!-- post attachments -->

        <!-- post attachments (audios) -->
        <div class="publisher-attachments attachments clearfix x-hidden js_attachments-audio"></div>
        <!-- post attachments -->

        <!-- post attachments (files) -->
        <div class="publisher-attachments attachments clearfix x-hidden js_attachments-file"></div>
        <!-- post attachments -->

        <!-- post video -->
        <div class="publisher-meta" data-meta="video">
          {include file='__svg_icons.tpl' icon="checkmark" class="main-icon static mr5" width="16px" height="16px"}
          {__("Video uploaded successfully")}
          <div class="float-end">
            <button type="button" class="btn-close js_publisher-attachment-file-remover" data-type="video"></button>
          </div>
        </div>
        <!-- post video -->

        <!-- post audio -->
        <div class="publisher-meta" data-meta="audio">
          {include file='__svg_icons.tpl' icon="checkmark" class="main-icon static mr5" width="16px" height="16px"}
          {__("Audio uploaded successfully")}
          <div class="float-end">
            <button type="button" class="btn-close js_publisher-attachment-file-remover" data-type="audio"></button>
          </div>
        </div>
        <!-- post audio -->

        <!-- post file -->
        <div class="publisher-meta" data-meta="file">
          {include file='__svg_icons.tpl' icon="checkmark" class="main-icon static mr5" width="16px" height="16px"}
          {__("File uploaded successfully")}
          <div class="float-end">
            <button type="button" class="btn-close js_publisher-attachment-file-remover" data-type="file"></button>
          </div>
        </div>
        <!-- post file -->

        <!-- publisher-tools-tabs -->
        <div class="publisher-tools-tabs">
          <ul class="row">
            {if $system['photos_enabled']}
              <li class="col-md-6">
                <div class="publisher-tools-tab attach js_publisher-tab js_publisher-images-uploader" data-tab="photos">
                  <span class="js_x-uploader" data-handle="publisher" data-multiple="true">
                    {include file='__svg_icons.tpl' icon="camera" class="main-icon mr5" width="24px" height="24px"}
                  </span>
                  {__("Upload Photos")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_upload_videos']}
              <li class="col-md-6">
                <div class="publisher-tools-tab attach js_publisher-tab" data-tab="video">
                  <span class="js_x-uploader" data-handle="publisher" data-type="video">
                    {include file='__svg_icons.tpl' icon="videos" class="main-icon mr5" width="24px" height="24px"}
                  </span>
                  {__("Upload Video")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_upload_audios']}
              <li class="col-md-6">
                <div class="publisher-tools-tab attach js_publisher-tab" data-tab="audio">
                  <span class="js_x-uploader" data-handle="publisher" data-type="audio">
                    {include file='__svg_icons.tpl' icon="audios" class="main-icon mr5" width="24px" height="24px"}
                  </span>
                  {__("Upload Audio")}
                </div>
              </li>
            {/if}
            {if $user->_data['can_upload_files']}
              <li class="col-md-6">
                <div class="publisher-tools-tab attach js_publisher-tab" data-tab="file">
                  <span class="js_x-uploader" data-handle="publisher" data-type="file">
                    {include file='__svg_icons.tpl' icon="files" class="main-icon mr5" width="24px" height="24px"}
                  </span>
                  {__("Upload File")}
                </div>
              </li>
            {/if}
          </ul>
        </div>
        <!-- publisher-tools-tabs -->

        <!-- publisher-footer -->
        <div class="publisher-footer">
          <!-- publisher-options -->
          <div class="publisher-footer-options">

            <!-- adult content -->
            {if $system['adult_mode']}
              <div class="form-table-row mb10" id="adult-toggle-wrapper">
                <div class="avatar">
                  {include file='__svg_icons.tpl' icon="adult" class="main-icon" width="24px" height="24px"}
                </div>
                <div>
                  <div class="form-label mb0">{__("Adult Content")}</div>
                  <div class="form-text d-none d-sm-block mt0">{__("Share your post as adult content")}</div>
                </div>
                <div class="text-end">
                  <label class="switch" for="for_adult">
                    <input type="checkbox" name="for_adult" id="for_adult" class="js_publisher-adult-toggle">
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>
            {/if}
            <!-- adult content -->

            <!-- paid message -->
            <div class="form-group" id="paid-price-wrapper">
              <input type="text" class="form-control" name="paid_price" placeholder="{__("Price")} ({$system['system_currency']})">
            </div>
            <div class="form-group" id="paid-text-wrapper">
              <textarea class="form-control" name="paid_text" rows="3" placeholder="{__("Paid Content Description")}"></textarea>
            </div>
            <div class="form-group" id="paid-image-wrapper">
              <div class="x-image">
                <button type="button" class="btn-close x-hidden js_x-image-remover" title='{__("Remove")}'></button>
                <div class="x-image-loader">
                  <div class="progress x-progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <i class="fa fa-camera fa-lg js_x-uploader" data-handle="x-image" data-blur="true"></i>
                <input type="hidden" class="js_x-uploader-input" name="paid_image" value="">
              </div>
              <div class="form-text">
                {__("Upload a preview image for your content (This image will be blured)")}
              </div>
            </div>
            <!-- paid message -->
          </div>
          <!-- publisher-options -->

          <!-- publisher-error -->
          <div class="alert alert-danger text-start mb15 x-hidden"></div>
          <!-- publisher-error -->

          <!-- publisher-buttons -->
          <div class="publisher-footer-buttons">
            <div class="d-grid">
              <button type="button" class="btn btn-primary ml5 js_publisher-btn js_post-paid-message">
                <i class="fa-solid fa-paper-plane d-inline-block d-xl-none"></i>
                <span class="d-none d-xl-inline-block ml5">{__("Send")}</span>
              </button>
            </div>
            <!-- publisher-buttons -->
          </div>
        </div>
        <!-- publisher-footer -->
      </div>
      <!-- publisher-slider -->
    </div>
</div>