<div id="wrapper" class="active">
    <?php
    include_once './header.php';
    include_once './dbConnect.php';

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
            
        } catch (Exception $ex) {
            echo $ex;
        }
    }
    ?>

    <script>
    function validateAttachmentUpload() {
        $isError = false;
        if ($('.customerAttachemnt').val() == "") {
            $('#attachmentErrorMsg').text("Attachment file is required");
            $('#attachmentError').show();
            $('.customerAttachemnt').addClass('inputHighlight');
            $isError = true;
            //            alert("hi");
        } else {
            $('#attachmentError').hide();
            $('.customerAttachemnt').removeClass('inputHighlight');
        }
        if ($('.attachmentinfo').val() == "") {
            $('#attachmentInfoErrorMsg').text("Attachment information is required");
            $('#attachmentInfoError').show();
            $('.attachmentinfo').addClass('inputHighlight');
            $isError = true;
            //            alert("hi");
        } else {
            $('#attachmentInfoError').hide();
            $('.attachmentinfo').removeClass('inputHighlight');
        } 
        return $isError;
    }
    
        $(document).ready(function () {
            var serviceTypeValues = new Array();
            <?php foreach ($services as $service) { ?>
                serviceTypeValues.push("<?php echo $service['service']; ?>");
            <?php } ?>
            //                alert("no values" + serviceTypeValues);
            $("#serviceType").autocomplete({
                source: serviceTypeValues
            });
            
            $('.saveattachemtninfo').click(function (e) {
                if (validateAttachmentUpload()) {
                    e.preventDefault();
                    return;
                }
            });
            
            $(".delteAttachment").click(function () {
//                alert("in");
                var customerid = $(this).attr('data-customer');
                var attachment = $(this).attr('data-id');
                $('#ducumentupload').modal("hide");
                    $('#delete-details').show();
                    $('.delete-confirm-ok').click(function () {
                    var url = 'uploadAttachemnts.php';
                    var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="attachemnt" value="' + attachment + '" />' +
                        '<input type="hidden" name="customer-id" value="' + customerid + '" />' +
                        '</form>');
                        $('body').append(form);
                    form.submit();
                });
                
                $('.delete-cancel').click(function () {
                    $('#delete-details').hide();
                    $('#ducumentupload').modal("show");
                });
            });
        });
    </script>

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
                                <?php if($attachemntsList) {                                    
                                 foreach ($attachemntsList as $list) {
                                    $ext = pathinfo($list["upload_path"], PATHINFO_EXTENSION); ?>
                                <li class="list-group-item attachment-list<?php echo $list["id"] ?>">
                                    <div class="checkbox">
                                        <?php if($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "gif"){ ?>
                                        <img src="<?php echo $list["upload_path"] ?>" alt="Smiley face" height="42" width="42">
                                        <?php } else if($ext == "pdf"){ ?>
                                        <span class="fa fa-file-pdf-o" style="font-size: 42px; color: red;"></span>
                                       <?php  } else if($ext == "doc" || $ext == "docx"){ ?>
                                        <span class="fa fa-file-word-o" style="font-size: 42px; color: #008ada;"></span>
                                       <?php  } else if($ext == "xls" || $ext == "xlsx"){ ?>
                                        <span class="fa fa-file-excel-o" style="font-size: 42px; color: green;"></span>
                                       <?php  } ?>
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
                                <?php }
                                } ?>
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
            <div class="" >
                <div class="row" style="">
                    <div class="top_qms_page">
                        <div class="">
                            <div class="row">
                                <div class="col-sm-12 qms_customer_name">                                    
                                    <span><i class="fa fa-building"></i></span>
                                    <span><?php
                                        echo $customer['company_name'];
                                        echo '';
                                        ?> </span>
                                    <?php if (!empty($customer["priority"]) && $customer["priority"] == "High") { ?>
                                        <img class="img-responsive" alt="High Priority User" title="High Priority User" src="images/medium.png" style="background-color: transparent; height: 18px; display:inline" />
                                    <?php } else if (!empty($customer["priority"]) && $customer["priority"] == "Medium") { ?>
                                        <img class="img-responsive" alt="Medium Priority User" title="High Priority User" src="images/low.png" style="background-color: transparent; height: 16px; display:inline" />
                                    <?php } else if (!empty($customer["priority"]) && $customer["priority"] == "Low") { ?>
                                        <img class="img-responsive" alt="Low Priority User" title="High Priority User" src="images/high.png" style="background-color: transparent; height: 15px; display:inline" />
                                    <?php } ?>
                                    <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing()) { ?>
                                        <a class="editCustomerDetails" style="float: right; color: #999;" data-id="<?php echo $_GET['customerid'] ?>" title="Edit customer details"><span><i class="fa fa-edit"></i></span></a>
                                        <a class="deleteCustomerDetails" style="float: right; color: #999; padding-right: 10px" data-id="<?php echo $_GET['customerid'] ?>" title="Delete customer details"><span><i class="fa fa-trash-o"></i></span></a>
                                        <a class="customerAttachemnts" style="float: right; color: #999; padding-right: 10px" data-id="<?php echo $_GET['customerid'] ?>" title="Attach Documents"><span><i class="fa fa-link"></i></span></a>
                                    <?php } ?>                                        
                                </div>
                            </div>
                            <div class="row" style="padding-top:10px">
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
                                    <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                        <div class="row" style="padding:5px; padding-left:20px">
                                            <div class="col-xs-1">
                                                <span style="color: #428bca" title="Client referd by-"><i class="fa fa-share-square-o fa-lg"></i></span> </div>
                                            <div class="col-xs-11">
                                                <span><?php echo $customer['client_reference'] ?></span>
                                            </div>
                                        </div>
                                    <?php } ?>
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
                                                ?>
                                            </textarea>
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
                            <div class="col-xs-4  text-center btn  qms_complete_btn invoiceBtn" data-customer="<?php echo $_GET['customerid'] ?>">
                                <span><i class="fa fa-inr "> Invoice</i></span>
                            </div>
                            <div class="col-xs-4  text-center btn qms_complete_btn quotationBtn" data-customer="<?php echo $_GET['customerid'] ?>">
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
                            <div class="col-xs-4  text-center btn qms_complete_btn <?php echo $active ?> " <?php echo $show ?>>
                                <span><i class="fa fa-plus-square "> Add Service </i></span>
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
                <div class="servicebox" style="display:<?php echo $saveButton ?> <?php //echo $srvcDisplay                     ?>">
                    <div class="row" id="service-row<?php echo $serviceId; ?>" class="service-row"> 
                        <div class="col-xs-12 qms_customer_name service_title" style="text-align: center" data-row-id="<?php echo $serviceId; ?>">Service - <span class="serviceStatus"><?php if (isset($service1['service_type'])) echo $service1['service_type']; ?></span>
                            <a class="saveService" id="saveService" data-row-id="<?php echo $serviceId; ?>"><i class="fa fa-floppy-o pull-right " style="color:white; padding-right:10px; display: <?php echo $saveButton; ?>"></i></a>
                            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin()) { ?>
                                <a class="deleteService" id="deleteService" data-row-id="<?php echo $serviceId; ?>"><i class="fa fa-trash-o pull-right " style="color:white; padding-right:10px;"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="servicebox-data" id="servicebox-data<?php if (isset($serviceId)) echo $serviceId; ?>" <?php echo $displayHtmlStr ?>>
                        <div class="row" style="padding-top:10px">
                            <div class="col-xs-3 form-group float-label-control">
                                <label>Service Stage</label>
                                <select class="service-type" id="service-type" name="service-type" data-row-id="<?php echo $serviceId; ?>" value="" <?php echo $financeAccess ?><?php echo $fieldAccess ?>>
                                    <option value="<?php if (isset($service1['service_type'])) echo $service1['service_type'] ?>" ><?php
                                        if (isset($service1['service_type']) && !empty($service1['service_type'])) {
                                            echo $service1['service_type'];
                                        } else {
                                            echo "Select Service Stage";
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
                                <!--<input type="text" class="consultant-list" id="consultant-list" name="consultant-list" placeholder="Consultant" data-row-id="<?php echo $serviceId; ?>" value="<?php if (isset($service1['consultant'])) echo $service1['consultant'] ?>" <?php echo $financeAccess ?> <?php echo $fieldAccess ?>>-->
                            </div>
                            <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="service-id" id="service-id" name="service-id" value="<?php if (isset($serviceId)) echo $serviceId; ?>">
                        </div>
                        <div class="row">
                            <div class="col-xs-2 form-group float-label-control" style="margin-bottom:2px;">
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
                                <!--<input type="text" class="auditor-list" data-row-id="<?php echo $serviceId; ?>" id="auditor-list" name="auditor-list" placeholder="Auditor" value="<?php if (isset($service1['auditor'])) echo $service1['auditor'] ?>" <?php echo $financeAccess ?>>-->
                            </div>
                            <div class="col-xs-2 form-group float-label-control" style="margin-bottom:2px;">
                                <label>Audit Date</label>
                                <input type="text" class="<?php echo $addClassToAudit ?>" data-row-id="<?php echo $serviceId; ?>"  name="audit-date" placeholder="Audit Date" value="<?php if (isset($service1['audit_date'])) echo simpleDateConversion($service1['audit_date']) ?>" <?php echo $financeAccess ?>>
                            </div>
                            <?php if (Utitlity::checkForUserConsultant() || Utitlity::checkForUserFinance() ) { ?>
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
                                            <small style="font-weight:bold; color:#003399; font-size:12px;">Contract Details - </small>
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
                                            <i class="fa fa-comment-o" aria-hidden="true"></i>
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
                                                <td class="" style="padding:0px;">
                                                    <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                                        <a class="cbComment" data-row-id="<?php echo $serviceId; ?>" data-customer="<?php echo $_GET['customerid'] ?>" data-cbid="<?php if (isset($cert['certification_id'])) echo $cert['certification_id']; ?>" data-comment="<?php if (isset($cert['cert_comment'])) echo $cert['cert_comment']; ?>"><i class="fa fa-comment-o" aria-hidden="true"></i></a>
                                                    <?php } ?>
                                                </td>
                                                <td class="" style="padding:0px;">
                                                    <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                        <input type="checkbox" class="cbDelete" data-row-id="<?php echo $serviceId; ?>" value="<?php echo $cert['certification_id'] ?>">
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="cb-row-comment <?php echo $cert['certification_id'] ?>" id="cb-row-comment" name="cb-row-comment" value="">
                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="cb-row-id <?php echo $cert['certification_id'] ?>" id="cb-row-id" name="cb-row-id" value="<?php if (isset($cert['certification_id'])) echo $cert['certification_id']; ?>">
                                        <?php
                                    }
                                } else {
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
                                        <td class="" style="padding:0px;">
                                        <!--<a class="cbComment" data-row-id="<?php echo $serviceId; ?>"><i class="fa fa-comment-o" aria-hidden="true"></i></a>-->
                                        </td>
                                        <td class="" style="padding:0px;">
                                            <div class="form-group"></div>
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
                                            <td class="" style="padding:0px;">
                                                <?php if (!Utitlity::checkForUserConsultant()) { ?>
                                                    <a class="consultingComment" data-row-id="<?php echo $serviceId; ?>" data-customer="<?php echo $_GET['customerid'] ?>" data-consultingid="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>" data-consultComment="<?php if (isset($consult['consulting_comment'])) echo $consult['consulting_comment']; ?>"><i class="fa fa-comment-o" aria-hidden="true"></i></a>
                                                <?php } ?>
                                            </td>
                                            <td class="" style="padding:0px;">
                                                <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?>
                                                    <input type="checkbox" class="consultingDelete" data-row-id="<?php echo $serviceId; ?>" value="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>" style="display: "/>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <input type="hidden" data-row-id="<?php echo $serviceId; ?>" class="consulting-row-id <?php echo $consult['consulting_id'] ?>" id="consulting-row-id" name="consulting-row-id" value="<?php if (isset($consult['consulting_id'])) echo $consult['consulting_id']; ?>">
                                        <?php
                                    }
                                } else {
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
                                        <td class="" style="padding:0px;">
                                        <!--<a><i class="fa fa-comment-o" aria-hidden="true"></i></a>-->
                                        </td>
                                        <td class="" style="padding:0px;">
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

<script type="text/javascript">
    function validateCertification() {
        $isError = false;
        if ($('.certification-date').val() == "")
        {
//            alert('in');
            $('.certificationDateError').show();
            $('.certificationDateError').show();
            $('.certification-date').addClass('inputHighlight');
            $isError = true;
//    alert("email not valid"+$error);
        } else {
//            alert('else');
            $('.certificationDateError').hide();
            $('.certification-date').removeClass('inputHighlight');
        }
        return $isError;
    }

    $(document).ready(function () {
        
        $('.customercomment').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();

                var inputVal = $(this).val();
                if (inputVal != "" && inputVal != null)
                    inputVal = $(this).val() + "\n";

                var appendText = "<?php echo date("d.m.Y") . " - " . $_SESSION['abbrevation'] . " - " ?>";
                // Enter pressed... do anything here...
                $(this).val(inputVal + appendText);
            }
        });

        $('.issueCert').change(function (e) {
            if (validateCertification()) {
                e.preventDefault();
                return;
            }
            var service = $(this).attr('data-service');
            var customer = $(this).attr('data-customer');
//            alert($(this).attr('data-service'));
            if ($(this).is(":checked")) {
                $.ajax({
                    url: "issueCertification.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        customer: customer,
                        service: service,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
                        $('.nextService').val(data['next_service_type']);
                    },
                    error: function (data, status, err) {

                    }
                });
            } else {
                $.ajax({
                    url: "issueCertification.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        customerID: customer,
                        serviceID: service,
                    },
                    success: function (data)
                    {
//                    alert(data);
//                        $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                        $('.nextService').val(data['next_service_type']);
                    },
                    error: function (data, status, err) {

                    }
                });
            }
        });

        $('.deleteCustomerDetails').click(function () {
            var customerid = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').click(function () {
                var url = 'deleteCustomerService.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="customerid" value="' + customerid + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('.deleteService').click(function () {
            var serviceId = $(this).attr('data-row-id');
            var customerId = '<?php echo $_GET['customerid'] ?>';
            $('#delete-details').show();
            $('.delete-confirm-ok').click(function () {
                var url = 'deleteCustomerService.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="serviceID" value="' + serviceId + '" />' +
                        '<input type="hidden" name="customerID" value="' + customerId + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $(".customerAttachemnts").click(function () {
            $("#ducumentupload").modal("show");
        });

        $('.editCustomerDetails').click(function () {
            $.ajax({
                url: "getUserDetails.php", type: "GET",
                dataType: "json",
                data: {
                    customerId: $(this).attr('data-id'),
                },
                success: function (data)
                {
                    var contact = data['contact_no'].split(', ');
                    var contactPerson = data['contact_person'].split(', ');
                    var contactEmail = data['contact_email'].split(', ');
                    var contactLen = contact.length;
                    var contactPrsnLen = contactPerson.length;
                    var contactEmainLen = contactEmail.length;
                    var max = (Math.max(contactLen, contactPrsnLen, contactEmainLen));
                    for (var key = 0; key < max; key++) {
//                        var no=contact[key];
                        var no = key <= contactLen && contact[key] != null && contact[key] != "" ? contact[key] : "";
                        var mail = key <= contactEmainLen && contactEmail[key] != null && contactEmail[key] != "" ? contactEmail[key] : "";
                        var person = key <= contactPrsnLen && contactPerson[key] != null && contactPerson[key] != "" ? contactPerson[key] : "";
//                        if (contact[key] != null && contact[key] != "") {

                        if (key == 0) {
                            $('.contactNo').val(no);
                            $('.customerEmail').val(mail);
                            $('.contactPerson').val(person);
                        } else {
                            $('.newCustomerContact').empty();
                            $('.newCustomerContact').append('<div class="row newContactRowParent">\n\
                                                            <div class="col-sm-4 form-group float-label-control">\n\
                                                            <label>Email Id</label>\n\
                                                            <input type="email" class="customerEmail" id="customerEmail" name="customerEmail" value="' + mail + '" placeholder="Client Email" >\n\
                                                            <div id="contactEmailError" style="display: none">\n\
                                                            <small><span id="contactEmailErrorMsg" style="color: #c9513c; font-style: italic;" ></span></small>\n\
                                                            </div>\n\
                                                            </div>\n\
                                                            <div class="col-sm-4 form-group float-label-control">\n\
                                                            <label>Contact Person</label>\n\
                                                            <input type="text" class="contactPerson" id="contactPerson" name="contactPerson" value="' + person + '" placeholder="Contact Person" >\n\
                                                            </div>\n\
                                                            <div class="col-sm-3 form-group float-label-control">\n\
                                                            <label>Contact No</label>\n\
                                                            <input type="text" class="contactNo" id="contactNo" name="contactNo" placeholder="Contact No" value="' + no + '">\n\
                                                            <div id="customerContactError" style="display: none">\n\
                                                            <small><span id="customerContactErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>\n\
                                                            </div>\n\
                                                            </div>\n\
                                                            <div class="col-sm-1 form-group float-label-control deleteNewContactParent">\n\
                                                            <a class="deleteContact" style="float: right;"><i class="fa fa-remove"></i></a>\n\
                                                            </div>\n\
                                                            </div>');
                        }
                    }
//                    console.log(data['contact_no'].split(',')); //                    alert(data['contact_no'].split(','));
                    $('.editCustomerId').val(data['customer_id']);
                    $('.companyName').val(data['company_name']);
                    $('.clientPriority').val(data['priority']);
                    $('.companyAddress').val(data['company_address']);
                    $('.customerReferred').val(data['client_reference']);
//                    $('.editContactPerson').val(data['contact_person']);
//                    $('.editContactNo').val(data['contact_no']);
                    $('.comments').val(data['comments']);
                    if(data["client_reference"]){
                        if(data["client_reference"] == "Just Dial" || data["client_of"] == "Just Dial"){
                            $('#justdial').prop('checked', true);
                        } else if(data["client_reference"] == "Tele Call" || data["client_of"] == "Tele Call"){
                            $("#telecall").prop('checked', true);
                        }
                        else
                        {
                            $("#other").prop('checked', true);
                            $(".customerReferred").prop('disabled', false);
                        }
                    }
                    $('input').trigger('change');
                    $('textarea').trigger('change');
                    $('#customerAdd').modal('show');
                },
                error: function (data, status, err)
                {
                    //                    $('.addNewService').empty();
                    alert(status);
                }
            });
        });
        
        $cbid = "";
        $customerId = "";
        $consulting_id = "";
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");
            table.draw();
        });
        $(document.body).on('click', '.service_title', function (e) {
            //        $('.service_title').click(function (e) {

//                    alert("da");
            $id = $(this).data("row-id");
//            alert($id);
            $('#servicebox-data' + $id).toggle();
        });
        $('.saveChanges').change(function (e) {
//            alert($('.nextService').val());
            saveData($(this));
        });
        $('.certificationComment').change(function (e) {
            saveComments($(this));
        });
        $checkedValue = "";
        function saveComments($selector) {
            $checkedValue = $selector.val();
//alert('hi');
            $.ajax({
                url: "addComments.php",
                dataType: "json",
                type: 'POST',
                data: {
                    cbId: $cbid,
                    certificationComment: $checkedValue,
                },
                success: function (data) {
//                    alert(data);
                },
                error: function (data, status, err) {

                }
            });
            $selector.removeClass("glowing-border");
            //           $(this).removeClass("form-group ");
            $selector.addClass("lookslike-label");
        }

        $('.consultComment').change(function (e) {
            saveCunsultComments($(this));
        });
        function saveCunsultComments($selector) {
            $checkedValue = $selector.val();
//            alert('hi');
            $.ajax({
                url: "addComments.php",
                dataType: "json",
                type: 'POST',
                data: {
                    consultId: $consulting_id, consultComment: $checkedValue,
                },
                success: function (data) {
//                    alert(data);
                },
                error: function (data, status, err) {

//                    alert("Error " + status);
                }
            });
            $selector.removeClass("glowing-border");                         //           $(this).removeClass("form-group ");
            $selector.addClass("lookslike-label");
        }

        function saveData($selector) {

            $checkedValue = $selector.val();
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    id: "<?php echo $_GET['customerid'] ?>",
                    columnName: $selector.attr('data-column'),
                    columnValue: $checkedValue
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                },
                success: function (data) {
//                    alert(data); 
//                   (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
            $selector.removeClass("glowing-border");
//           $(this).removeClass("form-group ");
            $selector.addClass("lookslike-label");
        }

        $(document.body).on('click', '.saveService', function () {
//                    alert("in");
            $rowId = $(this).data("row-id");
            if ($rowId == undefined) {
                $rowId = "";
            }
            var contractDetailsArray = [];
            $(".contractDetails").each(function () {         //                        alert($(this).data("row-id"));
                if ($rowId == $(this).data("row-id"))
                    contractDetailsArray.push($(this).val());
            });
            $cbContractDetails = contractDetailsArray.join("~");
            var amountDecidedArray = [];
            $('.amountDecided').each(function () {
                if ($rowId == $(this).data("row-id"))
                    amountDecidedArray.push($(this).val());
            });
            $cbAmountDecided = amountDecidedArray.join("~");
            var paymentReceivedArray = [];
            $('.paymentReceived').each(function () {
                if ($rowId == $(this).data("row-id"))
                    paymentReceivedArray.push($(this).val());
            });
            $cbPaymentReceived = paymentReceivedArray.join("~");
            var depositDateArray = [];
            $('.depositDate').each(function () {
                if ($rowId == $(this).data("row-id"))
                    depositDateArray.push($(this).val());
            });
            $cbDepositDate = depositDateArray.join("~");
            var chequeDetailsArray = [];
            $('.chequeDetails').each(function () {
                if ($rowId == $(this).data("row-id"))
                    chequeDetailsArray.push($(this).val());
            });
            $cbChequeDetails = chequeDetailsArray.join("~");
            var bankAcccountNoArray = [];
            $('.bankAcccountNo').each(function () {
                if ($rowId == $(this).data("row-id"))
                    bankAcccountNoArray.push($(this).val());
            });
            $cbBankAcccountNo = bankAcccountNoArray.join("~");
            var balanceArray = [];
            $('.balance').each(function () {
                if ($rowId == $(this).data("row-id"))
                    balanceArray.push($(this).val());
            });
            $cbBalance = balanceArray.join("~");
            var cbRowArray = [];
            $('.cb-row-id').each(function () {
                if ($rowId == $(this).data("row-id")) {
                    if (cbRowArray.indexOf($(this).val()) == -1)
                        cbRowArray.push($(this).val());
                }
            });
            $cbrowid = cbRowArray.join("~");
            var consultingContractDetailsArray = [];
            $('.consultingContractDetails').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingContractDetailsArray.push($(this).val());
            });
            $consultingContractDetails = consultingContractDetailsArray.join("~");
            var consultingAmountDecidedArray = [];
            $('.consultingAmountDecided').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingAmountDecidedArray.push($(this).val());
            });
            $consultingAmountDecided = consultingAmountDecidedArray.join("~");
            var consultingPaymentReceivedArray = [];
            $('.consultingPaymentReceived').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingPaymentReceivedArray.push($(this).val( ));
            });
            $consultingPaymentReceived = consultingPaymentReceivedArray.join("~");
            var consultingDepositDateArray = [];
            $('.consultingDepositDate').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingDepositDateArray.push($(this).val());
            });
            $consultingDepositDate = consultingDepositDateArray.join("~");
            var consultingChequeDetailsArray = [];
            $('.consultingChequeDetails').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingChequeDetailsArray.push($(this).val());
            });
            $consultingChequeDetails = consultingChequeDetailsArray.join("~");
            var consultingBankAcccountNoArray = [];
            $('.consultingBankAcccountNo').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingBankAcccountNoArray.push($(this).val());
            });
            $consultingBankAcccountNo = consultingBankAcccountNoArray.join("~");
            var consultingBalanceArray = [];
            $('.consultingBalance').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingBalanceArray.push($(this).val());
            });
            $consultingBalance = consultingBalanceArray.join("~");
            var consultingRowArray = [];
            $('.consulting-row-id').each(function () {
                if ($rowId == $(this).data("row-id")) {
                    if (consultingRowArray.indexOf($(this).val()) == -1)
                        consultingRowArray.push($(this).val());
                }
            });
            $consultingrowid = consultingRowArray.join("~");
//for delete row functionality
            var deleteCB = [];
            $('.cbDelete').each(function () {
                if ($rowId == $(this).data("row-id") && $(this).is(":checked"))
                    deleteCB.push($(this).val());
            });
            $deletCbRows = deleteCB.join("~");
            var deleteConsulting = [];
            $('.consultingDelete').each(function () {
                if ($rowId == $(this).data("row-id") && $(this).is(":checked"))
                    deleteConsulting.push($(this).val());
            });
            $deletConsultingRows = deleteConsulting.join("~");
            var serviceid = $('.service-id[data-row-id="' + $rowId + '"]').val();
//                    alert("serviceid" + serviceid);
            var serviceType = $('.service-type[data-row-id="' + $rowId + '"]').val(); //$('.service-type').val();
//                    alert(serviceType);
            var certBody = $('.cert-body[data-row-id="' + $rowId + '"]').val();
            var consultingFirm = $('.consulting-firm[data-row-id="' + $rowId + '"]').val();
            var consultant = $('.consultant-list[data-row-id="' + $rowId + '"]').val();
            var auditType = $('.audit-type[data-row-id="' + $rowId + '"]').val();
            var auditor = $('.auditor-list[data-row-id="' + $rowId + '"]').val();
            var auditdate = $('.audit-date[data-row-id="' + $rowId + '"]').val();
            var serviceaddto = $('.add-to-service[data-row-id="' + $rowId + '"]').val();
            var handelBy = $('.handel-by[data-row-id="' + $rowId + '"').val();
            var customerId = $('.customer_id[data-row-id="' + $rowId + '"]').val();
            if ($('.issueCert[data-row-id="' + $rowId + '"]').is(':checked')) {
                var issue_cert = "YES";
            } else {
                var issue_cert = "NO";
            }
            //            alert(issue_cert);
            var cbContractDetails = $cbContractDetails;
            var cbDecidedAmount = $cbAmountDecided;
            var cbPaymentReceived = $cbPaymentReceived;
            var cbDepositDate = $cbDepositDate;
            var cbChequeDetails = $cbChequeDetails;
            var cbBankAcccountNo = $cbBankAcccountNo;
            var cbBalance = $cbBalance;
            var cbRoeIds = $cbrowid;
            var consultingDetails = $consultingContractDetails;
            //            alert(consultingDetails);   
            var consultingAmount = $consultingAmountDecided;
            var consultingPayment = $consultingPaymentReceived;
            var consultingDepositDate = $consultingDepositDate;
            var consultingChequeDetails = $consultingChequeDetails;
            var consultingBankAcccountNo = $consultingBankAcccountNo;
            var consultingBalance = $consultingBalance;
            var consultingrowId = $consultingrowid;
            //Amol - there must be extra tr getting added, lets ty to remove it
            //            alert($deletCbRows);
            if (serviceType == "") {
                $('#validation-modal').modal('show');
            } else {
                $.ajax({
                    url: "addService.php",
                    dataType: "json",
                    type: 'GET',
                    data: {
                        serviceId: serviceid,
                        servicetype: serviceType,
                        certbody: certBody,
                        consultingfirm: consultingFirm,
                        consultantlist: consultant,
                        audittype: auditType,
                        auditorlist: auditor,
                        auditDate: auditdate,
                        addTo: serviceaddto,
                        handelby: handelBy,
                        certIssue: issue_cert,
                        customer_id: customerId,
                        cbContractDetails: cbContractDetails,
                        cbDecidedAmount: cbDecidedAmount,
                        cbPaymentReceived: cbPaymentReceived,
                        cbDepositDate: cbDepositDate,
                        cbChequeDetails: cbChequeDetails,
                        cbBankAcccountNo: cbBankAcccountNo,
                        cbBalance: cbBalance,
                        cbRows: cbRoeIds,
                        consultingDetails: consultingDetails,
                        consultingAmount: consultingAmount,
                        consultingPayment: consultingPayment,
                        consultingDepositDate: consultingDepositDate,
                        consultingChequeDetails: consultingChequeDetails,
                        consultingBankAcccountNo: consultingBankAcccountNo,
                        consultingBalance: consultingBalance,
                        consultingrows: consultingrowId,
                        cbDeleteRows: $deletCbRows,
                        consultingDeleteRows: $deletConsultingRows
                    },
                    success: function (data) {
                        //                        alert("Data updated successfully"); //change to dialog
                        if (data.error)
                            return;
                        //fire off other ajax calls
                        $(document).ajaxStop(function () {
                            location.reload(true);
                        });
                    },
                    error: function (data, status, err) {
                        alert("Error " + status + "err " + err + " data " + JSON.stringify(data));
                        //                    alert("There was error while saving data. Please refresh page to see if values are saved or contact support team");
                    }
                });
            }
        });
        $('body').on('click', ".cbComment", function (e) {

            $cbid = $(this).attr("data-cbid");
            $customerId = $(this).attr('data-customer');
            $('.certificationComment').val($(this).attr('data-comment'));
            $('.certificationComment').trigger('change');
            $('#addCertificetionComment').modal("show");
        });
        $('body').on('click', ".consultingComment", function (e) {
            $consulting_id = $(this).attr("data-consultingid");
            $customer = $(this).attr('data-customer');
            $('.consultComment').val($(this).attr('data-consultComment'));
            $('.consultComment').trigger('change');
            $('#addConsultingComment').modal("show");
        });
        $('.quotationBtn').click(function () {
            $('.quoteCustomerId').val($(this).attr('data-customer'));
            $('#quotation').modal("show");
        });
        $('.invoiceBtn').click(function () {
            $('.invoiceCustomerId').val($(this).attr('data-customer'));
                        alert($('.invoiceCustomerId').val());
            $('#invoiceModal').modal("show");
        });
        //        auto complete for service type

        $('.nextService').autocomplete({
            source: function (request, response) {
                //                alert($('.service-type').val());
                $.ajax({
                    url: "autoSearch.php",
                    dataType: "json",
                    type: 'GET',
                    data: {serviceName: $('.nextService').val(),
                    },
                    success: function (data) {
                        //                      alert(data);
                        response(data);
                        //                        $('.nextService').val(data['label']);                       
                        $checkedValue = data['label'];
                    },
                    error: function (data, status, err) {
                        //                        alert(status);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                //                alert(ui.item.label);
                $.ajax({
                    url: "addCustomerDetails.php", type: "GET",
                    //                    dataType: 'JSON',
                    data: {
                        customerid: "<?php echo $_GET['customerid'] ?>",
                        value: ui.item.label,
                    },
                    error: function (xhr, status, error) {
                        //                    alert("There seems to be issue saving data!");
                    },
                    success: function (data) {
                        //                    alert(data);                     
                    }
                });
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });
        $(document).on('keyup.autocomplete', ".bankAcccountNo", function () {
            $getVal = $(this).val();
            $serviceid = $(this).attr('data-row-id');
            $(this).autocomplete({
                source: function (request, response) {
                    //                alert($('.service-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            serviceid: $serviceid, //$('.service-type').val(), consultingBankAcccountNo
                            bankac: $getVal,
                        },
                        success: function (data) {
                            //                           alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
                            //                           alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
        $(document).on('keyup.autocomplete', ".consultingBankAcccountNo", function () {
            $getVal = $(this).val();
            $serviceid = $(this).attr('data-row-id');
            $(this).autocomplete({
                source: function (request, response) {
                    //                alert($('.service-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            serviceId: $serviceid, //$('.service-type').val(), 
                            consultbankac: $getVal,
                        },
                        success: function (data) {
                            //                           alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
                            //                           alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
        $(document).on('keyup.autocomplete', ".service-type", function () {
            $getVal = $(this).val();
            //                    alert($getVal);
            $(this).autocomplete({
                source: function (request, response) {
                    //                alert($('.service-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            serviceName: $getVal, //$('.service-type').val(),
                        },
                        success: function (data) {
//                      alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
                            //                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
        //        $(document).on('change', ".service-type", function () {
        //            $rowId = $(this).attr('data-row-id');
        //            if ($(this).val() != "CA" || $(this).val() != "SA-1" || $(this).val() != "SA-2" || $(this).val() != "RC") {
        //                document.getElementById('issueCert[data-row-id="' + $rowId + '"]').disabled= true;
        ////                $('.issueCert[data-row-id="' + $(this).data("row-id") + '"]').is(':checked')
//                alert('in');
        //            }
//        });

        $(document).on('keyup.autocomplete', ".cert-body", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.cert-body').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            certBody: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
        $(document).on('keyup.autocomplete', ".consulting-firm", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.consulting-firm').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            consultingFirm: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
        $(document).on('keyup.autocomplete', ".consultant-list", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.consultant-list').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            consultant: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {

//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
//        auto complete for auditor

        $(document).on('keyup.autocomplete', ".auditor-list", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.auditor-list').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            auditor: $('.auditor-list').val(),
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
//        auto complete for audit-type
        $(document).on('keyup.autocomplete', ".audit-type", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.audit-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            auditType: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
        $(document).on('keyup.autocomplete', ".add-to-service", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.audit-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            auditTo: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
        $('.saveChanges').click(function (e) {
            $(this).addClass("glowing-border");
//           $(this).addClass("form-group ");
        });
        $('.addNewService').click(function () {
//                    $('#addService').modal("show");
//                    $("#saveService0").hide();
            $serviceStr = '<div class="servicebox">\n\
<div class="row" id="service-row" class="service-row" >\n\
    <div class="col-xs-12 qms_customer_name service_title" data-row-id="" style="text-align: center">Service - <span class="serviceStatus"></span><a class="saveService" data-row-id=""><i class="fa fa-floppy-o pull-right " style="color:white; padding-right:10px"></i></a></div>\n\
</div>\n\
<div class="servicebox-data" id="servicebox-data">\n\
    <div class="row" style="padding-top:10px">\n\
        <div class="col-xs-3 form-group float-label-control">\n\
            <label>Service Stage</label>\n\
                <select class="service-type" id="service-type" name="service-type" data-row-id="" value="">\n\
                <option value="" >Select Service Stage</option>\n\<?php foreach ($allServices as $svc) { ?>\n\
                                                                    <option value="<?php echo $svc['service'] ?>"><?php echo $svc['service'] ?></option>\n\<?php } ?></select>\n\
        </div>\n\
        <div class="col-xs-3 form-group float-label-control">\n\
            <label>Cert Body</label>\n\
            <select class="cert-body" id="cert-body" name="cert-body" data-row-id="" value="">\n\
                <option value="" >Select Cert Body</option>\n\<?php foreach ($allCert as $crt) { ?>\n\
                                                                    <option value="<?php echo $crt['cert'] ?>"><?php echo $crt['cert'] ?></option>\n\<?php } ?></select>\n\
        </div>\n\
        <div class="col-xs-3 form-group float-label-control">\n\
            <label>Consulting firm</label>\n\
            <select class="consulting-firm" id="consulting-firm" name="consulting-firm" data-row-id="" value="">\n\
                <option value="" >Select Consulting Firm</option>\n\<?php foreach ($consultFirm as $firm) { ?>\n\
                                                                    <option value="<?php echo $firm['firm_name'] ?>"><?php echo $firm['firm_name'] ?></option>\n\<?php } ?></select>\n\
        </div>\n\
        <div class="col-xs-3 form-group float-label-control">\n\
            <label>Lead Consultant</label>\n\
            <select class="consultant-list" id="consultant-list" name="consultant-list" data-row-id="" value="">\n\
                <option value="" >Select Lead Consultant </option>\n\<?php foreach ($consultant as $cunslt) { ?>\n\
                                                                    <option value="<?php echo $cunslt['consultant_name'] ?>"><?php echo $cunslt['consultant_name'] ?></option>\n\<?php } ?></select>\n\
        </div>\n\
        <input type="hidden" class="service-id" id="service-id" data-row-id="" name="service-id" value="">\n\
    </div>\n\
    <div class="row">\n\
        <div class="col-xs-2 form-group float-label-control">\n\
            <label>Lead Auditor</label>\n\
            <select class="consultant-list" id="consultant-list" name="consultant-list" data-row-id="" value="">\n\
                <option value="" >Select Lead Auditor</option>\n\<?php foreach ($allAuditorList as $auditors) { ?>\n\
                                                                    <option value="<?php echo $auditors['auditor_name'] ?>"><?php echo $auditors['auditor_name'] ?></option>\n\<?php } ?></select>\n\
           <!-- <input type="text" class="auditor-list" data-row-id="" id="auditor-list" name="auditor-list" placeholder="Auditor" value="" required> -->\n\
        </div>\n\
        <div class="col-xs-2 form-group float-label-control">\n\
            <label>Audit Date</label>\n\
            <input type="text" class="audit-date" data-row-id="" name="audit-date" placeholder="Audit Date" value="" required>\n\
        </div>\n\
        <div class="col-xs-3 form-group float-label-control" style="margin-bottom:2px;">\n\
                <label>Add to</label>\n\
                <select class="add-to-service" id="add-to-service" name="add-to-service" data-row-id="" value="">\n\
                    <option value="" >Select add to</option>\n\<?php foreach ($addTODo as $to) { ?>\n\
                                                                        <option value="<?php echo $to['add_to'] ?>"><?php echo $to['add_to'] ?></option>\n\<?php } ?></select>\n\
        </div>\n\
        <div class="col-xs-2 form-group float-label-control" style="margin-bottom:2px;">\n\
                <label>Dealing Officer</label>\n\
                <select class="handel-by" id="handel-by" name="handel-by" data-row-id="" value="">\n\
                <option value="" >Select Dealing Officer</option>\n\<?php foreach ($handlerList as $handler) { ?>\n\
                                                    <option value="<?php echo $handler['handler_name'] ?>"><?php echo $handler['handler_name'] ?></option>\n\<?php } ?></select>\n\
                <!--<input type="text" class="add-to-service" data-row-id="<?php echo $serviceId; ?>" id="add-to-service" name="add-to-service" placeholder="Add To" value="<?php if (isset($service1['add_service_to'])) echo $service1['add_service_to'] ?>" required>-->\n\
            </div>\n\
        <div class="col-xs-3 form-group" style="">\n\
                <input type="checkbox" data-row-id="" class="issueCert" data-service="" data-customer="" value="">&nbsp;&nbsp; Certificate Given \n\
            </div>\n\
        <input type="hidden" class="customer_id" id="customer_id" data-row-id="" name="customer_id" value="<?php echo $_GET['customerid'] ?>"/></div>\n\
        <div class="row">\n\
            <div class="col-xs-12 table-responsive">\n\
                <table class="table table-bordered table-hover table-sortable service_details" id="service_details">\n\
                    <thead>\n\
                        <tr>\n\
                            <th class="" width="18%" style="padding:2px">\n\
                                <small>Contract Details</small>\n\
                            </th>\n\
                            <th class="" width="13%" style="padding:2px">\n\
                                <small>Amount Decided</small>\n\
                            </th>\n\
                            <th class="" width="13%" style="padding:2px">\n\
                                <small>Payment received</small>\n\
                            </th>\n\
                            <th class="" width="13%" style="padding:2px">\n\
                                <small>Date of Deposit</small>\n\
                            </th>\n\
                            <th class="" width="13%" style="padding:2px">\n\n\
                                <small>Instrument No</small>\n\
                            </th>\n\
                            <th class="" width="13%" style="padding:2px">\n\
                               <small>Bank A/c </small>\n\
                            </th>\n\
                            <th class="" width="13%" style="padding:2px">\n\
                                <small>Balance</small>\n\
                            </th>\n\
                            <th class="" width="4%" style="padding:2px">\n\
                                <i class="fa fa-comment-o" aria-hidden="true"></i>\n\
                            </th>\n\
                        </tr>\n\
                    </thead>\n\
    <tbody class = "" > \n\ <tr class = "cb_section" > \n\ <td colspan = 8 style="padding:0px;"> CB <a class = "btn btn-success btn-xs cbAddLink pull-right" data-row-id="" data-row-count="" > <i class = "fa fa-plus-circle fa-lg pull-right"  style="padding:3px"> </i></a > <?php if (Utitlity::checkForUserAdmin()) { ?><a class = "cbAddLinkForAdmin pull-right btn btn-success btn-xs " data-row-id="" data-row-count="" title="addCB" style="padding: 1px 5px;"> MS </a > <?php } ?> </td>\n\ </tr>\n\ <tr class = "cb_subsection previousCBData" id = "cb_subsection_0" > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input type = "text" data-row-id="" class = "contractDetails" id = "contractDetails" name = "contractDetails" value = "" placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input type = "number" data-row-id="" class = "amountDecided" id = "amountDecided" name = "amountDecided"  value = "" placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input type = "number" data-row-id="" class = "paymentReceived" id = "paymentReceived" name = "paymentReceived" value = "" placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "text" class = "depositDate"  name = "depositDate" value = "" placeholder = "" / > \n\ </div>\n\ </td > <td class = "" style="padding:0px;" > \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "text" class = "chequeDetails" id = "chequeDetails" name = "chequeDetails" value = "" placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "text" class = "bankAcccountNo"  id = "bankAcccountNo"  name = "bankAcccountNo" value = "" placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;" > \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "text" class = "balance" id = "balance" name = "balance" value = "" placeholder = ""  readonly = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;" > \n\  \n\ </td>\n\ </tr > \n\ <input data-row-id="" type = "hidden" class = "cb-row-id" id = "cb-row-id"  name = "cb-row-id" value = "" > \n\  <tr class = "consulting_section" > \n\ <td colspan = 8 style="padding:0px"> Consulting <a class = "consultingAddLink btn btn-warning btn-xs pull-right" data-row-count="" data-row-id=""> <i class = "fa fa-plus-circle fa-lg pull-right" style="padding:3px"> </i></a > </td>\n\ </tr > \n\ <tr class = "consulting_subsection previousConsultingData" > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "text"\n\
    class = "consultingContractDetails"\n\
    id = "consultingContractDetails"\n\
    name = "consultingContractDetails"\n\
    value = ""\n\
    placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "number"\n\
    class = "consultingAmountDecided"\n\
    id = "consultingAmountDecided"\n\
    name = "consultingAmountDecided" style="text-align: center"\n\
    value = ""\n\
    placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "number"\n\
    class = "consultingPaymentReceived"\n\
    id = "consultingPaymentReceived"\n\
    name = "consultingPaymentReceived"\n\
    value = ""\n\
    placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "text"\n\
    class = "consultingDepositDate"\n\
    id = "consultingDepositDate"\n\
    name = "consultingDepositDate"\n\
    value = ""\n\
    placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "text"\n\
    class = "consultingChequeDetails"\n\
    id = "consultingChequeDetails"\n\
    name = "consultingChequeDetails"\n\
    value = ""\n\
    placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "text"\n\
    class = "consultingBankAcccountNo"\n\
    id = "consultingBankAcccountNo"\n\
    name = "consultingBankAcccountNo"\n\
    value = ""\n\
    placeholder = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\ <div class = "table-edit-control" > \n\ <label > </label>\n\ <input data-row-id="" type = "text"\n\
    class = "consultingBalance"\n\
    id = "consultingBalance"\n\
    name = "consultingBalance"\n\
    value = ""\n\
    placeholder = ""\n\
    readonly = "" / > \n\ </div>\n\ </td > \n\ <td class = "" style="padding:0px;"> \n\  \n\ </td>\n\ </tr > \n\ <input data-row-id="" type = "hidden"\n\
    class = "consulting-row-id "\n\
    id = "consulting-row-id"\n\
    name = "consulting-row-id"\n\
value = "" >\n\
</tbody>\n\ </table > \n\ </div>\n\ </div > \n\ </div>\n\ </div > \n\ </div>\n\ </div > ';
            $(".servicebox-bunch").append($serviceStr);

        });

//        alert($rowCB);

        $topfixedRow = 2;

//        alert($rowConsult);

        $(document.body).on('click', '.consultingAddLink', function () {
            $rowId = $(this).data("row-id"); //rowId
            $rowCB = $('.cbAddLink[data-row-id="' + $rowId + '"]').data("row-count");
            if ($rowCB == "") {
                $rowCB = 1;
            }
//            alert("$rowCB" + $rowCB);
            $rowConsult = $(this).data("row-count");
            if ($rowConsult == "") {
                $rowConsult = 1;
            }
            $count = $rowCB + $topfixedRow + $rowConsult + 1;
//            alert($count);
            var table = document.getElementById("service_details" + $rowId);
            var row = table.insertRow($count);
            $(this).data("row-count", ($rowConsult + 1));
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);
            var cell9 = row.insertCell(7);

            cell1.style.padding = "0px";
            cell2.style.padding = "0px";
            cell3.style.padding = "0px";
            cell4.style.padding = "0px";
            cell5.style.padding = "0px";
            cell6.style.padding = "0px";
            cell7.style.padding = "0px";
            cell8.style.padding = "0px";
            cell9.style.padding = "0px";

            cell1.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="consultingContractDetails" id="consultingContractDetails" name="consultingContractDetails" data-row-id="' + $rowId + '" placeholder="" /></div>';
            cell2.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="consultingAmountDecided" id="consultingAmountDecided" name="consultingAmountDecided" placeholder="" /></div>';
            cell3.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="consultingPaymentReceived" id="consultingPaymentReceived" name="contractDetails" placeholder="" /></div>';
            cell4.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="consultingDepositDate"  name="consultingDepositDate" placeholder="" /></div>';
            cell5.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="consultingChequeDetails" id="consultingChequeDetails" name="consultingChequeDetails" placeholder="" /></div>';
            cell6.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="consultingBankAcccountNo" id="consultingBankAcccountNo" name="consultingBankAcccountNo" placeholder="" /></div>';
            cell7.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="consultingBalance" id="consultingBalance" name="consultingBalance" placeholder="" /></div>';
//            cell8.innerHTML = '<a><i class="fa fa-comment-o"  data-row-id="' + $rowId + '" aria-hidden="true"></i></a> <input type="hidden" class="consulting-row-id" id="consulting-row-id" data-row-id="' + $rowId + '" name="consulting-row-id" value="">';
            row.className = "consulting_subsection ";
            $tmpConsultCount = $tmpConsultCount + 1;

//            alert("out");

        });


        $(document.body).on('click', '.cbAddLink', function () {

//                    alert("in");
            $newCount = 0;
            $rowCB = $(this).data("row-count");
            if ($rowCB == "") {
                $rowCB = 1;
            }
//            alert("$rowCB" + $rowCB);
            $rowId = $(this).data("row-id");
//                    if($rowId==""){
//                        $newCount=1;
//                    }
//            alert("$rowId" + $rowId);
            $count = $rowCB + $topfixedRow;
//            $rowConsult = $rowConsult + 1;
//            alert("in");
            var table = document.getElementById("service_details" + $rowId);
            var row = table.insertRow($count);

            $(this).data("row-count", $rowCB + 1);
            $('.cbAddLinkForAdmin[data-row-id="' + $rowId + '"]').data("row-count", $rowCB + 1);

//                    alert($(this).data("row-count"));

            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);
            var cell9 = row.insertCell(8);

            cell1.style.padding = "0px";
            cell2.style.padding = "0px";
            cell3.style.padding = "0px";
            cell4.style.padding = "0px";
            cell5.style.padding = "0px";
            cell6.style.padding = "0px";
            cell7.style.padding = "0px";
            cell8.style.padding = "0px";
            cell9.style.padding = "0px";
            cell1.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="contractDetails" id="contractDetails" name="contractDetails" placeholder="" /></div>';
            cell2.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="amountDecided" id="amountDecided" name="amountDecided" placeholder="" /></div>';
            cell3.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="paymentReceived" id="paymentReceived" name="paymentReceived" placeholder="" /></div>';
            cell4.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="depositDate"  name="depositDate" placeholder="" /></div>';
            cell5.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="chequeDetails" id="chequeDetails" name="chequeDetails" placeholder="" /></div>';
            cell6.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="bankAcccountNo" id="bankAcccountNo" name="bankAcccountNo" placeholder="" /></div>';
            cell7.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="balance" id="balance" name="balance" placeholder="" /></div>';
//            cell8.innerHTML = '<a class="cbComment" data-row-id="' + $rowId + '"><i class="fa fa-comment-o" aria-hidden="true"></i></a><input type="hidden" class="cb-row-id" id="cb-row-id" name="cb-row-id" data-row-id="' + $rowId + '" value="">';
            row.className = "cb_subsection";
            $tmpCbCount = $tmpCbCount + 1;

//            alert("out");

        });

        $(document.body).on('click', '.cbAddLinkForAdmin', function () {

//                    alert("in");
            $newCount = 0;
            $rowCB = $(this).data("row-count");
            if ($rowCB == "") {
                $rowCB = 1;
            }
//            alert("$rowCB" + $rowCB);
            $rowId = $(this).data("row-id");
//                    if($rowId==""){
//                        $newCount=1;
//                    }
//            alert("$rowId" + $rowId);
            $count = $rowCB + $topfixedRow;
//            $rowConsult = $rowConsult + 1;
//            alert("in");

            var table = document.getElementById("service_details" + $rowId);
            var row = table.insertRow($count);
            $(this).data("row-count", $rowCB + 1);
            $('.cbAddLink[data-row-id="' + $rowId + '"]').data("row-count", $rowCB + 1);
//                    alert($(this).data("row-count"));

            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);
            var cell9 = row.insertCell(8);

            cell1.style.padding = "0px";
            cell2.style.padding = "0px";
            cell3.style.padding = "0px";
            cell4.style.padding = "0px";
            cell5.style.padding = "0px";
            cell6.style.padding = "0px";
            cell7.style.padding = "0px";
            cell8.style.padding = "0px";
            cell9.style.padding = "0px";

            cell1.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="contractDetails" id="contractDetails" name="contractDetails" placeholder="" value="Marketing Setup" /></div>';
            cell2.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="amountDecided" id="amountDecided" name="amountDecided" placeholder="" /></div>';
            cell3.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="paymentReceived" id="paymentReceived" name="paymentReceived" placeholder="" /></div>';
            cell4.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="depositDate"  name="depositDate" placeholder="" /></div>';
            cell5.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="chequeDetails" id="chequeDetails" name="chequeDetails" placeholder="" /></div>';
            cell6.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="bankAcccountNo" id="bankAcccountNo" name="bankAcccountNo" placeholder="" /></div>';
            cell7.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="balance" id="balance" name="balance" placeholder="" /></div>';
//            cell8.innerHTML = '<a class="cbComment" data-row-id="' + $rowId + '"><i class="fa fa-comment-o" aria-hidden="true"></i></a><input type="hidden" class="cb-row-id" id="cb-row-id" name="cb-row-id" data-row-id="' + $rowId + '" value="">';
            row.className = "cb_subsection";
            $tmpCbCount = $tmpCbCount + 1;
        });

    });

    $(function () {

        $('.certificationDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy',
            autoclose: true
        });

//        $("#certificationDate").keydown(function (e) {
//
//            e.preventDefault();
//
//        });

        $('.serviceDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy',
            autoclose: true
        });

//        $("#serviceDate").keydown(function (e) {
//            e.preventDefault();
//        });

        $(document).on('focus', ".audit-date", function () {
            if ($(this).hasClass('hasDatepicker') === false) {
                $(this).datepicker({
                    //            minDate: '-100Y',
                    //            maxDate: '-2Y',
                    //            changeYear: true,
                    //            changeMonth: true,
                    //            yearRange: "-100:+0",
                    minDate: '-100Y',
                    maxDate: '+100Y',
                    changeYear: true,
                    changeMonth: true,
                    yearRange: "-20:+100",
                    dateFormat: 'dd.mm.yy',
                    autoclose: true
                });
            }
        });

        $(document).on('focus', ".depositDate", function () {
            if ($(this).hasClass('hasDatepicker') === false) {
                $(this).datepicker({
                    minDate: '-100Y',
                    maxDate: '+100Y',
                    changeYear: true,
                    changeMonth: true,
                    yearRange: "-20:+100",
                    dateFormat: 'dd.mm.yy',
                    autoclose: true
                });
            }
        });

        $(document).on('focus', ".consultingDepositDate", function () {
            if ($(this).hasClass('hasDatepicker') === false) {
                $(this).datepicker({
                    minDate: '-100Y',
                    maxDate: '+100Y',
                    changeYear: true,
                    changeMonth: true,
                    yearRange: "-20:+100",
                    dateFormat: 'dd.mm.yy',
                    autoclose: true
                });
            }
        });

//        $("#consultingDepositDate").keydown(function (e) {
//            e.preventDefault();
//        });

    });
</script>

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