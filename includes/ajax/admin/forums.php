<?php

/**
 * ajax -> admin -> forums
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('../../../bootstrap.php');

// check AJAX Request
is_ajax();

// check admin|moderator permission
admin_access('mods_forums_permission');

// check demo account
if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

// handle forums
try {

  switch ($_GET['do']) {
    case 'add_forum':
      /* insert */
      $db->query(sprintf("INSERT INTO forums (forum_name, forum_description, forum_section, forum_order) VALUES (%s, %s, %s, %s)", secure($_POST['forum_name']), secure($_POST['forum_description']), secure($_POST['forum_section'], 'int'), secure($_POST['forum_order'], 'int')));
      /* return */
      return_json(['callback' => 'window.location = "' . $system['system_url'] . '/' . $control_panel['url'] . '/forums";']);
      break;

    case 'edit_forum':
      /* valid inputs */
      if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        _error(400);
      }
      if ($_GET['id'] == $_POST['forum_section']) {
        throw new Exception(__("You can not set the forum as a section to itself"));
      }
      /* update */
      $db->query(sprintf("UPDATE forums SET forum_name = %s, forum_description = %s, forum_section = %s, forum_order = %s WHERE forum_id = %s", secure($_POST['forum_name']), secure($_POST['forum_description']), secure($_POST['forum_section'], 'int'), secure($_POST['forum_order'], 'int'), secure($_GET['id'], 'int')));
      /* return */
      return_json(['success' => true, 'message' => __("Forum info have been updated")]);
      break;

    case 'pin_thread':
      if (!isset($_POST['thread_id'])) {
        throw new Exception(__("Missing thread_id"));
      }
      $pin = ($_POST['pin'] ?? 'false') == 'true' ? 1 : 0;
      $db->query(sprintf(
        "UPDATE forums_threads SET thread_pinned = %d WHERE thread_id = %d",
        $pin,
        intval($_POST['thread_id'])
      ));
      $db->query(sprintf(
        "INSERT INTO forums_moderation_log (moderator_id, action_type, target_type, target_id, reason)
         VALUES (%d, 'pin_thread', 'thread', %d, %s)",
        intval($user->_data['user_id']),
        intval($_POST['thread_id']),
        secure($pin ? 'Pinned' : 'Unpinned')
      ));
      return_json(['success' => true, 'message' => $pin ? __("Thread pinned") : __("Thread unpinned")]);
      break;

    case 'lock_thread':
      if (!isset($_POST['thread_id'])) {
        throw new Exception(__("Missing thread_id"));
      }
      $lock = ($_POST['lock'] ?? 'false') == 'true' ? 1 : 0;
      $db->query(sprintf(
        "UPDATE forums_threads SET thread_locked = %d WHERE thread_id = %d",
        $lock,
        intval($_POST['thread_id'])
      ));
      $db->query(sprintf(
        "INSERT INTO forums_moderation_log (moderator_id, action_type, target_type, target_id, reason)
         VALUES (%d, 'lock_thread', 'thread', %d, %s)",
        intval($user->_data['user_id']),
        intval($_POST['thread_id']),
        secure($lock ? 'Locked' : 'Unlocked')
      ));
      return_json(['success' => true, 'message' => $lock ? __("Thread locked") : __("Thread unlocked")]);
      break;

    case 'mark_answer':
      if (!isset($_POST['reply_id'])) {
        throw new Exception(__("Missing reply_id"));
      }
      $is_answer = ($_POST['is_answer'] ?? 'false') == 'true' ? 1 : 0;
      $db->query(sprintf(
        "UPDATE forums_replies SET reply_is_answer = %d WHERE reply_id = %d",
        $is_answer,
        intval($_POST['reply_id'])
      ));
      $db->query(sprintf(
        "INSERT INTO forums_moderation_log (moderator_id, action_type, target_type, target_id, reason)
         VALUES (%d, 'mark_answer', 'reply', %d, %s)",
        intval($user->_data['user_id']),
        intval($_POST['reply_id']),
        secure($is_answer ? 'Marked as answer' : 'Unmarked as answer')
      ));
      return_json(['success' => true, 'message' => __("Reply updated")]);
      break;

    case 'get_moderation_log':
      $limit = intval($_GET['limit'] ?? 50);
      $result = $db->query(sprintf(
        "SELECT ml.*, u.user_firstname, u.user_lastname FROM forums_moderation_log ml
         LEFT JOIN users u ON ml.moderator_id = u.user_id
         ORDER BY ml.created_at DESC LIMIT %d",
        $limit
      ));
      $logs = [];
      if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $logs[] = $row;
        }
      }
      return_json(['success' => true, 'logs' => $logs, 'total' => count($logs)]);
      break;

    default:
      _error(400);
      break;
  }
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
