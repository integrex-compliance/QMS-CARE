<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MPCB Compliance - Maharashtra Pollution Control Board">
    <title>MPCB Compliance - QMS Care</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/detail-modern.css">
</head>

<body>
    <?php
    $pageIdentifier = "mpcb";
    include('./sourceheader.php');
    ?>
    
    <div class="detail-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <div class="header-badge">
                    <i class="fas fa-leaf"></i>
                    Environmental Compliance
                </div>
                <h1 class="page-title">MPCB Compliance</h1>
                <p class="page-subtitle">Maharashtra Pollution Control Board</p>
            </div>
        </section>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content-grid">
                
                <!-- Main Content Column -->
                <div class="main-content">
                    <!-- About MPCB Section -->
                    <div class="content-card intro-section">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h2 class="section-heading">About MPCB</h2>
                        <p>MPCB being a major regulator for implementation of environmental Laws and pollution control in the State, plays an important role in securing sustainable development by enforcing various laws, rules, regulations notifications etc. pertaining to prevention and control of pollution. It is also responsible for monitoring of pollution and for preventive and curative action. MPCB's role has become multi-disciplinary including command and control, Regulator, Facilitator, Management Consultant and Guide to all the stake holders. Monitoring and surveillance has been one of the important functions of the Board. Besides that by dissemination of information on Environment Protection and Training to all Stake holders, creation of Awareness become one of the important function of the Board u/s 17 of both the Water (P&CP) and the Air (P&CP) Act, 1981 respectively. MPCB is also supposed to initiate remediation or restoration process by imposing remediation cost and penalty with the approval of the CPCB under the provisions of the Hazardous Waste (Management, Handling & Trans boundary Movement) rules, 2008. MPCB is also empowered to carry out certain urgent works, as are within its bound.</p>
                    </div>

                    <!-- Important Functions -->
                    <div class="benefits-card">
                        <div class="benefits-header">
                            <div class="benefits-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h2>Important Functions of MPCB</h2>
                        </div>
                        <ul class="benefits-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>To plan comprehensive program for the prevention, control or abatement of pollution and secure executions thereof</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>To collect and disseminate information relating to pollution and the prevention, control or abatement thereof</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>To inspect sewage or trade effluent treatment and disposal facilities, and air pollution control systems and to review plans, specification or any other data relating to the treatment plants, disposal systems and air pollution control systems in connection with the consent granted</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Supporting and encouraging the developments in the fields of pollution control, waste recycle reuse, eco-friendly practices etc.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>To educate and guide the entrepreneurs in improving environment by suggesting appropriate pollution control technologies and techniques</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Creation of public awareness about the clean and healthy environment and attending the public complaints regarding pollution</span>
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
                            <li><a href="mpcb.php" class="active"><i class="fas fa-chevron-right"></i> MPCB Compliance</a></li>
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
