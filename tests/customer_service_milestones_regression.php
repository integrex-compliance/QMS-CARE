<?php
// Render the real template with isolated fixtures; no database or session writes.
function renderMilestones(array $customerFlows, $isReadOnlyServiceType = false) {
    $_GET['customerid'] = 1;
    $_SESSION['service_flow_csrf'] = 'test';
    $allServiceStages = ['CA', 'SA-1', 'Gold', 'Silver', 'Bronze'];
    $serviceStagesByName = ['ISO 9001' => ['CA', 'SA-1'], 'ZED' => ['Gold', 'Silver', 'Bronze']];
    $customer = [];
    ob_start();
    require __DIR__ . '/../customer_service_milestones.php';
    $html = ob_get_clean();
    $document = new DOMDocument();
    libxml_use_internal_errors(true);
    $document->loadHTML($html);
    libxml_clear_errors();
    return new DOMXPath($document);
}
function expectMilestone($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
$flows = [];
foreach ([['ISO 9001', 'CA'], ['ISO 9001', 'CA'], ['ISO 9001', 'SA-1'], ['ZED', 'Gold'], ['ZED', 'Silver'], ['ZED', 'Bronze']] as $i => $entry) {
    $flows[] = ['id' => $i + 1, 'service_id' => $i + 101, 'service_name' => $entry[0], 'current_stage' => $entry[1], 'active' => 1, 'certification_date' => '2026-11-28', 'next_service_date' => null, 'next_service_type' => ''];
}
$xpath = renderMilestones($flows);
expectMilestone($xpath->query('//section[@class="service-flow-group"]')->length === 2, 'One group per service type');
expectMilestone($xpath->query('//h3[contains(., "ISO 9001")]')->length === 1, 'Service type name appears in a single group heading');
expectMilestone($xpath->query('//div[@class="service-flow-dates"]')->length === 2, 'Overview has one date row per service type, regardless of stage count');
expectMilestone($xpath->query('//input')->length === 4 && $xpath->query('//select[@data-column="next_service_type"]')->length === 2, 'Each type shows dates and a restricted next-stage dropdown');
expectMilestone($xpath->query('//section[@data-flow-name="ISO 9001"]//option[@value="Gold"]')->length === 0, 'ISO dropdowns must not offer ZED stages');
expectMilestone($xpath->query('//section[@data-flow-name="ZED"]//option[@value="CA"]')->length === 0, 'ZED dropdowns must not offer ISO stages');
expectMilestone($xpath->query('//label[text()="Add Service Stage"]')->length === 2, 'Each type has one Add Service Stage control');
expectMilestone($xpath->query('//select[@class="flow-current-stage"]')->length === 2, 'Stage addition remains available once per type');
expectMilestone($xpath->query('//input[@id="overview-flow-1-certification_date" and @value="28.11.2026"]')->length === 1, 'Existing dates are restored in the overview');
$reordered = renderMilestones(array_reverse($flows));
expectMilestone($reordered->query('//section[@data-flow-name="ISO 9001"]//select[@class="flow-current-stage" and @data-id="1"]')->length === 1, 'Overview keeps its date record when stage display order changes');
$ids = [];
foreach ($xpath->query('//*[@id]') as $element) $ids[] = $element->getAttribute('id');
expectMilestone(count($ids) === count(array_unique($ids)), 'Controls must have unique IDs');
$flows[0]['active'] = 0;
$xpath = renderMilestones($flows);
expectMilestone($xpath->query('//section[@data-flow-name="ISO 9001"]')->length === 1, 'Service type stays visible while another stage is active');
expectMilestone($xpath->query('//div[@data-flow-id="1"]')->length === 0, 'Archived rows must stay hidden');
expectMilestone($xpath->query('//section[@data-flow-name="ISO 9001"]//select[@class="flow-current-stage" and @data-id="2"]')->length === 1, 'Deleting the date record selects the next active record');
$xpath = renderMilestones($flows, true);
expectMilestone($xpath->query('//select[not(@disabled)]')->length === 0 && $xpath->query('//input[not(@readonly)]')->length === 0, 'Read-only accounts must not expose stage or date editing');
$xpath = renderMilestones([]);
expectMilestone($xpath->query('//section')->length === 0 && $xpath->query('//p[@class="service-flow-empty"]')->length === 1, 'Empty state must not leave an empty group');
$unconfigured = $flows[1];
$unconfigured['service_name'] = 'Unconfigured';
$xpath = renderMilestones([$unconfigured]);
expectMilestone($xpath->query('//select[@class="flow-current-stage" and @disabled]')->length === 1, 'Unconfigured types must not offer global stage choices');
expectMilestone($xpath->query('//option[@value!=""]')->length === 0, 'An empty mapping must remain empty in all dropdowns');
// Verify the date partial used by the overview retains each record's values.
$isReadOnlyServiceType = false;
ob_start();
foreach ($flows as $stageFlow) require __DIR__ . '/../customer_service_stage_dates.php';
$document = new DOMDocument();
$document->loadHTML(ob_get_clean());
$xpath = new DOMXPath($document);
expectMilestone($xpath->query('//input|//select')->length === 18, 'Date partial renders three fields for each supplied record');
expectMilestone($xpath->query('//div[@data-flow-id="4"]//*[@data-id="4"]')->length === 3, 'Date saves must still use the original stage ID');
expectMilestone($xpath->query('//input[@data-column="certification_date" and @value="28.11.2026"]')->length === 6, 'Existing dates remain populated');
$isReadOnlyServiceType = true;
ob_start();
require __DIR__ . '/../customer_service_stage_dates.php';
$document = new DOMDocument();
$document->loadHTML(ob_get_clean());
expectMilestone((new DOMXPath($document))->query('//input[not(@readonly)]')->length === 0, 'Moved date fields must respect read-only access');
echo "PASS: one compact overview per type, restored dates, Add Service Stage controls, independent stage dates and read-only access\n";
