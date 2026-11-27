<div id="customerServiceMilestones" data-customer="<?php echo (int)$_GET['customerid']; ?>" data-csrf="<?php echo htmlspecialchars($_SESSION['service_flow_csrf'], ENT_QUOTES, 'UTF-8'); ?>" data-readonly="<?php echo $isReadOnlyServiceType ? '1' : '0'; ?>">
    <?php foreach ($customerFlows as $flow) { if (!$flow['active']) continue; ?>
    <div class="service-flow-dates" data-flow-name="<?php echo htmlspecialchars($flow['service_name'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php $currentFlowStage = $flow['current_stage'] ?? ''; ?>
        <div class="service-flow-heading">
            <strong class="service-flow-label"><?php echo htmlspecialchars($flow['service_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <div class="service-flow-stage-field">
                <label for="flow-stage-<?php echo (int)$flow['id']; ?>">Service Stage</label>
                <select class="flow-current-stage" id="flow-stage-<?php echo (int)$flow['id']; ?>" data-id="<?php echo (int)$flow['id']; ?>" data-service-id="<?php echo (int)$flow['service_id']; ?>" data-saved-stage="<?php echo htmlspecialchars($currentFlowStage, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $isReadOnlyServiceType ? 'disabled' : ''; ?>>
                    <?php if (!in_array($currentFlowStage, $allServiceStages, true)) { ?>
                        <option value="<?php echo htmlspecialchars($currentFlowStage, ENT_QUOTES, 'UTF-8'); ?>" selected><?php echo htmlspecialchars($currentFlowStage ?: 'Select Service Stage', ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                    <?php foreach ($allServiceStages as $stage) { ?>
                        <option value="<?php echo htmlspecialchars($stage, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $stage === $currentFlowStage ? 'selected' : ''; ?>><?php echo htmlspecialchars($stage, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="qms-milestones-row">
        <?php foreach (['certification_date' => 'CA/RC Date', 'next_service_date' => 'Next Service Date', 'next_service_type' => 'Next Service Stage'] as $column => $label) {
            $value = $flow[$column];
            if ($column !== 'next_service_type') $value = $value ? date('d.m.Y', strtotime($value)) : '';
            $inputId = 'flow-' . $flow['id'] . '-' . $column;
        ?>
            <div class="qms-milestone-field">
                <label for="<?php echo $inputId; ?>"><i class="fa fa-calendar"></i> <?php echo $label; ?></label>
                <input id="<?php echo $inputId; ?>" class="flow-milestone lookslike-label" data-id="<?php echo $flow['id']; ?>" data-column="<?php echo $column; ?>" value="<?php echo htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?php echo $column === 'next_service_type' ? 'e.g. SA-1' : 'dd.mm.yyyy'; ?>" <?php echo $isReadOnlyServiceType ? 'readonly' : ''; ?>>
            </div>
        <?php } ?>
        </div>
    </div>
    <?php } ?>
    <?php if (!array_filter($customerFlows, function ($flow) { return !empty($flow['active']); })) { ?><p class="service-flow-empty">Select a service to create its section and individual dates.</p><?php } ?>
</div>
<?php if (!empty($customer['certification_date']) || !empty($customer['next_service_date'])) { ?>
<details class="service-flow-legacy"><summary>Previous customer-level dates (reference)</summary>
    CA/RC: <?php echo htmlspecialchars($customer['certification_date'] ?? ''); ?> &nbsp; Next service: <?php echo htmlspecialchars($customer['next_service_date'] ?? ''); ?> &nbsp; Stage: <?php echo htmlspecialchars($customer['next_service_type'] ?? ''); ?>
</details>
<?php } ?>
