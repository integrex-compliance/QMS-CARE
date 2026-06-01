<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quality Management Certification And Compliance Services - Expert ISO Consultants">
    <title>QMS Care - Quality Management Certification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index-modern.css">
</head>

<body>
    <?php
    $pageIdentifier = "index";
    include('./sourceheader.php');
    ?>
    
    <div class="container">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content-split">
                <div class="hero-text-left">
                    <div class="hero-badge">
                        <i class="fas fa-certificate"></i>
                        Quality Management Certification
                    </div>
                    <h1 class="hero-title">
                        QMS Care
                        <span class="hero-title-highlight">Expand Your Reach</span>
                    </h1>
                    <p class="hero-subtitle">Leading ISO Consultants Delivering 100% Customized Quality Management Solutions</p>
                    
                </div>
                <div class="hero-image-right">
                    <img src="images/At the office-amico.png" alt="Quality Management Consulting" class="hero-illustration-img" />
                </div>
            </div>
        </section>

        <!-- Alert Scripts -->
        <script type="text/javascript">
            $(document).ready(function () {
        <?php if (isset($_GET['invalid']) && $_GET['invalid'] == "invalid") { ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Login!',
                    html: '<b>Entered Email ID or Password is wrong.</b><br>Please enter correct credentials.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'animated bounceIn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#login-modal").modal("show");
                    }
                });
        <?php } ?>
        <?php if (isset($_GET['invalid']) && $_GET['invalid'] == "captcha") { ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Verification Failed!',
                    html: '<b>Please complete the reCAPTCHA verification.</b>',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'animated bounceIn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#login-modal").modal("show");
                    }
                });
        <?php } ?>
        <?php if (isset($_GET['invalid']) && $_GET['invalid'] == "invalidip") { ?>
                Swal.fire({
                    icon: 'warning',
                    title: 'Access Denied!',
                    html: '<b>This system is not authorized to login.</b>',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f39c12',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'animated bounceIn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#login-modal").modal("show");
                    }
                });
        <?php } ?>
        <?php if (isset($_GET["errorCode"]) && $_GET["errorCode"] == "101") { ?>
                Swal.fire({
                    icon: 'info',
                    title: 'Session Expired!',
                    html: '<b>You need to login first.</b><br>You might have been logged out due to inactive period.',
                    confirmButtonText: 'Login Now',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'animated bounceIn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#login-modal").modal("show");
                    }
                });
        <?php } ?>
            });
        </script>

        <!-- Main Content -->
        <div class="main-content-wrapper">
            
            <!-- Introduction Section with Background Image -->
            <section class="content-section section-with-bg-image">
                <div class="section-content-overlay">
                    <div class="section-header">
                        <span class="section-label">
                            <i class="fas fa-info-circle"></i>
                            About Us
                        </span>
                        <h2 class="section-title">Quality Management Certification</h2>
                        <p class="section-description">Your trusted partner in ISO certification and compliance services</p>
                    </div>

                    <div class="content-blocks-grid">
                        <div class="content-block">
                            <div class="content-block-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3>What Sets Us Apart</h3>
                            <p>Ever wondered what differentiates a successful organization from a not-so-successful organization? It is the team of employees. Our team of expert ISO consultants at Quality Management Certification is our greatest asset and are considered to be the best in their respective fields of ISO Management Systems.</p>
                        </div>

                        <div class="content-block">
                            <div class="content-block-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h3>Our Success Story</h3>
                            <p>Our success as one of the most effective and preferred ISO consultants is only because of the superior skillset, competence and project execution capabilities of our ISO experts who provide the solutions that are 100% customized to meet your specific requirements and are absolutely easy to implement and maintain.</p>
                        </div>

                        <div class="content-block">
                            <div class="content-block-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3>Proven Expertise</h3>
                            <p>Our team has handled projects delivering the same level of implementation effectiveness. Our consultants combine the unique differentiator that is functional expertise on various ISO standards and technical expertise on various industry domains. This is the reason we are able to constantly maintain the market leadership in terms of market share for ISO consulting.</p>
                        </div>

                        <div class="content-block">
                            <div class="content-block-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h3>Client-Centric Approach</h3>
                            <p>We believe in building long-term partnerships with our clients. Our dedicated support team ensures seamless communication throughout the certification journey. From initial consultation to post-certification support, we remain committed to your success, providing ongoing guidance and assistance to help you maintain compliance and achieve continuous improvement.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-image-overlay">
                    <img src="images/Investment data-amico.png" alt="Investment Data" class="section-bg-image" />
                </div>
            </section>

            <!-- Consulting Approach Section -->
            <section class="content-section methodology-section">
                <div class="section-header">
                    <span class="section-label">
                        <i class="fas fa-lightbulb"></i>
                        Our Methodology
                    </span>
                    <h2 class="section-title">Consulting Approach</h2>
                    <p class="section-description">Unique Bottom-Up consulting approach ensures 100% success of our consulting assignments</p>
                </div>

                <div class="methodology-intro">
                    <p>Our approach ensures that improvement plans are accepted and practiced at all levels of management, guaranteeing sustainable results for your organization.</p>
                </div>

                <ul class="approach-list">
                    <li class="approach-item">
                        <div class="approach-number">1</div>
                        <div class="approach-content">
                            <div class="approach-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <p class="approach-text">Superior consulting skills & proven implementation methodology</p>
                        </div>
                    </li>
                    <li class="approach-item">
                        <div class="approach-number">2</div>
                        <div class="approach-content">
                            <div class="approach-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <p class="approach-text">Ultimate experience across the world with domain / industry expertise</p>
                        </div>
                    </li>
                    <li class="approach-item">
                        <div class="approach-number">3</div>
                        <div class="approach-content">
                            <div class="approach-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <p class="approach-text">Project ownership to ensure benefits to the client organizations</p>
                        </div>
                    </li>
                    <li class="approach-item">
                        <div class="approach-number">4</div>
                        <div class="approach-content">
                            <div class="approach-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="approach-text">100% success rate and client satisfaction</p>
                        </div>
                    </li>
                    <li class="approach-item">
                        <div class="approach-number">5</div>
                        <div class="approach-content">
                            <div class="approach-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <p class="approach-text">Easy to implement solutions and timely project completion</p>
                        </div>
                    </li>
                    <li class="approach-item">
                        <div class="approach-number">6</div>
                        <div class="approach-content">
                            <div class="approach-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <p class="approach-text">Comprehensive risk management and compliance assurance throughout the certification process</p>
                        </div>
                    </li>
                </ul>
            </section>

            <!-- Features Section with Side Image -->
            <section class="content-section features-section section-with-side-image">
                <div class="side-image-wrapper">
                    <img src="images/Consultative sales-amico.png" alt="Consultative Sales" class="side-image-floating" />
                </div>
                <div class="features-content-wrapper">
                    <div class="section-header">
                        <span class="section-label">
                            <i class="fas fa-award"></i>
                            Why Choose Us
                        </span>
                        <h2 class="section-title">Our Key Strengths</h2>
                        <p class="section-description">Delivering excellence through expertise, experience, and commitment</p>
                    </div>

                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="feature-title">Expert Team</h3>
                            <p class="feature-description">Best-in-class ISO consultants with deep expertise in various ISO Management Systems</p>
                            <div class="feature-badge">
                                <i class="fas fa-check"></i>
                                Certified
                            </div>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h3 class="feature-title">Customized Solutions</h3>
                            <p class="feature-description">100% tailored solutions designed to meet your specific organizational requirements</p>
                            <div class="feature-badge">
                                <i class="fas fa-check"></i>
                                Tailored
                            </div>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h3 class="feature-title">Market Leadership</h3>
                            <p class="feature-description">Proven track record with consistent market leadership in ISO consulting services</p>
                            <div class="feature-badge">
                                <i class="fas fa-check"></i>
                                Proven
                            </div>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 class="feature-title">Timely Delivery</h3>
                            <p class="feature-description">Committed to meeting deadlines with efficient project management and streamlined processes</p>
                            <div class="feature-badge">
                                <i class="fas fa-check"></i>
                                Reliable
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <?php include('./sourcefootr.php') ?>

</body>
</html>
