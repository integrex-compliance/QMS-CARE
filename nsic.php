<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NSIC Registration - National Small Industries Corporation">
    <title>NSIC Registration - QMS Care</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/detail-modern.css">
</head>

<body>
    <?php
    $pageIdentifier = "nsic";
    include('./sourceheader.php');
    ?>
    
    <div class="detail-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <div class="header-badge">
                    <i class="fas fa-building"></i>
                    Government Registration
                </div>
                <h1 class="page-title">NSIC Registration</h1>
                <p class="page-subtitle">National Small Industries Corporation Registration</p>
            </div>
        </section>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content-grid">
                
                <!-- Main Content Column -->
                <div class="main-content">
                    <!-- About NSIC Section -->
                    <div class="content-card intro-section">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h2 class="section-heading">About NSIC</h2>
                        <p>National Small Industries Corporation Limited (NSIC) is a Mini Ratna PSU established by the Government of India in 1955. It falls under Ministry of Micro, Small & Medium Enterprises of India. NSIC is the nodal office for several schemes of Ministry of MSME such as Performance & Credit Rating, Single Point Registration, MSME Databank, National SC ST Hub, etc.</p>
                    </div>

                    <!-- History & Purpose -->
                    <div class="content-card highlight-card">
                        <div class="section-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h2 class="section-heading">History & Purpose</h2>
                        <p>It was established in 1955 to promote and develop micro and small scale industries and enterprises in the country. It was founded as a Government of India agency later made into a fully owned government corporation.</p>
                    </div>

                    <!-- Key Services -->
                    <div class="benefits-card">
                        <div class="benefits-header">
                            <div class="benefits-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h2>NSIC Key Services & Schemes</h2>
                        </div>
                        <ul class="benefits-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Performance & Credit Rating for MSMEs</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Single Point Registration for government tenders</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>MSME Databank for industry information</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>National SC ST Hub for inclusive growth</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Promotion and development of micro enterprises</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Support for small scale industries</span>
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
                            <li><a href="nsic.php" class="active"><i class="fas fa-chevron-right"></i> NSIC Registration</a></li>
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
