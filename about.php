<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="About Quality Management Certification - Leading ISO Consultants">
    <title>About Us - QMS Care</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/about-modern.css">
</head>

<body>
    <?php
    $pageIdentifier = "about";
    include('./sourceheader.php');
    ?>

    <div class="about-container">
        <!-- Page Header -->
        <section class="page-header">
            <div class="header-content">
                <div class="header-badge">
                    <i class="fas fa-info-circle"></i>
                    About Us
                </div>
                <h1 class="page-title">Quality Management Certification</h1>
                <p class="page-subtitle">One of the largest & fastest growing business management consulting
                    organization</p>
            </div>
        </section>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content-grid">

                <!-- Main Content Column -->
                <div class="main-content">
                    <div class="content-card">
                        <div class="card-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h2 class="card-title">Who We Are</h2>
                        <p>Quality Management Certification is one of the largest & fastest growing business management
                            consulting organization offering business consulting services to organizations of all nature
                            and size.</p>
                    </div>

                    <div class="content-card">
                        <div class="card-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h2 class="card-title">Our Mission</h2>
                        <p>Our Mission is to provide effective, competitive, result oriented & practical business
                            solutions to the organizations to optimize their operational efficiency in order to maximize
                            return on investment & ultimately to achieve their Goal.</p>
                    </div>

                    <div class="content-card">
                        <div class="card-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h2 class="card-title">Our Expertise</h2>
                        <p>Quality Management Certification specializes in value added consultancy for the entire range
                            of management system certifications. We specialize in ISO 9001:2008, ISO 14001, HACCP, ISO
                            22000, OHSAS 18001, TS 16949, etc.</p>
                    </div>

                    <div class="content-card">
                        <div class="card-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h2 class="card-title">Global Reach</h2>
                        <p>We help you transform your business so that it responds quickly to ever-changing levels of
                            competition, customer expectation and market flux across the world covering Asia, Europe,
                            Australia, Africa with strong client base in India, USA, UK, Saudi Arabia, UAE, Australia,
                            Europe and Africa.</p>
                    </div>

                    <div class="content-card">
                        <div class="card-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h2 class="card-title">Our Approach</h2>
                        <p>This means rethinking how processes, people and information can be integrated across your
                            company. Reinventing rigid, costly or under-productive processes. Replacing them with
                            flexible, scalable, productive and cost-effective processes by utilizing ISO 9001:2008, ISO
                            14001, OHSAS 18001, ISO 27001, HACCP ISO 22000and other management systems.</p>
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
                                <input type="text" id="name" name="name" class="form-input" placeholder="Your Name"
                                    required />
                            </div>
                            <div class="form-group">
                                <input type="text" id="phnumber" name="no" class="form-input" placeholder="Phone Number"
                                    required />
                            </div>
                            <div class="form-group">
                                <textarea rows="4" name="msg" class="form-input" placeholder="Your Message"></textarea>
                            </div>
                            <button type="submit" class="submit-btn" name="submit" value="submit">
                                <span>Send Message</span>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <input type="hidden" id="myemail" name="myemail"
                                value="<?php echo $email_adress_reciever; ?>" />
                            <input type="hidden" id="myblogname" name="myblogname"
                                value="<?php echo $name_of_your_site; ?>" />
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
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> ISI Mark</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> CE Mark</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> ISO 14001 Certification</a>
                            </li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> ISO 18001 Certification</a>
                            </li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> FSMS Certification</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> HACCP Certification</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> Copy Right</a></li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> Trade Mark Registration</a>
                            </li>
                            <li><a href="service.php"><i class="fas fa-chevron-right"></i> FSSI</a></li>
                        </ul>
                    </div>

                    <!-- Clients Slideshow -->
                    <div class="sidebar-card clients-card">
                        <div class="sidebar-header">
                            <i class="fas fa-users"></i>
                            <h3>Our Clients</h3>
                        </div>
                        <div id="slideshow" class="clients-slideshow">
                            <div><img src="images/client/1.jpg" alt="Client 1" /></div>
                            <div><img src="images/client/2.jpg" alt="Client 2" /></div>
                            <div><img src="images/client/3.jpg" alt="Client 3" /></div>
                            <div><img src="images/client/4.jpg" alt="Client 4" /></div>
                            <div><img src="images/client/5.jpg" alt="Client 5" /></div>
                            <div><img src="images/client/6.jpg" alt="Client 6" /></div>
                            <div><img src="images/client/7.jpg" alt="Client 7" /></div>
                            <div><img src="images/client/8.jpg" alt="Client 8" /></div>
                            <div><img src="images/client/9.jpg" alt="Client 9" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('./sourcefootr.php') ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $("#slideshow > div:gt(0)").hide();

        setInterval(function () {
            $('#slideshow > div:first')
                .fadeOut(1000)
                .next()
                .fadeIn(1000)
                .end()
                .appendTo('#slideshow');
        }, 3000);
    </script>
</body>

</html>