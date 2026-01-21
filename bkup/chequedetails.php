<?php
include("dbcon.php");

if(isset($_POST['action']))
{
	if($_POST['action']=="Add cheque details")
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
					$remains=$r['total_payment_amount'];
				}
			}
		}
		}
		catch(PDOException $e){
    			echo $e->getMessage();
		}
		$dbh = null;
	}
	if($_POST['action']=="Save")
	{
		
		try{
			$cid=$_POST['clientid'];
			$rp=$_POST['rp'];
			$cq=$_POST['cq'];
			$amt=$_POST['amt'];
			$bank=$_POST['bank'];
			$date=$_POST['date'];
			$sql = "INSERT INTO pay_details(client_id,amount_paid,cheque_details,date,bank) VALUES(?,?,?,?,?)";
			$stmt = $dbh->prepare($sql);
			$stmt->bindParam(1, $cid); 
			$stmt->bindParam(2, $amt); 
			$stmt->bindParam(3, $cq); 
			$stmt->bindParam(4, $date); 
			$stmt->bindParam(5, $bank); 
			$q=$stmt->execute();
			$mess= "sucessful";
			
			$remain=$rp-$amt;
			if($remain==0)
			{
				$sql = "UPDATE `client_status` SET pay_flag = '2' , total_payment_amount = '". $remain ."'WHERE client_id = '". $cid ."'";
				$result=$dbh->query($sql);

			}
			else
			{
				$sql = "UPDATE `client_status` SET total_payment_amount = '". $remain ."'WHERE client_id = '". $cid ."'";
				$result=$dbh->query($sql);
			}
			echo '<script type="text/javascript">'; 
			echo 'window.location.href = "payfollowsheet.php";';
			echo '</script>';
		}
		catch(PDOException $e){
    			echo $e->getMessage();
		}
		$dbh = null;		
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
		<div class="leaft_title"><h1>Cheque Details</h1></div>
	</div>
</div><!-- end page title --> 

<!-- Contant
======================================= -->

<div class="container">

<div class="content_fullwidth">

<div class="one_full">
    
		<div class="punchline_text_box">
       
        <div class="left">
		<form action="./chequedetails.php" method="post" enctype="multipart/form-data">   		
		<p><label for="name">Client Name:</label><input type="text" name="name" id="name" placeholder="Name" value="<?php echo $cname?>">&nbsp &nbsp &nbsp
		<label for="rp">Remaining Payment:</label><input type="text" name="rp" id="rp" placeholder="Remaining" value="<?php echo $remains?>"></p><br/>
		<p><label for="amt">Amount:</label><input type="text" name="amt" id="amt" placeholder="amt" value="">
		<label for="cq">Cheque details:</label><input type="text" name="cq" id="cq" placeholder="cq" value=""></p><br/>
		<p><label for="bank">Bank Name:</label><input type="text" name="bank" id="bank" placeholder="Bank" value="">
		<label for="date">Date:</label><input type="text" name="date" id="date" placeholder="Date" value=""></p><br/>
		<input name="action" type="submit" value="Save" alt="Save"/>
		<input name="clientid" type="hidden" value="<?php echo $cid ?>" alt="clientid"/>
		<input name="action" type="submit" value="Clear" alt="Clear"/>
  		<p>
		</form>
	  </div>
    </div>
</div>
</div>
