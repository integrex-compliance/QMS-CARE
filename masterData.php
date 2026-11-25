<div id="wrapper" class="active"> 

    <?php
    $pageIdentifier = "Master Data";

    include_once './header.php';
    include_once './dbConnect.php';

//    select service list
    $services = "SELECT * FROM service_type ORDER BY id ASC";
    $statement = $db->prepare($services);
    $statement->execute();
    $serviceList = $statement->fetchAll();

//    select cert list
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

//    bank details
    $bankAccounts = "SELECT * FROM bank_account";
    $statement = $db->prepare($bankAccounts);
    $statement->execute();
    $bankAccountList = $statement->fetchAll();

//    audit type
    $auditType = "SELECT * from audit_type";
    $statement = $db->prepare($auditType);
    $statement->execute();
    $auditTypeList = $statement->fetchAll();

    $allHandler = "SELECT * FROM handler";
    $statement = $db->prepare($allHandler);
    $statement->execute();
    $handlerList = $statement->fetchAll();
    
    $allpcip = "SELECT * FROM `pc_ip`";
    $statement = $db->prepare($allpcip);
    $statement->execute();
    $pcips = $statement->fetchAll();

//    service stages
    $serviceStageList = [];
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS `service_stage` (`id` int(11) NOT NULL AUTO_INCREMENT, `stage` varchar(50) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `stage` (`stage`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci");
        $serviceStagesSql = "SELECT * FROM service_stage ORDER BY id ASC";
        $statement = $db->prepare($serviceStagesSql);
        $statement->execute();
        $serviceStageList = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
    ?>

    <div id="page-content-wrapper" >
        <div class="page-content inset">
            <div class="container" style="margin-top: 60px;">

                <div class="row">                    
                    <div class="col-sm-6">
                        <div class="row" style="">
                            <div class="col-xs-8  text-center btn  qms_complete_btn ">
                                <span><i class="fa  ">Service Type</i></span>
                            </div>

                            <div class="col-xs-4  text-center btn qms_complete_btn newService">
                                <span><i class="fa fa-plus-square "> Add Service</i></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="well" style="max-height: 280px;overflow: auto;">
                                <ul class="list-group checked-list-box operateServices">
                                    <?php foreach ($serviceList as $service) { ?>
                                        <li class="list-group-item"><?php echo $service['service'] ?><a class="deleteService" data-id="<?php echo $service['id'] ?>" style="float: right"><i class="fa fa-trash"></i></a></li>                                
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="row addNewService">

                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="row" style="">
                            <div class="col-xs-8  text-center btn  qms_complete_btn ">
                                <span><i class="fa ">Cert Body</i></span>
                            </div>

                            <div class="col-xs-4  text-center btn qms_complete_btn newCert ">
                                <span><i class="fa fa-plus-square "> Add Cert</i></span>
                            </div>
                        </div>
                        <div class="row" style="">
                            <div class="well" style="max-height: 280px;overflow: auto;">
                                <ul class="list-group checked-list-box operateCert">
                                    <?php foreach ($certList as $cert) { ?>
                                        <li class="list-group-item"><?php echo $cert['cert'] ?><a class="deleteCert" data-id="<?php echo $cert['cert_id'] ?>" style="float: right"><i class="fa fa-trash"></i></a><a class="editCert" data-id="<?php echo $cert['cert_id'] ?>" style="float: right; padding-right: 5px"><i class="fa fa-edit"></i></a></li>                                
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="row addNewCert">

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="row" style="">
                            <div class="col-xs-8  text-center btn  qms_complete_btn ">
                                <span><i class="fa ">Consulting Firm</i></span>
                            </div>

                            <div class="col-xs-4  text-center btn qms_complete_btn addNewFirm">
                                <span><i class="fa fa-plus-square "> Add Consulting Firm</i></span>
                            </div>
                        </div>
                        <div class="row" style="">
                            <div class="well" style="max-height: 280px;overflow: auto;">
                                <ul class="list-group checked-list-box operateFirm">
                                    <?php foreach ($firmList as $firm) { ?>
                                        <li class="list-group-item" ><?php echo $firm['firm_name'] ?><a class="deleteFirm" data-id="<?php echo $firm['consulting_firm_id'] ?>" style="float: right" title="Delete Consulting Firm"><i class="fa fa-trash"></i></a><a class="editFirm" data-id="<?php echo $firm['consulting_firm_id'] ?>" style="float: right; padding-right: 5px" title="Edit Consulting Firm"><i class="fa fa-edit"></i></a></li>                                
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="row" style="">
                            <div class="col-xs-8  text-center btn  qms_complete_btn ">
                                <span><i class="fa ">Consultant</i></span>
                            </div>

                            <div class="col-xs-4  text-center btn qms_complete_btn addNewConsultant">
                                <span><i class="fa fa-plus-square "> Add Consultant</i></span>
                            </div>
                        </div>
                        <div class="row" style="">
                            <div class="well" style="max-height: 280px;overflow: auto;">
                                <ul class="list-group checked-list-box operateConsultant">
                                    <?php foreach ($consultantList as $consultant) { ?>
                                        <li class="list-group-item"><?php echo $consultant['consultant_name'] ?><a class="deleteConsultant" data-id="<?php echo $consultant['consultant_id'] ?>" style="float: right"><i class="fa fa-trash"></i></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="row newConsultant">

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="row" style="">
                            <div class="col-xs-8  text-center btn  qms_complete_btn ">
                                <span><i class="fa ">Auditors</i></span>
                            </div>

                            <div class="col-xs-4  text-center btn qms_complete_btn addNewAuditors">
                                <span><i class="fa fa-plus-square "> Add Auditors</i></span>
                            </div>
                        </div>
                        <div class="row" style="">
                            <div class="well" style="max-height: 280px;overflow: auto;">
                                <ul class="list-group checked-list-box operateAuditors">
                                    <?php foreach ($auditorsList as $auditor) { ?>
                                        <li class="list-group-item"><?php echo $auditor['auditor_name'] ?><a class="deleteAuditor" data-id="<?php echo $auditor['auditors_id'] ?>" style="float: right"><i class="fa fa-trash"></i></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="row newAuditors">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row" style="">
                            <div class="col-xs-8  text-center btn  qms_complete_btn ">
                                <span><i class="fa ">Handle By</i></span>
                            </div>

                            <div class="col-xs-4  text-center btn qms_complete_btn addNewHandler">
                                <span><i class="fa fa-plus-square "> Add Handler</i></span>
                            </div>
                        </div>
                        <div class="row" style="">
                            <div class="well" style="max-height: 280px;overflow: auto;">
                                <ul class="list-group checked-list-box operateHandler">
                                    <?php foreach ($handlerList as $handler) { ?>
                                        <li class="list-group-item"><?php echo $handler['handler_name'] ?><a class="deleteHandler" data-id="<?php echo $handler['handler_id'] ?>" style="float: right"><i class="fa fa-trash"></i></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="row add-new-Handler">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row" style="">
                            <div class="col-xs-8  text-center btn  qms_complete_btn ">
                                <span><i class="fa ">PC IP's</i></span>
                            </div>

                            <div class="col-xs-4  text-center btn qms_complete_btn addNewPCIP">
                                <span><i class="fa fa-plus-square "> Add PC IP</i></span>
                            </div>
                        </div>
                        <div class="row" style="">
                            <div class="well" style="max-height: 280px;overflow: auto;">
                                <ul class="list-group checked-list-box operatePCIP">
                                    <?php foreach ($pcips as $ips) { ?>
                                        <li class="list-group-item"><?php echo $ips['pcip'] ?><a class="deletePcip" data-id="<?php echo $ips['id'] ?>" style="float: right"><i class="fa fa-trash"></i></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="row newPCIP">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row" style="">
                            <div class="col-xs-8  text-center btn  qms_complete_btn ">
                                <span><i class="fa ">Service Stage</i></span>
                            </div>

                            <div class="col-xs-4  text-center btn qms_complete_btn newServiceStage">
                                <span><i class="fa fa-plus-square "> Add Stage</i></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="well" style="max-height: 280px;overflow: auto;">
                                <ul class="list-group checked-list-box operateServiceStages">
                                    <?php foreach ($serviceStageList as $stg) { ?>
                                        <li class="list-group-item"><?php echo htmlspecialchars($stg['stage'], ENT_QUOTES, 'UTF-8') ?><a class="deleteServiceStage" data-id="<?php echo (int)$stg['id'] ?>" style="float: right; cursor: pointer;"><i class="fa fa-trash"></i></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="row addNewServiceStage">

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
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        
        $('.addNewPCIP').click(function () {
            $('.newPCIP').empty();
            $('.newPCIP').append('<div class="col-sm-12"><div class="well" style="height: 100px;">\n\
                                        <div class="col-sm-12 form-group float-label-control">\n\
                                        <input type="text" name="new_pcip" class="form-control new_pcip" id="new_pcip" placeholder="New PC IP" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$"></div>\n\
                                        <a class="btn btn-info btn-xs createPcip" style="float: right;"><i class="fa fa-check"></i></a>\n\
                                        </div>\n\
                                        </div>');
        });
        
        $('body').on('click', ".createPcip", function (e) {
            $.ajax({
                url: "addNewServices.php",
                type: "GET",
                dataType: "json",
                data: {
                    newpcip: $('#new_pcip').val(),
                },
                success: function (data)
                {
//                        alert(data);
                    $('.newPCIP').empty();
                    $('.operatePCIP').empty();

                    for (var i in data) {
                        $('.operatePCIP').append('<li class="list-group-item">' + data[i]['pcip'] + '<a class="deletePcip" data-id="' + data[i]['id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                    }
                },
                error: function (data, status, err)
                {
                    $('.new_pcip').empty();
//                    alert(status);
                }
            });
        });
        
        $('body').on('click', ".deletePcip", function (e) {
            $id = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addNewServices.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        ipId: $id,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('#delete-details').hide();
                        $('.operatePCIP').empty();

                        for (var i in data) {
                            $('.operatePCIP').append('<li class="list-group-item">' + data[i]['pcip'] + '<a class="deletePcip" data-id="' + data[i]['id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                        }
                    },
                    error: function (data, status, err)
                    {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });
        
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container").toggleClass("active");

            table.draw();
        });

        $('.newService').click(function () {
            $('.addNewService').empty();
            $('.addNewService').append('<div class="col-sm-12"><div class="well" style="height: 100px;">\n\
                                        <div class="col-sm-12 form-group float-label-control">\n\
                                        <label>New service</label>\n\
                                        <input type="text" name="newService" class="form-control newService" id="newService" placeholder="New service" ></div>\n\
                                        <a class="btn btn-info btn-xs createService" style="float: right;"><i class="fa fa-check"></i></a>\n\
                                        </div>\n\
                                        </div>')
        });

        function renderServiceStagesList(data) {
            var $list = $('.operateServiceStages').empty();
            if (!Array.isArray(data)) return;
            for (var i = 0; i < data.length; i++) {
                var $li = $('<li class="list-group-item"></li>').text(data[i]['stage']);
                var $del = $('<a class="deleteServiceStage" style="float: right; cursor: pointer;"><i class="fa fa-trash"></i></a>').attr('data-id', data[i]['id']);
                $li.append($del);
                $list.append($li);
            }
        }

        $('.newServiceStage').click(function () {
            $('.addNewServiceStage').empty();
            $('.addNewServiceStage').append('<div class="col-sm-12"><div class="well" style="height: 100px;">\n\
                                        <div class="col-sm-12 form-group float-label-control">\n\
                                        <label>New Service Stage</label>\n\
                                        <input type="text" name="newServiceStage" class="form-control" id="newServiceStageInput" placeholder="New Service Stage" maxlength="50"></div>\n\
                                        <a class="btn btn-info btn-xs createServiceStage" style="float: right; cursor: pointer;"><i class="fa fa-check"></i></a>\n\
                                        </div>\n\
                                        </div>');
            $('#newServiceStageInput').focus();
        });

        $('body').on('keydown', '#newServiceStageInput', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('.createServiceStage').click();
            }
        });

        $('body').on('click', '.createServiceStage', function () {
            var stageVal = $.trim($('#newServiceStageInput').val());
            if (!stageVal) {
                $('#newServiceStageInput').focus();
                return;
            }
            $.ajax({
                url: "addNewServices.php",
                type: "GET",
                dataType: "json",
                data: {
                    newServiceStage: stageVal
                },
                success: function (data) {
                    $('.addNewServiceStage').empty();
                    renderServiceStagesList(data);
                },
                error: function () {
                    $('.addNewServiceStage').empty();
                }
            });
        });

        $('body').on('click', '.deleteServiceStage', function () {
            var stageId = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addNewServices.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        serviceStageId: stageId
                    },
                    success: function (data) {
                        $('#delete-details').hide();
                        renderServiceStagesList(data);
                    },
                    error: function (data, status) {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').off('click').on('click', function () {
                $('#delete-details').hide();
            });
        });

        $('.newCert').click(function () {
            $('#addNewCert').modal('show');
//            $('.addNewCert').empty();
//            $('.addNewCert').append('<div class="col-sm-12">\n\
//                                <div class="well" style="height: 150px;">\n\
//                                    <div class="row">\n\
//                                        <div class="col-sm-6 form-group float-label-control">\n\
//                                            <div class="row">\n\
//                                                <div class="col-sm-12 form-group float-label-control">\n\
//                                                    <label>New cert</label>\n\
//                                                    <input type="text" name="newCert" class="form-control newCert" id="newCert" placeholder="New Cert" >\n\
//                                                </div>\n\
//                                            </div>\n\
//                                        </div>\n\
//                                        <div class="col-sm-6 form-group float-label-control">\n\
//                                            <div class="row">\n\
//                                                <div class="col-sm-8 form-group float-label-control">\n\
//                                                    <label>New abbreviation</label>\n\
//                                                    <input type="text" name="newAbbreviation" class="form-control newAbbreviation" id="newAbbreviation" placeholder="New abbreviation" >\n\
//                                                </div>\n\
//                                                <div class="col-sm-4 form-group float-label-control">\n\
//                                                    <a class=" newAbbr" style="float: right;"><i class="fa fa-check"></i></a>\n\
//                                                </div>\n\
//                                            </div>\n\
//                                            <div class="row newAbbrAdd">\n\
//                                                \n\
//                                            </div>\n\
//                                        </div>\n\
//                                    </div>\n\
//                                    <a class="btn btn-info btn-xs createCert" style="float: right;"><i class="fa fa-check"></i></a>\n\
//                                </div>\n\
//                            </div>')
        });

        $('.addNewHandler').click(function () {
            $('.add-new-Handler').empty();
            $('.add-new-Handler').append('<div class="col-sm-12"><div class="well" style="height: 100px;">\n\
                                        <div class="col-sm-12 form-group float-label-control">\n\
                                        <input type="text" name="newHandler" class="form-control newHandler" id="newHandler" placeholder="New Handler" ></div>\n\
                                        <a class="btn btn-info btn-xs createHandler" style="float: right;"><i class="fa fa-check"></i></a>\n\
                                        </div></div>');
        });

        $('.addNewConsultant').click(function () {
            $('.newConsultant').empty();
            $('.newConsultant').append('<div class="col-sm-12"><div class="well" style="height: 100px;">\n\
                                        <div class="col-sm-12 form-group float-label-control">\n\
                                        <input type="text" name="newConsultant" class="form-control newConsultant" id="newConsultant" placeholder="New Consultant" ></div>\n\
                                        <a class="btn btn-info btn-xs createConsultant" style="float: right;"><i class="fa fa-check"></i></a>\n\
                                        </div></div>');
        });

        $('.addNewAuditors').click(function () {
            $('.newAuditors').empty();
            $('.newAuditors').append('<div class="col-sm-12"><div class="well" style="height: 100px;">\n\
                                        <div class="col-sm-12 form-group float-label-control">\n\
                                        <input type="text" name="newAuditors" class="form-control newAuditors" id="newAuditors" placeholder="New Auditors" ></div>\n\
                                        <a class="btn btn-info btn-xs createAuditors" style="float: right;"><i class="fa fa-check"></i></a>\n\
                                        </div></div>');
        });

        $('.addNewAuditType').click(function () {
            $('.newAuditType').empty();
            $('.newAuditType').append('<div class="col-sm-12"><div class="well" style="height: 100px;">\n\
                                        <div class="col-sm-12 form-group float-label-control">\n\
                                        <input type="text" name="newAudit_type" class="form-control newAudit_type" id="newAudit_type" placeholder="New Audit Type" ></div>\n\
                                        <a class="btn btn-info btn-xs createAudit_type" style="float: right;"><i class="fa fa-check"></i></a>\n\
                                        </div></div>');
        });

        $('body').on('click', ".createHandler", function (e) {
            $.ajax({
                url: "addNewServices.php",
                type: "GET",
                dataType: "json",
                data: {
                    newHandler: $('#newHandler').val(),
                },
                success: function (data)
                {
//                        alert(data);
                    $('.add-new-Handler').empty();
                    $('.operateHandler').empty();

                    for (var i in data) {
                        $('.operateHandler').append('<li class="list-group-item">' + data[i]['handler_name'] + '<a class="deleteHandler" data-id="' + data[i]['handler_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                    }
                },
                error: function (data, status, err)
                {
                    $('.addNewService').empty();
//                    alert(status);
                }
            });
        });

        $('body').on('click', ".createService", function (e) {
//            alert($('#newService').val());
            $.ajax({
                url: "addNewServices.php",
                type: "GET",
                dataType: "json",
                data: {
                    newService: $('#newService').val(),
                },
                success: function (data)
                {
//                        alert(data);
                    $('.addNewService').empty();
                    $('.displayServices').empty();
                    $('.operateServices').empty();

                    for (var i in data) {
                        $('.displayServices').append('<li class="list-group-item">' + data[i]['service'] + '</li>  ');
                        $('.operateServices').append('<li class="list-group-item">' + data[i]['service'] + '<a class="deleteService" data-id="' + data[i]['id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                    }
                },
                error: function (data, status, err)
                {
                    $('.addNewService').empty();
//                    alert(status);
                }
            });
        });

//        $('body').on('click', ".createCert", function (e) {
////            alert($('#newCert').val());
//
//            var certAbbrArray = [];
//
//            $('.newAbbreviation').each(function () {                
//                    certAbbrArray.push($(this).val());
//            });
//            $certAbbr = certAbbrArray.join("~");
//            
//            $.ajax({
//                url: "addNewServices.php",
//                type: "GET",
//                dataType: "json",
//                data: {
//                    newCert: $('#newCert').val(),
//                    newAbbr: $certAbbr,
//                },
//                success: function (data)
//                {
////                    alert(data);
//                    $('.addNewCert').empty();
//                    $('.displayCert').empty();
//                    $('.operateCert').empty();
//
//                    for (var i in data) {
//                        $('.displayCert').append('<li class="list-group-item">' + data[i]['cert'] + '</li> ');
//                        $('.operateCert').append('<li class="list-group-item">' + data[i]['cert'] + '<a class="deleteCert" data-id="' + data[i]['cert_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
//                    }
//                },
//                error: function (data, status, err)
//                {
//                    $('.addNewCert').empty();
////                    alert(status);
//                }
//            });
//        });

        $('body').on('click', ".createConsultant", function (e) {
            $.ajax({
                url: "addNewServices.php",
                type: "GET",
                dataType: "json",
                data: {
                    newConsultant: $('#newConsultant').val(),
                },
                success: function (data)
                {
//                    alert(data);
                    $('.newConsultant').empty();
                    $('.operateConsultant').empty();
                    for (var i in data) {
                        $('.operateConsultant').append('<li class="list-group-item">' + data[i]['consultant_name'] + '<a class="deleteConsultant" data-id="' + data[i]['consultant_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>')
                    }
                },
                error: function (data, status, err)
                {
                    $('.newConsultant').empty();
//                    alert(status);
                }
            });
        });

        $('body').on('click', ".createAuditors", function (e) {
            $.ajax({
                url: "addNewServices.php",
                type: "GET",
                dataType: "json",
                data: {
                    newAuditors: $('#newAuditors').val(),
                },
                success: function (data)
                {
//                    alert(data);
                    $('.newAuditors').empty();
                    $('.operateAuditors').empty();
                    for (var i in data) {
                        $('.operateAuditors').append('<li class="list-group-item">' + data[i]['auditor_name'] + '<a class="deleteAuditor" data-id="' + data[i]['auditors_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                    }
                },
                error: function (data, status, err)
                {
                    $('.newAuditors').empty();
//                    alert(status);
                }
            });
        });

        $('body').on('click', ".createAudit_type", function (e) {
            $.ajax({
                url: "addNewServices.php",
                type: "GET",
                dataType: "json",
                data: {
                    newAuditType: $('#newAudit_type').val(),
                },
                success: function (data)
                {
//                    alert(data);
                    $('.newAuditType').empty();
                    $('.operateAuditType').empty();
                    for (var i in data) {
                        $('.operateAuditType').append('<li class="list-group-item">' + data[i]['audit_types'] + '<a class="deleteAuditType" data-id="' + data[i]['audit_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                    }
                },
                error: function (data, status, err)
                {
                    $('.newAuditType').empty();
//                    alert(status);
                }
            });
        });

        $('body').on('click', ".deleteAuditType", function (e) {
            $id = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addNewServices.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        auditTypeId: $id,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('#delete-details').hide();
                        $('.operateAuditType').empty();
                        for (var i in data) {
                            $('.operateAuditType').append('<li class="list-group-item">' + data[i]['audit_types'] + '<a class="deleteAuditType" data-id="' + data[i]['audit_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                        }
                    },
                    error: function (data, status, err)
                    {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });

        });

        $('body').on('click', ".deleteAuditor", function (e) {
            $id = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addNewServices.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        auditorId: $id,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('#delete-details').hide();
                        $('.operateAuditors').empty();
                        for (var i in data) {
                            $('.operateAuditors').append('<li class="list-group-item">' + data[i]['auditor_name'] + '<a class="deleteAuditor" data-id="' + data[i]['auditors_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                        }
                    },
                    error: function (data, status, err)
                    {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('body').on('click', ".deleteHandler", function (e) {
            $id = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addNewServices.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        handlerId: $id,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('#delete-details').hide();
                        $('.operateHandler').empty();

                        for (var i in data) {
                            $('.operateHandler').append('<li class="list-group-item">' + data[i]['handler_name'] + '<a class="deleteHandler" data-id="' + data[i]['handler_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                        }
                    },
                    error: function (data, status, err)
                    {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('body').on('click', ".deleteService", function (e) {
            $id = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addNewServices.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        serviceId: $id,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('#delete-details').hide();
                        $('.displayServices').empty();
                        $('.operateServices').empty();

                        for (var i in data) {
                            $('.displayServices').append('<li class="list-group-item">' + data[i]['service'] + '</li>  ');
                            $('.operateServices').append('<li class="list-group-item">' + data[i]['service'] + '<a class="deleteService" data-id="' + data[i]['id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                        }
                    },
                    error: function (data, status, err)
                    {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('body').on('click', ".deleteCert", function (e) {
            $id = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addNewServices.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        certId: $id,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('#delete-details').hide();
                        $('.displayCert').empty();
                        $('.operateCert').empty();

                        for (var i in data) {
                            $('.displayCert').append('<li class="list-group-item">' + data[i]['cert'] + '</li>  ');
                            $('.operateCert').append('<li class="list-group-item">' + data[i]['cert'] + '<a class="deleteCert" data-id="' + data[i]['cert_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                        }
                    },
                    error: function (data, status, err)
                    {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('body').on('click', ".deleteFirm", function (e) {
            $id = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addConsultingFirm.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        firmId: $id,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('#delete-details').hide();
                        $('.operateFirm').empty();
                        for (var i in data) {
                            $('.operateFirm').append('<li class="list-group-item">' + data[i]['firm_name'] + '<a class="deleteFirm" data-id="' + data[i]['consulting_firm_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                        }
                    },
                    error: function (data, status, err)
                    {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('body').on('click', ".deleteConsultant", function (e) {
            $id = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addNewServices.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        consultantId: $id,
                    },
                    success: function (data)
                    {
//                    alert(data);
                        $('#delete-details').hide();
                        $('.operateConsultant').empty();
                        for (var i in data) {
                            $('.operateConsultant').append('<li class="list-group-item">' + data[i]['consultant_name'] + '<a class="deleteConsultant" data-id="' + data[i]['consultant_id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>')
                        }
                    },
                    error: function (data, status, err)
                    {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('body').on('click', ".deleteBankAccount", function (e) {
            $id = $(this).attr('data-id');
            $('#delete-details').show();
            $('.delete-confirm-ok').off('click').on('click', function () {
                $.ajax({
                    url: "addBankDetails.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        accountId: $id,
                    },
                    success: function (data)
                    {
//                        alert(data);
                        $('#delete-details').hide();
                        $('.operateBankAccount').empty();
                        for (var i in data) {
                            $('.operateBankAccount').append('<li class="list-group-item">' + data[i]['accountant_name'] + '<a class="deleteBankAccount" data-id="' + data[i]['id'] + '" style="float: right"><i class="fa fa-trash"></i></a></li>');
                        }
                    },
                    error: function (data, status, err)
                    {
                        alert(status);
                    }
                });
            });
            $('.delete-cancel').click(function () {
                $('#delete-details').hide();
            });
        });

        $('.addNewFirm').click(function () {
            $('#firmAdd').modal("show");
        });

        $('.addNewBankAccount').click(function () {
            $('#bankAcountAdd').modal('show');
        });

        $('.editCert').click(function () {
            $id = $(this).attr('data-id');
//            alert($(this).attr('data-id'));
            $.ajax({
                url: "addNewServices.php",
                type: "GET",
                dataType: "json",
                data: {
                    cert_ID: $(this).attr('data-id'),
                },
                success: function (data)
                {
//                    alert(data[0]['id']);
                    $('.editAbbrAdd').empty();
                    $('.editnewCert').val(data[0]['cert']);
                    $('.cert-id').val(data[0]['cert_id']);
                    $('#editCert').modal('show');

                    for (var key in data[1]) {
                        $('.editAbbrAdd').append('<div class="newCBAbbrRowParent">\n\
                                        <div class="col-sm-10 form-group float-label-control">\n\
                                            <label>New abbreviation</label>\n\
                                            <input type="text" name="editAbbreviation" class="form-control editAbbreviation" id="editAbbreviation" placeholder="New abbreviation" value="' + data[1][key]['bank_abbr'] + '">\n\
                                        </div>\n\
                                        <div class="col-sm-2 form-group float-label-control deleteNewCBAbbrParent">\n\
                                            <a class="deleteNewCBAbbr" style="float: right;"><i class="fa fa-remove"></i></a></div></div>');
                    }
                    $("input").trigger("change");
                    $('textarea').trigger('change');
                },
                error: function (data, status, err)
                {
                    alert(status);
                }
            });
        });

        $('.editFirm').click(function () {
//            alert($(this).attr('data-id')); //

$.ajax({
    url: "addConsultingFirm.php",
    type: "GET",
    dataType: "json",
    data: {
        firmid: $(this).attr('data-id'),
    },
    success: function (data) {
        $('.editserviceDescTd').empty();
        $('.editConsultAbbrvation').empty();

        if (data[0]['consulting_firm_id']) {
            $('.serviceDescTd').empty();
            $('.addConsultingFirm').text("Update");
        }

        // Corrected selectors: IDs start with #
        $('#firmServicesId').val(data[0]['consulting_firm_id']);
        $('#editconsultingFirmName').val(data[0]['firm_name']);
        $('#editconsultingFirmEmail').val(data[0]['firm_email']);
        $('#editfirmAddress').val(data[0]['firm_address']);
        $('#editfirmContact').val(data[0]['firm_contact']);
        $('#editpanNo').val(data[0]['firm_pan_no']);
        $('#editbankName').val(data[0]['bank_name']);
        $('#editbankAcNo').val(data[0]['bank_account_no']);
        $('#editbankAddress').val(data[0]['bank_address']);
        $('#editifscCode').val(data[0]['ifsc_code']);
        $('#editmicrNo').val(data[0]['micr_no']);
        $('#editFirmAbbreviation').val(data[0]['firm_abbrevation']);
        $('#editFirmInvoice').val(data[0]['invoice_no']);
        $('#editFirmQuotation').val(data[0]['quotation_no']);
        $('#editFirmImg').attr("src", data[0]['firm_logo']);
        $('#editSignatureImg').attr("src", data[0]['firmSignature']);
        if (data[0]['is_gst_applicable'] === 'Yes') {
    $('input[name="editisGstApplicable"][value="Yes"]').prop("checked", true);
    $('#editgstPercentRow').show();
    $('#editgstPercent').val(data[0]['gst_percent']);
} else {
    $('input[name="editisGstApplicable"][value="No"]').prop("checked", true);
    $('#editgstPercentRow').hide();
    $('#editgstPercent').val('');
}

        // Loop for service descriptions
        for (var key in data[1]) {
            $('.editserviceDescTd').append('<div class="row"><div class="col-sm-12 form-group float-label-control">\n\
                <label for = "" > Service Description </label>\n\
                <input type="text" name = "serviceDescription" rows = "2" class = "form-control form-group serviceDescription" id = "serviceDescription" placeholder = "Service Description" value="' + data[1][key]['service_desc'] + '">\n\
                <input type="hidden" class="serviceDescId" id="serviceDescId" name="serviceDescId" value="' + data[1][key]['service_id'] + '">\n\
                </div></div > ');
        }

        // Loop for bank abbreviations
        for (var key in data[2]) {
            $('.editConsultAbbrvation').append('<div class="newAbbrRowParent"><div class="newAbbrRow col-sm-10 form-group float-label-control ">\n\
                <label>Bank Abbreviation</label>\n\
                <input type="text" class="editbankAbbreviation" id="editbankAbbreviation" name="editbankAbbreviation" placeholder="Bank Abbreviation" value="' + data[2][key]['bank_abbr'] + '">\n\
                </div><div class="col-sm-2 form-group deleteNewConsultAbbrParent float-label-control">\n\
                <a class="deleteNewConsultAbbr" style="float: right;"><i class="fa fa-remove"></i></a>\n\
                <input type="hidden" class="editAbbrId" id="editAbbrId" name="editAbbrId" value="' + data[2][key]['id'] + '"></div></div>');
        }

        $('#editFirm').modal("show");
        $("input").trigger("change");
        $('textarea').trigger('change');
    },
    error: function (data, status, err) {
        alert(status);
    }
});

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