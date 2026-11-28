<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../include/hrms.php';
function hrmsCheck($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE user (user_id INTEGER PRIMARY KEY, user_name TEXT, role TEXT, abbreviation TEXT, email_id TEXT, is_deleted TEXT)');
$db->exec("INSERT INTO user VALUES (1, 'Main Admin', 'Main Admin', 'MA', '', '0'), (2, 'Admin', 'Admin', 'AD', '', 'N'), (3, 'Staff', 'Office Staff', 'ST', '', NULL), (4, 'Finance', 'Finance', 'FI', '', ''), (5, 'Deleted', 'Office Staff', 'DE', '', 'Y')");
hrms_ensure_schema($db);
hrms_ensure_schema($db);
hrmsCheck(count(hrms_active_users($db)) === 4, 'Deleted members must not be selectable.');
hrmsCheck(hrms_weekoff_day($db) === 0, 'Default must preserve Sunday weekoff.');
foreach (range(0, 6) as $day) {
    hrms_save_weekoff($db, $day, 1);
    hrmsCheck(hrms_weekoff_day($db) === $day, 'Weekoff must persist for every weekday.');
    $dates = hrms_weekoff_dates(2026, 11, $day);
    foreach ($dates as $date) hrmsCheck((int)(new DateTimeImmutable($date))->format('w') === $day, 'Calendar date must match selected weekday.');
}
hrmsCheck(count(hrms_weekoff_dates(2026, 11, 1)) === 5, 'November 2026 must have five Monday weekoffs.');
hrmsCheck(count(hrms_weekoff_dates(2024, 2, 4)) === 5, 'Leap-day weekoff must be included.');
foreach (['7', '-1', 'monday', '', [], '1.0'] as $invalid) {
    try { hrms_save_weekoff($db, $invalid, 1); throw new RuntimeException('Invalid day accepted'); }
    catch (InvalidArgumentException $e) {}
}
hrmsCheck(hrms_weekoff_day($db) === 6, 'Invalid saves must preserve prior setting.');
hrmsCheck(hrms_can_view_dashboard($db, 1, 'Main Admin', 3), 'Main Admin must have universal access.');
hrmsCheck(hrms_can_view_dashboard($db, 3, 'Office Staff', 3), 'Own dashboard must always be allowed.');
hrmsCheck(!hrms_can_view_dashboard($db, 2, 'Admin', 3), 'Admin role alone must not grant dashboard access.');
hrmsCheck(!hrms_can_view_dashboard($db, 4, 'Finance', 3), 'Finance role alone must not grant dashboard access.');
hrmsCheck(!hrms_can_view_dashboard($db, 0, 'Main Admin', 3), 'Missing identity must be denied.');
hrms_save_dashboard_access($db, 3, [2, 4, 4, 3], 1);
hrmsCheck(hrms_dashboard_targets($db, 3) === [2, 4], 'Grants must deduplicate and omit implicit self access.');
hrmsCheck(hrms_can_view_dashboard($db, 3, 'Office Staff', 2), 'Explicit grant must allow a staff viewer.');
hrmsCheck(!hrms_can_view_dashboard($db, 2, 'Admin', 3), 'Access must be directional, not reciprocal.');
hrmsCheck(!hrms_can_view_dashboard($db, 3, 'Office Staff', 1), 'Unselected dashboard must stay denied.');
$db->exec("CREATE TRIGGER fail_hrms_grant BEFORE INSERT ON hrms_dashboard_access WHEN NEW.target_user_id = 1 BEGIN SELECT RAISE(ABORT, 'Test failure'); END");
try { hrms_save_dashboard_access($db, 3, [1], 1); throw new RuntimeException('Expected database failure'); }
catch (PDOException $e) {}
hrmsCheck(hrms_dashboard_targets($db, 3) === [2, 4], 'A database failure must roll back revoked grants.');
$db->exec('DROP TRIGGER fail_hrms_grant');
foreach ([[5], [999], ['2 OR 1=1'], [[]]] as $invalid) {
    try { hrms_save_dashboard_access($db, 3, $invalid, 1); throw new RuntimeException('Invalid target accepted'); }
    catch (InvalidArgumentException $e) {}
    hrmsCheck(hrms_dashboard_targets($db, 3) === [2, 4], 'Invalid saves must not partially replace existing grants.');
}
try { hrms_save_dashboard_access($db, 1, [], 1); throw new RuntimeException('Main Admin access was editable'); }
catch (InvalidArgumentException $e) {}
hrms_save_dashboard_access($db, 3, [1], 1);
hrmsCheck(hrms_can_view_dashboard($db, 3, 'Office Staff', 1), 'Main Admin may explicitly share their dashboard.');
hrmsCheck(!hrms_can_view_dashboard($db, 3, 'Office Staff', 2), 'Replacing grants must revoke unchecked dashboards immediately.');
hrms_save_dashboard_access($db, 3, [], 1);
hrmsCheck(!hrms_can_view_dashboard($db, 3, 'Office Staff', 1), 'Empty selection must revoke all additional access.');
hrmsCheck(hrms_can_view_dashboard($db, 3, 'Office Staff', 3), 'Revocation must retain own dashboard access.');
echo "PASS: HRMS persistence, all weekdays, leap months, explicit grants, directional access, role defaults, revocation, validation and Main Admin access.\n";
