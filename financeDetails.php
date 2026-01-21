<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "Finance Page";

    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {

//        $allCustomers = "select * from (select s.customer_id, company_name, contact_no, service_type, add_service_to, "
//                . "contract_details, balance, cert_comment comment, certification_id rowid, 'CB' as consultid from "
//                . "certification_body c join services s on c.service_id=s.service_id join customer_details cd on "
//                . "s.customer_id = cd.customer_id where balance > 0 and contract_details not like 'Marketing Setup%' "
//                . "and contract_details not like 'Mark%' and contract_details not like 'Marketing%' and contract_details "
//                . "not like 'Setup%' and contract_details not like 'Marke%' and contract_details not like 'tds%' and "
//                . "contract_details not like 'TDS%' and add_service_to not like '%loss%' and s.is_delete<>'Y' union "
//                . "select s.customer_id, company_name, contact_no, service_type, add_service_to, consulting_fees as "
//                . "contract_details, consulting_balance as balance, consulting_comment comment, consulting_id rowid, "
//                . "'Consulting' as consultid from consulting c join services s on c.service_id=s.service_id join "
//                . "customer_details cd on s.customer_id = cd.customer_id where consulting_balance >0 and consulting_fees "
//                . "not like 'TDS%' and s.add_service_to not like '%loss%' and s.is_delete<>'Y') as t order by "
//                . "FIELD(add_service_to, 'Under Payment','Under Working','Under Marketing','Completed','Ideal',''),company_name";
        $allCustomers = "select * from (select s.customer_id, company_name, contact_no, service_type, add_service_to, contract_details, balance, cert_comment comment, certification_id rowid, 'CB' as consultid from certification_body c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance > 0 and contract_details not like 'Marketing Setup%' and contract_details not like 'Mark%' and contract_details not like 'Marketing%' and contract_details not like 'Setup%' and contract_details not like 'Marke%' and contract_details not like 'tds%' and contract_details not like 'TDS%' and add_service_to not like '%loss%' and (s.add_service_to != 'Under Marketing' and s.add_service_to != 'Under Working' and s.add_service_to != 'Idle' and s.add_service_to != 'Discontinue' and s.add_service_to !='Terminate') and s.is_delete<>'Y' union select s.customer_id, company_name, contact_no, service_type, add_service_to, consulting_fees as contract_details, consulting_balance as balance, consulting_comment comment, consulting_id rowid, 'Consulting' as consultid from consulting c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where consulting_balance >0 and consulting_fees not like 'TDS%' and s.add_service_to not like '%loss%' and (s.add_service_to != 'Under Marketing' and s.add_service_to != 'Under Working' and s.add_service_to != 'Idle' and s.add_service_to != 'Discontinue' and s.add_service_to !='Terminate') and s.is_delete<>'Y') as t order by FIELD(add_service_to, 'Under Payment',''),company_name";
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();

        $workingCustomers = "select * from (select s.customer_id, company_name, contact_no, service_type, add_service_to, contract_details, balance, cert_comment comment, certification_id rowid, 'CB' as consultid from certification_body c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance > 0 and contract_details not like 'Marketing Setup%' and contract_details not like 'Mark%' and contract_details not like 'Marketing%' and contract_details not like 'Setup%' and contract_details not like 'Marke%' and contract_details not like 'tds%' and contract_details not like 'TDS%' and add_service_to not like '%loss%' AND add_service_to = 'Under Working' and s.is_delete<>'Y' union select s.customer_id, company_name, contact_no, service_type, add_service_to, consulting_fees as contract_details, consulting_balance as balance, consulting_comment comment, consulting_id rowid, 'Consulting' as consultid from consulting c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where consulting_balance >0 and consulting_fees not like 'TDS%' and s.add_service_to not like '%loss%' AND add_service_to = 'Under Working' and s.is_delete<>'Y') as t order by FIELD(add_service_to, 'Under Payment',''),company_name";
        $statement = $db->prepare($workingCustomers);
        $statement->execute();
        $listOfWorkingCustomers = $statement->fetchAll();
        //select  * from consulting c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where consulting_balance >0
        //select  * from certification_body c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance >0

        $idleCustomers = "select * from (select s.customer_id, company_name, contact_no, service_type, add_service_to, contract_details, balance, cert_comment comment, certification_id rowid, 'CB' as consultid from certification_body c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance > 0 and contract_details not like 'Marketing Setup%' and contract_details not like 'Mark%' and contract_details not like 'Marketing%' and contract_details not like 'Setup%' and contract_details not like 'Marke%' and contract_details not like 'tds%' and contract_details not like 'TDS%' and add_service_to not like '%loss%' AND (add_service_to = 'Idle' OR add_service_to = 'Discontinue' OR add_service_to = 'Terminate') and s.is_delete<>'Y' union select s.customer_id, company_name, contact_no, service_type, add_service_to, consulting_fees as contract_details, consulting_balance as balance, consulting_comment comment, consulting_id rowid, 'Consulting' as consultid from consulting c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where consulting_balance >0 and consulting_fees not like 'TDS%' and s.add_service_to not like '%loss%' AND (add_service_to = 'Idle' OR add_service_to = 'Discontinue' OR add_service_to = 'Terminate') and s.is_delete<>'Y') as t order by FIELD(add_service_to, 'Under Payment',''),company_name";
        $statement = $db->prepare($idleCustomers);
        $statement->execute();
        $listOfIdleCustomers = $statement->fetchAll();
    }
    ?>
    <style>
        .custom-float-label-control {
            margin-bottom: 0em;
        }

        /*        hr.style2 {
                    border-top: 3px double #8c8b8b;
                }*/

    </style>
    <!-- Page content -->
    <div id="page-content-wrapper">
        <!-- Keep all page content within the page-content inset div! -->
        <div class="page-content inset">
            <div class="container">
                <div class="row" style="margin-top: 20px;">
                    <div class="col-sm-12">
                        <h5>Pay All</h5>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sortable" id="table_finance_details" width="100%">
                            <thead>
                                <tr>
                                    <th class="">
                                        <small><a>Sr.</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Client Name</a></small>
                                    </th>
                                    <th class="" >
                                        <small><a>Phone No.</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Service</a></small>
                                    </th>
                                    <th class="" >
                                        <small><a>Project Status</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Contract Details</a></small>
                                    </th>                                                    
                                    <th class="" >
                                        <small><a>Amount Pending</a></small>
                                    </th> 
                                    <th class="" >
                                        <small><a>Type</a></small>
                                    </th> 
                                    <th class="" >
                                        <small><a>Comments</a></small>
                                    </th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="7" style="text-align:right">Total:</th>
                                    <th colspan="2"></th>                                                    
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($listOfCustomers as $customer) {
                                    $count = $count + 1;
                                    ?>
                                    <tr id='addr0'>
                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $customer['customer_id'] ?>"><?php echo $count ?></a></small>
                                        </td>
                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_name'] ?></a></small>
                                        </td>
                                        <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['contact_no'] ?></small>
                                        </td>
                                        <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['service_type'] ?></small>
                                        </td>
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small>
                                                <?php
                                                $temp = "";
                                                $t = $customer['add_service_to'];
                                                $l = strlen($t);
                                                for ($i = 0; $i < $l; $i++) {
                                                    if ($t[$i] == "-" or $t[$i] == '+' or $t[$i] === ' ') {
                                                        $temp .= $t[$i] . "\n";
                                                    } else
                                                        $temp .= $t[$i];
                                                }
                                                echo $temp;
                                                ?></small>
                                        </td>
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['contract_details'] ?></small>
                                        </td>                                                        
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; color:red; border-right: 1px solid #ffffff; text-align: center">
                                            <?php echo $customer['balance'] ?>
                                        </td>
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php
                                                if ($customer['consultid'] == "Consulting")
                                                    echo "Consl";
                                                else
                                                    echo $customer['consultid']
                                                    ?>
                                            </small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <div class="table-edit-control">                                                                
                                                <textarea data-row-id="" rows="3" class="autosavecomments payComments" data-row="<?php echo $customer['rowid'] ?>" pay-type="<?php echo $customer['consultid'] ?>" contract="<?php echo $customer['contract_details'] ?>" name="payComments" value="" placeholder="" ><?php if (isset($customer['comment'])) echo $customer['comment'] ?></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-sm-12">
                        <h5>Working</h5>
                        <!--<hr class="style2">-->
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sortable" id="table_working_finance_details" width="100%">
                            <thead>
                                <tr>
                                    <th class="">
                                        <small><a>Sr.</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Client Name</a></small>
                                    </th>
                                    <th class="" >
                                        <small><a>Phone No.</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Service</a></small>
                                    </th>
                                    <th class="" >
                                        <small><a>Project Status</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Contract Details</a></small>
                                    </th>                                                    
                                    <th class="" >
                                        <small><a>Amount Pending</a></small>
                                    </th> 
                                    <th class="" >
                                        <small><a>Type</a></small>
                                    </th> 
                                    <th class="" >
                                        <small><a>Comments</a></small>
                                    </th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="7" style="text-align:right">Total:</th>
                                    <th colspan="2"></th>                                                    
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($listOfWorkingCustomers as $workingCustomer) {
                                    $count = $count + 1;
                                    ?>
                                    <tr id='addr0' >
                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $workingCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $workingCustomer['customer_id'] ?>"><?php echo $count ?></a></small>
                                        </td>
                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $workingCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $workingCustomer['customer_id'] ?>"><?php echo $workingCustomer['company_name'] ?></a></small>
                                        </td>
                                        <td data-name="desc" class="customerAccount" data-id="<?php echo $workingCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $workingCustomer['contact_no'] ?></small>
                                        </td>
                                        <td data-name="desc" class="customerAccount" data-id="<?php echo $workingCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $workingCustomer['service_type'] ?></small>
                                        </td>
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $workingCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small>
                                                <?php
                                                $temp = "";
                                                $t = $workingCustomer['add_service_to'];
                                                $l = strlen($t);
                                                for ($i = 0; $i < $l; $i++) {
                                                    if ($t[$i] == "-" or $t[$i] == '+' or $t[$i] === ' ') {
                                                        $temp .= $t[$i] . "\n";
                                                    } else
                                                        $temp .= $t[$i];
                                                }
                                                echo $temp;
                                                ?></small>
                                        </td>
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $workingCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $workingCustomer['contract_details'] ?></small>
                                        </td>                                                        
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $workingCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; color:red; border-right: 1px solid #ffffff; text-align: center">
                                            <?php echo $workingCustomer['balance'] ?>
                                        </td>
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $workingCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php
                                                if ($workingCustomer['consultid'] == "Consulting")
                                                    echo "Consl";
                                                else
                                                    echo $workingCustomer['consultid']
                                                    ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <div class="table-edit-control">                                                                
                                                <textarea data-row-id="" rows="3" class="autosavecomments payComments" data-row="<?php echo $workingCustomer['rowid'] ?>" pay-type="<?php echo $workingCustomer['consultid'] ?>" contract="<?php echo $workingCustomer['contract_details'] ?>" name="payComments" value="" placeholder="" ><?php if (isset($workingCustomer['comment'])) echo $workingCustomer['comment'] ?></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="col-sm-12">
                        <h5>Idle</h5>
                        <!--<hr class="style2">-->
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sortable" id="table_idle_finance_details" width="100%">
                            <thead>
                                <tr>
                                    <th class="">
                                        <small><a>Sr.</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Client Name</a></small>
                                    </th>
                                    <th class="" >
                                        <small><a>Phone No.</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Service</a></small>
                                    </th>
                                    <th class="" >
                                        <small><a>Project Status</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Contract Details</a></small>
                                    </th>                                                    
                                    <th class="" >
                                        <small><a>Amount Pending</a></small>
                                    </th> 
                                    <th class="" >
                                        <small><a>Type</a></small>
                                    </th> 
                                    <th class="" >
                                        <small><a>Comments</a></small>
                                    </th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="7" style="text-align:right">Total:</th>
                                    <th colspan="2"></th>                                                    
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($listOfIdleCustomers as $idleCustomer) {
                                    $count = $count + 1;
                                    ?>
                                    <tr id='addr0' >
                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $idleCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $idleCustomer['customer_id'] ?>"><?php echo $count ?></a></small>
                                        </td>
                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $idleCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $idleCustomer['customer_id'] ?>"><?php echo $idleCustomer['company_name'] ?></a></small>
                                        </td>
                                        <td data-name="desc" class="customerAccount" data-id="<?php echo $idleCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $idleCustomer['contact_no'] ?></small>
                                        </td>
                                        <td data-name="desc" class="customerAccount" data-id="<?php echo $idleCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $idleCustomer['service_type'] ?></small>
                                        </td>
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $idleCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small>
                                                <?php
                                                $temp = "";
                                                $t = $idleCustomer['add_service_to'];
                                                $l = strlen($t);
                                                for ($i = 0; $i < $l; $i++) {
                                                    if ($t[$i] == "-" or $t[$i] == '+' or $t[$i] === ' ') {
                                                        $temp .= $t[$i] . "\n";
                                                    } else
                                                        $temp .= $t[$i];
                                                }
                                                echo $temp;
                                                ?></small>
                                        </td>
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $idleCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $idleCustomer['contract_details'] ?></small>
                                        </td>                                                        
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $idleCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; color:red; border-right: 1px solid #ffffff; text-align: center">
                                            <?php echo $idleCustomer['balance'] ?>
                                        </td>
                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $idleCustomer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php
                                                if ($idleCustomer['consultid'] == "Consulting")
                                                    echo "Consl";
                                                else
                                                    echo $idleCustomer['consultid']
                                                    ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <div class="table-edit-control">                                                                
                                                <textarea data-row-id="" rows="3" class="autosavecomments payComments" data-row="<?php echo $idleCustomer['rowid'] ?>" pay-type="<?php echo $idleCustomer['consultid'] ?>" contract="<?php echo $idleCustomer['contract_details'] ?>" name="payComments" value="" placeholder="" ><?php if (isset($idleCustomer['comment'])) echo $idleCustomer['comment'] ?></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php // include_once 'service_detail_common.php'      ?>;
    </div>

</div>

<script type="text/javascript">

    $commentValue = "";
    $commentRow = "";
    $(document).ready(function () {

        $(".autosavecomments").animate({scrollTop: $('.autosavecomments').prop("scrollHeight")}, 1000);

        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");
            table.draw();
        });

        var table = $('#table_finance_details').DataTable({
//            "scrollY": "450px",
            "sScrollX": "100%",
            "scrollY": '70vh',
            "scrollCollapse": true,
            "paging": false,
            "ordering": false,
            "columnDefs": [
                {
                    "width": "3%",
                    "targets": 0
                },
                {
                    "width": "5%",
                    "targets": 1
                },
                {
                    "width": "5%",
                    "targets": 2
                },
                {
                    "width": "3%",
                    "targets": 3
                },
                {
                    "width": "5%",
                    "targets": 4
                },
                {
                    "width": "4%",
                    "targets": 5
                },
                {
                    "width": "5%",
                    "targets": 6
                },
                {
                    "width": "3%",
                    "targets": 7
                },
                {
                    "width": "50%",
                    "targets": 8
                }],
            "footerCallback": function (row, data, start, end, display) {
//            alert("in");
                var api = this.api(), data;
                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                };
//            alert("in"+intVal);
// alert("api"+JSON.stringify(api));
                // Total over all pages
                total = api
                        .column(6)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
// alert("total"+total);
                // Total over this page
                pageTotal = api
                        .column(6, {page: 'current'})
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
//                alert("pageTotal"+pageTotal);
                // Update footer
                $(api.column(6).footer()).html(
                        'Total: ' + pageTotal
                        );
            }
            //        "paging":false
        });

        var table = $('#table_working_finance_details').DataTable({
//            "scrollY": "450px",
            "sScrollX": "100%",
            "scrollY": '70vh',
            "scrollCollapse": true,
            "paging": false,
            "ordering": false,
            "columnDefs": [
                {
                    "width": "3%",
                    "targets": 0
                },
                {
                    "width": "5%",
                    "targets": 1
                },
                {
                    "width": "5%",
                    "targets": 2
                },
                {
                    "width": "3%",
                    "targets": 3
                },
                {
                    "width": "5%",
                    "targets": 4
                },
                {
                    "width": "4%",
                    "targets": 5
                },
                {
                    "width": "5%",
                    "targets": 6
                },
                {
                    "width": "3%",
                    "targets": 7
                },
                {
                    "width": "50%",
                    "targets": 8
                }],
            "footerCallback": function (row, data, start, end, display) {
//            alert("in");
                var api = this.api(), data;
                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                };
//            alert("in"+intVal);
// alert("api"+JSON.stringify(api));
                // Total over all pages
                total = api
                        .column(6)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                // alert("total"+total);
                // Total over this page
                pageTotal = api
                        .column(6, {page: 'current'})
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
//                alert("pageTotal"+pageTotal);
                // Update footer
                $(api.column(6).footer()).html('Total: ' + pageTotal);
            }
        });

        var table = $('#table_idle_finance_details').DataTable({
//            "scrollY": "450px",
            "sScrollX": "100%",
            "scrollY": '70vh',
            "scrollCollapse": true,
            "paging": false,
            "ordering": false,
            "columnDefs": [
                {
                    "width": "3%",
                    "targets": 0
                },
                {
                    "width": "5%",
                    "targets": 1
                },
                {
                    "width": "5%",
                    "targets": 2
                },
                {
                    "width": "3%",
                    "targets": 3
                },
                {
                    "width": "5%",
                    "targets": 4
                },
                {
                    "width": "4%",
                    "targets": 5
                },
                {
                    "width": "5%",
                    "targets": 6
                },
                {
                    "width": "3%",
                    "targets": 7
                },
                {
                    "width": "50%",
                    "targets": 8
                }],
            "footerCallback": function (row, data, start, end, display) {
//            alert("in");
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                };
//            alert("in"+intVal);
// alert("api"+JSON.stringify(api));
                // Total over all pages
                total = api
                        .column(6)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
// alert("total"+total);
                // Total over this page
                pageTotal = api
                        .column(6, {page: 'current'})
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
//                alert("pageTotal"+pageTotal);
                // Update footer
                $(api.column(6).footer()).html(
                        'Total: ' + pageTotal
                        );
            }
            //        "paging":         false
        });

        table.columns.adjust().draw();

// table.columns.adjust().draw();
        $('.payComments').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();

                var inputVal = $(this).val();
                if (inputVal != "" && inputVal != null)
                    inputVal = $(this).val() + "\n";

                var appendText = "<?php echo date("d.m.Y") . " - " . $_SESSION['abbrevation'] . " - " ?>";
                // Enter pressed... do anything here...
                $(this).val(inputVal + appendText);
//                alert($(this));
            }
        });

        $('.payComments').change(function (e) {
            $commentRow = $(this).attr('data-row');
            $payt = $(this).attr('pay-type');
            $cd = $(this).attr('contract');
            savePayComments($(this));
        });

        function savePayComments($selector) {
            $commentValue = $selector.val();
//alert($commentRow);
            $.ajax({
                url: "addComments.php",
//                dataType: "json",
                type: 'POST',
                data: {
                    rowId: $commentRow,
                    rowComment: $commentValue,
                    pay: $payt,
                    contractd: $cd,
                },
                success: function (data) {
//                    alert(data);
                },
                error: function (data, status, err) {
//                    alert(status);
                }
            });
            $selector.removeClass("glowing-border");

            //           $(this).removeClass("form-group ");
            $selector.addClass("lookslike-label");
        }

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

    });
</script>