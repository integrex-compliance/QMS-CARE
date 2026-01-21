<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "Mktg setup status";
    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
//        $allAuditDone = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, "
//                . "contact_email, client_reference, s.service_id, service_type, marketing_audit_type, audit_type, "
//                . "comments, next_service_date, audit_date, cert_body, contract_details, amount_decided, add_service_to,"
//                . "payment_received, deposit_date, check_details, bank_account, s.ms_applicability "
//                . "FROM certification_body c right join services s on c.service_id=s.service_id right join "
//                . "customer_details cd on s.customer_id = cd.customer_id WHERE s.ms_applicability ='yes' and "
//                . "cd.is_delete<>'Y' and s.cert_body <> 'MGMT - CB'";
//        $allAuditDone = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, s.service_id, service_type, marketing_audit_type, audit_type, comments, next_service_date, audit_date, cert_body, contract_details, amount_decided, add_service_to, payment_received, deposit_date, check_details, bank_account, s.ms_applicability FROM certification_body c right join services s on c.service_id=s.service_id right join customer_details cd on s.customer_id = cd.customer_id WHERE s.ms_applicability ='yes' and cd.is_delete<>'Y' and s.cert_body <> 'MGMT - CB' and payment_received = ''";
        $allAuditDone = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, s.service_id, service_type, marketing_audit_type, audit_type, comments, next_service_date, audit_date, cert_body, contract_details, amount_decided, add_service_to, payment_received, deposit_date, check_details, bank_account, s.ms_applicability FROM certification_body c right join services s on c.service_id=s.service_id right join customer_details cd on s.customer_id = cd.customer_id WHERE s.ms_applicability ='yes' and cd.is_delete<>'Y' and s.cert_body <> 'MGMT - CB' and add_service_to <> 'Under Marketing' and payment_received = ''";
        $statement = $db->prepare($allAuditDone);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();

        $allMarketingAuditDone = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, s.service_id, service_type, marketing_audit_type, audit_type, comments, next_service_date, audit_date, cert_body, contract_details, amount_decided, add_service_to, payment_received, deposit_date, check_details, bank_account, s.ms_applicability FROM certification_body c right join services s on c.service_id=s.service_id right join customer_details cd on s.customer_id = cd.customer_id WHERE s.ms_applicability ='yes' and cd.is_delete<>'Y' and s.cert_body <> 'MGMT - CB' and add_service_to = 'Under Marketing' and payment_received = ''";
        $statement = $db->prepare($allMarketingAuditDone);
        $statement->execute();
        $listOfMarketingCustomers = $statement->fetchAll();

        $allserv = "select distinct service_id from services";
        $statement = $db->prepare($allserv);
        $statement->execute();
        $allserid = $statement->fetchAll();
    }
    ?>
    <!--NOT s.cert_body ='MGMT' and--> 
    <!-- Page content -->
    <div id="page-content-wrapper">

        <!-- Keep all page content within the page-content inset div! -->
        <div class="page-content inset">
            <div class="row">
                <div class="col-sm-12">

                    <div class="container" style="margin-top: 20px;">
                        <div class="row">
                            <div class="col-sm-12 main_content" style="">
                                <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">
                                    <div class="col-sm-1"><a class="btn btn-info filterBtn"><i class="fa fa-filter "></i></a></div>                            
                                    <div class="col-sm-3 float-label-control custom-float-label-control">
                                        <select name="cert-body" class="form-control cert-body" id="cert-body">
                                            <option value="">Select cert body</option>
                                            <?php foreach ($certList as $certb) { ?>
                                                <option value="<?php echo $certb['cert'] ?>">
                                                    <?php echo $certb['cert'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 float-label-control custom-float-label-control">
                                        <input type="text" class="filterFromDate" id="filterFromDate" name="filterFromDate" placeholder="Audit Date From" value="">
                                    </div>
                                    <div class="col-sm-3 form-group float-label-control custom-float-label-control">
                                        <input type="text" class="filterToDate" id="filterToDate" name="filterToDate" placeholder="Audit Date To" value="">
                                    </div>
                                </div>


                                <div class="row clearfix">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered table-striped table-hover table-sortable" id="table_finance_details">
                                            <thead>
                                                <tr>
                                                    <th class="">
                                                        <small><a>Client Name</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Cert Body</a></small>
                                                    </th>
                                                    <th class="" width="10%">
                                                        <small><a>MS NA</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Audit Done For</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Audit Date</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Status</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Mktg Amount</a></small>
                                                    </th>
<!--                                                    <th class="">
                                                        <small><a>Mktg Amount Received</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Date</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Cheque Details</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Bank Account</a></small>
                                                    </th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $serviceNo = 1;
                                                $loc = array();
                                                $i = 0;
                                                $flag = array_fill(0, count($allserid), 0);
                                                foreach ($listOfCustomers as $customer) {

                                                    if (strpos($customer['contract_details'], "Marketing") != false or strpos($customer['contract_details'], "Setup") != false) {
                                                        $loc[$i] = $customer;
                                                        for ($j = 0; $j < count($allserid); $j++) {
                                                            if ($loc[$i]['service_id'] == $allserid[$j]['service_id']) {
                                                                $flag[$j] = 1;
                                                            }
                                                        }
                                                    }
                                                    $i++;
                                                }

                                                for ($j = 0; $j < count($allserid); $j++) {
                                                    foreach ($listOfCustomers as $customer) {
                                                        if ($customer['service_id'] == $allserid[$j]['service_id']) {
                                                            if ($flag[$j] == 0) {

                                                                $loc[$i] = $customer;
                                                                $loc[$i]['amount_decided'] = "";
                                                                $loc[$i]['payment_received'] = "";
                                                                $loc[$i]['check_details'] = "";
                                                                $loc[$i]['bank_account'] = "";
                                                                $loc[$i]['deposit_date'] = "";
                                                            }

                                                            $i++;
                                                        }
                                                    }
                                                }

                                                foreach ($loc as $customer) {
                                                    if (stripos($customer['company_name'], "consl. ") === FALSE) {
                                                        $service = "SELECT (select max(service_id) FROM services WHERE customer_id = :customerid) as maxid";
                                                        $statement = $db->prepare($service);
                                                        $statement->bindParam(':customerid', $customer['customer_id']);
                                                        $statement->execute();
                                                        $services = $statement->fetchAll();
                                                        $totalServiceCount = count($services);
                                                        if ($customer['cert_body'] != "MGMT" && ($customer['service_type'] == "CA" || $customer['service_type'] == "SA-1" || $customer['service_type'] == "SA-2" || $customer['service_type'] == "RC")) {
                                                            ?>
                                                            <tr id='addr0' data-id="<?php echo $customer['customer_id'] ?>" class="">

                                                                <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['cert_body'] ?></small>
                                                                </td>
                                                                <?php
                                                                if (isset($customer['ms_applicability']) && $customer['ms_applicability'] == 'yes' && ($serviceNo == $totalServiceCount)) {

                                                                    $chkVal = "";
                                                                    $disable = "";
                                                                } else {
                                                                    $chkVal = "";
                                                                    $disable = "";
                                                                }
                                                                ?>
                                                                <td data-name="" style="border-top: 1px solid #ffffff; color:red; border-right: 1px solid #ffffff; text-align: center">
                                                                    <?php if (($customer['service_type'] == "CA" || $customer['service_type'] == "SA-1" || $customer['service_type'] == "SA-2" || $customer['service_type'] == "RC")) { ?>
                                                                        <input type="checkbox" class="msapp" data-service="<?php echo $customer['service_id'] ?>" data-customer="<?php echo $customer['customer_id'] ?>" <?php echo $chkVal ?> <?php echo $disable ?>>
                                                                    <?php } ?>
                                                                </td>
                                                                <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['service_type'] ?></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><span><?php echo getFlatDate($customer['audit_date']); ?>~</span><?php echo simpleDateConversion($customer['audit_date']); ?></small>
                                                                </td>
                                                                <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['add_service_to'] ?></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['amount_decided'] ?></small>
                                                                </td>
            <!--                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['payment_received'] ?></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo simpleDateConversion($customer['deposit_date']) ?></small>
                                                                </td>
                                                                <?php
                                                                $customer['check_details'] = wordwrap($customer['check_details'], 8, ' ', true);
                                                                ?>

                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['check_details'] ?></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['bank_account'] ?></small>
                                                                </td>-->
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <!--                                <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; margin-top: 20px;">
                                                                    <div class="col-sm-1"><a class="btn btn-info filterBtn"><i class="fa fa-filter "></i></a></div>                            
                                                                    <div class="col-sm-3 float-label-control custom-float-label-control">
                                                                        <select name="cert-body-marketing" class="form-control cert-body-marketing" id="cert-body-marketing">
                                                                            <option value="">Select cert body</option>
                                <?php foreach ($certList as $certb) { ?>
                                                                                            <option value="<?php echo $certb['cert'] ?>">
                                    <?php echo $certb['cert'] ?>
                                                                                            </option>
                                <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-sm-3 float-label-control custom-float-label-control">
                                                                        <input type="text" class="filterFromDateMarketing" id="filterFromDateMarketing" name="filterFromDateMarketing" placeholder="Audit Date From" value="">
                                                                    </div>
                                                                    <div class="col-sm-3 form-group float-label-control custom-float-label-control">
                                                                        <input type="text" class="filterToDateMarketing" id="filterToDateMarketing" name="filterToDateMarketing" placeholder="Audit Date To" value="">
                                                                    </div>
                                                                </div>-->

                                <div class="row" style="margin-top: 20px;">
                    <div class="col-sm-12">
                        <h5>Under Marketing</h5>
                    </div>
                </div>
                                <div class="row clearfix" style="">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered table-striped table-hover table-sortable" id="table_marketing_finance_details">
                                            <thead>
                                                <tr>
                                                    <th class="">
                                                        <small><a>Client Name</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Cert Body</a></small>
                                                    </th>
                                                    <th class="" width="10%">
                                                        <small><a>MS NA</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Audit Done For</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Audit Date</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Status</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Mktg Amount</a></small>
                                                    </th>
<!--                                                    <th class="">
                                                        <small><a>Mktg Amount Received</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Date</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Cheque Details</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Bank Account</a></small>
                                                    </th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $serviceNo = 1;
                                                $loc = array();
                                                $i = 0;
                                                $flag = array_fill(0, count($allserid), 0);
                                                foreach ($listOfMarketingCustomers as $customer) {

                                                    if (strpos($customer['contract_details'], "Marketing") != false or strpos($customer['contract_details'], "Setup") != false) {
                                                        $loc[$i] = $customer;
                                                        for ($j = 0; $j < count($allserid); $j++) {
                                                            if ($loc[$i]['service_id'] == $allserid[$j]['service_id']) {
                                                                $flag[$j] = 1;
                                                            }
                                                        }
                                                    }
                                                    $i++;
                                                }

                                                for ($j = 0; $j < count($allserid); $j++) {
                                                    foreach ($listOfMarketingCustomers as $customer) {
                                                        if ($customer['service_id'] == $allserid[$j]['service_id']) {
                                                            if ($flag[$j] == 0) {

                                                                $loc[$i] = $customer;
                                                                $loc[$i]['amount_decided'] = "";
                                                                $loc[$i]['payment_received'] = "";
                                                                $loc[$i]['check_details'] = "";
                                                                $loc[$i]['bank_account'] = "";
                                                                $loc[$i]['deposit_date'] = "";
                                                            }

                                                            $i++;
                                                        }
                                                    }
                                                }

                                                foreach ($loc as $customer) {
                                                    if (stripos($customer['company_name'], "consl. ") === FALSE) {
                                                        $service = "SELECT (select max(service_id) FROM services WHERE customer_id = :customerid) as maxid";
                                                        $statement = $db->prepare($service);
                                                        $statement->bindParam(':customerid', $customer['customer_id']);
                                                        $statement->execute();
                                                        $services = $statement->fetchAll();
                                                        $totalServiceCount = count($services);
                                                        if ($customer['cert_body'] != "MGMT" && ($customer['service_type'] == "CA" || $customer['service_type'] == "SA-1" || $customer['service_type'] == "SA-2" || $customer['service_type'] == "RC")) {
                                                            ?>
                                                            <tr id='addr0' data-id="<?php echo $customer['customer_id'] ?>" class="">

                                                                <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['cert_body'] ?></small>
                                                                </td>
                                                                <?php
                                                                if (isset($customer['ms_applicability']) && $customer['ms_applicability'] == 'yes' && ($serviceNo == $totalServiceCount)) {

                                                                    $chkVal = "";
                                                                    $disable = "";
                                                                } else {
                                                                    $chkVal = "";
                                                                    $disable = "";
                                                                }
                                                                ?>
                                                                <td data-name="" style="border-top: 1px solid #ffffff; color:red; border-right: 1px solid #ffffff; text-align: center">
                                                                    <?php if (($customer['service_type'] == "CA" || $customer['service_type'] == "SA-1" || $customer['service_type'] == "SA-2" || $customer['service_type'] == "RC")) { ?>
                                                                        <input type="checkbox" class="msapp" data-service="<?php echo $customer['service_id'] ?>" data-customer="<?php echo $customer['customer_id'] ?>" <?php echo $chkVal ?> <?php echo $disable ?>>
                                                                    <?php } ?>
                                                                </td>
                                                                <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['service_type'] ?></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><span><?php echo getFlatDate($customer['audit_date']); ?>~</span><?php echo simpleDateConversion($customer['audit_date']); ?></small>
                                                                </td>
                                                                <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['add_service_to'] ?></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['amount_decided'] ?></small>
                                                                </td>
            <!--                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['payment_received'] ?></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo simpleDateConversion($customer['deposit_date']) ?></small>
                                                                </td>
                                                                <?php
                                                                $customer['check_details'] = wordwrap($customer['check_details'], 8, ' ', true);
                                                                ?>

                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['check_details'] ?></small>
                                                                </td>
                                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                    <small><?php echo $customer['bank_account'] ?></small>
                                                                </td>-->
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">

    var table = $('#table_marketing_finance_details').DataTable({
        "autoWidth": false,
//            "scrollY": "450px",
        "sScrollX": "100%",
        "scrollY": '70vh',
        "scrollCollapse": true,
        "paging": false,
        "columnDefs": [
            {
                "width": "20%",
                "targets": 0
            },
            {
                "width": "10%",
                "targets": 1
            },
            {
                "width": "10%",
                "targets": 2
            },
            {
                "width": "10%",
                "targets": 3
            },
            {
                "width": "10%",
                "targets": 4
            },
            {
                "width": "10%",
                "targets": 5
            },
            {
                "width": "10%",
                "targets": 6
            },
//            {
//                "width": "50px",
//                "targets": 7
//            },
//            {
//                "width": "50px",
//                "targets": 8
//            }
        ]
                //        "paging":         false
    });

    var table = $('#table_finance_details').DataTable({
        "autoWidth": false,
//            "scrollY": "450px",
        "sScrollX": "100%",
        "scrollY": '70vh',
        "scrollCollapse": true,
        "paging": false,
        "columnDefs": [
            {
                "width": "20%",
                "targets": 0
            },
            {
                "width": "10%",
                "targets": 1
            },
            {
                "width": "10%",
                "targets": 2
            },
            {
                "width": "10%",
                "targets": 3
            },
            {
                "width": "10%",
                "targets": 4
            },
            {
                "width": "10%",
                "targets": 5
            },
            {
                "width": "10%",
                "targets": 6
            },
//            {
//                "width": "50px",
//                "targets": 7
//            },
//            {
//                "width": "50px",
//                "targets": 8
//            }
        ]
                //        "paging":         false
    });

    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
        table.draw();
    });

    $(document).ready(function () {
        $('.msapp').change(function () {
            var service = $(this).attr('data-service');
            var customer = $(this).attr('data-customer');
            var t = "true";
            var f = "false";
            var temp = "app";
            if ($(this).is(":checked")) {
                $.ajax({
                    url: "msApplicability.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        ischecked: t,
                        customer: customer,
                        service: service,
                        file: temp,
                    },
                    success: function (data)
                    {
                    },
                    error: function (data, status, err) {

                    }
                });
            } else {
                $.ajax({
                    url: "msApplicability.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        ischecked: f,
                        customerID: customer,
                        serviceID: service,
                        file: temp,
                    },
                    success: function (data)
                    {
                    },
                    error: function (data, status, err) {

                    }
                });
            }
        });

        $('.customerAccount').click(function () {
            var customerID = $(this).attr('data-id');
            var url = 'customerAccountDetails.php'
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });

        //        $("#menu-toggle").click(function (e) {
        //            e.preventDefault();
        //            $("#wrapper").toggleClass("active");
        //        });

        $('.newCustomer').click(function () {
            //            alert('hi');
            $(".addNewCustomer").show();
            $('.newCustomer').hide();
        });

        $('.cancelAdding').click(function () {
            $('.newCustomer').show();
            $(".addNewCustomer").hide();
        });

        $('.customerAccount').click(function () {
            var customerID = $(this).attr('data-id');
            var url = 'customerAccountDetails.php'
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });

        table.columns.adjust().draw();

    });
    // <script type="text/javascript">

    function compareMinDates(filterDate, rowDate) {
        var filterDateSplit = filterDate.split(".");
        var rowDateSplit = rowDate.split(".");
        if (filterDateSplit[2] < rowDateSplit[2]) {
            return true;
        } else if (filterDateSplit[2] == rowDateSplit[2]) {
            if (filterDateSplit[1] < rowDateSplit[1]) {
                return true;
            } else if (filterDateSplit[1] == rowDateSplit[1]) {
                if (filterDateSplit[0] < rowDateSplit[0]) {
                    return true;
                } else if (filterDateSplit[0] == rowDateSplit[0]) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }
    function compareMaxDates(filterDate, rowDate) {
        var filterDateSplit = filterDate.split(".");
        var rowDateSplit = rowDate.split(".");
        //year comparison
        if (filterDateSplit[2] > rowDateSplit[2]) {
            return true;
        } else if (filterDateSplit[2] == rowDateSplit[2]) {
            if (filterDateSplit[1] > rowDateSplit[1]) {
                return true;
            } else if (filterDateSplit[1] == rowDateSplit[1]) {
                if (filterDateSplit[0] > rowDateSplit[0]) {
                    return true;
                } else if (filterDateSplit[0] == rowDateSplit[0]) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    //for filtering

//$.fn.dataTable.ext.search.push(
//            function (settings, data, dataIndex) {
//                console.log(settings.nTable.id);
//                if (settings.nTable.id !== 'table_marketing_finance_details') {
//                    return true;
//                }
//                alert("not in marketing");
//                var cb = $(".cert-body-marketing").val();
//                var fromDate = $(".filterFromDateMarketing").val();
//                var toDate = $(".filterToDateMarketing").val();
//                var isServiceType = false;
//                var isfromDate = false;
//                var isToDate = false;
//                //            alert(cb);
//                if (cb == "" && fromDate == "" && toDate == "") {
//                    return true;
//                }
//                if (cb != "") {
//                    var cbstr = data[1] || ""; // use data for the age column
//                    //            alert(cbstr );
//                    if (cbstr.toUpperCase().trim() == cb.toUpperCase().trim()) {
//                        //                alert("test");
//                        isServiceType = true;
//                    }
//                } else {
//                    isServiceType = true;
//                }
//                var dateValue = data[4] || "";
//                var dateSplit = dateValue.split("~");
//                //            alert(dateSplit[1]);
//                //            alert(fromDate);
//                if (fromDate != "") {
////                      alert(dateValue);
//                    isfromDate = compareMinDates(fromDate, dateSplit[1]);
//                } else {
//                    isfromDate = true;
//                }
//                if (toDate != "") {
//                    isToDate = compareMaxDates(toDate, dateSplit[1]);
//                } else {
//                    isToDate = true;
//                }
//                finalResult = (isServiceType && isfromDate && isToDate);
//                return finalResult;
//            }
//    );


    $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                console.log(settings.nTable.id);
                if (settings.nTable.id !== 'table_finance_details') {
//                     alert("in marketing");
                    return true;
                }
                var cb = $(".cert-body").val();
                var fromDate = $(".filterFromDate").val();
                var toDate = $(".filterToDate").val();
                var isServiceType = false;
                var isfromDate = false;
                var isToDate = false;
                //            alert(cb);
                if (cb == "" && fromDate == "" && toDate == "") {
                    return true;
                }
                if (cb != "") {
                    var cbstr = data[1] || ""; // use data for the age column
                    //            alert(cbstr );
                    if (cbstr.toUpperCase().trim() == cb.toUpperCase().trim()) {
                        //                alert("test");
                        isServiceType = true;
                    }
                } else {
                    isServiceType = true;
                }
                var dateValue = data[4] || "";
                var dateSplit = dateValue.split("~");
                //            alert(dateSplit[1]);
                //            alert(fromDate);
                if (fromDate != "") {
//                      alert(dateValue);
                    isfromDate = compareMinDates(fromDate, dateSplit[1]);
                } else {
                    isfromDate = true;
                }
                if (toDate != "") {
                    isToDate = compareMaxDates(toDate, dateSplit[1]);
                } else {
                    isToDate = true;
                }
                finalResult = (isServiceType && isfromDate && isToDate);
                return finalResult;
            }
    );



//    $.fn.dataTable.ext.search.push(
//            function (settings, data, dataIndex) {
//                var serviceType = $(".service-type").val();
//                var fromDate = $(".year").val();
//                var isServiceType = false;
//                var isfromDate = false;
//                var isToDate = false;
//                //            alert(serviceType);
//                if (serviceType == "" && fromDate == "" ) {
//                    return true;
//                }
//                if (serviceType != "") {
//                    var servieTypeStr = data[7] || ""; // use data for the age column
//                    //            alert(servieTypeStr);
//                    if (servieTypeStr.toUpperCase().trim() == serviceType.toUpperCase().trim()) {
//                        //                alert("test");
//                        isServiceType = true;
//                    }
//                } else {
//                    isServiceType = true;
//                }
//                var dateValue = data[6] || "";
//                var dateSplit = dateValue.split("~");
//                //            alert(dateSplit[1]);
//                //            alert(fromDate);
//                if (fromDate != "") {
////                      alert(dateValue);
//                    isfromDate = compareMinDates(fromDate, dateSplit[1]);
//                } else {
//                    isfromDate = true;
//                }                
//                finalResult = (isServiceType && isfromDate && isToDate);
//                return finalResult;
//            }
//    );

    var yetVisited = localStorage['visited'];
//    alert(yetVisited);
    window.location.hash = '#addr' + yetVisited;
    $(document).ready(function () {
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");

            $(".container").toggleClass("active");

//                 $('.dataTable').resize();

//                alert("after");

            table.draw();
        });

//        window.onhashchange = locationHashChanged;
//        if ("onhashchange" in window) {
//            alert("The browser supports the hashchange event!");
//        }
//        function locationHashChanged() {
//            if (location.hash === "#addr50") {
//                somecoolfeature();
//            }
//        }

        $('.customerAccount').click(function () {
            var customerID = $(this).attr('data-id');
//            alert(customerID);
            // open popup
            localStorage['visited'] = customerID;
            var url = 'customerAccountDetails.php'
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });


        /*
         
         //        "paging":         false
         });*/
//        alert("before load");

        //  table.columns.adjust().draw();

//alert("after load");

//        $('.cert-body').change(function () {
//            // var fromDate = $(".filterFromDate").val();
//            var serviceType = $(".cert-body").val();
//
//            // alert("t");
//            table.draw();
//        });

        $('.cert-body-marketing, .filterFromDateMarketing, .filterToDateMarketing').change(function () {
            var serviceType = $(".cert-body-marketing").val();
            var fromDate = $(".filterFromDateMarketing").val();
            var toDate = $(".filterToDateMarketing").val();
            if (fromDate != "" && toDate != "") {
                if (!compareMinDates(fromDate, toDate)) {
                    alert("To Date can't be after From Date");
                    $(".filterToDate").val("");
                }
            }
            //            alert("t");
            table.draw();
        });

        $('.cert-body, .filterFromDate, .filterToDate').change(function () {
            var serviceType = $(".cert-body").val();
            var fromDate = $(".filterFromDate").val();
            var toDate = $(".filterToDate").val();
            if (fromDate != "" && toDate != "") {
                if (!compareMinDates(fromDate, toDate)) {
                    alert("To Date can't be after From Date");
                    $(".filterToDate").val("");
                }
            }
            //            alert("t");
            table.draw();
        });


//                $("#menu-toggle").click(function (e) {
//                    e.preventDefault();
//                    $("#wrapper").toggleClass("active");
//                    table.draw();
//                });

        $('.newCustomer').click(function () {
            //            alert('hi');
            $(".addNewCustomer").show();
            $('.newCustomer').hide();
        });

        $('.cancelAdding').click(function () {
            $('.newCustomer').show();
            $(".addNewCustomer").hide();
        });

        $('#filterFromDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $("#filterFromDate").keydown(function (e) {
            //            e.preventDefault();
        });

        $('#filterToDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $("#filterToDate").keydown(function (e) {
            //            e.preventDefault();
        });

        $('#filterFromDateMarketing').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $("#filterFromDateMarketing").keydown(function (e) {
            //            e.preventDefault();
        });

        $('#filterToDateMarketing').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $("#filterToDateMarketing").keydown(function (e) {
            //            e.preventDefault();
        });
    });

</script>



