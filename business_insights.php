<div id="wrapper" class="active">
<?php
$pageIdentifier = "Business Insights";
include_once './header.php';
include_once './dbConnect.php';

// Generate an array of years dynamically, e.g., from 2017 to current year
$currentYear = date('Y');
$years = range(2017, $currentYear);
rsort($years);

$months = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];
?>
    <div id="page-content-wrapper" style="background-color: #f4f7f6; min-height: 100vh;">
        <!-- Premium Dashboard Styles -->
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
            
            body { 
                background-color: #f4f7f6;
            }
            .dashboard-container {
                font-family: 'Inter', sans-serif;
                padding: 20px 30px;
                margin-top: 50px;
                background-color: #f4f7f6;
            }
            /* Header & Filters */
            .dash-header {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
            }
            .dash-title {
                margin: 0;
                font-weight: 700;
                color: #1f2937;
                font-size: 28px;
                letter-spacing: -0.5px;
            }
            .dash-subtitle {
                color: #6b7280;
                font-size: 14px;
                margin-top: 5px;
            }
            .filter-group {
                display: flex;
                gap: 15px;
                background: #fff;
                padding: 10px 20px;
                border-radius: 12px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            }
            .filter-select {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 8px 16px;
                font-size: 14px;
                color: #374151;
                background-color: #f9fafb;
                outline: none;
                transition: all 0.2s ease;
                min-width: 130px;
                font-weight: 500;
            }
            .filter-select:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }
            
            /* Metric Cards */
            .metrics-row {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 25px;
                margin-bottom: 35px;
            }
            .metric-card {
                background: #fff;
                border-radius: 16px;
                padding: 25px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
                position: relative;
                overflow: hidden;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .metric-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            .metric-icon {
                position: absolute;
                top: 25px;
                right: 25px;
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
            }
            .icon-blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
            .icon-green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
            
            .metric-label {
                font-size: 14px;
                color: #6b7280;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 10px;
            }
            .metric-value {
                font-size: 36px;
                font-weight: 700;
                color: #111827;
                margin-bottom: 5px;
            }
            
            /* Chart & Table Area */
            .content-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 25px;
            }
            @media (max-width: 992px) {
                .content-row { grid-template-columns: 1fr; }
            }
            
            .chart-card, .table-card {
                background: #fff;
                border-radius: 16px;
                padding: 25px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            }
            .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #f3f4f6;
            }
            .card-title {
                font-size: 18px;
                font-weight: 600;
                color: #1f2937;
                margin: 0;
            }
            
            /* Table Styles */
            .modern-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
            }
            .modern-table th {
                background-color: #f9fafb;
                color: #6b7280;
                font-size: 13px;
                font-weight: 600;
                text-transform: uppercase;
                padding: 12px 15px;
                text-align: left;
                border-bottom: 1px solid #e5e7eb;
            }
            .modern-table th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
            .modern-table th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
            
            .modern-table td {
                padding: 16px 15px;
                color: #4b5563;
                font-size: 14px;
                border-bottom: 1px solid #f3f4f6;
                vertical-align: middle;
            }
            .modern-table tbody tr { transition: background-color 0.2s; }
            .modern-table tbody tr:hover { background-color: #f9fafb; cursor: pointer; }
            .modern-table tbody tr:last-child td { border-bottom: none; }
            
            .org-name {
                font-weight: 600;
                color: #111827;
            }
            .currency-val {
                font-weight: 600;
                color: #059669;
            }
            .badge-count {
                background: #eff6ff;
                color: #2563eb;
                padding: 4px 10px;
                border-radius: 9999px;
                font-size: 12px;
                font-weight: 600;
            }

            /* Loading Overlay */
            .loader-overlay {
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(255,255,255,0.8);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 10;
                border-radius: 16px;
            }
            .spinner {
                width: 40px; height: 40px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #3b82f6;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        </style>

        <div class="dashboard-container">
            <div class="dash-header">
                <div>
                    <h1 class="dash-title">Business Insights</h1>
                    <p class="dash-subtitle">Analyze invoice generation and revenue metrics</p>
                </div>
                <div class="filter-group">
                    <select id="filterMonth" class="filter-select">
                        <option value="all">All Months</option>
                        <?php foreach($months as $num => $name): ?>
                            <option value="<?= $num ?>" <?= ($num == date('m')) ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="filterYear" class="filter-select">
                        <option value="all">All Years</option>
                        <?php foreach($years as $yr): ?>
                            <option value="<?= $yr ?>" <?= ($yr == $currentYear) ? 'selected' : '' ?>><?= $yr ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Top Metrics -->
            <div class="metrics-row">
                <div class="metric-card">
                    <div class="loader-overlay" id="loaderMetrics1"><div class="spinner"></div></div>
                    <div class="metric-icon icon-blue"><i class="fa fa-file-text-o"></i></div>
                    <div class="metric-label">Total Invoices Generated</div>
                    <div class="metric-value" id="valTotalInvoices">0</div>
                    <div style="font-size: 13px; color: #9ca3af;">For selected period</div>
                </div>
                <div class="metric-card">
                    <div class="loader-overlay" id="loaderMetrics2"><div class="spinner"></div></div>
                    <div class="metric-icon icon-green"><i class="fa fa-inr"></i></div>
                    <div class="metric-label">Total Value (Due)</div>
                    <div class="metric-value" id="valTotalDue">₹ 0.00</div>
                    <div style="font-size: 13px; color: #9ca3af;">Sum of all generated invoice totals</div>
                </div>
            </div>

            <!-- Organization Breakdown & Chart -->
            <div class="content-row">
                <div class="chart-card" style="position: relative;">
                    <div class="loader-overlay" id="loaderChart"><div class="spinner"></div></div>
                    <div class="card-header">
                        <h3 class="card-title">Revenue by Organization</h3>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                
                <div class="table-card" style="position: relative;">
                    <div class="loader-overlay" id="loaderTable"><div class="spinner"></div></div>
                    <div class="card-header">
                        <h3 class="card-title">Organization Breakdown</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Organization Name</th>
                                    <th>Invoices</th>
                                    <th>Total Value (Rs)</th>
                                </tr>
                            </thead>
                            <tbody id="orgTableBody">
                                <!-- Data will be injected here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Modal for Invoice Details -->
            <div class="modal fade" id="invoiceDetailsModal" tabindex="-1" role="dialog" aria-labelledby="invoiceDetailsModalLabel">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="border-radius: 12px; border: none; overflow: hidden; font-family: 'Inter', sans-serif;">
                  <div class="modal-header" style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="invoiceDetailsModalLabel" style="font-weight: 600; color: #1f2937;">Organization Invoices</h4>
                  </div>
                  <div class="modal-body" style="padding: 20px;">
                    <div id="modalLoading" style="text-align: center; padding: 20px;"><div class="spinner" style="margin: 0 auto;"></div></div>
                    <div style="overflow-x: auto;">
                        <table class="modern-table" id="modalInvoiceTable" style="display: none;">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>Client Name</th>
                                    <th>Total Due (Rs)</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="modalInvoiceBody">
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

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    
    // Toggle sidebar format adjustment
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
    });

    let revenueChart = null;

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            minimumFractionDigits: 2
        }).format(amount);
    }

    // Number animation function
    function animateValue(obj, start, end, duration, formatFn = null) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            // using an ease-out function
            const easeOutProgress = 1 - Math.pow(1 - progress, 3);
            let current = easeOutProgress * (end - start) + start;
            
            if (formatFn) {
                obj.text(formatFn(current));
            } else {
                obj.text(Math.floor(current));
            }
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                if(formatFn) obj.text(formatFn(end));
                else obj.text(Math.floor(end));
            }
        };
        window.requestAnimationFrame(step);
    }

    function initChart(labels, dataArr) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        if (revenueChart) {
            revenueChart.destroy();
        }

        const gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Value (₹)',
                    data: dataArr,
                    backgroundColor: gradientBlue,
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: { size: 14, family: 'Inter' },
                        bodyFont: { size: 14, family: 'Inter' },
                        callbacks: {
                            label: function(context) {
                                let val = context.raw || 0;
                                return ' Value: ' + formatCurrency(val);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#e5e7eb' },
                        ticks: {
                            font: { family: 'Inter' },
                            callback: function(value) {
                                if(value >= 10000000) return '₹' + (value/10000000).toFixed(1) + 'C';
                                if(value >= 100000) return '₹' + (value/100000).toFixed(1) + 'L';
                                if(value >= 1000) return '₹' + (value/1000).toFixed(1) + 'k';
                                return '₹' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter' }, maxRotation: 45, minRotation: 45 }
                    }
                }
            }
        });
    }

    function fetchInsights() {
        const month = $('#filterMonth').val();
        const year = $('#filterYear').val();
        
        $('.loader-overlay').css('display', 'flex');

        $.ajax({
            url: 'business_insights_api.php',
            type: 'GET',
            dataType: 'json',
            data: { month: month, year: year },
            success: function(response) {
                $('.loader-overlay').hide();
                if (response.success) {
                    // Update Top Cards with animation
                    animateValue($('#valTotalInvoices'), 0, parseInt(response.overall.total_invoices), 1500);
                    animateValue($('#valTotalDue'), 0, parseFloat(response.overall.total_due), 2000, formatCurrency);

                    // Update Table & prepare Chart Data
                    const tbody = $('#orgTableBody');
                    tbody.empty();
                    
                    let chartLabels = [];
                    let chartData = [];

                    if (response.organizations.length > 0) {
                        response.organizations.forEach(function(org) {
                            let totalInv = parseInt(org.total_invoices);
                            let totalDue = parseFloat(org.total_due);
                            
                            // chart
                            chartLabels.push(org.firm_name);
                            chartData.push(totalDue);

                            // table row
                            let row = `
                                <tr class="org-row" data-firm-id="${org.consulting_firm_id}" data-firm-name="${org.firm_name}" title="Click to view invoices" style="cursor: pointer;">
                                    <td class="org-name">${org.firm_name} <i class="fa fa-external-link" style="font-size: 11px; color:#9ca3af; margin-left: 6px;"></i></td>
                                    <td><span class="badge-count">${totalInv}</span></td>
                                    <td class="currency-val">${formatCurrency(totalDue)}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    } else {
                        tbody.append('<tr><td colspan="3" style="text-align:center; padding:30px;">No invoice data found for this period.</td></tr>');
                    }

                    // Update Chart
                    initChart(chartLabels, chartData);

                } else {
                    console.error('Error fetching data:', response.error);
                }
            },
            error: function() {
                $('.loader-overlay').hide();
                alert('Failed to load business insights.');
            }
        });
    }

    // Click handler for opening modal with specific firm details
    $('#orgTableBody').on('click', '.org-row', function() {
        const firmId = $(this).data('firm-id');
        const firmName = $(this).data('firm-name');
        const month = $('#filterMonth').val();
        const year = $('#filterYear').val();

        $('#invoiceDetailsModalLabel').text('Invoices: ' + firmName);
        $('#modalInvoiceTable').hide();
        $('#modalInvoiceBody').empty();
        $('#modalLoading').show();
        $('#invoiceDetailsModal').modal('show');

        $.ajax({
            url: 'business_insights_api.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'details', firm_id: firmId, month: month, year: year },
            success: function(res) {
                $('#modalLoading').hide();
                if(res.success) {
                    $('#modalInvoiceTable').show();
                    if(res.invoices.length > 0) {
                        res.invoices.forEach(inv => {
                            let invDateParts = inv.invoice_date.split('-');
                            let niceDate = invDateParts[2] + '-' + invDateParts[1] + '-' + invDateParts[0];
                            let amount = formatCurrency(parseFloat(inv.total_amount));
                            let tr = `<tr>
                                <td style="color: #2563eb; font-weight: 500;">${inv.invoice_no}</td>
                                <td>${niceDate}</td>
                                <td style="font-weight: 500; color: #374151;">${inv.customer_name}</td>
                                <td style="font-weight: 600; color: #059669;">${amount}</td>
                                <td style="text-align: center;"><a class="viewInvoiceBtn" data-id="${inv.invoice_id}" data-invoice="${inv.invoice_no}" title="Download Invoice PDF" style="cursor: pointer; color: #ef4444;"><i class="fa fa-file-pdf-o fa-lg" style="transition: transform 0.2s;"></i></a></td>
                            </tr>`;
                            $('#modalInvoiceBody').append(tr);
                        });
                    } else {
                        $('#modalInvoiceBody').append('<tr><td colspan="5" style="text-align:center; padding: 25px;">No invoices found.</td></tr>');
                    }
                } else {
                    $('#modalInvoiceBody').append('<tr><td colspan="5" style="text-align:center; color:red; padding: 25px;">Error loading data.</td></tr>');
                    $('#modalInvoiceTable').show();
                }
            },
            error: function() {
                $('#modalLoading').hide();
                $('#modalInvoiceBody').append('<tr><td colspan="5" style="text-align:center; color:red; padding: 25px;">Failed to fetch invoice details.</td></tr>');
                $('#modalInvoiceTable').show();
            }
        });
    });

    // Add interactivity to the new invoice PDF buttons
    $('#modalInvoiceBody').on('mouseenter', '.viewInvoiceBtn', function() {
        $(this).find('i').css('transform', 'scale(1.2)');
    }).on('mouseleave', '.viewInvoiceBtn', function() {
        $(this).find('i').css('transform', 'scale(1)');
    });

    // Invoice PDF Download Handler
    $('#modalInvoiceBody').on('click', '.viewInvoiceBtn', function() {
        const invoiceid = $(this).data('id');
        const invoiceno = $(this).data('invoice');
        
        let url = 'viewInvoicePdf.php';
        let form = $('<form action="' + url + '" target="_blank" method="post">' +
                '<input type="hidden" name="invoiceId" value="' + invoiceid + '" />' +
                '<input type="hidden" name="invoiceNo" value="' + invoiceno + '" />' +
                '</form>');
        $('body').append(form);
        form.submit();
        form.remove(); // Clean up form after submission
    });

    // Bind filters
    $('#filterMonth, #filterYear').on('change', fetchInsights);

    // Initial load
    fetchInsights();
});
</script>
