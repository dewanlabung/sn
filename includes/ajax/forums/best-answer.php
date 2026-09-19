<?php

/**
 * ajax -> forums -> best-answer
 * Marks or unmarks a reply as the best/accepted answer (Quora style).
 *
 * @package Sngine
 */

require('../../../bootstrap.php');

is_ajax();
user_access(true);

if ($user->_data['user_demo']) {
  modal("ERROR", __("Demo Restriction"), __("You can't do this with demo account"));
}

try {
  $thread_id = isset($_POST['thread_id']) ? (int) $_POST['thread_id'] : 0;
  $reply_id  = isset($_POST['reply_id'])  ? (int) $_POST['reply_id']  : 0;

  if (!$thread_id || !$reply_id) {
    _error(400);
  }

  $success = $user->mark_best_answer($thread_id, $reply_id);

  if (!$success) {
    return_json(['error' => true, 'message' => __("You don't have permission to do this")]);
  }

  return_json(['callback' => 'reload']);
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
