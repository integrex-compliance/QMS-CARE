<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Kolkata');
include_once __DIR__ . '/dbConnect.php';
require_once __DIR__ . '/include/hrms.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Please log in to perform this action.']);
    exit();
}

if (!isset($db) || !($db instanceof PDO)) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit();
}

$currentUserId = (int)$_SESSION['id'];
$currentUserRole = (string)(isset($_SESSION['role']) ? $_SESSION['role'] : '');

// Permission helpers
if (!function_exists('aitf_is_main_admin_role')) {
    function aitf_is_main_admin_role($role) {
        $r = strtolower(str_replace([' ', '_', '-'], '', (string)$role));
        return in_array($r, ['mainadmin', 'topmgmt', 'topmanagement', 'director'], true);
    }
}

if (!function_exists('aitf_can_manage_attendance')) {
    function aitf_can_manage_attendance($role) {
        $r = strtolower(str_replace([' ', '_', '-'], '', (string)$role));
        return in_array($r, ['mainadmin', 'admin', 'finance', 'bdm', 'officebdm', 'topmgmt', 'topmanagement', 'director', 'taskmanager'], true)
            || (class_exists('Utitlity') && (Utitlity::checkForMainAdmin() || Utitlity::checkForUserAdmin()));
    }
}

$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$targetUserId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : $currentUserId;
if ($targetUserId <= 0) {
    $targetUserId = $currentUserId;
}

// Can only manage other users if admin / manager
$isManager = aitf_can_manage_attendance($currentUserRole) || aitf_is_main_admin_role($currentUserRole);
if (!hrms_can_view_dashboard($db, $currentUserId, $currentUserRole, $targetUserId)
    || ($action !== 'fetch_month' && $targetUserId !== $currentUserId && !$isManager)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Permission denied to manage other users attendance.']);
    exit();
}

$dateStr = isset($_POST['date']) ? trim($_POST['date']) : '';
if (!empty($dateStr) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
    echo json_encode(['success' => false, 'error' => 'Invalid date format (YYYY-MM-DD required).']);
    exit();
}

$systemCode = 'QMS_CARE';

if ($action === 'mark' || $action === 'clear') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Manual attendance status modification is disabled.']);
    exit();
} elseif ($action === 'fetch_month') {
    $year = isset($_POST['year']) ? (int)$_POST['year'] : (int)date('Y');
    $month = isset($_POST['month']) ? (int)$_POST['month'] : (int)date('m');
    if ($year < 2000 || $year > 2100 || $month < 1 || $month > 12) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Invalid calendar month.']);
        exit;
    }
    $startDate = sprintf('%04d-%02d-01', $year, $month);
    $endDate = date('Y-m-t', strtotime($startDate));

    $fetchStmt = $db->prepare("
        SELECT attendance_date, status, in_time, out_time, total_working_hours, remarks, 
               punch_in_selfie_url, punch_out_selfie_url, 
               in_latitude, in_longitude, in_address, 
               out_latitude, out_longitude, out_address
        FROM aitf_attendance
        WHERE user_id = ? AND attendance_date BETWEEN ? AND ?
        ORDER BY attendance_date ASC
    ");
    $fetchStmt->execute([$targetUserId, $startDate, $endDate]);
    $rows = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);

    $records = [];
    foreach ($rows as $r) {
        $st = strtolower($r['status'] ?? 'present');
        $lbl = ($st === 'late') ? 'Late Login' : (($st === 'halfday') ? 'Half Day' : ucfirst($st));
        $inA = trim($r['in_address'] ?? '');
        if ($inA === '' && !empty($r['in_latitude']) && !empty($r['in_longitude'])) {
            $inA = $r['in_latitude'] . ', ' . $r['in_longitude'];
        }
        $outA = trim($r['out_address'] ?? '');
        if ($outA === '' && !empty($r['out_latitude']) && !empty($r['out_longitude'])) {
            $outA = $r['out_latitude'] . ', ' . $r['out_longitude'];
        }

        $records[$r['attendance_date']] = [
            'status' => $st,
            'label' => $lbl,
            'inTime' => !empty($r['in_time']) ? date('h:i A', strtotime($r['in_time'])) : '--',
            'outTime' => !empty($r['out_time']) ? date('h:i A', strtotime($r['out_time'])) : '--',
            'workingHours' => !empty($r['total_working_hours']) ? (float)$r['total_working_hours'] : null,
            'inAddress' => $inA ?: null,
            'outAddress' => $outA ?: null,
            'note' => $r['remarks'] ?? '',
            'selfieUrl' => $r['punch_in_selfie_url'] ?? '',
            'outSelfieUrl' => $r['punch_out_selfie_url'] ?? ''
        ];
    }
    $weekoffDay = hrms_weekoff_day($db);
    echo json_encode(['success' => true, 'records' => $records, 'weekoff_day' => $weekoffDay, 'weekoff_dates' => hrms_weekoff_dates($year, $month, $weekoffDay)]);
    exit();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action.']);
    exit();
}
