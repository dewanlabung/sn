<?php

/**
 * ajax -> posts -> question_vote
 * Handle satisfaction votes on Ask Me question posts.
 *
 * @package Sngine
 */

require('../../../bootstrap.php');
is_ajax();
user_access();

$post_id   = isset($_POST['post_id'])  && is_numeric($_POST['post_id'])  ? (int)$_POST['post_id']  : 0;
$vote_type = $_POST['vote_type'] ?? '';

if (!$post_id || !in_array($vote_type, ['good', 'satisfactory', 'bad'])) {
  _error(400);
}

$post_id   = secure($post_id, 'int');
$user_id   = secure($user->_data['user_id'], 'int');
$vote_type = secure($vote_type);

/* upsert vote */
$db->query("INSERT INTO posts_questions_votes (post_id, user_id, vote_type)
            VALUES ($post_id, $user_id, $vote_type)
            ON DUPLICATE KEY UPDATE vote_type = $vote_type");

/* aggregate counts */
$get_stats = $db->query("SELECT vote_type, COUNT(*) AS count
                          FROM posts_questions_votes
                          WHERE post_id = $post_id
                          GROUP BY vote_type");

$stats = ['good' => 0, 'satisfactory' => 0, 'bad' => 0, 'total' => 0];
if ($get_stats) {
  while ($row = $get_stats->fetch_assoc()) {
    $stats[$row['vote_type']] = (int)$row['count'];
    $stats['total'] += (int)$row['count'];
  }
}

$t = $stats['total'];
return_json([
  'good_pct' => $t > 0 ? round($stats['good']          / $t * 100) : 0,
  'sat_pct'  => $t > 0 ? round($stats['satisfactory']  / $t * 100) : 0,
  'bad_pct'  => $t > 0 ? round($stats['bad']           / $t * 100) : 0,
  'total'    => $t,
  'my_vote'  => $vote_type,
]);
