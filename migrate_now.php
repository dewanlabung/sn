<?php
/**
 * Forum Migration Runner
 * One-click HTTP access to run migrations
 *
 * SECURITY: Remove after running!
 * This file should be deleted immediately after use
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><style>";
echo "body { font-family: monospace; background: #1a1a1a; color: #0f0; padding: 20px; }";
echo "h1 { color: #0ff; }";
echo ".success { background: #001400; padding: 10px; margin: 10px 0; border-left: 4px solid #0f0; }";
echo ".error { background: #400; padding: 10px; margin: 10px 0; border-left: 4px solid #f00; }";
echo ".warning { background: #440; padding: 10px; margin: 10px 0; border-left: 4px solid #f80; }";
echo "pre { background: #000; overflow-x: auto; }";
echo "</style></head><body>";

echo "<h1>🔧 Forum Migration - Direct Execution</h1>";

// Try multiple bootstrap paths
$found = false;
foreach ([__DIR__ . '/bootstrap.php', __DIR__ . '/../bootstrap.php'] as $path) {
  if (file_exists($path)) {
    require_once $path;
    $found = true;
    break;
  }
}

if (!$found) {
  echo "<div class='error'>❌ Could not load bootstrap</div>";
  exit;
}

global $db;

if (!$db) {
  echo "<div class='error'>❌ Database connection not available</div>";
  exit;
}

echo "<div class='success'>✅ Database connected</div>";
echo "<pre>";

// Read migration file
$sql_file = __DIR__ . '/includes/sql/forums_phase1.sql';
if (!file_exists($sql_file)) {
  echo "Migration file not found: $sql_file\n";
  exit;
}

echo "Running migrations from: $sql_file\n\n";

$sql = file_get_contents($sql_file);
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$skipped = 0;
$errors = [];

foreach ($statements as $stmt) {
  if (empty($stmt) || strpos($stmt, '--') === 0) continue;

  $short = substr($stmt, 0, 60) . "...";
  $result = $db->query($stmt);

  if ($result === false) {
    $err = $db->error;
    // Skip "already exists" - migration is idempotent
    if (stripos($err, 'already') !== false || stripos($err, 'duplicate') !== false) {
      echo "⚠️  SKIP (exists): $short\n";
      $skipped++;
    } else {
      echo "❌ ERROR: $short\n";
      echo "   " . $err . "\n";
      $errors[] = $err;
    }
  } else {
    echo "✅ OK: $short\n";
    $success++;
  }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Results: $success executed, $skipped skipped\n";

if (!empty($errors)) {
  echo "\n❌ Errors:\n";
  foreach ($errors as $e) {
    echo "  - $e\n";
  }
}

// Verify
echo "\n" . str_repeat("=", 60) . "\n";
echo "Verification:\n";

$tables = ['forums_moderation_log', 'forums_subscriptions'];
foreach ($tables as $t) {
  $r = $db->query("SHOW TABLES LIKE '$t'");
  echo ($r && $r->num_rows > 0 ? "✅" : "❌") . " $t\n";
}

// Check columns
$r = $db->query("DESCRIBE forums_threads");
$cols = [];
while ($row = $r->fetch_assoc()) {
  $cols[] = $row['Field'];
}
echo ($in_array('thread_pinned', $cols) ? "✅" : "❌") . " forums_threads.thread_pinned\n";
echo ($in_array('thread_locked', $cols) ? "✅" : "❌") . " forums_threads.thread_locked\n";

$r = $db->query("DESCRIBE forums_replies");
$cols = [];
while ($row = $r->fetch_assoc()) {
  $cols[] = $row['Field'];
}
echo ($in_array('reply_is_answer', $cols) ? "✅" : "❌") . " forums_replies.reply_is_answer\n";

echo "\n</pre>";

if (empty($errors)) {
  echo "<div class='success'>";
  echo "✅ Migration completed successfully!<br>";
  echo "🔒 IMPORTANT: Delete this file immediately!<br>";
  echo "<br>";
  echo "Via cPanel File Manager:<br>";
  echo "1. Find migrate_now.php in root directory<br>";
  echo "2. Right-click → Delete<br>";
  echo "<br>";
  echo "Then test: <a href='/forums' style='color: #0ff;'>https://nkhoj.com/forums</a>";
  echo "</div>";
} else {
  echo "<div class='error'>❌ Some errors occurred - see details above</div>";
}

echo "</body></html>";
?>
