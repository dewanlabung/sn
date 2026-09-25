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
  foreach (['forums_moderation_log', 'forums_subscriptions'] as $t) {
    $r = $db->query("SHOW TABLES LIKE '$t'");
    echo ($r && $r->num_rows > 0 ? "EXISTS " : "MISSING") . ": $t\n";
  }
  foreach ([
    ['forums_threads', 'thread_pinned'],
    ['forums_threads', 'thread_locked'],
    ['forums_replies', 'reply_is_answer'],
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
