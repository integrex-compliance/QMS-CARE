<!doctype html>
<!--[if IE 7 ]>    <html lang="en-gb" class="isie ie7 oldie no-js"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en-gb" class="isie ie8 oldie no-js"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en-gb" class="isie ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en-gb" class="no-js"> <!--<![endif]-->
<?php
include("dbcon.php");
include("session.php");
if($_SESSION['user_type']=='first')
{
?>
<head>
	<title>QMS CARE</title>
	
	<meta charset="utf-8">
	<meta name="keywords" content="" />
	<meta name="description" content="" />
    
    <!-- Favicon --> 
	<link rel="shortcut icon" href="imageused/fav.ico">
    
    <!-- this styles only adds some repairs on idevices  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <!-- Google fonts - witch you want to use - (rest you can just remove) -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
    
    <!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
    
    <!-- Favicon -->
  	<link rel="shortcut icon" type="image/x-icon" href="imageused/fav.ico">
    
    <!-- ######### CSS STYLES ######### -->
	
    <link rel="stylesheet" href="css/reset.css" type="text/css" />
	<link rel="stylesheet" href="css/style.css" type="text/css" />
    
    <!-- responsive devices styles -->
	<link rel="stylesheet" media="screen" href="css/responsive-leyouts.css" type="text/css" />   

<!-- just remove the below comments witch color skin you want to use -->
    <!--<link rel="stylesheet" href="styles/colors/lightblue.css" />-->
    <!--<link rel="stylesheet" href="styles/colors/lightgreen.css" />-->
    <!--<link rel="stylesheet" href="styles/colors/blue.css" />-->
    <!--<link rel="stylesheet" href="styles/colors/green.css" />-->
    <!--<link rel="stylesheet" href="styles/colors/red.css" />-->
    <!--<link rel="stylesheet" href="styles/colors/cyan.css" />-->
    <!--<link rel="stylesheet" href="styles/colors/purple.css" />-->
    <!--<link rel="stylesheet" href="styles/colors/yellow.css" />-->
    <!--<link rel="stylesheet" href="styles/colors/brown.css" />-->   
    
    <!-- style switcher -->
    <link rel = "stylesheet" media = "screen" href = "js/style-switcher/color-switcher.css" />
	  
    <!-- jquery jcarousel -->
    <link rel="stylesheet" type="text/css" href="js/jcarousel/skin.css" />
    <link rel="stylesheet" type="text/css" href="js/jcarousel/skintwo.css" />
    
</head>
<div class="image">
		<a href="index.html"><img src="imageused/webheading.jpg"/></a> 
	</div>


<div class="site_wrapper">

	<div class="container">
    
    	<div class="top_section">
              
            <div class="logo_innerpage"><a href="index.html" class="logo_innerpage"><h1>Q<i>M</i>S CARE <small></small></h1></a></div><!-- end logo -->
               
        <nav id="access" class="access" role="navigation">
        <div id="menu" class="menu">
        	
        	<ul id="tiny">
                    
                       
							
                            	<li><a href="index.php">Home</a></li>
							
                        
                        
                            <li><a href="qmsclients.php">Project Status</a></li>
                            
                        <?php
                        if($_SESSION['user_type']=='first')
                           {
                           ?>
                             <li><a href="payfollowsheet.php">Payment followup</a></li>
                             <li><a href="mrktclients.php">Marketing List</a></li>
                             <li><a href="view.php">View</a></li>
                           <?php } ?>

                           

                        <li><a href="logout.php">logout</a></li>
                    </ul>
		</div>
        
  	</nav>
    
 </div>
 
</div>
<!-- end top -->

<div class="clearfix"></div>
 
<div class="page_title">

	<div class="container">
		<div class="leaft_title"><h1>MARKETING STATUS</h1></div>
	</div>
</div><!-- end page title --> 

<!-- Contant
======================================= -->

<div class="container">

<div class="content_fullwidth">

    <div class="one_full">
    
		<div class="punchline_text_box">
        <div class="left">
        <strong>New Client Details</strong>
        
        </div>
        <div class="left">
		<form action="./addmrktclient.php" method="post" enctype="multipart/form-data">
		<p><label for="name">Client Name:</label><input type="text" name="name" id="name" placeholder="Name" value="" autocomplete="off" height=></p><br/>
		<p><label for="cno">Contact No:</label><input type="text" name="cno" id="cno" placeholder="Contact No" autocomplete="off"></p><br/>
		<p><label for="addr">Address:</label><input type="text" name="addr" id="addr" placeholder="Address" autocomplete="off"></p><br/>
		<p><label for="Type">Audit Type:</label><input type="text" name="type" id="type" placeholder="Audit Type" autocomplete="off"></p><br/>
		<p><label for="cmt">Comments:</label><textarea name="cmt" id="cmt" placeholder="Comments" autocomplete="off"; height: 50px; width: 600px
></textarea></p><br/>
   		<input name="action" type="hidden" value="Save"/>
		<input type="submit" value="Save"/>
  		<p>
		</form>
	  </div>
    </div>

			<div class="table-style">
                <table class="table-list">
                    <tr>
                       
                        <th>Client Name</th>
                        <th>Address / es</th>
                        <th>Contact No. </th>
				   <th>Audit Type</th>
				   <th width="500px">........................Comments......................</th>  
				    <th>Edit</th>    
                    </tr>
<?php
try{
 
$count=1;
echo "here";
$stmt = $dbh->query("SELECT * FROM `marketing_status`");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$ec=0;
foreach( $results as $r)//<-------line 14
{
if($r['client_name']!="")
{
?> <tr>
		
		<td><?php 
			print($r['client_name']);
			?>
		</td>
		<td><?php
			print($r['location']);
			?>
		</td>
		<td><?php
			print($r['ph_no']);
			?>
		</td>
		<td><?php
			print($r['audit_type']);
			?>
		</td>

		<td><?php
			print($r['comments']);
			?>
		</td>
		
		<td>
			<form action="./editmrktclient.php" method="post" enctype="multipart/form-data">
			<?php $temp=$ec; ?>
			<input name="action" type="hidden" value="<?php echo htmlspecialchars($temp); ?>"/>
			<input type="submit" value="Edit"/>
			</form>
		</td>
		

   </tr>

<?php
$ec++;
}
}
    $dbh = null;
}
catch(PDOException $e){
    echo $e->getMessage();
}
?>
  </table>
			</div>
    
    </div><!-- end tables -->
   

</div><!-- end content full width area -->

</div>


    
<!-- ######### JS FILES ######### -->
<!-- get jQuery from the google apis -->
<script type="text/javascript" src="js/universal/jquery.js"></script>

<!-- style switcher -->
<script src="js/style-switcher/jquery-1.js"></script>
<script src="js/style-switcher/styleselector.js"></script>

<!-- main menu -->
<script type="text/javascript" src="js/mainmenu/ddsmoothmenu.js"></script>
<script type="text/javascript" src="js/mainmenu/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/mainmenu/selectnav.js"></script>

<script type="text/javascript" src="js/mainmenu/scripts.js"></script>

<!-- jquery jcarousel -->
<script type="text/javascript" src="js/jcarousel/jquery.jcarousel.min.js"></script>

<!-- Toggle -->
<script type="text/javascript">
    $(document).ready(function(){
      $("#colosebut1").click(function(){
        $("#div1").fadeOut("slow");
      });
      $("#colosebut2").click(function(){
        $("#div2").fadeOut("slow");
      });
      $("#colosebut3").click(function(){
        $("#div3").fadeOut("slow");
      });
      $("#colosebut4").click(function(){
        $("#div4").fadeOut("slow");
      });
    
    });
</script>

<!-- scroll up -->
<script type="text/javascript">
    $(document).ready(function(){
 
        $(window).scroll(function(){
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });
 
        $('.scrollup').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 500);
            return false;
        });
 
    });
</script>

<!-- jquery jcarousel -->
<script type="text/javascript">

	jQuery(document).ready(function() {
			jQuery('#mycarousel').jcarousel();
	});
	
	jQuery(document).ready(function() {
			jQuery('#mycarouseltwo').jcarousel();
	});
	
</script>



</body>
</html>
<?php
}
else
{
	header("Location:http:qmsclients.php? msg=".urlencode(""));
}
?>
