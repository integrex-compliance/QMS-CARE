<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Quality Management Certification</title>
    <meta name="keywords" content="contact, quality management, certification, ISO" />
    <meta name="description" content="Get in touch with Quality Management Certification for expert guidance on ISO certifications and compliance services." />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="default.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/contact-modern.css" rel="stylesheet" type="text/css" media="all" />
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php
    $pageIdentifier = "contact";
    
    include('./sourceheader.php');
    ?>

    <div class="contact-page-wrapper">
        <!-- Hero Section -->
        <div class="contact-hero">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h1>Contact Us</h1>
                <p class="hero-subtitle">Feel free to contact us</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="contact-main-content">
            <div class="contact-grid">
                <!-- Contact Form Section -->
                <div class="contact-form-section">
                    <div class="section-header">
                        <h2><i class="fas fa-paper-plane"></i> Need an expert guidance?</h2>
                        <p>Send us a message and we'll get back to you as soon as possible</p>
                    </div>
                    
                    <form method="POST" action="form_submit.php" class="modern-contact-form" id="contactForm">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Your Name</label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="Enter your full name" required />
                        </div>

                        <div class="form-group">
                            <label for="phnumber"><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="text" id="phnumber" name="no" class="form-input" placeholder="Enter your phone number" required />
                        </div>

                        <div class="form-group">
                            <label for="message"><i class="fas fa-comment-dots"></i> Your Message</label>
                            <textarea id="message" name="msg" rows="6" class="form-input" placeholder="Tell us how we can help you..." required></textarea>
                        </div>

                        <!-- Google reCAPTCHA v2 -->
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" data-callback="enableContactBtn" data-expired-callback="disableContactBtn"></div>
                        </div>

                        <input type="hidden" id="myemail" name="myemail" value="<?php echo $email_adress_reciever; ?>" />
                        <input type="hidden" id="myblogname" name="myblogname" value="<?php echo $name_of_your_site; ?>" />

                        <button type="submit" class="submit-btn" id="contactSubmitBtn" name="submit" value="submit" disabled style="opacity: 0.6; cursor: not-allowed;">
                            <span>Please Verify Captcha</span>
                            <i class="fas fa-shield-alt"></i>
                        </button>
                    </form>
                </div>

                <!-- Contact Information Sidebar -->
                <div class="contact-sidebar">
                    <!-- Contact Information Cards -->
                    <div class="info-section">
                        <h3><i class="fas fa-info-circle"></i> Contact Information</h3>
                        
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="info-content">
                                <h4>QUALITY MANAGEMENT CERTIFICATION</h4>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <h4>Address</h4>
                                <p>Ramnagar Complex, Bhosari, Pune- 411 039.</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <h4>E mail</h4>
                                <p><a href="mailto:qmscare@gmail.com">qmscare@gmail.com</a></p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="info-content">
                                <h4>Ph. No.</h4>
                                <p><a href="tel:7774034698">7774034698</a> / <a href="tel:9689882677">9689882677</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php include('./sourcefootr.php') ?>
    
    <script>
        // Enable contact button after reCAPTCHA verification
        window.contactRecaptchaToken = '';

        function enableContactBtn(token) {
            window.contactRecaptchaToken = token || '';
            var btn = document.getElementById('contactSubmitBtn');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span>Send Message</span><i class="fas fa-arrow-right"></i>';
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        }

        function disableContactBtn() {
            window.contactRecaptchaToken = '';
            var btn = document.getElementById('contactSubmitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span>Please Verify Captcha</span><i class="fas fa-shield-alt"></i>';
                btn.style.opacity = '0.6';
                btn.style.cursor = 'not-allowed';
            }
        }
        
        $(document).ready(function() {
            $("#contactForm").submit(function(e) {
                if (!window.contactRecaptchaToken) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Verification Required',
                        text: 'Please complete the reCAPTCHA verification before submitting.',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }
            });
        });
    </script>
</body>
</html>
