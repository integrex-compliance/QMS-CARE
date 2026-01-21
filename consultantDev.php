<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">  
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "Consultant devlopment Page";

    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
//        $allCustomers = "SELECT * FROM customer_details WHERE customer_id NOT IN (SELECT customer_id FROM services) and is_delete <> 'Y' order by FIELD(priority, 'High', 'Medium', 'Low','')";
        $allCustomers = "SELECT distinct * FROM customer_details where company_name like 'consl.%'order by FIELD(priority, 'High', 'Medium', 'Low','')";
        $statement = $db->prepare($allCustomers);
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
                                        <table class="table table-bordered table-hover table-sortable table-striped" id="tab_newEnq_list">
                                            <thead>
                                                <tr> 
                                                    <th class="" >
                                                        <small><a>Sr.</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Prio.</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Client Name</a></small>
                                                    </th>                                                    
                                                    <th class="" >
                                                        <small><a>Contact No.</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Address</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Follow up date</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Comments</a></small>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $count = 0;
                                                foreach ($listOfCustomers as $customer) {
                                                    $count = $count + 1;
                                                    ?> 
                                                    <tr id='selectRow' >
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerName<?php echo $customer['customer_id'] ?>"><?php echo $count ?></a></small>
                                                        </td>
                                                        <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <?php
                                                            if (!empty($customer["priority"]) && $customer["priority"] == "High") {
                                                                $serPriority = '<img class="img-responsive" alt="High Priority User" title="High Priority User" src="images/medium.png" style="background-color: transparent; height: 18px">';
                                                            } else if (!empty($customer["priority"]) && $customer["priority"] == "Medium") {
                                                                $serPriority = '<img class="img-responsive" alt="High Priority User" title="Medium Priority User" src="images/low.png" style="background-color: transparent; height: 16px">';
                                                            } else if (!empty($customer["priority"]) && $customer["priority"] == "Low") {
                                                                $serPriority = '<img class="img-responsive" alt="High Priority User" title="Low Priority User" src="images/high.png" style="background-color: transparent; height: 15px">';
                                                            } else {
                                                                $serPriority = '<a class="customerAccount" id="customerName"></a>';
                                                            }
                                                            ?>
                                                            <div class="table-edit-control">
                                                                <?php if(isset($customer["priority"]) && !empty($customer["priority"]) && $customer["priority"] == "High"){
                                                                    $serColor = "color: greenyellow";
                                                                } else if(isset($customer["priority"]) && !empty($customer["priority"]) && $customer["priority"] == "Medium"){
                                                                    $serColor = "color: orange";
                                                                } else if(isset($customer["priority"]) && !empty($customer["priority"]) && $customer["priority"] == "Low"){
                                                                    $serColor = "color: maroon";
                                                                } else{
                                                                    $serColor = "";
                                                                }
                                                                ?>
                                                                <select style="<?php echo $serColor ?>" class="customPriority" id="customPriority" name="customPriority" data-id="<?php echo $customer['customer_id'] ?>" value="" >
                                                                    <option value="<?php if (isset($customer["priority"])) echo $customer["priority"] ?>" ><?php
                                                                        if (isset($customer["priority"]) && !empty($customer["priority"])) {
                                                                            echo $customer["priority"];
                                                                        } else {
                                                                            echo "Select ";
                                                                        }
                                                                        ?> </option>
                                                                    <option value="High" style="color: greenyellow">High</option>
                                                                    <option value="Medium"  style="color: orange">Medium</option>
                                                                    <option value="Low" style="color: maroon">Low</option>
                                                                    <!--<option value="volvo"><IMG src="images/medium.png" HEIGHT="15" WIDTH="15" BORDER="0"align="center"></option>-->
                                                                    <?php //foreach ($handelList as $handler) {  ?>                                                                   
    <?php //}  ?>
                                                                </select>
                                                                <!--<input type="text" data-row-id="" rows="3" class="saHandler" data-row="<?php echo $customer['customer_id'] ?>" name="saHandler" value="<?php echo $customer['handel_by'] ?>" placeholder="" >-->
                                                            </div>
                                                            <!--<small ><?php echo $serPriority ?></small>-->
                                                        </td>
    <!--                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerName<?php echo $customer['priority'] ?>"><?php echo $customer['priority'] ?></a></small>
                                                        </td>-->
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerAddress<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_name'] ?></a></small>
                                                        </td>
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerContact<?php echo $customer['customer_id'] ?>"><?php echo $customer['contact_no'] ?></a></small>
                                                        </td>
                                                        <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a class="customerAccount" id="customerContact<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_address'] ?></a></small>
                                                        </td>
                                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <div class="table-edit-control">                                                                
                                                                <small><input type="text" class="consultingDepositDate" name="consultingDepositDate" data-row="<?php echo $customer['customer_id'] ?>"  value="<?php echo simpleDateConversion($customer['certification_date']); ?>" placeholder="" ></small>
                                                            </div>
                                                            <!--<small><span><?php echo getFlatDate($customer['certification_date']); ?>~</span><?php echo simpleDateConversion($customer['certification_date']); ?></small>-->
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
    $(function () {
        
        $(document).on('focus', ".consultingDepositDate", function () {
            if ($(this).hasClass('hasDatepicker') === false) {
                $(this).datepicker({
                    minDate: '-100Y',
                    maxDate: '+100Y',
                    changeYear: true,
                    changeMonth: true,
                    yearRange: "-20:+100",
                    dateFormat: 'dd.mm.yy',
                    autoclose: true
                });
            }
        });
//        $('#rcFollowDate').datepicker({
//            minDate: '-100Y',
//            maxDate: '+100Y',
//            changeYear: true,
//            changeMonth: true,
//            yearRange: "-20:+100",
//            dateFormat: 'dd.mm.yy',
//            autoclose: true
//        });
//        $("#rcFollowDate").keydown(function (e) {
//            e.preventDefault();
//        });
    });
    
    $('.consultingDepositDate').change(function (){
            var customer = $(this).attr('data-row');
            var dateVal = $(this).val();
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    id: customer,
                    columnName: "certification_date",
                    columnValue: dateVal,
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                }
            });
        });
        
        $('.saHandler').change(function (){
//        alert($(this).attr('data-service'));
//        alert($(this).val());
           var svc = $(this).attr('data-service');
           var colVal = $(this).val();
           $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    svcid: svc,
                    handler: colVal,
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                }                
            });
        });

    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
        table.draw();
    });

    var table = $('#tab_newEnq_list').DataTable({
//        "scrollY": "450px",
        "sScrollX": "100%",
        "scrollY": '70vh',
        "scrollCollapse": true,
        "paging": false,
        "columnDefs": [
            {
                "width": "3%",
                "targets": 0
            },
            {
                "width": "5%",
                "targets": 1
            },
            {
                "width": "10%",
                "targets": 2
            },
            {
                "width": "5%",
                "targets": 3
            },
            {
                "width": "8%",
                "targets": 4
            },
            {
                "width": "15%",
                "targets": 5
            },
            {
                "width": "40%",
                "targets": 6
            }
        ]
                //        "paging":         false
    });

    $(document).ready(function () {
        
        $('.customPriority').change(function (){
            
           var id = $(this).attr('data-id');
           var priority = $(this).val();
//           alert(id);
//           alert(priority);
           $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    customId: id,
                    priority: priority,
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                }                
            });
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