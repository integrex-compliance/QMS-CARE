<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Kolkata');
include_once __DIR__ . '/dbConnect.php';

if (!function_exists('aitf_get_task_manager_columns')) {
    function aitf_get_task_manager_columns(PDO $db) {
        static $cols = null;
        if ($cols !== null) {
            return $cols;
        }
        $cols = [];
        try {
            $stmt = $db->query("SHOW COLUMNS FROM `task_manager`");
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cols[strtolower($r['Field'])] = true;
            }
        } catch (Exception $e) {}
        return $cols;
    }
}

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Please log in to perform this action.']);
    exit();
}

if (!function_exists('aitf_is_main_admin_role')) {
    function aitf_is_main_admin_role($role) {
        $r = strtolower(str_replace([' ', '_', '-'], '', (string)$role));
        return in_array($r, ['mainadmin', 'topmgmt', 'topmanagement', 'director'], true);
    }
}
if (!function_exists('aitf_can_manage_tasks')) {
    function aitf_can_manage_tasks($role) {
        $r = strtolower(str_replace([' ', '_', '-'], '', (string)$role));
        return in_array($r, ['mainadmin', 'admin', 'finance', 'bdm', 'officebdm', 'topmgmt', 'topmanagement', 'director', 'taskmanager'], true);
    }
}
if (!function_exists('aitf_can_delete_tasks')) {
    function aitf_can_delete_tasks($role) {
        $r = strtolower(str_replace([' ', '_', '-'], '', (string)$role));
        return in_array($r, ['mainadmin', 'admin', 'finance', 'topmgmt', 'topmanagement', 'director'], true);
    }
}

$currentUserId = (int)$_SESSION['id'];
$currentUserRole = (string)(isset($_SESSION['role']) ? $_SESSION['role'] : '');
$currentUsername = (string)(isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$isCurrentMainAdmin = aitf_is_main_admin_role($currentUserRole);

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'add_task') {
    $targetUid = (int)(isset($_POST['target_user_id']) ? $_POST['target_user_id'] : $currentUserId);
    if ($targetUid <= 0) {
        $targetUid = $currentUserId;
    }

    // Role permission check for assigning tasks to other users
    if ($targetUid !== $currentUserId) {
        if (!aitf_can_manage_tasks($currentUserRole)) {
            echo json_encode(['success' => false, 'error' => 'Permission denied. You cannot assign tasks to other users.']);
            exit();
        }

        $targetStmt = $db->prepare("SELECT role, user_name FROM `user` WHERE user_id = :uid LIMIT 1");
        $targetStmt->execute([':uid' => $targetUid]);
        $targetRow = $targetStmt->fetch(PDO::FETCH_ASSOC);
        if (!$targetRow) {
            echo json_encode(['success' => false, 'error' => 'Target user not found.']);
            exit();
        }

        $targetRole = isset($targetRow['role']) ? $targetRow['role'] : '';
        // Rule: Only Main Admin can assign tasks to Main Admin.
        // Admin, BDM, and Finance can assign tasks to everyone EXCEPT Main Admin.
        if (!$isCurrentMainAdmin && aitf_is_main_admin_role($targetRole)) {
            echo json_encode(['success' => false, 'error' => 'Permission denied. Admin, BDM, and Finance cannot assign tasks to Main Admin.']);
            exit();
        }
    }

    $title = trim(isset($_POST['title']) ? $_POST['title'] : '');
    if ($title === '') {
        echo json_encode(['success' => false, 'error' => 'Task title cannot be empty.']);
        exit();
    }

    $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $reminder = !empty($_POST['reminder_time']) ? trim($_POST['reminder_time']) : null;
    $isImportant = !empty($_POST['is_important']) ? 1 : 0;
    $priority = $isImportant ? 'High' : 'Normal';
    $additionalUids = isset($_POST['additional_user_ids']) ? $_POST['additional_user_ids'] : [];

    $nowStr = date('Y-m-d H:i:s');
    $curDate = date('Y-m-d');
    $assignedDateFormatted = date('D, j M Y, h:i A', strtotime($nowStr));

    try {
        $tmCols = aitf_get_task_manager_columns($db);

        $fields = ['userid', 'task', 'comment', 'date', 'update_date', 'status', 'priority', 'is_deleted'];
        $placeholders = [':uid', ':task', "''", ':task_date', ':update_date', "'Pending'", ':priority', "'N'"];
        $params = [
            ':uid' => $targetUid,
            ':task' => $title,
            ':task_date' => $nowStr,
            ':update_date' => $curDate,
            ':priority' => $priority
        ];

        if (isset($tmCols['due_date'])) {
            $fields[] = 'due_date';
            $placeholders[] = ':due_date';
            $params[':due_date'] = $dueDate;
        }
        if (isset($tmCols['reminder_time'])) {
            $fields[] = 'reminder_time';
            $placeholders[] = ':reminder_time';
            $params[':reminder_time'] = $reminder;
        }
        if (isset($tmCols['is_important'])) {
            $fields[] = 'is_important';
            $placeholders[] = ':is_important';
            $params[':is_important'] = $isImportant;
        }
        if (isset($tmCols['assigned_by'])) {
            $fields[] = 'assigned_by';
            $placeholders[] = ':assigned_by';
            $params[':assigned_by'] = $currentUserId;
        }
        if (isset($tmCols['assigned_by_name'])) {
            $fields[] = 'assigned_by_name';
            $placeholders[] = ':assigned_by_name';
            $params[':assigned_by_name'] = $currentUsername;
        }

        $sql = "INSERT INTO `task_manager` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $primaryTaskId = (int)$db->lastInsertId();

        // Also assign to additional users if specified
        if (!empty($additionalUids) && is_array($additionalUids) && aitf_can_manage_tasks($currentUserRole)) {
            foreach ($additionalUids as $addUid) {
                $addUid = (int)$addUid;
                if ($addUid > 0 && $addUid !== $targetUid) {
                    // If not main admin, ensure additional user is not a Main Admin
                    if (!$isCurrentMainAdmin) {
                        $cStmt = $db->prepare("SELECT role FROM `user` WHERE user_id = :uid LIMIT 1");
                        $cStmt->execute([':uid' => $addUid]);
                        $cRow = $cStmt->fetch(PDO::FETCH_ASSOC);
                        if ($cRow && aitf_is_main_admin_role(isset($cRow['role']) ? $cRow['role'] : '')) {
                            continue; // Skip assigning to Main Admin
                        }
                    }
                    $addParams = $params;
                    $addParams[':uid'] = $addUid;
                    $addStmt = $db->prepare($sql);
                    $addStmt->execute($addParams);
                }
            }
        }

        // Fetch user name of assigned_to
        $targetName = $currentUsername;
        $uStmt = $db->prepare("SELECT user_name FROM `user` WHERE user_id = :uid LIMIT 1");
        $uStmt->execute([':uid' => $targetUid]);
        $uRow = $uStmt->fetch(PDO::FETCH_ASSOC);
        if ($uRow && !empty($uRow['user_name'])) {
            $targetName = $uRow['user_name'];
        }

        $taskData = [
            'id' => $primaryTaskId,
            'title' => $title,
            'task' => $title,
            'status' => 'pending',
            'is_important' => $isImportant,
            'priority' => $priority,
            'due_date' => $dueDate,
            'reminder_time' => $reminder,
            'created_at' => $nowStr,
            'assigned_date_formatted' => $assignedDateFormatted,
            'assigned_by_user_id' => $currentUserId,
            'assigned_by_name' => $currentUsername,
            'assigned_to_user_id' => $targetUid,
            'assigned_to_name' => $targetName,
            'completed_at' => null
        ];

        echo json_encode(['success' => true, 'task' => $taskData, 'message' => 'Task created successfully!']);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

if ($action === 'toggle_status') {
    $taskId = (int)(isset($_POST['task_id']) ? $_POST['task_id'] : 0);
    if ($taskId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid task ID.']);
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT id, status, userid FROM `task_manager` WHERE id = :id AND is_deleted = 'N' LIMIT 1");
        $stmt->execute([':id' => $taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$task) {
            echo json_encode(['success' => false, 'error' => 'Task not found.']);
            exit();
        }

        if (!aitf_can_manage_tasks($currentUserRole) && (int)$task['userid'] !== $currentUserId) {
            echo json_encode(['success' => false, 'error' => 'Permission denied. You can only update your own tasks.']);
            exit();
        }

        $isCurrentlyCompleted = (strtolower(trim($task['status'])) === 'completed');
        $nowStr = date('Y-m-d H:i:s');
        $curDate = date('Y-m-d');
        $tmCols = aitf_get_task_manager_columns($db);
        $hasCompleteDate = isset($tmCols['complete_date']);
        $hasCompletedAt = isset($tmCols['completed_at']);

        if ($isCurrentlyCompleted) {
            $sets = ["status = 'Pending'", "update_date = :udate"];
            $params = [':udate' => $curDate, ':id' => $taskId];
            if ($hasCompleteDate) {
                $sets[] = "complete_date = NULL";
            }
            if ($hasCompletedAt) {
                $sets[] = "completed_at = NULL";
            }
            $updateStmt = $db->prepare("UPDATE `task_manager` SET " . implode(', ', $sets) . " WHERE id = :id");
            $updateStmt->execute($params);
            $completedAtFormatted = null;
            $newStatusClient = 'pending';
        } else {
            $sets = ["status = 'Completed'", "update_date = :udate"];
            $params = [':udate' => $curDate, ':id' => $taskId];
            if ($hasCompleteDate) {
                $sets[] = "complete_date = :cdate";
                $params[':cdate'] = $curDate;
            }
            if ($hasCompletedAt) {
                $sets[] = "completed_at = :cat";
                $params[':cat'] = $nowStr;
            }
            $updateStmt = $db->prepare("UPDATE `task_manager` SET " . implode(', ', $sets) . " WHERE id = :id");
            $updateStmt->execute($params);
            $completedAtFormatted = date('D, j M Y, h:i A', strtotime($nowStr));
            $newStatusClient = 'completed';
        }

        echo json_encode([
            'success' => true,
            'new_status' => $newStatusClient,
            'completed_at' => $isCurrentlyCompleted ? null : $nowStr,
            'completed_at_formatted' => $completedAtFormatted
        ]);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

if ($action === 'toggle_star') {
    $taskId = (int)(isset($_POST['task_id']) ? $_POST['task_id'] : 0);
    if ($taskId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid task ID.']);
        exit();
    }

    try {
        $stmt = $db->prepare("SELECT id, is_important, priority, userid FROM `task_manager` WHERE id = :id AND is_deleted = 'N' LIMIT 1");
        $stmt->execute([':id' => $taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$task) {
            echo json_encode(['success' => false, 'error' => 'Task not found.']);
            exit();
        }

        if (!aitf_can_manage_tasks($currentUserRole) && (int)$task['userid'] !== $currentUserId) {
            echo json_encode(['success' => false, 'error' => 'Permission denied. You can only update your own tasks.']);
            exit();
        }

        $tmCols = aitf_get_task_manager_columns($db);
        $hasIsImportant = isset($tmCols['is_important']);
        $hasPriority = isset($tmCols['priority']);

        $taskIsImp = (isset($task['is_important']) ? (int)$task['is_important'] : 0);
        $taskPrio = (isset($task['priority']) ? strtolower(trim((string)$task['priority'])) : '');
        $isStar = ($hasIsImportant && $taskIsImp === 1) || ($taskPrio === 'high');
        $newStar = $isStar ? 0 : 1;
        $newPriority = $newStar ? 'High' : 'Normal';

        $sets = [];
        $params = [':id' => $taskId];
        if ($hasPriority) {
            $sets[] = "priority = :prio";
            $params[':prio'] = $newPriority;
        }
        if ($hasIsImportant) {
            $sets[] = "is_important = :star";
            $params[':star'] = $newStar;
        }
        if (!empty($sets)) {
            $updateStmt = $db->prepare("UPDATE `task_manager` SET " . implode(', ', $sets) . " WHERE id = :id");
            $updateStmt->execute($params);
        }

        echo json_encode(['success' => true, 'is_important' => $newStar]);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

if ($action === 'delete_task') {
    $taskId = (int)(isset($_POST['task_id']) ? $_POST['task_id'] : 0);
    if ($taskId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid task ID.']);
        exit();
    }

    if (!aitf_can_delete_tasks($currentUserRole)) {
        echo json_encode(['success' => false, 'error' => 'Permission denied. Only Admins / Top Management can delete tasks.']);
        exit();
    }

    try {
        $updateStmt = $db->prepare("UPDATE `task_manager` SET is_deleted = 'Y', update_date = CURDATE() WHERE id = :id");
        $updateStmt->execute([':id' => $taskId]);
        echo json_encode(['success' => true, 'message' => 'Task deleted.']);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid action.']);
