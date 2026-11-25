<div id="wrapper" class="active">
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "New enq Page";
    include_once './header.php';
    include_once './dbConnect.php';

    $canDelete = (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing());
    $userAbbr = !empty($_SESSION['abbrevation']) ? $_SESSION['abbrevation'] : (!empty($_SESSION['username']) ? $_SESSION['username'] : 'USER');

    $activeTab = isset($_GET['tab']) ? strtolower(trim($_GET['tab'])) : 'referred';
    if (!in_array($activeTab, ['referred', 'jd', 'tel'], true)) {
        $activeTab = 'referred';
    }

    $referredCustomers = [];
    $jdCustomers = [];
    $tcCustomers = [];

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        try {
            $db->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED ;');

            // 1. Referred Enquiries (high performance single-scan query)
            $referredSql = <<<EOT
SELECT 
    cd.customer_id, cd.company_name, cd.company_address, cd.contact_person, cd.contact_no, cd.contact_email, 
    cd.client_reference, cd.priority, cd.comments, cd.next_service_date, cd.certification_date, s.handel_by,
    s.service_id, s.service_type, cd.marketing_audit_type, s.audit_type, cd.is_delete, cd.client_of
FROM customer_details cd
LEFT JOIN (
    SELECT customer_id, MAX(service_id) AS max_sid
    FROM services
    GROUP BY customer_id
) m ON cd.customer_id = m.customer_id
LEFT JOIN services s ON s.service_id = m.max_sid
WHERE cd.is_delete <> 'Y'
  AND cd.company_name NOT LIKE 'consl. %'
  AND IFNULL(cd.client_of, 'Tele Call') NOT IN ('Tele Call', 'Just Dial')
  AND (
      s.service_id IS NULL
      OR (
          IFNULL(s.add_service_to, 'Under Marketing') = 'Under Marketing'
          AND s.service_type NOT IN ('RC', 'SA-1', 'SA-2')
      )
  )
ORDER BY FIELD(cd.priority, 'High', 'Medium', 'Low', ''), cd.customer_id DESC
EOT;
            $statement = $db->prepare($referredSql);
            $statement->execute();
            $referredCustomers = $statement->fetchAll(PDO::FETCH_ASSOC);

            // 2. Just Dial Enquiries (matches legacy justdialEnquiry.php filter rules)
            $jdSql = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, priority, s.service_id, service_type, marketing_audit_type, add_service_to, audit_type, comments, next_service_date, certification_date, handel_by FROM customer_details cd left join services s on cd.customer_id = s.customer_id WHERE client_of = 'Just Dial' and cd.is_delete <> 'Y' order by FIELD(priority, 'High', 'Medium', 'Low', '')";
            $statement = $db->prepare($jdSql);
            $statement->execute();
            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $cust) {
                $svcType = $cust["service_type"] ?? "";
                $addSvcTo = $cust["add_service_to"] ?? "";
                if (($svcType == "CA" || $svcType == "New enq" || $svcType == "" || $svcType == "MPCB" || $svcType == "NSIC") && ($addSvcTo == "Under Marketing" || $addSvcTo == "")) {
                    if (stripos((string)($cust['company_name'] ?? ''), "consl. ") !== 0) {
                        $jdCustomers[] = $cust;
                    }
                }
            }

            // 3. Tele Call Enquiries (matches legacy telecallEnquiry.php filter rules)
            $tcSql = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, priority, s.service_id, service_type, marketing_audit_type, add_service_to, audit_type, comments, next_service_date, certification_date, handel_by FROM customer_details cd left join services s on cd.customer_id = s.customer_id WHERE client_of = 'Tele Call' and cd.is_delete <> 'Y' order by FIELD(priority, 'High', 'Medium', 'Low', '')";
            $statement = $db->prepare($tcSql);
            $statement->execute();
            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $cust) {
                $svcType = $cust["service_type"] ?? "";
                $addSvcTo = $cust["add_service_to"] ?? "";
                if (($svcType == "CA" || $svcType == "New enq" || $svcType == "") && ($addSvcTo == "Under Marketing" || $addSvcTo == "")) {
                    if (stripos((string)($cust['company_name'] ?? ''), "consl. ") !== 0) {
                        $tcCustomers[] = $cust;
                    }
                }
            }
        } catch (Exception $ex) {
            echo '<div class="alert alert-danger">Error loading inquiries: ' . htmlspecialchars($ex->getMessage()) . '</div>';
        }
    }

    $enquiryTabs = [
        'referred' => [
            'label' => 'Referred Enquiry',
            'icon' => 'fa-building',
            'tableId' => 'tab_newEnq_list',
            'paneId' => 'tabPaneReferred',
            'col4Label' => 'Reference By',
            'col4Field' => 'client_reference',
            'rows' => $referredCustomers,
        ],
        'jd' => [
            'label' => 'JD Enquiry',
            'icon' => 'fa-search',
            'tableId' => 'tab_jdEnq_list',
            'paneId' => 'tabPaneJd',
            'col4Label' => 'Location',
            'col4Field' => 'company_address',
            'rows' => $jdCustomers,
        ],
        'tel' => [
            'label' => 'Tel Enquiry',
            'icon' => 'fa-phone',
            'tableId' => 'tab_tcEnq_list',
            'paneId' => 'tabPaneTel',
            'col4Label' => 'Location',
            'col4Field' => 'company_address',
            'rows' => $tcCustomers,
        ],
    ];
    $totalCount = count($enquiryTabs[$activeTab]['rows']);
    ?>

    <!-- Modern Clean Styling for New Enquiries -->
    <style>
        #page-content-wrapper {
            background: #f1f5f9 !important;
            min-height: 100vh;
            padding-top: 15px;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        .enquiry-container {
            width: 100%;
            padding-left: 20px;
            padding-right: 20px;
            margin: 0 auto;
        }

        /* Top Action Toolbar */
        .enquiry-toolbar-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 18px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .page-title-badge {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25);
        }

        .page-title-text h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }

        .count-badge {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 11.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .count-badge i {
            color: #2563eb;
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action-delete {
            background: #ef4444;
            color: #ffffff !important;
            border: 1px solid #dc2626;
            border-radius: 6px;
            height: 32px;
            padding: 0 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(239, 68, 68, 0.2);
            text-decoration: none !important;
        }

        .btn-action-delete:hover {
            background: #dc2626;
            border-color: #b91c1c;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35);
            transform: translateY(-1px);
        }

        .btn-action-delete:active {
            transform: translateY(0);
        }

        /* Table Card & Loader Overlay */
        .table-card-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 25px;
            position: relative;
            min-height: 400px;
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
            transition: opacity 0.25s ease, visibility 0.25s ease;
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
            width: 40px;
            height: 40px;
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

        /* DataTables Controls & Search Box */
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

        /* Table Header: Dark Slate Gradient with Royal Blue Line */
        .dataTables_scrollHead,
        .enq-modern-table thead {
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
        .enq-modern-table thead th {
            background: transparent !important;
            color: #f8fafc !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 10px 8px !important;
            border: none !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
            line-height: normal !important;
            box-sizing: border-box !important;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.25);
        }

        .dataTables_scrollHead table thead th:last-child,
        .enq-modern-table thead th:last-child {
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
        .enq-modern-table {
            margin: 0 !important;
            border-collapse: collapse !important;
            min-width: 100%;
        }

        .enq-modern-table tbody tr {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.15s ease;
        }

        .enq-modern-table tbody tr:nth-of-type(even) {
            background-color: #f8fafc;
        }

        .enq-modern-table tbody tr:hover {
            background-color: #f0f7ff !important;
        }

        .enq-modern-table tbody td {
            padding: 8px 10px !important;
            font-size: 12.5px;
            color: #1e293b;
            vertical-align: middle !important;
            border: none !important;
            border-right: 1px solid #f1f5f9 !important;
            border-bottom: 1px solid #e2e8f0 !important;
            box-sizing: border-box;
        }

        .enq-modern-table tbody td:last-child {
            border-right: none !important;
        }

        /* Force all spans and links in this table to be visible (overrides .table-striped span { display:none } in legacy app.css) */
        .enq-modern-table span,
        .enq-modern-table a,
        .enq-modern-table .customer-link,
        .enq-modern-table .contact-text,
        .enq-modern-table .ref-text,
        .dataTables_scrollBody .enq-modern-table span,
        .dataTables_scrollBody .enq-modern-table a {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Enquiry 3-Tab Switcher */
        .enq-tab-switcher {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 9px;
            border: 1px solid #e2e8f0;
            gap: 4px;
            flex-wrap: wrap;
        }

        .enq-tab-btn {
            border: none;
            background: transparent;
            color: #475569;
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.15s ease;
            outline: none;
        }

        .enq-tab-btn:hover {
            color: #1e293b;
            background: rgba(255, 255, 255, 0.75);
        }

        .enq-tab-btn.active {
            background: #1d4ed8;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.25);
        }

        .enq-tab-count {
            background: rgba(15, 23, 42, 0.1);
            color: inherit;
            font-size: 11px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 10px;
        }

        .enq-tab-btn.active .enq-tab-count {
            background: rgba(255, 255, 255, 0.24);
            color: #ffffff;
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Checkbox styling */
        .enq-checkbox {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
            margin: 0 auto !important;
            display: block;
        }

        .th-checkbox-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Remove sorting arrow from checkbox header */
        .th-checkbox-col:after,
        .dataTables_scrollHead th.th-checkbox-col:after {
            display: none !important;
            content: "" !important;
        }

        /* Priority Select / Badge */
        .customPriority {
            height: 26px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            cursor: pointer;
            width: 100%;
            max-width: 95px;
            transition: all 0.2s ease;
            outline: none;
        }

        .customPriority:focus {
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }

        .prio-high {
            color: #b91c1c !important;
            background-color: #fef2f2 !important;
            border-color: #fecaca !important;
        }

        .prio-medium {
            color: #b45309 !important;
            background-color: #fffbeb !important;
            border-color: #fde68a !important;
        }

        .prio-low {
            color: #475569 !important;
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }

        .prio-none {
            color: #94a3b8 !important;
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        /* Customer Company Link */
        .customer-link {
            color: #0284c7 !important;
            font-weight: 600 !important;
            font-size: 12.5px !important;
            text-decoration: none !important;
            cursor: pointer !important;
            display: inline-block !important;
            line-height: 1.35 !important;
            transition: color 0.15s ease !important;
        }

        .customer-link:hover {
            color: #0369a1 !important;
            text-decoration: underline !important;
        }

        /* Contact & Reference text */
        .contact-text {
            color: #334155 !important;
            font-weight: 500 !important;
            font-size: 12px !important;
            display: inline-block !important;
            word-break: break-word !important;
        }

        .ref-text {
            color: #475569 !important;
            font-size: 12px !important;
            display: inline-block !important;
            word-break: break-word !important;
        }

        .empty-cell-dash {
            color: #94a3b8 !important;
            font-weight: 400 !important;
            font-size: 13px !important;
            display: inline-block !important;
        }

        /* Comments Textarea */
        .customComments {
            width: 100%;
            min-height: 48px;
            max-height: 120px;
            font-family: inherit;
            font-size: 11.5px;
            line-height: 1.4;
            color: #1e293b;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 8px;
            resize: vertical;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.2s ease;
            box-sizing: border-box;
        }

        .customComments:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }

        .customComments.saved-flash {
            border-color: #10b981 !important;
            background-color: #f0fdf4 !important;
        }

        /* Modern Delete Modal */
        #delete-customer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modern-modal-card {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 90%;
            max-width: 400px;
            padding: 24px 20px;
            text-align: center;
            border: 1px solid #e2e8f0;
            animation: modalFadeIn 0.2s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-warning-icon {
            width: 52px;
            height: 52px;
            background: #fef2f2;
            color: #dc2626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 14px;
            border: 1px solid #fecaca;
        }

        .modal-title-text {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .modal-body-text {
            font-size: 12.5px;
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.45;
        }

        .modal-actions-wrap {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn-modal-cancel {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            height: 34px;
            padding: 0 18px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-modal-cancel:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-modal-delete {
            background: #ef4444;
            color: #ffffff;
            border: 1px solid #dc2626;
            border-radius: 6px;
            height: 34px;
            padding: 0 18px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(239, 68, 68, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-modal-delete:hover {
            background: #dc2626;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35);
        }

        /* Toast notification feedback */
        #enqToastNotification {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #0f172a;
            color: #ffffff;
            border-radius: 8px;
            padding: 10px 18px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 999999;
            align-items: center;
            gap: 8px;
        }

        #enqToastNotification.toast-success {
            background: #065f46;
            color: #ecfdf5;
            border: 1px solid #059669;
        }

        #enqToastNotification.toast-warning {
            background: #92400e;
            color: #fffbeb;
            border: 1px solid #d97706;
        }
    </style>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="enquiry-container">
            
            <!-- Top Toolbar -->
            <div class="enquiry-toolbar-card">
                <div class="toolbar-left">
                    <div class="page-title-badge">
                        <div class="page-icon"><i class="fa fa-envelope-open-o"></i></div>
                        <div class="page-title-text">
                            <h3>Enquiries</h3>
                            <div class="page-subtitle">Referred, Just Dial &amp; Tele Call Inquiries</div>
                        </div>
                    </div>
                    <div class="enq-tab-switcher" role="tablist">
                        <?php foreach ($enquiryTabs as $tabKey => $tabCfg) { ?>
                            <button type="button"
                                    class="enq-tab-btn <?php echo ($activeTab === $tabKey) ? 'active' : ''; ?>"
                                    data-target-tab="<?php echo $tabKey; ?>"
                                    data-count="<?php echo count($tabCfg['rows']); ?>">
                                <i class="fa <?php echo $tabCfg['icon']; ?>"></i>
                                <span><?php echo htmlspecialchars($tabCfg['label']); ?></span>
                                <span class="enq-tab-count"><?php echo count($tabCfg['rows']); ?></span>
                            </button>
                        <?php } ?>
                    </div>
                </div>
                <div class="toolbar-right">
                    <span class="count-badge">
                        <i class="fa fa-list-ul"></i>
                        <span><strong id="displayCount"><?php echo $totalCount; ?></strong> Inquiries</span>
                    </span>
                    <?php if ($canDelete) { ?>
                        <button type="button" class="btn-action-delete deletebutton" title="Delete selected inquiries">
                            <i class="fa fa-trash-o"></i> Delete Selected
                        </button>
                    <?php } ?>
                </div>
            </div>

            <!-- Table Card Container -->
            <div class="table-card-wrapper">
                
                <!-- Instant Loading Overlay -->
                <div id="tableLoaderOverlay" class="table-loader-overlay">
                    <div class="loader-spinner-box">
                        <div class="modern-spinner"></div>
                        <div class="loader-title">Loading Enquiries...</div>
                        <div class="loader-desc">Optimizing layout and rendering records</div>
                    </div>
                </div>

                <?php foreach ($enquiryTabs as $tabKey => $tabCfg) { ?>
                    <div id="<?php echo $tabCfg['paneId']; ?>" class="enq-tab-pane" data-tab-key="<?php echo $tabKey; ?>" style="display: <?php echo ($activeTab === $tabKey) ? 'block' : 'none'; ?>;">
                        <table class="table table-hover enq-modern-table" id="<?php echo $tabCfg['tableId']; ?>" style="width: 100%;">
                            <thead>
                                <tr>
                                    <?php if ($canDelete) { ?>
                                        <th class="th-checkbox-col" style="width: 40px; text-align: center;">
                                            <div class="th-checkbox-wrap">
                                                <input type="checkbox" class="enq-checkbox select-all-tab-enq" title="Select All">
                                            </div>
                                        </th>
                                    <?php } ?>
                                    <th style="width: 100px; text-align: center;">Prio.</th>
                                    <th style="width: 250px;">Customer</th>
                                    <th style="width: 160px;">Contact</th>
                                    <th style="width: 160px;"><?php echo htmlspecialchars($tabCfg['col4Label']); ?></th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($tabCfg['rows'] as $customer) {
                                    $cid = $customer['customer_id'];
                                    $prio = trim($customer['priority'] ?? '');
                                    $prioClass = 'prio-none';
                                    if ($prio === 'High') {
                                        $prioClass = 'prio-high';
                                    } else if ($prio === 'Medium') {
                                        $prioClass = 'prio-medium';
                                    } else if ($prio === 'Low') {
                                        $prioClass = 'prio-low';
                                    }

                                    $companyName = trim($customer['company_name'] ?? '');
                                    $contact = trim($customer['contact_no'] ?? '');
                                    $col4Val = trim($customer[$tabCfg['col4Field']] ?? '');
                                    ?>
                                    <tr class="enquiry-row" data-customer-id="<?php echo $cid; ?>">
                                        <?php if ($canDelete) { ?>
                                            <td style="text-align: center;">
                                                <input type="checkbox" class="deletecustomerlist enq-checkbox" name="deletecustomerlist" value="<?php echo $cid; ?>">
                                            </td>
                                        <?php } ?>
                                        <td style="text-align: center;">
                                            <select class="customPriority <?php echo $prioClass; ?>" data-id="<?php echo $cid; ?>">
                                                <option value="" <?php echo empty($prio) ? 'selected' : ''; ?>>Select</option>
                                                <option value="High" <?php echo ($prio === 'High') ? 'selected' : ''; ?>>High</option>
                                                <option value="Medium" <?php echo ($prio === 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                                <option value="Low" <?php echo ($prio === 'Low') ? 'selected' : ''; ?>>Low</option>
                                            </select>
                                        </td>
                                        <td>
                                            <a class="customerAccount customer-link" data-id="<?php echo $cid; ?>" title="View customer account details">
                                                <span><?php echo htmlspecialchars($companyName ?: '—'); ?></span>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if (!empty($contact)) { ?>
                                                <span class="contact-text"><i class="fa fa-phone" style="font-size: 10.5px; color: #64748b; margin-right: 4px;"></i><?php echo htmlspecialchars($contact); ?></span>
                                            <?php } else { ?>
                                                <span class="empty-cell-dash">—</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($col4Val)) { ?>
                                                <span class="ref-text"><?php echo htmlspecialchars($col4Val); ?></span>
                                            <?php } else { ?>
                                                <span class="empty-cell-dash">—</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <textarea rows="2" class="autosavecomments customComments" data-row="<?php echo $cid; ?>" placeholder="Click to add note... (Press Enter for timestamp)"><?php echo htmlspecialchars($customer['comments'] ?? ''); ?></textarea>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>

<!-- Modern Delete Confirmation Modal -->
<div id="delete-customer" style="display: none;">
    <div class="modern-modal-card">
        <div class="modal-warning-icon">
            <i class="fa fa-trash-o"></i>
        </div>
        <div class="modal-title-text">Delete Selected Enquiries?</div>
        <div class="modal-body-text">
            Are you sure you want to delete the selected inquiries? This action will mark them as deleted and remove them from this list.
        </div>
        <div class="modal-actions-wrap">
            <button type="button" class="btn-modal-cancel delete-canceled">Cancel</button>
            <button type="button" class="btn-modal-delete delete-confirm">
                <i class="fa fa-trash-o"></i> Yes, Delete
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="enqToastNotification">
    <i class="fa fa-check-circle"></i> <span id="enqToastMessage">Saved</span>
</div>

<script type="text/javascript">
    // Helper to show modern toast notification
    function showEnqToast(msg, type) {
        var $toast = $('#enqToastNotification');
        $toast.removeClass('toast-success toast-warning');
        if (type === 'warning') {
            $toast.addClass('toast-warning');
            $toast.find('i').attr('class', 'fa fa-exclamation-circle');
        } else {
            $toast.addClass('toast-success');
            $toast.find('i').attr('class', 'fa fa-check-circle');
        }
        $('#enqToastMessage').text(msg);
        $toast.stop(true, true).fadeIn(200).delay(2200).fadeOut(300);
    }

    // Helper to update priority select badge color
    function updatePriorityBadgeStyle($select) {
        var val = $select.val();
        $select.removeClass('prio-high prio-medium prio-low prio-none');
        if (val === 'High') {
            $select.addClass('prio-high');
        } else if (val === 'Medium') {
            $select.addClass('prio-medium');
        } else if (val === 'Low') {
            $select.addClass('prio-low');
        } else {
            $select.addClass('prio-none');
        }
    }

    $(document).ready(function () {
        var activeEnqTab = <?php echo json_encode($activeTab); ?>;
        var hasDeleteCol = <?php echo $canDelete ? 'true' : 'false'; ?>;
        var colDefs = [];
        if (hasDeleteCol) {
            colDefs.push({ "width": "40px", "targets": 0, "orderable": false, "searchable": false });
            colDefs.push({ "width": "100px", "targets": 1 });
            colDefs.push({ "width": "240px", "targets": 2 });
            colDefs.push({ "width": "160px", "targets": 3 });
            colDefs.push({ "width": "160px", "targets": 4 });
            colDefs.push({ "targets": 5, "orderable": false });
        } else {
            colDefs.push({ "width": "100px", "targets": 0 });
            colDefs.push({ "width": "240px", "targets": 1 });
            colDefs.push({ "width": "160px", "targets": 2 });
            colDefs.push({ "width": "160px", "targets": 3 });
            colDefs.push({ "targets": 4, "orderable": false });
        }

        function initEnquiryDataTable(selector) {
            return $(selector).DataTable({
                "scrollY": "68vh",
                "sScrollX": "100%",
                "scrollCollapse": true,
                "paging": false,
                "ordering": true,
                "autoWidth": false,
                "columnDefs": colDefs,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search customer, contact, ref/location...",
                    "info": "Showing _TOTAL_ inquiry records",
                    "infoEmpty": "No inquiries available",
                    "infoFiltered": "(filtered from _MAX_ total inquiries)",
                    "zeroRecords": "No matching inquiries found"
                }
            });
        }

        var enqTables = {
            referred: initEnquiryDataTable('#tab_newEnq_list'),
            jd: initEnquiryDataTable('#tab_jdEnq_list'),
            tel: initEnquiryDataTable('#tab_tcEnq_list')
        };

        function adjustActiveTable() {
            if (enqTables[activeEnqTab]) {
                enqTables[activeEnqTab].columns.adjust().draw(false);
            }
        }

        // Instantly remove loading overlay once tables are ready
        setTimeout(function () {
            var $overlay = $('#tableLoaderOverlay');
            if ($overlay.length) {
                $overlay.fadeOut(150, function () { $(this).remove(); });
            }
            adjustActiveTable();
        }, 120);

        // Top 3-Tab Switcher
        $('.enq-tab-btn').on('click', function () {
            var targetTab = $(this).data('target-tab');
            var countVal = $(this).data('count');
            if (!targetTab || !enqTables[targetTab]) return;

            activeEnqTab = targetTab;
            $('.enq-tab-btn').removeClass('active');
            $(this).addClass('active');

            $('.enq-tab-pane').hide();
            $('.enq-tab-pane[data-tab-key="' + targetTab + '"]').show();
            $('#displayCount').text(countVal);

            if (window.history && window.history.replaceState) {
                var url = new URL(window.location.href);
                url.searchParams.set('tab', targetTab);
                window.history.replaceState({}, '', url.toString());
            }

            adjustActiveTable();
            $('.enq-tab-pane[data-tab-key="' + targetTab + '"] .customComments').each(function () {
                $(this).scrollTop($(this).prop("scrollHeight"));
            });
        });

        // Sidebar toggle adjustments
        $("#menu-toggle").on('click', function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            setTimeout(adjustActiveTable, 250);
        });

        // Scoped "Select All" checkbox toggle per tab pane
        $(document).on('change', '.select-all-tab-enq', function () {
            var isChecked = $(this).is(':checked');
            $(this).closest('.enq-tab-pane').find('.deletecustomerlist').prop('checked', isChecked);
        });

        // Uncheck "Select All" in active pane if any row checkbox is unchecked
        $(document).on('change', '.deletecustomerlist', function () {
            var $pane = $(this).closest('.enq-tab-pane');
            var $selectAll = $pane.find('.select-all-tab-enq');
            if (!$(this).is(':checked')) {
                $selectAll.prop('checked', false);
            } else {
                var total = $pane.find('.deletecustomerlist').length;
                var checked = $pane.find('.deletecustomerlist:checked').length;
                if (total > 0 && total === checked) {
                    $selectAll.prop('checked', true);
                }
            }
        });

        // Delete button click handler (only collects checked rows in active tab)
        var pendingDeleteList = "";
        $(".deletebutton").on('click', function (e) {
            e.preventDefault();
            var custList = [];
            $('.enq-tab-pane[data-tab-key="' + activeEnqTab + '"] .deletecustomerlist:checked').each(function () {
                custList.push($(this).val());
            });

            if (custList.length === 0) {
                showEnqToast("Please select at least one enquiry to delete.", "warning");
                return;
            }

            pendingDeleteList = custList.join("~");
            $("#delete-customer").fadeIn(150);
        });

        // Modal cancel
        $('.delete-canceled').on('click', function () {
            $("#delete-customer").fadeOut(150);
            pendingDeleteList = "";
        });

        // Close modal on click outside card
        $('#delete-customer').on('click', function (e) {
            if ($(e.target).is('#delete-customer')) {
                $("#delete-customer").fadeOut(150);
                pendingDeleteList = "";
            }
        });

        // Modal confirm delete
        $('.delete-confirm').on('click', function () {
            if (!pendingDeleteList) return;
            var url = 'deleteCustomerService.php';
            var form = $('<form action="' + url + '" method="post">' +
                '<input type="hidden" name="customersList" value="' + pendingDeleteList + '" />' +
                '<input type="hidden" name="page" value="newEnquiry_Test.php?tab=' + activeEnqTab + '" />' +
                '</form>');
            $('body').append(form);
            form.submit();
        });

        // Priority Change AJAX
        $(document).on('change', '.customPriority', function () {
            var $this = $(this);
            var id = $this.attr('data-id');
            var priority = $this.val();

            updatePriorityBadgeStyle($this);

            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'text',
                data: {
                    customId: id,
                    priority: priority
                },
                success: function () {
                    showEnqToast("Priority updated to " + (priority || "None"), "success");
                },
                error: function () {
                    showEnqToast("Error updating priority", "warning");
                }
            });
        });

        // Comments Enter Key timestamp appender: DD.MM.YYYY - USER - 
        $(document).on('keypress', '.customComments', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                var inputVal = $(this).val();
                if (inputVal !== "" && inputVal !== null) {
                    inputVal = inputVal + "\n";
                }
                var appendText = "<?php echo date("d.m.Y") . " - " . $userAbbr . " - "; ?>";
                $(this).val(inputVal + appendText);
                // Trigger change to autosave
                $(this).trigger('change');
            }
        });

        // Comments Autosave on Change
        $(document).on('change', '.customComments', function () {
            var $this = $(this);
            var colVal = $this.val();
            var customerId = $this.attr('data-row');

            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    id: customerId,
                    columnName: "comments",
                    columnValue: colVal
                },
                success: function () {
                    $this.addClass('saved-flash');
                    setTimeout(function () {
                        $this.removeClass('saved-flash');
                    }, 800);
                    showEnqToast("Comment saved", "success");
                },
                error: function () {
                    showEnqToast("Error saving comment", "warning");
                }
            });
        });

        // Customer Account Redirect
        $(document).on('click', '.customerAccount', function (e) {
            e.preventDefault();
            var customerID = $(this).attr('data-id');
            var url = 'customerAccountDetails.php';
            var form = $('<form action="' + url + '" method="get">' +
                '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                '</form>');
            $('body').append(form);
            form.submit();
        });

        // Auto scroll textareas to bottom on initial load
        $(".customComments").each(function () {
            $(this).scrollTop($(this).prop("scrollHeight"));
        });

        // Window resize adjust
        $(window).on('resize', function () {
            adjustActiveTable();
        });
    });
</script>

<?php
// Close the database connection cleanly
$statement = null;
$db = null;
?>
