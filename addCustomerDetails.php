<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
  && isset($_GET['columnValue']) && !empty($_GET['columnValue'])
 */

session_start();
include_once './dbConnect.php';

if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['columnName']) && !empty($_GET['columnName'])) {
    try {
//        echo $_GET['columnName'];
        $column = $_GET['columnName'];
//        if ($column == "certification_date")
//            $value = date("Y-m-d", strtotime($_GET['columnValue']));
//        else if ($column == "next_service_date")
//            $value = date("Y-m-d", strtotime($_GET['columnValue']));
//        if ($column == "next_service_type")
//            $value = $_GET['columnValue'];  

        $customerDetails = "SELECT certification_date, next_service_type FROM customer_details WHERE customer_id = :id";
        $statement = $db->prepare($customerDetails);
        $statement->bindParam(':id', $_GET['id']);
        $statement->execute();
        $customerInfo = $statement->fetch();

        if ($customerInfo['next_service_type'] == "") {
            $service = "CA";
        } else if ($customerInfo['next_service_type'] == "CA" || $customerInfo['next_service_type'] == "RC") {
            $service = "SA-1";
        } else if ($customerInfo['next_service_type'] == "SA-1") {
            $service = "SA-2";
        } else if ($customerInfo['next_service_type'] == "SA-2") {
            $service = "RC";
        }

        if ($column == "certification_date") {
            if (isset($_GET['columnValue']) && !empty($_GET['columnValue'])) {
                $value = date("Y-m-d", strtotime($_GET['columnValue']));
                $insertDate = date('Y-m-d ', strtotime($value . '+ 1 year'));
                $updateCustomer = "UPDATE customer_details SET certification_date = '" . $value . "' WHERE customer_id = '" . $_GET['id'] . "'";
                $statement = $db->prepare($updateCustomer);
                $statement->execute();
            } else {
                $value = NULL;
                //$insertDate = date('Y-m-d ', strtotime($value . '+ 1 year'));
                $updateCustomer = "UPDATE customer_details SET certification_date = NULL WHERE customer_id = '" . $_GET['id'] . "'";
                $statement = $db->prepare($updateCustomer);
                $statement->execute();
            }
//            $customerDetail = "SELECT next_service_date, next_service_type FROM customer_details WHERE customer_id = '" . $_GET['id'] . "'";
//            $statement = $db->prepare($customerDetail);
//            $statement->execute();
//            $customer = $statement->fetch();
////          echo $value;
//            echo json_encode($customer);
        } else if ($column == "next_service_date") {

            if (isset($_GET['columnValue']) && !empty($_GET['columnValue'])) {
                $value = date("Y-m-d", strtotime($_GET['columnValue']));
                //$insertDate = date('Y-m-d ', strtotime($value . '+ 1 year'));
                $updateCustomer = "UPDATE customer_details SET next_service_date = '" . $value . "' WHERE customer_id = '" . $_GET['id'] . "'";
                $statement = $db->prepare($updateCustomer);
                $statement->execute();
            } else {
                $value = NULL;
                //$insertDate = date('Y-m-d ', strtotime($value . '+ 1 year'));
                $updateCustomer = "UPDATE customer_details SET next_service_date = NULL WHERE customer_id = '" . $_GET['id'] . "'";
                $statement = $db->prepare($updateCustomer);
                $statement->execute();
            }

//            $customerDetail = "SELECT next_service_date, next_service_type FROM customer_details WHERE customer_id = '" . $_GET['id'] . "'";
//            $statement = $db->prepare($customerDetail);
//            $statement->execute();
//            $customer = $statement->fetch();
        } else if ($column == "next_service_type") {
            $value = $_GET['columnValue'];
            $updateCustomer = "UPDATE customer_details SET next_service_type = '" . $value . "' WHERE customer_id = '" . $_GET['id'] . "'";
            $statement = $db->prepare($updateCustomer);
            $statement->execute();

            $customerDetail = "SELECT next_service_date, next_service_type FROM customer_details WHERE customer_id = '" . $_GET['id'] . "'";
            $statement = $db->prepare($customerDetail);
            $statement->execute();
            $customer = $statement->fetch();

            echo json_encode($customer);
        } else if ($column == "comments") {
            $value = $_GET['columnValue'];
            $updateCustomer = "UPDATE customer_details SET comments = TRIM(:commentvalue) WHERE customer_id = '" . $_GET['id'] . "'";
            $statement = $db->prepare($updateCustomer);
            $statement->bindParam(':commentvalue', $_GET['columnValue']);
            $statement->execute();

            $customerDetail = "SELECT next_service_date, next_service_type,comments FROM customer_details WHERE customer_id = '" . $_GET['id'] . "'";
            $statement = $db->prepare($customerDetail);
            $statement->execute();
            $customer = $statement->fetch();

            echo json_encode($customer);
        } else if ($column == "marketing_audit_type") {
            $value = $_GET['columnValue'];
            $updateCustomer = "UPDATE customer_details SET marketing_audit_type = TRIM(:auditType) WHERE customer_id = :id";
            $statement = $db->prepare($updateCustomer);
            $statement->bindParam(':auditType', $_GET['columnValue']);
            $statement->bindParam(':id', $_GET['id']);
            $statement->execute();
        }
//
//        if ($column == "certification_date") {
//            $insertDate = date('Y-m-d ', strtotime($value . '+ 1 year'));
//            $updateCustomer = "UPDATE customer_details SET `" . $column . "` = '" . $value . "', next_service_date = '" . $insertDate . "' WHERE customer_id = 1";
//            $statement = $db->prepare($updateCustomer);
//            $statement->execute();
//            
////            $customerDetail = "SELECT next_service_date, next_service_type FROM customer_details WHERE customer_id = '".$_GET['id']."'";
////            $statement = $db->prepare($customerDetail);
////            $statement->execute();
////            $customer = $statement->fetch();
////            
////            echo json_encode($customer);
//        } else {
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['customerid']) && !empty($_GET['customerid']) && isset($_GET['value']) && !empty($_GET['value'])) {
    try {
        $nextService = "UPDATE customer_details SET next_service_type = :value WHERE customer_id = :id";
        $statement = $db->prepare($nextService);
        $statement->bindParam(':id', $_GET['customerid']);
        $statement->bindParam(':value', $_GET['value']);
        $statement->execute();
        echo "success";
    } catch (Exception $ex) {
        
    }
} else if(isset ($_GET['svcid']) && !empty ($_GET['svcid'])){
    try {
        $updateHandler = "UPDATE services SET handel_by = :handler where service_id = :id";
        $statement = $db->prepare($updateHandler);
        $statement->bindParam(':handler', $_GET['handler']);
        $statement->bindParam(':id', $_GET['svcid']);
        $statement->execute();
        echo 'success';
    } catch (Exception $ex) {
        echo $ex;
    }
} else if(isset ($_GET['customId']) && !empty ($_GET['customId'])){
    try {
        $updatePriority = "UPDATE customer_details SET priority = :priority where customer_id = :id";
        $statement = $db->prepare($updatePriority);
        $statement->bindParam(':priority', $_GET['priority']);
        $statement->bindParam(':id', $_GET['customId']);
        $statement->execute();
        echo "success";
    } catch (Exception $ex) {
        echo $ex;
    }
} else if(isset ($_GET['clientId']) && !empty ($_GET['clientId']) && isset ($_GET['auditDate']) && !empty ($_GET['auditDate'])){
    try {
        $updateAudit = "UPDATE services SET audit_date = :auditDate where service_id = :id";
        $statement = $db->prepare($updateAudit);
        $statement->bindParam(':auditDate', date("Y-m-d", strtotime($_GET['auditDate'])));
        $statement->bindParam(':id', $_GET['clientId']);
        $statement->execute();
        echo "success";
    } catch (Exception $ex) {
        echo $ex;
    }
}