<?php
session_start();
header('Content-Type: application/json');
function stageConfigReply($code, $data) { http_response_code($code); echo json_encode($data); exit; }
if (empty($_SESSION['username'])) stageConfigReply(401, ['error' => 'Please sign in again.']);
if (($_SESSION['role'] ?? '') !== 'Main Admin') stageConfigReply(403, ['error' => 'Only Main Admin can manage service stages.']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') stageConfigReply(405, ['error' => 'Use POST.']);
if (empty($_SESSION['service_stage_config_csrf']) || !is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['service_stage_config_csrf'], $_POST['csrf'])) stageConfigReply(403, ['error' => 'Refresh the page and try again.']);
require __DIR__ . '/dbConnect.php';
require __DIR__ . '/service_stage_config.php';
try {
    ensureServiceStageConfig($db);
    $typeId = $_POST['service_type_id'] ?? '';
    if (!ctype_digit((string)$typeId) || (int)$typeId < 1) throw new InvalidArgumentException('Choose a service type.');
    $stmt = $db->prepare('SELECT service FROM service_type WHERE id = ?');
    $stmt->execute([$typeId]);
    $name = $stmt->fetchColumn();
    if ($name === false) throw new InvalidArgumentException('Service type not found.');
    if (($_POST['action'] ?? '') === 'save') {
        $stages = json_decode($_POST['stages'] ?? '', true);
        if (!is_array($stages)) throw new InvalidArgumentException('Invalid stage selection.');
        saveServiceTypeStages($db, $typeId, $stages);
    } elseif (($_POST['action'] ?? '') !== 'load') throw new InvalidArgumentException('Unknown action.');
    $catalog = $db->query('SELECT id, stage FROM service_stage ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    stageConfigReply(200, ['status' => 'success', 'service_type_id' => (int)$typeId, 'service_name' => $name, 'selected' => allowedServiceTypeStages($db, $name), 'stages' => array_column($catalog, 'stage'), 'catalog' => $catalog]);
} catch (InvalidArgumentException $e) {
    stageConfigReply(422, ['error' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('Service stage configuration: ' . $e->getMessage());
    stageConfigReply(500, ['error' => 'Could not save service stages. Please retry.']);
}
