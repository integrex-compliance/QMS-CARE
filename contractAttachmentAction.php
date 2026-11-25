<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once './dbConnect.php';
include_once './utility.php';

header('Content-Type: application/json');

$action = isset($_POST['action']) ? trim($_POST['action']) : '';

// Ensure table exists (defensive)
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `service_contract_attachments` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `customer_id` INT NOT NULL,
        `service_id` INT NOT NULL,
        `file_name` VARCHAR(255) NOT NULL,
        `file_path` VARCHAR(500) NOT NULL,
        `file_ext` VARCHAR(20) NOT NULL,
        `uploaded_by` VARCHAR(100) NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_cust_svc` (`customer_id`, `service_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
} catch (Exception $e) {
    // Ignore if already exists
}

if ($action === 'upload') {
    $customerId = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
    $serviceId = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;

    if ($customerId <= 0 || $serviceId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid customer or service ID.']);
        exit;
    }

    if (empty($_FILES['contract_file'])) {
        echo json_encode(['status' => 'error', 'message' => 'No file selected.']);
        exit;
    }

    $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
    $allowedMimes = ['application/pdf', 'image/jpeg', 'image/pjpeg', 'image/png'];
    $maxSize = 15 * 1024 * 1024; // 15 MB per file

    // Normalize single vs multiple file input
    $files = [];
    if (is_array($_FILES['contract_file']['name'])) {
        $count = count($_FILES['contract_file']['name']);
        for ($i = 0; $i < $count; $i++) {
            $files[] = [
                'name'     => $_FILES['contract_file']['name'][$i],
                'type'     => $_FILES['contract_file']['type'][$i],
                'tmp_name' => $_FILES['contract_file']['tmp_name'][$i],
                'error'    => $_FILES['contract_file']['error'][$i],
                'size'     => $_FILES['contract_file']['size'][$i],
            ];
        }
    } else {
        $files[] = $_FILES['contract_file'];
    }

    $uploadDir = 'uploads/contracts/' . $customerId . '/' . $serviceId . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadedBy = isset($_SESSION['username']) ? $_SESSION['username'] : (isset($_SESSION['user']) ? $_SESSION['user'] : '');
    $uploadedRecords = [];
    $errors = [];

    foreach ($files as $f) {
        if ($f['error'] !== UPLOAD_ERR_OK || empty($f['tmp_name'])) {
            $errors[] = $f['name'] . ': Upload failed.';
            continue;
        }
        if ($f['size'] > $maxSize) {
            $errors[] = $f['name'] . ': File exceeds 15MB limit.';
            continue;
        }

        $origName = basename($f['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            $errors[] = $origName . ': Only PDF, JPG, JPEG, and PNG files are allowed.';
            continue;
        }

        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $f['tmp_name']);
            finfo_close($finfo);
            if ($mime && !in_array($mime, $allowedMimes)) {
                $errors[] = $origName . ': Invalid file content type (' . $mime . ').';
                continue;
            }
        }

        $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
        if ($safeBase === '') {
            $safeBase = 'contract_scan';
        }
        $storedFilename = time() . '_' . substr(md5(uniqid('', true)), 0, 6) . '_' . $safeBase . '.' . $ext;
        $targetPath = $uploadDir . $storedFilename;

        if (move_uploaded_file($f['tmp_name'], $targetPath)) {
            $stmt = $db->prepare("INSERT INTO service_contract_attachments (customer_id, service_id, file_name, file_path, file_ext, uploaded_by) VALUES (:cid, :sid, :fname, :fpath, :fext, :uby)");
            $stmt->execute([
                ':cid'   => $customerId,
                ':sid'   => $serviceId,
                ':fname' => $origName,
                ':fpath' => $targetPath,
                ':fext'  => $ext,
                ':uby'   => $uploadedBy
            ]);
            $uploadedRecords[] = [
                'id'        => (int)$db->lastInsertId(),
                'file_name' => $origName,
                'file_path' => $targetPath,
                'file_ext'  => $ext
            ];
        } else {
            $errors[] = $origName . ': Could not save file to disk.';
        }
    }

    if (!empty($uploadedRecords)) {
        echo json_encode([
            'status'      => 'success',
            'attachments' => $uploadedRecords,
            'warnings'    => $errors
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => !empty($errors) ? implode(' ', $errors) : 'No valid files uploaded.'
        ]);
    }
    exit;
}

if ($action === 'delete') {
    $attId = isset($_POST['attachment_id']) ? (int)$_POST['attachment_id'] : 0;
    $customerId = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
    $serviceId = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;

    if ($attId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid attachment ID.']);
        exit;
    }

    try {
        $stmt = $db->prepare("SELECT * FROM service_contract_attachments WHERE id = :id AND customer_id = :cid AND service_id = :sid");
        $stmt->execute([':id' => $attId, ':cid' => $customerId, ':sid' => $serviceId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $path = $row['file_path'];
            if (strpos($path, 'uploads/contracts/') === 0 && file_exists($path)) {
                @unlink($path);
            }
            $del = $db->prepare("DELETE FROM service_contract_attachments WHERE id = :id");
            $del->execute([':id' => $attId]);
        }

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
