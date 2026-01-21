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

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Retention</title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <link href="http://fonts.googleapis.com/css?family=Karla:400,700" rel="stylesheet" />
        <link href="default.css" rel="stylesheet" type="text/css" media="all" />
        <link href="fonts.css" rel="stylesheet" type="text/css" media="all" />
        <!--[if IE 6]>
        <link href="default_ie6.css" rel="stylesheet" type="text/css" />
        <![endif]-->
    </head>

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
                $pageIdentifier = "mpcb";
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
                        <h2>MPCB COMPLIANCE</h2>
                    </div>
                    <p>MPCB being a major regulator for implementation of environmental Laws and pollution control in the State, plays an 
                        important role in securing sustainable development by enforcing various laws, rules, regulations notifications etc. 
                        pertaining to prevention and control of pollution. It is also responsible for monitoring of pollution and for 
                        preventive and curative action. MPCB's role has become multi-disciplinary including command and control, Regulator, 
                        Facilitator, Management Consultant and Guide to all the stake holders. Monitoring and surveillance has been one of the 
                        important functions of the Board. Besides that by dissemination of information on Environment Protection and Training 
                        to all Stake holders, creation of Awareness become one of the important function of the Board u/s 17 of both the Water 
                        (P&CP) and the Air (P&CP) Act, 1981 respectively. MPCB is also supposed to initiate remediation or restoration process 
                        by imposing remediation cost and penalty with the approval of the CPCB under the provisions of the Hazardous Waste 
                        (Management, Handling & Trans boundary Movement) rules, 2008. MPCB is also empowered to carry out certain urgent works,
                        as are within its bound.</p>
                    <p><b>Some of the important functions of MPCB are;</b>
                        <br/>•	To plan comprehensive program for the prevention, control or abatement of pollution and secure executions thereof,
                        <br/>•	To collect and disseminate information relating to pollution and the prevention, control or abatement thereof,
                        <br/>•	To inspect sewage or trade effluent treatment and disposal facilities, and air pollution control systems and to review plans, 
                        specification or any other data relating to the treatment plants, disposal systems and air pollution control systems in connection with the 
                        consent granted,
                        <br/>•	Supporting and encouraging the developments in the fields of pollution control, waste recycle reuse, eco-friendly practices etc.
                        <br/>•	To educate and guide the entrepreneurs in improving environment by suggesting appropriate pollution control technologies and techniques
                        <br/>•	Creation of public awareness about the clean and healthy environment and attending the public complaints regarding pollution.

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
                                <li><a href="isi.php">ISI Mark</a></li>
                                <li><a href="ce.php">CE Mark</a></li>
                                <li><a href="iso14.php">ISO 14001Certification</a></li>
                                <li><a href="iso18.php">ISO 18001: Certification</a></li>
                                <li><a href="fsms.php">FSMS Certification</a></li>
                                <li><a href="haccp.php">HACCP Certification</a></li>
                                <li><a href="cright.php">Copy Right</a></li>
                                <li><a href="trade.php">Trade Mark Registration</a></li>
                                <li><a href="fssi.php">FSSI</a></li>
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

    <!--<body>
    <div id="wrapper">
            <div id="page" class="container">
                    <div id="content">
                            <div class="title">
                                    <h2>Integer sit amet aliquet</h2>
                                    <span class="byline">Phasellus nec erat sit amet nibh pellentesque congue</span> </div>
                            <p>This is <strong>Retention</strong>, a free, fully standards-compliant CSS template designed by <a href="http://templated.co" rel="nofollow">TEMPLATED</a>. The photos in this template are from <a href="http://fotogrph.com/"> Fotogrph</a>. This free template is released under the <a href="http://templated.co/license">Creative Commons Attribution</a> license, so you're pretty much free to do whatever you want with it (even use it commercially) provided you give us credit for it. Have fun :) </p>
                            <p>Consectetuer adipiscing elit. Nam pede erat, porta eu, lobortis eget, tempus et, tellus. Etiam neque. Vivamus consequat lorem at nisl. Nullam non wisi a sem semper eleifend. Donec mattis libero eget urna. Duis pretium velit ac mauris. Proin eu wisi suscipit nulla suscipit interdum. Aenean lectus lorem, imperdiet at, ultrices eget, ornare et, wisi. Pellentesque adipiscing purus ac magna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.  Nullam non wisi a sem semper eleifend. Donec mattis libero eget urna. </p>
                    </div>
                    </div>
    </div>
            
    <div id="footer-wrapper">
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
            </div>	
    </body>-->
</html>
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