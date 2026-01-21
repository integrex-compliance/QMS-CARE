<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include_once './dbConnect.php';

if (isset($_POST["customerid"]) && !empty($_POST["customerid"]) && isset($_POST["servicetype"]) && !empty($_POST["servicetype"]) && isset($_POST["serviceid"]) && !empty($_POST["serviceid"])) {
    try {
        $next_svc = "";
        if ($_POST["servicetype"] == "SA-1") {
            $next_svc = "SA-2";
        } else if ($_POST["servicetype"] == "SA-2") {
            $next_svc = "RC";
        } else if ($_POST["servicetype"] == "RC") {
            $next_svc = "SA-1";
        }

        $svc = "SELECT * FROM `services` WHERE service_id = :id";
        $statement = $db->prepare($svc);
        $statement->bindParam(":id", $_POST["serviceid"]);
        $statement->execute();
        $svcdetail = $statement->fetch();
        
        $serviceto = "Under Marketing";
        $addservice = "INSERT INTO `services`(`customer_id`, `service_type`, `cert_body`, `consulting_firm`, `consultant`, `add_service_to`, `handel_by`) VALUES (:customer_id, :servicetype, :cb, :cf, :consultant, :servceto, :hand_by)";
        $statement = $db->prepare($addservice);
        $statement->bindParam(":customer_id", $_POST["customerid"]);
        $statement->bindParam(":servicetype", $_POST["servicetype"]);
        $statement->bindParam(":cb", $svcdetail["cert_body"]);
        $statement->bindParam(":cf", $svcdetail["consulting_firm"]);
        $statement->bindParam(":consultant", $svcdetail["consultant"]);
        $statement->bindParam(":consultant", $svcdetail["consultant"]);
        $statement->bindParam(":servceto", $serviceto);
        $statement->bindParam(":hand_by", $svcdetail["handel_by"]);
        $statement->execute();

//        $updatecustomer = "UPDATE `customer_details` SET `next_service_type`= :nextsvc WHERE `customer_id` = :customer_id";
//        $statement = $db->prepare($updatecustomer);
//        $statement->bindParam(":nextsvc", $next_svc);
//        $statement->bindParam(":customer_id", $_POST["customerid"]);
//        $statement->execute();

        header("Location: customerAccountDetails.php?customerid=" . $_POST["customerid"]);
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
}