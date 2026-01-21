<?php
  include("dbcon.php");
  if(isset($_POST['action']))
	{
	if($_POST['action']=="Save")
	{
		 $cname=$_POST['name'];
 		 $cno=$_POST['cno'];
 		 $addr=$_POST['addr'];
 		 $sc=$_POST['sc'];
		$quot=$_POST['quot'];
		$cons=$_POST['cons'];
		$auditor=$_POST['auditor'];
		$ad=$_POST['ad'];
		$paycb=$_POST['paycb'];
		$paycon=$_POST['paycon'];
		$cb=$_POST['cb'];
		$comm=$_POST['comm'];
		$tamt=$_POST['tamt'];
		$at=$_POST['at'];
		$flag=0;
		try
		{
		$sql = "INSERT INTO client_status(client_name,address,contact_no,scope,quot_amount,consultant,audit_type,cert_body,auditor,audit_date,payment_status_cb,payment_status_con,comments,pay_flag,total_payment_amount,completion_flag) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";                             
		$stmt = $dbh->prepare($sql);
		$stmt->bindParam(1, $cname);      
		$stmt->bindParam(2, $addr);
		$stmt->bindParam(3, $cno);
		$stmt->bindParam(4, $sc);
		$stmt->bindParam(5, $quot);      
		$stmt->bindParam(6, $cons);
		$stmt->bindParam(7, $at);
		$stmt->bindParam(8, $cb);
		$stmt->bindParam(9, $auditor);
		$stmt->bindParam(10, $ad);      
		$stmt->bindParam(11, $paycb);
		$stmt->bindParam(12, $paycon);
		$stmt->bindParam(13, $comm);
		$stmt->bindParam(14, $flag);
		$stmt->bindParam(15, $tamt);
		$stmt->bindParam(16, $flag);
		$q=$stmt->execute();
		$mess= "sucessful";

		}
		catch (Exception $ex) {
        		$mess = $ex;
			}
echo '<script type="text/javascript">'; 
				echo "alert('".$mess."');"; 
				echo 'window.location.href = "qmsclients.php";';
				echo '</script>';

	}
	}
?>


