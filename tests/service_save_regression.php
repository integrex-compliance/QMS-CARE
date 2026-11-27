<?php
// Run: C:/xampp/php/php.exe tests/service_save_regression.php
// Exercise the production save handler with an isolated SQLite database.
$source = file_get_contents(__DIR__ . '/../addService.php');
$source = str_replace(array('<?php', 'session_start();', "include_once './dbConnect.php';"), '', $source);
$source = str_replace("__DIR__ . '/payment_schema.php'", var_export(__DIR__ . '/../payment_schema.php', true), $source);
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
preg_match_all('/INSERT INTO (services|certification_body|consulting) \(([^)]+)\)/', $source, $inserts, PREG_SET_ORDER);
$primaryKeys = array('services' => 'service_id', 'certification_body' => 'certification_id', 'consulting' => 'consulting_id');
foreach ($inserts as $insert) {
    $legacyColumns = array_diff(array_map('trim', explode(',', $insert[2])), ['tds_deducted', 'short_settle', 'bad_debt']);
    $db->exec('CREATE TABLE IF NOT EXISTS ' . $insert[1] . ' (' . $primaryKeys[$insert[1]] . ' INTEGER PRIMARY KEY AUTOINCREMENT, ' . implode(', ', $legacyColumns) . ')');
}
$db->exec('CREATE TABLE customer_details (customer_id, certification_date, next_service_date, next_service_type)');
$db->exec('CREATE TABLE invoice_details (invoice_id INTEGER PRIMARY KEY)');
$_SERVER['REQUEST_METHOD'] = 'GET';
$db->exec("INSERT INTO customer_details VALUES (1, '2026-01-01', NULL, NULL)");
preg_match_all('/\$_GET\[\x27([^\x27]+)\x27\]/', $source, $keys);
$request = array_fill_keys($keys[1], '');
$request['customer_id'] = 1;
$request['servicetype'] = 'CA';
$request['certIssue'] = 'NO';
function saveServiceForTest($request) {
    global $db, $source;
    $_GET = $request;
    http_response_code(200);
    ob_start();
    eval($source);
    return json_decode(ob_get_clean(), true, 512, JSON_THROW_ON_ERROR);
}
function checkSave($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
$created = saveServiceForTest($request);
$id = $created[0]['service_id'];
checkSave($id > 0, 'Create must return the new service ID');
checkSave($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == 1, 'New service must persist');
checkSave(in_array('tds_deducted', paymentTableColumns($db, 'consulting'), true), 'Old payment schema must be upgraded automatically');
checkSave(in_array('service_id', paymentTableColumns($db, 'invoice_details'), true), 'Invoice link column must be added automatically');
$request['serviceId'] = $id;
$request['certbody'] = 'Updated body';
$request['cbContractDetails'] = 'First installment';
$request['consultingDetails'] = 'Consulting installment';
$updated = saveServiceForTest($request);
checkSave($updated[0]['service_id'] == $id, 'Update must retain service ID');
checkSave($db->query('SELECT cert_body FROM services')->fetchColumn() === 'Updated body', 'Updated fields must persist');
checkSave(count($updated[1]) === 1 && count($updated[2]) === 1, 'Blank amounts must allow installment saves');
$request['cbRows'] = $updated[1][0]['certification_id'];
$request['consultingrows'] = $updated[2][0]['consulting_id'];
$request['cbDecidedAmount'] = 500;
$request['consultingPayment'] = '5000';
$request['consultingTds'] = '98.25';
$request['consultingShortSettle'] = '1.50';
$request['consultingBadDebt'] = '2.25';
$request['serviceNotes'] = 'Saved on a legacy database';
saveServiceForTest($request);
checkSave((float)$db->query('SELECT tds_deducted FROM consulting')->fetchColumn() === 98.25, 'TDS must persist after automatic migration');
checkSave((float)$db->query('SELECT short_settle FROM consulting')->fetchColumn() === 1.5, 'Short settlement must persist');
checkSave((float)$db->query('SELECT bad_debt FROM consulting')->fetchColumn() === 2.25, 'Bad debt must persist');
checkSave((float)$db->query('SELECT consulting_payment_received FROM consulting')->fetchColumn() === 5000.0, 'Payment amount must persist');
checkSave($db->query('SELECT service_notes FROM services')->fetchColumn() === $request['serviceNotes'], 'Notes must persist after automatic migration');
checkSave($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == 1, 'Repeated save must not duplicate service');
checkSave($db->query('SELECT COUNT(*) FROM certification_body')->fetchColumn() == 1, 'Repeated save must not duplicate installment');
checkSave($db->query('SELECT balance FROM certification_body')->fetchColumn() == 500, 'Balance must update');
$db->exec('CREATE TABLE customer_service_flow (service_id INTEGER, customer_id TEXT)');
$db->exec('INSERT INTO customer_service_flow VALUES (' . (int)$id . ', 1)');
$db->exec("UPDATE services SET service_type = 'SA-2' WHERE service_id = " . (int)$id);
$request['servicetype'] = 'CA';
saveServiceForTest($request);
checkSave($db->query('SELECT service_type FROM services WHERE service_id = ' . (int)$id)->fetchColumn() === 'SA-2', 'Payment/detail saves must not overwrite the header-controlled stage');
$request['serviceId'] = '';
$createdWithPayments = saveServiceForTest($request);
checkSave($createdWithPayments[0]['service_id'] != $id, 'Second service must get its own ID');
checkSave(count($createdWithPayments[1]) === 1 && count($createdWithPayments[2]) === 1, 'Create response must include its payment IDs');
$count = $db->query('SELECT COUNT(*) FROM services')->fetchColumn();
$db->exec("CREATE TRIGGER reject_consulting_insert BEFORE INSERT ON consulting BEGIN SELECT RAISE(ABORT, 'Simulated payment insert failure'); END");
$failure = saveServiceForTest($request);
checkSave(isset($failure['error']) && http_response_code() === 500, 'Failure must be reported');
checkSave($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == $count, 'Failed create must roll back');
echo "PASS: automatic legacy schema upgrade, repeated saves, payment/TDS/adjustments/notes, missing and existing service-flow table, duplicate prevention and rollback\n";
