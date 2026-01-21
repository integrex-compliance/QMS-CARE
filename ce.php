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
<?php
$pageIdentifier = "iso";
include('./sourceheader.php');
?>
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
                        <h2>
                            CE Marking
                        </h2>
                    </div>
                    <p>
                        CE marking is a certification mark that indicates conformity with health, safety, and environmental 
                        protection standards for products sold within the European Economic Area (EEA). The CE marking is also 
                        found on products sold outside the EEA that are manufactured in, or designed to be sold in, the EEA. 
                        This makes the CE marking recognizable worldwide even to people who are not familiar with the European 
                        Economic Area. It is in that sense similar to the FCC Declaration of Conformity used on certain electronic
                        devices sold in the United States.
</br>The CE marking is the manufacturer's declaration that the product meets the requirements of the applicable EC directives.
</br>The mark consists of the CE logo and, if applicable, the four digit identification number of the Notified Body involved in
the conformity assessment procedure.

                    </p>
                    </br>
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
        </div>
        <div id="wrapper">
        </div>
        <?php include('./sourcefootr.php') ?>
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
                                    <h2>ISO 9001:2015</h2>
                                    <span class="byline">Quality Management System</span> </div>
                            <p><br/>ISO 9001 Certification will provide maximum benefit to your organization if it approaches ISO 9001 implementation in a practical way. This will ensure that the Quality Management Systems that are adopted, work to improve the business and are not just a set of procedures that your employees will find hard to manage.
    
    <br/>By adopting an approach that starts out to implement more efficient working practices and focuses on the business objectives of the organization, you will achieve a system that will help and support your staff, and improve your levels of customer satisfaction.
    
    <br/>Whether you use an external assessor or allocate an internal resource to carry out the initial assessments, you will need to ensure that they have buy-in from senior management, so that all areas of the organization are aware of the importance of the ISO 9001 Certification process.
    
    <br/>ISO 9001 Certification is not just suitable for large organizations but also small businesses that will benefit from adopting efficient Quality Management Systems that will save time and cost, improve efficiency and ultimately improve customer relationships.
    <br/><br/><b>Some of the benefits to your organization:</b>
    <br/>•	Provides senior management with an efficient management process
    <br/>•	Sets out areas of responsibility across the organization
    <br/>•	Mandatory if you want to tender for some public sector work
    <br/>•	Communicates a positive message to staff and customers
    <br/>•	Identifies and encourages more efficient and time saving processes
    <br/>•	Highlights deficiencies
    <br/>•	Reduces your costs
    <br/>•	Provides continuous assessment and improvement
    <br/>•	Marketing opportunities
    
                            </p>
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

setInterval(function() { 
  $('#slideshow > div:first')
    .fadeOut(1000)
    .next()
    .fadeIn(1000)
    .end()
    .appendTo('#slideshow');
},  3000);
</script>