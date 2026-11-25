<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once './dbConnect.php';

$allServices = "SELECT * FROM service_type";
$statement = $db->prepare($allServices);
$statement->execute();
$services = $statement->fetchAll();

$allStatus = "SELECT * FROM customer_status";
$statement = $db->prepare($allStatus);
$statement->execute();
$allStatusList = $statement->fetchAll();

$certs = "SELECT * FROM cert_body";
$statement = $db->prepare($certs);
$statement->execute();
$certList = $statement->fetchAll();

//    consulting firms
$consultingFirm = "SELECT * FROM consulting_firm";
$statement = $db->prepare($consultingFirm);
$statement->execute();
$firmList = $statement->fetchAll();

//    consultants
$consultant = "SELECT * from consultant";
$statement = $db->prepare($consultant);
$statement->execute();
$consultantList = $statement->fetchAll();

//    auditors
$auditors = "SELECT * from auditors";
$statement = $db->prepare($auditors);
$statement->execute();
$auditorsList = $statement->fetchAll();

//    audit type
$auditType = "SELECT * from audit_type";
$statement = $db->prepare($auditType);
$statement->execute();
$auditTypeList = $statement->fetchAll();

//    user list
$allUsers = "SELECT * FROM `user`";
$statement = $db->prepare($allUsers);
$statement->execute();
$listOfUsers = $statement->fetchAll();
?>

<script src="js/form-fancy-control.js"></script>
<link rel="stylesheet" href="css/float-label.css">

<div class="container">    

    <div class="modal fade" id="addGeneralNote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Notes:</h4>
                </div>
                <form action="addComments.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">                        
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" style="padding-top:10px;">
                                <label for="">Add Notes:_</label>
                                <textarea type="text" name="generalNoteAdd" rows="8" data-column="cert_comment" class="form-control form-group generalNoteAdd" id="generalNoteAdd" placeholder="Add Notes:_"></textarea>
                            </div>
                        </div>
                        <input type="hidden" class="cbId" id="cbId" name="cbId" value="">
                        <input type="hidden" class="cbCustomer" id="cbCustomer" name="cbCustomer" value="">
                    </div>                    
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showResponsibility" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">My Responsibility</h4>
                </div>
                <form action="addTask.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">                        
                        <div class="row" >
                            <div class="col-sm-12 form-group ">
                                <label class="responsibilitytext"></label>
                            </div>
                        </div>                                                
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addNewTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create tasks</h4>
                </div>
                <form action="addTask.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">                        
                        <div class="row" >
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Select User</label>
                                <select class="user-type" id="user-type" name="user-type" required>
                                    <option value="">Select User</option>
                                    <?php foreach ($listOfUsers as $users) { ?>
                                        <option value="<?php echo $users['user_id'] ?>"><?php echo $users['user_name'] ?></option>
                                    <?php } ?>
                                </select>                                    
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Task</label>
                                <input type="text" class="task-name" id="task-name" name="task-name" placeholder="Task" required>
                            </div>
                        </div>                        
                        <!--                        <div class="row">
                                                    <div class="col-sm-12 form-group float-label-control" style="padding-top:10px;">
                                                        <label for="">Comment</label>
                                                        <textarea type="text" name="taskComment" rows="3" class="form-control form-group taskComment" id="taskComment" placeholder="Comment" required></textarea>
                                                    </div>
                                                </div>-->
                        <div class="newtaskrow"></div>
                        <input type="hidden" class="taskList" name="taskList" value="">
                        <div class="row" style="">
                            <div class="col-sm-12">
                                <a class="moreTask" style="" title="Add Multiple Task"><i class="fa fa-plus"></i></a>
                                <button type="submit" class="btn btn-info createTaskMgmt" style="float: right">Save</button>
                            </div> 
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCertificetionComment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Certification Comment</h4>
                </div>
                <form action="addComments.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">                        
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" style="padding-top:10px;">
                                <label for="">Certification Comment</label>
                                <textarea type="text" name="certificationComment" rows="4" data-column="cert_comment" class="form-control form-group certificationComment" id="certificationComment" placeholder="Add Certification Comment"></textarea>
                            </div>
                        </div>
                        <input type="hidden" class="cbId" id="cbId" name="cbId" value="">
                        <input type="hidden" class="cbCustomer" id="cbCustomer" name="cbCustomer" value="">
                    </div>                    
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addConsultingComment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Consulting Comment</h4>
                </div>
                <form action="addComments.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">                        
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" style="padding-top:10px;">
                                <label for="">Consulting Comment</label>
                                <textarea type="text" name="consultComment" rows="5" class="form-control form-group consultComment" id="consultComment" placeholder="Add Consulting Comment"></textarea>
                            </div>
                        </div>
                        <input type="hidden" class="consultingId" id="consultingId" name="consultingId" value="">
                        <input type="hidden" class="consultingCustomer" id="consultingCustomer" name="consultingCustomer" value="">
                    </div>                    
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">x</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Invoice</h4>
            </div>
            <form action="invoice.php" method="post" target="_blank" enctype="multipart/form-data">
                <input type="hidden" name="invoiceCustomerId" class="invoiceCustomerId" id="invoiceCustomerId" value="" />
                <input type="hidden" name="invoiceFirmId" class="invoiceFirmId" id="invoiceFirmId" value="" />
                <input type="hidden" class="invoiceId" name="invoiceId" value="">
                <input type="hidden" class="preInvDate" name="preInvDate" value="">
                <input type="hidden" name="invoiceServiceId" class="invoiceServiceId" id="invoiceServiceId" value="" />
                <div class="modal-body">
                    <div id="serviceInvoiceBadge" style="display:none; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 10px 14px; border-radius: 6px; margin-bottom: 15px; font-size: 13px;"><i class="fa fa-tag" style="color: #3b82f6; margin-right: 6px;"></i><strong>Service-wise Invoice: </strong><span id="serviceInvoiceName" style="font-weight: 700; color: #1d4ed8;"></span></div>
                    <div class="row">
                        <div class="col-sm-6 form-group float-label-control">
                            <label>Consulting Firm</label>
                            <select class="invoiceconsultingFirmName" id="invoiceconsultingFirmName" name="invoiceconsultingFirmName">
                                <option value="">Select Consulting Firm</option>
                                <?php foreach ($firmList as $firm) { ?>
                                    <option 
                                        value="<?php echo $firm['consulting_firm_id'] ?>" 
                                        data-gst="<?php echo $firm['is_gst_applicable'] ?>" 
                                        data-gst-percent="<?php echo $firm['gst_percent'] ?>">
                                        <?php echo $firm['firm_name'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-6 form-group float-label-control">
                            <label>Invoice Date</label>
                            <input type="text" class="invoiceDt" id="invoiceDt" name="invoiceDt" placeholder="Invoice Date" required>
                            <div id="invDateError" style="display: none">
                                <small><span id="invDateErrorMsg" style="color: #c9513c; font-style: italic;"></span></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12" style="margin-bottom:5px; background: #428bca;">
                            <strong style="color: white">Add Services</strong>
                            <a class="addServiceAmtRow pull-right" id="addServiceAmtRow" style="cursor: pointer;">
                                <i class="glyphicon glyphicon-plus-sign" style="margin-top: 9px; color: white;" title="Add new Service"></i>
                            </a>
                        </div>
                    </div>

                    <div class='serviceAmtTd'>
                        <div class="row" style="padding-top:20px;">
                            <div class="col-sm-8 form-group float-label-control">
                                <label for="">Service</label>
                                <input type="text" name="invoiceService[]" class="form-control form-group serviceInvoiceString" placeholder="Service">
                            </div>
                            <div class="col-sm-4 form-group float-label-control" style="padding-top:10px;">
                                <label for="">Amount</label>
                                <input type="number" name="invoiceServiceAmt[]" class="form-control form-group invoiceServiceAmt" placeholder="Amount" step="0.01" inputmode="decimal">
                            </div>
                        </div>
                    </div>

                    <!-- Tax Type Radio Buttons -->
                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <label>Tax Type</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="taxType" value="CGST_SGST" checked> CGST / SGST
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="taxType" value="IGST"> IGST
                            </label>
                        </div>
                    </div>

                    <!-- Net Price and GST -->
                    <div class="row">
                        <div class="col-sm-6 form-group float-label-control">
                            <label>Net Price</label>
                            <input type="number" class="netPrice" id="netPrice" placeholder="Net Price" name="netPrice" readonly step="0.01" inputmode="decimal">
                        </div>
                        <div class="col-sm-6 form-group float-label-control">
                            <label>GST/TAX</label>
                            <div class="row" id="taxBreakupRow" style="margin-top:10px; display:none;">
    <!-- CGST -->
    <div class="col-sm-6 text-center">
        <input type="text" id="cgstField" class="form-control text-center" >
        <div><small><strong>CGST</strong></small></div>
    </div>

    <!-- SGST -->
    <div class="col-sm-6 text-center">
        <input type="text" id="sgstField" class="form-control text-center" >
        <div><small><strong>SGST</strong></small></div>
    </div>

    <!-- IGST (Full Width) -->
    <div class="col-sm-12 text-center" id="igstWrapper" style="margin-top:10px; display:none;">
        <input type="text" id="igstField" class="form-control text-center" >
        <div><small><strong>IGST</strong></small></div>
    </div>
    <input type="hidden" name="serviceTax" id="serviceTax" value="0">
</div>


                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" target="_blank" class="btn btn-xs btn-info invoiceGenerate">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <div class="modal fade" id="invoiceEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Invoice</h4>
      </div>
      <form action="invoice.php" method="post" target="_blank" enctype="multipart/form-data">
        <input type="hidden" name="invoiceEditCustomerId" class="invoiceEditCustomerId" id="invoiceEditCustomerId" value="" />
        <input type="hidden" name="invoiceEditFirmId" class="invoiceEditFirmId" id="invoiceEditFirmId" value="" />
        <input type="hidden" name="invoiceEditServiceId" class="invoiceEditServiceId" id="invoiceEditServiceId" value="" />
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-6 form-group float-label-control">
              <label>Client Name</label>
              <input type="text" class="invoiceClient" id="invoiceClient" name="invoiceClient" placeholder="Client Name" required>
            </div>
            <div class="col-sm-6 form-group float-label-control">
              <label>Invoice Date</label>
              <input type="text" class="invoiceDtEdit" id="invoiceDtEdit" name="invoiceDtEdit" placeholder="Invoice Date" required>
              <div id="invEditDateError" style="display: none">
                <small><span id="invEditDateErrorMsg" style="color: #c9513c; font-style: italic;"></span></small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-12" style="margin-bottom:5px; background: #428bca;">
              <strong style="color: white">Add Services</strong>
              <a class="addServiceAmtRowedit pull-right" id='addServiceAmtRowedit' style="cursor: pointer;">
                <i style="margin-top: 9px; color: white;" class="glyphicon glyphicon-plus-sign" title="Add new Service"></i>
              </a>
            </div>
          </div>

          <div class="row" style="padding-top:20px;">
            <div class="col-sm-8 form-group float-label-control">
              <label>Service</label>
              <input type="text" name="invoiceServiceEdit[]" class="form-control form-group invoiceServiceEdit" id="invoiceServiceEdit" placeholder="Service">
            </div>
            <div class="col-sm-4 form-group float-label-control" style="padding-top:10px;">
              <label>Amount</label>
              <input type="number" name="invoiceServiceAmtEdit[]" class="form-control form-group invoiceServiceAmtEdit" id="invoiceServiceAmtEdit" placeholder="Amount" step="0.01" inputmode="decimal">
            </div>
          </div>

          <div class='serviceAmtTdedit'></div>

          <div class="row">
            <div class="col-sm-6 form-group float-label-control">
              <label>Net Price</label>
              <input type="number" class="netPriceEdit" id="netPriceEdit" name="netPriceEdit" placeholder="Net Price" readonly step="0.01" inputmode="decimal">
            </div>
            <div class="col-sm-6 form-group float-label-control">
<input type="hidden" class="serviceTaxEdit" id="serviceTaxEdit" name="serviceTaxEdit">
            </div>
          </div>

          <!-- Radio Buttons for GST Type -->
          <div class="row">
            <div class="col-sm-12 form-group">
              <label>Tax Type</label><br>
              <label class="radio-inline">
                <input type="radio" name="taxTypeEdit" value="CGST_SGST" checked> CGST / SGST
              </label>
              <label class="radio-inline">
                <input type="radio" name="taxTypeEdit" value="IGST"> IGST
              </label>
            </div>
          </div>

          <!-- GST Breakdown -->
          <div class="row" id="taxBreakupEditRow" style="margin-top:10px; display:none;">
            <!-- CGST -->
            <div class="col-sm-6 text-center">
              <input type="text" id="cgstFieldEdit" class="form-control text-center" readonly>
              <div><small><strong>CGST</strong></small></div>
            </div>

            <!-- SGST -->
            <div class="col-sm-6 text-center">
              <input type="text" id="sgstFieldEdit" class="form-control text-center" readonly>
              <div><small><strong>SGST</strong></small></div>
            </div>

            <!-- IGST -->
            <div class="col-sm-12 text-center" id="igstWrapperEdit" style="margin-top:10px; display:none;">
              <input type="text" id="igstFieldEdit" class="form-control text-center" readonly>
              <div><small><strong>IGST</strong></small></div>
            </div>
          </div>
        </div>

        <!-- Hidden Fields -->
        <input type="hidden" class="invoiceEditId" name="invoiceEditId" value="">
        <input type="hidden" class="invoiceEditNo" name="invoiceEditNo" value="">
        <input type="hidden" class="invoiceEditDate" name="invoiceEditDate" value="">
        <input type="hidden" class="invoiceNxtEditDate" name="invoiceNxtEditDate" value="">

        <div class="modal-footer">
          <button type="submit" target="_blank" class="btn btn-xs btn-info invoiceEdit">Generate</button>
        </div>
      </form>
    </div>
  </div>
</div>


    <div class="modal fade" id="quotation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Quotation</h4>
                </div>
                <form action="quotation.php" method="post" target="_blank" enctype="multipart/form-data">
                    <input type="hidden" class="quoteCustomerId" name="quoteCustomerId" id="quoteCustomerId" value="" />
                    <!--<input type="hidden" class="consultingFirmId" name="consultingFirmId" id="consultingFirmId" value="" />-->
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Consulting Firm</label>
                                <select class="consultingFirmId" id="consultingFirmId" name="consultingFirmId" value="">
                                    <option value="">Select Consulting Firm</option>
                                    <?php foreach ($firmList as $firm) { ?>
                                        <option value="<?php echo $firm['consulting_firm_id'] ?>"><?php echo $firm['firm_name'] ?></option>
                                    <?php } ?>
                                </select>
                            <!--<input type="text" class="quoteconsultingFirmName" id="consultingFirmName" name="consultingFirmName" placeholder="Consulting Firm" required>-->
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <label>Quotation Date</label>
                                        <input type="" class="quotationDt" id="quotationDt" name="quotationDt" placeholder="Quotation Date" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <div id="quotationDtError" style="display: none">
                                            <small><span id="quotationDtErrorMsg" style="color: #c9513c; font-style: italic;" ></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Service</label>
                                <select class="serviceString" id="serviceString" name="serviceString" value="">

                                </select>
                                <!--<input type="text" class="serviceString" id="serviceString" name="serviceString" placeholder="Service" required>-->
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Audit body charges NABCB* accredited</label>
                                <input type="number" class="auditBodyChange" id="auditBodyChange" placeholder="Audit body charges NABCB* accredited" name="auditBodyChange">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Consultancy Charges + Travelling, Lodging etc</label>
                                <input type="number" class="consultingCharges" id="consultingCharges" placeholder="Consultancy Charges + Travelling, Lodging etc" name="consultingCharges">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Initial amount with contract</label>
                                <input type="number" class="initialAmount" id="initialAmount" placeholder="Initial amount with contract" name="initialAmount">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Registration amount of certification</label>
                                <input type="number" class="registrationAmt" id="registrationAmt" placeholder="Registration amount of certification" name="registrationAmt">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Payment of certification body rest amount</label>
                                <input type="number" class="paymentOfCertification" id="paymentOfCertification" placeholder="Payment of certification body rest amount" name="paymentOfCertification">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Remaining consultancy amount</label>
                                <input type="number" class="remainingConsultancyAmt" id="remainingConsultancyAmt" placeholder="Remaining consultancy amount" name="remainingConsultancyAmt">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Other than NABCB (only enter if NABCB is not applicable)</label>
                                <input type="text" class="otherAccredited" id="otherAccredited" name="otherAccredited" placeholder="Other than NABCB (only enter if NABCB is not applicable)" name="remainingConsultancyAmt">
                            </div>
                        </div>
                        <div class="row">
                            <div class=" col-sm-12 " style="margin-bottom:5px; background: #428bca;"><strong style="color: white">Add Additional Conditions</strong><a class="addConditionRow pull-right" id='addConditionRow' style="cursor: pointer;"><i  style="margin-top: 9px; color: white;" class="glyphicon glyphicon-plus-sign" title="Add new Condition"></i></a> </div>
                        </div>
                        <div class='conditionDescTd'>
                            <div class="row">
                                <div class="col-sm-12 form-group float-label-control" style="padding-top:10px;">
                                    <label for="">Condition Details</label>
                                    <textarea name="conditionDescription" rows="2" class="form-control form-group conditionDescription" id="conditionDescription" placeholder="Condition Description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="preQotDate" name="preQotDate" value="">
                    <input type="hidden" class="quot-id" id="quot-id" name="quot-id" value="">
                    <input type="hidden" class="conditionsHidden" id="conditionsHidden" name="conditionsHidden" value="">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info generateQuotation">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editQuotation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Quotation</h4>
                </div>
                <form action="quotation.php" method="post" target="_blank" enctype="multipart/form-data">
                    <input type="hidden" class="editquoteCustomerId" name="editquoteCustomerId" id="editquoteCustomerId" value="" />
                    <input type="hidden" class="editconsultingFirmId" name="editconsultingFirmId" id="editconsultingFirmId" value="" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Client Name</label>
                                <input type="text" class="quotationClient" id="quotationClient" name="quotationClient" placeholder="Client Name" required>
                            <!--<input type="text" class="quoteconsultingFirmName" id="consultingFirmName" name="consultingFirmName" placeholder="Consulting Firm" required>-->
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <label>Quotation Date</label>
                                        <input type="" class="editquotationDt" id="editquotationDt" name="editquotationDt" placeholder="Quotation Date" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <div id="editquotationDtError" style="display: none">
                                            <small><span id="editquotationDtErrorMsg" style="color: #c9513c; font-style: italic;" ></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Service</label>
                                <select class="editserviceString" id="editserviceString" name="editserviceString" value="">

                                </select>
                                <!--<input type="text" class="serviceString" id="serviceString" name="serviceString" placeholder="Service" required>-->
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Audit body charges NABCB* accredited</label>
                                <input type="number" class="editauditBodyChange" id="editauditBodyChange" placeholder="Audit body charges NABCB* accredited" name="editauditBodyChange">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Consultancy Charges + Travelling, Lodging etc</label>
                                <input type="number" class="editconsultingCharges" id="editconsultingCharges" placeholder="Consultancy Charges + Travelling, Lodging etc" name="editconsultingCharges">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Initial amount with contract</label>
                                <input type="number" class="editinitialAmount" id="editinitialAmount" placeholder="Initial amount with contract" name="editinitialAmount">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Registration amount of certification</label>
                                <input type="number" class="editregistrationAmt" id="editregistrationAmt" placeholder="Registration amount of certification" name="editregistrationAmt">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Payment of certification body rest amount</label>
                                <input type="number" class="editpaymentOfCertification" id="editpaymentOfCertification" placeholder="Payment of certification body rest amount" name="editpaymentOfCertification">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Remaining consultancy amount</label>
                                <input type="number" class="editremainingConsultancyAmt" id="editremainingConsultancyAmt" placeholder="Remaining consultancy amount" name="editremainingConsultancyAmt">
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Other than NABCB (only enter if NABCB is not applicable)</label>
                                <input type="text" class="editotherAccredited" id="editotherAccredited" name="editotherAccredited" placeholder="Other than NABCB (only enter if NABCB is not applicable)" name="editremainingConsultancyAmt">
                            </div>
                        </div>
                        <div class="row">
                            <div class=" col-sm-12 " style="margin-bottom:5px; background: #428bca;"><strong style="color: white">Add Additional Conditions</strong><a class="editaddConditionRow pull-right" id='addConditionRow' style="cursor: pointer;"><i  style="margin-top: 9px; color: white;" class="glyphicon glyphicon-plus-sign" title="Add new Condition"></i></a> </div>
                        </div>
                        <div class='editconditionDescTd'>
                            <div class="row">
                                <div class="col-sm-12 form-group float-label-control" style="padding-top:10px;">
                                    <label for="">Condition Details</label>
                                    <textarea name="editconditionDescription" rows="2" class="form-control form-group editconditionDescription" id="editconditionDescription" placeholder="Condition Description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="editpreQotDate" name="editpreQotDate" value="">
                    <input type="hidden" class="editquot-id" id="editquot-id" name="editquot-id" value="">
                    <input type="hidden" class="editconditionsHidden" id="editconditionsHidden" name="editconditionsHidden" value="">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info editgenerateQuotation">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customerAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Client Information</h4>
                </div>
                <form class="customeraddform" id="customeraddform" action="addCustomer.php" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Client Name</label>
                                <input type="text" class="companyName" id="companyName" name="companyName" placeholder="Client Name">
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Client Referred </label>
                                <input type="text" class="customerReferred" id="customerReferred" name="customerReferred" placeholder="Client Referred" disabled="true">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6"><div id="companyNameError" style="display: none">
                                    <small><span id="companyNameErrorMsg" style="color: #c9513c; font-style: italic;" ></span></small>
                                </div></div>
                            <div class="col-sm-6">
                                <div id="input-type" class="row">
                                    <label class="radio-inline">
                                        <input name="client_of" class="clientreferance" id="justdial" value="Just Dial" type="radio" />Just Dial
                                    </label>
                                    <label class="radio-inline">
                                        <input name="client_of" class="clientreferance" id="telecall" value="Tele Call" type="radio" />Tele Call
                                    </label>
                                    <label class="radio-inline">
                                        <input name="client_of" class="clientreferance" id="other" value="Other" type="radio" />Other
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 25px;">
                            <div class="col-sm-6 form-group float-label-control">
                                <!--                            <div class="row">
                                                                <div class="col-sm-12 form-group float-label-control">-->
                                <label for="">Address</label>
                                <textarea name="companyAddress" rows="4" class="form-control form-group companyAddress" id="companyAddress" placeholder="Enter address"></textarea>
                                <!--                                </div>
                                                            </div>-->
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <!--                            <div class="row">
                                                                <div class="col-sm-12 form-group float-label-control">-->
                                <label>Location</label>
                                <textarea name="location" rows="4" class="form-control form-group location" id="location" placeholder="Enter location"></textarea>
                                <!--                                </div>
                                                            </div>-->
                            </div>
                        </div>                        
                        <div class="row">
                            <div class="col-sm-4 form-group float-label-control">
                                <div class="row float-label-control">
                                    <label>Email Id</label>
                                    <input type="email" class="customerEmail" id="customerEmail" name="customerEmail" placeholder="Client Email" >                                        
                                </div>
                                <div class="row float-label-control">
                                    <div id="contactEmailError" style="display: none">
                                        <small><span id="contactEmailErrorMsg" style="color: #c9513c; font-style: italic;" ></span></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 form-group float-label-control">
                                <label>Contact Person</label>
                                <input type="text" class="contactPerson" id="contactPerson" name="contactPerson" placeholder="Contact Person" >
                            </div>
                            <div class="col-sm-3 form-group float-label-control">
                                <div class="row float-label-control">
                                    <label>Contact No</label>
                                    <input type="text" class="contactNo" id="contactNo" name="contactNo" placeholder="Contact No" >
                                </div>
                                <div class="row float-label-control">
                                    <div id="customerContactError" style="display: none">
                                        <small><span id="customerContactErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1 form-group float-label-control">
                                <a class="newContactNo" style="" title="Add Multiple Contact"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="row newCustomerContact">

                        </div>
                        <div class="row">
    <div class="col-sm-6 form-group float-label-control">
        <label>Client GST Number</label>
        <input type="text" class="form-control clientGstNumber" id="clientGstNumber" name="clientGstNumber" placeholder="Enter GST Number" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$" title="Enter valid 15-digit GSTIN">
    </div>
</div>

                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <div class="row float-label-control">
                                    <label>Priority</label>
                                    <select class="clientPriority" id="clientPriority" name="clientPriority" value="">                                    
                                        <option value="">Select Priority</option>
                                        <option value="High">High</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Low">Low</option>
                                    </select>
                                    </select>
                                </div>
                            </div>                        
                            <div class="col-sm-6 form-group float-label-control">
                                <!--                            <div class="row">
                                                                <div class="col-sm-12 form-group float-label-control">-->
                                <label>Comment</label>
                                <textarea name="comments" rows="4" class="form-control form-group comments" id="comments" placeholder="Enter Comment"></textarea>
                                <!--                                </div>
                                                            </div>-->
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="editCustomerId" id="editCustomerId" name="editCustomerId" value="">
                    <input type="hidden" class="customerContactHidden" name="customerContactHidden" value="">
                    <input type="hidden" class="customerContactEmlHidden" name="customerContactEmlHidden" value="">
                    <input type="hidden" class="customerContactPrsnHidden" name="customerContactPrsnHidden" value="">
                    <input type="hidden" class="custrefferedhidden" name="custrefferedhidden" value="">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info addCustomerToService">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customerEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Client Information</h4>
                </div>
                <form action="addCustomer.php" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <label>Client Name</label>
                                        <input type="text" class="editCompanyName" id="editCompanyName" name="editCompanyName" placeholder="Client Name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <div id="editCompanyNameError" style="display: none">
                                            <small><span id="editCompanyNameErrorMsg" style="color: #c9513c; font-style: italic;" ></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <label>Email Id</label>
                                        <input type="email" class="editCustomerEmail" id="editCustomerEmail" name="editCustomerEmail" placeholder="Client Email" >                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <div id="editCustomerEmailError" style="display: none">
                                            <small><span id="editCustomerEmailErrorMsg" style="color: #c9513c; font-style: italic;" ></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <label for="">Address</label>
                                        <textarea name="editCompanyAddress" rows="4" class="form-control form-group editCompanyAddress" id="editCompanyAddress" placeholder="Enter address"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <label>Client Referred </label>
                                        <input type="text" class="editCustomerReferred" id="editCustomerReferred" name="editCustomerReferred" placeholder="Client Referred" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <label>Contact Person</label>
                                        <input type="text" class="editContactPerson" id="editContactPerson" name="editContactPerson" placeholder="Contact Person" >
                                    </div>
                                </div>
                                <!--                                <div class="row">
                                                                    <div class="col-sm-12 form-group float-label-control">
                                                                        <label>Contact No</label>
                                                                        <input type="text" class="editContactNo" id="editContactNo" name="editContactNo" placeholder="Contact No" >                                        
                                                                    </div>                                    
                                                                </div> -->
                                <div class="row">
                                    <div class="col-sm-10 form-group float-label-control">
                                        <label>Contact No</label>
                                        <input type="text" class="editcontactNo" id="editcontactNo" name="editcontactNo" placeholder="Contact No" >                                        
                                    </div>  
                                    <div class="col-sm-2 form-group float-label-control">
                                        <a class="eidtNewContactNo" style="float: right;" title="New Contact Add"><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                                <div class="row editNewCustomerContact">

                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group float-label-control">
                                        <div id="editContactNoError" style="display: none">
                                            <small><span id="editContactNoErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Comment</label>
                                <textarea name="editComments" rows="3" class="form-control form-group editComments" id="editComments" placeholder="Enter Comment"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="editContactNoHidden" name="editContactNoHidden" value="">
                    <input type="hidden" class="editCustomerId" id="editCustomerId" name="editCustomerId" value="">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info editCustomerToService">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addService" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h5 class="modal-title" id="myModalLabel"> Service Details</h5>
                </div>
                <form action="addService.php" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Service Stage</label>
                                <select name="serviceType" class="form-control serviceType" id="serviceType">
                                    <option value="">Select Service Stage</option>
                                    <?php foreach ($services as $service) { ?>
                                        <option value="<?php echo $service['service'] ?>">
                                            <?php echo $service['service'] ?>
                                        </option>
                                    <?php } ?>
                                </select>                                
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Cert Body</label>
                                <select name="certBody" class="form-control certBody" id="certBody">
                                    <option value="">Select Cert Body</option>
                                    <?php foreach ($certList as $cret) { ?>
                                        <option value="<?php echo $cret['cert'] ?>">
                                            <?php echo $cret['cert'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Consulting firm </label>
                                <select name="consultingFirm" class="form-control consultingFirm" id="consultingFirm">
                                    <option value="">Select Consulting Firm</option>
                                    <?php foreach ($firmList as $firm) { ?>
                                        <option value="<?php echo $firm['firm_name'] ?>">
                                            <?php echo $firm['firm_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Lead Consultant</label>
                                <select name="consultant" class="form-control consultant" id="consultant">
                                    <option value="">Select Lead Consultant</option>
                                    <?php foreach ($consultantList as $consultant) { ?>
                                        <option value="<?php echo $consultant['consultant_name'] ?>">
                                            <?php echo $consultant['consultant_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 form-group float-label-control">
                                <label>Audit Type</label>
                                <select name="auditType" class="form-control auditType" id="auditType">
                                    <option value="">Select Audit Type</option>
                                    <?php foreach ($auditTypeList as $auditType) { ?>
                                        <option value="<?php echo $auditType['audit_types'] ?>">
                                            <?php echo $auditType['audit_types'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-4 form-group float-label-control">
                                <label>Lead Auditor</label>
                                <select name="auditor" class="form-control auditor" id="auditor">
                                    <option value="">Select Lead Auditor</option>
                                    <?php foreach ($auditorsList as $auditors) { ?>
                                        <option value="<?php echo $auditors['auditor_name'] ?>">
                                            <?php echo $auditors['auditor_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-4 form-group float-label-control">
                                <label>Audit Date</label>
                                <input type="text" class="auditDate" id="auditDate" name="auditDate" placeholder="Audit Date" required>
                            </div>
                        </div>
                        <input type="hidden" class="customer_id" id="customer_id" name="customer_id" value="<?php echo $_GET['customerid'] ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default serviceAdd">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<div class="modal fade" id="firmAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Consulting Firm Information</h4>
            </div>
            <form action="addConsultingFirm.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">

                    <!-- Firm Name & Email -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Consulting Firm</label>
                            <input type="text" class="form-control" id="consultingFirmName" name="consultingFirmName" placeholder="Consulting Firm Name">
                            <div id="consultingFirmNameError" style="display:none">
                                <small><span id="consultingFirmNameErrorMsg" style="color:#c9513c;font-style:italic;"></span></small>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Email Id</label>
                            <input type="email" class="form-control" id="consultingFirmEmail" name="consultingFirmEmail" placeholder="Consulting Firm Email">
                            <div id="consultingFirmEmailError" style="display:none">
                                <small><span id="consultingFirmEmailErrorMsg" style="color:#c9513c;font-style:italic;"></span></small>
                            </div>
                        </div>
                    </div>

                    <!-- Logo Upload -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Logo</label>
                            <input type="file" name="firmLogo" id="firmLogo" class="form-control" accept="image/gif,image/jpeg,image/png" onchange="readURL(this, 'firmImg')">
                            <img id="firmImg" alt="" src="" class="form-control">
                        </div>

                        <!-- Signature Upload -->
                        <div class="col-sm-6 form-group">
                            <label>Signature</label>
                            <input type="file" name="firmSignature" id="firmSignature" class="form-control" accept="image/gif,image/jpeg,image/png" onchange="readURLSignature(this, 'firmSignatureImg')">
                            <img id="firmSignatureImg" alt="" src="" class="form-control">
                        </div>
                    </div>

                    <!-- Address & Contact -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Firm Address</label>
                            <textarea name="firmAddress" rows="4" class="form-control" id="firmAddress" placeholder="Firm Address"></textarea>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Firm Contact No.</label>
                            <input type="text" class="form-control" id="firmContact" name="firmContact" placeholder="Firm Contact No.">
                            <div id="firmContactError" style="display:none">
                                <small><span id="firmContactErrorMsg" style="color:#c9513c;font-style:italic;"></span></small>
                            </div>
                        </div>
                    </div>

                    <!-- PAN -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Pan No.</label>
                            <input type="text" class="form-control" id="panNo" name="panNo" placeholder="Pan No.">
                        </div>
                    </div>

                    <!-- Bank Details -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" id="bankName" name="bankName" placeholder="Bank Name">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Bank Account No.</label>
                            <input type="number" class="form-control" id="bankAcNo" name="bankAcNo" placeholder="Bank Account No.">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Bank Address</label>
                            <textarea name="bankAddress" rows="2" class="form-control" id="bankAddress" placeholder="Bank Address"></textarea>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>IFSC Code</label>
                            <input type="text" class="form-control" id="ifscCode" name="ifscCode" placeholder="IFSC code">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>MICR No.</label>
                            <input type="text" class="form-control" id="micrNo" name="micrNo" placeholder="MICR No.">
                        </div>

                        <div class="row">
                        <div class="newAbbrRow col-sm-6 form-group float-label-control">
                        <label>Bank Abbreviation</label>
                        <input type="text" class="form-control bankAbbreviation" name="bankAbbreviation" placeholder="Bank Abbreviation">
                        </div>
                        <div class="col-sm-6 form-group float-label-control">
                        <a class="newConsultAbbr btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        </div>
                        <div class="row newConsultAbbrvation"></div>
                        </div>

                    <!-- Firm Other Info -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Firm Abbreviation</label>
                            <input type="text" class="form-control" id="firmAbbreviation" name="firmAbbreviation" placeholder="Firm Abbreviation">
                            <div id="firmAbbreviationError" style="display:none">
                                <small><span id="firmAbbreviationErrorMsg" style="color:#c9513c;font-style:italic;"></span></small>
                            </div>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label>Firm Invoice No.</label>
                            <input type="number" class="form-control" id="firmInvoice" name="firmInvoice" placeholder="Firm Invoice No.">
                            <div id="firmInvoiceError" style="display:none">
                                <small><span id="firmInvoiceErrorMsg" style="color:#c9513c;font-style:italic;"></span></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Firm Quotation No.</label>
                            <input type="number" class="form-control" id="firmQuotation" name="firmQuotation" placeholder="Firm Quotation No.">
                            <div id="firmQuotationError" style="display:none">
                                <small><span id="firmQuotationErrorMsg" style="color:#c9513c;font-style:italic;"></span></small>
                            </div>
                        </div>
                    </div>

                    <!-- GST Fields -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Is GST Applicable?</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="isGstApplicable" value="Yes" onclick="document.getElementById('gstPercentRow').style.display='block';"> Yes
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="isGstApplicable" value="No" onclick="document.getElementById('gstPercentRow').style.display='none';"> No
                            </label>
                        </div>
                    </div>

                    <div class="row" id="gstPercentRow" style="display:none;">
                        <div class="col-sm-6 form-group">
                            <label>GST % Applicable</label>
                            <input type="number" class="form-control" id="gstPercent" name="gstPercent" placeholder="Enter GST %" min="0" step="0.01">
                        </div>
                    </div>

                    <!-- Service Description -->
                    <div class="row">
                        <div class="col-sm-12" style="margin-bottom:5px; background:#428bca;">
                            <strong style="color:white;">Add Firm Services</strong>
                            <a class="addnewrow pull-right" id="addNewFirmDesc" style="cursor:pointer;">
                                <i class="glyphicon glyphicon-plus-sign" title="Add new firm description" style="margin-top:9px; color:white;"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Initial service input -->
<div class="row">
    <div class="col-sm-12 form-group">
        <label>Service Description</label>
        <input type="text" class="form-control serviceDescription" id="serviceDescription" name="serviceDescription" placeholder="Service Description">
    </div>
</div>

<!-- New inputs will be added here -->
<div class="serviceDescTd"></div>

                </div>

                <input type="hidden" id="firmServicesHidden" name="firmServicesHidden" value="">
                <input type="hidden" id="firmBankAbbrHidden" name="firmBankAbbrHidden" value="">

                <div class="modal-footer">
                    <button type="submit" class="btn btn-info addConsultingFirm">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editFirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Consulting Firm Information</h4>
            </div>
            <form action="editConsultingFirm.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">

                    <!-- Row 1: Firm Name & Email -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Consulting Firm Name</label>
                            <input type="text" class="form-control" id="editconsultingFirmName" name="editconsultingFirmName" placeholder="Consulting Firm Name">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Email ID</label>
                            <input type="email" class="form-control" id="editconsultingFirmEmail" name="editconsultingFirmEmail" placeholder="Consulting Firm Email">
                        </div>
                    </div>

                    <!-- Row 2: Logo & Signature -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Logo</label>
                            <input type="file" name="editFirmLogo" id="editFirmLogo" class="form-control" accept="image/*" onchange="readURL(this, 'editFirmImg')"/>
                            <img id="editFirmImg" src="" alt="Logo Preview" class="img-thumbnail mt-2" style="max-height:150px;">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Signature</label>
                            <input type="file" name="editFirmSignature" id="editFirmSignature" class="form-control" accept="image/*" onchange="readURLSignature(this, 'editSignatureImg')" />
                            <img id="editSignatureImg" src="" alt="Signature Preview" class="img-thumbnail mt-2" style="max-height:150px;">
                        </div>
                    </div>

                    <!-- Row 3: Address & Contact -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Firm Address</label>
                            <textarea name="editfirmAddress" rows="4" class="form-control" id="editfirmAddress" placeholder="Firm Address"></textarea>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Firm Contact No.</label>
                            <input type="text" class="form-control" id="editfirmContact" name="editfirmContact" placeholder="Contact No.">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Pan No.</label>
                            <input type="text" class="form-control" id="editpanNo" name="editpanNo" placeholder="Pan No.">
                        </div>
                    </div>

                    <!-- Row 4: Bank Details -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" id="editbankName" name="editbankName" placeholder="Bank Name">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Account No.</label>
                            <input type="number" class="form-control" id="editbankAcNo" name="editbankAcNo" placeholder="Bank Account No.">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Bank Address</label>
                            <textarea name="editbankAddress" rows="2" class="form-control" id="editbankAddress" placeholder="Bank Address"></textarea>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>IFSC Code</label>
                            <input type="text" class="form-control" id="editifscCode" name="editifscCode" placeholder="IFSC Code">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>MICR No.</label>
                            <input type="text" class="form-control" id="editmicrNo" name="editmicrNo" placeholder="MICR No.">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Bank Abbreviation</label>
                            <a class="btn btn-sm btn-primary newConsultAbbrAtEdit" style="margin-bottom: 5px;"><i class="fa fa-plus"></i> Add Abbreviation</a>
                            <div class="editConsultAbbrvation"></div>
                        </div>
                    </div>

                    <!-- Row 5: GST Addition -->
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Is GST Applicable?</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="editisGstApplicable" value="Yes" onclick="document.getElementById('editgstPercentRow').style.display='block';"> Yes
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="editisGstApplicable" value="No" onclick="document.getElementById('editgstPercentRow').style.display='none';"> No
                            </label>
                        </div>
                    </div>

                    <div class="row" id="editgstPercentRow" style="display:none;">
                        <div class="col-sm-6 form-group">
                            <label>GST % Applicable</label>
                            <input type="number" class="form-control" id="editgstPercent" name="editgstPercent" placeholder="Enter GST %" min="0" step="0.01">
                        </div>
                    </div>

                    <!-- Row 6: Firm Codes -->
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Firm Abbreviation</label>
                            <input type="text" class="form-control" id="editFirmAbbreviation" name="editFirmAbbreviation" placeholder="Firm Abbreviation">
                        </div>
                        <div class="col-sm-4 form-group">
                            <label>Invoice No</label>
                            <input type="number" class="form-control" id="editFirmInvoice" name="editFirmInvoice" placeholder="Firm Invoice No.">
                        </div>
                        <div class="col-sm-4 form-group">
                            <label>Quotation No</label>
                            <input type="number" class="form-control" id="editFirmQuotation" name="editFirmQuotation" placeholder="Firm Quotation No.">
                        </div>
                    </div>

                    <!-- Row 7: Services -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="bg-primary text-white p-2 mb-2"><strong>Add Firm Services</strong>
                            <a class="addnewrowtoedit pull-right" style="cursor:pointer;"><i class="glyphicon glyphicon-plus-sign"></i></a></div>
                            <div class="editserviceDescTd"></div>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" id="firmServicesId" name="firmServicesId">
                    <input type="hidden" id="editfirmServicesHidden" name="editfirmServicesHidden">
                    <input type="hidden" id="editfirmServicesIdHidden" name="editfirmServicesIdHidden">
                    <input type="hidden" id="editAbbrIdHidden" name="editAbbrIdHidden">
                    <input type="hidden" id="editbankAbbreviationHidden" name="editbankAbbreviationHidden">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success editConsultingFirm">Update Firm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <div class="modal fade" id="bankAcountAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Bank Account Information</h4>
                </div>
                <form action="addBankDetails.php" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control">
                                <label>Account Holder</label>
                                <input type="text" class="accountHolderName" id="accountHolderName" name="accountHolderName" placeholder="Account Holder" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Bank Name</label>
                                <input type="text" class="bankName" id="bankName" name="bankName" placeholder="Bank Name" required>
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Account No.</label>
                                <input type="text" class="accountNumber" id="accountNumber" name="accountNumber" placeholder="Account Number" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>IFSC Code</label>
                                <input type="text" class="bankifscCode" id="bankifscCode" name="bankifscCode" placeholder="IFSC Code" required>
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <label>MICR No.</label>
                                <input type="text" class="bankMicrNo" id="bankMicrNo" name="bankMicrNo" placeholder="Micr Number" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="marketingCustomerEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Client Information</h4>
                </div>
                <form action="updateCustomerMktg.php" method="post">
                    <input type="hidden" name="mktgcustomreId" id="mktgcustomreId" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Client Name</label>
                                <input type="text" class="mktgcompanyName" id="mktgcompanyName" name="mktgcompanyName" placeholder="Client Name" required>
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Contact No</label>
                                <input type="text" class="mktgcontactNo" id="mktgcontactNo" name="mktgcontactNo" placeholder="Contact No" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label>Audit Type</label>
                                <input type="text" class="mktgauditType" id="mktgauditType" name="mktgauditType" placeholder="Audit Type">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control">
                                <label for="">Address</label>
                                <textarea name="mktgcompanyAddress" rows="4" class="form-control form-group mktgcompanyAddress" id="mktgcompanyAddress" placeholder="Address"></textarea>
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <label for="">Comments</label>
                                <textarea name="mktgcompanyComment" rows="4" class="form-control form-group mktgcompanyComment" id="mktgcompanyComment" placeholder="Comments"></textarea>
                            </div>
                        </div>
                    </div>
                    <!--                    <div class="row">
                                            <div class="col-sm-12 form-group float-label-control">
                                                <label for="">Comment</label>
                                                <textarea name="mktgcompanyuserComment" rows="2" class="form-control form-group mktgcompanyuserComment" id="mktgcompanyuserComment" placeholder="Address"></textarea>
                                            </div>
                                        </div>-->

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info ">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeUserPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Change Password</h4>
                </div>
                <form action="changePassword.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">                        
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" style="padding-top:10px;">
                                <label for="">Old Password</label>
                                <input type="text" name="oldPassword" class="form-control form-group oldPassword" id="oldPassword" placeholder="Old Password">
                                <div id="oldPasswordError" style="display: none">
                                    <small><span id="oldPasswordErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" style="padding-top:10px;">
                                <label for="">New Password</label>
                                <input type="text" name="newPassword" class="form-control form-group newPassword" id="newPassword" placeholder="New Password">
                                <div id="newPasswordError" style="display: none">
                                    <small><span id="newPasswordErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" style="padding-top:10px;">
                                <label for="">Old Password</label>
                                <input type="text" name="confirmPassword" class="form-control form-group confirmPassword" id="confirmPassword" placeholder="Confirm Password">
                                <div id="confirmPasswordError" style="display: none">
                                    <small><span id="confirmPasswordErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="user_id" class="user_id" id="user_id" value="<?php echo $_SESSION['id']; ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info changePassword">Change</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addNewCert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Certification Body</h4>
                </div>
                <form action="addNewServices.php" method="get" enctype="multipart/form-data">
                    <div class="modal-body">                        
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control" >
                                <label for="">New cert</label>
                                <input type="text" name="newCert" class="form-control form-group newCert" id="newCert" placeholder="Add New Cert">
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <div class="row">
                                    <div class="col-sm-10 form-group float-label-control">
                                        <label>New abbreviation</label>
                                        <input type="text" name="newAbbreviation" class="form-control newAbbreviation" id="newAbbreviation" placeholder="New abbreviation" >
                                    </div>
                                    <div class="col-sm-2 form-group float-label-control">
                                        <a class=" newAbbr" style="float: right;"><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                                <div class="row newAbbrAdd">

                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="newAbbreviationHidden" id="newAbbreviationHidden" name="newAbbreviationHidden" value="">
                        <!--<input type="hidden" class="consultingCustomer" id="consultingCustomer" name="consultingCustomer" value="">-->
                    </div> 
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info createCert">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Certification Body</h4>
                </div>
                <form action="addNewServices.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">                        
                        <div class="row">
                            <div class="col-sm-6 form-group float-label-control" >
                                <label for="">New cert</label>
                                <input type="text" name="editnewCert" class="form-control form-group editnewCert" id="editnewCert" placeholder="Add New Cert">
                            </div>
                            <div class="col-sm-6 form-group float-label-control">
                                <div class="row">                                    
                                    <div class="col-sm-12 form-group float-label-control">
                                        <a class=" newEditAbbr" ><i class="fa fa-plus"></i> Bank Abbreviation</a>
                                    </div>
                                </div>
                                <div class="row editAbbrAdd">

                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="cert-id" id="cert-id" name="cert-id" value="">
                        <input type="hidden" class="editAbbreviationHidden" id="editAbbreviationHidden" name="editAbbreviationHidden" value="">
                        <input type="hidden" class="editAbbreviationIDHidden" id="editAbbreviationIDHidden" name="editAbbreviationIDHidden" value="">
                    </div> 
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info createCertEdit">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-static fade" id="processing-modal" role="dialog" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <img src="images/saveData.png" class="icon" style="width: 100px"/>
                        <small><p>Service Saved successfully...</p></small>
                        <h4><a class="displayService">Click here...</a></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-static fade" id="validation-modal" role="dialog" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <img src="images/empty_input.png" class="icon" style="width: 100px"/>
                        <small><p>Service Type is empty...</p></small>
                        <h4><a class="displayService" data-dismiss="modal">Click here...</a></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-static-view fade" id="verification-modal" role="dialog" aria-hidden="true" >
        <?php
        if (isset($_GET['userid']) && !empty($_GET['userid'])) {
            $userEmail = "SELECT email_id FROM user WHERE user_id = '" . $_GET['userid'] . "'";
            $statement = $db->prepare($userEmail);
            $statement->execute();
            $email = $statement->fetch();
        }
        ?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <img src="images/successReset.png" class="icon" style="width: 75px"/>
                        <small><p>Password reset successfully...</p></small>
                        <p><small>User Email :</small><strong><?php if (isset($email['email_id'])) echo $email['email_id'] ?></strong></p>
                        <p><small>Password : </small><strong><?php if (isset($_GET['passwd'])) echo $_GET['passwd'] ?></strong></p>
                        <h4><a class="displayService" data-dismiss="modal">Click here...</a></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paswordReset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
                </div>
                <form action="changePassword.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body"> 
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" >
                                <strong>Name : </strong><span class="userNameToReset"></span><br>
                                <strong>User Email : </strong><span class="userEmailToReset"></span>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-sm-8 form-group float-label-control" >
                                <label for="">New Password</label>
                                <input type="password" name="resetNewPassword" class="form-control form-group resetNewPassword" id="resetNewPassword" placeholder="Reset Password">
                            </div>    
                            <div class="col-sm-2 form-group float-label-control" >
                                <a class="passwordShow" title="Show password"><i class="fa fa-eye"></i></a><a class="passwordHide" title="Hide password" style="display: none"><i class="fa fa-eye-slash"></i></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" >
                                <div id="resetNewPasswordError" style="display: none">
                                    <small><span id="resetNewPasswordErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 form-group float-label-control" >
                                <label for="">Confirm Password</label>
                                <input type="password" name="confirmResetNewPassword" class="form-control form-group confirmResetNewPassword" id="confirmResetNewPassword" placeholder="Reset Password">
                            </div>    
                            <!--                            <div class="col-sm-2 form-group float-label-control" >
                                                            <a class="passwordShow" title="Show password"><i class="fa fa-eye"></i></a><a class="passwordHide" title="Hide password" style="display: none"><i class="fa fa-eye-slash"></i></a>
                                                        </div>-->
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group float-label-control" >
                                <div id="confirmResetNewPasswordError" style="display: none">
                                    <small><span id="confirmResetNewPasswordErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="userID" id="userID" name="userID" value="">
    <!--                        <input type="hidden" class="editAbbreviationHidden" id="editAbbreviationHidden" name="editAbbreviationHidden" value="">
                        <input type="hidden" class="editAbbreviationIDHidden" id="editAbbreviationIDHidden" name="editAbbreviationIDHidden" value="">-->
                    </div> 
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-xs btn-info resetUserPassword">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade month-working" id="month-working" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Monthly Working</h4>
                </div>
                <div class="modal-body">
                    <div class="row monthlyworkrow">
                        <div class="col-sm-12 main_content table-responsive">
                            <table class="table table-hover table-sortable table-striped" id="tab_month_work_list" width="100%">
                                <thead>
                                    <tr>
                                        <th class="">
                                            <small><a>Client</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Ser. Type</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Cert Body</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Auditor</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Audit Date</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Consultant</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 01</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 02</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 03</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 04</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 05</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 06</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 07</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 08</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 09</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 10</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 11</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 12</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 13</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 14</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 15</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 16</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 17</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 18</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 19</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 20</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 21</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 22</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 23</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 24</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 25</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 26</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 27</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 28</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 29</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 30</a></small>
                                        </th>
                                        <th class="">
                                            <small><a>Day 31</a></small>
                                        </th>
                                </thead>
                                <tbody class="month-work-table-data">

                                </tbody>    
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-static { 
        position: fixed;
        top: 50% !important; 
        left: 50% !important; 
        margin-top: -100px;  
        margin-left: -100px; 
        overflow: visible !important;
    }
    .modal-static,
    .modal-static .modal-dialog,
    .modal-static .modal-content {
        width: 200px; 
        height: 200px; 
    }
    .modal-static .modal-dialog,
    .modal-static .modal-content {
        padding: 0 !important; 
        margin: 0 !important;
    }
    .modal-static .modal-content .icon {
    }

    .modal-static-view { 
        position: fixed;
        top: 50% !important; 
        left: 50% !important; 
        margin-top: -100px;  
        margin-left: -100px; 
        overflow: visible !important;
    }
    .modal-static-view,
    .modal-static-view .modal-dialog,
    .modal-static-view .modal-content {
        width: 250px; 
        height: 250px; 
    }
    .modal-static-view .modal-dialog,
    .modal-static-view .modal-content {
        padding: 0 !important; 
        margin: 0 !important;
    }
    .modal-static-view .modal-content .icon {
    }
</style>

<script type="text/javascript">
    $(function () {

        $('#auditDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy',
            autoclose: true
        });
        $("#auditDate").keydown(function (e) {
            e.preventDefault();
        });

        $('#invoiceDt').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy',
            autoclose: true
        });
        $("#invoiceDt").keydown(function (e) {
            e.preventDefault();
        });

        $('#quotationDt').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy',
//                    autoclose: true
        });
        $("#quotationDt").keydown(function (e) {
            e.preventDefault();
        });

        $('#invoiceDtEdit').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy',
//                    autoclose: true
        });
        $("#invoiceDtEdit").keydown(function (e) {
            e.preventDefault();
        });

        $('#editquotationDt').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy',
//                    autoclose: true
        });
        $("#editquotationDt").keydown(function (e) {
            e.preventDefault();
        });
    });

    function validateEmail(email) {
        var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        return re.test(email);
    }

    function  validatePassword(pass) {
//        alert(pass);
        return pass.match(/^(?=.*?[a-zA-Z])(?=.*?[0-9])(?=.*?[@#%$])[a-zA-Z0-9@#%$]{8,16}$/);
//        return pass.match(/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{8,})$/);
    }

    var err = true;
    var mainErr = false;

    function validatePasswordReset() {
        $isError = false;
        if ($('#resetNewPassword').val() == "") {

            $('#resetNewPasswordErrorMsg').text("Password is required");
            $('#resetNewPasswordError').show();
            $('#resetNewPassword').addClass('inputHighlight');
            $isError = true;
            //            alert("hi");
        } else if (!validatePassword($('#resetNewPassword').val())) {
            $('#resetNewPasswordErrorMsg').text("Password must contain at least 8 characters, contain at least 1 number, contain at least 1 lowercase character (a-z),contain at least 1 uppercase character (A-Z), contains at least 1 special character");
            $('#resetNewPasswordError').show();
            $('#resetNewPassword').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#resetNewPasswordError').hide();
            $('#resetNewPassword').removeClass('inputHighlight');
        }

        if ($('#confirmResetNewPassword').val() == "") {
            $('#confirmResetNewPasswordErrorMsg').text("Confirm your password");
            $('#confirmResetNewPasswordError').show();
            $('#confirmResetNewPassword').addClass('inputHighlight');
            $isError = true;
//            alert("hi");
        } else if ($('#resetNewPassword').val() != $('#confirmResetNewPassword').val()) {
            $('#confirmResetNewPasswordErrorMsg').text("Password not match");
            $('#confirmResetNewPasswordError').show();
            $('#confirmResetNewPassword').addClass('inputHighlight');
            $isError = true;

        } else {
            $('#confirmResetNewPasswordError').hide();
            $('#confirmResetNewPassword').removeClass('inputHighlight');

        }
        return $isError;
    }

    function validatrEditCustomer() {
        $isError = false;

        if ($('#editCompanyName').val() == "")
        {
            $('#editCompanyNameErrorMsg').text("The company or client name can not be empty");
            $('#editCompanyNameError').show();
            $('#editCompanyName').addClass('inputHighlight');
            $isError = true;
            //    alert("email not valid"+$error);
        }
//        else if ($('#editCompanyName').val() != "") {
//            $.ajax({
//                type: "POST",
//                url: "checkCustomer.php",
//                data: {customerName: $('#editCompanyName').val()},
//                async: false,
//                success: function (data) {
////                    alert(data);
//                    if (data == 1) {
//                        $('#editCompanyNameErrorMsg').text("The company or client name exist in the system");
//                        $('#editCompanyNameError').show();
//                        $('#editCompanyName').addClass('inputHighlight');
//                        $isError = true;
//                        mainErr = true;
//                    } else {
//                        $('#editCompanyNameError').hide();
//                        $('#editCompanyName').removeClass('inputHighlight');
//                        mainErr = false;
//                    }
//                }
//            });
//        }
        else {
            $('#editCompanyNameError').hide();
            $('#editCompanyName').removeClass('inputHighlight');
        }

        if ($('#editCustomerEmail').val() != "" && !validateEmail($('#editCustomerEmail').val()))
        {
            $('#editCustomerEmailErrorMsg').text("The email address is not valid");
            $('#editCustomerEmailError').show();
            $('#editCustomerEmail').addClass('inputHighlight');
            $isError = true;
            //    alert("email not valid"+$error);
        } else {
            $('#editCustomerEmailError').hide();
            $('#editCustomerEmail').removeClass('inputHighlight');
        }

//        var format = /^\+?([0-9, ]{12,})$/;
        var format = /^\+?([0-9]{8,})$/;
        if ($('#editContactNo').val() == "") {
//            alert('in');
            $('#editContactNoErrorMsg ').text("Customer contact can not be empty");
            $('#editContactNoError').show();
            $('#editContactNo').addClass('inputHighlight');
            $isError = true;
        } else if ($('#editContactNo').val() != "" && !$('#editContactNo').val().match(format)) {
            $('#editContactNoErrorMsg ').text("Please enter valid contact number");
            $('#editContactNoError').show();
            $('#editContactNo').addClass('inputHighlight');
            $isError = true;
        } else if ($('#editContactNo').val().length != 10) {
            $('#editContactNoErrorMsg ').text("Please enter contact number of 10 digits");
            $('#editContactNoError').show();
            $('#editContactNo').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#editContactNoError').hide();
            $('#editContactNo').removeClass('inputHighlight');
//            $error = true;
        }
        return $isError;
    }

    function validateCustomer() {
        $isError = false;

        if ($('#companyName').val() == "")
        {
            $('#companyNameErrorMsg').text("The company or client name can not be empty");
            $('#companyNameError').show();
            $('#companyName').addClass('inputHighlight');
            $isError = true;
            //    alert("email not valid"+$error);
        }
//        else if ($('#companyName').val() != "" && $('.editCustomerId').val() == "") {
//            $.ajax({
//                type: "POST",
//                url: "checkCustomer.php",
//                data: {customerName: $('#companyName').val()},
//                async: false,
//                success: function (data) {
////                    alert(data);
//                    if (data == 1) {
//                        $('#companyNameErrorMsg').text("The company or client name exist in the system");
//                        $('#companyNameError').show();
//                        $('#companyName').addClass('inputHighlight');
//                        $isError = true;
//                        mainErr = true;
//                    } else {
//                        $('#companyNameError').hide();
//                        $('#companyName').removeClass('inputHighlight');
//                        mainErr = false;
//                    }
//                }
//            });
//        }
        else {
            $('#companyNameError').hide();
            $('#companyName').removeClass('inputHighlight');
        }
        if ($('#customerEmail').val() != "" && !validateEmail($('#customerEmail').val()))
        {
            $('#contactEmailErrorMsg').text("The email address is not valid");
            $('#contactEmailError').show();
            $('#customerEmail').addClass('inputHighlight');
            $isError = true;
            //    alert("email not valid"+$error);
        } else if ($('#customerEmail').val() != "" && $('.editCustomerId').val() == "") {
            $.ajax({
                type: "POST",
                url: "checkCustomer.php",
                data: {customerEmail: $('#customerEmail').val()},
                async: false,
                success: function (data) {
//                    alert(data);
                    if (data == 1) {
                        $('#contactEmailErrorMsg').text("Email is already exist in the system");
                        $('#contactEmailError').show();
                        $('#customerEmail').addClass('inputHighlight');
                        mainErr = true;
                        $isError = true;
                    } else {
                        $('#contactEmailError').hide();
                        $('#customerEmail').removeClass('inputHighlight');
                        mainErr = false;
                    }
                }
            });
        } else {
            $('#contactEmailError').hide();
            $('#customerEmail').removeClass('inputHighlight');
        }
        var format = /^\+?([0-9]{8,})$/;
        if ($('#contactNo').val() == "") {
//            alert('in');
            $('#customerContactErrorMsg ').text("Customer contact can not be empty");
            $('#customerContactError').show();
            $('#contactNo').addClass('inputHighlight');
            $isError = true;
        } else if ($('#contactNo').val() != "" && !$('#contactNo').val().match(format)) {
            $('#customerContactErrorMsg ').text("Please enter valid contact number");
            $('#customerContactError').show();
            $('#contactNo').addClass('inputHighlight');
            $isError = true;
        } else if ($('#contactNo').val().length != 10) {
            $('#customerContactErrorMsg ').text("Please enter contact number of 10 digits");
            $('#customerContactError').show();
            $('#contactNo').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#customerContactError').hide();
            $('#contactNo').removeClass('inputHighlight');
//            $error = true;
        }
        return $isError;
    }

    function validateEditFirm() {
        $isError = false;
        if ($('.editconsultingFirmName').val() == "") {
//            alert('in');
            $('#editconsultingFirmNameErrorMsg ').text("Consulting firm name can not be empty");
            $('#editconsultingFirmNameError').show();
            $('.editconsultingFirmName').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#editconsultingFirmNameError').hide();
            $('.editconsultingFirmName').removeClass('inputHighlight');
//            $error = true;
        }
        var format = /^\+?([0-9]{8,})$/;
        if ($('.editfirmContact').val() == "") {
//            alert('in');
            $('#editfirmContactErrorMsg ').text("Consulting firm contact can not be empty");
            $('#editfirmContactError').show();
            $('.editfirmContact').addClass('inputHighlight');
            $isError = true;
        } else if ($('.editfirmContact').val() != "" && !$('.editfirmContact').val().match(format)) {
            $('#editfirmContactErrorMsg ').text("Please enter valid contact number");
            $('#editfirmContactError').show();
            $('.editfirmContact').addClass('inputHighlight');
            $isError = true;
        } else if ($('.editfirmContact').val().length != 10) {
            $('#editfirmContactErrorMsg ').text("Please enter contact number of 10 digits");
            $('#editfirmContactError').show();
            $('.editfirmContact').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#editfirmContactError').hide();
            $('.editfirmContact').removeClass('inputHighlight');
//            $error = true; 
        }
        if ($('.editconsultingFirmEmail').val() != "" && !validateEmail($('.editconsultingFirmEmail').val()))
        {
            $('#editconsultingFirmEmailErrorMsg').text("The email address is not valid");
            $('#editconsultingFirmEmailError').show();
            $('.editconsultingFirmEmail').addClass('inputHighlight');
            $isError = true;
            //    alert("email not valid"+$error);
        } else {
            $('#editconsultingFirmEmailError').hide();
            $('.editconsultingFirmEmail').removeClass('inputHighlight');
        }
        if ($('.editFirmAbbreviation').val() == "") {
//            alert('in');
            $('#editFirmAbbreviationErrorMsg ').text("Consulting firm abbreviation can not be empty");
            $('#editFirmAbbreviationError').show();
            $('.editFirmAbbreviation').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#editFirmAbbreviationError').hide();
            $('.editFirmAbbreviation').removeClass('inputHighlight');
//            $error = true;
        }
        if ($('.editFirmInvoice').val() == "") {
//            alert('in');
            $('#editFirmInvoiceErrorMsg ').text("Consulting firm invoice can not be empty");
            $('#editFirmInvoiceError').show();
            $('.editFirmInvoice').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#editFirmInvoiceError').hide();
            $('.editFirmInvoice').removeClass('inputHighlight');
//            $error = true;
        }
        if ($('.editFirmQuotation').val() == "") {
//            alert('in');
            $('#editFirmQuotationErrorMsg ').text("Consulting firm invoice can not be empty");
            $('#editFirmQuotationError').show();
            $('.editFirmQuotation').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#editFirmQuotationError').hide();
            $('.editFirmQuotation').removeClass('inputHighlight');
//            $error = true;
        }
        return $isError;
    }

    function validateFirm() {
        $isError = false;
        if ($('.consultingFirmName').val() == "") {
//            alert('in');
            $('#consultingFirmNameErrorMsg ').text("Consulting firm name can not be empty");
            $('#consultingFirmNameError').show();
            $('.consultingFirmName').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#consultingFirmNameError').hide();
            $('.consultingFirmName').removeClass('inputHighlight');
//            $error = true;
        }
        var format = /^\+?([0-9]{8,})$/;
        if ($('.firmContact').val() == "") {
//            alert('in');
            $('#firmContactErrorMsg ').text("Consulting firm contact can not be empty");
            $('#firmContactError').show();
            $('.firmContact').addClass('inputHighlight');
            $isError = true;
        } else if ($('.firmContact').val() != "" && !$('.firmContact').val().match(format)) {
            $('#firmContactErrorMsg ').text("Please enter valid contact number");
            $('#firmContactError').show();
            $('.firmContact').addClass('inputHighlight');
            $isError = true;
        } else if ($('.firmContact').val().length != 10) {
            $('#firmContactErrorMsg ').text("Please enter contact number of 10 digit");
            $('#firmContactError').show();
            $('.firmContact').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#firmContactError').hide();
            $('.firmContact').removeClass('inputHighlight');
//            $error = true;
        }
        if ($('.consultingFirmEmail').val() != "" && !validateEmail($('.consultingFirmEmail').val()))
        {
            $('#consultingFirmEmailErrorMsg').text("The email address is not valid");
            $('#consultingFirmEmailError').show();
            $('.consultingFirmEmail').addClass('inputHighlight');
            $isError = true;
            //    alert("email not valid"+$error);
        } else {
            $('#consultingFirmEmailError').hide();
            $('.consultingFirmEmail').removeClass('inputHighlight');
        }
        if ($('.firmAbbreviation').val() == "") {
//            alert('in');
            $('#firmAbbreviationErrorMsg ').text("Consulting firm abbreviation can not be empty");
            $('#firmAbbreviationError').show();
            $('.firmAbbreviation').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#firmAbbreviationError').hide();
            $('.firmAbbreviation').removeClass('inputHighlight');
//            $error = true;
        }
        if ($('.firmInvoice').val() == "") {
//            alert('in');
            $('#firmInvoiceErrorMsg ').text("Consulting firm invoice can not be empty");
            $('#firmInvoiceError').show();
            $('.firmInvoice').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#firmInvoiceError').hide();
            $('.firmInvoice').removeClass('inputHighlight');
//            $error = true;
        }
        if ($('.firmQuotation').val() == "") {
//            alert('in');
            $('#firmQuotationErrorMsg ').text("Consulting firm quotation can not be empty");
            $('#firmQuotationError').show();
            $('.firmQuotation').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#firmQuotationError').hide();
            $('.firmQuotation').removeClass('inputHighlight');
//            $error = true;
        }
        return $isError;
    }

    function validateChangePassword()
    {
        $isError = false;

        if ($('#oldPassword').val() == "") {
//            alert('in');
            $('#oldPasswordErrorMsg ').text("Old Password can not be empty");
            $('#oldPasswordError').show();
            $('#oldPassword').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#oldPasswordError').hide();
            $('#oldPassword').removeClass('inputHighlight');
        }
        if ($('#newPassword').val() == "") {
            $('#newPasswordErrorMsg').text("New password is required");
            $('#newPasswordError').show();
            $('#newPassword').addClass('inputHighlight');
            $isError = true;
//            alert("hi");
        } else if (!validatePassword($('#newPassword').val())) {
            $('#newPasswordErrorMsg').text("Password must contain at least 8 characters,contain at least 1 number,\n\
             contain at least 1 character (a-z A-Z), contains at least 1 special character");
            $('#newPasswordError').show();
            $('#newPassword').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#newPasswordError').hide();
            $('#newPassword').removeClass('inputHighlight');
        }
        if ($('#confirmPassword').val() == "") {
            $('#confirmPasswordErrorMsg').text("Confirm your password");
            $('#confirmPasswordError').show();
            $('#confirmPassword').addClass('inputHighlight');
            $isError = true;
//            alert("hi");
        } else if ($('#newPassword').val() != $('#confirmPassword').val()) {
            $('#confirmPasswordErrorMsg').text("Password not match");
            $('#confirmPasswordError').show();
            $('#confirmPassword').addClass('inputHighlight');
            $isError = true;

        } else {
            $('#confirmPasswordError').hide();
            $('#confirmPassword').removeClass('inputHighlight');

        }
        return $isError;
    }

    function validateInvDate() {
        $isError = false;
        if ($('#invoiceDt').val() == "") {
            $('#invDateErrorMsg').text("Invoice date can not be empty");
            $('#invDateError').show();
            $('#invoiceDt').addClass('inputHighlight');
            $isError = true;
        } else if ($('.preInvDate').val() != "" && moment($('#invoiceDt').val(), "DD/MM/YYYY") < (moment($('.preInvDate').val(), "DD/MM/YYYY"))) {
            $('#invDateErrorMsg').text("Date should be greater than prevoius invoice");
            $('#invDateError').show();
            $('#invoiceDt').addClass('inputHighlight');
            $isError = true;
        } else {
            $('.invDateError').hide();
            $('.invoiceDt').removeClass('inputHighlight');
        }
        return $isError;
    }

function calculateGST() {
    var total = 0;

    // Sum all service amounts
    $(".invoiceServiceAmt").each(function () {
        var val = parseFloat($(this).val());
        if (!isNaN(val)) {
            total += val;
        }
    });

    // Get selected firm GST info
    var selectedFirm = $('#invoiceconsultingFirmName option:selected');
    var isGstApplicable = selectedFirm.attr('data-gst');
    var gstPercent = parseFloat(selectedFirm.attr('data-gst-percent')) || 0;

    // Set net price
    $('.netPrice').val(total.toFixed(2));

    // Reset all GST display fields
    $('#cgstField, #sgstField, #igstField').val('');
    $('#taxBreakupRow').hide();
    $('#igstWrapper').hide();

    if (isGstApplicable === "Yes" && gstPercent > 0) {
        var gstAmount = (gstPercent / 100) * total;
        $('#serviceTax').val(gstAmount.toFixed(2));

        var selectedTaxType = $('input[name="taxType"]:checked').val();
        $('#taxBreakupRow').show();

        if (selectedTaxType === "CGST_SGST") {
            var halfGST = (gstAmount / 2).toFixed(2);
            $('#cgstField').val(halfGST);
            $('#sgstField').val(halfGST);
        } else if (selectedTaxType === "IGST") {
            $('#igstWrapper').show();
            $('#igstField').val(gstAmount.toFixed(2));
        }
    } else {
        $('#serviceTax').val(0);
    }
}

$(document).on('input', '.invoiceServiceAmt', calculateGST);
$('#invoiceconsultingFirmName').on('change', calculateGST);
$('input[name="taxType"]').on('change', calculateGST);

    function calculateEditGST() {
    let total = 0;

    // Sum all service amounts
    $(".invoiceServiceAmtEdit").each(function () {
        const val = parseFloat($(this).val());
        if (!isNaN(val)) {
            total += val;
        }
    });

    const $firmInput = $('#invoiceEditFirmId');
    const isGstApplicable = $firmInput.attr('data-gst');
    const gstPercent = parseFloat($firmInput.attr('data-gst-percent')) || 0;
    //alert($firmInput.attr('data-gst'));

    $('.netPriceEdit').val(total.toFixed(2));

    // Reset all fields
    $('#cgstFieldEdit, #sgstFieldEdit, #igstFieldEdit').val('');
    $('#taxBreakupEditRow').hide();
    $('#igstWrapperEdit').hide();

    if (isGstApplicable === "Yes" && gstPercent > 0) {
        const gstAmount = (gstPercent / 100) * total;
        $('#serviceTaxEdit').val(gstAmount.toFixed(2));

        const selectedTaxType = $('input[name="taxTypeEdit"]:checked').val();
        $('#taxBreakupEditRow').show();

        if (selectedTaxType === "CGST_SGST") {
            const halfGST = (gstAmount / 2).toFixed(2);
            $('#cgstFieldEdit').val(halfGST);
            $('#sgstFieldEdit').val(halfGST);
        } else if (selectedTaxType === "IGST") {
            $('#igstWrapperEdit').show();
            $('#igstFieldEdit').val(gstAmount.toFixed(2));
        }
    } else {
        $('#serviceTaxEdit').val(0);
    }
}

$('body').on('input', '.invoiceServiceAmtEdit', function () {
    calculateEditGST();
});

// Recalculate on tax type change
$('body').on('change', 'input[name="taxTypeEdit"]', function () {
    calculateEditGST();
});

    function validateInvEditDate() {
        $isError = false;
        var selectedDate = moment($('#invoiceDtEdit').val(), "DD.MM.YYYY", true);

        if ($('#invoiceDtEdit').val() == "") {
            $('#invEditDateErrorMsg').text("Invoice date can not be empty");
            $('#invEditDateError').show();
            $('#invoiceDtEdit').addClass('inputHighlight');
            $isError = true;
        } else if (!selectedDate.isValid()) {
            $('#invEditDateErrorMsg').text("Invalid invoice date");
            $('#invEditDateError').show();
            $('#invoiceDtEdit').addClass('inputHighlight');
            $isError = true;
        } else {
            $('#invEditDateError').hide();
            $('.invoiceDtEdit').removeClass('inputHighlight');
        }
        return $isError;
    }

    function validateQuotDate() {
        $isError = false;
        if ($('#quotationDt').val() == "") {
            $('#quotationDtErrorMsg').text("Quotation date can not be empty");
            $('#quotationDtError').show();
            $('#quotationDt').addClass('inputHighlight');
            $isError = true;
        } else if (moment($('#quotationDt').val(), "DD/MM/YYYY") < (moment($('.preQotDate').val(), "DD/MM/YYYY"))) {
            $('#quotationDtErrorMsg').text("Date should be greator than prevoius quotation");
            $('#quotationDtError').show();
            $('#quotationDt').addClass('inputHighlight');
            $isError = true;
        } else {
            $('.quotationDtError').hide();
            $('.quotationDt').removeClass('inputHighlight');
        }
        return $isError;
    }

    function validateQuotEditDate() {
        $isError = false;
        if ($('#editquotationDt').val() == "") {
            $('#editquotationDtErrorMsg').text("Quotation date can not be empty");
            $('#editquotationDtError').show();
            $('#editquotationDt').addClass('inputHighlight');
            $isError = true;
        } else if (moment($('#editquotationDt').val(), "DD/MM/YYYY") < (moment($('.editpreQotDate').val(), "DD/MM/YYYY"))) {
            $('#editquotationDtErrorMsg').text("Date should be greator than prevoius quotation");
            $('#editquotationDtError').show();
            $('#editquotationDt').addClass('inputHighlight');
            $isError = true;
        } else {
            $('.editquotationDtError').hide();
            $('.editquotationDt').removeClass('inputHighlight');
        }
        return $isError;
    }

    $(document).ready(function () {

        $(document).on("change", ".invoiceServiceAmt", function () {
            var sum = 0;
            $(".invoiceServiceAmt").each(function () {
                sum += +$(this).val();
            });
            $(".netPrice").val(sum);
        });

        $(document).on("change", ".invoiceServiceAmtEdit", function () {
            var sum = 0;
            $(".invoiceServiceAmtEdit").each(function () {
                sum += +$(this).val();
            });
            $(".netPriceEdit").val(sum);
        });

        $(".createTaskMgmt").click(function () {
//            alert("in");
            var tasknameArray = [];
            $(".task-name").each(function () {
                tasknameArray.push($(this).val());
            });
            $('.taskList').val(tasknameArray.join("~"));
//            alert($('.taskList').val()+ " -> " +$('.commentList').val());
        });

        $(".moreTask").click(function () {
//            alert("in");
            $(".newtaskrow").append('<div class="row">\n\
                                        <div class="col-sm-12 form-group float-label-control">\n\
                                        <label>Task</label><input type="text" class="task-name" id="task-name" name="task-name" placeholder="Task" required>\n\
                                        </div>\n\
                                        </div>');
        });

        $(".clientreferance").click(function () {

            $referance = $(this).val();
            if ($referance == 'Other')
            {
                $(".customerReferred").prop('disabled', false);
                $(".customerReferred").val("");

            } else
            {
                $(".customerReferred").val($referance);
                $(".customerReferred").prop('disabled', true);
            }

        });

        $('.addAccountCss').click(function () {
            document.getElementById("customeraddform").reset();
            $('#customerAdd').modal('show');
        });

        $('.quotationClient').autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "autoSearch.php",
                    dataType: "json",
                    type: 'GET',
                    data: {
                        consultingFirm: $('.quotationClient').val(),
                    },
                    success: function (data) {
//                        alert(JSON.stringify(data));
                        response(data);
                    },
                    error: function (data, status, err) {
//                        alert(status);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                //            alert(ui.item.id);
//                $("#consultingFirmId").val(ui.item.id);
                $("#editquoteCustomerId").val(ui.item.id);
                //                    //                    alert(ui.item.id);
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $('.consultingFirmId').change(function () {
            var frmid = $('.consultingFirmId').val();
//            $('.consultingFirmId').val(frmid);
            $.ajax({
                url: "autoSearch.php",
                dataType: "json",
                type: 'GET',
                data: {
                    consultingId: $('.consultingFirmId').val(),
                },
                success: function (data) {
//                    console.log(data);
//                    alert(data);
                    $('.serviceString').empty();
                    $('.serviceString').append('<option value="">Select Service</option>');
                    for (var key in data[0]) {
//                        alert(data[0][key]['service_desc'])
                        $('.serviceString').append('<option value="' + data[0][key]['service_desc'] + '">' + data[0][key]['service_desc'] + '</option>');
                    }
//                    alert(data[1]['invoice_date']);
                    $('.preQotDate').val(moment(data[2]['quotation_date']).format('DD.MM.YYYY'));
//                    alert($('.preInvDate').val());
                },
                error: function (data, status, err) {
                    alert(status);
                }
            });
        });

//        $('.invoiceEdit').click(function (){
//            alert($('.serviceTaxEdit').val());
//        });
        $('.invoiceEdit').click(function (e) {
            if (validateInvEditDate()) {
                e.preventDefault();
                return;
            }
            $('#invoiceEditModal').modal("hide");
        });

        $('.invoiceGenerate').click(function (e) {
//            alert('in');            
//            alert(validateInvDate());
            if (validateInvDate()) {
                e.preventDefault();
                return;
            }
            $('#invoiceModal').modal("hide");
        });

        $('.editgenerateQuotation').click(function () {
            var firmServiceArray = [];
            $(".editconditionDescription").each(function () {
                firmServiceArray.push($(this).val());
                //            alert()
            });
            $('.editconditionsHidden').val(firmServiceArray.join("~"));
            if (validateQuotEditDate()) {
                e.preventDefault();
                return;
            }
            $('#editQuotation').modal('hide');
        });

        $('.generateQuotation').click(function (e) {
            var firmServiceArray = [];
            $(".conditionDescription").each(function () {
                firmServiceArray.push($(this).val());
                //            alert()
            });
            $('.conditionsHidden').val(firmServiceArray.join("~"));
            if (validateQuotDate()) {
                e.preventDefault();
                return;
            }
            $('#quotation').modal('hide');
        });

        $('.addCustomerToService').click(function () {
            var contactNoArray = [];
            $('.contactNo').each(function () {
                contactNoArray.push($(this).val());
            });
            $('.customerContactHidden').val(contactNoArray.join(", "));

            var contactEmailArray = [];
            $('.customerEmail').each(function () {
                contactEmailArray.push($(this).val());
            });
            $('.customerContactEmlHidden').val(contactEmailArray.join(", "));

            var editcontactPrsnArray = [];
            $('.contactPerson').each(function () {
                editcontactPrsnArray.push($(this).val());
            });
            $('.customerContactPrsnHidden').val(editcontactPrsnArray.join(", "));

        });

        $('.editCustomerToService').click(function () {
            var editcontactNoArray = [];
            $('.editcontactNo').each(function () {
                editcontactNoArray.push($(this).val());
            });
            $('.editContactNoHidden').val(editcontactNoArray.join(","));
            alert(editcontactNoArray);
        });

        $('.eidtNewContactNo').click(function () {
            $('.editNewCustomerContact').append('<div class="editContactRowParent"><div class="editContactRow col-sm-10 form-group float-label-control ">\n\
                                                <label>Contact No</label>\n\
                                                <input type="number" class="editcontactNo" id="editcontactNo" value="" name="editcontactNo" placeholder="Contact No" required>\n\
                                                </div><div class="col-sm-2 form-group deleteEditContactParent float-label-control">\n\
                                                <a class="deleteEditContact" style="float: right;"><i class="fa fa-remove"></i></a>\n\
                                                </div></div>')
        });

        $('body').on("click", ".deleteEditContact", function () {
//        alert("Atleast one action item required");
            $(this).parent("div.deleteEditContactParent").parent('div.editContactRowParent').remove();
        });

        $('.newContactNo').click(function () {
            $('.newCustomerContact').append('<div class="row newContactRowParent">\n\
                            <div class="col-sm-4 form-group float-label-control">\n\
                                <label>Email Id</label>\n\
                                <input type="email" class="customerEmail" id="customerEmail" name="customerEmail" placeholder="Client Email" >\n\
                                <div id="contactEmailError" style="display: none">\n\
                                    <small><span id="contactEmailErrorMsg" style="color: #c9513c; font-style: italic;" ></span></small>\n\
                                </div>\n\
                            </div>\n\
                            <div class="col-sm-4 form-group float-label-control">\n\
                                <label>Contact Person</label>\n\
                                <input type="text" class="contactPerson" id="contactPerson" name="contactPerson" placeholder="Contact Person" >\n\
                            </div>\n\
                            <div class="col-sm-3 form-group float-label-control">\n\
                                <label>Contact No</label>\n\
                                <input type="text" class="contactNo" id="contactNo" name="contactNo" placeholder="Contact No" >\n\
                                <div id="customerContactError" style="display: none">\n\
                                    <small><span id="customerContactErrorMsg" style="color: #c9513c; font-style: italic; " ></span></small>\n\
                                </div>\n\
                            </div>\n\
                            <div class="col-sm-1 form-group float-label-control deleteNewContactParent">\n\
                            <a class="deleteContact" style="float: right;"><i class="fa fa-remove"></i></a>\n\
                            </div>\n\
                        </div>');
        });

        $('body').on("click", ".deleteContact", function () {
//        alert("Atleast one action item required");
            $(this).parent("div.deleteNewContactParent").parent('div.newContactRowParent').remove();
        });

        $('.passwordShow').click(function () {
//            $('.newPassword').type = 'text';
            $('.passwordHide').show();
            document.getElementById('resetNewPassword').type = 'text';
            document.getElementById('userPassword').type = 'text';
            document.getElementById('confirmResetNewPassword').type = 'text';
            $('.passwordShow').hide();
//            document.getElementById('newPassword').type = 'text';
        });

        $('.passwordHide').click(function () {
            $('.passwordShow').show();
            document.getElementById('resetNewPassword').type = 'password';
            document.getElementById('userPassword').type = 'password';
            document.getElementById('confirmResetNewPassword').type = 'password';
            $('.passwordHide').hide();
        });

        $('.newConsultAbbr').click(function () {
            $('.newConsultAbbrvation').append('<div class="newAbbrRowParent"><div class="newAbbrRow col-sm-10 form-group float-label-control ">\n\
                                                <label>Bank Abbreviation</label>\n\
                                                <input type="text" class="bankAbbreviation" id="bankAbbreviation" name="bankAbbreviation" placeholder="Bank Abbreviation" required>\n\
                                                </div><div class="col-sm-2 form-group deleteNewConsultAbbrParent float-label-control">\n\
                                                <a class="deleteNewConsultAbbr" style="float: right;"><i class="fa fa-remove"></i></a>\n\
                                                </div></div>');
        });

        $('.newConsultAbbrAtEdit').click(function () {
            $('.editConsultAbbrvation').append('<div class="newAbbrRowParent"><div class="newAbbrRow col-sm-10 form-group float-label-control ">\n\
                                                <label>Bank Abbreviation</label>\n\
                                                <input type="text" class="editbankAbbreviation" id="editbankAbbreviation" name="editbankAbbreviation" placeholder="Bank Abbreviation" value="">\n\
                                                </div><div class="col-sm-2 form-group deleteNewConsultAbbrParent float-label-control">\n\
                                                <a class="deleteNewConsultAbbr" style="float: right;"><i class="fa fa-remove"></i></a>\n\
                                                <input type="hidden" class="editAbbrId" id="editAbbrId" name="editAbbrId" value=""></div></div>');
        });

        $('body').on("click", ".deleteNewConsultAbbr", function () {
//        alert("Atleast one action item required");

            $(this).parent("div.deleteNewConsultAbbrParent").parent('div.newAbbrRowParent').remove();

        });

        $('body').on('click', ".newAbbr", function (e) {
//        $('.newAbbr').click(function (){
            $('.newAbbrAdd').append('<div class="newCBAbbrRowParent">\n\
                                        <div class="col-sm-10 form-group float-label-control">\n\
                                            <label>New abbreviation</label>\n\
                                            <input type="text" name="newAbbreviation" class="form-control newAbbreviation" id="newAbbreviation" placeholder="New abbreviation" >\n\
                                        </div>\n\
                                        <div class="col-sm-2 form-group float-label-control deleteNewCBAbbrParent">\n\
                                            <a class="deleteNewCBAbbr" style="float: right;"><i class="fa fa-remove"></i></a>\n\
                                        </div></div>');
        });

        $('body').on("click", ".deleteNewCBAbbr", function () {
//        alert("Atleast one action item required");

            $(this).parent("div.deleteNewCBAbbrParent").parent('div.newCBAbbrRowParent').remove();

        });

        $('.newEditAbbr').click(function () {
            $('.editAbbrAdd').append('<div class="newCBAbbrRowParent">\n\
                                        <div class="col-sm-10 form-group float-label-control">\n\
                                            <label>New abbreviation</label>\n\
                                            <input type="text" name="editAbbreviation" class="form-control editAbbreviation" id="editAbbreviation" placeholder="New abbreviation" value="">\n\
                                        </div>\n\
                                        <div class="col-sm-2 form-group float-label-control deleteNewCBAbbrParent">\n\
                                            <a class="deleteNewCBAbbr" style="float: right;"><i class="fa fa-remove"></i></a>\n\
                                        <input type="hidden" class="oldCBId" id="oldCBId" name="oldCBId" value=""></div></div>');
        });

        $('.createCert').click(function () {
            var certAbbrArray = [];

            $('.newAbbreviation').each(function () {
                certAbbrArray.push($(this).val());
            });
            $('.newAbbreviationHidden').val(certAbbrArray.join("~"));
        });

        $('.createCertEdit').click(function () {
//            alert($('.cert-id').val());
            var certAbbrIdArray = [];
            $('.oldCBId').each(function () {
                certAbbrIdArray.push($(this).val());
            });
            $('.editAbbreviationIDHidden').val(certAbbrIdArray.join("~"));

            var certeditAbbrArray = [];
            $('.editAbbreviation').each(function () {
                certeditAbbrArray.push($(this).val());
            });
            $('.editAbbreviationHidden').val(certeditAbbrArray.join("~"));
        });

        $('.addCustomerToService').click(function (e) {
            if (validateCustomer()) {
                e.preventDefault();
                return;
            }
        });

        $('.resetUserPassword').click(function (e) {
            if (validatePasswordReset()) {
                e.preventDefault();
                return;
            }
        });

        $('.editCustomerToService').click(function (e) {
            if (validatrEditCustomer()) {
                e.preventDefault();
                return;
            }
        });

        $('.changePassword').click(function (e) {
            if (validateChangePassword()) {
                e.preventDefault();
                return;
            }
        });

        $('.addCBComments').click(function () {
            alert($(this).attr('data-cbId'));
//            $.ajax({
//                url: "addComments.php",
//                dataType: "json",
//                type: 'POST',
//                data: {
//                    cbId: $('.cbId').val(),
//                    certificationComment: $('.certificationComment').val(),
//                },
//                success: function (data) {
//                    $('.certificationComment').val("");
//                    $('.cbComments').empty();
////                    alert(data);
//                    for (var i in data) {
//                        $('.cbComments').append('<span class=""><i class="fa fa-comment-o"></i> ' + data[i]['comment'] + '</span> <br>');
//                    }
//                },
//                error: function (data, status, err) {
//                    alert("Error " + status);
//                }
//            });
        });

        $('.insertConsultingComment').click(function () {
            $.ajax({
                url: "addComments.php",
                dataType: "json",
                type: 'POST',
                data: {
                    consultId: $('.consultingId').val(),
                    consultComment: $('.consultingComment').val(),
                },
                success: function (data) {
                    $('.consultingComment').val("");
                    $('.consultingComments').empty();
////                    alert(data);
                    for (var i in data) {
                        $('.consultingComments').append('<span class=""><i class="fa fa-comment-o"></i> ' + data[i]['comment'] + '</span> <br>');
                    }
                },
                error: function (data, status, err) {
//                    alert("Error " + status);
                }
            });
        });

        $(".addnewrow").on("click", function () {
    $('.serviceDescTd').append('<div class="row"><div class="col-sm-12 form-group float-label-control">\n\
        <label>Service Description</label>\n\
        <input type="text" name="serviceDescription" class="form-control form-group serviceDescription" placeholder="Service Description">\n\
    </div></div>');

    $('.serviceDescTd').fadeIn("slow");
});


        $('.addnewrowtoedit').click(function () {
            $('.editserviceDescTd').append('<div class="row"><div class="col-sm-12 form-group float-label-control">\n\
                                        <label for = "" > Service Description </label>\n\
                                        <input type="text" name="serviceDescription" rows = "2" class = "form-control form-group serviceDescription" id = "serviceDescription" placeholder = "Service Description" >\n\
                                        </div></div >');
            $('.editserviceDescTd').fadeIn("slow");
        });

        $('.addConsultingFirm').click(function (e) {
            var firmServiceArray = [];
            $(".serviceDescription").each(function () {
                firmServiceArray.push($(this).val());
            });
            $('#firmServicesHidden').val(firmServiceArray.join("~"));

            var bankAbbrArray = [];
            $(".bankAbbreviation").each(function () {
                bankAbbrArray.push($(this).val());
            });
            $('#firmBankAbbrHidden').val(bankAbbrArray.join("~"));
            if (validateFirm()) {
                e.preventDefault();
                return;
            }
        });

        $('.editConsultingFirm').click(function (e) {
            var firmServiceArray = [];
            $(".serviceDescription").each(function () {
                firmServiceArray.push($(this).val());
            });
            $('#editfirmServicesHidden').val(firmServiceArray.join("~"));

            var firmServiceIdArray = [];
            $(".serviceDescId").each(function () {
                firmServiceIdArray.push($(this).val());
            });
            $('#editfirmServicesIdHidden').val(firmServiceIdArray.join("~"));

            var firmAbbreviationIdArray = [];
            $(".editAbbrId").each(function () {
                firmAbbreviationIdArray.push($(this).val());
            });
            $('#editAbbrIdHidden').val(firmAbbreviationIdArray.join("~"));

            var firmAbbreviationArray = [];
            $(".editbankAbbreviation").each(function () {
                firmAbbreviationArray.push($(this).val());
            });

            $('#editbankAbbreviationHidden').val(firmAbbreviationArray.join("~"));
            if (validateEditFirm()) {
                e.preventDefault();
                return;
            }
        });

        $(".addConditionRow").on("click", function () {
            //            $('.actionItemTd').append('<tr><td><textarea placeholder="Enter action item description" required  class="form-control actionitems" rows="1"></textarea></td>\n\
            //                            \n\<td><input type="text"  placeholder="Enter name of employee" class="form-control assignedto" rows="1"></td><td class="deleterow" style="cursor: pointer;"><i style="margin-top: 9px" class="glyphicon glyphicon-remove-sign "></i></td><tr>');
            $('.conditionDescTd').append('<div class="row"><div class="col-sm-12 form-group float-label-control"><label for = "" > Condition Description </label>\n\
     <textarea name = "conditionDescription" rows = "2" class = "form-control form-group conditionDescription" id = "conditionDescription" placeholder = "Condition Description" > </textarea>\n\
        </div></div > ');
            //            actinitemadd.hide();
            //            $('tr:last-child').after(actinitemadd);
            $('.conditionDescTd').fadeIn("slow");
        });

        $('.editaddConditionRow').on("click", function () {
            //            $('.actionItemTd').append('<tr><td><textarea placeholder="Enter action item description" required  class="form-control actionitems" rows="1"></textarea></td>\n\
            //                            \n\<td><input type="text"  placeholder="Enter name of employee" class="form-control assignedto" rows="1"></td><td class="deleterow" style="cursor: pointer;"><i style="margin-top: 9px" class="glyphicon glyphicon-remove-sign "></i></td><tr>');
            $('.editconditionDescTd').append('<div class="row"><div class="col-sm-12 form-group float-label-control"><label for = "" > Condition Description </label>\n\
     <textarea name = "editconditionDescription" rows = "2" class = "form-control form-group editconditionDescription" id = "editconditionDescription" placeholder = "Condition Description" > </textarea>\n\
        </div></div > ');
            //            actinitemadd.hide();
            //            $('tr:last-child').after(actinitemadd);
            $('.editconditionDescTd').fadeIn("slow");
        });


        $(".addServiceAmtRowedit").on("click", function () {
//            alert("in");
            //            $('.actionItemTd').append('<tr><td><textarea placeholder="Enter action item description" required  class="form-control actionitems" rows="1"></textarea></td>\n\
            //                            \n\<td><input type="text"  placeholder="Enter name of employee" class="form-control assignedto" rows="1"></td><td class="deleterow" style="cursor: pointer;"><i style="margin-top: 9px" class="glyphicon glyphicon-remove-sign "></i></td><tr>');
            $('.serviceAmtTdedit').append('<div class="row removeServiceEditRow">\n\
                               <div class="col-sm-8 form-group float-label-control" style="padding-top:10px;">\n\
                                    <label for="">Service</label>\n\
                                    <input type="text" name="invoiceServiceEdit[]" class="form-control form-group invoiceServiceEdit" id="invoiceServiceEdit" value="" placeholder="Service">\n\
                                </div>\n\
                                <div class="col-sm-3 form-group float-label-control" style="padding-top:10px;">\n\
                                    <label for="">Amount</label>\n\
                                    <input type="number" name="invoiceServiceAmtEdit[]" class="form-control form-group invoiceServiceAmtEdit" id="invoiceServiceAmtEdit" value="" placeholder="Amount" step="0.01" inputmode="decimal">\n\
                                </div>\n\
                                <div class="col-sm-1 removeServiceEditCol">\n\
                                    <a class="removeServiceEditAmt pull-right" id="removeServiceEditAmt" style="cursor: pointer;">\n\
                                    <i style="margin-top: 9px; color: black;" class="glyphicon glyphicon-remove" title="Add new Service"></i>\n\
                                    </a>\n\
                                </div>\n\
                                </div>');
            //            actinitemadd.hide();
            //            $('tr:last-child').after(actinitemadd);
            $('.serviceAmtTdedit').fadeIn("slow");
        });

        $('body').on("click", ".removeServiceEditAmt", function () {
//        alert("Delete this");
            $(this).parent("div.removeServiceEditCol").parent('div.removeServiceEditRow').remove();
        });

        $(".addServiceAmtRow").on("click", function () {
//            alert("in");
            //            $('.actionItemTd').append('<tr><td><textarea placeholder="Enter action item description" required  class="form-control actionitems" rows="1"></textarea></td>\n\
            //                            \n\<td><input type="text"  placeholder="Enter name of employee" class="form-control assignedto" rows="1"></td><td class="deleterow" style="cursor: pointer;"><i style="margin-top: 9px" class="glyphicon glyphicon-remove-sign "></i></td><tr>');
            $('.serviceAmtTd').append('<div class="row removeServiceAmtRow">\n\
        <div class = "col-sm-8 form-group float-label-control" style = "padding-top:10px;" > \n\
        <label for = "" > Service </label>\n\
       <input type = "text" name = "invoiceService[]" class = "form-control form-group serviceInvoiceString" id = "invoiceService" placeholder = "Service" > </input>\n\
        </div>\n\
        <div class = "col-sm-3 form-group float-label-control" style = "padding-top:10px;" > \n\
        <label for = "" > Amount </label>\n\
        <input type = "number" name = "invoiceServiceAmt[]" class = "form-control form-group invoiceServiceAmt" id = "invoiceService" placeholder = "Amount" step="0.01" inputmode="decimal"> </input>\n\
        </div>\n\
        <div class="col-sm-1 removeServiceAmtCol">\n\
        <a class="removeServiceAmt pull-right" id="removeServiceAmt" style="cursor: pointer;">\n\
        <i style="margin-top: 9px; color: black;" class="glyphicon glyphicon-remove" title="Add new Service"></i>\n\
        </a>\n\
        </div>\n\
        </div>');
            //            actinitemadd.hide();
            //            $('tr:last-child').after(actinitemadd);
            $('.serviceAmtTd').fadeIn("slow");
        });

        $('body').on("click", ".removeServiceAmt", function () {
//        alert("Delete this");
            $(this).parent("div.removeServiceAmtCol").parent('div.removeServiceAmtRow').remove();
        });

//        $('.generateQuotation').click(function () {
//            var firmServiceArray = [];
//            $(".conditionDescription").each(function () {
//                firmServiceArray.push($(this).val());
//                //            alert()
//            });
//            $('.conditionsHidden').val(firmServiceArray.join("~"));
////            $('#quotation').modal('hide');
//        });

        $('.consultingFirmName').autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "autoSearch.php",
                    dataType: "json",
                    type: 'GET',
                    data: {
                        consultingFirm: $('.consultingFirmName').val(),
                    },
                    success: function (data) {
                        //                                                    alert(JSON.stringify(data));
                        response(data);
                    },
                    error: function (data, status, err) {
                        //                        alert(status);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                //            alert(ui.item.id);
                $("#consultingFirmId").val(ui.item.id);
                $("#invoiceFirmId").val(ui.item.id);
                //                    //                    alert(ui.item.id);
            },
            open: function () {

                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {

                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $('.invoiceClient').autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "autoSearch.php",
                    dataType: "json",
                    type: 'GET',
                    data: {
                        consultingFirm: $('.invoiceClient').val(),
                    },
                    success: function (data) {
                        //                                                    alert(JSON.stringify(data));
                        response(data);
                    },
                    error: function (data, status, err) {
                        //                        alert(status);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                //            alert(ui.item.id);
//                $("#consultingFirmId").val(ui.item.id);
                $("#invoiceEditCustomerId").val(ui.item.id);
                //                    //                    alert(ui.item.id);
            },
            open: function () {

                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {

                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $('.quoteconsultingFirmName').autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "autoSearch.php",
                    dataType: "json",
                    type: 'GET',
                    data: {
                        consultingFirm: $('.quoteconsultingFirmName').val(),
                    },
                    success: function (data) {
                        //                                                    alert(JSON.stringify(data));
                        response(data);
                    },
                    error: function (data, status, err) {
                        //                        alert(status);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                //            alert(ui.item.id);
                $("#consultingFirmId").val(ui.item.id);
//                $("#invoiceFirmId").val(ui.item.id);
                //                    //                    alert(ui.item.id);
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $('.invoiceconsultingFirmName').change(function () {
            var frmid = $('.invoiceconsultingFirmName').val();
            $('.consultingFirmId').val(frmid);
//            alert  $('.consultingFirmId').val(frmid);
            $.ajax({
                url: "autoSearch.php",
                dataType: "json",
                type: 'GET',
                data: {
                    consultingId: $('.invoiceconsultingFirmName').val(),
                },
                success: function (data) {
//                    console.log(data);
//                    alert(data);
//                    $('.invoiceService').empty();
//                    $('.invoiceService').append('<option value="">Select Service</option>');
//
//                    for (var key in data[0]) {
////                        alert(data[0][key]['service_desc'])
//                        $('.invoiceService').append('<option value="' + data[0][key]['service_desc'] + '">' + data[0][key]['service_desc'] + '</option>');
//                    }
////                    $('.invoiceService').append('<option class = "other" value="other">Other</option>');
//
//                    if (data[1]['invoice_date'] != "" && data[1]['invoice_date'] != null && data[1]['invoice_date'] != undefined) {
//                        $('.preInvDate').val(moment(data[1]['invoice_date']).format('DD.MM.YYYY'));
//                    } else {
//                        $('.preInvDate').val("");
//                    }
                    //alert($('.preInvDate').val());
                },
                error: function (data, status, err) {
                    alert(status);
                }
            });
        });



        $('.invoiceService').change(function () {
            var initialText = $('.other').val();
            //$('.editOption').val(initialText);
            var selected = $('option:selected', this).attr('class');
            var optionText = $('.other').text();

            if (selected == "other") {
                $('.editOption').show();
                $('.editOption').keyup(function (e) {

                    var editText = $('.editOption').val();
                    $('.other').val(editText);
                    $('.other').html(editText);
                });
            } else {
                $('.editOption').hide();
            }
        });

        $('.invoiceServiceEdit').change(function () {
            var initialText = $('.otheredit').val();
            //$('.editOption').val(initialText);
            var selected = $('option:selected', this).attr('class');
            var optionText = $('.otheredit').text();

            if (selected == "otheredit") {
                $('.editOptionedit').show();
                $('.editOptionedit').keyup(function (e) {

                    var editText = $('.editOptionedit').val();
                    $('.otheredit').val(editText);
                    $('.otheredit').html(editText);
                });
            } else {
                $('.editOptionedit').hide();
            }
        });

        $(document).on('keydown.autocomplete', '.serviceInvoiceString', function () {
//            alert($('.consultingFirmId').val());
            var firmid = $(".invoiceconsultingFirmName").val();
            $(this).autocomplete({
                source: function (request, response) {
//                alert($('.consulting-firm').val());
//                alert(request.term);
                    $.ajax({
                        url: "autoSearch.php",
                        dataType: "json",
                        type: 'GET',
                        data: {
                            consultingFirmId: $('.consultingFirmId').val(),
                            searchServiceString: request.term,
                        },
                        success: function (data) {
//                          alert(data);
                            response(data);
                        },
                        error: function (data, status, err) {
//                            alert(status);
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

        $('#mktgauditType').autocomplete({
            source: function (request, response) {

                $.ajax({
                    url: "autoSearch.php",
                    dataType: "json",
                    type: 'GET',
                    data: {
                        auditType: $('#mktgauditType').val(),
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

    function readURL(input, id) {
        //            alert("in");
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            //               alert("in"+input.files);
            reader.onload = function (e) {
                //                     alert(e.target.result);
                $('#' + id)
                        .attr('src', e.target.result)
                        .width(150)
                        .height(200);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function handleEditGst() {
    var gstApplicable = document.querySelector('input[name="editIsGstApplicable"]:checked');
    if (gstApplicable && gstApplicable.value === "Yes") {
        document.getElementById("editGstPercentBlock").style.display = "block";
    } else {
        document.getElementById("editGstPercentBlock").style.display = "none";
    }
}


    function readURLSignature(input, id) {
        //            alert("in");
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            //               alert("in"+input.files);
            reader.onload = function (e) {
                //                     alert(e.target.result);
                $('#' + id)
                        .attr('src', e.target.result)
                        .width(150)
                        .height(200);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
