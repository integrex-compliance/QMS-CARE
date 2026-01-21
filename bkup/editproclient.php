<?php
include("dbcon.php");

if(isset($_POST['action']))
	{
		
		//echo $_POST['action'];
		$cnt=$_POST['action'];
		$cname="";
		$loc="";
		$phno="";
		$comm="";
	if($_POST['action']=="Save")
	{
		try{
   		// Connect $dbh = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=".realpath("database/list_of_clients.mdb"));
		$count=1;
		
		$sql = "UPDATE `client_status` SET comments = '". $_POST['cmt'] ."', address='". $_POST['addr'] ."', contact_no='". $_POST['cno'] ."', scope='". $_POST['sc'] ."', quot_amount = '". $_POST['qa'] ."', consultant='". $_POST['cons'] ."', cert_body = '". $_POST['cb'] ."', auditor='". $_POST['aud'] ."', audit_date='". $_POST['ad'] . "', payment_status_cb='". $_POST['paycb'] ."', payment_status_con='". $_POST['paycon'] ."', comments='". $_POST['comm'] ."', audit_type='". $_POST['at'] ."', total_payment_amount='". $_POST['rp'] ."', client_name = '". $_POST['name'] ."' where client_id = '". $_POST['clientid'] ."'";  

                           
		$result=$dbh->query($sql);
		//echo $result;
		}
		catch(PDOException $e){
    			echo $e->getMessage();
		}
		$dbh = null;
		echo '<script type="text/javascript">'; 
				echo 'window.location.href = "qmsclients.php";';
				echo '</script>';

	}	
	if($_POST['action']=="Remove")
	{
		try{
   		
		$sql="DELETE FROM `client_status` WHERE client_name = '". $_POST['name'] . "'  AND client_id = '". $_POST['clientid'] ."'";  
		$result=$dbh->query($sql);
		}
		catch(PDOException $e){
    			echo $e->getMessage();
		}
		$dbh = null;
		echo '<script type="text/javascript">'; 
				echo 'window.location.href = "qmsclients.php";';
				echo '</script>';
	}
	if($_POST['action']=="Edit")
	{
		try{
   		$cid=$_POST['clientid'];

	$stmt = $dbh->query("SELECT * FROM `client_status`");
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$ec=0;
		foreach( $results as $r)//<-------line 14
		{
			if($r['client_name']!="")
			{
				if($cid==$r['client_id'])
				{

					$cname=$r['client_name'];
					$addr=$r['address'];
					$cno=$r['contact_no'];
					$sc=$r['scope'];
					$qa=$r['quot_amount'];
					$cons=$r['consultant'];
					$cb=$r['cert_body'];
					$aud=$r['auditor'];
					$ad=$r['audit_date'];
					$at=$r['audit_type'];
					$paycb=$r['payment_status_cb'];
					$paycon=$r['payment_status_con'];
					$comm=$r['comments'];
					$rp=$r['total_payment_amount'];

				}
  				$ec++;
			}
		}
		
		
    		$dbh = null;
		}
		catch(PDOException $e){
    			echo $e->getMessage();
		}
	}
		if($_POST['action']=="Add to payment follow")
	{
		try{
   			$cid=$_POST['clientid'];
			$sql = "UPDATE `client_status` SET pay_flag = '1' WHERE client_id = '". $cid ."'";
			$result=$dbh->query($sql);
		}
		catch(PDOException $e){
    			echo $e->getMessage();
		}
		$dbh = null;
		echo '<script type="text/javascript">'; 
				echo 'window.location.href = "qmsclients.php";';
				echo '</script>';
	}
		if($_POST['action']=="Completed")
	{
		try{
   			$cid=$_POST['clientid'];
			$sql = "UPDATE `client_status` SET completion_flag = '1' WHERE client_id = '". $cid ."'";
			$result=$dbh->query($sql);
		}
		catch(PDOException $e){
    			echo $e->getMessage();
		}
		$dbh = null;
		echo '<script type="text/javascript">'; 
				echo 'window.location.href = "qmsclients.php";';
				echo '</script>';
	}

}
?>

<!doctype html>
<!--[if IE 7 ]>    <html lang="en-gb" class="isie ie7 oldie no-js"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en-gb" class="isie ie8 oldie no-js"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en-gb" class="isie ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en-gb" class="no-js"> <!--<![endif]-->

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
<div class="clearfix"></div>
 
<div class="page_title">

	<div class="container">
		<div class="leaft_title"><h1>New Client Details</h1></div>
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

		<form action="./editproclient.php" method="post" enctype="multipart/form-data">   		
		<p><label for="namec">Client Name:</label><input type="text" name="name" id="name" placeholder="Name" value="<?php echo $cname?>" autocomplete="off">
		<label for="cnoc">Contact No:</label><input type="text" name="cno" id="cno" placeholder="Contact No" value="<?php echo $cno?>" autocomplete="off"></p><br/>
		<p><label for="addre">Address:</label><input type="text" name="addr" id="addr" placeholder="Address" value="<?php echo $addr?>" autocomplete="off">
		<label for="cerscope">Scope:</label><input type="text" name="sc" id="sc" placeholder="Scope" value="<?php echo $sc?>" autocomplete="off">
<label for="at">Audit Type:</label><input type="text" name="at" id="at" placeholder="Audit Type" value="<?php echo $at?>" autocomplete="off"></p></br>
		<p><label for="qout">Quotation:</label><input type="text" name="qa" id="qa" placeholder="Quotation" value="<?php echo $qa?>" autocomplete="off">
		<label for="consu">Consultant:</label><input type="text" name="cons" id="cons" placeholder="Consultant" value="<?php echo $cons?>" autocomplete="off"></p><br/>
		<p><label for="audi">Auditor:</label><input type="text" name="aud" id="aud" placeholder="auditor" value="<?php echo $aud?>" autocomplete="off">
		<label for="adt">Audit Date:</label><input type="text" name="ad" id="ad" placeholder="audit date" value="<?php echo $ad?>" autocomplete="off"></p><br/>
		<p><label for="paycbc">Payment status CB:</label><input type="text" name="paycb" id="paycb" value="<?php echo $paycb?>" autocomplete="off">
		<label for="payconc">payment consultancy:</label><input type="text" name="paycon" id="paycon" value="<?php echo $paycon?>" autocomplete="off"></p><br/>
		<p><label for="commc">Comments:</label><input style='width:100%;' type="text" name="comm" id="comm" placeholder="comm" value="<?php echo $comm?>" autocomplete="off">
		<label for="cbc">Certification Body:</label><input type="text" name="cb" id="cb" placeholder="Cert body" value="<?php echo $cb?>" autocomplete="off"></p><br/>
	<label for="rp">Remaining Payment:</label><input type="text" name="rp" id="cb" placeholder="Rem pay" value="<?php echo $rp?>"></p><br/>

		<input name="action" type="submit" value="Save" alt="Save"/>
		<input name="clientid" type="hidden" value="<?php echo $cid ?>" alt="clientid"/>
		<input name="action" type="submit" value="Remove" alt="Remove"/>
  		<p>
		</form>
	  </div>
    </div>
</div>
</div>



