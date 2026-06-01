<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ISO 9001:2015 Quality Management System Certification">
    <title>ISO 9001:2015 - QMS Care</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/detail-modern.css">
</head>

<body>
    <?php
    $pageIdentifier = "iso";
    include('./sourceheader.php');
    ?>
    
    <div class="detail-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <div class="header-badge">
                    <i class="fas fa-certificate"></i>
                    ISO Certification
                </div>
                <h1 class="page-title">ISO 9001:2015</h1>
                <p class="page-subtitle">Quality Management System</p>
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
                        <h2 class="section-heading">What is ISO 9001:2015?</h2>
                        <p>ISO 9001 Certification will provide maximum benefit to your organization if it approaches ISO 9001 implementation in a practical way. This will ensure that the Quality Management Systems that are adopted, work to improve the business and are not just a set of procedures that your employees will find hard to manage.</p>
                    </div>

                    <!-- Implementation Approach -->
                    <div class="content-card">
                        <div class="section-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h2 class="section-heading">Our Implementation Approach</h2>
                        <p>By adopting an approach that starts out to implement more efficient working practices and focuses on the business objectives of the organization, you will achieve a system that will help and support your staff, and improve your levels of customer satisfaction.</p>
                        <p>Whether you use an external assessor or allocate an internal resource to carry out the initial assessments, you will need to ensure that they have buy-in from senior management, so that all areas of the organization are aware of the importance of the ISO 9001 Certification process.</p>
                    </div>

                    <!-- Who Can Benefit -->
                    <div class="content-card highlight-card">
                        <div class="section-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h2 class="section-heading">Who Can Benefit?</h2>
                        <p>ISO 9001 Certification is not just suitable for large organizations but also small businesses that will benefit from adopting efficient Quality Management Systems that will save time and cost, improve efficiency and ultimately improve customer relationships.</p>
                    </div>

                    <!-- Benefits Section -->
                    <div class="benefits-card">
                        <div class="benefits-header">
                            <div class="benefits-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h2>Key Benefits to Your Organization</h2>
                        </div>
                        <ul class="benefits-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Provides senior management with an efficient management process</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Sets out areas of responsibility across the organization</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Mandatory if you want to tender for some public sector work</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Communicates a positive message to staff and customers</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Identifies and encourages more efficient and time saving processes</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Highlights deficiencies</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Reduces your costs</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Provides continuous assessment and improvement</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Marketing opportunities</span>
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
                            <li><a href="iso.php" class="active"><i class="fas fa-chevron-right"></i> ISO 9001:2015</a></li>
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
