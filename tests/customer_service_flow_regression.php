<?php
// Uses connection-local temporary tables; no customer or service data is changed.
require __DIR__ . '/../dbConnect.php';
require __DIR__ . '/../customer_service_flow.php';
$serviceSchema = $db->query('SHOW CREATE TABLE services')->fetch(PDO::FETCH_NUM)[1];
$db->exec(str_replace('CREATE TABLE', 'CREATE TEMPORARY TABLE', $serviceSchema));
$db->exec('CREATE TEMPORARY TABLE customer_details (customer_id VARCHAR(100) PRIMARY KEY, type_of_service TEXT) ENGINE=InnoDB');
$db->exec('CREATE TEMPORARY TABLE service_type (service VARCHAR(191))');
$db->exec('CREATE TEMPORARY TABLE service_stage (id INT AUTO_INCREMENT PRIMARY KEY, stage VARCHAR(50))');
$db->exec("INSERT INTO service_stage (stage) VALUES ('CA'), ('SA-1'), ('SA-2'), ('RC')");
$db->exec('CREATE TEMPORARY TABLE customer_service_flow (id INT AUTO_INCREMENT PRIMARY KEY, customer_id VARCHAR(100), service_name VARCHAR(191), service_id INT, certification_date DATE NULL, next_service_date DATE NULL, next_service_type VARCHAR(50) DEFAULT "", active TINYINT DEFAULT 1, UNIQUE KEY(customer_id, service_name), UNIQUE KEY(service_id)) ENGINE=InnoDB');
$db->exec("INSERT INTO customer_details VALUES ('1', ''), ('2', '')");
$db->exec("INSERT INTO service_type VALUES ('ISO 9001'), ('ISI Mark'), ('ZED'), ('MPCB')");
function expectFlow($condition, $message) { if (!$condition) throw new RuntimeException($message); }
syncCustomerServiceFlow($db, 1, ['ISO 9001', 'ISI Mark', 'ZED', 'MPCB']);
expectFlow($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == 4, 'Four selections create four service sections');
syncCustomerServiceFlow($db, 1, ['ISO 9001', 'ISI Mark', 'ZED', 'MPCB', 'ZED']);
expectFlow($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == 4, 'Repeated saves must not duplicate');
$flowId = $db->query("SELECT id FROM customer_service_flow WHERE customer_id = '1' AND service_name = 'ISO 9001'")->fetchColumn();
$changed = saveCustomerServiceStage($db, 1, $flowId, 'SA-1');
expectFlow($db->query("SELECT COUNT(*) FROM services WHERE service_type = 'CA'")->fetchColumn() == 3, 'Changing ISO stage must leave the other three services unchanged');
expectFlow($db->query('SELECT service_type FROM services WHERE service_id = ' . (int)$changed['service_id'])->fetchColumn() === 'SA-1', 'Header stage must persist');
preg_match('/UPDATE services SET (service_type = CASE.*? END) ,/', file_get_contents(__DIR__ . '/../addService.php'), $stageGuard);
expectFlow(isset($stageGuard[1]), 'Legacy save must guard linked stages');
$stmt = $db->prepare('UPDATE services SET ' . $stageGuard[1] . ' WHERE service_id = :id');
$stmt->execute([':servicetype' => 'RC', ':id' => $changed['service_id']]);
expectFlow($db->query('SELECT service_type FROM services WHERE service_id = ' . (int)$changed['service_id'])->fetchColumn() === 'SA-1', 'MySQL detail save cannot overwrite header stage');
try { saveCustomerServiceStage($db, 2, $flowId, 'RC'); throw new RuntimeException('Cross-customer update accepted'); } catch (InvalidArgumentException $e) {}
try { saveCustomerServiceStage($db, 1, $flowId, 'Invalid'); throw new RuntimeException('Invalid stage accepted'); } catch (InvalidArgumentException $e) {}
$db->exec("UPDATE customer_service_flow SET certification_date = '2026-11-26' WHERE service_name = 'ISO 9001'");
expectFlow($db->query('SELECT COUNT(*) FROM customer_service_flow WHERE certification_date IS NULL')->fetchColumn() == 3, 'Dates must be independent');
syncCustomerServiceFlow($db, 1, []);
try { saveCustomerServiceStage($db, 1, $flowId, 'RC'); throw new RuntimeException('Inactive update accepted'); } catch (InvalidArgumentException $e) {}
expectFlow($db->query('SELECT SUM(active) FROM customer_service_flow')->fetchColumn() == 0, 'Clear deactivates all');
syncCustomerServiceFlow($db, 1, ['ISO 9001']);
expectFlow($db->query("SELECT certification_date FROM customer_service_flow WHERE active = 1")->fetchColumn() === '2026-11-26', 'Reselect preserves dates');
expectFlow($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == 4, 'Reselect preserves service IDs and history');
syncCustomerServiceFlow($db, 2, ['ISO 9001']);
expectFlow($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == 5, 'Different customers get separate records');
try { syncCustomerServiceFlow($db, 1, ['Invalid']); throw new RuntimeException('Invalid selection accepted'); } catch (InvalidArgumentException $e) {}
expectFlow($db->query("SELECT type_of_service FROM customer_details WHERE customer_id = '1'")->fetchColumn() === 'ISO 9001', 'Failure rolls back selection');
expectFlow(serviceFlowDate('29.02.2024') === '2024-02-29', 'Leap date');
expectFlow(serviceFlowDate('') === null, 'Clear date');
syncCustomerServiceFlow($db, 1, ['ISO 9001', 'ISI Mark']);
try { deleteCustomerServiceFlow($db, 2, $flowId); throw new RuntimeException('Cross-customer deletion accepted'); } catch (InvalidArgumentException $e) {}
$deleted = deleteCustomerServiceFlow($db, 1, $flowId);
expectFlow($deleted['selected'] === ['ISI Mark'], 'Delete must only remove the requested selection');
expectFlow($db->query('SELECT active FROM customer_service_flow WHERE id = ' . (int)$flowId)->fetchColumn() == 0, 'Deleted assignment must be inactive');
expectFlow($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == 5, 'Delete must retain records linked to invoices and payments');
$again = deleteCustomerServiceFlow($db, 1, $flowId);
expectFlow($again['selected'] === ['ISI Mark'], 'Repeated deletion must be safe');
syncCustomerServiceFlow($db, 1, ['ISO 9001', 'ISI Mark']);
expectFlow($db->query('SELECT service_id FROM customer_service_flow WHERE id = ' . (int)$flowId)->fetchColumn() == $changed['service_id'], 'Reselect after delete must restore the same service');
expectFlow($db->query('SELECT certification_date FROM customer_service_flow WHERE id = ' . (int)$flowId)->fetchColumn() === '2026-11-26', 'Delete/restore must retain dates');
$added = syncCustomerServiceFlow($db, 1, ['ZED'], true);
expectFlow($added === ['ISO 9001', 'ISI Mark', 'ZED'], 'Single add must preserve all existing selections');
$addedAgain = syncCustomerServiceFlow($db, 1, ['ZED'], true);
expectFlow($addedAgain === $added, 'Repeated add must not duplicate a selection');
expectFlow($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == 5, 'Adding a retained type reuses its existing section');
foreach (['29.02.2025', '31.04.2026', '2026-11-26', 'abc'] as $bad) {
    try { serviceFlowDate($bad); throw new RuntimeException('Invalid date accepted'); } catch (InvalidArgumentException $e) {}
}
echo "PASS: four types, independent dates/stages, stage validation, delete isolation, repeated delete, delete/restore history, rollback and strict dates\n";
