<?php
/**
 * Database Schema Auto-Synchronization Utility
 * Ensures all required tables and columns for Task Management, Dashboard,
 * Guidelines, and Attendance are present in both local and live databases.
 */

if (!function_exists('aitf_sync_database_schema')) {
    function aitf_sync_database_schema($db) {
        static $schemaSynced = false;
        if ($schemaSynced) {
            return;
        }
        if (!($db instanceof PDO)) {
            return;
        }

        try {
            // 1. Task Manager table columns
            $tmCols = [];
            $colStmt = $db->query("SHOW COLUMNS FROM `task_manager`");
            if ($colStmt) {
                while ($c = $colStmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmCols[strtolower($c['Field'])] = true;
                }
            }

            if (!empty($tmCols)) {
                $tmColumnsToAdd = [
                    'due_date' => 'DATE NULL AFTER `priority`',
                    'reminder_time' => 'VARCHAR(100) NULL AFTER `due_date`',
                    'is_important' => 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `reminder_time`',
                    'assigned_by' => 'INT(11) NULL AFTER `is_important`',
                    'assigned_by_name' => 'VARCHAR(255) NULL AFTER `assigned_by`',
                    'completed_at' => 'DATETIME NULL AFTER `complete_date`'
                ];
                foreach ($tmColumnsToAdd as $col => $def) {
                    if (!isset($tmCols[strtolower($col)])) {
                        $db->exec("ALTER TABLE `task_manager` ADD COLUMN `$col` $def");
                        $tmCols[strtolower($col)] = true;
                    }
                }
            }

            // 2. User table profile and identity columns
            $userCols = [];
            $uColStmt = $db->query("SHOW COLUMNS FROM `user`");
            if ($uColStmt) {
                while ($uc = $uColStmt->fetch(PDO::FETCH_ASSOC)) {
                    $userCols[strtolower($uc['Field'])] = true;
                }
            }

            if (!empty($userCols)) {
                $userColumnsToAdd = [
                    'e_id' => 'VARCHAR(50) NULL',
                    'pc_id' => 'VARCHAR(50) NULL',
                    'designation_assigned' => 'VARCHAR(255) NULL',
                    'applicable_records' => 'TEXT NULL',
                    'resource_issue_record' => 'TEXT NULL',
                    'work_description' => 'TEXT NULL',
                    'account_status' => "VARCHAR(50) DEFAULT 'active'",
                    'contact_no' => 'VARCHAR(50) NULL'
                ];
                foreach ($userColumnsToAdd as $col => $def) {
                    if (!isset($userCols[strtolower($col)])) {
                        $db->exec("ALTER TABLE `user` ADD COLUMN `$col` $def");
                        $userCols[strtolower($col)] = true;
                    }
                }
            }

            // 3. Staff Guidelines table
            $db->exec("
                CREATE TABLE IF NOT EXISTS `staff_guidelines` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `title` VARCHAR(255) NOT NULL,
                    `description` LONGTEXT NULL,
                    `attachments` LONGTEXT NULL,
                    `created_by` INT(11) NULL,
                    `created_at` DATETIME NULL,
                    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");

            // 4. Staff Guideline Recipients table
            $db->exec("
                CREATE TABLE IF NOT EXISTS `staff_guideline_recipients` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `guideline_id` INT(11) NOT NULL,
                    `user_id` INT(11) NOT NULL,
                    `assigned_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_guideline_id` (`guideline_id`),
                    KEY `idx_user_id` (`user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");

            // 5. AITF Attendance table
            $db->exec("
                CREATE TABLE IF NOT EXISTS `aitf_attendance` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `user_id` INT(11) NOT NULL,
                    `attendance_date` DATE NOT NULL,
                    `status` VARCHAR(50) NOT NULL DEFAULT 'present',
                    `in_time` TIME NULL,
                    `out_time` TIME NULL,
                    `remarks` TEXT NULL,
                    `punch_in_selfie_url` VARCHAR(255) NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_user_date` (`user_id`, `attendance_date`),
                    KEY `idx_user_id` (`user_id`),
                    KEY `idx_attendance_date` (`attendance_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");

            $schemaSynced = true;
        } catch (Exception $e) {
            error_log("Schema auto-sync error: " . $e->getMessage());
        }
    }
}
