<?php
// Debug script to check database connection and user table
include_once './dbConnect.php';

echo "<h2>Database Connection Test</h2>";

// Check if database connection is working
if ($db) {
    echo "✓ Database connection successful<br><br>";
    
    // Check if user table exists and has data
    try {
        $sql = "SELECT user_id, user_name, email_id, role FROM user LIMIT 5";
        $statement = $db->prepare($sql);
        $statement->execute();
        $users = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) > 0) {
            echo "<h3>Users found in database:</h3>";
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>User ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['user_name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email_id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>⨯ No users found in database</p>";
            echo "<p>You need to create a user account first.</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>⨯ Database connection failed</p>";
}

echo "<br><br>";
echo "<a href='index.php'>← Back to Home</a>";
?>
