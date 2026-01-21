<div id="wrapper" class="active" >
    <!-- Sidebar -->
    <!-- Sidebar -->

    <?php
    $pageIdentifier = "To Marketing";
    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {

        $today = date('Y-m-d');
        $date10 = strtotime('-10 months');
        $tenMago = date('Y-m-d', $date10);
        $date12 = strtotime('-12 months');
        $twelveMago = date('Y-m-d', $date12);
        
//        $sa1_due = "SELECT cd.customer_id, company_name, next_service_type, next_service_date, sv.service_id, add_service_to FROM customer_details cd join services sv ON cd.customer_id = sv.customer_id  WHERE sv.service_id = (select max(tmp.service_id) from services tmp where tmp.customer_id = cd.customer_id) AND next_service_type = 'SA-1' AND sv.add_service_to != 'Under Marketing' AND (next_service_date BETWEEN '" . $twoMago . "' AND '" . $today . "')";
        $sa1_due = "SELECT cd.customer_id, company_name, next_service_type, certification_date, next_service_date, sv.service_id, service_type, add_service_to FROM customer_details cd join services sv ON cd.customer_id = sv.customer_id WHERE sv.service_id = (select max(tmp.service_id) from services tmp where tmp.customer_id = cd.customer_id) AND (sv.service_type = 'CA' OR sv.service_type = 'RC') AND (sv.add_service_to != 'Under Marketing' AND sv.add_service_to != 'Under Working' AND sv.add_service_to != 'Idle' AND sv.add_service_to != 'Discontinue' AND sv.add_service_to != 'Terminate') AND (certification_date BETWEEN '" . $twelveMago . "' AND '" . $tenMago . "')";
        $statement = $db->prepare($sa1_due);
        $statement->execute();
        $sa1_due_service = $statement->fetchAll();
        
        $date22 = strtotime('-22 months');
        $twentytwoMago = date('Y-m-d', $date22);
        $date24 = strtotime('-24 months');
        $twentyfourMago = date('Y-m-d', $date24);

        $sa2_due = "SELECT cd.customer_id, company_name, next_service_type, certification_date, next_service_date, sv.service_id, service_type, add_service_to FROM customer_details cd join services sv ON cd.customer_id = sv.customer_id  WHERE sv.service_id = (select max(tmp.service_id) from services tmp where tmp.customer_id = cd.customer_id) AND sv.service_type = 'SA-1' AND (sv.add_service_to != 'Under Marketing' AND sv.add_service_to != 'Under Working' AND sv.add_service_to != 'Idle' AND sv.add_service_to != 'Discontinue' AND sv.add_service_to != 'Terminate') AND (certification_date BETWEEN '" . $twentyfourMago . "' AND '" . $twentytwoMago . "')";
        $statement = $db->prepare($sa2_due);
        $statement->execute();
        $sa2_due_service = $statement->fetchAll();
        
        $date34 = strtotime('-34 months');
        $thirtyfourMago = date('Y-m-d', $date34);
        $date36 = strtotime('-36 months');
        $thirtysixMago = date('Y-m-d', $date36);
        
        $rc_due = "SELECT cd.customer_id, company_name, next_service_type, certification_date, next_service_date, sv.service_id, service_type, add_service_to FROM customer_details cd join services sv ON cd.customer_id = sv.customer_id WHERE sv.service_id = (select max(tmp.service_id) from services tmp where tmp.customer_id = cd.customer_id) AND sv.service_type = 'SA-2' AND (sv.add_service_to != 'Under Marketing' AND sv.add_service_to != 'Under Working' AND sv.add_service_to != 'Idle' AND sv.add_service_to != 'Discontinue' AND sv.add_service_to != 'Terminate') AND (certification_date BETWEEN '" . $thirtysixMago . "' AND '" . $thirtyfourMago . "')";
        $statement = $db->prepare($rc_due);
        $statement->execute();
        $rc_due_service = $statement->fetchAll();

    } ?>

    <style>
        .event-list {
            list-style: none;
            font-family: 'Lato', sans-serif;
            margin: 0px;
            padding: 0px;

        }
        .event-row {
            padding: 5px;
        }

        .event-list > li {
            background-color: rgb(255, 255, 255);
            box-shadow: 0px 0px 5px rgb(51, 51, 51);
            box-shadow: 0px 0px 5px rgba(51, 51, 51, 0.7);
            padding: 0px;
            margin: 0px 0px 0px;
        }
        
        .event-list > li > time {
            cursor: pointer;
            display: inline-block;
            width: 100%;
            color: rgb(255, 255, 255);

            padding: 5px;
            text-align: center;
            text-transform: uppercase;
        }

        .event-list > li > time > span {
            display: none;
        }
        .event-list > li > time > .day {
            display: block;
            font-size: 20pt;
            font-weight: 100;
            line-height: 1;
        }
        .event-list > li time > .month {
            display: block;
            font-size: 18pt;
            font-weight: 900;
            line-height: 1;
        }
        .event-list > li > img {
            width: 100%;
            cursor: pointer;
        }
        .event-list > li > .info {
            padding-top: 5px;
            cursor: pointer;  
        }

        .event-list > li > .info:hover {
            padding-top: 5px;
            cursor: pointer;  
            color: white;
            background-color: rgba(79, 213, 248, 0.2) !important;
        }

        .event-list > li > .info > .desc {
            line-height: 1.5em;
            height: 2em;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            margin: 0px;
        }
        .event-list > li > .info > .other-info {
            padding-left: 5px;
            padding-right: 5px;
        }

        .event-list > li > .info > ul,
        .event-list > li > .side-bar-menu > ul {
            display: table;
            list-style: none;
            margin: 10px 0px 0px;
            padding: 0px;
            width: 100%;
            text-align: center;
        }
        .event-list > li > .side-bar-menu > ul {
            margin: 0px;
        }
        .event-list > li > .info > ul > li,
        .event-list > li > .side-bar-menu > ul > li {
            display: table-cell;
            cursor: pointer;
            color: rgb(30, 30, 30);
            font-size: 11pt;
            font-weight: 300;
            padding: 3px 0px;
        }
        .event-list > li > .info > ul > li > a {
            display: block;
            width: 100%;
            color: rgb(30, 30, 30);
            text-decoration: none;
        } 
        .event-list > li > .side-bar-menu > ul > li {    
            padding: 0px;
        }
        .event-list > li > .side-bar-menu > ul > li > a {
            padding: 3.3px 0px;
        } 

        .event-list > li > .side-bar-menu > ul > li > .action-item-menu {
            padding: 9.8px 0px;
        } 

        .event-list > li > .info > ul > li:hover,
        .event-list > li > .side-bar-menu > ul > li:hover {
            color: rgb(30, 30, 30);
            background-color: rgb(200, 200, 200);
        }
    </style>

    <div id="page-content-wrapper">
        <div class="page-content inset">
            <div class="container" >
                <br>
                <div class="row">
                    
                    <!--SA-1 due customers-->
                    
                    <div class="col-sm-4 main_content" style="">
                        <div class="panel panel-primary" style="min-height: 80px" >
                            <div class="panel-heading text-center" style="background: #053c6d">
                                <h4 style="color: white">SA-1 Due</h4>
                            </div>
                            <?php foreach ($sa1_due_service as $svc1) { ?>
                            <div class="panel-rightbody homePagePanel" id="actionItemDisplayPanel">
                                <div class="row event-row" id="dummyActionItemRow">
                                    <div class="col-xs-12 col-sm-12">
                                        <ul class="event-list">
                                            <li>
                                                <div class="info showActionItem">
                                                    <a class="sent_to_marketing btn btn-default btn-xs btn-info" data-customer="<?php echo $svc1["customer_id"] ?>" data-service="<?php echo $svc1["next_service_type"] ?>" data-svcid="<?php echo $svc1["service_id"] ?>" title="Send To Marketing" style="float: right; margin-right: 10px">To Marketing <i class="fa fa-briefcase"></i></a>
                                                    <p class="desc text-primary customerName" data-customer="<?php echo $svc1["customer_id"] ?>" title="Customer Name" ><?php echo $svc1["company_name"] ?></p>
                                                    <p class="other-info">
                                                        <small title="Due date" class="text-muted"><i style="padding: 3px" class="fa fa-calendar"></i><?php echo simpleDateConversion($svc1['next_service_date']) ?></small>
                                                    </p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <!--SA2 due customers-->
                    
                    <div class="col-sm-4 main_content" style="">
                        <div class="panel panel-primary" style="min-height: 80px" >
                            <div class="panel-heading text-center" style="background: #053c6d">
                                <h4 style="color: white">SA-2 Due</h4>
                            </div>
                            <?php foreach ($sa2_due_service as $svc2) { ?>
                            <div class="panel-rightbody homePagePanel" id="actionItemDisplayPanel">
                                <div class="row event-row" id="dummyActionItemRow">
                                    <div class="col-xs-12 col-sm-12">
                                        <ul class="event-list">
                                            <li>
                                                <div class="info showActionItem">
                                                    <a class="sent_to_marketing btn btn-default btn-xs btn-info" data-customer="<?php echo $svc2["customer_id"] ?>" data-service="<?php echo $svc2["next_service_type"] ?>" data-svcid="<?php echo $svc2["service_id"] ?>" title="Send To Marketing" style="float: right; margin-right: 10px">To Marketing <i class="fa fa-briefcase"></i></a>
                                                    <p class="desc text-primary customerName" data-customer="<?php echo $svc2["customer_id"] ?>" title="Customer Name" ><?php echo $svc2["company_name"] ?></p>
                                                    <p class="other-info">
                                                        <small title="Due date" class="text-muted"><i style="padding: 3px" class="fa fa-calendar"></i><?php echo simpleDateConversion($svc2['next_service_date']) ?></small>
                                                    </p>
                                                </div>                                                
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <!--RC due customers-->
                    
                    <div class="col-sm-4 main_content" style="">
                        <div class="panel panel-primary" style="min-height: 80px" >
                            <div class="panel-heading text-center" style="background: #053c6d">
                                <h4 style="color: white">RC Due</h4>
                            </div>
                            <?php foreach ($rc_due_service as $svcrc) { ?>
                            <div class="panel-rightbody homePagePanel" id="actionItemDisplayPanel">
                                <div class="row event-row" id="dummyActionItemRow">
                                    <div class="col-xs-12 col-sm-12">
                                        <ul class="event-list">
                                            <li>
                                                <div class="info showActionItem">
                                                    <a class="sent_to_marketing btn btn-default btn-xs btn-info" data-customer="<?php echo $svcrc["customer_id"] ?>" data-service="<?php echo $svcrc["next_service_type"] ?>" data-svcid="<?php echo $svcrc["service_id"] ?>" title="Send To Marketing" style="float: right; margin-right: 10px">To Marketing <i class="fa fa-briefcase"></i></a>
                                                    <p class="desc text-primary customerName" data-customer="<?php echo $svcrc["customer_id"] ?>" title="Customer Name"><?php echo $svcrc["company_name"] ?></p>
                                                    <p class="other-info">
                                                        <small title="Due date" class="text-muted"><i style="padding: 3px" class="fa fa-calendar"></i><?php echo simpleDateConversion($svcrc['next_service_date']) ?></small>
                                                    </p>
                                                </div>                                                
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

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
        
        $('.sent_to_marketing').click(function () {
            var customerID = $(this).attr('data-customer');
            var servicetype = $(this).attr('data-service');
            var serviceid = $(this).attr('data-svcid');
//            alert(customerID);
            var url = 'addMarketingService.php';
            var form = $('<form action="' + url + '" method="post">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '<input type="hidden" name="servicetype" value="' + servicetype + '" />' +
                    '<input type="hidden" name="serviceid" value="' + serviceid + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });
        
        $('.customerName').click(function () {
            var customerID = $(this).attr('data-customer');
//            alert(customerID);
            // open popup
            var url = 'customerAccountDetails.php';
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });
        
    });
</script>