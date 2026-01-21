<?php
// Script to check and reset password for a specific user
include_once './dbConnect.php';

echo "<h2>Password Reset Tool</h2>";

if ($db) {
    try {
        $email = 'bh@qmscare.com';
        
        // Get current user info
        $getUserInfo = $db->prepare("SELECT user_id, user_name, email_id, password, role FROM user WHERE email_id = :email");
        $getUserInfo->bindParam(':email', $email);
        $getUserInfo->execute();
        $user = $getUserInfo->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<h3>Current User Information:</h3>";
            echo "<ul>";
            echo "<li><strong>User ID:</strong> " . htmlspecialchars($user['user_id']) . "</li>";
            echo "<li><strong>Name:</strong> " . htmlspecialchars($user['user_name']) . "</li>";
            echo "<li><strong>Email:</strong> " . htmlspecialchars($user['email_id']) . "</li>";
            echo "<li><strong>Role:</strong> " . htmlspecialchars($user['role']) . "</li>";
            echo "</ul>";
            
            // Check if password is hashed
            $currentPassword = $user['password'];
            echo "<h3>Password Analysis:</h3>";
            
            if (strlen($currentPassword) == 60 && substr($currentPassword, 0, 4) == '$2y$') {
                echo "<p style='color: green;'>✓ Password appears to be properly hashed (bcrypt)</p>";
            } else {
                echo "<p style='color: orange;'>⚠ Password may not be properly hashed</p>";
                echo "<p>Current password length: " . strlen($currentPassword) . " characters</p>";
            }
            
            // Reset password to a known value
            $newPassword = 'password123';
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $updatePassword = $db->prepare("UPDATE user SET password = :password WHERE email_id = :email");
            $updatePassword->bindParam(':password', $hashedPassword);
            $updatePassword->bindParam(':email', $email);
            
            if ($updatePassword->execute()) {
                echo "<hr>";
                echo "<h3 style='color: green;'>✓ Password has been reset!</h3>";
                echo "<h3>New Login Credentials:</h3>";
                echo "<ul>";
                echo "<li><strong>Email:</strong> bh@qmscare.com</li>";
                echo "<li><strong>Password:</strong> password123</li>";
                echo "<li><strong>Role:</strong> " . htmlspecialchars($user['role']) . "</li>";
                echo "</ul>";
                echo "<p><a href='index.php'><button style='padding: 10px 20px; background: #4d90fe; color: white; border: none; border-radius: 5px; cursor: pointer;'>Go to Login Page</button></a></p>";
            } else {
                echo "<p style='color: red;'>Error updating password</p>";
            }
            
        } else {
            echo "<p style='color: red;'>User not found with email: $email</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed</p>";
}
?>
