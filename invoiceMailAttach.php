<?php

//============================================================+
// File name   : example_003.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 003 for TCPDF class
//               Custom Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */
// Include the main TCPDF library (search for installation path).
require_once 'testemail.php';
require_once('tcpdf_include.php');
include_once './dbConnect.php';
date_default_timezone_set('Asia/Kolkata');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('QMS');
$pdf->SetTitle('Invoice');
$pdf->SetSubject('Invoice for Customer');
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
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

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
$customerEmail = "";
$quotation_no = "";
$quotation_date = "";
$firmName = "";
$firmLogo = "";
$firmAddress = "";
$firmDetails = "";
$firm_id = ""; //based on this we will take logo, address & name etc
$firm_email = "";
$firm_pan_no = "";
$bank_name = "";
$bank_address = "";
$bank_account_no = "";
$ifsc_code = "";
$micr_no = "";
$firmEmail = "";
$firmAddress = "";
$firmContact = "";
$abbrevation = "";
$net_price = "";
$service_tax = "";
$total_amt = "";
$firmSign = "";
$ST="";
$gstdec="";
//get variables from get/post to fill the details php selection code goes here

$invoice = "SELECT * FROM invoice_details WHERE invoice_id = :invoiceid and invoice_no = :invoiceno";
$statement = $db->prepare($invoice);
$statement->bindParam(':invoiceid', $_POST['invoiceid']);
$statement->bindParam(':invoiceno', $_POST['invoiceno']);
$statement->execute();
$invoiceDetails = $statement->fetch();

$sql = "SELECT consulting_firm_id, firm_name, firm_email, firm_logo, firm_pan_no, bank_name, bank_address, bank_account_no, ifsc_code, micr_no,firm_address, firm_contact, firm_abbrevation, invoice_no,firmSignature FROM consulting_firm WHERE consulting_firm_id = :firm_id";
$statement = $db->prepare($sql);
$statement->bindParam(":firm_id", $invoiceDetails['firm_id']);
$statement->execute();
$firmDetails = $statement->fetch();

$customerDetails = "SELECT * FROM customer_details WHERE customer_id = :customer_id";
$statement = $db->prepare($customerDetails);
$statement->bindParam(":customer_id", $invoiceDetails['customer_id']);
$statement->execute();
$customer = $statement->fetch();

$firmLogo = $firmDetails['firm_logo'];
$firmName = $firmDetails['firm_name'];
$firmAddress = $firmDetails['firm_address'];
$firmEmail = $firmDetails['firm_email'];
$firmContact = $firmDetails['firm_contact'];
$bank_name = $firmDetails['bank_name'];
$bank_address = $firmDetails['bank_address'];
$bank_account_no = $firmDetails['bank_account_no'];
$ifsc_code = $firmDetails['ifsc_code'];
$micr_no = $firmDetails['micr_no'];
$firmSign = $firmDetails['firmSignature'];
$abbrevation = $firmDetails['firm_abbrevation'];
$firm_pan_no = $firmDetails['firm_pan_no'];

$customerName = $customer['company_name'];
$customName = explode("(",$customerName);
$customerAddress = $customer['company_address'];
$customerEmail = $customer["contact_email"];
$newInvoice = $invoiceDetails['invoice_no'];
$quotation_date = date("d-m-Y", strtotime($invoiceDetails['invoice_date']));
$net_price = $invoiceDetails['net_price'];
$service_tax = $invoiceDetails['service_tax'];

$total_amt = $net_price + $service_tax;

 if($service_tax==0) 
        {
            $ST="NA";
        }
 else
        {
            $ST=$service_tax;
        }
$datec=date("01-07-2017");  
$d1=strtotime($quotation_date);
$d2=strtotime($datec);
if($d1 > $d2)
{
    $gstdec="<b>GST is not applicable to ". $firmName  ." as turnover of firm is less than 20 lac.</b>";
}
 else {
    $gstdec="<b>Service tax is not payble</b>";
}
$invoiceServices = "SELECT * FROM invoice_services WHERE invoice_ids = :id";
$statement = $db->prepare($invoiceServices);
$statement->bindParam(':id', $_POST['invoiceid']);
$statement->execute();
$serviceArray = $statement->fetchAll();

$invoiceServicesAmt = "SELECT service_amount FROM invoice_services WHERE invoice_ids = :id";
$statement = $db->prepare($invoiceServicesAmt);
$statement->bindParam(':id', $_POST['invoiceid']);
$statement->execute();
$serviceAmtArray = $statement->fetchAll();

// ---------------------------------------------------------
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();


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

    return $currency . $thecash ; // writes the final format where $currency is the currency symbol. 
}

function convertToWord($number) {
    $no = round($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'One', '2' => 'Two',
        '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
        '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
        '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
        '13' => 'Thirteen', '14' => 'Fourteen',
        '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
        '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
        '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
        '60' => 'Sixty', '70' => 'Seventy',
        '80' => 'Eighty', '90' => 'Ninety');
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
        } else
            $str[] = null;
    }
    $str = array_reverse($str);
    $result = implode('', $str);
    $points = ($point) ?
            "." . $words[$point / 10] . " " .
            $words[$point = $point % 10] : '';
    return $result . "Rupees  " . $points . " Only";
}


// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
//echo $firmName;
// create some HTML content
$html = '<table><tr><td width="10%" style="text-align:right"><img src="' . $firmLogo . '" alt="logo" height="110" width="110"></td><td width="90%" style="text-align:center; font-size:1.6em; vertical-align: middle;"><b>' . $firmName . '</b><br><span style="font-size:0.6em"><b>' . $firmAddress . '</b></span> </td></tr>
<tr><td></td></tr></table>
        <h2  style="text-align:center"><i>(ISO 9001 Certification Services)</i></h2>
        <h2  style="text-align:center">INVOICE</h2>

';

$pdf->writeHTML($html, true, false, true, false, '');
// create some HTML content
$subtable = '<table border="2" cellspacing="6" cellpadding="4"><tr><td>a</td><td>b</td></tr><tr><td>c</td><td>d</td></tr></table>';

$serviceRow = '';
//if (isset($_POST['invoiceService']) && isset($_POST['invoiceServiceAmt'])) {
//    $serviceArray = $_POST['invoiceService'];
//    $serviceAmtArray = $_POST['invoiceServiceAmt'];
$count = 0;
//foreach ($serviceArray as $service) {
////    for ($i = 0; $i < count($serviceArray); ++$i) {
//    $serviceRow = $serviceRow . '<tr><td>' . ($count + 1) . '</td><td>' . $service['service_desc'] . '</td><td>' . $service['service_amount'] . '</td></tr>';
//    $count = $count + 1;
//}

$serviceDesc = explode(',',$invoiceDetails['svc_desc']);
$serviceAmount = explode(',', $invoiceDetails['svc_amount']);
//foreach($serviceDesc as $service){
//   $service =  $service.'<tr><td>' . ($count + 1) . '</td><td>' . $service. '</td></tr>';
//}
for ($i = 0; $i < count($serviceDesc); $i++) {
    if($serviceDesc[$i] != "")
    $serviceRow = $serviceRow . '<tr><td>' . ($i + 1) . '</td><td>' . $serviceDesc[$i] . '</td><td>' . $serviceAmount[$i] . '/-</td></tr>';
}
//if(isset($invoiceDetails['svc_desc']) && !empty($invoiceDetails['svc_desc'])){
//    $serviceRow = $serviceRow . '<tr><td>' . ($count + 1) . '</td><td>' . $invoiceDetails['svc_desc'] . '</td><td>' . $invoiceDetails['svc_amount'] . '</td></tr>';
//}
//}
//echo $serviceRow;

$html = '<table border="1" cellspacing="1" cellpadding="4">
	<tr>
            <td height="100">TO - <br/> <b>' . $customName[0] . '</b><br/>' . $customerAddress . '<br/></td>
            <td style="vertical-align:top; position:relative; padding:0;"><div style="padding:10px;"> INVOICE NO: ' . $abbrevation . '/' . $newInvoice . '</div><hr style="border:1px solid #000; margin:0; width:100%;"/><div style="position:absolute; bottom:5px; left:10px;">DATE: ' . date("d.m.Y", strtotime($quotation_date)) . '</div></td>
	</tr>
    <tr><td colspan="3"><b>CONSULTING CHARGES</b> : QUALITY MANAGEMENT SYSTEM SERVICES</td></tr>
    </table>
    <table border="1" cellspacing="1" cellpadding="4">
    <tr><th width="5%">Sr.</th><th width="60%">Description</th><th width="35%">Amount in Rs.</th></tr>
  ' . $serviceRow . '
    <tr><td colspan="2" style="text-align:right">Net Price</td><td>' . convertcash($net_price, "") . '/-</td></tr>
    <tr><td colspan="2" style="text-align:right">GST @ 18%</td><td>' . convertcash($ST, "") . '</td></tr>
    <tr><td colspan="2" style="text-align:right"><b>Final Amount</b></td><td>' . convertcash($total_amt, "") . '/-</td></tr>
    <tr><td colspan="3"><b>Amount In Word : ' . convertToWord($total_amt) . '</b></td></tr>
    <tr><td colspan="3"><b>
    NOTE-</b>
    <ol>
    <li>Payment by cash is not acceptable.</li>
    <li>PAN Number :' . $firm_pan_no . '</li>
    <li>Bank details: ' . $bank_name . ', Address –' . $bank_address . '. Current account No.- ' . $bank_account_no . ', IFSC code – ' . $ifsc_code . ', MICR No.- ' . $micr_no . '</li>
    <li>In the event signature not mandatory if invoice send via QMS registered A/c mail ID i.e. “qmscare@gmail.com “.</li>
    <li>' . $gstdec . '</li>
    </ol>
    </td></tr>
    </table>
    <table border="1" cellspacing="1" cellpadding="4"> 
    <tr><td style="text-align:right;border-bottom:0.1px solid white">' . $firmName . '<br/>
        <img src="' . $firmSign . '" alt="logo" height="50" width="90"><br/>
    PROPRIETOR
    </td></tr>
    <tr><td style="text-align:left;border-top:0.1px solid white">OFFICE ADDRESS<br/>' . $firmName . '
<br/>' . $firmAddress . '<br/>E mail : ' . $firmEmail . '<br/>Phone: ' . $firmContact . '
    </td></tr>
    </table>';

//echo $html;
// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

//Close and output PDF document
//$pdf->Output('invoice.pdf', 'I');
$otp = rand(100000, 999999);
//Close and output PDF document
//$pdf->Output('Final Invoice.pdf', 'I');

$pdf->Output(__DIR__ . '/uploads/' . $otp . 'invoice.pdf', 'F');
$body = '<html><body><p>Dear Sir / Mam,</p>'
        . '<p>'.$_POST["msgforClient"].'</p>'
        . '<p>Thanks,</p>'
        . '<p>'.$firmName.'</p>'
        . '<body></html>';

$email = new PHPMailer();
$email->From = $firmEmail;
$email->FromName = $firmName;
$email->Subject = 'Invoice';
$email->Body = $body;
$email->IsHTML(true);
$email->AddAddress($customerEmail);
$email->addCC('puneqms@gmail.com');

$file_to_attach = __DIR__ . '/uploads/' . $otp . 'invoice.pdf';

$email->AddAttachment($file_to_attach, 'NameOfFile.pdf');
//die;
$email->Send();
$invoice = "1";

$invoicesent = "UPDATE `invoice_details` SET `inv_sent_count`= inv_sent_count + 1 WHERE `invoice_id` = :id";
$statement = $db->prepare($invoicesent);
$statement->bindParam(':id', $_POST['invoiceid']);
$statement->execute();

header("Location: invoiceList.php?mailsend=" . $invoice);
//============================================================+
// END OF FILE
//============================================================+


