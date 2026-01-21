<?php
// Check database structure and stored procedures
include_once './dbConnect.php';

echo "<h2>Database Structure Check</h2>";

if ($db) {
    try {
        // Check for stored procedures
        echo "<h3>Stored Procedures:</h3>";
        $procedures = $db->query("SHOW PROCEDURE STATUS WHERE Db = 'qmscare'");
        $procList = $procedures->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($procList) > 0) {
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>Name</th><th>Type</th></tr>";
            foreach ($procList as $proc) {
                echo "<tr><td>" . htmlspecialchars($proc['Name']) . "</td><td>" . htmlspecialchars($proc['Type']) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠ No stored procedures found</p>";
            echo "<p>The stored procedure <strong>usp_GetEmployeeDetailsDynamic</strong> is missing.</p>";
        }
        
        echo "<hr>";
        
        // Check for required tables
        echo "<h3>Database Tables:</h3>";
        $tables = $db->query("SHOW TABLES");
        $tableList = $tables->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<ul>";
        foreach ($tableList as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
        
        // Check if customer/employee related tables exist
        echo "<hr>";
        echo "<h3>Checking Required Tables:</h3>";
        $requiredTables = ['auditors', 'consultant', 'cert_body', 'customer', 'employee'];
        
        foreach ($requiredTables as $tableName) {
            if (in_array($tableName, $tableList)) {
                echo "<p style='color: green;'>✓ Table '$tableName' exists</p>";
            } else {
                echo "<p style='color: red;'>⨯ Table '$tableName' is missing</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed</p>";
}

echo "<br><br>";
echo "<a href='index.php'>← Back to Home</a>";
?>
