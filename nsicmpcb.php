<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "NSICMPCB Page";

    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {

        $allNsicCustomers = "SELECT distinct cd.customer_id, company_name,contact_no, s.service_type, s.cert_body, s.audit_type, s.consultant, s.auditor,s.audit_date, s.service_id, s.priorty, cd.comments FROM customer_details cd join services s on cd.customer_id = s.customer_id WHERE s.add_service_to = 'Under Working' and (s.service_type = 'MPCB' or s.service_type = 'NSIC') and s.service_id=(select max(tmp.service_id) from services tmp where tmp.customer_id=cd.customer_id) and s.is_delete <> 'Y' ORDER BY ISNULL(s.audit_date),s.audit_date ASC, ISNULL(s.priorty),s.priorty ASC";
        $statement = $db->prepare($allNsicCustomers);
        $statement->execute();
        $listNsicOfCustomers = $statement->fetchAll();

        $allAuditor = "SELECT * FROM auditors";
        $statement = $db->prepare($allAuditor);
        $statement->execute();
        $allAuditorList = $statement->fetchAll();

        $allConsultant = "SELECT * FROM consultant";
        $statement = $db->prepare($allConsultant);
        $statement->execute();
        $consultant = $statement->fetchAll();

        $allCertBody = "SELECT * FROM cert_body";
        $statement = $db->prepare($allCertBody);
        $statement->execute();
        $allCert = $statement->fetchAll();
    }
    ?>
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
                <div class="row" >
                    <div class="col-sm-12 contact-form">
                        <div class="row clearfix" style="margin-top:  ">
                            <div class="col-md-12 table-responsive" style="padding-left:5px; padding-right:5px">
                                <table class="table table-bordered table-hover table-sortable table-striped" id="table_nsic_working_details">
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
                                            $newdate = strtotime('-2 day', strtotime($date));
                                            $newdate = date('d-m-Y', $newdate);
                                            $orderdate = explode('-', $newdate);
                                            $day1 = $orderdate[0];
                                            ?>
                                            <th class="">
                                                <small><a>Day <?php echo $day1 ?></a></small>
                                            </th>
                                            <?php
                                            $date = date("d-m-Y");
                                            $newdate = strtotime('-1 day', strtotime($date));
                                            $newdate = date('d-m-Y', $newdate);
                                            $orderdate = explode('-', $newdate);
                                            $day2 = $orderdate[0];
                                            ?>
                                            <th class="">
                                                <small><a>Day <?php echo $day2 ?></a></small>
                                            </th>
                                            <?php
                                            $date = date("d-m-Y");
                                            $orderdate = explode('-', $date);
                                            $day3 = $orderdate[0];
                                            ?>
                                            <th class="">
                                                <small><a>Day <?php echo $day3; ?></a></small>
                                            </th>
                                            <?php
                                            $date = date("d-m-Y");
                                            $newdate = strtotime('+1 day', strtotime($date));
                                            $newdate = date('d-m-Y', $newdate);
                                            $orderdate = explode('-', $newdate);
                                            $day4 = $orderdate[0];
                                            ?>
                                            <th class="">
                                                <small><a>Day <?php echo $day4; ?></a></small>
                                            </th>
                                            <?php
                                            $date = date("d-m-Y");
                                            $newdate = strtotime('+2 day', strtotime($date));
                                            $newdate = date('d-m-Y', $newdate);
                                            $orderdate = explode('-', $newdate);
                                            $day5 = $orderdate[0];
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
                                        foreach ($listNsicOfCustomers as $customer) {
                                            $count = $count + 1;
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
                                                $date = date("d-m-Y");
                                                $newdate = strtotime('-2 day', strtotime($date));
                                                $newdate = date('d-M-Y', $newdate);
                                                $orderdate = explode('-', $newdate);

                                                $dayData1 = $orderdate[0];
                                                $monthData1 = $orderdate[1];
                                                $yearData1 = $orderdate[2];

                                                $columnName = 'day' . $dayData1;

                                                $dataDay1 = "SELECT " . $columnName . " as daydata FROM `working_services` WHERE customer_id = :customerid AND service_id = :serviceid AND year = :year AND month = :month";
                                                $statement = $db->prepare($dataDay1);
                                                $statement->bindParam(':customerid', $customer["customer_id"]);
                                                $statement->bindParam(':serviceid', $customer["service_id"]);
                                                $statement->bindParam(':year', $yearData1);
                                                $statement->bindParam(':month', $monthData1);
                                                $statement->execute();
                                                $dataDayVal1 = $statement->fetch();

                                                $date1 = date("Y-m-d");
                                                $newdate1 = strtotime('-2 day', strtotime($date1));
                                                $newdate1 = date('Y-m-d', $newdate1);
                                                $auditdate1 = $customer['audit_date'];

                                                if ($auditdate1 == $newdate1) {
                                                    $bg = "background-color: orange";
                                                } else {
                                                    $bg = "";
                                                }
                                                ?>
                                                <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg ?>">
                                                    <div class="table-edit-control">
                                                        <input type="text" class="dalyConsult" data-date="<?php echo $newdate ?>" data-column="<?php echo $columnName; ?>" data-day="<?php echo $dayData1; ?>" data-month="<?php echo $monthData1; ?>" data-year="<?php echo $yearData1; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $dataDayVal1["daydata"]; ?>">
                                                    </div>
                                                </td>
                                                <?php
                                                $date = date("d-m-Y");
                                                $newdate = strtotime('-1 day', strtotime($date));
                                                $newdate = date('d-M-Y', $newdate);
                                                $orderdate = explode('-', $newdate);

                                                $dayData2 = $orderdate[0];
                                                $monthData2 = $orderdate[1];
                                                $yearData2 = $orderdate[2];

                                                $columnName = 'day' . $dayData2;

                                                $dataDay2 = "SELECT " . $columnName . " as daydata FROM `working_services` WHERE customer_id = :customerid AND service_id = :serviceid AND year = :year AND month = :month";
                                                $statement = $db->prepare($dataDay2);
                                                $statement->bindParam(':customerid', $customer["customer_id"]);
                                                $statement->bindParam(':serviceid', $customer["service_id"]);
                                                $statement->bindParam(':year', $yearData2);
                                                $statement->bindParam(':month', $monthData2);
                                                $statement->execute();
                                                $dataDayVal2 = $statement->fetch();

                                                $date2 = date("Y-m-d");
                                                $newdate2 = strtotime('-1 day', strtotime($date2));
                                                $newdate2 = date('Y-m-d', $newdate2);
                                                $auditdate2 = $customer['audit_date'];

                                                if ($auditdate2 == $newdate2) {
                                                    $bg = "background-color: orange";
                                                } else {
                                                    $bg = "";
                                                }
                                                ?>
                                                <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg; ?>">
                                                    <div class="table-edit-control">
                                                        <input type="text" class="dalyConsult" data-date="<?php echo $newdate ?>" data-column="<?php echo $columnName; ?>" data-day="<?php echo $dayData2; ?>" data-month="<?php echo $monthData2; ?>" data-year="<?php echo $yearData2; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $dataDayVal2["daydata"]; ?>">
                                                    </div>
                                                </td>
                                                <?php
                                                $date = date("d-M-Y");
//                                                $newdate = strtotime('-2 day', strtotime($date));
//                                                $newdate = date('d-M-Y', $newdate);
                                                $orderdate = explode('-', $date);

                                                $dayData3 = $orderdate[0];
                                                $monthData3 = $orderdate[1];
                                                $yearData3 = $orderdate[2];

                                                $columnName = 'day' . $dayData3;

                                                $dataDay3 = "SELECT " . $columnName . " as daydata FROM `working_services` WHERE customer_id = :customerid AND service_id = :serviceid AND year = :year AND month = :month";
                                                $statement = $db->prepare($dataDay3);
                                                $statement->bindParam(':customerid', $customer["customer_id"]);
                                                $statement->bindParam(':serviceid', $customer["service_id"]);
                                                $statement->bindParam(':year', $yearData3);
                                                $statement->bindParam(':month', $monthData3);
                                                $statement->execute();
                                                $dataDayVal3 = $statement->fetch();

                                                $date3 = date("Y-m-d");
//                                                $newdate2 = strtotime('-1 day', strtotime($date2));
//                                                $newdate2 = date('Y-m-d', $newdate2);
                                                $auditdate3 = $customer['audit_date'];

                                                if ($auditdate3 == $date3) {
                                                    $bg = "background-color: orange";
                                                } else {
                                                    $bg = "";
                                                }
                                                ?>
                                                <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg; ?>">
                                                    <div class="table-edit-control">
                                                        <input type="text" class="dalyConsult" data-date="<?php echo $newdate ?>" data-column="<?php echo $columnName; ?>" data-day="<?php echo $dayData3; ?>" data-month="<?php echo $monthData3; ?>" data-year="<?php echo $yearData3; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $dataDayVal3["daydata"]; ?>">
                                                    </div>
                                                </td>
                                                <?php
                                                $date = date("d-m-Y");
                                                $newdate = strtotime('+1 day', strtotime($date));
                                                $newdate = date('d-M-Y', $newdate);
                                                $orderdate = explode('-', $newdate);

                                                $dayData4 = $orderdate[0];
                                                $monthData4 = $orderdate[1];
                                                $yearData4 = $orderdate[2];

                                                $columnName = 'day' . $dayData4;

                                                $dataDay4 = "SELECT " . $columnName . " as daydata FROM `working_services` WHERE customer_id = :customerid AND service_id = :serviceid AND year = :year AND month = :month";
                                                $statement = $db->prepare($dataDay4);
                                                $statement->bindParam(':customerid', $customer["customer_id"]);
                                                $statement->bindParam(':serviceid', $customer["service_id"]);
                                                $statement->bindParam(':year', $yearData4);
                                                $statement->bindParam(':month', $monthData4);
                                                $statement->execute();
                                                $dataDayVal4 = $statement->fetch();

                                                $date4 = date("Y-m-d");
                                                $newdate4 = strtotime('+1 day', strtotime($date4));
                                                $newdate4 = date('Y-m-d', $newdate4);
                                                $auditdate4 = $customer['audit_date'];

                                                if ($auditdate4 == $newdate4) {
                                                    $bg = "background-color: orange";
                                                } else {
                                                    $bg = "";
                                                }
                                                ?>
                                                <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg; ?>">
                                                    <div class="table-edit-control">
                                                        <input type="text" class="dalyConsult" data-date="<?php echo $newdate ?>" data-column="<?php echo $columnName; ?>" data-day="<?php echo $dayData4; ?>" data-month="<?php echo $monthData4; ?>" data-year="<?php echo $yearData4; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $dataDayVal4["daydata"]; ?>">
                                                    </div>
                                                </td>
                                                <?php
                                                $date = date("d-m-Y");
                                                $newdate = strtotime('+2 day', strtotime($date));
                                                $newdate = date('d-M-Y', $newdate);
                                                $orderdate = explode('-', $newdate);

                                                $dayData5 = $orderdate[0];
                                                $monthData5 = $orderdate[1];
                                                $yearData5 = $orderdate[2];

                                                $columnName = 'day' . $dayData5;

                                                $dataDay5 = "SELECT " . $columnName . " as daydata FROM `working_services` WHERE customer_id = :customerid AND service_id = :serviceid AND year = :year AND month = :month";
                                                $statement = $db->prepare($dataDay5);
                                                $statement->bindParam(':customerid', $customer["customer_id"]);
                                                $statement->bindParam(':serviceid', $customer["service_id"]);
                                                $statement->bindParam(':year', $yearData5);
                                                $statement->bindParam(':month', $monthData5);
                                                $statement->execute();
                                                $dataDayVal5 = $statement->fetch();

                                                $date5 = date("Y-m-d");
                                                $newdate5 = strtotime('+2 day', strtotime($date5));
                                                $newdate5 = date('Y-m-d', $newdate5);
                                                $auditdate5 = $customer['audit_date'];

                                                if ($auditdate5 == $newdate5) {
                                                    $bg = "background-color: orange";
                                                } else {
                                                    $bg = "";
                                                }
                                                ?>
                                                <td class="" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; <?php echo $bg; ?>">
                                                    <div class="table-edit-control">
                                                        <input type="text" class="dalyConsult" data-date="<?php echo $newdate ?>" data-column="<?php echo $columnName; ?>" data-day="<?php echo $dayData5; ?>" data-month="<?php echo $monthData5; ?>" data-year="<?php echo $yearData5; ?>" data-custome="<?php echo $customer["customer_id"]; ?>" data-service="<?php echo $customer["service_id"] ?>" value="<?php echo $dataDayVal5["daydata"]; ?>">
                                                    </div>
                                                </td>
                                                <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <div class="table-edit-control">                                                                
                                                        <textarea data-row-id="" rows="3" id="autosavecomments" class="autosavecomments saveChanges customComments" data-row="<?php echo $customer['customer_id'] ?>" name="payComments" value="" placeholder="" ><?php if (isset($customer['comments'])) echo $customer['comments'] ?></textarea>
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

    var table = $('#table_nsic_working_details').DataTable({
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

        $('.cert-body, .work-consultant').change(function () {
            var certificationBody = $(".cert-body").val();
            var consultant = $(".work-consultant").val();

            //            alert("t");
            table.draw();
        });

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

        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");
//                 $('.dataTable').resize();
//                alert("after");
            table.draw();
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

    });
</script>