<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/include/hrms.php';
function hrms_response($status, $message, $success = false) {
    http_response_code($status);
    echo json_encode(['success' => $success, $success ? 'message' : 'error' => $message]);
    exit;
}
if (empty($_SESSION['id']) || empty($_SESSION['username'])) hrms_response(401, 'Please sign in again.');
if (!hrms_is_main_admin($_SESSION['role'] ?? '')) hrms_response(403, 'Only Main Admin can manage HRMS settings.');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') hrms_response(405, 'Use POST to save settings.');
if (!is_string($_POST['csrf_token'] ?? null) || empty($_SESSION['hrms_csrf']) || !hash_equals($_SESSION['hrms_csrf'], $_POST['csrf_token'])) {
    hrms_response(403, 'Your session has expired. Reload this page and try again.');
}
require_once __DIR__ . '/dbConnect.php';
try {
    $action = $_POST['action'] ?? '';
    if ($action === 'save_weekoff') {
        hrms_save_weekoff($db, $_POST['weekoff_day'] ?? '', $_SESSION['id']);
        hrms_response(200, 'Weekoff saved. Attendance calendars now use this day.', true);
    }
    if ($action === 'save_access') {
        $targets = $_POST['target_ids'] ?? [];
        if (!is_array($targets)) throw new InvalidArgumentException('Select valid dashboards.');
        hrms_save_dashboard_access($db, (int)($_POST['viewer_id'] ?? 0), $targets, $_SESSION['id']);
        hrms_response(200, 'Dashboard access updated successfully.', true);
    }
    hrms_response(422, 'Invalid action.');
} catch (InvalidArgumentException $e) {
    hrms_response(422, $e->getMessage());
} catch (Exception $e) {
    error_log('HRMS save failed: ' . $e->getMessage());
    hrms_response(500, 'Settings could not be saved. Please try again.');
}
