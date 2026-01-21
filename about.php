<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
Design by TEMPLATED
http://templated.co
Released for free under the Creative Commons Attribution License

Name       : Retention 
Description: A two-column, fixed-width design with dark color scheme.
Version    : 1.0
Released   : 20140221

-->

<style>
    .form-login {
        background-color: #EDEDED;
        padding-top: 10px;
        padding-bottom: 20px;
        padding-left: 20px;
        padding-right: 20px;
        border-radius: 15px;
        border-color:#d2d2d2;
        border-width: 5px;
        box-shadow:0 1px 0 #cfcfcf;
    }

    h4 { 
        border:0 solid #fff; 
        border-bottom-width:1px;
        padding-bottom:10px;
        text-align: center;
    }

    .form-control {
        border-radius: 10px;
    }

    .wrapper {
        text-align: center;
    }
    #slideshow { 
        margin: 0px auto; 
        position: relative; 
        width: 240px; 
        height: 150px; 
        padding: 5px; 

    }

    #slideshow > div { 
        position: absolute; 
        top: 5px; 
        left: 10px; 
        right: 10px; 
        bottom: 10px; 
    }
</style>

<body>
    <div class="container">
        <div class="row">
            <?php
            $pageIdentifier = "about";
            include('./sourceheader.php');
            ?>
            <div class="col-sm-3" style="">
                <!--                <div class="row">
                                    <div class="col-sm-12">-->
                <div class="form-login">
                    <form method="POST" action="form_submit.php">
                        <h4 >Need an expert guidance?</h4>
                        <input type="text" id="name" name="name" class="form-control input-sm chat-input" placeholder="Name" />
                        </br>
                        <input type="text" id="phnumber" name="no" class="form-control input-sm chat-input" placeholder="Ph. No." />
                        <br>
                            <textarea rows="3" name="msg" name="msg" class="form-control input-sm chat-input msg" placeholder="Message"></textarea>
                        <!--<input type="text" id="userPassword" class="form-control input-sm chat-input" placeholder="password" />-->
                        </br>
                        <div class="wrapper">
                            <span class="group-btn">     
                                <!--<a href="#" class="btn btn-primary btn-md">Submit <i class="fa fa-sign-in"></i></a>-->
                                <button type="submit" class="btn btn-default btn-md" name="submit" value="submit">Submit</button>
                                <input type="hidden" id="myemail" name="myemail" value="<?php echo $email_adress_reciever; ?>" />
                                <input type="hidden" id="myblogname" name="myblogname" value="<?php echo $name_of_your_site; ?>" />
                            </span>
                        </div>
                    </form>
                </div>
                <br/>
                <div class="form-login">

                    <h4>OUR CLIENT'S</h4>
                    <div id="slideshow" style="background-color: white;">
                        <div style="display:table-cell; vertical-align:middle; text-align:center">
                            <img src="images/client/1.jpg" />
                        </div>
                        <div style="display:table-cell; vertical-align:middle; text-align:center">
                            <img src="images/client/2.jpg"/>
                        </div>
                        <div style="display:table-cell; vertical-align:middle; text-align:center">
                            <img src="images/client/3.jpg"/>
                        </div>
                        <div style="display:table-cell; vertical-align:middle; text-align:center">
                            <img src="images/client/4.jpg"/>
                        </div>
                        <div style="display:table-cell; vertical-align:middle; text-align:center">
                            <img src="images/client/5.jpg"/>
                        </div>
                        <div style="display:table-cell; vertical-align:middle; text-align:center">
                            <img src="images/client/6.jpg"/>
                        </div>
                        <div style="display:table-cell; vertical-align:middle; text-align:center">
                            <img src="images/client/7.jpg"/>
                        </div>
                        <div style="display:table-cell; vertical-align:middle; text-align:center">
                            <img src="images/client/8.jpg"/>
                        </div>
                        <div style="display:table-cell; vertical-align:middle; text-align:center">
                            <img src="images/client/9.jpg"/>
                        </div>
                    </div> 
                </div>
            </div>
            <div class="col-sm-6">
                <div class="title">
                    <h2>About Us</h2>
                    <span class="byline">Quality Management Certification</span> 
                </div>
                <p> Quality Management Certification is one of the largest & fastest growing business management consulting organization offering business consulting services to organizations of all nature and size.</p>
                <p>Our Mission is to provide effective, competitive, result oriented & practical business solutions to the organizations to optimize their operational efficiency in order to maximize return on investment & ultimately to achieve their Goal.</p>
                <p>Quality Management Certification specializes in value added consultancy for the entire range of management system certifications. We specialize in ISO 9001:2008, ISO 14001, HACCP, ISO 22000, OHSAS 18001, TS 16949, etc.</p>
                <p>We help you transform your business so that it responds quickly to ever-changing levels of competition, customer expectation and market flux across the world covering Asia, Europe, Australia, Africa with strong client base in India, USA, UK, Saudi Arabia, UAE, Australia, Europe and Africa. </p>
                <p>This means rethinking how processes, people and information can be integrated across your company. Reinventing rigid, costly or under-productive processes. Replacing them with flexible, scalable, productive and cost-effective processes by utilizing ISO 9001:2008, ISO 14001, OHSAS 18001, ISO 27001, HACCP ISO 22000and other management systems.</p>
            </div>        
            <div class="col-sm-3" style="">
                <div class="form-login">
                    <form method="POST" action="form_submit.php">
                        <h4 >Our Services</h4>
                        <ul class="style2">
                            <li><a href="iso.php">ISO 9001:2015</a></li>
                            <li><a href="iatf.php">IATF 16949</a></li>
                            <li><a href="nsic.php">NSIC Registration</a></li>
                            <li><a href="mpcb.php">MPCB Compliance</a></li>
                            <li><a href="service.php">ISI Mark</a></li>
                            <li><a href="service.php">CE Mark</a></li>
                            <li><a href="service.php">ISO 14001Certification</a></li>
                            <li><a href="service.php">ISO 18001: Certification</a></li>
                            <li><a href="service.php">FSMS Certification</a></li>
                            <li><a href="service.php">HACCP Certification</a></li>
                            <li><a href="service.php">Copy Right</a></li>
                            <li><a href="service.php">Trade Mark Registration</a></li>
                            <li><a href="service.php">FSSI</a></li>
                        </ul>
                    </form>
                </div>                
            </div>
        </div>
        <?php include('./sourcefootr.php') ?>
    </div>
    <div id="wrapper">
    </div>
    
    <!--    <div id="footer-wrapper">
            <div id="footer" class="container">
    
                <div id="box3">
                    <div class="title">
                        <h2>QUALITY MANAGEMENT CERTIFICATION</h2>
                    </div>
                    <p>Address: Ramnagar Complex, Bhosari, Pune- 411 039.
                        </br> E mail : qmscare@gmail.com
                        </br>Ph. No.: 7774034698 / 9689882677</p>
                </div>
            </div>
        </div>	-->
</body>
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