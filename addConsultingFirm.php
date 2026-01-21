<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include_once './dbConnect.php';
include 'utility.php';

function uploadFile($file, $varName) {
    $target_file = null;
    // process file upload -start
    //$target_dir = "uploads/puzzleImg/" . $username . "/";

    $folder = "uploads/" . $varName . "s/";

    $target_dir = $folder . $_POST['consultingFirmName'] . "/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }


    $target_file = $target_dir . basename($_FILES [$varName]["name"]);

    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    // if(isset($_POST["submit"])) {
    echo "a " + $_FILES [$varName] ["tmp_name"];
    $check = getimagesize($_FILES [$varName]["tmp_name"]);

    // echo $imageFileType;
    // Allow certain file formats
    if (strcasecmp($imageFileType, "jpg") != 0 && strcasecmp($imageFileType, "png") != 0 && strcasecmp($imageFileType, "jpeg") != 0 && strcasecmp($imageFileType, "gif") != 0) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        throw new Exception("There was an error uploading your file");
        // if everything is ok, try to upload file
    } else {

        // Check file size
        if ($_FILES [$varName] ["size"] > 500000) {
            Utitlity::compress($_FILES [$varName] ["tmp_name"], $target_file, 50);
        } else if (move_uploaded_file($_FILES [$varName] ["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES [$varName] ["name"]) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
            throw new Exception("There was an error uploading your file");
        }
    }
    // process file upload - end

    return $target_file;
}

$file = "Image";

if (isset($_POST['consultingFirmName']) && !empty($_POST['consultingFirmName']) && isset($_POST['firmContact']) && !empty($_POST['firmContact'])) {
    try {

        if (isset($_FILES ["firmLogo"]) && !( $_FILES['firmLogo']['size'] == 0 ) && isset($_FILES ["firmLogo"] ["name"]))
            $tf = uploadFile($file, "firmLogo");
        else
            $tf = "upload/img1.png";

        if (isset($_FILES ["firmSignature"]) && !( $_FILES['firmSignature']['size'] == 0 ) && isset($_FILES ["firmSignature"] ["name"]))
            $tf1 = uploadFile($file, "firmSignature");
        else
            $tf1 = "upload/img1.png";

        $services = explode('~', $_POST['firmServicesHidden']);
        $bankAbbr = explode('~', $_POST['firmBankAbbrHidden']);

        $consultingFirmEmail = "";
        $firmAddress = "";
        $panNo = "";
        $bankName = "";
        $bankAddress = "";
        $bankAcNo = "";
        $ifscCode = "";
        $micrNo = "";
        $firmAbbreviation = "";
        $firmInvoice = "";
        $firmQuotation = "";

        if (isset($_POST['consultingFirmEmail']) && !empty($_POST['consultingFirmEmail'])) {
            $consultingFirmEmail = $_POST['consultingFirmEmail'];
        }

        if (isset($_POST['firmAddress']) && !empty($_POST['firmAddress'])) {
            $firmAddress = $_POST['firmAddress'];
        }

        if (isset($_POST['panNo']) && !empty($_POST['panNo'])) {
            $panNo = $_POST['panNo'];
        }

        if (isset($_POST['bankName']) && !empty($_POST['bankName'])) {
            $bankName = $_POST['bankName'];
        }

        if (isset($_POST['bankAddress']) && !empty($_POST['bankAddress'])) {
            $bankAddress = $_POST['bankAddress'];
        }

        if (isset($_POST['bankAcNo']) && !empty($_POST['bankAcNo'])) {
            $bankAcNo = $_POST['bankAcNo'];
        }

        if (isset($_POST['ifscCode']) && !empty($_POST['ifscCode'])) {
            $ifscCode = $_POST['ifscCode'];
        }

        if (isset($_POST['micrNo']) && !empty($_POST['micrNo'])) {
            $micrNo = $_POST['micrNo'];
        }

        if (isset($_POST['firmAbbreviation']) && !empty($_POST['firmAbbreviation'])) {
            $firmAbbreviation = $_POST['firmAbbreviation'];
        }

        if (isset($_POST['firmInvoice']) && !empty($_POST['firmInvoice'])) {
            $firmInvoice = $_POST['firmInvoice'];
        }

        if (isset($_POST['firmQuotation']) && !empty($_POST['firmQuotation'])) {
            $firmQuotation = $_POST['firmQuotation'];
        }
        
        if (isset($_POST['isGstApplicable'])) {
    $isGstApplicable = $_POST['isGstApplicable'];
} else {
    $isGstApplicable = 'No';
}

if ($isGstApplicable === 'Yes' && isset($_POST['gstPercent'])) {
    $gstPercent = $_POST['gstPercent'];
} else {
    $gstPercent = 0;
}

        $addConsultingFirm = "INSERT INTO consulting_firm (firm_name, firm_email, firm_logo, firm_address, firm_contact, firm_pan_no, bank_name, bank_address, bank_account_no, ifsc_code, micr_no, firm_abbrevation, invoice_no, quotation_no, firmSignature, is_gst_applicable, gst_percent) VALUES (:name, :email, :logo, :firmAddress, :firmContact, :pan_no, :bank_name, :bank_address, :acount_no, :ifsc_code, :micr_no, :firm_abbr, :invoice_no, :quotation_no, :firmSignature, :isGstApplicable, :gstPercent)";
        $statement = $db->prepare($addConsultingFirm);
        $statement->bindParam(':name', $_POST['consultingFirmName']);
        $statement->bindParam(':email', $consultingFirmEmail);
        $statement->bindParam(':logo', $tf);
        $statement->bindParam(':firmAddress', $firmAddress);
        $statement->bindParam(':firmContact', $_POST['firmContact']);
        $statement->bindParam(':pan_no', $panNo);
        $statement->bindParam(':bank_name', $bankName);
        $statement->bindParam(':bank_address', $bankAddress);
        $statement->bindParam(':acount_no', $bankAcNo);
        $statement->bindParam(':ifsc_code', $ifscCode);
        $statement->bindParam(':micr_no', $micrNo);
        $statement->bindParam(':firm_abbr', $firmAbbreviation);
        $statement->bindParam(':invoice_no', $firmInvoice);
        $statement->bindParam(':quotation_no', $firmQuotation);
        $statement->bindParam(':firmSignature', $tf1);
        $statement->bindParam(':isGstApplicable', $isGstApplicable);
        $statement->bindParam(':gstPercent', $gstPercent);
        $statement->execute();

        $sql = "SELECT max(LAST_INSERT_ID(consulting_firm_id)) as id From consulting_firm";
        $statement = $db->prepare($sql);
        $statement->execute();
        $row = $statement->fetch();
        $firm_id = $row['id'];

        if ($firm_id > 0) {
            for ($i = 0; $i < count($services); ++$i) {
                if ($services[$i] != "") {
                    $addServices = "INSERT INTO consulting_firm_services (service_desc, consulting_firm_id) VALUES (:serviceDesc, :firmId)";
                    $statement = $db->prepare($addServices);
                    $statement->bindParam(':serviceDesc', $services[$i]);
                    $statement->bindParam(':firmId', $firm_id);
                    $statement->execute();
                }
            }

            for ($i = 0; $i < count($bankAbbr); ++$i) {
                if ($bankAbbr[$i] != "") {
                    $addBankAbbrivations = "INSERT INTO consulting_abbrevation (firm_ids, bank_abbr) VALUES (:firmId,:abbr)";
                    $statement = $db->prepare($addBankAbbrivations);
                    $statement->bindParam(':firmId', $firm_id);
                    $statement->bindParam(':abbr', $bankAbbr[$i]);
                    $statement->execute();
                }
            }
        }
        header("Location: masterData.php");
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['firmId']) && !empty($_GET['firmId'])) {
    try {
        $deleteFirm = "DELETE FROM consulting_firm WHERE consulting_firm_id = :firm_id";
        $statement = $db->prepare($deleteFirm);
        $statement->bindParam(':firm_id', $_GET['firmId']);
        if ($statement->execute()) {
            
            $firmServiceDlt = "DELETE FROM consulting_firm_services WHERE consulting_firm_id = :firm_id";
            $statement = $db->prepare($firmServiceDlt);
            $statement->bindParam(':firm_id', $_GET['firmId']);
            $statement->execute();
            
            $dltAbbr = "DELETE FROM consulting_abbrevation WHERE firm_ids = :firm_id";
            $statement = $db->prepare($dltAbbr);
            $statement->bindParam(':firm_id', $_GET['firmId']);
            $statement->execute();
            
            $allFirms = "SELECT * FROM consulting_firm";
            $statement = $db->prepare($allFirms);
            $statement->execute();
            
            $firmList = $statement->fetchAll();
            echo json_encode($firmList);
        }
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET['firmid']) && !empty($_GET['firmid'])) {
    try {
        $firmDetails = "SELECT * FROM consulting_firm WHERE consulting_firm_id = :firm_id";
        $statement = $db->prepare($firmDetails);
        $statement->bindParam(':firm_id', $_GET['firmid']);
        $statement->execute();
        $firm = $statement->fetch();

        $firmServices = "SELECT * FROM consulting_firm_services WHERE consulting_firm_id = :firm_id";
        $statement = $db->prepare($firmServices);
        $statement->bindParam(':firm_id', $_GET['firmid']);
        $statement->execute();
        $services = $statement->fetchAll();

        $firmAbbr = "SELECT * FROM consulting_abbrevation WHERE firm_ids = :firm_id ";
        $statement = $db->prepare($firmAbbr);
        $statement->bindParam(':firm_id', $_GET['firmid']);
        $statement->execute();
        $bankAbbr = $statement->fetchAll();

        echo json_encode(array($firm, $services, $bankAbbr));
    } catch (Exception $ex) {
        
    }
}