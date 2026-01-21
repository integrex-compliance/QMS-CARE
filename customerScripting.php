<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once './dbConnect.php';

$services = "";
$certBody = "";
$consultfrm = "";
$allConsultant = "";

$allServiceType = "SELECT * FROM service_type WHERE service <> ''";
$statement = $db->prepare($allServiceType);
$statement->execute();
$allServices = $statement->fetchAll();

foreach ($allServices as $service) {
    $services .= $service['service'] . ",";
}
//print_r($service);
$allCertBody = "SELECT * FROM cert_body";
$statement = $db->prepare($allCertBody);
$statement->execute();
$allCert = $statement->fetchAll();

foreach ($allCert as $cb) {
    $certBody .= $cb['cert'] . ",";
}

$allConsultFirms = "SELECT * FROM consulting_firm";
$statement = $db->prepare($allConsultFirms);
$statement->execute();
$consultFirm = $statement->fetchAll();

foreach ($consultFirm as $firm) {
    $consultfrm .= $firm['firm_name'] . ",";
}

$allConsultants = "SELECT * FROM consultant";
$statement = $db->prepare($allConsultants);
$statement->execute();
$consultant = $statement->fetchAll();

foreach ($consultant as $consult) {
    $allConsultant .= $consult['consultant_name'] . ",";
}

$allAuditor = "SELECT * FROM auditors";
$statement = $db->prepare($allAuditor);
$statement->execute();
$allAuditorList = $statement->fetchAll();

$addServicesTo = "SELECT * FROM add_to_service";
$statement = $db->prepare($addServicesTo);
$statement->execute();
$addTODo = $statement->fetchAll();

$allHandler = "SELECT * FROM handler";
$statement = $db->prepare($allHandler);
$statement->execute();
$handlerList = $statement->fetchAll();
?>
<script>
    function validateAttachmentUpload() {
        $isError = false;
        if ($('.customerAttachemnt').val() == "") {
            $('#attachmentErrorMsg').text("Attachment file is required");
            $('#attachmentError').show();
            $('.customerAttachemnt').addClass('inputHighlight');
            $isError = true;
            //            alert("hi");
        } else {
            $('#attachmentError').hide();
            $('.customerAttachemnt').removeClass('inputHighlight');
        }
        if ($('.attachmentinfo').val() == "") {
            $('#attachmentInfoErrorMsg').text("Attachment information is required");
            $('#attachmentInfoError').show();
            $('.attachmentinfo').addClass('inputHighlight');
            $isError = true;
            //            alert("hi");
        } else {
            $('#attachmentInfoError').hide();
            $('.attachmentinfo').removeClass('inputHighlight');
        }
        return $isError;
    }

    $(document).ready(function () {
        var serviceTypeValues = new Array();
<?php foreach ($services as $service) { ?>
            serviceTypeValues.push("<?php echo $service['service']; ?>");
<?php } ?>
        //                alert("no values" + serviceTypeValues);
        
        $("#serviceType").autocomplete({
            source: serviceTypeValues
        });

        $('.saveattachemtninfo').click(function (e) {
            if (validateAttachmentUpload()) {
                e.preventDefault();
                return;
            }
        });

        $(".delteAttachment").click(function () {
//                alert("in");
            var customerid = $(this).attr('data-customer');
            var attachment = $(this).attr('data-id');
            $('#ducumentupload').modal("hide");
            $('#delete-details').show();
            $('.delete-confirm-ok').click(function () {
                var url = 'uploadAttachemnts.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="attachemnt" value="' + attachment + '" />' +
                        '<input type="hidden" name="customer-id" value="' + customerid + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });

            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
                $('#ducumentupload').modal("show");
            });
        });
    });
</script>
<script type="text/javascript">
    function validateCertification() {
        $isError = false;
        if ($('.certification-date').val() == "")
        {
//            alert('in');
            $('.certificationDateError').show();
            $('.certificationDateError').show();
            $('.certification-date').addClass('inputHighlight');
            $isError = true;
//    alert("email not valid"+$error);
        } else {
//            alert('else');
            $('.certificationDateError').hide();
            $('.certification-date').removeClass('inputHighlight');
        }
        return $isError;
    }

    $(document).ready(function () {
        
        $('body').on('click', ".editInvoice", function (e) {
//        alert($(this).attr('data-id'));
            $.ajax({
                url: "invQuotDetails.php",
                type: "GET",
                dataType: "json",
                data: {
                    invoiceId: $(this).attr('data-id'),
                    firmId: $(this).attr('data-firm'),
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

                    $(".serviceAmtTdedit").empty();
if (data[0]['tax_type'] === "CGST_SGST") {
    $("input[name='taxTypeEdit'][value='CGST_SGST']").prop("checked", true);
    $('#taxBreakupEditRow').show();
    $('#igstWrapperEdit').hide();
} else if (data[0]['tax_type'] === "IGST") {
    $("input[name='taxTypeEdit'][value='IGST']").prop("checked", true);
    $('#taxBreakupEditRow').show();
    $('#igstWrapperEdit').show();
} else {
    // Uncheck both radio buttons and hide tax breakup row
    $("input[name='taxTypeEdit']").prop("checked", false);
    $('#taxBreakupEditRow').hide();
}
                    var isGstApplicable = data[6]['is_gst_applicable'];
                    var gstPercent = parseFloat(data[6]['gst_percent']) || 0;

// 2. Store GST info in hidden field or attribute for later use
                    $('#invoiceEditFirmId')
                        .attr('data-gst', isGstApplicable)
                        .attr('data-gst-percent', gstPercent);

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
                                    <input type="number" name="invoiceServiceAmtEdit[]" class="form-control form-group invoiceServiceAmtEdit" id="invoiceServiceAmtEdit" value="' + amt + '" placeholder="Amount">\n\
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

        $('.customercomment').bind('keypress', function (e) {
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

        $('.issueCert').change(function (e) {
            if (validateCertification()) {
                e.preventDefault();
                return;
            }
            var service = $(this).attr('data-service');
            var customer = $(this).attr('data-customer');
//            alert($(this).attr('data-service'));
            if ($(this).is(":checked")) {
                $.ajax({
                    url: "issueCertification.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        customer: customer,
                        service: service,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
                        $('.nextService').val(data['next_service_type']);
                    },
                    error: function (data, status, err) {

                    }
                });
            } else {
                $.ajax({
                    url: "issueCertification.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        customerID: customer,
                        serviceID: service,
                    },
                    success: function (data)
                    {
//                    alert(data);
//                        $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                        $('.nextService').val(data['next_service_type']);
                    },
                    error: function (data, status, err) {

                    }
                });
            }
        });

        $('.deleteCustomerDetails').click(function () {
            var customerid = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').click(function () {
                var url = 'deleteCustomerService.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="customerid" value="' + customerid + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('.deleteService').click(function () {
            var serviceId = $(this).attr('data-row-id');
            var customerId = '<?php echo $_GET['customerid'] ?>';
            $('#delete-details').show();
            $('.delete-confirm-ok').click(function () {
                var url = 'deleteCustomerService.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="serviceID" value="' + serviceId + '" />' +
                        '<input type="hidden" name="customerID" value="' + customerId + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $(".customerAttachemnts").click(function () {
            $("#ducumentupload").modal("show");
        });

        $('.editCustomerDetails').click(function () {
            $.ajax({
                url: "getUserDetails.php", type: "GET",
                dataType: "json",
                data: {
                    customerId: $(this).attr('data-id'),
                },
                success: function (data)
                {
                    var contact = data['contact_no'].split(', ');
                    var contactPerson = data['contact_person'].split(', ');
                    var contactEmail = data['contact_email'].split(', ');
                    var contactLen = contact.length;
                    var contactPrsnLen = contactPerson.length;
                    var contactEmainLen = contactEmail.length;
                    var max = (Math.max(contactLen, contactPrsnLen, contactEmainLen));
                    for (var key = 0; key < max; key++) {
//                        var no=contact[key];
                        var no = key <= contactLen && contact[key] != null && contact[key] != "" ? contact[key] : "";
                        var mail = key <= contactEmainLen && contactEmail[key] != null && contactEmail[key] != "" ? contactEmail[key] : "";
                        var person = key <= contactPrsnLen && contactPerson[key] != null && contactPerson[key] != "" ? contactPerson[key] : "";
//                        if (contact[key] != null && contact[key] != "") {

                        if (key == 0) {
                            $('.contactNo').val(no);
                            $('.customerEmail').val(mail);
                            $('.contactPerson').val(person);
                        } else {
                            $('.newCustomerContact').empty();
                            $('.newCustomerContact').append('<div class="row newContactRowParent">\n\
                                                        <div class="col-sm-4 form-group float-label-control">\n\
                                                        <label>Email Id</label>\n\
                                                        <input type="email" class="customerEmail" id="customerEmail" name="customerEmail" value="' + mail + '" placeholder="Client Email" >\n\
                                                        <div id="contactEmailError" style="display: none">\n\
                                                        <small><span id="contactEmailErrorMsg" style="color: #c9513c; font-style: italic;" ></span></small>\n\
                                                        </div>\n\
                                                        </div>\n\
                                                        <div class="col-sm-4 form-group float-label-control">\n\
                                                        <label>Contact Person</label>\n\
                                                        <input type="text" class="contactPerson" id="contactPerson" name="contactPerson" value="' + person + '" placeholder="Contact Person" >\n\
                                                        </div>\n\
                                                        <div class="col-sm-3 form-group float-label-control">\n\
                                                        <label>Contact No</label>\n\
                                                        <input type="text" class="contactNo" id="contactNo" name="contactNo" placeholder="Contact No" value="' + no + '">\n\
                                                        <div id="customerContactError" style="display: none">\n\
                                                        <small><span id="customerContactErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>\n\
                                                        </div>\n\
                                                        </div>\n\
                                                        <div class="col-sm-1 form-group float-label-control deleteNewContactParent">\n\
                                                        <a class="deleteContact" style="float: right;"><i class="fa fa-remove"></i></a>\n\
                                                        </div>\n\
                                                        </div>');
                        }
                    }
//                    console.log(data['contact_no'].split(',')); //                    alert(data['contact_no'].split(','));
                    $('.editCustomerId').val(data['customer_id']);
                    $('.companyName').val(data['company_name']);
                    $('.clientPriority').val(data['priority']);
                    $('.companyAddress').val(data['company_address']);
                    $('.location').val(data['company_location']);
                    $('.customerReferred').val(data['client_reference']);
                    $('.clientGstNumber').val(data['gst_number']);
//                    $('.editContactPerson').val(data['contact_person']);
//                    $('.editContactNo').val(data['contact_no']);
                    $('.comments').val(data['comments']);
                    if (data["client_reference"]) {
                        if (data["client_reference"] == "Just Dial" || data["client_of"] == "Just Dial") {
                            $('#justdial').prop('checked', true);
                        } else if (data["client_reference"] == "Tele Call" || data["client_of"] == "Tele Call") {
                            $("#telecall").prop('checked', true);
                        } else
                        {
                            $("#other").prop('checked', true);
                            $(".customerReferred").prop('disabled', false);
                        }
                    }
                    $('input').trigger('change');
                    $('textarea').trigger('change');
                    $('#customerAdd').modal('show');
                },
                error: function (data, status, err)
                {
                    //                    $('.addNewService').empty();
                    alert(status);
                }
            });
        });

        $cbid = "";
        $customerId = "";
        $consulting_id = "";
        
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");
            table.draw();
        });

        $(document.body).on('click', '.service_title', function (e) {
            //        $('.service_title').click(function (e) {

//                    alert("da");
            $id = $(this).data("row-id");
//            alert($id);
            $('#servicebox-data' + $id).toggle();
        });

        $('.saveChanges').change(function (e) {
//            alert($('.nextService').val());
            saveData($(this));
        });

        // Auto-resize for Contract Details textarea so all lines stay visible
        function resizeAuditDetails($el) {
            $el.each(function () {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }

        // Initial sizing on page load
        resizeAuditDetails($('.auditDetailsText'));

        // Adjust height as user types or pastes text
        $(document).on('input', '.auditDetailsText', function () {
            resizeAuditDetails($(this));
        });

        $('.certificationComment').change(function (e) {
            saveComments($(this));
        });

        $checkedValue = "";

        function saveComments($selector) {
            $checkedValue = $selector.val();
//alert('hi');
            $.ajax({
                url: "addComments.php",
                dataType: "json",
                type: 'POST',
                data: {
                    cbId: $cbid,
                    certificationComment: $checkedValue,
                },
                success: function (data) {
//                    alert(data);
                },
                error: function (data, status, err) {

                }
            });
            $selector.removeClass("glowing-border");
            //           $(this).removeClass("form-group ");
            $selector.addClass("lookslike-label");
        }

        $('.consultComment').change(function (e) {
            saveCunsultComments($(this));
        });

        function saveCunsultComments($selector) {
            $checkedValue = $selector.val();
//            alert('hi');
            $.ajax({
                url: "addComments.php",
                dataType: "json",
                type: 'POST',
                data: {
                    consultId: $consulting_id, consultComment: $checkedValue,
                },
                success: function (data) {
//                    alert(data);
                },
                error: function (data, status, err) {

//                    alert("Error " + status);
                }
            });
            $selector.removeClass("glowing-border");                         //           $(this).removeClass("form-group ");
            $selector.addClass("lookslike-label");
        }

        function saveData($selector) {

            $checkedValue = $selector.val();
            $.ajax({
                url: "addCustomerDetails.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    id: "<?php echo $_GET['customerid'] ?>",
                    columnName: $selector.attr('data-column'),
                    columnValue: $checkedValue
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                },
                success: function (data) {
//                    alert(data); 
//                   (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
            $selector.removeClass("glowing-border");
//           $(this).removeClass("form-group ");
            $selector.addClass("lookslike-label");
        }

        $(document.body).on('click', '.saveService', function () {
//                    alert("in");
            $rowId = $(this).data("row-id");
            if ($rowId == undefined) {
                $rowId = "";
            }
            
            var contractDetailsArray = [];
            $(".contractDetails").each(function () {         //                        alert($(this).data("row-id"));
                if ($rowId == $(this).data("row-id"))
                    contractDetailsArray.push($(this).val());
            });
            $cbContractDetails = contractDetailsArray.join("~");
            
            var amountDecidedArray = [];
            $('.amountDecided').each(function () {
                if ($rowId == $(this).data("row-id"))
                    amountDecidedArray.push($(this).val());
            });
            $cbAmountDecided = amountDecidedArray.join("~");
            
            var paymentReceivedArray = [];
            $('.paymentReceived').each(function () {
                if ($rowId == $(this).data("row-id"))
                    paymentReceivedArray.push($(this).val());
            });
            $cbPaymentReceived = paymentReceivedArray.join("~");
            
            var depositDateArray = [];
            $('.depositDate').each(function () {
                if ($rowId == $(this).data("row-id"))
                    depositDateArray.push($(this).val());
            });
            $cbDepositDate = depositDateArray.join("~");
            
            var chequeDetailsArray = [];
            $('.chequeDetails').each(function () {
                if ($rowId == $(this).data("row-id"))
                    chequeDetailsArray.push($(this).val());
            });
            $cbChequeDetails = chequeDetailsArray.join("~");
            
            var bankAcccountNoArray = [];
            $('.bankAcccountNo').each(function () {
                if ($rowId == $(this).data("row-id"))
                    bankAcccountNoArray.push($(this).val());
            });
            $cbBankAcccountNo = bankAcccountNoArray.join("~");
            
            var balanceArray = [];
            $('.balance').each(function () {
                if ($rowId == $(this).data("row-id"))
                    balanceArray.push($(this).val());
            });
            $cbBalance = balanceArray.join("~");
            
            var cbRowArray = [];
            $('.cb-row-id').each(function () {
                if ($rowId == $(this).data("row-id")) {
                    if (cbRowArray.indexOf($(this).val()) == -1)
                        cbRowArray.push($(this).val());
                }
            });
            $cbrowid = cbRowArray.join("~");
            
            var consultingContractDetailsArray = [];
            $('.consultingContractDetails').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingContractDetailsArray.push($(this).val());
            });
            $consultingContractDetails = consultingContractDetailsArray.join("~");
            
            var consultingAmountDecidedArray = [];
            $('.consultingAmountDecided').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingAmountDecidedArray.push($(this).val());
            });
            $consultingAmountDecided = consultingAmountDecidedArray.join("~");
            
            var consultingPaymentReceivedArray = [];
            $('.consultingPaymentReceived').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingPaymentReceivedArray.push($(this).val( ));
            });
            $consultingPaymentReceived = consultingPaymentReceivedArray.join("~");
            
            var consultingDepositDateArray = [];
            $('.consultingDepositDate').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingDepositDateArray.push($(this).val());
            });
            $consultingDepositDate = consultingDepositDateArray.join("~");



        
            
            var consultingChequeDetailsArray = [];
            $('.consultingChequeDetails').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingChequeDetailsArray.push($(this).val());
            });
            $consultingChequeDetails = consultingChequeDetailsArray.join("~");
            
            var consultingBankAcccountNoArray = [];
            $('.consultingBankAcccountNo').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingBankAcccountNoArray.push($(this).val());
            });
            $consultingBankAcccountNo = consultingBankAcccountNoArray.join("~");
            
            var consultingBalanceArray = [];
            $('.consultingBalance').each(function () {
                if ($rowId == $(this).data("row-id"))
                    consultingBalanceArray.push($(this).val());
            });
            $consultingBalance = consultingBalanceArray.join("~");
            
            var consultingRowArray = [];
            $('.consulting-row-id').each(function () {
                if ($rowId == $(this).data("row-id")) {
                    if (consultingRowArray.indexOf($(this).val()) == -1)
                        consultingRowArray.push($(this).val());
                }
            });
            $consultingrowid = consultingRowArray.join("~");
//for delete row functionality
            var deleteCB = [];
            $('.cbDelete').each(function () {
                if ($rowId == $(this).data("row-id") && $(this).is(":checked"))
                    deleteCB.push($(this).val());
            });
            $deletCbRows = deleteCB.join("~");
            
            var deleteConsulting = [];
            $('.consultingDelete').each(function () {
                if ($rowId == $(this).data("row-id") && $(this).is(":checked"))
                    deleteConsulting.push($(this).val());
            });
            $deletConsultingRows = deleteConsulting.join("~");
            
            var serviceid = $('.service-id[data-row-id="' + $rowId + '"]').val();
//                    alert("serviceid" + serviceid);
            var serviceType = $('.service-type[data-row-id="' + $rowId + '"]').val(); //$('.service-type').val();
//                    alert(serviceType);
            var certBody = $('.cert-body[data-row-id="' + $rowId + '"]').val();
            var consultingFirm = $('.consulting-firm[data-row-id="' + $rowId + '"]').val();
            var consultant = $('.consultant-list[data-row-id="' + $rowId + '"]').val();
            var auditType = $('.audit-type[data-row-id="' + $rowId + '"]').val();
            var auditor = $('.auditor-list[data-row-id="' + $rowId + '"]').val();
            var auditdate = $('.audit-date[data-row-id="' + $rowId + '"]').val();
            var serviceaddto = $('.add-to-service[data-row-id="' + $rowId + '"]').val();
            var handelBy = $('.handel-by[data-row-id="' + $rowId + '"').val();
            var customerId = $('.customer_id[data-row-id="' + $rowId + '"]').val();
            
            if ($('.issueCert[data-row-id="' + $rowId + '"]').is(':checked')) {
                var issue_cert = "YES";
            } else {
                var issue_cert = "NO";
            }
            //            alert(issue_cert);
            var cbContractDetails = $cbContractDetails;
            var cbDecidedAmount = $cbAmountDecided;
            var cbPaymentReceived = $cbPaymentReceived;
            var cbDepositDate = $cbDepositDate;
            var cbChequeDetails = $cbChequeDetails;
            var cbBankAcccountNo = $cbBankAcccountNo;
            var cbBalance = $cbBalance;
            var cbRoeIds = $cbrowid;
            var consultingDetails = $consultingContractDetails;
            //            alert(consultingDetails);   
            var consultingAmount = $consultingAmountDecided;
            var consultingPayment = $consultingPaymentReceived;
            var consultingDepositDate = $consultingDepositDate;
            var consultingChequeDetails = $consultingChequeDetails;
            var consultingBankAcccountNo = $consultingBankAcccountNo;
            var consultingBalance = $consultingBalance;
            var consultingrowId = $consultingrowid;
            //Amol - there must be extra tr getting added, lets ty to remove it
            //            alert($deletCbRows);
            if (serviceType == "") {
                $('#validation-modal').modal('show');
            } else {
                $.ajax({
                    url: "addService.php",
                    dataType: "json",
                    type: 'GET',
                    data: {
                        serviceId: serviceid,
                        servicetype: serviceType,
                        certbody: certBody,
                        consultingfirm: consultingFirm,
                        consultantlist: consultant,
                        audittype: auditType,
                        auditorlist: auditor,
                        auditDate: auditdate,
                        addTo: serviceaddto,
                        handelby: handelBy,
                        certIssue: issue_cert,
                        customer_id: customerId,
                        cbContractDetails: cbContractDetails,
                        cbDecidedAmount: cbDecidedAmount,
                        cbPaymentReceived: cbPaymentReceived,
                        cbDepositDate: cbDepositDate,
                        cbChequeDetails: cbChequeDetails,
                        cbBankAcccountNo: cbBankAcccountNo,
                        cbBalance: cbBalance,
                        cbRows: cbRoeIds,
                        consultingDetails: consultingDetails,
                        consultingAmount: consultingAmount,
                        consultingPayment: consultingPayment,
                        consultingDepositDate: consultingDepositDate,
                        consultingChequeDetails: consultingChequeDetails,
                        consultingBankAcccountNo: consultingBankAcccountNo,
                        consultingBalance: consultingBalance,
                        consultingrows: consultingrowId,
                        cbDeleteRows: $deletCbRows,
                        consultingDeleteRows: $deletConsultingRows
                    },
                    success: function (data) {
//                        alert(data);
                        //                        alert("Data updated successfully"); //change to dialog
                        if (data.error)
                            return;
                        //fire off other ajax calls
                        $(document).ajaxStop(function () {
                            location.reload(true);
                        });
                    },
                    error: function (data, status, err) {
                        alert("Error " + status + "err " + err + " data " + JSON.stringify(data));
                        //                    alert("There was error while saving data. Please refresh page to see if values are saved or contact support team");
                    }
                });
            }
        });

        $('body').on('click', ".cbComment", function (e) {

            $cbid = $(this).attr("data-cbid");
            $customerId = $(this).attr('data-customer');
            $('.certificationComment').val($(this).attr('data-comment'));
            $('.certificationComment').trigger('change');
            $('#addCertificetionComment').modal("show");
        });

        $('body').on('click', ".consultingComment", function (e) {
            $consulting_id = $(this).attr("data-consultingid");
            $customer = $(this).attr('data-customer');
            $('.consultComment').val($(this).attr('data-consultComment'));
            $('.consultComment').trigger('change');
            $('#addConsultingComment').modal("show");
        });

        $('.quotationBtn').click(function () {
            $('.quoteCustomerId').val($(this).attr('data-customer'));
            $('#quotation').modal("show");
        });

        $('.invoiceBtn').click(function () {
            $('.invoiceCustomerId').val($(this).attr('data-customer'));
//            alert($('.invoiceCustomerId').val());
            $('#invoiceModal').modal("show");
        });
        //        auto complete for service type

        $('.nextService').autocomplete({
            source: function (request, response) {
                //                alert($('.service-type').val());
                $.ajax({
                    url: "autoSearch.php",
                    dataType: "json",
                    type: 'GET',
                    data: {serviceName: $('.nextService').val(),
                    },
                    success: function (data) {
                        //                      alert(data);
                        response(data);
                        //                        $('.nextService').val(data['label']);                       
                        $checkedValue = data['label'];
                    },
                    error: function (data, status, err) {
                        //                        alert(status);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                //                alert(ui.item.label);
                $.ajax({
                    url: "addCustomerDetails.php", type: "GET",
                    //                    dataType: 'JSON',
                    data: {
                        customerid: "<?php echo $_GET['customerid'] ?>",
                        value: ui.item.label,
                    },
                    error: function (xhr, status, error) {
                        //                    alert("There seems to be issue saving data!");
                    },
                    success: function (data) {
                        //                    alert(data);                     
                    }
                });
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $(document).on('keyup.autocomplete', ".bankAcccountNo", function () {
            $getVal = $(this).val();
            $serviceid = $(this).attr('data-row-id');
            $(this).autocomplete({
                source: function (request, response) {
                    //                alert($('.service-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            serviceid: $serviceid, //$('.service-type').val(), consultingBankAcccountNo
                            bankac: $getVal,
                        },
                        success: function (data) {
                            //                           alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
                            //                           alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });

        $(document).on('keyup.autocomplete', ".consultingBankAcccountNo", function () {
            $getVal = $(this).val();
            $serviceid = $(this).attr('data-row-id');
            $(this).autocomplete({
                source: function (request, response) {
                    //                alert($('.service-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            serviceId: $serviceid, //$('.service-type').val(), 
                            consultbankac: $getVal,
                        },
                        success: function (data) {
                            //                           alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
                            //                           alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });

        $(document).on('keyup.autocomplete', ".service-type", function () {
            $getVal = $(this).val();
            //                    alert($getVal);
            $(this).autocomplete({
                source: function (request, response) {
                    //                alert($('.service-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            serviceName: $getVal, //$('.service-type').val(),
                        },
                        success: function (data) {
//                      alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
                            //                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });

        //        $(document).on('change', ".service-type", function () {
        //            $rowId = $(this).attr('data-row-id');
        //            if ($(this).val() != "CA" || $(this).val() != "SA-1" || $(this).val() != "SA-2" || $(this).val() != "RC") {
        //                document.getElementById('issueCert[data-row-id="' + $rowId + '"]').disabled= true;
        ////                $('.issueCert[data-row-id="' + $(this).data("row-id") + '"]').is(':checked')
//                alert('in');
        //            }
//        });

        $(document).on('keyup.autocomplete', ".cert-body", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.cert-body').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            certBody: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });

        $(document).on('keyup.autocomplete', ".consulting-firm", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.consulting-firm').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            consultingFirm: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });

        $(document).on('keyup.autocomplete', ".consultant-list", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.consultant-list').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            consultant: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {

//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
//        auto complete for auditor

        $(document).on('keyup.autocomplete', ".auditor-list", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.auditor-list').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            auditor: $('.auditor-list').val(),
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });
//        auto complete for audit-type
        $(document).on('keyup.autocomplete', ".audit-type", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.audit-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            auditType: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });

        $(document).on('keyup.autocomplete', ".add-to-service", function () {
            $getVal = $(this).val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.audit-type').val());
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            auditTo: $getVal,
                        },
                        success: function (data) {
//                                                    alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                        alert(status);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                },
                open: function () {
                    $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                },
                close: function () {
                    $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                }
            });
        });

        $('.saveChanges').click(function (e) {
            $(this).addClass("glowing-border");
//           $(this).addClass("form-group ");
        });

//        alert($rowCB);

        $topfixedRow = 2;

        function openDeleteDialog(callback) {
            // Set custom message
            $('.meeting-cancel-confirm-message').text('Are you sure to delete this?');

            // Remove any old handlers to avoid duplicate actions
            $('.delete-confirm-ok').off('click');
            $('.delete-cancel').off('click');

            $('.delete-confirm-ok').on('click', function () {
                $('#delete-details').hide();
                if (typeof callback === 'function') {
                    callback(true);
                }
            });

            $('.delete-cancel').on('click', function () {
                $('#delete-details').hide();
                if (typeof callback === 'function') {
                    callback(false);
                }
            });

            $('#delete-details').show();
        }

        // Delete single CB installment row (existing row)
        $(document.body).on('click', '.cbDeleteIcon', function (e) {
            e.preventDefault();
            var $row = $(this).closest('tr');
            var cbId = $(this).data('id');

            openDeleteDialog(function (confirmed) {
                if (!confirmed) {
                    return;
                }

                // If there is no id, this is an unsaved row; just remove from UI
                if (!cbId) {
                    $row.remove();
                    return;
                }

                $.ajax({
                    url: 'deleteCbRow.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: cbId},
                    success: function (resp) {
                        if (resp && resp.success) {
                            $row.remove();
                        } else {
                            alert(resp && resp.message ? resp.message : 'Unable to delete row.');
                        }
                    },
                    error: function () {
                        alert('Error while deleting row. Please try again.');
                    }
                });
            });
        });

        // Delete unsaved CB row (no id yet, only on UI)
        $(document.body).on('click', '.cbRowDelete', function (e) {
            e.preventDefault();
            var $row = $(this).closest('tr');
            openDeleteDialog(function (confirmed) {
                if (confirmed) {
                    $row.remove();
                }
            });
        });

        // Delete single Consulting installment row (existing row)
        $(document.body).on('click', '.consultingDeleteIcon', function (e) {
            e.preventDefault();
            var $row = $(this).closest('tr');
            var consultId = $(this).data('id');

            openDeleteDialog(function (confirmed) {
                if (!confirmed) {
                    return;
                }

                // If there is no id, this is an unsaved row; just remove from UI
                if (!consultId) {
                    $row.remove();
                    return;
                }

                $.ajax({
                    url: 'deleteConsultingRow.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: consultId},
                    success: function (resp) {
                        if (resp && resp.success) {
                            $row.remove();
                        } else {
                            alert(resp && resp.message ? resp.message : 'Unable to delete row.');
                        }
                    },
                    error: function () {
                        alert('Error while deleting row. Please try again.');
                    }
                });
            });
        });

        // Delete unsaved consulting row (no id yet, only on UI)
        $(document.body).on('click', '.consultingRowDelete', function (e) {
            e.preventDefault();
            var $row = $(this).closest('tr');
            openDeleteDialog(function (confirmed) {
                if (confirmed) {
                    $row.remove();
                }
            });
        });

        $(document.body).on('click', '.consultingAddLink', function () {
            $rowId = $(this).data("row-id"); //rowId
            $rowCB = $('.cbAddLink[data-row-id="' + $rowId + '"]').data("row-count");
            if ($rowCB == "") {
                $rowCB = 1;
            }
//            alert("$rowCB" + $rowCB);
            $rowConsult = $(this).data("row-count");
            if ($rowConsult == "") {
                $rowConsult = 1;
            }
            $count = $rowCB + $topfixedRow + $rowConsult + 1;
//            alert($count);
            var table = document.getElementById("service_details" + $rowId);
            var row = table.insertRow($count);
            $(this).data("row-count", ($rowConsult + 1));
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);
//            var cell9 = row.insertCell(7);

            cell1.style.padding = "0px";
            cell2.style.padding = "0px";
            cell3.style.padding = "0px";
            cell4.style.padding = "0px";
            cell5.style.padding = "0px";
            cell6.style.padding = "0px";
            cell7.style.padding = "0px";
            cell8.style.padding = "0px";
//            cell9.style.padding = "0px";

            cell1.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="consultingContractDetails" id="consultingContractDetails" name="consultingContractDetails" data-row-id="' + $rowId + '" placeholder="" /></div>';
            cell2.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="consultingAmountDecided" id="consultingAmountDecided" name="consultingAmountDecided" placeholder="" /></div>';
            cell3.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="consultingPaymentReceived" id="consultingPaymentReceived" name="contractDetails" placeholder="" /></div>';
            cell4.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="consultingDepositDate"  name="consultingDepositDate" placeholder="" /></div>';
            cell5.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="consultingChequeDetails" id="consultingChequeDetails" name="consultingChequeDetails" placeholder="" /></div>';
            cell6.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="consultingBankAcccountNo" id="consultingBankAcccountNo" name="consultingBankAcccountNo" placeholder="" /></div>';
            cell7.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="consultingBalance" id="consultingBalance" name="consultingBalance" placeholder="" /></div>';
            cell8.innerHTML = '<?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?><a href="javascript:void(0)" class="consultingRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a><?php } ?>';
            row.className = "consulting_subsection ";
            $tmpConsultCount = $tmpConsultCount + 1;

//            alert("out");

        });


        $(document.body).on('click', '.cbAddLink', function () {

//                    alert("in");
            $newCount = 0;
            $rowCB = $(this).data("row-count");
            if ($rowCB == "") {
                $rowCB = 1;
            }
//            alert("$rowCB" + $rowCB);
            $rowId = $(this).data("row-id");
//                    if($rowId==""){
//                        $newCount=1;
//                    }
//            alert("$rowId" + $rowId);
            $count = $rowCB + $topfixedRow;
//            $rowConsult = $rowConsult + 1;
//            alert("in");
            var table = document.getElementById("service_details" + $rowId);
            var row = table.insertRow($count);

            $(this).data("row-count", $rowCB + 1);
            $('.cbAddLinkForAdmin[data-row-id="' + $rowId + '"]').data("row-count", $rowCB + 1);

//                    alert($(this).data("row-count"));

            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);
//            var cell9 = row.insertCell(8);

            cell1.style.padding = "0px";
            cell2.style.padding = "0px";
            cell3.style.padding = "0px";
            cell4.style.padding = "0px";
            cell5.style.padding = "0px";
            cell6.style.padding = "0px";
            cell7.style.padding = "0px";
            cell8.style.padding = "0px";
//            cell9.style.padding = "0px";
            
            cell1.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="contractDetails" id="contractDetails" name="contractDetails" placeholder="" /></div>';
            cell2.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="amountDecided" id="amountDecided" name="amountDecided" placeholder="" /></div>';
            cell3.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="paymentReceived" id="paymentReceived" name="paymentReceived" placeholder="" /></div>';
            cell4.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="depositDate"  name="depositDate" placeholder="" /></div>';
            cell5.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="chequeDetails" id="chequeDetails" name="chequeDetails" placeholder="" /></div>';
            cell6.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="bankAcccountNo" id="bankAcccountNo" name="bankAcccountNo" placeholder="" /></div>';
            cell7.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="balance" id="balance" name="balance" placeholder="" /></div>';
            cell8.innerHTML = '<?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?><a href="javascript:void(0)" class="cbRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a><?php } ?>';
            row.className = "cb_subsection";
            $tmpCbCount = $tmpCbCount + 1;

//            alert("out");

        });

        $(document.body).on('click', '.cbAddLinkForAdmin', function () {

//                    alert("in");
            $newCount = 0;
            $rowCB = $(this).data("row-count");
            if ($rowCB == "") {
                $rowCB = 1;
            }
//            alert("$rowCB" + $rowCB);
            $rowId = $(this).data("row-id");
//                    if($rowId==""){
//                        $newCount=1;
//                    }
//            alert("$rowId" + $rowId);
            $count = $rowCB + $topfixedRow;
//            $rowConsult = $rowConsult + 1;
//            alert("in");

            var table = document.getElementById("service_details" + $rowId);
            var row = table.insertRow($count);
            $(this).data("row-count", $rowCB + 1);
            $('.cbAddLink[data-row-id="' + $rowId + '"]').data("row-count", $rowCB + 1);
//                    alert($(this).data("row-count"));

            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);
//            var cell9 = row.insertCell(8);

            cell1.style.padding = "0px";
            cell2.style.padding = "0px";
            cell3.style.padding = "0px";
            cell4.style.padding = "0px";
            cell5.style.padding = "0px";
            cell6.style.padding = "0px";
            cell7.style.padding = "0px";
            cell8.style.padding = "0px";
//            cell9.style.padding = "0px";

            cell1.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="contractDetails" id="contractDetails" name="contractDetails" placeholder="" value="Marketing Setup" /></div>';
            cell2.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="amountDecided" id="amountDecided" name="amountDecided" placeholder="" /></div>';
            cell3.innerHTML = '<div class="table-edit-control"><label></label><input type="number" data-row-id="' + $rowId + '" class="paymentReceived" id="paymentReceived" name="paymentReceived" placeholder="" /></div>';
            cell4.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="depositDate"  name="depositDate" placeholder="" /></div>';
            cell5.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="chequeDetails" id="chequeDetails" name="chequeDetails" placeholder="" /></div>';
            cell6.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="bankAcccountNo" id="bankAcccountNo" name="bankAcccountNo" placeholder="" /></div>';
            cell7.innerHTML = '<div class="table-edit-control"><label></label><input type="text" data-row-id="' + $rowId + '" class="balance" id="balance" name="balance" placeholder="" /></div>';
//            cell8.innerHTML = '<a class="cbComment" data-row-id="' + $rowId + '"><i class="fa fa-comment-o" aria-hidden="true"></i></a><input type="hidden" class="cb-row-id" id="cb-row-id" name="cb-row-id" data-row-id="' + $rowId + '" value="">';
            row.className = "cb_subsection";
            $tmpCbCount = $tmpCbCount + 1;
        });

        $('.addNewService').click(function () {
//            $('#addService').modal("show");
            $("#saveService0").hide();
//            alert(("<?php echo $services ?>"));
            var allServices = ("<?php echo $services ?>");
            var service = allServices.split(',');
            var allServiceLen = allServices.length;
            
            var max = (Math.max(allServiceLen));
            var option = "";
            for (var key = 0; key < max; key++) {
                var svcname = key <= allServiceLen && service[key] != null && service[key] != "" ? service[key] : "";
                option += '<option value="' + svcname + '">' + svcname + '</option>';
//                alert(svcname);
            }
            
//            alert(("<?php echo $certBody ?>"));
            var allCertBody = ("<?php echo $certBody ?>");
            var certBody = allCertBody.split(',');
            var allCertBodyLen = allCertBody.length;
            
            var max = (Math.max(allCertBodyLen));
            var option1 = "";
            for (var key = 0; key < max; key++) {
                var bodyname = key <= allCertBodyLen && certBody[key] != null && certBody[key] != "" ? certBody[key] : "";
                option1 += '<option value="' + bodyname + '">' + bodyname + '</option>';
//                alert(svcname);
            }
            
            var consultfrm = ("<?php echo $consultfrm ?>");
            var firms = consultfrm.split(',');
            var allfrmLen = consultfrm.length;
            
            var max = (Math.max(allfrmLen));
            var option2 = "";
            for (var key = 0; key < max; key++) {
                var firmname = key <= allfrmLen && firms[key] != null && firms[key] != "" ? firms[key] : "";
                option2 += '<option value="' + firmname + '">' + firmname + '</option>';
//                alert(svcname);
            }
            
            var allConsultant = ("<?php echo $allConsultant ?>");
//            alert(allConsultant);
            var consultant = allConsultant.split(',');
            var allConsultantLen = allConsultant.length;
            
            var max = (Math.max(allConsultantLen));
            var option3 = "";
            for (var key = 0; key < max; key++) {
                var consultantname = key <= allConsultantLen && consultant[key] != null && consultant[key] != "" ? consultant[key] : "";
                option3 += '<option value="' + consultantname + '">' + consultantname + '</option>';
//                alert(svcname);
            }
            
            $serviceStr = '<div class="servicebox">\n\
                            <div class="row" id="service-row" class="service-row">\n\
                                <div class="col-xs-12 qms_customer_name service_title" data-row-id="" style="text-align: center">Service - <span class="serviceStatus"></span><a class="saveService" data-row-id=""><i class="fa fa-floppy-o pull-right " style="color:white; padding-right:10px"></i></a></div>\n\
                                </div>\n\
                                <div class="servicebox-data" id="servicebox-data">\n\
                                <div class="row" style="padding-top:10px">\n\
                                    <div class="col-xs-3 form-group float-label-control"><label>Service Type</label>\n\
                                        <select class="service-type" id="service-type" name="service-type" data-row-id="" value="">\n\
                                            <option value="" >Select Service Type</option>'+option+'</select>\n\
                                    </div>\n\
                                    <div class="col-xs-3 form-group float-label-control"><label>Cert Body</label>\n\
                                        <select class="cert-body" id="cert-body" name="cert-body" data-row-id="" value="">\n\
                                            <option value="" >Select Certification Body</option>'+option1+'</select>\n\
                                    </div>\n\
                                    <div class="col-xs-3 form-group float-label-control"><label>Consulting firm</label>\n\
                                        <select class="consulting-firm" id="consulting-firm" name="consulting-firm" data-row-id="" value="">\n\
                                            <option value="" >Select Consulting Firm</option>'+option2+'</select>\n\
                                    </div>\n\
                                    <div class="col-xs-3 form-group float-label-control"><label>Consultant</label>\n\
                                        <select class="consultant-list" id="consultant-list" name="consultant-list" data-row-id="" value="">\n\
                                            <option value="" >Select Consultant </option>'+option3+'</select>\n\
                                    </div>\n\
                                    <input type="hidden" class="service-id" id="service-id" data-row-id="" name="service-id" value="">\n\
                                </div>\n\
                                    <div class="row">\n\
                                        <div class="col-xs-2 form-group float-label-control"><label>Auditor</label>\n\
                                            <select class="consultant-list" id="consultant-list" name="consultant-list" data-row-id="" value="">\n\
                                                <option value="" >Select Auditor</option><?php foreach ($allAuditorList as $auditors) { ?><option value="<?php echo $auditors['auditor_name'] ?>"><?php echo $auditors['auditor_name'] ?></option><?php } ?></select>\n\
                                        </div>\n\
                                        <div class="col-xs-2 form-group float-label-control"><label>Audit Date</label>\n\
                                            <input type="text" class="audit-date" data-row-id="" name="audit-date" placeholder="Audit Date" value="" required>\n\
                                        </div>\n\
                                        <div class="col-xs-3 form-group float-label-control" style="margin-bottom:2px;"><label>Add to</label>\n\
                                            <select class="add-to-service" id="add-to-service" name="add-to-service" data-row-id="" value="">\n\
                                                <option value="" >Select add to</option><?php foreach ($addTODo as $to) { ?><option value="<?php echo $to['add_to'] ?>"><?php echo $to['add_to'] ?></option><?php } ?></select>\n\
                                        </div>\n\
                                        <div class="col-xs-2 form-group float-label-control" style="margin-bottom:2px;"><label>Handel By</label>\n\
                                            <select class="handel-by" id="handel-by" name="handel-by" data-row-id="" value="">\n\
                                                <option value="" >Select Handel By</option><?php foreach ($handlerList as $handler) { ?><option value="<?php echo $handler['handler_name'] ?>"><?php echo $handler['handler_name'] ?></option><?php } ?></select>\n\
                                        </div>\n\
                                        <div class="col-xs-3 form-group" style="">\n\
                                            <input type="checkbox" data-row-id="" class="issueCert" data-service="" data-customer="" value="">&nbsp;&nbsp; Certificate Given\n\
                                        </div>\n\
                                        <input type="hidden" class="customer_id" id="customer_id" data-row-id="" name="customer_id" value="<?php echo $_GET['customerid'] ?>"/>\n\
                                    </div>\n\
                                    <div class="row">\n\
                                        <div class="col-xs-12 table-responsive">\n\
                                            <table class="table table-bordered table-hover table-sortable service_details" id="service_details">\n\
                                                <thead>\n\
                                                    <tr>\n\
                                                        <th class="" width="18%" style="padding:2px"><small>Contract Details</small></th>\n\
                                                        <th class="" width="13%" style="padding:2px"><small>Amount Decided</small></th>\n\
                                                        <th class="" width="13%" style="padding:2px"><small>Payment received</small></th>\n\
                                                        <th class="" width="13%" style="padding:2px"><small>Date of Deposit</small></th>\n\
                                                        <th class="" width="13%" style="padding:2px"><small>Instrument No</small></th>\n\
                                                        <th class="" width="13%" style="padding:2px"><small>Bank A/c </small></th>\n\
                                                        <th class="" width="13%" style="padding:2px"><small>Balance</small></th>\n\
                                                        <th class="" width="4%" style="padding:2px"><i class="fa fa-comment-o" aria-hidden="true"></i></th>\n\
                                                    </tr>\n\
                                                </thead>\n\
                                                <tbody class = "" >\n\
                                                    <tr class = "cb_section" >\n\
                            <td colspan = 8 style="padding:0px;">CB <a class = "btn btn-success btn-xs cbAddLink pull-right" data-row-id="" data-row-count="" ><i class = "fa fa-plus-circle fa-lg pull-right"  style="padding:3px"></i></a ><?php if (Utitlity::checkForUserAdmin()) { ?><a class = "cbAddLinkForAdmin pull-right btn btn-success btn-xs " data-row-id="" data-row-count="" title="addCB" style="padding: 1px 5px;">MS </a ><?php } ?></td>\n\
                        </tr>\n\
                        <tr class = "cb_subsection previousCBData" id = "cb_subsection_0" >\n\
                            <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input type = "text" data-row-id="" class = "contractDetails" id = "contractDetails" name = "contractDetails" value = "" placeholder = ""></div></td >\n\
                            <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input type = "number" data-row-id="" class = "amountDecided" id = "amountDecided" name = "amountDecided"  value = "" placeholder = ""></div></td >\n\
                            <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input type = "number" data-row-id="" class = "paymentReceived" id = "paymentReceived" name = "paymentReceived" value = "" placeholder = ""></div></td >\n\
                            <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "text" class = "depositDate"  name = "depositDate" value = "" placeholder = ""></div></td >\n\
                            <td class = "" style="padding:0px;" ><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "text" class = "chequeDetails" id = "chequeDetails" name = "chequeDetails" value = "" placeholder = "" ></div></td >\n\
                            <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "text" class = "bankAcccountNo"  id = "bankAcccountNo"  name = "bankAcccountNo" value = "" placeholder = ""></div></td >\n\
                            <td class = "" style="padding:0px;" ><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "text" class = "balance" id = "balance" name = "balance" value = "" placeholder = ""  readonly = ""></div></td >\n\
                            <td class = "" style="padding:0px;" ></td>\n\
                        </tr >\n\
                    <input data-row-id="" type = "hidden" class = "cb-row-id" id = "cb-row-id"  name = "cb-row-id" value = "" >\n\
                    <tr class = "consulting_section" >\n\
                        <td colspan = 8 style="padding:0px">Consulting <a class = "consultingAddLink btn btn-warning btn-xs pull-right" data-row-count="" data-row-id=""><i class = "fa fa-plus-circle fa-lg pull-right" style="padding:3px"></i></a ></td>\n\
                    </tr >\n\
                    <tr class = "consulting_subsection previousConsultingData" >\n\
                        <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "text" class = "consultingContractDetails" id = "consultingContractDetails" name = "consultingContractDetails" value = "" placeholder = ""></div></td >\n\
                        <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "number" class = "consultingAmountDecided" id = "consultingAmountDecided" name = "consultingAmountDecided" style="text-align: center" value = "" placeholder = ""></div></td >\n\
                        <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "number" class = "consultingPaymentReceived" id = "consultingPaymentReceived" name = "consultingPaymentReceived" value = "" placeholder = ""></div></td >\n\
                        <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "text" class = "consultingDepositDate" id = "consultingDepositDate" name = "consultingDepositDate" value = "" placeholder = ""></div></td >\n\
                        <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "text"class = "consultingChequeDetails" id = "consultingChequeDetails" name = "consultingChequeDetails" value = "" placeholder = ""></div></td >\n\
                        <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "text" class = "consultingBankAcccountNo" id = "consultingBankAcccountNo" name = "consultingBankAcccountNo" value = "" placeholder = ""></div></td >\n\
                        <td class = "" style="padding:0px;"><div class = "table-edit-control" ><label ></label><input data-row-id="" type = "text"class = "consultingBalance" id = "consultingBalance" name = "consultingBalance" value = "" placeholder = "" readonly = ""></div></td >\n\
                        <td class = "" style="padding:0px;"></td>\n\
                    </tr >\n\
                    <input data-row-id="" type = "hidden"  class = "consulting-row-id " id = "consulting-row-id" name = "consulting-row-id" value = "" >\n\
                    </tbody>\n\
                </table >\n\
            </div>\n\
        </div>\n\
    </div>\n\
</div>';
            $(".servicebox-bunch").append($serviceStr);
        });


    });

    $(function () {

        $('.certificationDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy',
            autoclose: true
        });

//        $("#certificationDate").keydown(function (e) {
//
//            e.preventDefault();
//
//        });

        $('.serviceDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy',
            autoclose: true
        });

//        $("#serviceDate").keydown(function (e) {
//            e.preventDefault();
//        });

        $(document).on('focus', ".audit-date", function () {
            if ($(this).hasClass('hasDatepicker') === false) {
                $(this).datepicker({
                    //            minDate: '-100Y',
                    //            maxDate: '-2Y',
                    //            changeYear: true,
                    //            changeMonth: true,
                    //            yearRange: "-100:+0",
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

        $(document).on('focus', ".depositDate", function () {
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

//        $("#consultingDepositDate").keydown(function (e) {
//            e.preventDefault();
//        });

    });
</script>