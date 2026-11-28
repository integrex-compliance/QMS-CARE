<?php
// Shared HRMS policy for the portal and attendance APIs.
function hrms_is_main_admin($role) {
    return in_array(strtolower(str_replace([' ', '_', '-'], '', (string)$role)),
        ['mainadmin', 'topmgmt', 'topmanagement', 'director'], true);
}

function hrms_ensure_schema(PDO $db) {
    static $ready = [];
    $key = spl_object_hash($db);
    if (isset($ready[$key])) return;
    $engine = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4' : '';
    $db->exec("CREATE TABLE IF NOT EXISTS hrms_settings (
        setting_key VARCHAR(64) NOT NULL PRIMARY KEY,
        setting_value VARCHAR(255) NOT NULL,
        updated_by INT NOT NULL,
        updated_at DATETIME NOT NULL
    )" . $engine);
    $db->exec("CREATE TABLE IF NOT EXISTS hrms_dashboard_access (
        viewer_user_id INT NOT NULL,
        target_user_id INT NOT NULL,
        granted_by INT NOT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (viewer_user_id, target_user_id)
    )" . $engine);
    $ready[$key] = true;
}

function hrms_active_users(PDO $db) {
    return $db->query("SELECT user_id, user_name, role, abbreviation, email_id FROM `user`
        WHERE is_deleted IS NULL OR is_deleted IN ('0', '', 'N') ORDER BY user_name, user_id")->fetchAll(PDO::FETCH_ASSOC);
}

function hrms_dashboard_targets(PDO $db, $viewerId) {
    hrms_ensure_schema($db);
    $stmt = $db->prepare('SELECT target_user_id FROM hrms_dashboard_access WHERE viewer_user_id = ?');
    $stmt->execute([(int)$viewerId]);
    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function hrms_can_view_dashboard(PDO $db, $viewerId, $role, $targetId) {
    if ((int)$viewerId <= 0 || (int)$targetId <= 0) return false;
    if ((int)$viewerId === (int)$targetId || hrms_is_main_admin($role)) return true;
    // Fail closed if permission storage is unavailable.
    try {
        return in_array((int)$targetId, hrms_dashboard_targets($db, $viewerId), true);
    } catch (Exception $e) {
        error_log('HRMS dashboard permission lookup failed: ' . $e->getMessage());
        return false;
    }
}

function hrms_weekoff_day(PDO $db) {
    hrms_ensure_schema($db);
    $value = $db->query("SELECT setting_value FROM hrms_settings WHERE setting_key = 'weekoff_day'")->fetchColumn();
    return $value !== false && preg_match('/^[0-6]$/', (string)$value) ? (int)$value : 0;
}

function hrms_save_weekoff(PDO $db, $day, $actorId) {
    if (!is_scalar($day) || !preg_match('/^[0-6]$/', (string)$day)) {
        throw new InvalidArgumentException('Select a valid weekoff day.');
    }
    hrms_ensure_schema($db);
    $stmt = $db->prepare("REPLACE INTO hrms_settings (setting_key, setting_value, updated_by, updated_at) VALUES ('weekoff_day', ?, ?, CURRENT_TIMESTAMP)");
    $stmt->execute([(string)$day, (int)$actorId]);
}

function hrms_save_dashboard_access(PDO $db, $viewerId, array $targetIds, $actorId) {
    hrms_ensure_schema($db);
    $users = [];
    foreach (hrms_active_users($db) as $user) $users[(int)$user['user_id']] = $user;
    if (!isset($users[$viewerId])) throw new InvalidArgumentException('Select an active team member.');
    if (hrms_is_main_admin($users[$viewerId]['role'])) {
        throw new InvalidArgumentException('Main Admin always has access to every dashboard.');
    }
    $targets = [];
    foreach ($targetIds as $id) {
        if (!is_scalar($id) || !ctype_digit((string)$id) || !isset($users[(int)$id])) {
            throw new InvalidArgumentException('One or more selected users are no longer available. Reload and try again.');
        }
        if ((int)$id !== (int)$viewerId) $targets[(int)$id] = (int)$id;
    }
    $db->beginTransaction();
    try {
        // Serialize saves for this viewer, including an initially empty grant list.
        $lock = $db->prepare('UPDATE `user` SET user_id = user_id WHERE user_id = ?');
        $lock->execute([(int)$viewerId]);
        $stmt = $db->prepare('DELETE FROM hrms_dashboard_access WHERE viewer_user_id = ?');
        $stmt->execute([(int)$viewerId]);
        $insert = $db->prepare('INSERT INTO hrms_dashboard_access (viewer_user_id, target_user_id, granted_by, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)');
        foreach ($targets as $targetId) $insert->execute([(int)$viewerId, $targetId, (int)$actorId]);
        $db->commit();
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
}

function hrms_weekoff_dates($year, $month, $day) {
    $dates = [];
    $start = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
    for ($date = $start; $date->format('Y-m') === $start->format('Y-m'); $date = $date->modify('+1 day')) {
        if ((int)$date->format('w') === (int)$day) $dates[] = $date->format('Y-m-d');
    }
    return $dates;
}
