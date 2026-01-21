<?php

session_start();
include_once './dbConnect.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (isset($_GET["dataDate"]) && !empty($_GET["dataDate"])) {
    try {

        $date = $_GET["dataDate"];
        $orderdate = explode('-', $date);

        $day = $orderdate[0];
        $month = $orderdate[1];
        $year = $orderdate[2];
        $svcWorkCount = "SELECT COUNT(id) as id FROM `working_services` WHERE service_id = " . $_GET['dataService'] . " and month = '" . $month . "' and year = " . $year;
        $statement = $db->prepare($svcWorkCount);
        $statement->execute();
        $svcCount = $statement->fetch();

        if ($svcCount['id'] == 0) {
            $working = "INSERT INTO `working_services`(`customer_id`, `service_id`, `year`, `month`,`" . $_GET["dataColumn"] . "`) VALUES (" . $_GET['dataCustomer'] . "," . $_GET['dataService'] . "," . $year . ",'" . $month . "','" . $_GET["dataVal"] . "')";

            $statement = $db->prepare($working);
//            $statement->bindParam(":customer", $_GET['dataCustomer']);
//            $statement->bindParam(":service", $_GET['serviceId']);
//            $statement->bindParam(":year", $year);
//            $statement->bindParam(":month", $month);
//            $statement->bindParam(":data", $_GET["dataVal"]);
            $statement->execute();
            echo "insert";
        } else {
//            echo $_GET["dataColumn"];
            $working = "UPDATE `working_services` SET `" . $_GET["dataColumn"] . "` = :data  WHERE `customer_id` = " . $_GET['dataCustomer'] . " AND service_id = " . $_GET['dataService'] . " and month = '" . $month . "' and year = " . $year;
//            echo $working;
            $statement = $db->prepare($working);
//            $statement->bindParam(":customer", $_GET['customer_id']);
//            $statement->bindParam(":service", $_GET['serviceId']);
//            $statement->bindParam(":year", $year);
//            $statement->bindParam(":month", $month);
            $statement->bindParam(":data", $_GET["dataVal"]);
            $statement->execute();
            echo "update";
        }

        echo $svcCount['id'];
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
} else if (isset($_GET["dataAuditor"]) && !empty($_GET["dataAuditor"])) {
    try {
        $auditor = "UPDATE `services` SET `auditor`= :dataauditor WHERE `service_id` = :dataservice";
        $statement = $db->prepare($auditor);
        $statement->bindParam(":dataauditor", $_GET["dataAuditor"]);
        $statement->bindParam(":dataservice", $_GET["dataServiceId"]);
        $statement->execute();
        echo "update";
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
} else if (isset($_GET["dataConsultant"]) && !empty($_GET["dataConsultant"])) {
    try {
        $auditor = "UPDATE `services` SET `consultant`= :dataconsultant WHERE `service_id` = :dataservice";
        $statement = $db->prepare($auditor);
        $statement->bindParam(":dataconsultant", $_GET["dataConsultant"]);
        $statement->bindParam(":dataservice", $_GET["dataService_Id"]);
        $statement->execute();
        echo "update";
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
} else if (isset($_GET["auditDateData"]) && !empty($_GET["auditDateData"]) && isset($_GET["auditServiceData"]) && !empty($_GET["auditServiceData"])) {
    try {
        $value = date("Y-m-d", strtotime($_GET['auditDateData']));
        $auditor = "UPDATE `services` SET `audit_date`= :dataauditdate WHERE `service_id` = :dataservice";
        $statement = $db->prepare($auditor);
        $statement->bindParam(":dataauditdate", $value);
        $statement->bindParam(":dataservice", $_GET["auditServiceData"]);
        $statement->execute();
        echo "update";
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
} else if(isset ($_GET["data_priority"]) && !empty ($_GET["data_priority"]) && isset ($_GET["data_serviceId"]) && !empty ($_GET["data_serviceId"])){
    try {
        $auditor = "UPDATE `services` SET `priorty`= :datapriority WHERE `service_id` = :dataservice";
        $statement = $db->prepare($auditor);
        $statement->bindParam(":datapriority", $_GET["data_priority"]);
        $statement->bindParam(":dataservice", $_GET["data_serviceId"]);
        $statement->execute();
        echo "update";
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
} else if(isset ($_GET["month"]) && !empty ($_GET["month"]) && isset ($_GET["year"]) && !empty ($_GET["year"])){
    try {
        $monthly = "SELECT ws.day01, day02, day03, day04, day05, day06, day07, day08, day09, day10, day11, day12, day13, day14, day15, day16, day17, day18, day19, day20, day21, day22, day23, day24, day25, day26, day27, day28, day29, day30, day31, cd.company_name, s.service_type, cert_body, consultant, auditor, audit_date FROM working_services ws LEFT JOIN customer_details cd ON ws.customer_id = cd.customer_id LEFT JOIN services s on ws.service_id = s.service_id WHERE month = '".$_GET["month"]."' AND year = ".$_GET["year"];
        $statement = $db->prepare($monthly);
        $statement->execute();
        $monthlyWorking = $statement->fetchAll();
        echo json_encode($monthlyWorking);
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
}