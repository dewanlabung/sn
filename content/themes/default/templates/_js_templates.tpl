{strip}

  <!-- Toasts -->
  <div class="toast-container p-3 bottom-0 start-0 fixed-bottom">
  </div>
  <!-- Toasts -->

  <!-- Modals -->
  <div id="modal" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div class="loader pt10 pb10"></div>
        </div>
      </div>
    </div>
  </div>

  <script id="modal-login" type="text/template">
    <div class="modal-header">
      <h6 class="modal-title">{__("Not Logged In")}</h6>
    </div>
    <div class="modal-body">
      <p>{__("Please log in to continue")}</p>
    </div>
    <div class="modal-footer">
      <a class="btn btn-primary" href="{$system['system_url']}/signin">{__("Login")}</a>
    </div>
  </script>

  <script id="modal-message" type="text/template">
    <div class="modal-header">
      <h6 class="modal-title">{literal}{{title}}{/literal}</h6>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
      </button>
    </div>
    <div class="modal-body">
      <p>{literal}{{{message}}}{/literal}</p>
    </div>
  </script>

  <script id="modal-success" type="text/template">
    <div class="modal-body text-center" style="padding: 50px;">
      {include file='__svg_icons.tpl' icon="checkmark" class="main-icon mb20" width="60px" height="60px"}
      <h4>{literal}{{title}}{/literal}</h4>
      <p class="mt20">{literal}{{{message}}}{/literal}</p>
    </div>
  </script>

  <script id="modal-info" type="text/template">
    <div class="modal-body text-center" style="padding: 50px;">
      {include file='__svg_icons.tpl' icon="info" class="main-icon mb20" width="60px" height="60px"}
      <h4>{literal}{{title}}{/literal}</h4>
      <p class="mt20">{literal}{{{message}}}{/literal}</p>
    </div>
  </script>

  <script id="modal-error" type="text/template">
    <div class="modal-body text-center" style="padding: 50px;">
      {include file='__svg_icons.tpl' icon="report" class="main-icon mb20" width="60px" height="60px"}
      <h4>{literal}{{title}}{/literal}</h4>
      <p class="mt20">{literal}{{{message}}}{/literal}</p>
    </div>
  </script>

  <script id="modal-confirm" type="text/template">
    <div class="modal-header">
      <h6 class="modal-title">{literal}{{title}}{/literal}</h6>
    </div>
    <div class="modal-body">
      <h6>{literal}{{{message}}}{/literal}</h6>
      {literal}{{#password_check}}{/literal}
      <div class="form-group mt20">
        <label class="form-label" for="modal-password-check">{__("Confirm Password")}</label>
        <input id="modal-password-check" name="password_check" type="password" class="form-control">
      </div>
      {literal}{{/password_check}}{/literal}
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
      <button type="button" class="btn btn-primary" id="modal-confirm-ok">{__("Confirm")}</button>
    </div>
  </script>

  <script id="modal-confirm-payment" type="text/template">
    <div class="modal-body text-center" style="padding: 50px;">
      {include file='__svg_icons.tpl' icon="market" class="main-icon mb20" width="60px" height="60px"}
      <h4>{__("Payment Alert")}</h4>
      <p class="mt20">{__("You are about to purchase the items, do you want to proceed?")}</p>
      <div class="mt30 text-center">
        <button type="button" class="btn btn-light rounded-pill mr10" data-bs-dismiss="modal">{__("Cancel")}</button>
        <button type="button" class="btn btn-primary rounded-pill" id="modal-confirm-payment-ok">{__("Confirm")}</button>
      </div>    
    </div>
  </script>

  <script id="modal-loading" type="text/template">
    <div class="modal-body text-center">
      <div class="spinner-border text-primary"></div>
    </div>
  </script>
  <!-- Modals -->

  <!-- Theme Switcher -->
  <script id="theme-switcher" type="text/template">
    <div class="modal-header">
      <h6 class="modal-title">
        {include file='__svg_icons.tpl' icon="themes_switcher" class="main-icon mr10" width="24px" height="24px"}
        {__("Theme Switcher")}
      </h6>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
      </button>
    </div>
    <div class="modal-body pb0 pt30">
      <div class="row">
        {foreach $system['themes'] as $theme}
          <div class="col-6">
            <div style="background: rgb(0 0 0 / 0.06); padding: 8px; border-radius: 8px; margin-bottom: 30px;">
              <a style="display: block; position: relative; padding-bottom: 60%;" href="?theme={$theme['name']}">
                <img style="width: 100%; height: 100%; position: absolute; top: 0; right: 0; bottom: 0; left: 0; border-radius: 4px;" src="{$system['system_url']}/content/themes/{$theme['name']}/thumbnail.png">
                <span style="position: absolute; background: linear-gradient(transparent, rgb(0 0 0 / 0.7)); bottom: 0; left: 0; right: 0; width: 100%; border-radius: 0 0 8px 8px; color: #fff; font-size: 14px; text-align: center; text-transform: capitalize; padding: 20px 8px 8px;">
                  {$theme['name']}
                </span>
              </a>
            </div>
          </div>
        {/foreach}
      </div>
    </div>
  </script>
  <!-- Themes -->

  <!-- Search -->
  <script id="search-for" type="text/template">
    <div class="ptb10 plr10">
      <a href="{$system['system_url']}/search/{literal}{{#hashtag}}hashtag/{{/hashtag}}{/literal}{literal}{{query}}{/literal}">
        <i class="fa fa-search pr5"></i> {__("Search for")} {literal}{{#hashtag}}#{{/hashtag}}{/literal}{literal}{{query}}{/literal}
      </a>
    </div>
  </script>
  <!-- Search -->

  <!-- Lightbox -->
  <script id="lightbox" type="text/template">
    <div class="lightbox">
      <div class="lightbox-container">
        <div class="lightbox-preview">
          <div class="lightbox-next js_lightbox-slider">
            {if $system['language']['dir'] == 'RTL'}
              <i class="fa fa-chevron-circle-left fa-3x"></i>
            {else}
              <i class="fa fa-chevron-circle-right fa-3x"></i>
            {/if}
          </div>
          <div class="lightbox-prev js_lightbox-slider">
            {if $system['language']['dir'] == 'RTL'}
              <i class="fa fa-chevron-circle-right fa-3x"></i>
            {else}
              <i class="fa fa-chevron-circle-left fa-3x"></i>
            {/if}
          </div>
          {if !$system['download_images_disabled']}
            <div class="lightbox-download">
              <a href="{literal}{{image}}{/literal}" download>{__("Download")}</a> - <a target="_blank" href="{literal}{{image}}{/literal}">{__("View Original")}</a>
            </div>
          {/if}
          <img alt="" class="img-fluid" src="{literal}{{image}}{/literal}">
        </div>
        <div class="lightbox-data">
          <div class="clearfix">
            <div class="pt5 pr5 float-end">
              <button type="button" class="btn-close lightbox-close js_lightbox-close">
              </button>
            </div>
          </div>
          <div class="lightbox-post">
            <div class="js_scroller" data-slimScroll-height="100%">
              <div class="loader mtb10"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </script>

  <script id="lightbox-nodata" type="text/template">
    <div class="lightbox">
      <div class="lightbox-container">
        <div class="lightbox-preview nodata">
          <div class="lightbox-exit js_lightbox-close">
            <i class="fas fa-times fa-2x"></i>
          </div>
          <img alt="" class="img-fluid" src="{literal}{{image}}{/literal}">
        </div>
      </div>
    </div>
  </script>

  <script id="lightbox-live" type="text/template">
    <div class="lightbox" data-live-post-id="{literal}{{post_id}}{/literal}">
      <div class="lightbox-container">
        <div class="lightbox-preview with-live">
          <div class="live-stream-video" id="js_live-video">
            <div class="live-counter" id="js_live-counter">
              <span class="status offline" id=js_live-counter-status>{__("Offline")}</span>
              <span class="number">
                <i class="fas fa-eye mr5"></i><strong id="js_live-counter-number">0</strong>
              </span>
            </div>
            <div class="live-status" id="js_live-status">
              {__("Loading")}<span class="spinner-grow spinner-grow-sm ml10"></span>
            </div>
          </div>
        </div>
        <div class="lightbox-data">
          <div class="clearfix">
            <div class="pt5 pr5 float-end">
              <button type="button" class="btn-close lightbox-close js_lightbox-close">
              </button>
            </div>
          </div>
          <div class="lightbox-post">
            <div class="js_scroller" data-slimScroll-height="100%">
              <div class="loader mtb10"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </script>
  <!-- Lightbox -->

  <!-- Two-Factor Authentication -->
  <script id="two-factor-authentication" type="text/template">
    <div class="modal-header">
      <h6 class="modal-title">{__("2FA Authentication")}</h6>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <form class="js_ajax-forms" data-url="core/two_factor_authentication.php">
      <div class="modal-body">
        <div class="mb20">
          {__("You've asked us to require a 6-digit login code when anyone tries to access your account from a new device or browser")}.
        </div>
        <div class="mb20">
          {__("Enter the 6-digit code that you received on your")} <strong>{literal}{{method}}{/literal}</strong>
        </div>
        <div class="form-group">
          <input class="form-control" name="two_factor_key" type="text" placeholder="######" required autofocus>
        </div>
        <!-- error -->
        <div class="alert alert-danger mb0 mt10 x-hidden"></div>
        <!-- error -->
      </div>
      <div class="modal-footer">
        <input name="user_id" type="hidden" value="{literal}{{user_id}}{/literal}">
        {literal}{{#remember}}{/literal}<input name="remember" type="hidden" value="true">{literal}{{/remember}}{/literal}
        {literal}{{#connecting_account}}{/literal}<input name="connecting_account" type="hidden" value="true">{literal}{{/connecting_account}}{/literal}
        <button type="submit" class="btn btn-primary">{__("Continue")}</button>
      </div>
    </form>
  </script>
  <!-- Two-Factor Authentication -->

  {if !$user->_logged_in}

    <!-- Forget Password -->
    <script id="forget-password-confirm" type="text/template">
      <div class="modal-header">
        <h6 class="modal-title">{__("Verification Code")}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="js_ajax-forms" data-url="core/forget_password_confirm.php">
        <div class="modal-body">
          <div class="mb20">
            {__("We sent you an email with a six-digit verification code. Enter it below to continue to reset your password")}.
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input class="form-control" name="reset_key" type="text" placeholder="######" required autofocus>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label mb5">{__("We sent your code to")}</label> <span class="badge badge-lg bg-light text-primary">{literal}{{email}}{/literal}</span>
            </div>
          </div>
          <!-- error -->
          <div class="alert alert-danger mt15 mb0 x-hidden"></div>
          <!-- error -->
        </div>
        <div class="modal-footer">
          <input name="email" type="hidden" value="{literal}{{email}}{/literal}">
          <button type="submit" class="btn btn-primary">{__("Continue")}</button>
        </div>
      </form>
    </script>

    <script id="forget-password-reset" type="text/template">
      <div class="modal-header">
        <h6 class="modal-title">{__("Change Your Password")}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="js_ajax-forms" data-url="core/forget_password_reset.php">
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label" for="password">{__("New Password")}</label>
            <input class="form-control" name="password" id="password" type="password" required autofocus>
          </div>
          <div class="form-group">
            <label class="form-label" for="confirm">{__("Confirm Password")}</label>
            <input class="form-control" name="confirm" id="confirm" type="password" required>
          </div>
          <!-- error -->
          <div class="alert alert-danger mb0 mt10 x-hidden"></div>
          <!-- error -->
        </div>
        <div class="modal-footer">
          <input name="email" type="hidden" value="{literal}{{email}}{/literal}">
          <input name="reset_key" type="hidden" value="{literal}{{reset_key}}{/literal}">
          <button type="submit" class="btn btn-primary">{__("Continue")}</button>
        </div>
      </form>
    </script>
    <!-- Forget Password -->

  {else}

    <!-- Account Switcher -->
    <script id="account-switcher" type="text/template">
      <div class="modal-header">
        <h6 class="modal-title">
          {include file='__svg_icons.tpl' icon="accounts_switcher" class="main-icon mr10" width="24px" height="24px"}
          {__("Switch Accounts")}
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body ptb30">
        <ul>
          {foreach $user->_data['connected_accounts'] as $connected_account}
            <li class="feeds-item with-hover mt5">
              <div class="data-container js_connected-account-switch" data-uid="{$connected_account['user_id']}">
                <span class="data-avatar">
                  <img src="{$connected_account['user_picture']}" alt="">
                </span>
                <div class="data-content">
                  {if $connected_account['user_id'] == $user->_data['user_id']}
                    <div class="float-end">
                      <i class="fa fa-check-circle fa-fw fa-2x text-success"></i>
                    </div>
                  {/if}
                  <div class="mt5">
                    <span class="name text-link-no-underline">
                      {if $system['show_usernames_enabled']}
                        {$connected_account['user_name']}
                      {else}
                        {$connected_account['user_firstname']} {$connected_account['user_lastname']}
                      {/if}
                    </span>
                    {if $connected_account['user_verified']}
                      <span class="verified-badge" data-bs-toggle="tooltip" title='{__("Verified User")}'>
                        {include file='__svg_icons.tpl' icon="verified_badge" width="20px" height="20px"}
                      </span>
                    {/if}
                    {if $connected_account['user_subscribed']}
                      <span class="pro-badge" data-bs-toggle="tooltip" title='{__("Pro User")}'>
                        {include file='__svg_icons.tpl' icon="pro_badge" width="20px" height="20px"}
                      </span>
                    {/if}
                  </div>
                </div>
              </div>
            </li>
          {/foreach}
        </ul>
        {if $user->_data['user_id'] == $user->_data['user_master_account']}
          <div class="d-grid mt20">
            <button class="btn btn-primary" data-toggle="modal" data-url="#account-connector">{__("Add Account")}</button>
          </div>
        {/if}
      </div>
    </script>

    <script id="account-connector" type="text/template">
      <div class="modal-header">
        <h6 class="modal-title">
          {include file='__svg_icons.tpl' icon="accounts_switcher" class="main-icon mr10" width="24px" height="24px"}  
          {__("Connect Accounts")}
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body ptb30">
        <form class="js_ajax-forms" data-url="users/switch.php" method="POST">
          <!-- username or email -->
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-text"><i class="far fa-user fa-fw"></i></span>
              <input class="form-control" type="text" placeholder='{__("Email")} {__("or")} {__("Username")}' name="username_email" required>
            </div>
          </div>
          <!-- username or email -->
          <!-- password -->
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-key fa-fw"></i></span>
              <input class="form-control" type="password" placeholder='{__("Password")}' name="password" required>
            </div>
          </div>
          <!-- password -->
          <!-- submit -->
          <div class="d-grid form-group">
            <input type="hidden" name="do" value="signin">
            <button type="submit" class="btn btn-lg btn-primary bg-gradient-blue border-0 rounded-pill">{__("Login")}</button>
          </div>
          <!-- submit -->
          <!-- error -->
          <div class="alert alert-danger mt15 mb0 x-hidden"></div>
          <!-- error -->
        </form>
      </div>
    </script>
    <!-- Account Switcher -->

    <!-- Email Activation -->
    <script id="activation-email" type="text/template">
      <div class="modal-header">
        <h6 class="modal-title">{__("Enter the code from the email")}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="js_ajax-forms" data-url="core/activation_email.php">
        <div class="modal-body">
          <div class="mb20">
            {__("Let us know if this email belongs to you. Enter the code in the email")}
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input class="form-control" name="code" type="text" placeholder="######" required autofocus>
                  {if $user->_data['user_email']}
                  <div class="form-text">
                    <span class="text-link" data-toggle="modal" data-url="core/activation_email_resend.php">{__("Resend Email")}</span>
                  </div>
                {/if}
              </div>
            </div>
            <div class="col-md-6">
              {if $user->_data['user_email']}
                {__("We sent your code to")} <strong>{$user->_data['user_email']}</strong>
              {/if}
            </div>
          </div>
          <!-- error -->
          <div class="alert alert-danger mb0 mt10 x-hidden"></div>
          <!-- error -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
          <button type="submit" class="btn btn-primary">{__("Continue")}</button>
        </div>
      </form>
    </script>

    <script id="activation-email-reset" type="text/template">
      <div class="modal-header">
        <h6 class="modal-title">{__("Change Email Address")}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="js_ajax-forms" data-url="core/activation_email_reset.php">
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label mb10">{__("Current Email")}</label><br>
            <span class="badge badge-lg bg-info">{$user->_data['user_email']}</span>
          </div>
          <div class="form-group">
            <label class="form-label" for="email">{__("New Email")}</label>
            <input class="form-control" name="email" id="email" type="email" required autofocus>
          </div>
          <!-- error -->
          <div class="alert alert-danger mb0 mt10 x-hidden"></div>
          <!-- error -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
          <button type="submit" class="btn btn-primary">{__("Continue")}</button>
        </div>
      </form>
    </script>
    <!-- Email Activation -->

    <!-- Phone Activation -->
    <script id="activation-phone" type="text/template">
      <div class="modal-header">
        <h6 class="modal-title">{__("Enter the code from the SMS message")}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="js_ajax-forms" data-url="core/activation_phone.php">
        <div class="modal-body">
          <div class="mb20">
            {__("Let us know if this mobile number belongs to you. Enter the code in the SMS")}
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input class="form-control" name="code" type="text" placeholder="######" required autofocus>
                {if $user->_data['user_phone']}
                  <div class="form-text">
                    <span class="text-link" data-toggle="modal" data-url="core/activation_phone_resend.php">{__("Resend SMS")}</span>
                  </div>
                {/if}
              </div>
            </div>
            <div class="col-md-6">
              {if $user->_data['user_phone']}
                {__("We sent your code to")} <strong>{$user->_data['user_phone']}</strong>
              {/if}
            </div>
          </div>
          <!-- error -->
          <div class="alert alert-danger mb0 mt10 x-hidden"></div>
          <!-- error -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
          <button type="submit" class="btn btn-primary">{__("Continue")}</button>
        </div>
      </form>
    </script>

    <script id="activation-phone-reset" type="text/template">
      <div class="modal-header">
        <h6 class="modal-title">{__("Change Phone Number")}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="js_ajax-forms" data-url="core/activation_phone_reset.php">
        <div class="modal-body">
          {if $user->_data['user_phone']}
            <div class="form-group">
              <label class="form-label">{__("Current Phone")}</label>
              <p class="form-control-plaintext">{$user->_data['user_phone']}</p>
            </div>
          {/if}
          <div class="form-group">
            <label class="form-label">{__("New Phone")}</label>
            <input class="form-control" name="phone" type="text" required autofocus>
            <div class="form-text">
              {__("For example")}: +12344567890
            </div>
          </div>
          <!-- error -->
          <div class="alert alert-danger mb0 mt10 x-hidden"></div>
          <!-- error -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
          <button type="submit" class="btn btn-primary">{__("Continue")}</button>
        </div>
      </form>
    </script>
    <!-- Phone Activation -->

    <!-- x-uploader -->
    {/strip}
    <script id="x-uploader" type="text/template">
      <form class="x-uploader" action="{literal}{{url}}{/literal}" method="post" enctype="multipart/form-data">
        {literal}{{#multiple}}{/literal}
        <input name="file[]" type="file" multiple="multiple" accept="{literal}{{accept}}{/literal}">
        {literal}{{/multiple}}{/literal}
        {literal}{{^multiple}}{/literal}
        <input name="file" type="file" accept="{literal}{{accept}}{/literal}">
        {literal}{{/multiple}}{/literal}
        <input type="hidden" name="secret" value="{literal}{{secret}}{/literal}">
      </form>
    </script>
    {strip}
    <!-- x-uploader -->

    <!-- Noty Notification -->
    {if $system['noty_notifications_enabled']}
      <script id="toast-notification" type="text/template">
        <a href="{literal}{{url}}{/literal}" class="toast align-items-center text-bg-info border-0 d-block" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
            <div class="data-container">
              <div class="data-avatar">
                <img src="{literal}{{image}}{/literal}" />
              </div>
              <div class="data-content mt10">{literal}{{message}}{/literal}</div>
            </div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </a>
      </script>
    {/if}
    <!-- Noty Notification -->

    <!-- Adblock Detector -->
    {if $system['adblock_detector_enabled']}
      <script id="adblock-detector" type="text/template">
        <div class="adblock-detector">
          {__("Our website is made possible by displaying online advertisements to our visitors")}<br>
          {__("Please consider supporting us by disabling your ad blocker")}.
        </div>
      </script>
    {/if}
    <!-- Adblock Detector -->

    <!-- Keyboard Shortcuts -->
    <script id="keyboard-shortcuts" type="text/template">
      <div class="modal-header">
        <h6 class="modal-title">
          {include file='__svg_icons.tpl' icon="keyboard" class="main-icon mr10" width="24px" height="24px"}
          {__("Keyboard Shortcuts")}
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-xlg">
        <div class="mb10">
          <kbd>J</kbd> {__("Scroll to the next post")}
        </div>
        <div>
          <kbd>K</kbd> {__("Scroll to the previous post")}
        </div>
      </div>
    </script>
    <!-- Keyboard Shortcuts -->

    <!-- Emoji Menu -->
    <script id="emoji-menu" type="text/template">
      <div class="emoji-menu">
        <div class="tab-content">
          <div class="tab-pane tab-emojis active" id="tab-emojis-{literal}{{id}}{/literal}">
            <div class="js_scroller" data-slimScroll-height="180">
              {foreach $emojis as $emoji_index => $emoji }
                <div class="item item-{$emoji_index}"{if $emoji_index > 50 } style="display: none;" js-hidden {/if}>
                    <i data-emoji="{$emoji['unicode_char']}" class="js_emoji twa twa-2x twa-{$emoji['class']}"></i> 
                </div>
              {/foreach}
            </div>
          </div>
          <div class="tab-pane" id="tab-stickers-{literal}{{id}}{/literal}">
            <div class="js_scroller" data-slimScroll-height="180">
              {foreach from=$user->get_stickers() item=sticker}
                <div class="item">
                  <img data-emoji=":STK-{$sticker['sticker_id']}:" src="{$system['system_uploads']}/{$sticker['image']}" class="js_emoji">
                </div>
              {/foreach}
            </div>
          </div>
        </div>
        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a class="nav-link active" href="#tab-emojis-{literal}{{id}}{/literal}" data-bs-toggle="tab">
              <i class="fa fa-smile fa-fw mr5"></i>{__("Emojis")}
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#tab-stickers-{literal}{{id}}{/literal}" data-bs-toggle="tab">
              <i class="fa fa-hand-peace fa-fw mr5"></i>{__("Stickers")}
            </a>
          </li>
        </ul>
      </div>
    </script>
    <!-- Emoji Menu -->

    <!-- Chat -->
    {if $system['chat_enabled'] && $user->_data['user_privacy_chat'] != "me"}
      <!-- Chat Sidebar -->
      <div class="chat-sidebar {if !$user->_data['user_chat_enabled']}disabled{/if}">
        <div class="chat-sidebar-content">
          <div class="js_scroller" data-slimScroll-height="100%">
            <!-- Online -->
            <div class="js_chat-contacts-online">
              {foreach $sidebar_friends as $_user}
                {if $_user['user_is_online']}
                  <div class="chat-avatar-wrapper clickable js_chat-start"
                      data-uid="{$_user['user_id']}"
                      data-name="{$_user['user_fullname']}"
                      data-link="{$_user['user_name']}"
                      data-picture="{$_user['user_picture']}">

                    <div class="chat-avatar">
                      <img src="{$_user['user_picture']}" alt="" />
                      <i class="online-status fa fa-circle online"></i>
                    </div>

                    <div class="pb10"></div>

                  </div>
                {/if}
              {/foreach}
            </div>
            <!-- Online -->
            <!-- Offline -->
            <div class="js_chat-contacts-offline">
              {foreach $sidebar_friends as $_user}
                {if !$_user['user_is_online']}
                  <div class="chat-avatar-wrapper clickable js_chat-start"
                      data-uid="{$_user['user_id']}"
                      data-name="{$_user['user_fullname']}"
                      data-link="{$_user['user_name']}"
                      data-picture="{$_user['user_picture']}">

                    <div class="chat-avatar">
                      <img src="{$_user['user_picture']}" alt="" />
                      <i class="online-status fa fa-circle offline"></i>
                    </div>

                    {if $system['chat_status_enabled']}
                      <div class="last-seen">
                        <span class="js_moment" data-time="{$_user['user_last_seen']}">{$_user["user_last_seen"]}</span>
                      </div>
                    {else}
                      <div class="pb10"></div>
                    {/if}

                  </div>
                {/if}
              {/foreach}
            </div>
            <!-- Offline -->
          </div>
        </div>
        <div class="chat-sidebar-footer">
          <a class="btn btn-chat rounded-circle mb10 js_chat-new" href="{$system['system_url']}/messages/new">
            {include file='__svg_icons.tpl' icon="start_chat" class="main-icon" width="20px" height="20px"}
          </a>
          <button class="btn btn-chat rounded-circle" data-bs-toggle="dropdown" data-display="static">
            {include file='__svg_icons.tpl' icon="cog" class="main-icon" width="20px" height="20px"}
          </button>
          <div class="dropdown-menu dropdown-menu-end action-dropdown-menu">
            <a class="dropdown-item" href="{$system['system_url']}/settings/blocking">
              <div class="action">
                {include file='__svg_icons.tpl' icon="blocking" class="main-icon mr10" width="20px" height="20px"}
                {__("Manage Blocking")}
              </div>
              <div class="action-desc">{__("Manage blocked users")}</div>
            </a>
            <a class="dropdown-item" href="{$system['system_url']}/settings/privacy">
              <div class="action">
                {include file='__svg_icons.tpl' icon="privacy" class="main-icon mr10" width="20px" height="20px"}
                {__("Privacy Settings")}
              </div>
              <div class="action-desc">{__("Manage your privacy")}</div>
            </a>
            <div class="dropdown-divider"></div>
            {if $user->_data['user_chat_enabled']}
              <div class="dropdown-item pointer js_chat-toggle" data-status="on">
                <div class="action">
                  {include file='__svg_icons.tpl' icon="hide" class="main-icon mr10" width="20px" height="20px"}
                  <span class="js_chat-toggle-text">{__("Turn Off Active Status")}</span>
                </div>
              </div>
            {else}
              <div class="dropdown-item pointer js_chat-toggle" data-status="off">
                <div class="action">
                  {include file='__svg_icons.tpl' icon="unhide" class="main-icon mr10" width="20px" height="20px"}
                  <span class="js_chat-toggle-text">{__("Turn On Active Status")}</span>
                </div>
              </div>
            {/if}
          </div>
        </div>
      </div>
      <!-- Chat Sidebar -->
    {/if}

    <script id="chat-box-new" type="text/template">
      <div class="chat-widget chat-box opened fresh">
        <!-- head -->
        <div class="chat-widget-head">
          <!-- user-card -->
          <div class="chat-user-card">
            <div class="name">
              {__("New Message")}
            </div>
          </div>
          <!-- user-card -->

          <!-- buttons-->
          <div class="chat-head-btns">
            <span class="chat-head-btn js_chat-box-close">
              {include file='__svg_icons.tpl' icon="close" class="main-icon" width="20px" height="20px"}
            </span>
          </div>
          <!-- buttons-->
        </div>
        <!-- head -->
        <!-- content -->
        <div class="chat-widget-content">
          <div class="chat-conversations js_scroller"></div>
          <div class="chat-to clearfix js_autocomplete-tags">
            <div class="to">{__("To")}:</div>
            <ul class="tags"></ul>
            <div class="typeahead">
              <input type="text" size="1" autofocus>
            </div>
          </div>
          <div class="chat-voice-notes">
            <div class="voice-recording-wrapper" data-handle="chat">
              <!-- processing message -->
              <div class="x-hidden js_voice-processing-message">
                {include file='__svg_icons.tpl' icon="upload" class="main-icon mr5" width="16px" height="16px"}
                {__("Processing")}<span class="loading-dots"></span>
              </div>
              <!-- processing message -->

              <!-- success message -->
              <div class="x-hidden js_voice-success-message">
                {include file='__svg_icons.tpl' icon="checkmark" class="main-icon mr5" width="16px" height="16px"}
                {__("Voice note recorded successfully")}
                <div class="float-end">
                  <button type="button" class="btn-close js_voice-remove"></button>
                </div>
              </div>
              <!-- success message -->

              <!-- start recording -->
              <div class="btn-voice-start js_voice-start">
                <i class="fas fa-microphone mr5"></i>{__("Record")}
              </div>
              <!-- start recording -->

              <!-- stop recording -->
              <div class="btn-voice-stop js_voice-stop" style="display: none">
                <i class="far fa-stop-circle mr5"></i>{__("Recording")} <span class="js_voice-timer">00:00</span>
              </div>
              <!-- stop recording -->
            </div>
          </div>
          <div class="chat-attachments attachments clearfix x-hidden">
            <ul>
              <li class="loading">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </li>
            </ul>
          </div>
          <div class="x-form chat-form invisible">
            <div class="chat-form-message">
              <textarea class="js_autosize js_post-message" dir="auto" rows="1" placeholder='{__("Write a message")}'></textarea>
            </div>
            <ul class="x-form-tools clearfix">
              {if $system['chat_photos_enabled']}
                <li class="x-form-tools-attach">
                  <i class="far fa-image fa-lg fa-fw js_x-uploader" data-handle="chat"></i>
                </li>
              {/if}
              {if $system['voice_notes_chat_enabled']}
                <li class="x-form-tools-voice js_chat-voice-notes-toggle">
                  <i class="fas fa-microphone fa-lg fa-fw"></i>
                </li>
              {/if}
              <li class="x-form-tools-emoji js_emoji-menu-toggle">
                <i class="far fa-smile-wink fa-lg fa-fw"></i>
              </li>
            </ul>
          </div>
        </div>
        <!-- content -->
      </div>
    </script>

    <script id="chat-box" type="text/template">
      <div class="chat-widget chat-box opened" id="{literal}{{chat_key_value}}{/literal}" {literal}{{#conversation_id}}{/literal}data-cid="{literal}{{conversation_id}}{/literal}" {literal}{{/conversation_id}}{/literal} {literal}{{#user_id}}{/literal}data-uid="{literal}{{user_id}}{/literal}" {literal}{{/user_id}}{/literal}>
        <!-- head -->
        <div class="chat-widget-head">
          <!-- label -->
          <div class="chat-head-label"><span class="badge rounded-pill bg-danger js_chat-box-label"></span></div>
          <!-- label -->
          
          <!-- user-card -->
          <a class="chat-user-card" href="{$system['system_url']}/{literal}{{link}}{/literal}" title="{literal}{{name_list}}{/literal}">
            {literal}{{^multiple}}{/literal}
            <div class="avatar">
              <img src="{literal}{{picture}}{/literal}">
              <i class="online-status fa fa-circle offline js_chat-box-status"></i>
            </div>
            {literal}{{/multiple}}{/literal}
            <div class="name">
              <span>{literal}{{name}}{/literal}</span>
            </div>
          </a>
          <!-- user-card -->
          
          <!-- buttons-->
          <div class="chat-head-btns">
            <!-- video/audio calls (not multiple) -->
            {literal}{{^multiple}}{/literal}
            {if $system['audio_call_enabled'] && $user->_data['can_start_audio_call']}
              <span class="chat-head-btn js_chat-call-start" data-type="audio" data-uid="{literal}{{user_id}}{/literal}" data-name="{literal}{{name_list}}{/literal}" data-picture="{literal}{{picture}}{/literal}">
                {include file='__svg_icons.tpl' icon="call_audio" class="main-icon" width="20px" height="20px"}
              </span>
            {/if}
            {if $system['video_call_enabled'] && $user->_data['can_start_video_call']}
              <span class="chat-head-btn js_chat-call-start" data-type="video" data-uid="{literal}{{user_id}}{/literal}" data-name="{literal}{{name_list}}{/literal}" data-picture="{literal}{{picture}}{/literal}">
                {include file='__svg_icons.tpl' icon="call_video" class="main-icon" width="20px" height="20px"}
              </span>
            {/if}
            {literal}{{/multiple}}{/literal}
            <!-- video/audio calls (not multiple) -->
            <span class="chat-head-btn js_chat-box-close">
              {include file='__svg_icons.tpl' icon="close" class="main-icon" width="20px" height="20px"}
            </span>
          </div>
          <!-- buttons-->
        </div>
        <!-- head -->
        <!-- content -->
        <div class="chat-widget-content">
          <div class="chat-conversations js_scroller">
            <ul></ul>
          </div>
          <div class="chat-typing">
            <i class="far fa-comment-dots mr5"></i><span class="loading-dots"><span class="js_chat-typing-users"></span> {__("Typing")}</span>
          </div>
          <div class="chat-voice-notes">
            <div class="voice-recording-wrapper" data-handle="chat">
              <!-- processing message -->
              <div class="x-hidden js_voice-processing-message">
                {include file='__svg_icons.tpl' icon="upload" class="main-icon mr5" width="16px" height="16px"}
                {__("Processing")}<span class="loading-dots"></span>
              </div>
              <!-- processing message -->

              <!-- success message -->
              <div class="x-hidden js_voice-success-message">
                {include file='__svg_icons.tpl' icon="checkmark" class="main-icon mr5" width="16px" height="16px"}
                {__("Voice note recorded successfully")}
                <div class="float-end">
                  <button type="button" class="btn-close js_voice-remove"></button>
                </div>
              </div>
              <!-- success message -->

              <!-- start recording -->
              <div class="btn-voice-start js_voice-start">
                <i class="fas fa-microphone mr5"></i>{__("Record")}
              </div>
              <!-- start recording -->

              <!-- stop recording -->
              <div class="btn-voice-stop js_voice-stop" style="display: none">
                <i class="far fa-stop-circle mr5"></i>{__("Recording")} <span class="js_voice-timer">00:00</span>
              </div>
              <!-- stop recording -->
            </div>
          </div>
          <div class="chat-attachments attachments clearfix x-hidden">
            <ul>
              <li class="loading">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </li>
            </ul>
          </div>
          <div class="x-form chat-form">
            <div class="chat-form-message">
              <textarea class="js_autosize js_post-message" dir="auto" rows="1" placeholder='{__("Write a message")}'></textarea>
            </div>
            <ul class="x-form-tools clearfix">
              {literal}{{#conversation_id}}{/literal}
                {if $user->_data['can_monetize_content']}
                  <li class="x-form-tools-monetize" data-toggle="modal" data-static="true" data-url="chat/publisher.php?cid={literal}{{conversation_id}}{/literal}">
                    <i class="fas fa-dollar-sign fa-lg fa-fw"></i>
                  </li>
                {/if}
              {literal}{{/conversation_id}}{/literal}
              {if $system['chat_photos_enabled']}
                <li class="x-form-tools-attach">
                  <i class="far fa-image fa-lg fa-fw js_x-uploader" data-handle="chat"></i>
                </li>
              {/if}
              {if $system['voice_notes_chat_enabled']}
                <li class="x-form-tools-voice js_chat-voice-notes-toggle">
                  <i class="fas fa-microphone fa-lg fa-fw"></i>
                </li>
              {/if}
              <li class="x-form-tools-emoji js_emoji-menu-toggle">
                <i class="far fa-smile-wink fa-lg fa-fw"></i>
              </li>
              <li class="x-form-tools-colors js_chat-colors-menu-toggle js_chat-color-me {literal}{{^conversation_id}}{/literal}x-hidden{literal}{{/conversation_id}}{/literal}">
                <i class="fa fa-circle fa-lg fa-fw"></i>
              </li>
            </ul>
          </div>
        </div>
        <!-- content -->
      </div>
    </script>

    <script id="chat-message" type="text/template">
      <li>
        <div class="conversation clearfix right" id="{literal}{{id}}{/literal}">
          <div class="conversation-body">
            {literal}{{#message}}{/literal}
            <div class="clearfix">
              <span class="text js_chat-color-me" {literal}{{#color}}{/literal}style="background-color: {literal}{{color}}{/literal}" {literal}{{/color}}{/literal}>
                {literal}{{{message}}}{/literal}
              </span>
            </div>
            {literal}{{/message}}{/literal}
            {literal}{{#image}}{/literal}
            <div class="chat-message-media {literal}{{#message}}{/literal}mt5{literal}{{/message}}{/literal}">
              <span class="text-link js_lightbox-nodata d-inline-block" data-image="{$system['system_uploads']}/{literal}{{image}}{/literal}">
                <img alt="" class="img-fluid img-wrapper" src="{$system['system_uploads']}/{literal}{{image}}{/literal}">
              </span>
            </div>
            {literal}{{/image}}{/literal}
            {literal}{{#voice_note}}{/literal}
            <div class="chat-message-media {literal}{{#message}}{/literal}mt5{literal}{{/message}}{/literal}">
              <audio class="js_audio" id="audio-{literal}{{id}}{/literal}" controls preload="auto" style="width: 100%; min-width: 120px;">
                <source src="{$system['system_uploads']}/{literal}{{voice_note}}{/literal}" type="audio/mpeg">
                <source src="{$system['system_uploads']}/{literal}{{voice_note}}{/literal}" type="audio/mp3">
                {__("Your browser does not support HTML5 audio")}
              </audio>
            </div>
            {literal}{{/voice_note}}{/literal}
            <div class="time js_moment" data-time="{literal}{{time}}{/literal}">
              {literal}{{time}}{/literal}
            </div>
          </div>
        </div>
      </li>
    </script>

    <script id="chat-calling" type="text/template">
      <div class="modal-header border-0">
        <h6 class="modal-title  mx-auto">
          {literal}{{#is_video}}{/literal}{include file='__svg_icons.tpl' icon="call_video" class="main-icon mr10" width="24px" height="24px"}{literal}{{/is_video}}{/literal}
          {literal}{{#is_audio}}{/literal}{include file='__svg_icons.tpl' icon="call_audio" class="main-icon mr10" width="24px" height="24px"}{literal}{{/is_audio}}{/literal}
          {__("Outgoing call")}
        </h6>
      </div>
      <div class="modal-body text-center">
      <div class="position-relative mb10" style="height: 106px;">
          <div class="profile-avatar-wrapper static">
            <img src="{literal}{{picture}}{/literal}" alt="{literal}{{name}}{/literal}" style="width: 98px; height: 98px;">
          </div>
        </div>
        <h3>{literal}{{name}}{/literal}</h3>
        <p class="js_chat-calling-message">{__("Connecting")}<span class="loading-dots"></span></p>

        <div class="video-call-stream-wrapper">
          <div class="video-call-stream"></div>
          <div class="video-call-stream-local"></div>
        </div>

        <div class="mt30">
          <button type="button" class="btn btn-light rounded-pill x-hidden js_chat-call-close" data-bs-dismiss="modal">
            <i class="fas fa-xmark fa-lg fa-fw mr5"></i>{__("Close")}
          </button>
          <button type="button" class="btn btn-danger rounded-pill x-hidden js_chat-call-cancel" data-bs-dismiss="modal">
            <i class="fas fa-xmark fa-lg fa-fw mr5"></i>{__("Cancel")}
          </button>
          <button type="button" class="btn btn-danger rounded-pill x-hidden js_chat-call-end" data-bs-dismiss="modal">
            <i class="fas fa-xmark fa-lg fa-fw mr5"></i>{__("End")}
          </button>
        </div>
      </div>
      <div class="modal-footer border-0"></div>
    </script>

    <script id="chat-ringing" type="text/template">
      <div class="modal-header border-0">
        <h6 class="modal-title mx-auto">
          {literal}{{#is_video}}{/literal}{include file='__svg_icons.tpl' icon="call_video" class="main-icon mr10" width="24px" height="24px"}{literal}{{/is_video}}{/literal}
          {literal}{{#is_audio}}{/literal}{include file='__svg_icons.tpl' icon="call_audio" class="main-icon mr10" width="24px" height="24px"}{literal}{{/is_audio}}{/literal}
          {__("Incoming call")}
        </h6>
      </div>
      <div class="modal-body text-center">
        <div class="position-relative mb10" style="height: 106px;">
          <div class="profile-avatar-wrapper static">
            <img src="{literal}{{picture}}{/literal}" alt="{literal}{{name}}{/literal}" style="width: 98px; height: 98px;">
          </div>
        </div>
        <h3>{literal}{{name}}{/literal}</h3>
        {literal}{{#is_video}}{/literal}<p class="js_chat-calling-message">{__("Wants to have video call with you")}</p>{literal}{{/is_video}}{/literal}
        {literal}{{#is_audio}}{/literal}<p class="js_chat-calling-message">{__("Wants to have audio call with you")}</p>{literal}{{/is_audio}}{/literal}

        <div class="video-call-stream-wrapper">
          <div class="video-call-stream"></div>
          <div class="video-call-stream-local"></div>
        </div>

        <div class="mt30">
          <button type="button" class="btn btn-success rounded-pill mr10 js_chat-call-answer" data-id="{literal}{{id}}{/literal}">
            {literal}{{#is_video}}{/literal}<i class="fas fa-video fa-lg fa-fw mr5"></i>{literal}{{/is_video}}{/literal}
            {literal}{{#is_audio}}{/literal}<i class="fas fa-phone-alt fa-lg fa-fw mr5"></i>{literal}{{/is_audio}}{/literal}
            {__("Accept")}
          </button>
          <button type="button" class="btn btn-danger rounded-pill js_chat-call-decline" data-id="{literal}{{id}}{/literal}" data-bs-dismiss="modal">
            <i class="fas fa-xmark fa-lg fa-fw mr5"></i>{__("Decline")}
          </button>
          <button type="button" class="btn btn-danger rounded-pill x-hidden js_chat-call-end" data-id="{literal}{{id}}{/literal}" data-bs-dismiss="modal">
            <i class="fas fa-xmark fa-lg fa-fw mr5"></i>{__("End")}
          </button>
        </div>
      </div>
      <div class="modal-footer border-0"></div>
    </script>

    <script id="chat-colors-menu" type="text/template">
      <div class="chat-colors-menu">
        <div class="js_scroller" data-slimScroll-height="180">
          <div class="item js_chat-color" data-color="#5e72e4" style="color: #5e72e4;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#0ba05d" style="color: #0ba05d;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#ed9e6a" style="color: #ed9e6a;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#a085e2" style="color: #a085e2;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#01a5a5" style="color: #01a5a5;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#2b87ce" style="color: #2b87ce;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#ff72d2" style="color: #ff72d2;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#c9605e" style="color: #c9605e;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#056bba" style="color: #056bba;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#fc9cde" style="color: #fc9cde;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#70a0e0" style="color: #70a0e0;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#f2812b" style="color: #f2812b;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#8ec96c" style="color: #8ec96c;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#f33d4c" style="color: #f33d4c;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#aa2294" style="color: #aa2294;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#0e71ea" style="color: #0e71ea;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#b582af" style="color: #b582af;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#a1ce79" style="color: #a1ce79;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#56c4c5" style="color: #56c4c5;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#f9a722" style="color: #f9a722;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#f9c270" style="color: #f9c270;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#609b41" style="color: #609b41;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#51bcbc" style="color: #51bcbc;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#008484" style="color: #008484;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
          <div class="item js_chat-color" data-color="#5462a5" style="color: #5462a5;">
            <i class="fa fa-circle fa-2x"></i>
          </div>
        </div>
      </div>
    </script>

    <script id="chat-attachments-item" type="text/template">
      <li class="item deletable" data-src="{literal}{{src}}{/literal}">
        <img alt="" src="{literal}{{image_path}}{/literal}">
        <button type="button" class="btn-close js_chat-attachment-remover" title='{__("Remove")}'></button>
      </li>
    </script>
    <!-- Chat -->

    <!-- DayTime Messages -->
    {if $system['daytime_msg_enabled'] && $page == "index"}
      <script id="message-morning" type="text/template">
        <div class="card daytime_message">
          <button type="button" class="btn-close float-end js_daytime-remover"></button>
          {include file='__svg_icons.tpl' icon="sun" width="40px" height="40px" class="d-table-cell vertical-align-middle pr10"}
          <div class="d-table-cell">
            <strong>{__("Good Morning")}, {$user->_data['user_fullname']}</strong><br>
            <span>{__($system['system_morning_message'])}</span>
          </div>
        </div>
      </script>

      <script id="message-afternoon" type="text/template">
        <div class="card daytime_message">
          <button type="button" class="btn-close float-end js_daytime-remover"></button>
          {include file='__svg_icons.tpl' icon="afternoon" width="40px" height="40px" class="d-table-cell vertical-align-middle pr10"}
          <div class="d-table-cell">
            <strong>{__("Good Afternoon")}, {$user->_data['user_fullname']}</strong><br>
            <span>{__($system['system_afternoon_message'])}</span>
          </div>
        </div>
      </script>

      <script id="message-evening" type="text/template">
        <div class="card daytime_message">
          <button type="button" class="btn-close float-end js_daytime-remover"></button>
          {include file='__svg_icons.tpl' icon="night" width="40px" height="40px" class="d-table-cell vertical-align-middle pr10"}
          <div class="d-table-cell">
            <strong>{__("Good Evening")}, {$user->_data['user_fullname']}</strong><br>
            <span>{__($system['system_evening_message'])}</span>
          </div>
        </div>
      </script>
    {/if}
    <!-- DayTime Messages -->

    <!-- Gifts -->
    {if $system['gifts_enabled'] && $page == "profile"}
      <script id="gifts" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">
            {include file='__svg_icons.tpl' icon="gifts" class="main-icon mr10" width="24px" height="24px"}
            {__("Gifts")}
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="js_ajax-forms" data-url="users/gifts.php?do=send&uid={literal}{{uid}}{/literal}">
          <div class="modal-body">
            <div class="js_scroller" data-slimScroll-height="440">
              <div class="d-flex flex-wrap justify-content-start">
                {foreach from=$gifts item=gift}
                  <div style="width: calc(100% / 3); padding: 5px; text-align: center;">
                    <input class="x-hidden input-label" type="radio" name="gift" value="{$gift['gift_id']}" id="gift_{$gift['gift_id']}" />
                    <label class="button-label-image" for="gift_{$gift['gift_id']}">
                      <img src="{$system['system_uploads']}/{$gift['image']}" style="width: 86px!important; height: 86px!important;" />
                      {if $system['gifts_points_enabled']}
                        <div class="mt5">
                          <span class="badge badge-sm bg-light text-primary">{$gift['points']} {__("Points")}</span>
                        </div>
                      {/if}
                    </label>
                  </div>
                {/foreach}
              </div>
            </div>
            <!-- error -->
            <div class="alert alert-danger mb0 mt10 x-hidden"></div>
            <!-- error -->
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">{__("Send")}</button>
          </div>
        </form>
      </script>

      <script id="gift" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">
            {include file='__svg_icons.tpl' icon="gifts" class="main-icon mr10" width="24px" height="24px"}
            {if $system['show_usernames_enabled']}{$gift['user_name']}{else}{$gift['user_firstname']} {$gift['user_lastname']}{/if} {__("sent you a gift")}
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          {if $system['gifts_points_enabled']}
            <div class="mb5">
              <span class="badge badge-sm bg-light text-primary">{$gift['points']} {__("Points")}</span>
            </div>
          {/if}
          <img class="img-fluid" src="{$system['system_uploads']}/{$gift['image']}">
        </div>
      </script>
    {/if}
    <!-- Gifts -->

    <!-- Uploader -->
    <script id="uploader-attachments-image-item" type="text/template">
      <li class="item deletable" data-src="{literal}{{src}}{/literal}">
        <img alt="" src="{literal}{{image_path}}{/literal}">
        <button type="button" class="btn-close {literal}{{#mini}}{/literal}js_publisher-mini-attachment-image-remover{literal}{{/mini}}{/literal}{literal}{{^mini}}{/literal}js_publisher-attachment-image-remover{literal}{{/mini}}{/literal}" title='{__("Remove")}'></button>
      </li>
    </script>

    <script id="uploader-attachments-video-item" type="text/template">
      <li class="item deletable" data-src="{literal}{{src}}{/literal}">
        <div class="name">{literal}{{name}}{/literal}</div>
        <button type="button" class="btn-close js_publisher-mini-attachment-video-remover" title='{__("Remove")}'></button>
      </li>
    </script>
    <!-- Uploader -->

    <!-- Publisher -->
    <script id="scraper-photo" type="text/template">
      <div class="publisher-scraper-remover js_publisher-scraper-remover">
        <button type="button" class="btn-close"></button>
      </div>
      <div class="post-media">
        <div class="post-media-image">
          <img src="{literal}{{url}}{/literal}">
        </div>
        <div class="post-media-meta">
          <div class="source">{literal}{{provider}}{/literal}</div>
        </div>
      </div>
    </script>

    <script id="scraper-link" type="text/template">
      <div class="publisher-scraper-remover js_publisher-scraper-remover">
        <button type="button" class="btn-close"></button>
      </div>
      <div class="post-media">
        {literal}{{#thumbnail}}{/literal}
        <a class="post-media-image" href="{literal}{{url}}{/literal}" target="_blank">
          <img src="{literal}{{thumbnail}}{/literal}">
          <div class="source">{literal}{{host}}{/literal}</div>
        </a>
        {literal}{{/thumbnail}}{/literal}
        <div class="post-media-meta">
          <a class="title mb5" href="{literal}{{url}}{/literal}" target="_blank">{literal}{{title}}{/literal}</a>
          <div class="text mb5">{literal}{{text}}{/literal}</div>
        </div>
      </div>
    </script>

    <script id="scraper-media" type="text/template">
      <div class="publisher-scraper-remover js_publisher-scraper-remover">
        <button type="button" class="btn-close"></button>
      </div>
      <div>
        {literal}{{{html}}}{/literal}
      </div>
    </script>

    <script id="scraper-player" type="text/template">
      <div class="publisher-scraper-remover js_publisher-scraper-remover">
        <button type="button" class="btn-close"></button>
      </div>
      <div>
        <div class="ratio ratio-16x9">
          {literal}{{{html}}}{/literal}
        </div>
      </div>
    </script>

    <script id="scraper-facebook" type="text/template">
      <div class="publisher-scraper-remover js_publisher-scraper-remover">
        <button type="button" class="btn-close"></button>
      </div>
      <div>
        <div class="embed-facebook-wrapper">
          {literal}{{{html}}}{/literal}
          <div class="embed-facebook-placeholder ptb30">
            <div class="d-flex justify-content-center">
              <div class="spinner-grow"></div>
            </div>
          </div>
        </div>
      </div>
    </script>

    <script id="poll-option" type="text/template">
      <div class="publisher-meta" data-meta="poll">
        {include file='__svg_icons.tpl' icon="plus" class="main-icon" width="16px" height="16px"}
        <input type="text" placeholder='{__("Add an option")}...'>
      </div>
    </script>

    <script id="pubisher-gif" type="text/template">
      <div class="publisher-scraper-remover js_publisher-gif-remover">
        <button type="button" class="btn-close"></button>
      </div>
      <div class="post-media">
        <div class="post-media-image">
          <div class="image" style="background-image:url('{literal}{{src}}{/literal}');"></div>
        </div>
      </div>
    </script>

    <script id="publisher-mention-item" type="text/template">
      <div class="publisher-mention-item">
        <div class="publisher-mention-item__avatar">
          <img class="publisher-mention-item__img" src="{literal}{{avatar}}{/literal}" alt="" loading="lazy" draggable="false">
        </div>
        <div class="publisher-mention-item__body">
          <div class="publisher-mention-item__name mention-item-label">{literal}{{value}}{/literal}</div>
        </div>
      </div>
    </script>

    <script id="publisher-hashtag-item" type="text/template">
      <div class="publisher-hashtag-item">
        <div class="publisher-hashtag-item__body">
          <div class="publisher-hashtag-item__name mention-item-label">#{literal}{{value}}{/literal}</div>
        </div>
      </div>
    </script>
    <!-- Publisher -->

    <!-- Posts & Comments -->
    {if in_array($page, ["index", "profile", "page", "group", "event", "post", "photo", "market", "blogs", "directory", "search", "share", "reels"])}
      <!-- Comments -->
      <script id="comment-attachments-item" type="text/template">
        <li class="item deletable" data-src="{literal}{{src}}{/literal}">
          <img alt="" src="{literal}{{image_path}}{/literal}">
          <button type="button" class="btn-close js_comment-attachment-remover" title='{__("Remove")}'></button>
        </li>
      </script>
      <!-- Comments -->

      <!-- Edit (Posts|Comments) -->
      <script id="edit-post" type="text/template">
        <div class="post-edit">
          <div class="x-form post-form">
            <textarea rows="2" class="js_autosize js_mentions js_update-post-textarea">{literal}{{text}}{/literal}</textarea>
            <ul class="x-form-tools clearfix">
              <li class="x-form-tools-emoji js_emoji-menu-toggle">
                <i class="far fa-smile-wink fa-lg fa-fw"></i>
              </li>
            </ul>
          </div>
          <div class="text-end mt5">
            <button class="btn btn-sm btn-outline-light plr15 rounded-pill mr5 js_unedit-post">{__("Cancel")}</button>
            <button class="btn btn-sm btn-outline-primary plr15 rounded-pill js_update-post">{__("Save")}</button>
          </div>
        </div>
      </script>

      <script id="edit-comment" type="text/template">
        <div class="comment-edit">
          <div class="x-form comment-form">
            <textarea rows="1" class="js_autosize js_mentions js_update-comment-textarea">{literal}{{text}}{/literal}</textarea>
            <ul class="x-form-tools clearfix">
              <li class="x-form-tools-attach">
                <i class="far fa-image fa-lg fa-fw js_x-uploader" data-handle="comment"></i>
              </li>
              <li class="x-form-tools-emoji js_emoji-menu-toggle">
                <i class="far fa-smile-wink fa-lg fa-fw"></i>
              </li>
            </ul>
          </div>
          <div class="comment-attachments attachments clearfix x-hidden">
            <ul>
              <li class="loading">
                <div class="progress x-progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </li>
            </ul>
          </div>
          <div class="text-end mt5">
            <button class="btn btn-sm btn-outline-light plr15 rounded-pill mr5 js_unedit-comment">{__("Cancel")}</button>
            <button class="btn btn-sm btn-outline-primary plr15 rounded-pill js_update-comment">{__("Save")}</button>
          </div>
        </div>
      </script>
      <!-- Edit (Posts|Comments) -->

      <!-- Hidden (Posts|Authors) -->
      <script id="hidden-post" type="text/template">
        <div class="post flagged" data-id="{literal}{{id}}{/literal}">
          <div class="text-semibold mb5">{__("Post Hidden")}</div>
          {__("This post will no longer appear to you")} <span class="text-link js_unhide-post">{__("Undo")}</span>
        </div>
      </script>

      <script id="hidden-author" type="text/template">
        <div class="post flagged" data-id="{literal}{{id}}{/literal}">
          {__("You won't see posts from")} {literal}{{name}}{/literal} {__("in News Feed anymore")}. <span class="text-link js_unhide-author" data-author-id="{literal}{{uid}}{/literal}" data-author-name="{literal}{{name}}{/literal}">{__("Undo")}</span>
        </div>
      </script>
      <!-- Hidden (Posts|Authors) -->
    {/if}
    <!-- Posts & Comments -->

    <!-- Wallet -->
    {if $page == "wallet"}
      <script id="wallet-withdraw-affiliates" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">
            {include file='__svg_icons.tpl' icon="affiliates" class="main-icon mr10" width="24px" height="24px"}
            {__("Withdraw Affiliates Credit")}
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="js_ajax-forms" data-url="payments/wallet.php?do=wallet_withdraw_affiliates">
          <div class="modal-body">
            <div class="bs-callout bs-callout-warning mt0">
              {__("Your Affiliates Balance")} <span class="badge badge-lg bg-light text-primary">{print_money($user->_data['user_affiliate_balance'])}</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="amount">{__("Amount")}</label>
              <div class="input-money {$system['system_currency_dir']}">
                <span>{$system['system_currency_symbol']}</span>
                <input class="form-control" type="text" placeholder="0.00" min="1.00" max="1000" name="amount">
              </div>
            </div>
            <!-- error -->
            <div class="alert alert-danger mb0 mt10 x-hidden"></div>
            <!-- error -->
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">{__("Continue")}</button>
          </div>
        </form>
      </script>

      <script id="wallet-withdraw-points" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">
            {include file='__svg_icons.tpl' icon="points" class="main-icon mr10" width="24px" height="24px"}
            {__("Withdraw Points Credit")}
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="js_ajax-forms" data-url="payments/wallet.php?do=wallet_withdraw_points">
          <div class="modal-body">
            <div class="bs-callout bs-callout-warning mt0">
              {__("Your Points Balance")} <span class="badge badge-lg bg-light text-primary">{if $system['points_per_currency'] == 0}0{else}{print_money((((1/$system['points_per_currency'])*$user->_data['user_points'])))}{/if}</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="amount">{__("Amount")}</label>
              <div class="input-money {$system['system_currency_dir']}">
                <span>{$system['system_currency_symbol']}</span>
                <input class="form-control" type="text" placeholder="0.00" min="1.00" max="1000" name="amount">
              </div>
            </div>
            <!-- error -->
            <div class="alert alert-danger mb0 mt10 x-hidden"></div>
            <!-- error -->
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">{__("Continue")}</button>
          </div>
        </form>
      </script>

      <script id="wallet-withdraw-market" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">
            {include file='__svg_icons.tpl' icon="market" class="main-icon mr10" width="24px" height="24px"}
            {__("Withdraw Market Credit")}
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="js_ajax-forms" data-url="payments/wallet.php?do=wallet_withdraw_market">
          <div class="modal-body">
            <div class="bs-callout bs-callout-warning mt0">
              {__("Your Marketplace Balance")} <span class="badge badge-lg bg-light text-primary">{print_money($user->_data['user_market_balance'])}</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="amount">{__("Amount")}</label>
              <div class="input-money {$system['system_currency_dir']}">
                <span>{$system['system_currency_symbol']}</span>
                <input class="form-control" type="text" placeholder="0.00" min="1.00" max="1000" name="amount">
              </div>
            </div>
            <!-- error -->
            <div class="alert alert-danger mb0 mt10 x-hidden"></div>
            <!-- error -->
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">{__("Continue")}</button>
          </div>
          </form>
        </script>
    {/if}
    <!-- Wallet -->

    <!-- Crop Profile (Picture|Cover) -->
    {if in_array($page, ["started", "profile", "page", "group", "event"])}
      <script id="crop-profile-picture" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">
            {include file='__svg_icons.tpl' icon="crop" class="main-icon mr10" width="24px" height="24px"}
            {__("Crop Picture")}
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div class="alert alert-info mb-20">
            <i class="fa fa-info-circle mr5"></i>{__("Crop animated images will make them static, You can skip the cropping process by clicking on the cancel button")}
          </div>
          <img id="cropped-profile-picture" src="{literal}{{image}}{/literal}" style="max-width: 100%;">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
          <button type="button" class="btn btn-primary js_crop-picture" data-handle="{literal}{{handle}}{/literal}" data-id="{literal}{{id}}{/literal}">{__("Save")}</button>
        </div>
      </script>

      <script id="crop-profile-cover" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">
            {include file='__svg_icons.tpl' icon="crop" class="main-icon mr10" width="24px" height="24px"}
            {__("Crop Cover")}
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div class="alert alert-info mb-20">
            <i class="fa fa-info-circle mr5"></i>{__("Crop animated images will make them static, You can skip the cropping process by clicking on the cancel button")}
          </div>
          <img id="cropped-profile-cover" src="{literal}{{image}}{/literal}" style="max-width: 100%;">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
          <button type="button" class="btn btn-primary js_crop-cover" data-handle="{literal}{{handle}}{/literal}" data-id="{literal}{{id}}{/literal}">{__("Save")}</button>
        </div>
      </script>
    {/if}
    <!-- Crop Profile (Picture|Cover) -->

    <!-- Download Information -->
    {if $page == "settings"}
      <script id="download-information" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">
            {include file='__svg_icons.tpl' icon="user_information" class="main-icon mr10" width="24px" height="24px"}
            {__("Download Your Information")}
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="text-center">
            {include file='__svg_icons.tpl' icon="ready" class="mb20" width="100px" height="100px"}
            <p class="text-lg">{__("Your file is ready to download")}</p>
            <a href="{$system['system_url']}/settings/download?hash={$user->_data['user_name']}-{$secret}" class="btn btn-md btn-primary bg-gradient-blue border-0 rounded-pill">
              <i class="fa fa-cloud-download-alt mr10"></i>{__("Download")}
            </a>
          </div>
        </div>
      </script>
    {/if}
    <!-- Download Information -->

    <!-- Verification Documents -->
    {if $page == "admin"}
      <script id="verification-documents" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">{__("Verification Documents")}</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          {if $system['verification_docs_required']}
            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Documents")}
              </label>
              <div class="col-sm-9">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="section-title mb20">
                      {literal}{{#is_page}}{/literal}
                        {__("Company Incorporation File")}
                      {literal}{{/is_page}}{/literal}
                      {literal}{{^is_page}}{/literal}
                        {__("Personal Photo")}
                      {literal}{{/is_page}}{/literal}
                    </div>
                    <a target="_blank" href="{literal}{{photo}}{/literal}">
                      <img class="img-fluid" src="{literal}{{photo}}{/literal}">
                    </a>
                  </div>
                  <div class="col-sm-6">
                    <div class="section-title mb20">
                      {literal}{{#is_page}}{/literal}
                        {__("Company Tax File")}
                      {literal}{{/is_page}}{/literal}
                      {literal}{{^is_page}}{/literal}
                        {__("Passport or National ID")}
                      {literal}{{/is_page}}{/literal}
                    </div>
                    <a target="_blank" href="{literal}{{passport}}{/literal}">
                      <img class="img-fluid" src="{literal}{{passport}}{/literal}">
                    </a>
                  </div>
                </div>
              </div>
            </div>
          {/if}
          {literal}{{#is_page}}{/literal}
            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Business Website")}
              </label>
              <div class="col-sm-9">
                <p class="pt5 pb0">{literal}{{website}}{/literal}</p>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-3 form-label">
                {__("Business Address")}
              </label>
              <div class="col-sm-9">
                <p class="pt5 pb0">{literal}{{address}}{/literal}</p>
              </div>
            </div>
          {literal}{{/is_page}}{/literal}
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Message")}
            </label>
            <div class="col-sm-9">
              <p class="pt5 pb0">{literal}{{message}}{/literal}</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-danger js_admin-unverify" data-handle="{literal}{{handle}}{/literal}" data-id="{literal}{{node-id}}{/literal}">
            <i class="fa fa-times mr5"></i>{__("Decline")}
          </button>
          <button class="btn btn-success js_admin-verify" data-handle="{literal}{{handle}}{/literal}" data-id="{literal}{{node-id}}{/literal}">
            <i class="fa fa-check mr5"></i>{__("Verify")}
          </button>
        </div>
      </script>
    {/if}
    <!-- Verification Documents -->

    <!-- Export CSV -->
    {if $page == "admin"}
      <script id="export-csv" type="text/template">
        <div class="modal-header">
          <h6 class="modal-title">{__("Export CSV")}</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="js_ajax-forms" data-url="admin/export.php">
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label" for="from_row">{__("Row to begin at")}</label>
              <input type="text" class="form-control" name="from_row" id="from_row" value="0" required autofocus>
            </div>
            <div class="form-group">
              <label class="form-label" for="results">{__("Number of rows")}</label>
              <input type="text" class="form-control" name="results" id="results" value="10" required>
              <div class="form-text">
                {__("Set to 0 to export all results")}
              </div>
            </div>
            <div class="form-group">
            <label class="form-label" for="to">{__("Date range")}</label>
              <div class="input-group">
                <input type="date" class="form-control" name="from" value="{literal}{{from}}{/literal}">
                <input type="date" class="form-control" name="to" value="{literal}{{to}}{/literal}">
              </div>
            </div>
            <!-- error -->
            <div class="alert alert-danger mb0 mt10 x-hidden"></div>
            <!-- error -->
          </div>
          <div class="modal-footer">
            <input type="hidden" name="handle" value="{literal}{{handle}}{/literal}">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{__("Cancel")}</button>
            <button type="submit" class="btn btn-success">{__("Export")}</button>
          </div>
        </form>
      </script>
    {/if}
    <!-- Export CSV -->

    <!-- Auto Connect -->
    {if $page == "admin" && $view == "tools" && $sub_view == "auto-connect"}
      <script id="auto-connect-node" type="text/template">
        <div class="auto-connect-node">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Select")} {literal}{{nodes_name}}{/literal}
            </label>
            <div class="col-md-9">
              <select class="form-select mb10" name="{literal}{{country_field_name}}{/literal}">
                {foreach $countries as $country}
                  <option value="{$country['country_id']}">{$country['country_name']}</option>
                {/foreach}
              </select>
              <input type="text" class="js_tagify-ajax-late x-hidden" data-handle="{literal}{{nodes}}{/literal}" name="{literal}{{nodes_field_name}}{/literal}">
              <div class="form-text">
                {__("Search for nodes you want new accounts to auto connect")} {__("to this country")}
              </div>
            </div>
          </div>
        </div>
      </script>
    {/if}
    <!-- Auto Connect -->

    <!-- Blog reference -->
    {if $page == "blogs" && in_array($view, ['edit', 'new'])}
      <script id="blog-reference" type="text/template">
        <div class="auto-connect-node">
          <div class="row form-group">
            <label class="col-md-2 form-label">
              {__("reference")}
            </label>
            <div class="col-md-10">
              <input type="text" class="form-control mb10" name="references[{literal}{{index}}{/literal}][title]" placeholder="{__('Title')}">
              <input type="text" class="form-control mb10" name="references[{literal}{{index}}{/literal}][link]" placeholder="{__('Link')}">
              <div class="form-text">
                {__("Add a reference title and link to cite your source")}
              </div>
            </div>
          </div>
        </div>
      </script>
    {/if}
    <!-- Blog reference -->

    {if $licence_monetization && is_file("content/themes/`$system.theme`/templates/_js_templates.payments.tpl")}
      {include file='_js_templates.payments.tpl'}
    {/if}

  {/if}

{/strip}