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

$allServiceStages = [];
$stageQueryOk = false;
try {
    $stageStmt = $db->query("SELECT stage FROM service_stage ORDER BY id ASC");
    if ($stageStmt) {
        $allServiceStages = $stageStmt->fetchAll(PDO::FETCH_COLUMN);
        $stageQueryOk = true;
    }
} catch (Exception $e) {}
if (!$stageQueryOk) {
    $allServiceStages = ['CA', 'SA-1', 'SA-2', 'RC', 'New enq', 'CNE', 'Mnt.'];
}
foreach ($allServiceStages as $stg) {
    $services .= $stg . ",";
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
                    $('.invoiceEditServiceId').val(data[0]['service_id'] || '');
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

        $(document.body).on('click', '.deleteService', function (e) {
            e.stopPropagation();
            var serviceId = $(this).attr('data-row-id');
            var customerId = '<?php echo $_GET['customerid'] ?>';
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                var url = 'deleteCustomerService.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="serviceID" value="' + serviceId + '" />' +
                        '<input type="hidden" name="customerID" value="' + customerId + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-cancel').off('click').on('click', function () {
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
            // Ignore click if user clicked on action buttons inside header (delete button, auto-save pill)
            if ($(e.target).closest('.deleteService, .flow-delete-service, .auto-save-status-pill').length) {
                return;
            }

            var $id = $(this).data("row-id");
            var $box = $('#servicebox-data' + $id);
            var $folderIcon = $(this).find('.service-folder-icon');
            var $indicator = $(this).find('.service-collapse-indicator');

            $box.slideToggle(200, function () {
                if ($box.is(':visible')) {
                    $folderIcon.removeClass('fa-folder-o').addClass('fa-folder-open-o');
                    $indicator.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    resizeAuditDetails($box.find('.auditDetailsText'));
                } else {
                    $folderIcon.removeClass('fa-folder-open-o').addClass('fa-folder-o');
                    $indicator.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });
        });

        // Toggle Consulting Payments Section
        $(document.body).on('click', '.consulting-header', function (e) {
            // Ignore click if user clicked on action buttons inside header (Issue Invoice, Add Consulting Row)
            if ($(e.target).closest('.header-actions, .serviceInvoiceBtn, .consultingAddLink, .btn, a, button').length) {
                return;
            }
            var $card = $(this).closest('.consulting-payment-block');
            var $body = $card.find('.consulting-content-body');
            var $indicator = $(this).find('.consulting-collapse-indicator');
            var $tableCard = $(this).closest('.compact-table-card');

            $body.slideToggle(180, function () {
                if ($body.is(':visible')) {
                    $indicator.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    $tableCard.removeClass('is-collapsed');
                } else {
                    $indicator.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    $tableCard.addClass('is-collapsed');
                }
            });
        });

        // Toggle External Party Payment Section
        $(document.body).on('click', '.cb-header, .payment-dotted-divider', function (e) {
            // Ignore click if user clicked on action buttons inside header (MS, Add External Party Row)
            if ($(e.target).closest('.header-actions, .cbAddLink, .cbAddLinkForAdmin, .btn, a, button').length) {
                return;
            }
            var $parentBlock = $(this).closest('.servicebox-data, .servicebox');
            var $card = $parentBlock.find('.cb-payment-block');
            var $body = $card.find('.cb-content-body');
            var $header = $card.find('.cb-header');
            var $indicator = $header.find('.cb-collapse-indicator');
            var $divIndicator = $parentBlock.find('.divider-collapse-indicator');
            var $tableCard = $card.find('.compact-table-card');

            $body.slideToggle(180, function () {
                if ($body.is(':visible')) {
                    $indicator.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    $divIndicator.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    $tableCard.removeClass('is-collapsed');
                } else {
                    $indicator.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    $divIndicator.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    $tableCard.addClass('is-collapsed');
                }
            });
        });

        // Auto-expand CB section if user clicks Add External Party Row or MS button
        $(document.body).on('click', '.cbAddLink, .cbAddLinkForAdmin', function () {
            var $parentBlock = $(this).closest('.servicebox-data, .servicebox');
            var $body = $parentBlock.find('.cb-content-body');
            var $indicator = $parentBlock.find('.cb-collapse-indicator');
            var $divIndicator = $parentBlock.find('.divider-collapse-indicator');
            var $tableCard = $parentBlock.find('.cb-payment-block .compact-table-card');
            if (!$body.is(':visible')) {
                $body.slideDown(150);
                $indicator.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $divIndicator.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $tableCard.removeClass('is-collapsed');
            }
        });

        // Auto-expand Consulting section if user clicks Add Consulting Row button
        $(document.body).on('click', '.consultingAddLink', function () {
            var $parentBlock = $(this).closest('.servicebox-data, .servicebox');
            var $body = $parentBlock.find('.consulting-content-body');
            var $indicator = $parentBlock.find('.consulting-collapse-indicator');
            var $tableCard = $parentBlock.find('.consulting-payment-block .compact-table-card');
            if (!$body.is(':visible')) {
                $body.slideDown(150);
                $indicator.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $tableCard.removeClass('is-collapsed');
            }
        });

        $('.saveChanges').change(function (e) {
//            alert($('.nextService').val());
            saveData($(this));
        });

        // Auto-resize for Contract Details textarea so all lines stay visible
        function resizeAuditDetails($el) {
            $el.each(function () {
                if (this.tagName && this.tagName.toLowerCase() === 'textarea') {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                }
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

        /* ==========================================================
           TOAST NOTIFICATION HELPER
           ========================================================== */
        window.showToast = function (message, type, title) {
            if (!type) type = 'success';
            if (!title) {
                title = (type === 'success') ? 'Saved' : (type === 'error' ? 'Error' : 'Notice');
            }
            var iconClass = (type === 'success') ? 'fa-check' : (type === 'error' ? 'fa-exclamation' : 'fa-info');

            var $container = $('#qms-toast-container');
            if (!$container.length) {
                $container = $('<div id="qms-toast-container"></div>').appendTo('body');
            }

            var $toast = $(
                '<div class="qms-toast qms-toast-' + type + '">' +
                    '<div class="qms-toast-icon"><i class="fa ' + iconClass + '"></i></div>' +
                    '<div class="qms-toast-body">' +
                        '<div class="qms-toast-title">' + title + '</div>' +
                        '<div class="qms-toast-msg">' + message + '</div>' +
                    '</div>' +
                    '<button type="button" class="qms-toast-close">&times;</button>' +
                    '<div class="qms-toast-bar"></div>' +
                '</div>'
            );

            $container.append($toast);
            setTimeout(function () { $toast.addClass('show'); }, 20);

            function dismissToast() {
                $toast.removeClass('show').addClass('hiding');
                setTimeout(function () { $toast.remove(); }, 350);
            }

            $toast.find('.qms-toast-close').on('click', dismissToast);
            setTimeout(dismissToast, 2600);
        };

        /* ==========================================================
           LIVE SERVICE KPI METRICS RE-CALCULATOR
           ========================================================== */
        window.updateServiceKpiMetrics = function (rowId) {
            if (!rowId) return;
            var cbDecided = 0;
            var cbReceived = 0;
            var consultDecided = 0;
            var consultReceived = 0;

            // Sum CB amounts for this service
            $('.amountDecided').each(function () {
                if ($(this).attr('data-row-id') == rowId) {
                    cbDecided += parseFloat($(this).val()) || 0;
                }
            });
            $('.paymentReceived').each(function () {
                if ($(this).attr('data-row-id') == rowId) {
                    cbReceived += parseFloat($(this).val()) || 0;
                }
            });

            // Sum Consulting amounts for this service
            $('.consultingAmountDecided').each(function () {
                if ($(this).attr('data-row-id') == rowId) {
                    consultDecided += parseFloat($(this).val()) || 0;
                }
            });
            $('.consultingPaymentReceived').each(function () {
                if ($(this).attr('data-row-id') == rowId) {
                    consultReceived += parseFloat($(this).val()) || 0;
                }
            });

            // Find the service container
            var $serviceBox = $('#servicebox-data' + rowId);

            // Update separated Consulting KPI chips
            $serviceBox.find('.kpi-consult-decided .kpi-chip-value').text('₹ ' + consultDecided.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $serviceBox.find('.kpi-consult-received .kpi-chip-value').text('₹ ' + consultReceived.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

            var consultInvText = $serviceBox.find('.kpi-consult-invoiced .kpi-chip-value').text().replace(/[^0-9.-]+/g, "");
            var consultInvoiced = parseFloat(consultInvText) || 0;
            var consultBalDue = Math.max(0, consultInvoiced - consultReceived);

            var $consultBalChip = $serviceBox.find('.kpi-consult-balance');
            $consultBalChip.find('.kpi-chip-value').text('₹ ' + consultBalDue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            if (consultBalDue > 0) {
                $consultBalChip.removeClass('kpi-chip-success').addClass('kpi-chip-danger');
            } else {
                $consultBalChip.removeClass('kpi-chip-danger').addClass('kpi-chip-success');
            }

            // Update separated CB KPI chips
            var cbBalDue = Math.max(0, cbDecided - cbReceived);
            $serviceBox.find('.kpi-cb-decided .kpi-chip-value').text('₹ ' + cbDecided.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $serviceBox.find('.kpi-cb-received .kpi-chip-value').text('₹ ' + cbReceived.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

            var $cbBalChip = $serviceBox.find('.kpi-cb-balance');
            $cbBalChip.find('.kpi-chip-value').text('₹ ' + cbBalDue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            if (cbBalDue > 0) {
                $cbBalChip.removeClass('kpi-chip-success').addClass('kpi-chip-danger');
            } else {
                $cbBalChip.removeClass('kpi-chip-danger').addClass('kpi-chip-success');
            }

            // Fallback for legacy KPI elements if present
            var totalDecided = cbDecided + consultDecided;
            var totalReceived = cbReceived + consultReceived;
            $serviceBox.find('.kpi-decided .kpi-value').text('₹ ' + totalDecided.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $serviceBox.find('.kpi-received .kpi-value').text('₹ ' + totalReceived.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        };

        /* ==========================================================
           DEBOUNCED AUTO-SAVE HANDLER
           ========================================================== */
        var autoSaveTimers = {};
        var serviceSavesInFlight = {};
        var pendingServiceSaves = {};
        var newServiceSequence = 0;
        window.addEventListener('beforeunload', function (event) {
            if (Object.keys(autoSaveTimers).length || Object.keys(serviceSavesInFlight).length) {
                event.preventDefault();
                event.returnValue = '';
            }
        });
        window.autoSaveService = function (rowId, delay) {
            if (!rowId) return;
            if (delay === undefined) delay = 600;

            var $pill = $('.auto-save-status-pill[data-row-id="' + rowId + '"]');
            $pill.addClass('saving').html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            if (autoSaveTimers[rowId]) {
                clearTimeout(autoSaveTimers[rowId]);
            }

            autoSaveTimers[rowId] = setTimeout(function () {
                delete autoSaveTimers[rowId];
                executeServiceSave(rowId, null, false);
            }, delay);
        };

        /* ==========================================================
           EXECUTE SERVICE & PAYMENT SAVE (AJAX - NO PAGE RELOAD)
           ========================================================== */
        window.executeServiceSave = function (rowId, callback, silent) {
            if (!rowId) return;
            clearTimeout(autoSaveTimers[rowId]);
            delete autoSaveTimers[rowId];
            // Serialize saves so edits made during creation use the returned database ID.
            if (serviceSavesInFlight[rowId]) {
                pendingServiceSaves[rowId] = true;
                return;
            }

            var contractDetailsArray = [];
            $(".contractDetails").each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    contractDetailsArray.push($(this).val());
            });
            var cbContractDetails = contractDetailsArray.join("~");

            var amountDecidedArray = [];
            $('.amountDecided').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    amountDecidedArray.push($(this).val());
            });
            var cbDecidedAmount = amountDecidedArray.join("~");

            var paymentReceivedArray = [];
            $('.paymentReceived').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    paymentReceivedArray.push($(this).val());
            });
            var cbPaymentReceived = paymentReceivedArray.join("~");

            var depositDateArray = [];
            $('.depositDate').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    depositDateArray.push($(this).val());
            });
            var cbDepositDate = depositDateArray.join("~");

            var chequeDetailsArray = [];
            $('.chequeDetails').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    chequeDetailsArray.push($(this).val());
            });
            var cbChequeDetails = chequeDetailsArray.join("~");

            var bankAcccountNoArray = [];
            $('.bankAcccountNo').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    bankAcccountNoArray.push($(this).val());
            });
            var cbBankAcccountNo = bankAcccountNoArray.join("~");

            var balanceArray = [];
            $('.balance').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    balanceArray.push($(this).val());
            });
            var cbBalance = balanceArray.join("~");

            var cbRowArray = [];
            $('.cb-row-id').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id")) {
                    cbRowArray.push($(this).val());
                }
            });
            var cbRoeIds = cbRowArray.join("~");

            var consultingContractDetailsArray = [];
            $('.consultingContractDetails').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingContractDetailsArray.push($(this).val());
            });
            var consultingDetails = consultingContractDetailsArray.join("~");

            var consultingAmountDecidedArray = [];
            $('.consultingAmountDecided').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingAmountDecidedArray.push($(this).val());
            });
            var consultingAmount = consultingAmountDecidedArray.join("~");

            var consultingPaymentReceivedArray = [];
            $('.consultingPaymentReceived').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingPaymentReceivedArray.push($(this).val());
            });
            var consultingPayment = consultingPaymentReceivedArray.join("~");

            var consultingDepositDateArray = [];
            $('.consultingDepositDate').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingDepositDateArray.push($(this).val());
            });
            var consultingDepositDate = consultingDepositDateArray.join("~");

            var consultingChequeDetailsArray = [];
            $('.consultingChequeDetails').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingChequeDetailsArray.push($(this).val());
            });
            var consultingChequeDetails = consultingChequeDetailsArray.join("~");

            var consultingBankAcccountNoArray = [];
            $('.consultingBankAcccountNo').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingBankAcccountNoArray.push($(this).val());
            });
            var consultingBankAcccountNo = consultingBankAcccountNoArray.join("~");

            var consultingBalanceArray = [];
            $('.consultingBalance').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingBalanceArray.push($(this).val());
            });
            var consultingBalance = consultingBalanceArray.join("~");

            var consultingTdsArray = [];
            $('.consultingTdsDeducted').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingTdsArray.push($(this).val());
            });
            var consultingTds = consultingTdsArray.join("~");

            var consultingShortSettleArray = [];
            $('.consultingShortSettle').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingShortSettleArray.push($(this).val());
            });
            var consultingShortSettle = consultingShortSettleArray.join("~");

            var consultingBadDebtArray = [];
            $('.consultingBadDebt').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id"))
                    consultingBadDebtArray.push($(this).val());
            });
            var consultingBadDebt = consultingBadDebtArray.join("~");

            var serviceNotes = $('.serviceNotesText[data-service-id="' + rowId + '"]').val() || '';

            var consultingRowArray = [];
            $('.consulting-row-id').each(function () {
                if (rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id")) {
                    consultingRowArray.push($(this).val());
                }
            });
            var consultingrowId = consultingRowArray.join("~");

            var deleteCB = [];
            $('.cbDelete').each(function () {
                if ((rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id")) && $(this).is(":checked"))
                    deleteCB.push($(this).val());
            });
            var deletCbRows = deleteCB.join("~");

            var deleteConsulting = [];
            $('.consultingDelete').each(function () {
                if ((rowId == $(this).data("row-id") || rowId == $(this).attr("data-row-id")) && $(this).is(":checked"))
                    deleteConsulting.push($(this).val());
            });
            var deletConsultingRows = deleteConsulting.join("~");

            var serviceid = $('.service-id[data-row-id="' + rowId + '"]').val() || '';
            var serviceType = $('.service-type[data-row-id="' + rowId + '"]').val();
            var certBody = $('.cert-body[data-row-id="' + rowId + '"]').val();
            var consultingFirm = $('.consulting-firm[data-row-id="' + rowId + '"]').val();
            var consultant = $('.consultant-list[data-row-id="' + rowId + '"]').val();
            var auditType = $('.audit-type[data-row-id="' + rowId + '"]').val();
            var auditor = $('.auditor-list[data-row-id="' + rowId + '"]').val();
            var auditdate = $('.audit-date[data-row-id="' + rowId + '"]').val();
            var serviceaddto = $('.add-to-service[data-row-id="' + rowId + '"]').val();
            var handelBy = $('.handel-by[data-row-id="' + rowId + '"]').val();
            var customerId = $('.customer_id[data-row-id="' + rowId + '"]').val() || "<?php echo isset($_GET['customerid']) ? $_GET['customerid'] : ''; ?>";

            var issue_cert = $('.issueCert[data-row-id="' + rowId + '"]').is(':checked') ? "YES" : "NO";

            if (!serviceType) {
                $('.auto-save-status-pill[data-row-id="' + rowId + '"]').removeClass('saving');
                return;
            }

            serviceSavesInFlight[rowId] = true;
            var saveSucceeded = false;
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
                    consultingTds: consultingTds,
                    consultingShortSettle: consultingShortSettle,
                    consultingBadDebt: consultingBadDebt,
                    serviceNotes: serviceNotes,
                    cbDeleteRows: deletCbRows,
                    consultingDeleteRows: deletConsultingRows
                },
                success: function (data) {
                    if (!data || !data[0] || !data[0].service_id) {
                        showToast("Service could not be saved. Please try again.", "error", "Save Failed");
                        return;
                    }
                    saveSucceeded = true;
                    var savedServiceId = data[0].service_id;
                    $('.service-id[data-row-id="' + rowId + '"]').val(savedServiceId);
                    var $serviceBox = $('#servicebox-data' + rowId);
                    $serviceBox.find('.issueCert, .serviceInvoiceBtn').attr('data-service', savedServiceId).data('service', savedServiceId);
                    $serviceBox.find('.serviceInvoiceBtn').attr('data-servicename', serviceType).attr('data-firm', consultingFirm);
                    var $pill = $('.auto-save-status-pill[data-row-id="' + rowId + '"]');
                    $pill.removeClass('saving error').html('<i class="fa fa-check-circle" style="color: #10b981;"></i> Auto-saved');

                    if (!silent) {
                        showToast("Payment & service details saved successfully!", "success", "Payment Saved");
                    }

                    // Update row IDs for new rows if returned
                    if (data && data[1] && data[1].length > 0) {
                        var $cbRows = $('#servicebox-data' + rowId).find('.cb-row-id');
                        $cbRows.each(function (idx) {
                            if (data[1][idx]) {
                                $(this).val(data[1][idx]['certification_id']);
                                $(this).closest('tr').find('.cbDeleteIcon').attr('data-id', data[1][idx]['certification_id']);
                            }
                        });
                    }
                    if (data && data[2] && data[2].length > 0) {
                        var $conRows = $('#servicebox-data' + rowId).find('.consulting-row-id');
                        $conRows.each(function (idx) {
                            if (data[2][idx]) {
                                $(this).val(data[2][idx]['consulting_id']);
                                $(this).closest('tr').find('.consultingDeleteIcon').attr('data-id', data[2][idx]['consulting_id']);
                            }
                        });
                    }

                    updateServiceKpiMetrics(rowId);

                    if (typeof callback === 'function') {
                        callback(data);
                    }
                },
                error: function (xhr, status, err) {
                    var $pill = $('.auto-save-status-pill[data-row-id="' + rowId + '"]');
                    $pill.removeClass('saving').addClass('error').html('<i class="fa fa-exclamation-triangle" style="color: #ef4444;"></i> Save error');
                    showToast("Error saving data. Please check your connection.", "error", "Save Failed");
                },
                complete: function () {
                    delete serviceSavesInFlight[rowId];
                    var needsSave = pendingServiceSaves[rowId];
                    delete pendingServiceSaves[rowId];
                    if (saveSucceeded && needsSave) executeServiceSave(rowId, null, silent);
                }
            });
        };

        // Legacy compatibility for .saveService click
        $(document.body).on('click', '.saveService', function (e) {
            e.preventDefault();
            var rowId = $(this).data("row-id") || $(this).attr("data-row-id");
            executeServiceSave(rowId, null, false);
        });

        /* ==========================================================
           LIVE AUTO-SAVE & BALANCE CALCULATION EVENT LISTENERS
           ========================================================== */
        // Helper: format currency
        function formatCurr(val) {
            return parseFloat(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Live calculation for Payment System matching Image 2
        function recalcServicePayment(rowId) {
            var $card = $('#service-payment-system-' + rowId);
            if (!$card.length) {
                $card = $('#service-row' + rowId).closest('.servicebox').find('.service-payment-system-card');
            }
            if (!$card.length) return;

            // 1. Sum Invoicing Gross Total from invoices in left table
            var invGrossTotal = 0;
            $card.find('.inv-data-row').each(function () {
                var amt = parseFloat($(this).attr('data-gross-amt')) || 0;
                invGrossTotal += amt;
            });
            $card.find('.invoicing-gross-total-field').val(invGrossTotal.toFixed(2));

            var targetAmt = invGrossTotal;

            // 2. Sum Received, TDS, Short Settle, Bad Debt
            var rcvTotal = 0;
            var tdsTotal = 0;
            var shortTotal = 0;
            var badTotal = 0;

            $card.find('#consulting_tbody_' + rowId + ' tr').each(function () {
                var $tr = $(this);
                var rcv = parseFloat($tr.find('.consultingPaymentReceived').val()) || 0;
                var tds = parseFloat($tr.find('.consultingTdsDeducted').val()) || 0;
                var sVal = parseFloat($tr.find('.consultingShortSettle').val()) || 0;
                var bVal = parseFloat($tr.find('.consultingBadDebt').val()) || 0;

                if ($tr.hasClass('row-is-loss')) {
                    badTotal += (bVal > 0 ? bVal : rcv);
                } else if ($tr.hasClass('row-is-short')) {
                    shortTotal += (sVal > 0 ? sVal : rcv);
                } else {
                    rcvTotal += rcv;
                    tdsTotal += tds;
                    shortTotal += sVal;
                    badTotal += bVal;
                }

                // Visual styling on Payment Received input
                var $rcvInput = $tr.find('.consultingPaymentReceived');
                if ($rcvInput.length && rcv > 0 && !$tr.hasClass('row-is-loss') && !$tr.hasClass('row-is-short')) {
                    $rcvInput.css({'border-color': '#10b981', 'background': '#f0fdf4', 'color': '#166534'});
                    if (!$tr.find('.payment-check-icon').length) {
                        $rcvInput.parent().append('<i class="fa fa-check text-success payment-check-icon" style="position: absolute; left: 6px; top: 5px; font-size: 10px;"></i>');
                    }
                } else if ($rcvInput.length && !$tr.hasClass('row-is-loss') && !$tr.hasClass('row-is-short')) {
                    $rcvInput.css({'border-color': '#cbd5e1', 'background': '#ffffff', 'color': '#0f172a'});
                    $tr.find('.payment-check-icon').remove();
                }
            });

            // Update footer totals
            $card.find('.val-received-total').text(formatCurr(rcvTotal));
            $card.find('.val-tds-total').text(formatCurr(tdsTotal));
            $card.find('.val-short-total').text(formatCurr(shortTotal));
            $card.find('.val-loss-total').text(formatCurr(badTotal));

            var effectiveCleared = rcvTotal + tdsTotal + shortTotal + badTotal;
            var diff = targetAmt - effectiveCleared;
            var balance = Math.max(0, diff);
            var excess = effectiveCleared > targetAmt ? (effectiveCleared - targetAmt) : 0;
            var isMatched = (targetAmt > 0 && Math.abs(diff) < 0.01);

            // Toggle green card styling & invoicing checkmark strictly when Invoicing Gross Total == Cleared/Received Amount
            $card.removeClass('is-matched is-excess has-due');
            var $invCheck = $card.find('.inv-matched-check-icon');
            if (isMatched) {
                $card.addClass('is-matched').css('border-color', '#22c55e');
                $invCheck.css('display', 'inline-flex');
            } else if (excess > 0) {
                $card.addClass('is-excess').css('border-color', '#f59e0b');
                $invCheck.hide();
            } else if (targetAmt > 0) {
                $card.addClass('has-due').css('border-color', '#cbd5e1');
                $invCheck.hide();
            } else {
                $card.css('border-color', '#cbd5e1');
                $invCheck.hide();
            }

            var $matchHeader = $card.closest('.compact-payment-block').find('.payment-match-indicator-header');
            var $statusChip = $card.find('.chip-matched-status');

            if (isMatched) {
                $matchHeader.html('<span style="color: #166534; font-weight: 700; font-size: 11.5px; margin-left: 4px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-check-circle"></i> MATCHED</span>');
                $statusChip.html('<span class="badge" style="background:#dcfce7; border:1px solid #86efac; color:#166534; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:6px;"><i class="fa fa-check-circle"></i> MATCHED: ₹0.00</span>');
            } else if (targetAmt > 0 && balance > 0) {
                $matchHeader.html('<span style="color: #dc2626; font-weight: 600; font-size: 11px; margin-left: 4px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-exclamation-circle"></i> DUE: ₹' + formatCurr(balance) + '</span>');
                $statusChip.html('<span class="badge" style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:6px;"><i class="fa fa-exclamation-circle"></i> BALANCE DUE: ₹' + formatCurr(balance) + '</span>');
            } else if (excess > 0) {
                $matchHeader.html('<span style="color: #d97706; font-weight: 700; font-size: 11px; margin-left: 4px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-exclamation-triangle"></i> EXCESS: ₹' + formatCurr(excess) + '</span>');
                $statusChip.html('<span class="badge" style="background:#fef3c7; border:1px solid #fcd34d; color:#b45309; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:6px;"><i class="fa fa-exclamation-triangle"></i> EXCESS: ₹' + formatCurr(excess) + '</span>');
            } else {
                $matchHeader.html('');
                $statusChip.html('<span class="badge" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:6px;">NO INVOICE</span>');
            }
        }

        // 1. Amount Decided, Payment Received & TDS: Live calculation + Auto-save
        $(document).on('input', '.amountDecided, .paymentReceived, .consultingAmountDecided, .consultingPaymentReceived, .consultingTdsDeducted', function () {
            var $tr = $(this).closest('tr');
            var rowId = $(this).attr('data-row-id') || $tr.find('[data-row-id]').first().attr('data-row-id');

            if ($(this).hasClass('amountDecided') || $(this).hasClass('paymentReceived')) {
                var decided = parseFloat($tr.find('.amountDecided').val()) || 0;
                var received = parseFloat($tr.find('.paymentReceived').val()) || 0;
                var bal = decided - received;
                $tr.find('.balance').val(bal.toFixed(2));
                updateServiceKpiMetrics(rowId);
            } else {
                recalcServicePayment(rowId);
            }

            autoSaveService(rowId, 600);
        });

        // 2. Immediate auto-save on blur / change for amount fields
        $(document).on('change', '.amountDecided, .paymentReceived, .consultingAmountDecided, .consultingPaymentReceived', function () {
            var rowId = $(this).attr('data-row-id') || $(this).closest('tr').find('[data-row-id]').first().attr('data-row-id');
            autoSaveService(rowId, 100);
        });

        // 3. Other payment table fields (deposit date, cheque, bank account, contract details)
        $(document).on('input', '.contractDetails, .consultingContractDetails, .chequeDetails, .consultingChequeDetails', function () {
            var rowId = $(this).attr('data-row-id') || $(this).closest('tr').find('[data-row-id]').first().attr('data-row-id');
            autoSaveService(rowId, 800);
        });

        $(document).on('change', '.depositDate, .consultingDepositDate, .bankAcccountNo, .consultingBankAcccountNo, .contractDetails, .consultingContractDetails, .chequeDetails, .consultingChequeDetails', function () {
            var rowId = $(this).attr('data-row-id') || $(this).closest('tr').find('[data-row-id]').first().attr('data-row-id');
            autoSaveService(rowId, 100);
        });

        // 4. Service metadata fields (Service Type, Cert Body, Consulting Firm, Consultant, Auditor, Audit Date, Add To, Handel By, Issue Cert)
        $(document).on('change', '.service-type, .cert-body, .consulting-firm, .consultant-list, .auditor-list, .audit-date, .add-to-service, .handel-by, .issueCert', function () {
            var rowId = $(this).attr('data-row-id');
            if ($(this).hasClass('service-type')) {
                var svcVal = $(this).val();
                $('.service_title[data-row-id="' + rowId + '"] .serviceStatus').text(svcVal);
            }
            autoSaveService(rowId, 100);
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
            $('.invoiceServiceId').val('');
            $('#serviceInvoiceBadge').hide();
            $('#invoiceModal').modal("show");
        });
        $('body').on('click', '.serviceInvoiceBtn', function (e) {
            e.preventDefault();
            var customerId = $(this).attr('data-customer');
            var serviceId = $(this).attr('data-service');
            var serviceName = $(this).attr('data-servicename');
            var firmName = $(this).attr('data-firm');

            $('.invoiceCustomerId').val(customerId);
            $('.invoiceServiceId').val(serviceId);

            if (serviceName) {
                $('#serviceInvoiceBadge').show();
                $('#serviceInvoiceName').text(serviceName);

                var $svcInput = $('.serviceAmtTd input[name="invoiceService[]"]').first();
                if ($svcInput.length && !$svcInput.val().trim()) {
                    $svcInput.val(serviceName + ' Professional Fees');
                }
            } else {
                $('#serviceInvoiceBadge').hide();
            }

            if (firmName && firmName.trim() !== '') {
                $('#invoiceconsultingFirmName option').each(function () {
                    if ($(this).text().trim().toLowerCase() === firmName.trim().toLowerCase()) {
                        $(this).prop('selected', true);
                        $('#invoiceconsultingFirmName').trigger('change');
                        return false;
                    }
                });
            }

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
                            var rowId = $row.find('[data-row-id]').first().attr('data-row-id');
                            $row.remove();
                            if (rowId) updateServiceKpiMetrics(rowId);
                            showToast("CB payment installment removed successfully.", "info", "Row Removed");
                        } else {
                            showToast(resp && resp.message ? resp.message : 'Unable to delete row.', 'error', 'Delete Failed');
                        }
                    },
                    error: function () {
                        showToast('Error while deleting row. Please try again.', 'error', 'Error');
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
                    var rowId = $row.find('[data-row-id]').first().attr('data-row-id');
                    $row.remove();
                    if (rowId) updateServiceKpiMetrics(rowId);
                    showToast("Unsaved row removed.", "info", "Removed");
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
                            var rowId = $row.find('[data-row-id]').first().attr('data-row-id');
                            $row.remove();
                            if (rowId) updateServiceKpiMetrics(rowId);
                            showToast("Consulting installment removed successfully.", "info", "Row Removed");
                        } else {
                            showToast(resp && resp.message ? resp.message : 'Unable to delete row.', 'error', 'Delete Failed');
                        }
                    },
                    error: function () {
                        showToast('Error while deleting row. Please try again.', 'error', 'Error');
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
                    var rowId = $row.find('[data-row-id]').first().attr('data-row-id');
                    $row.remove();
                    if (rowId) updateServiceKpiMetrics(rowId);
                    showToast("Unsaved row removed.", "info", "Removed");
                }
            });
        });

        $(document.body).on('click', '.consultingAddLink', function () {
            $rowId = $(this).data("row-id");
            var $tbody = $('#consulting_tbody_' + $rowId);
            if ($tbody.length > 0) {
                var newRow = '<tr class="consulting_subsection">' +
                    '<td><div class="table-edit-control"><input type="text" class="consultingContractDetails" name="consultingContractDetails" data-row-id="' + $rowId + '" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="consultingAmountDecided" name="consultingAmountDecided" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="consultingPaymentReceived" name="consultingPaymentReceived" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="consultingDepositDate datepicker" name="consultingDepositDate" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="consultingChequeDetails" name="consultingChequeDetails" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="consultingBankAcccountNo" name="consultingBankAcccountNo" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="consultingBalance" name="consultingBalance" placeholder="" readonly="" /></div></td>' +
                    '<td style="text-align:center;"><a href="javascript:void(0)" class="consultingRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a></td>' +
                    '</tr>';
                var $appended = $(newRow).appendTo($tbody);
                $appended.find('.consultingDepositDate').datepicker({ dateFormat: 'dd.mm.yy' });
                $tmpConsultCount = $tmpConsultCount + 1;
                return;
            }

            // Fallback for legacy monolithic table
            $rowCB = $('.cbAddLink[data-row-id="' + $rowId + '"]').data("row-count");
            if ($rowCB == "") { $rowCB = 1; }
            $rowConsult = $(this).data("row-count");
            if ($rowConsult == "") { $rowConsult = 1; }
            $count = $rowCB + $topfixedRow + $rowConsult + 1;
            var table = document.getElementById("service_details" + $rowId);
            if (table) {
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

                cell1.style.padding = "0px"; cell2.style.padding = "0px"; cell3.style.padding = "0px"; cell4.style.padding = "0px";
                cell5.style.padding = "0px"; cell6.style.padding = "0px"; cell7.style.padding = "0px"; cell8.style.padding = "0px";

                cell1.innerHTML = '<div class="table-edit-control"><input type="text" class="consultingContractDetails" name="consultingContractDetails" data-row-id="' + $rowId + '" placeholder="" /></div>';
                cell2.innerHTML = '<div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="consultingAmountDecided" name="consultingAmountDecided" placeholder="" /></div>';
                cell3.innerHTML = '<div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="consultingPaymentReceived" name="consultingPaymentReceived" placeholder="" /></div>';
                cell4.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="consultingDepositDate datepicker" name="consultingDepositDate" placeholder="" /></div>';
                cell5.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="consultingChequeDetails" name="consultingChequeDetails" placeholder="" /></div>';
                cell6.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="consultingBankAcccountNo" name="consultingBankAcccountNo" placeholder="" /></div>';
                cell7.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="consultingBalance" name="consultingBalance" placeholder="" readonly="" /></div>';
                cell8.innerHTML = '<?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?><a href="javascript:void(0)" class="consultingRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a><?php } ?>';
                row.className = "consulting_subsection ";
                $(row).find('.consultingDepositDate').datepicker({ dateFormat: 'dd.mm.yy' });
                $tmpConsultCount = $tmpConsultCount + 1;
            }
        });

        $(document.body).on('click', '.cbAddLink', function () {
            $rowId = $(this).data("row-id");
            var $tbody = $('#cb_tbody_' + $rowId);
            if ($tbody.length > 0) {
                var newRow = '<tr class="cb_subsection" id="cb_subsection_0">' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="contractDetails" name="contractDetails" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="amountDecided" name="amountDecided" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="paymentReceived" name="paymentReceived" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="depositDate datepicker" name="depositDate" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="chequeDetails" name="chequeDetails" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="bankAcccountNo" name="bankAcccountNo" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="balance" name="balance" placeholder="" readonly="" /></div></td>' +
                    '<td style="text-align:center;"><a href="javascript:void(0)" class="cbRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a></td>' +
                    '</tr>';
                var $appended = $(newRow).appendTo($tbody);
                $appended.find('.depositDate').datepicker({ dateFormat: 'dd.mm.yy' });
                $tmpCbCount = $tmpCbCount + 1;
                return;
            }

            // Fallback for legacy monolithic table
            $newCount = 0;
            $rowCB = $(this).data("row-count");
            if ($rowCB == "") { $rowCB = 1; }
            $count = $rowCB + $topfixedRow;
            var table = document.getElementById("service_details" + $rowId);
            if (table) {
                var row = table.insertRow($count);
                $(this).data("row-count", $rowCB + 1);
                $('.cbAddLinkForAdmin[data-row-id="' + $rowId + '"]').data("row-count", $rowCB + 1);
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);

                cell1.style.padding = "0px"; cell2.style.padding = "0px"; cell3.style.padding = "0px"; cell4.style.padding = "0px";
                cell5.style.padding = "0px"; cell6.style.padding = "0px"; cell7.style.padding = "0px"; cell8.style.padding = "0px";

                cell1.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="contractDetails" name="contractDetails" placeholder="" /></div>';
                cell2.innerHTML = '<div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="amountDecided" name="amountDecided" placeholder="" /></div>';
                cell3.innerHTML = '<div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="paymentReceived" name="paymentReceived" placeholder="" /></div>';
                cell4.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="depositDate datepicker" name="depositDate" placeholder="" /></div>';
                cell5.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="chequeDetails" name="chequeDetails" placeholder="" /></div>';
                cell6.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="bankAcccountNo" name="bankAcccountNo" placeholder="" /></div>';
                cell7.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="balance" name="balance" placeholder="" readonly="" /></div>';
                cell8.innerHTML = '<?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?><a href="javascript:void(0)" class="cbRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a><?php } ?>';
                row.className = "cb_subsection";
                $(row).find('.depositDate').datepicker({ dateFormat: 'dd.mm.yy' });
                $tmpCbCount = $tmpCbCount + 1;
            }
        });

        $(document.body).on('click', '.cbAddLinkForAdmin', function () {
            $rowId = $(this).data("row-id");
            var $tbody = $('#cb_tbody_' + $rowId);
            if ($tbody.length > 0) {
                var newRow = '<tr class="cb_subsection" id="cb_subsection_0">' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="contractDetails" name="contractDetails" placeholder="" value="Marketing Setup" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="amountDecided" name="amountDecided" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="paymentReceived" name="paymentReceived" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="depositDate datepicker" name="depositDate" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="chequeDetails" name="chequeDetails" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="bankAcccountNo" name="bankAcccountNo" placeholder="" /></div></td>' +
                    '<td><div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="balance" name="balance" placeholder="" readonly="" /></div></td>' +
                    '<td style="text-align:center;"><a href="javascript:void(0)" class="cbRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a></td>' +
                    '</tr>';
                var $appended = $(newRow).appendTo($tbody);
                $appended.find('.depositDate').datepicker({ dateFormat: 'dd.mm.yy' });
                $tmpCbCount = $tmpCbCount + 1;
                return;
            }

            // Fallback for legacy monolithic table
            $newCount = 0;
            $rowCB = $(this).data("row-count");
            if ($rowCB == "") { $rowCB = 1; }
            $count = $rowCB + $topfixedRow;
            var table = document.getElementById("service_details" + $rowId);
            if (table) {
                var row = table.insertRow($count);
                $(this).data("row-count", $rowCB + 1);
                $('.cbAddLink[data-row-id="' + $rowId + '"]').data("row-count", $rowCB + 1);
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);

                cell1.style.padding = "0px"; cell2.style.padding = "0px"; cell3.style.padding = "0px"; cell4.style.padding = "0px";
                cell5.style.padding = "0px"; cell6.style.padding = "0px"; cell7.style.padding = "0px"; cell8.style.padding = "0px";

                cell1.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="contractDetails" name="contractDetails" placeholder="" value="Marketing Setup" /></div>';
                cell2.innerHTML = '<div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="amountDecided" name="amountDecided" placeholder="" /></div>';
                cell3.innerHTML = '<div class="table-edit-control"><input type="number" data-row-id="' + $rowId + '" class="paymentReceived" name="paymentReceived" placeholder="" /></div>';
                cell4.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="depositDate datepicker" name="depositDate" placeholder="" /></div>';
                cell5.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="chequeDetails" name="chequeDetails" placeholder="" /></div>';
                cell6.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="bankAcccountNo" name="bankAcccountNo" placeholder="" /></div>';
                cell7.innerHTML = '<div class="table-edit-control"><input type="text" data-row-id="' + $rowId + '" class="balance" name="balance" placeholder="" readonly="" /></div>';
                cell8.innerHTML = '<?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForMainAdmin()) { ?><a href="javascript:void(0)" class="cbRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a><?php } ?>';
                row.className = "cb_subsection";
                $(row).find('.depositDate').datepicker({ dateFormat: 'dd.mm.yy' });
                $tmpCbCount = $tmpCbCount + 1;
            }
        });

        $('.addNewService').click(function () {
//            $('#addService').modal("show");
            $("#saveService0").hide();
//            alert(("<?php echo $services ?>"));
            var allServiceStagesArr = <?php echo json_encode(array_values($allServiceStages), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            var option = "";
            for (var key = 0; key < allServiceStagesArr.length; key++) {
                var svcname = allServiceStagesArr[key];
                if (svcname != null && svcname !== "") {
                    var escapedSvc = $('<div>').text(svcname).html();
                    option += '<option value="' + escapedSvc + '">' + escapedSvc + '</option>';
                }
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
                                <div class="col-xs-12 qms_customer_name service_title" data-row-id="" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; padding: 8px 14px;">\n\
                                    <div class="service-title-left" style="display: flex; align-items: center; gap: 8px; min-width: 80px;"><i class="fa fa-folder-open-o service-folder-icon" style="color: #ffffff; font-size: 15px;"></i></div>\n\
                                    <div class="service-title-center" style="flex: 1; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px;"><span style="font-weight: 800; font-size: 15px; letter-spacing: 0.5px; color: #ffffff; text-transform: uppercase;">SERVICE &mdash; <span class="serviceStatus" style="color: #ffd166; font-weight: 900; font-size: 16px; text-shadow: 0 1px 3px rgba(0,0,0,0.3);"></span></span></div>\n\
                                    <div class="service-title-right" style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; min-width: 80px;"><a class="saveService" data-row-id="" style="display:none !important;"></a><i class="fa fa-chevron-up service-collapse-indicator" style="color: #ffffff; font-size: 13px; margin-left: 4px;" title="Click to collapse / expand"></i></div>\n\
                                </div>\n\
                                </div>\n\
                                <div class="servicebox-data" id="servicebox-data">\n\
                                <div class="row service-detail-row service-detail-row-top" style="padding-top:2px;">\n\
                                    <div class="col-xs-3 form-group float-label-control"><label>Service Stage</label>\n\
                                        <select class="service-type" id="service-type" name="service-type" data-row-id="" value="">\n\
                                            <option value="" >Select Service Stage</option>'+option+'</select>\n\
                                    </div>\n\
                                    <div class="col-xs-3 form-group float-label-control"><label>Cert Body</label>\n\
                                        <select class="cert-body" id="cert-body" name="cert-body" data-row-id="" value="">\n\
                                            <option value="" >Select Certification Body</option>'+option1+'</select>\n\
                                    </div>\n\
                                    <div class="col-xs-3 form-group float-label-control"><label>Consulting firm</label>\n\
                                        <select class="consulting-firm" id="consulting-firm" name="consulting-firm" data-row-id="" value="">\n\
                                            <option value="" >Select Consulting Firm</option>'+option2+'</select>\n\
                                    </div>\n\
                                    <div class="col-xs-3 form-group float-label-control"><label>Lead Consultant</label>\n\
                                        <select class="consultant-list" id="consultant-list" name="consultant-list" data-row-id="" value="">\n\
                                            <option value="" >Select Lead Consultant</option>'+option3+'</select>\n\
                                    </div>\n\
                                    <input type="hidden" class="service-id" id="service-id" data-row-id="" name="service-id" value="">\n\
                                </div>\n\
                                    <div class="row service-detail-row">\n\
                                        <div class="col-xs-2 form-group float-label-control" style="margin-bottom:4px;"><label>Lead Auditor</label>\n\
                                            <select class="auditor-list" id="auditor-list" name="auditor-list" data-row-id="" value="">\n\
                                                <option value="" >Select Lead Auditor</option><?php foreach ($allAuditorList as $auditors) { ?><option value="<?php echo $auditors['auditor_name'] ?>"><?php echo $auditors['auditor_name'] ?></option><?php } ?></select>\n\
                                        </div>\n\
                                        <div class="col-xs-2 form-group float-label-control" style="margin-bottom:4px;"><label>Audit Date</label>\n\
                                            <input type="text" class="audit-date" data-row-id="" name="audit-date" placeholder="Audit Date" value="" required>\n\
                                        </div>\n\
                                        <div class="col-xs-3 form-group float-label-control" style="margin-bottom:4px;"><label>Add to</label>\n\
                                            <select class="add-to-service" id="add-to-service" name="add-to-service" data-row-id="" value="">\n\
                                                <option value="" >Select add to</option><?php foreach ($addTODo as $to) { ?><option value="<?php echo $to['add_to'] ?>"><?php echo $to['add_to'] ?></option><?php } ?></select>\n\
                                        </div>\n\
                                        <div class="col-xs-2 form-group float-label-control" style="margin-bottom:4px;"><label>Dealing Officer</label>\n\
                                            <select class="handel-by" id="handel-by" name="handel-by" data-row-id="" value="">\n\
                                                <option value="" >Select Dealing Officer</option><?php foreach ($handlerList as $handler) { ?><option value="<?php echo $handler['handler_name'] ?>"><?php echo $handler['handler_name'] ?></option><?php } ?></select>\n\
                                        </div>\n\
                                        <div class="col-xs-3 form-group cert-given-group" style="margin-bottom:4px;">\n\
                                            <label class="cert-given-box"><input type="checkbox" data-row-id="" class="issueCert" data-service="" data-customer="" value=""><span>Certificate Given</span></label>\n\
                                        </div>\n\
                                        <input type="hidden" class="customer_id" id="customer_id" data-row-id="" name="customer_id" value="<?php echo $_GET['customerid'] ?>"/>\n\
                                    </div>\n\
                                    <div class="row" style="margin-bottom: 4px; padding: 0 5px;">\n\
                                        <div class="col-xs-12">\n\
                                            <div class="compact-payment-block consulting-payment-block">\n\
                                                <div class="compact-table-card">\n\
                                                    <div class="compact-table-header consulting-header" style="cursor: pointer;" title="Click to collapse / expand Consulting section">\n\
                                                        <div class="header-title" style="cursor: pointer;"><i class="fa fa-handshake-o"></i> Consulting Fees & Installments <i class="fa fa-chevron-up consulting-collapse-indicator section-collapse-indicator" style="margin-left: 6px; font-size: 10px; color: #ffffff; opacity: 0.9;"></i></div>\n\
                                                        <div class="header-actions">\n\
                                                            <button type="button" class="btn btn-xs serviceInvoiceBtn header-invoice-btn" data-customer="<?php echo htmlspecialchars($_GET['customerid']); ?>" data-service="" data-servicename="" data-firm=""><i class="fa fa-plus-circle"></i> Issue Invoice</button>\n\
                                                            <a class="consultingAddLink btn btn-xs btn-compact-add" data-row-id="" data-row-count="1"><i class="fa fa-plus-circle"></i> Add Consulting Row</a>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                    <div class="consulting-content-body payment-content-body">\n\
                                                        <div class="table-responsive">\n\
                                                            <table class="table table-bordered table-hover compact-payment-table service_details" id="consulting_table_">\n\
                                                                <thead><tr>\n\
                                                                    <th width="24%">Installment Details</th>\n\
                                                                    <th width="13%" style="text-align: right;">Amount Decided</th>\n\
                                                                    <th width="13%" style="text-align: right;">Payment Received</th>\n\
                                                                    <th width="13%" style="text-align: center;">Date of Deposit</th>\n\
                                                                    <th width="13%">Instrument No</th>\n\
                                                                    <th width="10%">Bank A/c</th>\n\
                                                                    <th width="10%" style="text-align: right;">Balance</th>\n\
                                                                    <th width="4%" style="text-align: center;"><i class="fa fa-close"></i></th>\n\
                                                                </tr></thead>\n\
                                                                <tbody id="consulting_tbody_">\n\
                                                                    <tr class="consulting_subsection">\n\
                                                                        <td><div class="table-edit-control"><input type="text" data-row-id="" class="consultingContractDetails" name="consultingContractDetails" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="number" data-row-id="" class="consultingAmountDecided" name="consultingAmountDecided" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="number" data-row-id="" class="consultingPaymentReceived" name="consultingPaymentReceived" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="text" data-row-id="" class="consultingDepositDate datepicker" name="consultingDepositDate" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="text" data-row-id="" class="consultingChequeDetails" name="consultingChequeDetails" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="text" data-row-id="" class="consultingBankAcccountNo" name="consultingBankAcccountNo" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="text" class="consultingBalance" data-row-id="" name="consultingBalance" value="" placeholder="" readonly="" /></div></td>\n\
                                                                        <td style="text-align:center;"><a href="javascript:void(0)" class="consultingRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a></td>\n\
                                                                    </tr>\n\
                                                                    <input data-row-id="" type="hidden" class="consulting-row-id" name="consulting-row-id" value="">\n\
                                                                </tbody>\n\
                                                            </table>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                                <div class="consulting-summary-wrapper consulting-content-body payment-content-body">\n\
                                                    <div class="compact-summary-panel consulting-summary-panel">\n\
                                                        <div class="summary-top-bar">\n\
                                                            <div class="summary-title"><i class="fa fa-file-text-o" style="color: #0891b2; margin-right: 4px;"></i> Consulting Financial Summary</div>\n\
                                                        </div>\n\
                                                        <div class="compact-kpi-row">\n\
                                                            <div class="kpi-chip kpi-consult-decided"><span class="kpi-chip-label">Consulting Decided</span><span class="kpi-chip-value">₹ 0.00</span></div>\n\
                                                            <div class="kpi-chip kpi-consult-invoiced"><span class="kpi-chip-label">Total Invoiced</span><span class="kpi-chip-value">₹ 0.00</span></div>\n\
                                                            <div class="kpi-chip kpi-consult-received"><span class="kpi-chip-label">Payment Received</span><span class="kpi-chip-value">₹ 0.00</span></div>\n\
                                                            <div class="kpi-chip kpi-consult-balance kpi-chip-success"><span class="kpi-chip-label">Invoice Balance Due</span><span class="kpi-chip-value">₹ 0.00</span></div>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                            <div class="payment-dotted-divider" style="cursor: pointer;" title="Click to toggle External Party Payment">\n\
                                                <span class="divider-label"><i class="fa fa-university" style="color: #1e293b;"></i> External Party Payment <i class="fa fa-chevron-down divider-collapse-indicator" style="font-size: 8.5px; margin-left: 3px;"></i></span>\n\
                                            </div>\n\
                                            <div class="compact-payment-block cb-payment-block">\n\
                                                <div class="compact-table-card is-collapsed">\n\
                                                    <div class="compact-table-header cb-header" style="cursor: pointer;" title="Click to collapse / expand External Party Payment section">\n\
                                                        <div class="header-title" style="cursor: pointer;"><i class="fa fa-university"></i> External Party Payment <i class="fa fa-chevron-down cb-collapse-indicator section-collapse-indicator" style="margin-left: 6px; font-size: 10px; color: #ffffff; opacity: 0.9;"></i></div>\n\
                                                        <div class="header-actions">\n\
                                                            <a class="cbAddLinkForAdmin btn btn-xs btn-compact-ms" data-row-id="" data-row-count="1" title="Add Marketing Setup">MS</a>\n\
                                                            <a class="btn btn-xs cbAddLink btn-compact-add" data-row-id="" data-row-count="1"><i class="fa fa-plus-circle"></i> Add External Party Row</a>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                    <div class="cb-content-body payment-content-body" style="display: none;">\n\
                                                        <div class="table-responsive">\n\
                                                            <table class="table table-bordered table-hover compact-payment-table service_details" id="cb_table_">\n\
                                                                <thead><tr>\n\
                                                                    <th width="24%">Installment Details</th>\n\
                                                                    <th width="13%" style="text-align: right;">Amount Decided</th>\n\
                                                                    <th width="13%" style="text-align: right;">Payment Received</th>\n\
                                                                    <th width="13%" style="text-align: center;">Date of Deposit</th>\n\
                                                                    <th width="13%">Instrument No</th>\n\
                                                                    <th width="10%">Bank A/c</th>\n\
                                                                    <th width="10%" style="text-align: right;">Balance</th>\n\
                                                                    <th width="4%" style="text-align: center;"><i class="fa fa-close"></i></th>\n\
                                                                </tr></thead>\n\
                                                                <tbody id="cb_tbody_">\n\
                                                                    <tr class="cb_subsection" id="cb_subsection_0">\n\
                                                                        <td><div class="table-edit-control"><input type="text" data-row-id="" class="contractDetails" name="contractDetails" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="number" data-row-id="" class="amountDecided" name="amountDecided" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="number" data-row-id="" class="paymentReceived" name="paymentReceived" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="text" data-row-id="" class="depositDate datepicker" name="depositDate" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="text" data-row-id="" class="chequeDetails" name="chequeDetails" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="text" data-row-id="" class="bankAcccountNo" name="bankAcccountNo" value="" placeholder="" /></div></td>\n\
                                                                        <td><div class="table-edit-control"><input type="text" data-row-id="" class="balance" name="balance" value="" placeholder="" readonly="" /></div></td>\n\
                                                                        <td style="text-align:center;"><a href="javascript:void(0)" class="cbRowDelete"><i class="fa fa-close" aria-hidden="true"></i></a></td>\n\
                                                                    </tr>\n\
                                                                    <input data-row-id="" type="hidden" class="cb-row-id" name="cb-row-id" value="">\n\
                                                                </tbody>\n\
                                                            </table>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                                <div class="cb-summary-wrapper cb-content-body payment-content-body" style="display: none;">\n\
                                                    <div class="compact-summary-panel cb-summary-panel">\n\
                                                        <div class="summary-top-bar">\n\
                                                            <div class="summary-title"><i class="fa fa-calculator" style="color: #1e293b; margin-right: 4px;"></i> External Party Payment Financial Summary</div>\n\
                                                        </div>\n\
                                                        <div class="compact-kpi-row">\n\
                                                            <div class="kpi-chip kpi-cb-decided"><span class="kpi-chip-label">External Party Decided</span><span class="kpi-chip-value">₹ 0.00</span></div>\n\
                                                            <div class="kpi-chip kpi-cb-received"><span class="kpi-chip-label">External Party Payment Received</span><span class="kpi-chip-value">₹ 0.00</span></div>\n\
                                                            <div class="kpi-chip kpi-cb-balance kpi-chip-success"><span class="kpi-chip-label">External Party Balance Due</span><span class="kpi-chip-value">₹ 0.00</span></div>\n\
                                                        </div>\n\
                                                    </div>\n\
                                                </div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div></div>\n\
    </div>\n\
</div>';
            var draftRowId = 'new-' + (++newServiceSequence);
            var $newService = $($serviceStr);
            $newService.find('[data-row-id]').attr('data-row-id', draftRowId);
            $newService.find('[id]').each(function () {
                this.id += draftRowId;
            });
            // The UI key identifies the draft; service-id stays empty until INSERT succeeds.
            $newService.find('.service-id').val('');
            $newService.find('.issueCert').attr('data-customer', $newService.find('.customer_id').val());
            $(".servicebox-bunch").append($newService);
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


        // Add regular payment row matching Image 2
        $(document).on('click', '.btn-add-payment-row', function (e) {
            e.preventDefault();
            var serviceId = $(this).data('service-id');
            var $tbody = $('#consulting_tbody_' + serviceId);
            var newRow = '<tr class="consulting_subsection">' +
                '<td style="padding: 2px 4px; border: none;">' +
                    '<div class="table-edit-control payment-input-wrap" style="position: relative;">' +
                        '<input type="number" step="0.01" data-row-id="' + serviceId + '" class="consultingPaymentReceived form-control" name="consultingPaymentReceived" value="" placeholder="0.00" style="height: 23px; font-size: 11.5px; font-weight: 600; font-variant-numeric: tabular-nums; text-align: right; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px;" />' +
                    '</div>' +
                '</td>' +
                '<td style="padding: 2px 4px; border: none;">' +
                    '<div class="table-edit-control">' +
                        '<input type="number" step="0.01" data-row-id="' + serviceId + '" class="consultingTdsDeducted form-control" name="consultingTdsDeducted" value="" placeholder="" style="height: 23px; font-size: 11.5px; font-weight: 600; font-variant-numeric: tabular-nums; text-align: right; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px;" />' +
                    '</div>' +
                '</td>' +
                '<td style="padding: 2px 4px; border: none;">' +
                    '<div class="table-edit-control">' +
                        '<input type="text" data-row-id="' + serviceId + '" class="form-control consultingDepositDate datepicker" name="consultingDepositDate" value="" placeholder="dd.mm.yyyy" style="height: 23px; font-size: 11px; text-align: center; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 4px; background: #ffffff;" />' +
                    '</div>' +
                '</td>' +
                '<td style="padding: 2px 4px; border: none;">' +
                    '<div class="table-edit-control">' +
                        '<input type="text" data-row-id="' + serviceId + '" class="consultingChequeDetails form-control" name="consultingChequeDetails" value="" placeholder="NEFT / Cheque" style="height: 23px; font-size: 11px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px;" />' +
                    '</div>' +
                '</td>' +
                '<td style="padding: 2px 4px; border: none; text-align: center; vertical-align: middle;">' +
                    '<a href="javascript:void(0)" class="consultingRowDelete" title="Delete Row" style="color: #ef4444; font-size: 11px;"><i class="fa fa-trash-o"></i></a>' +
                '</td>' +
                '<input type="hidden" data-row-id="' + serviceId + '" class="consultingContractDetails" name="consultingContractDetails" value="Payment" />' +
                '<input type="hidden" data-row-id="' + serviceId + '" class="consultingAmountDecided" name="consultingAmountDecided" value="0" />' +
                '<input type="hidden" data-row-id="' + serviceId + '" class="consultingBankAcccountNo" name="consultingBankAcccountNo" value="" />' +
                '<input type="hidden" data-row-id="' + serviceId + '" class="consultingBalance" name="consultingBalance" value="0" />' +
                '<input type="hidden" data-row-id="' + serviceId + '" class="consultingShortSettle" name="consultingShortSettle" value="0" />' +
                '<input type="hidden" data-row-id="' + serviceId + '" class="consultingBadDebt" name="consultingBadDebt" value="0" />' +
                '<input type="hidden" data-row-id="' + serviceId + '" class="consulting-row-id" name="consulting-row-id" value="">' +
            '</tr>';
            var $appended = $(newRow).appendTo($tbody);
            $appended.find('.datepicker').datepicker({ dateFormat: 'dd.mm.yy' });
            $appended.find('.consultingPaymentReceived').focus();
        });

        // Add Loss / Bad Debt row
        $(document).on('click', '.btn-add-loss', function (e) {
            e.preventDefault();
            var serviceId = $(this).data('service-id');
            var lossAmt = prompt("Enter Bad Debt / Loss Amount (₹):");
            if (lossAmt && !isNaN(lossAmt) && parseFloat(lossAmt) > 0) {
                var num = parseFloat(lossAmt);
                var $tbody = $('#consulting_tbody_' + serviceId);
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                var dStr = dd + '.' + mm + '.' + yyyy;

                var newRow = '<tr class="consulting_subsection row-is-loss" style="background: #fff1f2;">' +
                    '<td style="padding: 2px 4px; border: none;">' +
                        '<span class="badge" style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; font-size:10px; font-weight:600; letter-spacing:0.02em;">LOSS / BAD DEBT</span>' +
                        '<input type="hidden" data-row-id="' + serviceId + '" class="consultingPaymentReceived" name="consultingPaymentReceived" value="0" />' +
                    '</td>' +
                    '<td style="padding: 2px 4px; border: none;">' +
                        '<span style="font-size:11px; color:#dc2626; font-weight:700;">-₹' + formatCurr(num) + '</span>' +
                        '<input type="hidden" data-row-id="' + serviceId + '" class="consultingTdsDeducted" name="consultingTdsDeducted" value="0" />' +
                    '</td>' +
                    '<td style="padding: 2px 4px; border: none;">' +
                        '<input type="text" data-row-id="' + serviceId + '" class="form-control consultingDepositDate datepicker" name="consultingDepositDate" value="' + dStr + '" style="height: 23px; font-size: 11px; text-align: center; border: 1px solid #fca5a5; border-radius: 4px; padding: 1px 4px; background: #ffffff;" />' +
                    '</td>' +
                    '<td style="padding: 2px 4px; border: none;">' +
                        '<input type="text" data-row-id="' + serviceId + '" class="consultingChequeDetails form-control" name="consultingChequeDetails" value="Bad Debt / Loss" style="height: 23px; font-size: 11px; border: 1px solid #fca5a5; border-radius: 4px; padding: 1px 6px;" />' +
                    '</td>' +
                    '<td style="padding: 2px 4px; border: none; text-align: center; vertical-align: middle;">' +
                        '<a href="javascript:void(0)" class="consultingRowDelete" title="Delete Row" style="color: #ef4444; font-size: 11px;"><i class="fa fa-trash-o"></i></a>' +
                    '</td>' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingContractDetails" name="consultingContractDetails" value="Loss" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingAmountDecided" name="consultingAmountDecided" value="0" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingBankAcccountNo" name="consultingBankAcccountNo" value="" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingBalance" name="consultingBalance" value="0" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingShortSettle" name="consultingShortSettle" value="0" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingBadDebt" name="consultingBadDebt" value="' + num + '" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consulting-row-id" name="consulting-row-id" value="">' +
                '</tr>';
                var $appended = $(newRow).appendTo($tbody);
                $appended.find('.datepicker').datepicker({ dateFormat: 'dd.mm.yy' });
                recalcServicePayment(serviceId);
                autoSaveService(serviceId, 100);
            }
        });

        // Add Short Settle / Adjustment row
        $(document).on('click', '.btn-add-adj', function (e) {
            e.preventDefault();
            var serviceId = $(this).data('service-id');
            var sAmt = prompt("Enter Short Settlement / Discount Amount (₹):");
            if (sAmt && !isNaN(sAmt) && parseFloat(sAmt) > 0) {
                var num = parseFloat(sAmt);
                var $tbody = $('#consulting_tbody_' + serviceId);
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                var dStr = dd + '.' + mm + '.' + yyyy;

                var newRow = '<tr class="consulting_subsection row-is-short" style="background: #faf5ff;">' +
                    '<td style="padding: 2px 4px; border: none;">' +
                        '<span class="badge" style="background:#f3e8ff; border:1px solid #d8b4fe; color:#6b21a8; font-size:10px; font-weight:600; letter-spacing:0.02em;">SHORT SETTLE</span>' +
                        '<input type="hidden" data-row-id="' + serviceId + '" class="consultingPaymentReceived" name="consultingPaymentReceived" value="0" />' +
                    '</td>' +
                    '<td style="padding: 2px 4px; border: none;">' +
                        '<span style="font-size:11px; color:#7c3aed; font-weight:700;">-₹' + formatCurr(num) + '</span>' +
                        '<input type="hidden" data-row-id="' + serviceId + '" class="consultingTdsDeducted" name="consultingTdsDeducted" value="0" />' +
                    '</td>' +
                    '<td style="padding: 2px 4px; border: none;">' +
                        '<input type="text" data-row-id="' + serviceId + '" class="form-control consultingDepositDate datepicker" name="consultingDepositDate" value="' + dStr + '" style="height: 23px; font-size: 11px; text-align: center; border: 1px solid #d8b4fe; border-radius: 4px; padding: 1px 4px; background: #ffffff;" />' +
                    '</td>' +
                    '<td style="padding: 2px 4px; border: none;">' +
                        '<input type="text" data-row-id="' + serviceId + '" class="consultingChequeDetails form-control" name="consultingChequeDetails" value="Short Settle / Round off" style="height: 23px; font-size: 11px; border: 1px solid #d8b4fe; border-radius: 4px; padding: 1px 6px;" />' +
                    '</td>' +
                    '<td style="padding: 2px 4px; border: none; text-align: center; vertical-align: middle;">' +
                        '<a href="javascript:void(0)" class="consultingRowDelete" title="Delete Row" style="color: #ef4444; font-size: 11px;"><i class="fa fa-trash-o"></i></a>' +
                    '</td>' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingContractDetails" name="consultingContractDetails" value="Short Settle" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingAmountDecided" name="consultingAmountDecided" value="0" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingBankAcccountNo" name="consultingBankAcccountNo" value="" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingBalance" name="consultingBalance" value="0" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingShortSettle" name="consultingShortSettle" value="' + num + '" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consultingBadDebt" name="consultingBadDebt" value="0" />' +
                    '<input type="hidden" data-row-id="' + serviceId + '" class="consulting-row-id" name="consulting-row-id" value="">' +
                '</tr>';
                var $appended = $(newRow).appendTo($tbody);
                $appended.find('.datepicker').datepicker({ dateFormat: 'dd.mm.yy' });
                recalcServicePayment(serviceId);
                autoSaveService(serviceId, 100);
            }
        });

        // Link unassigned invoice to service
        $(document).on('click', '.linkInvoiceAction', function (e) {
            e.preventDefault();
            var invoiceId = $(this).data('invoice-id');
            var serviceId = $(this).data('service-id');
            var customerId = $(this).data('customer-id');

            $.post('addService.php', {
                action: 'link_invoice',
                invoice_id: invoiceId,
                service_id: serviceId,
                customer_id: customerId
            }, function (res) {
                showToast("Invoice linked to service successfully!", "success", "Invoice Linked");
                setTimeout(function () { location.reload(); }, 500);
            }, 'json');
        });

        // Unlink invoice from service
        $(document).on('click', '.unlinkInvoiceAction', function (e) {
            e.preventDefault();
            if (!confirm("Are you sure you want to unlink this invoice from this service?")) return;
            var invoiceId = $(this).data('invoice-id');
            var customerId = $(this).data('customer-id');

            $.post('addService.php', {
                action: 'unlink_invoice',
                invoice_id: invoiceId,
                customer_id: customerId
            }, function (res) {
                showToast("Invoice unlinked successfully.", "info", "Unlinked");
                setTimeout(function () { location.reload(); }, 500);
            }, 'json');
        });

        // Auto-save service notes with debounce
        var serviceNotesTimers = {};
        $(document).on('input', '.serviceNotesText', function () {
            var $this = $(this);
            var serviceId = $this.data('service-id');
            var notes = $this.val();
            if (serviceNotesTimers[serviceId]) clearTimeout(serviceNotesTimers[serviceId]);
            serviceNotesTimers[serviceId] = setTimeout(function () {
                $.post('addService.php', {
                    action: 'save_service_notes',
                    service_id: serviceId,
                    notes: notes
                }, function () {
                    showToast("Service notes saved.", "success", "Saved");
                }, 'json');
            }, 600);
        });


        // ----------------------------------------------------
        // Contract Details Scan Attachment (PDF / JPG / PNG)
        // ----------------------------------------------------
        function updateContractAttachmentStatus($card) {
            var count = $card.find('.contract-att-card').length;
            var $badge = $card.find('.contract-att-status-badge');

            if (count > 0) {
                $card.addClass('has-attachments');
                $badge.removeClass('is-empty').addClass('is-attached')
                    .html('<i class="fa fa-check-circle"></i> ' + count + ' Attached');
            } else {
                $card.removeClass('has-attachments');
                $badge.removeClass('is-attached').addClass('is-empty')
                    .html('No Attachment');
            }
        }

        $(document).on('click', '.btnAttachContractScan', function (e) {
            e.preventDefault();
            var $card = $(this).closest('.contract-details-card');
            $card.find('.contractScanFileInput').trigger('click');
        });

        $(document).on('change', '.contractScanFileInput', function () {
            var input = this;
            if (!input.files || input.files.length === 0) return;

            var $input = $(this);
            var $card = $input.closest('.contract-details-card');
            var $btn = $card.find('.btnAttachContractScan');
            var $list = $card.find('.contract-files-list');
            var serviceId = $input.attr('data-service-id');
            var customerId = $input.attr('data-customer-id');

            var allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
            var formData = new FormData();
            formData.append('action', 'upload');
            formData.append('customer_id', customerId);
            formData.append('service_id', serviceId);

            var validCount = 0;
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];
                var parts = (file.name || '').split('.');
                var ext = parts.length > 1 ? parts.pop().toLowerCase() : '';
                if (allowedExts.indexOf(ext) === -1) {
                    if (typeof showToast === 'function') {
                        showToast('Invalid file: ' + file.name + '. Only PDF, JPG, JPEG, and PNG are allowed.', 'error', 'Invalid File');
                    } else {
                        alert('Only PDF, JPG, JPEG, and PNG files are allowed.');
                    }
                    continue;
                }
                formData.append('contract_file[]', file);
                validCount++;
            }

            if (validCount === 0) {
                $input.val('');
                return;
            }

            var origBtnHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');

            $.ajax({
                url: 'contractAttachmentAction.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html(origBtnHtml);
                    $input.val('');

                    if (res && res.status === 'success' && res.attachments) {
                        for (var j = 0; j < res.attachments.length; j++) {
                            var att = res.attachments[j];
                            var extLower = (att.file_ext || '').toLowerCase();
                            var isPdf = (extLower === 'pdf');

                            var $attCard = $('<div class="contract-att-card"></div>').attr('data-att-id', att.id);

                            var $thumb = $('<a target="_blank" class="contract-att-thumb"></a>')
                                .attr('href', att.file_path)
                                .attr('title', 'Preview ' + att.file_name)
                                .addClass(isPdf ? 'is-pdf' : 'is-img');
                            if (isPdf) {
                                $thumb.html('<i class="fa fa-file-pdf-o"></i>');
                            } else {
                                $thumb.append($('<img alt="Scan" />').attr('src', att.file_path));
                            }
                            $attCard.append($thumb);

                            $attCard.append(
                                $('<a target="_blank" class="contract-att-name"></a>')
                                    .attr('href', att.file_path)
                                    .attr('title', att.file_name)
                                    .text(att.file_name)
                            );
                            $attCard.append(
                                $('<a target="_blank" class="contract-btn-view" title="View Scan">View</a>')
                                    .attr('href', att.file_path)
                            );
                            $attCard.append(
                                $('<a class="contract-btn-dl" title="Download"><i class="fa fa-download"></i></a>')
                                    .attr('href', att.file_path)
                                    .attr('download', att.file_name)
                            );
                            $attCard.append(
                                $('<a href="javascript:void(0)" class="deleteContractAttachment contract-btn-del" title="Remove attachment"><i class="fa fa-trash-o"></i></a>')
                                    .attr('data-att-id', att.id)
                                    .attr('data-service-id', serviceId)
                                    .attr('data-customer-id', customerId)
                            );

                            $list.append($attCard);
                        }
                        updateContractAttachmentStatus($card);

                        if (typeof showToast === 'function') {
                            showToast(res.attachments.length + ' contract scan(s) attached.', 'success', 'Uploaded');
                        }
                    } else {
                        var msg = (res && res.message) ? res.message : 'Upload failed.';
                        if (typeof showToast === 'function') {
                            showToast(msg, 'error', 'Upload Failed');
                        } else {
                            alert(msg);
                        }
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html(origBtnHtml);
                    $input.val('');
                    if (typeof showToast === 'function') {
                        showToast('Failed to upload attachment.', 'error', 'Upload Error');
                    } else {
                        alert('Failed to upload attachment.');
                    }
                }
            });
        });

        $(document).on('click', '.deleteContractAttachment', function (e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to remove this contract attachment?')) return;
            var $link = $(this);
            var $card = $link.closest('.contract-details-card');
            var attId = $link.attr('data-att-id');
            var serviceId = $link.attr('data-service-id');
            var customerId = $link.attr('data-customer-id');

            $.post('contractAttachmentAction.php', {
                action: 'delete',
                attachment_id: attId,
                customer_id: customerId,
                service_id: serviceId
            }, function (res) {
                if (res && res.status === 'success') {
                    $link.closest('.contract-att-card').fadeOut(150, function () {
                        $(this).remove();
                        updateContractAttachmentStatus($card);
                    });
                    if (typeof showToast === 'function') {
                        showToast('Attachment removed.', 'info', 'Removed');
                    }
                }
            }, 'json');
        });

        // Payment System Header Toggle
        $(document).on('click', '.payment-system-header', function (e) {
            if ($(e.target).closest('.header-actions, button, a').length) return;
            var $block = $(this).closest('.compact-payment-block');
            var $body = $block.find('.payment-system-content-body');
            var $icon = $(this).find('.payment-system-collapse-indicator');
            $body.slideToggle(180, function () {
                if ($body.is(':visible')) {
                    $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });
        });
    });
</script>
