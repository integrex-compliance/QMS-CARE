<div class="service-flow-dates" data-flow-id="<?php echo (int)$stageFlow['id']; ?>">
    <div class="qms-milestones-row">
    <?php foreach (['certification_date' => 'CA/RC Date', 'next_service_date' => 'Next Service Date', 'next_service_type' => 'Next Service Stage'] as $column => $label) {
        $value = $stageFlow[$column];
        if ($column !== 'next_service_type') $value = $value ? date('d.m.Y', strtotime($value)) : '';
        $inputId = ($dateInputPrefix ?? '') . 'flow-' . (int)$stageFlow['id'] . '-' . $column;
    ?>
        <div class="qms-milestone-field">
            <label for="<?php echo $inputId; ?>"><i class="fa fa-calendar"></i> <?php echo $label; ?></label>
            <?php if ($column === 'next_service_type') { ?>
            <select id="<?php echo $inputId; ?>" class="flow-milestone" data-id="<?php echo (int)$stageFlow['id']; ?>" data-column="<?php echo $column; ?>" <?php echo $isReadOnlyServiceType ? 'disabled readonly' : ''; ?>>
                <option value="">Select next stage</option>
                <?php if ($value !== '' && !in_array($value, $allowedFlowStages ?? [], true)) { ?>
                <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" selected disabled><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?> (existing)</option>
                <?php } ?>
                <?php foreach ($allowedFlowStages ?? [] as $stage) { ?>
                <option value="<?php echo htmlspecialchars($stage, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $stage === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($stage, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php } ?>
            </select>
            <?php } else { ?>
            <input id="<?php echo $inputId; ?>" class="flow-milestone lookslike-label" data-id="<?php echo (int)$stageFlow['id']; ?>" data-column="<?php echo $column; ?>" value="<?php echo htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="dd.mm.yyyy" <?php echo $isReadOnlyServiceType ? 'readonly' : ''; ?>>
            <?php } ?>
        </div>
    <?php } ?>
    </div>
</div>
