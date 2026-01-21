<!--<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>-->

<div id="wrapper" class="active" >
    <!-- Sidebar -->
    <!-- Sidebar -->

    <?php
    $pageIdentifier = "Home Page";
    include_once './header.php';
    include_once './dbConnect.php';    

    //date("Y-m-d H:i:s") 
    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        $db->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED ;');

        $allCustomers = <<<EOT
SELECT cd.company_name, cd.customer_id, cd.priority, cd.contact_person, cd.contact_no, cd.client_reference,  cd.next_service_date, cd.certification_date,
	s.add_service_to, s.service_type, s.cert_body, cb.pending_cert, c.pending_cb
FROM customer_details as cd JOIN servicesMV s 
	ON cd.customer_id = s.customer_id
LEFT JOIN (select service_id, sum(balance) pending_cert from certification_body where contract_details not like 'Market%' group by service_id) cb 
on cb.service_id = s.service_id
LEFT JOIN (select service_id, sum(consulting_balance) as pending_cb from consulting group by service_id) c ON s.service_id = c.service_id
where cd.is_delete <> 'Y' and cd.company_name not like 'consl. %'
UNION ALL
SELECT cd.company_name, cd.customer_id, cd.priority, cd.contact_person, cd.contact_no, cd.client_reference,  cd.next_service_date, cd.certification_date, 
    null add_service_to, null service_type, null cert_body, null as pending_cert, null as pending_cb
FROM customer_details as cd LEFT JOIN servicesMV s on cd.customer_id = s.customer_id
WHERE s.service_id is null 
and cd.is_delete <> 'Y' and cd.company_name not like 'consl. %'
EOT;
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();
//        print_r($listOfCustomers); die;
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
                            <div class="col-sm-2 float-label-control custom-float-label-control">                                 
                                <select name="service-type" class="form-control service-type" id="service-type">
                                    <option value="">Select Service Type </option>
                                    <?php foreach ($services as $service) { ?>
                                        <option value="<?php echo $service['service'] ?>">
                                            <?php echo $service['service'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-2 form-group float-label-control custom-float-label-control">
                                <select name="certificate-body" class="form-control certificate-body" id="certificate-body">
                                    <option value="">Select CB</option>
                                    <?php foreach ($certList as $cbs) { ?>
                                        <option value="<?php echo $cbs['cert'] ?>">
                                            <?php echo $cbs['cert'] ?>
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
                        <table class="table table-hover table-sortable table-striped" id="tab_customer_list" width="100%" onmousedown="event.preventDefault ? event.preventDefault() : event.returnValue = false">
                            <thead>
                                <tr>
                                    <th class="" style="border-left: 1px solid #ffffff">
                                        <small>Sr.</small>
                                    </th>
                                    <th class="" style="border-left: 1px solid #ffffff">
                                        <small>Prio</small>
                                    </th>
                                    <th class="">
                                        <small><a>Customer</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Contact Person</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Ph No.</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Ref By</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>CB</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>CA/RC Date</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Next Service Due</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Next Service Type</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Current Status</a></small>
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
                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">
                                            <?php
                                            if (!empty($customer["priority"]) && $customer["priority"] == "High") {
                                                $serPriority = '<img class="img-responsive" alt="High Priority User" title="High Priority User" src="images/medium.png" style="background-color: transparent; height: 18px">';
                                            } else if (!empty($customer["priority"]) && $customer["priority"] == "Medium") {
                                                $serPriority = '<img class="img-responsive" alt="High Priority User" title="Medium Priority User" src="images/low.png" style="background-color: transparent; height: 16px">';
                                            } else if (!empty($customer["priority"]) && $customer["priority"] == "Low") {
                                                $serPriority = '<img class="img-responsive" alt="High Priority User" title="Low Priority User" src="images/high.png" style="background-color: transparent; height: 15px">';
                                            } else {
                                                $serPriority = '<a class="customerAccount" id="customerName"></a>';
                                            }
                                            ?>
                                            <small ><?php echo $serPriority ?></small>
                                        </td>
                                        <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                        </td>
                                        <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['contact_person'] ?></a></small>
                                        </td>
                                        <td data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['contact_no'] ?></small>
                                        </td>
                                        <td data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['client_reference'] ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;"> 
                                            <small><?php echo $customer['cert_body'] ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; text-align: center">
                                            <small><?php if (isset($customer["certification_date"])) echo simpleDateConversion($customer["certification_date"]); ?></small>
                                        </td>                                        
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; text-align: center">
                                            <small><span><?php echo getFlatDate($customer['next_service_date']); ?>~</span><?php echo simpleDateConversion($customer['next_service_date']); ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;"> 
                                            <small><?php echo $customer['service_type'] ?></small>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php if (isset($customer['add_service_to'])) echo $customer['add_service_to'] . '-' . $customer['service_type'] ?></small>
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
                var serviceType = $(".service-type").val();
                var certificationBody = $(".certificate-body").val();
                var fromDate = $(".filterFromDate").val();
                var toDate = $(".filterToDate").val();
                var isServiceType = false;
                var isCertBody = false;
                var isfromDate = false;
                var isToDate = false;
                            alert(serviceType);
                if (serviceType == "" && certificationBody == "" && fromDate == "" && toDate == "") {
                    return true;
                }
                if (serviceType != "") {
                    var servieTypeStr = data[9] || ""; // use data for the age column
                                alert(servieTypeStr);
                    if (servieTypeStr.toUpperCase().trim() == serviceType.toUpperCase().trim()) {
                        //                alert("test");
                        isServiceType = true;
                    }
                } else {
                    isServiceType = true;
                }

                if (certificationBody != "") {
                    var certBodyStr = data[6] || ""; // use data for the age column
                    //            alert(servieTypeStr);
                    if (certBodyStr.toUpperCase().trim() == certificationBody.toUpperCase().trim()) {
                        //                alert("test");
                        isCertBody = true;
                    }
                } else {
                    isCertBody = true;
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
                finalResult = (isServiceType && isCertBody && isfromDate && isToDate);
                return finalResult;
            }
    );

    $(function () {

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

    var yetVisited = localStorage['visited'];
//    alert(yetVisited);
    window.location.hash = '#addr' + yetVisited;

    $(document).ready(function () {

        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");

            $(".container").toggleClass("active");

            table.draw();
        });

        $('.customerAccount').click(function () {
            var customerID = $(this).attr('data-id');
//            alert(customerID);
            // open popup
            localStorage['visited'] = customerID;
            var url = 'customerAccountDetails.php';
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });

        var table = $('#tab_customer_list').DataTable({
//            "autoWidth":false,
//            "scrollY": "450px",
            "sScrollX": "100%",
            "scrollY": '70vh',
            "scrollCollapse": true,
            "paging": false,
            "ordering": false,
            "columnDefs": [
                {
                    "width": "5%",
                    "targets": 1
                },
                {
                    "width": "15%",
                    "targets": 2
                },
                {
                    "width": "15%",
                    "targets": 3
                },
                {
                    "width": "10%",
                    "targets": 4
                },
                {
                    "width": "5%",
                    "targets": 5
                },
                {
                    "width": "15%",
                    "targets": 6
                },
                {
                    "width": "8%",
                    "targets": 11
                }
            ]
                    //        "paging":         false
        });
//        alert("before load");

        table.columns.adjust().draw();

        $('.service-type, .certificate-body, .filterFromDate, .filterToDate').change(function () {
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

        $('.newCustomer').click(function () {
            //            alert('hi');
            $(".addNewCustomer").show();
            $('.newCustomer').hide();
        });

        $('.cancelAdding').click(function () {
            $('.newCustomer').show();
            $(".addNewCustomer").hide();
        });

    });
</script>
<?php
//Close the connection
$statement = null;
$db = null;
?>
