<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include_once './dbConnect.php';
include 'utility.php';

if (isset($_POST['customerEmail']) && !empty($_POST['customerEmail'])) {
    try {
        $checkCustomer = "SELECT count(*) as exist from customer_details where contact_email = '" . $_POST['customerEmail'] . "'";
        $statement = $db->prepare($checkCustomer);
        $statement->execute();
        $customerCount = $statement->fetch();
        if ($customerCount['exist'] > 0) {
            echo 1;
        } else {
            echo 0;
        }
    } catch (Exception $ex) {
        
    }
}
else if(isset ($_POST['userEmail']) && !empty ($_POST['userEmail'])){
    try {
        $checkUser = "SELECT count(*) as exist from user where email_id  = '" . $_POST['userEmail'] . "'";
        $statement = $db->prepare($checkUser);
        $statement->execute();
        $userCount = $statement->fetch();
        if ($userCount['exist'] > 0) {
            echo 1;
        } else {
            echo 0;
        }
    } catch (Exception $ex) {
        
    }
} 
else if(isset ($_POST['customerName']) && !empty ($_POST['customerName'])){
    try {
        $chkCustomerName = "SELECT count(*) as exist from customer_details where company_name = '" . $_POST['customerName'] . "'";
        $statement = $db->prepare($chkCustomerName);
        $statement->execute();
        $customerNameCount = $statement->fetch();      
        if ($customerNameCount['exist'] > 0) {
            echo 1;
        } else {
            echo 0;
        }
    } catch (Exception $ex) {
        
    }
}