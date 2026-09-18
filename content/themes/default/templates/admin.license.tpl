{assign var="payload" value=$licence_info['payload']}
{assign var="ui" value=$licence_info['ui']}
{assign var="kind" value=$licence_info['kind']}
{assign var="timeline" value=$licence_info['timeline']}

<div class="card admin-license admin-license-{$ui}">
  <div class="card-header with-icon">
    <div class="float-end">
      <a href="{$system['system_url']}/{$control_panel['url']}/license" class="btn btn-md btn-light">
        <i class="fa fa-rotate mr5"></i>{__("Re-check")}
      </a>
    </div>
    <i class="fa fa-key mr5"></i>{__("License")}
  </div>
  <div class="card-body p-0">

    <div class="admin-license-inner">

      <!-- identity -->
      <div class="admin-license-identity">
        <div class="admin-license-identity-copy">
          <h2 class="admin-license-title">
            {if $ui == "lapsed"}
              {__("Subscription lapsed")}
            {elseif $ui == "missing"}
              {__("No License Found")}
            {elseif $ui == "invalid"}
              {__("License Invalid")}
            {elseif $ui == "unreachable" && !$payload['domain']}
              {__("License Server Unreachable")}
            {elseif $payload['domain']}
              {__("Licensed to")} <span class="admin-license-mono">{$payload['domain']}</span>
            {else}
              {__("License")}
            {/if}
          </h2>
          <p class="admin-license-lead">
            {if $ui == "unreachable"}
              {if $licence_info['api_warning']}{$licence_info['api_warning']}{else}{__("License server unreachable; showing last verified payload.")}{/if}
            {elseif $ui == "missing" || $ui == "invalid"}
              {if $licence_info['message']}{$licence_info['message']}{else}{__("License verification failed.")}{/if}
              {if $licence_info['api_warning']}<br>{$licence_info['api_warning']}{/if}
            {elseif $ui == "lapsed"}
              {__("Payment for")} {if $payload['domain']}<span class="admin-license-mono">{$payload['domain']}</span>{else}{__("this site")}{/if} {__("stopped on")} {if $payload['support_expires_at']}{$payload['support_expires_at']|date_format:"%e %B %Y"}{else}&mdash;{/if}. {__("Monetization and updates are switched off until you renew.")}
            {elseif $ui == "support_ended"}
              {if $kind == "extended"}{__("Extended license, perpetual.")}{else}{__("Regular license, perpetual.")}{/if} {__("Your site keeps running on the version you have — only new releases and support have stopped.")}
            {elseif $kind == "subscription"}
              {if $licence_info['billing'] == "year"}
                {__("Subscription, billed yearly. Payments and monetization are part of this plan.")}
              {else}
                {__("Subscription, billed monthly. Payments and monetization are part of this plan.")}
              {/if}
            {elseif $kind == "extended"}
              {__("Extended license, bought once and yours to keep. Support and updates run for 12 months and can be renewed.")}
            {else}
              {__("Regular license, bought once and yours to keep. Support and updates run for 12 months and can be renewed.")}
            {/if}
          </p>
        </div>
        <span class="admin-license-badge">
          <span class="admin-license-badge-dot"></span>
          {if $ui == "active"}{__("Active")}
          {elseif $ui == "support_ended"}{__("Support ended")}
          {elseif $ui == "lapsed"}{__("Lapsed")}
          {elseif $ui == "unreachable"}{__("Unreachable")}
          {elseif $ui == "missing"}{__("Missing")}
          {else}
            {__("Invalid")}
          {/if}
        </span>
      </div>
      <!-- identity -->

      {if $licence_info['key_masked']}
        <div class="admin-license-keyrow">
          <span class="admin-license-key-label">{__("Key")}</span>
          <span class="admin-license-key">{$licence_info['key_masked']}</span>
        </div>
      {/if}

      {if $timeline['mode'] != "none"}
        <!-- period -->
        <div class="admin-license-period">
          <div class="admin-license-period-head">
            <span>
              {if $timeline['mode'] == "perpetual"}
                {__("Support & Updates")}
              {else}
                {__("License Period")}
              {/if}
            </span>
            <span>
              {if $ui == "support_ended"}
                {__("Ended")} &middot; {__("license unaffected")}
              {elseif $ui == "lapsed"}
                {__("Term ended")}
              {elseif $timeline['mode'] == "perpetual"}
                {__("12 months")} &middot; {__("renewable")}
              {elseif $licence_info['support_timeleft'] !== null && $licence_info['support_timeleft'] > 0}
                {$licence_info['support_timeleft']} {__("days remaining")}
              {else}
                {__("Term ended")}
              {/if}
            </span>
          </div>

          <div class="admin-license-timeline admin-license-timeline-{$timeline['mode']}">
            <div class="admin-license-track">
              <div class="admin-license-track-elapsed" style="width: {$timeline['today_pct']}%;"></div>
              {if $timeline['mode'] == "term"}
                <span class="admin-license-tick" aria-hidden="true"></span>
              {/if}
              <div class="admin-license-today{if $timeline['today_at_end']} admin-license-today-end{/if}" style="inset-inline-start: {$timeline['today_pct']}%;">
                <span>{__("Today")}</span>
              </div>
            </div>
            {if $timeline['mode'] == "perpetual"}
              <div class="admin-license-forever">
                <div class="admin-license-forever-line" aria-hidden="true"></div>
                <div class="admin-license-forever-end">
                  <span class="admin-license-inf" aria-hidden="true">&infin;</span>
                  <span class="admin-license-forever-label">{__("License")} &middot; {__("Perpetual")}</span>
                </div>
              </div>
            {/if}
          </div>

          <div class="admin-license-facts">
            <div>
              <div class="admin-license-fact-label"><i class="fa-regular fa-circle"></i>{__("Issued")}</div>
              <div class="admin-license-fact-value">{if $payload['issued_at']}{$payload['issued_at']|date_format:"%e %b %Y"}{else}&mdash;{/if}</div>
              <div class="admin-license-fact-sub">
                {if $kind == "subscription"}
                  {if $payload['source'] == "freemius" || $payload['source'] == "zamblek"}Zamblek{elseif $payload['source']}{$payload['source']|ucfirst}{else}&mdash;{/if}
                {else}
                  {__("one-time purchase")}
                {/if}
              </div>
            </div>
            <div>
              <div class="admin-license-fact-label"><i class="fa-solid fa-diamond"></i>{__("Next Verification")}</div>
              {if $ui == "lapsed"}
                <div class="admin-license-fact-value">{__("Paused")}</div>
                <div class="admin-license-fact-sub">{__("resumes on renewal")}</div>
              {else}
                <div class="admin-license-fact-value">{if $payload['expires_at']}{$payload['expires_at']|date_format:"%e %b %Y"}{else}&mdash;{/if}</div>
                <div class="admin-license-fact-sub">{if $licence_info['rel_verify']}{$licence_info['rel_verify']} &middot; {__("automatic")}{else}&nbsp;{/if}</div>
              {/if}
            </div>
            <div>
              {if $ui == "lapsed"}
                <div class="admin-license-fact-label"><i class="fa-solid fa-grip-lines-vertical"></i>{__("Lapsed On")}</div>
                <div class="admin-license-fact-value is-alert">{if $payload['support_expires_at']}{$payload['support_expires_at']|date_format:"%e %b %Y"}{else}&mdash;{/if}</div>
                <div class="admin-license-fact-sub">{if $licence_info['rel_support']}{$licence_info['rel_support']}{else}&nbsp;{/if}</div>
              {elseif $ui == "support_ended"}
                <div class="admin-license-fact-label"><i class="fa-solid fa-grip-lines-vertical"></i>{__("Support Ended")}</div>
                <div class="admin-license-fact-value is-alert">{if $payload['support_expires_at']}{$payload['support_expires_at']|date_format:"%e %b %Y"}{else}&mdash;{/if}</div>
                <div class="admin-license-fact-sub">{if $licence_info['rel_support']}{$licence_info['rel_support']}{else}&nbsp;{/if}</div>
              {elseif $kind == "subscription"}
                <div class="admin-license-fact-label"><i class="fa-solid fa-grip-lines-vertical"></i>{__("Renews On")}</div>
                <div class="admin-license-fact-value">{if $payload['support_expires_at']}{$payload['support_expires_at']|date_format:"%e %b %Y"}{else}&mdash;{/if}</div>
                <div class="admin-license-fact-sub">{if $licence_info['rel_support']}{$licence_info['rel_support']}{else}&nbsp;{/if}</div>
              {else}
                <div class="admin-license-fact-label"><i class="fa-solid fa-grip-lines-vertical"></i>{__("Support Ends")}</div>
                <div class="admin-license-fact-value">{if $payload['support_expires_at']}{$payload['support_expires_at']|date_format:"%e %b %Y"}{else}&mdash;{/if}</div>
                <div class="admin-license-fact-sub">{if $licence_info['rel_support']}{$licence_info['rel_support']}{else}&nbsp;{/if}</div>
              {/if}
            </div>
          </div>
        </div>
        <!-- period -->
      {/if}

    </div>

    {if $ui != "missing" && $ui != "invalid"}
      <!-- actions -->
      <div class="admin-license-actions">

        {if $ui == "support_ended"}
          <div class="admin-license-action">
            <div class="admin-license-action-main admin-licence-lock-error">
              <div class="admin-licence-lock-icon">
                <i class="fa-solid fa-headset"></i>
              </div>
              <div>
                <div class="admin-license-action-title">{__("Support & updates")}</div>
                <div class="admin-license-action-text">{__("Renew for another 12 months to download new releases and open support tickets again.")}</div>
              </div>
            </div>
            <a class="admin-license-ghost" href="https://zamblek.com/purchases" target="_blank" rel="noopener">{__("Renew Now")}</a>
          </div>
        {/if}

        {if $ui == "lapsed"}
          <div class="admin-license-action">
            <div>
              <div class="admin-license-action-title">{__("Subscription")}</div>
              <div class="admin-license-action-text">{__("Renew to restore updates, support and monetization. Nothing on your site has been deleted.")}</div>
            </div>
            <a class="admin-license-cta" href="https://zamblek.com/purchases" target="_blank" rel="noopener">{__("Renew subscription")}</a>
          </div>
        {/if}

        <div class="admin-license-action">
          <div class="admin-license-action-main admin-licence-lock-{$licence_lock['type']}">
            <div class="admin-licence-lock-icon">
              <i class="fa-solid fa-money-check-dollar"></i>
            </div>
            <div>
              <div class="admin-license-action-title">
                {__("Payments & monetization")}
              </div>
              <div class="admin-license-action-text">
                {if $ui == "unreachable"}
                  {__("License server unreachable. Re-check to refresh payments and monetization.")}
                {elseif $ui == "lapsed"}
                  {__("Switched off while the subscription is lapsed. Balances and payout history are kept.")}
                {elseif $kind == "regular"}
                  {__("Not part of the Regular license. Upgrade to Extended to sell subscriptions, run ads and take payouts.")}
                {elseif $licence_info['monetization'] && $licence_info['module_installed']}
                  {__("Enabled for")} {if $payload['domain']}<span class="admin-license-mono">{$payload['domain']}</span>{else}{__("this site")}{/if}
                  {if $payload['issued_at']} {__("since")} {$payload['issued_at']|date_format:"%e %B %Y"}{/if}.
                  {if $ui == "support_ended"} {__("Unaffected by the support window.")}{/if}
                {else}
                  {__("Included in this license. Verify the domain to turn on paid subscriptions, ads and payouts.")}
                {/if}
              </div>
            </div>
          </div>
          {if $ui == "unreachable"}
            <a class="admin-license-cta" href="{$system['system_url']}/{$control_panel['url']}/license">{__("Re-check")}</a>
          {elseif $ui == "lapsed"}
            <a class="admin-license-ghost" href="https://zamblek.com/purchases" target="_blank" rel="noopener">{__("Renew to restore")}</a>
          {elseif $kind == "regular"}
            <a class="admin-license-cta" href="https://zamblek.com/pricing" target="_blank" rel="noopener">{__("Upgrade for $19.99")}</a>
          {elseif $licence_info['monetization'] && $licence_info['module_installed']}
            <span class="admin-license-enabled"><i class="fa fa-circle-check"></i>{__("Enabled")}</span>
          {else}
            <a class="admin-license-cta js_admin-license-enable" href="#">{__("Verify and enable")}</a>
          {/if}
        </div>

      </div>
      <!-- actions -->
    {/if}

    {if $payload['license_type'] || $payload['source'] || $payload['domain']}
      <!-- footer -->
      <div class="admin-license-foot">
        <div>
          <div class="admin-license-foot-label">{__("License")}</div>
          <div class="admin-license-foot-value">
            {if $kind == "subscription"}
              {__("Subscription")}{if $licence_info['billing'] == "year"} &middot; {__("yearly")}{elseif $licence_info['billing'] == "month"} &middot; {__("monthly")}{/if}
            {elseif $kind == "extended"}
              {__("Extended")} &middot; {__("one-time")}
            {else}
              {__("Regular")} &middot; {__("one-time")}
            {/if}
          </div>
        </div>
        <div>
          <div class="admin-license-foot-label">{__("Source")}</div>
          <div class="admin-license-foot-value">
            {if $payload['source'] == "freemius" || $payload['source'] == "zamblek"}
              <a href="https://zamblek.com/purchases" target="_blank" rel="noopener">Zamblek</a>
            {elseif $payload['source']}
              {$payload['source']|ucfirst}
            {else}
              &mdash;
            {/if}
          </div>
        </div>
        <div>
          <div class="admin-license-foot-label">{__("Domain")}</div>
          <div class="admin-license-foot-value">{if $payload['domain']}<span class="admin-license-mono">{$payload['domain']}</span>{else}&mdash;{/if}</div>
        </div>
      </div>
      <!-- footer -->
    {/if}

  </div>
</div>