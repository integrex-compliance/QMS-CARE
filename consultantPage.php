<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->

    <?php
    $pageIdentifier = "Consultant Page";
    include_once './header.php';
    include_once './dbConnect.php';
    //    
    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        $allCustomers = "SELECT cd.customer_id, company_name, contact_no, status, svc.service_id, service_type, consultant, audit_date, add_service_to FROM customer_details cd left join services svc on cd.customer_id = svc.customer_id where cd.is_delete <> 'Y' ";
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();

        $consultant = "SELECT * FROM consultant";
        $statement = $db->prepare($consultant);
        $statement->execute();
        $consultList = $statement->fetchAll();
    }
    ?>

    <style>
        .custom-float-label-control {
            margin-bottom: 0em;
        }
    </style>

    <div id="page-content-wrapper">
        <div class="page-content inset">
            <div class="container" >
                <div class="row">
                    <div class="col-sm-12 main_content" style="">
                        <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">
                            <div class="col-sm-1"><a class="btn btn-info filterBtn"><i class="fa fa-filter "></i></a></div>                            
                            <div class="col-sm-3 float-label-control custom-float-label-control">
                                <select name="consultant_name" class="form-control consultant_name" id="consultant_name">
                                    <option value="">Select Consultant</option>
                                    <?php foreach ($consultList as $consultant) { ?>
                                        <option value="<?php echo $consultant['consultant_name'] ?>">
                                            <?php echo $consultant['consultant_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-3 float-label-control custom-float-label-control">
                                <input type="text" class="consultFromDate" id="consultFromDate" name="consultFromDate" placeholder="Service Due From" value="">
                            </div>
                            <div class="col-sm-3 form-group float-label-control custom-float-label-control">
                                <input type="text" class="consultToDate" id="consultToDate" name="consultToDate" placeholder="Service Due To" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row clearfix" >
                    <div class="col-sm-12 main_content table-responsive">
                        <table class="table table-hover table-sortable table-striped" id="tab_consultant_list" width="100%">
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
                                        <small><a>Consultant</a></small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($listOfCustomers as $customer) {
                                    $count = $count + 1;
                                    ?>
                                    <tr id='addr<?php echo $customer['customer_id'] ?>' class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>">
                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">
                                            <small><?php echo $count ?></small>
                                        </td>
                                        <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php if (isset($customer['service_type'])) echo $customer['service_type'] ?></small>
                                        </td>
							   <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php if (isset($customer['add_service_to'])) echo $customer['add_service_to'] ?></small>
                                        </td>

                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php if (isset($customer['audit_date'])) echo simpleDateConversion($customer['audit_date']) ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php if (isset($customer['consultant'])) echo $customer['consultant'] ?></small>
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
                var serviceType = $(".consultant_name").val();
                var fromDate = $(".consultFromDate").val();
                var toDate = $(".consultToDate").val();
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
        $('.consultFromDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $(".consultFromDate").keydown(function (e) {
            e.preventDefault();
        });
        
        $('.consultToDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $(".consultToDate").keydown(function (e) {
            e.preventDefault();
        });
    });


    $(document).ready(function () {

        $('.consultant_name, .consultFromDate, .consultToDate').change(function () {
            var fromDate = $(".consultFromDate").val();
            var toDate = $(".consultToDate").val();
            if (fromDate != "" && toDate != "") {
                if (!compareMinDates(fromDate, toDate)) {
                    alert("To Date can't be after From Date");
                    $(".consultToDate").val("");
                }
            }
            //            alert("t");
            table.draw();
        });

        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");
            table.draw();
        });

        var table = $('#tab_consultant_list').DataTable({
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