<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Kolkata');
include_once __DIR__ . '/dbConnect.php';

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
if ($targetUserId !== $currentUserId && !$isManager) {
    echo json_encode(['success' => false, 'error' => 'Permission denied to manage other users attendance.']);
    exit();
}

$dateStr = isset($_POST['date']) ? trim($_POST['date']) : '';
if (!empty($dateStr) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
    echo json_encode(['success' => false, 'error' => 'Invalid date format (YYYY-MM-DD required).']);
    exit();
}

$systemCode = 'QMS_CARE';

if ($action === 'mark') {
    if (empty($dateStr)) {
        echo json_encode(['success' => false, 'error' => 'Date is required.']);
        exit();
    }
    $status = isset($_POST['status']) ? strtolower(trim($_POST['status'])) : 'present';
    $validStatuses = ['present', 'late', 'halfday', 'leave', 'holiday'];
    if (!in_array($status, $validStatuses, true)) {
        $status = 'present';
    }

    $inTime = isset($_POST['in_time']) ? trim($_POST['in_time']) : null;
    $outTime = isset($_POST['out_time']) ? trim($_POST['out_time']) : null;
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

    if (empty($inTime)) {
        if ($status === 'present') {
            $inTime = '09:30:00';
            $outTime = '18:30:00';
            if (empty($remarks)) $remarks = 'Full day attendance';
        } elseif ($status === 'late') {
            $inTime = '10:15:00';
            $outTime = '18:30:00';
            if (empty($remarks)) $remarks = 'Late login marked';
        } elseif ($status === 'halfday') {
            $inTime = '09:30:00';
            $outTime = '14:00:00';
            if (empty($remarks)) $remarks = 'Half day shift';
        } elseif ($status === 'leave') {
            $inTime = null;
            $outTime = null;
            if (empty($remarks)) $remarks = 'Casual / Sick Leave';
        } elseif ($status === 'holiday') {
            $inTime = null;
            $outTime = null;
            if (empty($remarks)) $remarks = 'Company Holiday';
        }
    } else {
        $inTime = date('H:i:s', strtotime($inTime));
        if (!empty($outTime)) {
            $outTime = date('H:i:s', strtotime($outTime));
        }
    }

    $workingHours = 0.00;
    if (!empty($inTime) && !empty($outTime)) {
        $sec = strtotime($dateStr . ' ' . $outTime) - strtotime($dateStr . ' ' . $inTime);
        if ($sec > 0) {
            $workingHours = round($sec / 3600, 2);
        }
    }

    // Check if record exists
    $checkStmt = $db->prepare("SELECT id FROM aitf_attendance WHERE user_id = ? AND attendance_date = ?");
    $checkStmt->execute([$targetUserId, $dateStr]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $upStmt = $db->prepare("
            UPDATE aitf_attendance 
            SET status = ?, in_time = ?, out_time = ?, total_working_hours = ?, remarks = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $upStmt->execute([$status, $inTime, $outTime, $workingHours, $remarks, $existing['id']]);
    } else {
        $insStmt = $db->prepare("
            INSERT INTO aitf_attendance 
            (user_id, system_code, attendance_date, status, in_time, out_time, total_working_hours, remarks, in_address, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Web Portal', NOW(), NOW())
        ");
        $insStmt->execute([$targetUserId, $systemCode, $dateStr, $status, $inTime, $outTime, $workingHours, $remarks]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Attendance saved successfully.',
        'data' => [
            'status' => $status,
            'label' => ($status === 'late') ? 'Late Login' : (($status === 'halfday') ? 'Half Day' : ucfirst($status)),
            'inTime' => !empty($inTime) ? date('h:i A', strtotime($inTime)) : '--',
            'outTime' => !empty($outTime) ? date('h:i A', strtotime($outTime)) : '--',
            'workingHours' => $workingHours,
            'note' => $remarks
        ]
    ]);
    exit();
} elseif ($action === 'clear') {
    if (empty($dateStr)) {
        echo json_encode(['success' => false, 'error' => 'Date is required.']);
        exit();
    }
    $delStmt = $db->prepare("DELETE FROM aitf_attendance WHERE user_id = ? AND attendance_date = ?");
    $delStmt->execute([$targetUserId, $dateStr]);
    echo json_encode(['success' => true, 'message' => 'Attendance record cleared.']);
    exit();
} elseif ($action === 'fetch_month') {
    $year = isset($_POST['year']) ? (int)$_POST['year'] : (int)date('Y');
    $month = isset($_POST['month']) ? (int)$_POST['month'] : (int)date('m');
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
    echo json_encode(['success' => true, 'records' => $records]);
    exit();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action.']);
    exit();
}
