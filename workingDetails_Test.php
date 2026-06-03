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
        function workingDetailsValue($row, $key) {
            return isset($row[$key]) ? $row[$key] : "";
        }

        function workingDetailsMoney($value) {
            if ($value === "" || $value === null) {
                return "";
            }
            return number_format((float) $value, 2);
        }

        function workingDetailsEsc($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        }

        function workingDetailsDraftInput($value, $draftKey, $placeholder = "") {
            return '<input type="text" class="sheet-cell-input sheet-draft-input" data-draft-key="' . workingDetailsEsc($draftKey) . '" value="' . workingDetailsEsc($value) . '" placeholder="' . workingDetailsEsc($placeholder) . '" />';
        }

        function workingDetailsDraftTextarea($value, $draftKey, $placeholder = "") {
            return '<textarea rows="3" class="sheet-cell-input sheet-draft-input" data-draft-key="' . workingDetailsEsc($draftKey) . '" placeholder="' . workingDetailsEsc($placeholder) . '">' . workingDetailsEsc($value) . '</textarea>';
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
                <style>
                    .working-entry-toolbar {
                        display: flex;
                        align-items: center;
                        justify-content: flex-end;
                        gap: 12px;
                        margin: 8px 5px 10px;
                        padding: 0;
                    }
                    .working-entry-toolbar .entry-actions {
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    }
                    .add-entry-row {
                        border: 0;
                        border-radius: 20px;
                        padding: 8px 18px;
                        background: linear-gradient(135deg, #1769aa, #2b9acb);
                        color: #ffffff;
                        font-weight: 700;
                        box-shadow: 0 8px 18px rgba(23, 105, 170, .25);
                    }
                    .add-entry-row:hover,
                    .add-entry-row:focus {
                        color: #ffffff;
                        background: linear-gradient(135deg, #125a92, #238ab8);
                    }
                    .working-sheet-wrap {
                        padding-left: 5px;
                        padding-right: 5px;
                        overflow-x: auto;
                    }
                    #table_working_details {
                        min-width: 3600px;
                        border-collapse: collapse;
                        background: #ffffff;
                        table-layout: fixed;
                    }
                    #table_working_details th,
                    #table_working_details td {
                        border: 1px solid #d9e2ef !important;
                        vertical-align: middle !important;
                        padding: 5px 6px;
                        color: #172033;
                    }
                    #table_working_details thead th {
                        background: #eaf2ff;
                        color: #172033;
                        text-align: center;
                        font-weight: 700;
                        white-space: normal;
                        line-height: 1.2;
                    }
                    #table_working_details thead tr:first-child th {
                        background: #bcd4f6;
                        text-transform: uppercase;
                        letter-spacing: .03em;
                    }
                    #table_working_details thead tr:nth-child(2) th {
                        background: #d8e8fb;
                    }
                    #table_working_details tbody td {
                        background: #ffffff;
                        white-space: normal;
                        font-size: 12px;
                    }
                    #table_working_details tbody tr:nth-child(even) td {
                        background: #f8fbff;
                    }
                    #table_working_details .sheet-cell-input,
                    #table_working_details select,
                    #table_working_details textarea {
                        width: 100%;
                        border: 1px solid #cbd7e6;
                        border-radius: 2px;
                        background: #fff;
                        font-size: 12px;
                        padding: 3px 4px;
                    }
                    #table_working_details .sheet-cell-input:focus,
                    #table_working_details select:focus,
                    #table_working_details textarea:focus {
                        border-color: #2b6fbd;
                        box-shadow: 0 0 0 2px rgba(43, 111, 189, .14);
                        outline: none;
                    }
                    #table_working_details .sheet-draft-input.sheet-dirty {
                        background: #fff9df;
                    }
                    #table_working_details .blank-entry-row td {
                        background: #fffdf5;
                    }
                    #table_working_details textarea {
                        min-height: 44px;
                        resize: vertical;
                    }
                    #table_working_details td[data-name="company_name"] .sheet-cell-input {
                        white-space: nowrap;
                    }
                    #table_working_details th:nth-child(4),
                    #table_working_details td:nth-child(4) {
                        width: 300px !important;
                        min-width: 300px !important;
                        max-width: 300px !important;
                    }
                    #table_working_details td:nth-child(7) {
                        width: 300px !important;
                        min-width: 300px !important;
                        max-width: 300px !important;
                    }
                    #table_working_details td:nth-child(4) .sheet-cell-input {
                        min-width: 270px;
                    }
                    #table_working_details td:nth-child(7) textarea {
                        min-width: 270px;
                    }
                    #table_working_details td[data-name="contract_installment_details"] textarea {
                        min-height: 58px;
                        line-height: 1.35;
                    }
                    .sheet-col-sr { width: 55px; }
                    .sheet-col-sm { width: 115px; }
                    .sheet-col-md { width: 155px; }
                    .sheet-col-lg { width: 220px; }
                    .sheet-col-company { width: 300px; }
                    .sheet-col-installment { width: 300px; }
                    .sheet-col-xl { width: 280px; }
                </style>
                <div class="working-entry-toolbar">
                    <div class="entry-actions">
                        <button type="button" class="add-entry-row">+ Add 5 Rows</button>
                    </div>
                </div>
                <div class="row clearfix" style="margin-top:  ">
                    <div class="col-md-12 working-sheet-wrap">
                        <table class="table table-bordered table-hover table-sortable table-striped" id="table_working_details">
                            <colgroup>
                                <col style="width:55px">
                                <col style="width:115px">
                                <col style="width:115px">
                                <col style="width:430px">
                                <col style="width:155px">
                                <col style="width:115px">
                                <col style="width:430px">
                                <col style="width:115px">
                                <col style="width:115px">
                                <col style="width:155px">
                                <col style="width:115px">
                                <col style="width:115px">
                                <col style="width:115px">
                                <col style="width:115px">
                                <col style="width:155px">
                                <col style="width:155px">
                                <col style="width:115px">
                                <col style="width:115px">
                                <col style="width:155px">
                                <col style="width:155px">
                                <col style="width:280px">
                                <col style="width:280px">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th rowspan="2" class="sheet-col-sr"><small>Sr. No.</small></th>
                                    <th rowspan="2" class="sheet-col-sm"><small>Service Type</small></th>
                                    <th rowspan="2" class="sheet-col-sm"><small>Standard</small></th>
                                    <th rowspan="2" class="sheet-col-company"><small>Company Name</small></th>
                                    <th rowspan="2" class="sheet-col-md"><small>Service Firm</small></th>
                                    <th colspan="4"><small>Project Costing (Decided)</small></th>
                                    <th rowspan="2" class="sheet-col-md"><small>Contract Thanks Mail Date</small></th>
                                    <th colspan="2"><small>Received Amount</small></th>
                                    <th colspan="2"><small>Remaining Amount</small></th>
                                    <th rowspan="2" class="sheet-col-md"><small>1st Visit Date (Project Start)</small></th>
                                    <th colspan="3"><small>Consultant Visits Record</small></th>
                                    <th rowspan="2" class="sheet-col-md"><small>Audit Date (Project Closing)</small></th>
                                    <th rowspan="2" class="sheet-col-md"><small>Project Status</small></th>
                                    <th rowspan="2" class="sheet-col-xl"><small>Other Comments</small></th>
                                    <th rowspan="2" class="sheet-col-xl"><small>Guidelines / Notes section</small></th>
                                </tr>
                                <tr>
                                    <th class="sheet-col-sm"><small>Contract Total Cost</small></th>
                                    <th class="sheet-col-installment"><small>Contract Installment Details</small></th>
                                    <th class="sheet-col-sm"><small>CB Amount</small></th>
                                    <th class="sheet-col-sm"><small>Consultant Amount</small></th>
                                    <th class="sheet-col-sm"><small>CB</small></th>
                                    <th class="sheet-col-sm"><small>Consultant</small></th>
                                    <th class="sheet-col-sm"><small>CB</small></th>
                                    <th class="sheet-col-sm"><small>Consultant</small></th>
                                    <th class="sheet-col-md"><small>Consultant</small></th>
                                    <th class="sheet-col-sm"><small>Total Visit</small></th>
                                    <th class="sheet-col-sm"><small>Month Visit</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($listOfCustomers as $customer) {
                                    $count = $count + 1;
                                    $serviceId = workingDetailsValue($customer, "service_id");
                                    $cbSummary = array("contract_details" => "", "amount_decided" => 0, "payment_received" => 0, "balance" => 0);
                                    $consultingSummary = array("consulting_fees" => "", "consulting_amount_decided" => 0, "consulting_payment_received" => 0, "consulting_balance" => 0);
                                    $totalVisits = 0;
                                    $monthVisits = 0;

                                    if (!empty($serviceId)) {
                                        try {
                                            $sql = "SELECT GROUP_CONCAT(contract_details SEPARATOR ', ') AS contract_details, SUM(amount_decided) AS amount_decided, SUM(payment_received) AS payment_received, SUM(balance) AS balance FROM certification_body WHERE service_id = :serviceid";
                                            $statement = $db->prepare($sql);
                                            $statement->bindParam(':serviceid', $serviceId);
                                            $statement->execute();
                                            $cbData = $statement->fetch();
                                            if ($cbData) {
                                                $cbSummary = $cbData;
                                            }
                                        } catch (Exception $e) {
                                        }

                                        try {
                                            $sql = "SELECT GROUP_CONCAT(consulting_fees SEPARATOR ', ') AS consulting_fees, SUM(consulting_amount_decided) AS consulting_amount_decided, SUM(consulting_payment_received) AS consulting_payment_received, SUM(consulting_balance) AS consulting_balance FROM consulting WHERE service_id = :serviceid";
                                            $statement = $db->prepare($sql);
                                            $statement->bindParam(':serviceid', $serviceId);
                                            $statement->execute();
                                            $consultingData = $statement->fetch();
                                            if ($consultingData) {
                                                $consultingSummary = $consultingData;
                                            }
                                        } catch (Exception $e) {
                                        }
                                    }

                                    for ($visitCounter = 1; $visitCounter <= 31; $visitCounter++) {
                                        $visitKey = "day" . $visitCounter;
                                        if (!empty($customer[$visitKey])) {
                                            $totalVisits++;
                                            $monthVisits++;
                                        }
                                    }

                                    $cbAmount = isset($cbSummary['amount_decided']) ? $cbSummary['amount_decided'] : 0;
                                    $consultantAmount = isset($consultingSummary['consulting_amount_decided']) ? $consultingSummary['consulting_amount_decided'] : 0;
                                    $contractTotal = (float) $cbAmount + (float) $consultantAmount;
                                    $contractInstallments = trim(workingDetailsValue($cbSummary, "contract_details") . " " . workingDetailsValue($consultingSummary, "consulting_fees"));
                                    ?>
                                    <tr id='addr0' >
                                        <td data-name="sr">
                                            <small><?php echo $count; ?></small>
                                        </td>
                                        <td data-name="service_type">
                                            <?php echo workingDetailsDraftInput(workingDetailsValue($customer, 'service_type'), "existing-" . $serviceId . "-service_type", "Service Type"); ?>
                                        </td>
                                        <td data-name="standard">
                                            <?php echo workingDetailsDraftInput(workingDetailsValue($customer, 'cert_body'), "existing-" . $serviceId . "-standard", "Standard"); ?>
                                        </td>
                                        <td data-id="<?php echo workingDetailsValue($customer, 'customer_id'); ?>" data-name="company_name">
                                            <?php echo workingDetailsDraftInput(workingDetailsValue($customer, 'company_name'), "existing-" . $serviceId . "-company_name", "Company Name"); ?>
                                        </td>
                                        <td data-name="service_firm">
                                            <?php echo workingDetailsDraftInput(workingDetailsValue($customer, 'consulting_firm'), "existing-" . $serviceId . "-service_firm", "Service Firm"); ?>
                                        </td>
                                        <td data-name="contract_total_cost">
                                            <?php echo workingDetailsDraftInput(workingDetailsMoney($contractTotal), "existing-" . $serviceId . "-contract_total_cost", "Total Cost"); ?>
                                        </td>
                                        <td data-name="contract_installment_details">
                                            <?php echo workingDetailsDraftTextarea($contractInstallments, "existing-" . $serviceId . "-contract_installment_details", "Installment Details"); ?>
                                        </td>
                                        <td data-name="cb_amount">
                                            <?php echo workingDetailsDraftInput(workingDetailsMoney($cbAmount), "existing-" . $serviceId . "-cb_amount", "CB Amount"); ?>
                                        </td>
                                        <td data-name="consultant_amount">
                                            <?php echo workingDetailsDraftInput(workingDetailsMoney($consultantAmount), "existing-" . $serviceId . "-consultant_amount", "Consultant Amount"); ?>
                                        </td>
                                        <td data-name="contract_thanks_mail_date">
                                            <?php echo workingDetailsDraftInput(workingDetailsValue($customer, 'contract_thanks_mail_date'), "existing-" . $serviceId . "-contract_thanks_mail_date", "DD.MM.YYYY"); ?>
                                        </td>
                                        <td data-name="received_cb">
                                            <?php echo workingDetailsDraftInput(workingDetailsMoney(workingDetailsValue($cbSummary, 'payment_received')), "existing-" . $serviceId . "-received_cb", "Received CB"); ?>
                                        </td>
                                        <td data-name="received_consultant">
                                            <?php echo workingDetailsDraftInput(workingDetailsMoney(workingDetailsValue($consultingSummary, 'consulting_payment_received')), "existing-" . $serviceId . "-received_consultant", "Received Consultant"); ?>
                                        </td>
                                        <td data-name="remaining_cb">
                                            <?php echo workingDetailsDraftInput(workingDetailsMoney(workingDetailsValue($cbSummary, 'balance')), "existing-" . $serviceId . "-remaining_cb", "Remaining CB"); ?>
                                        </td>
                                        <td data-name="remaining_consultant">
                                            <?php echo workingDetailsDraftInput(workingDetailsMoney(workingDetailsValue($consultingSummary, 'consulting_balance')), "existing-" . $serviceId . "-remaining_consultant", "Remaining Consultant"); ?>
                                        </td>
                                        <td data-name="project_start">
                                            <?php echo workingDetailsDraftInput(simpleDateConversion(workingDetailsValue($customer, 'created_at')), "existing-" . $serviceId . "-project_start", "DD.MM.YYYY"); ?>
                                        </td>
                                        <td data-name="consultant">
                                            <div class="table-edit-control">
                                                <select class="workingConsultant" id="workingConsultant" name="workingConsultant" data-custome="<?php echo workingDetailsValue($customer, "customer_id"); ?>" data-service="<?php echo $serviceId; ?>" >
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
                                        <td data-name="total_visit">
                                            <?php echo workingDetailsDraftInput($totalVisits, "existing-" . $serviceId . "-total_visit", "Total Visit"); ?>
                                        </td>
                                        <td data-name="month_visit">
                                            <?php echo workingDetailsDraftInput($monthVisits, "existing-" . $serviceId . "-month_visit", "Month Visit"); ?>
                                        </td>
                                        <td data-name="audit_date">
                                            <div class="table-edit-control">
                                                <small><input type="text" class="workingAuditDate sheet-cell-input" name="workingAuditDate" data-custome="<?php echo workingDetailsValue($customer, "customer_id"); ?>" data-service="<?php echo $serviceId; ?>"  value="<?php echo simpleDateConversion(workingDetailsValue($customer, 'audit_date')); ?>" placeholder="" ></small>
                                            </div>
                                        </td>
                                        <td data-name="project_status">
                                            <?php echo workingDetailsDraftInput(workingDetailsValue($customer, 'add_service_to'), "existing-" . $serviceId . "-project_status", "Project Status"); ?>
                                        </td>
                                        <td data-name="other_comments">
                                            <div class="table-edit-control">
                                                <textarea data-row-id="" rows="3" id="autosavecomments" class="autosavecomments saveChanges customComments" data-row="<?php echo workingDetailsValue($customer, 'customer_id'); ?>" name="payComments" value="" placeholder="" ><?php if (isset($customer['comments'])) echo $customer['comments'] ?></textarea>
                                            </div>
                                        </td>
                                        <td data-name="guidelines_notes">
                                            <div class="table-edit-control">
                                                <?php echo workingDetailsDraftTextarea(workingDetailsValue($customer, 'guidelines_notes'), "existing-" . $serviceId . "-guidelines_notes", "Guidelines / Notes"); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php for ($blankRow = 1; $blankRow <= 25; $blankRow++) { ?>
                                    <tr class="blank-entry-row">
                                        <td data-name="sr"><small><?php echo $count + $blankRow; ?></small></td>
                                        <td data-name="service_type"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-service_type", "Service Type"); ?></td>
                                        <td data-name="standard"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-standard", "Standard"); ?></td>
                                        <td data-name="company_name"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-company_name", "Company Name"); ?></td>
                                        <td data-name="service_firm"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-service_firm", "Service Firm"); ?></td>
                                        <td data-name="contract_total_cost"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-contract_total_cost", "Total Cost"); ?></td>
                                        <td data-name="contract_installment_details"><?php echo workingDetailsDraftTextarea("", "blank-" . $blankRow . "-contract_installment_details", "Installment Details"); ?></td>
                                        <td data-name="cb_amount"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-cb_amount", "CB Amount"); ?></td>
                                        <td data-name="consultant_amount"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-consultant_amount", "Consultant Amount"); ?></td>
                                        <td data-name="contract_thanks_mail_date"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-contract_thanks_mail_date", "DD.MM.YYYY"); ?></td>
                                        <td data-name="received_cb"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-received_cb", "Received CB"); ?></td>
                                        <td data-name="received_consultant"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-received_consultant", "Received Consultant"); ?></td>
                                        <td data-name="remaining_cb"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-remaining_cb", "Remaining CB"); ?></td>
                                        <td data-name="remaining_consultant"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-remaining_consultant", "Remaining Consultant"); ?></td>
                                        <td data-name="project_start"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-project_start", "DD.MM.YYYY"); ?></td>
                                        <td data-name="consultant"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-consultant", "Consultant"); ?></td>
                                        <td data-name="total_visit"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-total_visit", "Total Visit"); ?></td>
                                        <td data-name="month_visit"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-month_visit", "Month Visit"); ?></td>
                                        <td data-name="audit_date"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-audit_date", "DD.MM.YYYY"); ?></td>
                                        <td data-name="project_status"><?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-project_status", "Project Status"); ?></td>
                                        <td data-name="other_comments"><?php echo workingDetailsDraftTextarea("", "blank-" . $blankRow . "-other_comments", "Other Comments"); ?></td>
                                        <td data-name="guidelines_notes"><?php echo workingDetailsDraftTextarea("", "blank-" . $blankRow . "-guidelines_notes", "Guidelines / Notes"); ?></td>
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
                    var certificationStr = data[2] || "";
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
                    var consultantStr = data[15] || "";
//                        alert(consultantStr);
                    var dateSplit = consultantStr.split("~");
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
                "targets": "_all",
                "className": "dt-head-center dt-body-left"
            },
        ]
                //        "paging":         false
    });

    table.columns.adjust().draw();

    var workingDraftPrefix = 'workingDetailsTestDraft:';
    var blankRowCounter = $('#table_working_details tbody tr').length;
    var blankColumns = [
        ['service_type', 'Service Type', 'input'],
        ['standard', 'Standard', 'input'],
        ['company_name', 'Company Name', 'input'],
        ['service_firm', 'Service Firm', 'input'],
        ['contract_total_cost', 'Total Cost', 'input'],
        ['contract_installment_details', 'Installment Details', 'textarea'],
        ['cb_amount', 'CB Amount', 'input'],
        ['consultant_amount', 'Consultant Amount', 'input'],
        ['contract_thanks_mail_date', 'DD.MM.YYYY', 'input'],
        ['received_cb', 'Received CB', 'input'],
        ['received_consultant', 'Received Consultant', 'input'],
        ['remaining_cb', 'Remaining CB', 'input'],
        ['remaining_consultant', 'Remaining Consultant', 'input'],
        ['project_start', 'DD.MM.YYYY', 'input'],
        ['consultant', 'Consultant', 'input'],
        ['total_visit', 'Total Visit', 'input'],
        ['month_visit', 'Month Visit', 'input'],
        ['audit_date', 'DD.MM.YYYY', 'input'],
        ['project_status', 'Project Status', 'input'],
        ['other_comments', 'Other Comments', 'textarea'],
        ['guidelines_notes', 'Guidelines / Notes', 'textarea']
    ];

    function escapeSheetValue(value) {
        return $('<div>').text(value || '').html();
    }

    function loadWorkingDrafts(scope) {
        $(scope).find('.sheet-draft-input').each(function () {
            var draftKey = $(this).attr('data-draft-key');
            var savedValue = localStorage.getItem(workingDraftPrefix + draftKey);
            if (savedValue !== null) {
                $(this).val(savedValue).addClass('sheet-dirty');
            }
        });
    }

    function buildBlankRow(rowNumber) {
        var rowKey = 'manual-' + rowNumber;
        var html = '<tr class="blank-entry-row"><td data-name="sr"><small>' + rowNumber + '</small></td>';
        for (var i = 0; i < blankColumns.length; i++) {
            var column = blankColumns[i];
            var draftKey = rowKey + '-' + column[0];
            html += '<td data-name="' + column[0] + '">';
            if (column[2] === 'textarea') {
                html += '<textarea rows="3" class="sheet-cell-input sheet-draft-input" data-draft-key="' + draftKey + '" placeholder="' + escapeSheetValue(column[1]) + '"></textarea>';
            } else {
                html += '<input type="text" class="sheet-cell-input sheet-draft-input" data-draft-key="' + draftKey + '" placeholder="' + escapeSheetValue(column[1]) + '" />';
            }
            html += '</td>';
        }
        html += '</tr>';
        return $(html);
    }

    loadWorkingDrafts(document);

    $(document).on('input change', '.sheet-draft-input', function () {
        var draftKey = $(this).attr('data-draft-key');
        localStorage.setItem(workingDraftPrefix + draftKey, $(this).val());
        $(this).addClass('sheet-dirty');
    });

    $('.add-entry-row').on('click', function () {
        for (var i = 0; i < 5; i++) {
            blankRowCounter++;
            var row = buildBlankRow(blankRowCounter);
            table.row.add(row[0]).draw(false);
        }
        table.columns.adjust().draw(false);
    });

    $(document).on('keydown', '#table_working_details input, #table_working_details select, #table_working_details textarea', function (e) {
        if (e.key !== 'Enter') {
            return;
        }
        if ($(this).is('textarea') && e.shiftKey) {
            return;
        }
        e.preventDefault();
        var cells = $('#table_working_details').find('input:visible, select:visible, textarea:visible').filter(':not([readonly])');
        var currentIndex = cells.index(this);
        if (currentIndex > -1 && currentIndex + 1 < cells.length) {
            cells.eq(currentIndex + 1).focus().select();
        }
    });

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
