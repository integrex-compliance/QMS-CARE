<?php

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
include_once('dbConnect.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('QMS');
$pdf->SetTitle('Quotation');
$pdf->SetSubject('Quotation for Customer');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Name of the firm here", PDF_HEADER_STRING);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

$customerId = ""; //based on this we need to take customer information
$customerName = "";
$customerAddress = "";
$quotation_no = "";
$quotation_date = "";
$firmName = "";
$firmLogo = "";
$firmAddress = "";
$customerAddress = "";
$firmAbbrevation = "";
$audit_body_charges = "";
$consultancy_charges = "";
$initial_amount = "";
$registration_amount = "";
$payment_certification = "";
$reamining_consultancy_amount = "";
$total_amt = 0;
$isNABCBAccredited = true;
$otherAccreditedFirm = "";
$firmDetails = "";
$firm_id = ""; //based on this we will take logo, address & name etc
$firm_email = "";
$firmAddress = "";
$firmContact = "";
$quotid = "";
$conditions = "";
$newQuotation = "";
$accredation = "";
$firmSign = "";
//Data extraction

$subconditions = "";

if (isset($_POST['quotationDt'])) {
    $quotation_date = $_POST['quotationDt'];
} else if (isset($_POST['editquotationDt'])) {
    $quotation_date = $_POST['editquotationDt'];
}
//if (isset($_POST['quotationNo'])) {
//    $quotation_no = $_POST['quotationNo'];
//}
$service_string = "";
if (isset($_POST['serviceString'])) {
    $service_string = $_POST['serviceString'];
} else if (isset($_POST['editserviceString'])) {
    $service_string = $_POST['editserviceString'];
}
if (isset($_POST['auditBodyChange'])) {
    $audit_body_charges = $_POST['auditBodyChange'];
} else if ($_POST['editauditBodyChange']) {
    $audit_body_charges = $_POST['editauditBodyChange'];
}
if (isset($_POST['consultingCharges'])) {
    $consultancy_charges = $_POST['consultingCharges'];
    $total_amt = $audit_body_charges + $consultancy_charges;
} else if (isset($_POST['editconsultingCharges'])) {
    $consultancy_charges = $_POST['editconsultingCharges'];
    $total_amt = $audit_body_charges + $consultancy_charges;
}
if (isset($_POST['initialAmount'])) {
    $initial_amount = $_POST['initialAmount'];
} else if (isset($_POST['editinitialAmount'])) {
    $initial_amount = $_POST['editinitialAmount'];
}
if (isset($_POST['registrationAmt'])) {
    $registration_amount = $_POST['registrationAmt'];
} else if (isset($_POST['editregistrationAmt'])) {
    $registration_amount = $_POST['editregistrationAmt'];
}
if (isset($_POST['paymentOfCertification'])) {
    $payment_certification = $_POST['paymentOfCertification'];
} else if (isset($_POST['editpaymentOfCertification'])) {
    $payment_certification = $_POST['editpaymentOfCertification'];
}
if (isset($_POST['remainingConsultancyAmt'])) {
    $reamining_consultancy_amount = $_POST['remainingConsultancyAmt'];
} else if (isset($_POST['editremainingConsultancyAmt'])) {
    $reamining_consultancy_amount = $_POST['editremainingConsultancyAmt'];
}
if (isset($_POST['otherAccredited']) && $_POST['otherAccredited'] != "") {
    $otherAccreditedFirm = $_POST['otherAccredited'];
    $isNABCBAccredited = false;
    $accredation = "1";
} else if (isset($_POST['editotherAccredited']) && $_POST['editotherAccredited'] != "") {
    $otherAccreditedFirm = $_POST['editotherAccredited'];
    $isNABCBAccredited = false;
    $accredation = "1";
}

//get variables from get/post to fill the details
if (isset($_POST['quoteCustomerId'])) {
    $customerId = $_POST['quoteCustomerId'];
} else if (isset($_POST['editquoteCustomerId'])) {
    $customerId = $_POST['editquoteCustomerId'];
}
//    echo $_POST['quoteCustomerId'];
$customerDetails = "SELECT * FROM customer_details WHERE customer_id = :customer_id";
$statement = $db->prepare($customerDetails);
$statement->bindParam(":customer_id", $customerId);
$statement->execute();
$customer = $statement->fetch();
$customerName = $customer['company_name'];
$customName = explode("(",$customerName);
$customerAddress = $customer['company_address'];

//echo $_POST['consultingFirmId'];
if (isset($_POST['consultingFirmId'])) {
//    echo $_POST['consultingFirmId'];
    $firm_id = $_POST['consultingFirmId'];
} else if (isset($_POST['editconsultingFirmId'])) {
    $firm_id = $_POST['editconsultingFirmId'];
}
$sql = "SELECT consulting_firm_id, firm_name, firm_email, firm_logo, firm_pan_no, bank_name, bank_address, bank_account_no, ifsc_code, micr_no, firm_address, firm_contact, firm_abbrevation, quotation_no,firmSignature FROM consulting_firm WHERE consulting_firm_id = :firm_id";
$statement = $db->prepare($sql);
$statement->bindParam(":firm_id", $firm_id);
$statement->execute();
$firmDetails = $statement->fetch();

$firmName = $firmDetails['firm_name'];
$firmLogo = $firmDetails['firm_logo'];
$firmEmail = $firmDetails['firm_email'];
$firmAddress = $firmDetails['firm_address'];
$firmContact = $firmDetails['firm_contact'];
$firmAbbrevation = $firmDetails['firm_abbrevation'];
$quotation_no = $firmDetails['quotation_no'];
$firmSign = $firmDetails['firmSignature'];
$newQuotation = $quotation_no;

if (isset($_POST['editquot-id']) && !empty($_POST['editquot-id'])) {
    $quotUpdate = "UPDATE quotation_details SET custer_id = :custId,quotation_date = :quotationDate, customer_name = :custName,"
            . "firm_service = :service, audit_body_charge = :auditBodyCharges, consulting_charge = :consultingCharges, "
            . "initial_amount = :initialAmount, registration_amt = :registrationAmt, pay_consultancy = :paymentCert, remaining_consultancy = :remainingAmt, "
            . "other = :other, total_amount = :amount, accredation = :accredation WHERE quotation_id = :id";
    $statement = $db->prepare($quotUpdate);
    $statement->bindParam(':custId', $customerId);
    $statement->bindParam(':service', $service_string);
    $quotationDateFormatted = date("Y-m-d", strtotime($quotation_date));
    $statement->bindParam(':quotationDate', $quotationDateFormatted);
    $statement->bindParam(':custName', $customerName);
    $statement->bindParam(':auditBodyCharges', $audit_body_charges);
    $statement->bindParam(':consultingCharges', $consultancy_charges);
    $statement->bindParam(':initialAmount', $initial_amount);
    $statement->bindParam(':registrationAmt', $registration_amount);
    $statement->bindParam(':paymentCert', $payment_certification);
    $statement->bindParam(':remainingAmt', $reamining_consultancy_amount);
    $statement->bindParam(':other', $otherAccreditedFirm);
    $statement->bindParam(':amount', $total_amt);
    $statement->bindParam(':id', $_POST['editquot-id']);
    $statement->bindParam(':accredation', $accredation);
    $statement->execute();

    $quotCondition = "DELETE FROM quotation_condition WHERE quotationid = :id";
    $statement = $db->prepare($quotCondition);
    $statement->bindParam(':id', $_POST['editquot-id']);
    $statement->execute();

    if (isset($_POST['editconditionsHidden'])) {
        $conditions = explode('~', $_POST['editconditionsHidden']);
        $count = 0;
        foreach ($conditions as $condition) {
            if ($conditions[$count] != "") {
                $quotCondition = "INSERT INTO quotation_condition(quotationid, quot_condition) VALUES (:quotId, :condition)";
                $statement = $db->prepare($quotCondition);
                $statement->bindParam(':quotId', $_POST['editquot-id']);
                $statement->bindParam(':condition', $conditions[$count]);
                $statement->execute();
            }
        }
    }
} else {
    $newQuotation = $quotation_no + 1;

    $incrementQuotation = "UPDATE consulting_firm SET quotation_no = :number WHERE consulting_firm_id = :id";
    $statement = $db->prepare($incrementQuotation);
    $statement->bindParam(':number', $newQuotation);
    $statement->bindParam(':id', $firm_id);
    $statement->execute();

    $quotationDetails = "INSERT INTO quotation_details (firm_id, custer_id, firm_abbrevation, quotation_no, customer_name,quotation_date, firm_service, audit_body_charge, consulting_charge, initial_amount, registration_amt, pay_consultancy, remaining_consultancy, other, total_amount, accredation) VALUES "
            . "(:firmId,:custId,:firmabbr,:quotationNo,:customerName,:quotationDate,:service,:auditBodyCharges,:consultingCharges,:initialAmount,:registrationAmt,:paymentCert,:remainingAmt,:other,:amount,:accredation)";
    $statement = $db->prepare($quotationDetails);
    $statement->bindParam(':firmId', $firm_id);
    $statement->bindParam(':custId', $_POST['quoteCustomerId']);
    $statement->bindParam(':firmabbr', $firmAbbrevation);
    $statement->bindParam(':quotationNo', $newQuotation);
    $statement->bindParam(':customerName', $customerName);
    $statement->bindParam(':service', $service_string);
    $quotationDateFormatted = date("Y-m-d", strtotime($quotation_date));
    $statement->bindParam(':quotationDate', $quotationDateFormatted);
    $statement->bindParam(':auditBodyCharges', $audit_body_charges);
    $statement->bindParam(':consultingCharges', $consultancy_charges);
    $statement->bindParam(':initialAmount', $initial_amount);
    $statement->bindParam(':registrationAmt', $registration_amount);
    $statement->bindParam(':paymentCert', $payment_certification);
    $statement->bindParam(':remainingAmt', $reamining_consultancy_amount);
    $statement->bindParam(':other', $otherAccreditedFirm);
    $statement->bindParam(':amount', $total_amt);
    $statement->bindParam(':accredation', $accredation);
    $statement->execute();

    $sql = "SELECT max(LAST_INSERT_ID(quotation_id)) as id From quotation_details";
    $statement = $db->prepare($sql);
    $statement->execute();
    $row = $statement->fetch();
    $quotid = $row['id'];

    if (isset($_POST['conditionsHidden'])) {
        $conditions = explode('~', $_POST['conditionsHidden']);
        $count = 0;
        foreach ($conditions as $condition) {
            if ($conditions[$count] != "") {
                $quotCondition = "INSERT INTO quotation_condition(quotationid, quot_condition) VALUES (:quotId, :condition)";
                $statement = $db->prepare($quotCondition);
                $statement->bindParam(':quotId', $quotid);
                $statement->bindParam(':condition', $conditions[$count]);
                $statement->execute();
            }
        }
    }
}


if (isset($_POST['conditionsHidden']) && !empty($_POST['conditionsHidden'])) {
    $conditions = explode('~', $_POST['conditionsHidden']);

    for ($i = 0; $i < count($conditions); ++$i) {
        if ($conditions[$i] != "") {
            $subconditions = $subconditions . "<li>" . $conditions[$i] . "</li>";
        }
    }
} else if(isset ($_POST['editconditionsHidden']) && !empty ($_POST['editconditionsHidden'])){
    $conditions = explode('~', $_POST['editconditionsHidden']);

    for ($i = 0; $i < count($conditions); ++$i) {
        if ($conditions[$i] != "") {
            $subconditions = $subconditions . "<li>" . $conditions[$i] . "</li>";
        }
    }
}

$other_conditions = ""; //array
// ---------------------------------------------------------
// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
// create some HTML content
$html = '<table><tr><td width="10%" style="text-align:right"><img src="' . $firmLogo . '" alt="logo" height="110" width="110"></td><td width="90%" style="text-align:center; color:blue; font-size:1.6em; vertical-align: middle;">' . $firmName . '<br><span style="font-size:0.6em">' . $firmAddress . '</span> </td></tr>
<tr><td></td></tr></table>
        <h2  style="text-align:center"><i>QUOTATION Cum AGREEMENT</i></h2>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// create some HTML content
$subtable = '<table border="2" cellspacing="6" cellpadding="4"><tr><td>a</td><td>b</td></tr><tr><td>c</td><td>d</td></tr></table>';
$nabcbImg = "";
$accreditedText = "";
$accreditedNote = "";

if ($isNABCBAccredited) {
    $nabcbImg = '<img src="images/nabcb.png"/>';
    $accreditedText = "NABCB*";
    $accreditedNote = '<b>Note:</b>  Kindly sign for approval & send back to us. The NABCB logo describes above is for providing information about NABCB, logo of Accredited certification. ';
} else {
    $accreditedText = $otherAccreditedFirm;
    $accreditedNote = "";
}

$html = '
<table border="1" cellspacing="1" cellpadding="4">
	
	<tr >
		<td height="100"><b>TO</b> - <br/> <b>' . $customName[0] . '</b><br/>' . $customerAddress . '<br/></td>
		
		<td > <br/> <b>QUOTE NO.</b>: ' . $firmAbbrevation . '/' . $newQuotation . ' <br/><br/><br/><b>DATE:</b> ' . $quotation_date . '</td>
	</tr>
    <tr><td width="20%">' . $nabcbImg . '</td>
    <td width="80%"><b>PROFESSIONAL CHARGES:</b> For ' . $service_string . ' of ISO 9001-2008.<br/>

<p>This Quote also Include Consultancy Charges + Travelling, Lodging, Training, and documentation including MRM, IQA etc.</p>
<table>
    <tr>
        <td width="70%" style="text-align:right">A)  Audit body charges <b>' . $accreditedText . '</b> accredited -</td>
        <td width="30%">' . convertcash($audit_body_charges, "Rs. ") . '/-</td>
    </tr>
    <tr>
        <td width="70%" style="text-align:right">B)   Consultancy Charges + Travelling, Lodging etc..  -</td>
        <td width="30%">' . convertcash($consultancy_charges, "Rs. ") . '/-</td>
    </tr>
    <tr>
        <td width="70%" style="text-align:right"></td>
        <td width="30%"><hr></td>
    </tr>
    <tr>
        <td width="70%" style="text-align:right"><b>Total Overall Expenditure -</b></td>
        <td width="30%"><b>' . convertcash($total_amt, "Rs. ") . '/-</b></td>
    </tr>
</table >
     </td></tr>
     <tr>
     <td colspan="2">
     <b>Terms & Conditions:</b>
     <ol>
     <li>Initial amount with contract is ' . convertcash($initial_amount, "Rs. ") . '/-.</li>
<li>Registration amount of certification ' . convertcash($registration_amount, "Rs. ") . ' /- once getting agreement & quotation from body.</li>
<li>Payment of certification body rest amount i.e. ' . convertcash($payment_certification, "Rs. ") . ' /- on same completion date of final certification audit.</li>
<li>Remaining consultancy amount i.e.   ' . convertcash($reamining_consultancy_amount, "Rs. ") . ' /-.after successfully completion of project.</li>
<li>Company shall assign a responsible person, shall be available during our consultant visit. Auditor traveling expenses, calibration, & documentation charges shall bear by Client.</li>
<li>Duration is 15 Mandays.</li>
<li>Payment made by account pay cheque, once Invoice rested by Concern Firm.</li>' . $subconditions . '
</ol>
' . $accreditedNote . '

     </td>
     </tr>
    </table>
    
    <table border="1" cellspacing="1" cellpadding="4"> 
    <tr><td style="border:0.1px solid white">
    <img src="images/client_sign.png"/>
    </td><td style="text-align:right;border-bottom:0.1px solid white">' . $firmName . '<br/><br/>
    <img src="' . $firmSign . '" alt="logo" height="50" width="90"><br/><br/>
    PROPRIETOR
    </td></tr>
    <tr><td style="text-align:left;border-top:0.1px solid white"><b>OFFICE ADDRESS:</b><br/>' . $firmName . '
<br/>' . $firmAddress . '<br/>E mail : ' . $firmEmail . '<br/> Phone : ' . $firmContact . '
    
    </td></tr>
    </table>
    ';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');



//Close and output PDF document
$pdf->Output('quotation.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

function convertcash($num, $currency) {
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3); // extracts the last three digits 
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping. 
        $explrestunits = "";
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            $explrestunits .= (int) $expunit[$i] . ","; // creates each of the 2's group and adds a comma to the end 
        }

        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }

    return $currency . $thecash . ".00"; // writes the final format where $currency is the currency symbol. 
}
