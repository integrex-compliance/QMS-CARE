<?php
session_start();
include_once __DIR__ . '/dbConnect.php';
require_once __DIR__ . '/include/hrms.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Role restriction: Only Admin, Main Admin, Finance (and Top Management / Director)
$userRoleClean = strtolower(trim((string)(isset($_SESSION['role']) ? $_SESSION['role'] : '')));
$userRoleCleanNorm = str_replace([' ', '_', '-'], '', $userRoleClean);
$isAuthorized = in_array($userRoleClean, ['main admin', 'admin', 'finance', 'top mgmt', 'top management', 'director'], true)
    || in_array($userRoleCleanNorm, ['mainadmin', 'admin', 'finance', 'topmgmt', 'topmanagement', 'director'], true);

if (!$isAuthorized) {
    echo json_encode(['success' => false, 'error' => 'Permission denied. Only Admin, Main Admin, and Finance can update records.']);
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'update_profile_records') {
    $targetUid = (int)(isset($_POST['target_user_id']) ? $_POST['target_user_id'] : (isset($_SESSION['id']) ? $_SESSION['id'] : 0));
    if ($targetUid <= 0) {
        $targetUid = (int)(isset($_SESSION['id']) ? $_SESSION['id'] : 0);
    }
    if (!hrms_can_view_dashboard($db, $_SESSION['id'], $_SESSION['role'] ?? '', $targetUid)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'You do not have access to this dashboard.']);
        exit;
    }

    $applicableRecords = isset($_POST['applicable_records']) ? $_POST['applicable_records'] : '';
    $resourceIssueRecord = isset($_POST['resource_issue_record']) ? $_POST['resource_issue_record'] : '';
    $workDescription = isset($_POST['work_description']) ? $_POST['work_description'] : '';
    $emailId = trim(isset($_POST['email_id']) ? $_POST['email_id'] : '');

    try {
        $sql = "UPDATE `user` SET 
                `applicable_records` = :applicable_records,
                `resource_issue_record` = :resource_issue_record,
                `work_description` = :work_description";
        $params = [
            ':applicable_records' => $applicableRecords,
            ':resource_issue_record' => $resourceIssueRecord,
            ':work_description' => $workDescription,
            ':uid' => $targetUid
        ];

        if ($emailId !== '') {
            $sql .= ", `email_id` = :email_id";
            $params[':email_id'] = $emailId;
        }

        $sql .= " WHERE `user_id` = :uid";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success' => true, 'message' => 'Profile records updated successfully.']);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
