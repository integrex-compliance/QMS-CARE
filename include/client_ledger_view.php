<?php
if (!isset($clientLedger)) return;
$ledgerEscape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$ledgerMoney = function ($value) { return '&#8377;' . number_format($value / 100, 2); };
$ledgerDate = function ($value) {
    $date = DateTime::createFromFormat('!Y-m-d', substr((string) $value, 0, 10));
    return $date && $date->format('Y-m-d') === substr((string) $value, 0, 10) ? $date->format('d.m.Y') : '-';
};
$ledgerTotals = $clientLedger['totals'];
?>
<div id="client-ledger-content" class="client-ledger">
    <div class="ledger-heading"><strong><i class="fa fa-book"></i> Client Ledger</strong><span><?php echo $ledgerEscape($customer['company_name'] ?? ''); ?> &middot; All services</span></div>
    <div class="ledger-columns">
    <section class="ledger-invoices" aria-label="Client invoices">
        <h4 class="ledger-section-title">Invoices <small><?php echo count($clientLedger['invoices']); ?> issued</small></h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover ledger-invoice-table">
                <thead><tr><th>Invoice No.</th><th>Date</th><th>Amount</th><th>V/E</th></tr></thead>
                <tbody>
                <?php foreach ($clientLedger['invoices'] as $invoice) { ?>
                    <tr>
                        <td><?php echo $ledgerEscape(($invoice['firm_abbrevation'] ?? '') . '/' . $invoice['invoice_no']); ?>
                            <details class="ledger-invoice-description"><summary>Details</summary><small><?php echo $ledgerEscape($invoice['firm_name'] ?? ''); ?></small><small><?php echo $ledgerEscape($invoice['svc_desc'] ?? ''); ?></small></details>
                        </td>
                        <td class="ledger-invoice-date"><?php echo $ledgerEscape($ledgerDate($invoice['invoice_date'])); ?></td>
                        <td class="ledger-invoice-amount"><?php echo $ledgerMoney(clientLedgerMoney($invoice['total_amount'])); ?></td>
                        <td class="ledger-invoice-actions">
                            <a class="viewInvoice" data-id="<?php echo (int) $invoice['invoice_id']; ?>" data-invoice="<?php echo $ledgerEscape($invoice['invoice_no']); ?>" data-customer="<?php echo (int) $invoice['customer_id']; ?>" title="View invoice PDF" aria-label="View invoice PDF" role="button" tabindex="0"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                            <a class="editInvoice" data-id="<?php echo (int) $invoice['invoice_id']; ?>" data-firm="<?php echo (int) $invoice['firm_id']; ?>" data-customer="<?php echo (int) $invoice['customer_id']; ?>" title="Edit invoice" aria-label="Edit invoice" role="button" tabindex="0"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                <?php } ?>
                <?php if (!$clientLedger['invoices']) { ?><tr><td colspan="4">No invoices issued for this client.</td></tr><?php } ?>
                </tbody>
                <tfoot><tr><th colspan="2">Total Invoiced</th><td class="ledger-invoice-amount"><?php echo $ledgerMoney($ledgerTotals['invoiced']); ?></td><td></td></tr></tfoot>
            </table>
        </div>
    </section>
    <section class="ledger-summary" aria-label="Payment summary">
    <h4 class="ledger-section-title">Payment Summary</h4>
    <div class="ledger-kpis">
        <?php foreach (['amount' => 'Total Amount Decided', 'invoiced' => 'Total Invoiced', 'received' => 'Payment Received', 'pending' => 'Pending Amount', 'advance' => 'Advance / Excess'] as $key => $label) { ?>
            <div class="ledger-kpi ledger-<?php echo $key; ?>"><span><?php echo $label; ?></span><strong><?php echo $ledgerMoney($ledgerTotals[$key]); ?></strong></div>
        <?php } ?>
    </div>
    <div class="ledger-adjustments">
        <span>TDS: <strong><?php echo $ledgerMoney($ledgerTotals['tds']); ?></strong></span>
        <span>Short settlement: <strong><?php echo $ledgerMoney($ledgerTotals['short']); ?></strong></span>
        <span>Bad debt / loss: <strong><?php echo $ledgerMoney($ledgerTotals['loss']); ?></strong></span>
    </div>
    </section>
    </div>
</div>
