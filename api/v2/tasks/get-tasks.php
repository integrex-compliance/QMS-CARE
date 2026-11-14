<?php
// C:\xampp\htdocs\public_html\api\v1\tasks\get-tasks.php
require_once __DIR__ . '/../helpers/cors.php';
require_once __DIR__ . '/../helpers/auth_middleware.php';

date_default_timezone_set('Asia/Kolkata');

$user = requireAuth($db);
$userId = (int)$user['user_id'];
$canManageTasks = (bool)$user['can_manage_tasks'];
$canDeleteTasks = (bool)$user['can_delete_tasks'];

// Ensure bidirectional synchronization between web task_manager and mobile aitf_tasks
$syncHelper = dirname(__DIR__, 3) . '/include/task_sync_helper.php';
if (file_exists($syncHelper)) {
    require_once $syncHelper;
    if (function_exists('aitf_sync_qms_task_tables')) {
        aitf_sync_qms_task_tables($db, $user['system_code'] ?? 'QMS_CARE');
        if (isset($GLOBALS['aitf_identity_db']) && $GLOBALS['aitf_identity_db'] instanceof PDO && $GLOBALS['aitf_identity_db'] !== $db) {
            aitf_sync_qms_task_tables($GLOBALS['aitf_identity_db'], $user['system_code'] ?? 'QMS_CARE');
        }
    }
}

$statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
$searchQuery  = trim($_GET['search'] ?? '');
$scope        = strtolower(trim($_GET['scope'] ?? 'assigned_to_me'));
$targetUserId = (int)($_GET['target_user_id'] ?? $_GET['user_id'] ?? 0);

// Target resolution: Managers can view another employee's dashboard tasks
$activeTargetUid = ($canManageTasks && $targetUserId > 0) ? $targetUserId : $userId;
$isTargetingOther = ($activeTargetUid !== $userId);

// Base query:
// If viewing another employee's dashboard, show tasks for that employee.
// If viewing self, show tasks assigned to current user, or assigned by user, or all (if manager).
$where = ["t.is_deleted = 0", "(t.system_code = :system_code OR (t.system_code IN ('QMS_CARE', '01') AND :system_code IN ('QMS_CARE', '01')) OR t.system_code IS NULL OR t.system_code = '')"];
$params = [':system_code' => $user['system_code']];

if ($isTargetingOther) {
    if ($scope === 'my_assigned' || $scope === 'assigned_by_me') {
        $where[] = "t.assigned_by_user_id = :active_uid";
        $params[':active_uid'] = $activeTargetUid;
    } elseif ($scope === 'all') {
        $where[] = "(t.assigned_to_user_id = :active_uid OR t.assigned_by_user_id = :active_uid)";
        $params[':active_uid'] = $activeTargetUid;
    } else {
        $where[] = "t.assigned_to_user_id = :active_uid";
        $params[':active_uid'] = $activeTargetUid;
    }
} else {
    if (!$canManageTasks || $scope === 'assigned_to_me' || $scope === 'only_me') {
        $where[] = "t.assigned_to_user_id = :current_uid";
        $params[':current_uid'] = $userId;
    } elseif ($scope === 'my_assigned' || $scope === 'assigned_by_me') {
        $where[] = "t.assigned_by_user_id = :current_uid";
        $params[':current_uid'] = $userId;
    } elseif ($scope === 'all' && $canManageTasks) {
        // Managers can see all organization tasks
    } else {
        $where[] = "t.assigned_to_user_id = :current_uid";
        $params[':current_uid'] = $userId;
    }
}

// Status filter
$todayDate = date('Y-m-d');
if ($statusFilter === 'pending') {
    $where[] = "t.status = 'pending'";
} elseif ($statusFilter === 'completed') {
    $where[] = "t.status = 'completed'";
} elseif ($statusFilter === 'starred') {
    $where[] = "t.is_important = 1";
} elseif ($statusFilter === 'today') {
    $where[] = "(t.due_date = :today_date OR (t.due_date IS NULL AND DATE(t.created_at) = :today_date))";
    $params[':today_date'] = $todayDate;
}

// Search query
if (!empty($searchQuery)) {
    $where[] = "(t.title LIKE :search OR t.description LIKE :search OR t.assigned_to_name LIKE :search)";
    $params[':search'] = "%{$searchQuery}%";
}

$whereClause = implode(" AND ", $where);

// 1. Fetch Task List
$query = "
    SELECT 
        t.id, t.title, t.description, 
        t.assigned_to_user_id, t.assigned_to_name, t.assigned_to_role,
        t.assigned_by_user_id, t.assigned_by_name, t.assigned_by_role,
        t.status, t.is_important, t.due_date, t.completed_at, t.created_at
    FROM aitf_tasks t
    WHERE {$whereClause}
    ORDER BY t.is_important DESC, t.due_date ASC, t.id DESC
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$formattedTasks = [];
foreach ($tasks as $task) {
    $dueDate = $task['due_date'];
    $isOverdue = false;
    $dueDateFormatted = null;

    if (!empty($dueDate)) {
        $dueTs = strtotime($dueDate);
        $todayTs = strtotime($todayDate);
        $isOverdue = ($task['status'] !== 'completed' && $dueTs < $todayTs);
        $dueDateFormatted = date('D, d M', $dueTs);
    }

    $formattedTasks[] = [
        'id' => (int)$task['id'],
        'title' => $task['title'],
        'description' => $task['description'] ?? '',
        'assigned_to_user_id' => (int)$task['assigned_to_user_id'],
        'assigned_to_name' => $task['assigned_to_name'],
        'assigned_to_role' => $task['assigned_to_role'] ?? '',
        'assigned_by_user_id' => (int)$task['assigned_by_user_id'],
        'assigned_by_name' => $task['assigned_by_name'],
        'assigned_by_role' => $task['assigned_by_role'] ?? '',
        'status' => $task['status'],
        'is_important' => (int)$task['is_important'],
        'due_date' => $dueDate,
        'due_date_formatted' => $dueDateFormatted,
        'is_overdue' => $isOverdue,
        'created_at' => $task['created_at'],
        'completed_at' => $task['completed_at']
    ];
}

// 2. Fetch Task Counts (aligned with current scope)
$countWhere = ["is_deleted = 0", "(system_code = :system_code OR (system_code IN ('QMS_CARE', '01') AND :system_code IN ('QMS_CARE', '01')) OR system_code IS NULL OR system_code = '')"];
$countParams = [':system_code' => $user['system_code']];

if ($isTargetingOther) {
    if ($scope === 'my_assigned' || $scope === 'assigned_by_me') {
        $countWhere[] = "assigned_by_user_id = :active_uid";
        $countParams[':active_uid'] = $activeTargetUid;
    } elseif ($scope === 'all') {
        $countWhere[] = "(assigned_to_user_id = :active_uid OR assigned_by_user_id = :active_uid)";
        $countParams[':active_uid'] = $activeTargetUid;
    } else {
        $countWhere[] = "assigned_to_user_id = :active_uid";
        $countParams[':active_uid'] = $activeTargetUid;
    }
} else {
    if (!$canManageTasks || $scope === 'assigned_to_me' || $scope === 'only_me') {
        $countWhere[] = "assigned_to_user_id = :uid";
        $countParams[':uid'] = $userId;
    } elseif ($scope === 'my_assigned' || $scope === 'assigned_by_me') {
        $countWhere[] = "assigned_by_user_id = :uid";
        $countParams[':uid'] = $userId;
    } elseif ($scope === 'all' && $canManageTasks) {
        // No user restriction for managers viewing all
    } else {
        $countWhere[] = "assigned_to_user_id = :uid";
        $countParams[':uid'] = $userId;
    }
}

$countWhereClause = implode(" AND ", $countWhere);

$countStmt = $db->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN is_important = 1 THEN 1 ELSE 0 END) as starred
    FROM aitf_tasks
    WHERE {$countWhereClause}
");
$countStmt->execute($countParams);
$counts = $countStmt->fetch(PDO::FETCH_ASSOC);

jsonResponse(true, "Tasks fetched", [
    'tasks' => $formattedTasks,
    'counts' => [
        'total' => (int)($counts['total'] ?? 0),
        'pending' => (int)($counts['pending'] ?? 0),
        'completed' => (int)($counts['completed'] ?? 0),
        'starred' => (int)($counts['starred'] ?? 0)
    ],
    'permissions' => [
        'can_add' => true,
        'can_assign_others' => $canManageTasks,
        'can_delete' => $canDeleteTasks
    ]
], 200);
