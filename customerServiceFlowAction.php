<?php
session_start();
header('Content-Type: application/json');
function flowReply($code, $data) { http_response_code($code); echo json_encode($data); exit; }
if (empty($_SESSION['username'])) flowReply(401, ['error' => 'Please sign in again.']);
if (!in_array($_SESSION['role'] ?? '', ['Admin', 'Main Admin', 'Office Staff', 'Marketing', 'Office BDM'], true)) flowReply(403, ['error' => 'You do not have permission to edit services.']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') flowReply(405, ['error' => 'Use POST.']);
if (empty($_SESSION['service_flow_csrf']) || !hash_equals($_SESSION['service_flow_csrf'], $_POST['csrf'] ?? '')) flowReply(403, ['error' => 'Refresh the page and try again.']);
require './dbConnect.php';
require './customer_service_flow.php';
try {
    ensureCustomerServiceFlow($db);
    $cid = $_POST['customer_id'] ?? '';
    if (!ctype_digit((string)$cid) || (int)$cid < 1) throw new InvalidArgumentException('Invalid customer.');
    if (($_POST['action'] ?? '') === 'delete') {
        if (!in_array($_SESSION['role'], ['Admin', 'Main Admin', 'Office Staff'], true)) flowReply(403, ['error' => 'You do not have permission to delete services.']);
        flowReply(200, deleteCustomerServiceFlow($db, $cid, $_POST['id'] ?? ''));
    }
    if (($_POST['action'] ?? '') === 'add') {
        $name = $_POST['service_name'] ?? '';
        if (!is_string($name) || trim($name) === '') throw new InvalidArgumentException('Choose a service to add.');
        $selected = syncCustomerServiceFlow($db, $cid, [$name], true);
        flowReply(200, ['status' => 'success', 'selected' => $selected]);
    }
    if (($_POST['action'] ?? '') === 'sync') {
        $selected = json_decode($_POST['selected'] ?? '', true);
        if (!is_array($selected) || count($selected) > 200 || count(array_filter($selected, 'is_string')) !== count($selected)) throw new InvalidArgumentException('Invalid selection.');
        $selected = syncCustomerServiceFlow($db, $cid, $selected);
        flowReply(200, ['status' => 'success', 'selected' => $selected]);
    }
    if (($_POST['action'] ?? '') === 'stage') {
        flowReply(200, saveCustomerServiceStage($db, $cid, $_POST['id'] ?? '', $_POST['stage'] ?? ''));
    }
    if (($_POST['action'] ?? '') !== 'dates') throw new InvalidArgumentException('Unknown action.');
    $column = $_POST['column'] ?? '';
    if (!in_array($column, ['certification_date', 'next_service_date', 'next_service_type'], true)) throw new InvalidArgumentException('Invalid field.');
    $value = trim($_POST['value'] ?? '');
    if ($column !== 'next_service_type') $value = serviceFlowDate($value);
    elseif (strlen($value) > 50) throw new InvalidArgumentException('Stage must be 50 characters or fewer.');
    $stmt = $db->prepare('SELECT id FROM customer_service_flow WHERE id = ? AND customer_id = ? AND active = 1');
    $stmt->execute([$_POST['id'] ?? '', $cid]);
    if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Service not found. Refresh the page.');
    $stmt = $db->prepare("UPDATE customer_service_flow SET $column = ? WHERE id = ? AND customer_id = ? AND active = 1");
    $stmt->execute([$value, $_POST['id'], $cid]);
    flowReply(200, ['status' => 'success']);
} catch (InvalidArgumentException $e) {
    flowReply(422, ['error' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('Service flow: ' . $e->getMessage());
    flowReply(500, ['error' => 'Services could not be saved. Please retry.']);
}
