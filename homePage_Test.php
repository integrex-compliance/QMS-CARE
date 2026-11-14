<div id="wrapper" class="active">
    <!-- Sidebar -->

    <?php
    $pageIdentifier = "Home Page";
    include_once './header.php';
    include_once './dbConnect.php';    

    $listOfCustomers = [];
    $tableData = [];
    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        $db->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED ;');

        $allCustomers = <<<EOT
SELECT cd.company_name, cd.customer_id, cd.priority, cd.contact_person, cd.contact_no, cd.client_reference, cd.next_service_date, cd.certification_date,
	s.add_service_to, s.service_type, s.cert_body
FROM customer_details as cd 
LEFT JOIN servicesMV s ON cd.customer_id = s.customer_id
WHERE cd.is_delete <> 'Y' and cd.company_name not like 'consl. %'
EOT;
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll(PDO::FETCH_ASSOC);

        $count = 0;
        foreach ($listOfCustomers as $customer) {
            $count++;
            $cid = $customer['customer_id'];
            $prio = trim($customer["priority"] ?? '');
            $certDate = !empty($customer["certification_date"]) ? simpleDateConversion($customer["certification_date"]) : '';
            $flatDate = !empty($customer['next_service_date']) ? getFlatDate($customer['next_service_date']) : '';
            $convNext = !empty($customer['next_service_date']) ? simpleDateConversion($customer['next_service_date']) : '';
            $currStatus = !empty($customer['add_service_to']) ? ($customer['add_service_to'] . '-' . $customer['service_type']) : '';

            $statusClass = 'status-default';
            if (!empty($currStatus)) {
                $statusLower = strtolower($currStatus);
                if (strpos($statusLower, 'complete') !== false) {
                    $statusClass = 'status-complete';
                } else if (strpos($statusLower, 'terminate') !== false || strpos($statusLower, 'cancel') !== false) {
                    $statusClass = 'status-terminate';
                } else if (strpos($statusLower, 'pend') !== false || strpos($statusLower, 'wait') !== false || strpos($statusLower, 'due') !== false) {
                    $statusClass = 'status-pending';
                } else {
                    $statusClass = 'status-active';
                }
            }

            $tableData[] = [
                'id' => $cid,
                'count' => $count,
                'prio' => $prio,
                'name' => $customer['company_name'],
                'person' => $customer['contact_person'] ?? '',
                'phone' => $customer['contact_no'] ?? '',
                'ref' => $customer['client_reference'] ?? '',
                'cb' => $customer['cert_body'] ?? '',
                'cert_date' => $certDate,
                'next_date' => $convNext,
                'flat_date' => $flatDate,
                'service_type' => $customer['service_type'] ?? '',
                'status' => $currStatus,
                'status_class' => $statusClass
            ];
        }
    }
    ?>

    <!-- Modern Clean Styles for Customer Directory -->
    <style>
        #page-content-wrapper {
            background: #f1f5f9 !important;
            min-height: 100vh;
            padding-top: 15px;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        .custom-page-container {
            width: 100%;
            padding-left: 20px;
            padding-right: 20px;
            margin: 0 auto;
        }

        /* 1-Line Compact Filter Strip */
        .filter-strip-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 14px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-strip-label {
            font-size: 11.5px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .filter-strip-label i {
            color: #2563eb;
        }

        .filter-input-compact {
            height: 32px !important;
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 11.5px !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            box-shadow: none !important;
            transition: all 0.15s ease;
        }

        .filter-input-compact:hover {
            border-color: #94a3b8 !important;
            background: #ffffff !important;
        }

        .filter-input-compact:focus {
            background: #ffffff !important;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
            outline: none !important;
        }

        select.filter-input-compact {
            cursor: pointer;
            width: auto;
            min-width: 155px;
        }

        input.filter-input-compact {
            width: 140px;
        }

        .btn-reset-compact {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            height: 32px;
            padding: 0 12px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
            white-space: nowrap;
            margin-left: auto;
        }

        .btn-reset-compact:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25);
        }

        /* 3. Table Card Wrapper & Loading Overlay */
        .table-card-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 25px;
            position: relative;
            min-height: 420px;
        }

        .table-loader-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(3px);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .loader-spinner-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 36px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            text-align: center;
        }

        .modern-spinner {
            width: 42px;
            height: 42px;
            border: 3.5px solid #e2e8f0;
            border-top: 3.5px solid #2563eb;
            border-right: 3.5px solid #2563eb;
            border-radius: 50%;
            animation: modernSpin 0.75s linear infinite;
            margin-bottom: 12px;
        }

        @keyframes modernSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loader-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .loader-desc {
            font-size: 11.5px;
            color: #64748b;
            font-weight: 500;
        }

        /* DataTables Controls */
        .dataTables_wrapper {
            padding: 0;
            position: relative;
            clear: both;
        }

        .dataTables_wrapper .dataTables_filter {
            padding: 10px 16px 8px;
            float: right;
        }

        .dataTables_wrapper .dataTables_filter label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
        }

        .dataTables_wrapper .dataTables_filter input {
            height: 32px;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            padding: 4px 12px 4px 28px;
            font-size: 12px;
            color: #1e293b;
            background: #f8fafc url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="%2364748b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>') no-repeat 9px center;
            outline: none;
            width: 220px;
            margin-left: 0 !important;
            transition: all 0.2s ease;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            width: 260px;
        }

        .dataTables_wrapper .dataTables_info {
            padding: 8px 16px;
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        /* Header Alignment in DataTables & Pre-rendered Table */
        .dataTables_scrollHead,
        #tab_customer_list thead {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%) !important;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            border-bottom: 2px solid #3b82f6 !important;
            overflow: hidden !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .dataTables_scrollHead table {
            margin: 0 !important;
            background: transparent !important;
            border-collapse: collapse !important;
        }

        .dataTables_scrollHead table thead th,
        #tab_customer_list thead th {
            background: transparent !important;
            color: #f8fafc !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 8px 6px !important;
            border: none !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
            line-height: normal !important;
            box-sizing: border-box !important;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.25);
        }

        .dataTables_scrollHead table thead th:last-child,
        #tab_customer_list thead th:last-child {
            border-right: none !important;
        }

        /* Hide dummy cloned thead inside scroll body */
        .dataTables_scrollBody table thead,
        .dataTables_scrollBody table thead tr,
        .dataTables_scrollBody table thead th {
            height: 0px !important;
            max-height: 0px !important;
            min-height: 0px !important;
            padding: 0px !important;
            margin: 0px !important;
            border: none !important;
            font-size: 0px !important;
            line-height: 0px !important;
            color: transparent !important;
            background: transparent !important;
            visibility: hidden !important;
            overflow: hidden !important;
        }

        /* Table Body Styling */
        #tab_customer_list {
            margin: 0 !important;
            border-collapse: collapse !important;
            min-width: 100%;
        }

        #tab_customer_list tbody tr {
            cursor: pointer;
            transition: background-color 0.15s ease;
            background-color: #ffffff;
        }

        #tab_customer_list tbody tr:nth-child(even) td {
            background-color: #f8fafc !important;
        }

        #tab_customer_list tbody tr:nth-child(odd) td {
            background-color: #ffffff !important;
        }

        #tab_customer_list tbody tr:hover td {
            background-color: #e0f2fe !important;
        }

        /* All body cells must remain clear, horizontal, and never wrap - compact density */
        #tab_customer_list tbody td {
            padding: 5px 6px !important;
            font-size: 12px !important;
            color: #1e293b !important;
            border: none !important;
            border-bottom: 1px solid #e9eef5 !important;
            border-right: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
        }

        #tab_customer_list tbody td:last-child {
            border-right: none !important;
        }

        /* OVERRIDE: Prevent app.css from hiding spans */
        #tab_customer_list span,
        .dataTables_wrapper span {
            display: inline-block !important;
        }

        /* Only hide the raw flat-date */
        #tab_customer_list .flat-date-hide {
            display: none !important;
            visibility: hidden !important;
            position: absolute !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
        }

        /* Link Styling */
        .customer-link {
            font-weight: 600;
            color: #1d4ed8 !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            font-size: 12px;
            cursor: pointer;
            display: inline-block;
            max-width: 210px;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            transition: color 0.15s ease;
        }

        .customer-link:hover {
            color: #0284c7 !important;
            text-decoration: underline !important;
        }

        .person-text {
            display: inline-block;
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
            color: #334155;
        }

        .phone-text {
            display: inline-block;
            max-width: 110px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
            color: #334155;
        }

        .ref-by-text {
            display: inline-block;
            max-width: 125px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
            color: #64748b;
        }

        /* Badges - Compact */
        .prio-pill {
            display: inline-block !important;
            padding: 1.5px 5px !important;
            border-radius: 8px !important;
            font-size: 9.5px !important;
            font-weight: 700 !important;
            letter-spacing: 0.2px !important;
            text-transform: uppercase !important;
            white-space: nowrap !important;
            text-align: center;
        }
        .prio-high { background: #fee2e2 !important; color: #b91c1c !important; border: 1px solid #fca5a5 !important; }
        .prio-med  { background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fcd34d !important; }
        .prio-low  { background: #dcfce7 !important; color: #15803d !important; border: 1px solid #86efac !important; }
        .prio-none { color: #cbd5e1 !important; }

        .chip-cb {
            display: inline-block !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            background: #eef2ff !important;
            color: #4338ca !important;
            border: 1px solid #c7d2fe !important;
            white-space: nowrap !important;
        }

        .chip-service {
            display: inline-block !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            background: #f0fdfa !important;
            color: #0d9488 !important;
            border: 1px solid #99f6e4 !important;
            white-space: nowrap !important;
        }

        .status-pill {
            display: inline-block !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            text-align: center;
        }
        .status-complete  { background: #ecfdf5 !important; color: #047857 !important; border: 1px solid #6ee7b7 !important; }
        .status-terminate { background: #fff1f2 !important; color: #be123c !important; border: 1px solid #fda4af !important; }
        .status-pending   { background: #fff7ed !important; color: #c2410c !important; border: 1px solid #fdba74 !important; }
        .status-active    { background: #eff6ff !important; color: #1d4ed8 !important; border: 1px solid #93c5fd !important; }
        .status-default   { background: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1 !important; }

        /* Highlight Visited Customer Row */
        .highlight-visited td {
            background-color: #fef3c7 !important;
            color: #78350f !important;
            transition: background-color 2s ease;
        }
        .highlight-visited td:first-child {
            border-left: 4px solid #f59e0b !important;
        }

        /* Scrollbar */
        .dataTables_scrollBody::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .dataTables_scrollBody::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .dataTables_scrollBody::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <div id="page-content-wrapper">
        <div class="custom-page-container">

            <!-- 1-Line Compact Filter Strip -->
            <div class="filter-strip-card">
                <span class="filter-strip-label">
                    <i class="fa fa-sliders"></i> Filters:
                </span>

                <select name="service-type" class="form-control filter-input-compact service-type" id="service-type" title="Filter by Service Type">
                    <option value="">All Service Types</option>
                    <?php foreach ($services as $service) { ?>
                        <option value="<?php echo htmlspecialchars($service['service']); ?>">
                            <?php echo htmlspecialchars($service['service']); ?>
                        </option>
                    <?php } ?>
                </select>

                <select name="certificate-body" class="form-control filter-input-compact certificate-body" id="certificate-body" title="Filter by Certification Body">
                    <option value="">All CBs</option>
                    <?php foreach ($certList as $cbs) { ?>
                        <option value="<?php echo htmlspecialchars($cbs['cert']); ?>">
                            <?php echo htmlspecialchars($cbs['cert']); ?>
                        </option>
                    <?php } ?>
                </select>

                <input type="text" class="filterFromDate filter-input-compact" id="filterFromDate" name="filterFromDate" placeholder="Due From: DD.MM.YYYY" autocomplete="off" title="Filter by Next Service Due From Date">

                <input type="text" class="filterToDate filter-input-compact" id="filterToDate" name="filterToDate" placeholder="Due To: DD.MM.YYYY" autocomplete="off" title="Filter by Next Service Due To Date">

                <button type="button" class="btn-reset-compact" id="resetFilters" title="Clear all filters">
                    <i class="fa fa-refresh"></i> Reset Filters
                </button>
            </div>

            <!-- 3. Table Card Wrapper -->
            <div class="table-card-wrapper">
                <!-- Modern Loading Spinner Overlay -->
                <div id="tableLoaderOverlay" class="table-loader-overlay">
                    <div class="loader-spinner-box">
                        <div class="modern-spinner"></div>
                        <div class="loader-title">Loading Customer Directory</div>
                        <div class="loader-desc">Please wait, preparing accounts & service schedules...</div>
                    </div>
                </div>

                <table class="table table-hover" id="tab_customer_list">
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 40px;">Sr.</th>
                            <th style="text-align: center; width: 55px;">Prio</th>
                            <th style="text-align: left; width: 210px;">Customer</th>
                            <th style="text-align: left; width: 130px;">Contact Person</th>
                            <th style="text-align: left; width: 110px;">Phone No.</th>
                            <th style="text-align: left; width: 125px;">Ref By</th>
                            <th style="text-align: center; width: 85px;">CB</th>
                            <th style="text-align: center; width: 95px;">CA/RC Date</th>
                            <th style="text-align: center; width: 120px;">Next Service Due</th>
                            <th style="text-align: center; width: 85px;">Next Type</th>
                            <th style="text-align: center; width: 145px;">Current Status</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    var allCustomerData = <?php echo json_encode($tableData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);

    function compareMinDates(filterDate, rowDate) {
        if (!filterDate || !rowDate) return true;
        var filterDateSplit = filterDate.split(".");
        var rowDateSplit = rowDate.split(".");
        if (filterDateSplit.length < 3 || rowDateSplit.length < 3) return true;
        if (filterDateSplit[2] < rowDateSplit[2]) {
            return true;
        } else if (filterDateSplit[2] == rowDateSplit[2]) {
            if (filterDateSplit[1] < rowDateSplit[1]) {
                return true;
            } else if (filterDateSplit[1] == rowDateSplit[1]) {
                if (filterDateSplit[0] <= rowDateSplit[0]) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    function compareMaxDates(filterDate, rowDate) {
        if (!filterDate || !rowDate) return true;
        var filterDateSplit = filterDate.split(".");
        var rowDateSplit = rowDate.split(".");
        if (filterDateSplit.length < 3 || rowDateSplit.length < 3) return true;
        if (filterDateSplit[2] > rowDateSplit[2]) {
            return true;
        } else if (filterDateSplit[2] == rowDateSplit[2]) {
            if (filterDateSplit[1] > rowDateSplit[1]) {
                return true;
            } else if (filterDateSplit[1] == rowDateSplit[1]) {
                if (filterDateSplit[0] >= rowDateSplit[0]) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    // Custom filtering for Service Type, CB, and Date Range
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var serviceType = $(".service-type").val();
            var certificationBody = $(".certificate-body").val();
            var fromDate = $(".filterFromDate").val();
            var toDate = $(".filterToDate").val();

            if (!serviceType && !certificationBody && !fromDate && !toDate) {
                return true;
            }

            if (serviceType) {
                var servieTypeStr = (data[9] || "").trim();
                if (servieTypeStr.toUpperCase() !== serviceType.toUpperCase().trim()) {
                    return false;
                }
            }

            if (certificationBody) {
                var certBodyStr = (data[6] || "").trim();
                if (certBodyStr.toUpperCase() !== certificationBody.toUpperCase().trim()) {
                    return false;
                }
            }

            if (fromDate || toDate) {
                var dateValue = data[8] || "";
                var dateSplit = dateValue.split("~");
                var targetDate = (dateSplit.length > 1) ? dateSplit[1] : dateSplit[0];
                targetDate = (targetDate || "").trim();

                if (fromDate && !compareMinDates(fromDate, targetDate)) {
                    return false;
                }
                if (toDate && !compareMaxDates(toDate, targetDate)) {
                    return false;
                }
            }

            return true;
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

        $('#filterToDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });
    });

    $(document).ready(function () {
        var table = $('#tab_customer_list').DataTable({
            "data": allCustomerData,
            "deferRender": true,
            "scrollY": '70vh',
            "sScrollX": "100%",
            "scrollCollapse": true,
            "paging": false,
            "ordering": false,
            "autoWidth": false,
            "createdRow": function (row, data, dataIndex) {
                $(row).attr('id', 'cust_row_' + data.id);
                $(row).attr('data-id', data.id);
                $(row).attr('data-addr', 'addr' + data.id);
                $(row).addClass('customerAccount');
            },
            "columns": [
                {
                    "data": "count",
                    "className": "text-center",
                    "render": function (data) {
                        return '<strong>' + data + '</strong>';
                    }
                },
                {
                    "data": "prio",
                    "className": "text-center",
                    "render": function (data) {
                        if (data === "High") return '<span class="prio-pill prio-high">HIGH</span>';
                        if (data === "Medium") return '<span class="prio-pill prio-med">MED</span>';
                        if (data === "Low") return '<span class="prio-pill prio-low">LOW</span>';
                        return '<span class="prio-pill prio-none">—</span>';
                    }
                },
                {
                    "data": "name",
                    "render": function (data, type, row) {
                        var safe = $('<div>').text(data || '').html();
                        return '<a data-id="' + row.id + '" class="customerAccount customer-link" title="' + safe + '">' + safe + '</a>';
                    }
                },
                {
                    "data": "person",
                    "render": function (data) {
                        if (!data) return '—';
                        var safe = $('<div>').text(data).html();
                        return '<span class="person-text" title="' + safe + '">' + safe + '</span>';
                    }
                },
                {
                    "data": "phone",
                    "render": function (data) {
                        if (!data) return '—';
                        var safe = $('<div>').text(data).html();
                        return '<span class="phone-text" title="' + safe + '">' + safe + '</span>';
                    }
                },
                {
                    "data": "ref",
                    "render": function (data) {
                        if (!data) return '—';
                        var safe = $('<div>').text(data).html();
                        return '<span class="ref-by-text" title="' + safe + '">' + safe + '</span>';
                    }
                },
                {
                    "data": "cb",
                    "className": "text-center",
                    "render": function (data) {
                        if (!data) return '<span style="color: #94a3b8;">—</span>';
                        var safe = $('<div>').text(data).html();
                        return '<span class="chip-cb">' + safe + '</span>';
                    }
                },
                {
                    "data": "cert_date",
                    "className": "text-center",
                    "render": function (data) {
                        if (!data) return '<span style="color: #94a3b8;">—</span>';
                        return '<span style="color: #475569; font-size: 11.5px;">' + data + '</span>';
                    }
                },
                {
                    "data": null,
                    "className": "text-center",
                    "render": function (data, type, row) {
                        if (!row.next_date) return '<span style="color: #94a3b8;">—</span>';
                        return '<span class="flat-date-hide">' + (row.flat_date || '') + '~</span><strong style="color: #0f172a; font-size: 11.5px;">' + row.next_date + '</strong>';
                    }
                },
                {
                    "data": "service_type",
                    "className": "text-center",
                    "render": function (data) {
                        if (!data) return '<span style="color: #94a3b8;">—</span>';
                        var safe = $('<div>').text(data).html();
                        return '<span class="chip-service">' + safe + '</span>';
                    }
                },
                {
                    "data": null,
                    "className": "text-center",
                    "render": function (data, type, row) {
                        if (!row.status) return '<span style="color: #94a3b8;">—</span>';
                        var safe = $('<div>').text(row.status).html();
                        return '<span class="status-pill ' + row.status_class + '" title="' + safe + '">' + safe + '</span>';
                    }
                }
            ],
            "columnDefs": [
                { "width": "40px", "targets": 0 },
                { "width": "55px", "targets": 1 },
                { "width": "210px", "targets": 2 },
                { "width": "130px", "targets": 3 },
                { "width": "110px", "targets": 4 },
                { "width": "125px", "targets": 5 },
                { "width": "85px", "targets": 6 },
                { "width": "95px", "targets": 7 },
                { "width": "120px", "targets": 8 },
                { "width": "85px", "targets": 9 },
                { "width": "145px", "targets": 10 }
            ],
            "language": {
                "search": "",
                "searchPlaceholder": "Search customer, person, phone...",
                "info": "Showing <strong>_TOTAL_</strong> customer records",
                "infoEmpty": "No matching customers found",
                "infoFiltered": "(filtered from _MAX_ total records)"
            },
            "initComplete": function (settings, json) {
                $('#tableLoaderOverlay').fadeOut(120);
            }
        });

        table.columns.adjust().draw();
        setTimeout(function () {
            table.columns.adjust();
            $('#tableLoaderOverlay').fadeOut(120);
        }, 80);

        $(window).on('resize', function () {
            if (table) {
                table.columns.adjust();
            }
        });

        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");
            if (table) {
                setTimeout(function () {
                    table.columns.adjust();
                }, 300);
            }
        });

        $(document).on('click', '.customerAccount', function () {
            var customerID = $(this).attr('data-id');
            if (customerID) {
                localStorage['visited'] = customerID;
                var url = 'customerAccountDetails.php';
                var form = $('<form action="' + url + '" method="get">' +
                        '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            }
        });

        // Fast & smooth scroll to visited customer
        var yetVisited = localStorage['visited'];
        if (!yetVisited && window.location.hash) {
            var hashMatch = window.location.hash.match(/#addr(\d+)/);
            if (hashMatch) {
                yetVisited = hashMatch[1];
            }
        }

        if (yetVisited) {
            var foundIndex = -1;
            for (var i = 0; i < allCustomerData.length; i++) {
                if (String(allCustomerData[i].id) === String(yetVisited)) {
                    foundIndex = i;
                    break;
                }
            }

            if (foundIndex !== -1) {
                if (window.location.hash !== '#addr' + yetVisited) {
                    if (history.replaceState) {
                        history.replaceState(null, null, '#addr' + yetVisited);
                    } else {
                        window.location.hash = '#addr' + yetVisited;
                    }
                }
                var scrollBody = $('.dataTables_scrollBody');
                if (scrollBody.length) {
                    setTimeout(function () {
                        scrollBody.scrollTop(foundIndex * 31);
                        setTimeout(function () {
                            var targetRow = $('#cust_row_' + yetVisited);
                            if (targetRow.length) {
                                targetRow.addClass('highlight-visited');
                                setTimeout(function () {
                                    targetRow.removeClass('highlight-visited');
                                }, 2500);
                            }
                        }, 50);
                    }, 80);
                }
            }
        }

        // Live filter triggers
        $('.service-type, .certificate-body, .filterFromDate, .filterToDate').change(function () {
            var fromDate = $(".filterFromDate").val();
            var toDate = $(".filterToDate").val();
            if (fromDate != "" && toDate != "") {
                if (!compareMinDates(fromDate, toDate)) {
                    alert("To Date can't be after From Date");
                    $(".filterToDate").val("");
                }
            }
            table.draw();
        });

        // Reset Filter Action
        $('#resetFilters').click(function () {
            $('.service-type').val('');
            $('.certificate-body').val('');
            $('.filterFromDate').val('');
            $('.filterToDate').val('');
            table.search('').draw();
        });

        $('.newCustomer').click(function () {
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
