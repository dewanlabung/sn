<!-- need payment message -->
<div class="chat-paid-message ptb15 plr15{if $paid_image} chat-paid-message-bg{/if}" {if $paid_image} style="background-image: url('{$system['system_uploads']}/{$paid_image}');" {/if}>
  <div class="chat-paid-message-inner text-center text-muted{if $paid_image} chat-paid-message-overlay{/if}">
    {include file='__svg_icons.tpl' icon="locked" class="main-icon chat-paid-icon mb10" width="24px" height="24px"}
    <div>
      <span class="chat-paid-label">{__("PAID MESSAGE")}</span>
    </div>
    <div class="d-grid">
      {if $discounted_price}
        <button class="btn btn-info rounded rounded-pill mt10 chat-paid-btn{if !$user->_logged_in} js_login{/if}" {if $user->_logged_in}data-toggle="modal" data-url="#payment" data-options='{ "handle": "paid_message", "paid_message": "true", "id": {$message_id}, "price": {$discounted_price}, "vat": "{get_payment_vat_value($discounted_price)}", "fees": "{get_payment_fees_value($discounted_price)}", "total": "{get_payment_total_value($discounted_price)}", "total_printed": "{get_payment_total_value($discounted_price, true)}" }' {/if}>
          <i class="fa fa-money-check-alt mr5"></i>{__("PAY TO UNLOCK")} {print_money($discounted_price)}<span class="ml5 chat-paid-old-price">{print_money($price)}</span>
        </button>
      {else}
        <button class="btn btn-info rounded rounded-pill mt10 chat-paid-btn{if !$user->_logged_in} js_login{/if}" {if $user->_logged_in}data-toggle="modal" data-url="#payment" data-options='{ "handle": "paid_message", "paid_message": "true", "id": {$message_id}, "price": {$price}, "vat": "{get_payment_vat_value($price)}", "fees": "{get_payment_fees_value($price)}", "total": "{get_payment_total_value($price)}", "total_printed": "{get_payment_total_value($price, true)}" }' {/if}>
          <i class="fa fa-money-check-alt mr5"></i>{__("PAY TO UNLOCK")} {print_money($price)}
        </button>
      {/if}
      {if $paid_text}
        <div class="post-paid-description rounded mt10{if $paid_image} post-paid-description-on-image{/if}">
          {$paid_text}
        </div>
      {/if}
    </div>
  </div>
</div>
<!-- need payment message -->