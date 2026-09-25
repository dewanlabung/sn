<?php
/**
 * Temporary Forum Migration Admin Tool
 * Place at /admin_migrate_forums.php
 * Delete after use
 *
 * Only accessible to super admins
 */

require('bootstrap.php');

// Security: Only admins
if (!$user || !$user->_logged_in || $user->_data['user_admin_status'] != 'super') {
  die('Access denied. Super admin only.');
}

echo "<!DOCTYPE html><html><head><style>";
echo "body { font-family: monospace; background: #1a1a1a; color: #0f0; padding: 20px; }";
echo "h1 { color: #0ff; } .success { background: #001400; padding: 10px; border-left: 4px solid #0f0; }";
echo ".error { background: #400; padding: 10px; border-left: 4px solid #f00; }";
echo ".warning { background: #440; padding: 10px; border-left: 4px solid #f80; }";
echo "pre { background: #000; padding: 10px; overflow-x: auto; }";
echo "</style></head><body>";

echo "<h1>Forum Migration Tool</h1>";

$migrations = [
  "ALTER TABLE `forums_threads` ADD COLUMN IF NOT EXISTS `thread_pinned` TINYINT(1) NOT NULL DEFAULT 0",
  "ALTER TABLE `forums_threads` ADD COLUMN IF NOT EXISTS `thread_locked` TINYINT(1) NOT NULL DEFAULT 0",
  "ALTER TABLE `forums_replies` ADD COLUMN IF NOT EXISTS `reply_is_answer` TINYINT(1) NOT NULL DEFAULT 0",
  "CREATE TABLE IF NOT EXISTS `forums_moderation_log` (
    `log_id` INT(11) NOT NULL AUTO_INCREMENT,
    `moderator_id` INT(11) NOT NULL,
    `action_type` VARCHAR(50) NOT NULL,
    `target_type` VARCHAR(20) NOT NULL,
    `target_id` INT(11) NOT NULL,
    `reason` TEXT,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `idx_mod_log_moderator` (`moderator_id`),
    KEY `idx_mod_log_target` (`target_type`, `target_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
  "CREATE TABLE IF NOT EXISTS `forums_subscriptions` (
    `subscription_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `forum_id` INT(11),
    `thread_id` INT(11),
    `subscription_type` ENUM('forum','thread') NOT NULL,
    `notify_email` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`subscription_id`),
    UNIQUE KEY `uq_sub_forum` (`user_id`, `forum_id`, `subscription_type`),
    UNIQUE KEY `uq_sub_thread` (`user_id`, `thread_id`, `subscription_type`),
    KEY `idx_sub_user` (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

$success = 0;
$errors = [];

echo "<pre>";
echo "Running forum migrations...\n\n";

foreach ($migrations as $i => $migration) {
  $short = substr($migration, 0, 60) . "...";
  $result = $db->query($migration);

  if ($result === false) {
    $err = $db->error;
    if (stripos($err, 'duplicate') !== false || stripos($err, 'already') !== false) {
      echo "⚠️  SKIP (exists): $short\n";
      $success++;
    } else {
      echo "❌ ERROR: $short\n";
      echo "   " . htmlspecialchars($err) . "\n";
      $errors[] = $err;
    }
  } else {
    echo "✅ OK: $short\n";
    $success++;
  }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Results: $success completed\n";

if (!empty($errors)) {
  echo "\n❌ Errors:\n";
  foreach ($errors as $e) {
    echo "  - " . htmlspecialchars($e) . "\n";
  }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Verification:\n\n";

// Check tables
$tables = ['forums_moderation_log', 'forums_subscriptions'];
foreach ($tables as $t) {
  $r = $db->query("SHOW TABLES LIKE '$t'");
  echo ($r && $r->num_rows > 0 ? "✅" : "❌") . " Table: $t\n";
}

// Check columns
echo "\nColumns on forums_threads:\n";
$r = $db->query("DESCRIBE forums_threads");
$cols = [];
if ($r) {
  while ($row = $r->fetch_assoc()) {
    $cols[] = $row['Field'];
  }
  echo ($in_array('thread_pinned', $cols) ? "✅" : "❌") . " thread_pinned\n";
  echo ($in_array('thread_locked', $cols) ? "✅" : "❌") . " thread_locked\n";
}

echo "\nColumns on forums_replies:\n";
$r = $db->query("DESCRIBE forums_replies");
$cols = [];
if ($r) {
  while ($row = $r->fetch_assoc()) {
    $cols[] = $row['Field'];
  }
  echo ($in_array('reply_is_answer', $cols) ? "✅" : "❌") . " reply_is_answer\n";
}

echo "</pre>";

if (empty($errors)) {
  echo "<div class='success'>✅ All migrations completed successfully!</div>";
} else {
  echo "<div class='error'>⚠️ Some migrations had errors. See details above.</div>";
}

echo "<p style='margin-top: 20px; color: #888;'><strong>🔒 SECURITY:</strong> Delete this file immediately after use:</p>";
echo "<pre>rm admin_migrate_forums.php</pre>";

echo "</body></html>";
