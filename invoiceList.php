<div id="wrapper" class="active"> 

    <?php
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    $pageIdentifier = "Invoices";

    include_once './header.php';
    include_once './dbConnect.php';

    $allFirms = "SELECT * FROM consulting_firm";
    $statement = $db->prepare($allFirms);
    $statement->execute();
    $firmList = $statement->fetchAll();
    ?>
    <div id="page-content-wrapper">

        <div class="page-content inset">
            <div class="container" style="margin-top: 60px;">
                <div class="row">                    
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="row" style="">
                            <div class="col-xs-12  text-center btn  qms_complete_btn ">
                                <span><i class="fa  ">Consulting Firm</i></span>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px">
                            <div class="col-sm-8 col-sm-offset-2 form-group float-label-control">
                                <label>Consulting Firm</label>
                                <select class="add-to-service form-control firmInvoices" id="firmInvoices" name="firmInvoices" data-row-id="" value="">
                                    <option value="" >Select Consulting Firm</option>
                                    <?php foreach ($firmList as $firms) { ?>
                                    <option 
                                    value="<?php echo $firms['consulting_firm_id'] ?>" 
                                    data-gst="<?php echo $firms['is_gst_applicable'] ?>" 
                                    data-gst-percent="<?php echo $firms['gst_percent'] ?>">
                                    <?php echo $firms['firm_name'] ?>
                                    </option> 
                                    <?php } ?>
                                </select>                
                            </div>
                        </div>
                        <div class="row addNewService">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2 form-group float-label-control">
                        <div class="row" style="">
                            <div class="col-xs-12  text-center btn  qms_complete_btn ">
                                <span><i class="fa  ">Invoice Generated</i></span>
                            </div>
                        </div>
                        <div class="row invoiceInfo" style="">
                            <div class="well" >
                                <ul class="list-group checked-list-box operateInvoices">
                                    <span>Select consulting firm to see invoice generated</span>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row clearfix" >
                    <div class="col-sm-8 col-sm-offset-2 main_content table-responsive">
                        <table class="table table-hover table-sortable table-striped" id="tab_proforma_inv_list" width="100%">
                            <thead>
                                <tr class="invoice-list-head">

                                </tr>
                            </thead>
                            <tbody class="invoice-table-data">

                            </tbody>    
                        </table>
                    </div>
                </div>
            </div>    
        </div>

        <div id="delete-details" style="display: none">
            <div id="loading-div" class="ui-corner-all">
                <span style="font-size:30pt;"><i style="padding:5px;" class="fa fa-warning text-warning"></i></span>
                <div> 
                    <span style="font-size:11pt;padding:10px" class="text-primary meeting-cancel-confirm-message" >Are you sure that you want to delete details?</span>
                </div>
                <div style="padding:3px;">
                    <button title="Click here to delete"  class="btn btn-primary btn-round btn-round-sm btn-sm delete-confirm-ok">Ok</button>
                    <button title="Click here to cancel" class="btn btn-danger  btn-round btn-round-sm btn-sm delete-cancel">Cancel</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="invoice-msg-sent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">Message</h4>
                    </div>
                    <form class="customeraddform" id="customeraddform" action="invoiceMailAttach.php" method="post">
                        <div class="modal-body">
                            <div class="row" style="">
                                <div class="col-sm-12 form-group float-label-control">
                                    <!--                            <div class="row">
                                                                    <div class="col-sm-12 form-group float-label-control">-->
                                    <label for="">Message</label>
                                    <textarea name="msgforClient" rows="5" class="form-control form-group" id="companyAddress" placeholder="Message">Your invoice has been sent, please find an attachment.</textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="invoiceFirmId" name="invoiceFirmId" value="">
                        <input type="hidden" class="invoiceCustomerId" name="invoiceCustomerId" value="">
                        <input type="hidden" class="invoiceid" name="invoiceid" value="">
                        <input type="hidden" class="invoiceno" name="invoiceno" value="">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-xs btn-danger cancelInvSent">Cancel</button>
                            <button type="submit" class="btn btn-xs btn-info">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");
            table.draw();
        });

        $(".firmInvoices").on("change", function () {
    $.ajax({
        url: "invQuotDetails.php",
        type: "GET",
        dataType: "json",
        data: {
            firmid: $('.firmInvoices').val(),
        },
        success: function (data) {
            let selectedFirmId = $('.firmInvoices').val();

            $('.invoiceInfo').hide();
            $('.invoice-list-head').empty();
            $('.invoice-table-data').empty();

            // Build header
            let headerHtml = `
                <th style="border-left: 1px solid #ffffff"><small><a>Invoice No.</a></small></th>
                <th><small><a>Invoice Date</a></small></th>
                <th><small><a>Name of Customer</a></small></th>
            `;

            // Add GST column only if firm ID is 34
            if (selectedFirmId === "34") {
                headerHtml += `<th><small><a>Customer GST No.</a></small></th>`;
            }

            headerHtml += `
                <th><small><a>Service</a></small></th>
                <th><small><a>Total Amount</a></small></th>
                <th><small><a>Edit/View/Send</a></small></th>
            `;

            $('.invoice-list-head').append(headerHtml);

            // Loop through invoice data
            for (let key in data[0]) {
                let invoice = data[0][key];
                let firmInfo = data[1];

                let dateVal = (invoice['invoice_date'] === '0000-00-00') ? "" : moment(invoice['invoice_date']).format('DD.MM.YYYY');
                let iconClass = (invoice['inv_sent_count'] === '0') ? "fa-envelope-o" : "fa-envelope";
                let iconTitle = (invoice['inv_sent_count'] === '0') ? "Send Invoice" : "Re-send Invoice";

                let rowHtml = `<tr>
                    <td><small>${firmInfo['firm_abbrevation']}/${invoice['invoice_no']}</small></td>
                    <td><small>${dateVal}</small></td>
                    <td><small>${invoice['customer_name']}</small></td>`;

                if (selectedFirmId === "34") {
                    rowHtml += `<td><small>${invoice['customer_gst'] || '-'}</small></td>`;
                }

                rowHtml += `
                    <td><small>${invoice['svc_desc']}</small></td>
                    <td><small>${invoice['total_amount']}</small></td>
                    <td>
                        <a class="editInvoice" data-id="${invoice['invoice_id']}" data-firm="${invoice['firm_id']}" data-customer="${invoice['customer_id']}" title="Edit invoice"><i class="fa fa-pencil-square"></i></a>
                        <a class="viewInvoice" data-id="${invoice['invoice_id']}" data-invoice="${invoice['invoice_no']}" data-customer="${invoice['customer_id']}" title="View invoice"><i class="fa fa-file-pdf-o"></i></a>
                        <a class="sentInvoice" data-id="${invoice['invoice_id']}" data-invoice="${invoice['invoice_no']}" data-firm="${invoice['firm_id']}" data-firmname="${firmInfo['firm_name']}" data-customer="${invoice['customer_id']}" title="${iconTitle}"><i class="fa ${iconClass}"></i></a>
                    </td>
                </tr>`;

                $('.invoice-table-data').append(rowHtml);
            }
        },
        error: function () {
            $('.addNewService').empty();
        }
    });
});


        $(".cancelInvSent").click(function () {
            $(".invoiceFirmId").val("");
            $(".invoiceCustomerId").val("");
            $(".invoiceid").val("");
            $(".invoiceno").val("");
            $("#invoice-msg-sent").modal("hide");
        });

        $('body').on('click', ".sentInvoice", function (e) {
            var firmanme = $(this).attr('data-firmname');
//            alert($(this).attr('data-firmname'));
            $(".invoiceFirmId").val($(this).attr('data-firm'));
            $(".invoiceCustomerId").val($(this).attr('data-customer'));
            $(".invoiceid").val($(this).attr('data-id'));
            $(".invoiceno").val($(this).attr('data-invoice'));
            $("#invoice-msg-sent").modal("show");
//        alert("in");
//            ShowProgressAnimation();
//            var customerID = $(this).attr('data-customer');
//            var firmId = $(this).attr('data-firm');
//            var invoiceid = $(this).attr('data-id');
//            var invoiceno = $(this).attr('data-invoice');
////            alert(invoiceno+" -> "+invoiceid+" -> "+firmId+" -> "+ customerID);
//            // open popup
//            var url = 'invoiceMailAttach.php'
//            var form = $('<form action="' + url + '" method="post" >' +
//                    '<input type="hidden" name="invoiceCustomerId" value="' + customerID + '" />' +
//                    '<input type="hidden" name="invoiceFirmId" value="' + firmId + '" />' +
//                    '<input type="hidden" name="invoiceid" value="' + invoiceid + '" />' +
//                    '<input type="hidden" name="invoiceno" value="' + invoiceno + '" />' +
//                    '</form>');
//            $('body').append(form);
//            form.submit();
        });

        $('body').on('click', ".editInvoice", function (e) {
        //alert($(this).attr('data-id'));
            $.ajax({
                url: "invQuotDetails.php",
                type: "GET",
                dataType: "json",
                data: {
                    invoiceId: $(this).attr('data-id'),
//                    firmId: $(this).attr('data-firm'),
//                    customer: $(this).attr('data-customer'),
                },
                success: function (data)
                {
//                    alert(data[4]['invoice_date']);
                    $('.invoiceEditId').val(data[0]['invoice_id']);
                    $('.invoiceEditCustomerId').val(data[0]['customer_id']);
                    $('.invoiceEditFirmId').val(data[0]['firm_id']);
                    $('.invoiceconsultingFirmName').val(data[0]['firm_name']);
                    $('.invoiceDtEdit').val(moment(data[0]['invoice_date']).format('DD.MM.YYYY'));
                    $('.netPriceEdit').val(data[0]['net_price']);
                    $('.serviceTaxEdit').val(data[0]['service_tax']);
                    $('.invoiceClient').val(data[2]['company_name']);
                    $('.invoiceEditNo').val(data[0]['invoice_no']);
                    if (data[0]['tax_type']) {
    $("input[name='taxTypeEdit'][value='" + data[0]['tax_type'] + "']").prop('checked', true);
}
                    var firmId = data[0]['firm_id'];
                    var firmOption = $('#firmInvoices option[value="' + firmId + '"]');
                    var isGstApplicable = (firmOption.data('gst'));
                    var gstPercent = parseFloat(firmOption.data('gst-percent')) || 0;

// 2. Store GST info in hidden field or attribute for later use
                    $('#invoiceEditFirmId')
                        .attr('data-gst', isGstApplicable)
                        .attr('data-gst-percent', gstPercent);

                    $(".serviceAmtTdedit").empty();

                    var svcdesc = data[0]['svc_desc'].split(',');
                    var svcdescLen = svcdesc.length;

                    var svcamt = data[0]['svc_amount'].split(',');
                    var svcamtLen = svcamt.length;

                    var max = (Math.max(svcdescLen, svcamtLen));

                    for (var key = 0; key < max; key++) {
                        var svc = key <= svcdescLen && svcdesc[key] != null && svcdesc[key] != "" ? svcdesc[key] : "";
                        var amt = key <= svcamtLen && svcamt[key] != null && svcamt[key] != "" ? svcamt[key] : "";
//                        alert(svc);
                        if (key == 0) {
                            $('.invoiceServiceEdit').val(svc);
                            $(".invoiceServiceAmtEdit").val(amt);
                        } else {
                            if (svc != "") {
                                $('.serviceAmtTdedit').append('<div class="row removeServiceEditRow">\n\
                               <div class="col-sm-8 form-group float-label-control" style="padding-top:10px;">\n\
                                    <label for="">Service</label>\n\
                                    <input type="text" name="invoiceServiceEdit[]" class="form-control form-group invoiceServiceEdit" id="invoiceServiceEdit" value="' + svc + '" placeholder="Service">\n\
                                </div>\n\
                                <div class="col-sm-3 form-group float-label-control" style="padding-top:10px;">\n\
                                    <label for="">Amount</label>\n\
                                    <input type="number" name="invoiceServiceAmtEdit[]" class="form-control form-group invoiceServiceAmtEdit" id="invoiceServiceAmtEdit" value="' + amt + '" placeholder="Amount" step="0.01" inputmode="decimal">\n\
                                </div>\n\
                                <div class="col-sm-1 removeServiceEditCol">\n\
                                    <a class="removeServiceEditAmt pull-right" id="removeServiceEditAmt" style="cursor: pointer;">\n\
                                    <i style="margin-top: 9px; color: black;" class="glyphicon glyphicon-remove" title="Add new Service"></i>\n\
                                    </a>\n\
                                </div>\n\
                                </div>');
                            }
                        }
                    }

//                    $('.invoiceServiceEdit').val(data[0]['svc_desc']);
//                    $('.invoiceServiceAmtEdit').val(data[0]['svc_amount']);
                    $('.invoiceEditDate').val(moment(data[4]['invoice_date']).format('DD.MM.YYYY'));
                    if (data[5]['invoice_date'] != "" && data[5]['invoice_date'] != null)
                        $('.invoiceNxtEditDate').val(moment(data[5]['invoice_date']).format('DD.MM.YYYY'));

//                    $('.serviceAmtTdedit').empty();
//                    for (var key in data[1]) {
//                        $('.serviceAmtTdedit').append('<div class="row">\n\
//                                <div class="col-sm-8 form-group float-label-control" style="padding-top:10px;">\n\
//                                    <label for="">Service</label>\n\
//                                    <input type="text" name="invoiceServiceEdit[]" class="form-control form-group invoiceServiceEdit" id="invoiceServiceEdit" value="' + data[1][key]['service_desc'] + '" placeholder="Service">\n\
//                                </div>\n\
//                                <div class="col-sm-4 form-group float-label-control" style="padding-top:10px;">\n\
//                                    <label for="">Amount</label>\n\
//                                    <input type="number" name="invoiceServiceAmtEdit[]" class="form-control form-group invoiceServiceAmtEdit" id="invoiceServiceAmtEdit" value="' + data[1][key]['service_amount'] + '" placeholder="Amount">\n\
//                                </div>\n\
//                            </div>');
//                    }
                    $('input').trigger('change');
                    $('textarea').trigger('change');
                    $('#invoiceEditModal').modal("show");
                },
                error: function (data, status, err)
                {
                    $('.addNewService').empty();
                    alert(status);
                }
            });
        });

        $('.invoiceServiceEdit').change(function () {
            var initialText = $('.other').val();
            //$('.editOption').val(initialText);
            var selected = $('option:selected', this).attr('class');
            var optionText = $('.other').text();

            if (selected == "other") {
                $('.editOption').show();
                $('.editOption').keyup(function (e) {
                    if (e.keyCode == 13)
                        $('.editOption').hide();
                    var editText = $('.editOption').val();
                    $('.other').val(editText);
                    $('.other').html(editText);
                });
            } else {
                $('.editOption').hide();
            }
        });
//        $('body').on('click', ".deleteInvoice", function (e) {
//            <!--<a class="deleteInvoice" data-id="' + data[0][key]['invoice_id'] + '" data-firm="' + data[0][key]['firm_id'] + '" data-customer="' + data[0][key]['customer_id'] + '" title="Delete envoice" style="padding: 1px 5px; "><i class="fa fa-trash" style="padding:3px"></i></a>-->
//            var invoiceid = $(this).attr('data-id');
//            $('#delete-details').show();
//            $('.delete-confirm-ok').click(function () {//
//                var url = 'invQuotDetails.php'
//                var form = $('<form action="' + url + '" method="post">' +
//                        '<input type="hidden" name="invoice_id" value="' + invoiceid + '" />' +
//                        '</form>');
//                $('body').append(form);
//                form.submit();
//            });
//            $('.delete-cancel').click(function () {//
//                $('#delete-details').hide();
//            });
//        });

        $('body').on('click', ".viewInvoice", function (e) {
            var invoiceid = $(this).attr('data-id');
            var invoiceno = $(this).attr('data-invoice');
//            alert(customerID);
            // open popup
//            localStorage['visited'] = customerID;
            var url = 'viewInvoicePdf.php'
            var form = $('<form action="' + url + '" target="_blank" method="post">' +
                    '<input type="hidden" name="invoiceId" value="' + invoiceid + '" />' +
                    '<input type="hidden" name="invoiceNo" value="' + invoiceno + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });

    });
</script>

<style>
    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
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
</style>
