<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['id']) || empty($_SESSION['username'])) {
    header('Location: index.php?errorCode=101');
    exit;
}
require_once __DIR__ . '/dbConnect.php';
require_once __DIR__ . '/include/hrms.php';
$pageIdentifier = 'HRMS';
$canManageHrms = hrms_is_main_admin($_SESSION['role'] ?? '');
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$hrmsError = '';
$hrmsUsers = [];
$grants = [];
$weekoffDay = 0;
try {
    $weekoffDay = hrms_weekoff_day($db);
    if ($canManageHrms) {
        $hrmsUsers = hrms_active_users($db);
        foreach ($db->query('SELECT viewer_user_id, target_user_id FROM hrms_dashboard_access')->fetchAll(PDO::FETCH_ASSOC) as $grant) {
            $grants[(int)$grant['viewer_user_id']][] = (int)$grant['target_user_id'];
        }
    } else {
        $allowedIds = hrms_dashboard_targets($db, $_SESSION['id']);
        foreach (hrms_active_users($db) as $member) {
            if ((int)$member['user_id'] === (int)$_SESSION['id'] || in_array((int)$member['user_id'], $allowedIds, true)) $hrmsUsers[] = $member;
        }
    }
} catch (Exception $e) {
    error_log('HRMS page failed: ' . $e->getMessage());
    $hrmsError = 'HRMS settings are temporarily unavailable. Please reload to try again.';
}
if (empty($_SESSION['hrms_csrf'])) $_SESSION['hrms_csrf'] = bin2hex(random_bytes(32));
function hrms_escape($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
?>
<div id="wrapper" class="active">
    <?php include_once __DIR__ . '/header.php'; ?>
    <link rel="stylesheet" href="css/hrms.css?v=1">
    <main id="page-content-wrapper" class="hrms-page">
        <div class="hrms-content">
            <div class="hrms-heading">
                <div><span class="hrms-eyebrow">PEOPLE &amp; PERMISSIONS</span><h1>HRMS</h1><p>Manage your weekly schedule and dashboard visibility in one place.</p></div>
                <span class="hrms-role"><i class="fa fa-shield" aria-hidden="true"></i> <?php echo $canManageHrms ? 'Main Admin controls' : 'Your workspace settings'; ?></span>
            </div>
            <?php if ($hrmsError): ?>
                <div class="alert alert-danger" role="alert"><?php echo hrms_escape($hrmsError); ?></div>
            <?php else: ?>
            <section class="hrms-card" aria-labelledby="weekoff-title">
                <div class="hrms-section-heading"><span class="hrms-icon"><i class="fa fa-calendar" aria-hidden="true"></i></span><div><h2 id="weekoff-title">Select weekoff day</h2><p>Choose the recurring weekly off for everyone in your organisation.</p></div><span class="hrms-tag">Organisation-wide</span></div>
                <form id="hrmsWeekoffForm">
                    <fieldset class="hrms-days" <?php echo !$canManageHrms ? 'disabled' : ''; ?>>
                        <legend class="sr-only">Weekly off day</legend>
                        <?php foreach ($days as $index => $day): ?>
                            <label class="hrms-day"><input type="radio" name="weekoff_day" value="<?php echo $index; ?>" <?php echo $weekoffDay === $index ? 'checked' : ''; ?>><span><i class="fa fa-check-circle" aria-hidden="true"></i><?php echo $day; ?></span></label>
                        <?php endforeach; ?>
                    </fieldset>
                    <div class="hrms-form-footer"><p><i class="fa fa-info-circle" aria-hidden="true"></i> Calendars show <strong>Weekoff</strong> on the selected day, including past months. Existing attendance records are preserved.</p><?php if ($canManageHrms): ?><button class="hrms-button" type="submit">Save weekoff</button><?php endif; ?></div>
                    <div id="weekoffFeedback" class="hrms-feedback" role="status" aria-live="polite" hidden></div>
                </form>
            </section>
            <section class="hrms-card" aria-labelledby="dashboard-control-title">
                <div class="hrms-section-heading"><span class="hrms-icon"><i class="fa fa-lock" aria-hidden="true"></i></span><div><h2 id="dashboard-control-title">Dashboard view control</h2><p>Decide who can view each team member's dashboard.</p></div></div>
                <div class="hrms-policy"><i class="fa fa-shield" aria-hidden="true"></i><div><strong>Main Admin can view all dashboards</strong><p>Everyone can view their own dashboard. Access to other dashboards must be allowed by Main Admin. View access does not add editing permissions.</p></div><span class="hrms-tag">Always enabled</span></div>
                <?php if ($canManageHrms): ?>
                <form id="hrmsAccessForm">
                    <div class="hrms-access-layout">
                        <div class="hrms-viewer-panel"><h3>Allow dashboard access</h3><p>Choose who should receive access, then select the dashboards they can view.</p><label for="hrmsViewer">Team member receiving access</label><select id="hrmsViewer" class="form-control"><option value="">Select a team member</option><?php foreach ($hrmsUsers as $member): ?><option value="<?php echo (int)$member['user_id']; ?>" data-main-admin="<?php echo hrms_is_main_admin($member['role']) ? '1' : '0'; ?>"><?php echo hrms_escape($member['user_name'] . ' — ' . $member['role']); ?></option><?php endforeach; ?></select><div class="hrms-viewer-note" id="hrmsViewerNote">Select a team member to review their current access.</div></div>
                        <div class="hrms-target-panel">
                            <div class="hrms-list-heading"><h3>Dashboards they can view</h3><span id="hrmsSelectedCount" class="hrms-tag">0 selected</span></div>
                            <label class="sr-only" for="hrmsUserSearch">Search dashboards by name or role</label><div class="hrms-search"><i class="fa fa-search" aria-hidden="true"></i><input id="hrmsUserSearch" type="search" placeholder="Search by name or role" autocomplete="off"></div>
                            <div class="hrms-list-actions"><button type="button" id="hrmsSelectAll" disabled>Select all</button><button type="button" id="hrmsClearAll" disabled>Clear selection</button><span>Own dashboard is always included</span></div>
                            <div class="hrms-user-list">
                                <?php foreach ($hrmsUsers as $member): ?>
                                <label class="hrms-user-row" data-search="<?php echo hrms_escape(strtolower($member['user_name'] . ' ' . $member['role'])); ?>"><input type="checkbox" name="target_ids[]" value="<?php echo (int)$member['user_id']; ?>" disabled><span class="hrms-avatar" aria-hidden="true"><i class="fa fa-user-o"></i></span><span class="hrms-user-text"><strong><?php echo hrms_escape($member['user_name']); ?></strong><small><?php echo hrms_escape($member['role']); ?></small></span><span class="hrms-user-state"></span></label>
                                <?php endforeach; ?>
                                <div id="hrmsNoResults" class="hrms-empty" hidden>No team members match your search.</div>
                            </div>
                        </div>
                    </div>
                    <div class="hrms-form-footer"><button id="hrmsSaveAccess" class="hrms-button" type="submit" disabled>Save dashboard access</button></div>
                    <div id="accessFeedback" class="hrms-feedback" role="status" aria-live="polite" hidden></div>
                </form>
                <?php else: ?>
                    <div class="hrms-own-access"><h3>Your allowed dashboards</h3><p>Contact Main Admin to change your weekoff or dashboard access.</p><div class="hrms-dashboard-links"><?php foreach ($hrmsUsers as $member): ?><a href="myDashboard.php?view_user=user_<?php echo (int)$member['user_id']; ?>"><i class="fa fa-tachometer" aria-hidden="true"></i> <?php echo hrms_escape($member['user_name']); ?><?php echo (int)$member['user_id'] === (int)$_SESSION['id'] ? ' (You)' : ''; ?><i class="fa fa-angle-right" aria-hidden="true"></i></a><?php endforeach; ?></div></div>
                <?php endif; ?>
            </section>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php if ($canManageHrms && !$hrmsError): ?>
<script>
window.QMS_HRMS = <?php echo json_encode(['csrf' => $_SESSION['hrms_csrf'], 'grants' => (object)$grants], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<script src="js/hrms.js?v=1"></script>
<?php endif; ?>
