<!--<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>-->
<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "Working Page";
    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        try {
            $db->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED ;');
            
            // Try to call stored procedure, if it fails, use fallback query
            try {
                $allCustomers = "CALL usp_GetEmployeeDetailsDynamic()";
                $statement = $db->prepare($allCustomers);
                $statement->execute();
                $listOfCustomers = $statement->fetchAll();
            } catch (Exception $e) {
                // Fallback: If stored procedure doesn't exist, try to get from customer or employee table
                try {
                    $allCustomers = "SELECT * FROM customer ORDER BY customer_id DESC";
                    $statement = $db->prepare($allCustomers);
                    $statement->execute();
                    $listOfCustomers = $statement->fetchAll();
                } catch (Exception $e2) {
                    // If customer table also doesn't exist, initialize empty array
                    $listOfCustomers = array();
                }
            }

            // Get auditors with error handling
            try {
                $allAuditor = "SELECT * FROM auditors";
                $statement = $db->prepare($allAuditor);
                $statement->execute();
                $allAuditorList = $statement->fetchAll();
            } catch (Exception $e) {
                $allAuditorList = array();
            }

            // Get consultants with error handling
            try {
                $allConsultant = "SELECT * FROM consultant";
                $statement = $db->prepare($allConsultant);
                $statement->execute();
                $consultant = $statement->fetchAll();
            } catch (Exception $e) {
                $consultant = array();
            }

            // Get certification bodies with error handling
            try {
                $allCertBody = "SELECT * FROM cert_body";
                $statement = $db->prepare($allCertBody);
                $statement->execute();
                $allCert = $statement->fetchAll();
            } catch (Exception $e) {
                $allCert = array();
            }
        } catch (Exception $mainException) {
            // Initialize all arrays as empty if main try block fails
            $listOfCustomers = array();
            $allAuditorList = array();
            $consultant = array();
            $allCert = array();
            // Optional: Log error or show message
            // echo "Error loading data: " . $mainException->getMessage();
        }
//        print_r($listOfCustomers);
    }
    ?>
    <input type="submit" value="" />
    <!-- Page content -->
    <div id="page-content-wrapper">
        <div class="page-content inset">
            <div class="container" style="margin-top: 20px;padding-left:0px;">
                <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">                             

                    <div class="col-sm-1"><a class="btn btn-info filterBtn"><i class="fa fa-filter "></i></a></div>                            
                    <div class="col-sm-2 float-label-control custom-float-label-control">
                        <select name="cert-body" class="form-control cert-body" id="cert-body">
                            <option value="">Select Cert Body</option>
                            <?php foreach ($allCert as $crt) { ?>
                                <option value="<?php echo $crt['cert'] ?>"><?php echo $crt['cert'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-2 float-label-control custom-float-label-control">
                        <select name="work-consultant" class="form-control work-consultant" id="work-consultant">
                            <option value="">Select Consultant</option>
                            <?php foreach ($consultant as $consult) { ?>
                                <option value="<?php echo $consult["consultant_name"] ?>" ><?php echo $consult["consultant_name"] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <!--                <div class="row">
                                    <div class="col-sm-12 main_content" style="">-->
                <!--                <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">
                                    
                                </div>-->
                <!--                    </div>
                                </div>-->
                <div class="row clearfix" style="margin-top:  ">
                    <div class="col-md-12 table-responsive" style="padding-left:5px; padding-right:5px">
                        <table class="table table-bordered table-hover table-sortable table-striped" id="table_working_details">
                            <thead>
                                <tr>
                                    <th class="">
                                        <small><a>Priori</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Client</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Ser. Type</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Cert Body</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Auditor</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Audit Date</a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Consultant</a></small>
                                    </th>
                                    <?php
                                    $date = date("d-m-Y");
                                    $newdate1 = strtotime('-2 day', strtotime($date));
                                    $newdate1 = date('d-M-Y', $newdate1);
                                    $orderdate = explode('-', $newdate1);
                                    $day1 = $orderdate[0];
									$columnName1 = 'day' . $day1;
									$monthName1 = $orderdate[1];
									$year1 = $orderdate[2];
                                    ?>
                                    <th class="">
                                        <small><a>Day <?php echo $day1 ?></a></small>
                                    </th>
                                    <?php
                                    $newdate2 = strtotime('-1 day', strtotime($date));
                                    $newdate2 = date('d-M-Y', $newdate2);
                                    $orderdate = explode('-', $newdate2);
                                    $day2 = $orderdate[0];
									$columnName2 = 'day' . $day2;
									$monthName2 = $orderdate[1];
									$year2 = $orderdate[2];
                                    ?>
                                    <th class="">
                                        <small><a>Day <?php echo $day2 ?></a></small>
                                    </th>
                                    <?php
                                    $newdate3 = strtotime($date);
                                    $newdate3 = date('d-M-Y', $newdate3);
                                    $orderdate = explode('-', $newdate3);
                                    $day3 = $orderdate[0];
									$columnName3 = 'day' . $day3;
									$monthName3 = $orderdate[1];
									$year3 = $orderdate[2];
                                    ?>
                                    <th class="">
                                        <small><a>Day <?php echo $day3; ?></a></small>
                                    </th>
                                    <?php
                                    $newdate4 = strtotime('+1 day', strtotime($date));
                                    $newdate4 = date('d-M-Y', $newdate4);
                                    $orderdate = explode('-', $newdate4);
                                    $day4 = $orderdate[0];
									$columnName4 = 'day' . $day4;
									$monthName4 = $orderdate[1];
									$year4 = $orderdate[2];
                                    ?>
                                    <th class="">
                                        <small><a>Day <?php echo $day4; ?></a></small>
                                    </th>
                                    <?php
                                    $newdate5 = strtotime('+2 day', strtotime($date));
                                    $newdate5 = date('d-M-Y', $newdate5);
                                    $orderdate = explode('-', $newdate5);
                                    $day5 = $orderdate[0];
									$columnName5 = 'day' . $day5;
									$monthName5 = $orderdate[1];
									$year5 = $orderdate[2];
                                    ?>
                                    <th class="">
                                        <small><a>Day <?php echo $day5; ?></a></small>
                                    </th>
                                    <th class="">
                                        <small><a>Comments</a></small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($listOfCustomers as $customer) {
                                    $count = $count + 1;
                                    $diff = $customer['DateDiff'];
                                    ?>
                                    <tr id='addr0' >
                                        <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <div class="table-edit-control">
                                                <select  class="workingPriority" id="workingPriority" name="workingPriority" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" >
                                                    <option value="<?php
                                                    if (isset($customer["priorty"])) {
                                                        echo $customer["priorty"];
                                                    } else {
                                                        echo "";
                                                    }
                                                    ?>" ><?php
                                                                if (isset($customer["priorty"]) && !empty($customer["priorty"])) {
                                                                    echo $customer["priorty"];
                                                                } else {
                                                                    echo "Select";
                                                                }
                                                                ?> </option>
                                                    <option value="1" >1</option>
                                                    <option value="2" >2</option>
                                                    <option value="3" >3</option>
                                                    <option value="4" >4</option>
                                                    <option value="5" >5</option>
                                                </select>
                                                <!--<input type="text" class="dalyConsult" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $customer["priorty"]; ?>">-->
                                            </div>
                                        </td>
                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                        </td>
                                        <td class=""  data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><span><?php echo $customer['consultant']; ?>~</span><?php echo $customer['service_type'] ?></small>
                                        </td>
                                        <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <small><?php echo $customer['cert_body'] ?></small>
                                        </td>
                                        <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <div class="table-edit-control">
                                                <select  class="workingAuditor" id="workingAuditor" name="workingAuditor" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" >
                                                    <option value="<?php
                                                    if (isset($customer['auditor'])) {
                                                        echo $customer['auditor'];
                                                    } else {
                                                        echo "";
                                                    }
                                                    ?>" ><?php
                                                                if (isset($customer['auditor']) && !empty($customer['auditor'])) {
                                                                    echo $customer['auditor'];
                                                                } else {
                                                                    echo "Select";
                                                                }
                                                                ?> </option>
                                                    <option value="" >Select</option>
                                                    <?php foreach ($allAuditorList as $auditor) { ?>
                                                        <option value="<?php echo $auditor["auditor_name"] ?>" ><?php echo $auditor["auditor_name"] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <div class="table-edit-control">                                                                
                                                <small><input type="text" class="workingAuditDate" name="workingAuditDate" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>"  value="<?php echo simpleDateConversion($customer['audit_date']); ?>" placeholder="" ></small>
                                            </div>
                                            <!--<small><span><?php echo getFlatDate($customer['next_service_date']); ?>~</span><?php echo simpleDateConversion($customer['certification_date']); ?></small>-->
                                        </td>
                                        <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <div class="table-edit-control">                                                        
                                                <select  class="workingConsultant" id="workingConsultant" name="workingConsultant" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" >
                                                    <option value="<?php
                                                    if (isset($customer['consultant'])) {
                                                        echo $customer['consultant'];
                                                    } else {
                                                        echo "";
                                                    }
                                                    ?>" ><?php
                                                                if (isset($customer['consultant']) && !empty($customer['consultant'])) {
                                                                    echo $customer['consultant'];
                                                                } else {
                                                                    echo "Select";
                                                                }
                                                                ?> </option>
                                                    <option value="" >Select</option>
                                                    <?php foreach ($consultant as $consult) { ?>
                                                        <option value="<?php echo $consult["consultant_name"] ?>" ><?php echo $consult["consultant_name"] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </td>
                                        <?php
                                        if ($diff == "2") {
                                            $bg = "background-color: orange";
                                        } else {
                                            $bg = "";
                                        }
                                        ?>
                                        <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg ?>">
                                            <div class="table-edit-control">
                                                <input type="text" class="dalyConsult" data-date="<?php echo $newdate1 ?>" data-column="<?php echo $columnName1; ?>" data-day="<?php echo $day1; ?>" data-month="<?php echo $monthName1; ?>" data-year="<?php echo $year1; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $customer["day1"]; ?>">
                                            </div>
                                        </td>
                                        <?php
                                        if ($diff == "1") {
                                            $bg = "background-color: orange";
                                        } else {
                                            $bg = "";
                                        }
                                        ?>
                                        <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg; ?>">
                                            <div class="table-edit-control">
												<input type="text" class="dalyConsult" data-date="<?php echo $newdate2 ?>" data-column="<?php echo $columnName2; ?>" data-day="<?php echo $day2; ?>" data-month="<?php echo $monthName2; ?>" data-year="<?php echo $year2; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $customer["day2"]; ?>">
                                            </div>
                                        </td>
                                        <?php
                                        if ($diff == "0") {
                                            $bg = "background-color: orange";
                                        } else {
                                            $bg = "";
                                        }
                                        ?>
                                        <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg; ?>">
                                            <div class="table-edit-control">
												<input type="text" class="dalyConsult" data-date="<?php echo $newdate3 ?>" data-column="<?php echo $columnName3; ?>" data-day="<?php echo $day3; ?>" data-month="<?php echo $monthName3; ?>" data-year="<?php echo $year3; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $customer["day3"]; ?>">
                                            </div>
                                        </td>
                                        <?php
                                        if ($diff == "-1") {
                                            $bg = "background-color: orange";
                                        } else {
                                            $bg = "";
                                        }
                                        ?>
                                        <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg; ?>">
                                            <div class="table-edit-control">
												<input type="text" class="dalyConsult" data-date="<?php echo $newdate4 ?>" data-column="<?php echo $columnName4; ?>" data-day="<?php echo $day4; ?>" data-month="<?php echo $monthName4; ?>" data-year="<?php echo $year4; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $customer["day4"]; ?>">
                                            </div>
                                        </td>
                                        <?php
                                        if ($diff == "-2") {
                                            $bg = "background-color: orange";
                                        } else {
                                            $bg = "";
                                        }
                                        ?>
                                        <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg; ?>">
                                            <div class="table-edit-control">
												<input type="text" class="dalyConsult" data-date="<?php echo $newdate5 ?>" data-column="<?php echo $columnName5; ?>" data-day="<?php echo $day5; ?>" data-month="<?php echo $monthName5; ?>" data-year="<?php echo $year5; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $customer["day5"]; ?>">
                                            </div>
                                        </td>
                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                            <div class="table-edit-control">                                                                
                                                <textarea data-row-id="" rows="3" id="autosavecomments" class="autosavecomments saveChanges customComments" data-row="<?php echo $customer['customer_id'] ?>" name="payComments" value="" placeholder="" ><?php if (isset($customer['comments'])) echo $customer['comments'] ?></textarea>
                                            </div>
                                            <!--<small><?php echo nl2br($customer['comments']); ?></small>-->
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once 'service_detail_common.php' ?>;
    </div>

</div>

<script type="text/javascript">

    $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var certification = $(".cert-body").val();
                var consultant = $(".work-consultant").val();
//                    alert(certification);
//                    var toDate = $(".filterToDate").val();
                var iscertification = false;
                var isconsultant = false;
//                    var isToDate = false;
                //            alert(serviceType);
                if (certification == "" && consultant == "") {
                    return true;
                }

                if (certification != "") {
//                        alert(certification);
                    var certificationStr = data[3] || ""; // use data for the age column
                    //            alert(servieTypeStr);
                    if (certificationStr.toUpperCase().trim() == certification.toUpperCase().trim()) {
                        //                alert("test");
                        iscertification = true;
                    }
                } else {
                    iscertification = true;
                }

                if (consultant != "") {
//                        alert("test");
                    var consultantStr = data[2] || "";
//                        alert(consultantStr);
                    var dateSplit = consultantStr.split("~");// use data for the age column
//                                    alert(dateSplit[0]);
//                        alert(consultant);
                    if (dateSplit[0].toUpperCase().trim() == consultant.toUpperCase().trim()) {
                        //                alert("test");
                        isconsultant = true;
                    }
                } else {
                    isconsultant = true;
                }

                finalResult = (iscertification && isconsultant);
                return finalResult;
            }
    );

    $(function () {
        $(document).on('focus', ".workingAuditDate", function () {
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

    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
//                 $('.dataTable').resize();
//                alert("after");
        table.draw();
    });


    var table = $('#table_working_details').DataTable({
//            "autoWidth":false,
//            "scrollY": "450px",
        "sScrollX": "100%",
        "scrollY": '70vh',
        "scrollCollapse": true,
        "paging": false,
        "ordering": false,
        "columnDefs": [
            {
                "width": "7%",
                "targets": 0
            },
            {
                "width": "3%",
                "targets": 1
            },
            {
                "width": "5%",
                "targets": 2
            },
            {
                "width": "5%",
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
                "width": "3%",
                "targets": 6
            },
            {
                "width": "3%",
                "targets": 7
            },
            {
                "width": "3%",
                "targets": 8
            },
            {
                "width": "3%",
                "targets": 9
            },
            {
                "width": "3%",
                "targets": 10
            },
            {
                "width": "3%",
                "targets": 11
            },
        ]
                //        "paging":         false
    });

    table.columns.adjust().draw();

    $(document).ready(function () {

        $(".year").keypress(function (e) {
//                alert($(".month-name").val());
            $month = $(".month-name").val();
            $year = $(".year").val();
            if (e.which == 13) {
                if ($month == "" || $year == "") {
                    alert("Please select Month and Year Value");
                } else {
                    $.ajax({
                        url: "addWorkingDetails.php",
                        type: "GET",
                        dataType: 'JSON',
                        data: {
                            month: $month,
                            year: $year,
                        },
                        error: function (xhr, status, error) {
                            alert("There seems to be issue saving data!");
                        },
                        success: function (data) {
//                            alert(data);
                            $('.month-work-table-data').empty();
                            for (var key in data) {
//                        alert(data[i]['invoice_date']);
//                        if (data[0][key]['invoice_date'] == '0000-00-00')
//                            var dateVal = "";
//                        else
//                            var dateVal = moment(data[0][key]['invoice_date']).format('DD.MM.YYYY');
                                if (data[key]['day01'] == null) {
                                    var day01 = "";
                                } else {
                                    var day01 = data[key]['day01'];
                                }
                                if (data[key]['day02'] == null) {
                                    var day02 = "";
                                } else {
                                    var day02 = data[key]['day02'];
                                }
                                if (data[key]['day03'] == null) {
                                    var day03 = "";
                                } else {
                                    var day03 = data[key]['day03'];
                                }
                                if (data[key]['day04'] == null) {
                                    var day04 = "";
                                } else {
                                    var day04 = data[key]['day04'];
                                }
                                if (data[key]['day05'] == null) {
                                    var day05 = "";
                                } else {
                                    var day05 = data[key]['day05'];
                                }
                                if (data[key]['day06'] == null) {
                                    var day06 = "";
                                } else {
                                    var day06 = data[key]['day06'];
                                }
                                if (data[key]['day07'] == null) {
                                    var day07 = "";
                                } else {
                                    var day07 = data[key]['day07'];
                                }
                                if (data[key]['day08'] == null) {
                                    var day08 = "";
                                } else {
                                    var day08 = data[key]['day08'];
                                }
                                if (data[key]['day09'] == null) {
                                    var day09 = "";
                                } else {
                                    var day09 = data[key]['day09'];
                                }
                                if (data[key]['day10'] == null) {
                                    var day10 = "";
                                } else {
                                    var day10 = data[key]['day10'];
                                }
                                if (data[key]['day11'] == null) {
                                    var day11 = "";
                                } else {
                                    var day11 = data[key]['day10'];
                                }
                                if (data[key]['day12'] == null) {
                                    var day12 = "";
                                } else {
                                    var day12 = data[key]['day12'];
                                }
                                if (data[key]['day13'] == null) {
                                    var day13 = "";
                                } else {
                                    var day13 = data[key]['day13'];
                                }
                                if (data[key]['day14'] == null) {
                                    var day14 = "";
                                } else {
                                    var day14 = data[key]['day14'];
                                }
                                if (data[key]['day15'] == null) {
                                    var day15 = "";
                                } else {
                                    var day15 = data[key]['day15'];
                                }
                                if (data[key]['day16'] == null) {
                                    var day16 = "";
                                } else {
                                    var day16 = data[key]['day16'];
                                }
                                if (data[key]['day17'] == null) {
                                    var day17 = "";
                                } else {
                                    var day17 = data[key]['day17'];
                                }
                                if (data[key]['day18'] == null) {
                                    var day18 = "";
                                } else {
                                    var day18 = data[key]['day18'];
                                }
                                if (data[key]['day19'] == null) {
                                    var day19 = "";
                                } else {
                                    var day19 = data[key]['day19'];
                                }
                                if (data[key]['day20'] == null) {
                                    var day20 = "";
                                } else {
                                    var day20 = data[key]['day20'];
                                }
                                if (data[key]['day21'] == null) {
                                    var day21 = "";
                                } else {
                                    var day21 = data[key]['day21'];
                                }
                                if (data[key]['day22'] == null) {
                                    var day22 = "";
                                } else {
                                    var day22 = data[key]['day22'];
                                }
                                if (data[key]['day23'] == null) {
                                    var day23 = "";
                                } else {
                                    var day23 = data[key]['day23'];
                                }
                                if (data[key]['day24'] == null) {
                                    var day24 = "";
                                } else {
                                    var day24 = data[key]['day24'];
                                }
                                if (data[key]['day25'] == null) {
                                    var day25 = "";
                                } else {
                                    var day25 = data[key]['day25'];
                                }
                                if (data[key]['day26'] == null) {
                                    var day26 = "";
                                } else {
                                    var day26 = data[key]['day26'];
                                }
                                if (data[key]['day27'] == null) {
                                    var day27 = "";
                                } else {
                                    var day27 = data[key]['day27'];
                                }
                                if (data[key]['day28'] == null) {
                                    var day28 = "";
                                } else {
                                    var day28 = data[key]['day28'];
                                }
                                if (data[key]['day29'] == null) {
                                    var day29 = "";
                                } else {
                                    var day29 = data[key]['day29'];
                                }
                                if (data[key]['day30'] == null) {
                                    var day30 = "";
                                } else {
                                    var day30 = data[key]['day30'];
                                }
                                if (data[key]['day31'] == null) {
                                    var day31 = "";
                                } else {
                                    var day31 = data[key]['day31'];
                                }
                                $('.month-work-table-data').append('<tr><td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[key]['company_name'] + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[key]['service_type'] + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[key]['cert_body'] + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[key]['auditor'] + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + moment(data[key]['audit_date']).format('DD.MM.YYYY') + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[key]['consultant'] + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day01 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day02 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day03 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day04 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day05 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day06 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day07 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day08 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day09 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day10 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day11 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day12 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day13 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day14 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day15 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day16 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day17 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day18 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day19 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day20 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day21 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day22 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day23 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day24 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day25 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day26 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day27 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day28 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day29 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day30 + '</small></td>\n\
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + day31 + '</small></td>\n\</tr>');
                            }
                            $("#month-working").modal('show');
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                        }
                    });
                }
            }
        });

        $('.cert-body, .work-consultant').change(function () {
            var certificationBody = $(".cert-body").val();
            var consultant = $(".work-consultant").val();

            //            alert("t");
            table.draw();
        });

        $(".workingAuditDate").change(function () {
            $customerid = $(this).attr('data-custome');
            $serviceid = $(this).attr('data-service');
            $auditdate = $(this).val();
            $.ajax({
                url: "addWorkingDetails.php",
                type: "GET",
//                    dataType: 'JSON',
                data: {
                    auditDateData: $auditdate,
                    auditCustomerData: $customerid,
                    auditServiceData: $serviceid,
                },
                error: function (xhr, status, error) {
                    alert("There seems to be issue saving data!");
                },
                success: function (data) {
//                        alert(data);
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
        });

        $(".workingPriority").change(function () {
            $customerid = $(this).attr('data-custome');
            $serviceid = $(this).attr('data-service');
            $priority = $(this).val();

            $.ajax({
                url: "addWorkingDetails.php",
                type: "GET",
//                    dataType: 'JSON',
                data: {
                    data_priority: $priority,
                    data_customerId: $customerid,
                    data_serviceId: $serviceid,
                },
                error: function (xhr, status, error) {
                    alert("There seems to be issue saving data!");
                },
                success: function (data) {
//                        alert(data);
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
        });

        $(".workingAuditor").change(function () {
//                alert($(this).attr('data-custome'));
            $customerid = $(this).attr('data-custome');
            $serviceid = $(this).attr('data-service');
            $auditor = $(this).val();
            $.ajax({
                url: "addWorkingDetails.php",
                type: "GET",
//                    dataType: 'JSON',
                data: {
                    dataAuditor: $auditor,
                    dataCustomerId: $customerid,
                    dataServiceId: $serviceid,
                },
                error: function (xhr, status, error) {
                    alert("There seems to be issue saving data!");
                },
                success: function (data) {
//                        alert(data);
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
        });

        $(".workingConsultant").change(function () {
            $customerid = $(this).attr('data-custome');
            $serviceid = $(this).attr('data-service');
            $consultant = $(this).val();
            $.ajax({
                url: "addWorkingDetails.php",
                type: "GET",
//                    dataType: 'JSON',
                data: {
                    dataConsultant: $consultant,
                    dataCustomer_Id: $customerid,
                    dataService_Id: $serviceid,
                },
                error: function (xhr, status, error) {
                    alert("There seems to be issue saving data!");
                },
                success: function (data) {
//                        alert(data);
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
        });

        $(".dalyConsult").change(function () {
//                alert($(this).attr('data-date'));
            $dayVal = $(this).val();
            $dataColumn = $(this).attr('data-column');
            $dataDay = $(this).attr('data-day');
            $dataMonth = $(this).attr('data-month');
            $dataYear = $(this).attr('data-year');
            $dataCustomer = $(this).attr('data-custome');
            $dataService = $(this).attr('data-service');
            $dataDate = $(this).attr('data-date');
            $.ajax({
                url: "addWorkingDetails.php",
                type: "GET",
//                    dataType: 'JSON',
                data: {
                    dataVal: $dayVal,
                    dataColumn: $dataColumn,
                    dataDay: $dataDay,
                    dataMonth: $dataMonth,
                    dataYear: $dataYear,
                    dataCustomer: $dataCustomer,
                    dataService: $dataService,
                    dataDate: $dataDate,
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                },
                success: function (data) {
//                    alert(data);
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
        });

        // scroll down the textarea at bottom on page load 
        $(".autosavecomments").animate({scrollTop: $('.autosavecomments').prop("scrollHeight")}, 1000);

        $('.customComments').change(function (e) {
//            alert($(this).val());
            var colVal = $(this).val();
//            $customer = $(this).attr('data-row');
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    id: $(this).attr('data-row'),
                    columnName: "comments",
                    columnValue: colVal,
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
//            saveData($(this));
        });

        $('.customComments').bind('keypress', function (e) {
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

        //        $("#menu-toggle").click(function (e) {
        //            e.preventDefault();
        //            $("#wrapper").toggleClass("active");
        //        });

        $('.customerAccount').click(function () {
            var customerID = $(this).attr('data-id');
            var url = 'customerAccountDetails.php'
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
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