<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
$subconditions = "";
//echo $_POST['quot_id'];
$firmInfo = "SELECT * FROM consulting_firm WHERE consulting_firm_id = :firm";
$statement = $db->prepare($firmInfo);
$statement->bindParam(':firm', $_POST['firmid']);
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

$quotationDetails = "SELECT * FROM quotation_details WHERE quotation_id = :id";
$statement = $db->prepare($quotationDetails);
$statement->bindParam(':id', $_POST['quot_id']);
$statement->execute();
$quotInfo = $statement->fetch();

$quotation_date = $quotInfo['quotation_date'];
$service_string  = $quotInfo['firm_service'];
$customerName = $quotInfo['customer_name'];
$customName = explode("(",$customerName);
$audit_body_charges  = $quotInfo['audit_body_charge'];
$consultancy_charges = $quotInfo['consulting_charge'];
$total_amt = $quotInfo['total_amount'];
$initial_amount = $quotInfo['initial_amount'];
$registration_amount = $quotInfo['registration_amt'];
$payment_certification = $quotInfo['pay_consultancy'];
$reamining_consultancy_amount = $quotInfo['remaining_consultancy'];
$otherAccreditedFirm = $quotInfo['other'];
$accredationval = $quotInfo['accredation'];

$customerDetails = "SELECT company_address FROM customer_details WHERE company_name = :name";
$statement = $db->prepare($customerDetails);
$statement->bindParam(':name', $_POST['customer']);
$statement->execute();
$address  = $statement->fetch();
$customerAddress = $address['company_address'];

$quotCondi = "SELECT * FROM quotation_condition WHERE quotationid = :id";
$statement = $db->prepare($quotCondi);
$statement->bindParam(':id', $_POST['quot_id']);
$statement->execute();
$quot_info = $statement->fetchAll();

foreach ($quot_info as $condition) {
    $subconditions = $subconditions . "<li>" . $condition['quot_condition'] . "</li>";
}

//for ($i = 0; $i < count($conditions); ++$i) {
//    if ($conditions[$i] != "") {
//        $subconditions = $subconditions . "<li>" . $conditions[$i] . "</li>";
//    }
//}

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

if ($accredationval == '1') {
//    $nabcbImg = '<img src="images/nabcb.png"/>';
    $accreditedText = "NABCB*";
    $accreditedNote = '<b>Note:</b>  Kindly sign for approval & send back to us. The NABCB logo describes above is for providing information about NABCB, logo of Accredited certification. ';
} else {
    $nabcbImg = '<img src="images/nabcb.png"/>';
    $accreditedText = $otherAccreditedFirm;
    $accreditedNote = "";
}

$html = '
<table border="1" cellspacing="1" cellpadding="4">
	
	<tr >
		<td height="100"><b>TO</b> - <br/> <b>' . $customName[0] . ' -> '.$accredationval.'</b><br/>' . $customerAddress . '<br/></td>
		
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
    </table>';

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
