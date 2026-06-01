<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Clients - Quality Management Certification</title>
    <meta name="keywords" content="clients, quality management, certification, ISO" />
    <meta name="description" content="Our trusted clients across various industries" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="default.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/client-modern.css" rel="stylesheet" type="text/css" media="all" />
</head>
<body>
    <?php
    $pageIdentifier = "client";
    include('./sourceheader.php');
    ?>
    
    <div class="container">
        <!-- Large Client Slider Section -->
        <div class="client-slider-section">
            <div class="slider-header">
                <h2><i class="fas fa-building"></i> Our Valued Clients</h2>
                <p>We are proud to serve these amazing organizations</p>
            </div>

            <div class="infinite-slider-container">
                <!-- First Row - Slides Left to Right -->
                <div class="slider-row row-1">
                    <div class="slider-track track-1">
                        <!-- First Set -->
                        <div class="client-logo">
                            <img src="images/client/1.jpg" alt="Client 1" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/2.jpg" alt="Client 2" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/3.jpg" alt="Client 3" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/4.jpg" alt="Client 4" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/5.jpg" alt="Client 5" />
                        </div>
                        <!-- Duplicate Set for Seamless Loop -->
                        <div class="client-logo">
                            <img src="images/client/1.jpg" alt="Client 1" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/2.jpg" alt="Client 2" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/3.jpg" alt="Client 3" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/4.jpg" alt="Client 4" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/5.jpg" alt="Client 5" />
                        </div>
                    </div>
                </div>

                <!-- Second Row - Slides Right to Left (Opposite) -->
                <div class="slider-row row-2">
                    <div class="slider-track track-2">
                        <!-- First Set -->
                        <div class="client-logo">
                            <img src="images/client/6.jpg" alt="Client 6" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/7.jpg" alt="Client 7" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/8.jpg" alt="Client 8" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/9.jpg" alt="Client 9" />
                        </div>
                        <!-- Duplicate Set for Seamless Loop -->
                        <div class="client-logo">
                            <img src="images/client/6.jpg" alt="Client 6" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/7.jpg" alt="Client 7" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/8.jpg" alt="Client 8" />
                        </div>
                        <div class="client-logo">
                            <img src="images/client/9.jpg" alt="Client 9" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Content Section -->
        <div class="client-content-section">
            <div class="content-grid">
                <div class="content-main">
                    <div class="content-card">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h2>Client</h2>
                        <p>Consultants of our organization have been associated with top Government & Private organizations of various size and 
                        nature across all the industry verticals. We have been partnering organizations across the world for their 
                        standardization and process improvement initiatives and guarantee 100% successful completion of the ISO certification 
                        projects with highest level of consulting standards, proven consulting and implementation methodologies and timely 
                        project completion. We strive to establish & maintain long term relationship with our clients across the world and not 
                        only initial certification but post certification consulting has become a regular part of our relationship with all our 
                        clients. An impressive 98% repeat customer base speaks about quality of our services and our customer oriented services. 
                        We consult organizations from across the industry domains. Our clients are from manufacturing to service providers.</p>
                        <p>We partner our clients to gain sustainable competitive advantage in the market.... contact us at 
                        qmscare@gmail.com to get our client references and case studies.</p>
                    </div>
                </div>

                <div class="content-sidebar">
                    <!-- Contact Form -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-paper-plane"></i> Need an expert guidance?</h3>
                        <form method="POST" action="form_submit.php" class="sidebar-form">
                            <div class="form-group">
                                <input type="text" id="name" name="name" class="form-input" placeholder="Name" required />
                            </div>
                            <div class="form-group">
                                <input type="text" id="phnumber" name="no" class="form-input" placeholder="Ph. No." required />
                            </div>
                            <div class="form-group">
                                <textarea rows="3" name="msg" class="form-input" placeholder="Message" required></textarea>
                            </div>
                            <input type="hidden" id="myemail" name="myemail" value="<?php echo $email_adress_reciever; ?>" />
                            <input type="hidden" id="myblogname" name="myblogname" value="<?php echo $name_of_your_site; ?>" />
                            <button type="submit" class="submit-btn" name="submit" value="submit">
                                <span>Submit</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Services List -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-cogs"></i> Our Services</h3>
                        <ul class="services-list">
                            <li><a href="iso.php"><i class="fas fa-chevron-right"></i> ISO 9001:2015</a></li>
                            <li><a href="iatf.php"><i class="fas fa-chevron-right"></i> IATF 16949</a></li>
                            <li><a href="nsic.php"><i class="fas fa-chevron-right"></i> NSIC Registration</a></li>
                            <li><a href="mpcb.php"><i class="fas fa-chevron-right"></i> MPCB Compliance</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> ISI Mark</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> CE Mark</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> ISO 14001Certification</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> ISO 18001: Certification</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> FSMS Certification</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> HACCP Certification</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> Copy Right</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> Trade Mark Registration</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> FSSI</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php include('./sourcefootr.php') ?>
    </div>
</body>
</html>