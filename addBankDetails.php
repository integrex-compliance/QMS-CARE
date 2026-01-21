<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include_once './dbConnect.php';

if (isset($_POST['accountHolderName']) && !empty($_POST['accountHolderName']) && isset($_POST['bankName']) && !empty($_POST['bankName']) && isset($_POST['accountNumber']) && !empty($_POST['accountNumber']) && isset($_POST['bankifscCode']) && !empty($_POST['bankifscCode']) && isset($_POST['bankMicrNo']) && !empty($_POST['bankMicrNo'])) {
    try {
        $addBankAccount = "INSERT INTO bank_account (bank_name, accountant_name, ac_number, bank_ifsc_code, bank_micr_no) VALUES (:bankName, :accountName, :accountNo, :fscCode, :micrNo)";
        $statement = $db->prepare($addBankAccount);
        $statement->bindParam(':bankName', $_POST['accountHolderName']);
        $statement->bindParam(':accountName', $_POST['bankName']);
        $statement->bindParam(':accountNo', $_POST['accountNumber']);
        $statement->bindParam(':fscCode', $_POST['bankifscCode']);
        $statement->bindParam(':micrNo', $_POST['bankMicrNo']);
        $statement->execute();
        header("Location: masterData.php");
    } catch (Exception $ex) {
        
    }
} else {
    if (isset($_GET['accountId']) && !empty($_GET['accountId'])) {
        try {
            $removeBankAccount = "DELETE FROM bank_account WHERE id = :accountid";
            $statement = $db->prepare($removeBankAccount);
            $statement->bindParam(':accountid', $_GET['accountId']);
            $statement->execute();

            $bankAccounts = "SELECT * FROM bank_account";
            $statement = $db->prepare($bankAccounts);
            $statement->execute();
            $bankAccountList = $statement->fetchAll();
            
            echo json_encode($bankAccountList);
        } catch (Exception $ex) {
            
        }
    }
}