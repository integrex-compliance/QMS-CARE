<?php
// Test login functionality
session_start();
include_once './dbConnect.php';
include 'utility.php';

echo "<h2>Login Test & Debug</h2>";
echo "<hr>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['useremail']) ? $_POST['useremail'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    echo "<h3>Login Attempt Details:</h3>";
    echo "<ul>";
    echo "<li><strong>Email submitted:</strong> " . htmlspecialchars($email) . "</li>";
    echo "<li><strong>Password submitted:</strong> " . (empty($password) ? '(empty)' : '****** (hidden, length: ' . strlen($password) . ')') . "</li>";
    echo "</ul>";
    echo "<hr>";
    
    if (!empty($email) && !empty($password)) {
        try {
            // Check if user exists
            $selectUserEmail = "SELECT count(*) as user from user where email_id = :email";
            $statement = $db->prepare($selectUserEmail);
            $statement->bindParam(":email", $email);
            $statement->execute();
            $rowUserCount = $statement->fetch();
            
            echo "<h3>Step 1: Check if user exists</h3>";
            if ($rowUserCount['user'] > 0) {
                echo "<p style='color: green;'>✓ User found in database</p>";
                
                // Get user details
                $userLogin = "SELECT user_id, user_name, abbreviation, email_id, password, role FROM user WHERE email_id = :useremail";
                $statement = $db->prepare($userLogin);
                $statement->bindParam(':useremail', $email);
                $statement->execute();
                $row = $statement->fetch();
                
                echo "<h3>Step 2: User Details Retrieved</h3>";
                echo "<ul>";
                echo "<li><strong>User ID:</strong> " . htmlspecialchars($row['user_id']) . "</li>";
                echo "<li><strong>Name:</strong> " . htmlspecialchars($row['user_name']) . "</li>";
                echo "<li><strong>Role:</strong> " . htmlspecialchars($row['role']) . "</li>";
                echo "</ul>";
                
                echo "<h3>Step 3: Password Verification</h3>";
                if (password_verify($password, $row['password'])) {
                    echo "<p style='color: green;'>✓ Password is correct!</p>";
                    
                    // Set session
                    $_SESSION['username'] = $row['user_name'];
                    $_SESSION['id'] = $row['user_id'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['abbrevation'] = $row['abbreviation'];
                    
                    echo "<h3>Step 4: Session Set</h3>";
                    echo "<ul>";
                    echo "<li><strong>Session Username:</strong> " . htmlspecialchars($_SESSION['username']) . "</li>";
                    echo "<li><strong>Session Role:</strong> " . htmlspecialchars($_SESSION['role']) . "</li>";
                    echo "</ul>";
                    
                    echo "<h3>Step 5: Role Check & Redirect</h3>";
                    echo "<ul>";
                    
                    $redirected = false;

                    if (Utitlity::checkForConsultant()) {
                        echo "<li style='color: green;'>✓ User is Consultant - Would redirect to: myDashboard.php</li>";
                        $redirectUrl = "myDashboard.php";
                        $redirected = true;
                    }
                    if (Utitlity::checkForUserAdmin()) {
                        echo "<li style='color: green;'>✓ User is Admin - Would redirect to: servicedue.php</li>";
                        $redirectUrl = "servicedue.php";
                        $redirected = true;
                    }
                    if (Utitlity::checkForOfficeStaff()) {
                        echo "<li style='color: green;'>✓ User is Office Staff - Would redirect to: workingDetails_Test.php</li>";
                        $redirectUrl = "workingDetails_Test.php";
                        $redirected = true;
                    }
                    if (Utitlity::checkForUserConsultant()) {
                        echo "<li style='color: green;'>✓ User is Working Project - Would redirect to: workingDetails_Test.php</li>";
                        $redirectUrl = "workingDetails_Test.php";
                        $redirected = true;
                    }
                    if (Utitlity::checkForUserFinance()) {
                        echo "<li style='color: green;'>✓ User is Finance - Would redirect to: financeDetails.php</li>";
                        $redirectUrl = "financeDetails.php";
                        $redirected = true;
                    }
                    if (Utitlity::checkForUserMarketing()) {
                        echo "<li style='color: green;'>✓ User is Marketing - Would redirect to: marketingDetails.php</li>";
                        $redirectUrl = "marketingDetails.php";
                        $redirected = true;
                    }
                    if (Utitlity::checkForMainAdmin()) {
                        echo "<li style='color: green;'>✓ User is Main Admin - Would redirect to: workingDetails_Test.php</li>";
                        $redirectUrl = "workingDetails_Test.php";
                        $redirected = true;
                    }
                    if (Utitlity::checkForOfficeBDM()) {
                        echo "<li style='color: green;'>✓ User is Office BDM - Would redirect to: homePage_Test.php</li>";
                        $redirectUrl = "homePage_Test.php";
                        $redirected = true;
                    }
                    if (Utitlity::checkTaskManager()) {
                        echo "<li style='color: green;'>✓ User is Task Manager - Would redirect to: taskMgmt.php</li>";
                        $redirectUrl = "taskMgmt.php";
                        $redirected = true;
                    }
                    
                    if (!$redirected) {
                        echo "<li style='color: red;'>⨯ No role matched! User role is: " . htmlspecialchars($row['role']) . "</li>";
                    }
                    
                    echo "</ul>";
                    
                    if ($redirected) {
                        echo "<hr>";
                        echo "<h3 style='color: green;'>Login Successful!</h3>";
                        echo "<p><a href='$redirectUrl'><button style='padding: 10px 20px; background: #4d90fe; color: white; border: none; border-radius: 5px; cursor: pointer;'>Click here to go to your dashboard</button></a></p>";
                    }
                    
                } else {
                    echo "<p style='color: red;'>⨯ Password is incorrect!</p>";
                    echo "<p><strong>Tip:</strong> Use the reset_password.php script to reset this user's password.</p>";
                }
                
            } else {
                echo "<p style='color: red;'>⨯ User not found with email: " . htmlspecialchars($email) . "</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Email or password is empty!</p>";
    }
    
} else {
    // Show login form
    ?>
    <form method="POST" action="">
        <div style="max-width: 400px; margin: 20px 0;">
            <h3>Test Login Form</h3>
            <p>Use this form to test login functionality with detailed debugging output.</p>
            
            <div style="margin-bottom: 15px;">
                <label><strong>Email:</strong></label><br>
                <input type="text" name="useremail" placeholder="Email" value="bh@qmscare.com" style="width: 100%; padding: 10px; margin-top: 5px;" required />
            </div>
            
            <div style="margin-bottom: 15px;">
                <label><strong>Password:</strong></label><br>
                <input type="password" name="password" placeholder="Password" style="width: 100%; padding: 10px; margin-top: 5px;" required />
            </div>
            
            <button type="submit" style="padding: 10px 20px; background: #4d90fe; color: white; border: none; border-radius: 5px; cursor: pointer;">Test Login</button>
        </div>
    </form>
    
    <hr>
    <p><strong>Quick Actions:</strong></p>
    <ul>
        <li><a href="reset_password.php">Reset password for bh@qmscare.com</a></li>
        <li><a href="check_login_debug.php">View all users in database</a></li>
        <li><a href="index.php">Go to main login page</a></li>
    </ul>
    <?php
}
?>