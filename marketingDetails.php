<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">  
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "Marketing Page";

    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        $allCustomers = "SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, s.service_type, marketing_audit_type, audit_type, comments, next_service_date FROM customer_details cd left join services s on cd.customer_id = s.customer_id WHERE  ((s.add_service_to = 'Under Marketing' and s.service_id=(select max(tmp.service_id) from services tmp where tmp.customer_id=cd.customer_id)) or s.service_id is null) and cd.is_delete<>'Y'";
        $statement = $db->prepare($allCustomers);
        $statement->execute();
        $listOfCustomers = $statement->fetchAll();
    }
    ?>

    <!-- Page content -->
    <div id="page-content-wrapper" >        

        <!-- Keep all page content within the page-content inset div! -->
        <div class="page-content inset">
            <div class="row">
                <div class="col-md-12">

                    <div class="container" style="margin-top: 20px; ">                        
                        <div class="row">
                            <div class="col-sm-12 contact-form" style="padding-left:5px;" >
                                <div class="row clearfix">
                                    <div class="col-md-12 table-responsive" style="padding-left:5px;">
                                        <table class="table table-bordered table-hover table-sortable table-striped" id="tab_marketing_list">
                                            <thead>
                                                <tr > 
                                                    <th class="" >
                                                        <small><a>Sr.</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Client Name</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Address(es)</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Contact No.</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Service Type</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Next Service Due</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Comments</a></small>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $count = 0;
                                                foreach ($listOfCustomers as $customer) {
                                                    $count = $count + 1; ?>
                                                    <tr id='selectRow' >
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerName<?php echo $customer['customer_id'] ?>"><?php echo $count ?></a></small>
                                                        </td>
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerName<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_name'] ?></a></small>
                                                        </td>
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerAddress<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_address'] ?></a></small>
                                                        </td>
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerContact<?php echo $customer['customer_id'] ?>"><?php echo $customer['contact_no'] ?></a></small>
                                                        </td>
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerAuditType<?php echo $customer['customer_id'] ?>"><?php echo $customer['service_type'] ?></a></small>
                                                        </td>
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><span><?php echo getFlatDate($customer['next_service_date']); ?>~</span><?php echo simpleDateConversion($customer['next_service_date']); ?></small>
                                                        </td>
                                                        <td data-name="sel"style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <div class="table-edit-control">                                                                
                                                                <textarea data-row-id="" rows="3" class="saveChanges customComments" data-row="<?php echo $customer['customer_id'] ?>" name="payComments" value="" placeholder="" ><?php if (isset($customer['comments'])) echo $customer['comments'] ?></textarea>
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

<script type="text/javascript">
    
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
        table.draw();
    });

    var table = $('#tab_marketing_list').DataTable({
//        "scrollY": "450px",
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
                "width": "10%",
                "targets": 1
            },
            {
                "width": "10%",
                "targets": 5
            },
            {
                "width": "30%",
                "targets": 6
            },
            {
                "width": "10%",
                "targets": 1
            },
            {
                "width": "6%",
                "targets": 2
            },
            {
                "width": "6%",
                "targets": 3
            },
            {
                "width": "5%",
                "targets": 4
            },            
        ]
                //        "paging":         false
    });

    $(document).ready(function () {        
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

        $('.selectRow').click(function () {
            var id = $(this).data("customer-id");

            $customerName = $("#customerName" + id).text();
            $customerAddress = $("#customerAddress" + id).text();
            $customerContact = $("#customerContact" + id).text();
            $customerAuditType = $("#customerAuditType" + id).text();
            $customerComent = $("#customerComment" + id).text();

            $("#mktgcompanyName").val($customerName);
            $("#mktgcontactNo").val($customerContact);
            $("#mktgauditType").val($customerAuditType);
            $("#mktgcompanyAddress").val($customerAddress);
            $("#mktgcompanyComment").val($customerComent);
            $("#mktgcustomreId").val(id);
            $("#marketingCustomerEdit").modal("show");
//            var customerID = $(this).attr('data-id');
//            var url = 'customerAccountDetails.php'
//            var form = $('<form action="' + url + '" method="get">' +
//                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
//                    '</form>');
//            $('body').append(form);
//            form.submit();
            $("input").trigger("change");
            $("textarea").trigger("change");
        });



        table.columns.adjust().draw();
    });
</script>

