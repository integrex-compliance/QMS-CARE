<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include_once './dbConnect.php';

if (isset($_POST['customer']) && !empty($_POST['customer']) && isset($_POST['service']) && !empty($_POST['service'])) {
    try {
        $customerDetail = "SELECT certification_date, next_service_date, next_service_type FROM customer_details WHERE customer_id = :id";
        $statement = $db->prepare($customerDetail);
        $statement->bindParam(':id', $_POST['customer']);
        $statement->execute();
        $customer = $statement->fetch();

        $serviceType = "SELECT service_type FROM services WHERE service_id = :id";
        $statement = $db->prepare($serviceType);
        $statement->bindParam(':id', $_POST['service']);
        $statement->execute();
        $service_type = $statement->fetch();

        if ($service_type['service_type'] == "CA") {
            $service = "SA-1";
            $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 1 year'));
        } else if ($service_type['service_type'] == "SA-1") {
            $service = "SA-2";
//            $serviceType = "SELECT count(*) as count FROM services WHERE customer_id = :id and service_type = :serviceType";
//            $statement = $db->prepare($serviceType);
//            $statement->bindParam(':id', $_POST['customer']);
//            $statement->bindParam(':serviceType', $service_type['service_type']);
//            $statement->execute();
//            $service_count = $statement->fetch();
////                echo $service_count['count'];
//            $num = (int) $service_count['count'];
//            $num = 2 + (3 * ($num - 1));
//                echo $num;
            $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 2 year'));
        } else if ($service_type['service_type'] == "SA-2") {
            $service = "RC";
//            $serviceType = "SELECT count(*) as count FROM services WHERE customer_id = :id and service_type = :serviceType";
//            $statement = $db->prepare($serviceType);
//            $statement->bindParam(':id', $_POST['customer']);
//            $statement->bindParam(':serviceType', $service_type['service_type']);
//            $statement->execute();
//            $service_count = $statement->fetch();
////                echo $service_count['count'];
//            $num = (int) $service_count['count'];
//            $num = 3 + (3 * ($num - 1));
            $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 3 year'));
        } else if ($service_type['service_type'] == "RC") {
            $service = "SA-1";
//            $serviceType = "SELECT count(*) as count FROM services WHERE customer_id = :id and service_type = :serviceType";
//            $statement = $db->prepare($serviceType);
//            $statement->bindParam(':id', $_POST['customer']);
//            $statement->bindParam(':serviceType', $service_type['service_type']);
//            $statement->execute();
//            $service_count = $statement->fetch();
////                echo $service_count['count'];
//            $num = (int) $service_count['count'];
//            $num = 4 + (3 * ($num - 1));
            $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 1 year'));
        }

        $updateCustomer = "UPDATE customer_details SET next_service_date = '" . $insertDate . "', next_service_type = '" . $service . "'  WHERE customer_id = '" . $_POST['customer'] . "'";
        $statement = $db->prepare($updateCustomer);
        $statement->execute();

        $issueCert = "UPDATE services SET is_certified = 'YES' WHERE service_id = :service";
        $statement = $db->prepare($issueCert);
        $statement->bindParam(':service', $_POST['service']);
        $statement->execute();

        $customerdetail = "SELECT next_service_date, next_service_type FROM customer_details WHERE customer_id = '" . $_POST['customer'] . "'";
        $statement = $db->prepare($customerdetail);
        $statement->execute();
        $customerInfo = $statement->fetch();

        echo json_encode($customerInfo);
//        header('Location:customerAccountDetails.php?customerid='.$_POST['customer']);
    } catch (Exception $ex) {
        echo $ex;
    }
}
else if(isset ($_POST['serviceID']) && !empty ($_POST['serviceID']) && isset ($_POST['customerID']) && !empty ($_POST['customerID'])){
    try {
        $issueCert = "UPDATE services SET is_certified = 'NO' WHERE service_id = :service";
        $statement = $db->prepare($issueCert);
        $statement->bindParam(':service',$_POST['serviceID']);
        $statement->execute();
        header('Location:customerAccountDetails.php?customerid='.$_POST['customerID']);
    } catch (Exception $ex) {
        echo $ex;
    }    
}