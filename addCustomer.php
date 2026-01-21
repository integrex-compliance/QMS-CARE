<?php

session_start();
include_once './dbConnect.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (isset($_POST['editCustomerId']) && !empty($_POST['editCustomerId'])) {
    try {
//        echo "in";
        if (isset($_POST['comments']) && !empty($_POST['comments']))
            $customerComment = $_POST['comments'];
        else
            $customerComment = "";

        if (isset($_POST['clientPriority']) && !empty($_POST['clientPriority']))
            $priority = $_POST['clientPriority'];
        else
            $priority = "High";

        if (isset($_POST["client_of"]) && !empty($_POST["client_of"]))
        {
            $ctype = $_POST["client_of"];
            if($_POST['customerReferred']==NULL)
            {
            $cref = $_POST["client_of"];
            
            }
            else $cref = $_POST['customerReferred'];
        }
        else
            $ctype = NULL;
$ref = NULL;
        $updateCustomer = "UPDATE customer_details SET company_name = :c_name, company_address = :c_address, company_location =:c_location, contact_person = :c_person, contact_no = :c_no, contact_email = :c_email, client_reference = :c_referance, comments = :c_comment, priority = :priority, client_of = :type, gst_number = :gst  WHERE customer_id = :id";
        $statement = $db->prepare($updateCustomer);
        $statement->bindParam(':c_name', $_POST['companyName']);
        $statement->bindParam(':c_address', $_POST['companyAddress']);
        $statement->bindParam(':c_location', $_POST['location']);
        $statement->bindParam(':c_person', $_POST['customerContactPrsnHidden']);
        $statement->bindParam(':c_no', $_POST['customerContactHidden']);
        $statement->bindParam(':c_email', $_POST['customerContactEmlHidden']);
        $statement->bindParam(':c_referance',$cref );
        $statement->bindParam(':c_comment', $customerComment);
        $statement->bindParam(':id', $_POST['editCustomerId']);
        $statement->bindParam(":priority", $priority);
        $statement->bindParam(":type", $ctype);
        $statement->bindParam(":gst", $_POST['clientGstNumber']);
        $statement->execute();
        header("Location: customerAccountDetails.php?customerid=" . $_POST['editCustomerId']);
    } catch (Exception $ex) {
        echo $ex;
    }
} else {
    if (isset($_POST['contactNo']) && !empty($_POST['contactNo'])) {
        try {

            if (isset($_POST['comments']) && !empty($_POST['comments']))
                $customerComment = $_POST['comments'];
            else
                $customerComment = "";
            $type = "CA";

            if (isset($_POST['clientPriority']) && !empty($_POST['clientPriority']))
                $priority = $_POST['clientPriority'];
            else
                $priority = "High";

            if (isset($_POST["client_of"]) && !empty($_POST["client_of"]))
                $ctype = $_POST["client_of"];
            else
                $ctype = NULL;
            if($ctype=='Other')
                $ref= $_POST['customerReferred'];
            else
                $ref=$ctype;
            $addCustomer = "INSERT INTO customer_details (company_name, company_address,company_location, contact_person, contact_no, contact_email, client_reference, comments, next_service_type, priority, client_of, gst_number) VALUES (:company,:address,:c_location,:name,:number,:email,:referance,:comment,:service,:priority,:type,:gst)";
            $statement = $db->prepare($addCustomer);
            $statement->bindParam(":company", $_POST['companyName']);
            $statement->bindParam(":address", $_POST['companyAddress']);
            $statement->bindParam(":c_location", $_POST['location']);
            $statement->bindParam(":name", $_POST['customerContactPrsnHidden']);
            $statement->bindParam(":number", $_POST['customerContactHidden']);
            $statement->bindParam(":email", $_POST['customerContactEmlHidden']);
            $statement->bindParam(":referance", $ref);
            $statement->bindParam(":comment", $customerComment);
            $statement->bindParam(":service", $type);
            $statement->bindParam(":priority", $priority);
            $statement->bindParam(":type", $ctype);
            $statement->bindParam(":gst", $_POST['clientGstNumber']);
            $statement->execute();

            $sql = "SELECT max(LAST_INSERT_ID(customer_id)) as id From customer_details";
            $statement = $db->prepare($sql);
            $statement->execute();
            $row = $statement->fetch();
            $customerId = $row['id'];
//        echo "success";
            header("Location: customerAccountDetails.php?customerid=" . $customerId);
        } catch (Exception $ex) {
            
        }
    }
}