<?php
require_once __DIR__ . '/service_stage_config.php';
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
        KEY customer_type (customer_id, service_name),
        UNIQUE KEY linked_service (service_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    migrateCustomerServiceFlowStages($db);
    ensureServiceStageConfig($db);
}

function migrateCustomerServiceFlowStages(PDO $db) {
    // Upgrade the old one-record-per-type schema without touching existing IDs/history.
    $indexes = [];
    foreach ($db->query('SHOW INDEX FROM customer_service_flow')->fetchAll(PDO::FETCH_ASSOC) as $index) {
        if (!$index['Non_unique']) $indexes[$index['Key_name']][(int)$index['Seq_in_index']] = $index['Column_name'];
    }
    foreach ($indexes as $name => $columns) {
        ksort($columns);
        if (array_values($columns) === ['customer_id', 'service_name']) {
            $quoted = '`' . str_replace('`', '``', $name) . '`';
            $db->exec("ALTER TABLE customer_service_flow DROP INDEX $quoted, ADD INDEX $quoted (customer_id, service_name)");
        }
    }
}

function createCustomerStageService(PDO $db, $customerId, $stage) {
    $stmt = $db->prepare("INSERT INTO services (customer_id, service_type, cert_body, consulting_firm, consultant, audit_type, auditor, handel_by, add_service_to, is_certified) VALUES (?, ?, '', '', '', '', '', '', 'Under Working', 'NO')");
    $stmt->execute([$customerId, $stage]);
    return $db->lastInsertId();
}

// Business default for legacy records that predate named service assignments.
// Link the original service ID so its stage, invoices and payments stay intact.
function assignLegacyCustomerServices(PDO $db, $customerId) {
    $db->beginTransaction();
    try {
        $stmt = $db->prepare('SELECT type_of_service FROM customer_details WHERE customer_id = ? FOR UPDATE');
        $stmt->execute([$customerId]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$customer) {
            $db->commit();
            return 0;
        }
        $stmt = $db->prepare("SELECT s.service_id FROM services s LEFT JOIN customer_service_flow f ON f.service_id = s.service_id WHERE s.customer_id = ? AND f.id IS NULL AND COALESCE(s.is_delete, 'N') <> 'Y' ORDER BY s.service_id");
        $stmt->execute([$customerId]);
        $serviceIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if ($serviceIds) {
            $insert = $db->prepare("INSERT INTO customer_service_flow (customer_id, service_name, service_id) VALUES (?, 'ISO 9001', ?)");
            foreach ($serviceIds as $serviceId) $insert->execute([$customerId, $serviceId]);
            $selected = array_values(array_filter(array_map('trim', explode(',', $customer['type_of_service'] ?? '')), 'strlen'));
            if (!in_array('ISO 9001', $selected, true)) {
                $selected[] = 'ISO 9001';
                $stmt = $db->prepare('UPDATE customer_details SET type_of_service = ? WHERE customer_id = ?');
                $stmt->execute([implode(', ', $selected), $customerId]);
            }
        }
        $db->commit();
        return count($serviceIds);
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
}

// Keep each named service together, with its stages in certification order.
function sortCustomerServiceFlows(array $flows) {
    $groups = [];
    foreach ($flows as $flow) $groups[$flow['service_name']][] = $flow;
    uasort($groups, function ($a, $b) { return min(array_column($a, 'id')) <=> min(array_column($b, 'id')); });
    $ordered = [];
    $ranks = ['CA' => 0, 'SA-1' => 1, 'SA-2' => 2, 'RC' => 3];
    foreach ($groups as $group) {
        usort($group, function ($a, $b) use ($ranks) {
            return (($ranks[$a['current_stage'] ?? ''] ?? 4) <=> ($ranks[$b['current_stage'] ?? ''] ?? 4)) ?: ($a['id'] <=> $b['id']);
        });
        foreach ($group as $flow) $ordered[] = $flow;
    }
    return $ordered;
}

function groupCustomerStageServices(array $services, array $flows) {
    $byId = [];
    foreach ($services as $service) $byId[$service['service_id']] = $service;
    $linked = array_fill_keys(array_column($flows, 'service_id'), true);
    $ordered = array_values(array_filter($services, function ($service) use ($linked) { return !isset($linked[$service['service_id']]); }));
    foreach (sortCustomerServiceFlows($flows) as $flow) {
        if (isset($byId[$flow['service_id']])) $ordered[] = $byId[$flow['service_id']];
    }
    return $ordered;
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
    if (!is_string($stage) || $stage === '') {
        throw new InvalidArgumentException('Select a valid service stage.');
    }
    $db->beginTransaction();
    try {
        // Use the same customer lock as selection updates to serialize removal and editing.
        $stmt = $db->prepare('SELECT customer_id FROM customer_details WHERE customer_id = ? FOR UPDATE');
        $stmt->execute([$customerId]);
        if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Customer not found.');
        $stmt = $db->prepare('SELECT f.service_name FROM customer_service_flow f INNER JOIN services s ON s.service_id = f.service_id AND s.customer_id = f.customer_id WHERE f.id = ? AND f.customer_id = ? AND f.active = 1');
        $stmt->execute([$flowId, $customerId]);
        $name = $stmt->fetchColumn();
        if (!$name) throw new InvalidArgumentException('Service not found. Refresh the page.');
        $stmt = $db->prepare('SELECT id FROM service_type WHERE service = ? FOR UPDATE');
        $stmt->execute([$name]);
        if (!$stmt->fetchColumn() || !in_array($stage, allowedServiceTypeStages($db, $name), true)) {
            throw new InvalidArgumentException('This stage is not enabled for ' . $name . ' in Master Creator. Refresh the page.');
        }
        $stmt = $db->prepare('SELECT f.id, f.service_id FROM customer_service_flow f INNER JOIN services s ON s.service_id = f.service_id AND s.customer_id = f.customer_id WHERE f.customer_id = ? AND f.service_name = ? AND s.service_type = ? ORDER BY f.active DESC, f.id LIMIT 1');
        $stmt->execute([$customerId, $name, $stage]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            $serviceId = $existing['service_id'];
            $flowId = $existing['id'];
            $stmt = $db->prepare('UPDATE customer_service_flow SET active = 1 WHERE id = ?');
            $stmt->execute([$flowId]);
        } else {
            $serviceId = createCustomerStageService($db, $customerId, $stage);
            $stmt = $db->prepare('INSERT INTO customer_service_flow (customer_id, service_name, service_id) VALUES (?, ?, ?)');
            $stmt->execute([$customerId, $name, $serviceId]);
            $flowId = $db->lastInsertId();
        }
        $db->commit();
        return ['status' => 'success', 'service_id' => $serviceId, 'flow_id' => $flowId, 'stage' => $stage];
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
        $stmt = $db->prepare('SELECT COUNT(*) FROM customer_service_flow WHERE customer_id = ? AND service_name = ? AND active = 1');
        $stmt->execute([$customerId, $flow['service_name']]);
        $hasOtherStages = (int)$stmt->fetchColumn() > 0;
        $selected = array_values(array_filter(array_map('trim', explode(',', $customer['type_of_service'] ?? '')), function ($name) use ($flow, $hasOtherStages) {
            return $name !== '' && ($hasOtherStages || $name !== $flow['service_name']);
        }));
        $stmt = $db->prepare('UPDATE customer_details SET type_of_service = ? WHERE customer_id = ?');
        $stmt->execute([implode(', ', $selected), $customerId]);
        $db->commit();
        return ['status' => 'success', 'selected' => $selected, 'service_name' => $flow['service_name'], 'service_id' => $flow['service_id'], 'flow_id' => $flowId];
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
        $stmt = $db->prepare('SELECT DISTINCT service_name FROM customer_service_flow WHERE customer_id = ?');
        $stmt->execute([$customerId]);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $name) {
            if (!in_array($name, $selected, true)) {
                $deactivate = $db->prepare('UPDATE customer_service_flow SET active = 0 WHERE customer_id = ? AND service_name = ?');
                $deactivate->execute([$customerId, $name]);
            }
        }
        foreach ($selected as $name) {
            $stmt = $db->prepare('SELECT f.*, s.service_id AS existing_id FROM customer_service_flow f LEFT JOIN services s ON s.service_id = f.service_id AND s.customer_id = f.customer_id WHERE f.customer_id = ? AND f.service_name = ?');
            $stmt->execute([$customerId, $name]);
            $flows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($flows ?: [null] as $flow) {
                if (!$flow || !$flow['existing_id']) {
                    $allowedStages = allowedServiceTypeStages($db, $name);
                    if (!$allowedStages) throw new InvalidArgumentException('Add stages for ' . $name . ' in Master Creator first.');
                    $initialStage = in_array('CA', $allowedStages, true) ? 'CA' : $allowedStages[0];
                    $sid = createCustomerStageService($db, $customerId, $initialStage);
                    if ($flow) {
                        $stmt = $db->prepare('UPDATE customer_service_flow SET service_id = ?, active = 1 WHERE id = ?');
                        $stmt->execute([$sid, $flow['id']]);
                    } else {
                        // New stages start blank; dates belong to their original stage.
                        $stmt = $db->prepare('INSERT INTO customer_service_flow (customer_id, service_name, service_id) VALUES (?, ?, ?)');
                        $stmt->execute([$customerId, $name, $sid]);
                    }
                } elseif (!in_array($name, $legacy, true)) {
                    $stmt = $db->prepare('UPDATE customer_service_flow SET active = 1 WHERE id = ?');
                    $stmt->execute([$flow['id']]);
                }
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
