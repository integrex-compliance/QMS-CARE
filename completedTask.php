<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<div id="wrapper" class="active">  
    <!-- Sidebar -->
    <!-- Sidebar -->
    <?php
    $pageIdentifier = "completedTask";
    include_once './header.php';
    include_once './dbConnect.php';

    if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
        if ($_SESSION['role'] == "Main Admin" || $_SESSION['role'] == "Admin" || $_SESSION['role'] == 'task manager') {
            $allTask = "SELECT tm.id, userid, task, comment, date, status, update_date, complete_date, us.user_name, abbreviation, email_id, role FROM task_manager tm left join user us on tm.userid = us.user_id where status = 'Completed' and tm.is_deleted = 'N' ORDER BY update_date DESC";
        } else {
            $allTask = "SELECT tm.id, userid, task, comment, date, status, update_date, complete_date, us.user_name, abbreviation, email_id, role FROM task_manager tm left join user us on tm.userid = us.user_id where us.user_id = :id and status = 'Completed' and tm.is_deleted = 'N' ORDER BY update_date DESC";
        }
        $statement = $db->prepare($allTask);
        $statement->bindParam(":id", $_SESSION['id']);
        $statement->execute();
        $listOfTask = $statement->fetchAll();
//        print_r($listOfTask);

        $allUsers = "SELECT * FROM `user` where is_deleted = '0'";
        $statement = $db->prepare($allUsers);
        $statement->execute();
        $listOfUsers = $statement->fetchAll();
//        print_r($listOfTask);

        $userResp = "SELECT responsibility FROM user WHERE user_id = :id";
        $statement = $db->prepare($userResp);
        $statement->bindParam(":id", $_SESSION['id']);
        $statement->execute();
        $responsibility = $statement->fetch();
    }
    ?>
    
    <div id="page-content-wrapper" >        
        <!-- Keep all page content within the page-content inset div! -->
        <div class="page-content inset">
            <div class="row">
                <div class="col-md-12">
                    <div class="container" style="margin-top: 20px; ">
                        <div class="row" style="margin:5px;padding:5px;padding-top:10px; padding-left:20px; ">                                        
                            <div class="col-sm-3 float-label-control custom-float-label-control">
                                <select name="usertype" class="form-control usertype" id="use-type">
                                    <option value="">Select User</option>
                                    <?php foreach ($listOfUsers as $user) { ?>
                                        <option value="<?php echo $user['user_name'] ?>"><?php echo $user['user_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-3 float-label-control custom-float-label-control">
                                <input type="text" class="filterFromDate" id="filterFromDate" name="filterFromDate" placeholder="Search From Date" value="">
                            </div>
                            <div class="col-sm-3 float-label-control custom-float-label-control">
                                <input type="text" class="filterToDate" id="filterToDate" name="filterToDate" placeholder="Search To Date" value="">
                            </div>
                            <div class="col-sm-3 ">
                                <button type="" class="btn btn-info createNewTask" style="float: right; margin-left: 5px;">New Task</button>
                                <button type="" class="btn btn-info myresponsibility" data-val="<?php echo $responsibility["responsibility"]; ?>" style="float: right; ">My Responsibility</button>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-12 table-responsive" style="padding-left:5px;">
                                <table class="table table-bordered table-hover table-sortable table-striped" id="tab_completed_task">
                                    <thead>
                                        <tr>
                                            <?php if (Utitlity::checkForMainAdmin() || Utitlity::checkForUserAdmin()) { ?>
                                                <th class="">
                                                    <small><i class="fa fa-trash-o"></i></small>
                                                </th>
                                            <?php } ?>
                                            <th class="">
                                                <small><a>User</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Date</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Task</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Comment</a></small>
                                            </th>
                                            <th class="">
                                                <small><a>Status</a></small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($listOfTask as $task) { ?>
                                            <tr >
                                                <?php if (Utitlity::checkForMainAdmin() || Utitlity::checkForUserAdmin()) { ?>
                                                    <td data-name="name" style="width:40px ; border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; border-left: 1px solid #ffffff">                                            
                                                        <small><span><?php echo $task['user_name'] ?></span>
                                                            <a class="deleteTask" style="float: right; color: #999; padding-right: " data-id="<?php echo $task['id'] ?>" title="Delete Task"> <i class="fa fa-trash-o"></i></a> 
                                                        </small>
                                                    </td>
                                                <?php } ?>
                                                <?php
                                                $userview = "";
                                                if (Utitlity::checkForMainAdmin() || Utitlity::checkForUserAdmin()) {
                                                    $userview = "";
                                                } else {
                                                    $userview = "readonly";
                                                }
                                                ?>
                                                <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <?php if (Utitlity::checkForMainAdmin() || Utitlity::checkForUserAdmin()) { ?>
                                                        <div class="table-edit-control">                                                                
                                                            <select class="taskHandler" id="taskHandler" name="taskHandler" data-row="<?php echo $task['id'] ?>" <?php echo $userview ?>>
                                                                <option value="<?php if (isset($task['user_name'])) echo $task['user_name'] ?>" ><?php
                                                                    if (isset($task['user_name']) && !empty($task['user_name'])) {
                                                                        echo $task['user_name'];
                                                                    } else {
                                                                        echo "Select ";
                                                                    }
                                                                    ?> </option>
                                                                <?php foreach ($listOfUsers as $users) { ?>
                                                                    <option value="<?php echo $users["user_id"] ?>"><?php echo $users["user_name"] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div> 
                                                    <?php } else { ?>
                                                        <small><a data-id="<?php echo $task['userid'] ?>" class="customerAccount"><?php echo $task['user_name'] ?></a></small>
                                                    <?php } ?>
                                                </td>
                                                <td data-name="sel" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff; text-align: center">
                                                    <span><?php echo getFlatDate($task['update_date']); ?>~</span><small><?php echo simpleDateConversion($task['update_date']); ?></small>
                                                </td>
                                                <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">                                            
                                                    <div class="table-edit-control">                                                                
                                                        <textarea rows="3" class="autosavecomments saveChanges taskname" data-row="<?php echo $task['id'] ?>" ><?php if (isset($task['task'])) echo $task['task'] ?></textarea>
                                                    </div>                                           
                                                </td>
                                                <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <div class="table-edit-control">                                                                
                                                        <textarea rows="3" class="autosavecomments saveChanges customComments" data-row="<?php echo $task['id'] ?>" ><?php if (isset($task['comment'])) echo $task['comment'] ?></textarea>
                                                    </div>
                                                </td>
                                                <td data-name="mail" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                                    <small style="font-size: 12"><?php if ($task['complete_date'] && $task['status'] == "Completed") echo simpleDateConversion($task['complete_date']); ?></small>
                                                    <div class="table-edit-control">                                                                
                                                        <select class="taskstatus" id="taskstatus" name="taskstatus" data-row="<?php echo $task['id'] ?>" value="<?php echo $task['status'] ?>" >
                                                            <option value="<?php if (isset($task['status'])) echo $task['status'] ?>" ><?php
                                                                if (isset($task['status']) && !empty($task['status'])) {
                                                                    echo $task['status'];
                                                                } else {
                                                                    echo "Select ";
                                                                }
                                                                ?> </option>                                                   
                                                            <option value="Pending">Pending</option>
                                                            <option value="Completed">Completed</option>
                                                        </select>
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

<div id="delete-customer" style="display: none">
    <div id="loading-div" class="ui-corner-all">
        <span style="font-size:30pt;"><i style="padding:5px;" class="fa fa-warning text-warning"></i></span>
        <div> 
            <span style="font-size:11pt;padding:10px" class="text-primary meeting-cancel-confirm-message" >Are you sure that you want to delete users?</span>
        </div>
        <div style="padding:3px;">
            <button title="Click here to Restore"  class="btn btn-primary btn-round btn-round-sm btn-sm delete-confirm">Ok</button>
            <button title="Click here to cancel" class="btn btn-danger  btn-round btn-round-sm btn-sm delete-canceled">Cancel</button>
        </div>
    </div>
</div>

<style>
    .custom-float-label-control {
        margin-bottom: 0em;
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

    .unselectable {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>

<script type="text/javascript">

    function compareMinDates(filterDate, rowDate) {
        var filterDateSplit = filterDate.split(".");
        var rowDateSplit = rowDate.split(".");
        if (filterDateSplit[2] < rowDateSplit[2]) {
            return true;
        } else if (filterDateSplit[2] == rowDateSplit[2]) {
            if (filterDateSplit[1] < rowDateSplit[1]) {
                return true;
            } else if (filterDateSplit[1] == rowDateSplit[1]) {
                if (filterDateSplit[0] < rowDateSplit[0]) {
                    return true;
                } else if (filterDateSplit[0] == rowDateSplit[0]) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    function compareMaxDates(filterDate, rowDate) {
        var filterDateSplit = filterDate.split(".");
        var rowDateSplit = rowDate.split(".");
        //year comparison
        if (filterDateSplit[2] > rowDateSplit[2]) {
            return true;
        } else if (filterDateSplit[2] == rowDateSplit[2]) {
            if (filterDateSplit[1] > rowDateSplit[1]) {
                return true;
            } else if (filterDateSplit[1] == rowDateSplit[1]) {
                if (filterDateSplit[0] > rowDateSplit[0]) {
                    return true;
                } else if (filterDateSplit[0] == rowDateSplit[0]) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var serviceType = $(".usertype").val();
                var fromDate = $(".filterFromDate").val();
                var toDate = $(".filterToDate").val();
                var isServiceType = false;
                var isfromDate = false;
                var isToDate = false;
                //            alert(serviceType);
                if (serviceType == "" && fromDate == "" && toDate == "") {
                    return true;
                }
                if (serviceType != "") {
                    var servieTypeStr = data[0] || ""; // use data for the age column
                    //            alert(servieTypeStr);
                    if (servieTypeStr.toUpperCase().trim() == serviceType.toUpperCase().trim()) {
                        //                alert("test");
                        isServiceType = true;
                    }
                } else {
                    isServiceType = true;
                }
                var dateValue = data[2] || "";
                var dateSplit = dateValue.split("~");
                //            alert(dateSplit[1]);
                //            alert(fromDate);
                if (fromDate != "") {
//                      alert(dateValue);
                    isfromDate = compareMinDates(fromDate, dateSplit[1]);
                } else {
                    isfromDate = true;
                }
                if (toDate != "") {
                    isToDate = compareMaxDates(toDate, dateSplit[1]);
                } else {
                    isToDate = true;
                }
                finalResult = (isServiceType && isfromDate && isToDate);
                return finalResult;
            });

    $(document).ready(function () {

        $(".myresponsibility").click(function () {
//            alert($(this).attr("data-val"));
            $('.responsibilitytext').html($(this).attr("data-val"));
            $("#showResponsibility").modal("show");
        });

        $('#filterFromDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $("#filterFromDate").keydown(function (e) {
//            e.preventDefault();
        });
        
        $('#filterToDate').datepicker({
            minDate: '-100Y',
            maxDate: '+100Y',
            changeYear: true,
            changeMonth: true,
            yearRange: "-20:+100",
            dateFormat: 'dd.mm.yy'
        });

        $("#filterToDate").keydown(function (e) {
//            e.preventDefault();
        });

        $('.usertype,.filterFromDate,.filterToDate').change(function () {
            // var fromDate = $(".filterFromDate").val();
            var serviceType = $(".usertype").val();
//            alert($(".filterFromDate").val());
            table.draw();
        });

        $(".createNewTask").click(function () {
//            alert("in");
            $("#addNewTask").modal("show");
        });

        $('.deleteTask').click(function () {
            var taskid = $(this).attr('data-id');
//            alert("customer id "+customerid);
            $("#delete-customer").show();
            $('.delete-confirm').click(function () {
                var url = 'addTask.php';
                var form = $('<form action="' + url + '" method="post">' +
                        '<input type="hidden" name="taskdlt" value="' + taskid + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            });
            $('.delete-canceled').click(function () {
                $('#delete-customer').hide();
            });
        });

        $('.taskname').change(function (e) {
//            alert($(this).val());
            var colVal = $(this).val();
            $.ajax({
                url: "addTask.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    rowid: $(this).attr('data-row'),
                    columnVal: colVal,
                },
                error: function (xhr, status, error) {
//                    alert(status);
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
//            saveData($(this));
        });

        $('.taskHandler').change(function () {
//        alert($(this).attr('data-service'));
//        alert($(this).val());
            var id = $(this).attr('data-row');
            var colVal = $(this).val();
            $.ajax({
                url: "addTask.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    rowID: id,
                    user: colVal,
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                }
            });
        });

        $('.taskstatus').change(function () {
//        alert($(this).attr('data-service'));
//        alert($(this).val());
            var id = $(this).attr('data-row');
            var colVal = $(this).val();
            $.ajax({
                url: "addTask.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    row: id,
                    status: colVal,
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                },
                error: function (xhr, status, error) {
//                    alert("There seems to be issue saving data!");
                }
            });
        });

        $(".autosavecomments").animate({scrollTop: $('.autosavecomments').prop("scrollHeight")}, 1000);

        $('.customComments').bind('keypress', function (e) {
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

        $('.customComments').change(function (e) {
//            alert($(this).val());
            var colVal = $(this).val();
            $.ajax({
                url: "addTask.php",
                type: "GET",
                dataType: 'JSON',
                data: {
                    id: $(this).attr('data-row'),
                    columnValue: colVal,
                },
                error: function (xhr, status, error) {
//                    alert(status);
                },
                success: function (data) {
//                    alert(data); 
//                    (moment(object[0]['start_time']).format('DD-MMM-YYYY'))
//                    $('.serviceDate').val(data['next_service_date']);
//                    $('.serviceDate').val(moment(data['next_service_date']).format('DD.MM.YYYY'));
//                    $('.nextService').val(data['next_service_type']);
                }
            });
//            saveData($(this));
        });

        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");

            $(".container").toggleClass("active");
            table.draw();
        });

        var table = $('#tab_completed_task').DataTable({
//            "autoWidth":false,
//            "scrollY": "450px",
            "sScrollX": "100%",
            "scrollY": '70vh',
            "scrollCollapse": true,
            "paging": false,
            "columnDefs": [
                {
                    "width": "5%",
                    "targets": 0
                },
                {
                    "width": "10%",
                    "targets": 1
                },
                {
                    "width": "5%",
                    "targets": 2
                },
                {
                    "width": "30%",
                    "targets": 3
                },
                {
                    "width": "30%",
                    "targets": 4
                },
                {
                    "width": "10%",
                    "targets": 5
                }]
        });
//        alert("before load");
        table.columns.adjust().draw();
    });
</script>