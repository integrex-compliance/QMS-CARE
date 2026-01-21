<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "TDS rcv list";

    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
//        $allCustomers = "select * from (select s.customer_id, company_name, contact_no, service_type, add_service_to, contract_details, balance, cert_comment comment, certification_id rowid from certification_body c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance > 0 and contract_details not like 'Marketing Setup%' and contract_details not like 'Mark%' and contract_details not like 'Marketing%' and contract_details not like 'Setup%' and contract_details not like 'Marke%' and s.is_delete<>'Y' union select s.customer_id, company_name, contact_no, service_type, add_service_to, consulting_fees as contract_details, consulting_balance as balance, consulting_comment comment, consulting_id rowid from consulting c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where consulting_balance >0 and s.is_delete<>'Y') as t order by company_name";
        $allCustomers = "select * from ( select s.customer_id, company_name, contact_no, service_type, add_service_to, contract_details, payment_received, deposit_date, check_details, bank_account, cert_comment comment, certification_id rowid, 'CB' as consultid from certification_body c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where payment_received > 0 and contract_details like 'tds%' and s.is_delete<>'Y' union select s.customer_id, company_name, contact_no, service_type, add_service_to, consulting_fees as contract_details, consulting_payment_received as payment_received, consulting_deposit_date as deposit_date, consulting_chque_details as check_details, bank_ac as bank_account, consulting_comment comment, consulting_id rowid, 'Consulting' as consultid from consulting c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where consulting_payment_received >0 and consulting_fees like 'TDS%' and s.is_delete<>'Y') as t order by FIELD(add_service_to, 'Under Payment','Under Working','Under Marketing','Completed','Ideal','')";
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();

        $bankAbbr = "SELECT * FROM consulting_abbrevation";
        $statement = $db->prepare($bankAbbr);
        $statement->execute();
        $bankList = $statement->fetchAll();

        //select  * from consulting c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where consulting_balance >0
        //select  * from certification_body c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance >0
    }
    ?>

    <!-- Page content -->
    <div id="page-content-wrapper">
        <!-- Keep all page content within the page-content inset div! -->
        <div class="page-content inset">
            <div class="row">
                <div class="col-md-12">
                    <div class="container" style="margin-top: 20px;">
                        <div class="row">
                            <div class="col-sm-12 main_content" style="">
                                <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">
                                    <div class="col-sm-1"><a class="btn btn-info filterBtn"><i class="fa fa-filter "></i></a></div>                            
                                    <div class="col-sm-3 float-label-control custom-float-label-control">
                                        <select name="bank_ac" class="form-control bank_ac" id="bank_ac">
                                            <option value="">Select Bank Account</option>
                                            <?php foreach ($bankList as $bankAbbr) { ?>
                                                <option value="<?php echo $bankAbbr['bank_abbr'] ?>">
                                                    <?php echo $bankAbbr['bank_abbr'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 float-label-control custom-float-label-control">
                                        <input type="text" class="filterDateFrom" id="filterDateFrom" name="filterDateFrom" placeholder="From Date" value="">
                                    </div>
                                    <div class="col-sm-3 form-group float-label-control custom-float-label-control">
                                        <input type="text" class="filterDateTo" id="filterDateTo" name="filterDateTo" placeholder="To Date" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 contact-form">
                                <div class="row clearfix">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered table-striped table-hover table-sortable" id="table_payrcv_details">
                                            <thead>
                                                <tr>
                                                    <th class="">
                                                        <small><a>Sr.</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Client Name</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Amount</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Instrument No</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>CB / Consulting</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Date</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Bank a/c</a></small>
                                                    </th>                                                    
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="0"></th>
                                                    <th colspan="1"></th>  
                                                    <th colspan="2" style="text-align:left">Total:</th>
                                                    <!--<th colspan="2"></th>-->   
                                                    <!--<th colspan="3"></th>-->   
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                $count = 0;
                                                foreach ($listOfCustomers as $customer) {
                                                    $count = $count + 1;
                                                    ?>
                                                    <tr id='addr0' >
                                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a data-id="<?php echo $customer['customer_id'] ?>"><?php echo $count ?></a></small>
                                                        </td>
                                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a data-id="<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_name'] ?></a></small>
                                                        </td>
                                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; color:green;">
                                                            <?php echo $customer['payment_received'] ?>
                                                        </td>
                                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <?php echo $customer['check_details'] ?>
                                                        </td>
                                                        <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><?php echo $customer['consultid'] ?></small>
                                                        </td>
                                                        <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><?php echo simpleDateConversion($customer['deposit_date']) ?></small>
                                                        </td>
                                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><?php echo $customer['bank_account'] ?></small>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
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
        <?php // include_once 'service_detail_common.php'  ?>;
    </div>

</div>

<script type="text/javascript">
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

//    $.fn.dataTable.ext.search.push(
//            function (settings, data, dataIndex) {
////                  alert(data[4]);
////                var serviceType = $(".service-type").val();
//                var fromDate = $(".filterDateFrom").val();
//                var toDate = $(".filterDateTo").val();
//                var isfromDate = false;
//                var isToDate = false;
//                //            alert(serviceType);
//                if (fromDate == "" && toDate == "") {
//                    return true;
//                }
//
//                var dateValue = data[4] || "";
//                var dateSplit = data[4] || "";
//                //            alert(dateSplit[1]);
//                //            alert(fromDate);
//                if (fromDate != "") {
////                      alert(dateValue);
//                    isfromDate = compareMinDates(fromDate, dateValue);
//                } else {
//                    isfromDate = true;
//                }
//                if (toDate != "") {
//                    isToDate = compareMaxDates(toDate, dateValue);
//                } else {
//                    isToDate = true;
//                }
//                finalResult = (isfromDate && isToDate);
//
//                return finalResult;
//            }
//    );

    $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var serviceType = $(".bank_ac").val();
                var fromDate = $(".filterDateFrom").val();
                var toDate = $(".filterDateTo").val();
                var isServiceType = false;
                var isfromDate = false;
                var isToDate = false;
                //            alert(serviceType);
                if (serviceType == "" && fromDate == "" && toDate == "") {
                    return true;
                }
                if (serviceType != "") {
                    var servieTypeStr = data[6] || ""; // use data for the age column
                    //            alert(servieTypeStr);
                    if (servieTypeStr.toUpperCase().trim() == serviceType.toUpperCase().trim()) {
                    //                alert("test");
                        isServiceType = true;
                    }
                } else {
                    isServiceType = true;
                }
                var dateValue = data[5] || "";
                //            var dateSplit = dateValue.split("~");
                //            alert(dateSplit[1]);
                //            alert(fromDate);
                if (fromDate != "") {
                //            alert(dateValue);
                    isfromDate = compareMinDates(fromDate, dateValue);
                } else {
                    isfromDate = true;
                }
                if (toDate != "") {
                    isToDate = compareMaxDates(toDate, dateValue);
                } else {
                    isToDate = true;
                }
                finalResult = (isServiceType && isfromDate && isToDate);
                return finalResult;
            }
    );

    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
        table.draw();
    });

    $commentValue = "";
    $commentRow = "";
    $(document).ready(function () {

        $('.bank_ac, .filterDateFrom, .filterDateTo').change(function () {
            var fromDate = $(".filterDateFrom").val();
            var toDate = $(".filterDateTo").val();
            if (fromDate != "" && toDate != "") {
                if (!compareMinDates(fromDate, toDate)) {
                    alert("To Date can't be after From Date");
                    $(".filterDateTo").val("");
                }
            }
            //            alert("t");
            table.draw();
        });

        var table = $('#table_payrcv_details').DataTable({
            "scrollY": "450px",
            "sScrollX": "100%",
            "scrollY": '70vh',
                    "scrollCollapse": true,
            "paging": false,
            "columnDefs": [
                {
                    "width": "10%",
                    "targets": 0
                },
                {
                    "width": "40%",
                    "targets": 1
                },
                {
                    "width": "12%",
                    "targets": 2
                },
                {
                    "width": "12%",
                    "targets": 3
                },
                {
                    "width": "12%",
                    "targets": 4
                },
                {
                    "width": "12%",
                    "targets": 5
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
                        .column(2)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
// alert("total"+total);
                // Total over this page
                pageTotal = api
                        .column(2, {page: 'current'})
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
//                alert("pageTotal"+pageTotal);
                // Update footer
                $(api.column(2).footer()).html(
                        'Total: ' + pageTotal
                        );
            }
            //        "paging":         false
        });

        table.columns.adjust().draw();

        $('.payComments').change(function (e) {
            $commentRow = $(this).attr('data-row')
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
        // table.columns.adjust().draw();

        $('#filterDateFrom').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $("#filterDateFrom").keydown(function (e) {
            //            e.preventDefault();
        });

        $('#filterDateTo').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $("#filterDateTo").keydown(function (e) {
            //            e.preventDefault();
        });

    });
</script>