<?php
// Run: C:/xampp/php/php.exe tests/service_save_regression.php
// Exercise the production save handler with an isolated SQLite database.
$source = file_get_contents(__DIR__ . '/../addService.php');
$source = str_replace(array('<?php', 'session_start();', "include_once './dbConnect.php';"), '', $source);
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
preg_match_all('/INSERT INTO (services|certification_body|consulting) \(([^)]+)\)/', $source, $inserts, PREG_SET_ORDER);
$primaryKeys = array('services' => 'service_id', 'certification_body' => 'certification_id', 'consulting' => 'consulting_id');
foreach ($inserts as $insert) {
    $db->exec('CREATE TABLE IF NOT EXISTS ' . $insert[1] . ' (' . $primaryKeys[$insert[1]] . ' INTEGER PRIMARY KEY AUTOINCREMENT, ' . $insert[2] . ')');
}
$db->exec('CREATE TABLE customer_details (customer_id, certification_date, next_service_date, next_service_type)');
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
saveServiceForTest($request);
checkSave($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == 1, 'Repeated save must not duplicate service');
checkSave($db->query('SELECT COUNT(*) FROM certification_body')->fetchColumn() == 1, 'Repeated save must not duplicate installment');
checkSave($db->query('SELECT balance FROM certification_body')->fetchColumn() == 500, 'Balance must update');
$request['serviceId'] = '';
$createdWithPayments = saveServiceForTest($request);
checkSave($createdWithPayments[0]['service_id'] != $id, 'Second service must get its own ID');
checkSave(count($createdWithPayments[1]) === 1 && count($createdWithPayments[2]) === 1, 'Create response must include its payment IDs');
$count = $db->query('SELECT COUNT(*) FROM services')->fetchColumn();
$db->exec('DROP TABLE consulting');
$failure = saveServiceForTest($request);
checkSave(isset($failure['error']) && http_response_code() === 500, 'Failure must be reported');
checkSave($db->query('SELECT COUNT(*) FROM services')->fetchColumn() == $count, 'Failed create must roll back');
echo "PASS: create, persisted fields, update, payment IDs, empty amounts, duplicate prevention and rollback\n";
