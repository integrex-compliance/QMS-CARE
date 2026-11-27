<?php
// Per-customer certification types are separate from service stages (CA/SA/RC).
function ensureCustomerServiceFlow(PDO $db) {
    $db->exec("CREATE TABLE IF NOT EXISTS customer_service_flow (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        customer_id VARCHAR(100) NOT NULL,
        service_name VARCHAR(191) NOT NULL,
        service_id INT NOT NULL,
        certification_date DATE NULL,
        next_service_date DATE NULL,
        next_service_type VARCHAR(50) NOT NULL DEFAULT '',
        active TINYINT NOT NULL DEFAULT 1,
        UNIQUE KEY customer_type (customer_id, service_name),
        UNIQUE KEY linked_service (service_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function serviceFlowDate($value) {
    if ($value === '') return null;
    $date = DateTime::createFromFormat('!d.m.Y', $value);
    if (!$date || $date->format('d.m.Y') !== $value) {
        throw new InvalidArgumentException('Enter a valid date in dd.mm.yyyy format.');
    }
    return $date->format('Y-m-d');
}

function customerServiceStages(PDO $db) {
    try {
        return $db->query('SELECT stage FROM service_stage ORDER BY id ASC')->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        // Older installations do not yet have the stage master.
        if ($e->getCode() !== '42S02') throw $e;
        return ['CA', 'SA-1', 'SA-2', 'RC', 'New enq', 'CNE', 'Mnt.'];
    }
}

function saveCustomerServiceStage(PDO $db, $customerId, $flowId, $stage) {
    if (!is_string($stage) || !in_array($stage, customerServiceStages($db), true)) {
        throw new InvalidArgumentException('Select a valid service stage.');
    }
    $db->beginTransaction();
    try {
        // Use the same customer lock as selection updates to serialize removal and editing.
        $stmt = $db->prepare('SELECT customer_id FROM customer_details WHERE customer_id = ? FOR UPDATE');
        $stmt->execute([$customerId]);
        if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Customer not found.');
        $stmt = $db->prepare('SELECT s.service_id FROM customer_service_flow f INNER JOIN services s ON s.service_id = f.service_id AND s.customer_id = f.customer_id WHERE f.id = ? AND f.customer_id = ? AND f.active = 1');
        $stmt->execute([$flowId, $customerId]);
        $serviceId = $stmt->fetchColumn();
        if (!$serviceId) throw new InvalidArgumentException('Service not found. Refresh the page.');
        $stmt = $db->prepare('UPDATE services SET service_type = ? WHERE service_id = ? AND customer_id = ?');
        $stmt->execute([$stage, $serviceId, $customerId]);
        $db->commit();
        return ['status' => 'success', 'service_id' => $serviceId, 'stage' => $stage];
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
}

function deleteCustomerServiceFlow(PDO $db, $customerId, $flowId) {
    $db->beginTransaction();
    try {
        $stmt = $db->prepare('SELECT type_of_service FROM customer_details WHERE customer_id = ? FOR UPDATE');
        $stmt->execute([$customerId]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$customer) throw new InvalidArgumentException('Customer not found.');
        $stmt = $db->prepare('SELECT service_name, service_id FROM customer_service_flow WHERE id = ? AND customer_id = ?');
        $stmt->execute([$flowId, $customerId]);
        $flow = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$flow) throw new InvalidArgumentException('Service not found for this customer.');
        // Archive the assignment, retaining linked invoices, payments and dates for restoration.
        $stmt = $db->prepare('UPDATE customer_service_flow SET active = 0 WHERE id = ? AND customer_id = ?');
        $stmt->execute([$flowId, $customerId]);
        $selected = array_values(array_filter(array_map('trim', explode(',', $customer['type_of_service'] ?? '')), function ($name) use ($flow) {
            return $name !== '' && $name !== $flow['service_name'];
        }));
        $stmt = $db->prepare('UPDATE customer_details SET type_of_service = ? WHERE customer_id = ?');
        $stmt->execute([implode(', ', $selected), $customerId]);
        $db->commit();
        return ['status' => 'success', 'selected' => $selected, 'service_name' => $flow['service_name'], 'service_id' => $flow['service_id']];
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
}

function syncCustomerServiceFlow(PDO $db, $customerId, array $selected, $addOnly = false) {
    $db->beginTransaction();
    try {
        // Serialize selection updates, including simultaneous requests from different tabs.
        $stmt = $db->prepare('SELECT * FROM customer_details WHERE customer_id = ? FOR UPDATE');
        $stmt->execute([$customerId]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$customer) throw new InvalidArgumentException('Customer not found.');
        $known = $db->query('SELECT service FROM service_type')->fetchAll(PDO::FETCH_COLUMN);
        $legacy = array_filter(array_map('trim', explode(',', $customer['type_of_service'] ?? '')));
        // Add against the locked database selection, never a potentially stale browser list.
        if ($addOnly) $selected = array_merge($legacy, $selected);
        $selected = array_values(array_unique(array_map('trim', $selected)));
        foreach ($selected as $name) {
            if (!$name || strlen($name) > 191 || strpos($name, ',') !== false || (!in_array($name, $known, true) && !in_array($name, $legacy, true))) {
                throw new InvalidArgumentException('Select a valid service from the list.');
            }
        }
        $stmt = $db->prepare('UPDATE customer_service_flow SET active = 0 WHERE customer_id = ?');
        $stmt->execute([$customerId]);
        foreach ($selected as $name) {
            $stmt = $db->prepare('SELECT f.*, s.service_id AS existing_id FROM customer_service_flow f LEFT JOIN services s ON s.service_id = f.service_id AND s.customer_id = f.customer_id WHERE f.customer_id = ? AND f.service_name = ?');
            $stmt->execute([$customerId, $name]);
            $flow = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$flow || !$flow['existing_id']) {
                $stmt = $db->prepare("INSERT INTO services (customer_id, service_type, cert_body, consulting_firm, consultant, audit_type, auditor, handel_by, add_service_to, is_certified) VALUES (?, 'CA', '', '', '', '', '', '', 'Under Working', 'NO')");
                $stmt->execute([$customerId]);
                $sid = $db->lastInsertId();
                if ($flow) {
                    $stmt = $db->prepare('UPDATE customer_service_flow SET service_id = ?, active = 1 WHERE id = ?');
                    $stmt->execute([$sid, $flow['id']]);
                } else {
                    // New types start blank; customer-wide legacy dates cannot be assigned reliably.
                    $stmt = $db->prepare('INSERT INTO customer_service_flow (customer_id, service_name, service_id) VALUES (?, ?, ?)');
                    $stmt->execute([$customerId, $name, $sid]);
                }
            } else {
                $stmt = $db->prepare('UPDATE customer_service_flow SET active = 1 WHERE id = ?');
                $stmt->execute([$flow['id']]);
            }
        }
        $stmt = $db->prepare('UPDATE customer_details SET type_of_service = ? WHERE customer_id = ?');
        $stmt->execute([implode(', ', $selected), $customerId]);
        $db->commit();
        return $selected;
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
}
