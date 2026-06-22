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
        function workingDetailsValue($row, $key)
        {
            return isset($row[$key]) ? $row[$key] : "";
        }

        function workingDetailsMoney($value)
        {
            if ($value === "" || $value === null) {
                return "";
            }
            return number_format((float) $value, 2);
        }

        function workingDetailsEsc($value)
        {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        }

        function workingDetailsDraftInput($value, $draftKey, $placeholder = "")
        {
            return '<input type="text" class="sheet-cell-input sheet-draft-input" data-draft-key="' . workingDetailsEsc($draftKey) . '" value="' . workingDetailsEsc($value) . '" placeholder="' . workingDetailsEsc($placeholder) . '" />';
        }

        function workingDetailsDraftTextarea($value, $draftKey, $placeholder = "")
        {
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
                <div class="dashboard-filter-card">
                    <div class="filter-header">
                        <h4 class="filter-title">
                            <i class="fa fa-filter"></i> Project Filters
                        </h4>
                    </div>
                    <div class="filter-inputs">
                        <div class="filter-group">
                            <span class="filter-label">Service Type</span>
                            <select name="filter-service-type" class="form-control" id="filter-service-type">
                                <option value="">Select Service Type</option>
                                <option value="CA">CA</option>
                                <option value="RC">RC</option>
                                <option value="SA-01">SA-01</option>
                                <option value="SA-02">SA-02</option>
                                <option value="MPCB">MPCB</option>
                                <option value="Factory Audit">Factory Audit</option>
                                <option value="Zed">Zed</option>
                                <option value="NSIC">NSIC</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Standard / Cert Body</span>
                            <select name="filter-standard" class="form-control" id="filter-standard">
                                <option value="">Select Standard</option>
                                <option value="QMS">QMS</option>
                                <option value="IATF">IATF</option>
                                <option value="EMS">EMS</option>
                                <option value="OHSMS">OHSMS</option>
                                <option value="Customer Audit">Customer Audit</option>
                                <option value="Factory License">Factory License</option>
                                <option value="MPCB">MPCB</option>
                                <option value="AS">AS</option>
                                <option value="ISMS">ISMS</option>
                                <option value="CE">CE</option>
                                <option value="Bronze">Bronze</option>
                                <option value="Silver">Silver</option>
                                <option value="Gold">Gold</option>
                                <option value="FSMS">FSMS</option>
                                <option value="EnMS">EnMS</option>
                                <option value="Other">Other</option>
                                <?php foreach ($allCert as $crt) { ?>
                                    <option value="<?php echo $crt['cert'] ?>"><?php echo $crt['cert'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Consultant</span>
                            <select name="filter-consultant" class="form-control" id="filter-consultant">
                                <option value="">Select Consultant</option>
                                <?php foreach ($consultant as $consult) { ?>
                                    <option value="<?php echo $consult["consultant_name"] ?>">
                                        <?php echo $consult["consultant_name"] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Project Status</span>
                            <select name="filter-status" class="form-control" id="filter-status">
                                <option value="">Select Status</option>
                                <option value="Completed">Completed</option>
                                <option value="Idle">Idle</option>
                                <option value="Pay.Pending">Pay.Pending</option>
                                <option value="Working">Working</option>
                                <option value="Initial PP">Initial PP</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="button" class="add-entry-row"><i class="fa fa-plus"></i> Add 5 Rows</button>
                    </div>
                </div>
                <!--                <div class="row">
                                    <div class="col-sm-12 main_content" style="">-->
                <!--                <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">
                                    
                                </div>-->
                <!--                    </div>
                                </div>-->
                <style>
                    /* Custom typography and spreadsheet theme */
                    #table_working_details,
                    .dashboard-filter-card,
                    .add-entry-row {
                        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", Arial, sans-serif;
                    }

                    /* Elegant Top Filter Card */
                    .dashboard-filter-card {
                        background: #ffffff;
                        border: 1px solid #e2e8f0;
                        border-radius: 12px;
                        padding: 16px 24px;
                        margin-bottom: 24px;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 20px;
                        flex-wrap: wrap;
                    }

                    .dashboard-filter-card .filter-header {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                    }

                    .dashboard-filter-card .filter-title {
                        font-size: 16px;
                        font-weight: 700;
                        color: #0f172a;
                        margin: 0;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        border-right: 1px solid #e2e8f0;
                        padding-right: 20px;
                    }

                    .dashboard-filter-card .filter-title i {
                        color: #3b82f6;
                    }

                    .dashboard-filter-card .filter-inputs {
                        display: flex;
                        align-items: center;
                        gap: 16px;
                        flex-grow: 1;
                        flex-wrap: wrap;
                    }

                    .dashboard-filter-card .filter-group {
                        display: flex;
                        flex-direction: column;
                        gap: 6px;
                    }

                    .dashboard-filter-card .filter-label {
                        font-size: 11px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        color: #475569;
                    }

                    .dashboard-filter-card select.form-control {
                        height: 38px;
                        border: 1px solid #cbd5e1;
                        border-radius: 8px;
                        padding: 6px 16px 6px 12px;
                        font-size: 13px;
                        color: #1e293b;
                        background-color: #f8fafc;
                        min-width: 170px;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        font-weight: 500;
                    }

                    .dashboard-filter-card select.form-control:hover {
                        border-color: #94a3b8;
                    }

                    .dashboard-filter-card select.form-control:focus {
                        border-color: #3b82f6;
                        background-color: #ffffff;
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
                        outline: none;
                    }

                    .dashboard-filter-card .filter-actions {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        margin-left: auto;
                    }

                    .add-entry-row {
                        border: 0;
                        border-radius: 8px;
                        padding: 10px 20px;
                        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                        color: #ffffff !important;
                        font-weight: 600;
                        font-size: 13px;
                        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
                        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                        cursor: pointer;
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        white-space: nowrap;
                    }

                    .add-entry-row:hover,
                    .add-entry-row:focus {
                        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3);
                        transform: translateY(-1px);
                        text-decoration: none;
                    }

                    .add-entry-row:active {
                        transform: translateY(0);
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

                    #table_working_details th {
                        border: 1px solid #cbd5e1 !important;
                        vertical-align: middle !important;
                        padding: 8px 10px;
                        color: #1e293b;
                        position: relative;
                    }

                    #table_working_details td {
                        border: 1px solid #cbd5e1 !important;
                        vertical-align: middle !important;
                        padding: 0 !important;
                        color: #1e293b;
                        position: relative;
                    }

                    #table_working_details .table-edit-control {
                        width: 100%;
                        height: 100%;
                        display: block;
                        margin: 0;
                        padding: 0;
                    }

                    #table_working_details td.active-cell {
                        z-index: 9999 !important;
                    }

                    #table_working_details thead th {
                        text-align: center;
                        font-weight: 700;
                        white-space: normal;
                        line-height: 1.3;
                    }


                    .hdr-teal {
                        background-color: #d1f2eb !important;
                        color: #0e6251 !important;
                    }

                    .hdr-green {
                        background-color: #d5f5e3 !important;
                        color: #145a32 !important;
                    }

                    .hdr-yellow {
                        background-color: #fcf3cf !important;
                        color: #7d6608 !important;
                    }

                    .hdr-blue {
                        background-color: #e8f8f5 !important;
                        color: #117864 !important;
                    }

                    .hdr-peach {
                        background-color: #fdebd0 !important;
                        color: #7e5109 !important;
                    }

                    .hdr-pale {
                        background-color: #fdf2e9 !important;
                        color: #6e2c00 !important;
                    }

                    .hdr-grey {
                        background-color: #ebedef !important;
                        color: #2c3e50 !important;
                    }

                    /* Body Column Group Colors - Excel Themed but Modern */
                    #table_working_details tbody td[data-name="sr"],
                    #table_working_details tbody td[data-name="service_type"],
                    #table_working_details tbody td[data-name="standard"],
                    #table_working_details tbody td[data-name="company_name"] {
                        background-color: #f4fbf9;
                    }

                    #table_working_details tbody td[data-name="service_firm"] {
                        background-color: #f2faf5;
                    }

                    #table_working_details tbody td[data-name="contract_total_cost"],
                    #table_working_details tbody td[data-name="contract_installment_details"],
                    #table_working_details tbody td[data-name="cb_amount"],
                    #table_working_details tbody td[data-name="consultant_amount"] {
                        background-color: #fefdf9;
                    }

                    #table_working_details tbody td[data-name="contract_thanks_mail_date"] {
                        background-color: #f6fcfb;
                    }

                    #table_working_details tbody td[data-name="received_cb"],
                    #table_working_details tbody td[data-name="received_consultant"] {
                        background-color: #f2faf5;
                    }

                    #table_working_details tbody td[data-name="remaining_cb"],
                    #table_working_details tbody td[data-name="remaining_consultant"] {
                        background-color: #fffaf5;
                    }

                    #table_working_details tbody td[data-name="project_start"],
                    #table_working_details tbody td[data-name="consultant"],
                    #table_working_details tbody td[data-name="total_visit"],
                    #table_working_details tbody td[data-name="month_visit"] {
                        background-color: #fffaf6;
                    }

                    #table_working_details tbody td[data-name="audit_date"],
                    #table_working_details tbody td[data-name="project_status"],
                    #table_working_details tbody td[data-name="other_comments"],
                    #table_working_details tbody td[data-name="guidelines_notes"] {
                        background-color: #fafbfc;
                    }

                    #table_working_details tbody td {
                        white-space: normal;
                        font-size: 12px;
                    }

                    #table_working_details tbody tr:nth-child(even) td {
                        filter: brightness(0.99);
                        /* subtle zebra striping keeping group colors */
                    }

                    /* Click to Edit Sheet Cell inputs */
                    #table_working_details .sheet-cell-input,
                    #table_working_details select,
                    #table_working_details textarea {
                        width: 100%;
                        height: 100%;
                        min-height: 38px;
                        border: none !important;
                        border-radius: 0 !important;
                        background: transparent;
                        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
                        font-size: 13px;
                        padding: 8px 10px;
                        color: #1e293b;
                        transition: all 0.15s ease;
                        resize: none;
                        display: block;
                        box-sizing: border-box;
                    }

                    #table_working_details td[data-name="company_name"] .sheet-cell-input {
                        font-weight: 600 !important;
                        font-size: 15px !important;
                        color: #0f172a !important;
                    }

                    #table_working_details .sheet-cell-input:hover,
                    #table_working_details select:hover,
                    #table_working_details textarea:hover {
                        background-color: rgba(241, 245, 249, 0.5);
                    }

                    #table_working_details .sheet-cell-input:focus,
                    #table_working_details select:focus,
                    #table_working_details textarea:focus {
                        background-color: #ffffff !important;
                        box-shadow: inset 0 0 0 2px #3b82f6 !important;
                        outline: none !important;
                        resize: vertical;
                    }

                    #table_working_details .sheet-draft-input.sheet-dirty {
                        background-color: #fefdf0 !important;
                    }

                    #table_working_details .sheet-draft-input.sheet-dirty:focus {
                        background-color: #ffffff !important;
                    }

                    #table_working_details .blank-entry-row td {
                        filter: brightness(0.98);
                    }

                    #table_working_details textarea {
                        min-height: 44px;
                    }

                    #table_working_details td[data-name="company_name"] .sheet-cell-input {
                        white-space: normal;
                    }

                    #table_working_details th:nth-child(3),
                    #table_working_details td:nth-child(3) {
                        width: 180px !important;
                        min-width: 180px !important;
                        max-width: 180px !important;
                    }

                    #table_working_details th:nth-child(4),
                    #table_working_details td:nth-child(4) {
                        width: 280px !important;
                        min-width: 280px !important;
                        max-width: 280px !important;
                    }

                    #table_working_details td:nth-child(7) {
                        width: 250px !important;
                        min-width: 250px !important;
                        max-width: 250px !important;
                    }

                    #table_working_details td:nth-child(4) .sheet-cell-input {
                        min-width: 150px;
                    }

                    #table_working_details td:nth-child(7) textarea {
                        min-width: 270px;
                    }

                    #table_working_details td[data-name="contract_installment_details"] textarea {
                        min-height: 58px;
                        line-height: 1.35;
                    }

                    .sheet-col-sr {
                        width: 55px;
                    }

                    .sheet-col-sm {
                        width: 115px;
                    }

                    .sheet-col-md {
                        width: 155px;
                    }

                    .sheet-col-lg {
                        width: 220px;
                    }

                    .sheet-col-company {
                        width: 280px;
                    }

                    .sheet-col-installment {
                        width: 300px;
                    }

                    .sheet-col-xl {
                        width: 280px;
                    }

                    /* Status Badge Styling using attribute selectors */
                    /* Status Badge Styling using attribute selectors */
                    #table_working_details td[data-name="project_status"] input[value="Idle"],
                    #table_working_details td[data-name="project_status"] input[value="idle"] {
                        background-color: #fef2f2 !important;
                        color: #ef4444 !important;
                        font-weight: 700;
                        text-align: center;
                        border-radius: 12px;
                        border: 1px solid #fee2e2;
                    }

                    #table_working_details td[data-name="project_status"] input[value="Working"],
                    #table_working_details td[data-name="project_status"] input[value="working"] {
                        background-color: #f0fdf4 !important;
                        color: #15803d !important;
                        font-weight: 700;
                        text-align: center;
                        border-radius: 12px;
                        border: 1px solid #dcfce7;
                    }

                    #table_working_details td[data-name="project_status"] input[value="Completed"],
                    #table_working_details td[data-name="project_status"] input[value="completed"] {
                        background-color: #eff6ff !important;
                        color: #1d4ed8 !important;
                        font-weight: 700;
                        text-align: center;
                        border-radius: 12px;
                        border: 1px solid #dbeafe;
                    }

                    #table_working_details td[data-name="project_status"] input[value="Pay.Pending"],
                    #table_working_details td[data-name="project_status"] input[value="pay.pending"] {
                        background-color: #fffbeb !important;
                        color: #d97706 !important;
                        font-weight: 700;
                        text-align: center;
                        border-radius: 12px;
                        border: 1px solid #fef3c7;
                    }

                    #table_working_details td[data-name="project_status"] input[value="Initial PP"],
                    #table_working_details td[data-name="project_status"] input[value="initial pp"] {
                        background-color: #f5f3ff !important;
                        color: #7c3aed !important;
                        font-weight: 700;
                        text-align: center;
                        border-radius: 12px;
                        border: 1px solid #ddd6fe;
                    }

                    #table_working_details td[data-name="project_status"] input:focus {
                        background-color: #ffffff !important;
                        color: #1e293b !important;
                        text-align: left;
                        border-radius: 4px;
                    }

                    /* Project Status Selector Styles */
                    .project-status-selector-wrapper {
                        position: relative;
                        width: 100%;
                    }

                    .project-status-display {
                        cursor: pointer;
                        background-color: transparent !important;
                    }

                    .project-status-dropdown {
                        position: absolute;
                        top: 100%;
                        left: 0;
                        right: 0;
                        background: #ffffff;
                        border: 1px solid #cbd5e1;
                        border-radius: 8px;
                        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                        z-index: 1000;
                        max-height: 250px;
                        overflow-y: auto;
                        margin-top: 4px;
                        padding: 4px;
                        width: 150px;
                    }

                    .project-status-option {
                        padding: 8px 12px;
                        cursor: pointer;
                        font-size: 12px;
                        border-bottom: none;
                        transition: all 0.15s ease;
                        background-color: transparent;
                        margin: 2px 0;
                        border-radius: 6px;
                        text-align: left;
                        font-weight: 500;
                        color: #334155;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }

                    .project-status-option:hover {
                        background-color: #f1f5f9;
                        color: #1e293b;
                    }

                    .project-status-option.selected {
                        background-color: #e0f2fe;
                        color: #0369a1;
                        font-weight: 600;
                    }

                    .project-status-option.selected::after {
                        content: "✓";
                        font-weight: bold;
                        color: #0284c7;
                    }

                    /* Service Type Selector Styles */
                    .service-type-selector-wrapper {
                        position: relative;
                        width: 100%;
                    }

                    .service-type-display {
                        cursor: pointer;
                        background-color: transparent !important;
                    }

                    .service-type-dropdown {
                        position: absolute;
                        top: 100%;
                        left: 0;
                        right: 0;
                        background: #ffffff;
                        border: 1px solid #cbd5e1;
                        border-radius: 8px;
                        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                        z-index: 1000;
                        max-height: 250px;
                        overflow-y: auto;
                        margin-top: 4px;
                        padding: 4px;
                    }

                    /* Modern thin scrollbar */
                    .service-type-dropdown::-webkit-scrollbar,
                    .standard-options-container::-webkit-scrollbar,
                    .service-firm-options-container::-webkit-scrollbar {
                        width: 6px;
                    }

                    .service-type-dropdown::-webkit-scrollbar-track,
                    .standard-options-container::-webkit-scrollbar-track,
                    .service-firm-options-container::-webkit-scrollbar-track {
                        background: #f1f5f9;
                        border-radius: 4px;
                    }

                    .service-type-dropdown::-webkit-scrollbar-thumb,
                    .standard-options-container::-webkit-scrollbar-thumb,
                    .service-firm-options-container::-webkit-scrollbar-thumb {
                        background: #cbd5e1;
                        border-radius: 4px;
                    }

                    .service-type-dropdown::-webkit-scrollbar-thumb:hover,
                    .standard-options-container::-webkit-scrollbar-thumb:hover,
                    .service-firm-options-container::-webkit-scrollbar-thumb:hover {
                        background: #94a3b8;
                    }

                    .service-type-option {
                        padding: 8px 12px;
                        cursor: pointer;
                        font-size: 12px;
                        border-bottom: none;
                        transition: all 0.15s ease;
                        background-color: transparent;
                        margin: 2px 0;
                        border-radius: 6px;
                        text-align: left;
                        font-weight: 500;
                        color: #334155;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }

                    .service-type-option:hover {
                        background-color: #f1f5f9;
                        color: #1e293b;
                        transform: none;
                    }

                    .service-type-option.selected {
                        background-color: #e0f2fe;
                        color: #0369a1;
                        font-weight: 600;
                    }

                    .service-type-option.selected::after {
                        content: "✓";
                        font-weight: bold;
                        color: #0284c7;
                    }

                    /* Standard Selector Styles (Multi-select) */
                    .standard-selector-wrapper {
                        position: relative;
                        width: 100%;
                    }

                    .standard-display {
                        cursor: pointer;
                        background-color: transparent !important;
                    }

                    .standard-dropdown,
                    .service-firm-dropdown {
                        position: absolute;
                        top: 100%;
                        left: 0;
                        background: #ffffff;
                        border: 1px solid #cbd5e1;
                        border-radius: 8px;
                        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                        z-index: 1000;
                        max-height: 360px;
                        margin-top: 4px;
                        display: flex;
                        flex-direction: column;
                        padding: 4px;
                        width: 220px;
                    }

                    .standard-search-box,
                    .service-firm-search-box {
                        padding: 6px;
                        border-bottom: 1px solid #f1f5f9;
                    }

                    .standard-search,
                    .service-firm-search {
                        width: 100%;
                        padding: 8px 10px;
                        border: 1px solid #e2e8f0;
                        border-radius: 6px;
                        font-size: 12px;
                        background-color: #f8fafc;
                        transition: all 0.2s ease;
                    }

                    .standard-search:focus,
                    .service-firm-search:focus {
                        outline: none;
                        border-color: #3b82f6;
                        background-color: #ffffff;
                        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
                    }

                    .standard-options-container,
                    .service-firm-options-container {
                        overflow-y: auto;
                        max-height: 200px;
                        padding: 4px 2px;
                    }

                    .standard-option,
                    .service-firm-option {
                        padding: 8px 12px;
                        cursor: pointer;
                        font-size: 12px;
                        border-bottom: none;
                        transition: all 0.15s ease;
                        background-color: transparent;
                        margin: 2px 0;
                        border-radius: 6px;
                        font-weight: 500;
                        color: #334155;
                        position: relative;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }

                    .standard-option:hover,
                    .service-firm-option:hover {
                        background-color: #f1f5f9;
                        color: #1e293b;
                    }

                    .standard-option.selected,
                    .service-firm-option.selected {
                        background-color: #ecfdf5;
                        color: #047857;
                        font-weight: 600;
                    }

                    .standard-option.selected::after,
                    .service-firm-option.selected::after {
                        content: "✓";
                        position: static;
                        transform: none;
                        font-weight: bold;
                        color: #059669;
                    }

                    .standard-actions,
                    .service-firm-actions {
                        display: flex;
                        gap: 6px;
                        padding: 6px;
                        border-top: 1px solid #f1f5f9;
                        background-color: #f8fafc;
                        border-radius: 0 0 6px 6px;
                    }

                    .standard-clear-btn,
                    .standard-done-btn,
                    .service-firm-clear-btn,
                    .service-firm-done-btn {
                        flex: 1;
                        padding: 6px 10px;
                        border: none;
                        border-radius: 6px;
                        font-size: 11px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.15s ease;
                        text-align: center;
                    }

                    .standard-clear-btn,
                    .service-firm-clear-btn {
                        background-color: #fee2e2;
                        color: #ef4444;
                    }

                    .standard-clear-btn:hover,
                    .service-firm-clear-btn:hover {
                        background-color: #fecaca;
                    }

                    .standard-done-btn,
                    .service-firm-done-btn {
                        background-color: #3b82f6;
                        color: white;
                    }

                    .standard-done-btn:hover,
                    .service-firm-done-btn:hover {
                        background-color: #2563eb;
                    }

                    /* Service Firm Selector Styles (Multi-select with space separator) */
                    .service-firm-selector-wrapper {
                        position: relative;
                        width: 100%;
                    }

                    .service-firm-display {
                        cursor: pointer;
                        background-color: transparent !important;
                    }
                </style>

                <div class="row clearfix" style="margin-top:  ">
                    <div class="col-md-12 working-sheet-wrap">
                        <table class="table table-bordered table-hover table-sortable table-striped"
                            id="table_working_details">
                            <colgroup>
                                <col style="width:55px">
                                <col style="width:115px">
                                <col style="width:180px">
                                <col style="width:280px">
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
                                    <th rowspan="2" class="sheet-col-sr hdr-teal"><small>Sr. No.</small></th>
                                    <th rowspan="2" class="sheet-col-sm hdr-teal"><small>Service Type</small></th>
                                    <th rowspan="2" class="sheet-col-sm hdr-teal"><small>Standard</small></th>
                                    <th rowspan="2" class="sheet-col-company hdr-teal"><small>Company Name</small></th>
                                    <th rowspan="2" class="sheet-col-md hdr-green"><small>Service Firm</small></th>
                                    <th colspan="4" class="hdr-yellow"><small>Project Costing (Decided)</small></th>
                                    <th rowspan="2" class="sheet-col-md hdr-blue"><small>Contract Thanks Mail
                                            Date</small></th>
                                    <th colspan="2" class="hdr-green"><small>Received Amount</small></th>
                                    <th colspan="2" class="hdr-peach"><small>Remaining Amount</small></th>
                                    <th rowspan="2" class="sheet-col-md hdr-pale"><small>1st Visit Date (Project
                                            Start)</small></th>
                                    <th colspan="3" class="hdr-pale"><small>Consultant Visits Record</small></th>
                                    <th rowspan="2" class="sheet-col-md hdr-grey"><small>Audit Date (Project
                                            Closing)</small></th>
                                    <th rowspan="2" class="sheet-col-md hdr-grey"><small>Project Status</small></th>
                                    <th rowspan="2" class="sheet-col-xl hdr-grey"><small>Other Comments</small></th>
                                    <th rowspan="2" class="sheet-col-xl hdr-grey"><small>Guidelines / Notes
                                            section</small></th>
                                </tr>
                                <tr>
                                    <th class="sheet-col-sm hdr-yellow"><small>Contract Total Cost</small></th>
                                    <th class="sheet-col-installment hdr-yellow"><small>Contract Installment
                                            Details</small></th>
                                    <th class="sheet-col-sm hdr-yellow"><small>CB Amount</small></th>
                                    <th class="sheet-col-sm hdr-yellow"><small>Consultant Amount</small></th>
                                    <th class="sheet-col-sm hdr-green"><small>CB</small></th>
                                    <th class="sheet-col-sm hdr-green"><small>Consultant</small></th>
                                    <th class="sheet-col-sm hdr-peach"><small>CB</small></th>
                                    <th class="sheet-col-sm hdr-peach"><small>Consultant</small></th>
                                    <th class="sheet-col-md hdr-pale"><small>Consultant</small></th>
                                    <th class="sheet-col-sm hdr-pale"><small>Total Visit</small></th>
                                    <th class="sheet-col-sm hdr-pale"><small>Month Visit</small></th>
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
                                    <tr id='addr0'>
                                        <td data-name="sr">
                                            <small><?php echo $count; ?></small>
                                        </td>
                                        <td data-name="service_type">
                                            <div class="service-type-selector-wrapper">
                                                <input type="text"
                                                    class="sheet-cell-input sheet-draft-input service-type-display"
                                                    data-draft-key="existing-<?php echo $serviceId; ?>-service_type"
                                                    value="<?php echo workingDetailsEsc(workingDetailsValue($customer, 'service_type')); ?>"
                                                    placeholder="Service Type" readonly />
                                                <div class="service-type-dropdown" style="display: none;">
                                                    <div class="service-type-option" data-value="CA">CA</div>
                                                    <div class="service-type-option" data-value="RC">RC</div>
                                                    <div class="service-type-option" data-value="SA-01">SA-01</div>
                                                    <div class="service-type-option" data-value="SA-02">SA-02</div>
                                                    <div class="service-type-option" data-value="MPCB">MPCB</div>
                                                    <div class="service-type-option" data-value="Factory Audit">Factory
                                                        Audit</div>
                                                    <div class="service-type-option" data-value="Zed">Zed</div>
                                                    <div class="service-type-option" data-value="NSIC">NSIC</div>
                                                    <div class="service-type-option" data-value="Other">Other</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-name="standard">
                                            <div class="standard-selector-wrapper">
                                                <textarea class="sheet-cell-input sheet-draft-input standard-display"
                                                    data-draft-key="existing-<?php echo $serviceId; ?>-standard"
                                                    placeholder="Standard"
                                                    readonly><?php echo workingDetailsEsc(workingDetailsValue($customer, 'cert_body')); ?></textarea>
                                                <div class="standard-dropdown" style="display: none;">
                                                    <div class="standard-search-box">
                                                        <input type="text" class="standard-search"
                                                            placeholder="Search standards..." />
                                                    </div>
                                                    <div class="standard-options-container">
                                                        <div class="standard-option" data-value="QMS">QMS</div>
                                                        <div class="standard-option" data-value="IATF">IATF</div>
                                                        <div class="standard-option" data-value="EMS">EMS</div>
                                                        <div class="standard-option" data-value="OHSMS">OHSMS</div>
                                                        <div class="standard-option" data-value="Customer Audit">Customer
                                                            Audit</div>
                                                        <div class="standard-option" data-value="Factory License">Factory
                                                            License</div>
                                                        <div class="standard-option" data-value="MPCB">MPCB</div>
                                                        <div class="standard-option" data-value="AS">AS</div>
                                                        <div class="standard-option" data-value="ISMS">ISMS</div>
                                                        <div class="standard-option" data-value="CE">CE</div>
                                                        <div class="standard-option" data-value="Bronze">Bronze</div>
                                                        <div class="standard-option" data-value="Silver">Silver</div>
                                                        <div class="standard-option" data-value="Gold">Gold</div>
                                                        <div class="standard-option" data-value="FSMS">FSMS</div>
                                                        <div class="standard-option" data-value="EnMS">EnMS</div>
                                                        <div class="standard-option" data-value="Other">Other</div>
                                                    </div>
                                                    <div class="standard-actions">
                                                        <button type="button" class="standard-clear-btn">Clear All</button>
                                                        <button type="button" class="standard-done-btn">Done</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-id="<?php echo workingDetailsValue($customer, 'customer_id'); ?>"
                                            data-name="company_name">
                                            <?php echo workingDetailsDraftTextarea(workingDetailsValue($customer, 'company_name'), "existing-" . $serviceId . "-company_name", "Company Name"); ?>
                                        </td>
                                        <td data-name="service_firm">
                                            <div class="service-firm-selector-wrapper">
                                                <input type="text"
                                                    class="sheet-cell-input sheet-draft-input service-firm-display"
                                                    data-draft-key="existing-<?php echo $serviceId; ?>-service_firm"
                                                    value="<?php echo workingDetailsEsc(workingDetailsValue($customer, 'consulting_firm')); ?>"
                                                    placeholder="Service Firm" readonly />
                                                <div class="service-firm-dropdown" style="display: none;">
                                                    <div class="service-firm-search-box">
                                                        <input type="text" class="service-firm-search"
                                                            placeholder="Search firms..." />
                                                    </div>
                                                    <div class="service-firm-options-container">
                                                        <div class="service-firm-option" data-value="AITF">AITF</div>
                                                        <div class="service-firm-option" data-value="Integrex">Integrex
                                                        </div>
                                                        <div class="service-firm-option" data-value="PMC">PMC</div>
                                                        <div class="service-firm-option" data-value="QMC">QMC</div>
                                                        <div class="service-firm-option" data-value="Rina India">Rina India
                                                        </div>
                                                        <div class="service-firm-option" data-value="TUV India">TUV India
                                                        </div>
                                                        <div class="service-firm-option" data-value="Bureau Veritas">Bureau
                                                            Veritas</div>
                                                        <div class="service-firm-option" data-value="SGS">SGS</div>
                                                        <div class="service-firm-option" data-value="DNV">DNV</div>
                                                        <div class="service-firm-option" data-value="Other">Other</div>
                                                    </div>
                                                    <div class="service-firm-actions">
                                                        <button type="button" class="service-firm-clear-btn">Clear
                                                            All</button>
                                                        <button type="button" class="service-firm-done-btn">Done</button>
                                                    </div>
                                                </div>
                                            </div>
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
                                                <select class="workingConsultant" id="workingConsultant"
                                                    name="workingConsultant"
                                                    data-custome="<?php echo workingDetailsValue($customer, "customer_id"); ?>"
                                                    data-service="<?php echo $serviceId; ?>">
                                                    <option value="<?php
                                                    if (isset($customer['consultant'])) {
                                                        echo $customer['consultant'];
                                                    } else {
                                                        echo "";
                                                    }
                                                    ?>"><?php
                                                    if (isset($customer['consultant']) && !empty($customer['consultant'])) {
                                                        echo $customer['consultant'];
                                                    } else {
                                                        echo "Select";
                                                    }
                                                    ?> </option>
                                                    <option value="">Select</option>
                                                    <?php foreach ($consultant as $consult) { ?>
                                                        <option value="<?php echo $consult["consultant_name"] ?>">
                                                            <?php echo $consult["consultant_name"] ?>
                                                        </option>
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
                                                <small><input type="text" class="workingAuditDate sheet-cell-input"
                                                        name="workingAuditDate"
                                                        data-custome="<?php echo workingDetailsValue($customer, "customer_id"); ?>"
                                                        data-service="<?php echo $serviceId; ?>"
                                                        value="<?php echo simpleDateConversion(workingDetailsValue($customer, 'audit_date')); ?>"
                                                        placeholder=""></small>
                                            </div>
                                        </td>
                                        <td data-name="project_status">
                                            <div class="project-status-selector-wrapper">
                                                <input type="text"
                                                    class="sheet-cell-input sheet-draft-input project-status-display"
                                                    data-draft-key="existing-<?php echo $serviceId; ?>-project_status"
                                                    value="<?php echo workingDetailsEsc(workingDetailsValue($customer, 'add_service_to')); ?>"
                                                    placeholder="Project Status" readonly />
                                                <div class="project-status-dropdown" style="display: none;">
                                                    <div class="project-status-option" data-value="Completed">Completed
                                                    </div>
                                                    <div class="project-status-option" data-value="Idle">Idle</div>
                                                    <div class="project-status-option" data-value="Pay.Pending">Pay.Pending
                                                    </div>
                                                    <div class="project-status-option" data-value="Working">Working</div>
                                                    <div class="project-status-option" data-value="Initial PP">Initial PP
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-name="other_comments">
                                            <div class="table-edit-control">
                                                <textarea data-row-id="" rows="3" id="autosavecomments"
                                                    class="autosavecomments saveChanges customComments"
                                                    data-row="<?php echo workingDetailsValue($customer, 'customer_id'); ?>"
                                                    name="payComments" value="" placeholder=""><?php if (isset($customer['comments']))
                                                        echo $customer['comments'] ?></textarea>
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
                                        <td data-name="service_type">
                                            <div class="service-type-selector-wrapper">
                                                <input type="text"
                                                    class="sheet-cell-input sheet-draft-input service-type-display"
                                                    data-draft-key="blank-<?php echo $blankRow; ?>-service_type" value=""
                                                    placeholder="Service Type" readonly />
                                                <div class="service-type-dropdown" style="display: none;">
                                                    <div class="service-type-option" data-value="CA">CA</div>
                                                    <div class="service-type-option" data-value="RC">RC</div>
                                                    <div class="service-type-option" data-value="SA-01">SA-01</div>
                                                    <div class="service-type-option" data-value="SA-02">SA-02</div>
                                                    <div class="service-type-option" data-value="MPCB">MPCB</div>
                                                    <div class="service-type-option" data-value="Factory Audit">Factory
                                                        Audit</div>
                                                    <div class="service-type-option" data-value="Zed">Zed</div>
                                                    <div class="service-type-option" data-value="NSIC">NSIC</div>
                                                    <div class="service-type-option" data-value="Other">Other</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-name="standard">
                                            <div class="standard-selector-wrapper">
                                                <textarea class="sheet-cell-input sheet-draft-input standard-display"
                                                    data-draft-key="blank-<?php echo $blankRow; ?>-standard"
                                                    placeholder="Standard" readonly></textarea>
                                                <div class="standard-dropdown" style="display: none;">
                                                    <div class="standard-search-box">
                                                        <input type="text" class="standard-search"
                                                            placeholder="Search standards..." />
                                                    </div>
                                                    <div class="standard-options-container">
                                                        <div class="standard-option" data-value="QMS">QMS</div>
                                                        <div class="standard-option" data-value="IATF">IATF</div>
                                                        <div class="standard-option" data-value="EMS">EMS</div>
                                                        <div class="standard-option" data-value="OHSMS">OHSMS</div>
                                                        <div class="standard-option" data-value="Customer Audit">Customer
                                                            Audit</div>
                                                        <div class="standard-option" data-value="Factory License">Factory
                                                            License</div>
                                                        <div class="standard-option" data-value="MPCB">MPCB</div>
                                                        <div class="standard-option" data-value="AS">AS</div>
                                                        <div class="standard-option" data-value="ISMS">ISMS</div>
                                                        <div class="standard-option" data-value="CE">CE</div>
                                                        <div class="standard-option" data-value="Bronze">Bronze</div>
                                                        <div class="standard-option" data-value="Silver">Silver</div>
                                                        <div class="standard-option" data-value="Gold">Gold</div>
                                                        <div class="standard-option" data-value="FSMS">FSMS</div>
                                                        <div class="standard-option" data-value="EnMS">EnMS</div>
                                                        <div class="standard-option" data-value="Other">Other</div>
                                                    </div>
                                                    <div class="standard-actions">
                                                        <button type="button" class="standard-clear-btn">Clear All</button>
                                                        <button type="button" class="standard-done-btn">Done</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-name="company_name">
                                            <?php echo workingDetailsDraftTextarea("", "blank-" . $blankRow . "-company_name", "Company Name"); ?>
                                        </td>
                                        <td data-name="service_firm">
                                            <div class="service-firm-selector-wrapper">
                                                <input type="text"
                                                    class="sheet-cell-input sheet-draft-input service-firm-display"
                                                    data-draft-key="blank-<?php echo $blankRow; ?>-service_firm" value=""
                                                    placeholder="Service Firm" readonly />
                                                <div class="service-firm-dropdown" style="display: none;">
                                                    <div class="service-firm-search-box">
                                                        <input type="text" class="service-firm-search"
                                                            placeholder="Search firms..." />
                                                    </div>
                                                    <div class="service-firm-options-container">
                                                        <div class="service-firm-option" data-value="AITF">AITF</div>
                                                        <div class="service-firm-option" data-value="Integrex">Integrex
                                                        </div>
                                                        <div class="service-firm-option" data-value="PMC">PMC</div>
                                                        <div class="service-firm-option" data-value="QMC">QMC</div>
                                                        <div class="service-firm-option" data-value="Rina India">Rina India
                                                        </div>
                                                        <div class="service-firm-option" data-value="TUV India">TUV India
                                                        </div>
                                                        <div class="service-firm-option" data-value="Bureau Veritas">Bureau
                                                            Veritas</div>
                                                        <div class="service-firm-option" data-value="SGS">SGS</div>
                                                        <div class="service-firm-option" data-value="DNV">DNV</div>
                                                        <div class="service-firm-option" data-value="Other">Other</div>
                                                    </div>
                                                    <div class="service-firm-actions">
                                                        <button type="button" class="service-firm-clear-btn">Clear
                                                            All</button>
                                                        <button type="button" class="service-firm-done-btn">Done</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-name="contract_total_cost">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-contract_total_cost", "Total Cost"); ?>
                                        </td>
                                        <td data-name="contract_installment_details">
                                            <?php echo workingDetailsDraftTextarea("", "blank-" . $blankRow . "-contract_installment_details", "Installment Details"); ?>
                                        </td>
                                        <td data-name="cb_amount">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-cb_amount", "CB Amount"); ?>
                                        </td>
                                        <td data-name="consultant_amount">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-consultant_amount", "Consultant Amount"); ?>
                                        </td>
                                        <td data-name="contract_thanks_mail_date">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-contract_thanks_mail_date", "DD.MM.YYYY"); ?>
                                        </td>
                                        <td data-name="received_cb">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-received_cb", "Received CB"); ?>
                                        </td>
                                        <td data-name="received_consultant">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-received_consultant", "Received Consultant"); ?>
                                        </td>
                                        <td data-name="remaining_cb">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-remaining_cb", "Remaining CB"); ?>
                                        </td>
                                        <td data-name="remaining_consultant">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-remaining_consultant", "Remaining Consultant"); ?>
                                        </td>
                                        <td data-name="project_start">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-project_start", "DD.MM.YYYY"); ?>
                                        </td>
                                        <td data-name="consultant">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-consultant", "Consultant"); ?>
                                        </td>
                                        <td data-name="total_visit">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-total_visit", "Total Visit"); ?>
                                        </td>
                                        <td data-name="month_visit">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-month_visit", "Month Visit"); ?>
                                        </td>
                                        <td data-name="audit_date">
                                            <?php echo workingDetailsDraftInput("", "blank-" . $blankRow . "-audit_date", "DD.MM.YYYY"); ?>
                                        </td>
                                        <td data-name="project_status">
                                            <div class="project-status-selector-wrapper">
                                                <input type="text"
                                                    class="sheet-cell-input sheet-draft-input project-status-display"
                                                    data-draft-key="blank-<?php echo $blankRow; ?>-project_status" value=""
                                                    placeholder="Project Status" readonly />
                                                <div class="project-status-dropdown" style="display: none;">
                                                    <div class="project-status-option" data-value="Completed">Completed
                                                    </div>
                                                    <div class="project-status-option" data-value="Idle">Idle</div>
                                                    <div class="project-status-option" data-value="Pay.Pending">Pay.Pending
                                                    </div>
                                                    <div class="project-status-option" data-value="Working">Working</div>
                                                    <div class="project-status-option" data-value="Initial PP">Initial PP
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-name="other_comments">
                                            <?php echo workingDetailsDraftTextarea("", "blank-" . $blankRow . "-other_comments", "Other Comments"); ?>
                                        </td>
                                        <td data-name="guidelines_notes">
                                            <?php echo workingDetailsDraftTextarea("", "blank-" . $blankRow . "-guidelines_notes", "Guidelines / Notes"); ?>
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
            var filterServiceType = $("#filter-service-type").val() || "";
            var filterStandard = $("#filter-standard").val() || "";
            var filterConsultant = $("#filter-consultant").val() || "";
            var filterStatus = $("#filter-status").val() || "";

            if (filterServiceType === "" && filterStandard === "" && filterConsultant === "" && filterStatus === "") {
                return true;
            }

            var row = settings.aoData[dataIndex].nTr;
            if (!row) {
                return true;
            }

            // 1. Service Type Filter
            if (filterServiceType !== "") {
                var rowServiceType = $(row).find('.service-type-display').val() || "";
                if (rowServiceType.toUpperCase().trim() !== filterServiceType.toUpperCase().trim()) {
                    return false;
                }
            }

            // 2. Standard Filter (supports partial match e.g. "QMS & EMS")
            if (filterStandard !== "") {
                var rowStandard = $(row).find('.standard-display').val() || "";
                if (rowStandard.toUpperCase().indexOf(filterStandard.toUpperCase()) === -1) {
                    return false;
                }
            }

            // 3. Consultant Filter (works for <select> in existing rows and <input> in blank rows)
            if (filterConsultant !== "") {
                var rowConsultant = "";
                var $select = $(row).find('.workingConsultant');
                if ($select.length > 0) {
                    rowConsultant = $select.val() || "";
                } else {
                    rowConsultant = $(row).find('td[data-name="consultant"] input').val() || "";
                }

                if (rowConsultant.toUpperCase().trim() !== filterConsultant.toUpperCase().trim()) {
                    return false;
                }
            }

            // 4. Project Status Filter
            if (filterStatus !== "") {
                var rowStatus = $(row).find('.project-status-display').val() || "";
                if (rowStatus.toUpperCase().trim() !== filterStatus.toUpperCase().trim()) {
                    return false;
                }
            }

            return true;
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
        ['company_name', 'Company Name', 'textarea'],
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

        // Special handling for service_type column
        html += '<td data-name="service_type">';
        html += '<div class="service-type-selector-wrapper">';
        html += '<input type="text" class="sheet-cell-input sheet-draft-input service-type-display" data-draft-key="' + rowKey + '-service_type" placeholder="Service Type" readonly />';
        html += '<div class="service-type-dropdown" style="display: none;">';
        html += '<div class="service-type-option" data-value="CA">CA</div>';
        html += '<div class="service-type-option" data-value="RC">RC</div>';
        html += '<div class="service-type-option" data-value="SA-01">SA-01</div>';
        html += '<div class="service-type-option" data-value="SA-02">SA-02</div>';
        html += '<div class="service-type-option" data-value="MPCB">MPCB</div>';
        html += '<div class="service-type-option" data-value="Factory Audit">Factory Audit</div>';
        html += '<div class="service-type-option" data-value="Zed">Zed</div>';
        html += '<div class="service-type-option" data-value="NSIC">NSIC</div>';
        html += '<div class="service-type-option" data-value="Other">Other</div>';
        html += '</div>';
        html += '</div>';
        html += '</td>';

        // Special handling for standard column (multi-select)
        html += '<td data-name="standard">';
        html += '<div class="standard-selector-wrapper">';
        html += '<textarea class="sheet-cell-input sheet-draft-input standard-display" data-draft-key="' + rowKey + '-standard" placeholder="Standard" readonly></textarea>';
        html += '<div class="standard-dropdown" style="display: none;">';
        html += '<div class="standard-search-box">';
        html += '<input type="text" class="standard-search" placeholder="Search standards..." />';
        html += '</div>';
        html += '<div class="standard-options-container">';
        html += '<div class="standard-option" data-value="QMS">QMS</div>';
        html += '<div class="standard-option" data-value="IATF">IATF</div>';
        html += '<div class="standard-option" data-value="EMS">EMS</div>';
        html += '<div class="standard-option" data-value="OHSMS">OHSMS</div>';
        html += '<div class="standard-option" data-value="Customer Audit">Customer Audit</div>';
        html += '<div class="standard-option" data-value="Factory License">Factory License</div>';
        html += '<div class="standard-option" data-value="MPCB">MPCB</div>';
        html += '<div class="standard-option" data-value="AS">AS</div>';
        html += '<div class="standard-option" data-value="ISMS">ISMS</div>';
        html += '<div class="standard-option" data-value="CE">CE</div>';
        html += '<div class="standard-option" data-value="Bronze">Bronze</div>';
        html += '<div class="standard-option" data-value="Silver">Silver</div>';
        html += '<div class="standard-option" data-value="Gold">Gold</div>';
        html += '<div class="standard-option" data-value="FSMS">FSMS</div>';
        html += '<div class="standard-option" data-value="EnMS">EnMS</div>';
        html += '<div class="standard-option" data-value="Other">Other</div>';
        html += '</div>';
        html += '<div class="standard-actions">';
        html += '<button type="button" class="standard-clear-btn">Clear All</button>';
        html += '<button type="button" class="standard-done-btn">Done</button>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</td>';

        // Rest of the columns (starting from index 2, skipping service_type and standard)
        for (var i = 2; i < blankColumns.length; i++) {
            var column = blankColumns[i];
            var draftKey = rowKey + '-' + column[0];
            html += '<td data-name="' + column[0] + '">';
            if (column[0] === 'service_firm') {
                html += '<div class="service-firm-selector-wrapper">';
                html += '<input type="text" class="sheet-cell-input sheet-draft-input service-firm-display" data-draft-key="' + draftKey + '" placeholder="Service Firm" readonly />';
                html += '<div class="service-firm-dropdown" style="display: none;">';
                html += '<div class="service-firm-search-box"><input type="text" class="service-firm-search" placeholder="Search firms..." /></div>';
                html += '<div class="service-firm-options-container">';
                html += '<div class="service-firm-option" data-value="AITF">AITF</div>';
                html += '<div class="service-firm-option" data-value="Integrex">Integrex</div>';
                html += '<div class="service-firm-option" data-value="PMC">PMC</div>';
                html += '<div class="service-firm-option" data-value="QMC">QMC</div>';
                html += '<div class="service-firm-option" data-value="Rina India">Rina India</div>';
                html += '<div class="service-firm-option" data-value="TUV India">TUV India</div>';
                html += '<div class="service-firm-option" data-value="Bureau Veritas">Bureau Veritas</div>';
                html += '<div class="service-firm-option" data-value="SGS">SGS</div>';
                html += '<div class="service-firm-option" data-value="DNV">DNV</div>';
                html += '<div class="service-firm-option" data-value="Other">Other</div>';
                html += '</div>';
                html += '<div class="service-firm-actions">';
                html += '<button type="button" class="service-firm-clear-btn">Clear All</button>';
                html += '<button type="button" class="service-firm-done-btn">Done</button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            } else if (column[0] === 'project_status') {
                html += '<div class="project-status-selector-wrapper">';
                html += '<input type="text" class="sheet-cell-input sheet-draft-input project-status-display" data-draft-key="' + draftKey + '" placeholder="Project Status" readonly />';
                html += '<div class="project-status-dropdown" style="display: none;">';
                html += '<div class="project-status-option" data-value="Completed">Completed</div>';
                html += '<div class="project-status-option" data-value="Idle">Idle</div>';
                html += '<div class="project-status-option" data-value="Pay.Pending">Pay.Pending</div>';
                html += '<div class="project-status-option" data-value="Working">Working</div>';
                html += '<div class="project-status-option" data-value="Initial PP">Initial PP</div>';
                html += '</div>';
                html += '</div>';
            } else {
                if (column[2] === 'textarea') {
                    html += '<textarea rows="3" class="sheet-cell-input sheet-draft-input" data-draft-key="' + draftKey + '" placeholder="' + escapeSheetValue(column[1]) + '"></textarea>';
                } else {
                    html += '<input type="text" class="sheet-cell-input sheet-draft-input" data-draft-key="' + draftKey + '" placeholder="' + escapeSheetValue(column[1]) + '" />';
                }
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

        // Service Type Dropdown Functionality
        $(document).on('click', '.service-type-display', function (e) {
            e.stopPropagation();
            var $dropdown = $(this).siblings('.service-type-dropdown');
            var $td = $(this).closest('td');

            // Close all other dropdowns and remove active classes
            $('.service-type-dropdown, .standard-dropdown, .service-firm-dropdown').not($dropdown).hide();
            $('td.active-cell').not($td).removeClass('active-cell');

            // Toggle current dropdown
            $dropdown.toggle();

            if ($dropdown.is(':visible')) {
                $td.addClass('active-cell');
            } else {
                $td.removeClass('active-cell');
            }
        });

        $(document).on('click', '.service-type-option', function (e) {
            e.stopPropagation();
            var selectedValue = $(this).attr('data-value');
            var $wrapper = $(this).closest('.service-type-selector-wrapper');
            var $input = $wrapper.find('.service-type-display');

            // Update input value
            $input.val(selectedValue);

            // Trigger change/input events for draft saving
            $input.trigger('input').trigger('change');

            // Mark as selected
            $(this).siblings().removeClass('selected');
            $(this).addClass('selected');

            // Hide dropdown
            $(this).closest('.service-type-dropdown').hide();
            $(this).closest('td').removeClass('active-cell');
        });

        // Mark already selected options on page load
        $('.service-type-display').each(function () {
            var currentValue = $(this).val();
            if (currentValue) {
                $(this).siblings('.service-type-dropdown').find('.service-type-option[data-value="' + currentValue + '"]').addClass('selected');
            }
        });

        // Standard Multi-Select Dropdown Functionality
        $(document).on('click', '.standard-display', function (e) {
            e.stopPropagation();
            var $dropdown = $(this).siblings('.standard-dropdown');
            var $td = $(this).closest('td');

            // Close all other dropdowns and remove active classes
            $('.standard-dropdown, .service-type-dropdown, .service-firm-dropdown').not($dropdown).hide();
            $('td.active-cell').not($td).removeClass('active-cell');

            // Toggle current dropdown
            $dropdown.toggle();

            // Focus on search box when opened
            if ($dropdown.is(':visible')) {
                $td.addClass('active-cell');
                $dropdown.find('.standard-search').focus();
            } else {
                $td.removeClass('active-cell');
            }
        });

        // Standard option click (multi-select)
        $(document).on('click', '.standard-option', function (e) {
            e.stopPropagation();
            $(this).toggleClass('selected');

            // Update input with selected standards
            var $wrapper = $(this).closest('.standard-selector-wrapper');
            var selectedStandards = [];
            $wrapper.find('.standard-option.selected').each(function () {
                selectedStandards.push($(this).attr('data-value'));
            });

            var displayValue = selectedStandards.join(' & ');
            var $input = $wrapper.find('.standard-display');
            $input.val(displayValue);

            // Trigger change/input events for draft saving
            $input.trigger('input').trigger('change');
        });

        // Standard search functionality
        $(document).on('input', '.standard-search', function (e) {
            e.stopPropagation();
            var searchTerm = $(this).val().toLowerCase();
            var $options = $(this).closest('.standard-dropdown').find('.standard-option');

            $options.each(function () {
                var optionText = $(this).text().toLowerCase();
                if (optionText.indexOf(searchTerm) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Clear all selected standards
        $(document).on('click', '.standard-clear-btn', function (e) {
            e.stopPropagation();
            var $wrapper = $(this).closest('.standard-selector-wrapper');
            $wrapper.find('.standard-option').removeClass('selected');
            var $input = $wrapper.find('.standard-display');
            $input.val('');
            $input.trigger('input').trigger('change');
        });

        // Done button - close dropdown
        $(document).on('click', '.standard-done-btn', function (e) {
            e.stopPropagation();
            $(this).closest('.standard-dropdown').hide();
            $(this).closest('td').removeClass('active-cell');
        });

        // Close all dropdowns when clicking outside
        $(document).on('click', function () {
            $('.standard-dropdown, .service-type-dropdown, .service-firm-dropdown, .project-status-dropdown').hide();
            $('td.active-cell').removeClass('active-cell');
        });

        // Prevent dropdowns from closing when clicking inside them
        $(document).on('click', '.standard-dropdown, .service-type-dropdown, .service-firm-dropdown, .project-status-dropdown', function (e) {
            e.stopPropagation();
        });

        // Mark already selected standards on page load
        $('.standard-display').each(function () {
            var currentValue = $(this).val();
            if (currentValue) {
                var standards = currentValue.split(' & ');
                var $dropdown = $(this).siblings('.standard-dropdown');
                standards.forEach(function (std) {
                    $dropdown.find('.standard-option[data-value="' + std.trim() + '"]').addClass('selected');
                });
            }
        });

        // Service Firm Multi-Select Dropdown Functionality
        $(document).on('click', '.service-firm-display', function (e) {
            e.stopPropagation();
            var $dropdown = $(this).siblings('.service-firm-dropdown');
            var $td = $(this).closest('td');

            // Close all other dropdowns and remove active classes
            $('.service-firm-dropdown, .standard-dropdown, .service-type-dropdown').not($dropdown).hide();
            $('td.active-cell').not($td).removeClass('active-cell');

            // Toggle current dropdown
            $dropdown.toggle();

            // Focus on search box when opened
            if ($dropdown.is(':visible')) {
                $td.addClass('active-cell');
                $dropdown.find('.service-firm-search').focus();
            } else {
                $td.removeClass('active-cell');
            }
        });

        // Service firm option click (multi-select)
        $(document).on('click', '.service-firm-option', function (e) {
            e.stopPropagation();
            $(this).toggleClass('selected');

            // Update input with selected firms
            var $wrapper = $(this).closest('.service-firm-selector-wrapper');
            var selectedFirms = [];
            $wrapper.find('.service-firm-option.selected').each(function () {
                selectedFirms.push($(this).attr('data-value'));
            });

            var displayValue = selectedFirms.join(' & ');
            var $input = $wrapper.find('.service-firm-display');
            $input.val(displayValue);

            // Trigger change/input events for draft saving
            $input.trigger('input').trigger('change');
        });

        // Service firm search functionality
        $(document).on('input', '.service-firm-search', function (e) {
            e.stopPropagation();
            var searchTerm = $(this).val().toLowerCase();
            var $options = $(this).closest('.service-firm-dropdown').find('.service-firm-option');

            $options.each(function () {
                var optionText = $(this).text().toLowerCase();
                if (optionText.indexOf(searchTerm) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Clear all selected service firms
        $(document).on('click', '.service-firm-clear-btn', function (e) {
            e.stopPropagation();
            var $wrapper = $(this).closest('.service-firm-selector-wrapper');
            $wrapper.find('.service-firm-option').removeClass('selected');
            var $input = $wrapper.find('.service-firm-display');
            $input.val('');
            $input.trigger('input').trigger('change');
        });

        // Done button - close dropdown
        $(document).on('click', '.service-firm-done-btn', function (e) {
            e.stopPropagation();
            $(this).closest('.service-firm-dropdown').hide();
            $(this).closest('td').removeClass('active-cell');
        });

        // Mark already selected service firms on page load
        $('.service-firm-display').each(function () {
            var currentValue = $(this).val();
            if (currentValue) {
                var firms = currentValue.split(' & ');
                var $dropdown = $(this).siblings('.service-firm-dropdown');
                firms.forEach(function (frm) {
                    $dropdown.find('.service-firm-option[data-value="' + frm.trim() + '"]').addClass('selected');
                });
            }
        });

        // Project Status Dropdown Functionality
        $(document).on('click', '.project-status-display', function (e) {
            e.stopPropagation();
            var $dropdown = $(this).siblings('.project-status-dropdown');
            var $td = $(this).closest('td');

            // Close all other dropdowns and remove active classes
            $('.service-type-dropdown, .standard-dropdown, .service-firm-dropdown, .project-status-dropdown').not($dropdown).hide();
            $('td.active-cell').not($td).removeClass('active-cell');

            // Toggle current dropdown
            $dropdown.toggle();

            if ($dropdown.is(':visible')) {
                $td.addClass('active-cell');
            } else {
                $td.removeClass('active-cell');
            }
        });

        $(document).on('click', '.project-status-option', function (e) {
            e.stopPropagation();
            var selectedValue = $(this).attr('data-value');
            var $wrapper = $(this).closest('.project-status-selector-wrapper');
            var $input = $wrapper.find('.project-status-display');

            // Update input value
            $input.val(selectedValue);

            // Trigger change/input events for draft saving
            $input.trigger('input').trigger('change');

            // Mark as selected
            $(this).siblings().removeClass('selected');
            $(this).addClass('selected');

            // Hide dropdown
            $(this).closest('.project-status-dropdown').hide();
            $(this).closest('td').removeClass('active-cell');
        });

        // Mark already selected options on page load
        $('.project-status-display').each(function () {
            var currentValue = $(this).val();
            if (currentValue) {
                $(this).siblings('.project-status-dropdown').find('.project-status-option[data-value="' + currentValue + '"]').addClass('selected');
            }
        });

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

        $('#filter-service-type, #filter-standard, #filter-consultant, #filter-status').change(function () {
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
        $(".autosavecomments").animate({ scrollTop: $('.autosavecomments').prop("scrollHeight") }, 1000);

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