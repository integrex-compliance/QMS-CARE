<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IATF 16949 Automotive Quality Management System Certification">
    <title>IATF 16949 - QMS Care</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/detail-modern.css">
</head>

<body>
    <?php
    $pageIdentifier = "iatf";
    include('./sourceheader.php');
    ?>
    
    <div class="detail-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <div class="header-badge">
                    <i class="fas fa-car"></i>
                    Automotive Certification
                </div>
                <h1 class="page-title">IATF 16949</h1>
                <p class="page-subtitle">ISO/TS 16949, a technical specification for automotive sector</p>
            </div>
        </section>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content-grid">
                
                <!-- Main Content Column -->
                <div class="main-content">
                    <!-- Introduction Section -->
                    <div class="content-card intro-section">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h2 class="section-heading">About IATF 16949</h2>
                        <p>One of the automotive industry's most widely used international standards for quality management, ISO/TS 16949, is evolving with the publication of a new global industry standard by the International Automotive Task Force (IATF). This latest version was developed with an unprecedented level of industry feedback and engagement by AIAG members representing North America.</p>
                    </div>

                    <!-- Technical Specification -->
                    <div class="content-card highlight-card">
                        <div class="section-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h2 class="section-heading">Technical Specification</h2>
                        <p>ISO/TS 16949, a technical specification for automotive sector quality management systems, has become one of the most widely used international standards in the automotive industry, harmonizing the different assessment and certification systems in the global automotive supply chain.</p>
                    </div>

                    <!-- Key Features -->
                    <div class="benefits-card">
                        <div class="benefits-header">
                            <div class="benefits-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h2>Key Features of IATF 16949</h2>
                        </div>
                        <ul class="benefits-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Global automotive industry standard for quality management</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Harmonizes different assessment and certification systems</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Developed with unprecedented industry feedback</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Widely used across the global automotive supply chain</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Ensures consistent quality management practices</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Improves operational efficiency in automotive sector</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Contact Form -->
                    <div class="sidebar-card contact-card">
                        <div class="sidebar-header">
                            <i class="fas fa-headset"></i>
                            <h3>Need Expert Guidance?</h3>
                        </div>
                        <form method="POST" action="form_submit.php" class="contact-form">
                            <div class="form-group">
                                <input type="text" id="name" name="name" class="form-input" placeholder="Your Name" required />
                            </div>
                            <div class="form-group">
                                <input type="text" id="phnumber" name="no" class="form-input" placeholder="Phone Number" required />
                            </div>
                            <div class="form-group">
                                <textarea rows="4" name="msg" class="form-input" placeholder="Your Message"></textarea>
                            </div>
                            <button type="submit" class="submit-btn" name="submit" value="submit">
                                <span>Send Message</span>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <input type="hidden" id="myemail" name="myemail" value="<?php echo $email_adress_reciever; ?>" />
                            <input type="hidden" id="myblogname" name="myblogname" value="<?php echo $name_of_your_site; ?>" />
                        </form>
                    </div>

                    <!-- Services List -->
                    <div class="sidebar-card services-card">
                        <div class="sidebar-header">
                            <i class="fas fa-list-check"></i>
                            <h3>Our Services</h3>
                        </div>
                        <ul class="services-list">
                            <li><a href="iso.php"><i class="fas fa-chevron-right"></i> ISO 9001:2015</a></li>
                            <li><a href="iatf.php" class="active"><i class="fas fa-chevron-right"></i> IATF 16949</a></li>
                            <li><a href="nsic.php"><i class="fas fa-chevron-right"></i> NSIC Registration</a></li>
                            <li><a href="mpcb.php"><i class="fas fa-chevron-right"></i> MPCB Compliance</a></li>
                            <li><a href="isi.php"><i class="fas fa-chevron-right"></i> ISI Mark</a></li>
                            <li><a href="ce.php"><i class="fas fa-chevron-right"></i> CE Mark</a></li>
                            <li><a href="iso14.php"><i class="fas fa-chevron-right"></i> ISO 14001 Certification</a></li>
                            <li><a href="iso18.php"><i class="fas fa-chevron-right"></i> ISO 18001 Certification</a></li>
                            <li><a href="fsms.php"><i class="fas fa-chevron-right"></i> FSMS Certification</a></li>
                            <li><a href="haccp.php"><i class="fas fa-chevron-right"></i> HACCP Certification</a></li>
                            <li><a href="cright.php"><i class="fas fa-chevron-right"></i> Copy Right</a></li>
                            <li><a href="trade.php"><i class="fas fa-chevron-right"></i> Trade Mark Registration</a></li>
                            <li><a href="fssi.php"><i class="fas fa-chevron-right"></i> FSSI</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('./sourcefootr.php') ?>

</body>
</html>
