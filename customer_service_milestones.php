<div id="customerServiceMilestones" data-customer="<?php echo (int)$_GET['customerid']; ?>" data-csrf="<?php echo htmlspecialchars($_SESSION['service_flow_csrf'], ENT_QUOTES, 'UTF-8'); ?>" data-readonly="<?php echo $isReadOnlyServiceType ? '1' : '0'; ?>">
    <?php
    $milestoneGroups = [];
    foreach ($customerFlows as $flow) {
        if ($flow['active']) $milestoneGroups[$flow['service_name']][] = $flow;
    }
    foreach ($milestoneGroups as $serviceName => $stageFlows) {
        // Keep the overview's date record stable as more stages are added.
        usort($stageFlows, function ($a, $b) { return $a['id'] <=> $b['id']; });
        $firstFlow = $stageFlows[0];
        $allowedFlowStages = $serviceStagesByName[$serviceName] ?? [];
        $groupHeadingId = 'flow-group-' . (int)$firstFlow['id'];
    ?>
    <section class="service-flow-group" data-flow-name="<?php echo htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8'); ?>" aria-labelledby="<?php echo $groupHeadingId; ?>">
        <div class="service-flow-overview-heading">
            <h3 id="<?php echo $groupHeadingId; ?>" class="service-flow-label"><span class="service-flow-prefix">Type of Service:</span> <?php echo htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8'); ?></h3>
            <?php if (!$isReadOnlyServiceType) { ?>
            <div class="service-flow-stage-field">
                <label for="overview-stage-<?php echo (int)$firstFlow['id']; ?>">Add Service Stage</label>
                <select class="flow-current-stage" id="overview-stage-<?php echo (int)$firstFlow['id']; ?>" data-id="<?php echo (int)$firstFlow['id']; ?>" <?php echo $allowedFlowStages ? '' : 'disabled'; ?>>
                    <option value=""><?php echo $allowedFlowStages ? 'Select stage to add' : 'Set stages in Master Creator'; ?></option>
                    <?php foreach ($allowedFlowStages as $stage) { ?>
                    <option value="<?php echo htmlspecialchars($stage, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($stage, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php } ?>
        </div>
        <?php
        $stageFlow = $firstFlow;
        $dateInputPrefix = 'overview-';
        include __DIR__ . '/customer_service_stage_dates.php';
        unset($dateInputPrefix);
        ?>
    </section>
    <?php } ?>
    <?php if (!$milestoneGroups) { ?><p class="service-flow-empty">Select a service to create its section and individual dates.</p><?php } ?>
</div>
<?php if (!empty($customer['certification_date']) || !empty($customer['next_service_date'])) { ?>
<details class="service-flow-legacy"><summary>Previous customer-level dates (reference)</summary>
    CA/RC: <?php echo htmlspecialchars($customer['certification_date'] ?? ''); ?> &nbsp; Next service: <?php echo htmlspecialchars($customer['next_service_date'] ?? ''); ?> &nbsp; Stage: <?php echo htmlspecialchars($customer['next_service_type'] ?? ''); ?>
</details>
<?php } ?>
