<?php
session_start();
include_once __DIR__ . '/dbConnect.php';

if (isset($db) && $db instanceof PDO) {
    if (file_exists(__DIR__ . '/include/db_schema_sync.php')) {
        require_once __DIR__ . '/include/db_schema_sync.php';
        aitf_sync_database_schema($db);
    }
}

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Role restriction: Only Admin, Main Admin, Finance (and Top Management / Director)
$userRoleClean = strtolower(trim((string)($_SESSION['role'] ?? '')));
$userRoleCleanNorm = str_replace([' ', '_', '-'], '', $userRoleClean);
$isAuthorized = in_array($userRoleClean, ['main admin', 'admin', 'finance', 'top mgmt', 'top management', 'director'], true)
    || in_array($userRoleCleanNorm, ['mainadmin', 'admin', 'finance', 'topmgmt', 'topmanagement', 'director'], true);

if (!$isAuthorized) {
    echo json_encode(['success' => false, 'error' => 'Permission denied. Only Admin, Main Admin, and Finance can update records.']);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'update_profile_records') {
    $targetUid = (int)($_POST['target_user_id'] ?? $_SESSION['id']);
    if ($targetUid <= 0) {
        $targetUid = (int)$_SESSION['id'];
    }

    $applicableRecords = $_POST['applicable_records'] ?? '';
    $resourceIssueRecord = $_POST['resource_issue_record'] ?? '';
    $workDescription = $_POST['work_description'] ?? '';
    $emailId = trim($_POST['email_id'] ?? '');

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
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
