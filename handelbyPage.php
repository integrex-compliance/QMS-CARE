<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->

    <?php
    $pageIdentifier = "Handel By Page";
    include_once './header.php';
    include_once './dbConnect.php';
    
    date_default_timezone_set('Asia/Kolkata');
    $nevifation = "Handle By";
    $loginUser = $_SESSION['username'];

    $currentTimestamp = date("Y-m-d H:i:s");
    $pageLoad = "INSERT INTO `error_detect`(`page_name`, `login_user`, `on_page_redirect`) VALUES (:customer, :loginuser, :currenttimestamp)";
    $statement = $db->prepare($pageLoad);
    $statement->bindParam("customer", $nevifation);
    $statement->bindParam("loginuser", $loginUser);
    $statement->bindParam("currenttimestamp", $currentTimestamp);
    $statement->execute();
    
    $maxid = "SELECT id FROM error_detect WHERE id=( SELECT max(id) FROM error_detect )";
    $statement = $db->prepare($maxid);
    $statement->execute();
    $maxrow = $statement->fetch();
    $updaterow = $maxrow['id'];
    //    
    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        $allCustomers = "SELECT cd.customer_id, company_name, contact_no, status, svc.service_id, service_type, consultant, audit_date, add_service_to, handel_by FROM customer_details cd left join services svc on cd.customer_id = svc.customer_id where cd.is_delete <> 'Y'";
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();
        
        $handler = "SELECT * FROM handler";
        $statement = $db->prepare($handler);
        $statement->execute();
        $statement->execute();
        $handlerList = $statement->fetchAll();
    }
    
    $fetchtime = date("Y-m-d H:i:s");
    $result_fetch = "UPDATE `error_detect` SET `on_result_fetch`= :fetchTime WHERE id = :rowid";
    $statement = $db->prepare($result_fetch);
    $statement->bindParam("fetchTime", $fetchtime);
    $statement->bindParam("rowid", $updaterow);
    $statement->execute();
    ?>

    <style>
        .custom-float-label-control {
            margin-bottom: 0em;
        }
    </style>
    
    <?php
    $beforeloadtime = date("Y-m-d H:i:s");
    $result_fetch = "UPDATE `error_detect` SET `before_page_load`= :beforeloadTime WHERE id = :rowid";
    $statement = $db->prepare($result_fetch);
    $statement->bindParam("beforeloadTime", $beforeloadtime);
    $statement->bindParam("rowid", $updaterow);
    $statement->execute();
    ?>

    <div id="page-content-wrapper">
        <div class="page-content inset">
            <div class="container" >
                <div class="row">
                    <div class="col-sm-12 main_content" style="">
                        <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">
                            <div class="col-sm-1"><a class="btn btn-info filterBtn"><i class="fa fa-filter "></i></a></div>                            
                            <div class="col-sm-3 float-label-control custom-float-label-control">
                                <select name="handler_name" class="form-control handler_name" id="handler_name">
                                    <option value="">Select Handler</option>
                                    <?php foreach ($handlerList as $handler) { ?>
                                        <option value="<?php echo $handler['handler_name'] ?>">
                                            <?php echo $handler['handler_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-3 float-label-control ">
                                <input type="text" class="handlerFromDate" id="handlerFromDate" name="handlerFromDate" placeholder="From Date" value="">
                            </div>
                            <div class="col-sm-3  float-label-control ">
                                <input type="text" class="handlerToDate" id="handlerToDate" name="handlerToDate" placeholder="To Date" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row clearfix" >
                    <div class="col-sm-12 main_content table-responsive">
                        <table class="table table-hover table-sortable table-striped" id="tab_handelby_list" width="100%">
                            <thead>
                                <tr>
                                    <th class="" style="border-left: 1px solid #ffffff">
                                        <small>Sr.</small>
                                    </th>
                                    <th class="">
                                        <small><a>Client Name</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Audit Type</a></small>
                                    </th>
 							<th class="">
                                        <small><a>Current Status</a></small>
                                    </th>

                                    <th class="">
                                        <small><a>Date</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Handel By</a></small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($listOfCustomers as $customer) {
                                    $count = $count + 1;
                                    ?>
                                    <tr id='addr<?php echo $customer['customer_id'] ?>' >
                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">
                                            <small><?php echo $count ?></small>
                                        </td>
                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                        </td>
                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php if (isset($customer['service_type'])) echo $customer['service_type'] ?></small>
                                        </td>
 							   <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">        
                                                <small><?php if (isset($customer['add_service_to'])) echo $customer['add_service_to'] ?></small>

                                        </td>

                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">        
                                                <small><?php echo simpleDateConversion($customer['audit_date']); ?></small>
                                            <!--<small><?php if (isset($customer['audit_date'])) echo simpleDateConversion($customer['audit_date']) ?></small>-->
                                        </td>
                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php if (isset($customer['handel_by'])) echo $customer['handel_by'] ?></small>
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
    
    <?php
    $afterloadtime = date("Y-m-d H:i:s");
    $result_fetch = "UPDATE `error_detect` SET `on_page_load`= :afterloadTime WHERE id = :rowid";
    $statement = $db->prepare($result_fetch);
    $statement->bindParam("afterloadTime", $afterloadtime);
    $statement->bindParam("rowid", $updaterow);
    $statement->execute();
    ?>
</div>

<?php
$beforescriptloadtime = date("Y-m-d H:i:s");
$result_fetch = "UPDATE `error_detect` SET `before_script_load`= :beforescriptloadtime WHERE id = :rowid";
$statement = $db->prepare($result_fetch);
$statement->bindParam("beforescriptloadtime", $beforescriptloadtime);
$statement->bindParam("rowid", $updaterow);
$statement->execute();
?>

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

    $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var serviceType = $(".handler_name").val();
                var fromDate = $(".handlerFromDate").val();
                var toDate = $(".handlerToDate").val();
                var isServiceType = false;
                var isfromDate = false;
                var isToDate = false;
                //            alert(serviceType);
                if (serviceType == "" && fromDate == "" && toDate == "") {
                    return true;
                }
                if (serviceType != "") {
                    var servieTypeStr = data[5] || ""; // use data for the age column
                    //            alert(servieTypeStr);
                    if (servieTypeStr.toUpperCase().trim() == serviceType.toUpperCase().trim()) {
                        //                alert("test");
                        isServiceType = true;
                    }
                } else {
                    isServiceType = true;
                }
                var dateValue = data[4] || "";
//                var dateSplit = dateValue.split("~");
                //            alert(dateSplit[1]);
                //            alert(fromDate);
                if (fromDate != "") {
//                      alert(dateValue);
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

    $(function () {
        $('.handlerFromDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $(".handlerFromDate").keydown(function (e) {
            e.preventDefault();
        });
        
        $('.handlerToDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $(".handlerToDate").keydown(function (e) {
            e.preventDefault();
        });
    
        $(document).on('focus', ".auditDateHandleBy", function () {
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
    });

    $(document).ready(function () {
        
        $('.handler_name, .handlerFromDate, .handlerToDate').change(function () {
            var fromDate = $(".handlerFromDate").val();
            var toDate = $(".handlerToDate").val();
            if (fromDate != "" && toDate != "") {
                if (!compareMinDates(fromDate, toDate)) {
                    alert("To Date can't be after From Date");
                    $(".handlerToDate").val("");
                }
            }
            //            alert("t");
            table.draw();
        });

        $('.saHandler').change(function () {
//        alert($(this).attr('data-service'));
//        alert($(this).val());
            var svc = $(this).attr('data-service');
            var colVal = $(this).val();
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    svcid: svc,
                    handler: colVal,
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                }
            });
        });

        $('.auditDateHandleBy').change(function () {
            var customer = $(this).attr('data-row');
            var dateVal = $(this).val();
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    clientId: customer,
                    auditDate: dateVal,
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                }
            });
        });

        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");
            table.draw();
        });

        var table = $('#tab_handelby_list').DataTable({
//            "autoWidth":false,
//            "scrollY": "450px",
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
                    "width": "30%",
                    "targets": 1
                },
                {
                    "width": "20%",
                    "targets": 2
                },
                {
                    "width": "20%",
                    "targets": 3
                },
                {
                    "width": "20%",
                    "targets": 4
                }
            ]
                    //        "paging":         false
        });

        table.columns.adjust().draw();

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

<?php
$afterscriptloadtime = date("Y-m-d H:i:s");
$result_fetch = "UPDATE `error_detect` SET `on_script_load`= :afterscriptloadtime WHERE id = :rowid";
$statement = $db->prepare($result_fetch);
$statement->bindParam("afterscriptloadtime", $afterscriptloadtime);
$statement->bindParam("rowid", $updaterow);
$statement->execute();
?>