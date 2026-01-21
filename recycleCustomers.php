<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->

    <?php
    $pageIdentifier = "Recycle";
    include_once './header.php';
    include_once './dbConnect.php';

    date_default_timezone_set('Asia/Kolkata');
    $nevifation = "Recycle Cust";
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
        $allCustomers = "select * from customer_details where is_delete = 'Y'";
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();
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
                            <div class="col-sm-2"><a class="btn btn-info deletebutton">Delete</a></div>                            
                            <div class="col-sm-1"><a class="btn btn-info filterBtn"><i class="fa fa-filter "></i></a></div>                            
                            <div class="col-sm-3 float-label-control custom-float-label-control">
                                <select name="service-type" class="form-control service-type" id="service-type">
                                    <option value="">Select Service Type</option>
                                    <?php foreach ($services as $service) { ?>
                                        <option value="<?php echo $service['service'] ?>">
                                            <?php echo $service['service'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-3 float-label-control custom-float-label-control">
                                <input type="text" class="filterFromDate" id="filterFromDate" name="filterFromDate" placeholder="Service Due From" value="">
                            </div>
                            <div class="col-sm-3 form-group float-label-control custom-float-label-control">
                                <input type="text" class="filterToDate" id="filterToDate" name="filterToDate" placeholder="Service Due To" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row clearfix" >
                    <div class="col-sm-12 main_content table-responsive">
                        <table class="table table-hover table-sortable table-striped" id="tab_customer_recycle_list" width="100%">
                            <thead>
                                <tr>
                                    <th class="">
                                        <small><a>Delete</a></small>
                                    </th>
                                    <th class="" style="border-left: 1px solid #ffffff">
                                        <small><a>RS / Dlt</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Customer</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Current Status</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Ph. No.</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Pay. Due CB</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Pay.Due Cons.</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Next Service due:</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Next Service Type:</a></small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($listOfCustomers as $customer) {
                                    $count = $count + 1;
                                    $sql = "select * from services where customer_id= " . $customer['customer_id'] . " and service_id = (select max(service_id) from services where customer_id=" . $customer['customer_id'] . ")";
                                    $statement = $db->prepare($sql);
                                    $statement->execute();
                                    $status = $statement->fetch();

                                    $sql = "SELECT sum(balance) as pending_cert FROM certification_body group by service_id having service_id=(select max(service_id) from services where customer_id=" . $customer['customer_id'] . ")";
                                    $statement = $db->prepare($sql);
                                    $statement->execute();
                                    $pendingCert = $statement->fetch();

                                    $sql = "SELECT sum(consulting_balance) as pending_cb FROM consulting group by service_id having service_id=(select max(service_id) from services where customer_id=" . $customer['customer_id'] . ")";
                                    $statement = $db->prepare($sql);
                                    $statement->execute();
                                    $pendingConsulting = $statement->fetch();
                                    ?>                                
                                    <tr id='addr<?php echo $customer['customer_id'] ?>'  data-id="<?php echo $customer['customer_id'] ?>">
                                        <td data-name="name" style="width:40px ; border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">
                                            <small><input type="checkbox" class="deletecustomerlist" name="deletecustomerlist" value="<?php echo $customer['customer_id'] ?>" ></small>
                                        </td>
                                        <td data-name="name" style="width:40px ; border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">
                                            <small><a class="restoreCustomer" style="float: left; color: #999; padding-right: " data-id="<?php echo $customer['customer_id'] ?>" title="Restore Customer"> <i class="fa fa-undo"></i></a> 
                                                <?php if (Utitlity::checkForMainAdmin()) { ?><a class="deleteCustomer" style="float: right; color: #999; padding-right: " data-id="<?php echo $customer['customer_id'] ?>" title="Delete Customer"> <i class="fa fa-trash-o"></i></a> <?php } ?></small>
                                        </td>
                                        <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php if (isset($status['add_service_to'])) echo $status['add_service_to'] ?></small>
                                        </td>
                                        <td data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['contact_no'] ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; text-align: center">
                                            <small><?php if (isset($pendingCert["pending_cert"])) echo $pendingCert["pending_cert"]; ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; text-align: center">
                                            <small><?php if (isset($pendingConsulting["pending_cb"])) echo $pendingConsulting["pending_cb"] ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; text-align: center">
                                            <small><span><?php echo getFlatDate($customer['next_service_date']); ?>~</span><?php echo simpleDateConversion($customer['next_service_date']); ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;"> 
                                            <small><?php echo $customer['next_service_type'] ?></small>
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
<div id="delete-details" style="display: none">
    <div id="loading-div" class="ui-corner-all">
        <span style="font-size:30pt;"><i style="padding:5px;" class="fa fa-warning text-warning"></i></span>
        <div> 
            <span style="font-size:11pt;padding:10px" class="text-primary meeting-cancel-confirm-message" >Are you sure that you want to restore details?</span>
        </div>
        <div style="padding:3px;">
            <button title="Click here to Restore"  class="btn btn-primary btn-round btn-round-sm btn-sm delete-confirm-ok">Ok</button>
            <button title="Click here to cancel" class="btn btn-danger  btn-round btn-round-sm btn-sm delete-cancel">Cancel</button>
        </div>
    </div>
</div>

<div id="delete-customer" style="display: none">
    <div id="loading-div" class="ui-corner-all">
        <span style="font-size:30pt;"><i style="padding:5px;" class="fa fa-warning text-warning"></i></span>
        <div> 
            <span style="font-size:11pt;padding:10px" class="text-primary meeting-cancel-confirm-message" >Are you sure that you want to permanently delete details?</span>
        </div>
        <div style="padding:3px;">
            <button title="Click here to Restore"  class="btn btn-primary btn-round btn-round-sm btn-sm delete-confirm">Ok</button>
            <button title="Click here to cancel" class="btn btn-danger  btn-round btn-round-sm btn-sm delete-canceled">Cancel</button>
        </div>
    </div>
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
                var serviceType = $(".service-type").val();
                var fromDate = $(".filterFromDate").val();
                var toDate = $(".filterToDate").val();
                var isServiceType = false;
                var isfromDate = false;
                var isToDate = false;
                //            alert(serviceType);
                if (serviceType == "" && fromDate == "" && toDate == "") {
                    return true;
                }
                if (serviceType != "") {
                    var servieTypeStr = data[7] || ""; // use data for the age column
                    //            alert(servieTypeStr);
                    if (servieTypeStr.toUpperCase().trim() == serviceType.toUpperCase().trim()) {
                        //                alert("test");
                        isServiceType = true;
                    }
                } else {
                    isServiceType = true;
                }
                var dateValue = data[6] || "";
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

    var yetVisited = localStorage['visited'];
//    alert(yetVisited);
    window.location.hash = '#addr' + yetVisited;
    $(document).ready(function () {

        $(".deletebutton").click(function () {
            var custlist = [];

            $.each($("input[name='deletecustomerlist']:checked"), function () {
                custlist.push($(this).val());
            });
//            $('.deletecustomerlist').each(function () {
//                custlist.push($(this).prop('checked'));
//            });
            var dltlist = custlist.join("~");
//            alert(dltlist);

            $("#delete-customer").show();
            $('.delete-confirm').click(function () {
                var url = 'deleteCustomerService.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="customerDltList" value="' + dltlist + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-canceled').click(function () {
                $('#delete-customer').hide();
            });
        });

        $('.deleteCustomer').click(function () {
            var customerid = $(this).attr('data-id');
//            alert("customer id "+customerid);
            $("#delete-customer").show();
            $('.delete-confirm').click(function () {
                var url = 'deleteCustomerService.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="customerDlt" value="' + customerid + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-canceled').click(function () {
                $('#delete-customer').hide();
            });
        });

        $('.restoreCustomer').click(function () {
            var customerid = $(this).attr('data-id');
//            alert("customer id "+customerid);
            $("#delete-details").show();
            $('.delete-confirm-ok').click(function () {
                var url = 'restoreCustomerService.php';
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
            var url = 'recycleCustomerDetails.php'
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });



        var table = $('#tab_customer_recycle_list').DataTable({
//            "autoWidth":false,
//            "scrollY": "450px",
            "sScrollX": "100%",
            "scrollY": '70vh',
            "scrollCollapse": true,
            "paging": false,
            "columnDefs": [
                {
                    "width": "5%",
                    "targets": 1
                },
                {
                    "width": "20%",
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
                }
            ]
                    //        "paging":         false
        });
//        alert("before load");

        table.columns.adjust().draw();

//alert("after load");

        $('.service-type, .filterFromDate, .filterToDate').change(function () {
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