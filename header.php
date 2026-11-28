<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include './head.php';
include 'modal_forms.php';
include 'utility.php';
require_once __DIR__ . '/include/hrms.php';
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

        $(document).on('click', '#menu-toggle', function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
            $(".container-fluid, .container").toggleClass("active");
            if (typeof table !== 'undefined' && table && table.draw) {
                table.draw();
            }
        });

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

                var appendText = "<?php echo date("d.m.Y") . " - " . (isset($_SESSION['abbrevation']) ? $_SESSION['abbrevation'] : '') . " - "; ?>";
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
    exit();
} else if (Utitlity::checkForConsultant()) {
    if (!isset($pageIdentifier) || !in_array($pageIdentifier, ['My Dashboard', 'HRMS'], true)) {
        header("Location: myDashboard.php");
        exit();
    }
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
} else if (isset($pageIdentifier) && $pageIdentifier == "Business Insights") {
    $addBusinessInsightsClass = "active";
    if (!Utitlity::checkForMainAdmin()) {
        header("Location: invalidAccess.php?errorCode=102");
    }
} else if (isset($pageIdentifier) && $pageIdentifier == "My Dashboard") {
    $addMyDashboardClass = "active";
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
        <li class="sidebar-brand">
            <a class="sidebar-brand-link" id="menu-toggle" href="javascript:void(0)" title="Toggle Menu">
                <div class="brand-logo-wrap">
                    <div class="brand-icon-box">
                        <i class="fa fa-cubes"></i>
                    </div>
                    <div class="brand-text-wrap">
                        <span class="brand-title">QMS</span>
                        <span class="brand-badge">CARE</span>
                    </div>
                </div>
                <div class="brand-toggle-btn">
                    <span id="main_icon" class="fa fa-bars"></span>
                </div>
            </a>
        </li>
    </ul>
    <ul class="sidebar-nav" id="sidebar">
        <li><a href="myDashboard.php" class="animate <?php echo isset($addMyDashboardClass) ? $addMyDashboardClass : '' ?>"><i class="sub_icon fa fa-tachometer"></i><span class="menu-label">My Dashboard</span></a></li>
        <li><a href="hrms.php" class="animate <?php echo isset($pageIdentifier) && $pageIdentifier === 'HRMS' ? 'active' : ''; ?>"><i class="sub_icon fa fa-users"></i><span class="menu-label">HRMS</span></a></li>
        <?php if ($_SESSION['username']=="Harish Ajabe") { ?>
	<!--<li><a href="homePage_Test.php" class="animate <?php echo $addClass ?? '' ?>"><i class="sub_icon fa fa-building"></i><span class="menu-label">Customers (Test)</span></a></li>-->
        <?php } ?>
		<?php if (Utitlity::checkForMainAdmin()) { ?>
            <!--<li><a href="homePage.php" class="animate <?php echo $addClass ?? '' ?>"><i class="sub_icon fa fa-building"></i><span class="menu-label">Customers</span></a>-->
                <li><a href="homePage_Test.php" class="animate <?php echo $addClass ?? '' ?>"><i class="sub_icon fa fa-building"></i><span class="menu-label">Customers</span></a>
            </li>
            <li><a href="business_insights.php" class="animate <?php echo isset($addBusinessInsightsClass) ? $addBusinessInsightsClass : '' ?>"><i class="sub_icon fa fa-line-chart"></i><span class="menu-label">Dashboard</span></a></li>
        <?php } ?>
        <?php if ($_SESSION['username']=="Harish Ajabe") { ?>
            <!--<li><a href="newEnquiry_Test.php" class="animate <?php echo $addClass ?? '' ?>"><i class="sub_icon fa fa-building"></i><span class="menu-label">Referred Enquiry (Test)</span></a></li>-->
        <?php } ?>
		<?php if (Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="newEnquiry_Test.php" class="animate <?php echo $addNewEnqClass ?? '' ?>"><i class="sub_icon fa fa-phone"></i><span class="menu-label">Enquiries</span></a></li>
            <li><a href="saDuePage.php" class="animate <?php echo $addSaDueClass ?? '' ?>"><i class="sub_icon fa fa-briefcase"></i><span class="menu-label">SA/RC Due</span></a></li>
            <li><a href="disclient.php" class="animate <?php echo $adddisClass ?? '' ?>"><i class="sub_icon fa fa-briefcase"></i><span class="menu-label">Discontinued Clients</span></a></li>
        <?php } ?>
		<?php if ($_SESSION['username']=="Harish Ajabe") { ?>
            <!--<li><a href="workingDetails_Test.php" class="animate <?php echo $addClass ?? '' ?>"><i class="sub_icon fa fa-building"></i><span class="menu-label">Working Projects (Test)</span></a></li>-->
        <?php } ?>
        <?php if (Utitlity::checkForUserMarketing() || Utitlity::checkForUserConsultant() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="workingDetails_Test.php" class="animate <?php echo $addWorkingClass ?? '' ?>"><i class="sub_icon fa fa-building"></i><span class="menu-label">Working Projects</span></a></li>
            <!--<li><a href="workingDetails.php" class="animate <?php echo $addWorkingClass ?? '' ?>"><i class="sub_icon fa fa-tasks"></i><span class="menu-label">Working Projects</span></a></li>-->
            <li><a href="nsicmpcb.php" class="animate <?php echo $addNsicMpcbClass ?? '' ?>"><i class="sub_icon fa fa-tasks"></i><span class="menu-label">NSICMPCB Projects</span></a></li>
        <?php } ?>
        <?php if (Utitlity::checkForUserMarketing() || Utitlity::checkForUserConsultant() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="idleclient.php" class="animate <?php echo $addidleClass ?? '' ?>"><i class="sub_icon fa fa-tasks"></i><span class="menu-label">Idle Projects</span></a></li>
        <?php } ?>
        <?php if (Utitlity::checkForUserFinance() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="financeDetails.php" class="animate <?php echo $addFinanceClass ?? '' ?>"><i class="sub_icon fa fa-money"></i><span class="menu-label">Payment Follow Up</span></a></li>
        <?php } if (Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForUserAdmin() || Utitlity::checkForUserMarketing() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="paymentLoss.php" class="animate <?php echo $payloss ?? '' ?>"><i class="sub_icon fa fa-money"></i><span class="menu-label">Payment Loss</span></a></li>
        <?php } if (Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="tdsDetails.php" class="animate <?php echo $tdspay ?? '' ?>"><i class="sub_icon fa fa-money"></i><span class="menu-label">TDS</span></a></li>
            <li><a href="tdsReceived.php" class="animate <?php echo $tdsrcv ?? '' ?>"><i class="sub_icon fa fa-money"></i><span class="menu-label">TDS Received</span></a></li>
        <?php } if (Utitlity::checkForMainAdmin()) { ?>
            <li><a href="paymentReceived.php" class="animate <?php echo $addPayRcv ?? '' ?>"><i class="sub_icon fa fa-money"></i><span class="menu-label">Payment Received</span></a></li>
        <?php } if (Utitlity::checkForMainAdmin() || Utitlity::checkForUserAdmin()) { ?>
            <li><a href="mktgSetupStatus.php" class="animate <?php echo $addmktgsetupstatusClass ?? '' ?>"><i class="sub_icon fa fa-briefcase"></i><span class="menu-label">Mktg Analysis</span></a></li>
            <li><a href="marketSetupDetails.php" class="animate <?php echo $addMarketingSetupClass ?? '' ?>"><i class="sub_icon fa fa-briefcase"></i><span class="menu-label">Mktg Pending</span></a></li>
            <li><a href="setupReceived.php" class="animate <?php echo $addmktgsetupreceivedClass ?? '' ?>"><i class="sub_icon fa fa-briefcase"></i><span class="menu-label">Mktg Received</span></a></li>
        <?php } ?>
        <?php if (Utitlity::checkForMainAdmin()) { ?>
            <li><a href="masterData.php" class="animate <?php echo $addMasterClass ?? '' ?> "><i class="sub_icon fa fa-wrench"></i><span class="menu-label">Master Creator</span></a></li>
            <li><a href="addUsers.php" class="animate <?php echo $addUserClass ?? '' ?>"><i class="sub_icon fa fa-user-plus"></i><span class="menu-label">Add User</span></a></li>
        <?php } ?>
        <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForUserConsultant() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="invoiceList.php" class="animate <?php echo $addInvoiceClass ?? '' ?>"><i class="sub_icon fa fa-inr"></i><span class="menu-label">Invoice</span></a></li>
            <li><a href="quotationList.php" class="animate <?php echo $addQuotationClass ?? '' ?>"><i class="sub_icon fa fa-quote-left"></i><span class="menu-label">Quotation</span></a></li>
        <?php } ?>
        <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeStaff()) { ?>
            <li><a href="auditPage.php" class="animate <?php echo $addAuditClass ?? '' ?>"><i class="sub_icon fa fa-search"></i><span class="menu-label">Month Wise Audit</span></a></li>
        <?php } if (Utitlity::checkForMainAdmin()) { ?>
            <li><a href="recycleCustomers.php" class="animate <?php echo $addRecycleClass ?? '' ?>"><i class="sub_icon fa fa-trash"></i><span class="menu-label">Recycle Cust</span></a></li>
        <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) { ?>
            <!--<li><a href="mktgSetupStatus.php" class="animate <?php echo $addmktgsetupstatusClass ?? '' ?>"><span class="menu-label">Mktg Setup Status</span></a></li>-->
        <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) { ?>
            <li><a href="consultantPage.php" class="animate <?php echo $addConsultantClass ?? '' ?>"><i class="sub_icon fa fa-user"></i><span class="menu-label">Consultant</span></a></li>
            <li><a href="handelbyPage.php" class="animate <?php echo $addHandelbyClass ?? '' ?>"><i class="sub_icon fa fa-hand-o-right"></i><span class="menu-label">Handle By</span></a></li>
        <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
            <li><a href="consultantDev.php" class="animate <?php echo $addConsultantDevClass ?? '' ?>"><i class="sub_icon fa fa-user"></i><span class="menu-label">Consol Dev</span></a></li>
        <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin()) { ?>
            <li><a href="contactinfo.php" class="animate <?php echo $addcontactinfoClass ?? '' ?>"><i class="sub_icon fa fa-building"></i><span class="menu-label">Contact Info</span></a></li>
        <?php } ?>            
        <?php if (!Utitlity::checkForConsultant()) { ?>
        <li><a href="taskMgmt.php" class="animate <?php echo isset($pendingtask) ? $pendingtask : ''; ?>"><i class="sub_icon fa fa-calendar-plus-o"></i><span class="menu-label">Pending Tasks</span></a></li>
        <li><a href="completedTask.php" class="animate <?php echo isset($completetask) ? $completetask : ''; ?>"><i class="sub_icon fa fa-calendar-check-o"></i><span class="menu-label">Completed Tasks</span></a></li>
        <li><a href="servicedue.php" class="animate <?php echo isset($tomarketing) ? $tomarketing : ''; ?>"><i class="sub_icon fa fa-calendar-check-o"></i><span class="menu-label">Service Due</span></a></li>
        <?php } ?>
    </ul>
</div>
<!--vertile nav bar-->

<nav class="navbar navbar-fixed-top navbar-bootsnipp animate" role="navigation">
    <div class="top-header-bar">
        <!-- Left: Quick Action Button & Mobile Toggle -->
        <div class="top-header-left">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <?php if (isset($_SESSION['username']) && !Utitlity::checkForConsultant()) { ?>
                <a href="javascript:void(0)" class="addAccountCss btn-header-add" title="Add New Client">
                    <i class="fa fa-plus"></i>
                    <span class="hidden-xs">Add Client</span>
                </a>
            <?php } ?>
        </div>

        <!-- Right: Toolbar Actions -->
        <div class="top-header-right">
            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM() || Utitlity::checkForUserMarketing()) { ?>
                <!-- General Note Button -->
                <a href="javascript:void(0)" class="addGeneralNote btn-header-icon" title="View / Add Notes">
                    <i class="fa fa-sticky-note-o"></i>
                    <span class="btn-icon-label hidden-xs">Notes</span>
                </a>

                <!-- Customer Search Field -->
                <div class="header-search-box">
                    <i class="fa fa-search header-search-icon"></i>
                    <input type="text" class="searchcustomer" placeholder="Search Customer..." autocomplete="off" />
                </div>
            <?php } ?>

            <?php if (isset($_SESSION['username'])) { 
                $userDisplayName = trim($_SESSION['username']);
                $firstInitial = strtoupper(substr($userDisplayName, 0, 1));
            ?>
                <!-- User Profile Dropdown -->
                <div class="dropdown header-user-dropdown">
                    <a class="dropdown-toggle header-user-btn" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="header-user-avatar"><?php echo $firstInitial; ?></span>
                        <span class="header-user-name hidden-xs"><?php echo htmlspecialchars($userDisplayName); ?></span>
                        <i class="fa fa-angle-down header-user-caret"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right header-user-menu" role="menu">
                        <li class="header-user-menu-info">
                            <div class="menu-user-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                            <div class="menu-user-role"><?php echo htmlspecialchars(isset($_SESSION['role']) ? $_SESSION['role'] : 'Staff'); ?></div>
                        </li>
                        <li role="separator" class="divider"></li>
                        <li>
                            <a class="passwordChange" data-id="<?php echo isset($_SESSION['id']) ? $_SESSION['id'] : ''; ?>" href="javascript:void(0)">
                                <i class="fa fa-key"></i> Change Password
                            </a>
                        </li>
                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="logout.php" class="menu-logout-link">
                                <i class="fa fa-power-off"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Quick Logout Button -->
                <a href="logout.php" class="btn-header-logout" title="Logout">
                    <i class="fa fa-power-off"></i>
                </a>
            <?php } ?>
        </div>
    </div>

    <!-- Mobile collapse navigation -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav visible-xs">
            <li><a href="myDashboard.php" class="animate <?php echo isset($addMyDashboardClass) ? $addMyDashboardClass : '' ?>">MY DASHBOARD</a></li>
            <li><a href="hrms.php" class="animate <?php echo isset($pageIdentifier) && $pageIdentifier === 'HRMS' ? 'active' : ''; ?>">HRMS</a></li>
            <?php if (Utitlity::checkForUserAdmin() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                <li><a href="homePage.php" class="animate <?php echo isset($addClass) ? $addClass : '' ?>">CUSTOMERS</a></li>
                <?php if (Utitlity::checkForMainAdmin()) { ?>
                    <li><a href="business_insights.php" class="animate <?php echo isset($addBusinessInsightsClass) ? $addBusinessInsightsClass : '' ?>">DASHBOARD</a></li>
                <?php } ?>
            <?php } ?>
            <?php if (Utitlity::checkForUserMarketing() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                <li><a href="newEnquiry_Test.php" class="animate <?php echo isset($addNewEnqClass) ? $addNewEnqClass : '' ?>">ENQUIRIES</a></li>
                <li><a href="saDuePage.php" class="animate <?php echo isset($addSaDueClass) ? $addSaDueClass : '' ?>">SA/RC DUE</a></li>
            <?php } if (Utitlity::checkForUserConsultant() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                <li><a href="workingDetails.php" class="animate <?php echo isset($addWorkingClass) ? $addWorkingClass : '' ?>">WORKING PROJECTS</a></li>
            <?php } if (Utitlity::checkForUserFinance() || Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                <li><a href="financeDetails.php" class="animate <?php echo isset($addFinanceClass) ? $addFinanceClass : '' ?>">PAYMENT FOLLOW UP</a></li>
            <?php } if (Utitlity::checkForMainAdmin()) { ?>
                <li><a href="paymentReceived.php" class="animate <?php echo isset($addPayRcv) ? $addPayRcv : '' ?>">PAYMENT RECEIVED</a></li>
            <?php } if (Utitlity::checkForMainAdmin() || Utitlity::checkForUserAdmin()) { ?>
                <li><a href="marketSetupDetails.php" class="animate <?php echo isset($addMarketingSetupClass) ? $addMarketingSetupClass : '' ?>">MARKETING SETUP</a></li>
            <?php } if (Utitlity::checkForMainAdmin()) { ?>
                <li><a href="masterData.php" class="animate <?php echo isset($addMasterClass) ? $addMasterClass : '' ?>">MASTER CREATOR</a></li>
                <li><a href="addUsers.php" class="animate <?php echo isset($addUserClass) ? $addUserClass : '' ?>">ADD USER</a></li>
            <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                <li><a href="invoiceList.php" class="animate <?php echo isset($addInvoiceClass) ? $addInvoiceClass : '' ?>">INVOICE</a></li>
                <li><a href="quotationList.php" class="animate <?php echo isset($addQuotationClass) ? $addQuotationClass : '' ?>">QUOTATION</a></li>
                <li><a href="auditPage.php" class="animate <?php echo isset($addAuditClass) ? $addAuditClass : '' ?>">MONTH WISE AUDIT</a></li>                    
            <?php } if (Utitlity::checkForMainAdmin()) { ?>
                <li><a href="recycleCustomers.php" class="animate <?php echo isset($addUserClass) ? $addUserClass : '' ?>">RECYCLE CUST</a></li>
            <?php } if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin() || Utitlity::checkForOfficeBDM()) { ?>
                <li><a href="consultantPage.php" class="animate <?php echo isset($addConsultantClass) ? $addConsultantClass : '' ?>">CONSULTANT</a></li>
                <li><a href="handelbyPage.php" class="animate <?php echo isset($addHandelbyClass) ? $addHandelbyClass : '' ?>">HANDEL BY</a></li>
            <?php } ?>
        </ul>
    </div>
</nav>
<!--</div>-->
