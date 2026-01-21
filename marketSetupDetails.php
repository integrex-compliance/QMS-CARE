<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "Marketing Setup";

    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        $allCustomers = "select s.customer_id, company_name, service_type,add_service_to, cert_body, contract_details, balance from certification_body c join services s on c.service_id = s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance != '0' and (contract_details like 'Marketing Setup%' or contract_details like 'Marketing%' or contract_details like 'Setup%' or contract_details like 'Mark%' or contract_details like 'Marke%') and cd.is_delete<>'Y'";
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();

        //select  * from consulting c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where consulting_balance >0
        //select  * from certification_body c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance >0
    }
    ?>

    <!-- Page content -->
    <div id="page-content-wrapper">
        <!-- Keep all page content within the page-content inset div! -->
        <div class="page-content inset">
            <div class="row">
                <div class="col-md-12">

                    <div class="container" style="margin-top: 20px;">
                        <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">
                            <div class="col-sm-1"><a class="btn btn-info filterBtn"><i class="fa fa-filter "></i></a></div>                            
                            <div class="col-sm-3 float-label-control custom-float-label-control">
                                <select name="cert-body" class="form-control cert-body" id="cert-body">
                                    <option value="">Select cert body</option>
                                    <?php foreach ($certList as $certb) { ?>
                                        <option value="<?php echo $certb['cert'] ?>">
                                            <?php echo $certb['cert'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-12 table-responsive">

                                <table class="table table-bordered table-striped table-hover table-sortable" id="table_finance_details">
                                    <thead>
                                        <tr>
                                            <th class="">
                                                <small><a>Sr.</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Client Name</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Service Type</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Contract Details</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Project Status</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Cert Body</a></small>
                                            </th>
                                            <th class="" width="10%">
                                                <small><a>Amount Pending</a></small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 0;
                                        foreach ($listOfCustomers as $customer) {
                                            $count = $count + 1;
                                            ?>
                                            <tr id='addr0' data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount">
                                                <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $count ?></a></small>
                                                </td>
                                                <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><a data-id="<?php echo $customer['customer_id'] ?>" class="customerAccount"><?php echo $customer['company_name'] ?></a></small>
                                                </td>
                                                <td data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['service_type'] ?></small>
                                                </td>
                                                <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['contract_details'] ?></small>
                                                </td>
                                                <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['add_service_to'] ?></small>
                                                </td>
                                                <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small><?php echo $customer['cert_body'] ?></small>
                                                </td>
                                                <td data-name="sel" style="border-top: 1px solid #ffffff; color:red; border-right: 1px solid #ffffff; text-align: center">
                                                    <small><?php echo $customer['balance'] ?></small>
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

<script type="text/javascript">
    var table = $('#table_finance_details').DataTable({
//            "scrollY": "450px",
        "sScrollX": "100%",
        "scrollY": '70vh',
        "scrollCollapse": true,
        "paging": false,
        "columnDefs": [
            {
                "width": "5%",
                "targets": 0
            },
            {
                "width": "20%",
                "targets": 1
            },
            {
                "width": "10%",
                "targets": 2
            },
            {
                "width": "15%",
                "targets": 3
            },
            {
                "width": "15%",
                "targets": 4
            },
            {
                "width": "15%",
                "targets": 5
            },
            {
                "width": "20%",
                "targets": 5
            },
        ]
                //        "paging":         false
    });

    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
        table.draw();
    });

    $(document).ready(function () {

        $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var cb = $(".cert-body").val();
                    var isServiceType = false;
                    //            alert(cb);
                    if (cb == "") {
                        return true;
                    }
                    if (cb != "") {
                        var cbstr = data[5] || ""; // use data for the age column
                        //            alert(cbstr );
                        if (cbstr.toUpperCase().trim() == cb.toUpperCase().trim()) {
                            //                alert("test");
                            isServiceType = true;
                        }
                    } else {
                        isServiceType = true;
                    }
                    return isServiceType;
                }
        );

        $('.cert-body').change(function () {
            var serviceType = $(".cert-body").val();
            //            alert("t");
            table.draw();
        });

        $('.customerAccount').click(function () {
            var customerID = $(this).attr('data-id');
            var url = 'customerAccountDetails.php'
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });

        //        $("#menu-toggle").click(function (e) {
        //            e.preventDefault();
        //            $("#wrapper").toggleClass("active");
        //        });

        $('.newCustomer').click(function () {
            //            alert('hi');
            $(".addNewCustomer").show();
            $('.newCustomer').hide();
        });

        $('.cancelAdding').click(function () {
            $('.newCustomer').show();
            $(".addNewCustomer").hide();
        });

        $('.customerAccount').click(function () {
            var customerID = $(this).attr('data-id');
            var url = 'customerAccountDetails.php'
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });

        table.columns.adjust().draw();

    });
</script>
