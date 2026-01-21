<?php
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

//get variables from get/post to fill the details

$customerId = ""; //based on this we need to take customer information
$customerName = "";
$customerAddress = "";
$quotation_no = "";
$quotation_date = "";
$firmName = "";
$firmLogo = "";
$firmAddress = "";
$customerAddress = "";
$firmSign = "";

//echo $_POST['invoiceServiceAmt'];
//$svc = $_POST['invoiceService'];
//for($i=0; $i < count($svc) ; $i++){
//    echo $svc[$i];
//}
//echo '<br>'+ $_POST['invoiceServiceAmt'];die;
//get variables from get/post to fill the details
if (isset($_POST['invoiceCustomerId'])) {
    $customerId = $_POST['invoiceCustomerId'];
} else if (isset($_POST['invoiceEditCustomerId']) && !empty($_POST['invoiceEditCustomerId'])) {
    $customerId = $_POST['invoiceEditCustomerId'];
}
$customerDetails = "SELECT * FROM customer_details WHERE customer_id = :customer_id";
$statement = $db->prepare($customerDetails);
$statement->bindParam(":customer_id", $customerId);
$statement->execute();
$customer = $statement->fetch();
$customerName = $customer['company_name'];
$customName = explode("(", $customerName);
$customerAddress = $customer['company_address'];
$customergst = $customer['gst_number'];

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
$ST = "";
$serviceRow = '';

if (isset($_POST['invoiceDt'])) {
    $quotation_date = $_POST['invoiceDt'];
} else if (isset($_POST['invoiceDtEdit'])) {
    $quotation_date = $_POST['invoiceDtEdit'];
}

if (isset($_POST['invoiceNo'])) {
    $quotation_no = $_POST['invoiceNo'];
}

$net_price = "";
$service_tax = "";
$total_amt = 0;

if (isset($_POST['netPrice'])) {
    $net_price = floor($_POST['netPrice']);
}
if (isset($_POST['netPriceEdit'])) {
    $net_price = floor($_POST['netPriceEdit']);
}
if (isset($_POST['taxType'])) {
    $tax_type = $_POST['taxType'];
}
if (isset($_POST['taxTypeEdit'])) {
    $tax_type = $_POST['taxTypeEdit'];
}
if (isset($_POST['serviceTax'])) {
    $service_tax = floor($_POST['serviceTax']);
    $total_amt = $net_price + $service_tax;
}
if (isset($_POST['serviceTaxEdit'])) {
    $service_tax = floor($_POST['serviceTaxEdit']);
    $total_amt = $net_price + $service_tax;
}

//echo $_POST['invoiceFirmId'];
if (isset($_POST['invoiceconsultingFirmName']) && !empty($_POST['invoiceconsultingFirmName']))
    $firm_id = $_POST['invoiceconsultingFirmName'];
if (isset($_POST['invoiceEditFirmId']) && !empty($_POST['invoiceEditFirmId']))
    $firm_id = $_POST['invoiceEditFirmId'];

$sql = "SELECT consulting_firm_id, firm_name, firm_email, firm_logo, firm_pan_no, bank_name, bank_address, bank_account_no, ifsc_code, micr_no,firm_address, firm_contact, firm_abbrevation, invoice_no,firmSignature FROM consulting_firm WHERE consulting_firm_id = :firm_id";
$statement = $db->prepare($sql);
$statement->bindParam(":firm_id", $firm_id);
$statement->execute();
$firmDetails = $statement->fetch();

$firmName = $firmDetails['firm_name'];
$firmLogo = $firmDetails['firm_logo'];
$firmEmail = $firmDetails['firm_email'];
$firm_pan_no = $firmDetails['firm_pan_no'];
$bank_name = $firmDetails['bank_name'];
$bank_address = $firmDetails['bank_address'];
$bank_account_no = $firmDetails['bank_account_no'];
$ifsc_code = $firmDetails['ifsc_code'];
$micr_no = $firmDetails['micr_no'];
$firmAddress = $firmDetails['firm_address'];
$firmContact = $firmDetails['firm_contact'];
$invoice = $firmDetails['invoice_no'];
$abbrevation = $firmDetails['firm_abbrevation'];
$firmSign = $firmDetails['firmSignature'];
$customerName = $customer['company_name'];
$customerAddress = $customer['company_address'];

if (isset($_POST['invoiceEditId']) && !empty($_POST['invoiceEditId'])) {

    if (isset($_POST['invoiceServiceEdit']) && isset($_POST['invoiceServiceEdit'])) {
        $serviceArray = $_POST['invoiceServiceEdit'];
        $serviceAmtArray = $_POST['invoiceServiceAmtEdit'];
        $services = "";
        $svcamount = "";
        $count = 0;
        foreach ($serviceArray as $service) {
            if ($serviceArray[$count] != "") {
                $services = $services . $serviceArray[$count] . ',';
            }
            if ($serviceAmtArray[$count] != "") {
                $svcamount = $svcamount . $serviceAmtArray[$count] . ',';

                $serviceRow = $serviceRow . '<tr><td>' . ($count + 1) . '</td><td>' . $serviceArray[$count] . '</td><td>' . $serviceAmtArray[$count] . '/-</td></tr>';
                $count = $count + 1;
            }
        }
    }

    $invoiceid = $_POST['invoiceEditId'];
    $newInvoice = $_POST['invoiceEditNo'];
    $invoiceUpdate = "UPDATE invoice_details SET customer_id = :customerId, customer_name = :customerName, invoice_date = :invoicedate, net_price = :netprice, service_tax = :servicetax, total_amount = :amount, svc_desc = :invsvc, svc_amount = :svcamt,tax_type = :taxtype  WHERE invoice_id = :id";
    $statement = $db->prepare($invoiceUpdate);
    $statement->bindParam(':id', $_POST['invoiceEditId']);
    $statement->bindParam(':customerId', $customerId);
    $statement->bindParam(':customerName', $customerName);
    $statement->bindParam(':invoicedate', date("Y-m-d", strtotime($quotation_date)));
    $statement->bindParam(':netprice', $net_price);
    $statement->bindParam(':servicetax', $service_tax);
    $statement->bindParam(':invsvc', $services);
    $statement->bindParam(':svcamt', $svcamount);
    $statement->bindParam(':amount', $total_amt);
    $statement->bindParam(':taxtype', $_POST['taxTypeEdit']); // NEW LINE
    $statement->execute();

    $deleteInvoiceService = "DELETE FROM invoice_services WHERE invoice_ids = :id";
    $statement = $db->prepare($deleteInvoiceService);
    $statement->bindParam(':id', $_POST['invoiceId']);
    $statement->execute();
    if ($service_tax == 0) {
        $ST = "NA";
    } else {
        $ST = $service_tax;
    }

//    if (isset($_POST['invoiceServiceEdit']) && isset($_POST['invoiceServiceEdit'])) {
//        $serviceArray = $_POST['invoiceServiceEdit'];
//        $serviceAmtArray = $_POST['invoiceServiceAmtEdit'];
//        $count = 0;
//        foreach ($serviceArray as $service) {
//            if ($serviceArray[$count] != "") {
//                $invoiceServices = "INSERT INTO invoice_services (invoice_ids, service_desc, service_amount) VALUES (:id,:service,:amount)";
//                $statement = $db->prepare($invoiceServices);
//                $statement->bindParam(':id', $invoiceid);
//                $statement->bindParam(':service', $serviceArray[$count]);
//                $statement->bindParam(':amount', $serviceAmtArray[$count]);
//                $statement->execute();
//            }
//        }
//    }
} else {
    $services = "";
    $svcamount = "";

    $newInvoice = $invoice + 1;

    $incrementInvoice = "UPDATE consulting_firm SET invoice_no = :number WHERE consulting_firm_id = :id";
    $statement = $db->prepare($incrementInvoice);
    $statement->bindParam(':number', $newInvoice);
    $statement->bindParam(':id', $firm_id);
    $statement->execute();

    if (isset($_POST['invoiceService']) && isset($_POST['invoiceService'])) {
        $serviceArray = $_POST['invoiceService'];
        $serviceAmtArray = $_POST['invoiceServiceAmt'];
        $count = 0;
        foreach ($serviceArray as $service) {
            if ($serviceArray[$count] != "") {
                $services = $services . $serviceArray[$count] . ',';
            }
            if ($serviceAmtArray[$count] != "") {
                $svcamount = $svcamount . $serviceAmtArray[$count] . ',';

                $serviceRow = $serviceRow . '<tr><td>' . ($count + 1) . '</td><td>' . $serviceArray[$count] . '</td><td>' . $serviceAmtArray[$count] . '/-</td></tr>';
                $count = $count + 1;
            }
        }
    }

    $invoiceDetails = "INSERT INTO invoice_details (firm_id, customer_id, customer_name, invoice_no, invoice_date, net_price, service_tax, total_amount, svc_desc, svc_amount, tax_type) VALUES "
            . "(:firmid, :customer_id, :customer_name, :invoiceno, :invoicedate, :netprice, :servicetax, :amount, :invsvc, :svcamt, :taxtype)";
//        $invoiceDetails = "INSERT INTO invoice_details (firm_id,customer_id, firm_name, firm_address, firm_logo, invoice_no, invoice_date, pan_no,bank_name, bank_address, account_no, ifsc_code, micr_no, mail_id, customer_name, customer_address, contact_no, net_price, service_tax, total_amount,firm_sign, firm_abbrevation) "
//                . "VALUES (:firmid,:customer_id,:firmname,:firmaddress,:firmlogo,:invoiceno,:invoicedate,:panno,:bankname,:bankaddress,:accno,:ifsccode,:micrno,:mailid,:customername,:customeraddress,:contactno,:netprice,:servicetax,:amount,:sign,:abbrevation);";
    $statement = $db->prepare($invoiceDetails);
    $statement->bindParam(':firmid', $firm_id);
    $statement->bindParam(":customer_id", $_POST['invoiceCustomerId']);
    $statement->bindParam(":customer_name", $customerName);
    $statement->bindParam(':invoiceno', $newInvoice);
    $statement->bindParam(':invoicedate', date("Y-m-d", strtotime($quotation_date)));
    $statement->bindParam(':netprice', $net_price);
    $statement->bindParam(':servicetax', $service_tax);
    $statement->bindParam(':invsvc', $services);
    $statement->bindParam(':svcamt', $svcamount);
    $statement->bindParam(':amount', $total_amt);
    $statement->bindParam(':taxtype', $_POST['taxType']); // NEW LINE
    $statement->execute();

    $sql = "SELECT max(LAST_INSERT_ID(invoice_id)) as id From invoice_details";
    $statement = $db->prepare($sql);
    $statement->execute();
    $row = $statement->fetch();
    $invoiceid = $row['id'];

    if ($service_tax == 0) {
        $ST = "NA";
    } else {
        $ST = $service_tax;
    }
}

$datec = date("01-07-2017");
$d1 = strtotime($quotation_date);
$d2 = strtotime($datec);
if ($d1 > $d2) {
    $gstdec = "<b>GST is not applicable to " . $firmName . " as turnover of firm is less than 20 lac.</b>";
} else {
    $gstdec = "<b>Service tax is not payble</b>";
}
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set font
$pdf->SetFont('dejavusans', '', 10);

$pdf->SetMargins(15, 5, 5); // Left:10mm, Top:5mm, Right:10mm
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
//echo $firmName;
// create some HTML content
$invoiceHeading = ($firm_id == 34) ? 'TAX INVOICE' : 'INVOICE';

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

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


// create some HTML content
$subtable = '<table border="1" cellspacing="0" cellpadding="4"><tr><td>a</td><td>b</td></tr><tr><td>c</td><td>d</td></tr></table>';

//$serviceRow = '';
//if (isset($_POST['invoiceService']) && isset($_POST['invoiceServiceAmt'])) {
//    $serviceArray = $_POST['invoiceService'];
//    $serviceAmtArray = $_POST['invoiceServiceAmt'];
//    $count = 0;
//    
//        $serviceRow = $serviceRow . '<tr><td>' . ($count + 1) . '</td><td>' . $serviceArray . '</td><td>' . convertcash(floor($serviceAmtArray),"") . '</td></tr>';
//    
//}
//
//if (isset($_POST['invoiceServiceEdit']) && isset($_POST['invoiceServiceAmtEdit'])) {
//    $serviceArray = $_POST['invoiceServiceEdit'];
//    $serviceAmtArray = $_POST['invoiceServiceAmtEdit'];
//    $count = 0;
//
//    $serviceRow = $serviceRow . '<tr><td>' . ($count + 1) . '</td><td>' . $serviceArray . '</td><td>' . convertcash(floor($serviceAmtArray), "") . '</td></tr>';
//}
//echo $serviceRow;
$taxHtml = '';
if (!empty($tax_type) && $ST > 0) {
    if ($tax_type === "IGST") {
        $taxHtml = '<tr><td colspan="2" style="text-align:right">IGST @ 18%</td><td>' . convertcash($ST, "") . '/-</td></tr>';
    } elseif ($tax_type === "CGST_SGST") {
        $halfTax = $ST / 2;
        $taxHtml = '<tr><td colspan="2" style="text-align:right">CGST @ 9%</td><td>' . convertcash($halfTax, "") . '/-</td></tr>';
        $taxHtml .= '<tr><td colspan="2" style="text-align:right">SGST @ 9%</td><td>' . convertcash($halfTax, "") . '/-</td></tr>';
    }
}

$html = '
<table border="1" cellspacing="0" cellpadding="4">
	
	<tr >
		<td height="100">TO - <br/> 
<b>' . $customName[0] . '</b><br/>' . $customerAddress . 
($firm_id == 34 ? '<br/>GSTIN: ' . $customergst : '') . 
'</td>		
		<td style="vertical-align:top; position:relative; padding:0;"><div style="padding:10px;"> INVOICE NO: ' . $abbrevation . '/' . $newInvoice . '</div><hr style="border:1px solid #000; margin:0; width:100%;"/><div style="position:absolute; bottom:5px; left:10px;">DATE: ' . $quotation_date . '</div></td>
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
        <b>NOTE-</b><ol>
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
        <img src="' . $firmSign . '" alt="logo" height="60" width="210"><br/>
        Authorized Signatory
    </td>
</tr>
</table>

<br/>';

if ($firm_id == 34) {
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


// Output the HTML content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

//echo $html;
// output the HTML content


//Close and output PDF document
$pdf->Output('invoice.pdf', 'I');

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

    return $currency . $thecash; // writes the final format where $currency is the currency symbol. 
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
