<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "Idle Page";

    include_once './header.php';
    include_once './dbConnect.php';

    date_default_timezone_set('Asia/Kolkata');
    $nevifation = "IDLE Project";
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

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        $allCustomers = "SELECT distinct cd.customer_id, company_name,contact_no, s.service_type, s.cert_body, s.audit_type, s.consultant, s.auditor,s.audit_date, s.service_id, cd.comments FROM customer_details cd join services s on cd.customer_id = s.customer_id WHERE s.add_service_to = 'Idle' and s.service_id=(select max(tmp.service_id) from services tmp where tmp.customer_id=cd.customer_id) and cd.company_name not like '%(CB)' and s.is_delete <> 'Y'";
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();
    }

    $fetchtime = date("Y-m-d H:i:s");
    $result_fetch = "UPDATE `error_detect` SET `on_result_fetch`= :fetchTime WHERE id = :rowid";
    $statement = $db->prepare($result_fetch);
    $statement->bindParam("fetchTime", $fetchtime);
    $statement->bindParam("rowid", $updaterow);
    $statement->execute();
    ?>

    <?php
    $beforeloadtime = date("Y-m-d H:i:s");
    $result_fetch = "UPDATE `error_detect` SET `before_page_load`= :beforeloadTime WHERE id = :rowid";
    $statement = $db->prepare($result_fetch);
    $statement->bindParam("beforeloadTime", $beforeloadtime);
    $statement->bindParam("rowid", $updaterow);
    $statement->execute();
    ?>

    <!-- Page content -->
    <div id="page-content-wrapper">
        <div class="page-content inset">

            <div class="container" style="margin-top: 20px;padding-left:0px;">
                <div class="row">
                    <div class="col-sm-12 contact-form">
                        <div class="row clearfix" style="margin-top: 20px; ">
                            <div class="col-md-12 table-responsive" style="padding-left:5px; padding-right:5px">
                                <table class="table table-bordered table-hover table-sortable table-striped"
                                    id="table_working_details">
                                    <thead>
                                        <tr>
                                            <!--                                            <th class="">
                                                <small><a>Sr.</a></small>
                                            </th>-->
                                            <th class="">
                                                <small><a>Client</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Ser. Type</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Cert Body</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Cert Fees Pend.</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Cons Fees Pend.</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Auditor</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Audit Date</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Consultant</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Comments</a></small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 0;
                                        foreach ($listOfCustomers as $customer) {

                                            $count = $count + 1;
                                            $sql = "SELECT sum(balance) as pending_cert FROM certification_body group by service_id having service_id=" . $customer['service_id'];
                                            $statement = $db->prepare($sql);
                                            $statement->execute();
                                            $pendingCert = $statement->fetch();

                                            $sql = "SELECT sum(consulting_balance) as pending_cb FROM consulting group by service_id having service_id=" . $customer['service_id'];
                                            $statement = $db->prepare($sql);
                                            $statement->execute();
                                            $pendingConsulting = $statement->fetch();
                                            //                                                        echo $customer['service_id'];
                                            ?>
                                            <tr id='addr0'>
                                                <!--                                                <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $count ?></a></small>
                                                </td>-->
                                                <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>"
                                                    data-name="mail"
                                                    style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><a data-id="<?php echo $customer['customer_id'] ?>"
                                                            class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                                </td>
                                                <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>"
                                                    data-name="desc"
                                                    style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['service_type'] ?></small>
                                                </td>
                                                <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>"
                                                    data-name="sel"
                                                    style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['cert_body'] ?></small>
                                                </td>
                                                <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>"
                                                    data-name="sel"
                                                    style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; text-align: center">
                                                    <small><?php echo $pendingCert["pending_cert"] ?></small>
                                                </td>
                                                <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>"
                                                    data-name="sel"
                                                    style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; text-align: center">
                                                    <small><?php echo $pendingConsulting["pending_cb"] ?></small>
                                                </td>
                                                <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>"
                                                    data-name="sel"
                                                    style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['auditor'] ?></small>
                                                </td>
                                                <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>"
                                                    data-name="sel"
                                                    style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; text-align: center">
                                                    <small><span><?php echo getFlatDate($customer['audit_date']); ?></span><?php echo simpleDateConversion($customer['audit_date']); ?></small>
                                                </td>
                                                <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>"
                                                    data-name="sel"
                                                    style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><span><?php echo $customer['consultant']; ?></span><?php echo $customer['consultant']; ?></small>
                                                </td>
                                                <td data-name="sel"
                                                    style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <div class="table-edit-control">
                                                        <textarea data-row-id="" rows="3" id="autosavecomments"
                                                            class="autosavecomments saveChanges customComments"
                                                            data-row="<?php echo $customer['customer_id'] ?>"
                                                            name="payComments" value=""
                                                            placeholder=""><?php if (isset($customer['comments']))
                                                                echo $customer['comments'] ?></textarea>
                                                        </div>
                                                        <!--<small><?php echo nl2br($customer['comments']); ?></small>-->
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
        <?php include_once 'service_detail_common.php' ?>;
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
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
        //                 $('.dataTable').resize();
        //                alert("after");
        table.draw();
    });

    var table = $('#table_working_details').DataTable({
        //            "scrollY": "450px",
        "sScrollX": "100%",
        "scrollY": '70vh',
        "scrollCollapse": true,
        "paging": false,
        "ordering": false,
        "columnDefs": [
            {
                "width": "8%",
                "targets": 0
            },
            {
                "width": "5%",
                "targets": 1
            },
            {
                "width": "5%",
                "targets": 2
            },
            {
                "width": "5%",
                "targets": 3
            },
            {
                "width": "5%",
                "targets": 4
            },
            {
                "width": "8%",
                "targets": 5
            },
            {
                "width": "8%",
                "targets": 6
            },
            {
                "width": "8%",
                "targets": 7
            },
            {
                "width": "45%",
                "targets": 8
            }
            //                {
            //                    "width": "60%",
            //                    "targets": 9
            //                }
        ]

        //        "paging":         false

    });
    $(document).ready(function () {

        // scroll down the textarea at bottom on page load 
        $(".autosavecomments").animate({ scrollTop: $('.autosavecomments').prop("scrollHeight") }, 1000);

        $('.customComments').change(function (e) {
            //            alert($(this).val());
            var colVal = $(this).val();
            //            $customer = $(this).attr('data-row');
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    id: $(this).attr('data-row'),
                    columnName: "comments",
                    columnValue: colVal,
                },
                error: function (xhr, status, error) {
                    //                    alert("There seems to be issue saving data!");
                },
                success: function (data) {
                    //                    alert(data); 
                    //                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
                    //                    $('.serviceDate').val(data['next_service_date']);
                    //                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
                    //                    $('.nextService').val(data['next_service_type']);
                }
            });
            //            saveData($(this));
        });

        $('.customComments').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();

                var inputVal = $(this).val();
                if (inputVal != "" && inputVal != null)
                    inputVal = $(this).val() + "\n";

                var appendText = "<?php echo date("d.m.Y") . " - " . $_SESSION['abbrevation'] . " - " ?>";
                // Enter pressed... do anything here...
                $(this).val(inputVal + appendText);
            }
        });

        //        $("#menu-toggle").click(function (e) {
        //            e.preventDefault();
        //            $("#wrapper").toggleClass("active");
        //        });

        $('.customerAccount').click(function () {
            var customerID = $(this).attr('data-id');
            var url = 'customerAccountDetails.php'
            var form = $('<form action="' + url + '" method="get">' +
                '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                '</form>');
            $('body').append(form);
            form.submit();
        });

        $('.newCustomer').click(function () {
            //            alert('hi');
            $(".addNewCustomer").show();
            $('.newCustomer').hide();
        });

        $('.cancelAdding').click(function () {
            $('.newCustomer').show();
            $(".addNewCustomer").hide();
        });


        table.columns.adjust().draw();

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