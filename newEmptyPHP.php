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

    //for filtering

    $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var serviceType = $(".service-type").val();
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
                    var servieTypeStr = data[9] || ""; // use data for the age column
                    //            alert(servieTypeStr);
                    if (servieTypeStr.toUpperCase().trim() == serviceType.toUpperCase().trim()) {
                        //                alert("test");
                        isServiceType = true;
                    }
                } else {
                    isServiceType = true;
                }
                var dateValue = data[8] || "";
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
            }
    );
//$(window).load(function(){

// $('#tab_customer_list').resize();

// });
    var yetVisited = localStorage['visited'];
//    alert(yetVisited);
    window.location.hash = '#addr' + yetVisited;
    $(document).ready(function () {     
        
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");

            $(".container").toggleClass("active");

            table.draw();
        });

//        window.onhashchange = locationHashChanged;
//        if ("onhashchange" in window) {
//            alert("The browser supports the hashchange event!");
//        }
//        function locationHashChanged() {
//            if (location.hash === "#addr50") {
//                somecoolfeature();
//            }
//        }

        $('.customerAccount').click(function () {
            var customerID = $(this).attr('data-id');
//            alert(customerID);
            // open popup
            localStorage['visited'] = customerID;
            var url = 'customerAccountDetails.php';
            var form = $('<form action="' + url + '" method="get">' +
                    '<input type="hidden" name="customerid" value="' + customerID + '" />' +
                    '</form>');
            $('body').append(form);
            form.submit();
        });



        var table = $('#tab_customer_list').DataTable({
//            "autoWidth":false,
//            "scrollY": "450px",
            "sScrollX": "100%",
            "scrollY": '70vh',
            "scrollCollapse": true,
            "paging": false,
		"ordering": false,
            "columnDefs": [
                {
                    "width": "5%",
                    "targets": 1
                },
                {
                    "width": "20%",
                    "targets": 2
                },
                {
                    "width": "15%",
                    "targets": 3
                },
                {
                    "width": "12%",
                    "targets": 4
                },
                {
                    "width": "12%",
                    "targets": 5
                },
                {
                    "width": "12%",
                    "targets": 6
                }
            ]
                    //        "paging":         false
        });
//        alert("before load");

        table.columns.adjust().draw();

//alert("after load");

        $('.service-type, .filterFromDate, .filterToDate').change(function () {
            var fromDate = $(".filterFromDate").val();
            var toDate = $(".filterToDate").val();
            if (fromDate != "" && toDate != "") {
                if (!compareMinDates(fromDate, toDate)) {
                    alert("To Date can't be after From Date");
                    $(".filterToDate").val("");
                }
            }
            //            alert("t");
            table.draw();
        });

//                $("#menu-toggle").click(function (e) {
//                    e.preventDefault();
//                    $("#wrapper").toggleClass("active");
//                    table.draw();
//                });

        $('.newCustomer').click(function () {
            //            alert('hi');
            $(".addNewCustomer").show();
            $('.newCustomer').hide();
        });

        $('.cancelAdding').click(function () {
            $('.newCustomer').show();
            $(".addNewCustomer").hide();
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

    });

</script>
