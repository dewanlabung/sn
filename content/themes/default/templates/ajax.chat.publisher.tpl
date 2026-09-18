<div class="modal-body ptb0 plr0">
  {include file='_publisher.chat.tpl'}
</div>

<script>
  $(document).ready(function() {

    // detach the publisher if exists
    if ($('#publisher-wapper').length > 0) {
      $('#publisher-wapper').data('publisher-detached', $('#publisher-wapper').contents().detach());
    }

    // trigger the publisher textarea
    $('.publisher textarea').trigger('click');
  });
</script>