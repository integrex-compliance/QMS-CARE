<?php
ob_start();
session_start();
include './head.php';
include 'modal_forms.php';
include 'utility.php';
?>

<head>
    <title>QMS Care </title>
</head>

<script type="text/javascript">
    $(document).ready(function () {
        //disabled copy pest and mouse right click
//        Disable full page
//        $("body").on("contextmenu", function (e) {
//            return false;
//        });

//    //Disable part of page
//    $("#id").on("contextmenu",function(e){
//        return false;
//    });
//
////        $('body').bind('cut copy paste', function (e) {
////            e.preventDefault();
////        });

//    //Disable part of page
//    $('#id').bind('cut copy paste', function (e) {
//        e.preventDefault();
//    });

        $('.addAccountCss').click(function () {
            $('.newCustomerContact').empty();
            $(".customerAdd").modal("show");
        });

        $('.passwordChange').click(function () {
            $('#changeUserPassword').modal("show");
        });
        $minlen = 0;
        $usertype = '<?php echo Utitlity::checkForOfficeStaff() || Utitlity::checkForUserMarketing() ?>';
        if ($usertype == 1) {
            $minlen = 3;
        } else {
            $minlen = 1;
        }
//alert($usertype+" -> "+$minlen);
        $('.searchcustomer').autocomplete({
            source: function (request, response) {
//                alert($('.cert-body').val());
                $.ajax({
                    url: "autoSearch.php",
                    dataType: "json",
                    type: 'GET',
                    data: {
                        customersearch: $('.searchcustomer').val(),
                    },
                    success: function (data) {
//                        alert(data);
                        response(data);
                    },
                    error: function (data, status, err) {
//                        alert(status);
                    }
                });
            },
//            if($usertype){
            minLength: $minlen,
//            } else {
//                minLength: 1,
//            }

            select: function (event, ui) {
//                alert(ui.item.id);
                localStorage['visited'] = ui.item.id;
                var url = 'customerAccountDetails.php'
                var form = $('<form action="' + url + '" method="get">' +
                        '<input type="hidden" name="customerid" value="' + ui.item.id + '" />' +
                        '</form>');
                $('body').append(form);
                form.submit();
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });
        
        $(".addGeneralNote").click(function () {
            $.ajax({
                url: "autoSearch.php",
                dataType: "json",
                type: 'GET',
                data: {
                    noteid: "1",
                },
                success: function (data) {
//                    console.log(data);
//                    alert(data);
                    $(".generalNoteAdd").val(data["note_data"]);

                },
                error: function (data, status, err) {
                    alert(status);
                }
            });
//            alert("in");
            $("#addGeneralNote").modal("show");
        });

//        $(".generalNoteAdd").keyup(function (){
////            alert("in");
//        });

        $('.generalNoteAdd').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();

                var inputVal = $(this).val();
                if (inputVal != "" && inputVal != null)
                    inputVal = $(this).val() + "\n";

                var appendText = "<?php echo date("d.m.Y") . " - " . $_SESSION['abbrevation'] . " - " ?>";
                // Enter pressed... do anything here...
                $(this).val(inputVal + appendText);
            }
//            alert("in");
            $.ajax({
                url: "autoSearch.php",
                dataType: "json",
                type: 'GET',
                data: {
                    noteData: $(".generalNoteAdd").val(),
                },
                success: function (data) {
//                    console.log(data);
//                    alert(data);
//                    $(".generalNoteAdd").val(data["note_data"]);
                },
                error: function (data, status, err) {
//                    alert(status);
                }
            });
        });
        
    });
</script>
<!--<div id="wrapper" class=" active">-->

<?php
if (!Utitlity::checkIfLoggedIn()) {
    header("Location: index.php?errorCode=101");
} else if (isset($pageIdentifier) && $pageIdentifier == "Home Page") {
    $addClass = "active";
    if (!(Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Add User") {
    $addUserClass = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Master Data") {
    $addMasterClass = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "New enq Page") {
    $addMarketingClass = "active";
    $addNewEnqClass = "active";
    if (!(Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Discontinue client") {
    $addMarketingClass = "active";
    $adddisClass = "active";
    if (!(Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "JD enq Page") {
    $addMarketingClass = "active";
    $addJDClass = "active";
    if (!(Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "TC enq Page") {
    $addMarketingClass = "active";
    $addTCClass = "active";
    if (!(Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "RC due Page") {
    $addMarketingClass = "active";
    $addRcDueClass = "active";
    if (!(Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "SA due Page") {
    $addMarketingClass = "active";
    $addSaDueClass = "active";
    if (!(Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Working Page") {
    $addWorkingClass = "active";
    if (!(Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForUserConsultant() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "NSICMPCB Page") {
    $addNsicMpcbClass = "active";
    if (!(Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForUserConsultant() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Idle Page") {
    $addidleClass = "active";
    if (!(Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForUserConsultant() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Finance Page") {
    $addFinanceClass = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForUserFinance() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Payment rcv Page") {
    $addPayRcv = "active";
    if (!Utitlity::checkForMainAdmin()) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "TDS Finance Page") {
    $tdspay = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForUserFinance() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "TDS rcv list") {
    $tdsrcv = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForUserFinance() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Payloss Page") {
    $payloss = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForUserFinance() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Marketing Setup") {
    $addMarketingSetupClass = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Invoices") {
    $addInvoiceClass = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserConsultant() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Recycle") {
    $addRecycleClass = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Auditpermonth") {
    $addAuditClass = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "completedTask") {
    $completetask = "active";
} else if (isset($pageIdentifier) && $pageIdentifier == "To Marketing") {
    $tomarketing = "active";
} else if (isset($pageIdentifier) && $pageIdentifier == "TaskMGMT") {
    $pendingtask = "active";
} else if (isset($pageIdentifier) && $pageIdentifier == "Mktg setup status") {
    $addmktgsetupstatusClass = "active";
} else if (isset($pageIdentifier) && $pageIdentifier == "Marketing Setup Received") {
    $addmktgsetupreceivedClass = "active";
} else if (isset($pageIdentifier) && $pageIdentifier == "Consultant Page") {
    $addConsultantClass = "active";
} else if (isset($pageIdentifier) && $pageIdentifier == "Consultant devlopment Page") {
    $addConsultantDevClass = "active";
} else if (isset($pageIdentifier) && $pageIdentifier == "Handel By Page") {
    $addHandelbyClass = "active";
} else if (isset($pageIdentifier) && $pageIdentifier == "Contact Info") {
    $addcontactinfoClass = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "Qoutation") {
    $addQuotationClass = "active";
    if (!(Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserConsultant() || Utitlity::checkForOfficeBDM())) {
        header("Location: invalidAccess.php?errorCode=102");
    }
}

function simpleDateConversion($inputStr) {
    if ($inputStr === "") {
        return "";
    }
    $strSplit = explode("-", $inputStr);
    if (sizeOf($strSplit) > 1)
        return $strSplit[2] . "." . $strSplit[1] . "." . $strSplit[0];
    else
        return $inputStr;
}

function startsWith($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function getFlatDate($inputStr) {
    if ($inputStr === "") {
        return "";
    }
    $strSplit = explode("-", $inputStr);
    if (sizeOf($strSplit) > 1)
        return $strSplit[0] . "" . $strSplit[1] . "" . $strSplit[2];
    else
        return $inputStr;
}
?>
<div id="sidebar-wrapper" class="hidden-xs" >
    <ul id="sidebar_menu" class="sidebar-nav" >
        <li class="sidebar-brand"><a class="" id="menu-toggle" href="#">QMS<span id="main_icon" class="glyphicon glyphicon-align-justify"></span></a></li>
    </ul>
    <ul class="sidebar-nav" id="sidebar">
        <?php if ($_SESSION['username']=="Harish Ajabe") { ?>
	<!--<li><a href="homePage_Test.php" class="animate <?php echo $addClass ?>">CUSTOMERS(Test)<i class="sub_icon fa fa-building fa-2x" style="vertical-align:middle"></i></a></li>-->
        <?php } ?>
		<?php if (Utitlity::checkForMainAdmin()) { ?>
            <!--<li><a href="homePage.php" class="animate <?php echo $addClass ?>">CUSTOMERS<i class="sub_icon fa fa-building fa-2x" style="vertical-align:middle"></i></a>-->
                <li><a href="homePage_Test.php" class="animate <?php echo $addClass ?>">CUSTOMERS<i class="sub_icon fa fa-building fa-2x" style="vertical-align:middle"></i></a>     
            </li>
        <?php } ?>
        <?php if ($_SESSION['username']=="Harish Ajabe") { ?>
            <!--<li><a href="newEnquiry_Test.php" class="animate <?php echo $addClass ?>">REFERED ENQUIRY(Test)<i class="sub_icon fa fa-building fa-2x" style="vertical-align:middle"></i></a></li>-->
        <?php } ?>
		<?php if (Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="newEnquiry_Test.php" class="animate <?php echo $addNewEnqClass ?>">REFERED ENQUIRY<i class="sub_icon fa fa-building fa-2x" style="vertical-align:middle"></i></a></li>
            <!--<li><a href="newEnquiry.php" class="animate <?php echo $addNewEnqClass ?>">REFERED ENQUIRY<i class="sub_icon fa fa-user-plus fa-2x" style="vertical-align:middle"></i></a></li>-->
            <li><a href="justdialEnquiry.php" class="animate <?php echo $addJDClass ?>">JD ENQUIRY<i class="sub_icon fa fa-phone fa-2x" style="vertical-align:middle"></i></a></li>
            <li><a href="telecallEnquiry.php" class="animate <?php echo $addTCClass ?>">TEL ENQUIRY<i class="sub_icon fa fa-phone fa-2x" style="vertical-align:middle"></i></a></li>
            <li><a href="saDuePage.php" class="animate <?php echo $addSaDueClass ?>">SA DUE<i class="sub_icon fa fa-briefcase fa-2x" style="vertical-align:middle"></i></a></li>
            <li><a href="rcDue.php" class="animate <?php echo $addRcDueClass ?>">RC DUE<i class="sub_icon fa fa-briefcase fa-2x" style="vertical-align:middle"></i></a></li>
            <li><a href="disclient.php" class="animate <?php echo $adddisClass ?>">DISCONTINUED CLI.<i class="sub_icon fa fa-briefcase fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } ?>
		<?php if ($_SESSION['username']=="Harish Ajabe") { ?>
            <!--<li><a href="workingDetails_Test.php" class="animate <?php echo $addClass ?>">WORKING PROJECTS(Test)<i class="sub_icon fa fa-building fa-2x" style="vertical-align:middle"></i></a></li>-->
        <?php } ?>
        <?php if (Utitlity::checkForUserMarketing() || Utitlity::checkForUserConsultant() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="workingDetails_Test.php" class="animate <?php echo $addWorkingClass ?>">WORKING PROJECTS<i class="sub_icon fa fa-building fa-2x" style="vertical-align:middle"></i></a></li>
            <!--<li><a href="workingDetails.php" class="animate <?php echo $addWorkingClass ?>">WORKING PROJECTS<i class="sub_icon fa fa-tasks fa-2x" style="vertical-align:middle"></i></a></li>-->
            <li><a href="nsicmpcb.php" class="animate <?php echo $addNsicMpcbClass ?>">NSICMPCB PROJECTS<i class="sub_icon fa fa-tasks fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } ?>
        <?php if (Utitlity::checkForUserMarketing() || Utitlity::checkForUserConsultant() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="idleclient.php" class="animate <?php echo $addidleClass ?>">IDLE PROJECTS<i class="sub_icon fa fa-tasks fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } ?>
        <?php if (Utitlity::checkForUserFinance() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="financeDetails.php" class="animate <?php echo $addFinanceClass ?>">PAYMENT FOLLOW UP<i class="sub_icon fa fa-money fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } if (Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="paymentLoss.php" class="animate <?php echo $payloss ?>">PAYMENT LOSS<i class="sub_icon fa fa-money fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } if (Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="tdsDetails.php" class="animate <?php echo $tdspay ?>">TDS<i class="sub_icon fa fa-money fa-2x" style="vertical-align:middle"></i></a></li>
            <li><a href="tdsReceived.php" class="animate <?php echo $tdsrcv ?>">TDS RECEIVED<i class="sub_icon fa fa-money fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } if (Utitlity::checkForMainAdmin()) { ?>
            <li><a href="paymentReceived.php" class="animate <?php echo $addPayRcv ?>">PAYMENT RECEIVED<i class="sub_icon fa fa-money fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } if (Utitlity::checkForMainAdmin() || Utitlity::checkForUserAdmin()) { ?>
            <li><a href="mktgSetupStatus.php" class="animate <?php echo $addmktgsetupstatusClass ?>">MKTG ANALYLIS<i class="sub_icon fa fa-briefcase fa-2x" style="vertical-align:middle"></i></a></li>
            <li><a href="marketSetupDetails.php" class="animate <?php echo $addMarketingSetupClass ?>">MKTG PENDING<i class="sub_icon fa fa-briefcase fa-2x" style="vertical-align:middle"></i></a></li>            
            <li><a href="setupReceived.php" class="animate <?php echo $addmktgsetupreceivedClass ?>">MKTG RECEIVED<i class="sub_icon fa fa-briefcase fa-2x" style="vertical-align:middle"></i></a></li>          
        <?php } ?>
        <?php if (Utitlity::checkForMainAdmin()) { ?>
            <li><a href="masterData.php" class="animate <?php echo $addMasterClass ?> ">MASTER CREATOR<i class="sub_icon fa fa-wrench  fa-2x" style="vertical-align:middle"></i></a></li>
            <li><a href="addUsers.php" class="animate <?php echo $addUserClass ?>">ADD USER<i class="sub_icon fa fa-user-plus fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } ?>
        <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserConsultant() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="invoiceList.php" class="animate <?php echo $addInvoiceClass ?>">INVOICE<i class="sub_icon fa fa-inr fa-2x" style="vertical-align:middle"></i></a></li>
            <li><a href="quotationList.php" class="animate <?php echo $addQuotationClass ?>">QUOTATION<i class="sub_icon fa fa-quote-left fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } ?>
        <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeStaff()) { ?>
            <li><a href="auditPage.php" class="animate <?php echo $addAuditClass ?>">MONTH WISE AUDIT<i class="sub_icon fa fa-search fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } if (Utitlity::checkForMainAdmin()) { ?>
            <li><a href="recycleCustomers.php" class="animate <?php echo $addRecycleClass ?>">RECYCLE CUST<i class="sub_icon fa fa-trash fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) { ?>
            <!--<li><a href="mktgSetupStatus.php" class="animate <?php echo $addmktgsetupstatusClass ?>">MKTG SETUP STATUS<i class="sub_icon fa fa-briefcase fa-2x" style="vertical-align:middle"></i></a></li>-->
        <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) { ?>
            <li><a href="consultantPage.php" class="animate <?php echo $addConsultantClass ?>">CONSULTANT<i class="sub_icon fa fa-user fa-2x" style="vertical-align:middle"></i></a></li>
            <li><a href="handelbyPage.php" class="animate <?php echo $addHandelbyClass ?>">HANDLE BY<i class="sub_icon fa fa-hand-o-right fa-2x" style="vertical-align:middle"></i></a></li>        
        <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="consultantDev.php" class="animate <?php echo $addConsultantDevClass ?>">CONSOL DEV<i class="sub_icon fa fa-user fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) { ?>
            <li><a href="contactinfo.php" class="animate <?php echo $addcontactinfoClass ?>">CONTACT INFO<i class="sub_icon fa fa-building fa-2x" style="vertical-align:middle"></i></a></li>
        <?php } ?>            
        <li><a href="taskMgmt.php" class="animate <?php echo $pendingtask ?>">PENDING TASKS<i class="sub_icon fa fa-calendar-plus-o fa-2x" style="vertical-align:middle"></i></a></li>
        <li><a href="completedTask.php" class="animate <?php echo $completetask ?>">COMPLETED TASKS<i class="sub_icon fa fa-calendar-check-o fa-2x" style="vertical-align:middle"></i></a></li>
        <li><a href="servicedue.php" class="animate <?php echo $tomarketing ?>">SERVICE DUE<i class="sub_icon fa fa-calendar-check-o fa-2x" style="vertical-align:middle"></i></a></li>
        <!--        <li>
                    <div class="dropdown">
                        <span class="animate dropdown-toggle" data-toggle="dropdown" style="color: white">dropdown menu </span>
                        <ul class="dropdown-menu" style="background-color: #353535">
                            <li><a href="servicedue.php" class="animate <?php echo $tomarketing ?>">SERVICE DUE<i class="sub_icon fa fa-calendar-check-o fa-2x" style="vertical-align:middle"></i></a></li>
                            <li><a href="#">CSS</a></li>
                            <li><a href="#">JavaScript</a></li>
                        </ul>
                    </div>
                </li>
        <li>
                    <div class="dropdown">
                        <span class="animate dropdown-toggle" data-toggle="dropdown" style="color: white">dropdown menu </span>
                        <ul class="dropdown-menu" style="background-color: #353535">
                            <li><a href="#">HTML</a></li>
                            <li><a href="#">CSS</a></li>
                            <li><a href="#">JavaScript</a></li>
                        </ul>
                    </div>
                </li>-->
    </ul>
</div>
<!--vertile nav bar-->

<div class="float-button-wrapper" style="">
    <a class="addAccountCss" style="text-align:center" title="Add new client">
        <span class=" pull-right glyphicon glyphicon-plus "></span>
    </a>    
</div>

<nav class="navbar navbar-fixed-top navbar-bootsnipp animate " role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header ">
            <button type="button" class="navbar-toggle " data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only ">Toggle navigation</span>
                <span class="icon-bar "></span>
                <span class="icon-bar "></span>
                <span class="icon-bar "></span>
            </button>            
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse " id="bs-example-navbar-collapse-1">
            <!--<div class="span12">-->

            <!--</div>-->
            <ul class="nav navbar-nav navbar-right ">

                <!--Adding hidden menu items here -->
                <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                    <li class="visible-xs"><a href="homePage.php" class="animate //<?php echo $addClass ?>">CUSTOMERS</a></li>
                <?php } ?>
                <?php if (Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                    <li class="visible-xs"><a href="newEnquiry.php" class="animate //<?php echo $addWorkingClass ?>">NEW ENQUIRY</a></li>
                    <li class="visible-xs"><a href="saDuePage.php" class="animate<?php echo $addSaDueClass ?>">SA DUE</a></li>
                    <li class="visible-xs"><a href="rcDue.php" class="animate<?php echo $addRcDueClass ?>">RC DUE</a></li>
                <?php } if (Utitlity::checkForUserConsultant() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                    <li class="visible-xs"><a href="workingDetails.php" class="animate //<?php echo $addWorkingClass ?>">WORKING PROJECTS</a></li>
                <?php } if (Utitlity::checkForUserFinance() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                    <li class="visible-xs"><a href="financeDetails.php" class="animate //<?php echo $addFinanceClass ?>">PAYMENT FOLLOW UP</a></li>
                <?php } if (Utitlity::checkForMainAdmin()) { ?>
                    <li class="visible-xs"><a href="paymentReceived.php" class="animate //<?php echo $addPayRcv ?>">PAYMENT RECEIVED</a></li>
                <?php } if (Utitlity::checkForMainAdmin() || Utitlity::checkForUserAdmin()) { ?>
                    <li class="visible-xs"><a href="marketSetupDetails.php" class="animate //<?php echo $addMarketingSetupClass ?>">MARKETING SETUP</a></li>
                <?php } if (Utitlity::checkForMainAdmin()) { ?>
                    <li class="visible-xs"><a href="masterData.php" class="animate //<?php echo $addMasterClass ?> ">MASTER CREATOR</a></li>
                    <li class="visible-xs"><a href="addUsers.php" class="animate //<?php echo $addUserClass ?>">ADD USER</a></li>
                <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                    <li class="visible-xs"><a href="invoiceList.php" class="animate //<?php echo $addInvoiceClass ?>">INVOICE</a></li>
                    <li class="visible-xs"><a href="quotationList.php" class="animate //<?php echo $addQuotationClass ?>">QUOTATION</a></li>
                    <li class="visible-xs"><a href="auditPage.php" class="animate //<?php echo $addAuditClass ?>">MONTH WISE AUDIT</a></li>                    
                <?php } if (Utitlity::checkForMainAdmin()) { ?>
                    <li class="visible-xs"><a href="recycleCustomers.php" class="animate //<?php echo $addUserClass ?>">RECYCLE CUST</a></li>
                <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                    <li class="visible-xs"><a href="consultantPage.php" class="animate //<?php echo $addConsultantClass ?>">CONSULTANT</a></li>
                    <li class="visible-xs"><a href="handelbyPage.php" class="animate //<?php echo $addHandelbyClass ?>">HANDEL BY</a></li>
                <?php } ?>
                <!-- Adding ends here -->
                <!--<li style="float: left;"><a class="addAccountCss" style="text-align:center" title="Add new client"><span class=" pull-right glyphicon glyphicon-plus "></span></a></li>-->
                <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM() || Utitlity::checkForUserMarketing()) { ?>
                    <li>
                        <a class="addGeneralNote" style="margin-right: 50px;" title="Add Note">
                            <span class="glyphicon glyphicon-list "></span>
                        </a>
                    </li>    
                    <li><input class="searchcustomer" placeholder="Search Customer" style="margin-top: 10px; padding-left: 40px; z-index: 2; border: 2px solid #dadada; border-radius: 7px;" /></li>
                <?php } ?>
                <?php if (isset($_SESSION['username'])) { ?>                 
                    <li><a class=" dropdown-toggle  animate " data-toggle="dropdown" href="#"><?php echo $_SESSION['username']; ?>&nbsp;</a>
                        <ul class=" dropdown-menu " role="menu">
                            <li><a class=" passwordChange " data-id="<?php echo $_SESSION['id']; ?>">Change Password</a></li>
                        </ul>
                    <li><a href="logout.php" class=" animate "><span class=" pull-right fa fa-power-off "></span></a></li>              
                        <?php } ?>
            </ul>
        </div>
    </div>
</nav>
<!--</div>-->