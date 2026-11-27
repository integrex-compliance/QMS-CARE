<?php
// Additive upgrades for installations predating the payment system.
// Run before payment transactions: MySQL ALTER TABLE implicitly commits.
function paymentTableColumns(PDO $db, $table) {
    if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        $rows = $db->query('PRAGMA table_info(`' . $table . '`)')->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) throw new RuntimeException('Missing payment table: ' . $table);
        return array_column($rows, 'name');
    }
    return $db->query('SHOW COLUMNS FROM `' . $table . '`')->fetchAll(PDO::FETCH_COLUMN);
}

function ensurePaymentSchema(PDO $db) {
    if ($db->inTransaction()) throw new LogicException('Payment schema must be checked before starting a transaction.');
    $required = [
        'consulting' => [
            'tds_deducted' => 'DECIMAL(15,2) NOT NULL DEFAULT 0',
            'short_settle' => 'DECIMAL(15,2) NOT NULL DEFAULT 0',
            'bad_debt' => 'DECIMAL(15,2) NOT NULL DEFAULT 0',
        ],
        'services' => ['service_notes' => 'TEXT NULL'],
        'invoice_details' => ['service_id' => 'INT NULL'],
    ];
    foreach ($required as $table => $columns) {
        $existing = paymentTableColumns($db, $table);
        foreach ($columns as $column => $definition) {
            if (in_array($column, $existing, true)) continue;
            try {
                $db->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
            } catch (PDOException $e) {
                // Another request may have added the same column concurrently.
                if (!in_array($column, paymentTableColumns($db, $table), true)) throw $e;
            }
        }
    }
}

function paymentHasServiceFlow(PDO $db) {
    try {
        $db->query('SELECT service_id, customer_id FROM customer_service_flow WHERE 1 = 0');
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() === '42S02' || ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite' && strpos($e->getMessage(), 'no such table: customer_service_flow') !== false)) return false;
        throw $e;
    }
}

function paymentSchemaFailureMessage(Throwable $error) {
    if ($error instanceof PDOException && in_array((int) ($error->errorInfo[1] ?? 0), [1044, 1142, 1143], true)) {
        return 'Payment database upgrade needs ALTER permission. Ask the hosting administrator to grant it, then retry. No payment was saved.';
    }
    return 'Payment database upgrade could not finish. Ask the administrator to check the server error log, then retry. No payment was saved.';
}
