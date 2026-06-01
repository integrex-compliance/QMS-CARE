<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FSSAI Registration - Food Safety and Standards Authority of India">
    <title>FSSAI Registration - QMS Care</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/detail-modern.css">
</head>

<body>
    <?php
    $pageIdentifier = "fssi";
    include('./sourceheader.php');
    ?>
    
    <div class="detail-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <div class="header-badge">
                    <i class="fas fa-utensils"></i>
                    Food Safety Registration
                </div>
                <h1 class="page-title">FSSAI Registration</h1>
                <p class="page-subtitle">Food Safety and Standards Authority of India (FSSAI) is an autonomous body established under the Ministry of Health & Family Welfare, Government of India.</p>
            </div>
        </section>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content-grid">
                
                <!-- Main Content Column -->
                <div class="main-content">
                    <!-- About FSSAI Section -->
                    <div class="content-card intro-section">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h2 class="section-heading">About FSSAI</h2>
                        <p>Food Safety and Standards Authority of India (FSSAI) is an autonomous body established under the Ministry of Health & Family Welfare, Government of India. The FSSAI has been established under the Food Safety and Standards Act, 2006 which is a consolidating statute related to food safety and regulation in India. FSSAI is responsible for protecting and promoting public health through the regulation and supervision of food safety.</p>
                    </div>

                    <!-- Statutory Powers -->
                    <div class="benefits-card">
                        <div class="benefits-header">
                            <div class="benefits-icon">
                                <i class="fas fa-gavel"></i>
                            </div>
                            <h2>Statutory Powers of FSSAI</h2>
                        </div>
                        <p style="font-size: 16px; color: #475569; margin-bottom: 24px;">The following are the statutory powers that the FSS Act, 2006 gives to the Food Safety and Standards Authority of India (FSSAI):</p>
                        <ul class="benefits-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Framing of regulations to lay down food safety standards</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Laying down guidelines for accreditation of laboratories for food testing</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Providing scientific advice and technical support to the Central Government</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Contributing to the development of international technical standards in food</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Collecting and collating data regarding food consumption, contamination, emerging risks etc.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Disseminating information and promoting awareness about food safety and nutrition in India</span>
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
                            <li><a href="iatf.php"><i class="fas fa-chevron-right"></i> IATF 16949</a></li>
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
                            <li><a href="fssi.php" class="active"><i class="fas fa-chevron-right"></i> FSSAI</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('./sourcefootr.php') ?>

</body>
</html>
