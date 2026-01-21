<!--<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>-->

<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->

    <?php
    $pageIdentifier = "Contact Info";
    include_once './header.php';
    include_once './dbConnect.php';
    //
    date_default_timezone_set('Asia/Kolkata');
    $nevifation = "Contact Info";
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
    
    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        $allCustomers = "select * from customer_details where is_delete <> 'Y'";
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
                        <table class="table table-hover table-sortable table-striped" id="tab_contact_list" width="100%">
                            <thead>
                                <tr>
                                    <th class="" style="border-left: 1px solid #ffffff">
                                        <small>Sr.</small>
                                    </th>
                                   
                                    <th class="">
                                        <small><a>Name of Customer</a></small>
                                    </th>
				    <th class="">
                                        <small><a>Address</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Contact Person</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Contact Number</a></small>
                                    </th>
                                    <th class="">
                                            <small><a>Email ID</a></small>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $count = 0;
                                foreach ($listOfCustomers as $customer) {
                                    
                                    if(stripos($customer['company_name'],"consl. ")!== 0){
                                       
                                    $count = $count + 1;
                                    $sql = "select * from services where customer_id= " . $customer['customer_id'] . " and service_id = (select max(service_id) from services where customer_id=" . $customer['customer_id'] . ")";
                                    $statement = $db->prepare($sql);
                                    $statement->execute();
                                    $status = $statement->fetch();

                                    $sql = "SELECT sum(balance) as pending_cert FROM certification_body where contract_details not like 'Market%' group by service_id having service_id=(select max(service_id) from services where customer_id=" . $customer['customer_id'] . ")";
                                    $statement = $db->prepare($sql);
                                    $statement->execute();
                                    $pendingCert = $statement->fetch();

                                    $sql = "SELECT sum(consulting_balance) as pending_cb FROM consulting group by service_id having service_id=(select max(service_id) from services where customer_id=" . $customer['customer_id'] . ")";
                                    $statement = $db->prepare($sql);
                                    $statement->execute();
                                    $pendingConsulting = $statement->fetch();
                                    ?>                                
                                    <tr>
                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">
                                            <small><?php echo $count ?></small>
                                        </td>
                                       
                                        <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                        </td>
					<td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['company_address'] ?></small>
                                        </td>

                                        <td data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['contact_person'] ?></small>
                                        </td>
                                        <td data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['contact_no'] ?></small>
                                        </td>
                                        <td data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['contact_email'] ?></small>
                                        </td>
                                        </td>
                                    </tr>
                                <?php }
                                }
                                ?>
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
                    var servieTypeStr = data[9] || ""; // use data for the age column
                    //            alert(servieTypeStr);
                    if (servieTypeStr.toUpperCase().trim() == serviceType.toUpperCase().trim()) {
                        //                alert("test");
                        isServiceType = true;
                    }
                } else {
                    isServiceType = true;
                }
                var dateValue = data[8] || "";
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
//$(window).load(function(){

// $('#tab_customer_list').resize();

// });
    var yetVisited = localStorage['visited'];
//    alert(yetVisited);
    window.location.hash = '#addr' + yetVisited;
    $(document).ready(function () {
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");
//                 $('.dataTable').resize();
//                 alert("after");
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

        var table = $('#tab_contact_list').DataTable({
//            "autoWidth":false,
//            "scrollY": "450px",
            "sScrollX": "100%",
            "scrollY": '70vh',
            "scrollCollapse": true,
            "paging": false,
		"ordering": false,
            "columnDefs": [
                 {
                    "width": "2%",
                    "targets": 0
                },
                {
                    "width": "20%",
                    "targets": 1
                },
                {
                    "width": "20%",
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