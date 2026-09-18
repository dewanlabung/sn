<div class="modal-header">
  <h6 class="modal-title">
    {include file='__svg_icons.tpl' icon="html" class="main-icon mr10" width="24px" height="24px"}
    {__("Edit Rich Text Post")}
  </h6>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="js_ajax-forms" data-url="posts/rich_text.php?do=update&post_id={$post['post_id']}">
  <div class="modal-body">
    <!-- rich text editor -->
    <div class="form-group">
      <textarea name="text" class="form-control js_wysiwyg" id="rich-text-editor">{$post['rich_text']['text']}</textarea>
    </div>
    <!-- rich text editor -->
    <!-- error -->
    <div class="alert alert-danger mb0 mt10 x-hidden"></div>
    <!-- error -->
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
    <button type="submit" class="btn btn-primary">{__("Save")}</button>
  </div>
</form>

<style>
  .tox-tinymce-aux {
    z-index: 99999 !important;
  }
</style>

<script>
  $(document).ready(function() {
    init_tinymce_editor();
    /* let TinyMCE dialogs receive focus inside a Bootstrap modal */
    if (!window.tinymce_modal_focusfix) {
      window.tinymce_modal_focusfix = true;
      document.addEventListener('focusin', function(e) {
        if (e.target.closest('.tox-tinymce-aux, .tox-dialog, .moxman-window, .tam-assetmanager-root')) {
          e.stopImmediatePropagation();
        }
      }, true);
    }
  });
</script>