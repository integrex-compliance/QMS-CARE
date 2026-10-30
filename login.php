<?php

session_start();
include_once './dbConnect.php';
include 'utility.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//include_once './dbConnect.php';

if (isset($_POST['useremail']) && !empty($_POST['useremail']) && isset($_POST['password']) && !empty($_POST['password'])) {
    
    // Verify reCAPTCHA v2
    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        $secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        
        if (!$responseData->success) {
            header("Location: index.php?invalid=captcha");
            exit();
        }
    } else {
        header("Location: index.php?invalid=captcha");
        exit();
    }
    
    try {
        $localIP = getHostByName(php_uname('n'));
//        echo $localIP; die;
//        $sql = "SELECT count(pcip) as ip FROM `pc_ip` WHERE pcip = :ids";
//        $statement = $db->prepare($sql);
//        $statement->bindParam(":ids", $localIP);
//        $statement->execute();
//        $rowIpCount = $statement->fetch();
//////        echo $rowIpCount["ip"]; die;
//        if ($rowIpCount["ip"] > 0) {
            $selectUserEmail = "SELECT count(*) as user from user where email_id = :email AND (is_deleted = '0' OR is_deleted IS NULL OR is_deleted = '' OR is_deleted = 'N') AND (is_deleted IS NULL OR (is_deleted != '1' AND is_deleted != 'Y'))";
            $statement = $db->prepare($selectUserEmail);
            $statement->bindParam(":email", $_POST['useremail']);
            $statement->execute();
            $rowUserCount = $statement->fetch();

            if ($rowUserCount['user'] > 0) {
                $userLogin = "SELECT user_id, user_name, abbreviation, email_id, password, role FROM user WHERE email_id = :useremail AND (is_deleted = '0' OR is_deleted IS NULL OR is_deleted = '' OR is_deleted = 'N') AND (is_deleted IS NULL OR (is_deleted != '1' AND is_deleted != 'Y'))";
                $statement = $db->prepare($userLogin);
                $statement->bindParam(':useremail', $_POST['useremail']);
                $statement->execute();
                $row = $statement->fetch();

//            if ($_POST['password'] == $row['password']) 
                if (password_verify($_POST['password'], $row['password'])) {
                    $_SESSION['username'] = $row['user_name'];
                    $_SESSION['id'] = $row['user_id'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['abbrevation'] = $row['abbreviation'];

                    // Redirect based on role - use exit() after each redirect
                    if (Utitlity::checkForConsultant()) {
                        header("Location: myDashboard.php");
                        exit();
                    }
                    if (Utitlity::checkForUserAdmin()) {
                        header("Location: servicedue.php");
                        exit();
                    }
                    if (Utitlity::checkForOfficeStaff()) {
                        header("Location: workingDetails_Test.php");
                        exit();
                    }
                    if (Utitlity::checkForUserConsultant()) {
                        header("Location: workingDetails_Test.php");
                        exit();
                    }
                    if (Utitlity::checkForUserFinance()) {
                        header("Location: financeDetails.php");
                        exit();
                    }
                    if (Utitlity::checkForUserMarketing()) {
                        header("Location: marketingDetails.php");
                        exit();
                    }
                    if (Utitlity::checkForMainAdmin()) {
                        header("Location: workingDetails_Test.php");
                        exit();
                    }
                    if (Utitlity::checkForOfficeBDM()) {
                        header("Location: homePage_Test.php");
                        exit();
                    }
                    if (Utitlity::checkTaskManager()) {
                        header("Location: taskMgmt.php");
                        exit();
                    }
                    
                    // If no role matches, redirect to a default page
                    header("Location: index.php?invalid=norole");
                    exit();
                } else {
                    $msg = "invalid";
                    header("Location: index.php?invalid=" . $msg);
                    exit();
                }
            } else {
                $msg = "invalid";
                header("Location: index.php?invalid=" . $msg);
                exit();
            }
//        } else {
//            $msg = "invalidip";
//            header("Location: index.php?invalid=" . $msg);
//        }
    } catch (Exception $exc) {
        echo "Login Error: " . $exc->getMessage() . "<br>";
        echo "<a href='index.php'>Back to Home</a>";
        die();
    }
} else {
    // If form is not submitted properly, redirect back
    header("Location: index.php");
    exit();
}
