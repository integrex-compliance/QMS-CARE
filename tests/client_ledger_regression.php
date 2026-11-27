<?php
require_once __DIR__ . '/../client_ledger.php';
function ledgerCheck($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
$services = [
    ['service_id' => 1, 'service_type' => 'CA', 'created_at' => '2025-01-01', 'is_delete' => 'N'],
    ['service_id' => 2, 'service_type' => 'SA-1', 'created_at' => '2026-01-01', 'is_delete' => 'N'],
    ['service_id' => 3, 'service_type' => 'RC', 'is_delete' => 'Y'],
];
$payments = [
    ['service_id' => 1, 'consulting_amount_decided' => '1000.10', 'consulting_payment_received' => '600.05', 'tds_deducted' => '100', 'consulting_deposit_date' => '2025-02-01'],
    ['service_id' => 1, 'consulting_fees' => 'TDS', 'consulting_amount_decided' => '50', 'consulting_payment_received' => '50'],
    ['service_id' => 1, 'consulting_fees' => 'Short settlement', 'consulting_amount_decided' => '20'],
    ['service_id' => 1, 'consulting_fees' => 'Loss', 'bad_debt' => '30'],
    ['service_id' => 2, 'consulting_amount_decided' => '200', 'consulting_payment_received' => '250', 'consulting_deposit_date' => '2026-02-01'],
    ['service_id' => 3, 'consulting_amount_decided' => '99999', 'consulting_payment_received' => '99999'],
    ['service_id' => 1, 'consulting_amount_decided' => '99999', 'is_delete' => 'Y'],
];
$cb = [['service_id' => 1, 'amount_decided' => '500', 'payment_received' => '400', 'deposit_date' => '2025-03-01']];
$invoices = [['total_amount' => '1500', 'is_deleted' => 'NO'], ['total_amount' => '9000', 'is_deleted' => 'Yes']];
$ledger = buildClientLedger($services, $payments, $cb, $invoices);
$t = $ledger['totals'];
ledgerCheck($t['amount'] === 170010, 'Amounts decided must exclude legacy adjustments and deleted records');
ledgerCheck($t['received'] === 125005, 'Cash receipts must include CB and exclude TDS-only records');
ledgerCheck($t['tds'] === 15000 && $t['short'] === 2000 && $t['loss'] === 3000, 'All adjustment types must remain separate');
ledgerCheck($t['pending'] === 25005 && $t['invoiced'] === 150000, 'Invoice references must not double-count charges');
ledgerCheck($ledger['services'][2]['balance'] === -5000, 'Service advances must remain signed when consolidating');
ledgerCheck(end($ledger['entries'])['balance'] === $t['balance'], 'Running ledger must reconcile to summary');
ledgerCheck(end($ledger['entries'])['date'] === '', 'Undated entries must be last');
$empty = buildClientLedger([], [], [], []);
ledgerCheck($empty['totals']['pending'] === 0 && !$empty['entries'], 'Empty client must have a zero ledger');
$advance = buildClientLedger([$services[0]], [['service_id' => 1, 'consulting_payment_received' => '100.25']], [], []);
ledgerCheck($advance['totals']['advance'] === 10025 && $advance['totals']['pending'] === 0, 'Advance cannot become negative pending');
ledgerCheck($advance['missingAmounts'] === 1, 'Incomplete financial data must be visible');

// Check customer scoping and soft-deletion against a real SQL engine, without production writes.
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE services (service_id INTEGER, customer_id INTEGER, service_type TEXT, created_at TEXT, is_delete TEXT)');
$db->exec('CREATE TABLE consulting (service_id, consulting_amount_decided, consulting_payment_received, is_delete)');
$db->exec('CREATE TABLE certification_body (service_id, amount_decided, payment_received, is_delete)');
$db->exec("INSERT INTO services VALUES (1, 60, 'CA', '2025-01-01', 'N'), (2, 61, 'CA', '2025-01-01', 'N'), (3, 60, 'RC', '2025-01-01', 'Y')");
$db->exec("INSERT INTO consulting VALUES (1, 100, 20, 'N'), (2, 9999, 9999, 'N'), (1, 9999, 9999, 'Y'), (3, 9999, 9999, 'N')");
$db->exec("INSERT INTO certification_body VALUES (1, 50, 10, 'N')");
$scoped = loadClientLedger($db, 60, []);
ledgerCheck($scoped['totals']['amount'] === 15000 && $scoped['totals']['received'] === 3000, 'Queries must isolate the client and exclude deleted rows/services');
echo "PASS: ledger totals, receipt types, adjustments, invoice deduplication, advances, running balances, empty/incomplete data and SQL scoping\n";
