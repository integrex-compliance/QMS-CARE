<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
session_start();
include_once './dbConnect.php';
include 'utility.php';

if (isset($_GET['serviceName']) && !empty($_GET['serviceName'])) {
    try {
        $row_set = array();
        $serviceList = "select DISTINCT service from service_type where service like '" . $_GET['serviceName'] . "%'";
        $statement = $db->prepare($serviceList);
        $statement->execute();
        if ($statement->rowCount() > 0)
            $services = $statement->fetchAll();
        foreach ($services as $data) {
            $new['value'] = htmlentities(stripslashes($data['service']));
            $new['label'] = $data['service'];
            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET["customersearch"]) && !empty($_GET["customersearch"])) {
    try {
        $row_set = array();
        $customerlist = "SELECT DISTINCT customer_id, company_name FROM customer_details where company_name like '" . $_GET["customersearch"] . "%' and is_delete = 'N'";
        $statement = $db->prepare($customerlist);
        $statement->execute();
        if ($statement->rowCount() > 0)
            $customers = $statement->fetchAll();
        foreach ($customers as $data) {
            $new['value'] = htmlentities(stripslashes($data['company_name']));
            $new['label'] = $data['company_name'];
            $new['id'] = $data['customer_id'];

            array_push($row_set, $new);
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['certBody']) && !empty($_GET['certBody'])) {
    try {
        $row_set = array();
        $certList = "select DISTINCT cert  from cert_body  where cert  like '" . $_GET['certBody'] . "%'";
        $statement = $db->prepare($certList);
        $statement->execute();
        if ($statement->rowCount() > 0)
            $certs = $statement->fetchAll();
        foreach ($certs as $data) {
            $new['value'] = htmlentities(stripslashes($data['cert']));
            $new['label'] = $data['cert'];
            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['consultingFirm']) && !empty($_GET['consultingFirm'])) {
    try {
        $row_set = array();
        $firmList = "SELECT DISTINCT company_name, customer_id from customer_details  where company_name like '" . $_GET['consultingFirm'] . "%'";
        $statement = $db->prepare($firmList);
        $statement->execute();
        if ($statement->rowCount() > 0)
            $firms = $statement->fetchAll();
        foreach ($firms as $data) {
            $new['value'] = htmlentities(stripslashes($data['company_name']));
            $new['label'] = $data['company_name'];
            $new['id'] = $data['customer_id'];

            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['consultant']) && !empty($_GET['consultant'])) {
    try {
        $row_set = array();
        $consultantList = "SELECT DISTINCT consultant_name from consultant  where consultant_name like '" . $_GET['consultant'] . "%'";
        $statement = $db->prepare($consultantList);
        $statement->execute();
        if ($statement->rowCount() > 0)
            $consultans = $statement->fetchAll();
        foreach ($consultans as $data) {
            $new['value'] = htmlentities(stripslashes($data['consultant_name']));
            $new['label'] = $data['consultant_name'];
            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['auditor']) && !empty($_GET['auditor'])) {
    try {
        $row_set = array();
        $auditorList = "SELECT DISTINCT auditor_name from auditors  where auditor_name like '" . $_GET['auditor'] . "%'";
        $statement = $db->prepare($auditorList);
        $statement->execute();
        if ($statement->rowCount() > 0)
            $suditors = $statement->fetchAll();
        foreach ($suditors as $data) {
            $new['value'] = htmlentities(stripslashes($data['auditor_name']));
            $new['label'] = $data['auditor_name'];
            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['auditType']) && !empty($_GET['auditType'])) {
    try {
        $row_set = array();
        $auditTypeList = "SELECT DISTINCT audit_types from audit_type where audit_types like '" . $_GET['auditType'] . "%'";
        $statement = $db->prepare($auditTypeList);
        $statement->execute();
        if ($statement->rowCount() > 0)
            $auditTyps = $statement->fetchAll();
        foreach ($auditTyps as $data) {
            $new['value'] = htmlentities(stripslashes($data['audit_types']));
            $new['label'] = $data['audit_types'];
            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['consultingFirmId']) && !empty($_GET['consultingFirmId']) && isset($_GET['searchServiceString']) && !empty($_GET['searchServiceString'])) {
    try {
        $row_set = array();
        $firmList = "SELECT DISTINCT service_desc from consulting_firm_services  where consulting_firm_id=" . $_GET['consultingFirmId'] . " and service_desc like '" . $_GET['searchServiceString'] . "%'";
        $statement = $db->prepare($firmList);
        $statement->execute();
        if ($statement->rowCount() > 0)
            $firms = $statement->fetchAll();
        foreach ($firms as $data) {
            $new['value'] = htmlentities(stripslashes($data['service_desc']));
            $new['label'] = $data['service_desc'];
            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['auditTo']) && !empty($_GET['auditTo'])) {
    try {
        $row_set = array();
        $addTo = "SELECT add_to FROM add_to_service WHERE add_to like '" . $_GET['auditTo'] . "%'";
        $statement = $db->prepare($addTo);
        $statement->execute();
        if ($statement->rowCount() > 0)
            $addTos = $statement->fetchAll();
        foreach ($addTos as $data) {
            $new['value'] = htmlentities(stripslashes($data['add_to']));
            $new['label'] = $data['add_to'];
            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['serviceid']) && !empty($_GET['serviceid']) && isset($_GET['bankac']) && !empty($_GET['bankac'])) {
    try {
//        echo $_GET['serviceid'];
        $service = "SELECT cert_body FROM services WHERE service_id = :service";
        $statement = $db->prepare($service);
        $statement->bindParam(':service', $_GET['serviceid']);
        $statement->execute();
        $certBody = $statement->fetch();

        $csrtsId = "SELECT cert_id FROM cert_body WHERE cert = '" . $certBody['cert_body'] . "'";
        $statement = $db->prepare($csrtsId);
        $statement->execute();
        $certId = $statement->fetch();
//        echo json_encode($certBody);
        $row_set = array();
        $bank = "SELECT cert_abbr FROM cert_abbrevation WHERE cert_abbr like '" . $_GET['bankac'] . "%' and cert_id = :cert";
        $statement = $db->prepare($bank);
        $statement->bindParam(':cert', $certId['cert_id']);
        $statement->execute();

        if ($statement->rowCount() > 0)
            $bankAbbv = $statement->fetchAll();
        foreach ($bankAbbv as $data) {
            $new['value'] = htmlentities(stripslashes($data['cert_abbr']));
            $new['label'] = $data['cert_abbr'];
            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['consultbankac']) && !empty($_GET['consultbankac']) && isset($_GET['serviceId']) && !empty($_GET['serviceId'])) {
    try {
        $service = "SELECT consulting_firm FROM services WHERE service_id = :service";
        $statement = $db->prepare($service);
        $statement->bindParam(':service', $_GET['serviceId']);
        $statement->execute();
        $consultingFirm = $statement->fetch();

        $firmsId = "SELECT consulting_firm_id FROM consulting_firm WHERE firm_name = '" . $consultingFirm['consulting_firm'] . "'";
        $statement = $db->prepare($firmsId);
        $statement->execute();
        $firmId = $statement->fetch();
//echo $firmId['consulting_firm_id'];
        $row_set = array();
        $bank = "SELECT bank_abbr FROM consulting_abbrevation WHERE bank_abbr like '" . $_GET['consultbankac'] . "%' and firm_ids = :firm";
        $statement = $db->prepare($bank);
        $statement->bindParam(':firm', $firmId['consulting_firm_id']);
        $statement->execute();

        if ($statement->rowCount() > 0)
            $bankAbbv = $statement->fetchAll();
        foreach ($bankAbbv as $data) {
            $new['value'] = htmlentities(stripslashes($data['bank_abbr']));
            $new['label'] = $data['bank_abbr'];
            array_push($row_set, $new); //build an array
        }
        echo json_encode($row_set);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['consultingId']) && !empty($_GET['consultingId'])) {
    try {
        $firmServices = "SELECT * FROM consulting_firm_services WHERE consulting_firm_id = :id";
        $statement = $db->prepare($firmServices);
        $statement->bindParam(':id', $_GET['consultingId']);
        $statement->execute();
        $firmList = $statement->fetchAll();

        $invoiceDate = "SELECT invoice_date, invoice_id FROM invoice_details WHERE firm_id = :id and invoice_id = (select max(tmp.invoice_id) from invoice_details tmp) and is_deleted = 'No'";
        $statement = $db->prepare($invoiceDate);
        $statement->bindParam(':id', $_GET['consultingId']);
        $statement->execute();
        $invDate = $statement->fetch();

        $quotationDate = "SELECT quotation_date, quotation_id FROM quotation_details WHERE firm_id = '29' and quotation_id = (select max(tmp.quotation_id) from quotation_details tmp) and is_deleted = 'No'";
        $statement = $db->prepare($quotationDate);
        $statement->bindParam(':id', $_GET['consultingId']);
        $statement->execute();
        $quotDate = $statement->fetch();
        echo json_encode(array($firmList, $invDate, $quotDate));
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET["noteid"]) && !empty($_GET["noteid"])) {
    $note = "SELECT * FROM `general_note`";
    $statement = $db->prepare($note);
    $statement->execute();
    $noteDate = $statement->fetch();
    echo json_encode($noteDate);
} else if(isset ($_GET['noteData']) && !empty ($_GET["noteData"])){
    $updateNote = "UPDATE `general_note` SET `note_data`= '".$_GET["noteData"]."' WHERE `note_id` = '1'";
    $statement = $db->prepare($updateNote);
    $statement->execute();
    
    echo "success";
}