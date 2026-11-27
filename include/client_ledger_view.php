<?php
if (!isset($clientLedger)) return;
$ledgerEscape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$ledgerMoney = function ($value) { return '&#8377;' . number_format($value / 100, 2); };
$ledgerDate = function ($value) { return $value ? date('d.m.Y', strtotime($value)) : 'Not recorded'; };
$ledgerTotals = $clientLedger['totals'];
?>
<div id="client-ledger-content" class="client-ledger">
    <div class="ledger-heading"><strong><i class="fa fa-book"></i> Client Ledger</strong><span><?php echo $ledgerEscape($customer['company_name'] ?? ''); ?> &middot; All services</span></div>
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
    <p class="ledger-note">Pending = Consulting + CB amounts decided &minus; payments received &minus; TDS &minus; settlements &minus; loss. Invoices are shown separately and are not added again. Deleted services/payment rows are excluded.</p>
    <?php if ($clientLedger['missingAmounts']) { ?>
        <p class="ledger-warning"><i class="fa fa-info-circle"></i> <?php echo (int) $clientLedger['missingAmounts']; ?> payment row(s) have no numeric amount decided. These amounts are not included in the total; complete them for a full balance.</p>
    <?php } ?>
    <h4>Service-wise Summary</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover ledger-table">
            <thead><tr><th>Service</th><th>Consulting</th><th>CB Amount</th><th>Total Amount</th><th>Received</th><th>TDS</th><th>Settlement / Loss</th><th>Balance</th></tr></thead>
            <tbody>
            <?php foreach ($clientLedger['services'] as $group) { ?>
                <tr><td><?php echo $ledgerEscape($group['label'] . ' #' . $group['id']); ?></td>
                    <?php foreach (['consulting', 'cb', 'amount', 'received', 'tds'] as $key) { ?><td class="ledger-number"><?php echo $ledgerMoney($group[$key]); ?></td><?php } ?>
                    <td class="ledger-number"><?php echo $ledgerMoney($group['short'] + $group['loss']); ?></td>
                    <td class="ledger-number <?php echo $group['balance'] > 0 ? 'ledger-due' : 'ledger-paid'; ?>"><?php echo $ledgerMoney(abs($group['balance'])); ?><?php echo $group['balance'] < 0 ? ' Cr' : ''; ?></td>
                </tr>
            <?php } ?>
            <?php if (!$clientLedger['services']) { ?><tr><td colspan="8" class="text-center">No service payment records found.</td></tr><?php } ?>
            </tbody>
            <tfoot><tr><th colspan="3">Overall Total</th><td class="ledger-number"><?php echo $ledgerMoney($ledgerTotals['amount']); ?></td><td class="ledger-number"><?php echo $ledgerMoney($ledgerTotals['received']); ?></td><td class="ledger-number"><?php echo $ledgerMoney($ledgerTotals['tds']); ?></td><td class="ledger-number"><?php echo $ledgerMoney($ledgerTotals['short'] + $ledgerTotals['loss']); ?></td><td class="ledger-number"><?php echo $ledgerMoney(abs($ledgerTotals['balance'])); ?><?php echo $ledgerTotals['balance'] < 0 ? ' Cr' : ''; ?></td></tr></tfoot>
        </table>
    </div>
    <h4>Payment Ledger <small>Oldest to newest</small></h4>
    <p class="ledger-note">Amount-decided entries use the service creation date. Receipts use their recorded payment date; undated entries appear last. Cr indicates advance / excess credit.</p>
    <div class="table-responsive ledger-scroll">
        <table class="table table-bordered table-hover ledger-table">
            <thead><tr><th>Date</th><th>Service / Type</th><th>Particulars</th><th>Instrument / Bank</th><th>Amount (Dr)</th><th>Received / Adjustment (Cr)</th><th>Running Balance</th></tr></thead>
            <tbody>
            <?php foreach ($clientLedger['entries'] as $entry) { ?>
                <tr><td class="ledger-date"><?php echo $ledgerDate($entry['date']); ?></td><td><?php echo $ledgerEscape($entry['service']); ?><small><?php echo $ledgerEscape($entry['category']); ?></small></td><td><strong><?php echo $ledgerEscape($entry['type']); ?></strong><small><?php echo $ledgerEscape($entry['description']); ?></small></td><td><?php echo $ledgerEscape($entry['instrument']); ?></td><td class="ledger-number"><?php echo $entry['debit'] ? $ledgerMoney($entry['debit']) : '&mdash;'; ?></td><td class="ledger-number ledger-paid"><?php echo $entry['credit'] ? $ledgerMoney($entry['credit']) : '&mdash;'; ?></td><td class="ledger-number"><?php echo $ledgerMoney(abs($entry['balance'])); ?><?php echo $entry['balance'] < 0 ? ' Cr' : ''; ?></td></tr>
            <?php } ?>
            <?php if (!$clientLedger['entries']) { ?><tr><td colspan="7" class="text-center">No amounts or payments recorded for this client.</td></tr><?php } ?>
            </tbody>
            <tfoot><tr><th colspan="4">Closing Balance</th><td class="ledger-number"><?php echo $ledgerMoney($ledgerTotals['amount']); ?></td><td class="ledger-number"><?php echo $ledgerMoney($ledgerTotals['received'] + $ledgerTotals['tds'] + $ledgerTotals['short'] + $ledgerTotals['loss']); ?></td><td class="ledger-number"><?php echo $ledgerMoney(abs($ledgerTotals['balance'])); ?><?php echo $ledgerTotals['balance'] < 0 ? ' Cr' : ''; ?></td></tr></tfoot>
        </table>
    </div>
    <h4>Invoice References <small><?php echo count($clientLedger['invoices']); ?> invoices</small></h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover ledger-table">
            <thead><tr><th>Invoice No.</th><th>Date</th><th>Consulting Firm</th><th>Amount</th><th>Service</th><th>V/E</th></tr></thead>
            <tbody>
            <?php foreach ($clientLedger['invoices'] as $invoice) { ?>
                <tr><td><?php echo $ledgerEscape($invoice['firm_abbrevation'] . '/' . $invoice['invoice_no']); ?></td><td class="ledger-date"><?php echo $ledgerEscape(simpleDateConversion($invoice['invoice_date'])); ?></td><td><?php echo $ledgerEscape($invoice['firm_name']); ?></td><td class="ledger-number"><?php echo $ledgerMoney(clientLedgerMoney($invoice['total_amount'])); ?></td><td><?php echo $ledgerEscape($invoice['svc_desc']); ?></td><td class="ledger-actions">
                    <a class="viewInvoice" data-id="<?php echo (int) $invoice['invoice_id']; ?>" data-invoice="<?php echo $ledgerEscape($invoice['invoice_no']); ?>" data-customer="<?php echo (int) $invoice['customer_id']; ?>" title="View invoice"><i class="fa fa-file-pdf-o"></i></a>
                    <a class="editInvoice" data-id="<?php echo (int) $invoice['invoice_id']; ?>" data-firm="<?php echo (int) $invoice['firm_id']; ?>" data-customer="<?php echo (int) $invoice['customer_id']; ?>" title="Edit invoice"><i class="fa fa-pencil-square"></i></a>
                </td></tr>
            <?php } ?>
            <?php if (!$clientLedger['invoices']) { ?><tr><td colspan="6" class="text-center">No invoices issued for this client.</td></tr><?php } ?>
            </tbody><tfoot><tr><th colspan="3">Total Invoiced</th><td class="ledger-number"><?php echo $ledgerMoney($ledgerTotals['invoiced']); ?></td><td colspan="2"></td></tr></tfoot>
        </table>
    </div>
</div>
