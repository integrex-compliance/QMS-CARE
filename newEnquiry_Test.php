<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active" >
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "New enq Page";
    include_once './header.php';
    include_once './dbConnect.php';
    
    try {
        if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
			$db->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED ;');
            $allCustomers = <<<EOT
SELECT a.* from
(
	SELECT distinct cd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, priority, s.service_id, 
		service_type, marketing_audit_type, audit_type, comments, next_service_date,certification_date, handel_by, cd.is_delete, client_of 
	FROM customer_details cd 
	join (
			select customer_id, max(service_id) as service_id 
			from services
			group by customer_id
		) maxS on cd.customer_id = maxS.customer_id
	join services s on cd.customer_id = s.customer_id
		and s.service_id = maxS.service_id
	WHERE IFNULL(s.add_service_to,'Under Marketing') = 'Under Marketing' 
	and s.service_type NOT IN('RC', 'SA-1', 'SA-2')
	and IFNULL(cd.client_of,'Tele Call') NOT IN('Tele Call', 'Just Dial')	 
	and cd.is_delete <> 'Y'
)a 
UNION ALL 
SELECT b.* 
FROM 
(
	SELECT distinct csd.customer_id, company_name, company_address, contact_person, contact_no, contact_email, client_reference, priority, svc.service_id, 
	service_type, marketing_audit_type, audit_type, comments, next_service_date,certification_date, handel_by, csd.is_delete, client_of 
	FROM customer_details csd left join services svc on csd.customer_id = svc.customer_id 
	WHERE svc.service_id is null 
	and csd.is_delete <> 'Y' 
	and IFNULL(csd.client_of,'Tele Call') NOT IN('Tele Call', 'Just Dial')
)b 
order by FIELD(priority, 'High', 'Medium', 'Low','')
EOT;
			$statement = $db->prepare($allCustomers);
            $statement->execute();
            $listOfCustomers = $statement->fetchAll();
        }
    } catch (Exception $ex) {
        echo $ex;
    }
    
    ?>
    
    <!-- Page content -->
    <div id="page-content-wrapper" >        
        <!-- Keep all page content within the page-content inset div! -->
        <div class="page-content inset">
            <div class="row">
                <div class="col-md-12">
                    <div class="container" style="margin-top: 2px; ">
                        <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">
                            <div class="col-sm-2"><a class="btn btn-info deletebutton">Delete</a></div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-12 table-responsive" style="padding-left:5px;">
                                <table class="table table-bordered table-hover table-sortable table-striped" id="tab_newEnq_list">
                                    <thead>
                                        <tr>
                                            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing()) { ?>
                                                <th class="">
                                                    <small><i class="fa fa-trash-o"></i></small>
                                                </th>
                                            <?php } ?>
<!--                                            <th class="" >
                                                <small><a>Sr.</a></small>
                                            </th>-->
                                            <th class="" >
                                                <small><a>Prio.</a></small>
                                            </th>
                                            <th class="" >
                                                <small><a>Customer</a></small>
                                            </th>                                                    
                                            <th class="" >
                                                <small><a>Contact</a></small>
                                            </th>
                                            <th class="" >
                                                <small><a>Reference By</a></small>
                                            </th>
<!--                                                    <th class="" >
                                                <small><a>Follow up date</a></small>
                                            </th>-->
                                            <th class="" >
                                                <small><a>Comments</a></small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 0;
                                        foreach ($listOfCustomers as $customer) {
                                            if (stripos($customer['company_name'], "consl. ") !== 0) {
                                                $count = $count + 1;
                                                ?> 
                                                <tr id='selectRow' >
                                                    <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing()) { ?>
                                                        <td data-name="name" style="width:40px ; border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">
                                                            <small><input type="checkbox" class="deletecustomerlist" name="deletecustomerlist" value="<?php echo $customer['customer_id'] ?>" ></small>
                                                        </td>
                                                    <?php } ?>
<!--                                                    <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small><a class="customerAccount" id="customerName<?php echo $customer['customer_id'] ?>"><?php echo $count ?></a></small>
                                                    </td>-->
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
                                                            <?php
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
                                                                <?php //foreach ($handelList as $handler) {   ?>                                                                   
                                                                <?php //}  ?>
                                                            </select>
                                                            <!--<input type="text" data-row-id="" rows="3" class="saHandler" data-row="<?php echo $customer['customer_id'] ?>" name="saHandler" value="<?php echo $customer['handel_by'] ?>" placeholder="" >-->
                                                        </div>
                                                    </td>
                                                    <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small><a class="customerAccount" id="customerAddress<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_name'] ?></a></small>
                                                    </td>
                                                    <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small><a class="customerAccount" id="customerContact<?php echo $customer['customer_id'] ?>"><?php echo $customer['contact_no'] ?></a></small>
                                                    </td>
                                                    <td class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small><a class="customerAccount" id="customerContact<?php echo $customer['customer_id'] ?>"><?php echo $customer['client_reference'] ?></a></small>
                                                    </td>
                                                    <td data-name="sel"style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <div class="table-edit-control">                                                                
                                                            <textarea data-row-id="" rows="3" class="autosavecomments saveChanges customComments" data-row="<?php echo $customer['customer_id'] ?>" name="payComments" value="" placeholder="" ><?php if (isset($customer['comments'])) echo $customer['comments'] ?></textarea>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
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

<div id="delete-customer" style="display: none">
    <div id="loading-div" class="ui-corner-all">
        <span style="font-size:30pt;"><i style="padding:5px;" class="fa fa-warning text-warning"></i></span>
        <div> 
            <span style="font-size:11pt;padding:10px" class="text-primary meeting-cancel-confirm-message" >Are you sure that you want to delete users?</span>
        </div>
        <div style="padding:3px;">
            <button title="Click here to Restore"  class="btn btn-primary btn-round btn-round-sm btn-sm delete-confirm">Ok</button>
            <button title="Click here to cancel" class="btn btn-danger  btn-round btn-round-sm btn-sm delete-canceled">Cancel</button>
        </div>
    </div>
</div>

<style>
    .custom-float-label-control {
        margin-bottom: 0em;
    }
    #loading-div-background{
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(255, 255, 255, 0.5);
        width: 100%;
        height: 100%;
    }

    #loading-div{
        width: 300px;
        height: 150px;
        background-color: #fff;
        color:#1468b3;
        border: 5px solid #1468b3;
        text-align: center;
        color: #202020;
        position: absolute;
        left: 50%;
        top: 50%;
        margin-left: -150px;
        margin-top: -100px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        opacity: 1.0;
        behavior: url("/css/pie/PIE.htc"); /* HANDLES IE */

    }

    #loading-div-user-info{
        width: 350px;
        background-color: #fff;
        color:#1468b3;
        border: 1px solid gray;
        color: #202020;
        position: absolute;
        left: 50%;
        top: 50%;
        margin-left: -150px;
        margin-top: -100px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        opacity: 1.0;
        behavior: url("/css/pie/PIE.htc"); /* HANDLES IE */

    }

    #loading-div-background-resend{
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(255, 255, 255, 0.5);
        width: 100%;
        height: 100%;
    }

    #loading-div-background-meetedit{
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(255, 255, 255, 0.5);
        width: 100%;
        height: 100%;
    }

    .unselectable {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>

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

    $(".deletebutton").click(function (){
           var custlist = [];           
           $.each($("input[name='deletecustomerlist']:checked"), function(){            
                custlist.push($(this).val());
            });
//            $('.deletecustomerlist').each(function () {
//                custlist.push($(this).prop('checked'));
//            });
            var dltlist = custlist.join("~");
            var page = 'newEnquiry_Test.php';
//            alert(dltlist);            
            $("#delete-customer").show();
            $('.delete-confirm').click(function () {
                var url = 'deleteCustomerService.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="customersList" value="' + dltlist + '" />' +
                        '<input type="hidden" name="page" value="' + page + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-canceled').click(function () {
                $('#delete-customer').hide();
            });
       });

    $('.consultingDepositDate').change(function () {
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

    $('.saHandler').change(function () {
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
        "ordering": false,
        "columnDefs": [
            {
                "width": "3%",
                "targets": 0
            },
            {
                "width": "3%",
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
                "width": "35%",
                "targets": 5
            }
        ]
                //        "paging":         false
    });

    $(document).ready(function () {

        $(".autosavecomments").animate({scrollTop: $('.autosavecomments').prop("scrollHeight")}, 1000);

        $('.customPriority').change(function () {

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

<?php
//Close the connection
$statement = null;
$db = null;
?>