<?php
// Script to create a test user for login
include_once './dbConnect.php';

echo "<h2>Create Test User</h2>";

if ($db) {
    try {
        // Check if user table exists
        $checkTable = $db->query("SHOW TABLES LIKE 'user'");
        if ($checkTable->rowCount() == 0) {
            echo "<p style='color: red;'>Error: 'user' table does not exist in the database.</p>";
            echo "<p>Please create the database structure first.</p>";
            exit;
        }
        
        // Create test user
        $email = 'admin@qmscare.com';
        $password = 'admin123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userName = 'Admin User';
        $abbreviation = 'ADM';
        $role = 'Main Admin';
        
        // Check if user already exists
        $checkUser = $db->prepare("SELECT email_id FROM user WHERE email_id = :email");
        $checkUser->bindParam(':email', $email);
        $checkUser->execute();
        
        if ($checkUser->rowCount() > 0) {
            echo "<p style='color: orange;'>User already exists with email: <strong>$email</strong></p>";
            echo "<p>You can login with:</p>";
            echo "<ul>";
            echo "<li><strong>Email:</strong> $email</li>";
            echo "<li><strong>Password:</strong> admin123</li>";
            echo "</ul>";
        } else {
            // Insert new user
            $insertUser = $db->prepare("INSERT INTO user (user_name, abbreviation, email_id, password, role) VALUES (:name, :abbr, :email, :pass, :role)");
            $insertUser->bindParam(':name', $userName);
            $insertUser->bindParam(':abbr', $abbreviation);
            $insertUser->bindParam(':email', $email);
            $insertUser->bindParam(':pass', $hashedPassword);
            $insertUser->bindParam(':role', $role);
            
            if ($insertUser->execute()) {
                echo "<p style='color: green;'>✓ Test user created successfully!</p>";
                echo "<h3>Login Credentials:</h3>";
                echo "<ul>";
                echo "<li><strong>Email:</strong> $email</li>";
                echo "<li><strong>Password:</strong> admin123</li>";
                echo "<li><strong>Role:</strong> $role</li>";
                echo "</ul>";
                echo "<p><a href='index.php'>Go to Login Page</a></p>";
            } else {
                echo "<p style='color: red;'>Error creating user</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed</p>";
}
?>
