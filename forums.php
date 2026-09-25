<?php

/**
 * forums
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootloader
require('bootloader.php');

// forums enabled
if (!$system['forums_enabled']) {
  _error(404);
}

// user access
if ($user->_logged_in || !$system['system_public']) {
  user_access();
}

try {

  // get view content
  switch ($_GET['view']) {
    case '':
      // page header
      page_header(__("Forums") . ' | ' . __($system['system_title']), __($system['system_description_forums']));

      // get forums
      $forums = $user->get_forums();
      /* assign variables */
      $smarty->assign('forums', $forums);

      // get online users
      if ($system['forums_online_enabled']) {
        $online_users = $user->get_forum_online_users();
        /* assign variables */
        $smarty->assign('online_users', $online_users);
      }

      // get insights
      if ($system['forums_statistics_enabled']) {
        /* total threads */
        $get_threads = $db->query("SELECT COUNT(*) as count FROM forums_threads");
        $insights['threads'] = $get_threads->fetch_assoc()['count'];
        /* total replies */
        $get_replies = $db->query("SELECT COUNT(*) as count FROM forums_replies");
        $insights['replies'] = $get_replies->fetch_assoc()['count'];
        /* total users */
        $get_users = $db->query("SELECT COUNT(*) as count FROM users WHERE user_group >= 3 AND user_banned = '0'");
        $insights['users'] = $get_users->fetch_assoc()['count'];
        /* assign variables */
        $smarty->assign('insights', $insights);
      }
      /* top contributors */
      $top_contributors = $db->query(
        "SELECT u.user_id, u.user_name, u.user_firstname, u.user_lastname, u.user_gender, u.user_picture, u.user_verified,
                COUNT(DISTINCT ft.thread_id) AS thread_count,
                COUNT(DISTINCT fr.reply_id)  AS reply_count
         FROM users u
         LEFT JOIN forums_threads ft ON ft.user_id = u.user_id
         LEFT JOIN forums_replies  fr ON fr.user_id = u.user_id
         WHERE u.user_banned = '0' AND u.user_group >= 3
         GROUP BY u.user_id
         HAVING (COUNT(DISTINCT ft.thread_id) + COUNT(DISTINCT fr.reply_id)) > 0
         ORDER BY (COUNT(DISTINCT ft.thread_id) + COUNT(DISTINCT fr.reply_id)) DESC
         LIMIT 5"
      );
      $contributors = [];
      if ($top_contributors && $top_contributors->num_rows > 0) {
        while ($c = $top_contributors->fetch_assoc()) {
          $c['user_picture']  = get_picture($c['user_picture'], $c['user_gender']);
          $c['user_fullname'] = ($system['show_usernames_enabled']) ? $c['user_name'] : $c['user_firstname'] . " " . $c['user_lastname'];
          $contributors[] = $c;
        }
      }
      $smarty->assign('top_contributors', $contributors);
      /* popular tags */
      $popular_tags = $user->get_all_tags(15);
      $smarty->assign('popular_tags', $popular_tags);
      break;

    case 'forum':
      // get forum
      $forum = $user->get_forum($_GET['forum_id']);
      if (!$forum) {
        _error(404);
      }
      /* sort */
      $sort = isset($_GET['sort']) && in_array($_GET['sort'], ['newest','top','hot','rising','q&a','unanswered']) ? $_GET['sort'] : 'newest';
      $smarty->assign('sort', $sort);
      /* get threads */
      $forum['threads'] = $user->get_forum_threads(['forum' => $forum, 'sort' => $sort]);
      /* assign variables */
      $smarty->assign('forum', $forum);

      // page header
      page_header($forum['forum_name'] . ' | ' . __($system['system_title']), $forum['forum_description']);
      break;

    case 'thread':
      // get thread
      $thread = $user->get_forum_thread($_GET['thread_id'], true);
      if (!$thread) {
        _error(404);
      }
      /* get replies */
      if ($thread['replies'] > 0) {
        $thread['thread_replies'] = $user->get_forum_replies(['thread' => $thread]);
      }
      /* assign variables */
      $smarty->assign('thread', $thread);

      // page header
      page_header($thread['title'], $thread['text_snippet']);
      break;

    case 'new-thread':
      // user access
      user_access();

      // page header
      page_header(__("Forums") . " &rsaquo; " . __("Write New Thread") . ' | ' . __($system['system_title']), __($system['system_description_forums']));

      // get forum
      $forum = $user->get_forum($_GET['forum_id'], false);
      if (!$forum) {
        _error(404);
      }
      /* assign variables */
      $smarty->assign('forum', $forum);
      break;

    case 'edit-thread':
      // user access
      user_access();

      // page header
      page_header(__("Forums") . " &rsaquo; " . __("Edit Thread") . ' | ' . __($system['system_title']), __($system['system_description_forums']));

      // get thread
      $thread = $user->get_forum_thread($_GET['thread_id']);
      if (!$thread) {
        _error(404);
      }
      /* assign variables */
      $smarty->assign('thread', $thread);
      break;

    case 'new-reply':
      // user access
      user_access();

      // page header
      page_header(__("Forums") . " &rsaquo; " . __("Post Reply") . ' | ' . __($system['system_title']), __($system['system_description_forums']));

      // get thread
      $thread = $user->get_forum_thread($_GET['thread_id']);
      if (!$thread) {
        _error(404);
      }
      /* assign variables */
      $smarty->assign('thread', $thread);
      break;

    case 'edit-reply':
      // user access
      user_access();

      // page header
      page_header(__("Forums") . " &rsaquo; " . __("Edit Reply") . ' | ' . __($system['system_title']), __($system['system_description_forums']));

      // get reply
      $reply = $user->get_forum_reply($_GET['reply_id']);
      if (!$reply) {
        _error(404);
      }
      /* assign variables */
      $smarty->assign('reply', $reply);
      break;

    case 'my-threads':
      // user access
      user_access();

      // page header
      page_header(__("Forums") . " &rsaquo; " . __("My Threads") . ' | ' . __($system['system_title']), __($system['system_description_forums']));

      // get threads
      $threads = $user->get_forum_threads(['user_id' => $user->_data['user_id']]);
      /* assign variables */
      $smarty->assign('threads', $threads);
      break;

    case 'my-replies':
      // user access
      user_access();

      // page header
      page_header(__("Forums") . " &rsaquo; " . __("My Replies") . ' | ' . __($system['system_title']), __($system['system_description_forums']));

      // get replies
      $replies = $user->get_forum_replies(['user_id' => $user->_data['user_id']]);
      /* assign variables */
      $smarty->assign('replies', $replies);
      break;

    case 'search':
      // page header
      page_header(__("Forums") . " &rsaquo; " . __("Search") . ' | ' . __($system['system_title']), __($system['system_description_forums']));

      // get forums
      $forums = $user->get_forums();
      /* assign variables */
      $smarty->assign('forums', $forums);
      break;

    case 'search-results':
      // page header
      page_header(__("Forums") . " &rsaquo; " . __("Search Results") . ' | ' . __($system['system_title']), __($system['system_description_forums']));

      if (!isset($_GET['query']) || !isset($_GET['type']) || !isset($_GET['forum'])) {
        redirect('/forums/search');
      }
      $results = $user->search_forums($_GET['query'], $_GET['type'], $_GET['forum'], $_GET['recursive']);
      $smarty->assign('query', htmlentities($_GET['query'], ENT_QUOTES, 'utf-8'));
      $smarty->assign('type', $_GET['type']);
      $smarty->assign('results', $results);
      break;

    default:
      _error(404);
      break;
  }
  /* assign variables */
  $smarty->assign('view', $_GET['view']);
} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

// page footer
page_footer('forums');
