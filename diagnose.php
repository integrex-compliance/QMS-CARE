<?php
// Ensure this script NEVER throws 500
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>QMS Care - Live Diagnostics</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, monospace; padding: 25px; background: #0f172a; color: #e2e8f0; line-height: 1.6; }
        .card { background: #1e293b; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; border: 1px solid #334155; }
        h3 { margin-top: 0; color: #38bdf8; border-bottom: 1px solid #334155; padding-bottom: 6px; font-size: 16px; }
        .ok { color: #4ade80; font-weight: bold; }
        .err { color: #f87171; font-weight: bold; }
        .warn { color: #fbbf24; font-weight: bold; }
        pre { background: #020617; color: #f1f5f9; padding: 12px; border-radius: 6px; overflow-x: auto; font-size: 13px; max-height: 350px; overflow-y: auto; }
    </style>
</head>
<body>
    <h2 style="color: #38bdf8;">QMS Care - Live System Diagnostics</h2>

    <div class="card">
        <h3>1. PHP Environment</h3>
        <div>PHP Version: <strong><?php echo PHP_VERSION; ?></strong></div>
        <div>PHP SAPI: <strong><?php echo php_sapi_name(); ?></strong></div>
        <div>display_errors: <strong><?php echo ini_get('display_errors'); ?></strong></div>
        <div>mbstring extension: <?php echo extension_loaded('mbstring') ? '<span class="ok">✔ Enabled</span>' : '<span class="warn">✖ Disabled (Safe fallbacks in use)</span>'; ?></div>
        <div>PDO extension: <?php echo extension_loaded('pdo') ? '<span class="ok">✔ Enabled</span>' : '<span class="err">✖ Missing</span>'; ?></div>
        <div>PDO MySQL: <?php echo extension_loaded('pdo_mysql') ? '<span class="ok">✔ Enabled</span>' : '<span class="err">✖ Missing</span>'; ?></div>
    </div>

    <div class="card">
        <h3>2. Recent cPanel Error Log (Last 20 entries)</h3>
        <?php
        $logFiles = [
            __DIR__ . '/error_log',
            __DIR__ . '/../error_log',
            __DIR__ . '/php_errors.log'
        ];
        $foundLog = null;
        foreach ($logFiles as $lf) {
            if (file_exists($lf) && is_readable($lf)) {
                $foundLog = $lf;
                break;
            }
        }
        if ($foundLog) {
            echo "<div>Found log file at: <code>" . htmlspecialchars($foundLog) . "</code> (Size: " . round(filesize($foundLog) / 1024, 1) . " KB)</div>";
            $lines = file($foundLog);
            if (!empty($lines)) {
                $lastLines = array_slice($lines, -20);
                echo "<pre>" . htmlspecialchars(implode("", $lastLines)) . "</pre>";
            } else {
                echo "<div class='ok'>Log file is currently empty.</div>";
            }
        } else {
            echo "<div class='warn'>No readable error_log found in directory.</div>";
        }
        ?>
    </div>

    <div class="card">
        <h3>3. Database Connection Check</h3>
        <?php
        $dbConnected = false;
        if (file_exists(__DIR__ . '/dbConnect.php')) {
            echo "<div>✔ <code>dbConnect.php</code> exists.</div>";
            // Test connection using dbConnect.php
            try {
                include_once __DIR__ . '/dbConnect.php';
                if (isset($db) && $db instanceof PDO) {
                    $dbConnected = true;
                    $q = $db->query("SELECT DATABASE() as dbname, USER() as dbuser, VERSION() as mysql_version");
                    $info = $q->fetch(PDO::FETCH_ASSOC);
                    echo "<div class='ok'>✔ Database connected successfully!</div>";
                    echo "<div>Database Name: <strong>" . htmlspecialchars(isset($info['dbname']) ? $info['dbname'] : '') . "</strong></div>";
                    echo "<div>Database User: <strong>" . htmlspecialchars(isset($info['dbuser']) ? $info['dbuser'] : '') . "</strong></div>";
                    echo "<div>MySQL Version: <strong>" . htmlspecialchars(isset($info['mysql_version']) ? $info['mysql_version'] : '') . "</strong></div>";
                } else {
                    echo "<div class='err'>✖ \$db is not set after dbConnect.php.</div>";
                }
            } catch (Exception $e) {
                echo "<div class='err'>✖ Connection Exception: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            echo "<div class='err'>✖ dbConnect.php not found!</div>";
        }
        ?>
    </div>

    <div class="card">
        <h3>4. File Checks</h3>
        <?php
        $files = [
            'myDashboard.php',
            'header.php',
            'head.php',
            'utility.php',
            'modal_forms.php',
            'include/task_manager_component.php',
            'include/attendance_calendar_component.php',
            'task_ajax_action.php',
            'guideline_action.php'
        ];
        foreach ($files as $f) {
            $p = __DIR__ . '/' . $f;
            if (file_exists($p)) {
                echo "<div><span class='ok'>✔</span> <code>$f</code> (" . round(filesize($p) / 1024, 1) . " KB)</div>";
            } else {
                echo "<div><span class='err'>✖</span> <code>$f</code> <span class='err'>MISSING</span></div>";
            }
        }
        ?>
    </div>

    <div class="card">
        <h3>5. Session Status</h3>
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        echo "<div>Session ID: <code>" . (session_id() ?: 'none') . "</code></div>";
        echo "<div>Username: <strong>" . htmlspecialchars(isset($_SESSION['username']) ? $_SESSION['username'] : 'Not Logged In') . "</strong></div>";
        echo "<div>User Role: <strong>" . htmlspecialchars(isset($_SESSION['role']) ? $_SESSION['role'] : 'None') . "</strong></div>";
        ?>
    </div>
</body>
</html>
