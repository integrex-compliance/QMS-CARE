<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "Auditpermonth";

    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        // $allCustomers = " select s.customer_id, company_name, service_type, cert_body, contract_details, balance from certification_body c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance != '0' and (contract_details like 'Marketing Setup%' or contract_details like 'Marketing%' or contract_details like 'Setup%' or contract_details like 'Mark%' or contract_details like 'Marke%')";
//        $allCustomers = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, s.service_type, marketing_audit_type, audit_type, comments, next_service_date, audit_date  FROM customer_details cd left join services s on cd.customer_id = s.customer_id WHERE  ((s.audit_date IS NOT NULL and s.service_id=(select max(tmp.service_id) from services tmp where tmp.customer_id=cd.customer_id))) and cd.is_delete<>'Y'";
        if (Utitlity::checkForOfficeStaff()) {
            $today = date('Y-m-d');
            $date = strtotime('-3 months');
            $oneMago = date('Y-m-d', $date);
            $allAuditDone = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, s.service_id, service_type, cert_body, add_service_to, auditor, consultant, audit_type, comments, next_service_date, audit_date, is_certified FROM customer_details cd left join services s on cd.customer_id = s.customer_id WHERE s.audit_date IS NOT NULL and (audit_date BETWEEN '" . $oneMago . "' AND '" . $today . "') and  cd.is_delete<>'Y'";
            $statement = $db->prepare($allAuditDone);
            $statement->execute();
            $listOfCustomers = $statement->fetchAll();
            //print_r($listOfCustomers);
        } else {
            $allAuditDone = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, s.service_id, service_type, cert_body, add_service_to, auditor, consultant, audit_type, comments, next_service_date, audit_date, is_certified FROM customer_details cd left join services s on cd.customer_id = s.customer_id WHERE s.audit_date IS NOT NULL and cd.is_delete<>'Y'";
            $statement = $db->prepare($allAuditDone);
            $statement->execute();
            $listOfCustomers = $statement->fetchAll();
        }

        $cb = "SELECT * FROM `cert_body`";
        $statement = $db->prepare($cb);
        $statement->execute();
        $cblist = $statement->fetchAll();

        $auditors = "SELECT * FROM `auditors`";
        $statement = $db->prepare($auditors);
        $statement->execute();
        $auditorslist = $statement->fetchAll();

        $consultants = "SELECT * FROM `consultant`";
        $statement = $db->prepare($consultants);
        $statement->execute();
        $consultantslist = $statement->fetchAll();
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
                        <?php
                        if (Utitlity::checkForOfficeStaff()) {
                            $rowview = 'none';
                        } else {
                            $rowview = "";
                        }
                        ?>
                        <div class="row" style="display: <?php echo $rowview; ?>">                            
                            <div class="col-sm-2 float-label-control custom-float-label-control">
                                <select name="service-type" class="form-control service-type" id="service-type">                                   
                                    <option value="">Month</option>
                                    <option value="01">January</option>
                                    <option value="02">February</option>                                    
                                    <option value="03">March</option>
                                    <option value="04">April</option>                                    
                                    <option value="05">May</option>
                                    <option value="06">June</option>                                   
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="col-sm-2 float-label-control custom-float-label-control">
                                <input type="text" class="year" name="year" placeholder="Enter Year" value="">
                            </div>
                            <div class="col-sm-2 float-label-control custom-float-label-control">
                                <select name="cb-name" class="form-control cb-name" id="cb-name">                                   
                                    <option value="">Select CB</option>
                                    <?php foreach ($cblist as $cbs) { ?> 
                                        <option value="<?php echo $cbs["cert"] ?>"><?php echo $cbs["cert"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
<!--                            <div class="col-sm-2 float-label-control custom-float-label-control">
                                <select name="auditor-name" class="form-control auditor-name" id="auditor-name">                                   
                                    <option value="">Select Auditor</option>
                                    <?php foreach ($auditorslist as $auditors) { ?>
                                        <option value="<?php echo $auditors["auditor_name"] ?>"><?php echo $auditors["auditor_name"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-2 float-label-control custom-float-label-control">
                                <select name="consultant-name" class="form-control consultant-name" id="consultant-name">                                   
                                    <option value="">Select Consultant</option>
                                    <?php foreach ($consultantslist as $consultants) { ?>
                                        <option value="<?php echo $consultants["consultant_name"] ?>"><?php echo $consultants["consultant_name"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>-->
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
                                                <small><a>Contact Person</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>CB</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Audit Done For</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Dated</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Status</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Auditor</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Consultant</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Reference By</a></small>
                                            </th>
                                            <th class="" width="10%">
                                                <small><a>Certificate Given</a></small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $serviceNo = 1;
                                        foreach ($listOfCustomers as $customer) {
                                            $service = "SELECT (select max(service_id) FROM services WHERE customer_id = :customerid) as maxid";
                                            $statement = $db->prepare($service);
                                            $statement->bindParam(':customerid', $customer['customer_id']);
                                            $statement->execute();
                                            $services = $statement->fetchAll();
                                            $totalServiceCount = count($services);

//                                            $certificationCount = "SELECT (select max(service_id) FROM services WHERE customer_id = 82) as maxid ,service_id, customer_id, service_type, cert_body, consulting_firm, consultant, audit_type, auditor, audit_date, created_at, add_service_to, is_certified FROM services WHERE customer_id = 82 and (service_type = 'CA' or service_type = 'SA-1' or service_type = 'SA-2' or service_type = 'RC')";
//                                            $statement = $db->prepare($certificationCount);
//                                            $statement->bindParam(':customerid', $customer['customer_id']);
//                                            $statement->execute();
//                                            $svcCount = $statement->fetchAll();
//                                            $totalServiceCount = count($svcCount);

                                            $disable = "";
                                            ?>
                                            <tr id='addr0' data-id="<?php echo $customer['customer_id'] ?>" class="">

                                                <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                                </td>
                                                <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['contact_person'] ?></small>
                                                </td>
                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['cert_body'] ?></small>
                                                </td>
                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['service_type'] ?></small>
                                                </td>
                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo simpleDateConversion($customer['audit_date']) ?></small>
                                                </td>
                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer["add_service_to"]; ?></small>
                                                </td>
                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer["auditor"]; ?></small>
                                                </td>
                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer["consultant"]; ?></small>
                                                </td>
                                                <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer["client_reference"]; ?></small>
                                                </td>
                                                <?php
                                                if (isset($customer['is_certified']) && $customer['is_certified'] == 'YES' && ($serviceNo == $totalServiceCount)) {
                                                    $chkVal = "checked";
                                                    $disable = 'disabled="disabled"';
                                                } else {
                                                    $chkVal = "";
                                                    $disable = "";
                                                }
                                                ?>
                                                <td data-name="" style="border-top: 1px solid #ffffff; color:red; border-right: 1px solid #ffffff; text-align: center">
                                                    <?php if (($customer['service_type'] == "CA" || $customer['service_type'] == "SA-1" || $customer['service_type'] == "SA-2" || $customer['service_type'] == "RC")) { ?>
                                                        <input type="checkbox" class="issueCert" data-service="<?php echo $customer['service_id'] ?>" data-customer="<?php echo $customer['customer_id'] ?>" <?php echo $chkVal ?> <?php echo $disable ?>>
                                                    <?php } ?>
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

<script type="text/javascript">
    var table = $('#table_finance_details').DataTable({
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
                "width": "20%",
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
                "width": "15%",
                "targets": 4
            },
            {
                "width": "15%",
                "targets": 5
            },
            {
                "width": "15%",
                "targets": 6
            },
            {
                "width": "10%",
                "targets": 7
            },
            {
                "width": "10%",
                "targets": 8
            },
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

        $('.issueCert').change(function () {
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

    $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var serviceType = $(".service-type").val();
                var year = $(".year").val();
                var cbname = $(".cb-name").val();
//                var auditorname = $(".auditor-name").val();
//                var consultantname = $(".consultant-name").val();
                // var toDate = $(".filterToDate").val();
                var isServiceType = false;
                var iscb = false;
//                var isauditor = false;
//                var isconsult = false;
                // var isToDate = false;

                // alert(serviceType);
                if (cbname == "") {
                    return true;
                }
                if (serviceType == "") {
//                    return true;
                    if (year != "") {
                        serviceType = serviceType + year
                    }
                } else {
                    serviceType = '.' + serviceType + '.';
                    if (year != "") {
                        serviceType = serviceType + year
                    }
//                    return true;
                }

                if (serviceType != "") {
                    var servieTypeStr = data[4] || ""; // use data for the age column
//                     alert(servieTypeStr);
                    if (servieTypeStr.toUpperCase().includes(serviceType)) {
                        //                alert("test");
                        isServiceType = true;
                    }
                } else {
                    isServiceType = true;
                }

                if (cbname != "") {
                    var cbnameTypeStr = data[2] || ""; // use data for the age column
//                     alert(servieTypeStr);
                    if (cbnameTypeStr.toUpperCase().includes(cbname)) {
//                                        alert("test");
                        iscb = true;
                    }
                } else {
                    iscb = true;
                }

//                if (auditorname != "") {
//                    var auditornameTypeStr = data[6] || "";
////                    alert(auditornameTypeStr);
//                    if (auditornameTypeStr.toUpperCase().includes(auditorname)) {
////                        alert("test");
//                        isauditor = true;
//                    }
//                } else {
////                    alert("test fail");
//                    isauditor = true;
//                }
//                alert(isauditor);
                finalResult = (isServiceType && iscb);
                return finalResult;
//                return isServiceType;
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

        $('.service-type, .year, .cb-name').change(function () {
            // var fromDate = $(".filterFromDate").val();
            var serviceType = $(".service-type").val();
            var cbType = $(".cb-name").val();
//            var auditor = $(".auditor-name").val();
            // alert("t");
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
            dateFormat: 'dd-mm-yy'
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
            dateFormat: 'dd-mm-yy'
        });

        $("#filterToDate").keydown(function (e) {
            //            e.preventDefault();
        });

    });

</script>



