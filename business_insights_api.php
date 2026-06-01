<?php
session_start();
include_once './dbConnect.php';
include_once './utility.php';

// Only allow main admin
if (!Utitlity::checkForMainAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$month = isset($_GET['month']) ? $_GET['month'] : 'all';
$year = isset($_GET['year']) ? $_GET['year'] : 'all';

$params = [];
$dateCondition = "";

if ($month !== 'all' && $year !== 'all') {
    // Both month and year selected
    $dateCondition = " AND MONTH(i.invoice_date) = :month AND YEAR(i.invoice_date) = :year ";
    $params[':month'] = $month;
    $params[':year'] = $year;
} elseif ($year !== 'all') {
    // Only year selected
    $dateCondition = " AND YEAR(i.invoice_date) = :year ";
    $params[':year'] = $year;
} elseif ($month !== 'all') {
    // Only month selected (for any year, though usually year is selected too)
    $dateCondition = " AND MONTH(i.invoice_date) = :month ";
    $params[':month'] = $month;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'summary';

try {
    if ($action === 'details') {
        $firmId = isset($_GET['firm_id']) ? $_GET['firm_id'] : 0;
        
        $detailQuery = "
            SELECT 
                i.invoice_id,
                i.invoice_no,
                i.invoice_date,
                i.customer_name,
                i.total_amount,
                i.net_price,
                i.service_tax
            FROM invoice_details i
            WHERE i.is_deleted = 'No' AND i.firm_id = :firm_id
            $dateCondition
            ORDER BY i.invoice_date DESC
        ";
        
        $params[':firm_id'] = $firmId;
        $stmt = $db->prepare($detailQuery);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->execute();
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'invoices' => $invoices]);
        exit;
    }

    // 1. Overall stats
    $overallQuery = "
        SELECT 
            COUNT(i.invoice_id) as total_invoices,
            SUM(i.total_amount) as total_due
        FROM invoice_details i
        WHERE i.is_deleted = 'No'
        $dateCondition
    ";
    
    $stmt = $db->prepare($overallQuery);
    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val);
    }
    $stmt->execute();
    $overallStats = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Organization-wise stats
    $orgQuery = "
        SELECT 
            c.firm_name,
            c.consulting_firm_id,
            COUNT(i.invoice_id) as total_invoices,
            SUM(i.total_amount) as total_due,
            SUM(i.net_price) as net_price,
            SUM(i.service_tax) as tax_amount
        FROM consulting_firm c
        LEFT JOIN invoice_details i ON c.consulting_firm_id = i.firm_id AND i.is_deleted = 'No' $dateCondition
        GROUP BY c.consulting_firm_id, c.firm_name
        ORDER BY total_due DESC
    ";
    
    $stmt2 = $db->prepare($orgQuery);
    foreach ($params as $key => &$val) {
        $stmt2->bindParam($key, $val);
    }
    $stmt2->execute();
    $orgStats = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    // Clean up null values from left join
    foreach ($orgStats as &$org) {
        if ($org['total_invoices'] === null) $org['total_invoices'] = 0;
        if ($org['total_due'] === null) $org['total_due'] = 0;
    }

    echo json_encode([
        'success' => true,
        'overall' => [
            'total_invoices' => (int)$overallStats['total_invoices'],
            'total_due' => (float)$overallStats['total_due']
        ],
        'organizations' => $orgStats
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
