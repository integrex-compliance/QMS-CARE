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
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <script src="css/bootstrap/js/bootstrap.min.js"></script>
            <script src="css/bootstrap/js/jquery-ui.js"></script>
            <!-- SweetAlert2 for beautiful alerts -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!-- Google reCAPTCHA v2 -->
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">-->
            <link href="http://fonts.googleapis.com/css?family=Karla:400,700" rel="stylesheet" />
            <link href="css/default.css" rel="stylesheet" type="text/css" media="all" />
            <link href="css/fonts.css" rel="stylesheet" type="text/css" media="all" />
            <!--[if IE 6]>
            <link href="default_ie6.css" rel="stylesheet" type="text/css" />
            <![endif]-->
    </head>
    <style>
        /* Reset body and html margins/padding */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            overflow-x: hidden !important;
        }

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

        .public-nav {
            margin: 0 !important;
            border: 0;
            border-radius: 0;
            background: linear-gradient(135deg, #0f172a 0%, #123054 100%);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.14);
            overflow: visible;
            min-height: 70px;
            width: 100% !important;
            position: relative;
            left: 0;
            right: 0;
        }

        .public-nav .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
            padding-left: 40px !important;
            padding-right: 40px !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0 !important;
        }

        .public-nav .navbar-header {
            display: flex;
            align-items: center;
        }

        .navbar-brand {
            padding: 10px 0;
            margin: 0;
            height: auto;
            display: flex;
            align-items: center;
        }

        .navbar-brand .logo-img {
            height: 50px;
            width: auto;
            display: block;
        }

        .public-nav .navbar-collapse {
            border-top: 0;
            box-shadow: none;
            flex-grow: 1;
            display: flex !important;
            justify-content: flex-end;
        }

        .public-nav .navbar-nav {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 4px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .public-nav .navbar-nav > li {
            display: inline-block;
        }

        .public-nav .navbar-nav > li > a {
            padding: 10px 14px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border-radius: 999px;
            transition: all 0.25s ease;
            white-space: nowrap;
        }

        .public-nav .navbar-nav > li > a:hover,
        .public-nav .navbar-nav > li > a:focus {
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
        }

        .public-nav .navbar-nav > li > a.login-form {
            background: linear-gradient(135deg, #22c55e 0%, #0ea5a4 100%);
            color: #fff;
            box-shadow: 0 14px 24px rgba(34, 197, 94, 0.22);
        }

        .public-nav .navbar-nav > li > a.login-form:hover,
        .public-nav .navbar-nav > li > a.login-form:focus {
            background: linear-gradient(135deg, #16a34a 0%, #0f766e 100%);
        }

        .public-nav .navbar-toggle {
            display: none;
            margin: 0;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
            padding: 10px;
        }

        .public-nav .navbar-toggle .icon-bar {
            background: #fff;
            width: 20px;
            height: 2px;
            border-radius: 999px;
        }

        @media screen and (max-width: 991px) {
            .public-shell {
                padding-top: 10px;
            }

            .public-nav .container-fluid {
                padding-left: 16px;
                padding-right: 16px;
                flex-wrap: wrap;
            }

            .public-nav .navbar-toggle {
                display: block;
            }

            .navbar-brand .logo-img {
                height: 40px;
            }

            .public-nav .navbar-collapse {
                width: 100%;
                flex-basis: 100%;
                background: rgba(15, 23, 42, 0.98);
                margin-top: 10px;
                border-radius: 12px;
                padding: 10px;
            }

            .public-nav .navbar-collapse.collapse {
                display: none !important;
            }

            .public-nav .navbar-collapse.collapse.in {
                display: block !important;
            }

            .public-nav .navbar-nav {
                display: block;
                padding: 6px 0;
            }

            .public-nav .navbar-nav > li {
                display: block;
            }

            .public-nav .navbar-nav > li > a {
                border-radius: 8px;
                margin-bottom: 4px;
                display: block;
            }
        }
    </style>
    <script type="text/javascript">
        // Enable login button after reCAPTCHA verification
        function enableLoginBtn() {
            var btn = document.getElementById('loginSubmitBtn');
            if (btn) {
                btn.disabled = false;
                btn.value = 'Login';
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        }
        
        $(document).ready(function () {
            $(".login-form").click(function () {
                $("#login-modal").modal("show");
            });
            
            // Password toggle functionality
            $(".toggle-password").click(function() {
                var passwordField = $($(this).attr("toggle"));
                var icon = $("#toggleIcon");
                
                if (passwordField.attr("type") === "password") {
                    passwordField.attr("type", "text");
                    icon.removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    passwordField.attr("type", "password");
                    icon.removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });
            
            // reCAPTCHA v2 validation for login form
            $("#loginForm").submit(function(e) {
                var recaptchaResponse = grecaptcha.getResponse();
                if (recaptchaResponse.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Verification Required',
                        text: 'Please complete the reCAPTCHA verification.',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }
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
                            <span class="login-message"><strong>Invalid Login!</strong> Email ID or Password is incorrect.</span>
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

                        <form action="login.php" method="post" id="loginForm">
                            <input type="text" class="useremail" id="useremail" name="useremail" placeholder="User Email" required />
                            <div style="position: relative;">
                                <input type="password" class="password" id="password" name="password" placeholder="Password" required style="padding-right: 40px;" />
                                <span toggle="#password" class="toggle-password" style="position: absolute; right: 10px; top: 12px; cursor: pointer; color: #666;">
                                    <i class="fa fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                            <!-- Google reCAPTCHA v2 -->
                            <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" data-callback="enableLoginBtn" style="margin: 15px 0; transform:scale(0.95); transform-origin:0 0;"></div>
                            <input type="submit" name="login" id="loginSubmitBtn" class="login loginmodal-submit" value="Please Verify Captcha" disabled style="opacity: 0.6; cursor: not-allowed;" />
                        </form>                                        
                </div>
            </div>
        </div>

        <nav class="navbar public-nav">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">
                        <img src="images/qmc_new_logo copy.png" alt="QMC Logo" class="logo-img">
                    </a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-4" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
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
                        <li><a href="loginpage.php" class="login-form">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </body>
</html>
