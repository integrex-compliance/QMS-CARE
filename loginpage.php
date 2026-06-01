<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Quality Management Certification</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="default.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/login-modern.css" rel="stylesheet" type="text/css" media="all" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <?php
    $pageIdentifier = "login";
    include('./sourceheader.php');
    ?>

    <div class="login-page-wrapper">
        <div class="login-content-container">
            <!-- Left Side - Branding -->
            <div class="login-left">
                <div class="brand-content">
                    <div class="brand-logo">
                        <div class="logo-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h1>QMS Care</h1>
                    </div>
                    <h2>Quality Management Certification</h2>
                    <p>Leading ISO Consultants Delivering 100% Customized Quality Management Solutions</p>
                    
                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="feature-text">
                                <h3>Expert Consultants</h3>
                                <p>Best-in-class ISO experts</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-text">
                                <h3>100% Success Rate</h3>
                                <p>Guaranteed certification</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="feature-text">
                                <h3>Market Leadership</h3>
                                <p>Trusted by 500+ clients</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-right">
                <div class="login-form-container">
                    <div class="login-header">
                        <div class="login-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h2>Welcome Back</h2>
                        <p>Login to your account to continue</p>
                    </div>

                    <?php if (isset($_GET['invalid']) && $_GET['invalid'] == "invalid"): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Invalid Login!</strong>
                            <p>Entered Email ID or Password is wrong. Please try again.</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['invalid']) && $_GET['invalid'] == "invalidip"): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Access Denied!</strong>
                            <p>This user is not authorized to login.</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET["errorCode"]) && $_GET["errorCode"] == "101"): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Session Expired!</strong>
                            <p>You need to login first. You might have been logged out due to inactive period.</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['invalid']) && $_GET['invalid'] == "captcha"): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Verification Failed!</strong>
                            <p>Please complete the reCAPTCHA verification.</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php" class="modern-login-form" id="loginPageForm">
                        <div class="form-group">
                            <label for="useremail">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </label>
                            <input 
                                type="email" 
                                id="useremail" 
                                name="useremail" 
                                class="form-input" 
                                placeholder="Enter your email address" 
                                required 
                                autofocus
                            />
                        </div>

                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock"></i>
                                Password
                            </label>
                            <div class="password-input-wrapper">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="form-input" 
                                    placeholder="Enter your password" 
                                    required
                                />
                                <button type="button" class="toggle-password" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="remember-me">
                                <input type="checkbox" name="remember" />
                                <span>Remember me</span>
                            </label>
                            <a href="#" class="forgot-password" onclick="showForgotPasswordAlert(event)">Forgot Password?</a>
                        </div>

                        <!-- Google reCAPTCHA v2 -->
                        <div class="form-group recaptcha-wrapper">
                            <div
                                class="g-recaptcha"
                                data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"
                                data-callback="enableLoginPageBtn"
                                data-expired-callback="disableLoginPageBtn"
                            ></div>
                        </div>

                        <button type="submit" name="login" id="loginPageSubmitBtn" class="login-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                            <span>Please Verify Captcha</span>
                            <i class="fas fa-shield-alt"></i>
                        </button>
                    </form>

                    <div class="login-footer">
                        <p>Need help? Contact us at <a href="mailto:qmscare@gmail.com">qmscare@gmail.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('./sourcefootr.php') ?>

    <script>
        // Enable login button after reCAPTCHA verification
        // Note: page can have multiple reCAPTCHA widgets (login modal + this form).
        // Use the token passed to THIS widget's callback, instead of grecaptcha.getResponse()
        // which can return the first widget's response.
        window.loginPageRecaptchaToken = '';

        function enableLoginPageBtn(token) {
            window.loginPageRecaptchaToken = token || '';
            var btn = document.getElementById('loginPageSubmitBtn');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span>Login to Account</span><i class="fas fa-arrow-right"></i>';
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        }

        function disableLoginPageBtn() {
            window.loginPageRecaptchaToken = '';
            var btn = document.getElementById('loginPageSubmitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span>Please Verify Captcha</span><i class="fas fa-shield-alt"></i>';
                btn.style.opacity = '0.6';
                btn.style.cursor = 'not-allowed';
            }
        }
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput && toggleIcon) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            }
        }

        function showForgotPasswordAlert(event) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Something Went Wrong!',
                text: 'Please contact the administrator for more help.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'animated-popup'
                }
            });
        }

        // Ensure the toggle button works after page load
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('.toggle-password');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    togglePassword();
                });
            }

            // reCAPTCHA v2 validation for login form
            const loginForm = document.getElementById('loginPageForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    // Use widget-specific token (see enableLoginPageBtn).
                    if (!window.loginPageRecaptchaToken) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Verification Required',
                            text: 'Please complete the reCAPTCHA verification before logging in.',
                            confirmButtonColor: '#3b82f6',
                            customClass: {
                                popup: 'animated-popup'
                            }
                        });
                        return false;
                    }
                });
            }
        });
    </script>
</body>
</html>
