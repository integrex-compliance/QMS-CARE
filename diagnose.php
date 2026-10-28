<?php
ob_start();
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
        body { font-family: monospace; padding: 25px; background: #0f172a; color: #e2e8f0; line-height: 1.6; }
        .card { background: #1e293b; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; border: 1px solid #334155; }
        h3 { margin-top: 0; color: #38bdf8; border-bottom: 1px solid #334155; padding-bottom: 6px; }
        .ok { color: #4ade80; font-weight: bold; }
        .err { color: #f87171; font-weight: bold; }
        .warn { color: #fbbf24; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Diagnostics for qmscare.com</h2>

    <div class="card">
        <h3>1. PHP Environment</h3>
        <div>PHP Version: <strong><?php echo PHP_VERSION; ?></strong></div>
        <div>PHP SAPI: <strong><?php echo php_sapi_name(); ?></strong></div>
        <div>display_errors: <strong><?php echo ini_get('display_errors'); ?></strong></div>
        <div>mbstring extension: <?php echo extension_loaded('mbstring') ? '<span class="ok">✔ Enabled</span>' : '<span class="warn">✖ Disabled (safe fallbacks active)</span>'; ?></div>
        <div>PDO extension: <?php echo extension_loaded('pdo') ? '<span class="ok">✔ Enabled</span>' : '<span class="err">✖ Missing</span>'; ?></div>
        <div>PDO MySQL: <?php echo extension_loaded('pdo_mysql') ? '<span class="ok">✔ Enabled</span>' : '<span class="err">✖ Missing</span>'; ?></div>
    </div>

    <div class="card">
        <h3>2. Database Connection Test</h3>
        <?php
        try {
            if (file_exists(__DIR__ . '/dbConnect.php')) {
                require_once __DIR__ . '/dbConnect.php';
                if (isset($db) && $db instanceof PDO) {
                    echo "<div class='ok'>✔ dbConnect.php loaded and \$db connected successfully!</div>";
                    $testQuery = $db->query("SELECT DATABASE() as dbname, USER() as dbuser, VERSION() as mysql_version");
                    $dbInfo = $testQuery->fetch(PDO::FETCH_ASSOC);
                    echo "<div>Database Name: <strong>" . ($dbInfo['dbname'] ?? 'unknown') . "</strong></div>";
                    echo "<div>Database User: <strong>" . ($dbInfo['dbuser'] ?? 'unknown') . "</strong></div>";
                    echo "<div>MySQL Version: <strong>" . ($dbInfo['mysql_version'] ?? 'unknown') . "</strong></div>";
                } else {
                    echo "<div class='err'>✖ \$db is not an instance of PDO after dbConnect.php.</div>";
                }
            } else {
                echo "<div class='err'>✖ dbConnect.php not found!</div>";
            }
        } catch (Exception $e) {
            echo "<div class='err'>✖ Database Exception: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        ?>
    </div>

    <div class="card">
        <h3>3. Required Dashboard Files</h3>
        <?php
        $filesToCheck = [
            'header.php' => __DIR__ . '/header.php',
            'utility.php' => __DIR__ . '/utility.php',
            'modal_forms.php' => __DIR__ . '/modal_forms.php',
            'include/db_schema_sync.php' => __DIR__ . '/include/db_schema_sync.php',
            'include/attendance_calendar_component.php' => __DIR__ . '/include/attendance_calendar_component.php',
            'include/task_manager_component.php' => __DIR__ . '/include/task_manager_component.php',
            'myDashboard.php' => __DIR__ . '/myDashboard.php',
            'task_ajax_action.php' => __DIR__ . '/task_ajax_action.php',
            'guideline_action.php' => __DIR__ . '/guideline_action.php'
        ];

        foreach ($filesToCheck as $label => $path) {
            if (file_exists($path)) {
                $perms = substr(sprintf('%o', fileperms($path)), -4);
                echo "<div><span class='ok'>✔</span> <strong>$label</strong> (Permissions: $perms)</div>";
            } else {
                echo "<div><span class='err'>✖</span> <strong>$label</strong> - <span class='err'>FILE NOT FOUND</span></div>";
            }
        }
        ?>
    </div>

    <div class="card">
        <h3>4. Session & Role Simulation</h3>
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        echo "<div>Session ID: <strong>" . (session_id() ?: 'none') . "</strong></div>";
        echo "<div>Logged In Username: <strong>" . ($_SESSION['username'] ?? '<span class="warn">Not Logged In (Will redirect to index.php)</span>') . "</strong></div>";
        echo "<div>Logged In Role: <strong>" . ($_SESSION['role'] ?? 'None') . "</strong></div>";
        ?>
    </div>
</body>
</html>
