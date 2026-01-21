<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "Payloss Page";

    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {

        $allCustomers = "select * from (select s.customer_id, company_name, contact_no, service_type, add_service_to, contract_details, balance, cert_comment comment, certification_id rowid, 'CB' as consultid from certification_body c join services s on c.service_id = s.service_id join customer_details cd on s.customer_id = cd.customer_id where balance > 0 and add_service_to like '%loss%' and s.is_delete<>'Y' union select s.customer_id, company_name, contact_no, service_type, add_service_to, consulting_fees as contract_details, consulting_balance as balance, consulting_comment comment, consulting_id rowid, 'Consulting' as consultid from consulting c join services s on c.service_id=s.service_id join customer_details cd on s.customer_id = cd.customer_id where consulting_balance >0 and s.add_service_to like '%loss%' and s.is_delete<>'Y') as t";

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
            <div class="container" >
            
                <div class="col-md-12">
                    <div class="" style="margin-top: 20px;">
                        <div class="row">
                            <div class="col-sm-12 contact-form">
                                <div class="row clearfix" style="margin-left:-15px;margin-right:-15px;">
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
                                                    <th class="" >
                                                        <small><a>Phone No.</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Service Type</a></small>
                                                    </th>
                                                    <th class="" >
                                                        <small><a>Project Status</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>Contract Details</a></small>
                                                    </th>                                                    
                                                    <th class="" >
                                                        <small><a>Amount Pending</a></small>
                                                    </th> 
                                                    <th class="" >
                                                        <small><a>CB / Cons</a></small>
                                                    </th> 
                                                    <th class="" >
                                                        <small><a>Comments</a></small>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="7" style="text-align:right">Total:</th>
                                                    <th colspan="2"></th>                                                    
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                $count = 0;
                                                foreach ($listOfCustomers as $customer) {
                                                    $count = $count + 1;
                                                    ?>
                                                    <tr id='addr0' >
                                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a data-id="<?php echo $customer['customer_id'] ?>"><?php echo $count ?></a></small>
                                                        </td>
                                                        <td data-name="mail" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><a data-id="<?php echo $customer['customer_id'] ?>"><?php echo $customer['company_name'] ?></a></small>
                                                        </td>
                                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><?php echo $customer['contact_no'] ?></small>
                                                        </td>
                                                        <td data-name="desc" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><?php echo $customer['service_type'] ?></small>
                                                        </td>
                                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><?php echo $customer['add_service_to'] ?></small>
                                                        </td>
                                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><?php echo $customer['contract_details'] ?></small>
                                                        </td>                                                        
                                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; color:red; border-right: 1px solid #ffffff; text-align: center">
                                                            <?php echo $customer['balance'] ?>
                                                        </td>
                                                        <td data-name="sel" class="customerAccount" data-id="<?php echo $customer['customer_id'] ?>" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <small><?php echo $customer['consultid'] ?></small>
                                                        </td>
                                                        <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                            <div class="table-edit-control">                                                                
                                                                <textarea data-row-id="" rows="3" class="autosavecomments payComments" data-row="<?php echo $customer['rowid'] ?>" pay-type="<?php echo $customer['consultid'] ?>" contract="<?php echo $customer['contract_details'] ?>" name="payComments" value="" placeholder="" ><?php if (isset($customer['comment'])) echo $customer['comment'] ?></textarea>
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
        <?php // include_once 'service_detail_common.php'  ?>;
    </div>

</div>

<script type="text/javascript">

    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
        $(".container").toggleClass("active");
        table.draw();
    });

    $commentValue = "";
    $commentRow = "";
    $(document).ready(function () {
        
        $(".autosavecomments").animate({scrollTop: $('.autosavecomments').prop("scrollHeight")}, 1000);
        
        var table = $('#table_finance_details').DataTable({
//            "scrollY": "450px",
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
                "width": "8%",
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
                "width": "5%",
                "targets": 5
            },
            {
                "width": "5%",
                "targets": 6
            },
            {
                "width": "5%",
                "targets": 7
            },
            {
                "width": "40%",
                "targets": 8
            }],
        "footerCallback": function ( row, data, start, end, display ) {
//            alert("in");
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };            
//            alert("in"+intVal);
// alert("api"+JSON.stringify(api));
            // Total over all pages
            total = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
// alert("total"+total);
            // Total over this page
            pageTotal = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
//                alert("pageTotal"+pageTotal);
            // Update footer
            $( api.column( 6 ).footer() ).html(
                'Total: '+pageTotal 
            );
        }    
                //        "paging":         false
    });
    
     table.columns.adjust().draw();
        
// table.columns.adjust().draw();
        $('.payComments').bind('keypress', function (e) {
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

        $('.payComments').change(function (e) {
            $commentRow = $(this).attr('data-row');
             $payt = $(this).attr('pay-type');
            $cd = $(this).attr('contract');
            savePayComments($(this));
        });

        function savePayComments($selector) {
            $commentValue = $selector.val();
//alert($commentRow);
            $.ajax({
                url: "addComments.php",
//                dataType: "json",
                type: 'POST',
                data: {
                  rowId: $commentRow,
                    rowComment: $commentValue,
                    pay: $payt,
                    contractd: $cd,
                },
                success: function (data) {
//                    alert(data);
                },
                error: function (data, status, err) {
//                    alert(status);
                }
            });
            $selector.removeClass("glowing-border");

            //           $(this).removeClass("form-group ");
            $selector.addClass("lookslike-label");
        }

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
        
    });
</script>