# Forum → Reddit/Quora Transformation Plan

**Project**: Sngine Forum → Reddit/Quora Hybrid  
**Date**: 2026-09-19  
**Repo**: dewanlabung/sn  

---

## Vision

Transform the current **hierarchical forum** (Categories → Sub-forums → Threads → Replies)
into a **Reddit/Quora hybrid**:

| Current | New |
|---------|-----|
| Forum categories | Topics/Communities |
| Threads | Posts/Questions |
| Replies | Comments/Answers |
| No voting | Upvote/Downvote |
| Newest sort | Hot / New / Top / Rising |
| No best answer | Accepted Answer (Quora) |
| No karma | User reputation/karma |

---

## Phase 1: Database Migration

### 1a. Add vote columns to threads

```sql
ALTER TABLE forums_threads
  ADD COLUMN `upvotes`   INT UNSIGNED DEFAULT 0 AFTER `views`,
  ADD COLUMN `downvotes` INT UNSIGNED DEFAULT 0 AFTER `upvotes`,
  ADD COLUMN `score`     INT DEFAULT 0 AFTER `downvotes`,
  ADD COLUMN `post_type` ENUM('post','question') DEFAULT 'post' AFTER `score`,
  ADD COLUMN `solved`    TINYINT(1) DEFAULT 0 AFTER `post_type`,
  ADD COLUMN `best_reply_id` INT UNSIGNED NULL AFTER `solved`,
  ADD COLUMN `tags`      VARCHAR(512) DEFAULT '' AFTER `best_reply_id`;
```

### 1b. Add vote columns to replies

```sql
ALTER TABLE forums_replies
  ADD COLUMN `upvotes`   INT UNSIGNED DEFAULT 0 AFTER `text`,
  ADD COLUMN `downvotes` INT UNSIGNED DEFAULT 0 AFTER `upvotes`,
  ADD COLUMN `score`     INT DEFAULT 0 AFTER `downvotes`;
```

### 1c. Create votes table

```sql
CREATE TABLE `forums_votes` (
  `vote_id`   INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `user_id`   INT UNSIGNED NOT NULL,
  `item_type` ENUM('thread','reply') NOT NULL,
  `item_id`   INT UNSIGNED NOT NULL,
  `vote`      TINYINT(1) NOT NULL,     -- 1=upvote, -1=downvote
  `time`      DATETIME DEFAULT NOW(),
  UNIQUE KEY `unique_vote` (`user_id`, `item_type`, `item_id`),
  KEY `idx_item` (`item_type`, `item_id`)
);
```

### 1d. Create tags/topics table (subreddits equivalent)

```sql
CREATE TABLE `forums_tags` (
  `tag_id`       INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `tag_name`     VARCHAR(100) NOT NULL UNIQUE,
  `tag_slug`     VARCHAR(100) NOT NULL UNIQUE,
  `tag_desc`     TEXT,
  `tag_color`    VARCHAR(7) DEFAULT '#6366f1',
  `tag_posts`    INT UNSIGNED DEFAULT 0,
  `tag_followers`INT UNSIGNED DEFAULT 0,
  KEY `idx_slug` (`tag_slug`)
);

CREATE TABLE `forums_tag_follows` (
  `user_id` INT UNSIGNED NOT NULL,
  `tag_id`  INT UNSIGNED NOT NULL,
  `time`    DATETIME DEFAULT NOW(),
  PRIMARY KEY (`user_id`, `tag_id`)
);
```

### 1e. Add karma to users

```sql
ALTER TABLE users ADD COLUMN `forum_karma` INT DEFAULT 0 AFTER `user_verified`;
```

---

## Phase 2: Backend — ForumsTrait Additions

### File: `includes/traits/forums.php`

#### 2a. Hot score ranking (Reddit algorithm)

```php
/**
 * Calculate Reddit-style hot score
 */
private function hot_score(int $ups, int $downs, string $time): float {
    $score = $ups - $downs;
    $order = log(max(abs($score), 1), 10);
    $sign  = $score > 0 ? 1 : ($score < 0 ? -1 : 0);
    $seconds = strtotime($time) - 1134028003;
    return round($sign * $order + $seconds / 45000, 7);
}
```

#### 2b. Vote on thread or reply

```php
/**
 * vote_forum_item
 * @param string $type 'thread'|'reply'
 * @param int    $item_id
 * @param int    $vote   1=up, -1=down
 */
public function vote_forum_item(string $type, int $item_id, int $vote): array {
    global $db;

    $table = $type === 'thread' ? 'forums_threads' : 'forums_replies';
    $pk    = $type === 'thread' ? 'thread_id'      : 'reply_id';

    // Check existing vote
    $existing = $db->query(sprintf(
        "SELECT vote FROM forums_votes WHERE user_id=%d AND item_type='%s' AND item_id=%d",
        secure($this->_data['user_id'], 'int'),
        secure($type),
        secure($item_id, 'int')
    ))->fetch_assoc();

    if ($existing) {
        if ($existing['vote'] == $vote) {
            // Undo vote
            $db->query(sprintf(
                "DELETE FROM forums_votes WHERE user_id=%d AND item_type='%s' AND item_id=%d",
                secure($this->_data['user_id'], 'int'), secure($type), secure($item_id, 'int')
            ));
            $col = $vote === 1 ? 'upvotes' : 'downvotes';
            $db->query(sprintf(
                "UPDATE $table SET $col = IF($col=0,0,$col-1), score=upvotes-downvotes WHERE $pk=%d",
                secure($item_id, 'int')
            ));
            return ['action' => 'removed', 'vote' => 0];
        } else {
            // Change vote
            $db->query(sprintf(
                "UPDATE forums_votes SET vote=%d WHERE user_id=%d AND item_type='%s' AND item_id=%d",
                $vote, secure($this->_data['user_id'], 'int'), secure($type), secure($item_id, 'int')
            ));
            if ($vote === 1) {
                $db->query(sprintf("UPDATE $table SET upvotes=upvotes+1, downvotes=IF(downvotes=0,0,downvotes-1), score=upvotes-downvotes WHERE $pk=%d", secure($item_id, 'int')));
            } else {
                $db->query(sprintf("UPDATE $table SET downvotes=downvotes+1, upvotes=IF(upvotes=0,0,upvotes-1), score=upvotes-downvotes WHERE $pk=%d", secure($item_id, 'int')));
            }
            return ['action' => 'changed', 'vote' => $vote];
        }
    }

    // New vote
    $db->query(sprintf(
        "INSERT INTO forums_votes (user_id, item_type, item_id, vote) VALUES (%d,'%s',%d,%d)",
        secure($this->_data['user_id'], 'int'), secure($type), secure($item_id, 'int'), $vote
    ));
    $col = $vote === 1 ? 'upvotes' : 'downvotes';
    $db->query(sprintf("UPDATE $table SET $col=$col+1, score=upvotes-downvotes WHERE $pk=%d", secure($item_id, 'int')));

    return ['action' => 'voted', 'vote' => $vote];
}
```

#### 2c. Get user's vote on item

```php
public function get_user_vote(string $type, int $item_id): int {
    global $db;
    if (!$this->_logged_in) return 0;
    $r = $db->query(sprintf(
        "SELECT vote FROM forums_votes WHERE user_id=%d AND item_type='%s' AND item_id=%d",
        secure($this->_data['user_id'], 'int'), secure($type), secure($item_id, 'int')
    ))->fetch_assoc();
    return $r ? (int)$r['vote'] : 0;
}
```

#### 2d. Mark best answer (Quora)

```php
public function mark_best_answer(int $thread_id, int $reply_id): void {
    global $db;
    $thread = $this->get_forum_thread($thread_id);
    if (!$thread || $thread['user_id'] != $this->_data['user_id']) return;
    $db->query(sprintf(
        "UPDATE forums_threads SET best_reply_id=%d, solved=1 WHERE thread_id=%d",
        secure($reply_id, 'int'), secure($thread_id, 'int')
    ));
}
```

#### 2e. Modify `get_forum_threads` for Reddit-style sort

```php
// Add to $args handling in get_forum_threads():
$sort = $args['sort'] ?? 'hot';

$order_by = match($sort) {
    'hot'     => 'score DESC, last_reply DESC',
    'new'     => 'time DESC',
    'top'     => 'upvotes DESC',
    'rising'  => 'views DESC, time DESC',
    'q&a'     => 'solved ASC, upvotes DESC',  // unanswered questions first
    default   => 'score DESC'
};
```

#### 2f. Tags/Topics methods

```php
public function get_all_tags(int $limit = 20): array {
    global $db;
    $result = $db->query("SELECT * FROM forums_tags ORDER BY tag_posts DESC LIMIT $limit");
    $tags = [];
    while ($t = $result->fetch_assoc()) $tags[] = $t;
    return $tags;
}

public function get_threads_by_tag(string $slug, array $args = []): array {
    // Search forums_threads.tags LIKE '%slug%' with pagination
    // Returns same format as get_forum_threads()
}

public function follow_tag(int $tag_id): void {
    global $db;
    $db->query(sprintf(
        "INSERT IGNORE INTO forums_tag_follows (user_id, tag_id) VALUES (%d, %d)",
        secure($this->_data['user_id'], 'int'), secure($tag_id, 'int')
    ));
    $db->query(sprintf("UPDATE forums_tags SET tag_followers=tag_followers+1 WHERE tag_id=%d", secure($tag_id, 'int')));
}
```

---

## Phase 3: New AJAX Endpoints

### File: `includes/ajax/forums/vote.php`

```php
<?php
require('../../../bootloader.php');
if (!$user->_logged_in) exit(json_encode(['error' => 'Login required']));

$type    = in_array($_POST['type'], ['thread', 'reply']) ? $_POST['type'] : null;
$item_id = (int)$_POST['id'];
$vote    = (int)$_POST['vote']; // 1 or -1

if (!$type || !$item_id || !in_array($vote, [1, -1])) {
    exit(json_encode(['error' => 'Invalid params']));
}

$result = $user->vote_forum_item($type, $item_id, $vote);
// Recalc score for display
$table = $type === 'thread' ? 'forums_threads' : 'forums_replies';
$pk    = $type === 'thread' ? 'thread_id' : 'reply_id';
$item  = $db->query(sprintf("SELECT upvotes, downvotes, score FROM $table WHERE $pk=%d", $item_id))->fetch_assoc();

echo json_encode([
    'callback'  => 'vote_updated',
    'action'    => $result['action'],
    'vote'      => $result['vote'],
    'upvotes'   => (int)$item['upvotes'],
    'downvotes' => (int)$item['downvotes'],
    'score'     => (int)$item['score'],
]);
```

### File: `includes/ajax/forums/best-answer.php`

```php
<?php
require('../../../bootloader.php');
if (!$user->_logged_in) exit;
$thread_id = (int)$_POST['thread_id'];
$reply_id  = (int)$_POST['reply_id'];
$user->mark_best_answer($thread_id, $reply_id);
echo json_encode(['callback' => 'reload']);
```

---

## Phase 4: Template Changes

### File: `content/themes/default/templates/forums.tpl`

#### 4a. Replace thread list row with Reddit-style card

**Current** (table row with title + reply count):
```html
<tr><td>{thread.title}</td><td>{thread.replies}</td></tr>
```

**New** (Reddit-style card):
```html
{foreach $threads as $thread}
<div class="forum-post-card {if $thread.solved}solved{/if}">
  <!-- Vote Column -->
  <div class="vote-col">
    <button class="vote-btn upvote {if $thread.user_vote == 1}active{/if}"
            data-type="thread" data-id="{$thread.thread_id}" data-vote="1">▲</button>
    <span class="vote-score" id="score-thread-{$thread.thread_id}">{$thread.score}</span>
    <button class="vote-btn downvote {if $thread.user_vote == -1}active{/if}"
            data-type="thread" data-id="{$thread.thread_id}" data-vote="-1">▼</button>
  </div>

  <!-- Content Column -->
  <div class="post-content">
    {if $thread.post_type == 'question'}
      <span class="post-type-badge question">❓ Question</span>
    {/if}
    {if $thread.solved}
      <span class="post-type-badge solved">✅ Answered</span>
    {/if}

    <h3><a href="/forums/thread/{$thread.thread_id}/{$thread.title_url}">{$thread.title}</a></h3>

    <div class="post-meta">
      <img src="{$thread.user_picture}" class="avatar-xs">
      <a href="/profile/{$thread.user_name}">{$thread.user_fullname}</a>
      · <span>{$thread.time|time_ago}</span>
      · <span>💬 {$thread.replies} comments</span>
      · <span>👁 {$thread.views}</span>
    </div>

    {if $thread.tags}
    <div class="post-tags">
      {foreach explode(',', $thread.tags) as $tag}
        <a href="/forums/tag/{$tag|trim}" class="tag-pill">#{$tag|trim}</a>
      {/foreach}
    </div>
    {/if}
  </div>
</div>
{/foreach}
```

#### 4b. Sort tabs (Hot / New / Top / Rising / Q&A)

```html
<div class="forum-sort-tabs">
  <a href="?sort=hot"    class="{if $sort=='hot'}active{/if}">🔥 Hot</a>
  <a href="?sort=new"    class="{if $sort=='new'}active{/if}">✨ New</a>
  <a href="?sort=top"    class="{if $sort=='top'}active{/if}">⬆️ Top</a>
  <a href="?sort=rising" class="{if $sort=='rising'}active{/if}">📈 Rising</a>
  <a href="?sort=q&a"    class="{if $sort=='q&a'}active{/if}">❓ Q&A</a>
</div>
```

#### 4c. Answer card with "Mark as Best Answer" (Quora style)

```html
{foreach $replies as $reply}
<div class="answer-card {if $reply.reply_id == $thread.best_reply_id}best-answer{/if}">
  {if $reply.reply_id == $thread.best_reply_id}
    <div class="best-answer-badge">✅ Best Answer</div>
  {/if}

  <!-- Vote on answer -->
  <div class="answer-votes">
    <button class="vote-btn upvote" data-type="reply" data-id="{$reply.reply_id}" data-vote="1">▲</button>
    <span class="vote-score">{$reply.score}</span>
    <button class="vote-btn downvote" data-type="reply" data-id="{$reply.reply_id}" data-vote="-1">▼</button>
  </div>

  <div class="answer-body">
    {$reply.text}
    {if $thread.user_id == $user._data.user_id && !$thread.solved}
      <button class="mark-best-btn" data-thread="{$thread.thread_id}" data-reply="{$reply.reply_id}">
        ✅ Mark as Best Answer
      </button>
    {/if}
  </div>
</div>
{/foreach}
```

#### 4d. Tags sidebar widget

```html
<div class="sidebar-widget topics-widget">
  <h4>🏷️ Popular Topics</h4>
  {foreach $popular_tags as $tag}
  <a href="/forums/tag/{$tag.tag_slug}" class="topic-row">
    <span class="topic-dot" style="background:{$tag.tag_color}"></span>
    <span class="topic-name">{$tag.tag_name}</span>
    <span class="topic-count">{$tag.tag_posts}</span>
  </a>
  {/foreach}
</div>
```

---

## Phase 5: JavaScript — Vote Handler

### Add to `forums.tpl` or a new `forums.js`

```javascript
// Vote buttons
document.querySelectorAll('.vote-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    if (!window._isLoggedIn) { alert('Please login to vote'); return; }
    const type  = this.dataset.type;
    const id    = this.dataset.id;
    const vote  = parseInt(this.dataset.vote);

    fetch('/includes/ajax/forums/vote.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `type=${type}&id=${id}&vote=${vote}`
    })
    .then(r => r.json())
    .then(data => {
      // Update score display
      document.getElementById(`score-${type}-${id}`).textContent = data.score;
      // Toggle active state
      const card = this.closest('.vote-col, .answer-votes');
      card.querySelectorAll('.vote-btn').forEach(b => b.classList.remove('active'));
      if (data.vote !== 0) this.classList.add('active');
    });
  });
});

// Mark best answer
document.querySelectorAll('.mark-best-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    fetch('/includes/ajax/forums/best-answer.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `thread_id=${this.dataset.thread}&reply_id=${this.dataset.reply}`
    }).then(() => location.reload());
  });
});
```

---

## Phase 6: CSS Additions

```css
/* Reddit/Quora style cards */
.forum-post-card {
  display: flex; gap: 12px;
  background: #fff; border: 1px solid #e5e7eb;
  border-radius: 8px; padding: 12px;
  margin-bottom: 8px; transition: border-color .2s;
}
.forum-post-card:hover { border-color: #6366f1; }
.forum-post-card.solved { border-left: 3px solid #10b981; }

.vote-col { display: flex; flex-direction: column; align-items: center; min-width: 40px; }
.vote-btn { background: none; border: none; cursor: pointer; font-size: 18px; color: #9ca3af; padding: 2px 6px; }
.vote-btn:hover, .vote-btn.active { color: #6366f1; }
.vote-btn.active.downvote { color: #ef4444; }
.vote-score { font-weight: 700; font-size: 14px; }

.post-type-badge { font-size: 11px; padding: 2px 6px; border-radius: 9999px; margin-right: 4px; }
.post-type-badge.question { background: #ede9fe; color: #7c3aed; }
.post-type-badge.solved   { background: #d1fae5; color: #065f46; }

.forum-sort-tabs { display: flex; gap: 4px; margin-bottom: 16px; }
.forum-sort-tabs a { padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: 500; color: #6b7280; }
.forum-sort-tabs a.active { background: #6366f1; color: #fff; }

.tag-pill { display: inline-block; background: #f3f4f6; border-radius: 9999px; padding: 2px 8px; font-size: 12px; color: #4b5563; margin-right: 4px; text-decoration: none; }
.tag-pill:hover { background: #e0e7ff; color: #4f46e5; }

.best-answer-badge { background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 6px; font-weight: 600; margin-bottom: 8px; }
.best-answer { border: 2px solid #10b981; border-radius: 8px; }

.topic-row { display: flex; align-items: center; gap: 8px; padding: 6px 0; text-decoration: none; color: inherit; }
.topic-dot { width: 12px; height: 12px; border-radius: 50%; }
.topic-count { margin-left: auto; font-size: 12px; color: #9ca3af; }
```

---

## Implementation Order

| # | Task | File(s) | Effort |
|---|------|---------|--------|
| 1 | Run DB migrations | SQL | 10 min |
| 2 | Add vote methods to ForumsTrait | `includes/traits/forums.php` | 1h |
| 3 | Modify `get_forum_threads` sorting | `includes/traits/forums.php` | 30 min |
| 4 | Create vote AJAX endpoint | `includes/ajax/forums/vote.php` | 20 min |
| 5 | Create best-answer AJAX endpoint | `includes/ajax/forums/best-answer.php` | 15 min |
| 6 | Update forums.php controller (sort tabs, tags) | `forums.php` | 30 min |
| 7 | Update forums.tpl (Reddit card layout) | `content/themes/default/templates/forums.tpl` | 2h |
| 8 | Add CSS to theme | theme CSS file | 30 min |
| 9 | Add JS vote handler | forums.tpl inline or forums.js | 30 min |
| 10 | Test all flows | Manual QA | 1h |

**Total estimated**: ~6 hours

---

## What Stays the Same

- Auth system (no changes)
- Admin forum management
- Notification system (reuse for vote notifications)
- Media/image upload in posts
- Pagination (Pager class)
- Permission system (extend, not replace)

---

## What's New vs Current

| Feature | Before | After |
|---------|--------|-------|
| Voting | ❌ | ✅ Reddit upvote/downvote |
| Sort | newest only | 🔥 Hot / ✨ New / ⬆️ Top / 📈 Rising |
| Post types | thread only | post + question |
| Best answer | ❌ | ✅ Quora-style accepted answer |
| Tags | ❌ | ✅ Topic tags with follow |
| Karma | ❌ | ✅ User reputation from votes |
| Card layout | table rows | Reddit-style cards |
| Q&A mode | ❌ | ✅ Unanswered questions feed |
