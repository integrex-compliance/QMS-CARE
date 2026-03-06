<?php

//============================================================+
// File name   : example_006.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 006 for TCPDF class
//               WriteHTML and RTL support
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
 * @abstract TCPDF - Example: WriteHTML and RTL support
 * @author Nicola Asuni
 * @since 2008-03-04
 */
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
include_once('dbConnect.php');
// create new PDF document
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
$quotation_no = "";
$quotation_date = "";
$firmName = "";
$firmLogo = "";
$firmAddress = "";
$customerAddress = "";
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
$statement->bindParam(':invoiceid', $_POST['invoiceId']);
$statement->bindParam(':invoiceno', $_POST['invoiceNo']);
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

$newInvoice = $invoiceDetails['invoice_no'];
$quotation_date = date("d-m-Y", strtotime($invoiceDetails['invoice_date']));
$net_price = $invoiceDetails['net_price'];
$service_tax = $invoiceDetails['service_tax'];
$tax_type = $invoiceDetails['tax_type'];

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
$statement->bindParam(':id', $_POST['invoiceId']);
$statement->execute();
$serviceArray = $statement->fetchAll();

$invoiceServicesAmt = "SELECT service_amount FROM invoice_services WHERE invoice_ids = :id";
$statement = $db->prepare($invoiceServicesAmt);
$statement->bindParam(':id', $_POST['invoiceId']);
$statement->execute();
$serviceAmtArray = $statement->fetchAll();

// ---------------------------------------------------------
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetFont('dejavusans', '', 10);
$pdf->SetMargins(15, 5, 5);
$pdf->AddPage();

$invoiceHeading = ($invoiceDetails['firm_id'] == 34) ? 'TAX INVOICE' : 'INVOICE';

// Header
$html = '<table cellpadding="4" cellspacing="0" style="margin:0; padding:0;">
<tr>
    <td width="20%" style="text-align:right; vertical-align:top; padding:0; margin:0;">
        <img src="' . $firmLogo . '" alt="logo" height="110" width="110" style="display:block; margin:0; padding:0;">
    </td>
    <td width="80%" style="text-align:center; font-size:1.4em; vertical-align:middle; padding:0; margin:0; line-height:1.2;">
        <b>' . $firmName . '</b><br>
        <span style="font-size:0.6em;"><b>' . $firmAddress . '</b></span>
    </td>
</tr>
</table>
<h2 style="text-align:center">' . $invoiceHeading . '</h2>';

$pdf->writeHTML($html, true, false, true, false, '');

// Service Rows
$serviceRow = '';
$serviceDesc = explode(',', $invoiceDetails['svc_desc']);
$serviceAmount = explode(',', $invoiceDetails['svc_amount']);
for ($i = 0; $i < count($serviceDesc); $i++) {
    if ($serviceDesc[$i] != "") {
        $serviceAmountValue = number_format((float) $serviceAmount[$i], 2, '.', '');
        $serviceRow .= '<tr><td>' . ($i + 1) . '</td><td>' . $serviceDesc[$i] . '</td><td>' . convertcash($serviceAmountValue, "") . '/-</td></tr>';
    }
}

// Tax logic
$taxHtml = '';
if (!empty($tax_type) && $ST > 0) {
    if ($tax_type === "IGST") {
        $taxHtml = '<tr><td colspan="2" style="text-align:right">IGST @ 18%</td><td>' . convertcash($ST, "") . '/-</td></tr>';
    } elseif ($tax_type === "CGST_SGST") {
        $halfTax = $ST / 2;
        $taxHtml .= '<tr><td colspan="2" style="text-align:right">CGST @ 9%</td><td>' . convertcash($halfTax, "") . '/-</td></tr>';
        $taxHtml .= '<tr><td colspan="2" style="text-align:right">SGST @ 9%</td><td>' . convertcash($halfTax, "") . '/-</td></tr>';
    }
}

// Main Table
$html = '
<table border="1" cellspacing="0" cellpadding="4">
<tr>
    <td height="100">TO - <br/> 
        <b>' . $customName[0] . '</b><br/>' . $customerAddress . 
        ($invoiceDetails['firm_id'] == 34 ? '<br/>GSTIN: ' . $customergst : '') . 
    '</td>
    <td style="vertical-align:top; position:relative; padding:0;"><div style="padding:10px;"> INVOICE NO: ' . $abbrevation . '/' . $newInvoice . '</div><hr style="border:1px solid #000; margin:0; width:100%;"/><div style="position:absolute; bottom:5px; left:10px;">DATE: ' . date("d.m.Y", strtotime($quotation_date)) . '</div></td>
</tr>
<tr><td colspan="3"><b>' . 
    ($invoiceDetails['firm_id'] == 34 
        ? 'HSN / SAC</b> : 998311' 
        : 'CONSULTATION FEES</b> : Management System Services') . 
'</td></tr>
</table>

<table border="1" cellspacing="0" cellpadding="4">
<tr><th width="5%">Sr.</th><th width="60%">Description</th><th width="35%">Amount in Rs.</th></tr>
' . $serviceRow . '
<tr><td colspan="2" style="text-align:right">Net Price</td><td>' . convertcash($net_price, "") . '/-</td></tr>
' . $taxHtml . '
<tr><td colspan="2" style="text-align:right"><b>Final Amount</b></td><td>' . convertcash($total_amt, "") . '/-</td></tr>
<tr><td colspan="3"><b>Amount In Word : ' . convertToWord($total_amt) . '</b></td></tr>
<tr>
    <td colspan="3">
        <b>NOTE-</b>
        <ol>
            <li>Please issue Payment in favor of ' . $firmName . '</li>
            <li>' . $firmName . ' is not accepting Payments by Cash</li>
            <li>PAN Number : ' . $firm_pan_no . '</li>
        </ol>
    </td>
</tr>
</table>

<table border="1" cellspacing="0" cellpadding="6" width="100%">
<tr>
    <td width="50%" valign="top"><b>BANK ACCOUNT DETAILS:</b><br/>
        Bank Name: ' . $bank_name . ',<br/>
        Address: ' . $bank_address . '<br/>
        Current account No.- ' . $bank_account_no . '<br/>
        IFSC code – ' . $ifsc_code . '
    </td>
    <td width="50%" align="right" valign="top">
        <img src="' . $firmSign . '" alt="sign" height="60" width="210"><br/>
        Authorized Signatory
    </td>
</tr>
</table><br/>';

// Extra Services for firm_id = 34
if ($invoiceDetails['firm_id'] == 34) {
    $html .= '<br><br>
<table width="100%" style="font-size:10pt; font-family:dejavusans;">
<tr>
    <td colspan="2" align="center" style="color:#0066cc; font-weight:bold; font-style:italic; font-size:12pt;">
        “ <u>WE ARE PROVIDING FOLLOWING SERVICES AND COMPLIANCES</u> ”
    </td>
</tr>
<tr>
    <td width="50%" valign="top">
        <ul style="margin:0; padding-left:15px;">
            <li>IATF 16949 Certification</li>
            <li>ISO 9001 Certification</li>
            <li>ISO 14001 Certification</li>
            <li>ISO 45001 Certification</li>
            <li>AS 9100 Certification</li>
        </ul>
    </td>
    <td width="50%" valign="top">
        <ul style="margin:0; padding-left:15px;">
            <li>Factory License compliance as Per Act 1948</li>
            <li>Management System software</li>
            <li>FSMS Certification</li>
            <li>HACCP Certification</li>
            <li>CE Mark</li>
        </ul>
    </td>
</tr>
</table>';
}

// Output
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('invoice.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

function convertcash($num, $currency) {
    if ($num === "" || $num === null) {
        $num = 0;
    }
    $normalized = number_format((float) $num, 2, '.', '');
    $parts = explode('.', $normalized);
    $intPart = $parts[0];
    $decPart = $parts[1];
    $negative = false;

    if (strpos($intPart, '-') === 0) {
        $negative = true;
        $intPart = substr($intPart, 1);
    }

    if (strlen($intPart) > 3) {
        $lastthree = substr($intPart, strlen($intPart) - 3, strlen($intPart));
        $restunits = substr($intPart, 0, strlen($intPart) - 3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $explrestunits = "";
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            $explrestunits .= (int) $expunit[$i] . ",";
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $intPart;
    }

    if ($negative) {
        $thecash = '-' . $thecash;
    }

    return $currency . $thecash . '.' . $decPart;
}

function convertToWord($number) {
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
    $formatted = number_format((float) $number, 2, '.', '');
    $parts = explode('.', $formatted);
    $no = (int) $parts[0];
    $point = (int) $parts[1];

    if ($no == 0) {
        $result = 'Zero';
    } else {
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str[] = ($number < 21) ? $words[$number] .
                        " " . $digits[$counter] . $plural . " " . $hundred :
                        $words[floor($number / 10) * 10]
                        . " " . $words[$number % 10] . " "
                        . $digits[$counter] . $plural . " " . $hundred;
            } else {
                $str[] = null;
            }
        }
        $str = array_reverse($str);
        $result = trim(implode('', $str));
    }

    $paiseWords = '';
    if ($point > 0) {
        if ($point < 21) {
            $paiseWords = $words[$point];
        } else {
            $paiseWords = trim($words[floor($point / 10) * 10] . " " . $words[$point % 10]);
        }
    }

    $output = $result . " Rupees";
    if ($paiseWords !== '') {
        $output .= " and " . $paiseWords . " Paise";
    }
    return trim($output) . " Only";
}
