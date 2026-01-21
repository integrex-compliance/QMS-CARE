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

<script type="text/javascript">
    $(document).ready(function () {
<?php if (isset($_GET['invalid']) && $_GET['invalid'] == "invalid") { ?>
            $(".invalidalert").show();
            $("#login-modal").modal("show");
<?php } ?>
<?php if (isset($_GET['invalid']) && $_GET['invalid'] == "invalidip") { ?>
            $(".invalidipalert").show();
            $("#login-modal").modal("show");
<?php } ?>
<?php if (isset($_GET["errorCode"]) && $_GET["errorCode"] == "101") { ?>
            $(".invalidalerterror").show();
            $("#login-modal").modal("show");
<?php } ?>
    });
</script>

<body>
    <div class="container">
        
        <div class="row">
            <?php
        $pageIdentifier = "index";
        include('./sourceheader.php');
        ?>
            <div class="col-sm-3" style="">
                <div class="form-login">
                    <form method="POST" action="form_submit.php">
                        <h4 >Need an expert guidance?</h4>
                        <input type="text" id="name" name="name" class="form-control input-sm chat-input" placeholder="Name" />
                        </br>
                        <input type="text" id="phnumber" name="no" class="form-control input-sm chat-input" placeholder="Ph. No." />
                        <br>
                            <textarea rows="3" name="msg" name="msg" class="form-control input-sm chat-input msg" placeholder="Message"></textarea>
                        </br>
                        <div class="wrapper">
                            <span class="group-btn">
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
                    <h2>INTRODUCTION</h2>
                    <span class="byline">QUALITY MANAGEMENT CERTIFICATION</span> 
                </div>
                <p>Ever wondered what differentiates a successful organization with not so successful organization….! It is the team of 
                    employees. Our team of expert ISO consultants at Quality Management Certification is our greatest asset and are .
                    considered to be the best in their respective fields of ISO Management Systems.</p>

                <p>Our success as one of the most effective and preferred ISO consultants is only because of the superior skillset, competence and project 
                    execution capabilities of our ISO experts who provide the solutions that are 100% customized to meet your specific requirements and are
                    absolutely easy to implement and maintain.</p>

                <p>Our team has handled projects delivering the same level of implementation effectiveness. Our consultants combine the unique 
                    differentiator that is functional expertise on various ISO standards and technical expertise on various industry domains. This is the reason 
                    we are able to constantly maintain the market leadership in terms of market share for ISO consulting.</p> 
                <p>Consulting Approach of Our ISO Consultants</p>
                <p>Unique Bottom-Up consulting approach of our consultants ensures 100% success of our consulting assignments. This approach ensures 
                    that improvement plans are accepted & practiced at all the levels of management.</p>
                <ul>
                    <li>Superior consulting skills & proven implementation methodology</li>
                    <li>Ultimate experience across the world with domain / industry expertise</li>
                    <li>Project ownership to ensure benefits to the client organizations</li>
                    <li>100% success rate and client satisfaction</li>
                    <li>Easy to implement solutions and timely project completion.</li>
                </ul>
                </p>
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