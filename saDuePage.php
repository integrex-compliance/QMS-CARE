<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">  
    <?php
    $pageIdentifier = "SA due Page";

    include_once './header.php';
    include_once './dbConnect.php';

    date_default_timezone_set('Asia/Kolkata');
    $nevifation = "SA/RC Due";
    $loginUser = $_SESSION['username'];

    $currentTimestamp = date("Y-m-d H:i:s");
    $pageLoad = "INSERT INTO `error_detect`(`page_name`, `login_user`, `on_page_redirect`) VALUES (:customer, :loginuser, :currenttimestamp)";
    $statement = $db->prepare($pageLoad);
    $statement->bindParam("customer", $nevifation);
    $statement->bindParam("loginuser", $loginUser);
    $statement->bindParam("currenttimestamp", $currentTimestamp);
    $statement->execute();

    $maxid = "SELECT id FROM error_detect WHERE id=( SELECT max(id) FROM error_detect )";
    $statement = $db->prepare($maxid);
    $statement->execute();
    $maxrow = $statement->fetch();
    $updaterow = $maxrow['id'];

    $activeTab = (isset($_GET['tab']) && strtolower(trim($_GET['tab'])) === 'rc') ? 'rc' : 'sa';

    $saCustomers = [];
    $rcCustomers = [];
    $handelList = [];

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        // Fetch SA Due Customers (SA-1 / SA-2)
        $saQuery = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, priority, s.service_id, service_type, marketing_audit_type, audit_type, comments, next_service_date, certification_date, handel_by FROM customer_details cd left join services s on cd.customer_id = s.customer_id WHERE (s.service_type = 'SA-1' or s.service_type = 'SA-2') and ((s.add_service_to = 'Under Marketing' and s.service_id=(select max(tmp.service_id) from services tmp where tmp.customer_id=cd.customer_id)) or s.service_id is null) and cd.is_delete<>'Y' order by FIELD(priority, 'High', 'Medium', 'Low','')";
        $statement = $db->prepare($saQuery);
        $statement->execute();
        $saCustomers = $statement->fetchAll();

        // Fetch RC Due Customers (RC)
        $rcQuery = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, priority, s.service_id, service_type, marketing_audit_type, audit_type, comments, next_service_date, certification_date, handel_by FROM customer_details cd left join services s on cd.customer_id = s.customer_id WHERE s.service_type = 'RC' and ((s.add_service_to = 'Under Marketing' and s.service_id=(select max(tmp.service_id) from services tmp where tmp.customer_id=cd.customer_id)) or s.service_id is null) and cd.is_delete<>'Y' order by FIELD(priority, 'High', 'Medium', 'Low','')";
        $statement = $db->prepare($rcQuery);
        $statement->execute();
        $rcCustomers = $statement->fetchAll();

        // Fetch Handler list once
        $handlerStmt = $db->prepare("SELECT * FROM handler");
        $handlerStmt->execute();
        $handelList = $handlerStmt->fetchAll();
    }

    $fetchtime = date("Y-m-d H:i:s");
    $result_fetch = "UPDATE `error_detect` SET `on_result_fetch`= :fetchTime WHERE id = :rowid";
    $statement = $db->prepare($result_fetch);
    $statement->bindParam("fetchTime", $fetchtime);
    $statement->bindParam("rowid", $updaterow);
    $statement->execute();

    $beforeloadtime = date("Y-m-d H:i:s");
    $result_fetch = "UPDATE `error_detect` SET `before_page_load`= :beforeloadTime WHERE id = :rowid";
    $statement = $db->prepare($result_fetch);
    $statement->bindParam("beforeloadTime", $beforeloadtime);
    $statement->bindParam("rowid", $updaterow);
    $statement->execute();
    ?>

    <style>
        .sarc-tabs-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 14px;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #dbe4f0;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(15, 35, 70, 0.05);
        }
        .sarc-page-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f294a;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }
        .sarc-page-title i {
            color: #2563eb;
        }
        .sarc-tab-switcher {
            display: inline-flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            gap: 4px;
        }
        .sarc-tab-btn {
            border: none;
            background: transparent;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            padding: 7px 18px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }
        .sarc-tab-btn:hover {
            color: #1e293b;
            background: rgba(255, 255, 255, 0.6);
        }
        .sarc-tab-btn.active {
            background: #1d4ed8;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.28);
        }
        .sarc-tab-count {
            background: rgba(15, 23, 42, 0.1);
            color: inherit;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
        }
        .sarc-tab-btn.active .sarc-tab-count {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
        }
    </style>

    <!-- Page content -->
    <div id="page-content-wrapper">
        <div class="page-content inset">
            <div class="row">
                <div class="col-md-12">
                    <div class="container" style="margin-top: 20px;">

                        <!-- Top 2-Tab Header: SA Due & RC Due -->
                        <div class="sarc-tabs-header">
                            <h4 class="sarc-page-title">
                                <i class="fa fa-briefcase"></i> SA / RC Due
                            </h4>
                            <div class="sarc-tab-switcher" role="tablist">
                                <button type="button" class="sarc-tab-btn <?php echo ($activeTab === 'sa') ? 'active' : ''; ?>" data-target-tab="sa" id="btnTabSaDue">
                                    <span>SA Due</span>
                                    <span class="sarc-tab-count"><?php echo count($saCustomers); ?></span>
                                </button>
                                <button type="button" class="sarc-tab-btn <?php echo ($activeTab === 'rc') ? 'active' : ''; ?>" data-target-tab="rc" id="btnTabRcDue">
                                    <span>RC Due</span>
                                    <span class="sarc-tab-count"><?php echo count($rcCustomers); ?></span>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 contact-form" style="padding-left:5px;">
                                <!-- SA Due Tab Pane -->
                                <div id="saDueTabPane" class="sarc-tab-pane" style="display: <?php echo ($activeTab === 'sa') ? 'block' : 'none'; ?>;">
                                    <div class="row clearfix">
                                        <div class="col-md-12 table-responsive" style="padding-left:5px;">
                                            <table class="table table-bordered table-hover table-sortable table-striped" id="tab_saDue_list">
                                                <thead>
                                                    <tr>
                                                        <th><small><a>Sr.</a></small></th>
                                                        <th><small><a>Prio.</a></small></th>
                                                        <th><small><a>Client Name</a></small></th>
                                                        <th><small><a>Contact No.</a></small></th>
                                                        <th><small><a>Han</a></small></th>
                                                        <th><small><a>Due Date</a></small></th>
                                                        <th><small><a>Type</a></small></th>
                                                        <th><small><a>Comments</a></small></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $count = 0;
                                                    foreach ($saCustomers as $customer) {
                                                        $count++;
                                                        if (isset($customer["priority"]) && !empty($customer["priority"]) && $customer["priority"] == "High") {
                                                            $serColor = "color: greenyellow";
                                                        } else if (isset($customer["priority"]) && !empty($customer["priority"]) && $customer["priority"] == "Medium") {
                                                            $serColor = "color: orange";
                                                        } else if (isset($customer["priority"]) && !empty($customer["priority"]) && $customer["priority"] == "Low") {
                                                            $serColor = "color: maroon";
                                                        } else {
                                                            $serColor = "";
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <small><a class="customerAccount" id="customerName<?php echo $customer['customer_id'] ?>"><?php echo $count ?></a></small>
                                                            </td>
                                                            <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <div class="table-edit-control">
                                                                    <select style="<?php echo $serColor ?>" class="customPriority" name="customPriority" data-id="<?php echo $customer['customer_id'] ?>">
                                                                        <option value="<?php if (isset($customer["priority"])) echo $customer["priority"] ?>"><?php
                                                                            echo (isset($customer["priority"]) && !empty($customer["priority"])) ? $customer["priority"] : "Select ";
                                                                        ?></option>
                                                                        <option value="High" style="color: greenyellow">High</option>
                                                                        <option value="Medium" style="color: orange">Medium</option>
                                                                        <option value="Low" style="color: maroon">Low</option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <small><a class="customerAccount" id="customerAddress<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_name'] ?></a></small>
                                                            </td>
                                                            <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <small><a class="customerAccount" id="customerContact<?php echo $customer['customer_id'] ?>"><?php echo $customer['contact_no'] ?></a></small>
                                                            </td>
                                                            <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <div class="table-edit-control">
                                                                    <select class="saHandler" name="saHandler" data-service="<?php echo $customer['service_id'] ?>">
                                                                        <option value="<?php if (isset($customer['handel_by'])) echo $customer['handel_by'] ?>"><?php
                                                                            echo (isset($customer['handel_by']) && !empty($customer['handel_by'])) ? $customer['handel_by'] : "Select ";
                                                                        ?></option>
                                                                        <?php foreach ($handelList as $handler) { ?>
                                                                            <option value="<?php echo $handler['handler_name'] ?>"><?php echo $handler['handler_name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <small><span><?php echo getFlatDate($customer['next_service_date']); ?>~</span><?php echo simpleDateConversion($customer['next_service_date']); ?></small>
                                                            </td>
                                                            <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <small><span><?php echo $customer['service_type']; ?>~</span><?php echo $customer['service_type']; ?></small>
                                                            </td>
                                                            <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <div class="table-edit-control">
                                                                    <textarea rows="3" class="autosavecomments saveChanges customComments" data-row="<?php echo $customer['customer_id'] ?>" name="payComments"><?php if (isset($customer['comments'])) echo $customer['comments'] ?></textarea>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- RC Due Tab Pane -->
                                <div id="rcDueTabPane" class="sarc-tab-pane" style="display: <?php echo ($activeTab === 'rc') ? 'block' : 'none'; ?>;">
                                    <div class="row clearfix">
                                        <div class="col-md-12 table-responsive" style="padding-left:5px;">
                                            <table class="table table-bordered table-hover table-sortable table-striped" id="tab_rcDue_list">
                                                <thead>
                                                    <tr>
                                                        <th><small><a>Sr.</a></small></th>
                                                        <th><small><a>Prio.</a></small></th>
                                                        <th><small><a>Client Name</a></small></th>
                                                        <th><small><a>Contact No.</a></small></th>
                                                        <th><small><a>Hand. By</a></small></th>
                                                        <th><small><a>Due Date</a></small></th>
                                                        <th><small><a>Comments</a></small></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $rcCount = 0;
                                                    foreach ($rcCustomers as $customer) {
                                                        $rcCount++;
                                                        if (isset($customer["priority"]) && !empty($customer["priority"]) && $customer["priority"] == "High") {
                                                            $serColor = "color: greenyellow";
                                                        } else if (isset($customer["priority"]) && !empty($customer["priority"]) && $customer["priority"] == "Medium") {
                                                            $serColor = "color: orange";
                                                        } else if (isset($customer["priority"]) && !empty($customer["priority"]) && $customer["priority"] == "Low") {
                                                            $serColor = "color: maroon";
                                                        } else {
                                                            $serColor = "";
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <small><a class="customerAccount" id="rcCustomerName<?php echo $customer['customer_id'] ?>"><?php echo $rcCount ?></a></small>
                                                            </td>
                                                            <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <div class="table-edit-control">
                                                                    <select style="<?php echo $serColor ?>" class="customPriority" name="customPriority" data-id="<?php echo $customer['customer_id'] ?>">
                                                                        <option value="<?php if (isset($customer["priority"])) echo $customer["priority"] ?>"><?php
                                                                            echo (isset($customer["priority"]) && !empty($customer["priority"])) ? $customer["priority"] : "Select ";
                                                                        ?></option>
                                                                        <option value="High" style="color: greenyellow">High</option>
                                                                        <option value="Medium" style="color: orange">Medium</option>
                                                                        <option value="Low" style="color: maroon">Low</option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <small><a class="customerAccount" id="rcCustomerAddress<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_name'] ?></a></small>
                                                            </td>
                                                            <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <small><a class="customerAccount" id="rcCustomerContact<?php echo $customer['customer_id'] ?>"><?php echo $customer['contact_no'] ?></a></small>
                                                            </td>
                                                            <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <div class="table-edit-control">
                                                                    <select class="saHandler" name="saHandler" data-service="<?php echo $customer['service_id'] ?>">
                                                                        <option value="<?php if (isset($customer['handel_by'])) echo $customer['handel_by'] ?>"><?php
                                                                            echo (isset($customer['handel_by']) && !empty($customer['handel_by'])) ? $customer['handel_by'] : "Select ";
                                                                        ?></option>
                                                                        <?php foreach ($handelList as $handler) { ?>
                                                                            <option value="<?php echo $handler['handler_name'] ?>"><?php echo $handler['handler_name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <small><span><?php echo getFlatDate($customer['certification_date']); ?>~</span><?php echo simpleDateConversion($customer['next_service_date']); ?></small>
                                                            </td>
                                                            <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                                <div class="table-edit-control">
                                                                    <textarea rows="3" class="autosavecomments saveChanges customComments" data-row="<?php echo $customer['customer_id'] ?>" name="payComments"><?php if (isset($customer['comments'])) echo $customer['comments'] ?></textarea>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
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
        </div>
    </div>

    <?php
    $afterloadtime = date("Y-m-d H:i:s");
    $result_fetch = "UPDATE `error_detect` SET `on_page_load`= :afterloadTime WHERE id = :rowid";
    $statement = $db->prepare($result_fetch);
    $statement->bindParam("afterloadTime", $afterloadtime);
    $statement->bindParam("rowid", $updaterow);
    $statement->execute();
    ?>
</div>

<?php
$beforescriptloadtime = date("Y-m-d H:i:s");
$result_fetch = "UPDATE `error_detect` SET `before_script_load`= :beforescriptloadtime WHERE id = :rowid";
$statement = $db->prepare($result_fetch);
$statement->bindParam("beforescriptloadtime", $beforescriptloadtime);
$statement->bindParam("rowid", $updaterow);
$statement->execute();
?>

<script type="text/javascript">
    var saTable = $('#tab_saDue_list').DataTable({
        "sScrollX": "100%",
        "scrollY": '70vh',
        "scrollCollapse": true,
        "paging": false,
        "ordering": false,
        "columnDefs": [
            { "width": "3%", "targets": 0 },
            { "width": "5%", "targets": 1 },
            { "width": "10%", "targets": 2 },
            { "width": "8%", "targets": 3 },
            { "width": "10%", "targets": 4 },
            { "width": "10%", "targets": 5 },
            { "width": "3%", "targets": 6 },
            { "width": "45%", "targets": 7 }
        ]
    });

    var rcTable = $('#tab_rcDue_list').DataTable({
        "sScrollX": "100%",
        "scrollY": '70vh',
        "scrollCollapse": true,
        "paging": false,
        "ordering": false,
        "columnDefs": [
            { "width": "5%", "targets": 0 },
            { "width": "3%", "targets": 1 },
            { "width": "10%", "targets": 2 },
            { "width": "10%", "targets": 3 },
            { "width": "10%", "targets": 4 },
            { "width": "10%", "targets": 5 },
            { "width": "40%", "targets": 6 }
        ]
    });

    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
        if ($('#saDueTabPane').is(':visible')) {
            saTable.columns.adjust().draw();
        }
        if ($('#rcDueTabPane').is(':visible')) {
            rcTable.columns.adjust().draw();
        }
    });

    $(document).ready(function () {
        // Tab switching between SA Due & RC Due
        $('.sarc-tab-btn').on('click', function () {
            var target = $(this).attr('data-target-tab');
            $('.sarc-tab-btn').removeClass('active');
            $(this).addClass('active');

            if (target === 'rc') {
                $('#saDueTabPane').hide();
                $('#rcDueTabPane').show();
                rcTable.columns.adjust().draw();
            } else {
                $('#rcDueTabPane').hide();
                $('#saDueTabPane').show();
                saTable.columns.adjust().draw();
            }
        });

        $(".autosavecomments").animate({scrollTop: $('.autosavecomments').prop("scrollHeight")}, 1000);

        $(document).on('change', '.customPriority', function () {
            var id = $(this).attr('data-id');
            var priority = $(this).val();
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    customId: id,
                    priority: priority
                }
            });
        });

        $(document).on('keypress', '.customComments', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                var inputVal = $(this).val();
                if (inputVal != "" && inputVal != null) {
                    inputVal = $(this).val() + "\n";
                }
                var appendText = "<?php echo date("d.m.Y") . " - " . $_SESSION['abbrevation'] . " - " ?>";
                $(this).val(inputVal + appendText);
            }
        });

        $(document).on('change', '.saHandler', function () {
            var svc = $(this).attr('data-service');
            var colVal = $(this).val();
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    svcid: svc,
                    handler: colVal
                }
            });
        });

        $(document).on('change', '.customComments', function () {
            var colVal = $(this).val();
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    id: $(this).attr('data-row'),
                    columnName: "comments",
                    columnValue: colVal
                }
            });
        });

        $(document).on('click', '.customerAccount', function () {
            var customerID = $(this).attr('data-id');
            var url = 'customerAccountDetails.php';
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });

        if ($('#saDueTabPane').is(':visible')) {
            saTable.columns.adjust().draw();
        } else {
            rcTable.columns.adjust().draw();
        }
    });
</script>

<?php
$afterscriptloadtime = date("Y-m-d H:i:s");
$result_fetch = "UPDATE `error_detect` SET `on_script_load`= :afterscriptloadtime WHERE id = :rowid";
$statement = $db->prepare($result_fetch);
$statement->bindParam("afterscriptloadtime", $afterscriptloadtime);
$statement->bindParam("rowid", $updaterow);
$statement->execute();
?>
