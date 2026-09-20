<?php

/**
 * ajax -> forums -> subscriptions
 *
 * @package Sngine
 * @author Zamblek
 */

require('../../../bootstrap.php');
is_ajax();

if (!isset($user) || !$user->_logged_in) {
  return_json(['error' => true, 'message' => __('You must be logged in')]);
  die();
}

try {
  global $db;
  $user_id = intval($user->_data['user_id']);

  switch ($_GET['do'] ?? null) {

    case 'subscribe_forum':
      $forum_id = $_POST['forum_id'] ?? null;
      if (!$forum_id) {
        return_json(['error' => true, 'message' => __('Missing forum_id')]);
        die();
      }
      $db->query(sprintf(
        "INSERT IGNORE INTO forums_subscriptions (user_id, forum_id, subscription_type, notify_email)
         VALUES (%d, %d, 'forum', 1)",
        $user_id,
        intval($forum_id)
      ));
      return_json(['success' => true, 'message' => __('Subscribed to forum')]);
      break;

    case 'unsubscribe_forum':
      $forum_id = $_POST['forum_id'] ?? null;
      if (!$forum_id) {
        return_json(['error' => true, 'message' => __('Missing forum_id')]);
        die();
      }
      $db->query(sprintf(
        "DELETE FROM forums_subscriptions WHERE user_id = %d AND forum_id = %d AND subscription_type = 'forum'",
        $user_id,
        intval($forum_id)
      ));
      return_json(['success' => true, 'message' => __('Unsubscribed from forum')]);
      break;

    case 'subscribe_thread':
      $thread_id = $_POST['thread_id'] ?? null;
      if (!$thread_id) {
        return_json(['error' => true, 'message' => __('Missing thread_id')]);
        die();
      }
      $db->query(sprintf(
        "INSERT IGNORE INTO forums_subscriptions (user_id, thread_id, subscription_type, notify_email)
         VALUES (%d, %d, 'thread', 1)",
        $user_id,
        intval($thread_id)
      ));
      return_json(['success' => true, 'message' => __('Subscribed to thread')]);
      break;

    case 'unsubscribe_thread':
      $thread_id = $_POST['thread_id'] ?? null;
      if (!$thread_id) {
        return_json(['error' => true, 'message' => __('Missing thread_id')]);
        die();
      }
      $db->query(sprintf(
        "DELETE FROM forums_subscriptions WHERE user_id = %d AND thread_id = %d AND subscription_type = 'thread'",
        $user_id,
        intval($thread_id)
      ));
      return_json(['success' => true, 'message' => __('Unsubscribed from thread')]);
      break;

    case 'get_subscriptions':
      $result = $db->query(sprintf(
        "SELECT * FROM forums_subscriptions WHERE user_id = %d ORDER BY created_at DESC",
        $user_id
      ));
      $subscriptions = [];
      if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $subscriptions[] = $row;
        }
      }
      return_json(['success' => true, 'subscriptions' => $subscriptions]);
      break;

    default:
      return_json(['error' => true, 'message' => __('Invalid action')]);
      break;
  }

} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
