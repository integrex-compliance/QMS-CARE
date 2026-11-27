<div id="wrapper" class="active">
    <?php
    include_once './header.php';
    include_once './dbConnect.php';
    include './customerScripting.php';
    require_once './customer_service_flow.php';
    require_once './client_ledger.php';
    ensureCustomerServiceFlow($db);
    if (empty($_SESSION['service_flow_csrf'])) $_SESSION['service_flow_csrf'] = bin2hex(random_bytes(32));
    $flowStmt = $db->prepare('SELECT f.*, s.service_type AS current_stage FROM customer_service_flow f LEFT JOIN services s ON s.service_id = f.service_id AND s.customer_id = f.customer_id WHERE f.customer_id = ? ORDER BY f.id');
    $flowStmt->execute([$_GET['customerid'] ?? '']);
    $customerFlows = $flowStmt->fetchAll(PDO::FETCH_ASSOC);
    $flowByService = [];
    foreach ($customerFlows as $flow) $flowByService[$flow['service_id']] = $flow;
    ?>
    <link rel="stylesheet" href="css/customer-service-flow.css?v=<?php echo filemtime(__DIR__ . '/css/customer-service-flow.css'); ?>">
    <script src="js/customer-service-flow.js?v=<?php echo filemtime(__DIR__ . '/js/customer-service-flow.js'); ?>" defer></script>
    <link rel="stylesheet" href="css/client-ledger.css?v=<?php echo filemtime(__DIR__ . '/css/client-ledger.css'); ?>">
    <script src="js/payment-input-guard.js?v=<?php echo filemtime(__DIR__ . '/js/payment-input-guard.js'); ?>" defer></script>
    <style>
        #wrapper input[type="number"] { -moz-appearance: textfield; appearance: textfield; }
        #wrapper input[type="number"]::-webkit-inner-spin-button,
        #wrapper input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        .payment-firm-line { margin: 0 0 6px; padding: 4px 6px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; color: #475569; font-size: 11.5px; overflow-wrap: anywhere; }
        .payment-firm-line strong { color: #193048; }
    </style>
    <?php

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
                $contractAttachments = [];
                try {
                    $attStmt = $db->prepare("SELECT * FROM service_contract_attachments WHERE customer_id = :cid AND service_id = :sid ORDER BY id ASC");
                    $attStmt->execute([':cid' => $_GET['customerid'], ':sid' => $serviceId]);
                    $contractAttachments = $attStmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {}
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

                // Separate Consulting & Payment Totals
                $consultTotalDecided = 0;
                $consultTotalReceived = 0;
                $consultTotalTds = 0;
                $consultTotalShortSettle = 0;
                $consultTotalBadDebt = 0;
                if (!empty($consultingList)) {
                    foreach ($consultingList as $conItem) {
                        $cDec = (isset($conItem['consulting_amount_decided']) && is_numeric($conItem['consulting_amount_decided'])) ? (float)$conItem['consulting_amount_decided'] : 0;
                        $cRcv = (isset($conItem['consulting_payment_received']) && is_numeric($conItem['consulting_payment_received'])) ? (float)$conItem['consulting_payment_received'] : 0;
                        $cTds = (isset($conItem['tds_deducted']) && is_numeric($conItem['tds_deducted'])) ? (float)$conItem['tds_deducted'] : 0;
                        $cShort = (isset($conItem['short_settle']) && is_numeric($conItem['short_settle'])) ? (float)$conItem['short_settle'] : 0;
                        $cBad = (isset($conItem['bad_debt']) && is_numeric($conItem['bad_debt'])) ? (float)$conItem['bad_debt'] : 0;

                        $cLabel = isset($conItem['consulting_fees']) ? strtolower(trim($conItem['consulting_fees'])) : '';
                        if (stripos($cLabel, 'tds') !== false && $cTds == 0) {
                            $cTds = $cRcv > 0 ? $cRcv : $cDec;
                            $cRcv = 0;
                        }
                        if (stripos($cLabel, 'loss') !== false && $cBad == 0) {
                            $cBad = $cRcv > 0 ? $cRcv : $cDec;
                            $cRcv = 0;
                        }
                        if (stripos($cLabel, 'short') !== false && $cShort == 0) {
                            $cShort = $cRcv > 0 ? $cRcv : $cDec;
                            $cRcv = 0;
                        }

                        $consultTotalDecided += $cDec;
                        $consultTotalReceived += $cRcv;
                        $consultTotalTds += $cTds;
                        $consultTotalShortSettle += $cShort;
                        $consultTotalBadDebt += $cBad;
                    }
                }
                $effectiveCleared = $consultTotalReceived + $consultTotalTds + $consultTotalShortSettle + $consultTotalBadDebt;
                $targetAmount = $svcTotalInvoiced;
                $paymentDiff = $targetAmount - $effectiveCleared;
                $paymentBalance = max(0, $paymentDiff);
                $paymentExcess = ($effectiveCleared > $targetAmount) ? ($effectiveCleared - $targetAmount) : 0;
                $isMatched = ($targetAmount > 0 && abs($paymentDiff) < 0.01);
                $consultInvoiceBalance = $paymentBalance;

                $svcTotalDecided = $cbTotalDecided + $consultTotalDecided;
                $svcTotalReceived = $cbTotalReceived + $consultTotalReceived;

            $addServicesTo = "SELECT * FROM add_to_service";
            $statement = $db->prepare($addServicesTo);
            $statement->execute();
            $addTODo = $statement->fetchAll();

            $allServiceType = "SELECT * FROM service_type ORDER BY id ASC";
            $statement = $db->prepare($allServiceType);
            $statement->execute();
            $allServices = $statement->fetchAll();
            $allMasterServiceTypes = $allServices;
            $allServiceStages = [];
            $stageQueryOk = false;
            try {
                $allServiceStageSql = "SELECT stage FROM service_stage ORDER BY id ASC";
                $stageStmt = $db->query($allServiceStageSql);
                if ($stageStmt) {
                    $allServiceStages = $stageStmt->fetchAll(PDO::FETCH_COLUMN);
                    $stageQueryOk = true;
                }
            } catch (Exception $e) {}
            if (!$stageQueryOk) {
                $allServiceStages = ['CA', 'SA-1', 'SA-2', 'RC', 'New enq', 'CNE', 'Mnt.'];
            }

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
            $clientLedger = loadClientLedger($db, $_GET['customerid'], $giveninvoices);

            $unassignedSql = "SELECT inv.invoice_id, inv.firm_id, inv.customer_id, inv.service_id, inv.customer_name, inv.invoice_no, inv.invoice_date, inv.net_price, inv.total_amount, inv.svc_desc, cf.firm_name, cf.firm_abbrevation FROM invoice_details inv LEFT JOIN consulting_firm cf ON inv.firm_id = cf.consulting_firm_id WHERE inv.customer_id = :customer AND (inv.service_id IS NULL OR inv.service_id = '' OR inv.service_id = 0) AND inv.is_deleted = 'No' ORDER BY inv.invoice_date DESC, inv.invoice_id DESC";
            $unassignedStmt = $db->prepare($unassignedSql);
            $unassignedStmt->bindParam(':customer', $_GET["customerid"]);
            $unassignedStmt->execute();
            $unassignedInvoices = $unassignedStmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <div class="qms-card-inner">
                            <!-- Card Header -->
                            <div class="qms_customer_name">
                                <div class="qms-header-left">
                                    <span class="qms-header-icon"><i class="fa fa-building"></i></span>
                                    <span class="qms-company-title"><?php echo $customer['company_name']; ?></span>
                                    <?php if (!empty($customer["priority"]) && $customer["priority"] == "High") { ?>
                                        <img class="img-responsive" alt="High Priority User" title="High Priority User" src="images/medium.png" style="background-color: transparent; height: 18px; display:inline" />
                                    <?php } else if (!empty($customer["priority"]) && $customer["priority"] == "Medium") { ?>
                                        <img class="img-responsive" alt="Medium Priority User" title="Medium Priority User" src="images/low.png" style="background-color: transparent; height: 16px; display:inline" />
                                    <?php } else if (!empty($customer["priority"]) && $customer["priority"] == "Low") { ?>
                                        <img class="img-responsive" alt="Low Priority User" title="Low Priority User" src="images/high.png" style="background-color: transparent; height: 15px; display:inline" />
                                    <?php } ?>
                                </div>
                                <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing()) { ?>
                                <div class="qms-header-actions">
                                    <a class="editCustomerDetails" data-id="<?php echo $_GET['customerid'] ?>" title="Edit customer details"><i class="fa fa-edit"></i></a>
                                    <a class="deleteCustomerDetails" data-id="<?php echo $_GET['customerid'] ?>" title="Delete customer details"><i class="fa fa-trash-o"></i></a>
                                    <a class="customerAttachemnts" data-id="<?php echo $_GET['customerid'] ?>" title="Attach Documents"><i class="fa fa-link"></i></a>
                                </div>
                                <?php } ?>
                            </div>

                            <!-- Customer Contact Info & Notes Grid -->
                            <div class="qms-profile-body">
                                <div class="qms-profile-info-col">
                                    <div class="qms-info-item">
                                        <span class="qms-info-icon" title="Address"><i class="fa fa-map-marker"></i></span>
                                        <span class="qms-info-text"><?php echo $customer['company_address']; ?></span>
                                    </div>

                                    <div class="qms-info-split-row">
                                        <div class="qms-info-item">
                                            <span class="qms-info-icon" title="Contact Person"><i class="fa fa-user"></i></span>
                                            <span class="qms-info-text"><?php echo $customer['contact_person']; ?></span>
                                        </div>
                                        <div class="qms-info-item">
                                            <span class="qms-info-icon" title="Phone"><i class="fa fa-phone"></i></span>
                                            <span class="qms-info-text"><?php echo $customer['contact_no']; ?></span>
                                        </div>
                                    </div>

                                    <div class="qms-info-item">
                                        <span class="qms-info-icon" title="Email Id"><i class="fa fa-envelope"></i></span>
                                        <span class="qms-info-text"><?php echo $customer['contact_email']; ?></span>
                                    </div>

                                    <?php if (!empty($customer['gst_number'])) { ?>
                                    <div class="qms-info-item">
                                        <span class="qms-info-icon" title="GST Number"><i class="fa fa-credit-card"></i></span>
                                        <span class="qms-info-text"><?php echo $customer['gst_number']; ?></span>
                                    </div>
                                    <?php } ?>

                                    <div class="qms-info-split-row">
                                        <?php if (!Utitlity::checkForUserConsultant() && !empty($customer['client_reference'])) { ?>
                                        <div class="qms-info-item">
                                            <span class="qms-info-icon" title="Client referred by"><i class="fa fa-share-square-o"></i></span>
                                            <span class="qms-info-text"><?php echo $customer['client_reference']; ?></span>
                                        </div>
                                        <?php } ?>
                                        <?php if (!empty($customer['company_location'])) { ?>
                                        <div class="qms-info-item">
                                            <span class="qms-info-icon" title="G-Map / Location"><i class="fa fa-globe"></i></span>
                                            <span class="qms-info-text"><?php echo $customer['company_location']; ?></span>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="qms-profile-notes-col">
                                    <label for="add-notes" class="qms-notes-label"><i class="fa fa-sticky-note-o"></i> Notes</label>
                                    <textarea name="add-notes" rows="4" class="form-group saveChanges customercomment lookslike-label" data-column="comments" id="add-notes" placeholder="Add customer notes here..."><?php
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
                                                    echo "";
                                            } else
                                                echo $customer['comments'];
                                        }
                                        ?></textarea>
                                </div>
                            </div>

                            <!-- Type of Service Selector Band -->
                            <?php
                            $customerAssignedServices = isset($customer['type_of_service']) ? $customer['type_of_service'] : '';
                            $assignedServicesArray = array_values(array_filter(array_map('trim', explode(',', $customerAssignedServices))));
                            $isReadOnlyServiceType = (Utitlity::checkForUserConsultant() || Utitlity::checkForUserFinance());
                            ?>
                            <div class="customer-type-of-service-row">
                                <div class="tos-row-inner">
                                    <div class="tos-label-col">
                                        <span class="tos-label-icon"><i class="fa fa-tags"></i></span>
                                        <span>Type of Service</span>
                                        <span class="tos-count-pill customer-selected-services-count" title="Added services"><?php echo count($assignedServicesArray); ?></span>
                                    </div>

                                    <div class="tos-add-panel" id="tosMultiselectWrapper">
                                        <?php if (!$isReadOnlyServiceType) { ?>
                                        <div class="tos-add-controls">
                                            <select id="tosAddServiceSelect" class="tos-add-select" aria-label="Choose a service to add" aria-describedby="tosAddHint">
                                                <option value="">Select service...</option>
                                                <?php foreach ($allMasterServiceTypes ?? [] as $mst) {
                                                    $sName = trim($mst['service']);
                                                    if ($sName === '') continue;
                                                    $isSelected = in_array($sName, $assignedServicesArray, true);
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($sName, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $isSelected ? 'disabled' : ''; ?>><?php echo htmlspecialchars($sName, ENT_QUOTES, 'UTF-8'); ?></option>
                                                <?php } ?>
                                            </select>
                                            <button type="button" id="tosAddServiceButton" class="tos-add-button" disabled><i class="fa fa-plus" aria-hidden="true"></i> Add</button>
                                        </div>
                                        <span id="tosAddHint" class="sr-only" aria-live="polite">Add one service at a time.</span>
                                        <?php } ?>
                                        <div class="tos-selected-tags" id="tosSelectedTags" aria-live="polite">
                                            <?php if (empty($assignedServicesArray)) { ?>
                                                <span class="tos-placeholder">No services added yet.</span>
                                            <?php } else { foreach ($assignedServicesArray as $selSvc) { ?>
                                                <span class="tos-tag-chip" data-service="<?php echo htmlspecialchars($selSvc, ENT_QUOTES, 'UTF-8'); ?>"><span><?php echo htmlspecialchars($selSvc, ENT_QUOTES, 'UTF-8'); ?></span></span>
                                            <?php } } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Milestone Dates Row -->
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
                                    <?php include './customer_service_milestones.php'; ?>
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
                            <div class="col-xs-6 text-center btn qms_complete_btn show-all-invoices client-ledger-toggle" role="button" tabindex="0" aria-expanded="false" aria-controls="client-ledger-panel">
                                <span><i class="fa fa-inr" aria-hidden="true"></i> Payment System Summary / Client Ledger</span>
                            </div>
                        </div>
                        <div class="row hidden-row" id="client-ledger-panel" style="display: none">
                            <div class="col-xs-12">
                                <p id="client-ledger-status" class="text-muted" role="status" style="margin: 8px 0;"></p>
                                <?php include __DIR__ . '/include/client_ledger_view.php'; ?>
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

            $service = "SELECT (select max(service_id) FROM services WHERE customer_id = :customerid) as maxid ,service_id, customer_id, service_type, cert_body, consulting_firm, consultant, audit_type, auditor, audit_date, created_at, add_service_to, handel_by, is_certified, service_notes FROM services WHERE customer_id = :customerid";
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
                $serviceIndex = $serviceNo;
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
                $contractAttachments = [];
                try {
                    $attStmt = $db->prepare("SELECT * FROM service_contract_attachments WHERE customer_id = :cid AND service_id = :sid ORDER BY id ASC");
                    $attStmt->execute([':cid' => $_GET['customerid'], ':sid' => $serviceId]);
                    $contractAttachments = $attStmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {}
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

                // Separate Consulting & Payment Totals (per service)
                $consultTotalDecided = 0;
                $consultTotalReceived = 0;
                $consultTotalTds = 0;
                $consultTotalShortSettle = 0;
                $consultTotalBadDebt = 0;
                if (!empty($consultingList)) {
                    foreach ($consultingList as $conItem) {
                        $cDec = (isset($conItem['consulting_amount_decided']) && is_numeric($conItem['consulting_amount_decided'])) ? (float)$conItem['consulting_amount_decided'] : 0;
                        $cRcv = (isset($conItem['consulting_payment_received']) && is_numeric($conItem['consulting_payment_received'])) ? (float)$conItem['consulting_payment_received'] : 0;
                        $cTds = (isset($conItem['tds_deducted']) && is_numeric($conItem['tds_deducted'])) ? (float)$conItem['tds_deducted'] : 0;
                        $cShort = (isset($conItem['short_settle']) && is_numeric($conItem['short_settle'])) ? (float)$conItem['short_settle'] : 0;
                        $cBad = (isset($conItem['bad_debt']) && is_numeric($conItem['bad_debt'])) ? (float)$conItem['bad_debt'] : 0;

                        $cLabel = isset($conItem['consulting_fees']) ? strtolower(trim($conItem['consulting_fees'])) : '';
                        if (stripos($cLabel, 'tds') !== false && $cTds == 0) {
                            $cTds = $cRcv > 0 ? $cRcv : $cDec;
                            $cRcv = 0;
                        }
                        if (stripos($cLabel, 'loss') !== false && $cBad == 0) {
                            $cBad = $cRcv > 0 ? $cRcv : $cDec;
                            $cRcv = 0;
                        }
                        if (stripos($cLabel, 'short') !== false && $cShort == 0) {
                            $cShort = $cRcv > 0 ? $cRcv : $cDec;
                            $cRcv = 0;
                        }

                        $consultTotalDecided += $cDec;
                        $consultTotalReceived += $cRcv;
                        $consultTotalTds += $cTds;
                        $consultTotalShortSettle += $cShort;
                        $consultTotalBadDebt += $cBad;
                    }
                }
                $effectiveCleared = $consultTotalReceived + $consultTotalTds + $consultTotalShortSettle + $consultTotalBadDebt;
                $targetAmount = $svcTotalInvoiced;
                $paymentDiff = $targetAmount - $effectiveCleared;
                $paymentBalance = max(0, $paymentDiff);
                $paymentExcess = ($effectiveCleared > $targetAmount) ? ($effectiveCleared - $targetAmount) : 0;
                $isMatched = ($targetAmount > 0 && abs($paymentDiff) < 0.01);
                $consultInvoiceBalance = $paymentBalance;

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
                <div class="servicebox" data-flow-name="<?php echo htmlspecialchars($flowByService[$serviceId]['service_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-flow-active="<?php echo $flowByService[$serviceId]['active'] ?? 1; ?>" style="display:<?php echo $saveButton ?> <?php //echo $srvcDisplay       ?>">
                    <div class="row" id="service-row<?php echo $serviceId; ?>" class="service-row"> 
                        <div class="col-xs-12 qms_customer_name service_title" data-row-id="<?php echo $serviceId; ?>" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; padding: 8px 14px;">
                            <div class="service-title-left" style="display: flex; align-items: center; gap: 8px; min-width: 80px;">
                                <i class="fa <?php echo ($displayHtmlStr == '') ? 'fa-folder-open-o' : 'fa-folder-o'; ?> service-folder-icon" style="color: #ffffff; font-size: 15px;"></i>
                                <?php if (count($currentServiceInvoices) > 0) { ?>
                                    <span class="badge badge-inv-count" title="Total Invoices for this Service" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.35); color: #ffffff; font-size: 10.5px; vertical-align: middle; padding: 2px 6px;">
                                        <i class="fa fa-file-text-o"></i> <?php echo count($currentServiceInvoices); ?> Inv
                                    </span>
                                <?php } ?>
                            </div>

                            <div class="service-title-center" style="flex: 1; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <span style="font-weight: 600; font-size: 13.5px; letter-spacing: 0.04em; color: #ffffff; text-transform: uppercase;">
                                    <?php if (isset($flowByService[$serviceId])) echo htmlspecialchars($flowByService[$serviceId]['service_name'], ENT_QUOTES, 'UTF-8') . ' &mdash; '; else echo 'SERVICE &mdash; '; ?> <span class="serviceStatus" style="color: #fde047; font-weight: 700; font-size: 14px; letter-spacing: 0.04em;"><?php if (isset($service1['service_type'])) echo $service1['service_type']; ?></span>
                                </span>
                            </div>

                            <div class="service-title-right" style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; min-width: 80px;">
                                <a class="saveService" data-row-id="<?php echo $serviceId; ?>" style="display:none !important;"></a>

                                <?php if (isset($flowByService[$serviceId]) && (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin())) { ?>
                                    <button type="button" class="flow-delete-service" data-flow-id="<?php echo (int)$flowByService[$serviceId]['id']; ?>" title="Delete service" aria-label="Delete <?php echo htmlspecialchars($flowByService[$serviceId]['service_name'], ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                <?php } ?>
                                <?php if (!isset($flowByService[$serviceId]) && (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin())) { ?>
                                    <a class="deleteService" id="deleteService" data-row-id="<?php echo $serviceId; ?>" title="Delete Service" style="color: #dc3545; background: #ffffff; width: 24px; height: 24px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; text-decoration: none; transition: all 0.15s ease;"><i class="fa fa-trash-o"></i></a>
                                <?php } ?>
                                <i class="fa <?php echo ($displayHtmlStr == '') ? 'fa-chevron-up' : 'fa-chevron-down'; ?> service-collapse-indicator" style="color: #ffffff; font-size: 13px; margin-left: 4px; transition: transform 0.2s;" title="Click to collapse / expand"></i>
                            </div>
                        </div>
                    </div>
                    <div class="servicebox-data" id="servicebox-data<?php if (isset($serviceId)) echo $serviceId; ?>" <?php echo $displayHtmlStr ?>>
                        <div class="row service-detail-row service-detail-row-top" style="padding-top:2px;">
                            <?php if (isset($flowByService[$serviceId])) { ?>
                                <input type="hidden" class="service-type" data-row-id="<?php echo $serviceId; ?>" value="<?php echo htmlspecialchars($service1['service_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?php } else { ?>
                            <div class="col-xs-3 form-group float-label-control">
                                <label>Service Stage</label>
                                <?php $currentStage = isset($service1['service_type']) ? trim((string)$service1['service_type']) : ''; ?>
                                <select class="service-type" id="service-type" name="service-type" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?><?php echo $fieldAccess ?>>
                                    <?php if ($currentStage !== '') { ?>
                                        <option value="<?php echo htmlspecialchars($currentStage, ENT_QUOTES, 'UTF-8') ?>" selected><?php echo htmlspecialchars($currentStage, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php } else { ?>
                                        <option value="">Select Service Stage</option>
                                    <?php } ?>
                                    <?php foreach ($allServiceStages as $stg) {
                                        if ($stg === $currentStage) continue;
                                    ?>
                                        <option value="<?php echo htmlspecialchars($stg, ENT_QUOTES, 'UTF-8') ?>"><?php echo htmlspecialchars($stg, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php } ?>
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
                            </div>
                            <div class="col-xs-3 form-group float-label-control">
                                <label>Lead Consultant</label>
                                <select class="consultant-list" id="consultant-list" name="consultant-list" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?> <?php echo $fieldAccess ?>>
                                    <option value="<?php if (isset($service1['consultant'])) echo $service1['consultant'] ?>" ><?php
                                        if (isset($service1['consultant']) && !empty($service1['consultant'])) {
                                            echo $service1['consultant'];
                                        } else {
                                            echo "Select Lead Consultant";
                                        }
                                        ?> </option>
                                    <?php foreach ($consultant as $cunslt) { ?>
                                        <option value="<?php echo $cunslt['consultant_name'] ?>"><?php echo $cunslt['consultant_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="service-id" id="service-id" name="service-id" value="<?php if (isset($serviceId)) echo $serviceId; ?>">
                        </div>
                        <div class="row service-detail-row">
                            <div class="col-xs-2 form-group float-label-control" style="margin-bottom:4px;">
                                <label>Lead Auditor</label>
                                <select class="auditor-list" id="auditor-list" name="auditor-list" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?>>
                                    <option value="<?php if (isset($service1['auditor'])) echo $service1['auditor'] ?>" ><?php
                                        if (isset($service1['auditor']) && !empty($service1['auditor'])) {
                                            echo $service1['auditor'];
                                        } else {
                                            echo "Select Lead Auditor";
                                        }
                                        ?> </option>
                                    <?php foreach ($allAuditorList as $auditors) { ?>
                                        <option value="<?php echo $auditors['auditor_name'] ?>"><?php echo $auditors['auditor_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-xs-2 form-group float-label-control" style="margin-bottom:4px;">
                                <label>Audit Date</label>
                                <input type="text" class="<?php echo $addClassToAudit ?>" data-row-id="<?php echo $serviceId; ?>"  name="audit-date" placeholder="Audit Date" value="<?php if (isset($service1['audit_date'])) echo simpleDateConversion($service1['audit_date']) ?>" <?php echo $financeAccess ?>>
                            </div>
                            <?php if (Utitlity::checkForUserConsultant() || Utitlity::checkForUserFinance()) { ?>
                                <div class="col-xs-3 form-group float-label-control" style="margin-bottom:4px;">
                                    <label>Add to</label>
                                    <input type="text" class="add-to-service" data-row-id="<?php echo $serviceId; ?>"  name="add-to-service" placeholder="add-to-service" value="<?php if (isset($service1['add_service_to'])) echo $service1['add_service_to'] ?>" <?php echo $financeAccess ?> <?php echo $fieldAccess ?> <?php echo $marketAccess ?>>
                                </div>
                            <?php } else { ?>                           
                                <div class="col-xs-3 form-group float-label-control" style="margin-bottom:4px;">
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
                                </div>
                            <?php } ?>
                            <div class="col-xs-2 form-group float-label-control" style="margin-bottom:4px;">
                                <label>Dealing Officer</label>
                                <select class="handel-by" id="handel-by" name="handel-by" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?>>
                                    <option value="<?php if (isset($service1['handel_by'])) echo $service1['handel_by'] ?>" ><?php
                                        if (isset($service1['handel_by']) && !empty($service1['handel_by'])) {
                                            echo $service1['handel_by'];
                                        } else {
                                            echo "Select Dealing Officer";
                                        }
                                        ?></option>
                                    <?php foreach ($handlerList as $handler) { ?>
                                        <option value="<?php echo $handler['handler_name'] ?>"><?php echo $handler['handler_name'] ?></option>
                                    <?php } ?>
                                </select>                                
                            </div>
                            <?php if (isset($service1['service_type']) && ($service1['service_type'] == "CA" || $service1['service_type'] == "SA-1" || $service1['service_type'] == "SA-2" || $service1['service_type'] == "RC")) { ?>
                                <div class="col-xs-3 form-group cert-given-group" style="margin-bottom:4px;">
                                    <?php
                                    if (isset($service1['is_certified']) && $service1['is_certified'] == 'YES')
                                        $chkVal = "checked";
                                    else
                                        $chkVal = "";
                                    ?>
                                    <label class="cert-given-box">
                                        <input type="checkbox" class="issueCert" id="issueCert" data-row-id="<?php echo $serviceId; ?>" data-service="<?php echo $serviceId ?>" data-customer="<?php echo $service1['customer_id'] ?>" <?php echo $chkVal ?> <?php echo $disable ?>>
                                        <span>Certificate Given</span>
                                    </label>
                                    <div class="certificationDateError" id="certificationDateError" style="display: none">
                                        <small><span id="certificationDateErrorMsg" style="color: #c9513c; font-style: italic; font-size: 10px;" >CA/RC Date cannot be empty.</span></small>
                                    </div>
                                </div>
                            <?php } ?>
                            <input type="hidden" class="customer_id" data-row-id="<?php echo $serviceId; ?>" id="customer_id" name="customer_id" value="<?php echo htmlspecialchars($_GET['customerid']); ?>" />
                        </div>

                    <div class="row payment-fullwidth-row" style="margin-left: -3px; margin-right: -3px; margin-top: 3px; margin-bottom: 4px; padding: 0;">
                        <div class="col-xs-12" style="padding-left: 0; padding-right: 0;">
                            <?php
                            $attCount = !empty($contractAttachments) ? count($contractAttachments) : 0;
                            $canManageContractAtt = (!Utitlity::checkForUserConsultant() && !Utitlity::checkForUserFinance());
                            ?>
                            <div class="contract-details-card <?php echo ($attCount > 0) ? 'has-attachments' : ''; ?>" data-service-id="<?php echo $serviceId; ?>">
                                <div class="contract-details-top">
                                    <div class="contract-label-wrap">
                                        <i class="fa fa-pencil-square-o" style="color: #2563eb; font-size: 12px;"></i>
                                        <span class="contract-label-text">Contract Details:</span>
                                    </div>

                                    <input type="text" name="audit-details" class="saveChanges auditDetailsText form-control contract-details-input" data-column="marketing_audit_type" id="audit-details" placeholder="Enter contract details, audit scope, or terms..." value="<?php if (isset($customer['marketing_audit_type']) && $customer['marketing_audit_type'] != '') echo htmlspecialchars($customer['marketing_audit_type']); ?>" />

                                    <div class="contract-actions-wrap" data-service-id="<?php echo $serviceId; ?>" data-customer-id="<?php echo htmlspecialchars($_GET['customerid']); ?>">
                                        <div class="contract-files-list" id="contract-files-list-<?php echo $serviceId; ?>">
                                            <?php if (!empty($contractAttachments)) {
                                                foreach ($contractAttachments as $cAtt) {
                                                    $cExt = strtolower($cAtt['file_ext']);
                                                    $isPdf = ($cExt === 'pdf');
                                            ?>
                                                <div class="contract-att-card" data-att-id="<?php echo (int)$cAtt['id']; ?>">
                                                    <a href="<?php echo htmlspecialchars($cAtt['file_path']); ?>" target="_blank" class="contract-att-thumb <?php echo $isPdf ? 'is-pdf' : 'is-img'; ?>" title="Preview <?php echo htmlspecialchars($cAtt['file_name']); ?>">
                                                        <?php if ($isPdf) { ?>
                                                            <i class="fa fa-file-pdf-o"></i>
                                                        <?php } else { ?>
                                                            <img src="<?php echo htmlspecialchars($cAtt['file_path']); ?>" alt="Scan" />
                                                        <?php } ?>
                                                    </a>
                                                    <a href="<?php echo htmlspecialchars($cAtt['file_path']); ?>" target="_blank" class="contract-att-name" title="<?php echo htmlspecialchars($cAtt['file_name']); ?>">
                                                        <?php echo htmlspecialchars($cAtt['file_name']); ?>
                                                    </a>
                                                    <a href="<?php echo htmlspecialchars($cAtt['file_path']); ?>" target="_blank" class="contract-btn-view" title="View Scan">View</a>
                                                    <a href="<?php echo htmlspecialchars($cAtt['file_path']); ?>" download="<?php echo htmlspecialchars($cAtt['file_name']); ?>" class="contract-btn-dl" title="Download">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <?php if ($canManageContractAtt) { ?>
                                                        <a href="javascript:void(0)" class="deleteContractAttachment contract-btn-del" data-att-id="<?php echo (int)$cAtt['id']; ?>" data-service-id="<?php echo $serviceId; ?>" data-customer-id="<?php echo htmlspecialchars($_GET['customerid']); ?>" title="Remove attachment">
                                                            <i class="fa fa-trash-o"></i>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            <?php }
                                            } ?>
                                        </div>

                                        <span class="contract-att-status-badge <?php echo ($attCount > 0) ? 'is-attached' : 'is-empty'; ?>">
                                            <?php if ($attCount > 0) { ?>
                                                <i class="fa fa-check-circle"></i> <?php echo $attCount; ?> Attached
                                            <?php } else { ?>
                                                No Attachment
                                            <?php } ?>
                                        </span>

                                        <?php if ($canManageContractAtt) { ?>
                                            <input type="file" class="contractScanFileInput" data-service-id="<?php echo $serviceId; ?>" data-customer-id="<?php echo htmlspecialchars($_GET['customerid']); ?>" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" multiple style="display: none;" />
                                            <button type="button" class="btnAttachContractScan" data-service-id="<?php echo $serviceId; ?>" title="Attach scanned contract (PDF, JPG, PNG)">
                                                <i class="fa fa-paperclip"></i> Attach Scan (PDF/JPG/PNG)
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                                        <!-- ========================================================
                         SEPARATED & COMPACT PAYMENT SYSTEM (CONSULTING & CB)
                         ======================================================== -->
                    <div class="row payment-fullwidth-row" style="margin-left: -3px; margin-right: -3px; margin-bottom: 4px; padding: 0;">
                        <div class="col-xs-12" style="padding-left: 0; padding-right: 0;">

                            <!-- SECTION 1: PAYMENT SYSTEM (INVOICING, RECEIVED PAYMENT & NOTES) -->
                            <div class="compact-payment-block consulting-payment-block">
                                <!-- Header bar matching Image 2 -->
                                <div class="compact-table-header payment-system-header" style="background: #1e293b; color: #ffffff; padding: 4px 10px; border-radius: 4px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; cursor: pointer;" title="Toggle Payment System">
                                    <div class="header-title" style="font-weight: 700; font-size: 11.5px; display: flex; align-items: center; gap: 6px; color: #ffffff;">
                                        <i class="fa fa-credit-card" style="color: #60a5fa;"></i> PAYMENT SYSTEM
                                        <i class="fa fa-chevron-up payment-system-collapse-indicator" style="font-size: 9px; opacity: 0.8; margin-left: 4px;"></i>
                                    </div>
                                    <div class="header-actions">
                                        <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                            <button type="button" class="btn btn-xs addNewService" style="background: #2563eb; color: #ffffff; border: none; border-radius: 3px; font-size: 10.5px; font-weight: 600; padding: 2px 8px;" title="Add Service Header for Invoicing">
                                                <i class="fa fa-plus-circle"></i> Add Service Header for Invoicing
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="payment-system-content-body">
                                    <!-- Green Outline Card Matching Image 2 -->
                                    <div class="service-payment-system-card <?php echo $isMatched ? 'is-matched' : ($paymentExcess > 0 ? 'is-excess' : ($targetAmount > 0 ? 'has-due' : '')); ?>" id="service-payment-system-<?php echo $serviceId; ?>" style="background: #ffffff; border: 1.5px solid <?php echo $isMatched ? '#22c55e' : ($paymentExcess > 0 ? '#f59e0b' : '#cbd5e1'); ?>; border-radius: 6px; padding: 6px 8px; margin-bottom: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); width: 100%;">
                                        <div class="row payment-columns-flex" style="margin: 0; display: flex; flex-wrap: nowrap; gap: 10px; align-items: stretch; width: 100%;">

                                            <!-- ==============================================
                                                 LEFT COLUMN: INVOICING (~39%)
                                                 ============================================== -->
                                            <div class="invoicing-column" style="flex: 0 0 34%; max-width: 34%; min-width: 310px; border-right: 1px solid #e2e8f0; padding-right: 10px;">
                                                <!-- Invoicing Header Row -->
                                                <div class="invoicing-header-row" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                                    <div style="display: flex; align-items: center; gap: 6px;">
                                                        <span class="inv-index-circle" style="width: 20px; height: 20px; border-radius: 50%; border: 1.5px solid #3b82f6; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">
                                                            <?php echo $serviceIndex; ?>
                                                        </span>
                                                        <span style="font-size: 11.5px; font-weight: 700; color: #1e40af; letter-spacing: 0.04em;">INVOICING</span>
                                                        <span class="inv-service-badge" style="border: 1px solid #93c5fd; background: #ffffff; color: #1e40af; font-size: 11px; font-weight: 700; padding: 1px 8px; border-radius: 4px;">
                                                            <?php echo htmlspecialchars($service1['service_type'] ?: 'SVC'); ?>
                                                        </span>
                                                        <span class="inv-matched-check-icon" style="display: <?php echo $isMatched ? 'inline-flex' : 'none'; ?>; align-items: center; justify-content: center; width: 16px; height: 16px; border-radius: 50%; background: #10b981; color: #ffffff; font-size: 9px;" title="Invoicing & Received Matched">
                                                            <i class="fa fa-check"></i>
                                                        </span>
                                                    </div>

                                                    <div style="display: flex; align-items: center; gap: 4px;">
                                                        <?php if (!empty($unassignedInvoices) && count($unassignedInvoices) > 0) { ?>
                                                            <div class="dropdown" style="display: inline-block;">
                                                                <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" style="font-size: 10px; border-radius: 12px; padding: 2px 7px; color: #475569;" title="Link existing unassigned invoice">
                                                                    <i class="fa fa-link" style="color: #0284c7;"></i> Link Inv (<?php echo count($unassignedInvoices); ?>) <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-right" style="font-size: 11px; min-width: 230px; padding: 4px 0; z-index: 1050;">
                                                                    <li class="dropdown-header" style="font-size: 10px; font-weight: 700; color: #64748b;">LINK UNASSIGNED INVOICE</li>
                                                                    <?php foreach ($unassignedInvoices as $uInv) { ?>
                                                                        <li>
                                                                            <a href="javascript:void(0)" class="linkInvoiceAction" data-invoice-id="<?php echo $uInv['invoice_id']; ?>" data-service-id="<?php echo $serviceId; ?>" data-customer-id="<?php echo htmlspecialchars($_GET['customerid']); ?>" style="display: flex; justify-content: space-between; padding: 4px 10px;">
                                                                                <span><strong><?php echo htmlspecialchars($uInv['firm_abbrevation'] . '/' . $uInv['invoice_no']); ?></strong> (<?php echo simpleDateConversion($uInv['invoice_date']); ?>)</span>
                                                                                <span style="font-weight: 600; color: #0f172a; margin-left: 6px;">₹<?php echo number_format((float)$uInv['total_amount'], 2); ?></span>
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        <?php } ?>

                                                        <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                                            <button type="button" class="btn btn-xs serviceInvoiceBtn header-invoice-btn" 
                                                                    data-customer="<?php echo htmlspecialchars($_GET['customerid']); ?>" 
                                                                    data-service="<?php echo $serviceId; ?>" 
                                                                    data-servicename="<?php echo htmlspecialchars($service1['service_type'] ?: ''); ?>"
                                                                    data-firm="<?php echo htmlspecialchars($service1['consulting_firm'] ?: ''); ?>"
                                                                    style="background: #1d4ed8; color: #ffffff; border: none; border-radius: 14px; font-size: 10.5px; font-weight: 600; padding: 2px 10px; box-shadow: 0 1px 2px rgba(29, 78, 216, 0.2);">
                                                                <i class="fa fa-plus"></i> issue tax invoice
                                                            </button>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <!-- Invoicing Table -->
                                                <div class="table-responsive" style="margin-bottom: 4px;">
                                                    <table class="table compact-invoicing-table" style="margin-bottom: 0; width: 100%; border-collapse: separate; border-spacing: 0 3px;">
                                                        <thead>
                                                            <tr style="font-size: 10px; color: #64748b; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase;">
                                                                <th style="padding: 2px 4px; border: none; width: 33%;">INVOICE NO.</th>
                                                                <th style="padding: 2px 4px; border: none; width: 21%;">DATE</th>
                                                                <th style="padding: 2px 4px; border: none; width: 23%; text-align: right;">TAXABLE AMT.</th>
                                                                <th style="padding: 2px 4px; border: none; width: 23%; text-align: right;">GROSS AMOUNT</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($currentServiceInvoices) && count($currentServiceInvoices) > 0) {
                                                                foreach ($currentServiceInvoices as $sInv) {
                                                                    $invNet = (float)$sInv['net_price'];
                                                                    $invGross = (float)$sInv['total_amount'];
                                                                    ?>
                                                                    <tr class="inv-data-row" data-gross-amt="<?php echo $invGross; ?>">
                                                                        <td style="padding: 2px 4px; border: none;">
                                                                            <span class="inv-pill" style="border: 1px solid #86efac; background: #f0fdf4; border-radius: 4px; padding: 1px 6px; display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; color: #166534;">
                                                                                <span title="<?php echo htmlspecialchars($sInv['firm_abbrevation'] . '/' . $sInv['invoice_no']); ?>"><?php echo htmlspecialchars($sInv['firm_abbrevation'] . '/' . $sInv['invoice_no']); ?></span>
                                                                                <a class="viewInvoice" data-id="<?php echo $sInv['invoice_id']; ?>" data-invoice="<?php echo $sInv['invoice_no']; ?>" data-customer="<?php echo $sInv['customer_id']; ?>" title="View / Print PDF" style="cursor: pointer;">
                                                                                    <i class="fa fa-file-pdf-o" style="color: #ef4444; font-size: 11px;"></i>
                                                                                </a>
                                                                                <a class="editInvoice" data-id="<?php echo $sInv['invoice_id']; ?>" data-firm="<?php echo $sInv['firm_id']; ?>" data-customer="<?php echo $sInv['customer_id']; ?>" title="Edit Invoice" style="cursor: pointer;">
                                                                                    <i class="fa fa-pencil-square-o" style="color: #2563eb; font-size: 11px;"></i>
                                                                                </a>
                                                                                <a href="javascript:void(0)" class="unlinkInvoiceAction" data-invoice-id="<?php echo $sInv['invoice_id']; ?>" data-customer-id="<?php echo $sInv['customer_id']; ?>" title="Unlink from this service" style="cursor: pointer;">
                                                                                    <i class="fa fa-chain-broken" style="color: #94a3b8; font-size: 9.5px;"></i>
                                                                                </a>
                                                                            </span>
                                                                        </td>
                                                                        <td style="padding: 2px 4px; border: none;">
                                                                            <div class="inv-cell-box" style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 4px; font-size: 11px; text-align: center; color: #334155; background: #ffffff;">
                                                                                <?php echo simpleDateConversion($sInv['invoice_date']); ?>
                                                                            </div>
                                                                        </td>
                                                                        <td style="padding: 2px 4px; border: none; text-align: right;">
                                                                            <div class="inv-cell-box" style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px; font-size: 11px; text-align: right; color: #334155; background: #ffffff; font-weight: 600;">
                                                                                <?php echo number_format($invNet, 2, '.', ''); ?>
                                                                            </div>
                                                                        </td>
                                                                        <td style="padding: 2px 4px; border: none; text-align: right;">
                                                                            <div class="inv-cell-box inv-gross-box" style="border: 1px solid #86efac; border-radius: 4px; padding: 1px 6px; font-size: 11px; text-align: right; color: #166534; background: #f0fdf4; font-weight: 700;">
                                                                                <?php echo number_format($invGross, 2, '.', ''); ?>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php }
                                                            } else { ?>
                                                                <tr class="inv-empty-row">
                                                                    <td colspan="4" style="text-align: center; color: #94a3b8; font-size: 10.5px; padding: 8px 4px; font-style: italic; border: none;">
                                                                        No invoice issued yet. Click <strong style="color: #1d4ed8;">+ issue tax invoice</strong> to create one.
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Invoicing Gross Total Footer -->
                                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; margin-top: 4px; padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                                                    <span style="font-size: 11px; font-weight: 700; color: #334155;">
                                                        Invoicing Gross Total (<?php echo $serviceIndex; ?>):
                                                    </span>
                                                    <input type="text" class="invoicing-gross-total-field" readonly value="<?php echo number_format($svcTotalInvoiced, 2, '.', ''); ?>" style="width: 100px; height: 23px; text-align: right; font-weight: 700; font-size: 12px; color: #0f172a; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px; font-variant-numeric: tabular-nums;" />
                                                </div>
                                            </div>

                                            <!-- ==============================================
                                                 MIDDLE/RIGHT COLUMN: RECEIVED PAYMENT (~45%)
                                                 ============================================== -->
                                            <div class="payment-column" style="flex: 1 1 auto; min-width: 480px; border-right: 1px solid #e2e8f0; padding-right: 10px; padding-left: 2px;">
                                                <!-- Payment Header Row -->
                                                <div class="payment-header-row" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                                    <div style="display: flex; align-items: center; gap: 4px;">
                                                        <span style="font-size: 11.5px; font-weight: 700; color: #1e293b; letter-spacing: 0.03em;">
                                                            RECEIVED PAYMENT (<?php echo $serviceIndex; ?>. <?php echo htmlspecialchars($service1['service_type'] ?: 'SVC'); ?>)
                                                        </span>
                                                        <span class="payment-match-indicator-header">
                                                            <?php if ($isMatched) { ?>
                                                                <span style="color: #166534; font-weight: 700; font-size: 11.5px; margin-left: 4px; display: inline-flex; align-items: center; gap: 3px;">
                                                                    <i class="fa fa-check-circle"></i> MATCHED
                                                                </span>
                                                            <?php } elseif ($targetAmount > 0 && $paymentBalance > 0) { ?>
                                                                <span style="color: #dc2626; font-weight: 600; font-size: 11px; margin-left: 4px; display: inline-flex; align-items: center; gap: 3px;">
                                                                    <i class="fa fa-exclamation-circle"></i> DUE: ₹<?php echo number_format($paymentBalance, 2); ?>
                                                                </span>
                                                            <?php } elseif ($paymentExcess > 0) { ?>
                                                                <span style="color: #d97706; font-weight: 700; font-size: 11px; margin-left: 4px; display: inline-flex; align-items: center; gap: 3px;">
                                                                    <i class="fa fa-exclamation-triangle"></i> EXCESS: ₹<?php echo number_format($paymentExcess, 2); ?>
                                                                </span>
                                                            <?php } ?>
                                                        </span>
                                                    </div>

                                                    <div style="display: flex; align-items: center; gap: 4px;">
                                                        <button type="button" class="btn btn-xs btn-add-adj" data-service-id="<?php echo $serviceId; ?>" title="Add Short Settle / Adjustment" style="font-size: 10px; font-weight: 700; color: #64748b; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 4px; padding: 1px 6px;">
                                                            (-+)
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-add-loss" data-service-id="<?php echo $serviceId; ?>" title="Record Bad Debt / Loss" style="font-size: 10px; font-weight: 700; color: #dc2626; border: 1px solid #fca5a5; background: #ffffff; border-radius: 4px; padding: 1px 6px;">
                                                            <i class="fa fa-ban"></i> Loss
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-add-payment-row" data-service-id="<?php echo $serviceId; ?>" title="Add Payment Row" style="background: #0f172a; color: #ffffff; border: none; border-radius: 4px; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 10px;">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="payment-firm-line">
                                                    <i class="fa fa-building-o" aria-hidden="true"></i> Consulting Firm:
                                                    <strong class="payment-firm-name"><?php echo htmlspecialchars(trim($service1['consulting_firm'] ?? '') ?: 'Not selected', ENT_QUOTES, 'UTF-8'); ?></strong>
                                                </div>
                                                <!-- Payment Table -->
                                                <div class="table-responsive" style="margin-bottom: 4px;">
                                                    <table class="table compact-payment-receipt-table service_details" id="consulting_table_<?php echo $serviceId; ?>" style="margin-bottom: 0; width: 100%; border-collapse: separate; border-spacing: 0 3px;">
                                                        <thead>
                                                            <tr style="font-size: 10px; color: #64748b; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase;">
                                                                <th style="padding: 2px 4px; border: none; width: 24%;">AMOUNT RECEIVED</th>
                                                                <th style="padding: 2px 4px; border: none; width: 18%;">TDS DEDUCTED</th>
                                                                <th style="padding: 2px 4px; border: none; width: 22%;">RECEIVED DATE</th>
                                                                <th style="padding: 2px 4px; border: none; width: 31%;">INSTRUMENT</th>
                                                                <th style="padding: 2px 4px; border: none; width: 5%; text-align: center;"><i class="fa fa-trash-o" style="color: #94a3b8;"></i></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="consulting_tbody_<?php echo $serviceId; ?>">
                                                            <?php
                                                            if (!empty($consultingList) && count($consultingList) > 0) {
                                                                foreach ($consultingList as $consult) {
                                                                    $cDec = (isset($consult['consulting_amount_decided']) && is_numeric($consult['consulting_amount_decided'])) ? (float)$consult['consulting_amount_decided'] : 0;
                                                                    $cRcv = (isset($consult['consulting_payment_received']) && is_numeric($consult['consulting_payment_received'])) ? (float)$consult['consulting_payment_received'] : 0;
                                                                    $cTds = (isset($consult['tds_deducted']) && is_numeric($consult['tds_deducted'])) ? (float)$consult['tds_deducted'] : 0;
                                                                    $cShort = (isset($consult['short_settle']) && is_numeric($consult['short_settle'])) ? (float)$consult['short_settle'] : 0;
                                                                    $cBad = (isset($consult['bad_debt']) && is_numeric($consult['bad_debt'])) ? (float)$consult['bad_debt'] : 0;

                                                                    $cLabel = isset($consult['consulting_fees']) ? trim($consult['consulting_fees']) : 'Payment';
                                                                    $cLabelLower = strtolower($cLabel);
                                                                    if (stripos($cLabelLower, 'tds') !== false && $cTds == 0) {
                                                                        $cTds = $cRcv > 0 ? $cRcv : $cDec;
                                                                        $cRcv = 0;
                                                                    }
                                                                    if (stripos($cLabelLower, 'loss') !== false && $cBad == 0) {
                                                                        $cBad = $cRcv > 0 ? $cRcv : $cDec;
                                                                        $cRcv = 0;
                                                                    }
                                                                    if (stripos($cLabelLower, 'short') !== false && $cShort == 0) {
                                                                        $cShort = $cRcv > 0 ? $cRcv : $cDec;
                                                                        $cRcv = 0;
                                                                    }
                                                                    $isLossRow = ($cBad > 0 || stripos($cLabelLower, 'loss') !== false);
                                                                    $isShortRow = ($cShort > 0 || stripos($cLabelLower, 'short') !== false);
                                                                    $rowBg = $isLossRow ? 'background: #fff1f2;' : ($isShortRow ? 'background: #faf5ff;' : '');
                                                                    ?>
                                                                    <tr class="consulting_subsection previousConsultingData <?php echo $isLossRow ? 'row-is-loss' : ($isShortRow ? 'row-is-short' : ''); ?>" style="<?php echo $rowBg; ?>">
                                                                        <td style="padding: 2px 4px; border: none;">
                                                                            <div class="table-edit-control payment-input-wrap" style="position: relative;">
                                                                                <?php if ($isLossRow) { ?>
                                                                                    <span class="badge" style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; font-size:10px; font-weight:600; letter-spacing:0.02em;">LOSS / BAD DEBT</span>
                                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingPaymentReceived" name="consultingPaymentReceived" value="0" />
                                                                                <?php } elseif ($isShortRow) { ?>
                                                                                    <span class="badge" style="background:#f3e8ff; border:1px solid #d8b4fe; color:#6b21a8; font-size:10px; font-weight:600; letter-spacing:0.02em;">SHORT SETTLE</span>
                                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingPaymentReceived" name="consultingPaymentReceived" value="0" />
                                                                                <?php } else { ?>
                                                                                    <input type="number" step="0.01" data-row-id="<?php echo $serviceId; ?>" class="consultingPaymentReceived form-control" id="consultingPaymentReceived" name="consultingPaymentReceived" value="<?php if ($cRcv > 0) echo $cRcv; ?>" placeholder="0.00" <?php echo $fieldAccess ?> style="height: 23px; font-size: 11px; font-weight: 700; text-align: right; border-radius: 4px; padding: 1px 6px; <?php echo ($cRcv > 0 ? 'border: 1px solid #10b981; background: #f0fdf4; color: #166534;' : 'border: 1px solid #cbd5e1;'); ?>" />
                                                                                    <?php if ($cRcv > 0) { ?>
                                                                                        <i class="fa fa-check text-success payment-check-icon" style="position: absolute; left: 6px; top: 5px; font-size: 10px;"></i>
                                                                                    <?php } ?>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </td>
                                                                        <td style="padding: 2px 4px; border: none;">
                                                                            <div class="table-edit-control">
                                                                                <?php if ($isLossRow) { ?>
                                                                                    <span style="font-size:11px; color:#dc2626; font-weight:700;">-₹<?php echo number_format($cBad, 2); ?></span>
                                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingTdsDeducted" name="consultingTdsDeducted" value="0" />
                                                                                <?php } elseif ($isShortRow) { ?>
                                                                                    <span style="font-size:11px; color:#7c3aed; font-weight:700;">-₹<?php echo number_format($cShort, 2); ?></span>
                                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingTdsDeducted" name="consultingTdsDeducted" value="0" />
                                                                                <?php } else { ?>
                                                                                    <input type="number" step="0.01" data-row-id="<?php echo $serviceId; ?>" class="consultingTdsDeducted form-control" id="consultingTdsDeducted" name="consultingTdsDeducted" value="<?php if ($cTds > 0) echo $cTds; ?>" placeholder="" <?php echo $fieldAccess ?> style="height: 23px; font-size: 11px; font-weight: 600; text-align: right; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px;" />
                                                                                <?php } ?>
                                                                            </div>
                                                                        </td>
                                                                        <td style="padding: 2px 4px; border: none;">
                                                                            <div class="table-edit-control">
                                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassToConsulting ?> form-control" name="consultingDepositDate" value="<?php if (isset($consult['consulting_deposit_date']) && !empty($consult['consulting_deposit_date'])) echo simpleDateConversion($consult['consulting_deposit_date']); ?>" placeholder="dd.mm.yyyy" <?php echo $fieldAccess ?> style="height: 23px; font-size: 11px; text-align: center; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 4px; background: #ffffff;" />
                                                                            </div>
                                                                        </td>
                                                                        <td style="padding: 2px 4px; border: none;">
                                                                            <div class="table-edit-control">
                                                                                <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingChequeDetails form-control" id="consultingChequeDetails" name="consultingChequeDetails" value="<?php if (isset($consult['consulting_chque_details'])) echo htmlspecialchars($consult['consulting_chque_details']); ?>" placeholder="NEFT / Cheque" <?php echo $fieldAccess ?> style="height: 23px; font-size: 11px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px;" />
                                                                            </div>
                                                                        </td>
                                                                        <td style="padding: 2px 4px; border: none; text-align: center; vertical-align: middle;">
                                                                            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                                                <input type="checkbox" class="consultingDelete" data-row-id="<?php echo $serviceId; ?>" value="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>" style="display:none;"/>
                                                                                <a href="javascript:void(0)" class="consultingDeleteIcon" data-row-id="<?php echo $serviceId; ?>" data-id="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>" title="Delete Row" style="color: #ef4444; font-size: 11px;"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                                            <?php } ?>
                                                                        </td>
                                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingContractDetails" name="consultingContractDetails" value="<?php echo htmlspecialchars($cLabel ?: 'Payment'); ?>" />
                                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingAmountDecided" name="consultingAmountDecided" value="<?php echo $cDec; ?>" />
                                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingBankAcccountNo" name="consultingBankAcccountNo" value="<?php if (isset($consult['bank_ac'])) echo htmlspecialchars($consult['bank_ac']); ?>" />
                                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingBalance" name="consultingBalance" value="0" />
                                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingShortSettle" name="consultingShortSettle" value="<?php echo $cShort; ?>" />
                                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingBadDebt" name="consultingBadDebt" value="<?php echo $cBad; ?>" />
                                                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consulting-row-id <?php echo $consult['consulting_id'] ?>" name="consulting-row-id" value="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>">
                                                                    </tr>
                                                                <?php }
                                                            } else { ?>
                                                                <tr class="consulting_subsection">
                                                                    <td style="padding: 2px 4px; border: none;">
                                                                        <div class="table-edit-control payment-input-wrap">
                                                                            <input type="number" step="0.01" data-row-id="<?php echo $serviceId; ?>" class="consultingPaymentReceived form-control" name="consultingPaymentReceived" value="" placeholder="0.00" <?php echo $fieldAccess ?> style="height: 23px; font-size: 11px; font-weight: 700; text-align: right; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px;" />
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding: 2px 4px; border: none;">
                                                                        <div class="table-edit-control">
                                                                            <input type="number" step="0.01" data-row-id="<?php echo $serviceId; ?>" class="consultingTdsDeducted form-control" name="consultingTdsDeducted" value="" placeholder="" <?php echo $fieldAccess ?> style="height: 23px; font-size: 11px; font-weight: 600; text-align: right; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px;" />
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding: 2px 4px; border: none;">
                                                                        <div class="table-edit-control">
                                                                            <input type="text" data-row-id="<?php echo $serviceId; ?>" class="<?php echo $addClassToConsulting ?> form-control" name="consultingDepositDate" value="" placeholder="dd.mm.yyyy" <?php echo $fieldAccess ?> style="height: 23px; font-size: 11px; text-align: center; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 4px; background: #ffffff;" />
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding: 2px 4px; border: none;">
                                                                        <div class="table-edit-control">
                                                                            <input type="text" data-row-id="<?php echo $serviceId; ?>" class="consultingChequeDetails form-control" name="consultingChequeDetails" value="" placeholder="NEFT / Cheque" <?php echo $fieldAccess ?> style="height: 23px; font-size: 11px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px;" />
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding: 2px 4px; border: none; text-align: center; vertical-align: middle;">
                                                                        <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                                            <a href="javascript:void(0)" class="consultingRowDelete" title="Delete Row" style="color: #ef4444; font-size: 11px;"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingContractDetails" name="consultingContractDetails" value="Payment" />
                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingAmountDecided" name="consultingAmountDecided" value="0" />
                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingBankAcccountNo" name="consultingBankAcccountNo" value="" />
                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingBalance" name="consultingBalance" value="0" />
                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingShortSettle" name="consultingShortSettle" value="0" />
                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consultingBadDebt" name="consultingBadDebt" value="0" />
                                                                    <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consulting-row-id" name="consulting-row-id" value="">
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Payment Footer Chips Matching Image 2 -->
                                                <div class="payment-footer-chips-row" style="display: flex; align-items: center; flex-wrap: wrap; gap: 6px; margin-top: 4px; padding-top: 4px; border-top: 1px dashed #e2e8f0; font-size: 10.5px;">
                                                    <span class="chip-item chip-received" style="color: #059669; font-weight: 700;">
                                                        RECEIVED TOTAL: ₹<span class="val-received-total"><?php echo number_format($consultTotalReceived, 2); ?></span>
                                                    </span>
                                                    <span class="chip-item chip-tds" style="color: #d97706; font-weight: 700;">
                                                        TDS TOTAL: ₹<span class="val-tds-total"><?php echo number_format($consultTotalTds, 2); ?></span>
                                                    </span>
                                                    <span class="chip-item chip-short" style="color: #7c3aed; font-weight: 700;">
                                                        SHORT SETTLE: ₹<span class="val-short-total"><?php echo number_format($consultTotalShortSettle, 2); ?></span>
                                                    </span>
                                                    <span class="chip-item chip-loss" style="color: #dc2626; font-weight: 700;">
                                                        BAD DEBT: ₹<span class="val-loss-total"><?php echo number_format($consultTotalBadDebt, 2); ?></span>
                                                    </span>
                                                    <span class="chip-matched-status" style="margin-left: auto;">
                                                        <?php if ($isMatched) { ?>
                                                            <span class="badge" style="background:#dcfce7; border:1px solid #86efac; color:#166534; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:6px;">
                                                                <i class="fa fa-check-circle"></i> MATCHED: ₹0.00
                                                            </span>
                                                        <?php } elseif ($targetAmount > 0 && $paymentBalance > 0) { ?>
                                                            <span class="badge" style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:6px;">
                                                                <i class="fa fa-exclamation-circle"></i> BALANCE DUE: ₹<?php echo number_format($paymentBalance, 2); ?>
                                                            </span>
                                                        <?php } elseif ($paymentExcess > 0) { ?>
                                                            <span class="badge" style="background:#fef3c7; border:1px solid #fcd34d; color:#b45309; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:6px;">
                                                                <i class="fa fa-exclamation-triangle"></i> EXCESS: ₹<?php echo number_format($paymentExcess, 2); ?>
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="badge" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:6px;">
                                                                NO INVOICE
                                                            </span>
                                                        <?php } ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- ==============================================
                                                 FAR RIGHT COLUMN: NOTES (IF ANY) (~15%)
                                                 ============================================== -->
                                            <div class="notes-column" style="flex: 0 0 18%; max-width: 18%; min-width: 170px; display: flex; flex-direction: column;">
                                                <div class="notes-header-row" style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;">
                                                    <i class="fa fa-commenting-o" style="color: #64748b;"></i>
                                                    <span>NOTES (IF ANY)...</span>
                                                </div>
                                                <textarea class="form-control serviceNotesText" data-service-id="<?php echo $serviceId; ?>" placeholder="Notes (if any)..." style="width: 100%; flex: 1; min-height: 68px; font-size: 11px; resize: vertical; border: 1px solid #cbd5e1; border-radius: 4px; padding: 3px 6px; background: #ffffff; line-height: 1.3; color: #1e293b;"><?php echo htmlspecialchars(isset($service1['service_notes']) ? $service1['service_notes'] : ''); ?></textarea>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
<!-- DOTTED SEPARATOR LINE BETWEEN CONSULTING AND CB -->
                            <div class="payment-dotted-divider" style="cursor: pointer;" title="Click to toggle External Party Payment">
                                <span class="divider-label"><i class="fa fa-university" style="color: #1e293b;"></i> External Party Payment <i class="fa fa-chevron-down divider-collapse-indicator" style="font-size: 8.5px; margin-left: 3px;"></i></span>
                            </div>

                            <!-- SECTION 2: CERTIFICATION BODY (CB) PAYMENTS -->
                            <div class="compact-payment-block cb-payment-block">
                                <div class="compact-table-card is-collapsed">
                                    <div class="compact-table-header cb-header" style="cursor: pointer;" title="Click to collapse / expand External Party Payment section">
                                        <div class="header-title" style="cursor: pointer;">
                                            <i class="fa fa-university"></i> External Party Payment
                                            <i class="fa fa-chevron-down cb-collapse-indicator section-collapse-indicator" style="margin-left: 6px; font-size: 10px; color: #ffffff; opacity: 0.9;"></i>
                                        </div>
                                        <div class="header-actions">
                                            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) { ?>
                                                <a class="cbAddLinkForAdmin btn btn-xs btn-compact-ms" data-row-id="<?php echo $serviceId; ?>" data-row-count="<?php echo $cbCount['totalcb'] ?>" title="Add Marketing Setup">
                                                    MS
                                                </a>
                                            <?php } ?>
                                            <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                                <a class="btn btn-xs cbAddLink btn-compact-add" data-row-id="<?php echo $serviceId; ?>" data-row-count="<?php echo $cbCount['totalcb'] ?>" title="Add new External Party payment installment row">
                                                    <i class="fa fa-plus-circle"></i> Add External Party Row
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="cb-content-body payment-content-body" style="display: none;">
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
                                </div>

                                <!-- Compact CB Financial Summary -->
                                <div class="cb-summary-wrapper cb-content-body payment-content-body" style="display: none;">
                                <div class="compact-summary-panel cb-summary-panel">
                                    <div class="summary-top-bar">
                                        <div class="summary-title">
                                            <i class="fa fa-calculator" style="color: #1e293b; margin-right: 4px;"></i> 
                                            External Party Payment Financial Summary
                                        </div>
                                    </div>
                                    <div class="compact-kpi-row">
                                        <div class="kpi-chip kpi-cb-decided">
                                            <span class="kpi-chip-label">External Party Decided</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($cbTotalDecided, 2); ?></span>
                                        </div>
                                        <div class="kpi-chip kpi-cb-received">
                                            <span class="kpi-chip-label">External Party Payment Received</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($cbTotalReceived, 2); ?></span>
                                        </div>
                                        <?php 
                                            $kpiCbBalClass = ($cbTotalBalance > 0) ? 'kpi-chip-danger' : 'kpi-chip-success';
                                        ?>
                                        <div class="kpi-chip kpi-cb-balance <?php echo $kpiCbBalClass; ?>">
                                            <span class="kpi-chip-label">External Party Balance Due</span>
                                            <span class="kpi-chip-value">₹ <?php echo number_format($cbTotalBalance, 2); ?></span>
                                        </div>
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
        var ledgerRequest = null;
        $(".show-all-invoices").on('click', function () {
            var panel = $('#client-ledger-panel');
            var opening = !panel.is(':visible');
            panel.toggle(opening);
            $(this).attr('aria-expanded', opening ? 'true' : 'false');
            if (!opening || ledgerRequest) return;
            $('#client-ledger-status').text('Refreshing saved payments...');
            ledgerRequest = $.ajax({url: window.location.href, cache: false, dataType: 'html'})
                .done(function (html) {
                    var updated = $('<div>').append($.parseHTML(html, document, false)).find('#client-ledger-content');
                    if (updated.length) {
                        $('#client-ledger-content').replaceWith(updated);
                        $('#client-ledger-status').text('Showing saved payments across all services.');
                    } else {
                        $('#client-ledger-status').text('Unable to refresh ledger. Reload the page to see latest payments.');
                    }
                })
                .fail(function () { $('#client-ledger-status').text('Unable to refresh ledger. Reload the page to see latest payments.'); })
                .always(function () { ledgerRequest = null; });
        }).on('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); $(this).click(); }
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
   GLOBAL MODERN CLEAN TYPOGRAPHY (Inter / Apple System)
   ======================================================== */
*, *::before, *::after,
body, #wrapper, #page-content-wrapper,
input, select, textarea, button,
table, thead, tbody, tfoot, tr, th, td,
label, span, p, a, div, h1, h2, h3, h4, h5, h6,
.form-control, .btn, .badge, .dropdown-menu,
.servicebox, .service_title, .qms_customer_name, .top_qms_page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: optimizeLegibility !important;
}

/* Preserve Icon Fonts (FontAwesome & Glyphicons) */
.fa, .fa::before, [class^="fa-"], [class*=" fa-"] {
    font-family: 'FontAwesome' !important;
}
.glyphicon, .glyphicon::before {
    font-family: 'Glyphicons Halflings' !important;
}

/* Number / Currency Tabular Alignment */
input[type="number"],
input.amountDecided,
input.consultingAmountDecided,
input.paymentReceived,
input.consultingPaymentReceived,
input.balance,
input.consultingBalance,
input.consultingTdsDeducted,
input.invoicing-gross-total-field,
.inv-data-row td,
.consult-data-row td,
.cb-data-row td,
.compact-invoicing-table td,
.compact-payment-receipt-table td,
.compact-payment-table td,
.kpi-chip-value,
.val-received-total,
.val-tds-total,
.val-short-total,
.val-baddebt-total,
.payment-footer-chips-row span {
    font-variant-numeric: tabular-nums lining-nums !important;
    font-feature-settings: "tnum" 1, "cv05" 1 !important;
}

/* ========================================================
   PAGE CONTAINER & TOP CUSTOMER OVERVIEW CARD
   ======================================================== */
body, #page-content-wrapper {
    background-color: #f1f5f9 !important;
    color: #1e293b !important;
}

.top_qms_page {
    background: #ffffff !important;
    border: 1px solid #dbe4f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 2px 10px rgba(15, 35, 70, 0.06) !important;
    margin-top: 8px !important;
    margin-bottom: 12px !important;
    overflow: visible !important;
    color: #1e293b !important;
}

/* Customer Title Bar */
.top_qms_page .qms_customer_name {
    position: relative !important;
    top: auto !important;
    left: auto !important;
    right: auto !important;
    z-index: 10 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: linear-gradient(135deg, #0f294a 0%, #1d3c6a 100%) !important;
    color: #ffffff !important;
    height: auto !important;
    min-height: 44px !important;
    padding: 8px 18px !important;
    border-radius: 9px 9px 0 0 !important;
    box-shadow: none !important;
}
.qms-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}
.qms-header-icon {
    width: 28px !important;
    height: 28px !important;
    border-radius: 6px !important;
    background: rgba(255, 255, 255, 0.14) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #93c5fd !important;
    font-size: 13px !important;
}
.qms-company-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    letter-spacing: 0.03em !important;
    text-transform: uppercase !important;
    color: #ffffff !important;
}
.qms-header-actions {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}
.top_qms_page .qms_customer_name a {
    color: #e2e8f0 !important;
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 6px !important;
    width: 30px !important;
    height: 30px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
    padding: 0 !important;
    margin: 0 !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
}
.top_qms_page .qms_customer_name a:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.22) !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
    transform: translateY(-1px) !important;
}

/* Profile Body (Left Info + Right Notes) */
.qms-profile-body {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 20px 28px !important;
    padding: 16px 20px !important;
    background: #ffffff !important;
}
.qms-profile-info-col {
    flex: 1.35 !important;
    min-width: 300px !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
}
.qms-info-split-row {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 10px 24px !important;
}
.qms-info-split-row > .qms-info-item {
    flex: 1 !important;
    min-width: 180px !important;
}
.qms-info-item {
    display: flex !important;
    align-items: flex-start !important;
    gap: 10px !important;
}
.qms-info-icon {
    width: 24px !important;
    height: 24px !important;
    min-width: 24px !important;
    border-radius: 6px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12px !important;
    margin-top: 1px !important;
}
.qms-info-text {
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    line-height: 1.5 !important;
    word-break: break-word !important;
}

/* Notes Column */
.qms-profile-notes-col {
    flex: 1 !important;
    min-width: 260px !important;
    display: flex !important;
    flex-direction: column !important;
}
.qms-notes-label {
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    color: #64748b !important;
    margin-bottom: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
}
.qms-notes-label i {
    color: #2563eb !important;
}
.top_qms_page #add-notes {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 10px 12px !important;
    font-size: 12.5px !important;
    font-weight: 400 !important;
    color: #1e293b !important;
    width: 100% !important;
    flex: 1 !important;
    min-height: 105px !important;
    height: auto !important;
    line-height: 1.5 !important;
    box-shadow: none !important;
    resize: vertical !important;
    margin: 0 !important;
    transition: all 0.15s ease !important;
}
.top_qms_page #add-notes:focus {
    background: #ffffff !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
    outline: none !important;
}

/* Type of Service Selector Band */
.customer-type-of-service-row {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 12px 20px !important;
    position: relative !important;
}
.tos-row-inner {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 12px 16px !important;
}
.tos-label-col {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    white-space: nowrap !important;
    min-width: 130px !important;
}
.tos-label-icon {
    width: 24px !important;
    height: 24px !important;
    border-radius: 6px !important;
    background: #e0f2fe !important;
    color: #0284c7 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12px !important;
}
.tos-multiselect-wrapper {
    flex: 1 !important;
    min-width: 260px !important;
    max-width: 640px !important;
    position: relative !important;
}
.tos-selector-box {
    min-height: 38px !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 7px !important;
    padding: 5px 12px !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
    transition: all 0.15s ease !important;
}
.tos-selector-box:hover:not(.is-disabled) {
    border-color: #94a3b8 !important;
}
.tos-selector-box.is-open {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
}
.tos-selector-box.is-disabled {
    background: #f1f5f9 !important;
    cursor: not-allowed !important;
}
.tos-selected-tags {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 5px !important;
    flex: 1 !important;
}
.tos-placeholder {
    color: #94a3b8 !important;
    font-size: 12.5px !important;
    font-weight: 400 !important;
}
.tos-tag-chip {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 5px !important;
    padding: 2px 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 1.4 !important;
}
.tos-tag-remove {
    cursor: pointer !important;
    color: #60a5fa !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    line-height: 1 !important;
    transition: color 0.1s !important;
}
.tos-tag-remove:hover {
    color: #dc2626 !important;
}
.tos-selector-right {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
}
.tos-count-pill {
    background: #e2e8f0 !important;
    color: #334155 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 2px 7px !important;
    border-radius: 10px !important;
    min-width: 20px !important;
    text-align: center !important;
}
.tos-count-pill.has-items {
    background: #2563eb !important;
    color: #ffffff !important;
}
.tos-caret {
    color: #64748b !important;
    font-size: 11px !important;
    transition: transform 0.2s ease !important;
}
.tos-selector-box.is-open .tos-caret {
    transform: rotate(180deg) !important;
    color: #2563eb !important;
}

/* Dropdown Panel */
.tos-dropdown-menu {
    position: absolute !important;
    top: calc(100% + 6px) !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 1050 !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14) !important;
    overflow: hidden !important;
}
.tos-search-wrap {
    padding: 8px 10px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    background: #f8fafc !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}
.tos-search-icon {
    color: #94a3b8 !important;
    font-size: 12px !important;
}
.tos-search-input {
    border: none !important;
    background: transparent !important;
    font-size: 12.5px !important;
    color: #1e293b !important;
    width: 100% !important;
    outline: none !important;
    padding: 2px 0 !important;
    box-shadow: none !important;
}
.tos-options-list {
    max-height: 230px !important;
    overflow-y: auto !important;
    padding: 4px 0 !important;
}
.tos-option-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 8px 14px !important;
    margin: 0 !important;
    cursor: pointer !important;
    font-size: 12.5px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    border-bottom: 1px solid #f8fafc !important;
    transition: background 0.1s ease !important;
}
.tos-option-item:hover {
    background: #f1f5f9 !important;
}
.tos-option-item.is-checked {
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    font-weight: 600 !important;
}
.tos-checkbox {
    margin: 0 !important;
    width: 15px !important;
    height: 15px !important;
    cursor: pointer !important;
    accent-color: #2563eb !important;
}
.tos-option-label {
    flex: 1 !important;
}
.tos-option-check {
    opacity: 0 !important;
    color: #2563eb !important;
    font-size: 11px !important;
}
.tos-option-item.is-checked .tos-option-check {
    opacity: 1 !important;
}
.tos-empty-msg {
    padding: 14px !important;
    text-align: center !important;
    font-size: 12px !important;
    color: #94a3b8 !important;
}
.tos-dropdown-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 8px 12px !important;
    border-top: 1px solid #e2e8f0 !important;
    background: #f8fafc !important;
}
.tos-footer-actions {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}
.tos-text-btn {
    background: transparent !important;
    border: none !important;
    padding: 2px 4px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    cursor: pointer !important;
}
.tos-text-btn:hover {
    color: #2563eb !important;
    text-decoration: underline !important;
}
.tos-done-btn {
    background: #2563eb !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 5px !important;
    padding: 4px 12px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
}
.tos-done-btn:hover {
    background: #1d4ed8 !important;
}

/* Milestone Dates Row */
.qms-milestones-row {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 14px 28px !important;
    padding: 12px 20px !important;
    background: #ffffff !important;
    border-radius: 0 0 9px 9px !important;
}
.qms-milestone-field {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
.qms-milestone-field label {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    white-space: nowrap !important;
}
.qms-milestone-field label i {
    color: #0284c7 !important;
    font-size: 12px !important;
}
.top_qms_page input.lookslike-label {
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    height: 30px !important;
    padding: 4px 10px !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    width: 130px !important;
    box-shadow: none !important;
    transition: all 0.15s ease !important;
}
.top_qms_page input.lookslike-label:focus {
    background: #ffffff !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
    outline: none !important;
}


/* Action Buttons Connected Segmented Bar */
.qms_complete_btn {
    border-radius: 0px !important;
    padding: 5px 14px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    letter-spacing: 0.02em !important;
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
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
}
.qms_complete_btn.quotationBtn {
    background: #193048 !important;
    color: #ffffff !important;
    border-color: #193048 !important;
}
.qms_complete_btn.addNewService {
    background: #16a34a !important;
    color: #ffffff !important;
    border-color: #16a34a !important;
}
.qms_complete_btn.show-all-invoices {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
}

/* ========================================================
   MODERN SERVICE CARD & PAYMENT SYSTEM STYLING
   ======================================================== */
.servicebox {
    background: #ffffff !important;
    border: 1px solid #dce4ec !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 8px rgba(15, 35, 70, 0.04) !important;
    margin-bottom: 20px !important;
    overflow: hidden !important;
    transition: all 0.2s ease-in-out !important;
}
.servicebox:hover {
    box-shadow: 0 4px 12px rgba(15, 35, 70, 0.07) !important;
    border-color: #cbd5e1 !important;
}

/* Service Title Header - Crisp Royal Blue */
.service_title {
    background: #0d52b3 !important;
    color: #ffffff !important;
    padding: 7px 14px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    user-select: none;
    box-shadow: none !important;
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
    display: flex !align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
}
.service_title .serviceStatus {
    color: #fde047 !important;
    font-weight: 700 !important;
    font-size: 14.5px !important;
    letter-spacing: 0.04em !important;
    text-shadow: none !important;
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

/* Service Box Data Section - Clean & Compact Detailing */
.servicebox-data {
    padding: 8px 12px 6px 12px !important;
}
.servicebox-data .service-detail-row {
    margin-left: -5px !important;
    margin-right: -5px !important;
    margin-top: 0 !important;
}
.servicebox-data .service-detail-row > [class*="col-"] {
    padding-left: 5px !important;
    padding-right: 5px !important;
}
.servicebox-data .float-label-control {
    margin-bottom: 4px !important;
    position: relative !important;
}
.servicebox-data .float-label-control label {
    font-size: 10px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    color: #64748b !important;
    margin-top: 2px !important;
    margin-bottom: 0 !important;
    display: block !important;
    position: static !important;
    opacity: 1 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.servicebox-data select,
.servicebox-data .float-label-control input[type="text"] {
    height: 27px !important;
    min-height: 27px !important;
    max-height: 27px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 4px !important;
    padding: 0 20px 0 8px !important;
    font-size: 11.5px !important;
    font-weight: 500 !important;
    line-height: 25px !important;
    color: #0f172a !important;
    background-color: #ffffff !important;
    width: 100% !important;
    box-sizing: border-box !important;
    box-shadow: none !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%2364748b' d='M0 0l5 5 5-5z'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 7px center !important;
    background-size: 7px 4px !important;
    cursor: pointer !important;
}
.servicebox-data .float-label-control input[type="text"] {
    padding: 0 8px !important;
    background-image: none !important;
    cursor: text !important;
}
.servicebox-data select:hover,
.servicebox-data .float-label-control input[type="text"]:hover {
    border-color: #94a3b8 !important;
}
.servicebox-data select:focus,
.servicebox-data .float-label-control input[type="text"]:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
    outline: none !important;
}
.servicebox-data select:disabled,
.servicebox-data select[readonly],
.servicebox-data .float-label-control input[type="text"]:disabled,
.servicebox-data .float-label-control input[type="text"][readonly] {
    background-color: #f8fafc !important;
    color: #64748b !important;
    cursor: not-allowed !important;
    border-color: #e2e8f0 !important;
}

/* Certificate Given Compact Box */
.cert-given-box {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    height: 27px !important;
    min-height: 27px !important;
    max-height: 27px !important;
    padding: 0 8px !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    font-size: 11.5px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    margin-bottom: 0 !important;
    width: 100% !important;
    box-sizing: border-box !important;
    transition: all 0.15s ease !important;
    user-select: none !important;
}
.cert-given-box:hover {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
}
.cert-given-box input[type="checkbox"].issueCert {
    margin: 0 !important;
    width: 14px !important;
    height: 14px !important;
    accent-color: #0d52b3 !important;
    cursor: pointer !important;
    vertical-align: middle !important;
}
.cert-given-box span {
    line-height: 25px !important;
    white-space: nowrap !important;
}

/* Contract Details & Scan Attachment Bar (Single-Line Compact) */
.contract-details-card {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 5px !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
    overflow: hidden !important;
    transition: border-color 0.15s ease !important;
}
.contract-details-card.has-attachments {
    border-color: #86efac !important;
}
.contract-details-top {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 6px 8px !important;
    padding: 4px 8px !important;
    background: #ffffff !important;
}
.contract-label-wrap {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    white-space: nowrap !important;
}
.contract-label-text {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #1e3a8a !important;
}
.contract-details-input {
    flex: 1 1 200px !important;
    min-width: 160px !important;
    height: 26px !important;
    min-height: 26px !important;
    max-height: 26px !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 4px !important;
    padding: 2px 8px !important;
    font-size: 11.5px !important;
    line-height: 20px !important;
    color: #0f172a !important;
    box-shadow: none !important;
    transition: all 0.15s ease !important;
}
.contract-details-input:focus {
    background: #ffffff !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12) !important;
    outline: none !important;
}
.contract-actions-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
}
.contract-files-list {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 5px !important;
}
.contract-att-card {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    height: 26px !important;
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 4px !important;
    padding: 1px 6px !important;
    transition: border-color 0.15s ease !important;
}
.contract-att-card:hover {
    border-color: #93c5fd !important;
    background: #f1f5f9 !important;
}
.contract-att-thumb {
    width: 18px !important;
    height: 18px !important;
    border-radius: 3px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
    text-decoration: none !important;
}
.contract-att-thumb.is-pdf {
    background: #fee2e2 !important;
    color: #dc2626 !important;
    font-size: 11px !important;
}
.contract-att-thumb.is-img img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
.contract-att-name {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    text-decoration: none !important;
    max-width: 190px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}
.contract-att-name:hover {
    color: #2563eb !important;
    text-decoration: underline !important;
}
.contract-btn-view {
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 3px !important;
    padding: 0 6px !important;
    height: 19px !important;
    line-height: 17px !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
}
.contract-btn-view:hover {
    background: #dbeafe !important;
}
.contract-btn-dl, .contract-btn-del {
    width: 19px !important;
    height: 19px !important;
    border-radius: 3px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 10px !important;
    text-decoration: none !important;
}
.contract-btn-dl {
    background: #ffffff !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
}
.contract-btn-dl:hover {
    background: #e2e8f0 !important;
}
.contract-btn-del {
    background: #fef2f2 !important;
    color: #dc2626 !important;
    border: 1px solid #fecaca !important;
}
.contract-btn-del:hover {
    background: #fee2e2 !important;
}
.contract-att-status-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    height: 24px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    padding: 0 8px !important;
    border-radius: 12px !important;
    white-space: nowrap !important;
}
.contract-att-status-badge.is-attached {
    background: #dcfce7 !important;
    color: #166534 !important;
    border: 1px solid #86efac !important;
}
.contract-att-status-badge.is-empty {
    background: #f1f5f9 !important;
    color: #64748b !important;
    border: 1px solid #cbd5e1 !important;
}
.btnAttachContractScan {
    height: 26px !important;
    background: #2563eb !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 4px !important;
    padding: 0 9px !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    cursor: pointer !important;
    transition: background 0.15s ease !important;
    white-space: nowrap !important;
}
.btnAttachContractScan:hover {
    background: #1d4ed8 !important;
}

/* ========================================================
   SEPARATED & COMPACT PAYMENT SYSTEM (CONSULTING & CB)
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
    transition: all 0.2s ease !important;
}
.compact-table-card.is-collapsed {
    margin-bottom: 2px !important;
}
.compact-table-card.is-collapsed .compact-table-header {
    border-radius: 4px !important;
}
.compact-table-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 3px 8px !important;
    min-height: 24px !important;
    height: 24px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    letter-spacing: 0.02em !important;
    cursor: pointer !important;
    user-select: none !important;
    transition: filter 0.15s ease !important;
}
.compact-table-header:hover {
    filter: brightness(1.06) !important;
}
.section-collapse-indicator {
    display: inline-block !important;
    margin-left: 6px !important;
    font-size: 10px !important;
    color: #ffffff !important;
    opacity: 0.9 !important;
    transition: transform 0.2s ease, opacity 0.15s ease !important;
}
.compact-table-header:hover .section-collapse-indicator {
    opacity: 1 !important;
    transform: scale(1.15);
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
    font-weight: 600 !important;
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
    font-weight: 600 !important;
    font-size: 10px !important;
    padding: 0 7px !important;
    height: 19px !important;
    line-height: 17px !important;
    border-radius: 3px !important;
    border: 1px solid rgba(255, 255, 255, 0.8) !important;
    box-shadow: none !important;
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
    font-weight: 600 !important;
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

/* Payment Tables Dimensions & Input Controls */
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
    font-size: 10px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    padding: 3px 6px !important;
    height: 22px !important;
    line-height: 1.1 !important;
    border: 1px solid #e2e8f0 !important;
    border-top: none !important;
    vertical-align: middle !important;
}
.compact-payment-table tbody td,
table.compact-payment-table.service_details tbody td {
    padding: 2px 3px !important;
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
    height: 23px !important;
    min-height: 23px !important;
    max-height: 23px !important;
    padding: 0 6px !important;
    font-size: 11.5px !important;
    font-weight: 500 !important;
    line-height: 21px !important;
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
    border-color: #2563eb !important;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
    outline: none !important;
}
.table-edit-control input.amountDecided,
.table-edit-control input.consultingAmountDecided,
.compact-payment-table .table-edit-control input.amountDecided,
.compact-payment-table .table-edit-control input.consultingAmountDecided {
    font-weight: 600 !important;
    text-align: right !important;
    color: #0f172a !important;
}
.table-edit-control input.paymentReceived,
.table-edit-control input.consultingPaymentReceived,
.compact-payment-table .table-edit-control input.paymentReceived,
.compact-payment-table .table-edit-control input.consultingPaymentReceived {
    font-weight: 600 !important;
    text-align: right !important;
    color: #15803d !important;
    background: #f0fdf4 !important;
    border-color: #86efac !important;
}
.table-edit-control input.balance,
.table-edit-control input.consultingBalance,
.compact-payment-table .table-edit-control input.balance,
.compact-payment-table .table-edit-control input.consultingBalance {
    font-weight: 700 !important;
    text-align: right !important;
    color: #b91c1c !important;
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
    cursor: default !important;
}

/* DOTTED SEPARATOR LINE BETWEEN CONSULTING AND CB */
.payment-dotted-divider {
    display: flex !important;
    align-items: center !important;
    text-align: center !important;
    margin: 6px 0 5px 0 !important;
    position: relative !important;
}
.payment-dotted-divider::before,
.payment-dotted-divider::after {
    content: '' !important;
    flex: 1 !important;
    border-bottom: 1.5px dashed #cbd5e1 !important;
}
.payment-dotted-divider .divider-label {
    padding: 2px 10px !important;
    background: #f1f5f9 !important;
    color: #334155 !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    border-radius: 12px !important;
    border: 1px dashed #cbd5e1 !important;
    margin: 0 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    line-height: 16px !important;
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
    font-weight: 600 !important;
    color: #475569 !important;
    display: flex !important;
    align-items: center !important;
    line-height: 1 !important;
}
.header-invoice-btn,
.serviceInvoiceBtn.header-invoice-btn {
    background: #1d4ed8 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    padding: 1px 10px !important;
    height: 21px !important;
    line-height: 19px !important;
    border-radius: 12px !important;
    border: none !important;
    box-shadow: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    transition: all 0.15s ease !important;
    cursor: pointer !important;
}
.header-invoice-btn:hover,
.serviceInvoiceBtn.header-invoice-btn:hover {
    background: #1e40af !important;
    color: #ffffff !important;
}
.compact-invoice-btn,
.serviceInvoiceBtn.compact-invoice-btn {
    background: #0891b2 !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 10.5px !important;
    padding: 1px 9px !important;
    height: 21px !important;
    line-height: 19px !important;
    border-radius: 4px !important;
    box-shadow: none !important;
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
    box-shadow: none !important;
    overflow: hidden !important;
}
.kpi-chip-label {
    font-size: 9px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.03em !important;
    color: #64748b !important;
    line-height: 1 !important;
    margin: 0 !important;
    white-space: nowrap !important;
}
.kpi-chip-value {
    font-size: 11.5px !important;
    font-weight: 700 !important;
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

/* Invoicing & Payment System 3-Column Component Styles */
.compact-invoicing-table thead th,
.compact-payment-receipt-table thead th {
    font-size: 10px !important;
    font-weight: 600 !important;
    letter-spacing: 0.04em !important;
    text-transform: uppercase !important;
    color: #64748b !important;
    padding: 2px 4px !important;
}
.compact-invoicing-table tbody td,
.compact-payment-receipt-table tbody td {
    padding: 2px 4px !important;
    vertical-align: middle !important;
}
.service-payment-system-card {
    border: 1.5px solid #cbd5e1 !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}
.service-payment-system-card.is-matched {
    border: 1.5px solid #22c55e !important;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.12) !important;
}
.service-payment-system-card.is-excess {
    border: 1.5px solid #f59e0b !important;
}
.inv-pill {
    border: 1px solid #cbd5e1 !important;
    background: #f8fafc !important;
    border-radius: 4px !important;
    padding: 1px 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #334155 !important;
}
.service-payment-system-card.is-matched .inv-pill {
    border-color: #86efac !important;
    background: #f0fdf4 !important;
    color: #166534 !important;
}
.inv-cell-box {
    border: 1px solid #cbd5e1 !important;
    border-radius: 4px !important;
    padding: 1px 5px !important;
    font-size: 11.5px !important;
    line-height: 18px !important;
    background: #ffffff !important;
}
.inv-cell-box.inv-gross-box {
    border-color: #cbd5e1 !important;
    background: #f8fafc !important;
    color: #0f172a !important;
    font-weight: 600 !important;
}
.service-payment-system-card.is-matched .inv-cell-box.inv-gross-box {
    border-color: #86efac !important;
    background: #f0fdf4 !important;
    color: #166534 !important;
}
.service-payment-system-card.is-matched .invoicing-gross-total-field {
    border-color: #86efac !important;
    background: #f0fdf4 !important;
    color: #166534 !important;
}
.payment-footer-chips-row {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    letter-spacing: 0.02em !important;
}
.payment-footer-chips-row .chip-item {
    padding: 1px 6px !important;
    border-radius: 4px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
}
.payment-footer-chips-row .chip-received {
    color: #166534 !important;
    background: #f0fdf4 !important;
    border-color: #bbf7d0 !important;
}
.payment-footer-chips-row .chip-tds {
    color: #c2410c !important;
    background: #fff7ed !important;
    border-color: #fed7aa !important;
}
.payment-footer-chips-row .chip-short {
    color: #6b21a8 !important;
    background: #faf5ff !important;
    border-color: #e9d5ff !important;
}
.payment-footer-chips-row .chip-baddebt {
    color: #b91c1c !important;
    background: #fef2f2 !important;
    border-color: #fecaca !important;
}

/* ========================================================
   MODAL DIALOG THEME ENHANCEMENTS
   ======================================================== */
.modal-content {
    border-radius: 8px !important;
    border: 1px solid #dce4ec !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12) !important;
    overflow: hidden !important;
}
.modal-header {
    background: #0d52b3 !important;
    color: #ffffff !important;
    padding: 10px 16px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
}
.modal-header .modal-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
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
    padding: 10px 16px !important;
}

/* ========================================================
   TOAST NOTIFICATION SYSTEM (TOASTER)
   ======================================================== */
#qms-toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999999;
    display: flex;
    flex-direction: column;
    gap: 8px;
    pointer-events: none;
}
.qms-toast {
    pointer-events: auto;
    min-width: 280px;
    max-width: 380px;
    background: #ffffff;
    border-radius: 6px;
    box-shadow: 0 10px 25px -5px rgba(15, 35, 70, 0.12);
    border: 1px solid #dce4ec;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
    transform: translateX(120%);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
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
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 13px;
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
.qms-toast-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.qms-toast-title {
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.2;
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
    font-size: 14px;
    cursor: pointer;
    padding: 0 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.qms-toast-close:hover {
    color: #334155;
}
.qms-toast-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 2.5px;
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
