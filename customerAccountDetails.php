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

            $invoices = "SELECT inv.invoice_id, firm_id, customer_id, customer_name, inv.invoice_no, invoice_date, total_amount, svc_desc, cf.firm_name, firm_abbrevation FROM invoice_details inv LEFT JOIN consulting_firm cf ON inv.firm_id = cf.consulting_firm_id WHERE `customer_id` = :customer";
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
                        <div class="col-xs-12 qms_customer_name service_title" style="text-align: center" data-row-id="<?php echo $serviceId; ?>">Service - <span class="serviceStatus"><?php if (isset($service1['service_type'])) echo $service1['service_type']; ?></span>
                            <a class="saveService" id="saveService" data-row-id="<?php echo $serviceId; ?>" title="Save Service"><i class="fa fa-floppy-o pull-right " style="color:white; padding-right:10px; display: <?php echo $saveButton; ?>"></i></a>
                            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin()) { ?>
                                <a class="deleteService" id="deleteService" data-row-id="<?php echo $serviceId; ?>" title="Delete Service"><i class="fa fa-trash-o pull-right " style="color:white; padding-right:10px;"></i></a>
                            <?php } ?>
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
                            <input type="hidden" class="customer_id" data-row-id="<?php echo $serviceId; ?>" id="customer_id" name="customer_id" value="<?php echo $_GET['customerid'] ?>" 
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-sortable" style="margin-bottom:2px;">
                                <thead>
                                    <tr style="background-color:#f5f5f5;">
                                        <th colspan="8" style="padding:2px 8px; vertical-align:middle;">
                                            <small style="font-size:12px; font-weight:bold; color:#003399;">Contract Details - </small>
                                            <textarea name="audit-details" class="saveChanges auditDetailsText" data-column="marketing_audit_type" id="audit-details" rows="2" style="border:0; background:transparent; width:75%; outline:none; font-size:12px; line-height:16px; resize:none; overflow:hidden; padding:0; margin:0; box-shadow:none; border-radius:0; vertical-align:middle;"><?php
                                                if (isset($customer['marketing_audit_type']) && $customer['marketing_audit_type'] != "") {
                                                    echo $customer['marketing_audit_type'];
                                                }
                                                ?></textarea>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-sortable service_details" id="service_details<?php echo $serviceId; ?>">
                                <thead>
                                    <tr>
                                        <th class="" width="20%" style="padding:2px">
                                            <small>Installment Details</small>
                                        </th>
                                        <th class="" width="13%" style="padding:2px">
                                            <small>Amount Decided</small>
                                        </th>
                                        <th class="" width="13%" style="padding:2px">
                                            <small>Payment received</small>
                                        </th>
                                        <th class="" width="14%" style="padding:2px">
                                            <small>Date of Deposit</small>
                                        </th>
                                        <th class="" width="13%" style="padding:2px">
                                            <small>Instrument No</small>
                                        </th>
                                        <th class="" width="8%" style="padding:2px">
                                            <small>Bank A/c </small>
                                        </th>
                                        <th class="" width="11%" style="padding:2px">
                                            <small>Balance</small>
                                        </th>
                                        <th class="" width="4%" style="padding:2px">
                                            <i class="fa fa-close" aria-hidden="true"></i>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="">
                                    <tr class="cb_section">
                                        <td colspan=9 style="padding:0px;">CB  <?php if (!Utitlity::checkForUserConsultant()) { ?><a class="btn btn-xs btn-success cbAddLink pull-right" data-row-id="<?php echo $serviceId; ?>" data-row-count="<?php echo $cbCount['totalcb'] ?>" title="Add new cb row" style="padding: 1px 5px; "><i class="fa fa-plus-circle fa-lg " style="padding:3px"></i></a> <?php } ?> <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) { ?><a class = "cbAddLinkForAdmin pull-right btn btn-success btn-xs" data-row-id="<?php echo $serviceId; ?>" data-row-count="<?php echo $cbCount['totalcb'] ?>" title="Add Marketing Setup" style="padding: 1px 5px;"> MS </a > <?php } ?></td>
                                    </tr>
                                    <?php
                                    if ($cbCount['totalcb'] > 0) {
                                        foreach ($certList as $cert) {
                                            $a = strtoupper($cert['contract_details']);
                                            if ((startsWith($a, 'MARKETING SETUP') || startsWith($a, 'MARKETING-SETUP') || startsWith($a, 'MARKETING') || startsWith($a, 'SETUP') || startsWith($a, 'MARK') || startsWith($a, 'MARKE')) && !(Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin())) {
                                                $display = 'none';
                                            } else {
                                                $display = '';
                                            }
                                            ?>
                                            <tr class="cb_subsection previousCBData" id="cb_subsection_0" style="display: <?php echo $display ?>">
                                                <td class="" style="padding:0px;">
                                                    <div class="table-edit-control">
                                                        <label></label>
                                                        <input type="text" data-row-id="<?php echo $serviceId; ?>" class="contractDetails" id="contractDetails" name="contractDetails" value="<?php if (isset($cert['contract_details'])) echo $cert['contract_details']; ?>" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                    </div>
                                                </td>
                                                <td class="" style="padding:0px;">
                                                    <div class="table-edit-control">
                                                        <label></label>
                                                        <input type="number" data-row-id="<?php echo $serviceId; ?>" class="amountDecided" id="amountDecided" name="amountDecided" style="text-align: center" value="<?php if (isset($cert['amount_decided'])) echo $cert['amount_decided']; ?>" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                    </div>
                                                </td>
                                                <td class="" style="padding:0px;">
                                                    <div class="table-edit-control">
                                                        <label></label>
                                                        <input type="number" data-row-id="<?php echo $serviceId; ?>" class="paymentReceived" id="paymentReceived" name="paymentReceived" style="text-align: center" value="<?php if (isset($cert['payment_received'])) echo $cert['payment_received']; ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                    </div>
                                                </td>
                                                <td class="" style="padding:0px;">
                                                    <div class="table-edit-control">
                                                        <label></label>
                                                        <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassTo ?>"  name="depositDate" style="text-align: center" value="<?php if (isset($cert['deposit_date'])) echo simpleDateConversion($cert['deposit_date']); ?>" placeholder="" <?php echo $fieldAccess ?>>
                                                    </div>
                                                </td>
                                                <td class="" style="padding:0px;">
                                                    <div class="table-edit-control">
                                                        <label></label>
                                                        <input type="text" data-row-id="<?php echo $serviceId; ?>" class="chequeDetails" id="chequeDetails" name="chequeDetails" value="<?php if (isset($cert['check_details'])) echo $cert['check_details']; ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                    </div>
                                                </td>
                                                <td class="" style="padding:0px;">                                                    
                                                    <div class="table-edit-control">
                                                        <?php
                                                        $cbBank = "SELECT * FROM consulting_abbrevation order by id desc";
                                                        $statement = $db->prepare($cbBank);
                                                        $statement->execute();
                                                        $certAbbr = $statement->fetchAll();
                                                        ?>
                                                        <label></label>
                                                        <select class="bankAcccountNo" id="bankAcccountNo" name="bankAcccountNo" data-row-id="<?php echo $serviceId; ?>" value="<?php if (isset($cert['bank_account'])) echo $cert['bank_account']; ?>" placeholder="" <?php echo $fieldAccess ?>>
                                                            <option value="<?php if (isset($cert['bank_account'])) echo $cert['bank_account'] ?>" ><?php
                                                                if (isset($cert['bank_account']) && !empty($cert['bank_account'])) {
                                                                    echo $cert['bank_account'];
                                                                } else {
                                                                    echo "Select";
                                                                }
                                                                ?>
                                                            </option>
                                                            <?php foreach ($certAbbr as $bankAb) { ?>
                                                                <option value="<?php if (isset($bankAb['bank_abbr'])) echo $bankAb['bank_abbr'] ?>" ><?php if (isset($bankAb['bank_abbr'])) echo $bankAb['bank_abbr'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <!--<input type="text" class="bankAcccountNo" data-row-id="<?php echo $serviceId; ?>" id="bankAcccountNo" name="bankAcccountNo" value="<?php if (isset($cert['bank_account'])) echo $cert['bank_account']; ?>" placeholder="" <?php echo $fieldAccess ?> />-->
                                                    </div>
                                                </td>
                                                <td class="" style="padding:0px;">
                                                    <div class="table-edit-control">
                                                        <label></label>
                                                        <input type="text" data-row-id="<?php echo $serviceId; ?>" class="balance" id="balance" name="balance" style="text-align: center" value="<?php if (isset($cert['balance'])) echo $cert['balance']; ?>" placeholder="" readonly="" <?php echo $fieldAccess ?> />
                                                    </div>
                                                </td>
                                                <td class="" style="padding:0px; text-align:center;">
                                                    <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                        <input type="checkbox" class="cbDelete" data-row-id="<?php echo $serviceId; ?>" value="<?php echo $cert['certification_id'] ?>" style="display:none;">
                                                        <a href="javascript:void(0)" class="cbDeleteIcon" data-row-id="<?php echo $serviceId; ?>" data-id="<?php echo $cert['certification_id']; ?>"><i class="fa fa-close" aria-hidden="true"></i></a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="cb-row-comment <?php echo $cert['certification_id'] ?>" id="cb-row-comment" name="cb-row-comment" value="">
                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="cb-row-id <?php echo $cert['certification_id'] ?>" id="cb-row-id" name="cb-row-id" value="<?php if (isset($cert['certification_id'])) echo $cert['certification_id']; ?>">
                                    <?php }
                                    ?>

                                <?php } else {
                                    ?>
                                    <tr class="cb_subsection" id="cb_subsection_0" style="padding:0px;">
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="contractDetails" id="contractDetails" name="contractDetails" value="" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="number" data-row-id="<?php echo $serviceId; ?>" class="amountDecided" id="amountDecided" name="amountDecided" style="text-align: center" value="" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="number" data-row-id="<?php echo $serviceId; ?>" class="paymentReceived" id="paymentReceived" name="paymentReceived" style="text-align: center" value="" placeholder="" <?php echo $fieldAccess ?> />
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassTo ?>"  name="depositDate" value="" style="text-align: center" placeholder="" <?php echo $fieldAccess ?> />
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="chequeDetails" id="chequeDetails" name="chequeDetails" value="" placeholder="" <?php echo $fieldAccess ?> />
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="bankAcccountNo" id="bankAcccountNo" name="bankAcccountNo" value="" placeholder="" <?php echo $fieldAccess ?> />
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" class="balance" data-row-id="<?php echo $serviceId; ?>" id="balance" name="balance" style="text-align: center" value="" placeholder="" readonly="" />
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px; text-align:center;">
                                            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                <a href="javascript:void(0)" class="cbRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <input type="hidden" class="contractDetailsHidden" id="contractDetailsHidden" name="contractDetailsHidden" value="">
                                <tr class="consulting_section">
                                    <td colspan=9 style="padding:0px">Consulting <?php if (!Utitlity::checkForUserConsultant()) { ?><a class="consultingAddLink btn btn-warning btn-xs pull-right" title="Add new consulting row" data-row-id="<?php echo $serviceId; ?>" data-row-count="<?php echo $consultingsListCount['totalconsulting'] ?>"><i class="fa fa-plus-circle fa-lg pull-right" style="padding:3px; "></i></a><?php } ?></td>
                                </tr>
                                <?php
                                if ($consultingsListCount['totalconsulting'] > 0) {
                                    foreach ($consultingList as $consult) {
                                        ?>
                                        <tr class="consulting_subsection previousConsultingData">
                                            <td class="" style="padding:0px;">
                                                <div class="table-edit-control">
                                                    <label></label>
                                                    <input data-row-id="<?php echo $serviceId; ?>" type="text" class="consultingContractDetails" id="consultingContractDetails" name="consultingContractDetails" value="<?php if (isset($consult['consulting_fees'])) echo $consult['consulting_fees']; ?>" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                </div>
                                            </td>
                                            <td class="" style="padding:0px;">
                                                <div class="table-edit-control">
                                                    <label></label>
                                                    <input type="number" data-row-id="<?php echo $serviceId; ?>" class="consultingAmountDecided" id="consultingAmountDecided" name="consultingAmountDecided" style="text-align: center" value="<?php if (isset($consult['consulting_amount_decided'])) echo $consult['consulting_amount_decided']; ?>" placeholder="" <?php echo $fieldAccess ?> <?php echo $financeAccess ?>/>
                                                </div>
                                            </td>
                                            <td class="" style="padding:0px;">
                                                <div class="table-edit-control">
                                                    <label></label>
                                                    <input type="number" data-row-id="<?php echo $serviceId; ?>" class="consultingPaymentReceived" id="consultingPaymentReceived" name="consultingPaymentReceived" style="text-align: center" value="<?php if (isset($consult['consulting_payment_received'])) echo $consult['consulting_payment_received']; ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                </div>
                                            </td>
                                            <td class="" style="padding:0px;">
                                                <div class="table-edit-control">
                                                    <label></label>
                                                    <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassToConsulting ?>"  name="consultingDepositDate" style="text-align: center" value="<?php if (isset($consult['consulting_deposit_date'])) echo simpleDateConversion($consult['consulting_deposit_date']); ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                </div>
                                            </td>
                                            <td class="" style="padding:0px;">
                                                <div class="table-edit-control">
                                                    <label></label>
                                                    <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingChequeDetails" id="consultingChequeDetails" name="consultingChequeDetails" value="<?php if (isset($consult['consulting_chque_details'])) echo $consult['consulting_chque_details']; ?>" placeholder="" <?php echo $fieldAccess ?> />
                                                </div>
                                            </td>
                                            <td class="" style="padding:0px;">
                                                <div class="table-edit-control">
                                                    <?php
                                                    $cfrmAbr = "SELECT * FROM consulting_abbrevation ";
                                                    $statement = $db->prepare($cfrmAbr);
                                                    $statement->execute();
                                                    $abList = $statement->fetchAll();
                                                    ?>
                                                    <label></label>
                                                    <select class="consultingBankAcccountNo" id="consultingBankAcccountNo" name="consultingBankAcccountNo" data-row-id="<?php echo $serviceId; ?>" value="<?php if (isset($consult['bank_ac'])) echo $consult['bank_ac']; ?>" placeholder="" <?php echo $fieldAccess ?>>
                                                        <option value="<?php if (isset($consult['bank_ac'])) echo $consult['bank_ac'] ?>" ><?php
                                                            if (isset($consult['bank_ac']) && !empty($consult['bank_ac'])) {
                                                                echo $consult['bank_ac'];
                                                            } else {
                                                                echo "Select";
                                                            }
                                                            ?>
                                                        </option>
                                                        <?php foreach ($abList as $bankAb) { ?>
                                                            <option value="<?php if (isset($bankAb['bank_abbr'])) echo $bankAb['bank_abbr'] ?>" ><?php if (isset($bankAb['bank_abbr'])) echo $bankAb['bank_abbr'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <!--<input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingBankAcccountNo" id="consultingBankAcccountNo" name="consultingBankAcccountNo" value="<?php if (isset($consult['bank_ac'])) echo $consult['bank_ac']; ?>" placeholder="" <?php echo $fieldAccess ?> />-->
                                                </div>
                                            </td>
                                            <td class="" style="padding:0px;">
                                                <div class="table-edit-control">
                                                    <label></label>
                                                    <input type="text" class="consultingBalance" data-row-id="<?php echo $serviceId; ?>" id="consultingBalance" name="consultingBalance" style="text-align: center" value="<?php if (isset($consult['consulting_balance'])) echo $consult['consulting_balance']; ?>" placeholder="" readonly="" />
                                                </div>
                                            </td>
                                            <td class="" style="padding:0px; text-align:center;">
                                                <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                    <input type="checkbox" class="consultingDelete" data-row-id="<?php echo $serviceId; ?>" value="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>" style="display:none;"/>
                                                    <a href="javascript:void(0)" class="consultingDeleteIcon" data-row-id="<?php echo $serviceId; ?>" data-id="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>"><i class="fa fa-close" aria-hidden="true"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consulting-row-id <?php echo $consult['consulting_id'] ?>" id="consulting-row-id" name="consulting-row-id" value="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>">
                                    <?php }
                                    ?>

                                <?php } else {
                                    ?>
                                    <tr class="consulting_subsection">
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingContractDetails" id="consultingContractDetails" name="consultingContractDetails" value="" placeholder="" <?php echo $fieldAccess ?><?php echo $financeAccess ?>/>
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="number" data-row-id="<?php echo $serviceId; ?>" class="consultingAmountDecided" id="consultingAmountDecided" style="text-align: center" name="consultingAmountDecided" value="" placeholder="" <?php echo $fieldAccess ?><?php echo $financeAccess ?>/>
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="number" data-row-id="<?php echo $serviceId; ?>" class="consultingPaymentReceived" id="consultingPaymentReceived" style="text-align: center" name="consultingPaymentReceived" value="" placeholder="" <?php echo $fieldAccess ?>/>
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassToConsulting ?>" name="consultingDepositDate" style="text-align: center" value="" placeholder="" <?php echo $fieldAccess ?>/>
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingChequeDetails" id="consultingChequeDetails" name="consultingChequeDetails" value="" placeholder="" <?php echo $fieldAccess ?>/>
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingBankAcccountNo" id="consultingBankAcccountNo" name="consultingBankAcccountNo" value="" placeholder="" <?php echo $fieldAccess ?>/>
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="table-edit-control">
                                                <label></label>
                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingBalance" id="consultingBalance" name="consultingBalance" style="text-align: center" value="" placeholder="" readonly="" />
                                            </div>
                                        </td>
                                        <td class="" style="padding:0px; text-align:center;">
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