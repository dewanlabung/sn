<?php

/**
 * ajax -> admin -> deploy
 * Deploy tool: run migrations and/or clear cache
 *
 * @package Sngine
 */

require('../../../bootstrap.php');
is_ajax();
admin_access();

header('Content-Type: text/plain; charset=utf-8');

$do = $_GET['do'] ?? 'all';

$migrations = [
  "ALTER TABLE `forums_threads` ADD COLUMN IF NOT EXISTS `thread_pinned` TINYINT(1) NOT NULL DEFAULT 0",
  "ALTER TABLE `forums_threads` ADD COLUMN IF NOT EXISTS `thread_locked` TINYINT(1) NOT NULL DEFAULT 0",
  "ALTER TABLE `forums_replies` ADD COLUMN IF NOT EXISTS `reply_is_answer` TINYINT(1) NOT NULL DEFAULT 0",
  "CREATE TABLE IF NOT EXISTS `forums_moderation_log` (
    `log_id`       INT(11)     NOT NULL AUTO_INCREMENT,
    `moderator_id` INT(11)     NOT NULL,
    `action_type`  VARCHAR(50) NOT NULL,
    `target_type`  VARCHAR(20) NOT NULL,
    `target_id`    INT(11)     NOT NULL,
    `reason`       TEXT,
    `created_at`   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `idx_mod_log_moderator` (`moderator_id`),
    KEY `idx_mod_log_target`    (`target_type`, `target_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
  "CREATE TABLE IF NOT EXISTS `forums_subscriptions` (
    `subscription_id`   INT(11)               NOT NULL AUTO_INCREMENT,
    `user_id`           INT(11)               NOT NULL,
    `forum_id`          INT(11),
    `thread_id`         INT(11),
    `subscription_type` ENUM('forum','thread') NOT NULL,
    `notify_email`      TINYINT(1)            NOT NULL DEFAULT 1,
    `created_at`        TIMESTAMP             NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`subscription_id`),
    UNIQUE KEY `uq_sub_forum`  (`user_id`, `forum_id`,  `subscription_type`),
    UNIQUE KEY `uq_sub_thread` (`user_id`, `thread_id`, `subscription_type`),
    KEY `idx_sub_user` (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
  "CREATE TABLE IF NOT EXISTS `forums_votes` (
    `vote_id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`   INT(10) UNSIGNED NOT NULL,
    `item_id`   INT(10) UNSIGNED NOT NULL,
    `item_type` ENUM('thread','reply') NOT NULL DEFAULT 'thread',
    `vote_type` ENUM('up','down')      NOT NULL DEFAULT 'up',
    `time`      DATETIME NOT NULL,
    PRIMARY KEY (`vote_id`),
    UNIQUE KEY `unique_vote` (`user_id`,`item_id`,`item_type`),
    KEY `idx_item` (`item_id`,`item_type`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
  "ALTER TABLE `forums_threads` ADD COLUMN IF NOT EXISTS `thread_votes_up`   INT(11) NOT NULL DEFAULT 0",
  "ALTER TABLE `forums_threads` ADD COLUMN IF NOT EXISTS `thread_votes_down` INT(11) NOT NULL DEFAULT 0",
  "ALTER TABLE `forums_replies` ADD COLUMN IF NOT EXISTS `reply_votes_up`    INT(11) NOT NULL DEFAULT 0",
  "ALTER TABLE `forums_replies` ADD COLUMN IF NOT EXISTS `reply_votes_down`  INT(11) NOT NULL DEFAULT 0",
  "ALTER TABLE `forums_threads` ADD COLUMN IF NOT EXISTS `thread_best_reply_id` INT(11) DEFAULT NULL",
  "CREATE TABLE IF NOT EXISTS `questions_categories` (
    `category_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_name` VARCHAR(255) NOT NULL,
    `category_order` INT(10) UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`category_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
  "INSERT IGNORE INTO `questions_categories` (`category_id`, `category_name`, `category_order`) VALUES (1, 'General Questions', 1)",
  "CREATE TABLE IF NOT EXISTS `posts_questions` (
    `post_id`        INT(10) UNSIGNED NOT NULL,
    `question_title` TEXT NOT NULL,
    `category_id`    INT(10) UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`post_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
  "CREATE TABLE IF NOT EXISTS `posts_questions_votes` (
    `vote_id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `post_id`    INT(10) UNSIGNED NOT NULL,
    `user_id`    INT(10) UNSIGNED NOT NULL,
    `vote_type`  ENUM('satisfactory','good','bad') NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`vote_id`),
    UNIQUE KEY `unique_vote` (`post_id`,`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

if ($do === 'migrate' || $do === 'all') {
  echo "=== Database Migrations ===\n";
  $ok = 0;
  $errs = [];
  foreach ($migrations as $sql) {
    $short = substr(trim(preg_replace('/\s+/', ' ', $sql)), 0, 72);
    if ($db->query($sql) !== false) {
      echo "OK    : $short\n";
      $ok++;
    } else {
      $e = $db->error;
      if (stripos($e, 'duplicate') !== false || stripos($e, 'already') !== false) {
        echo "SKIP  : $short\n";
        $ok++;
      } else {
        echo "ERROR : $short\n        $e\n";
        $errs[] = $e;
      }
    }
  }
  echo "\nResult: $ok/" . count($migrations) . " OK";
  echo empty($errs) ? " — all good\n" : " — " . count($errs) . " failed\n";

  echo "\n--- Verify ---\n";
  foreach (['forums_moderation_log', 'forums_subscriptions', 'forums_votes', 'questions_categories', 'posts_questions', 'posts_questions_votes'] as $t) {
    $r = $db->query("SHOW TABLES LIKE '$t'");
    echo ($r && $r->num_rows > 0 ? "EXISTS " : "MISSING") . ": $t\n";
  }
  foreach ([
    ['forums_threads', 'thread_pinned'],
    ['forums_threads', 'thread_locked'],
    ['forums_threads', 'thread_votes_up'],
    ['forums_threads', 'thread_best_reply_id'],
    ['forums_replies', 'reply_is_answer'],
    ['forums_replies', 'reply_votes_up'],
  ] as [$tbl, $col]) {
    $r = $db->query("SHOW COLUMNS FROM `$tbl` LIKE '$col'");
    echo ($r && $r->num_rows > 0 ? "EXISTS " : "MISSING") . ": $tbl.$col\n";
  }
  echo "\n";
}

if ($do === 'cache' || $do === 'all') {
  echo "=== Cache Clear ===\n";
  $smarty->clearAllCache();
  $smarty->clearCompiledTemplate();
  echo "OK    : Smarty cache cleared\n";
  echo "OK    : Compiled templates cleared\n\n";
}

echo "=== Done ===\n";
