<div class="servicebox serviceEditDeta" style="display: none">
    <div class="row">
        <div class="col-xs-12 qms_customer_name" style="text-align: center">Service - <span class="serviceStatus"><?php if (isset($customer['service_type'])) echo $customer['service_type']; ?></span>
            <a class="saveService"><i class="fa fa-floppy-o pull-right " style="color:white; padding-right:10px"></i></a>
        </div>
    </div>
    <div class="row" style="padding-top:10px">
        <div class="col-xs-3 form-group float-label-control">
            <label>Service Type</label>
            <input type="text" class="service-type" id="service-type" name="service-type" placeholder="Service Type" value="<?php if (isset($service1['service_type'])) echo $service1['service_type'] ?>" required>
        </div>
        <div class="col-xs-3 form-group float-label-control">
            <label>Cert Body</label>
            <input type="text" class="cert-body" id="cert-body" name="cert-body" placeholder="Ceritfication Body" value="<?php if (isset($service1['cert_body'])) echo $service1['cert_body'] ?>" required>
        </div>
        <div class="col-xs-3 form-group float-label-control">
            <label>Consulting firm</label>
            <input type="text" class="consulting-firm" id="consulting-firm" name="consulting-firm" placeholder="Consulting firm" value="<?php if (isset($service1['consulting_firm'])) echo $service1['consulting_firm'] ?>" required>
        </div>
        <div class="col-xs-3 form-group float-label-control">
            <label>Consultant</label>
            <input type="text" class="consultant-list" id="consultant-list" name="consultant-list" placeholder="Consultant" value="<?php if (isset($service1['consultant'])) echo $service1['consultant'] ?>" required>
        </div>
        <input type="hidden" class="service-id" id="service-id" name="service-id" value="<?php if (isset($serviceId)) echo $serviceId; ?>">
    </div>
    <div class="row">
        <div class="col-xs-3 form-group float-label-control">
            <label>Audit Type</label>
            <input type="text" class="audit-type" id="audit-type" name="audit-type" placeholder="Audit Type" value="<?php if (isset($service1['audit_type'])) echo $service1['audit_type'] ?>" required>
        </div>
        <!--                <div class="col-xs-3 form-group float-label-control">
                                    <label>Audit Type</label>
                                    <select name="auditType" class="form-control auditType" id="auditType">
                                        <option value="">Select Audit Type</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Working Project">Working Project</option>
                                    </select>
                                </div>-->
        <div class="col-xs-3 form-group float-label-control">
            <label>Auditor</label>
            <input type="text" class="auditor-list" id="auditor-list" name="auditor-list" placeholder="Auditor" value="<?php if (isset($service1['auditor'])) echo $service1['auditor'] ?>" required>
        </div>
        <div class="col-xs-3 form-group float-label-control">
            <label>Audit Date</label>
            <input type="text" class="audit-date" id="audit-date" name="audit-date" placeholder="Audit Date" value="<?php if (isset($service1['audit_date'])) echo $service1['audit_date'] ?>" required>
        </div>
        <input type="hidden" class="customer_id" id="customer_id" name="customer_id" value="<?php echo $_GET['customerid'] ?>" </div>
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-bordered table-hover table-sortable" id="service_details">
                    <thead>
                        <tr>
                            <th class="" width="18%">
                                <small>Contract Details</small>
                            </th>
                            <th class="" width="13%">
                                <small>Amount Decided</small>
                            </th>
                            <th class="" width="13%">
                                <small>Payment received</small>
                            </th>
                            <th class="" width="13%">
                                <small>Date of Deposit</small>
                            </th>
                            <th class="" width="13%">
                                <small>Chaque Details</small>
                            </th>
                            <th class="" width="13%">
                                <small>Bank A/c </small>
                            </th>
                            <th class="" width="13%">
                                <small>Balance</small>
                            </th>
                            <th class="" width="4%">
                                <i class="fa fa-comment-o" aria-hidden="true"></i>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="">
                        <tr class="cb_section">
                            <td colspan=8>CB <a class="cbAddLink"><i class="fa fa-plus-circle fa-lg pull-right"></i></a></td>
                        </tr>

                        <tr class="cb_subsection" id="cb_subsection_0">
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="contractDetails" id="contractDetails" name="contractDetails" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="amountDecided" id="amountDecided" name="amountDecided" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="paymentReceived" id="paymentReceived" name="paymentReceived" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="depositDate" id="depositDate" name="depositDate" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="chequeDetails" id="chequeDetails" name="chequeDetails" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="bankAcccountNo" id="bankAcccountNo" name="bankAcccountNo" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="balance" id="balance" name="balance" value="" placeholder="" readonly="" />
                                </div>
                            </td>
                            <td class="">
                                <a class="cbComment"><i class="fa fa-comment-o" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        <input type="hidden" class="contractDetailsHidden" id="contractDetailsHidden" name="contractDetailsHidden" value="">
                        <tr class="consulting_section">
                            <td colspan=8>Consulting <a class="consultingAddLink"><i class="fa fa-plus-circle fa-lg pull-right"></i></a></td>
                        </tr>

                        <tr class="consulting_subsection">
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="consultingContractDetails" id="consultingContractDetails" name="consultingContractDetails" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="consultingAmountDecided" id="consultingAmountDecided" name="consultingAmountDecided" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="consultingPaymentReceived" id="consultingPaymentReceived" name="consultingPaymentReceived" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="consultingDepositDate" id="consultingDepositDate" name="consultingDepositDate" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="consultingChequeDetails" id="consultingChequeDetails" name="consultingChequeDetails" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="consultingBankAcccountNo" id="consultingBankAcccountNo" name="consultingBankAcccountNo" value="" placeholder="" />
                                </div>
                            </td>
                            <td class="">
                                <div class="table-edit-control">
                                    <label></label>
                                    <input type="text" class="consultingBalance" id="consultingBalance" name="consultingBalance" value="" placeholder="" readonly="" />
                                </div>
                            </td>
                            <td class="">
                                <a><i class="fa fa-comment-o" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function () {



        $('.customerAccount').click(function () {
            //            alert($(this).attr('data-id'));
            $('.consulting_subsection').empty();
            //            $serviceType=("#service_type").val();
            $.ajax({
                url: "customerServiceDetails.php",
                dataType: "json",
                type: 'GET',
                data: {
                    customerID: $(this).attr('data-id'),
                    serviceType: '',
                },
                success: function (data) {
                    $('.consulting_subsection').empty();
                    $('.cb_subsection').empty();
                    //                    alert(data[0][0]);
                    $('.service-type').val(data[0]['service_type']);
                    $('.cert-body').val(data[0]['cert_body']);
                    $('.consulting-firm').val(data[0]['consulting_firm']);
                    $('.consultant-list').val(data[0]['consultant']);
                    $('.service-id').val(data[0]['service_id']);
                    $('.audit-type').val(data[0]['audit_type']);
                    $('.auditor-list').val(data[0]['auditor']);
                    $('.audit-date').val(data[0]['audit_date']);
                    $('.customer_id').val(data[0]['customer_id']);
                    $('.serviceEditDeta').fadeIn("slow");


                    //                    alert(data[1].length);
                    $i = 0;
                    for (var key in data[1]) {
                        //                        alert("1");
                        var table = document.getElementById("service_details");
                        var row = table.insertRow($i + 2);
                        $i = $i + 1;
                        var cell1 = row.insertCell(0);
                        var cell2 = row.insertCell(1);
                        var cell3 = row.insertCell(2);
                        var cell4 = row.insertCell(3);
                        var cell5 = row.insertCell(4);
                        var cell6 = row.insertCell(5);
                        var cell7 = row.insertCell(6);
                        var cell8 = row.insertCell(7);

                        $dateField = "";
                        if (data[1][key]['deposit_date'] == "null" || data[1][key]['deposit_date'] == null) {
                            $dateField = "";
                        } else {
                            $dateField = data[1][key]['deposit_date'];
                        }

                        cell1.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="contractDetails" id="contractDetails" value="' + data[1][key]['contract_details'] + '" name="contractDetails" placeholder="" /></div>';
                        cell2.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="amountDecided" id="amountDecided" value="' + data[1][key]['amount_decided'] + '" name="amountDecided" placeholder="" /></div>';
                        cell3.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="paymentReceived" id="paymentReceived" value="' + data[1][key]['payment_received'] + '" name="paymentReceived" placeholder="" /></div>';
                        cell4.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="depositDate" id="depositDate" value="' + $dateField + '" name="depositDate" placeholder="" /></div>';
                        cell5.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="chequeDetails" id="chequeDetails" value="' + data[1][key]['check_details'] + '" name="chequeDetails" placeholder="" /></div>';
                        cell6.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="bankAcccountNo" id="bankAcccountNo" value="' + data[1][key]['bank_account'] + '" name="bankAcccountNo" placeholder="" /></div>';
                        cell7.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="balance" id="balance" name="balance" value="' + data[1][key]['balance'] + '" placeholder="" /></div>';
                        cell8.innerHTML = '<a class="cbComment"><i class="fa fa-comment-o" aria-hidden="true"></i></a><input type="hidden" class="cb-row-id" id="cb-row-id" value="' + data[1][key]['certification_id'] + '" name="cb-row-id" value="">';
                        row.className = "cb_subsection";
                        //                        alert($i);
                    }

                    $j = 0;
                    for (var key in data[2]) {
                        //                        alert(data[1][key]['contract_details']);

                        var table = document.getElementById("service_details");
                        var row = table.insertRow(data[1].length + 4 + $j);
                        //                        alert(data[1].length+"-"+$j+"-"+(data[1].length+4 +$j));
                        $j = $j + 1;
                        var cell1 = row.insertCell(0);
                        var cell2 = row.insertCell(1);
                        var cell3 = row.insertCell(2);
                        var cell4 = row.insertCell(3);
                        var cell5 = row.insertCell(4);
                        var cell6 = row.insertCell(5);
                        var cell7 = row.insertCell(6);
                        var cell8 = row.insertCell(7);

                        $dateField = "";
                        if (data[2][key]['consulting_deposit_date'] == "null" || data[2][key]['consulting_deposit_date'] == null) {
                            $dateField = "";
                        } else {
                            $dateField = data[2][key]['consulting_deposit_date'];
                        }

                        cell1.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="consultingContractDetails" id="consultingContractDetails" name="consultingContractDetails" value="' + data[2][key]['consulting_fees'] + '" placeholder="" /></div>';
                        cell2.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="consultingAmountDecided" id="consultingAmountDecided" name="consultingAmountDecided" value="' + data[2][key]['consulting_amount_decided'] + '" placeholder="" /></div>';
                        cell3.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="consultingPaymentReceived" id="consultingPaymentReceived" name="contractDetails" value="' + data[2][key]['consulting_payment_received'] + '" placeholder="" /></div>';
                        cell4.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="consultingDepositDate" id="consultingDepositDate" name="consultingDepositDate" value="' + data[2][key]['consulting_deposit_date'] + '" placeholder="" /></div>';
                        cell5.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="consultingChequeDetails" id="consultingChequeDetails" name="consultingChequeDetails" value="' + data[2][key]['consulting_chque_details'] + '" placeholder="" /></div>';
                        cell6.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="consultingBankAcccountNo" id="consultingBankAcccountNo" name="consultingBankAcccountNo" value="' + data[2][key]['bank_ac'] + '" placeholder="" /></div>';
                        cell7.innerHTML = '<div class="table-edit-control"><label></label><input type="text" class="consultingBalance" id="consultingBalance" name="consultingBalance" value="' + data[2][key]['consulting_balance'] + '" placeholder="" /></div>';
                        cell8.innerHTML = '<a><i class="fa fa-comment-o" aria-hidden="true"></i></a> <input type="hidden" class="consulting-row-id" id="consulting-row-id" value="' + data[2][key]['consulting_id'] + '" name="consulting-row-id" value="">';
                        row.className = "consulting_subsection";
                        //                         alert($j);
                    }
                },
                error: function (data, status, err) {
                    //                        alert(status);
                }

            });
        });

    });
</script>