<?php

session_start();
include_once './dbConnect.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
try{
 $addCustomer = "update customer_details set company_name = :company, company_address = :address,"
                . " contact_no=:number, comments=:comment, marketing_audit_type=:auditType where customer_id=:customer_id";
        $statement = $db->prepare($addCustomer);
        $statement->bindParam(":company", $_POST['mktgcompanyName']);
        $statement->bindParam(":address", $_POST['mktgcompanyAddress']);
        $statement->bindParam(":number", $_POST['mktgcontactNo']);
        $statement->bindParam(":auditType", $_POST['mktgauditType']);
        $statement->bindParam(":comment", $_POST['mktgcompanyComment']);
        $statement->bindParam(":customer_id", $_POST['mktgcustomreId']);
        $statement->execute();
//        echo $_POST['mktgaudityType'];
        header("Location: marketingDetails.php");
}catch(Exception $e){
    echo $e->getMessage();
}