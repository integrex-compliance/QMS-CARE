<?php

session_start();
include_once './dbConnect.php';
require_once __DIR__ . '/payment_schema.php';
header('Content-Type: application/json');

try {
    ensurePaymentSchema($db);
    $paymentHasFlow = paymentHasServiceFlow($db);
} catch (Throwable $ex) {
    error_log('Payment schema upgrade failed: ' . $ex->getMessage());
    http_response_code(500);
    echo json_encode(['error' => paymentSchemaFailureMessage($ex)]);
    return;
}

// Quick Ajax actions for invoice linking, unlinking, and service notes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
    header('Content-Type: application/json');
    if ($_POST['action'] === 'link_invoice') {
        $invId = (int)$_POST['invoice_id'];
        $svcId = (int)$_POST['service_id'];
        $custId = (int)$_POST['customer_id'];
        $stmt = $db->prepare("UPDATE invoice_details SET service_id = :svc WHERE invoice_id = :inv AND customer_id = :cust");
        $stmt->execute([':svc' => $svcId, ':inv' => $invId, ':cust' => $custId]);
        echo json_encode(['status' => 'success', 'message' => 'Invoice linked successfully']);
        exit;
    }
    if ($_POST['action'] === 'unlink_invoice') {
        $invId = (int)$_POST['invoice_id'];
        $custId = (int)$_POST['customer_id'];
        $stmt = $db->prepare("UPDATE invoice_details SET service_id = NULL WHERE invoice_id = :inv AND customer_id = :cust");
        $stmt->execute([':inv' => $invId, ':cust' => $custId]);
        echo json_encode(['status' => 'success', 'message' => 'Invoice unlinked successfully']);
        exit;
    }
    if ($_POST['action'] === 'save_service_notes') {
        $svcId = (int)$_POST['service_id'];
        $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
        $stmt = $db->prepare("UPDATE services SET service_notes = :notes WHERE service_id = :svc");
        $stmt->execute([':notes' => $notes, ':svc' => $svcId]);
        echo json_encode(['status' => 'success', 'message' => 'Notes saved']);
        exit;
    }
    } catch (Throwable $ex) {
        error_log('Payment action failed: ' . $ex->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Unable to save payment changes. Please retry; if this continues, ask the administrator to check the server error log.']);
        return;
    }
}

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
        $consultingTds = isset($_GET['consultingTds']) ? explode('~', $_GET['consultingTds']) : [];
        $consultingShortSettle = isset($_GET['consultingShortSettle']) ? explode('~', $_GET['consultingShortSettle']) : [];
        $consultingBadDebt = isset($_GET['consultingBadDebt']) ? explode('~', $_GET['consultingBadDebt']) : [];

        $auditDate = NULL;
        if (isset($_GET['auditDate']) && !empty($_GET['auditDate'])) {
            $auditDate = date("Y-m-d", strtotime($_GET['auditDate']));
            $nextservicedate = date("Y-m-d", strtotime($auditDate . '+1 year'));
            if ($_GET['servicetype'] == "RC" || $_GET['servicetype'] == "CA") {
                $nextservicedate = date("Y-m-d", strtotime($auditDate . '+1 year'));
                $updateCustomer = "UPDATE customer_details SET certification_date = '" . $auditDate . "' WHERE customer_id = '" . $_GET['customer_id'] . "'";
                $statement = $db->prepare($updateCustomer);
                $statement->execute();
            }
        }
        $stageValue = $paymentHasFlow ? 'CASE WHEN EXISTS (SELECT 1 FROM customer_service_flow f WHERE f.service_id = services.service_id AND f.customer_id = services.customer_id) THEN service_type ELSE :servicetype END' : ':servicetype';
        $serviceUpdate = "UPDATE services SET service_type = $stageValue , cert_body =:certbody , consulting_firm = :consultingfirm , consultant = :consultants , audit_type = :audittype , auditor = :auditor , audit_date = :auditdate, handel_by = :handel, add_service_to = :addTo WHERE service_id = :service_id";
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

        if (isset($_GET['serviceNotes'])) {
            $notesStmt = $db->prepare("UPDATE services SET service_notes = :notes WHERE service_id = :svc");
            $notesStmt->execute([':notes' => $_GET['serviceNotes'], ':svc' => $_GET['serviceId']]);
        }

        $cbDeleteRows = explode('~', $_GET['cbDeleteRows']);
        $consultingDeleteRows = explode('~', $_GET['consultingDeleteRows']);

        for ($i = 0; $i < count($cbContractDetails); ++$i) {
            if ($cbContractDetails[$i] != '') {
                if (array_key_exists($i, $cbrowid) && $cbrowid[$i] != "" && $cbrowid != null) {
                    if (in_array($cbrowid[$i], $cbDeleteRows)) {
                        $updateDB = "delete from certification_body WHERE service_id = :serviceId and certification_id = :certificationId";
                        $statement = $db->prepare($updateDB);
                        $statement->bindParam(':serviceId', $_GET['serviceId']);
                        $statement->bindParam(':certificationId', $cbrowid[$i]);
                        $statement->execute();
                    } else {
                        if (count($cbDepositDate) < ($i + 1) || $cbDepositDate[$i] == "")
                            $date = NULL;
                        else
                            $date = date("Y-m-d", strtotime($cbDepositDate[$i]));

                        $cbBalance = $cbAmountDecided[$i] - $cbPaymentReceived[$i];
                        $updateCertBody = "UPDATE certification_body SET contract_details = :contractDetails, amount_decided = :decidedAmount, payment_received = :paymentReceived, deposit_date = :depositDate, check_details = :checkDetails, bank_account = :bankAccount, balance = :balance WHERE service_id = :serviceId and certification_id = :certificationId";
                        $statement = $db->prepare($updateCertBody);
                        $statement->bindParam(':serviceId', $_GET['serviceId']);
                        $statement->bindParam(':certificationId', $cbrowid[$i]);
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
            $cDetailVal = trim($consultingDetails[$i]);
            $tdsVal = isset($consultingTds[$i]) && $consultingTds[$i] !== '' ? $consultingTds[$i] : '0';
            $shortVal = isset($consultingShortSettle[$i]) && $consultingShortSettle[$i] !== '' ? $consultingShortSettle[$i] : '0';
            $badVal = isset($consultingBadDebt[$i]) && $consultingBadDebt[$i] !== '' ? $consultingBadDebt[$i] : '0';

            if ($cDetailVal != '' || (isset($consultingPayment[$i]) && $consultingPayment[$i] > 0) || (float)$tdsVal > 0 || (float)$shortVal > 0 || (float)$badVal > 0) {
                if ($cDetailVal == '') {
                    $cDetailVal = 'Payment';
                }
                if (array_key_exists($i, $consultingrowid) && $consultingrowid[$i] != "" && $consultingrowid != null) {
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
                        $updateConsulting = "UPDATE consulting SET consulting_fees = :contractDetails, consulting_amount_decided = :decidedAmount, consulting_payment_received = :paymentReceived, tds_deducted = :tdsDeducted, short_settle = :shortSettle, bad_debt = :badDebt, consulting_deposit_date = :depositDate, consulting_chque_details = :checkDetails, bank_ac = :bankAccount, consulting_balance = :balance WHERE service_id = :serviceId and consulting_id = :consultingId";
                        $statement = $db->prepare($updateConsulting);
                        $statement->bindParam(':serviceId', $_GET['serviceId']);
                        $statement->bindParam(':consultingId', $consultingrowid[$i]);
                        $statement->bindParam(':contractDetails', $cDetailVal);
                        $statement->bindParam(':decidedAmount', $consultingAmount[$i]);
                        $statement->bindParam(':paymentReceived', $consultingPayment[$i]);
                        $statement->bindParam(':tdsDeducted', $tdsVal);
                        $statement->bindParam(':shortSettle', $shortVal);
                        $statement->bindParam(':badDebt', $badVal);
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
                    $insertConsulting = "INSERT INTO consulting (service_id, consulting_fees, consulting_amount_decided, consulting_payment_received, tds_deducted, short_settle, bad_debt, consulting_deposit_date, consulting_chque_details, bank_ac, consulting_balance) VALUES (:serviceId, :contractDetails, :decidedAmount, :paymentReceived, :tdsDeducted, :shortSettle, :badDebt, :depositDate, :checkDetails, :bankAccount, :balance)";
                    $statement = $db->prepare($insertConsulting);
                    $statement->bindParam(':serviceId', $_GET['serviceId']);
                    $statement->bindParam(':contractDetails', $cDetailVal);
                    $statement->bindParam(':decidedAmount', $consultingAmount[$i]);
                    $statement->bindParam(':paymentReceived', $consultingPayment[$i]);
                    $statement->bindParam(':tdsDeducted', $tdsVal);
                    $statement->bindParam(':shortSettle', $shortVal);
                    $statement->bindParam(':badDebt', $badVal);
                    $statement->bindParam(':depositDate', $date);
                    $statement->bindParam(':checkDetails', $consultingChequeDetails[$i]);
                    $statement->bindParam(':bankAccount', $consultingBankAcccountNo[$i]);
                    $statement->bindParam(':balance', $consultingBalance);
                    $statement->execute();
                }
            }
        }

        if (isset($_GET['addTo']) && !empty($_GET['addTo']) && $_GET['addTo'] == "Under Working") {
            try {
                $date = date("d-M-Y");
                $orderdate = explode('-', $date);
                $year = $orderdate[2];
                $month = $orderdate[1];
                $day = $orderdate[0];

                $order_date = $year . "-" . $month . "-" . $day;
                $updateCustomer = "UPDATE customer_details SET order_date = '" . $order_date . "' WHERE customer_id = '" . $_GET['customer_id'] . "'";
                $statement = $db->prepare($updateCustomer);
                $statement->execute();
            } catch (Exception $ex) {
                
            }
        }

        $service_type = "SELECT service_type FROM services WHERE service_id = :id";
        $statement = $db->prepare($service_type);
        $statement->bindParam(':id', $_GET['serviceId']);
        $statement->execute();
        $type = $statement->fetch();

        $customerDetail = "SELECT certification_date, next_service_date, next_service_type FROM customer_details WHERE customer_id = :id";
        $statement = $db->prepare($customerDetail);
        $statement->bindParam(':id', $_GET['customer_id']);
        $statement->execute();
        $customer = $statement->fetch();

        if (isset($_GET['certIssue']) && !empty($_GET['certIssue']) && $_GET['certIssue'] == "YES" && ($_GET['servicetype'] == "CA" || $_GET['servicetype'] == "SA-1" || $_GET['servicetype'] == "SA-2" || $_GET['servicetype'] == "RC")) {
            if ($type['service_type'] == "CA") {
                $service = "SA-1";
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 1 year'));
            } else if ($type['service_type'] == "SA-1") {
                $service = "SA-2";
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 2 year'));
            } else if ($type['service_type'] == "SA-2") {
                $service = "RC";
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 3 year'));
            } else if ($type['service_type'] == "RC") {
                $service = "SA-1";
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 4 year'));
            }

            $updateCustomer = "UPDATE customer_details SET next_service_date = '" . $insertDate . "', next_service_type = '" . $service . "'  WHERE customer_id = '" . $_GET['customer_id'] . "'";
            $statement = $db->prepare($updateCustomer);
            $statement->execute();
        }

        $serviceStatus = "SELECT service_type,service_id from services WHERE service_id = :service";
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
    } catch (Throwable $ex) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('Service update failed: ' . $ex->getMessage());
        http_response_code(500);
        echo json_encode(array('error' => 'Unable to save service'));
    }
} else if (isset($_GET['servicetype']) && !empty($_GET['servicetype'])) {
    try {
        $db->beginTransaction();
        $certBody = "";
        if (isset($_GET['certbody']) && !empty($_GET['certbody'])) {
            $certBody = $_GET['certbody'];
        }
        $consultingFirm = "";
        if (isset($_GET['consultingfirm']) && !empty($_GET['consultingfirm'])) {
            $consultingFirm = $_GET['consultingfirm'];
        }
        $consultantList = "";
        if (isset($_GET['consultantlist']) && !empty($_GET['consultantlist'])) {
            $consultantList = $_GET['consultantlist'];
        }
        $auditType = "";
        if (isset($_GET['audittype']) && !empty($_GET['audittype'])) {
            $auditType = $_GET['audittype'];
        }
        $auditorlist = "";
        if (isset($_GET['auditorlist']) && !empty($_GET['auditorlist'])) {
            $auditorlist = $_GET['auditorlist'];
        }
        $auditDate = NULL;
        if (isset($_GET['auditDate']) && !empty($_GET['auditDate'])) {
            $auditDate = date("Y-m-d", strtotime($_GET['auditDate']));
            $nextservicedate = date("Y-m-d", strtotime($auditDate . '+1 year'));
            if ($_GET['servicetype'] == "RC" || $_GET['servicetype'] == "CA") {
                $nextservicedate = date("Y-m-d", strtotime($auditDate . '+1 year'));
                $updateCustomer = "UPDATE customer_details SET certification_date = '" . $auditDate . "' WHERE customer_id = '" . $_GET['customer_id'] . "'";
                $statement = $db->prepare($updateCustomer);
                $statement->execute();
            }
        }
        $addServiceTo = "";
        if (isset($_GET['addTo']) && !empty($_GET['addTo'])) {
            $addServiceTo = $_GET['addTo'];
        }

        $issueCert = "NO";
        if (isset($_GET['certIssue']) && !empty($_GET['certIssue'])) {
            $issueCert = $_GET['certIssue'];
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
        $consultingTds = isset($_GET['consultingTds']) ? explode('~', $_GET['consultingTds']) : [];
        $consultingShortSettle = isset($_GET['consultingShortSettle']) ? explode('~', $_GET['consultingShortSettle']) : [];
        $consultingBadDebt = isset($_GET['consultingBadDebt']) ? explode('~', $_GET['consultingBadDebt']) : [];

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

        if (isset($_GET['serviceNotes']) && $serviceid) {
            $notesStmt = $db->prepare("UPDATE services SET service_notes = :notes WHERE service_id = :svc");
            $notesStmt->execute([':notes' => $_GET['serviceNotes'], ':svc' => $serviceid]);
        }

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
                    $cDetailVal = trim($consultingDetails[$i]);
                    $tdsVal = isset($consultingTds[$i]) && $consultingTds[$i] !== '' ? $consultingTds[$i] : '0';
                    $shortVal = isset($consultingShortSettle[$i]) && $consultingShortSettle[$i] !== '' ? $consultingShortSettle[$i] : '0';
                    $badVal = isset($consultingBadDebt[$i]) && $consultingBadDebt[$i] !== '' ? $consultingBadDebt[$i] : '0';

                    if ($cDetailVal != '' || (isset($consultingPayment[$i]) && $consultingPayment[$i] > 0) || (float)$tdsVal > 0 || (float)$shortVal > 0 || (float)$badVal > 0) {
                        if ($cDetailVal == '') {
                            $cDetailVal = 'Payment';
                        }
                        if ($consultingDepositDate[$i] != "") {
                            $consultingDate = date("Y-m-d", strtotime($consultingDepositDate[$i]));
                        } else {
                            $consultingDate = NULL;
                        }
                        $consultingBalance = $consultingAmount[$i] - $consultingPayment[$i];
                        $insertConsulting = "INSERT INTO consulting (service_id, consulting_fees, consulting_amount_decided, consulting_payment_received, tds_deducted, short_settle, bad_debt, consulting_deposit_date, consulting_chque_details, bank_ac, consulting_balance) VALUES (:serviceId, :contractDetails, :decidedAmount, :paymentReceived, :tdsDeducted, :shortSettle, :badDebt, :depositDate, :checkDetails, :bankAccount, :balance)";
                        $statement = $db->prepare($insertConsulting);
                        $statement->bindParam(':serviceId', $serviceid);
                        $statement->bindParam(':contractDetails', $cDetailVal);
                        $statement->bindParam(':decidedAmount', $consultingAmount[$i]);
                        $statement->bindParam(':paymentReceived', $consultingPayment[$i]);
                        $statement->bindParam(':tdsDeducted', $tdsVal);
                        $statement->bindParam(':shortSettle', $shortVal);
                        $statement->bindParam(':badDebt', $badVal);
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

        if ($issueCert == "YES" && ($_GET['servicetype'] == "CA" || $_GET['servicetype'] == "SA-1" || $_GET['servicetype'] == "SA-2" || $_GET['servicetype'] == "RC")) {
            if ($type['service_type'] == "CA") {
                $service = "SA-1";
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 1 year'));
            } else if ($type['service_type'] == "SA-1") {
                $service = "SA-2";
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 2 year'));
            } else if ($type['service_type'] == "SA-2") {
                $service = "RC";
                $insertDate = date('Y-m-d ', strtotime($customer['certification_date'] . '+ 3 year'));
            } else if ($type['service_type'] == "RC") {
                $service = "SA-1";
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
    } catch (Throwable $ex) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('Service save failed: ' . $ex->getMessage());
        http_response_code(500);
        echo json_encode(array('error' => 'Unable to save service'));
    }
}
