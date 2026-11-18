<?php

session_start();
include_once './dbConnect.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (isset($_GET['serviceId']) && !empty($_GET['serviceId'])) {
    try {
        $db->beginTransaction();
        $existingService = $db->prepare('SELECT service_id FROM services WHERE service_id = :id AND customer_id = :customer');
        $existingService->execute(array(':id' => $_GET['serviceId'], ':customer' => $_GET['customer_id']));
        if (!$existingService->fetch()) {
            throw new RuntimeException('Service not found for this customer');
        }
//        cretification details
        $cbrowid = explode('~', $_GET['cbRows']);
        $cbContractDetails = explode('~', $_GET['cbContractDetails']);
        $cbAmountDecided = array_map(function ($value) { return $value === '' ? 0 : $value; }, explode('~', $_GET['cbDecidedAmount']));
        $cbPaymentReceived = array_map(function ($value) { return $value === '' ? 0 : $value; }, explode('~', $_GET['cbPaymentReceived']));
        $cbDepositDate = explode('~', $_GET['cbDepositDate']);
        $cbChequeDetails = explode('~', $_GET['cbChequeDetails']);
        $cbBankAcccountNo = explode('~', $_GET['cbBankAcccountNo']);
        $cbBalance = explode('~', $_GET['cbBalance']);
//        consulting details
        $consultingrowid = explode('~', $_GET['consultingrows']);
        $consultingDetails = explode('~', $_GET['consultingDetails']);
        $consultingAmount = array_map(function ($value) { return $value === '' ? 0 : $value; }, explode('~', $_GET['consultingAmount']));
        $consultingPayment = array_map(function ($value) { return $value === '' ? 0 : $value; }, explode('~', $_GET['consultingPayment']));
        $consultingDepositDate = explode('~', $_GET['consultingDepositDate']);
        $consultingChequeDetails = explode('~', $_GET['consultingChequeDetails']);
        $consultingBankAcccountNo = explode('~', $_GET['consultingBankAcccountNo']);
        $consultingBalance = explode('~', $_GET['consultingBalance']);

        $auditDate = NULL;
        if (isset($_GET['auditDate']) && !empty($_GET['auditDate'])) {
            $auditDate = date("Y-m-d", strtotime($_GET['auditDate']));
            $nextservicedate = date("Y-m-d", strtotime($auditDate . '+1 year'));
            if ($_GET['servicetype'] == "RC" || $_GET['servicetype'] == "CA") {
                $nextservicedate = date("Y-m-d", strtotime($auditDate . '+1 year'));
//                $updateCustomer = "UPDATE customer_details SET certification_date = '" . $auditDate . "', next_service_date = '" . $nextservicedate . "', next_service_type='SA-1' WHERE customer_id = '" . $_GET['customer_id'] . "'";
                $updateCustomer = "UPDATE customer_details SET certification_date = '" . $auditDate . "' WHERE customer_id = '" . $_GET['customer_id'] . "'";
                $statement = $db->prepare($updateCustomer);
                $statement->execute();
            }
        }
        $serviceUpdate = "UPDATE services SET service_type = :servicetype , cert_body =:certbody , consulting_firm = :consultingfirm , consultant = :consultants , audit_type = :audittype , auditor = :auditor , audit_date = :auditdate, handel_by = :handel, add_service_to = :addTo WHERE service_id = :service_id";
        $addserviceto = $_GET['addTo'] . '-' . $_GET['servicetype'];
        $statement = $db->prepare($serviceUpdate);
        $statement->bindParam(':servicetype', $_GET['servicetype']);
        $statement->bindParam(':certbody', $_GET['certbody']);
        $statement->bindParam(':consultingfirm', $_GET['consultingfirm']);
        $statement->bindParam(':consultants', $_GET['consultantlist']);
        $statement->bindParam(':audittype', $_GET['audittype']);
        $statement->bindParam(':auditor', $_GET['auditorlist']);
        $statement->bindParam(':auditdate', $auditDate);
        $statement->bindParam(':service_id', $_GET['serviceId']);
        $statement->bindParam(':addTo', $_GET['addTo']);
        $statement->bindParam(':handel', $_GET['handelby']);
        $statement->execute();

        $cbDeleteRows = explode('~', $_GET['cbDeleteRows']);
        $consultingDeleteRows = explode('~', $_GET['consultingDeleteRows']);

        for ($i = 0; $i < count($cbContractDetails); ++$i) {

            if ($cbContractDetails[$i] != '') {

                if (array_key_exists($i, $cbrowid) && $cbrowid[$i] != "" && $cbrowid != null) {
                    if (in_array($cbrowid[$i], $cbDeleteRows)) {
                        $updateDB = "Delete from certification_body WHERE service_id = :serviceId and certification_id = :cbId";
                        $statement = $db->prepare($updateDB);
                        $statement->bindParam(':serviceId', $_GET['serviceId']);
                        $statement->bindParam(':cbId', $cbrowid[$i]);
                        $statement->execute();
                    } else {
                        if (count($cbDepositDate) < ($i + 1) || $cbDepositDate[$i] == "")
                            $date = NULL;
                        else
                            $date = date("Y-m-d", strtotime($cbDepositDate[$i]));
                        $cbBalance = $cbAmountDecided[$i] - $cbPaymentReceived[$i];
                        $updateDB = "UPDATE certification_body SET contract_details = :contractDetails , amount_decided = :decidedAmount , payment_received = :paymentReceived, deposit_date = :depositDate, check_details = :checkDetails, bank_account = :bankAccount, balance = :balance WHERE service_id = :serviceId and certification_id = :cbId";
                        $statement = $db->prepare($updateDB);
                        $statement->bindParam(':serviceId', $_GET['serviceId']);
                        $statement->bindParam(':cbId', $cbrowid[$i]);
                        $statement->bindParam(':contractDetails', $cbContractDetails[$i]);
                        $statement->bindParam(':decidedAmount', $cbAmountDecided[$i]);
                        $statement->bindParam(':paymentReceived', $cbPaymentReceived[$i]);
                        $statement->bindParam(':depositDate', $date);
                        $statement->bindParam(':checkDetails', $cbChequeDetails[$i]);
                        $statement->bindParam(':bankAccount', $cbBankAcccountNo[$i]);
                        $statement->bindParam(':balance', $cbBalance);
                        $statement->execute();
                    }
                } else {
                    if (count($cbDepositDate) < ($i + 1) || $cbDepositDate[$i] == "")
                        $date = NULL;
                    else
                        $date = date("Y-m-d", strtotime($cbDepositDate[$i]));
                    $cbBalance = $cbAmountDecided[$i] - $cbPaymentReceived[$i];
                    $insertCertBody = "INSERT INTO certification_body (service_id, contract_details, amount_decided, payment_received, deposit_date, check_details, bank_account, balance) VALUES (:serviceId, :contractDetails, :decidedAmount, :paymentReceived, :depositDate, :checkDetails, :bankAccount, :balance)";
                    $statement = $db->prepare($insertCertBody);
                    $statement->bindParam(':serviceId', $_GET['serviceId']);
                    $statement->bindParam(':contractDetails', $cbContractDetails[$i]);
                    $statement->bindParam(':decidedAmount', $cbAmountDecided[$i]);
                    $statement->bindParam(':paymentReceived', $cbPaymentReceived[$i]);
                    $statement->bindParam(':depositDate', $date);
                    $statement->bindParam(':checkDetails', $cbChequeDetails[$i]);
                    $statement->bindParam(':bankAccount', $cbBankAcccountNo[$i]);
                    $statement->bindParam(':balance', $cbBalance);
                    $statement->execute();
                }
            }
        }

        for ($i = 0; $i < count($consultingDetails); ++$i) {
            if ($consultingDetails[$i] != '') {
                if (array_key_exists($i, $consultingrowid) && $consultingrowid[$i] != "" && $consultingrowid != null) {

                    //do delete functionality in case consulting id is set but not contract details are empty
                    if (in_array($consultingrowid[$i], $consultingDeleteRows)) {
                        $updateDB = "delete from consulting WHERE service_id = :serviceId and consulting_id = :consultingId";
                        $statement = $db->prepare($updateDB);
                        $statement->bindParam(':serviceId', $_GET['serviceId']);
                        $statement->bindParam(':consultingId', $consultingrowid[$i]);
                        $statement->execute();
                    } else {

                        if (count($consultingDepositDate) < ($i + 1) || $consultingDepositDate[$i] == "")
                            $date = NULL;
                        else
                            $date = date("Y-m-d", strtotime($consultingDepositDate[$i]));
                        $consultingBalance = $consultingAmount[$i] - $consultingPayment[$i];
                        $updateConsulting = "UPDATE consulting SET consulting_fees = :contractDetails, consulting_amount_decided = :decidedAmount, consulting_payment_received = :paymentReceived, consulting_deposit_date = :depositDate, consulting_chque_details = :checkDetails, bank_ac = :bankAccount,consulting_balance = :balance WHERE service_id = :serviceId and consulting_id = :consultingId";
                        $statement = $db->prepare($updateConsulting);
                        $statement->bindParam(':serviceId', $_GET['serviceId']);
                        $statement->bindParam(':consultingId', $consultingrowid[$i]);
                        $statement->bindParam(':contractDetails', $consultingDetails[$i]);
                        $statement->bindParam(':decidedAmount', $consultingAmount[$i]);
                        $statement->bindParam(':paymentReceived', $consultingPayment[$i]);
                        $statement->bindParam(':depositDate', $date);
                        $statement->bindParam(':checkDetails', $consultingChequeDetails[$i]);
                        $statement->bindParam(':bankAccount', $consultingBankAcccountNo[$i]);
                        $statement->bindParam(':balance', $consultingBalance);
                        $statement->execute();
                    }
                } else {
                    if (count($consultingDepositDate) < ($i + 1) || $consultingDepositDate[$i] == "")
                        $date = NULL;
                    else
                        $date = date("Y-m-d", strtotime($consultingDepositDate[$i]));

                    $consultingBalance = $consultingAmount[$i] - $consultingPayment[$i];
                    $insertConsulting = "INSERT INTO consulting (service_id, consulting_fees, consulting_amount_decided, consulting_payment_received, consulting_deposit_date, consulting_chque_details, bank_ac, consulting_balance) VALUES (:serviceId, :contractDetails, :decidedAmount, :paymentReceived, :depositDate, :checkDetails, :bankAccount, :balance)";
                    $statement = $db->prepare($insertConsulting);
                    $statement->bindParam(':serviceId', $_GET['serviceId']);
                    $statement->bindParam(':contractDetails', $consultingDetails[$i]);
                    $statement->bindParam(':decidedAmount', $consultingAmount[$i]);
                    $statement->bindParam(':paymentReceived', $consultingPayment[$i]);
                    $statement->bindParam(':depositDate', $date);
                    $statement->bindParam(':checkDetails', $consultingChequeDetails[$i]);
                    $statement->bindParam(':bankAccount', $consultingBankAcccountNo[$i]);
                    $statement->bindParam(':balance', $consultingBalance);
                    $statement->execute();
                }
            }
        }

        if (isset($_GET['addTo']) && !empty($_GET['addTo']) && $_GET['addTo'] == "Under Working") {
//            echo "in";
            try {
                $date = date("d-M-Y");
                $orderdate = explode('-', $date);

                $day = $orderdate[0];
                $month = $orderdate[1];
                $year = $orderdate[2];

                $svcWorkCount = "SELECT COUNT(id) as id FROM `working_services` WHERE service_id = :service and month = :month and year = :year";
                $statement = $db->prepare($svcWorkCount);
                $statement->bindParam(":service", $_GET['serviceId']);
                $statement->bindParam(":year", $year);
                $statement->bindParam(":month", $month);
                $statement->execute();
                $svcCount = $statement->fetch();
//                echo $svcCount['id'];
                if ($svcCount['id'] == 0) {

                    $working = "INSERT INTO `working_services`(`customer_id`, `service_id`, `year`, `month`) VALUES (:customer,:service,:year,:month)";
                    $statement = $db->prepare($working);
                    $statement->bindParam(":customer", $_GET['customer_id']);
                    $statement->bindParam(":service", $_GET['serviceId']);
                    $statement->bindParam(":year", $year);
                    $statement->bindParam(":month", $month);
                    $statement->execute();
                }
            } catch (Exception $ex) {
                
            }
        }
//die;
        $serviceStatus = "SELECT service_type, service_id from services WHERE service_id = :service";
        $statement = $db->prepare($serviceStatus);
        $statement->bindParam(':service', $_GET['serviceId']);
        $statement->execute();
        $serviceStatus = $statement->fetch();

        $cbid = "SELECT * FROM certification_body WHERE service_id = :service ORDER BY certification_id ";
        $statement = $db->prepare($cbid);
        $statement->bindParam(':service', $_GET['serviceId']);
        $statement->execute();
        $cbIds = $statement->fetchAll();

        $consultingId = "SELECT * FROM consulting WHERE service_id = :service ORDER BY consulting_id ";
        $statement = $db->prepare($consultingId);
        $statement->bindParam(':service', $_GET['serviceId']);
        $statement->execute();
        $consultingIds = $statement->fetchAll();

        $db->commit();
        echo json_encode(array($serviceStatus, $cbIds, $consultingIds));
//        echo $serviceStatus['service_type'];
//        echo json_encode($serviceStatus);
    } catch (Throwable $ex) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('Service save failed: ' . $ex->getMessage());
        http_response_code(500);
        echo json_encode(array('error' => 'Unable to save service'));
    }
} else if (isset($_GET['servicetype']) && !empty($_GET['servicetype']) && isset($_GET['customer_id']) && !empty($_GET['customer_id'])) {

    //&& isset($_GET['certbody']) && !empty($_GET['certbody']) && isset($_GET['consultingfirm']) && !empty($_GET['consultingfirm']) && isset($_GET['consultantlist']) && !empty($_GET['consultantlist']) && isset($_GET['audittype']) && !empty($_GET['audittype']) && isset($_GET['auditorlist']) && !empty($_GET['auditorlist']) && isset($_GET['auditDate']) && !empty($_GET['auditDate']) 
    try {
        $db->beginTransaction();

        $certBody = "";
        $consultingFirm = "";
        $consultantList = "";
        $auditType = "";
        $auditorlist = "";
        $auditDate = "";
        $addServiceTo = "";
        $issueCert = "";

        if (isset($_GET['certbody']) && !empty($_GET['certbody'])) {
            $certBody = $_GET['certbody'];
        }

        if (isset($_GET['consultingfirm']) && !empty($_GET['consultingfirm'])) {
            $consultingFirm = $_GET['consultingfirm'];
        }

        if (isset($_GET['consultantlist']) && !empty($_GET['consultantlist'])) {
            $consultantList = $_GET['consultantlist'];
        }

        if (isset($_GET['audittype']) && !empty($_GET['audittype'])) {
            $auditType = $_GET['audittype'];
        }

        if (isset($_GET['auditorlist']) && !empty($_GET['auditorlist'])) {
            $auditorlist = $_GET['auditorlist'];
        }

        if (isset($_GET['auditDate']) && !empty($_GET['auditDate'])) {
            $auditDate = date("Y-m-d", strtotime($_GET['auditDate']));
        } else {
            $auditDate = NULL;
        }

        if (isset($_GET['addTo']) && !empty($_GET['addTo'])) {
            $addServiceTo = $_GET['addTo'];
        }

        if (isset($_GET['certIssue']) && !empty($_GET['certIssue']) && $_GET['certIssue'] == "YES" && ($_GET['servicetype'] == "CA" || $_GET['servicetype'] == "SA-1" || $_GET['servicetype'] == "SA-2" || $_GET['servicetype'] == "RC")) {
            $issueCert = 'YES';
        }
//        cretification details
        $cbContractDetails = explode('~', $_GET['cbContractDetails']);
        $cbAmountDecided = array_map(function ($value) { return $value === '' ? 0 : $value; }, explode('~', $_GET['cbDecidedAmount']));
        $cbPaymentReceived = array_map(function ($value) { return $value === '' ? 0 : $value; }, explode('~', $_GET['cbPaymentReceived']));
        $cbDepositDate = explode('~', $_GET['cbDepositDate']);
        $cbChequeDetails = explode('~', $_GET['cbChequeDetails']);
        $cbBankAcccountNo = explode('~', $_GET['cbBankAcccountNo']);
        $cbBalance = explode('~', $_GET['cbBalance']);

//        consulting details
        $consultingDetails = explode('~', $_GET['consultingDetails']);
        $consultingAmount = array_map(function ($value) { return $value === '' ? 0 : $value; }, explode('~', $_GET['consultingAmount']));
        $consultingPayment = array_map(function ($value) { return $value === '' ? 0 : $value; }, explode('~', $_GET['consultingPayment']));
        $consultingDepositDate = explode('~', $_GET['consultingDepositDate']);
        $consultingChequeDetails = explode('~', $_GET['consultingChequeDetails']);
        $consultingBankAcccountNo = explode('~', $_GET['consultingBankAcccountNo']);
        $consultingBalance = explode('~', $_GET['consultingBalance']);


        $addService = "INSERT INTO services (customer_id, service_type, cert_body, consulting_firm, consultant, audit_type, auditor, audit_date, handel_by, add_service_to, is_certified) VALUES (:customerid,:servicetype,:certbody,:consultingfirm,:consultants,:audittype,:auditor,:auditdate,:handel,:addTo,:certIssue)";
        $statement = $db->prepare($addService);
        $statement->bindParam(':customerid', $_GET['customer_id']);
        $statement->bindParam(':servicetype', $_GET['servicetype']);
        $statement->bindParam(':certbody', $certBody);
        $statement->bindParam(':consultingfirm', $consultingFirm);
        $statement->bindParam(':consultants', $consultantList);
        $statement->bindParam(':audittype', $auditType);
        $statement->bindParam(':auditor', $auditorlist);
        $statement->bindParam(':auditdate', $auditDate);
        $statement->bindParam(':addTo', $addServiceTo);
        $statement->bindParam(':certIssue', $issueCert);
        $statement->bindParam(':handel', $_GET['handelby']);
        $statement->execute();

        $serviceid = $db->lastInsertId();
//  
        if ($serviceid) {
            if ($cbContractDetails != "") {
                for ($i = 0; $i < count($cbContractDetails); ++$i) {
                    if ($cbContractDetails[$i] != "") {
                        if ($cbDepositDate[$i] != "") {
                            $cbDate = date("Y-m-d", strtotime($cbDepositDate[$i]));
                        } else {
                            $cbDate = NULL;
                        }
                        $cbBalance = $cbAmountDecided[$i] - $cbPaymentReceived[$i];
                        $insertCertBody = "INSERT INTO certification_body (service_id, contract_details, amount_decided, payment_received, deposit_date, check_details, bank_account, balance) VALUES (:serviceId, :contractDetails, :decidedAmount, :paymentReceived, :depositDate, :checkDetails, :bankAccount, :balance)";
                        $statement = $db->prepare($insertCertBody);
                        $statement->bindParam(':serviceId', $serviceid);
                        $statement->bindParam(':contractDetails', $cbContractDetails[$i]);
                        $statement->bindParam(':decidedAmount', $cbAmountDecided[$i]);
                        $statement->bindParam(':paymentReceived', $cbPaymentReceived[$i]);
                        $statement->bindParam(':depositDate', $cbDate);
                        $statement->bindParam(':checkDetails', $cbChequeDetails[$i]);
                        $statement->bindParam(':bankAccount', $cbBankAcccountNo[$i]);
                        $statement->bindParam(':balance', $cbBalance);
                        $statement->execute();
                    }
                }
            }
            if ($consultingDetails != "") {
                for ($i = 0; $i < count($consultingDetails); ++$i) {
                    if ($consultingDetails[$i] != "") {
                        if ($consultingDepositDate[$i] != "") {
                            $consultingDate = date("Y-m-d", strtotime($consultingDepositDate[$i]));
                        } else {
                            $consultingDate = NULL;
                        }
                        $consultingBalance = $consultingAmount[$i] - $consultingPayment[$i];
                        $insertConsulting = "INSERT INTO consulting (service_id, consulting_fees, consulting_amount_decided, consulting_payment_received, consulting_deposit_date, consulting_chque_details, bank_ac, consulting_balance) VALUES (:serviceId, :contractDetails, :decidedAmount, :paymentReceived, :depositDate, :checkDetails, :bankAccount, :balance)";
                        $statement = $db->prepare($insertConsulting);
                        $statement->bindParam(':serviceId', $serviceid);
                        $statement->bindParam(':contractDetails', $consultingDetails[$i]);
                        $statement->bindParam(':decidedAmount', $consultingAmount[$i]);
                        $statement->bindParam(':paymentReceived', $consultingPayment[$i]);
                        $statement->bindParam(':depositDate', $consultingDate);
                        $statement->bindParam(':checkDetails', $consultingChequeDetails[$i]);
                        $statement->bindParam(':bankAccount', $consultingBankAcccountNo[$i]);
                        $statement->bindParam(':balance', $consultingBalance);
                        $statement->execute();
                    }
                }
            }
        }

        $customerDetail = "SELECT certification_date, next_service_date, next_service_type FROM customer_details WHERE customer_id = :id";
        $statement = $db->prepare($customerDetail);
        $statement->bindParam(':id', $_GET['customer_id']);
        $statement->execute();
        $customer = $statement->fetch();

        $service_type = "SELECT service_type FROM services WHERE service_id = :id";
        $statement = $db->prepare($service_type);
        $statement->bindParam(':id', $serviceid);
        $statement->execute();
        $type = $statement->fetch();
//        $insertDate = "";
        if ($issueCert == "YES" && ($_GET['servicetype'] == "CA" || $_GET['servicetype'] == "SA-1" || $_GET['servicetype'] == "SA-2" || $_GET['servicetype'] == "RC")) {
            if ($type['service_type'] == "CA") {
                $service = "SA-1";
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 1 year'));
//                echo "In CA-> ".$insertDate;
            } else if ($type['service_type'] == "SA-1") {
                $service = "SA-2";
//                $serviceType = "SELECT count(*) as count FROM services WHERE customer_id = :id and service_type = :serviceType";
//                $statement = $db->prepare($serviceType);
//                $statement->bindParam(':id', $_GET['customer_id']);
//                $statement->bindParam(':serviceType', $type['service_type']);
//                $statement->execute();
//                $service_count = $statement->fetch();
////                echo $service_count['count'];
//                $num = (int) $service_count['count'];
//                $num = 2 + (3 * ($num - 1));
//                echo $num;
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 2 year'));
            } else if ($type['service_type'] == "SA-2") {
                $service = "RC";
//                $serviceType = "SELECT count(*) as count FROM services WHERE customer_id = :id and service_type = :serviceType";
//                $statement = $db->prepare($serviceType);
//                $statement->bindParam(':id', $_GET['customer_id']);
//                $statement->bindParam(':serviceType', $type['service_type']);
//                $statement->execute();
//                $service_count = $statement->fetch();
////                echo $service_count['count'];
//                $num = (int) $service_count['count'];
//                $num = 3 + (3 * ($num - 1));
//                echo $num;
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 3 year'));
            } else if ($type['service_type'] == "RC") {
                $service = "SA-1";
//                $serviceType = "SELECT count(*) as count FROM services WHERE customer_id = :id and service_type = :serviceType";
//                $statement = $db->prepare($serviceType);
//                $statement->bindParam(':id', $_GET['customer_id']);
//                $statement->bindParam(':serviceType', $type['service_type']);
//                $statement->execute();
//                $service_count = $statement->fetch();
////                echo $service_count['count'];
//                $num = (int) $service_count['count'];
//                $num = 4 + (3 * ($num - 1));
//                echo $num;
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 4 year'));
            }

            $updateCustomer = "UPDATE customer_details SET next_service_date = '" . $insertDate . "', next_service_type = '" . $service . "'  WHERE customer_id = '" . $_GET['customer_id'] . "'";
            $statement = $db->prepare($updateCustomer);
            $statement->execute();
            
           
        }

        $serviceStatus = "SELECT service_type,service_id from services WHERE service_id = :service";
        $statement = $db->prepare($serviceStatus);
        $statement->bindParam(':service', $serviceid);
        $statement->execute();
        $serviceStatus = $statement->fetch();

        $cbid = "SELECT * FROM certification_body WHERE service_id = :service ORDER BY certification_id ";
        $statement = $db->prepare($cbid);
        $statement->bindParam(':service', $serviceid);
        $statement->execute();
        $cbIds = $statement->fetchAll();

        $consultingId = "SELECT * FROM consulting WHERE service_id = :service ORDER BY consulting_id ";
        $statement = $db->prepare($consultingId);
        $statement->bindParam(':service', $serviceid);
        $statement->execute();
        $consultingIds = $statement->fetchAll();

        
        $db->commit();
        echo json_encode(array($serviceStatus, $cbIds, $consultingIds));
//        echo $serviceStatus['service_type'];
//        echo json_encode($serviceStatus);
    } catch (Throwable $ex) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('Service save failed: ' . $ex->getMessage());
        http_response_code(500);
        echo json_encode(array('error' => 'Unable to save service'));
    }
}
