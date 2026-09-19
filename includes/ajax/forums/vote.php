<?php

/**
 * ajax -> forums -> vote
 * Handles upvote/downvote for forum threads and replies
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
  $return = [];

  // ensure forums_votes table exists
  $db->query("CREATE TABLE IF NOT EXISTS `forums_votes` (
    `vote_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` int(10) UNSIGNED NOT NULL,
    `item_id` int(10) UNSIGNED NOT NULL,
    `item_type` enum('thread','reply') NOT NULL DEFAULT 'thread',
    `vote_type` enum('up','down') NOT NULL DEFAULT 'up',
    `time` datetime NOT NULL,
    PRIMARY KEY (`vote_id`),
    UNIQUE KEY `unique_vote` (`user_id`,`item_id`,`item_type`),
    KEY `idx_item` (`item_id`,`item_type`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC");

  $item_id   = isset($_POST['item_id'])   ? (int) $_POST['item_id']   : 0;
  $item_type = isset($_POST['item_type']) ? $_POST['item_type']        : '';
  $vote_type = isset($_POST['vote_type']) ? $_POST['vote_type']        : '';

  if (!$item_id || !in_array($item_type, ['thread', 'reply']) || !in_array($vote_type, ['up', 'down'])) {
    _error(400);
  }

  $user_id = $user->_data['user_id'];

  // check existing vote
  $existing = $db->query(sprintf(
    "SELECT vote_id, vote_type FROM forums_votes WHERE user_id = %s AND item_id = %s AND item_type = %s",
    secure($user_id, 'int'), secure($item_id, 'int'), secure($item_type)
  ));

  if ($existing->num_rows > 0) {
    $row = $existing->fetch_assoc();
    if ($row['vote_type'] === $vote_type) {
      // same vote — remove (toggle off)
      $db->query(sprintf(
        "DELETE FROM forums_votes WHERE vote_id = %s",
        secure($row['vote_id'], 'int')
      ));
      $return['status'] = 'removed';
    } else {
      // change vote direction
      $db->query(sprintf(
        "UPDATE forums_votes SET vote_type = %s, time = %s WHERE vote_id = %s",
        secure($vote_type), secure(date('Y-m-d H:i:s')), secure($row['vote_id'], 'int')
      ));
      $return['status'] = 'changed';
    }
  } else {
    // new vote
    $db->query(sprintf(
      "INSERT INTO forums_votes (user_id, item_id, item_type, vote_type, time) VALUES (%s, %s, %s, %s, %s)",
      secure($user_id, 'int'), secure($item_id, 'int'), secure($item_type), secure($vote_type), secure(date('Y-m-d H:i:s'))
    ));
    $return['status'] = 'added';
  }

  // return fresh counts
  $counts = $db->query(sprintf(
    "SELECT
       SUM(vote_type = 'up')   AS upvotes,
       SUM(vote_type = 'down') AS downvotes
     FROM forums_votes
     WHERE item_id = %s AND item_type = %s",
    secure($item_id, 'int'), secure($item_type)
  ))->fetch_assoc();

  $return['upvotes']   = (int) $counts['upvotes'];
  $return['downvotes'] = (int) $counts['downvotes'];
  $return['score']     = $return['upvotes'] - $return['downvotes'];

  // my current vote (after action)
  $my_vote = $db->query(sprintf(
    "SELECT vote_type FROM forums_votes WHERE user_id = %s AND item_id = %s AND item_type = %s",
    secure($user_id, 'int'), secure($item_id, 'int'), secure($item_type)
  ));
  $return['my_vote'] = $my_vote->num_rows > 0 ? $my_vote->fetch_assoc()['vote_type'] : null;

  return_json($return);
} catch (Exception $e) {
  return_json(['error' => true, 'message' => $e->getMessage()]);
}
