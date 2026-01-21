<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">  
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php include_once './header.php'; ?>

    <!-- Page content -->
    <div id="page-content-wrapper" >        

        <!-- Keep all page content within the page-content inset div! -->
        <div class="page-content inset">
            <div class="row">
                <div class="col-md-12">

                    <div class="container">

                        <div class="row">
                            <div class="col-md-12">
                                <a href="#" class="btn btn-xs btn-info newCustomer" >New customer</a>
                            </div>
                        </div>

                        <div class="row"> 
                            <div class="col-sm-10 contact-form"> 
                                <div class="panel panel-default addNewCustomer" style="display: none"> 
                                    <div class="panel-heading">
                                        <h3>Customer Information</h3>
                                    </div>
                                    <form action="addCustomer.php" method="post">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Title</label> 
                                                                <label class="radio-inline"> 
                                                                    <input type="radio" name="mr"id="inlineRadio1" value="male"> Mr.
                                                                </label> 
                                                                <label class="radio-inline"> 
                                                                    <input type="radio" name="miss" id="inlineRadio2" value="female" > Miss.
                                                                </label>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <!--                                                    <div class="col-sm-2 form-group">
                                                                                                                <label>Title</label>
                                                                                                                <input type="text" class="customerTitle" id="customerTitle" name="customerTitle" placeholder="Title" required>                
                                                                                                            </div>-->
                                                        <div class="col-sm-4 form-group">
                                                            <label>First Name</label>
                                                            <input type="text" class="customerFName" id="customerFName" name="customerFName" placeholder="First Name" required>                
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label>Middle Name</label>
                                                            <input type="text" class="customerLName" id="customerLName" name="customerMName" placeholder="Middle Name" required>                
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label>Last Name</label>
                                                            <input type="text" class="customerLName" id="customerLName" name="customerMName" placeholder="Last Name" required>                
                                                        </div>                                
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="row">
                                                                <label>Email Id</label>
                                                                <input type="email" class="customerEmail" id="customerEmail" name="customerEmail" placeholder="Customer Email" required>
                                                            </div>
                                                            <div class="row">
                                                                <!--<div class="col-sm-12">-->
                                                                <label>Contact Person</label>
                                                                <input type="text" class="contactPerson" id="contactPerson" name="contactPerson" placeholder="Contact Person" required>
                                                                <!--</div>-->
                                                            </div>
                                                            <div class="row">
                                                                <label>Contact No</label>
                                                                <input type="text" class="contactNo" id="contactNo" name="contactNo" placeholder="Contact No" required>
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="row">
                                                                <label>Address</label>
                                                                <textarea name="contact-address" rows="7" class="form-control form-group contact-address" id="contact-address" placeholder="Enter address" ></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-sm btn-danger cancelAdding">Cancel</a>
                                            <button type="submit" class="btn btn-xs btn-info" data-dismiss="modal">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div> 
                        </div>                                                 
                        <hr>
                        <div class="row">
                            <div class="col-sm-11 contact-form">
                                <div class="row clearfix">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered table-hover table-sortable" id="tab_logic">
                                            <thead>
                                                <tr >
                                                    <th class="" width="5%" style="border-left: 1px solid #ffffff">
                                                        <small><input type="checkbox"></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>CUSTOMER <span class="glyphicon glyphicon-chevron-up"></span></a> / <a>COMPANY <span class="glyphicon glyphicon-chevron-up"></span></a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>PHONE</a></small>
                                                    </th>
                                                    <th class="">
                                                        <small><a>OPEN BALANCE</a></small>
                                                    </th>
                                                    <th class="" width="10%">
                                                        <small><a>ACTION</a></small>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id='addr0' >
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">
                                                        <input type="checkbox">
                                                    </td>
                                                    <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small><a href="customerAccountDetails.php" class="customerAccount">Pallavi</a></small>
                                                    </td>
                                                    <td data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small>+91 8007822753</small>
                                                    </td>
                                                    <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small>RS 0.00</small>
                                                    </td>
                                                    <td data-name="sel"style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <select name="sel0">
                                                            <option value="">Select Option</option>
                                                            <option value="1">Invoice</option>
                                                            <option value="2">Invoice Quotation</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr id='addr0' >
                                                    <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">
                                                        <input type="checkbox">
                                                    </td>
                                                    <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small><a href="customerAccountDetails.php" class="customerAccount">Pallavi</a></small>
                                                    </td>
                                                    <td data-name="desc" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small>+91 8007822753</small>
                                                    </td>
                                                    <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <small>RS 0.00</small>
                                                    </td>
                                                    <td data-name="sel"style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                        <select name="sel0">
                                                            <option value="">Select Option</option>
                                                            <option value="1">Invoice</option>
                                                            <option value="2">Invoice Quotation</option>
                                                        </select>
                                                    </td>
                                                </tr>
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
    });

    $(document).ready(function () {

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

        $("#add_row").on("click", function () {
            // Dynamic Rows Code

            // Get max row id and set new id
            var newid = 0;
            $.each($("#tab_logic tr"), function () {
                if (parseInt($(this).data("id")) > newid) {
                    newid = parseInt($(this).data("id"));
                }
            });
            newid++;

            var tr = $("<tr></tr>", {
                id: "addr" + newid,
                "data-id": newid
            });

            // loop through each td and create new elements with name of newid
            $.each($("#tab_logic tbody tr:nth(0) td"), function () {
                var cur_td = $(this);

                var children = cur_td.children();

                // add new td and element if it has a nane
                if ($(this).data("name") != undefined) {
                    var td = $("<td></td>", {
                        "data-name": $(cur_td).data("name")
                    });

                    var c = $(cur_td).find($(children[0]).prop('tagName')).clone().val("");
                    c.attr("name", $(cur_td).data("name") + newid);
                    c.appendTo($(td));
                    td.appendTo($(tr));
                } else {
                    var td = $("<td></td>", {
                        'text': $('#tab_logic tr').length
                    }).appendTo($(tr));
                }
            });

            // add delete button and td
            /*
             $("<td></td>").append(
             $("<button class='btn btn-danger glyphicon glyphicon-remove row-remove'></button>")
             .click(function() {
             $(this).closest("tr").remove();
             })
             ).appendTo($(tr));
             */

            // add the new row
            $(tr).appendTo($('#tab_logic'));

            $(tr).find("td button.row-remove").on("click", function () {
                $(this).closest("tr").remove();
            });
        });




        // Sortable Code
        var fixHelperModified = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();

            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width())
            });

            return $helper;
        };

        $(".table-sortable tbody").sortable({
            helper: fixHelperModified
        }).disableSelection();

        $(".table-sortable thead").disableSelection();



        $("#add_row").trigger("click");

    });
</script>

