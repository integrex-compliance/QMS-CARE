<?php
$name_of_your_site = "www.qmscare.com";
$email_adress_reciever = "qmscare@gmail.com";
//include('./loginmodalform.php');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>QMSCARE</title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <script src="css/bootstrap/js/jquery.min.js"></script>
        <link media="all" type="text/css" rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
            <script src="css/bootstrap/js/bootstrap.min.js"></script>
            <script src="css/bootstrap/js/jquery-ui.js"></script>
            <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">-->
            <link href="http://fonts.googleapis.com/css?family=Karla:400,700" rel="stylesheet" />
            <link href="css/default.css" rel="stylesheet" type="text/css" media="all" />
            <link href="css/fonts.css" rel="stylesheet" type="text/css" media="all" />
            <!--[if IE 6]>
            <link href="default_ie6.css" rel="stylesheet" type="text/css" />
            <![endif]-->
    </head>
    <style>
        /*
Code snippet by maridlcrmn for Bootsnipp.com
Follow me on Twitter @maridlcrmn
        */

        .navbar-brand { position: relative; z-index: 2; }

        .navbar-nav.navbar-right .btn { position: relative; z-index: 2; padding: 4px 20px; margin: 10px auto; transition: transform 0.3s; }

        .navbar .navbar-collapse { position: relative; overflow: hidden !important; }
        .navbar .navbar-collapse .navbar-right > li:last-child { padding-left: 22px; }

        .navbar .nav-collapse { position: absolute; z-index: 1; top: 0; left: 0; right: 0; bottom: 0; margin: 0; padding-right: 120px; padding-left: 80px; width: 100%; }
        .navbar.navbar-default .nav-collapse { background-color: #f8f8f8; }
        .navbar.navbar-inverse .nav-collapse { background-color: #222; }
        .navbar .nav-collapse .navbar-form { border-width: 0; box-shadow: none; }
        .nav-collapse>li { float: right; }

        .btn.btn-circle { border-radius: 50px; }
        .btn.btn-outline { background-color: transparent; }

        .navbar-nav.navbar-right .btn:not(.collapsed) {
            background-color: rgb(111, 84, 153);
            border-color: rgb(111, 84, 153);
            color: rgb(255, 255, 255);
        }

        .navbar.navbar-default .nav-collapse,
        .navbar.navbar-inverse .nav-collapse {
            height: auto !important;
            transition: transform 0.3s;
            transform: translate(0px,-50px);
        }
        .navbar.navbar-default .nav-collapse.in,
        .navbar.navbar-inverse .nav-collapse.in {
            transform: translate(0px,0px);
        }




        /* now im going to add new in this section  */



    


        @media screen and (max-width: 767px) {
            .navbar .navbar-collapse .navbar-right > li:last-child { padding-left: 15px; padding-right: 15px; } 

            .navbar .nav-collapse { margin: 7.5px auto; padding: 0; }
            .navbar .nav-collapse .navbar-form { margin: 0; }
            .nav-collapse>li { float: none; }

            .navbar.navbar-default .nav-collapse,
            .navbar.navbar-inverse .nav-collapse {
                transform: translate(-100%,0px);
            }
            .navbar.navbar-default .nav-collapse.in,
            .navbar.navbar-inverse .nav-collapse.in {
                transform: translate(0px,0px);
            }

            .navbar.navbar-default .nav-collapse.slide-down,
            .navbar.navbar-inverse .nav-collapse.slide-down {
                transform: translate(0px,-100%);
            }
            .navbar.navbar-default .nav-collapse.in.slide-down,
            .navbar.navbar-inverse .nav-collapse.in.slide-down {
                transform: translate(0px,0px);
            }
        }
        /* login model style*/    
        .loginmodal-container {
            padding: 30px;
            max-width: 350px;
            width: 100% !important;
            background-color: #F7F7F7;
            margin: 0 auto;
            border-radius: 2px;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            font-family: roboto;
        }

        .loginmodal-container h1 {
            text-align: center;
            font-size: 1.8em;
            font-family: roboto;
        }

        .loginmodal-container input[type=submit] {
            width: 100%;
            display: block;
            margin-bottom: 10px;
            position: relative;
        }

        .loginmodal-container input[type=text], input[type=password] {
            height: 44px;
            font-size: 16px;
            width: 100%;
            margin-bottom: 10px;
            -webkit-appearance: none;
            background: #fff;
            border: 1px solid #d9d9d9;
            border-top: 1px solid #c0c0c0;
            /* border-radius: 2px; */
            padding: 0 8px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .loginmodal-container input[type=text]:hover, input[type=password]:hover {
            border: 1px solid #b9b9b9;
            border-top: 1px solid #a0a0a0;
            -moz-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
            -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }

        .loginmodal {
            text-align: center;
            font-size: 14px;
            font-family: 'Arial', sans-serif;
            font-weight: 700;
            height: 36px;
            padding: 0 8px;
            /* border-radius: 3px; */
            /* -webkit-user-select: none;
              user-select: none; */
        }

        .loginmodal-submit {
            /* border: 1px solid #3079ed; */
            border: 0px;
            color: #fff;
            text-shadow: 0 1px rgba(0,0,0,0.1); 
            background-color: #4d90fe;
            padding: 17px 0px;
            font-family: roboto;
            font-size: 14px;
            /* background-image: -webkit-gradient(linear, 0 0, 0 100%,   from(#4d90fe), to(#4787ed)); */
        }

        .loginmodal-submit:hover {
            /* border: 1px solid #2f5bb7; */
            border: 0px;
            text-shadow: 0 1px rgba(0,0,0,0.3);
            background-color: #357ae8;
            /* background-image: -webkit-gradient(linear, 0 0, 0 100%,   from(#4d90fe), to(#357ae8)); */
        }

        .loginmodal-container a {
            text-decoration: none;
            color: #666;
            font-weight: 400;
            text-align: center;
            display: inline-block;
            opacity: 0.6;
            transition: opacity ease 0.5s;
        } 

        .login-help{
            font-size: 12px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".login-form").click(function () {
                $("#login-modal").modal("show");
            });
        });
    </script>
    <body>

        <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="loginmodal-container">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">X</span><span class="sr-only">Close</span></button>
                    <h1>Login to Your Account</h1><br>
                        <div class="alert alert-danger invalidalert" style="display: none">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <span class="login-message">These credentials do not match our records.</span>
                        </div>

                        <div class="alert alert-danger invalidipalert" style="display: none">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <span class="login-message">This system is not authorized to login.</span>
                        </div>

                        <div class="alert alert-danger invalidalerterror" style="display: none">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <span class="login-message">In order to access this functionality, you need to login first. You might have been logged out due to inactive period.</span>
                        </div>

                        <form action="login.php" method="post">
                            <input type="text" class="useremail" id="useremail" name="useremail" placeholder="User Email" required />
                            <input type="password" class="password" id="password" name="password" placeholder="Password" required />
                            <input type="submit" name="login" class="login loginmodal-submit" value="Login" />
                        </form>                                        
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div id="logo">
                <a href="index.php"><img src="images/QMC_logo.png" alt="" height="100" style="float:left;"/></a>
                <a href="index.php"><img src="images/QMC_head.png" alt="" width="900" height="80" style=""/></a>                                            
            </div> 
            <!-- Second navbar for profile settings -->
            <nav class="navbar navbar-inverse">
                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="navbar-collapse-4">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="index.php">Homepage</a></li>
                            <li><a href="about.php">About</a></li>
                            <li><a href="service.php">Service</a></li>
                            <li><a href="iso.php">ISO 9001:2015</a></li>
                            <li><a href="iatf.php">IATF 16949</a></li>
                            <li><a href="nsic.php">NSIC</a></li>
                            <li><a href="mpcb.php">MPCB</a></li>
                            <li><a href="fssi.php">FSSI</a></li>
                            <li><a href="client.php">Client</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                            <li><a class="login-form">Login</a></li>
                            <!--<li><a class="" style="color: white">Login</a></li>-->
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container -->
            </nav><!-- /.navbar -->
        </div><!-- /.container-fluid -->
    </body>
</html>