<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include_once './dbConnect.php';
include 'utility.php';

if (isset($_POST['serviceID']) && !empty($_POST['serviceID']) && isset($_POST['customerID']) && !empty($_POST['customerID'])) {
    try {
        $certDlt = "DELETE FROM certification_body WHERE service_id = :id";
        $statement = $db->prepare($certDlt);
        $statement->bindParam(':id', $_POST['serviceID']);
        $statement->execute();

        $consultingDlt = "DELETE FROM consulting WHERE service_id = :id";
        $statement = $db->prepare($consultingDlt);
        $statement->bindParam(':id', $_POST['serviceID']);
        $statement->execute();

        $serviceDlt = "DELETE FROM services WHERE service_id = :id";
        $statement = $db->prepare($serviceDlt);
        $statement->bindParam(':id', $_POST['serviceID']);
        $statement->execute();

        header("Location: customerAccountDetails.php?customerid=" . $_POST['customerID']);
    } catch (Exception $ex) {
        
    }
} else if (isset($_POST["customerDltList"]) && !empty($_POST["customerDltList"])) {
    try {
        $checkPoint = explode('~', $_POST['customerDltList']);
        for ($i = 0; $i < count($checkPoint); ++$i) {
            if ($checkPoint[$i] != "") {
                $dltCustomer = "DELETE FROM customer_details WHERE customer_id = :id";
                $statement = $db->prepare($dltCustomer);
                $statement->bindParam(':id', $checkPoint[$i]);
                $statement->execute();
            }
        }
        header("Location: recycleCustomers.php");
    } catch (Exception $ex) {
        
    }
} else if (isset($_POST['customerid']) && !empty($_POST['customerid'])) {
    try {
        $serviceList = "SELECT * FROM services WHERE customer_id = :id";
        $statement = $db->prepare($serviceList);
        $statement->bindParam(':id', $_POST['customerid']);
        $statement->execute();
        $services = $statement->fetchAll();

        foreach ($services as $service) {

            $certDlt = "update certification_body set is_delete = 'Y' WHERE service_id = :id";
            $statement = $db->prepare($certDlt);
            $statement->bindParam(':id', $service['service_id']);
            $statement->execute();

            $consultingDlt = "update consulting set is_delete = 'Y' WHERE service_id = :id";
            $statement = $db->prepare($consultingDlt);
            $statement->bindParam(':id', $service['service_id']);
            $statement->execute();
        }

        $serviceDlt = "update services set is_delete='Y' WHERE customer_id = :id";
        $statement = $db->prepare($serviceDlt);
        $statement->bindParam(':id', $_POST['customerid']);
        $statement->execute();

        $customerDlt = "update customer_details set is_delete='Y' WHERE customer_id = :id";
        $statement = $db->prepare($customerDlt);
        $statement->bindParam(':id', $_POST['customerid']);
        $statement->execute();
//        echo Utitlity::checkForUserMarketing(); die;
        if (Utitlity::checkForUserMarketing() || Utitlity::checkForOfficeStaff())
            header("Location: workingDetails.php");
        else
            header("Location: homePage.php");
    } catch (Exception $ex) {
        
    }
} else if (isset($_POST["customersList"]) && !empty($_POST["customersList"])) {
    try {
        $checkPoint = explode('~', $_POST['customersList']);
        for ($i = 0; $i < count($checkPoint); ++$i) {
            if ($checkPoint[$i] != "") {
                $serviceList = "SELECT * FROM services WHERE customer_id = :id";
                $statement = $db->prepare($serviceList);
                $statement->bindParam(':id', $checkPoint[$i]);
                $statement->execute();
                $services = $statement->fetchAll();

                foreach ($services as $service) {
                    $certDlt = "update certification_body set is_delete = 'Y' WHERE service_id = :id";
                    $statement = $db->prepare($certDlt);
                    $statement->bindParam(':id', $service['service_id']);
                    $statement->execute();

                    $consultingDlt = "update consulting set is_delete = 'Y' WHERE service_id = :id";
                    $statement = $db->prepare($consultingDlt);
                    $statement->bindParam(':id', $service['service_id']);
                    $statement->execute();
                }

                $serviceDlt = "update services set is_delete='Y' WHERE customer_id = :id";
                $statement = $db->prepare($serviceDlt);
                $statement->bindParam(':id', $checkPoint[$i]);
                $statement->execute();

                $customerDlt = "update customer_details set is_delete='Y' WHERE customer_id = :id";
                $statement = $db->prepare($customerDlt);
                $statement->bindParam(':id', $checkPoint[$i]);
                $statement->execute();
            }
        }
        header("Location: " . $_POST["page"]);
    } catch (Exception $ex) {
        
    }
} else if (isset($_POST['customerDlt']) && !empty($_POST['customerDlt'])) {
    try {
        $dltCustomer = "DELETE FROM customer_details WHERE customer_id = :id";
        $statement = $db->prepare($dltCustomer);
        $statement->bindParam(':id', $_POST['customerDlt']);
        $statement->execute();
        header("Location: recycleCustomers.php");
    } catch (Exception $ex) {
        
    }
}