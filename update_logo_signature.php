<?php
/**
 * Script to update logo and signature for all consulting firms
 * This will replace the logo and signature in all PDF documents
 */

include_once('dbConnect.php');

// New logo and signature paths
$newLogoPath = 'images/qmc_new_logo.png';
$newSignaturePath = 'images/qmc_new_signature.png';

// Check if files exist
echo "<h2>Checking Files...</h2>";

$logoFullPath = __DIR__ . '/' . $newLogoPath;
$signFullPath = __DIR__ . '/' . $newSignaturePath;

$errors = array();

if (!file_exists($logoFullPath)) {
    $errors[] = "❌ Logo file NOT FOUND: $logoFullPath";
} else {
    $logoSize = filesize($logoFullPath);
    if ($logoSize < 100) {
        $errors[] = "❌ Logo file is EMPTY or INVALID (size: $logoSize bytes)";
    } else {
        echo "✅ Logo file exists: $logoFullPath (Size: " . round($logoSize/1024, 2) . " KB)<br>";
    }
}

if (!file_exists($signFullPath)) {
    $errors[] = "❌ Signature file NOT FOUND: $signFullPath";
} else {
    $signSize = filesize($signFullPath);
    if ($signSize < 100) {
        $errors[] = "❌ Signature file is EMPTY or INVALID (size: $signSize bytes)";
    } else {
        echo "✅ Signature file exists: $signFullPath (Size: " . round($signSize/1024, 2) . " KB)<br>";
    }
}

if (!empty($errors)) {
    echo "<br><div style='background:#ffcccc; padding:20px; border:2px solid red;'>";
    echo "<h2>⚠️ ERRORS FOUND:</h2>";
    foreach($errors as $error) {
        echo $error . "<br>";
    }
    echo "<br><h3>📋 INSTRUCTIONS:</h3>";
    echo "1. Save the QMC circular logo as: <b>c:\\xampp\\htdocs\\bkup\\images\\qmc_new_logo.png</b><br>";
    echo "2. Save the pen signature as: <b>c:\\xampp\\htdocs\\bkup\\images\\qmc_new_signature.png</b><br>";
    echo "3. Refresh this page after saving the files<br>";
    echo "</div>";
    die();
}

echo "<hr>";

try {
    // Update all consulting firms with new logo and signature
    $updateSql = "UPDATE consulting_firm 
                  SET firm_logo = :logo, 
                      firmSignature = :signature";
    
    $stmt = $db->prepare($updateSql);
    $stmt->bindParam(':logo', $newLogoPath);
    $stmt->bindParam(':signature', $newSignaturePath);
    
    if ($stmt->execute()) {
        $rowsAffected = $stmt->rowCount();
        echo "✅ SUCCESS!<br><br>";
        echo "Updated <b>$rowsAffected</b> consulting firms with:<br>";
        echo "📌 New Logo: <b>$newLogoPath</b><br>";
        echo "📌 New Signature: <b>$newSignaturePath</b><br><br>";
        echo "All PDFs (Invoices & Quotations) will now use the new logo and signature!<br>";
    } else {
        echo "❌ ERROR: Failed to update database.";
    }
    
} catch(Exception $e) {
    echo "❌ Database Error: " . $e->getMessage();
}

// Display current firms and their logo/signature paths
try {
    echo "<hr><h3>Current Firms Status:</h3>";
    $selectSql = "SELECT consulting_firm_id, firm_name, firm_logo, firmSignature FROM consulting_firm";
    $stmt = $db->prepare($selectSql);
    $stmt->execute();
    $firms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr><th>Firm ID</th><th>Firm Name</th><th>Logo Path</th><th>Signature Path</th></tr>";
    
    foreach($firms as $firm) {
        echo "<tr>";
        echo "<td>" . $firm['consulting_firm_id'] . "</td>";
        echo "<td>" . $firm['firm_name'] . "</td>";
        echo "<td>" . $firm['firm_logo'] . "</td>";
        echo "<td>" . $firm['firmSignature'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch(Exception $e) {
    echo "<br>❌ Error displaying firms: " . $e->getMessage();
}
?>
