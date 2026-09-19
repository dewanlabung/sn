{include file='_head.tpl'}
{include file='_header.tpl'}

<!-- FORUMS MODERN UI -->
<div class="{if $system['fluid_design']}container-fluid{else}container{/if} forum-modern-wrap">

  {* ================================================================ *}
  {* VIEW: HOME                                                        *}
  {* ================================================================ *}
  {if $view == ""}

    <!-- forum home header -->
    <div class="fm-page-hero">
      <div class="fm-page-hero-inner">
        <h1>{__("Forums")}</h1>
        <p>{__($system['system_description_forums'])}</p>
        {if $user->_logged_in}
          <a href="{$system['system_url']}/forums" class="fm-btn fm-btn-ghost">
            {include file='__svg_icons.tpl' icon="home" class="fm-icon" width="16px" height="16px"}
            {__("Browse Categories")}
          </a>
        {/if}
      </div>
    </div>
    <!-- /forum home header -->

    <div class="fm-layout">
      <!-- main column -->
      <div class="fm-main">

        <!-- tabs -->
        <div class="fm-tabs-bar">
          <ul class="fm-tabs">
            <li class="active"><a href="{$system['system_url']}/forums">{__("Home")}</a></li>
            {if $user->_logged_in}
              <li><a href="{$system['system_url']}/forums/my-threads">{__("My Threads")}</a></li>
              <li><a href="{$system['system_url']}/forums/my-replies">{__("My Replies")}</a></li>
            {/if}
            <li><a href="{$system['system_url']}/forums/search">{__("Search")}</a></li>
          </ul>
        </div>
        <!-- /tabs -->

        <!-- forum categories -->
        {foreach $forums as $forum}
          <div class="fm-category-card">
            <div class="fm-category-head">
              <div class="fm-category-icon">
                {include file='__svg_icons.tpl' icon="comments" class="fm-icon" width="20px" height="20px"}
              </div>
              <div class="fm-category-info">
                <a href="{$system['system_url']}/forums/{$forum['forum_id']}/{$forum['title_url']}" class="fm-category-name">{__($forum['forum_name'])}</a>
                {if $forum['forum_description']}
                  <p class="fm-category-desc">{__($forum['forum_description'])}</p>
                {/if}
              </div>
              <div class="fm-category-stats">
                <span>{$forum['total_threads']|number_format:0} <small>{__("threads")}</small></span>
                <span>{$forum['total_replies']|number_format:0} <small>{__("replies")}</small></span>
              </div>
            </div>
            {if $forum['childs']}
              <div class="fm-subforums">
                {foreach $forum['childs'] as $_forum}
                  <div class="fm-subforum-row">
                    <div class="fm-subforum-left">
                      <a href="{$system['system_url']}/forums/{$_forum['forum_id']}/{$_forum['title_url']}" class="fm-subforum-name">{__($_forum['forum_name'])}</a>
                      {if $_forum['forum_description']}
                        <p class="fm-subforum-desc">{__($_forum['forum_description'])|nl2br}</p>
                      {/if}
                      {if $_forum['childs']}
                        <div class="fm-subforums-list">
                          {foreach $_forum['childs'] as $__forum}
                            <a href="{$system['system_url']}/forums/{$__forum['forum_id']}/{$_forum['title_url']}" class="fm-tag">{__($__forum['forum_name'])}</a>
                          {/foreach}
                        </div>
                      {/if}
                    </div>
                    <div class="fm-subforum-meta">
                      <span class="fm-meta-badge">{$_forum['total_threads']|number_format:0} {__("threads")}</span>
                      <span class="fm-meta-badge">{$_forum['total_replies']|number_format:0} {__("replies")}</span>
                    </div>
                  </div>
                {/foreach}
              </div>
            {/if}
          </div>
        {/foreach}
        <!-- /forum categories -->

        <!-- what's going on -->
        {if $system['forums_online_enabled'] || $system['forums_statistics_enabled']}
          <div class="fm-card fm-whats-on">
            <div class="fm-card-head">{__("What's Going On?")}</div>
            {if $system['forums_online_enabled']}
              <div class="fm-online-row">
                <strong>{__("Who's online")}</strong>
                <span class="fm-badge fm-badge-primary">{count($online_users)|number_format:0}</span>
              </div>
              <div class="fm-online-users">
                {foreach $online_users as $_user}
                  <a href="{$system['system_url']}/{$_user['user_name']}" class="fm-online-chip">
                    <img src="{$_user['user_picture']}" class="fm-micro-avatar">
                    {if $system['show_usernames_enabled']}{$_user['user_name']}{else}{$_user['user_firstname']}{/if}
                  </a>
                {/foreach}
              </div>
            {/if}
            {if $system['forums_statistics_enabled']}
              <div class="fm-stats-row">
                <div class="fm-stat-item">
                  <span class="fm-stat-num">{$insights['threads']|number_format:0}</span>
                  <span class="fm-stat-label">{__("Threads")}</span>
                </div>
                <div class="fm-stat-item">
                  <span class="fm-stat-num">{$insights['replies']|number_format:0}</span>
                  <span class="fm-stat-label">{__("Replies")}</span>
                </div>
                <div class="fm-stat-item">
                  <span class="fm-stat-num">{$insights['users']|number_format:0}</span>
                  <span class="fm-stat-label">{__("Members")}</span>
                </div>
              </div>
            {/if}
          </div>
        {/if}
        <!-- /what's going on -->

      </div>
      <!-- /main column -->

      <!-- sidebar -->
      <div class="fm-sidebar">
        {if $top_contributors}
          <div class="fm-widget">
            <div class="fm-widget-head">{__("Top Contributors")}</div>
            {foreach $top_contributors as $idx => $contrib}
              <div class="fm-contributor-row">
                <span class="fm-contrib-rank">{$idx+1}</span>
                <img src="{$contrib['user_picture']}" class="fm-sm-avatar">
                <div class="fm-contrib-info">
                  <a href="{$system['system_url']}/{$contrib['user_name']}" class="fm-contrib-name">
                    {$contrib['user_fullname']}
                    {if $contrib['user_verified'] == '1'}<span class="fm-verified">✓</span>{/if}
                  </a>
                  <span class="fm-contrib-meta">{$contrib['thread_count']}t · {$contrib['reply_count']}r</span>
                </div>
              </div>
            {/foreach}
          </div>
        {/if}
        {include file='_sidebar.tpl'}
      </div>
      <!-- /sidebar -->
    </div>


  {* ================================================================ *}
  {* VIEW: FORUM (thread list)                                         *}
  {* ================================================================ *}
  {elseif $view == "forum"}

    <!-- breadcrumb -->
    <ol class="fm-breadcrumb">
      <li><a href="{$system['system_url']}/forums">{__("Forums")}</a></li>
      {if $forum['parents']}
        {foreach array_reverse($forum['parents']) as $parent}
          <li><a href="{$system['system_url']}/forums/{$parent['forum_id']}/{$parent['title_url']}">{__($parent['forum_name'])}</a></li>
        {/foreach}
      {/if}
      <li class="active">{__($forum['forum_name'])}</li>
    </ol>
    <!-- /breadcrumb -->

    <div class="fm-layout">
      <div class="fm-main">

        <!-- forum header card -->
        <div class="fm-forum-hero">
          <div class="fm-forum-hero-text">
            <h2>{__($forum['forum_name'])}</h2>
            {if $forum['forum_description']}<p>{__($forum['forum_description']|nl2br)}</p>{/if}
          </div>
          {if $forum['forum_section'] != '0' && $user->_logged_in}
            <a href="{$system['system_url']}/forums/new-thread/{$forum['forum_id']}" class="fm-btn fm-btn-primary">
              + {__("New Thread")}
            </a>
          {/if}
        </div>
        <!-- /forum header card -->

        <!-- sub-forums -->
        {if $forum['childs']}
          <div class="fm-category-card">
            <div class="fm-card-head">{if $forum['forum_section'] == '0'}{__("Forums")}{else}{__("Sub-Forums")}{/if}</div>
            {foreach $forum['childs'] as $_forum}
              <div class="fm-subforum-row">
                <div class="fm-subforum-left">
                  <a href="{$system['system_url']}/forums/{$_forum['forum_id']}/{$_forum['title_url']}" class="fm-subforum-name">{__($_forum['forum_name'])}</a>
                  {if $_forum['forum_description']}<p class="fm-subforum-desc">{__($_forum['forum_description'])|nl2br}</p>{/if}
                </div>
                <div class="fm-subforum-meta">
                  <span class="fm-meta-badge">{$_forum['total_threads']|number_format:0} {__("threads")}</span>
                  <span class="fm-meta-badge">{$_forum['total_replies']|number_format:0} {__("replies")}</span>
                </div>
              </div>
            {/foreach}
          </div>
        {/if}
        <!-- /sub-forums -->

        <!-- sort tabs -->
        {if $forum['forum_section'] != '0'}
          <div class="fm-sort-bar">
            <div class="fm-sort-tabs">
              <a href="?sort=newest" class="fm-sort-tab {if $sort == 'newest' || !$sort}active{/if}">{__("Newest")}</a>
              <a href="?sort=hot"    class="fm-sort-tab {if $sort == 'hot'}active{/if}">{__("Hot 🔥")}</a>
              <a href="?sort=top"    class="fm-sort-tab {if $sort == 'top'}active{/if}">{__("Top")}</a>
              <a href="?sort=unanswered" class="fm-sort-tab {if $sort == 'unanswered'}active{/if}">{__("Unanswered")}</a>
            </div>
            {if $user->_logged_in}
              <a href="{$system['system_url']}/forums/new-thread/{$forum['forum_id']}" class="fm-btn fm-btn-sm fm-btn-primary">+ {__("Thread")}</a>
            {/if}
          </div>
        {/if}
        <!-- /sort tabs -->

        <!-- thread list -->
        {if $forum['forum_section'] != '0'}
          {if $forum['threads']}
            {foreach $forum['threads'] as $thread}
              {include file='__forum_thread_card.tpl'}
            {/foreach}
            {$pager}
          {else}
            <div class="fm-empty">
              <p>{if $sort == 'unanswered'}{__("No unanswered threads!")}{else}{__("No threads yet. Be the first to post!")}{/if}</p>
              {if $user->_logged_in}
                <a href="{$system['system_url']}/forums/new-thread/{$forum['forum_id']}" class="fm-btn fm-btn-primary">+ {__("New Thread")}</a>
              {/if}
            </div>
          {/if}
        {/if}
        <!-- /thread list -->

      </div>

      <!-- sidebar -->
      <div class="fm-sidebar">
        <div class="fm-widget">
          <div class="fm-widget-head">{__("Forum Stats")}</div>
          <div class="fm-widget-row">
            <span>{__("Threads")}</span>
            <strong>{$forum['total_threads']|number_format:0}</strong>
          </div>
          <div class="fm-widget-row">
            <span>{__("Replies")}</span>
            <strong>{$forum['total_replies']|number_format:0}</strong>
          </div>
        </div>
        {include file='_sidebar.tpl'}
      </div>
      <!-- /sidebar -->
    </div>


  {* ================================================================ *}
  {* VIEW: THREAD (single thread + replies)                            *}
  {* ================================================================ *}
  {elseif $view == "thread"}

    <!-- breadcrumb -->
    <ol class="fm-breadcrumb">
      <li><a href="{$system['system_url']}/forums">{__("Forums")}</a></li>
      {if $thread['forum']['parents']}
        {foreach array_reverse($thread['forum']['parents']) as $parent}
          <li><a href="{$system['system_url']}/forums/{$parent['forum_id']}/{$parent['title_url']}">{__($parent['forum_name'])}</a></li>
        {/foreach}
      {/if}
      <li><a href="{$system['system_url']}/forums/{$thread['forum']['forum_id']}/{$thread['forum']['title_url']}">{__($thread['forum']['forum_name'])}</a></li>
      <li class="active">{$thread['title']|truncate:50}</li>
    </ol>
    <!-- /breadcrumb -->

    <div class="fm-layout">
      <div class="fm-main">

        <!-- thread post -->
        <div class="fm-thread-post {if $thread['replies'] > 0 && $selected_page != 1}x-hidden{/if}">

          <!-- vote column -->
          <div class="fm-vote-col" data-item-id="{$thread['thread_id']}" data-item-type="thread">
            <button class="fm-vote-btn fm-vote-up {if $thread['votes']['my_vote'] == 'up'}active{/if}"
                    onclick="forumVote(this,'up','thread',{$thread['thread_id']})" title="{__('Upvote')}">
              ▲
            </button>
            <span class="fm-vote-score" id="score-thread-{$thread['thread_id']}">{$thread['votes']['score']}</span>
            <button class="fm-vote-btn fm-vote-down {if $thread['votes']['my_vote'] == 'down'}active{/if}"
                    onclick="forumVote(this,'down','thread',{$thread['thread_id']})" title="{__('Downvote')}">
              ▼
            </button>
          </div>
          <!-- /vote column -->

          <!-- thread content -->
          <div class="fm-thread-body">
            <div class="fm-thread-meta-top">
              <img src="{$thread['user_picture']}" class="fm-sm-avatar">
              <span class="fm-author">
                <a href="{$system['system_url']}/{$thread['user_name']}">{$thread['user_fullname']}</a>
                {if $thread['user_group'] == 1}
                  <span class="fm-role fm-role-admin">{__("Admin")}</span>
                {elseif $thread['user_group'] == 2}
                  <span class="fm-role fm-role-mod">{__("Mod")}</span>
                {/if}
              </span>
              <span class="fm-dot">·</span>
              <span class="fm-time js_moment" data-time="{$thread['time']}">{$thread['time']}</span>
              <span class="fm-dot">·</span>
              <span class="fm-views">{$thread['views']|number_format:0} {__("views")}</span>

              <!-- action buttons -->
              <div class="fm-thread-actions">
                {if $thread['manage_thread']}
                  <a href="{$system['system_url']}/forums/edit-thread/{$thread['thread_id']}" class="fm-action-btn" title="{__('Edit')}">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                  <button class="fm-action-btn js_delete-forum" data-handle="thread" data-id="{$thread['thread_id']}" title="{__('Delete')}">
                    <i class="fa fa-trash-alt"></i>
                  </button>
                {else}
                  <button class="fm-action-btn" data-toggle="modal" data-url="data/report.php?do=create&handle=forum_thread&id={$thread['thread_id']}" title="{__('Report')}">
                    <i class="fa fa-flag"></i>
                  </button>
                {/if}
              </div>
              <!-- /action buttons -->
            </div>

            <h1 class="fm-thread-title">{$thread['title']}</h1>

            <div class="fm-thread-text">
              {$thread['parsed_text']}
            </div>

            <div class="fm-thread-footer">
              <a href="{$system['system_url']}/forums/new-reply/{$thread['thread_id']}" class="fm-btn fm-btn-reply">
                <i class="fa fa-reply mr5"></i>{__("Reply")}
              </a>
              <span class="fm-footer-count">{$thread['replies']|number_format:0} {__("replies")}</span>
            </div>
          </div>
          <!-- /thread content -->

        </div>
        <!-- /thread post -->

        <!-- replies -->
        {if $thread['replies'] > 0}
          <div class="fm-replies-head">{$thread['replies']|number_format:0} {__("Replies")}</div>
          {foreach $thread['thread_replies'] as $reply}
            <div class="fm-reply-card" id="reply-{$reply['reply_id']}">

              <!-- vote column -->
              <div class="fm-vote-col" data-item-id="{$reply['reply_id']}" data-item-type="reply">
                <button class="fm-vote-btn fm-vote-up {if $reply['votes']['my_vote'] == 'up'}active{/if}"
                        onclick="forumVote(this,'up','reply',{$reply['reply_id']})" title="{__('Upvote')}">
                  ▲
                </button>
                <span class="fm-vote-score" id="score-reply-{$reply['reply_id']}">{$reply['votes']['score']}</span>
                <button class="fm-vote-btn fm-vote-down {if $reply['votes']['my_vote'] == 'down'}active{/if}"
                        onclick="forumVote(this,'down','reply',{$reply['reply_id']})" title="{__('Downvote')}">
                  ▼
                </button>
              </div>
              <!-- /vote column -->

              <!-- reply content -->
              <div class="fm-reply-body">
                <div class="fm-thread-meta-top">
                  <img src="{$reply['user_picture']}" class="fm-sm-avatar">
                  <span class="fm-author">
                    <a href="{$system['system_url']}/{$reply['user_name']}">{$reply['user_fullname']}</a>
                    {if $reply['user_group'] == 1}
                      <span class="fm-role fm-role-admin">{__("Admin")}</span>
                    {elseif $reply['user_group'] == 2}
                      <span class="fm-role fm-role-mod">{__("Mod")}</span>
                    {/if}
                  </span>
                  <span class="fm-dot">·</span>
                  <span class="fm-time js_moment" data-time="{$reply['time']}">{$reply['time']}</span>

                  <!-- reply actions -->
                  <div class="fm-thread-actions">
                    <a href="{$system['system_url']}/forums/thread/{$thread['thread_id']}/{$thread['title_url']}#reply-{$reply['reply_id']}"
                       class="fm-action-btn" title="{__('Link')}">
                      <i class="fa fa-link"></i>
                    </a>
                    {if $reply['manage_reply']}
                      <a href="{$system['system_url']}/forums/edit-reply/{$reply['reply_id']}" class="fm-action-btn" title="{__('Edit')}">
                        <i class="fa fa-pencil-alt"></i>
                      </a>
                      <button class="fm-action-btn js_delete-forum" data-handle="reply" data-id="{$reply['reply_id']}" title="{__('Delete')}">
                        <i class="fa fa-trash-alt"></i>
                      </button>
                    {else}
                      <button class="fm-action-btn" data-toggle="modal" data-url="data/report.php?do=create&handle=forum_reply&id={$reply['reply_id']}" title="{__('Report')}">
                        <i class="fa fa-flag"></i>
                      </button>
                    {/if}
                  </div>
                  <!-- /reply actions -->
                </div>

                <div class="fm-reply-text">
                  {$reply['parsed_text']}
                </div>
              </div>
              <!-- /reply content -->

            </div>
          {/foreach}
          <div class="fm-pager">{$pager}</div>
        {/if}
        <!-- /replies -->

        <!-- reply CTA -->
        {if $user->_logged_in}
          <div class="fm-reply-cta">
            <a href="{$system['system_url']}/forums/new-reply/{$thread['thread_id']}" class="fm-btn fm-btn-primary">
              <i class="fa fa-reply mr5"></i>{__("Post a Reply")}
            </a>
          </div>
        {/if}
        <!-- /reply CTA -->

      </div>

      <!-- sidebar -->
      <div class="fm-sidebar">
        <div class="fm-widget">
          <div class="fm-widget-head">{__("Thread Info")}</div>
          <div class="fm-widget-row"><span>{__("Views")}</span><strong>{$thread['views']|number_format:0}</strong></div>
          <div class="fm-widget-row"><span>{__("Replies")}</span><strong>{$thread['replies']|number_format:0}</strong></div>
          <div class="fm-widget-row"><span>{__("Forum")}</span><a href="{$system['system_url']}/forums/{$thread['forum']['forum_id']}/{$thread['forum']['title_url']}">{__($thread['forum']['forum_name'])}</a></div>
        </div>
        {include file='_sidebar.tpl'}
      </div>
      <!-- /sidebar -->
    </div>

    <!-- vote JS -->
    <script>
    function forumVote(btn, voteType, itemType, itemId) {
      {if !$user->_logged_in}
        window.location.href = '{$system['system_url']}/login';
        return;
      {/if}
      var scoreEl = document.getElementById('score-' + itemType + '-' + itemId);
      var col = btn.closest('.fm-vote-col');

      fetch('{$system['system_url']}/includes/ajax/forums/vote.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: 'item_id=' + itemId + '&item_type=' + itemType + '&vote_type=' + voteType
      })
      .then(r => r.json())
      .then(data => {
        if (data.error) return;
        scoreEl.textContent = data.score;
        col.querySelectorAll('.fm-vote-btn').forEach(b => b.classList.remove('active'));
        if (data.my_vote) {
          col.querySelector('.fm-vote-' + data.my_vote).classList.add('active');
        }
      });
    }
    </script>
    <!-- /vote JS -->


  {* ================================================================ *}
  {* VIEW: NEW THREAD                                                  *}
  {* ================================================================ *}
  {elseif $view == "new-thread"}

    <ol class="fm-breadcrumb">
      <li><a href="{$system['system_url']}/forums">{__("Forums")}</a></li>
      {if $forum['parents']}
        {foreach array_reverse($forum['parents']) as $parent}
          <li><a href="{$system['system_url']}/forums/{$parent['forum_id']}/{$parent['title_url']}">{__($parent['forum_name'])}</a></li>
        {/foreach}
      {/if}
      <li><a href="{$system['system_url']}/forums/{$forum['forum_id']}/{$forum['title_url']}">{__($forum['forum_name'])}</a></li>
      <li class="active">{__("New Thread")}</li>
    </ol>

    <div class="fm-form-card">
      <div class="fm-form-head">{__("Write New Thread")}</div>
      <form class="js_ajax-forms" data-url="forums/thread.php?do=create&id={$forum['forum_id']}">
        <div class="fm-form-body">
          <div class="fm-form-group">
            <label class="fm-label">{__("Title")}</label>
            <input class="fm-input" name="title" placeholder="{__('Enter thread title...')}">
          </div>
          <div class="fm-form-group">
            <label class="fm-label">{__("Content")}</label>
            <textarea name="text" class="fm-input fm-textarea js_wysiwyg" placeholder="{__('Write your thread content...')}"></textarea>
          </div>
          <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        </div>
        <div class="fm-form-footer">
          <button type="submit" class="fm-btn fm-btn-primary">{__("Publish")}</button>
          <a href="{$system['system_url']}/forums/{$forum['forum_id']}/{$forum['title_url']}" class="fm-btn fm-btn-ghost">{__("Cancel")}</a>
        </div>
      </form>
    </div>


  {* ================================================================ *}
  {* VIEW: EDIT THREAD                                                 *}
  {* ================================================================ *}
  {elseif $view == "edit-thread"}

    <ol class="fm-breadcrumb">
      <li><a href="{$system['system_url']}/forums">{__("Forums")}</a></li>
      <li><a href="{$system['system_url']}/forums/{$thread['forum']['forum_id']}/{$thread['forum']['title_url']}">{__($thread['forum']['forum_name'])}</a></li>
      <li><a href="{$system['system_url']}/forums/thread/{$thread['thread_id']}/{$thread['title_url']}">{$thread['title']|truncate:40}</a></li>
      <li class="active">{__("Edit")}</li>
    </ol>

    <div class="fm-form-card">
      <div class="fm-form-head">{__("Edit Thread")}</div>
      <form class="js_ajax-forms" data-url="forums/thread.php?do=edit&id={$thread['thread_id']}">
        <div class="fm-form-body">
          <div class="fm-form-group">
            <label class="fm-label">{__("Title")}</label>
            <input class="fm-input" name="title" value="{$thread['title']}">
          </div>
          <div class="fm-form-group">
            <label class="fm-label">{__("Content")}</label>
            <textarea name="text" class="fm-input fm-textarea js_wysiwyg">{$thread['text']}</textarea>
          </div>
          <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        </div>
        <div class="fm-form-footer">
          <button type="submit" class="fm-btn fm-btn-primary">{__("Update")}</button>
          <a href="{$system['system_url']}/forums/thread/{$thread['thread_id']}/{$thread['title_url']}" class="fm-btn fm-btn-ghost">{__("Cancel")}</a>
        </div>
      </form>
    </div>


  {* ================================================================ *}
  {* VIEW: NEW REPLY                                                   *}
  {* ================================================================ *}
  {elseif $view == "new-reply"}

    <ol class="fm-breadcrumb">
      <li><a href="{$system['system_url']}/forums">{__("Forums")}</a></li>
      <li><a href="{$system['system_url']}/forums/{$thread['forum']['forum_id']}/{$thread['forum']['title_url']}">{__($thread['forum']['forum_name'])}</a></li>
      <li><a href="{$system['system_url']}/forums/thread/{$thread['thread_id']}/{$thread['title_url']}">{$thread['title']|truncate:40}</a></li>
      <li class="active">{__("Reply")}</li>
    </ol>

    <div class="fm-form-card">
      <div class="fm-form-head">{__("Post Reply")} — <a href="{$system['system_url']}/forums/thread/{$thread['thread_id']}/{$thread['title_url']}">{$thread['title']}</a></div>
      <form class="js_ajax-forms" data-url="forums/reply.php?do=create&id={$thread['thread_id']}">
        <div class="fm-form-body">
          <div class="fm-form-group">
            <textarea name="text" class="fm-input fm-textarea js_wysiwyg" placeholder="{__('Write your reply...')}"></textarea>
          </div>
          <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        </div>
        <div class="fm-form-footer">
          <button type="submit" class="fm-btn fm-btn-primary">{__("Reply")}</button>
          <a href="{$system['system_url']}/forums/thread/{$thread['thread_id']}/{$thread['title_url']}" class="fm-btn fm-btn-ghost">{__("Cancel")}</a>
        </div>
      </form>
    </div>


  {* ================================================================ *}
  {* VIEW: EDIT REPLY                                                  *}
  {* ================================================================ *}
  {elseif $view == "edit-reply"}

    <ol class="fm-breadcrumb">
      <li><a href="{$system['system_url']}/forums">{__("Forums")}</a></li>
      <li><a href="{$system['system_url']}/forums/thread/{$reply['thread']['thread_id']}/{$reply['thread']['title_url']}">{$reply['thread']['title']|truncate:40}</a></li>
      <li class="active">{__("Edit Reply")}</li>
    </ol>

    <div class="fm-form-card">
      <div class="fm-form-head">{__("Edit Reply")}</div>
      <form class="js_ajax-forms" data-url="forums/reply.php?do=edit&id={$reply['reply_id']}">
        <div class="fm-form-body">
          <div class="fm-form-group">
            <textarea name="text" class="fm-input fm-textarea js_wysiwyg">{$reply['text']}</textarea>
          </div>
          <div class="alert alert-danger mt15 mb0 x-hidden"></div>
        </div>
        <div class="fm-form-footer">
          <button type="submit" class="fm-btn fm-btn-primary">{__("Update")}</button>
        </div>
      </form>
    </div>


  {* ================================================================ *}
  {* VIEW: MY THREADS / MY REPLIES / SEARCH / SEARCH-RESULTS          *}
  {* ================================================================ *}
  {elseif $view == "my-threads" || $view == "my-replies" || $view == "search" || $view == "search-results"}

    <ol class="fm-breadcrumb">
      <li><a href="{$system['system_url']}/forums">{__("Forums")}</a></li>
      <li class="active">
        {if $view == "my-threads"}{__("My Threads")}
        {elseif $view == "my-replies"}{__("My Replies")}
        {elseif $view == "search"}{__("Search")}
        {else}{__("Search Results")}{/if}
      </li>
    </ol>

    <div class="fm-layout">
      <div class="fm-main">

        {if $view == "search" || $view == "search-results"}
          <!-- search form -->
          <div class="fm-form-card">
            <form action="{$system['system_url']}/forums/search-results" method="get">
              <div class="fm-search-bar">
                <input class="fm-input" name="query" placeholder="{__('Search forums...')}" value="{if $query}{$query}{/if}" autofocus required>
                <select class="fm-select" name="type">
                  <option value="threads">{__("Threads")}</option>
                  <option value="replies">{__("Replies")}</option>
                </select>
                <select class="fm-select" name="forum">
                  <option value="all">{__("All Forums")}</option>
                  {foreach $forums as $forum}
                    {include file='admin.forums.recursive_options.tpl'}
                  {/foreach}
                </select>
                <button type="submit" class="fm-btn fm-btn-primary">{__("Search")}</button>
              </div>
              <div class="fm-form-body" style="padding-top:0">
                <label class="fm-checkbox-label">
                  <input type="checkbox" name="recursive"> {__("Also search in child forums")}
                </label>
              </div>
            </form>
          </div>
          <!-- /search form -->
        {/if}

        {if $view == "search-results"}
          <div class="fm-results-info">
            {__("Results for")} "<strong>{$query}</strong>" — <span class="fm-badge fm-badge-primary">{if $total}{$total}{else}0{/if}</span> {__("found")}
          </div>
        {/if}

        <!-- thread / reply list -->
        {if $view == "my-threads"}
          {assign var="items" value=$threads}
        {elseif $view == "my-replies"}
          {assign var="items" value=$replies}
        {elseif $view == "search-results" && $type == "threads"}
          {assign var="items" value=$results}
        {elseif $view == "search-results" && $type == "replies"}
          {assign var="items" value=$results}
        {/if}

        {if $items}
          {foreach $items as $item}
            <div class="fm-result-card">
              <div class="fm-result-meta">
                <span class="fm-time js_moment" data-time="{if $item['time']}{$item['time']}{else}{$item['thread']['time']}{/if}">
                  {if $item['time']}{$item['time']}{else}{$item['thread']['time']}{/if}
                </span>
                <span class="fm-dot">·</span>
                <a href="{$system['system_url']}/forums/{if $item['forum']}{$item['forum']['forum_id']}/{$item['forum']['title_url']}{elseif $item['thread']['forum']}{$item['thread']['forum']['forum_id']}/{$item['thread']['forum']['title_url']}{/if}" class="fm-tag">
                  {if $item['forum']}{__($item['forum']['forum_name'])}{elseif $item['thread']['forum']}{__($item['thread']['forum']['forum_name'])}{/if}
                </a>
              </div>
              <h3 class="fm-result-title">
                <a href="{$system['system_url']}/forums/thread/{if $item['thread_id']}{$item['thread_id']}/{$item['title_url']}{else}{$item['thread']['thread_id']}/{$item['thread']['title_url']}{/if}">
                  {if $item['title']}{$item['title']}{else}{$item['thread']['title']}{/if}
                </a>
              </h3>
              <p class="fm-result-snippet">{if $item['text_snippet']}{$item['text_snippet']|truncate:200}{/if}</p>
              <div class="fm-result-footer">
                <span>{if isset($item['replies'])}{$item['replies']|number_format:0} {__("replies")}{/if}</span>
                <span>{if isset($item['views'])}{$item['views']|number_format:0} {__("views")}{/if}</span>
              </div>
            </div>
          {/foreach}
          <div class="fm-pager">{$pager}</div>
        {elseif $view != "search"}
          {include file='_no_data.tpl'}
        {/if}
        <!-- /list -->

      </div>

      <div class="fm-sidebar">
        {include file='_sidebar.tpl'}
      </div>
    </div>

  {/if}

</div>
<!-- /FORUMS MODERN UI -->

{include file='_footer.tpl'}
