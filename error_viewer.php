<?php
/**
 * Error Log Viewer
 * Shows recent PHP errors from nkhoj.com
 */

?><!DOCTYPE html>
<html>
<head>
<title>Error Log Viewer</title>
<style>
  body { font-family: monospace; background: #222; color: #0f0; padding: 20px; }
  .container { max-width: 1000px; margin: 0 auto; }
  h1 { color: #0ff; }
  .error { background: #400; padding: 10px; margin: 10px 0; border-left: 3px solid #f00; }
  .info { background: #004; padding: 10px; margin: 10px 0; border-left: 3px solid #0f0; }
  .warning { background: #440; padding: 10px; margin: 10px 0; border-left: 3px solid #f80; }
  pre { overflow-x: auto; }
</style>
</head>
<body>
<div class="container">
<h1>📋 nkhoj.com Error Log</h1>

<?php
$error_log = __DIR__ . '/error_log';

if (!file_exists($error_log)) {
  echo '<div class="info">✅ No error_log found - that\'s a good sign!</div>';
} else {
  $lines = file($error_log, FILE_IGNORE_NEW_LINES);
  $total = count($lines);

  echo "<div class='info'>📊 Total lines: $total</div>";
  echo "<div class='info'>📅 Last 50 lines:</div>";
  echo "<pre>";

  // Get last 50 lines
  $last_lines = array_slice($lines, -50);

  foreach ($last_lines as $line) {
    // Color code by error type
    if (stripos($line, 'Fatal error') !== false || stripos($line, 'Parse error') !== false) {
      echo "<div class='error'>$line</div>";
    } elseif (stripos($line, 'Warning') !== false) {
      echo "<div class='warning'>$line</div>";
    } elseif (stripos($line, 'Notice') !== false) {
      echo "<div class='warning'>$line</div>";
    } else {
      echo htmlspecialchars($line) . "\n";
    }
  }

  echo "</pre>";

  // Check for forum-related errors
  echo "<h2>🔍 Forum-Related Errors</h2>";
  $forum_errors = array_filter($last_lines, function($line) {
    return stripos($line, 'forums') !== false ||
           stripos($line, 'subscriptions') !== false ||
           stripos($line, 'moderation') !== false;
  });

  if (empty($forum_errors)) {
    echo "<div class='info'>✅ No forum-related errors found</div>";
  } else {
    echo "<pre>";
    foreach ($forum_errors as $error) {
      echo htmlspecialchars($error) . "\n";
    }
    echo "</pre>";
  }

  // Check database errors
  echo "<h2>🔍 Database-Related Errors</h2>";
  $db_errors = array_filter($last_lines, function($line) {
    return stripos($line, 'mysql') !== false ||
           stripos($line, 'database') !== false ||
           stripos($line, 'sql') !== false;
  });

  if (empty($db_errors)) {
    echo "<div class='info'>✅ No database errors found</div>";
  } else {
    echo "<pre>";
    foreach ($db_errors as $error) {
      echo htmlspecialchars($error) . "\n";
    }
    echo "</pre>";
  }
}
?>

<hr>
<p style="color: #888; font-size: 12px;">
  Viewer accessed: <?php echo date('Y-m-d H:i:s'); ?><br>
  Error log path: <?php echo $error_log; ?>
</p>

</div>
</body>
</html>
