{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- page header -->
<div class="page-header">
  <div class="circle-2"></div>
  <div class="circle-3"></div>
  <div class="inner">
    <h2>🇰🇷 {__("कोरिया भिसा केन्द्र")}</h2>
    <p class="text-xlg">{__("Korea Visa Information Center")}</p>
  </div>
</div>
<!-- /page header -->

<!-- page content -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} sg-offcanvas" style="margin-top: -25px;">
  <div class="row">

    <!-- mobile sidebar -->
    <div class="col-12 d-block d-md-none sg-offcanvas-sidebar mt20">
      {include file='_sidebar.tpl'}
    </div>
    <!-- /mobile sidebar -->

    <!-- main content -->
    <div class="col-12 col-md-8 col-lg-9 sg-offcanvas-mainbar">

      <!-- intro card -->
      <div class="card mb20">
        <div class="card-body">
          <div class="alert alert-info mb0">
            <div class="icon"><i class="fa fa-info-circle fa-2x"></i></div>
            <div class="text pt5">
              {__("यहाँ नेपालीहरूका लागि सबैभन्दा सामान्य कोरिया भिसाहरूको जानकारी छ। थप विवरणका लागि कोरियाली दूतावास वा Hi Korea वेबसाइट भ्रमण गर्नुहोस्।")}
            </div>
          </div>
        </div>
      </div>
      <!-- /intro card -->

      {foreach $visa_categories as $cat_np => $cat_en}
        <!-- category: {$cat_en} -->
        <div class="card mb20">
          <div class="card-header with-icon">
            <i class="fa fa-folder-open mr10"></i>{$cat_np} <small class="text-muted ml5">({$cat_en})</small>
          </div>
          <div class="card-body p0">

            {foreach $visas as $visa}
              {if $visa.cat == $cat_np}
                <div class="kv-visa-row">
                  <div class="kv-badge" style="background:{$visa.color}">{$visa.code}</div>
                  <div class="kv-info">
                    <div class="kv-name">
                      {$visa.name} <span class="kv-name-en">({$visa.name_en})</span>
                    </div>
                    <p class="kv-desc">{$visa.desc}</p>
                    <div class="kv-meta">
                      <i class="fa fa-clock-o mr5"></i>{__("अवधि")}: <strong>{$visa.stay}</strong>
                      <a href="{$visa.link}" target="_blank" rel="noopener" class="kv-ref-link ml15">
                        <i class="fa fa-external-link mr5"></i>{__("विस्तृत जानकारी")}
                      </a>
                    </div>
                  </div>
                </div>
              {/if}
            {/foreach}

          </div>
        </div>
        <!-- /category -->
      {/foreach}

      <!-- disclaimer -->
      <div class="card mb20">
        <div class="card-body">
          <p class="text-muted text-sm mb0">
            <i class="fa fa-exclamation-triangle mr5 text-warning"></i>
            {__("यो जानकारी केवल सूचनामूलक उद्देश्यका लागि हो। भिसा नियम परिवर्तन हुन सक्छन्। कुनै पनि निर्णय गर्नु अघि नेपालस्थित कोरियाली दूतावास वा आधिकारिक HiKorea.go.kr वेबसाइट हेर्नुहोस्।")}
          </p>
        </div>
      </div>
      <!-- /disclaimer -->

    </div>
    <!-- /main content -->

    <!-- desktop sidebar -->
    <div class="col-md-4 col-lg-3 d-none d-md-block">
      {include file='_sidebar.tpl'}
    </div>
    <!-- /desktop sidebar -->

  </div>
</div>
<!-- /page content -->

<style>
.kv-visa-row {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  padding: 18px 20px;
  border-bottom: 1px solid var(--border-color, #eee);
}
.kv-visa-row:last-child { border-bottom: none; }
.kv-badge {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 54px;
  height: 54px;
  border-radius: 10px;
  color: #fff;
  font-weight: 700;
  font-size: 13px;
  letter-spacing: .5px;
  text-align: center;
}
.kv-info { flex: 1; min-width: 0; }
.kv-name { font-weight: 600; font-size: 15px; margin-bottom: 4px; }
.kv-name-en { font-weight: 400; color: #888; font-size: 13px; }
.kv-desc { color: #666; font-size: 14px; margin: 4px 0 8px; line-height: 1.5; }
.kv-meta { font-size: 13px; color: #555; }
.kv-ref-link { color: #5e72e4; text-decoration: none; }
.kv-ref-link:hover { text-decoration: underline; }
body.night-mode .kv-visa-row { border-color: var(--border-dark-color, #333); }
body.night-mode .kv-desc,
body.night-mode .kv-meta { color: #aaa; }
</style>

{include file='_footer.tpl'}
