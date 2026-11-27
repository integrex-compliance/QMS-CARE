<?php
// Read-only client ledger. Invoices are supporting documents, not extra charges.
function clientLedgerMoney($value) {
    return is_numeric($value) ? (int) round((float) $value * 100) : 0;
}

function buildClientLedger(array $services, array $consulting, array $certification, array $invoices) {
    $totals = ['amount' => 0, 'received' => 0, 'tds' => 0, 'short' => 0, 'loss' => 0, 'invoiced' => 0];
    $groups = [];
    $entries = [];
    $missingAmounts = 0;
    foreach ($services as $service) {
        if (strtoupper($service['is_delete'] ?? 'N') === 'Y') continue;
        $groups[(string) $service['service_id']] = array_merge($totals, [
            'id' => $service['service_id'], 'label' => $service['service_type'],
            'created' => $service['created_at'] ?? '', 'consulting' => 0, 'cb' => 0,
        ]);
    }
    foreach (['Consulting' => $consulting, 'CB' => $certification] as $kind => $rows) {
        foreach ($rows as $row) {
            $id = (string) $row['service_id'];
            if (!isset($groups[$id]) || strtoupper($row['is_delete'] ?? 'N') === 'Y') continue;
            $isConsulting = $kind === 'Consulting';
            $label = trim($row[$isConsulting ? 'consulting_fees' : 'contract_details'] ?? '');
            $amountValue = $row[$isConsulting ? 'consulting_amount_decided' : 'amount_decided'] ?? '';
            $amount = clientLedgerMoney($amountValue);
            $received = clientLedgerMoney($row[$isConsulting ? 'consulting_payment_received' : 'payment_received'] ?? 0);
            $tds = $isConsulting ? clientLedgerMoney($row['tds_deducted'] ?? 0) : 0;
            $short = $isConsulting ? clientLedgerMoney($row['short_settle'] ?? 0) : 0;
            $loss = $isConsulting ? clientLedgerMoney($row['bad_debt'] ?? 0) : 0;
            // Older adjustment-only rows stored the adjustment in received/decided.
            $legacyAdjustment = false;
            if ($isConsulting) {
                foreach (['tds' => &$tds, 'short' => &$short, 'loss' => &$loss] as $word => &$adjustment) {
                    if (stripos($label, $word) !== false) {
                        if ($adjustment === 0) $adjustment = $received !== 0 ? $received : $amount;
                        $received = 0;
                        $legacyAdjustment = true;
                        break;
                    }
                }
                unset($adjustment);
            }
            if ($legacyAdjustment) $amount = 0;
            if (!$legacyAdjustment && !is_numeric($amountValue) && ($label !== '' || $received !== 0)) $missingAmounts++;
            $group = &$groups[$id];
            $group['amount'] += $amount;
            $group[$isConsulting ? 'consulting' : 'cb'] += $amount;
            foreach (['received' => $received, 'tds' => $tds, 'short' => $short, 'loss' => $loss] as $key => $value) $group[$key] += $value;
            $base = ['service' => $group['label'] . ' #' . $id, 'category' => $kind, 'description' => $label];
            if ($amount !== 0) {
                $entries[] = array_merge($base, ['date' => substr($group['created'], 0, 10), 'type' => 'Amount decided', 'debit' => $amount, 'credit' => 0, 'instrument' => '']);
            }
            $date = $row[$isConsulting ? 'consulting_deposit_date' : 'deposit_date'] ?? '';
            $instrument = trim(($row[$isConsulting ? 'consulting_chque_details' : 'check_details'] ?? '') . ' ' . ($row[$isConsulting ? 'bank_ac' : 'bank_account'] ?? ''));
            foreach (['Payment received' => $received, 'TDS deducted' => $tds, 'Short settlement' => $short, 'Bad debt / loss' => $loss] as $type => $value) {
                if ($value !== 0) $entries[] = array_merge($base, ['date' => $date, 'type' => $type, 'debit' => 0, 'credit' => $value, 'instrument' => $instrument]);
            }
            unset($group);
        }
    }
    foreach ($groups as &$group) {
        $group['balance'] = $group['amount'] - $group['received'] - $group['tds'] - $group['short'] - $group['loss'];
        foreach (['amount', 'received', 'tds', 'short', 'loss'] as $key) $totals[$key] += $group[$key];
    }
    unset($group);
    $activeInvoices = [];
    foreach ($invoices as $invoice) {
        if (isset($invoice['is_deleted']) && strcasecmp($invoice['is_deleted'], 'No') !== 0) continue;
        $totals['invoiced'] += clientLedgerMoney($invoice['total_amount']);
        $activeInvoices[] = $invoice;
    }
    foreach ($entries as $index => &$entry) {
        $date = substr((string) $entry['date'], 0, 10);
        $parts = explode('-', $date);
        $entry['date'] = count($parts) === 3 && checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0]) ? $date : '';
        $entry['order'] = $index;
    }
    unset($entry);
    usort($entries, function ($a, $b) {
        return strcmp($a['date'] ?: '9999-12-31', $b['date'] ?: '9999-12-31') ?: ($a['order'] <=> $b['order']);
    });
    $balance = 0;
    foreach ($entries as &$entry) {
        $balance += $entry['debit'] - $entry['credit'];
        $entry['balance'] = $balance;
    }
    unset($entry);
    $totals['balance'] = $balance;
    $totals['pending'] = max(0, $balance);
    $totals['advance'] = max(0, -$balance);
    return ['totals' => $totals, 'services' => $groups, 'entries' => $entries, 'invoices' => $activeInvoices, 'missingAmounts' => $missingAmounts];
}

function loadClientLedger(PDO $db, $customerId, array $invoices) {
    $stmt = $db->prepare("SELECT * FROM services WHERE customer_id = ? AND COALESCE(is_delete, 'N') <> 'Y'");
    $stmt->execute([$customerId]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $rows = [];
    foreach (['consulting', 'certification_body'] as $table) {
        $stmt = $db->prepare("SELECT c.* FROM $table c INNER JOIN services s ON s.service_id = c.service_id WHERE s.customer_id = ? AND COALESCE(s.is_delete, 'N') <> 'Y' AND COALESCE(c.is_delete, 'N') <> 'Y'");
        $stmt->execute([$customerId]);
        $rows[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return buildClientLedger($services, $rows['consulting'], $rows['certification_body'], $invoices);
}
