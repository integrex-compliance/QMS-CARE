<?php
// Uses connection-local temporary tables; no customer or service data is changed.
require __DIR__ . '/../dbConnect.php';
require __DIR__ . '/../customer_service_flow.php';
$serviceSchema = $db->query('SHOW CREATE TABLE services')->fetch(PDO::FETCH_NUM)[1];
$db->exec(str_replace('CREATE TABLE', 'CREATE TEMPORARY TABLE', $serviceSchema));
$db->exec('CREATE TEMPORARY TABLE customer_details (customer_id VARCHAR(100) PRIMARY KEY, type_of_service TEXT) ENGINE=InnoDB');
$db->exec('CREATE TEMPORARY TABLE service_type (id INT AUTO_INCREMENT PRIMARY KEY, service VARCHAR(191)) ENGINE=InnoDB');
$db->exec('CREATE TEMPORARY TABLE service_stage (id INT AUTO_INCREMENT PRIMARY KEY, stage VARCHAR(50))');
$db->exec("INSERT INTO service_stage (stage) VALUES ('CA'), ('SA-1'), ('SA-2'), ('RC'), ('Custom review')");
// Start with the old schema to exercise the upgrade with existing data.
$db->exec('CREATE TEMPORARY TABLE customer_service_flow (id INT AUTO_INCREMENT PRIMARY KEY, customer_id VARCHAR(100), service_name VARCHAR(191), service_id INT, certification_date DATE NULL, next_service_date DATE NULL, next_service_type VARCHAR(50) DEFAULT "", active TINYINT DEFAULT 1, UNIQUE KEY customer_type(customer_id, service_name), UNIQUE KEY linked_service(service_id)) ENGINE=InnoDB');
$db->exec('CREATE TEMPORARY TABLE consulting (service_id INT, amount DECIMAL(10,2)) ENGINE=InnoDB');
$db->exec('CREATE TEMPORARY TABLE invoice_details (service_id INT, amount DECIMAL(10,2)) ENGINE=InnoDB');
$db->exec("INSERT INTO customer_details VALUES ('1', ''), ('2', '')");
$db->exec("INSERT INTO service_type (service) VALUES ('ISO 9001'), ('ISI Mark'), ('ZED'), ('MPCB')");
$db->exec('CREATE TEMPORARY TABLE service_type_stages (service_type_id INT, stage_id INT, sort_order INT DEFAULT 0, PRIMARY KEY(service_type_id, stage_id)) ENGINE=InnoDB');
$db->exec('INSERT INTO service_type_stages SELECT t.id, s.id, s.id FROM service_type t CROSS JOIN service_stage s');
function expectFlow($condition, $message) { if (!$condition) throw new RuntimeException($message); }
function flowCount() { global $db; return (int)$db->query('SELECT COUNT(*) FROM services')->fetchColumn(); }
function flowRecord($id) {
    global $db;
    $stmt = $db->prepare('SELECT f.*, s.service_type AS current_stage FROM customer_service_flow f JOIN services s ON s.service_id = f.service_id WHERE f.id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function invalidFlow($callback) {
    try { $callback(); } catch (InvalidArgumentException $e) { return; }
    throw new RuntimeException('Invalid operation accepted');
}
syncCustomerServiceFlow($db, 1, ['ISO 9001', 'ISI Mark', 'ZED', 'MPCB']);
expectFlow(flowCount() === 4, 'Four selections create four service sections');
$flowId = $db->query("SELECT id FROM customer_service_flow WHERE customer_id = '1' AND service_name = 'ISO 9001'")->fetchColumn();
$caId = flowRecord($flowId)['service_id'];
$db->exec("UPDATE customer_service_flow SET certification_date = '2026-11-26', next_service_date = '2027-11-26', next_service_type = 'SA-1' WHERE id = " . (int)$flowId);
$db->exec('INSERT INTO consulting VALUES (' . (int)$caId . ', 123.45)');
$db->exec('INSERT INTO invoice_details VALUES (' . (int)$caId . ', 500.00)');
$originalCa = flowRecord($flowId);
migrateCustomerServiceFlowStages($db);
migrateCustomerServiceFlowStages($db);
expectFlow(flowRecord($flowId) === $originalCa, 'Migration must preserve all existing stage data and IDs');
syncCustomerServiceFlow($db, 1, ['ISO 9001', 'ISI Mark', 'ZED', 'MPCB', 'ZED']);
expectFlow(flowCount() === 4, 'Repeated selections must not duplicate services');
// Add out of order, after other types already exist: CA must never be renamed.
$sa2 = saveCustomerServiceStage($db, 1, $flowId, 'SA-2');
$sa1 = saveCustomerServiceStage($db, 1, $flowId, 'SA-1');
$rc = saveCustomerServiceStage($db, 1, $sa1['flow_id'], 'RC');
expectFlow(flowCount() === 7, 'Each additional stage needs a separate service record');
expectFlow(flowRecord($flowId) === $originalCa, 'Adding stages must preserve original CA and its dates');
expectFlow(flowRecord($sa1['flow_id'])['current_stage'] === 'SA-1', 'SA-1 must persist independently');
expectFlow(flowRecord($sa2['flow_id'])['current_stage'] === 'SA-2', 'SA-2 must persist independently');
expectFlow(flowRecord($sa1['flow_id'])['certification_date'] === null, 'New stages must have independent dates');
expectFlow($db->query('SELECT service_id FROM consulting')->fetchColumn() == $caId, 'Payments must stay linked to CA');
expectFlow($db->query('SELECT service_id FROM invoice_details')->fetchColumn() == $caId, 'Invoices must stay linked to CA');
$repeat = saveCustomerServiceStage($db, 1, $sa2['flow_id'], 'SA-1');
expectFlow($repeat['service_id'] == $sa1['service_id'] && flowCount() === 7, 'Repeated stage addition must reuse its record');
// Exercise the production stage expression used by detail/payment saves.
$paymentHasFlow = true;
foreach (file(__DIR__ . '/../addService.php') as $line) {
    if (strpos($line, '$stageValue = ') !== false) { eval(trim($line)); break; }
}
expectFlow(isset($stageValue), 'Legacy save must guard linked stages');
$stmt = $db->prepare('UPDATE services SET service_type = ' . $stageValue . ' WHERE service_id = :id');
$stmt->execute([':servicetype' => 'RC', ':id' => $caId]);
expectFlow(flowRecord($flowId)['current_stage'] === 'CA', 'Detail saves cannot overwrite an existing stage');
invalidFlow(function () use ($db, $flowId) { saveCustomerServiceStage($db, 2, $flowId, 'RC'); });
invalidFlow(function () use ($db, $flowId) { saveCustomerServiceStage($db, 1, $flowId, 'Invalid'); });
invalidFlow(function () use ($db, $flowId) { deleteCustomerServiceFlow($db, 2, $flowId); });
$flows = $db->query('SELECT f.*, s.service_type AS current_stage FROM customer_service_flow f JOIN services s ON s.service_id = f.service_id ORDER BY f.id')->fetchAll(PDO::FETCH_ASSOC);
$ordered = sortCustomerServiceFlows(array_reverse($flows));
expectFlow(array_column($ordered, 'service_name') === ['ISO 9001', 'ISO 9001', 'ISO 9001', 'ISO 9001', 'ISI Mark', 'ZED', 'MPCB'], 'Same service stages must remain together');
expectFlow(array_slice(array_column($ordered, 'current_stage'), 0, 4) === ['CA', 'SA-1', 'SA-2', 'RC'], 'Certification stages must sort correctly even when added out of order');
$services = $db->query('SELECT * FROM services ORDER BY service_id')->fetchAll(PDO::FETCH_ASSOC);
array_unshift($services, ['service_id' => 999999, 'service_type' => 'CA']);
expectFlow(array_column(groupCustomerStageServices($services, $flows), 'service_id') == array_merge([999999], array_column($ordered, 'service_id')), 'Account cards must use the same grouping and retain unassigned history');
$deleted = deleteCustomerServiceFlow($db, 1, $sa1['flow_id']);
expectFlow(in_array('ISO 9001', $deleted['selected'], true), 'Deleting one stage must keep the service selected');
expectFlow(flowRecord($sa1['flow_id'])['active'] == 0 && flowRecord($flowId)['active'] == 1, 'Delete only archives the requested stage');
syncCustomerServiceFlow($db, 1, ['ZED'], true);
expectFlow(flowRecord($sa1['flow_id'])['active'] == 0, 'Adding another type must not restore an individually deleted stage');
expectFlow(deleteCustomerServiceFlow($db, 1, $sa1['flow_id'])['selected'] === $deleted['selected'], 'Repeated deletion must be safe');
$restored = saveCustomerServiceStage($db, 1, $flowId, 'SA-1');
expectFlow($restored['service_id'] == $sa1['service_id'] && flowCount() === 7, 'Re-adding an archived stage restores the original record');
syncCustomerServiceFlow($db, 1, []);
expectFlow($db->query('SELECT SUM(active) FROM customer_service_flow')->fetchColumn() == 0, 'Deselecting types archives all their stages');
invalidFlow(function () use ($db, $flowId) { saveCustomerServiceStage($db, 1, $flowId, 'RC'); });
syncCustomerServiceFlow($db, 1, ['ISO 9001']);
expectFlow($db->query('SELECT SUM(active) FROM customer_service_flow')->fetchColumn() == 4, 'Reselecting a type restores all stages');
expectFlow(flowRecord($flowId) === $originalCa && flowCount() === 7, 'Restore retains IDs, dates and stages');
syncCustomerServiceFlow($db, 2, ['ISO 9001']);
expectFlow(flowCount() === 8, 'Different customers get separate records');
invalidFlow(function () use ($db) { syncCustomerServiceFlow($db, 1, ['Invalid']); });
expectFlow($db->query("SELECT type_of_service FROM customer_details WHERE customer_id = '1'")->fetchColumn() === 'ISO 9001', 'Failure rolls back selection');
foreach ([$sa1['flow_id'], $sa2['flow_id'], $rc['flow_id'], $flowId] as $id) $deleted = deleteCustomerServiceFlow($db, 1, $id);
expectFlow($deleted['selected'] === [], 'Only deleting the last stage removes the selected type');
syncCustomerServiceFlow($db, 1, ['ISO 9001', 'ISI Mark']);
$isiFlow = $db->query("SELECT id FROM customer_service_flow WHERE customer_id = '1' AND service_name = 'ISI Mark'")->fetchColumn();
$custom = saveCustomerServiceStage($db, 1, $isiFlow, 'Custom review');
expectFlow(flowRecord($custom['flow_id'])['current_stage'] === 'Custom review', 'Every service supports master-defined custom stages');
expectFlow(flowRecord($isiFlow)['current_stage'] === 'CA', 'Custom stage must also retain the old stage');
expectFlow(serviceFlowDate('29.02.2024') === '2024-02-29', 'Leap date');
expectFlow(serviceFlowDate('') === null, 'Clear date');
foreach (['29.02.2025', '31.04.2026', '2026-11-26', 'abc'] as $bad) {
    invalidFlow(function () use ($bad) { serviceFlowDate($bad); });
}
// Legacy records are assigned to ISO 9001 without recreating or merging stages.
$db->exec("INSERT INTO customer_details VALUES ('3', 'ISI Mark'), ('4', '')");
syncCustomerServiceFlow($db, 3, ['ISI Mark']);
$assignedBefore = $db->query("SELECT * FROM customer_service_flow WHERE customer_id = '3'")->fetchAll(PDO::FETCH_ASSOC);
$legacyIds = [];
foreach (['CA', 'SA-1', 'SA-2', 'CA', 'RC'] as $stage) $legacyIds[] = createCustomerStageService($db, 3, $stage);
$otherCustomerId = createCustomerStageService($db, 4, 'CA');
$deletedId = createCustomerStageService($db, 3, 'CA');
$db->exec("UPDATE services SET is_delete = 'Y' WHERE service_id = " . (int)$deletedId);
$db->exec('INSERT INTO consulting VALUES (' . (int)$legacyIds[0] . ', 99.00)');
$db->exec('INSERT INTO invoice_details VALUES (' . (int)$legacyIds[0] . ', 199.00)');
$servicesBefore = $db->query('SELECT * FROM services ORDER BY service_id')->fetchAll(PDO::FETCH_ASSOC);
$paymentsBefore = $db->query('SELECT * FROM consulting')->fetchAll(PDO::FETCH_ASSOC);
$invoicesBefore = $db->query('SELECT * FROM invoice_details')->fetchAll(PDO::FETCH_ASSOC);
expectFlow(assignLegacyCustomerServices($db, 3) === 5, 'Every unassigned active legacy record gets linked');
expectFlow(assignLegacyCustomerServices($db, 3) === 0, 'Repeated account loads must not duplicate assignments');
expectFlow($db->query("SELECT service_id FROM customer_service_flow WHERE customer_id = '3' AND service_name = 'ISO 9001' ORDER BY service_id")->fetchAll(PDO::FETCH_COLUMN) == $legacyIds, 'Legacy IDs, including repeated CA stages, must all survive');
expectFlow($db->query("SELECT * FROM customer_service_flow WHERE customer_id = '3' AND service_name = 'ISI Mark'")->fetchAll(PDO::FETCH_ASSOC) === $assignedBefore, 'Existing assigned types must not be relabeled');
expectFlow($db->query("SELECT type_of_service FROM customer_details WHERE customer_id = '3'")->fetchColumn() === 'ISI Mark, ISO 9001', 'Customer selection must include the default type');
expectFlow($db->query("SELECT COUNT(*) FROM customer_service_flow WHERE customer_id = '4'")->fetchColumn() == 0, 'Account migration must stay scoped to its customer');
expectFlow($db->query('SELECT * FROM services ORDER BY service_id')->fetchAll(PDO::FETCH_ASSOC) === $servicesBefore, 'Assignment must not modify stages, dates or service details');
expectFlow($db->query('SELECT * FROM consulting')->fetchAll(PDO::FETCH_ASSOC) === $paymentsBefore, 'Assignment must preserve payments');
expectFlow($db->query('SELECT * FROM invoice_details')->fetchAll(PDO::FETCH_ASSOC) === $invoicesBefore, 'Assignment must preserve invoices');
$legacyFlowId = $db->query('SELECT id FROM customer_service_flow WHERE service_id = ' . (int)$legacyIds[0])->fetchColumn();
deleteCustomerServiceFlow($db, 3, $legacyFlowId);
assignLegacyCustomerServices($db, 3);
expectFlow(flowRecord($legacyFlowId)['active'] == 0, 'Account refresh must not restore archived assignments');
expectFlow(assignLegacyCustomerServices($db, 4) === 1, 'The same default applies to other customers');
expectFlow(assignLegacyCustomerServices($db, 999999) === 0, 'Missing customers must not create assignments');
// Each service's master selection controls additions, including stale requests.
$historyBefore = $db->query('SELECT * FROM services ORDER BY service_id')->fetchAll(PDO::FETCH_ASSOC);
expectFlow(saveServiceTypeStages($db, 1, ['CA', 'SA-1', 'ca']) === ['CA', 'SA-1'], 'Mapping must deduplicate stage names');
expectFlow(allowedServiceTypeStages($db, 'ISO 9001') === ['CA', 'SA-1'], 'Only mapped stages are selectable for ISO');
invalidFlow(function () use ($db, $flowId) { saveCustomerServiceStage($db, 1, $flowId, 'SA-2'); });
expectFlow($db->query('SELECT * FROM services ORDER BY service_id')->fetchAll(PDO::FETCH_ASSOC) === $historyBefore, 'Removing a permitted stage must retain existing service history');
expectFlow(saveServiceTypeStages($db, 3, ['Bronze', 'Silver', 'Gold']) === ['Bronze', 'Silver', 'Gold'], 'Master can create and map custom stages');
expectFlow(serviceTypeStageMap($db)['ZED'] === ['Bronze', 'Silver', 'Gold'], 'Customer map must use per-type choices');
$db->exec("INSERT INTO customer_details VALUES ('5', '')");
syncCustomerServiceFlow($db, 5, ['ZED']);
$zedFlow = $db->query("SELECT id FROM customer_service_flow WHERE customer_id = '5'")->fetchColumn();
expectFlow(flowRecord($zedFlow)['current_stage'] === 'Bronze', 'New service must use a permitted initial stage instead of hardcoded CA');
invalidFlow(function () use ($db, $zedFlow) { saveCustomerServiceStage($db, 5, $zedFlow, 'CA'); });
$gold = saveCustomerServiceStage($db, 5, $zedFlow, 'Gold');
expectFlow(flowRecord($gold['flow_id'])['current_stage'] === 'Gold', 'Mapped custom stage can be added');
saveServiceTypeStages($db, 3, ['Silver']);
expectFlow(flowRecord($gold['flow_id'])['current_stage'] === 'Gold', 'Unmapped existing stages stay in history');
invalidFlow(function () use ($db, $zedFlow) { saveCustomerServiceStage($db, 5, $zedFlow, 'Gold'); });
saveServiceTypeStages($db, 3, []);
expectFlow(allowedServiceTypeStages($db, 'ZED') === [], 'Empty mapping must not fall back to global stages');
invalidFlow(function () use ($db, $zedFlow) { saveCustomerServiceStage($db, 5, $zedFlow, 'Silver'); });
invalidFlow(function () use ($db) { saveServiceTypeStages($db, 1, ['']); });
invalidFlow(function () use ($db) { saveServiceTypeStages($db, 1, [str_repeat('x', 51)]); });
invalidFlow(function () use ($db) { saveServiceTypeStages($db, 999999, ['Gold']); });
expectFlow(allowedServiceTypeStages($db, 'ISO 9001') === ['CA', 'SA-1'], 'Invalid configuration must not erase the existing mapping');
$db->exec("INSERT INTO service_type (service) VALUES ('Unconfigured')");
$before = flowCount();
invalidFlow(function () use ($db) { syncCustomerServiceFlow($db, 5, ['Unconfigured'], true); });
expectFlow(flowCount() === $before, 'Unconfigured types must not create an unauthorized default stage');
echo "PASS: separate stages, legacy assignment, per-type master rules, custom stages, empty mappings, duplicate prevention, preserved history and rollback\n";
