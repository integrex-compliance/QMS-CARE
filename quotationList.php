<div id="wrapper" class="active"> 

    <?php
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    $pageIdentifier = "Qoutation";

    include_once './header.php';
    include_once './dbConnect.php';

    $allFirms = "SELECT consulting_firm_id, firm_name FROM consulting_firm";
    $statement = $db->prepare($allFirms);
    $statement->execute();
    $firmList = $statement->fetchAll();
    ?>

    <div id="page-content-wrapper" >
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
                            <div class="col-sm-10 col-sm-offset-1 form-group float-label-control">
                                <label>Consulting Firm</label>
                                <select class="add-to-service form-control firmInvoices" id="firmInvoices" name="firmInvoices" data-row-id="" value="">
                                    <option value="" >Select Consulting Firm</option>
                                    <?php foreach ($firmList as $firms) { ?>
                                        <option value="<?php echo $firms['consulting_firm_id'] ?>"><?php echo $firms['firm_name'] ?></option> 
                                    <?php } ?>
                                </select>                
                            </div>
                        </div>                        
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1 form-group float-label-control">
                        <div class="row" style="">
                            <div class="col-xs-12  text-center btn  qms_complete_btn ">
                                <span><i class="fa  ">Quotation Generated</i></span>
                            </div>
                        </div>
                        <div class="row invoiceInfo" style="">
                            <div class="well" >
                                <ul class="list-group checked-list-box operateInvoices">
                                    <span>Select consulting firm to see quotation generated</span>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row clearfix" >
                    <div class="col-sm-10 col-sm-offset-1 main_content table-responsive">
                        <table class="table table-hover table-sortable table-striped" id="tab_quotation_list" width="100%">
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

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $(".firmInvoices").on("change", function () {
//        $(".firmInvoices").click(function () {
            //            alert($(this).attr('data-id'));
            $usertype = '<?php echo Utitlity::checkForOfficeStaff() ?>'
            $.ajax({
                url: "invQuotDetails.php",
                type: "GET",
                dataType: "json",
                data: {
                    firm_ID: $('.firmInvoices').val(),
                    user: $usertype,
                },
                success: function (data)
                {
//                  alert(data);
                    $('.invoiceInfo').hide();
                    $('.invoice-list-head').empty();
                    $('.invoice-table-data').empty();

                    $('.invoice-list-head').append('<th class="" style="border-left: 1px solid #ffffff">\n\
                                                    <small><a>Quotation No.</a></small>\n\
                                                    </th>\n\
                                                    <th class="">\n\
                                                    <small><a>Quotation Date</a></small>\n\
                                                    </th>\n\
                                                    <th class="">\n\
                                                    <small><a>Name of Customer</a></small>\n\
                                                    </th>\n\
                                                    <th class="">\n\
                                                    <small><a>Service</a></small>\n\
                                                    </th>\n\
                                                    <th class="">\n\
                                                    <small><a>Audit body charges</a></small>\n\
                                                    </th>\n\
                                                    <th class="">\n\
                                                    <small><a>Consultancy Charges</a></small>\n\
                                                    </th>\n\
                                                    <th class="">\n\
                                                    <small><a>Total Amount</a></small>\n\
                                                    </th>\n\
                                                    <th class="">\n\
                                                    <small><a>Edit/Delete/View</a></small>\n\
                                                    </th>');

                    for (var i in data) {
                        if (data[i]['quotation_date'] == '0000-00-00') {
                            var dateVal = "";
                        } else {
                            var dateVal = moment(data[i]['quotation_date']).format('DD.MM.YYYY');
                        }
                        $('.invoice-table-data').append('<tr><td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[i]['firm_abbrevation'] + '/' + data[i]['quotation_no'] + '</small></td>\n\
                                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + dateVal + '</small></td>\n\
                                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[i]['customer_name'] + '</small></td>\n\
                                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[i]['firm_service'] + '</small></td>\n\
                                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[i]['audit_body_charge'] + '</small></td>\n\
                                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[i]['consulting_charge'] + '</small></td>\n\
                                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff"><small>' + data[i]['total_amount'] + '</small></td>\n\
                                                        <td data-name="name" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">\n\
                                                        <a class="editQuotation" data-id="' + data[i]['quotation_id'] + '" data-firm="' + data[i]['firm_id'] + '" data-customer="' + data[i]['customer_name'] + '" title="Edit quotation" style="padding: 1px 5px; "><i class="fa fa-pencil-square" style="padding:3px"></i></a>\n\
                                                        <a class="deleteQuotation" data-id="' + data[i]['quotation_id'] + '" data-firm="" data-customer="" title="Delete quotation" style="padding: 1px 5px; "><i class="fa fa-trash" style="padding:3px"></i></a>\n\
                                                        <a class="viewQuotation" data-id="' + data[i]['quotation_id'] + '" data-firm="' + data[i]['firm_id'] + '" data-customer="' + data[i]['customer_name'] + '" title="View quotation" style="padding: 1px 5px; "><i class="fa fa-file-pdf-o" style="padding:3px"></i></a></td></tr>');
                    }
                },
                error: function (data, status, err)
                {
                    $('.addNewService').empty();
                    //                    alert(status);
                }
            });
        });

        $('body').on('click', ".editQuotation", function (e) {
//        alert($(this).attr('data-firm'));
            $.ajax({
                url: "invQuotDetails.php",
                type: "GET",
                dataType: "json",
                data: {
                    quotationId: $(this).attr('data-id'),                    
                },
                success: function (data)
                {
//                    alert(data);
//                    alert(data[3]['consulting_firm_id']);
                    $('.editconsultingFirmId').val(data[0]['firm_id']);
                    $('.quotationClient').val(data[0]['customer_name']);
                    $('.editquotationDt').val(moment(data[0]['quotation_date']).format('DD.MM.YYYY'));
                    $('.editserviceString').val(data[0]['firm_service']);
                    $('.editauditBodyChange').val(data[0]['audit_body_charge']);
                    $('.editconsultingCharges').val(data[0]['consulting_charge']);
                    $('.editinitialAmount').val(data[0]['initial_amount']);
                    $('.editregistrationAmt').val(data[0]['registration_amt']);
                    $('.editpaymentOfCertification').val(data[0]['pay_consultancy']);
                    $('.editremainingConsultancyAmt').val(data[0]['remaining_consultancy']);
                    $('.editotherAccredited').val(data[0]['accredation']);
                    $('.editquoteCustomerId').val(data[0]['custer_id']);
                    $('.editpreQotDate').val(moment(data[0]['quotation_date']).format('DD.MM.YYYY'));
                    $('.editquot-id').val(data[0]['quotation_id']);
                    $('.editserviceString').empty();
                    for(var key in data[2]){
//                        alert(data[2][key]['service_desc']);
                        $('.editserviceString').append('<option value="'+data[2][key]['service_desc']+'">'+data[2][key]['service_desc']+'</option>')
                    }
//                    $('.quoteconsultingFirmName').val(data[3]['firm_name']);
////                    alert($('.consultingFirmId').val());
                    for (var i in data[1]) {
                        if (i == 0) {
                            $('.editconditionDescription').val(data[1][i]['quot_condition']);
                        } else {
                            $('.editconditionDescTd').append('<div class="row"><div class="col-sm-12 form-group float-label-control"><label for = "" > Condition Description </label>\n\
                                                        <textarea name = "editconditionDescription" rows = "2" class = "form-control form-group editconditionDescription" id = "editconditionDescription" placeholder = "Condition Description" >' + data[1][i]['quot_condition'] + '</textarea>\n\
                                                        </div></div>');
                        }
                    }
                    $('input').trigger('change');
                    $('textarea').trigger('change');
                    $('#editQuotation').modal('show');
                },
                error: function (data, status, err)
                {
                    alert(status);
                }
            });
        });

        $('body').on('click', ".deleteQuotation", function (e) {
            var quotid = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').click(function () {
                var url = 'invQuotDetails.php'
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="quot_id" value="' + quotid + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('body').on('click', ".viewQuotation", function (e) {
            var quotid = $(this).attr('data-id');
            var firmid = $(this).attr('data-firm');
            var customer = $(this).attr('data-customer');
            var url = 'viewQuotationPdf.php'
            var form = $('<form action="' + url + '" target="_blank" method="post">' +
                    '<input type="hidden" name="quot_id" value="' + quotid + '" />' +
                    '<input type="hidden" name="firmid" value="' + firmid + '" />' +
                    '<input type="hidden" name="customer" value="' + customer + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });

//        $('body').on('click', ".viewInvoice", function (e) {
//            var invoiceId = $(this).attr('data-id');
//            var invoiceNo = $(this).attr('data-invoice');
//            var url = 'viewInvoicePdf.php';
//            var form = $('<form action="' + url + '" method="post" target="_blank">' +
//                    '<input type="hidden" name="invoiceId" value="' + invoiceId + '" />' +
//                    '<input type="hidden" name="invoiceNo" value="' + invoiceNo + '" />' +
//                    '</form>');
//            $('body').append(form);
//            form.submit();
//        });

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