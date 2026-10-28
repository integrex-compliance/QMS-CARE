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
    echo json_encode(['success' => false, 'error' => 'Please log in to perform this action.']);
    exit();
}

$action = $_POST['action'] ?? ($_GET['action'] ?? '');

// 1. CREATE GUIDELINE
if ($action === 'create_guideline') {
    $title = trim($_POST['guideline_title'] ?? '');
    $description = trim($_POST['guideline_description'] ?? '');

    if ($title === '') {
        echo json_encode(['success' => false, 'error' => 'Guideline title cannot be empty.']);
        exit();
    }

    $uploadedFiles = [];
    if (!empty($_FILES['guideline_files']['name'])) {
        $uploadDir = __DIR__ . '/uploads/guidelines/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $names = $_FILES['guideline_files']['name'];
        $tmpNames = $_FILES['guideline_files']['tmp_name'];
        $sizes = $_FILES['guideline_files']['size'];

        for ($i = 0; $i < count($names); $i++) {
            if (!empty($names[$i]) && is_uploaded_file($tmpNames[$i])) {
                $origName = basename($names[$i]);
                $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
                $targetPath = $uploadDir . $safeName;
                if (move_uploaded_file($tmpNames[$i], $targetPath)) {
                    $uploadedFiles[] = [
                        'file_name' => $safeName,
                        'original_name' => $origName,
                        'file_size' => $sizes[$i] ?? 0,
                        'file_url' => 'uploads/guidelines/' . $safeName
                    ];
                }
            }
        }
    }

    try {
        $stmt = $db->prepare("
            INSERT INTO `staff_guidelines` (`title`, `description`, `attachments`, `created_by`, `created_at`, `is_deleted`) 
            VALUES (:title, :description, :attachments, :created_by, NOW(), 0)
        ");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':attachments' => !empty($uploadedFiles) ? json_encode($uploadedFiles) : null,
            ':created_by' => (int)$_SESSION['id']
        ]);
        $guidelineId = (int)$db->lastInsertId();

        echo json_encode([
            'success' => true,
            'guideline_id' => $guidelineId,
            'message' => 'Staff guideline created successfully!'
        ]);
        exit();
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

// 2. ASSIGN GUIDELINE TO USERS
if ($action === 'assign_guideline') {
    $guidelineId = (int)($_POST['guideline_id'] ?? 0);
    $userIds = $_POST['user_ids'] ?? [];

    if ($guidelineId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid guideline ID.']);
        exit();
    }

    try {
        // Clear previous assignments for this guideline
        $delStmt = $db->prepare("DELETE FROM `staff_guideline_recipients` WHERE `guideline_id` = :gid");
        $delStmt->execute([':gid' => $guidelineId]);

        $assignedCount = 0;
        if (!empty($userIds) && is_array($userIds)) {
            $insStmt = $db->prepare("INSERT INTO `staff_guideline_recipients` (`guideline_id`, `user_id`, `assigned_at`) VALUES (:gid, :uid, NOW())");
            foreach ($userIds as $uid) {
                $uid = (int)$uid;
                if ($uid > 0) {
                    $insStmt->execute([':gid' => $guidelineId, ':uid' => $uid]);
                    $assignedCount++;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'assigned_count' => $assignedCount,
            'message' => "Guideline sent to {$assignedCount} user(s)!"
        ]);
        exit();
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

// 3. DELETE GUIDELINE
if ($action === 'delete_guideline') {
    $guidelineId = (int)($_POST['guideline_id'] ?? 0);
    if ($guidelineId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid guideline ID.']);
        exit();
    }

    try {
        $stmt = $db->prepare("UPDATE `staff_guidelines` SET `is_deleted` = 1 WHERE `id` = :id");
        $stmt->execute([':id' => $guidelineId]);
        echo json_encode(['success' => true, 'message' => 'Guideline deleted.']);
        exit();
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid action.']);
