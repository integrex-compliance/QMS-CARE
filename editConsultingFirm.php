<?php
session_start();
include_once './dbConnect.php';
include 'utility.php';

function uploadFile($file, $varName) {
    $target_file = null;
    $folder = "uploads/" . $varName . "s/";
    $target_dir = $folder . $_POST['editconsultingFirmName'] . "/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES[$varName]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
    $check = getimagesize($_FILES[$varName]["tmp_name"]);

    if (strcasecmp($imageFileType, "jpg") != 0 && strcasecmp($imageFileType, "png") != 0 && strcasecmp($imageFileType, "jpeg") != 0 && strcasecmp($imageFileType, "gif") != 0) {
        echo "Only JPG, JPEG, PNG & GIF allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        throw new Exception("Error uploading file");
    } else {
        if ($_FILES[$varName]["size"] > 500000) {
            Utitlity::compress($_FILES[$varName]["tmp_name"], $target_file, 50);
        } else if (move_uploaded_file($_FILES[$varName]["tmp_name"], $target_file)) {
            // success
        } else {
            throw new Exception("Error uploading file");
        }
    }
    return $target_file;
}

$file = "Image";
if (isset($_POST['firmServicesId']) && !empty($_POST['firmServicesId'])) {
    try {
        $tf = null;
        $tf1 = null;

        if (isset($_FILES["editFirmLogo"]) && !($_FILES['editFirmLogo']['size'] == 0))
            $tf = uploadFile($file, "editFirmLogo");

        if (isset($_FILES["editFirmSignature"]) && !($_FILES['editFirmSignature']['size'] == 0))
            $tf1 = uploadFile($file, "editFirmSignature");

        $services = explode('~', $_POST['editfirmServicesHidden']);
        $servicesids = explode('~', $_POST['editfirmServicesIdHidden']);
        $abbreviationId = explode('~', $_POST['editAbbrIdHidden']);
        $abbreviation = explode('~', $_POST['editbankAbbreviationHidden']);

        // Read GST fields from POST
        $is_gst_applicable = isset($_POST['editisGstApplicable']) ? $_POST['editisGstApplicable'] : 'No';
        $gst_percent = ($is_gst_applicable == 'Yes') ? $_POST['editgstPercent'] : null;

        // Update consulting firm details (including GST)
        $updateFirm = "UPDATE consulting_firm SET 
                firm_name = :name, 
                firm_email = :email, 
                firm_address = :firmAddress, 
                firm_contact = :firmContact, 
                firm_pan_no = :pan_no, 
                bank_name = :bank_name, 
                bank_address = :bank_address, 
                bank_account_no = :acount_no, 
                ifsc_code = :ifsc_code, 
                micr_no = :micr_no, 
                firm_abbrevation = :firm_abbr, 
                invoice_no = :invoice_no, 
                quotation_no = :quotatio_no, 
                is_gst_applicable = :is_gst_applicable, 
                gst_percent = :gst_percent 
                WHERE consulting_firm_id = :id";

        $statement = $db->prepare($updateFirm);
        $statement->bindParam(':id', $_POST['firmServicesId']);
        $statement->bindParam(':name', $_POST['editconsultingFirmName']);
        $statement->bindParam(':email', $_POST['editconsultingFirmEmail']);
        $statement->bindParam(':firmAddress', $_POST['editfirmAddress']);
        $statement->bindParam(':firmContact', $_POST['editfirmContact']);
        $statement->bindParam(':pan_no', $_POST['editpanNo']);
        $statement->bindParam(':bank_name', $_POST['editbankName']);
        $statement->bindParam(':bank_address', $_POST['editbankAddress']);
        $statement->bindParam(':acount_no', $_POST['editbankAcNo']);
        $statement->bindParam(':ifsc_code', $_POST['editifscCode']);
        $statement->bindParam(':micr_no', $_POST['editmicrNo']);
        $statement->bindParam(':firm_abbr', $_POST['editFirmAbbreviation']);
        $statement->bindParam(':invoice_no', $_POST['editFirmInvoice']);
        $statement->bindParam(':quotatio_no', $_POST['editFirmQuotation']);
        $statement->bindParam(':is_gst_applicable', $is_gst_applicable);
        $statement->bindParam(':gst_percent', $gst_percent);
        $statement->execute();

        $dltserviceAbbr = "DELETE FROM consulting_firm_services WHERE consulting_firm_id = :id";
        $stmt = $db->prepare($dltserviceAbbr);
        $stmt->bindParam(':id', $_POST['firmServicesId']);
        $stmt->execute();

        for ($i = 0; $i < count($services); ++$i) {
            if ($services[$i] != '') {
                    $addFirmService = "INSERT INTO consulting_firm_services (service_desc, consulting_firm_id) VALUES (:desc, :id)";
                    $stmt = $db->prepare($addFirmService);
                    $stmt->bindParam(':desc', $services[$i]);
                    $stmt->bindParam(':id', $_POST['firmServicesId']);
                    $stmt->execute();
                }
            }

        // Delete & re-insert abbreviations
        $dltFirmAbbr = "DELETE FROM consulting_abbrevation WHERE firm_ids = :id";
        $stmt = $db->prepare($dltFirmAbbr);
        $stmt->bindParam(':id', $_POST['firmServicesId']);
        $stmt->execute();

        for ($i = 0; $i < count($abbreviation); ++$i) {
            if ($abbreviation[$i] != '') {                
                $add = "INSERT INTO consulting_abbrevation (firm_ids, bank_abbr) VALUES (:id, :abbrevation)";
                $stmt = $db->prepare($add);
                $stmt->bindParam(':id', $_POST['firmServicesId']);
                $stmt->bindParam(':abbrevation', $abbreviation[$i]);
                $stmt->execute();
            }
        }

        // Handle uploaded logo
        if ($tf != null) {
            $sql = "UPDATE consulting_firm SET firm_logo= :firmLogo where consulting_firm_id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':firmLogo', $tf);
            $stmt->bindParam(':id', $_POST['firmServicesId']);
            $stmt->execute();
        }

        // Handle uploaded signature
        if ($tf1 != null) {
            $sql = "UPDATE consulting_firm SET firmSignature= :firmSignature where consulting_firm_id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':firmSignature', $tf1);
            $stmt->bindParam(':id', $_POST['firmServicesId']);
            $stmt->execute();
        }

        header("Location: masterData.php");
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
}
?>
