<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include_once './dbConnect.php';

if (isset($_GET['newService']) && !empty($_GET['newService'])) {
    try {
        $newServiceAdd = "INSERT INTO service_type (service) VALUES (:service)";
        $statement = $db->prepare($newServiceAdd);
        $statement->bindParam(':service', $_GET['newService']);
        $statement->execute();

        $services = "SELECT * FROM service_type";
        $statement = $db->prepare($services);
        $statement->execute();
        $serviceList = $statement->fetchAll();
        echo json_encode($serviceList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['newCert']) && !empty($_GET['newCert']) && isset($_GET['newAbbreviationHidden']) && !empty($_GET['newAbbreviationHidden'])) {
    try {
        $abbrs = explode('~', $_GET['newAbbreviationHidden']);
        $newServiceAdd = "INSERT INTO cert_body (cert) VALUES (:cert)";
        $statement = $db->prepare($newServiceAdd);
        $statement->bindParam(':cert', $_GET['newCert']);
        $statement->execute();

        $sql = "SELECT max(LAST_INSERT_ID(cert_id)) as id From cert_body";
        $statement = $db->prepare($sql);
        $statement->execute();
        $row = $statement->fetch();
        $serviceid = $row['id'];

        for ($i = 0; $i < count($abbrs); ++$i) {
            if ($abbrs[$i] != "") {
                $abbrInsert = "INSERT INTO consulting_abbrevation (cert_id, bank_abbr) VALUES (:certid,:abbr)";
                $statement = $db->prepare($abbrInsert);
                $statement->bindParam(':certid', $serviceid);
                $statement->bindParam(':abbr', $abbrs[$i]);
                $statement->execute();
            }
        }

//        $certs = "SELECT * FROM cert_body";
//        $statement = $db->prepare($certs);
//        $statement->execute();
//        $certList = $statement->fetchAll();
        header("Location: masterData.php");
//        echo json_encode($certList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['newConsultant']) && !empty($_GET['newConsultant'])) {
    try {
        $newConsultantAdd = "INSERT INTO consultant (consultant_name) VALUES (:consultant)";
        $statement = $db->prepare($newConsultantAdd);
        $statement->bindParam(':consultant', $_GET['newConsultant']);
        $statement->execute();

        $consultant = "SELECT * from consultant";
        $statement = $db->prepare($consultant);
        $statement->execute();
        $consultantList = $statement->fetchAll();

        echo json_encode($consultantList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['newAuditors']) && !empty($_GET['newAuditors'])) {
    try {
        $newAuditor = "INSERT INTO auditors (auditor_name) VALUES (:newAuditors)";
        $statement = $db->prepare($newAuditor);
        $statement->bindParam(':newAuditors', $_GET['newAuditors']);
        $statement->execute();

        $auditors = "SELECT * from auditors";
        $statement = $db->prepare($auditors);
        $statement->execute();
        $auditorsList = $statement->fetchAll();

        echo json_encode($auditorsList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['newAuditType']) && !empty($_GET['newAuditType'])) {
    try {
        $newAuditType = "INSERT INTO audit_type (audit_types) VALUES (:newAuditType)";
        $statement = $db->prepare($newAuditType);
        $statement->bindParam(':newAuditType', $_GET['newAuditType']);
        $statement->execute();

        $auditType = "SELECT * from audit_type";
        $statement = $db->prepare($auditType);
        $statement->execute();
        $auditTypeList = $statement->fetchAll();

        echo json_encode($auditTypeList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['serviceId']) && !empty($_GET['serviceId'])) {
    try {
        $removeService = "DELETE FROM service_type WHERE id = :id";
        $statement = $db->prepare($removeService);
        $statement->bindparam(':id', $_GET['serviceId']);
        $statement->execute();

        $services = "SELECT * FROM service_type";
        $statement = $db->prepare($services);
        $statement->execute();
        $serviceList = $statement->fetchAll();
        echo json_encode($serviceList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['certId']) && !empty($_GET['certId'])) {
    try {
        $removeCert = "DELETE FROM cert_body WHERE cert_id = :id";
        $statement = $db->prepare($removeCert);
        $statement->bindparam(':id', $_GET['certId']);
        $statement->execute();

        $deltAbbr = "DELETE FROM consulting_abbrevation WHERE cert_id = :id";
        $statement = $db->prepare($deltAbbr);
        $statement->bindParam(':id', $_GET['certId']);
        $statement->execute();

        $certs = "SELECT * FROM cert_body";
        $statement = $db->prepare($certs);
        $statement->execute();
        $certList = $statement->fetchAll();

        echo json_encode($certList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['consultantId']) && !empty($_GET['consultantId'])) {
    try {
        $removeConsultant = "DELETE FROM consultant WHERE consultant_id = :conslutId";
        $statement = $db->prepare($removeConsultant);
        $statement->bindParam(':conslutId', $_GET['consultantId']);
        $statement->execute();

        $consultant = "SELECT * FROM consultant";
        $statement = $db->prepare($consultant);
        $statement->execute();
        $consultantList = $statement->fetchAll();

        echo json_encode($consultantList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['auditorId']) && !empty($_GET['auditorId'])) {
    try {
        $removeAuditors = "DELETE FROM auditors WHERE auditors_id = :auditorId";
        $statement = $db->prepare($removeAuditors);
        $statement->bindParam(':auditorId', $_GET['auditorId']);
        $statement->execute();

        $auditors = "SELECT * from auditors";
        $statement = $db->prepare($auditors);
        $statement->execute();
        $auditorsList = $statement->fetchAll();

        echo json_encode($auditorsList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['auditTypeId']) && !empty($_GET['auditTypeId'])) {
    try {
        $removAuditType = "DELETE FROM audit_type WHERE audit_id = :auditTypeId";
        $statement = $db->prepare($removAuditType);
        $statement->bindParam(':auditTypeId', $_GET['auditTypeId']);
        $statement->execute();

        $auditType = "SELECT * from audit_type";
        $statement = $db->prepare($auditType);
        $statement->execute();
        $auditTypeList = $statement->fetchAll();

        echo json_encode($auditTypeList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['cert_ID']) && !empty($_GET['cert_ID'])) {
    try {
        $cert = "SELECT * FROM cert_body WHERE cert_id = :id";
        $statement = $db->prepare($cert);
        $statement->bindParam(':id', $_GET['cert_ID']);
        $statement->execute();
        $certDetail = $statement->fetch();

//        $bankAbbrvtn = "SELECT * FROM cert_abbrevation WHERE cert_id = :id";
        $bankAbbrvtn = "SELECT * FROM consulting_abbrevation WHERE cert_id = :id";
        $statement = $db->prepare($bankAbbrvtn);
        $statement->bindParam(':id', $_GET['cert_ID']);
        $statement->execute();
        $bankAbbrvtion = $statement->fetchAll();

        echo json_encode(array($certDetail, $bankAbbrvtion));
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_POST['editnewCert']) && !empty($_POST['editnewCert']) && isset($_POST['cert-id']) && !empty($_POST['cert-id'])) {
    try {

        $abbreviationId = explode('~', $_POST['editAbbreviationIDHidden']);
        $abbreviation = explode('~', $_POST['editAbbreviationHidden']);

        $updateCert = "UPDATE cert_body SET cert = :certBody WHERE cert_id = :id";
        $statement = $db->prepare($updateCert);
        $statement->bindParam(':certBody', $_POST['editnewCert']);
        $statement->bindParam(':id', $_POST['cert-id']);
        $statement->execute();

        $dlt = "DELETE FROM consulting_abbrevation WHERE cert_id = :id";
        $statement = $db->prepare($dlt);
        $statement->bindParam(':id', $_POST['cert-id']);
//                    $statement->bindParam(':ids', $abbreviationId[$i]);
        $statement->execute();

        for ($i = 0; $i < count($abbreviation); ++$i) {
            if ($abbreviation[$i] != "") {
                $abbrInsert = "INSERT INTO consulting_abbrevation (cert_id, bank_abbr) VALUES (:certid,:abbr)";
                $statement = $db->prepare($abbrInsert);
                $statement->bindParam(':certid', $_POST['cert-id']);
                $statement->bindParam(':abbr', $abbreviation[$i]);
                $statement->execute();
            }
        }

//        for ($i = 0; $i < count($abbreviation); ++$i) {
//            if ($abbreviation[$i] != '') {
//                if ($abbreviationId[$i] != "" && $abbreviationId[$i] != null) {
////                    $update = "UPDATE cert_abbrevation SET cert_abbr = :desc WHERE id = :ids";
//                    $dlt = "DELETE FROM consulting_abbrevation WHERE cert_id = :id";
//                    $statement = $db->prepare($dlt);
//                    $statement->bindParam(':id', $_POST['cert-id']);
////                    $statement->bindParam(':ids', $abbreviationId[$i]);
//                    $statement->execute();
//                } else {
//                    $insert = "INSERT INTO cert_abbrevation(cert_id, cert_abbr) VALUES (:id,:desc)";
//                    $statement = $db->prepare($insert);
//                    $statement->bindParam(':desc', $abbreviation[$i]);
//                    $statement->bindParam(':id', $_POST['cert-id']);
//                    $statement->execute();
//                }
//            }
//        }
        header("Location: masterData.php");
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['newHandler']) && !empty($_GET['newHandler'])) {
    try {
        $handler = "INSERT INTO handler (handler_name) VALUES (:handlername)";
        $statement = $db->prepare($handler);
        $statement->bindParam(':handlername', $_GET['newHandler']);
        $statement->execute();

        $allHandler = "SELECT * FROM handler";
        $statement = $db->prepare($allHandler);
        $statement->execute();
        $handlerList = $statement->fetchAll();
        echo json_encode($handlerList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET['handlerId']) && !empty($_GET['handlerId'])) {
    try {
        $dltHandler = "DELETE FROM handler WHERE handler_id = :id";
        $statement = $db->prepare($dltHandler);
        $statement->bindParam(':id', $_GET['handlerId']);
        $statement->execute();

        $allHandler = "SELECT * FROM handler";
        $statement = $db->prepare($allHandler);
        $statement->execute();
        $handlerList = $statement->fetchAll();
        echo json_encode($handlerList);
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_GET["newpcip"]) && !empty($_GET["newpcip"])) {
    try {
        $ips = "INSERT INTO pc_ip (pcip) VALUES (:ips)";
        $statement = $db->prepare($ips);
        $statement->bindParam(":ips", $_GET["newpcip"]);
        $statement->execute();

        $allips = "SELECT * FROM pc_ip WHERE is_deleted = 'N'";
        $statement = $db->prepare($allips);
        $statement->execute();
        $iplist = $statement->fetchAll();
        echo json_encode($iplist);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET["ipId"]) && !empty($_GET["ipId"])) {
    try {
        $sql = "DELETE FROM pc_ip WHERE id = :id";
        $statement = $db->prepare($sql);
        $statement->bindParam(":id", $_GET["ipId"]);
        $statement->execute();

        $allips = "SELECT * FROM pc_ip WHERE is_deleted = 'N'";
        $statement = $db->prepare($allips);
        $statement->execute();
        $iplist = $statement->fetchAll();
        echo json_encode($iplist);
    } catch (Exception $ex) {
        
    }
}