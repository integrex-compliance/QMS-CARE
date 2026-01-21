<?php
include("dbcon.php");

if(isset($_POST['action']))
{echo "here";
	if($_POST['action']=="View")
	{
		try
		{
		echo "here";
		$title = array("Client Name","Address","Contact Details","Scope","Last audit type","Last audit date","Quotation","consultant","Auditor","certification Body","CB Payment","consultancy Payment","Comments","Remaining Payment","Payment details");
		$content = array();
		$pay_title = array("Amount","cheque details","Date","Bank");
		$pay_det = array();
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
					$content[0]=$r['client_name'];
					$content[1]=$r['address'];
					$content[2]=$r['contact_no'];
					$content[3]=$r['scope'];
					$content[4]=$r['quot_amount'];
					$content[5]=$r['consultant'];
					$content[6]=$r['cert_body'];
					$content[7]=$r['auditor'];
					$content[8]=$r['audit_date'];
					$content[9]=$r['audit_type'];
					$content[10]=$r['payment_status_cb'];
					$content[11]=$r['payment_status_con'];
					$content[12]=$r['comments'];
					$content[13]=$r['total_payment_amount'];
				}
			}
		}
		
		$stmt = $dbh->query("SELECT * FROM `pay_details`");
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach( $results as $r)//<-------line 14
		{
			$i=0;
			if($cid==$r['client_id'])
				{
					$pay_det[i][0]=$r['amount_paid'];
					$pay_det[i][1]=$r['cheque_details'];
					$pay_det[i][2]=$r['date'];
					$pay_det[i][3]=$r['bank'];
				}
			$i++;
		}
	
    		$dbh = null;
		}
		catch(Exception $e){
    			echo $e->getMessage();
		}
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
		
			<div class="table-style">
                <table class="table-list">
			<tr>
				<th>Title</th>
				<th>Content</th>
			</tr>
			<?php
				for($r=0;$r<count($title);$r++)
				{
			?>
			<tr>
				<td><?php echo $title[$r];?></td>
				<td>
				<?php 
					if($title[$r]=="Payment details")
					{
				?>
						
					<div class="table-style">
              				  <table class="table-list">
						<tr>
						<?php
						foreach($pay_title as $p)
						{
						?>
						<th><?php echo $p;?></th>
						<?php
						}
						?>
						</tr>
						
						<?php
						for($k=0;$k<count($pay_det);$k++)
						{
						?>
						<tr>
						<td><?php echo $pay_det[$k][0];?></td>
						<td><?php echo $pay_det[$k][1];?></td>
						<td><?php echo $pay_det[$k][2];?></td>
						<td><?php echo $pay_det[$k][3];?></td>
						</tr>
						<?php	
						}
						?>
					</table>
					</div>   
					<?php
					}
				 	else
				 	echo $content[$r];	
				 		?>
				 </td>
			</tr>
			<?php
				}
			?>
			
			</table>
			</div>   
	  </div>
    </div>
</div>
</div>


