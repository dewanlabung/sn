<div class="card">

  {if $sub_view == "" || $sub_view == "users_ads"}
    {if $licence_monetization && is_file("content/themes/`$system.theme`/templates/admin.ads.users.tpl")}
      {include file='admin.ads.users.tpl'}
    {else}
      <div class="card-header with-icon">
        <i class="fa fa-bullseye mr10"></i>{__("Ads")}
        {if $sub_view == ""} &rsaquo; {__("Settings")}{/if}
        {if $sub_view == "users_ads"} &rsaquo; {__("Users Ads")}{/if}
      </div>
      <div class="card-body p-0">
        {include file='__licence_lock.tpl'}
      </div>
    {/if}

  {elseif $sub_view == "system_ads"}

    <div class="card-header with-icon">
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/ads/add" class="btn btn-md btn-primary">
          <i class="fa fa-plus mr5"></i>{__("Add New Ads")}
        </a>
      </div>
      <i class="fa fa-bullseye mr10"></i>{__("Ads")} &rsaquo; {__("System Ads")}
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover js_dataTable">
          <thead>
            <tr>
              <th>{__("ID")}</th>
              <th>{__("Title")}</th>
              <th>{__("Place")}</th>
              <th>{__("Date")}</th>
              <th>{__("Actions")}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $rows as $row}
              <tr>
                <td>{$row['ads_id']}</td>
                <td>{$row['title']}</td>
                <td>
                  {if $row['place'] == "home"}<i class='fa fa-home fa-fw mr5'></i>{__("Home Page")}{/if}
                  {if $row['place'] == "search"}<i class='fa fa-search fa-fw mr5'></i>{__("Search Page")}{/if}
                  {if $row['place'] == "people"}<i class='fa fa-users fa-fw mr5'></i>{__("Discover People Page")}{/if}
                  {if $row['place'] == "notifications"}<i class='fa fa-bell fa-fw mr5'></i>{__("Notifications Page")}{/if}
                  {if $row['place'] == "post"}<i class='fa fa-file-powerpoint fa-fw mr5'></i>{__("Post (Right Panel)")}{/if}
                  {if $row['place'] == "post_footer"}<i class='fa fa-file-powerpoint fa-fw mr5'></i>{__("Post (Footer)")}{/if}
                  {if $row['place'] == "photo"}<i class='fa fa-file-image fa-fw mr5'></i>{__("Photo Page")}{/if}
                  {if $row['place'] == "pages"}<i class='fa fa-flag fa-fw mr5'></i>{__("Pages")}{/if}
                  {if $row['place'] == "groups"}<i class='fa fa-users fa-fw mr5'></i>{__("Groups")}{/if}
                  {if $row['place'] == "directory"}<i class='fa fa-th-list fa-fw mr5'></i>{__("Directory Page")}{/if}
                  {if $row['place'] == "market"}<i class='fa fa-shopping-bag fa-fw mr5'></i>{__("Market Page")}{/if}
                  {if $row['place'] == "offers"}<i class='fa fa-tag fa-fw mr5'></i>{__("Offers Page")}{/if}
                  {if $row['place'] == "jobs"}<i class='fa fa-briefcase fa-fw mr5'></i>{__("Jobs Page")}{/if}
                  {if $row['place'] == "courses"}<i class='fa fa-book fa-fw mr5'></i>{__("Courses Page")}{/if}
                  {if $row['place'] == "movies"}<i class='fa fa-film fa-fw mr5'></i>{__("Movies Page")}{/if}
                  {if $row['place'] == "newfeed_1"}<i class='fa fa-newspaper fa-fw mr5'></i>{__("Posts Feed")} 1{/if}
                  {if $row['place'] == "newfeed_2"}<i class='fa fa-newspaper fa-fw mr5'></i>{__("Posts Feed")} 2{/if}
                  {if $row['place'] == "newfeed_3"}<i class='fa fa-newspaper fa-fw mr5'></i>{__("Posts Feed")} 3{/if}
                  {if $row['place'] == "blog"}<i class='fa fa-file-alt fa-fw mr5'></i>{__("Blog (Right Panel)")}{/if}
                  {if $row['place'] == "blog_footer"}<i class='fa fa-file-alt fa-fw mr5'></i>{__("Blog (Footer)")}{/if}
                  {if $row['place'] == "header"}<i class='fa fa-chevron-circle-up fa-fw mr5'></i>{__("Header")}{/if}
                  {if $row['place'] == "footer"}<i class='fa fa-chevron-circle-down fa-fw mr5'></i>{__("Footer")}{/if}
                </td>
                <td>{$row['time']|date_format:"%e %B %Y"}</td>
                <td>
                  <a data-bs-toggle="tooltip" title='{__("Edit")}' href="{$system['system_url']}/{$control_panel['url']}/ads/edit/{$row['ads_id']}" class="btn btn-sm btn-icon btn-rounded btn-primary">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                  <button data-bs-toggle="tooltip" title='{__("Delete")}' class="btn btn-sm btn-icon btn-rounded btn-danger js_admin-deleter" data-handle="ads_system" data-id="{$row['ads_id']}">
                    <i class="fa fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    </div>

  {elseif $sub_view == "edit"}

    <div class="card-header with-icon">
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/ads/system_ads" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
      <i class="fa fa-bullseye mr10"></i>{__("Ads")} &rsaquo; {$data['title']}
    </div>

    <form class="js_ajax-forms" data-url="admin/ads.php?do=edit&id={$data['ads_id']}">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Title")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="title" value="{$data['title']}">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Place")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="place" id="js_ads-place">
              <option {if $data['place'] == "home"}selected{/if} value="home">{__("Home")}</option>
              <option {if $data['place'] == "search"}selected{/if} value="search">{__("Search")}</option>
              <option {if $data['place'] == "people"}selected{/if} value="people">{__("Discover People")}</option>
              <option {if $data['place'] == "notifications"}selected{/if} value="notifications">{__("Notifications")}</option>
              <option {if $data['place'] == "post"}selected{/if} value="post">{__("Post (Right Panel)")}</option>
              <option {if $data['place'] == "post_footer"}selected{/if} value="post_footer">{__("Post (Footer)")}</option>
              <option {if $data['place'] == "photo"}selected{/if} value="photo">{__("Photo")}</option>
              <option {if $data['place'] == "pages"}selected{/if} value="pages">{__("Pages")}</option>
              <option {if $data['place'] == "groups"}selected{/if} value="groups">{__("Groups")}</option>
              <option {if $data['place'] == "directory"}selected{/if} value="directory">{__("Directory")}</option>
              <option {if $data['place'] == "market"}selected{/if} value="market">{__("Marketplace")}</option>
              <option {if $data['place'] == "offers"}selected{/if} value="offers">{__("Offers")}</option>
              <option {if $data['place'] == "jobs"}selected{/if} value="jobs">{__("Jobs")}</option>
              <option {if $data['place'] == "courses"}selected{/if} value="courses">{__("Courses")}</option>
              <option {if $data['place'] == "movies"}selected{/if} value="movies">{__("Movies")}</option>
              <option {if $data['place'] == "newfeed_1"}selected{/if} value="newfeed_1">{__("Posts Feed")} 1</option>
              <option {if $data['place'] == "newfeed_2"}selected{/if} value="newfeed_2">{__("Posts Feed")} 2</option>
              <option {if $data['place'] == "newfeed_3"}selected{/if} value="newfeed_3">{__("Posts Feed")} 3</option>
              <option {if $data['place'] == "blog"}selected{/if} value="blog">{__("Blog (Right Panel)")}</option>
              <option {if $data['place'] == "blog_footer"}selected{/if} value="blog_footer">{__("Blog (Footer)")}</option>
              <option {if $data['place'] == "header"}selected{/if} value="header">{__("Header")}</option>
              <option {if $data['place'] == "footer"}selected{/if} value="footer">{__("Footer")}</option>
            </select>
          </div>
        </div>

        <div id="js_selected-pages" {if !$data['ads_pages_ids']}class="x-hidden" {/if}>
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Select Pages")}
            </label>
            <div class="col-md-9">
              <input type="text" class="js_tagify-ajax x-hidden" data-handle="pages" name="ads_pages_ids" value="{$data['ads_pages_ids']}">
              <div class="form-text">
                {__("Search for pages you want to show this ads")}
              </div>
            </div>
          </div>
        </div>

        <div id="js_selected-groups" {if !$data['ads_groups_ids']}class="x-hidden" {/if}>
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Select Groups")}
            </label>
            <div class="col-md-9">
              <input type="text" class="js_tagify-ajax x-hidden" data-handle="groups" name="ads_groups_ids" value="{$data['ads_groups_ids']}">
              <div class="form-text">
                {__("Search for groups you want to show this ads")}
              </div>
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("HTML")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="message" rows="8">{$data['code']}</textarea>
          </div>
        </div>

        <!-- success -->
        <div class="alert alert-success mt15 mb0 x-hidden"></div>
        <!-- success -->

        <!-- error -->
        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        <!-- error -->
      </div>
      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>

  {elseif $sub_view == "add"}

    <div class="card-header with-icon">
      <div class="float-end">
        <a href="{$system['system_url']}/{$control_panel['url']}/ads/system_ads" class="btn btn-md btn-light">
          <i class="fa fa-arrow-circle-left mr5"></i>{__("Go Back")}
        </a>
      </div>
      <i class="fa fa-bullseye mr10"></i>{__("Ads")} &rsaquo; {__("Add New Ads")}
    </div>

    <form class="js_ajax-forms" data-url="admin/ads.php?do=add">
      <div class="card-body">
        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Title")}
          </label>
          <div class="col-md-9">
            <input class="form-control" name="title">
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("Place")}
          </label>
          <div class="col-md-9">
            <select class="form-select" name="place" id="js_ads-place">
              <option value="home">{__("Home")}</option>
              <option value="search">{__("Search")}</option>
              <option value="people">{__("Discover People")}</option>
              <option value="notifications">{__("Notifications")}</option>
              <option value="post">{__("Post (Right Panel)")}</option>
              <option value="post_footer">{__("Post (Footer)")}</option>
              <option value="photo">{__("Photo")}</option>
              <option value="pages">{__("Pages")}</option>
              <option value="groups">{__("Groups")}</option>
              <option value="directory">{__("Directory")}</option>
              <option value="market">{__("Marketplace")}</option>
              <option value="offers">{__("Offers")}</option>
              <option value="jobs">{__("Jobs")}</option>
              <option value="courses">{__("Courses")}</option>
              <option value="movies">{__("Movies")}</option>
              <option value="newfeed_1">{__("Posts Feed")} 1</option>
              <option value="newfeed_2">{__("Posts Feed")} 2</option>
              <option value="newfeed_3">{__("Posts Feed")} 3</option>
              <option value="blog">{__("Blog (Right Panel)")}</option>
              <option value="blog_footer">{__("Blog (Footer)")}</option>
              <option value="header">{__("Header")}</option>
              <option value="footer">{__("Footer")}</option>
            </select>
          </div>
        </div>

        <div id="js_selected-pages" class="x-hidden">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Select Pages")}
            </label>
            <div class="col-md-9">
              <input type="text" class="js_tagify-ajax x-hidden" data-handle="pages" name="ads_pages_ids">
              <div class="form-text">
                {__("Search for pages you want to show this ads")}
              </div>
            </div>
          </div>
        </div>

        <div id="js_selected-groups" class="x-hidden">
          <div class="row form-group">
            <label class="col-md-3 form-label">
              {__("Select Groups")}
            </label>
            <div class="col-md-9">
              <input type="text" class="js_tagify-ajax x-hidden" data-handle="groups" name="ads_groups_ids">
              <div class="form-text">
                {__("Search for groups you want to show this ads")}
              </div>
            </div>
          </div>
        </div>

        <div class="row form-group">
          <label class="col-md-3 form-label">
            {__("HTML")}
          </label>
          <div class="col-md-9">
            <textarea class="form-control" name="message" rows="8"></textarea>
          </div>
        </div>

        <!-- success -->
        <div class="alert alert-success mt15 mb0 x-hidden"></div>
        <!-- success -->

        <!-- error -->
        <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        <!-- error -->
      </div>
      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">{__("Save Changes")}</button>
      </div>
    </form>

  {/if}

</div>