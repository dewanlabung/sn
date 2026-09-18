{if $_mini}
  {assign var="_lock_rows" value=3}
{else}
  {assign var="_lock_rows" value=6}
{/if}
<div class="admin-licence-lock {if $_mini}admin-licence-lock-mini{/if} admin-licence-lock-{$licence_lock['type']}">
  <div class="admin-licence-lock-backdrop" aria-hidden="true">
    {for $row=1 to $_lock_rows}
      <div class="admin-licence-lock-row">
        <div class="admin-licence-lock-avatar"></div>
        <div class="admin-licence-lock-lines">
          <div class="admin-licence-lock-line"></div>
          <div class="admin-licence-lock-line short"></div>
        </div>
        <div class="admin-licence-lock-toggle"></div>
      </div>
    {/for}
  </div>
  <div class="admin-licence-lock-message">
    <div class="admin-licence-lock-card">
      <div class="admin-licence-lock-icon">
        {if $licence_lock['type'] == "success"}
          <i class="fa-solid fa-check"></i>
        {else}
          <i class="fa-solid fa-lock"></i>
        {/if}
      </div>
      <div class="admin-licence-lock-body">
        {if $_title}
          <h5>{$_title}</h5>
          <p>{$licence_lock['title']}</p>
        {else}
          <h5>{$licence_lock['title']}</h5>
          <p>{$licence_lock['text']}</p>
        {/if}
      </div>
      <div class="admin-licence-lock-actions">
        <a class="btn btn-md {if $_mini}btn-dark{else}btn-primary{/if}" href="{$licence_lock['url']}">{__("Go to License Page")}</a>
      </div>
    </div>
  </div>
</div>
