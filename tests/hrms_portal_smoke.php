<?php
// Read-only integration check against the configured local database.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
chdir(dirname(__DIR__));
require_once 'dbConnect.php';
require_once 'include/hrms.php';
hrms_ensure_schema($db);
$mode = $argv[1] ?? 'admin';
$users = hrms_active_users($db);
$chosen = null;
foreach ($users as $user) {
    if (($mode === 'admin' || $mode === 'calendar') === hrms_is_main_admin($user['role'])) {
        if ($mode === 'deny' && hrms_dashboard_targets($db, $user['user_id'])) continue;
        $chosen = $user;
        break;
    }
}
if (!$chosen) throw new RuntimeException('No suitable existing user for read-only smoke check.');
session_start();
$_SESSION = ['id' => (int)$chosen['user_id'], 'username' => $chosen['user_name'], 'role' => $chosen['role'], 'abbrevation' => $chosen['abbreviation']];
$bufferLevel = ob_get_level();
ob_start();
register_shutdown_function(function () use ($bufferLevel, $mode) {
    $output = '';
    while (ob_get_level() > $bufferLevel) $output = ob_get_clean() . $output;
    if (session_status() === PHP_SESSION_ACTIVE) session_abort();
    $ok = strpos($output, 'Fatal error') === false && strpos($output, 'Warning:') === false;
    if ($mode === 'admin') $ok = $ok && strpos($output, 'id="hrmsAccessForm"') !== false && strpos($output, 'id="hrmsWeekoffForm"') !== false;
    if ($mode === 'readonly') $ok = $ok && strpos($output, 'Your allowed dashboards') !== false && strpos($output, 'id="hrmsAccessForm"') === false;
    if ($mode === 'deny') $ok = $ok && http_response_code() === 403 && strpos($output, 'You do not have access to this dashboard.') !== false;
    if ($mode === 'calendar') $ok = $ok && strpos($output, 'var weekoffDay = ') !== false && strpos($output, 'id="sumWeekoffCount"') !== false;
    if (!$ok) { fwrite(STDERR, 'FAIL: portal smoke ' . $mode . PHP_EOL); exit(1); }
    echo 'PASS: portal smoke ' . $mode . PHP_EOL;
});
if ($mode === 'deny') {
    foreach ($users as $user) {
        if ((int)$user['user_id'] !== (int)$chosen['user_id']) { $_GET['view_user'] = 'user_' . $user['user_id']; break; }
    }
    include 'myDashboard.php';
} elseif ($mode === 'calendar') {
    $targetAttendanceUid = (int)$chosen['user_id'];
    include 'include/attendance_calendar_component.php';
} else {
    include 'hrms.php';
}
