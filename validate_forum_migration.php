<?php
/**
 * Forum Migration Validator
 * Access via: https://nkhoj.com/validate_forum_migration.php
 */

// Load bootstrap for DB access
if (!file_exists(__DIR__ . '/bootstrap.php')) {
  $bootstrap_paths = [
    '/home/alphaome/public_html/nkhoj.com/bootstrap.php',
    __DIR__ . '/../bootstrap.php',
    __DIR__ . '/../../bootstrap.php',
  ];

  $found = false;
  foreach ($bootstrap_paths as $path) {
    if (file_exists($path)) {
      require_once($path);
      $found = true;
      break;
    }
  }

  if (!$found) {
    die('Error: bootstrap.php not found');
  }
} else {
  require_once(__DIR__ . '/bootstrap.php');
}

global $db;

echo "<h1>🔍 Forum Phase 1 Migration Validator</h1>\n";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace;'>\n";

$checks = [];
$errors = [];

// CHECK 1: forums_threads columns
echo "[CHECK 1] forums_threads columns\n";
$result = $db->query("DESCRIBE forums_threads");
$columns = [];
while ($row = $result->fetch_assoc()) {
  $columns[] = $row['Field'];
}

if (in_array('thread_pinned', $columns)) {
  echo "  ✅ thread_pinned column EXISTS\n";
  $checks[] = 'thread_pinned';
} else {
  echo "  ❌ thread_pinned column MISSING\n";
  $errors[] = 'thread_pinned';
}

if (in_array('thread_locked', $columns)) {
  echo "  ✅ thread_locked column EXISTS\n";
  $checks[] = 'thread_locked';
} else {
  echo "  ❌ thread_locked column MISSING\n";
  $errors[] = 'thread_locked';
}

// CHECK 2: forums_replies columns
echo "\n[CHECK 2] forums_replies columns\n";
$result = $db->query("DESCRIBE forums_replies");
$columns = [];
while ($row = $result->fetch_assoc()) {
  $columns[] = $row['Field'];
}

if (in_array('reply_is_answer', $columns)) {
  echo "  ✅ reply_is_answer column EXISTS\n";
  $checks[] = 'reply_is_answer';
} else {
  echo "  ❌ reply_is_answer column MISSING\n";
  $errors[] = 'reply_is_answer';
}

// CHECK 3: forums_moderation_log table
echo "\n[CHECK 3] New tables\n";
$tables_to_check = ['forums_moderation_log', 'forums_subscriptions'];

foreach ($tables_to_check as $table) {
  $result = $db->query("SHOW TABLES LIKE '$table'");
  if ($result && $result->num_rows > 0) {
    echo "  ✅ $table EXISTS\n";
    $checks[] = $table;
  } else {
    echo "  ❌ $table MISSING\n";
    $errors[] = $table;
  }
}

// SUMMARY
echo "\n" . str_repeat("=", 50) . "\n";
echo "\n📊 SUMMARY:\n";
echo "  ✅ Passed: " . count($checks) . " checks\n";
echo "  ❌ Failed: " . count($errors) . " checks\n";

if (count($errors) === 0) {
  echo "\n🎉 MIGRATION COMPLETE!\n";
  echo "All forum features are ready to use:\n";
  echo "  - Admin: /includes/ajax/admin/forums.php?do=pin_thread|lock_thread|mark_answer\n";
  echo "  - Users: /includes/ajax/forums/subscriptions.php?do=subscribe_forum\n";
} else {
  echo "\n⚠️  MIGRATION INCOMPLETE\n";
  echo "Missing:\n";
  foreach ($errors as $error) {
    echo "  - $error\n";
  }
  echo "\nRun SQL migration:\n";
  echo "  mysql -u alphaome_nkhojfm -p alphaome_nkhojfm < includes/sql/forums_phase1.sql\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "</pre>\n";
?>
