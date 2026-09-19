<div class="fm-thread-card">
  <!-- vote strip -->
  <div class="fm-vote-strip">
    <button class="fm-vote-btn fm-vote-up {if $thread['votes']['my_vote'] == 'up'}active{/if}"
            onclick="forumVote(this,'up','thread',{$thread['thread_id']})"
            title="{__('Upvote')}">▲</button>
    <span class="fm-vote-score" id="score-thread-{$thread['thread_id']}">{$thread['votes']['score']}</span>
    <button class="fm-vote-btn fm-vote-down {if $thread['votes']['my_vote'] == 'down'}active{/if}"
            onclick="forumVote(this,'down','thread',{$thread['thread_id']})"
            title="{__('Downvote')}">▼</button>
  </div>
  <!-- /vote strip -->

  <!-- card body -->
  <div class="fm-tc-body">
    <h3 class="fm-tc-title">
      <a href="{$system['system_url']}/forums/thread/{$thread['thread_id']}/{$thread['title_url']}">{$thread['title']}</a>
    </h3>
    <p class="fm-tc-snippet">{$thread['text_snippet']|truncate:150}</p>
    <div class="fm-tc-meta">
      <img src="{$thread['user_picture']}" class="fm-micro-avatar">
      <a href="{$system['system_url']}/{$thread['user_name']}" class="fm-tc-author">{$thread['user_fullname']}</a>
      <span class="fm-dot">·</span>
      <span class="fm-time js_moment" data-time="{$thread['time']}">{$thread['time']}</span>
    </div>
  </div>
  <!-- /card body -->

  <!-- stats -->
  <div class="fm-tc-stats">
    <div class="fm-tc-stat">
      <span class="fm-stat-val">{$thread['replies']|number_format:0}</span>
      <span class="fm-stat-lbl">{__("replies")}</span>
    </div>
    <div class="fm-tc-stat">
      <span class="fm-stat-val">{$thread['views']|number_format:0}</span>
      <span class="fm-stat-lbl">{__("views")}</span>
    </div>
  </div>
  <!-- /stats -->
</div>

<script>
function forumVote(btn, voteType, itemType, itemId) {
  {if !$user->_logged_in}
    window.location.href = '{$system['system_url']}/login';
    return;
  {/if}
  var scoreEl = document.getElementById('score-' + itemType + '-' + itemId);
  var col = btn.closest('.fm-vote-strip') || btn.closest('.fm-vote-col');
  fetch('{$system['system_url']}/includes/ajax/forums/vote.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: 'item_id=' + itemId + '&item_type=' + itemType + '&vote_type=' + voteType
  })
  .then(r => r.json())
  .then(data => {
    if (data.error) return;
    if (scoreEl) scoreEl.textContent = data.score;
    if (col) {
      col.querySelectorAll('.fm-vote-btn').forEach(b => b.classList.remove('active'));
      if (data.my_vote) col.querySelector('.fm-vote-' + data.my_vote).classList.add('active');
    }
  });
}
</script>
