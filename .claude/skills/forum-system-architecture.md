# Forum System Architecture & Implementation Guide

**Project**: Sngine Forum Module  
**Last Updated**: 2026-09-19  
**Version**: 1.0 - Complete System Mapping

---

## Quick Navigation

1. [Architecture Overview](#1-architecture-overview)
2. [File Structure](#2-file-structure)
3. [Database Schema](#3-database-schema)
4. [Frontend Views & Flows](#4-frontend-views--flows)
5. [API Endpoints](#5-api-endpoints)
6. [ForumsTrait Methods](#6-forumstrait-methods)
7. [Permission System](#7-permission--access-control)
8. [Data Flow Diagrams](#8-data-flow-diagrams)
9. [Integration Points](#9-integration-points)
10. [Configuration](#10-configuration-reference)

---

## 1. Architecture Overview

The forum system is a **hierarchical discussion platform** with:
- **3 main database entities**: Forums (categories/sections), Threads, Replies
- **Modular PHP architecture** using traits
- **AJAX-based frontend** for seamless interactions
- **Permission-based access control**
- **Notification integration** for user updates

**Key Principle**: Forums can be nested sections (category) or contain threads (sub-forum)

---

## 2. File Structure

### Core Files

```
├── forums.php                          [8.6 KB] Main controller
├── includes/
│   ├── traits/forums.php              [26 KB]  Business logic trait
│   └── ajax/
│       ├── forums/
│       │   ├── thread.php              Create/edit threads
│       │   ├── reply.php               Create/edit replies
│       │   └── delete.php              Delete threads/replies
│       └── admin/
│           └── forums.php              Admin forum management
└── content/themes/default/templates/
    ├── forums.tpl                      [883 lines] Main UI
    ├── admin.forums.tpl                Admin panel
    ├── admin.forums.recursive_rows.tpl Admin tree display
    └── admin.forums.recursive_options.tpl Admin dropdown
```

### AJAX Endpoints Reference

| File | Endpoint | Operations |
|------|----------|-----------|
| `thread.php` | `/includes/ajax/forums/thread.php` | `?do=create`, `?do=edit` |
| `reply.php` | `/includes/ajax/forums/reply.php` | `?do=create`, `?do=edit` |
| `delete.php` | `/includes/ajax/forums/delete.php` | `?handle=thread/reply` |
| `admin/forums.php` | `/includes/ajax/admin/forums.php` | `?do=add_forum`, `?do=edit_forum` |

---

## 3. Database Schema

### Table: `forums` (Hierarchical Categories)

```sql
CREATE TABLE `forums` (
  `forum_id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `forum_section` INT UNSIGNED DEFAULT 0,      -- Parent forum (0 = root)
  `forum_name` VARCHAR(256) NOT NULL,
  `forum_description` MEDIUMTEXT,
  `forum_order` INT UNSIGNED DEFAULT 1,        -- Display order
  `forum_threads` INT UNSIGNED DEFAULT 0,      -- Cached count
  `forum_replies` INT UNSIGNED DEFAULT 0       -- Cached count
);
```

**Hierarchy Rules**:
- `forum_section = 0` → Category (no threads allowed)
- `forum_section > 0` → Sub-forum (can contain threads)

### Table: `forums_threads` (Discussion Posts)

```sql
CREATE TABLE `forums_threads` (
  `thread_id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `forum_id` INT UNSIGNED NOT NULL,            -- FK: forums
  `user_id` INT UNSIGNED NOT NULL,             -- FK: users (author)
  `title` VARCHAR(256) NOT NULL,
  `text` LONGTEXT NOT NULL,
  `replies` INT UNSIGNED DEFAULT 0,            -- Reply count
  `views` INT UNSIGNED DEFAULT 0,              -- View count
  `time` DATETIME NOT NULL,
  `last_reply` DATETIME,                       -- Last activity
  KEY `idx_forum` (forum_id),
  KEY `idx_user` (user_id),
  KEY `idx_time` (time),
  KEY `idx_last_reply` (last_reply)
);
```

### Table: `forums_replies` (Thread Responses)

```sql
CREATE TABLE `forums_replies` (
  `reply_id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `thread_id` INT UNSIGNED NOT NULL,           -- FK: forums_threads
  `user_id` INT UNSIGNED NOT NULL,             -- FK: users (author)
  `text` LONGTEXT NOT NULL,
  `time` DATETIME NOT NULL,
  KEY `idx_thread` (thread_id),
  KEY `idx_user` (user_id)
);
```

### Relationship Diagram

```
forums (hierarchical tree)
  ├─ forum_section = 0 (Category - no threads)
  ├─ forum_section = parent_id (Sub-forum)
  │   └─ forums_threads
  │       ├─ thread_id
  │       ├─ forum_id → forums
  │       ├─ user_id → users
  │       └─ forums_replies
  │           ├─ reply_id
  │           ├─ thread_id → forums_threads
  │           └─ user_id → users
```

---

## 4. Frontend Views & Flows

### User-Facing Views

| View | Route | Purpose |
|------|-------|---------|
| Forum Home | `/forums` | List all forums & categories |
| Forum Details | `/forums/{forum_id}/{title}` | Show forum threads |
| Thread View | `/forums/thread/{thread_id}/{title}` | Show thread & replies |
| Create Thread | `/forums/new-thread/{forum_id}` | New thread form |
| Edit Thread | `/forums/edit-thread/{thread_id}` | Edit thread form |
| Create Reply | `/forums/new-reply/{thread_id}` | New reply form |
| Edit Reply | `/forums/edit-reply/{reply_id}` | Edit reply form |
| My Threads | `/forums/my-threads?page={n}` | User's threads |
| My Replies | `/forums/my-replies?page={n}` | User's replies |
| Search | `/forums/search` | Search interface |
| Results | `/forums/search-results?query={q}&type={t}&forum={id}` | Results |

### Create Thread Flow

```
1. User visits /forums/{forum_id}
2. Clicks "Write New Thread"
3. Fills form (title, content)
4. AJAX POST /includes/ajax/forums/thread.php?do=create&id={forum_id}
5. Backend validates:
   - Forums enabled
   - User has forums_permission
   - Forum is sub-forum (not section)
   - Title ≥ 3 chars
   - Content not empty
6. INSERT forums_threads
7. UPDATE forums.forum_threads++
8. Process image uploads
9. Return redirect URL
10. Display /forums/thread/{thread_id}/{slug}
```

### Reply to Thread Flow

```
1. User visits /forums/thread/{thread_id}
2. Clicks "Post Reply"
3. Fills content
4. AJAX POST /includes/ajax/forums/reply.php?do=create&id={thread_id}
5. Backend validates & inserts
6. UPDATE forums_threads.last_reply
7. UPDATE forums_threads.replies++
8. UPDATE forums.forum_replies++
9. Send notification to thread author
10. Reload page with anchor #reply-{id}
```

---

## 5. API Endpoints

### Thread Operations

**Create**: `POST /includes/ajax/forums/thread.php?do=create&id={forum_id}`
- Params: `title`, `text`
- Returns: `{path: "/forums/thread/{id}/{slug}", callback: "redirect"}`

**Edit**: `POST /includes/ajax/forums/thread.php?do=edit&id={thread_id}`
- Params: `title`, `text`
- Returns: `{path: "/forums/thread/{id}/{slug}", callback: "redirect"}`

### Reply Operations

**Create**: `POST /includes/ajax/forums/reply.php?do=create&id={thread_id}`
- Params: `text`
- Returns: `{path: "/forums/thread/{id}/{slug}#reply-{id}", callback: "redirect"}`

**Edit**: `POST /includes/ajax/forums/reply.php?do=edit&id={reply_id}`
- Params: `text`
- Returns: `{path: "/forums/thread/{id}/{slug}#reply-{id}", callback: "redirect"}`

### Delete Operations

**Delete Thread**: `POST /includes/ajax/forums/delete.php`
- Params: `handle=thread`, `id={thread_id}`
- Redirects: `/forums/{forum_id}/{slug}`

**Delete Reply**: `POST /includes/ajax/forums/delete.php`
- Params: `handle=reply`, `id={reply_id}`
- Reloads page

### Admin Operations

**Add Forum**: `POST /includes/ajax/admin/forums.php?do=add_forum`
- Params: `forum_name`, `forum_description`, `forum_section`, `forum_order`
- Permission: `mods_forums_permission`

**Edit Forum**: `POST /includes/ajax/admin/forums.php?do=edit_forum&id={id}`
- Params: `forum_name`, `forum_description`, `forum_section`, `forum_order`
- Permission: `mods_forums_permission`

---

## 6. ForumsTrait Methods

### Forum Management

```php
// Get all forums as hierarchical tree
get_forums($node_id = 0, $reverse = false): Array

// Get child forums recursively
get_child_forums($node_id, $iteration = 1): Array

// Get single forum with metadata
get_forum($forum_id, $get_childs = true): Array|false

// Delete forum and cascade to threads/replies
delete_forum($node_id): void
```

### Thread Operations

```php
// Get paginated threads
get_forum_threads($args[]): Array
// Args: 'forum' => id, 'user_id' => id, 'sort' => newest|top|hot

// Get single thread with author info
get_forum_thread($thread_id, $update_views = false): Array|false

// Create new thread
post_forum_thread($forum_id, $title, $text): Integer (thread_id)

// Update thread
edit_forum_thread($thread_id, $title, $text): void

// Delete thread
delete_forum_thread($thread_id): Array (forum object)
```

### Reply Operations

```php
// Get paginated replies
get_forum_replies($args[]): Array
// Args: 'thread' => obj, 'user_id' => id

// Get single reply
get_forum_reply($reply_id): Array|false

// Create new reply
post_forum_reply($thread_id, $text): Array (reply_id, thread)

// Update reply
edit_forum_reply($reply_id, $text): Array

// Delete reply
delete_forum_reply($reply_id): void
```

### Search & Utility

```php
// Search threads and replies
search_forums($query, $type, $forum, $recursive): Array

// Get users online in forum
get_forum_online_users(): Array

// Get forum statistics
get_forum_stats(): Array
```

---

## 7. Permission & Access Control

### Permission Model

| Role | forums_permission | Can Do |
|------|------------------|--------|
| Public/Guest | 0 | View only |
| Member | 1 | Create threads/replies |
| Moderator | user_group < 3 | Edit/delete ANY |
| Admin | user_group < 3 | Full management |

### Access Control Logic

```php
// Can manage (edit/delete) thread?
$manage = false;
if ($user->_logged_in) {
    // Admin/Moderator
    if ($user->_data['user_group'] < 3) {
        $manage = true;
    }
    // OR is author
    if ($user->_data['user_id'] == $thread['user_id']) {
        $manage = true;
    }
}
```

### System Flags

```php
$system['forums_enabled']              // Master switch
$system['forums_online_enabled']       // Show online users
$system['forums_statistics_enabled']   // Show stats widget
```

---

## 8. Data Flow Diagrams

### View Thread - Complete Flow

```
GET /forums/thread/{thread_id}/{title_url}
         ↓
    forums.php (controller)
         ↓
    get_forum_thread($thread_id, update_views=true)
    ├─ SELECT FROM forums_threads
    ├─ JOIN users (author info)
    ├─ Get parent forum
    ├─ Check manage permissions
    ├─ UPDATE views counter
    └─ Return thread data
         ↓
    get_forum_replies($thread)
    ├─ SELECT COUNT(*) for pagination
    ├─ Paginate (max_results * 2 per page)
    ├─ SELECT FROM forums_replies
    ├─ JOIN users (author info)
    ├─ Check manage permissions per reply
    └─ Return replies array
         ↓
    Render forums.tpl
    ├─ Display thread
    ├─ Loop replies
    │  ├─ Display reply
    │  ├─ Show author badge (Admin/Mod/Member)
    │  └─ Show edit/delete/report buttons
    └─ Display pager
```

### Create & Update Counters

When thread is created:
```
INSERT forums_threads
UPDATE forums.forum_threads = forum_threads + 1
UPDATE forums.forum_replies = 0 (initial)
```

When reply is created:
```
INSERT forums_replies
UPDATE forums_threads.replies = replies + 1
UPDATE forums_threads.last_reply = NOW()
UPDATE forums.forum_replies = forum_replies + 1
```

When reply is deleted:
```
DELETE FROM forums_replies
UPDATE forums_threads.replies = IF(replies=0, 0, replies-1)
UPDATE forums.forum_replies = IF(forum_replies=0, 0, forum_replies-1)
```

---

## 9. Integration Points

### With User Class

```php
// In class-user.php
class User {
    use ForumsTrait;  // Adds all forum methods
}

// Usage
$user = new User();
$forums = $user->get_forums();
$thread_id = $user->post_forum_thread($forum_id, $title, $text);
```

### With Notifications

```php
// When reply posted
$this->post_notification([
    'to_user_id' => $thread['user_id'],
    'action' => 'forum_reply',
    'node_url' => $thread_id . '/' . $slug . '/#reply-' . $reply_id
]);

// When reply deleted
$this->delete_notification(
    $thread['user_id'],
    'forum_reply',
    '',
    $thread_id . '/' . $slug . '/#reply-' . $reply_id
);
```

### With Media System

```php
// Extract images from rich text
$images = extract_uploaded_images_from_text($text);

// Remove pending uploads after posting
remove_pending_uploads($images);
```

### With HTML Purifier

```php
// If richtext disabled
if (!$system['html_richtext_enabled']) {
    $purifier = get_html_purifier();
    $text = $purifier->purify($text);
}
```

### With Pagination

```php
// Uses Pager class
$pager = new Pager([
    'selected_page' => $_GET['page'],
    'total_items' => $total_count,
    'items_per_page' => $system['max_results'] * 2,
    'url' => $pager_url_pattern
]);
```

---

## 10. Configuration Reference

### System Settings

```sql
INSERT INTO settings VALUES
(215, 'forums_enabled', '0'),
(216, 'forums_online_enabled', '1'),
(217, 'forums_statistics_enabled', '1');
```

### User Permission Column

```sql
ALTER TABLE permissions_groups ADD COLUMN
forums_permission INT(10) DEFAULT 0;
-- 0 = Cannot use forums
-- 1 = Can use forums
```

### Template Variables

```php
// Passed to forums.tpl
$smarty->assign('forums', $forums);           // Forum tree
$smarty->assign('online_users', $users);      // Online count
$smarty->assign('insights', $insights);       // Stats widget
$smarty->assign('forum', $forum);             // Current forum
$smarty->assign('thread', $thread);           // Current thread
$smarty->assign('threads', $threads);         // Thread list
$smarty->assign('replies', $replies);         // Reply list
$smarty->assign('view', $view);               // Current view
```

---

## Implementation Checklist

- [ ] Database tables created (`forums`, `forums_threads`, `forums_replies`)
- [ ] ForumsTrait added to User class
- [ ] forum.php controller deployed
- [ ] AJAX endpoints deployed (`/includes/ajax/forums/*.php`)
- [ ] Admin API endpoint deployed (`/includes/ajax/admin/forums.php`)
- [ ] Smarty templates deployed
- [ ] Permission column added to `permissions_groups`
- [ ] System settings inserted (forums_enabled, etc.)
- [ ] Notification integration tested
- [ ] Media upload integration tested
- [ ] Admin panel wired up

---

## Known Limitations & TODOs

- [ ] Pagination: Replies use double max_results (optimize)
- [ ] Search: No full-text index (consider adding)
- [ ] Moderation: No soft-delete (deleted posts lost)
- [ ] Hierarchy: No depth limit (could cause performance issues)
- [ ] Caching: Counters updated on every action (consider batch)

---

## Troubleshooting

| Issue | Cause | Fix |
|-------|-------|-----|
| 500 error on /forums | config.php missing | Upload includes/config.php |
| "Forums not enabled" | System setting off | SET forums_enabled = 1 |
| No permission to post | forums_permission = 0 | Update permissions_groups |
| Threads not showing | forum_section != 0 | Set forum_section = 0 for root |
| Replies not incrementing | Counter bug | Manual: UPDATE forums_threads SET replies = COUNT(...) |

---

**End of Document**

For questions or updates, refer to:
- GitHub: https://github.com/dewanlabung/sn
- Contact: 25951031@sjcu.ac.kr
