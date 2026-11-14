<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include_once './dbConnect.php';
include './utility.php';

if (isset($_GET['firmid']) && !empty($_GET['firmid'])) {
    try {
        $today = date('Y-m-d');
        $date = strtotime('-1 months');
        $oneMago = date('Y-m-d', $date);

        $baseQuery = "
            SELECT 
                ind.*, 
                ins.service_desc, 
                ins.service_amount, 
                cd.gst_number AS customer_gst,
                s.service_type AS service_type_name
            FROM 
                invoice_details ind
            LEFT JOIN services s ON ind.service_id = s.service_id
            LEFT JOIN invoice_services ins ON ind.invoice_id = ins.invoice_ids
            LEFT JOIN customer_details cd ON cd.customer_id = ind.customer_id
            WHERE 
                ind.firm_id = :id 
                AND ind.is_deleted = 'No'
        ";

        if (Utitlity::checkForOfficeStaff()) {
            $baseQuery .= " AND (ind.invoice_date BETWEEN '$oneMago' AND '$today')";
        }

        $baseQuery .= " ORDER BY ind.invoice_date DESC";

        $statement = $db->prepare($baseQuery);
        $statement->bindParam(':id', $_GET['firmid']);
        $statement->execute();
        $invoiceList = $statement->fetchAll();

        $firmDetails = "SELECT * FROM consulting_firm WHERE consulting_firm_id = :id";
        $statement = $db->prepare($firmDetails);
        $statement->bindParam(':id', $_GET['firmid']);
        $statement->execute();
        $firmInfo = $statement->fetch();

        echo json_encode(array($invoiceList, $firmInfo));

    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['firm_ID']) && !empty($_GET['firm_ID'])) {
    try {
        if ($_GET["user"] == '1') {
            $today = date('Y-m-d');
            $date = strtotime('-1 months');
            $oneMago = date('Y-m-d', $date);
            $quotation = "SELECT * FROM quotation_details WHERE firm_id = :id and (quotation_date BETWEEN '" . $oneMago . "' AND '" . $today . "') and is_deleted = 'No'";
            $statement = $db->prepare($quotation);
            $statement->bindParam(':id', $_GET['firm_ID']);
            $statement->execute();
            $quotationList = $statement->fetchAll();
        } else {
            $quotation = "SELECT * FROM quotation_details WHERE firm_id = :id and is_deleted = 'No'";
            $statement = $db->prepare($quotation);
            $statement->bindParam(':id', $_GET['firm_ID']);
            $statement->execute();
            $quotationList = $statement->fetchAll();
        }
        echo json_encode($quotationList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['invoiceId']) && !empty($_GET['invoiceId'])) {
    try {
        $invoiceDetails = "SELECT * FROM invoice_details WHERE invoice_id = :id";
        $statement = $db->prepare($invoiceDetails);
        $statement->bindParam(':id', $_GET['invoiceId']);
        $statement->execute();
        $invoiceInfo = $statement->fetch();

        $invService = "SELECT * FROM invoice_services WHERE invoice_ids = :id";
        $statement = $db->prepare($invService);
        $statement->bindParam(':id', $_GET['invoiceId']);
        $statement->execute();
        $invServiceInfo = $statement->fetchAll();

        $invoiceCustomer = "SELECT * FROM customer_details WHERE customer_id = :id";
        $statement = $db->prepare($invoiceCustomer);
        $statement->bindParam(':id', $invoiceInfo['customer_id']);
        $statement->execute();
        $customerInfo = $statement->fetch();

        $firmService = "SELECT * FROM consulting_firm_services WHERE consulting_firm_id = :id";
        $statement = $db->prepare($firmService);
        $statement->bindParam(':id', $invoiceInfo['firm_id']);
        $statement->execute();
        $serviceList = $statement->fetchAll();

        $prevInvoice = "SELECT * FROM invoice_details WHERE invoice_id = (SELECT MAX(invoice_id) FROM invoice_details WHERE invoice_id < :id and is_deleted = 'No')";
        $statement = $db->prepare($prevInvoice);
        $statement->bindParam(':id', $_GET['invoiceId']);
        $statement->execute();
        $preInvInfo = $statement->fetch();

$nextInvoice = "SELECT * FROM invoice_details WHERE invoice_id = (SELECT MAX(invoice_id) FROM invoice_details WHERE invoice_id > :id and is_deleted = 'No')";
        $statement = $db->prepare($nextInvoice);
        $statement->bindParam(':id', $_GET['invoiceId']);
        $statement->execute();
        $nextInvInfo = $statement->fetch();

        $firmDetails = "SELECT * FROM consulting_firm WHERE consulting_firm_id = :id ";
        $statement = $db->prepare($firmDetails);
        $statement->bindParam(':id', $_GET['firmId']);
        $statement->execute();
        $firmInfo = $statement->fetch();

        echo json_encode(array($invoiceInfo, $invServiceInfo, $customerInfo, $serviceList, $preInvInfo, $nextInvInfo, $firmInfo));
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_POST['invoice_id']) && !empty($_POST['invoice_id'])) {
    try {
        $deleteInvoice = "UPDATE invoice_details SET is_deleted = 'Yes' WHERE invoice_id = :id";
        $statement = $db->prepare($deleteInvoice);
        $statement->bindParam(':id', $_POST['invoice_id']);
        $statement->execute();
        header('location:invoiceList.php');
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['quotationId']) && !empty($_GET['quotationId'])) {
    try {
        $quotDetail = "SELECT * FROM quotation_details WHERE quotation_id = :id";
        $statement = $db->prepare($quotDetail);
        $statement->bindParam(':id', $_GET['quotationId']);
        $statement->execute();
        $quotInfo = $statement->fetch();

        $quotCondition = "SELECT * FROM quotation_condition WHERE quotationid = :id";
        $statement = $db->prepare($quotCondition);
        $statement->bindParam(':id', $_GET['quotationId']);
        $statement->execute();
        $quotCond = $statement->fetchAll();

        $firmServices = "SELECT * FROM consulting_firm_services WHERE consulting_firm_id = :id";
        $statement = $db->prepare($firmServices);
        $statement->bindParam(':id', $quotInfo['firm_id']);
        $statement->execute();
        $firmSvc = $statement->fetchAll();
//        
//        $customer = "SELECT * FROM customer_details WHERE company_name = :name";
//        $statement = $db->prepare($customer);
//        $statement->bindParam(':name', $_GET['costumerName']);
//        $statement->execute();
//        $customerInfo = $statement->fetch();
//        
//        $firm = "SELECT * FROM consulting_firm WHERE consulting_firm_id = :id";
//        $statement = $db->prepare($firm);
//        $statement->bindParam(':id', $_GET['firm_id']);
//        $statement->execute();
//        $firmInfo = $statement->fetch();

        echo json_encode(array($quotInfo, $quotCond, $firmSvc));
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_POST['quot_id']) && !empty($_POST['quot_id'])) {
    try {
        $deleteQuotation = "UPDATE quotation_details SET is_deleted = 'Yes' WHERE quotation_id = :id";
        $statement = $db->prepare($deleteQuotation);
        $statement->bindParam(':id', $_POST['quot_id']);
        $statement->execute();
        header('location:quotationList.php');
    } catch (Exception $ex) {
        echo $ex;
    }
}