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
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <script src="css/bootstrap/js/jquery.min.js"></script>
        <link media="all" type="text/css" rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="css/bootstrap/js/bootstrap.min.js"></script>
        <script src="css/bootstrap/js/jquery-ui.js"></script>
        <!-- SweetAlert2 for beautiful alerts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Google reCAPTCHA v2 -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <!-- Google Fonts: Plus Jakarta Sans & Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Karla:400,700" rel="stylesheet" />
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

        body {
            padding-top: 76px !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* ========================================================
           ULTRA-STYLISH MODERN NAVBAR
           ======================================================== */

        /* Top animated micro gradient bar */
        .nav-top-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #38bdf8 0%, #3b82f6 25%, #818cf8 50%, #06b6d4 75%, #38bdf8 100%);
            background-size: 200% 100%;
            animation: navAccentShift 6s ease-in-out infinite;
            z-index: 1002;
        }

        @keyframes navAccentShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Base Navbar with Frosted Glassmorphism */
        .public-nav {
            margin: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: rgba(10, 17, 34, 0.88) !important;
            backdrop-filter: blur(20px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.4), 0 1px 0 rgba(255, 255, 255, 0.05) inset !important;
            height: 76px;
            min-height: 76px;
            width: 100% !important;
            position: fixed !important;
            top: 0 !important;
            left: 0;
            right: 0;
            z-index: 1000 !important;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* Scrolled state: slightly more compact, higher contrast, glowing border */
        .public-nav.scrolled {
            height: 68px !important;
            min-height: 68px !important;
            background: rgba(7, 13, 26, 0.96) !important;
            backdrop-filter: blur(24px) saturate(200%) !important;
            -webkit-backdrop-filter: blur(24px) saturate(200%) !important;
            border-bottom: 1px solid rgba(56, 189, 248, 0.22) !important;
            box-shadow: 0 12px 35px -5px rgba(2, 6, 23, 0.65), 0 0 25px rgba(37, 99, 235, 0.12) !important;
        }

        /* Container alignment */
        .public-nav .container-fluid {
            width: 100% !important;
            max-width: 1540px !important;
            padding-left: 32px !important;
            padding-right: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            margin: 0 auto !important;
            min-height: 76px;
            height: 100%;
            transition: min-height 0.35s ease !important;
        }

        .public-nav.scrolled .container-fluid {
            min-height: 68px !important;
        }

        /* Brand & Logo Section */
        .public-nav .navbar-header {
            display: flex !important;
            align-items: center !important;
            flex-shrink: 0;
        }

        .modern-brand {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 4px 0 !important;
            margin: 0 !important;
            height: 100% !important;
            text-decoration: none !important;
            outline: none !important;
            transition: transform 0.25s ease !important;
        }

        .modern-brand:hover,
        .modern-brand:focus {
            text-decoration: none !important;
            outline: none !important;
            transform: translateY(-1px);
        }

        .logo-circle-glow {
            position: relative;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.4), 0 4px 14px rgba(14, 165, 233, 0.28);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
        }

        .modern-brand:hover .logo-circle-glow {
            box-shadow: 0 0 0 2.5px rgba(56, 189, 248, 0.75), 0 6px 20px rgba(14, 165, 233, 0.45);
            transform: scale(1.05) rotate(2deg);
        }

        .logo-circle-glow .logo-img {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
            border-radius: 50% !important;
            display: block !important;
        }

        .brand-text-block {
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.15;
            user-select: none;
        }

        .brand-title {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            font-weight: 800;
            font-size: 17px;
            letter-spacing: 0.04em;
            color: #ffffff;
            display: inline-block;
        }

        .brand-title .brand-glow {
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-sub {
            font-family: 'Inter', sans-serif;
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(226, 232, 240, 0.65);
        }

        /* Navbar Collapse Container */
        .public-nav .navbar-collapse {
            border-top: 0 !important;
            box-shadow: none !important;
            flex-grow: 1;
            display: flex !important;
            justify-content: flex-end !important;
            align-items: center;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Nav links flex list */
        .public-nav .modern-nav-links {
            display: flex !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: 2px !important;
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
        }

        .public-nav .modern-nav-links > li {
            display: inline-flex !important;
            align-items: center !important;
            position: relative;
        }

        /* Link Items */
        .public-nav .modern-nav-links > li > a {
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 8px 11px;
            color: rgba(241, 245, 249, 0.82) !important;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            text-decoration: none !important;
            border-radius: 8px;
            background: transparent;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        /* Hover Effect: Soft Pill & Subtle Glow */
        .public-nav .modern-nav-links > li > a:hover,
        .public-nav .modern-nav-links > li > a:focus {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08) !important;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
            transform: translateY(-1px);
        }

        /* Active Page Indicator: Illuminated pill + subtle underline */
        .public-nav .modern-nav-links > li.active > a,
        .public-nav .modern-nav-links > li > a.active {
            color: #38bdf8 !important;
            background: rgba(56, 189, 248, 0.12) !important;
            box-shadow: inset 0 0 0 1px rgba(56, 189, 248, 0.3) !important;
            font-weight: 700;
        }

        .public-nav .modern-nav-links > li.active > a::after,
        .public-nav .modern-nav-links > li > a.active::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 20%;
            right: 20%;
            height: 2px;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            border-radius: 999px;
            box-shadow: 0 0 8px #38bdf8;
        }

        /* Login Button item wrapper */
        .public-nav .modern-nav-links > li.login-li {
            margin-left: 8px;
        }

        /* Modern Gradient CTA Login Button */
        .public-nav .modern-nav-links > li > a.modern-login-btn {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 20px !important;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #ffffff !important;
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 50%, #4f46e5 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.22) !important;
            border-radius: 50px !important;
            box-shadow: 0 4px 16px rgba(14, 165, 233, 0.35), 0 1px 2px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            z-index: 1;
        }

        .public-nav .modern-nav-links > li > a.modern-login-btn .login-btn-icon {
            font-size: 12px;
            transition: transform 0.25s ease;
        }

        /* Shimmer Sweep Animation on Hover */
        .public-nav .modern-nav-links > li > a.modern-login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 70%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.32), transparent);
            transform: skewX(-25deg);
            transition: left 0.65s ease;
            z-index: -1;
        }

        .public-nav .modern-nav-links > li > a.modern-login-btn:hover,
        .public-nav .modern-nav-links > li > a.modern-login-btn:focus {
            transform: translateY(-2px) scale(1.02);
            color: #ffffff !important;
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%) !important;
            box-shadow: 0 8px 24px rgba(14, 165, 233, 0.5), 0 2px 5px rgba(0, 0, 0, 0.25) !important;
        }

        .public-nav .modern-nav-links > li > a.modern-login-btn:hover::before {
            left: 150%;
        }

        .public-nav .modern-nav-links > li > a.modern-login-btn:hover .login-btn-icon {
            transform: scale(1.15) rotate(-5deg);
        }

        .public-nav .modern-nav-links > li > a.modern-login-btn:active {
            transform: translateY(0) scale(0.98);
        }

        /* Scrolled state for login button keeps high contrast */
        .public-nav.scrolled .modern-nav-links > li > a.modern-login-btn {
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 50%, #4f46e5 100%) !important;
            box-shadow: 0 4px 18px rgba(14, 165, 233, 0.4) !important;
        }

        /* Mobile Hamburger Toggle */
        .public-nav .modern-toggle {
            display: none;
            margin: 0;
            padding: 9px 11px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .public-nav .modern-toggle:hover,
        .public-nav .modern-toggle:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(56, 189, 248, 0.5);
            outline: none !important;
        }

        .public-nav .modern-toggle .icon-bar {
            background: #ffffff;
            width: 22px;
            height: 2px;
            border-radius: 2px;
            transition: all 0.3s ease;
            display: block;
        }

        .public-nav .modern-toggle .icon-bar + .icon-bar {
            margin-top: 5px;
        }

        /* Responsive Breakpoints */
        @media screen and (max-width: 1280px) and (min-width: 992px) {
            .public-nav .container-fluid {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .brand-sub {
                display: none;
            }
            .public-nav .modern-nav-links > li > a {
                padding: 7px 8px;
                font-size: 11px;
                letter-spacing: 0.02em;
            }
            .public-nav .modern-nav-links > li > a.modern-login-btn {
                padding: 7px 14px !important;
                font-size: 11.5px;
                margin-left: 4px;
            }
        }

        @media screen and (max-width: 991px) {
            body {
                padding-top: 72px !important;
            }

            .public-nav {
                height: 72px !important;
                min-height: 72px !important;
            }

            .public-nav .container-fluid {
                padding-left: 18px !important;
                padding-right: 18px !important;
                min-height: 72px !important;
                flex-wrap: wrap !important;
            }

            .public-nav .navbar-header {
                width: 100%;
                justify-content: space-between;
            }

            .public-nav .modern-toggle {
                display: block !important;
            }

            .public-nav .navbar-collapse {
                width: 100% !important;
                flex-basis: 100% !important;
                background: rgba(10, 17, 34, 0.98) !important;
                backdrop-filter: blur(24px) !important;
                -webkit-backdrop-filter: blur(24px) !important;
                border: 1px solid rgba(255, 255, 255, 0.12) !important;
                border-radius: 16px !important;
                padding: 16px !important;
                margin-top: 10px !important;
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6) !important;
            }

            .public-nav .navbar-collapse.collapse {
                display: none !important;
            }

            .public-nav .navbar-collapse.collapse.in {
                display: block !important;
            }

            .public-nav .modern-nav-links {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 6px !important;
            }

            .public-nav .modern-nav-links > li {
                display: block !important;
                width: 100%;
            }

            .public-nav .modern-nav-links > li > a {
                display: block !important;
                padding: 10px 14px !important;
                border-radius: 10px !important;
                font-size: 13px !important;
            }

            .public-nav .modern-nav-links > li.active > a::after,
            .public-nav .modern-nav-links > li > a.active::after {
                display: none;
            }

            .public-nav .modern-nav-links > li.login-li {
                margin-left: 0 !important;
                margin-top: 8px !important;
            }

            .public-nav .modern-nav-links > li > a.modern-login-btn {
                display: flex !important;
                justify-content: center !important;
                width: 100% !important;
                padding: 11px 20px !important;
                font-size: 13px !important;
            }
        }

        @media screen and (max-width: 480px) {
            .brand-sub {
                display: none !important;
            }
            .logo-circle-glow {
                width: 38px !important;
                height: 38px !important;
            }
            .brand-title {
                font-size: 15px !important;
            }
            .public-nav .container-fluid {
                padding-left: 14px !important;
                padding-right: 14px !important;
            }
        }

        /* Modern Login Modal Styling */
        .loginmodal-container {
            padding: 36px 32px;
            max-width: 400px;
            width: 100% !important;
            background: #ffffff;
            margin: 40px auto;
            border-radius: 20px;
            box-shadow: 0 25px 60px -10px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
            position: relative;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }

        .loginmodal-container h1 {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 20px 0;
            letter-spacing: -0.01em;
        }

        .loginmodal-container .close {
            position: absolute;
            top: 18px;
            right: 20px;
            font-size: 24px;
            color: #94a3b8;
            opacity: 0.8;
            transition: color 0.2s ease;
            background: none;
            border: 0;
            cursor: pointer;
        }

        .loginmodal-container .close:hover {
            color: #0f172a;
            opacity: 1;
        }

        .loginmodal-container input[type=text],
        .loginmodal-container input[type=password] {
            height: 48px;
            font-size: 14.5px;
            width: 100%;
            margin-bottom: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 14px;
            box-sizing: border-box;
            transition: all 0.2s ease;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
        }

        .loginmodal-container input[type=text]:focus,
        .loginmodal-container input[type=password]:focus {
            background: #ffffff;
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .loginmodal-submit {
            width: 100%;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 12px;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
            cursor: pointer;
            transition: all 0.25s ease;
            margin-top: 10px;
        }

        .loginmodal-submit:hover:not([disabled]) {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.45);
            background: linear-gradient(135deg, #0ea5e9 0%, #1d4ed8 100%);
        }

        .loginmodal-container a {
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            text-align: center;
            display: inline-block;
            transition: color 0.2s ease;
        }

        .loginmodal-container a:hover {
            color: #2563eb;
        }

        .login-help {
            font-size: 13px;
        }
    </style>
    <script type="text/javascript">
        // Enable login button after reCAPTCHA verification
        window.loginModalRecaptchaToken = '';

        function enableLoginBtn(token) {
            window.loginModalRecaptchaToken = token || '';
            var btn = document.getElementById('loginSubmitBtn');
            if (btn) {
                btn.disabled = false;
                btn.value = 'Login';
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        }

        function disableLoginBtn() {
            window.loginModalRecaptchaToken = '';
            var btn = document.getElementById('loginSubmitBtn');
            if (btn) {
                btn.disabled = true;
                btn.value = 'Please Verify Captcha';
                btn.style.opacity = '0.6';
                btn.style.cursor = 'not-allowed';
            }
        }
        
        $(document).ready(function () {
            $(".login-form").click(function (e) {
                e.preventDefault();
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
                if (!window.loginModalRecaptchaToken) {
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
            
            // Navbar scroll effect
            $(window).scroll(function() {
                if ($(window).scrollTop() > 30) {
                    $('.public-nav').addClass('scrolled');
                } else {
                    $('.public-nav').removeClass('scrolled');
                }
            });
        });
    </script>
    <body>

        <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="loginmodal-container">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h1>Login to Your Account</h1>
                    
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
                            <span toggle="#password" class="toggle-password" style="position: absolute; right: 12px; top: 14px; cursor: pointer; color: #64748b;">
                                <i class="fa fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        <!-- Google reCAPTCHA v2 -->
                        <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" data-callback="enableLoginBtn" data-expired-callback="disableLoginBtn" style="margin: 15px 0; transform:scale(0.95); transform-origin:0 0;"></div>
                        <input type="submit" name="login" id="loginSubmitBtn" class="login loginmodal-submit" value="Please Verify Captcha" disabled style="opacity: 0.6; cursor: not-allowed;" />
                    </form>                                        
                </div>
            </div>
        </div>

        <?php
        $currentPage = '';
        if (isset($pageIdentifier) && !empty($pageIdentifier)) {
            $currentPage = strtolower(trim($pageIdentifier));
        } else {
            $currentPage = strtolower(basename($_SERVER['PHP_SELF'], '.php'));
        }
        $currentScript = strtolower(basename($_SERVER['PHP_SELF']));
        ?>

        <nav class="navbar public-nav">
            <div class="nav-top-accent"></div>
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand modern-brand" href="index.php">
                        <div class="logo-circle-glow">
                            <img src="images/qmc_new_logo copy.png" alt="QMC Logo" class="logo-img">
                        </div>
                        <div class="brand-text-block">
                            <span class="brand-title">QMS <span class="brand-glow">CARE</span></span>
                            <span class="brand-sub">Certification &amp; Compliance</span>
                        </div>
                    </a>
                    <button type="button" class="navbar-toggle collapsed modern-toggle" data-toggle="collapse" data-target="#navbar-collapse-4" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbar-collapse-4">
                    <ul class="nav navbar-nav navbar-right modern-nav-links">
                        <li class="<?php echo ($currentPage == 'index' || $currentPage == 'homepage' || $currentScript == 'index.php') ? 'active' : ''; ?>">
                            <a href="index.php" class="<?php echo ($currentPage == 'index' || $currentPage == 'homepage' || $currentScript == 'index.php') ? 'active' : ''; ?>">Homepage</a>
                        </li>
                        <li class="<?php echo ($currentPage == 'about' || $currentScript == 'about.php') ? 'active' : ''; ?>">
                            <a href="about.php" class="<?php echo ($currentPage == 'about' || $currentScript == 'about.php') ? 'active' : ''; ?>">About</a>
                        </li>
                        <li class="<?php echo ($currentPage == 'service' || $currentScript == 'service.php') ? 'active' : ''; ?>">
                            <a href="service.php" class="<?php echo ($currentPage == 'service' || $currentScript == 'service.php') ? 'active' : ''; ?>">Service</a>
                        </li>
                        <li class="<?php echo ($currentScript == 'iso.php') ? 'active' : ''; ?>">
                            <a href="iso.php" class="<?php echo ($currentScript == 'iso.php') ? 'active' : ''; ?>">ISO 9001:2015</a>
                        </li>
                        <li class="<?php echo ($currentScript == 'iatf.php') ? 'active' : ''; ?>">
                            <a href="iatf.php" class="<?php echo ($currentScript == 'iatf.php') ? 'active' : ''; ?>">IATF 16949</a>
                        </li>
                        <li class="<?php echo ($currentPage == 'nsic' || $currentScript == 'nsic.php') ? 'active' : ''; ?>">
                            <a href="nsic.php" class="<?php echo ($currentPage == 'nsic' || $currentScript == 'nsic.php') ? 'active' : ''; ?>">NSIC</a>
                        </li>
                        <li class="<?php echo ($currentScript == 'mpcb.php') ? 'active' : ''; ?>">
                            <a href="mpcb.php" class="<?php echo ($currentScript == 'mpcb.php') ? 'active' : ''; ?>">MPCB</a>
                        </li>
                        <li class="<?php echo ($currentPage == 'fssi' || $currentScript == 'fssi.php') ? 'active' : ''; ?>">
                            <a href="fssi.php" class="<?php echo ($currentPage == 'fssi' || $currentScript == 'fssi.php') ? 'active' : ''; ?>">FSSI</a>
                        </li>
                        <li class="<?php echo ($currentPage == 'client' || $currentScript == 'client.php') ? 'active' : ''; ?>">
                            <a href="client.php" class="<?php echo ($currentPage == 'client' || $currentScript == 'client.php') ? 'active' : ''; ?>">Client</a>
                        </li>
                        <li class="<?php echo ($currentPage == 'contact' || $currentScript == 'contact.php') ? 'active' : ''; ?>">
                            <a href="contact.php" class="<?php echo ($currentPage == 'contact' || $currentScript == 'contact.php') ? 'active' : ''; ?>">Contact Us</a>
                        </li>
                        <li class="login-li">
                            <a href="loginpage.php" class="login-form modern-login-btn">
                                <i class="fa fa-lock login-btn-icon"></i>
                                <span>Login</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </body>
</html>
