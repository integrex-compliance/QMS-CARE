<?php
function ensureServiceStageConfig(PDO $db) {
    if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        $db->exec('CREATE TABLE IF NOT EXISTS service_stage (id INTEGER PRIMARY KEY AUTOINCREMENT, stage VARCHAR(50) NOT NULL UNIQUE)');
        $db->exec('CREATE TABLE IF NOT EXISTS service_type_stages (service_type_id INTEGER NOT NULL, stage_id INTEGER NOT NULL, sort_order INTEGER NOT NULL DEFAULT 0, PRIMARY KEY (service_type_id, stage_id))');
        return;
    }
    $db->exec("CREATE TABLE IF NOT EXISTS service_stage (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, stage VARCHAR(50) NOT NULL, UNIQUE KEY stage (stage)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");
    $db->exec("CREATE TABLE IF NOT EXISTS service_type_stages (
        service_type_id INT NOT NULL,
        stage_id INT NOT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        PRIMARY KEY (service_type_id, stage_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function serviceTypeStageMap(PDO $db) {
    $map = [];
    foreach ($db->query('SELECT t.service, s.stage FROM service_type_stages m INNER JOIN service_type t ON t.id = m.service_type_id INNER JOIN service_stage s ON s.id = m.stage_id ORDER BY m.sort_order, s.id')->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $map[$row['service']][] = $row['stage'];
    }
    return $map;
}

function allowedServiceTypeStages(PDO $db, $name) {
    $stmt = $db->prepare('SELECT s.stage FROM service_type_stages m INNER JOIN service_type t ON t.id = m.service_type_id INNER JOIN service_stage s ON s.id = m.stage_id WHERE t.service = ? ORDER BY m.sort_order, s.id');
    $stmt->execute([$name]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function saveServiceTypeStages(PDO $db, $typeId, array $stages) {
    if (count($stages) > 200) throw new InvalidArgumentException('Choose no more than 200 stages.');
    $names = [];
    foreach ($stages as $stage) {
        if (!is_string($stage) || trim($stage) === '' || mb_strlen(trim($stage), 'UTF-8') > 50 || preg_match('/[\x00-\x1f\x7f]/', $stage)) {
            throw new InvalidArgumentException('Each stage must be a name of 1 to 50 characters.');
        }
        $names[] = trim($stage);
    }
    $db->beginTransaction();
    try {
        $stmt = $db->prepare('SELECT service FROM service_type WHERE id = ? FOR UPDATE');
        $stmt->execute([$typeId]);
        $name = $stmt->fetchColumn();
        if ($name === false) throw new InvalidArgumentException('Service type not found. Refresh the page.');
        $stmt = $db->prepare('DELETE FROM service_type_stages WHERE service_type_id = ?');
        $stmt->execute([$typeId]);
        $seen = [];
        foreach ($names as $position => $stage) {
            $stmt = $db->prepare('SELECT id FROM service_stage WHERE LOWER(TRIM(stage)) = LOWER(?) ORDER BY id LIMIT 1');
            $stmt->execute([$stage]);
            $stageId = $stmt->fetchColumn();
            if (!$stageId) {
                $stmt = $db->prepare('INSERT INTO service_stage (stage) VALUES (?) ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)');
                $stmt->execute([$stage]);
                $stageId = $db->lastInsertId();
            }
            if (isset($seen[$stageId])) continue;
            $seen[$stageId] = true;
            $stmt = $db->prepare('INSERT INTO service_type_stages (service_type_id, stage_id, sort_order) VALUES (?, ?, ?)');
            $stmt->execute([$typeId, $stageId, $position]);
        }
        $db->commit();
        return allowedServiceTypeStages($db, $name);
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
}
