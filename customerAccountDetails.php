<div id="wrapper" class="active">
    <?php
    include_once './header.php';
    include_once './dbConnect.php';
    include './customerScripting.php';

    if (isset($_GET['customerid']) && !empty($_GET['customerid'])) {
        try {
            $customerDetails = "SELECT * FROM customer_details WHERE customer_id = :customer_id";
            $statement = $db->prepare($customerDetails);
            $statement->bindParam(":customer_id", $_GET['customerid']);
            $statement->execute();
            $customer = $statement->fetch();

            $customerServices = "SELECT * FROM services WHERE customer_id = :customer_id";
            $statement = $db->prepare($customerServices);
            $statement->bindParam(":customer_id", $_GET['customerid']);
            $statement->execute();
            $servicesList = $statement->fetchAll();

            $service = "SELECT service_id, customer_id, service_type, cert_body, consulting_firm, consultant, audit_type, auditor, audit_date, created_at, add_service_to FROM services WHERE customer_id = :customerid";
            $statement = $db->prepare($service);
            $statement->bindParam(':customerid', $_GET['customerid']);
            $statement->execute();
            $service1 = $statement->fetch();
            $serviceId = $service1['service_id'];

            $certBodyCount = "SELECT count(*) as totalcb FROM certification_body WHERE service_id = :serviceid";
            $statement = $db->prepare($certBodyCount);
            $statement->bindParam(':serviceid', $serviceId);
            $statement->execute();
            $cbCount = $statement->fetch();

            $certBodyList = "SELECT * FROM certification_body WHERE service_id = :serviceid";
            $statement = $db->prepare($certBodyList);
            $statement->bindParam(':serviceid', $serviceId);
            $statement->execute();
            $certList = $statement->fetchAll();

            $consultingListCount = "SELECT count(*) as totalconsulting FROM consulting  WHERE service_id = :serviceid";
            $statement = $db->prepare($consultingListCount);
            $statement->bindParam(':serviceid', $serviceId);
            $statement->execute();
            $consultingsListCount = $statement->fetch();

//            echo $consultingsListCount['totalconsulting'];
            $consultList = "SELECT * FROM consulting  WHERE service_id = :serviceid";
            $statement = $db->prepare($consultList);
            $statement->bindParam(':serviceid', $serviceId);
            $statement->execute();
            $consultingList = $statement->fetchAll();
                // Service-wise Invoices and Financials
                $svcInvSql = "SELECT inv.*, cf.firm_name, cf.firm_abbrevation FROM invoice_details inv LEFT JOIN consulting_firm cf ON inv.firm_id = cf.consulting_firm_id WHERE inv.customer_id = :customer_id AND inv.service_id = :service_id AND inv.is_deleted = 'No' ORDER BY inv.invoice_date DESC, inv.invoice_id DESC";
                $svcInvStmt = $db->prepare($svcInvSql);
                $svcInvStmt->bindParam(':customer_id', $_GET['customerid']);
                $svcInvStmt->bindParam(':service_id', $serviceId);
                $svcInvStmt->execute();
                $currentServiceInvoices = $svcInvStmt->fetchAll(PDO::FETCH_ASSOC);
                $svcTotalInvoiced = 0;
                foreach ($currentServiceInvoices as $invItem) {
                    $svcTotalInvoiced += (float)$invItem['total_amount'];
                }
                // Separate CB Totals
                $cbTotalDecided = 0;
                $cbTotalReceived = 0;
                if (!empty($certList)) {
                    foreach ($certList as $cbItem) {
                        if (isset($cbItem['amount_decided']) && is_numeric($cbItem['amount_decided'])) {
                            $cbTotalDecided += (float)$cbItem['amount_decided'];
                        }
                        if (isset($cbItem['payment_received']) && is_numeric($cbItem['payment_received'])) {
                            $cbTotalReceived += (float)$cbItem['payment_received'];
                        }
                    }
                }
                $cbTotalBalance = max(0, $cbTotalDecided - $cbTotalReceived);

                // Separate Consulting Totals
                $consultTotalDecided = 0;
                $consultTotalReceived = 0;
                if (!empty($consultingList)) {
                    foreach ($consultingList as $conItem) {
                        if (isset($conItem['consulting_amount_decided']) && is_numeric($conItem['consulting_amount_decided'])) {
                            $consultTotalDecided += (float)$conItem['consulting_amount_decided'];
                        }
                        if (isset($conItem['consulting_payment_received']) && is_numeric($conItem['consulting_payment_received'])) {
                            $consultTotalReceived += (float)$conItem['consulting_payment_received'];
                        }
                    }
                }
                $consultInvoiceBalance = max(0, $svcTotalInvoiced - $consultTotalReceived);

                $svcTotalDecided = $cbTotalDecided + $consultTotalDecided;
                $svcTotalReceived = $cbTotalReceived + $consultTotalReceived;

            $addServicesTo = "SELECT * FROM add_to_service";
            $statement = $db->prepare($addServicesTo);
            $statement->execute();
            $addTODo = $statement->fetchAll();

            $allServiceType = "SELECT * FROM service_type";
            $statement = $db->prepare($allServiceType);
            $statement->execute();
            $allServices = $statement->fetchAll();

            $allCertBody = "SELECT * FROM cert_body";
            $statement = $db->prepare($allCertBody);
            $statement->execute();
            $allCert = $statement->fetchAll();

            $allConsultFirms = "SELECT * FROM consulting_firm";
            $statement = $db->prepare($allConsultFirms);
            $statement->execute();
            $consultFirm = $statement->fetchAll();

            $allConsultant = "SELECT * FROM consultant";
            $statement = $db->prepare($allConsultant);
            $statement->execute();
            $consultant = $statement->fetchAll();

            $allAuditor = "SELECT * FROM auditors";
            $statement = $db->prepare($allAuditor);
            $statement->execute();
            $allAuditorList = $statement->fetchAll();

            $allHandler = "SELECT * FROM handler";
            $statement = $db->prepare($allHandler);
            $statement->execute();
            $handlerList = $statement->fetchAll();

            $attachemnts = "SELECT * FROM `document_upload` where cstomer_id = :customer and is_deleted = 'N'";
            $statement = $db->prepare($attachemnts);
            $statement->bindParam(':customer', $_GET["customerid"]);
            $statement->execute();
            $attachemntsList = $statement->fetchAll();

            $invoices = "SELECT inv.invoice_id, inv.firm_id, inv.customer_id, inv.service_id, inv.customer_name, inv.invoice_no, inv.invoice_date, inv.total_amount, inv.svc_desc, cf.firm_name, cf.firm_abbrevation, s.service_type AS linked_service_type FROM invoice_details inv LEFT JOIN consulting_firm cf ON inv.firm_id = cf.consulting_firm_id LEFT JOIN services s ON inv.service_id = s.service_id WHERE inv.customer_id = :customer AND inv.is_deleted = 'No' ORDER BY inv.invoice_date DESC, inv.invoice_id DESC";
            $statement = $db->prepare($invoices);
            $statement->bindParam(':customer', $_GET["customerid"]);
            $statement->execute();
            $giveninvoices = $statement->fetchAll();
        } catch (Exception $ex) {
            echo $ex;
        }
    }
    ?>

    <style>
        td {
            .table > tbody > tr > td,
            .table > tbody > tr > th,
            .table > tfoot > tr > td,
            .table > tfoot > tr > th,
            .table > thead > tr > td,
            .table > thead > tr > th {
                padding: 2px;
                line-height: 1.0;
            }
        }
    </style>

    <style>
        .trash { color:rgb(209, 91, 71); }
        .flag { color:rgb(248, 148, 6); }
        .panel-body { padding:0px; }
        .panel-footer .pagination { margin: 0; }
        .panel .glyphicon,.list-group-item .glyphicon { margin-right:5px; }
        .panel-body .radio, .checkbox { display:inline-block;margin:0px; }
        .panel-body input[type=checkbox]:checked + label { text-decoration: line-through;color: rgb(128, 144, 160); }
        .list-group-item:hover, a.list-group-item:focus {text-decoration: none;background-color: rgb(245, 245, 245);}
        .list-group { margin-bottom:0px; }
    </style>

    <div class="modal fade" id="ducumentupload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Attachment</h4>
                </div>
                <form action="uploadAttachemnts.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <ul class="list-group">
                                <?php
                                if ($attachemntsList) {
                                    foreach ($attachemntsList as $list) {
                                        $ext = pathinfo($list["upload_path"], PATHINFO_EXTENSION);
                                        ?>
                                        <li class="list-group-item attachment-list<?php echo $list["id"] ?>">
                                            <div class="checkbox">
                                                <?php if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "gif") { ?>
                                                    <img src="<?php echo $list["upload_path"] ?>" alt="Smiley face" height="42" width="42">
                                                <?php } else if ($ext == "pdf") { ?>
                                                    <span class="fa fa-file-pdf-o" style="font-size: 42px; color: red;"></span>
                                                <?php } else if ($ext == "doc" || $ext == "docx") { ?>
                                                    <span class="fa fa-file-word-o" style="font-size: 42px; color: #008ada;"></span>
                                                <?php } else if ($ext == "xls" || $ext == "xlsx") { ?>
                                                    <span class="fa fa-file-excel-o" style="font-size: 42px; color: green;"></span>
                                                <?php } ?>
        <!--<input type="checkbox" id="checkbox" />-->
                                                <label for="checkbox">
                                                    <?php echo $list["description"]; ?>
                                                </label>
                                            </div>
                                            <div class="pull-right action-buttons">
                                                <a href="<?php echo $list["upload_path"] ?>" download title="Download Attachment"><span class="glyphicon glyphicon-download"></span></a>
                                                <a class="delteAttachment" data-id="<?php echo $list["id"] ?>" data-customer="<?php echo $_GET["customerid"] ?>"><span class="glyphicon glyphicon-trash" title="Delete Attachment"></span></a>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-12 form-group float-label-control">
                                <!--<label for="">Logo</label>-->
                                <input type="file" name="customerAttachemnt" class="form-control customerAttachemnt" accept=".gif,.jpg,.jpeg,.png,.doc,.docx,.pdf,.xls,.xlsx" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" >
                                <div id="attachmentError" style="display: none">
                                    <small><span id="attachmentErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="">
                            <div class="col-sm-12 form-group float-label-control">
                                <label for="">Attachment Information</label>
                                <input type="text" class="attachmentinfo" name="attachmentinfo" placeholder="Attachment Information" >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" >
                                <div id="attachmentInfoError" style="display: none">
                                    <small><span id="attachmentInfoErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="customerid" class="customerid" value="<?php echo $_GET["customerid"] ?>">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info saveattachemtninfo">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="page-content-wrapper">
        <div class="page-content inset">
            <div class="">
                <div class="row" style="">
                    <div class="top_qms_page">
                        <div class="">
                            <div class="row">
                                <div class="col-sm-12 qms_customer_name" style="position: fixed; z-index: 1;">
                                    <span><i class="fa fa-building"></i></span>
                                    <span><?php echo $customer['company_name']; ?></span>
                                    <?php if (!empty($customer["priority"]) && $customer["priority"] == "High") { ?>
                                        <img class="img-responsive" alt="High Priority User" title="High Priority User" src="images/medium.png" style="background-color: transparent; height: 18px; display:inline" />
                                    <?php } else if (!empty($customer["priority"]) && $customer["priority"] == "Medium") { ?>
                                        <img class="img-responsive" alt="Medium Priority User" title="High Priority User" src="images/low.png" style="background-color: transparent; height: 16px; display:inline" />
                                    <?php } else if (!empty($customer["priority"]) && $customer["priority"] == "Low") { ?>
                                        <img class="img-responsive" alt="Low Priority User" title="High Priority User" src="images/high.png" style="background-color: transparent; height: 15px; display:inline" />
                                    <?php } ?>
                                    <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing()) { ?>
                                        <a class="editCustomerDetails " style="color: #999; color: white; padding-left: 450px;" data-id="<?php echo $_GET['customerid'] ?>" title="Edit customer details"><span><i class="fa fa-edit"></i></span></a>
                                        <a class="deleteCustomerDetails" style="color: #999; color: white; padding-right: 10px;" data-id="<?php echo $_GET['customerid'] ?>" title="Delete customer details"><span><i class="fa fa-trash-o"></i></span></a>
                                        <a class="customerAttachemnts" style="color: #999; color: white; padding-right: 10px;" data-id="<?php echo $_GET['customerid'] ?>" title="Attach Documents"><span><i class="fa fa-link"></i></span></a>
                                    <?php } ?>                                        
                                </div>
                            </div>
                            <div class="row" style="padding-top: 50px;">
                                <div class="col-sm-6">
                                    <div class="row" style="padding:5px; padding-left:20px">
                                        <div class="col-xs-1">
                                            <span style="color: #428bca" title="Address"><i class="fa fa-map-marker fa-lg"></i></span>
                                        </div>
                                        <div class="col-xs-11">
                                            <span><?php echo $customer['company_address'] ?></span>
                                        </div>
                                    </div>
                                    <div class="row" style="padding:5px; padding-left:20px">
                                        <div class="col-xs-1">
                                            <span style="color: #428bca" title="Contact Person"><i class="fa fa-user "></i></span>
                                        </div>
                                        <div class="col-xs-5">
                                            <span><?php echo $customer['contact_person'] ?></span>
                                        </div>
                                        <div class="col-xs-1">
                                            <span style="color: #428bca"><i class="fa fa-phone"></i></span>
                                        </div>
                                        <div class="col-xs-5">
                                            <span><?php echo $customer['contact_no'] ?></span>
                                        </div>
                                    </div>
                                    <div class="row" style="padding:5px; padding-left:20px">
                                        <div class="col-xs-1">
                                            <span style="color: #428bca" title="Email Id"><i class="fa fa-envelope"></i></span>
                                        </div>
                                        <div class="col-xs-11">
                                            <span><?php echo $customer['contact_email'] ?></span>
                                        </div>
                                    </div>
                                    <?php if (!empty($customer['gst_number'])) { ?>
    <div class="row" style="padding:5px; padding-left:20px">
        <div class="col-xs-1">
            <span style="color: #428bca" title="GST Number"><i class="fa fa-credit-card"></i></span>
        </div>
        <div class="col-xs-11">
            <span><?php echo $customer['gst_number']; ?></span>
        </div>
    </div>
<?php } ?>
                                    <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                        <div class="row" style="padding:5px; padding-left:20px">
                                            <div class="col-xs-1">
                                                <span style="color: #428bca" title="Client referd by-"><i class="fa fa-share-square-o fa-lg"></i></span> </div>
                                            <div class="col-xs-11">
                                                <span><?php echo $customer['client_reference'] ?></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="row" style="padding:5px; padding-left:20px">
                                            <div class="col-xs-1">
                                                <span style="color: #428bca" title="G-Map"><i class="fa fa-globe fa-lg"></i></span> </div>
                                            <div class="col-xs-11">
                                                <span><?php echo $customer['company_location'] ?></span>
                                            </div>
                                        </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row" style="padding:5px; padding-left:20px">
                                        <div class="col-sm-10">
                                            <textarea name="add-notes" rows="5" cols="50" class=" form-group saveChanges customercomment lookslike-label" data-column="comments" id="add-notes" placeholder="Add Notes"><?php
                                                if ($customer['comments'] != "") {
                                                    $flag = 0;
                                                    foreach ($servicesList as $s) {
                                                        if ($s['service_type'] != "" && $s['service_type'] == "CNE")
                                                            $flag = 1;
                                                    }
                                                    if ($flag == 1) {
                                                        if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) {
                                                            echo $customer['comments'];
                                                        } else
                                                            echo"";
                                                    } else
                                                        echo $customer['comments'];
                                                }
                                                ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="color:slategray; margin:0px;  border:1px solid slategray" />
                            <?php
                            if (Utitlity::checkForUserFinance()) {
                                $customerAccess = "readonly";
                                $certDate = "";
                                $srvcDate = "";
                            } else {
                                $customerAccess = "";
                                $certDate = "certificationDate";
                                $srvcDate = "serviceDate";
                            }
                            ?>
                            <?php
                            if (stripos($customer['company_name'], "consl. ") === FALSE) {
                                if (!Utitlity::checkForUserConsultant()) {
                                    ?>
                                    <div class="row" style="padding:10px;">
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6 text-right" style="color:#428bca; font-weight:small"><span>CA/RC Date:</span></div>
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6">
                                            <input class="saveChanges dob certification-date lookslike-label <?php echo $certDate ?>" id="certificationDate" placeholder="Click to enter" data-column="certification_date" placeholder="dd/mm/yyyy" value="<?php if (isset($customer['certification_date'])) echo simpleDateConversion($customer['certification_date']) ?>" <?php echo $customerAccess ?> />
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6 text-right" style="color:#428bca; font-weight:small">Next Service Date:</div>
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6">
                                            <input  class="saveChanges dob  lookslike-label <?php echo $srvcDate ?>" id="serviceDate" placeholder="Click to enter" data-column="next_service_date" placeholder="dd/mm/yyyy" value="<?php if (isset($customer['next_service_date'])) echo simpleDateConversion($customer['next_service_date']) ?>" <?php echo $customerAccess ?>/>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6 text-right" style="color:#428bca; font-weight:small">Next Service Type</div>
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6">
                                            <input class="saveChanges  nextService lookslike-label" id="birthDate" placeholder="Click to enter" data-column="next_service_type" placeholder="Service Type" value="<?php if (isset($customer['next_service_type'])) echo $customer['next_service_type'] ?>" <?php echo $customerAccess ?>/>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!Utitlity::checkForUserConsultant()) { ?>
                        <div class="row" style="">
                            <div class="col-xs-2  text-center btn  qms_complete_btn invoiceBtn" data-customer="<?php echo $_GET['customerid'] ?>">
                                <span><i class="fa fa-inr "> Invoice</i></span>
                            </div>
                            <div class="col-xs-2  text-center btn qms_complete_btn quotationBtn" data-customer="<?php echo $_GET['customerid'] ?>">
                                <span><i class="fa fa-quote-left "> Quotation</i></span>
                            </div>
                            <?php
                            if (Utitlity::checkForUserFinance()) {
                                $active = "";
                                $show = "disabled";
                            } else {
                                $active = "addNewService";
                                $show = "";
                            }
                            ?>
                            <div class="col-xs-2  text-center btn qms_complete_btn <?php echo $active ?> " <?php echo $show ?>>
                                <span><i class="fa fa-plus-square "> Add Service </i></span>
                            </div>
                            <div class="col-xs-6  text-center btn qms_complete_btn show-all-invoices" <?php echo $show ?>>
                                <span><i class="fa fa-inr"> Invoices Issued </i></span>
                            </div>
                        </div>
                        <div class="row hidden-row" style="display: none">
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-6">
                                <table class="table table-bordered table-hover table-sortable service_details" id="service_details">
                                    <thead>
                                        <tr>
                                            <th class="" width="10%" style="padding: 2px">
                                                <small>Invoice No.</small>
                                            </th>
                                            <th class="" width="10%" style="padding: 2px">
                                                <small>Date</small>
                                            </th>
                                            <th class="" width="10%" style="padding: 2px">
                                                <small>Consulting Firm</small>
                                            </th>
                                            <th class="" width="10%" style="padding: 2px">
                                                <small>Amount</small>
                                            </th>
                                            <th class="" width="20%" style="padding: 2px">
                                                <small>Service</small>
                                            </th>
                                            <th class="" width="10%" style="padding: 2px">
                                                <small>V/E</small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="">
                                        <?php foreach ($giveninvoices as $invoice) { ?>
                                            <tr class="cb_subsection" id="cb_subsection_0" style="display: ">
                                                <td class="" style="padding: 0px;">
                                                    <?php echo $invoice["firm_abbrevation"] . '/' . $invoice["invoice_no"] ?>
                                                </td>
                                                <td class="" style="padding: 0px;">
                                                    <?php echo simpleDateConversion($invoice["invoice_date"]) ?>
                                                </td>
                                                <td class="" style="padding: 0px;">
                                                    <?php echo $invoice["firm_name"] ?>
                                                </td>
                                                <td class="" style="padding: 0px;">
                                                    <?php echo $invoice["total_amount"] ?>
                                                </td>
                                                <td class="" style="padding: 0px;">
                                                    <?php if (!empty($invoice["linked_service_type"])) { ?>
                                                        <span class="label label-primary" style="font-size:10px; margin-right:4px;"><?php echo htmlspecialchars($invoice["linked_service_type"]); ?></span>
                                                    <?php } ?>
                                                    <?php echo $invoice["svc_desc"] ?>
                                                </td>
                                                <td class="" style="padding: 0px;">
                                                    <a class="viewInvoice" data-id="<?php echo $invoice["invoice_id"] ?>" data-invoice="<?php echo $invoice["invoice_no"] ?>" data-customer="<?php echo $invoice["customer_id"] ?>"><i class="fa fa-file-pdf-o" style="padding:3px"></i></a>
                                                    <a class="editInvoice" data-id="<?php echo $invoice["invoice_id"] ?>" data-firm="<?php echo $invoice['firm_id'] ?>" data-customer="<?php echo $invoice["customer_id"] ?>" title="Edit envoice" style="padding: 1px 5px; "><i class="fa fa-pencil-square" style="padding:3px"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="servicebox-bunch">
            <!-- iterate through here-->
            <?php
            if (Utitlity::checkForUserConsultant()) {
                $fieldAccess = "readonly";
                $display = "none";
                $addClassTo = "";
                $addClassToConsulting = "";
            } else {
                $fieldAccess = "";
                $display = "";
                $addClassTo = "depositDate";
                $addClassToConsulting = "consultingDepositDate";
            }

            if (Utitlity::checkForUserFinance()) {
                $financeAccess = "readonly";
                $displayNone = "none";
                $addClassToAudit = "";
            } else {
                $financeAccess = "";
                $displayNone = "";
                $addClassToAudit = "audit-date";
            }

            if (Utitlity::checkForUserMarketing()) {
                $marketAccess = "readonly";
            } else {
                $marketAccess = "";
            }

            $serviceId = "";

            $service = "SELECT (select max(service_id) FROM services WHERE customer_id = :customerid) as maxid ,service_id, customer_id, service_type, cert_body, consulting_firm, consultant, audit_type, auditor, audit_date, created_at, add_service_to, handel_by, is_certified  FROM services WHERE customer_id = :customerid";
            $statement = $db->prepare($service);
            $statement->bindParam(':customerid', $_GET['customerid']);
            $statement->execute();
            $services = $statement->fetchAll();
            $totalServiceCount = count($services);
            $serviceNo = 1;

            $certificationCount = "SELECT (select max(service_id) FROM services WHERE customer_id = :customerid) as maxid ,service_id, customer_id, service_type, cert_body, consulting_firm, consultant, audit_type, auditor, audit_date, created_at, add_service_to, is_certified FROM services WHERE customer_id = :customerid and (service_type = 'CA' or service_type = 'SA-1' or service_type = 'SA-2' or service_type = 'RC')";
            $statement = $db->prepare($certificationCount);
            $statement->bindParam(':customerid', $_GET['customerid']);
            $statement->execute();
            $svcCount = $statement->fetchAll();
            $totalSvcCount = count($svcCount);

            foreach ($services as $service1) {
                $displayHtmlStr = "";
                $disable = "";
                if ($serviceNo == $totalServiceCount) {
                    
                } else {
                    $displayHtmlStr = "style='display:none'";
                }

                if ($serviceNo == $totalSvcCount && ($service1['service_type'] == "CA" || $service1['service_type'] == "SA-1" || $service1['service_type'] == "SA-2" || $service1['service_type'] == "RC")) {
                    
                } else {
                    $disable = 'disabled="disabled"';
                }

                $serviceNo = $serviceNo + 1;
                $serviceId = $service1['service_id'];

                $certBodyCount = "SELECT count(*) as totalcb FROM certification_body WHERE service_id = :serviceid";
                $statement = $db->prepare($certBodyCount);
                $statement->bindParam(':serviceid', $serviceId);
                $statement->execute();
                $cbCount = $statement->fetch();

                $certBodyList = "SELECT * FROM certification_body WHERE service_id = :serviceid";
                $statement = $db->prepare($certBodyList);
                $statement->bindParam(':serviceid', $serviceId);
                $statement->execute();
                $certList = $statement->fetchAll();

                $consultingListCount = "SELECT count(*) as totalconsulting FROM consulting  WHERE service_id = :serviceid";
                $statement = $db->prepare($consultingListCount);
                $statement->bindParam(':serviceid', $serviceId);
                $statement->execute();
                $consultingsListCount = $statement->fetch();

//            echo $consultingsListCount['totalconsulting'];

                $consultList = "SELECT * FROM consulting  WHERE service_id = :serviceid";
                $statement = $db->prepare($consultList);
                $statement->bindParam(':serviceid', $serviceId);
                $statement->execute();
                $consultingList = $statement->fetchAll();
                // Service-wise Invoices and Financials
                $svcInvSql = "SELECT inv.*, cf.firm_name, cf.firm_abbrevation FROM invoice_details inv LEFT JOIN consulting_firm cf ON inv.firm_id = cf.consulting_firm_id WHERE inv.customer_id = :customer_id AND inv.service_id = :service_id AND inv.is_deleted = 'No' ORDER BY inv.invoice_date DESC, inv.invoice_id DESC";
                $svcInvStmt = $db->prepare($svcInvSql);
                $svcInvStmt->bindParam(':customer_id', $_GET['customerid']);
                $svcInvStmt->bindParam(':service_id', $serviceId);
                $svcInvStmt->execute();
                $currentServiceInvoices = $svcInvStmt->fetchAll(PDO::FETCH_ASSOC);
                $svcTotalInvoiced = 0;
                foreach ($currentServiceInvoices as $invItem) {
                    $svcTotalInvoiced += (float)$invItem['total_amount'];
                }
                // Separate CB Totals
                $cbTotalDecided = 0;
                $cbTotalReceived = 0;
                if (!empty($certList)) {
                    foreach ($certList as $cbItem) {
                        if (isset($cbItem['amount_decided']) && is_numeric($cbItem['amount_decided'])) {
                            $cbTotalDecided += (float)$cbItem['amount_decided'];
                        }
                        if (isset($cbItem['payment_received']) && is_numeric($cbItem['payment_received'])) {
                            $cbTotalReceived += (float)$cbItem['payment_received'];
                        }
                    }
                }
                $cbTotalBalance = max(0, $cbTotalDecided - $cbTotalReceived);

                // Separate Consulting Totals
                $consultTotalDecided = 0;
                $consultTotalReceived = 0;
                if (!empty($consultingList)) {
                    foreach ($consultingList as $conItem) {
                        if (isset($conItem['consulting_amount_decided']) && is_numeric($conItem['consulting_amount_decided'])) {
                            $consultTotalDecided += (float)$conItem['consulting_amount_decided'];
                        }
                        if (isset($conItem['consulting_payment_received']) && is_numeric($conItem['consulting_payment_received'])) {
                            $consultTotalReceived += (float)$conItem['consulting_payment_received'];
                        }
                    }
                }
                $consultInvoiceBalance = max(0, $svcTotalInvoiced - $consultTotalReceived);

                $svcTotalDecided = $cbTotalDecided + $consultTotalDecided;
                $svcTotalReceived = $cbTotalReceived + $consultTotalReceived;
                ?>
                <?php
                if (Utitlity::checkForUserConsultant() && $service1['add_service_to'] == 'Under Working') {
                    $saveButton = "";
                }
//                else if (Utitlity::checkForUserMarketing() && $service1['add_service_to'] == "Under Marketing") {
//                    $saveButton = "";
//                } 
                else if (Utitlity::checkForUserFinance() && ($service1['maxid'] == $service1["service_id"])) {
                    $saveButton = "";
                } else if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing()) {
                    $saveButton = "";
                } else {
                    $saveButton = "None";
//                    continue;
                }
                ?>
                <div class="servicebox" style="display:<?php echo $saveButton ?> <?php //echo $srvcDisplay       ?>">
                    <div class="row" id="service-row<?php echo $serviceId; ?>" class="service-row"> 
                        <div class="col-xs-12 qms_customer_name service_title" data-row-id="<?php echo $serviceId; ?>" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; padding: 12px 18px;">
                            <div class="service-title-left" style="display: flex; align-items: center; gap: 8px; min-width: 80px;">
                                <i class="fa <?php echo ($displayHtmlStr == '') ? 'fa-folder-open-o' : 'fa-folder-o'; ?> service-folder-icon" style="color: #ffffff; font-size: 16px;"></i>
                                <?php if (count($currentServiceInvoices) > 0) { ?>
                                    <span class="badge badge-inv-count" title="Total Invoices for this Service" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.35); color: #ffffff; font-size: 11px; vertical-align: middle;">
                                        <i class="fa fa-file-text-o"></i> <?php echo count($currentServiceInvoices); ?> Inv
                                    </span>
                                <?php } ?>
                            </div>

                            <div class="service-title-center" style="flex: 1; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <span style="font-weight: 800; font-size: 16px; letter-spacing: 0.6px; color: #ffffff; text-transform: uppercase;">
                                    SERVICE &mdash; <span class="serviceStatus" style="color: #ffd166; font-weight: 900; font-size: 18px; text-shadow: 0 1px 3px rgba(0,0,0,0.3);"><?php if (isset($service1['service_type'])) echo $service1['service_type']; ?></span>
                                </span>
                            </div>

                            <div class="service-title-right" style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; min-width: 80px;">
                                <a class="saveService" data-row-id="<?php echo $serviceId; ?>" style="display:none !important;"></a>

                                <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin()) { ?>
                                    <a class="deleteService" id="deleteService" data-row-id="<?php echo $serviceId; ?>" title="Delete Service" style="color: #dc3545; background: #ffffff; width: 26px; height: 26px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; text-decoration: none; transition: all 0.15s ease;"><i class="fa fa-trash-o"></i></a>
                                <?php } ?>
                                <i class="fa <?php echo ($displayHtmlStr == '') ? 'fa-chevron-up' : 'fa-chevron-down'; ?> service-collapse-indicator" style="color: #ffffff; font-size: 14px; margin-left: 4px; transition: transform 0.2s;" title="Click to collapse / expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="servicebox-data" id="servicebox-data<?php if (isset($serviceId)) echo $serviceId; ?>" <?php echo $displayHtmlStr ?>>
                        <div class="row" style="padding-top:10px">
                            <div class="col-xs-3 form-group float-label-control">
                                <label>Service Type </label>
                                <select class="service-type" id="service-type" name="service-type" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?><?php echo $fieldAccess ?>>
                                    <option value="<?php if (isset($service1['service_type'])) echo $service1['service_type'] ?>" ><?php
                                        if (isset($service1['service_type']) && !empty($service1['service_type'])) {
                                            echo $service1['service_type'];
                                        } else {
                                            echo "Select Service Type";
                                        }
                                        ?> </option>
                                    <?php foreach ($allServices as $svc) { ?>
                                        <option value="<?php echo $svc['service'] ?>"><?php echo $svc['service'] ?></option>
                                    <?php } ?>
                                </select>
                            <!--<input type="text" class="service-type" id="service-type" name="service-type" data-row-id="<?php echo $serviceId; ?>" placeholder="Service Type" value="<?php if (isset($service1['service_type'])) echo $service1['service_type'] ?>" <?php echo $financeAccess ?><?php echo $fieldAccess ?>>-->
                            </div>
                            <div class="col-xs-3 form-group float-label-control">
                                <label>Cert Body</label>
                                <select class="cert-body" id="cert-body" name="cert-body" data-row-id="<?php echo $serviceId; ?>" value=""  <?php echo $financeAccess ?><?php echo $fieldAccess ?>>
                                    <option value="<?php if (isset($service1['cert_body'])) echo $service1['cert_body'] ?>" ><?php
                                        if (isset($service1['cert_body']) && !empty($service1['cert_body'])) {
                                            echo $service1['cert_body'];
                                        } else {
                                            echo "Select Cert Body";
                                        }
                                        ?> </option>
                                    <?php foreach ($allCert as $crt) { ?>
                                        <option value="<?php echo $crt['cert'] ?>"><?php echo $crt['cert'] ?></option>
                                    <?php } ?>
                                </select>
                                <!--<input type="text" class="cert-body" id="cert-body" data-row-id="<?php echo $serviceId; ?>" name="cert-body" placeholder="Ceritfication Body" value="<?php if (isset($service1['cert_body'])) echo $service1['cert_body'] ?>" <?php echo $financeAccess ?><?php echo $fieldAccess ?>>-->
                            </div>
                            <div class="col-xs-3 form-group float-label-control">
                                <label>Consulting firm</label>
                                <select class="consulting-firm" id="consulting-firm" name="consulting-firm" data-row-id="<?php echo $serviceId; ?>" value=""  <?php echo $financeAccess ?><?php echo $fieldAccess ?>>
                                    <option value="<?php if (isset($service1['consulting_firm'])) echo $service1['consulting_firm'] ?>" ><?php
                                        if (isset($service1['consulting_firm']) && !empty($service1['consulting_firm'])) {
                                            echo $service1['consulting_firm'];
                                        } else {
                                            echo "Select Consulting firm";
                                        }
                                        ?> </option>
                                    <?php foreach ($consultFirm as $firm) { ?>
                                        <option value="<?php echo $firm['firm_name'] ?>"><?php echo $firm['firm_name'] ?></option>
                                    <?php } ?>
                                </select>
                                <!--<input type="text" class="consulting-firm" id="consulting-firm" data-row-id="<?php echo $serviceId; ?>" name="consulting-firm" placeholder="Consulting firm" value="<?php if (isset($service1['consulting_firm'])) echo $service1['consulting_firm'] ?>" <?php echo $financeAccess ?><?php echo $fieldAccess ?>>-->
                            </div>
                            <div class="col-xs-3 form-group float-label-control">
                                <label>Consultant</label>
                                <select class="consultant-list" id="consultant-list" name="consultant-list" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?> <?php echo $fieldAccess ?>>
                                    <option value="<?php if (isset($service1['consultant'])) echo $service1['consultant'] ?>" ><?php
                                        if (isset($service1['consultant']) && !empty($service1['consultant'])) {
                                            echo $service1['consultant'];
                                        } else {
                                            echo "Select Consultant";
                                        }
                                        ?> </option>
                                    <?php foreach ($consultant as $cunslt) { ?>
                                        <option value="<?php echo $cunslt['consultant_name'] ?>"><?php echo $cunslt['consultant_name'] ?></option>
                                    <?php } ?>
                                </select>
                                <!--<input type="text" class="consultant-list" id="consultant-list" name="consultant-list" placeholder="Consultant" data-row-id="<?php echo $serviceId; ?>" value="<?php if (isset($service1['consultant'])) echo $service1['consultant'] ?>" <?php echo $financeAccess ?> <?php echo $fieldAccess ?>>-->
                            </div>
                            <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="service-id" id="service-id" name="service-id" value="<?php if (isset($serviceId)) echo $serviceId; ?>">
                        </div>
                        <div class="row">
                            <div class="col-xs-2 form-group float-label-control" style="margin-bottom:2px;">
                                <label>Auditor</label>
                                <select class="auditor-list" id="auditor-list" name="auditor-list" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?>>
                                    <option value="<?php if (isset($service1['auditor'])) echo $service1['auditor'] ?>" ><?php
                                        if (isset($service1['auditor']) && !empty($service1['auditor'])) {
                                            echo $service1['auditor'];
                                        } else {
                                            echo "Select Auditor";
                                        }
                                        ?> </option>
                                    <?php foreach ($allAuditorList as $auditors) { ?>
                                        <option value="<?php echo $auditors['auditor_name'] ?>"><?php echo $auditors['auditor_name'] ?></option>
                                    <?php } ?>
                                </select>
                                <!--<input type="text" class="auditor-list" data-row-id="<?php echo $serviceId; ?>" id="auditor-list" name="auditor-list" placeholder="Auditor" value="<?php if (isset($service1['auditor'])) echo $service1['auditor'] ?>" <?php echo $financeAccess ?>>-->
                            </div>
                            <div class="col-xs-2 form-group float-label-control" style="margin-bottom:2px;">
                                <label>Audit Date</label>
                                <input type="text" class="<?php echo $addClassToAudit ?>" data-row-id="<?php echo $serviceId; ?>"  name="audit-date" placeholder="Audit Date" value="<?php if (isset($service1['audit_date'])) echo simpleDateConversion($service1['audit_date']) ?>" <?php echo $financeAccess ?>>
                            </div>
                            <?php if (Utitlity::checkForUserConsultant() || Utitlity::checkForUserFinance()) { ?>
                                <div class="col-xs-3 form-group float-label-control" style="margin-bottom:2px;">
                                    <label>Add to</label>
                                    <input type="text" class="add-to-service" data-row-id="<?php echo $serviceId; ?>"  name="add-to-service" placeholder="add-to-service" value="<?php if (isset($service1['add_service_to'])) echo $service1['add_service_to'] ?>" <?php echo $financeAccess ?> <?php echo $fieldAccess ?> <?php echo $marketAccess ?>>
                                </div>
                            <?php } else { ?>                           
                                <div class="col-xs-3 form-group float-label-control" style="margin-bottom:2px;">
                                    <label>Add to</label>
                                    <select class="add-to-service" id="add-to-service" name="add-to-service" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?>>
                                        <option value="<?php if (isset($service1['add_service_to'])) echo $service1['add_service_to'] ?>" ><?php
                                            if (isset($service1['add_service_to']) && !empty($service1['add_service_to'])) {
                                                echo $service1['add_service_to'];
                                            } else {
                                                echo "Select add to";
                                            }
                                            ?></option>
                                        <?php foreach ($addTODo as $to) { ?>
                                            <option value="<?php echo $to['add_to'] ?>"><?php echo $to['add_to'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <!--<input type="text" class="add-to-service" data-row-id="<?php echo $serviceId; ?>" id="add-to-service" name="add-to-service" placeholder="Add To" value="<?php if (isset($service1['add_service_to'])) echo $service1['add_service_to'] ?>" required>-->
                                </div>
                            <?php } ?>
                            <div class="col-xs-2 form-group float-label-control" style="margin-bottom:2px;">
                                <label>Handel By</label>
                                <select class="handel-by" id="handel-by" name="handel-by" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?>>
                                    <option value="<?php if (isset($service1['handel_by'])) echo $service1['handel_by'] ?>" ><?php
                                        if (isset($service1['handel_by']) && !empty($service1['handel_by'])) {
                                            echo $service1['handel_by'];
                                        } else {
                                            echo "Select Handel By";
                                        }
                                        ?></option>
                                    <?php foreach ($handlerList as $handler) { ?>
                                        <option value="<?php echo $handler['handler_name'] ?>"><?php echo $handler['handler_name'] ?></option>
                                    <?php } ?>
                                </select>                                
                                <!--<input type="text" class="add-to-service" data-row-id="<?php echo $serviceId; ?>" id="add-to-service" name="add-to-service" placeholder="Add To" value="<?php if (isset($service1['add_service_to'])) echo $service1['add_service_to'] ?>" required>-->
                            </div>
                            <?php if (isset($service1['service_type']) && ($service1['service_type'] == "CA" || $service1['service_type'] == "SA-1" || $service1['service_type'] == "SA-2" || $service1['service_type'] == "RC")) { ?>
                                <div class="col-xs-3 form-group" style="">
                                    <?php
                                    if (isset($service1['is_certified']) && $service1['is_certified'] == 'YES')
                                        $chkVal = "checked";
                                    else
                                        $chkVal = "";
                                    ?>
                                    <input type="checkbox" class="issueCert" id="issueCert" data-row-id="<?php echo $serviceId; ?>" data-service="<?php echo $serviceId ?>" data-customer="<?php echo $service1['customer_id'] ?>" <?php echo $chkVal ?> <?php echo $disable ?>>&nbsp;&nbsp;Certificate Given 
                                    <div class="certificationDateError" id="certificationDateError" style="display: none">
                                        <small><span id="certificationDateErrorMsg" style="color: #c9513c; font-style: italic;" >CA/RC Date con not be empty.</span></small>
                                    </div>
                                </div>
                            <?php } ?>
                            <input type="hidden" class="customer_id" data-row-id="<?php echo $serviceId; ?>" id="customer_id" name="customer_id" value="<?php echo htmlspecialchars($_GET['customerid']); ?>" />
                        </div>

                    <div class="row" style="margin-top: 2px; margin-bottom: 4px; padding: 0 10px;">
                        <div class="col-xs-12">
                            <div class="compact-scope-bar" style="display: flex; align-items: center; gap: 6px; background: #ffffff; border: 1px solid #dce4ec; border-radius: 4px; padding: 2px 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                <span style="font-size: 10px; font-weight: 700; color: #1e3a8a; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa fa-pencil-square-o" style="color: #2563eb;"></i> Scope / Contract:
                                </span>
                                <input type="text" name="audit-details" class="saveChanges auditDetailsText form-control" data-column="marketing_audit_type" id="audit-details" placeholder="Enter contract details, audit scope, or terms..." value="<?php if (isset($customer['marketing_audit_type']) && $customer['marketing_audit_type'] != '') echo htmlspecialchars($customer['marketing_audit_type']); ?>" style="flex: 1; height: 23px; min-height: 23px; max-height: 23px; border: 1px solid #cbd5e1; border-radius: 3px; padding: 1px 6px; font-size: 11px; line-height: 21px; background: #f8fafc; color: #0f172a; box-shadow: none;" />
                            </div>
                        </div>
                    </div>

                                        <!-- ========================================================
                         SEPARATED & COMPACT PAYMENT SYSTEM (CONSULTING & CB)
                         ======================================================== -->
                    <div class="row" style="margin-bottom: 4px; padding: 0 10px;">
                        <div class="col-xs-12">

                            <!-- SECTION 1: CONSULTING FEES & INSTALLMENTS -->
                            <div class="compact-payment-block consulting-payment-block">
                                <div class="compact-table-card">
                                    <div class="compact-table-header consulting-header">
                                        <div class="header-title">
                                            <i class="fa fa-handshake-o"></i> Consulting Fees & Installments
                                        </div>
                                        <div class="header-actions">
                                            <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                                <button type="button" class="btn btn-xs serviceInvoiceBtn header-invoice-btn" 
                                                        data-customer="<?php echo htmlspecialchars($_GET['customerid']); ?>" 
                                                        data-service="<?php echo $serviceId; ?>" 
                                                        data-servicename="<?php echo htmlspecialchars($service1['service_type'] ?: ''); ?>"
                                                        data-firm="<?php echo htmlspecialchars($service1['consulting_firm'] ?: ''); ?>"
                                                        title="Issue Invoice for <?php echo htmlspecialchars($service1['service_type'] ?: 'Service'); ?>">
                                                    <i class="fa fa-plus-circle"></i> Issue Invoice for <?php echo htmlspecialchars($service1['service_type'] ?: 'Service'); ?>
                                                </button>
                                                <a class="consultingAddLink btn btn-xs btn-compact-add" title="Add new consulting installment row" data-row-id="<?php echo $serviceId; ?>" data-row-count="<?php echo $consultingsListCount['totalconsulting'] ?>">
                                                    <i class="fa fa-plus-circle"></i> Add Consulting Row
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover compact-payment-table service_details" id="consulting_table_<?php echo $serviceId; ?>">
                                            <thead>
                                                <tr>
                                                    <th width="24%">Installment Details</th>
                                                    <th width="13%" style="text-align: right;">Amount Decided</th>
                                                    <th width="13%" style="text-align: right;">Payment Received</th>
                                                    <th width="13%" style="text-align: center;">Date of Deposit</th>
                                                    <th width="13%">Instrument No</th>
                                                    <th width="10%">Bank A/c</th>
                                                    <th width="10%" style="text-align: right;">Balance</th>
                                                    <th width="4%" style="text-align: center;"><i class="fa fa-close"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody id="consulting_tbody_<?php echo $serviceId; ?>">
                                                <?php
                                                if ($consultingsListCount['totalconsulting'] > 0) {
                                                    $cfrmAbr = "SELECT * FROM consulting_abbrevation ";
                                                    $statement = $db->prepare($cfrmAbr);
                                                    $statement->execute();
                                                    $abList = $statement->fetchAll();

                                                    foreach ($consultingList as $consult) {
                                                        ?>
                                                        <tr class="consulting_subsection previousConsultingData">
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input data-row-id="<?php echo $serviceId; ?>" type="text" class="consultingContractDetails" id="consultingContractDetails" name="consultingContractDetails" value="<?php if (isset($consult['consulting_fees'])) echo htmlspecialchars($consult['consulting_fees']); ?>" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="number" data-row-id="<?php echo $serviceId; ?>" class="consultingAmountDecided" id="consultingAmountDecided" name="consultingAmountDecided" value="<?php if (isset($consult['consulting_amount_decided'])) echo $consult['consulting_amount_decided']; ?>" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="number" data-row-id="<?php echo $serviceId; ?>" class="consultingPaymentReceived" id="consultingPaymentReceived" name="consultingPaymentReceived" value="<?php if (isset($consult['consulting_payment_received'])) echo $consult['consulting_payment_received']; ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassToConsulting ?>" name="consultingDepositDate" value="<?php if (isset($consult['consulting_deposit_date'])) echo simpleDateConversion($consult['consulting_deposit_date']); ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingChequeDetails" id="consultingChequeDetails" name="consultingChequeDetails" value="<?php if (isset($consult['consulting_chque_details'])) echo htmlspecialchars($consult['consulting_chque_details']); ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <select class="consultingBankAcccountNo" id="consultingBankAcccountNo" name="consultingBankAcccountNo" data-row-id="<?php echo $serviceId; ?>" <?php echo $fieldAccess ?>>
                                                                        <option value="<?php if (isset($consult['bank_ac'])) echo htmlspecialchars($consult['bank_ac']); ?>">
                                                                            <?php echo (isset($consult['bank_ac']) && !empty($consult['bank_ac'])) ? htmlspecialchars($consult['bank_ac']) : 'Select'; ?>
                                                                        </option>
                                                                        <?php foreach ($abList as $bankAb) { ?>
                                                                            <option value="<?php if (isset($bankAb['bank_abbr'])) echo htmlspecialchars($bankAb['bank_abbr']); ?>">
                                                                                <?php if (isset($bankAb['bank_abbr'])) echo htmlspecialchars($bankAb['bank_abbr']); ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="text" class="consultingBalance" data-row-id="<?php echo $serviceId; ?>" id="consultingBalance" name="consultingBalance" value="<?php if (isset($consult['consulting_balance'])) echo $consult['consulting_balance']; ?>" placeholder="" readonly="" />
                                                                </div>
                                                            </td>
                                                            <td style="text-align:center;">
                                                                <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                                    <input type="checkbox" class="consultingDelete" data-row-id="<?php echo $serviceId; ?>" value="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>" style="display:none;"/>
                                                                    <a href="javascript:void(0)" class="consultingDeleteIcon" data-row-id="<?php echo $serviceId; ?>" data-id="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>"><i class="fa fa-close" aria-hidden="true"></i></a>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consulting-row-id <?php echo $consult['consulting_id'] ?>" id="consulting-row-id" name="consulting-row-id" value="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>">
                                                    <?php }
                                                } else { ?>
                                                    <tr class="consulting_subsection">
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingContractDetails" id="consultingContractDetails" name="consultingContractDetails" value="" placeholder="" <?php echo $fieldAccess ?><?php echo $financeAccess ?>/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="number" data-row-id="<?php echo $serviceId; ?>" class="consultingAmountDecided" id="consultingAmountDecided" name="consultingAmountDecided" value="" placeholder="" <?php echo $fieldAccess ?><?php echo $financeAccess ?>/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="number" data-row-id="<?php echo $serviceId; ?>" class="consultingPaymentReceived" id="consultingPaymentReceived" name="consultingPaymentReceived" value="" placeholder="" <?php echo $fieldAccess ?>/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassToConsulting ?>" name="consultingDepositDate" value="" placeholder="" <?php echo $fieldAccess ?>/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingChequeDetails" id="consultingChequeDetails" name="consultingChequeDetails" value="" placeholder="" <?php echo $fieldAccess ?>/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingBankAcccountNo" id="consultingBankAcccountNo" name="consultingBankAcccountNo" value="" placeholder="" <?php echo $fieldAccess ?>/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingBalance" id="consultingBalance" name="consultingBalance" value="" placeholder="" readonly="" />
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                                <a href="javascript:void(0)" class="consultingRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Compact Consulting Financial Summary & Invoices -->
                                <div class="compact-summary-panel consulting-summary-panel">
                                    <div class="summary-top-bar">
                                        <div class="summary-title">
                                            <i class="fa fa-file-text-o" style="color: #0891b2; margin-right: 4px;"></i> 
                                            Consulting Financial Summary & Invoices &mdash; <span style="color: #0891b2;"><?php echo htmlspecialchars($service1['service_type'] ?: 'Service #' . $serviceId); ?></span>
                                        </div>
                                        <div class="summary-actions">
                                            <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                                <button type="button" class="btn btn-xs btn-primary serviceInvoiceBtn compact-invoice-btn" 
                                                        data-customer="<?php echo htmlspecialchars($_GET['customerid']); ?>" 
                                                        data-service="<?php echo $serviceId; ?>" 
                                                        data-servicename="<?php echo htmlspecialchars($service1['service_type'] ?: ''); ?>"
                                                        data-firm="<?php echo htmlspecialchars($service1['consulting_firm'] ?: ''); ?>">
                                                    <i class="fa fa-plus-circle"></i> Issue Invoice for <?php echo htmlspecialchars($service1['service_type'] ?: 'Service'); ?>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- 4 Compact KPI chips for Consulting -->
                                    <div class="compact-kpi-row">
                                        <div class="kpi-chip kpi-consult-decided">
                                            <span class="kpi-chip-label">Consulting Decided</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($consultTotalDecided, 2); ?></span>
                                        </div>
                                        <div class="kpi-chip kpi-consult-invoiced">
                                            <span class="kpi-chip-label">Total Invoiced</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($svcTotalInvoiced, 2); ?></span>
                                        </div>
                                        <div class="kpi-chip kpi-consult-received">
                                            <span class="kpi-chip-label">Payment Received</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($consultTotalReceived, 2); ?></span>
                                        </div>
                                        <?php 
                                            $kpiConsultBalClass = ($consultInvoiceBalance > 0) ? 'kpi-chip-danger' : 'kpi-chip-success';
                                        ?>
                                        <div class="kpi-chip kpi-consult-balance <?php echo $kpiConsultBalClass; ?>">
                                            <span class="kpi-chip-label">Invoice Balance Due</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($consultInvoiceBalance, 2); ?></span>
                                        </div>
                                    </div>

                                    <!-- Invoices List for this Service (if any) -->
                                    <?php if (count($currentServiceInvoices) > 0) { ?>
                                        <div class="table-responsive" style="margin-top: 6px; border-radius: 4px; overflow: hidden; border: 1px solid #e2e8f0; background: #fff;">
                                            <table class="table table-hover table-condensed table-striped" style="margin-bottom: 0; font-size: 11.5px;">
                                                <thead style="background: #f8fafc; color: #475569;">
                                                    <tr>
                                                        <th style="padding: 5px 8px;">Invoice No.</th>
                                                        <th style="padding: 5px 8px;">Date</th>
                                                        <th style="padding: 5px 8px;">Consulting Firm</th>
                                                        <th style="padding: 5px 8px;">Service Desc</th>
                                                        <th style="padding: 5px 8px; text-align: right;">Total Amount</th>
                                                        <th style="padding: 5px 8px; text-align: center;">Status</th>
                                                        <th style="padding: 5px 8px; text-align: center;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $cumPaid = $consultTotalReceived;
                                                    foreach ($currentServiceInvoices as $sInv) { 
                                                        $curAmt = (float)$sInv['total_amount'];
                                                        if ($cumPaid >= $curAmt && $curAmt > 0) {
                                                            $payStatus = '<span class="label label-success" style="font-size:9.5px; padding:2px 6px; border-radius:10px;"><i class="fa fa-check"></i> Paid</span>';
                                                            $cumPaid -= $curAmt;
                                                        } elseif ($cumPaid > 0 && $curAmt > 0) {
                                                            $payStatus = '<span class="label label-warning" style="font-size:9.5px; padding:2px 6px; border-radius:10px;"><i class="fa fa-clock-o"></i> Partial</span>';
                                                            $cumPaid = 0;
                                                        } else {
                                                            $payStatus = '<span class="label label-danger" style="font-size:9.5px; padding:2px 6px; border-radius:10px;"><i class="fa fa-exclamation-circle"></i> Unpaid</span>';
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td style="padding: 5px 8px; font-weight: 600; color: #1e40af;">
                                                                <?php echo htmlspecialchars($sInv['firm_abbrevation'] . '/' . $sInv['invoice_no']); ?>
                                                            </td>
                                                            <td style="padding: 5px 8px; color: #64748b;">
                                                                <?php echo simpleDateConversion($sInv['invoice_date']); ?>
                                                            </td>
                                                            <td style="padding: 5px 8px; color: #334155;">
                                                                <?php echo htmlspecialchars($sInv['firm_name']); ?>
                                                            </td>
                                                            <td style="padding: 5px 8px; color: #475569;">
                                                                <?php echo htmlspecialchars($sInv['svc_desc']); ?>
                                                            </td>
                                                            <td style="padding: 5px 8px; text-align: right; font-weight: 700; color: #0f172a;">
                                                                ₹ <?php echo number_format($curAmt, 2); ?>
                                                            </td>
                                                            <td style="padding: 5px 8px; text-align: center;">
                                                                <?php echo $payStatus; ?>
                                                            </td>
                                                            <td style="padding: 5px 8px; text-align: center;">
                                                                <a class="viewInvoice btn btn-default btn-xs" data-id="<?php echo $sInv['invoice_id']; ?>" data-invoice="<?php echo $sInv['invoice_no']; ?>" data-customer="<?php echo $sInv['customer_id']; ?>" title="View / Print PDF" style="margin-right: 3px; color: #dc2626; padding: 1px 5px; font-size: 10.5px;">
                                                                    <i class="fa fa-file-pdf-o"></i> PDF
                                                                </a>
                                                                <a class="editInvoice btn btn-default btn-xs" data-id="<?php echo $sInv['invoice_id']; ?>" data-firm="<?php echo $sInv['firm_id']; ?>" data-customer="<?php echo $sInv['customer_id']; ?>" title="Edit Invoice" style="color: #2563eb; padding: 1px 5px; font-size: 10.5px;">
                                                                    <i class="fa fa-pencil"></i> Edit
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- DOTTED SEPARATOR LINE BETWEEN CONSULTING AND CB -->
                            <div class="payment-dotted-divider">
                                <span class="divider-label"><i class="fa fa-university" style="color: #1e293b;"></i> Certification Body (CB) Payments</span>
                            </div>

                            <!-- SECTION 2: CERTIFICATION BODY (CB) PAYMENTS -->
                            <div class="compact-payment-block cb-payment-block">
                                <div class="compact-table-card">
                                    <div class="compact-table-header cb-header">
                                        <div class="header-title">
                                            <i class="fa fa-university"></i> Certification Body (CB) Payments
                                        </div>
                                        <div class="header-actions">
                                            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) { ?>
                                                <a class="cbAddLinkForAdmin btn btn-xs btn-compact-ms" data-row-id="<?php echo $serviceId; ?>" data-row-count="<?php echo $cbCount['totalcb'] ?>" title="Add Marketing Setup">
                                                    MS
                                                </a>
                                            <?php } ?>
                                            <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                                <a class="btn btn-xs cbAddLink btn-compact-add" data-row-id="<?php echo $serviceId; ?>" data-row-count="<?php echo $cbCount['totalcb'] ?>" title="Add new CB payment installment row">
                                                    <i class="fa fa-plus-circle"></i> Add CB Row
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover compact-payment-table service_details" id="cb_table_<?php echo $serviceId; ?>">
                                            <thead>
                                                <tr>
                                                    <th width="24%">Installment Details</th>
                                                    <th width="13%" style="text-align: right;">Amount Decided</th>
                                                    <th width="13%" style="text-align: right;">Payment Received</th>
                                                    <th width="13%" style="text-align: center;">Date of Deposit</th>
                                                    <th width="13%">Instrument No</th>
                                                    <th width="10%">Bank A/c</th>
                                                    <th width="10%" style="text-align: right;">Balance</th>
                                                    <th width="4%" style="text-align: center;"><i class="fa fa-close"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody id="cb_tbody_<?php echo $serviceId; ?>">
                                                <?php
                                                if ($cbCount['totalcb'] > 0) {
                                                    $cbBank = "SELECT * FROM consulting_abbrevation order by id desc";
                                                    $statement = $db->prepare($cbBank);
                                                    $statement->execute();
                                                    $certAbbr = $statement->fetchAll();

                                                    foreach ($certList as $cert) {
                                                        $a = strtoupper($cert['contract_details']);
                                                        if ((startsWith($a, 'MARKETING SETUP') || startsWith($a, 'MARKETING-SETUP') || startsWith($a, 'MARKETING') || startsWith($a, 'SETUP') || startsWith($a, 'MARK') || startsWith($a, 'MARKE')) && !(Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin())) {
                                                            $display = 'none';
                                                        } else {
                                                            $display = '';
                                                        }
                                                        ?>
                                                        <tr class="cb_subsection previousCBData" id="cb_subsection_0" style="display: <?php echo $display ?>">
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="text" data-row-id="<?php echo $serviceId; ?>" class="contractDetails" id="contractDetails" name="contractDetails" value="<?php if (isset($cert['contract_details'])) echo htmlspecialchars($cert['contract_details']); ?>" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="number" data-row-id="<?php echo $serviceId; ?>" class="amountDecided" id="amountDecided" name="amountDecided" value="<?php if (isset($cert['amount_decided'])) echo $cert['amount_decided']; ?>" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="number" data-row-id="<?php echo $serviceId; ?>" class="paymentReceived" id="paymentReceived" name="paymentReceived" value="<?php if (isset($cert['payment_received'])) echo $cert['payment_received']; ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassTo ?>" name="depositDate" value="<?php if (isset($cert['deposit_date'])) echo simpleDateConversion($cert['deposit_date']); ?>" placeholder="" <?php echo $fieldAccess ?>>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="text" data-row-id="<?php echo $serviceId; ?>" class="chequeDetails" id="chequeDetails" name="chequeDetails" value="<?php if (isset($cert['check_details'])) echo htmlspecialchars($cert['check_details']); ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <select class="bankAcccountNo" id="bankAcccountNo" name="bankAcccountNo" data-row-id="<?php echo $serviceId; ?>" <?php echo $fieldAccess ?>>
                                                                        <option value="<?php if (isset($cert['bank_account'])) echo htmlspecialchars($cert['bank_account']); ?>">
                                                                            <?php echo (isset($cert['bank_account']) && !empty($cert['bank_account'])) ? htmlspecialchars($cert['bank_account']) : 'Select'; ?>
                                                                        </option>
                                                                        <?php foreach ($certAbbr as $bankAb) { ?>
                                                                            <option value="<?php if (isset($bankAb['bank_abbr'])) echo htmlspecialchars($bankAb['bank_abbr']); ?>">
                                                                                <?php if (isset($bankAb['bank_abbr'])) echo htmlspecialchars($bankAb['bank_abbr']); ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="table-edit-control">
                                                                    <input type="text" data-row-id="<?php echo $serviceId; ?>" class="balance" id="balance" name="balance" value="<?php if (isset($cert['balance'])) echo $cert['balance']; ?>" placeholder="" readonly="" <?php echo $fieldAccess ?> />
                                                                </div>
                                                            </td>
                                                            <td style="text-align:center;">
                                                                <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                                    <input type="checkbox" class="cbDelete" data-row-id="<?php echo $serviceId; ?>" value="<?php echo $cert['certification_id'] ?>" style="display:none;">
                                                                    <a href="javascript:void(0)" class="cbDeleteIcon" data-row-id="<?php echo $serviceId; ?>" data-id="<?php echo $cert['certification_id']; ?>"><i class="fa fa-close" aria-hidden="true"></i></a>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="cb-row-comment <?php echo $cert['certification_id'] ?>" id="cb-row-comment" name="cb-row-comment" value="">
                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="cb-row-id <?php echo $cert['certification_id'] ?>" id="cb-row-id" name="cb-row-id" value="<?php if (isset($cert['certification_id'])) echo $cert['certification_id']; ?>">
                                                    <?php }
                                                } else { ?>
                                                    <tr class="cb_subsection" id="cb_subsection_0">
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="contractDetails" id="contractDetails" name="contractDetails" value="" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="number" data-row-id="<?php echo $serviceId; ?>" class="amountDecided" id="amountDecided" name="amountDecided" value="" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="number" data-row-id="<?php echo $serviceId; ?>" class="paymentReceived" id="paymentReceived" name="paymentReceived" value="" placeholder="" <?php echo $fieldAccess ?> />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassTo ?>" name="depositDate" value="" placeholder="" <?php echo $fieldAccess ?> />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="chequeDetails" id="chequeDetails" name="chequeDetails" value="" placeholder="" <?php echo $fieldAccess ?> />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="bankAcccountNo" id="bankAcccountNo" name="bankAcccountNo" value="" placeholder="" <?php echo $fieldAccess ?> />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="table-edit-control">
                                                                <input type="text" class="balance" data-row-id="<?php echo $serviceId; ?>" id="balance" name="balance" value="" placeholder="" readonly="" />
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                                <a href="javascript:void(0)" class="cbRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Compact CB Financial Summary -->
                                <div class="compact-summary-panel cb-summary-panel">
                                    <div class="summary-top-bar">
                                        <div class="summary-title">
                                            <i class="fa fa-calculator" style="color: #1e293b; margin-right: 4px;"></i> 
                                            Certification Body (CB) Financial Summary
                                        </div>
                                    </div>
                                    <div class="compact-kpi-row">
                                        <div class="kpi-chip kpi-cb-decided">
                                            <span class="kpi-chip-label">CB Decided</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($cbTotalDecided, 2); ?></span>
                                        </div>
                                        <div class="kpi-chip kpi-cb-received">
                                            <span class="kpi-chip-label">CB Payment Received</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($cbTotalReceived, 2); ?></span>
                                        </div>
                                        <?php 
                                            $kpiCbBalClass = ($cbTotalBalance > 0) ? 'kpi-chip-danger' : 'kpi-chip-success';
                                        ?>
                                        <div class="kpi-chip kpi-cb-balance <?php echo $kpiCbBalClass; ?>">
                                            <span class="kpi-chip-label">CB Balance Due</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($cbTotalBalance, 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div id="delete-details" style="display: none; z-index: 2">
    <div id="loading-div" class="ui-corner-all">
        <span style="font-size:30pt;"><i style="padding:5px;" class="fa fa-warning text-warning"></i></span>
        <div> 
            <span style="font-size:11pt;padding:10px" class="text-primary meeting-cancel-confirm-message" >Are you sure that you want to delete details?</span>
        </div>
        <div style="padding:3px;">
            <button title="Click here to delete"  class="btn btn-primary btn-round btn-round-sm btn-sm delete-confirm-ok">Ok</button>
            <button title="Click here to cancel" class="btn btn-danger  btn-round btn-round-sm btn-sm delete-cancel">Cancel</button>
        </div>
    </div>
</div>

<div id="empty-date" style="display: none">
    <div id="loading-div" class="ui-corner-all">
        <span style="font-size:30pt;"><i style="padding:5px;" class="fa fa-warning text-warning"></i></span>
        <div> 
            <span style="font-size:11pt;padding:10px" class="text-primary meeting-cancel-confirm-message" >Are you sure that you want to delete details?</span>
        </div>
        <div style="padding:3px;">
            <button title="Click here to delete"  class="btn btn-primary btn-round btn-round-sm btn-sm delete-confirm-ok">Ok</button>
            <button title="Click here to cancel" class="btn btn-danger  btn-round btn-round-sm btn-sm delete-cancel">Cancel</button>
        </div>
    </div>
</div>
</div>

<style>
    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
    }

    #loading-div-background{
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(255, 255, 255, 0.5);
        width: 100%;
        height: 100%;
    }

    #loading-div{
        width: 300px;
        height: 150px;
        background-color: #fff;
        color:#1468b3;
        border: 5px solid #1468b3;
        text-align: center;
        color: #202020;
        position: absolute;
        left: 50%;
        top: 50%;
        margin-left: -150px;
        margin-top: -100px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        opacity: 1.0;
        behavior: url("/css/pie/PIE.htc"); /* HANDLES IE */

    }

    #loading-div-user-info{
        width: 350px;
        background-color: #fff;
        color:#1468b3;
        border: 1px solid gray;
        color: #202020;
        position: absolute;
        left: 50%;
        top: 50%;
        margin-left: -150px;
        margin-top: -100px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        opacity: 1.0;
        behavior: url("/css/pie/PIE.htc"); /* HANDLES IE */

    }

    #loading-div-background-resend{
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(255, 255, 255, 0.5);
        width: 100%;
        height: 100%;
    }

    #loading-div-background-meetedit{
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(255, 255, 255, 0.5);
        width: 100%;
        height: 100%;
    }

    .unselectable {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>
<script>
    $(document).ready(function () {
        $(".show-all-invoices").click(function () {
//            alert("in");
            $('.hidden-row').toggle();
        });

        $('body').on('click', ".viewInvoice", function (e) {
            var invoiceid = $(this).attr('data-id');
            var invoiceno = $(this).attr('data-invoice');
//            alert(customerID);
            // open popup
//            localStorage['visited'] = customerID;
            var url = 'viewInvoicePdf.php'
            var form = $('<form action="' + url + '" target="_blank" method="post">' +
                    '<input type="hidden" name="invoiceId" value="' + invoiceid + '" />' +
                    '<input type="hidden" name="invoiceNo" value="' + invoiceno + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });
    });
</script>

<style>
/* ========================================================
   CLEAN DEMO THEME (Royal Blue Headers + Crisp White Cards)
   ======================================================== */
body, #page-content-wrapper {
    background-color: #eef3f8 !important;
    font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    color: #1e293b;
}

/* ========================================================
   TOP CUSTOMER OVERVIEW CARD (COMPACT - EXACT MATCH IMAGE 2)
   ======================================================== */
.top_qms_page {
    background: #e3eaf3 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 4px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
    margin-top: 6px !important;
    margin-bottom: 8px !important;
    overflow: hidden !important;
    color: #193048 !important;
}

/* Customer Title Bar (Dark Navy matching Image 2) */
.top_qms_page .qms_customer_name {
    position: relative !important;
    top: auto !important;
    left: auto !important;
    right: auto !important;
    z-index: 10 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: #193048 !important;
    color: #ffffff !important;
    height: 38px !important;
    min-height: 38px !important;
    padding: 6px 14px !important;
    border-radius: 3px 3px 0 0 !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    letter-spacing: 0.5px !important;
    box-shadow: none !important;
}
.top_qms_page .qms_customer_name > span:first-child i {
    color: #ffffff !important;
    font-size: 15px !important;
    margin-right: 6px !important;
}
.top_qms_page .qms_customer_name img {
    margin-left: 6px !important;
    vertical-align: middle !important;
}

/* Action Icons inside Customer Header Bar */
.top_qms_page .qms_customer_name .editCustomerDetails {
    margin-left: auto !important;
    padding-left: 0 !important;
}
.top_qms_page .qms_customer_name a {
    color: #ffffff !important;
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    width: auto !important;
    height: auto !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    padding: 2px 6px !important;
    margin-left: 6px !important;
    text-decoration: none !important;
    box-shadow: none !important;
    opacity: 0.85;
    transition: opacity 0.15s ease !important;
}
.top_qms_page .qms_customer_name a:hover {
    color: #ffffff !important;
    background: transparent !important;
    opacity: 1 !important;
    transform: none !important;
    box-shadow: none !important;
}
.top_qms_page .qms_customer_name .deleteCustomerDetails {
    color: #ffffff !important;
}

/* Customer Info Columns (Compact Layout) */
.top_qms_page > div > .row:nth-child(2) {
    padding-top: 6px !important;
    padding-bottom: 4px !important;
}
.top_qms_page .col-sm-6:first-child .row {
    padding: 2px 8px !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    display: flex !important;
    align-items: center !important;
}
.top_qms_page .col-sm-6:first-child .row:hover {
    background: transparent !important;
}
.top_qms_page .col-sm-6:first-child .col-xs-1 {
    width: 24px !important;
    padding: 0 !important;
    text-align: center !important;
}
.top_qms_page .col-sm-6:first-child .col-xs-1 span {
    background: transparent !important;
    color: #428bca !important;
    border: none !important;
    width: auto !important;
    height: auto !important;
    display: inline !important;
    font-size: 13px !important;
}
.top_qms_page .col-sm-6:first-child .col-xs-11,
.top_qms_page .col-sm-6:first-child .col-xs-5 {
    padding-left: 6px !important;
    padding-right: 4px !important;
}
.top_qms_page .col-sm-6:first-child .col-xs-11 span,
.top_qms_page .col-sm-6:first-child .col-xs-5 span {
    color: #193048 !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    line-height: 1.3 !important;
}

/* Customer Notes Textarea (Compact borderless) */
.top_qms_page #add-notes {
    background: transparent !important;
    border: 1px solid rgba(0, 0, 0, 0.12) !important;
    border-radius: 4px !important;
    padding: 6px 8px !important;
    font-size: 12px !important;
    color: #193048 !important;
    width: 100% !important;
    height: 90px !important;
    min-height: 80px !important;
    line-height: 1.4 !important;
    box-shadow: none !important;
    resize: none !important;
}
.top_qms_page #add-notes:focus {
    background: #ffffff !important;
    border-color: #428bca !important;
    box-shadow: 0 0 0 2px rgba(66, 139, 202, 0.2) !important;
    outline: none !important;
}

/* Divider & Milestone Dates Row (Image 2 Match) */
.top_qms_page hr {
    border: 0 !important;
    border-top: 1px solid #94a3b8 !important;
    margin: 4px 8px !important;
}
.top_qms_page .text-right {
    color: #428bca !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    text-transform: none !important;
    letter-spacing: normal !important;
    line-height: 24px !important;
}
.top_qms_page input.lookslike-label {
    background: transparent !important;
    border: 1px solid rgba(0, 0, 0, 0.15) !important;
    border-radius: 3px !important;
    height: 24px !important;
    padding: 2px 6px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    width: 100% !important;
    box-shadow: none !important;
    transition: all 0.15s ease !important;
}
.top_qms_page input.lookslike-label:focus {
    background: #ffffff !important;
    border-color: #428bca !important;
    box-shadow: 0 0 0 2px rgba(66, 139, 202, 0.2) !important;
    outline: none !important;
}

/* Action Buttons Connected Segmented Bar (Image 2 Match) */
.qms_complete_btn {
    border-radius: 0px !important;
    padding: 5px 14px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    letter-spacing: 0.2px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border: 1px solid #193048 !important;
    box-shadow: none !important;
    cursor: pointer !important;
    margin-bottom: 8px !important;
    height: 30px !important;
    line-height: 20px !important;
    transition: all 0.15s ease !important;
}
.qms_complete_btn:hover {
    opacity: 0.9 !important;
    transform: none !important;
    box-shadow: none !important;
}
.qms_complete_btn.invoiceBtn {
    background: #428bca !important;
    color: #ffffff !important;
}
.qms_complete_btn.quotationBtn {
    background: #193048 !important;
    color: #ffffff !important;
}
.qms_complete_btn.addNewService {
    background: #47a447 !important;
    color: #ffffff !important;
}
.qms_complete_btn.show-all-invoices {
    background: #428bca !important;
    color: #ffffff !important;
}
/* ========================================================
   MODERN SERVICE CARD & PAYMENT SYSTEM STYLING
   ======================================================== */
.servicebox {
    background: #ffffff !important;
    border: 1px solid #dce4ec !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 14px rgba(15, 35, 70, 0.06) !important;
    margin-bottom: 24px !important;
    overflow: hidden !important;
    transition: all 0.2s ease-in-out !important;
}
.servicebox:hover {
    box-shadow: 0 6px 18px rgba(15, 35, 70, 0.09) !important;
    border-color: #b8c9dc !important;
}

/* Service Title Header - Royal Blue Matching Demo */
.service_title {
    background: #0d52b3 !important;
    color: #ffffff !important;
    padding: 12px 18px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
    user-select: none;
    box-shadow: 0 2px 4px rgba(13, 82, 179, 0.15);
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}
.service_title:hover {
    background: #0b4596 !important;
}
.service-title-center {
    flex: 1 !important;
    text-align: center !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
}
.service_title .serviceStatus {
    color: #ffd166 !important;
    font-weight: 900 !important;
    font-size: 18px !important;
    letter-spacing: 0.5px !important;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3) !important;
    text-transform: uppercase !important;
}
.service-collapse-indicator {
    color: #ffffff !important;
    transition: color 0.2s ease, transform 0.2s ease;
}

/* Auto-Save Live Status Pill - Hidden per user request */
.auto-save-status-pill {
    display: none !important;
}

/* Service Box Data Section */
.servicebox-data {
    padding: 16px 18px 12px 18px !important;
}
.servicebox-data .float-label-control {
    margin-bottom: 12px !important;
}
.servicebox-data .float-label-control label {
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    color: #475569 !important;
    margin-bottom: 4px !important;
    display: block !important;
    position: static !important;
    opacity: 1 !important;
}
.servicebox-data select,
.servicebox-data input[type="text"] {
    height: 38px !important;
    border: 1px solid #dce4ec !important;
    border-radius: 6px !important;
    padding: 6px 10px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #0f172a !important;
    background-color: #ffffff !important;
    width: 100% !important;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.02) !important;
    transition: all 0.18s ease-in-out !important;
}
.servicebox-data select:focus,
.servicebox-data input[type="text"]:focus {
    border-color: #0b57cf !important;
    box-shadow: 0 0 0 3px rgba(11, 87, 207, 0.15) !important;
    outline: none !important;
}
.servicebox-data .issueCert {
    width: 16px !important;
    height: 16px !important;
    accent-color: #0d52b3 !important;
    vertical-align: middle !important;
    cursor: pointer !important;
}

/* Service Contract Panel */
.service-contract-panel {
    background: #f8fafc !important;
    border: 1px solid #dce4ec !important;
    border-radius: 8px !important;
    padding: 12px 16px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important;
}
.service-contract-panel textarea#audit-details {
    background: #ffffff !important;
    border: 1px solid #dce4ec !important;
    border-radius: 6px !important;
    font-size: 13px !important;
    color: #0f172a !important;
    line-height: 1.5 !important;
    padding: 8px 12px !important;
    transition: all 0.2s ease !important;
}
.service-contract-panel textarea#audit-details:focus {
    border-color: #0b57cf !important;
    box-shadow: 0 0 0 3px rgba(11, 87, 207, 0.15) !important;
    outline: none !important;
}

/* ========================================================
   ULTRA-COMPACT & SEPARATED PAYMENT SYSTEM (CONSULTING & CB)
   ======================================================== */
.compact-payment-block {
    margin-bottom: 3px !important;
}
.compact-table-card {
    border: 1px solid #dce4ec !important;
    border-radius: 4px !important;
    background: #ffffff !important;
    box-shadow: 0 1px 2px rgba(15, 35, 70, 0.03) !important;
    overflow: hidden !important;
    margin-bottom: 2px !important;
}
.compact-table-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 2px 8px !important;
    min-height: 24px !important;
    height: 24px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px !important;
}
.compact-table-header.consulting-header {
    background: #0891b2 !important;
    color: #ffffff !important;
}
.compact-table-header.cb-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
    color: #ffffff !important;
}
.compact-table-header .header-title {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    line-height: 1 !important;
}
.compact-table-header .header-actions {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}
.btn-compact-add,
a.consultingAddLink.btn-compact-add,
a.cbAddLink.btn-compact-add {
    background: #ffffff !important;
    font-weight: 700 !important;
    font-size: 10px !important;
    padding: 0 7px !important;
    height: 19px !important;
    line-height: 17px !important;
    border-radius: 3px !important;
    border: 1px solid rgba(255, 255, 255, 0.8) !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.12) !important;
    transition: all 0.15s ease !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
}
.consulting-header .btn-compact-add,
a.consultingAddLink.btn-compact-add {
    color: #0891b2 !important;
}
.cb-header .btn-compact-add,
a.cbAddLink.btn-compact-add {
    color: #1e293b !important;
}
.btn-compact-add:hover,
a.consultingAddLink.btn-compact-add:hover,
a.cbAddLink.btn-compact-add:hover {
    background: #f8fafc !important;
    transform: translateY(-1px);
}
.btn-compact-ms,
a.cbAddLinkForAdmin.btn-compact-ms {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 9.5px !important;
    padding: 0 6px !important;
    height: 19px !important;
    line-height: 17px !important;
    border-radius: 3px !important;
    border: 1px solid rgba(255, 255, 255, 0.4) !important;
    transition: all 0.15s ease !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
}
.btn-compact-ms:hover,
a.cbAddLinkForAdmin.btn-compact-ms:hover {
    background: #ffffff !important;
    color: #1e293b !important;
}

/* Table Dimensions & Input Controls */
.compact-payment-table,
table.compact-payment-table.service_details {
    margin-bottom: 0 !important;
    border-collapse: collapse !important;
    width: 100% !important;
}
.compact-payment-table thead th,
table.compact-payment-table.service_details thead th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    padding: 2px 4px !important;
    height: 20px !important;
    line-height: 1.1 !important;
    border: 1px solid #e2e8f0 !important;
    border-top: none !important;
    vertical-align: middle !important;
}
.compact-payment-table tbody td,
table.compact-payment-table.service_details tbody td {
    padding: 1px 2px !important;
    height: 24px !important;
    border: 1px solid #e2e8f0 !important;
    vertical-align: middle !important;
    line-height: 1 !important;
    background: #ffffff;
}
.table-edit-control,
.compact-payment-table .table-edit-control {
    margin: 0 !important;
    padding: 0 !important;
}
.table-edit-control input, 
.table-edit-control select,
.compact-payment-table .table-edit-control input,
.compact-payment-table .table-edit-control select {
    height: 22px !important;
    min-height: 22px !important;
    max-height: 22px !important;
    padding: 0 5px !important;
    font-size: 11px !important;
    line-height: 20px !important;
    border-radius: 3px !important;
    border: 1px solid #cbd5e1 !important;
    box-shadow: none !important;
    background: #ffffff;
    color: #0f172a;
    width: 100% !important;
    box-sizing: border-box !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.table-edit-control input:focus,
.table-edit-control select:focus,
.compact-payment-table .table-edit-control input:focus,
.compact-payment-table .table-edit-control select:focus {
    border-color: #0b57cf !important;
    box-shadow: 0 0 0 2px rgba(11, 87, 207, 0.15) !important;
    outline: none !important;
}
.table-edit-control input.amountDecided,
.table-edit-control input.consultingAmountDecided,
.compact-payment-table .table-edit-control input.amountDecided,
.compact-payment-table .table-edit-control input.consultingAmountDecided {
    font-weight: 700 !important;
    text-align: right !important;
    color: #0f172a !important;
}
.table-edit-control input.paymentReceived,
.table-edit-control input.consultingPaymentReceived,
.compact-payment-table .table-edit-control input.paymentReceived,
.compact-payment-table .table-edit-control input.consultingPaymentReceived {
    font-weight: 700 !important;
    text-align: right !important;
    color: #15803d !important;
    background: #f0fdf4 !important;
    border-color: #86efac !important;
}
.table-edit-control input.balance,
.table-edit-control input.consultingBalance,
.compact-payment-table .table-edit-control input.balance,
.compact-payment-table .table-edit-control input.consultingBalance {
    font-weight: 800 !important;
    text-align: right !important;
    color: #b91c1c !important;
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
    cursor: default !important;
}
.cbDeleteIcon,
.consultingDeleteIcon,
.cbRowDelete,
.consultingRowDelete,
.compact-payment-table .cbDeleteIcon,
.compact-payment-table .consultingDeleteIcon,
.compact-payment-table .cbRowDelete,
.compact-payment-table .consultingRowDelete {
    width: 18px !important;
    height: 18px !important;
    min-width: 18px !important;
    font-size: 9px !important;
    line-height: 16px !important;
    padding: 0 !important;
    margin: 0 auto !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 3px !important;
    color: #dc3545 !important;
    background: #fff5f5 !important;
    border: 1px solid #fed7d7 !important;
    transition: all 0.15s ease !important;
    cursor: pointer !important;
}
.cbDeleteIcon:hover,
.consultingDeleteIcon:hover,
.cbRowDelete:hover,
.consultingRowDelete:hover {
    background: #dc3545 !important;
    color: #ffffff !important;
    border-color: #dc3545 !important;
}

/* DOTTED SEPARATOR LINE BETWEEN CONSULTING AND CB */
.payment-dotted-divider {
    display: flex !important;
    align-items: center !important;
    text-align: center !important;
    margin: 5px 0 4px 0 !important;
    position: relative !important;
}
.payment-dotted-divider::before,
.payment-dotted-divider::after {
    content: '' !important;
    flex: 1 !important;
    border-bottom: 1.5px dashed #94a3b8 !important;
}
.payment-dotted-divider .divider-label {
    padding: 1px 8px !important;
    background: #f1f5f9 !important;
    color: #334155 !important;
    font-size: 9.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    border-radius: 10px !important;
    border: 1px dashed #cbd5e1 !important;
    margin: 0 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    line-height: 15px !important;
}

/* ULTRA-COMPACT SUMMARY PANELS & HORIZONTAL MINI KPI STRIPS */
.compact-summary-panel {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 4px !important;
    padding: 3px 6px !important;
    margin-top: 2px !important;
}
.compact-summary-panel .summary-top-bar {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    margin-bottom: 2px !important;
}
.compact-summary-panel .summary-title {
    font-size: 10px !important;
    font-weight: 700 !important;
    color: #475569 !important;
    display: flex !important;
    align-items: center !important;
    line-height: 1 !important;
}
.header-invoice-btn,
.serviceInvoiceBtn.header-invoice-btn {
    background: #ffffff !important;
    color: #0891b2 !important;
    font-weight: 800 !important;
    font-size: 11px !important;
    padding: 1px 10px !important;
    height: 21px !important;
    line-height: 19px !important;
    border-radius: 4px !important;
    border: 1px solid rgba(255, 255, 255, 0.9) !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    transition: all 0.18s ease !important;
    cursor: pointer !important;
}
.header-invoice-btn:hover,
.serviceInvoiceBtn.header-invoice-btn:hover {
    background: #f0fdf4 !important;
    color: #15803d !important;
    border-color: #86efac !important;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
    transform: translateY(-1px) !important;
}
.compact-invoice-btn,
.serviceInvoiceBtn.compact-invoice-btn {
    background: #0891b2 !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    color: #ffffff !important;
    font-weight: 800 !important;
    font-size: 11px !important;
    padding: 1px 9px !important;
    height: 21px !important;
    line-height: 19px !important;
    border-radius: 4px !important;
    box-shadow: 0 1px 3px rgba(8, 145, 178, 0.3) !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    cursor: pointer !important;
}
.compact-invoice-btn:hover,
.serviceInvoiceBtn.compact-invoice-btn:hover {
    background: #0e7490 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}

/* Horizontal Mini KPI Chips */
.compact-kpi-row {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 4px !important;
}
.cb-summary-panel .compact-kpi-row {
    grid-template-columns: repeat(3, 1fr) !important;
}
.kpi-chip {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 3px !important;
    padding: 1px 6px !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    height: 22px !important;
    min-height: 22px !important;
    max-height: 22px !important;
    box-shadow: 0 1px 1px rgba(0,0,0,0.02) !important;
    overflow: hidden !important;
}
.kpi-chip-label {
    font-size: 8.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.2px !important;
    color: #64748b !important;
    line-height: 1 !important;
    margin: 0 !important;
    white-space: nowrap !important;
}
.kpi-chip-value {
    font-size: 11px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    color: #0f172a !important;
    margin: 0 !important;
    margin-left: 4px !important;
    white-space: nowrap !important;
}
.kpi-consult-decided { border-left: 3px solid #64748b !important; }
.kpi-consult-invoiced { border-left: 3px solid #0891b2 !important; background: #ecfeff !important; }
.kpi-consult-invoiced .kpi-chip-value { color: #0891b2 !important; }
.kpi-consult-received { border-left: 3px solid #16a34a !important; background: #f0fdf4 !important; }
.kpi-consult-received .kpi-chip-value { color: #15803d !important; }
.kpi-consult-balance.kpi-chip-danger { border-left: 3px solid #dc2626 !important; background: #fef2f2 !important; }
.kpi-consult-balance.kpi-chip-danger .kpi-chip-value { color: #b91c1c !important; }
.kpi-consult-balance.kpi-chip-success { border-left: 3px solid #16a34a !important; background: #f0fdf4 !important; }
.kpi-consult-balance.kpi-chip-success .kpi-chip-value { color: #15803d !important; }

.kpi-cb-decided { border-left: 3px solid #475569 !important; }
.kpi-cb-received { border-left: 3px solid #16a34a !important; background: #f0fdf4 !important; }
.kpi-cb-received .kpi-chip-value { color: #15803d !important; }
.kpi-cb-balance.kpi-chip-danger { border-left: 3px solid #dc2626 !important; background: #fef2f2 !important; }
.kpi-cb-balance.kpi-chip-danger .kpi-chip-value { color: #b91c1c !important; }
.kpi-cb-balance.kpi-chip-success { border-left: 3px solid #16a34a !important; background: #f0fdf4 !important; }
.kpi-cb-balance.kpi-chip-success .kpi-chip-value { color: #15803d !important; }

/* ========================================================
   MODAL DIALOG THEME ENHANCEMENTS
   ======================================================== */
.modal-content {
    border-radius: 12px !important;
    border: 1px solid #dce4ec !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.08) !important;
    overflow: hidden !important;
}
.modal-header {
    background: #0d52b3 !important;
    color: #ffffff !important;
    padding: 14px 20px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
}
.modal-header .close {
    color: #ffffff !important;
    opacity: 0.8 !important;
    text-shadow: none !important;
}
.modal-header .close:hover {
    opacity: 1 !important;
}
.modal-footer {
    background: #f8fafc !important;
    border-top: 1px solid #dce4ec !important;
    padding: 12px 20px !important;
}

/* ========================================================
   TOAST NOTIFICATION SYSTEM (TOASTER)
   ======================================================== */
#qms-toast-container {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 9999999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}
.qms-toast {
    pointer-events: auto;
    min-width: 300px;
    max-width: 400px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 10px 25px -5px rgba(15, 35, 70, 0.15), 0 8px 10px -6px rgba(15, 35, 70, 0.08);
    border: 1px solid #dce4ec;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    overflow: hidden;
    transform: translateX(120%);
    opacity: 0;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.qms-toast.show {
    transform: translateX(0);
    opacity: 1;
}
.qms-toast.hiding {
    transform: translateX(120%);
    opacity: 0;
}
.qms-toast-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 15px;
}
.qms-toast-success .qms-toast-icon {
    background: #ecfdf5;
    color: #10b981;
    border: 1px solid #a7f3d0;
}
.qms-toast-error .qms-toast-icon {
    background: #fef2f2;
    color: #ef4444;
    border: 1px solid #fecaca;
}
.qms-toast-info .qms-toast-icon {
    background: #eff6ff;
    color: #0d52b3;
    border: 1px solid #bfdbfe;
}
.qms-toast-body {
    flex: 1;
}
.qms-toast-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: 2px;
}
.qms-toast-msg {
    font-size: 11px;
    color: #64748b;
    line-height: 1.3;
}
.qms-toast-close {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    padding: 2px 4px;
    transition: color 0.15s;
}
.qms-toast-close:hover {
    color: #0f172a;
}
.qms-toast-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: #10b981;
    width: 100%;
    animation: qmsToastProgress 2.5s linear forwards;
}
.qms-toast-error .qms-toast-bar { background: #ef4444; }
.qms-toast-info .qms-toast-bar { background: #0d52b3; }
@keyframes qmsToastProgress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>
<div id="qms-toast-container"></div>