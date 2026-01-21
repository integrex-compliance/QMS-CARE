<?php
  include("dbcon.php");
  if(isset($_POST['action']))
	{
	if($_POST['action']=="Save")
	{
		 $cname=$_POST['name'];
  $cno=$_POST['cno'];
  $loc=$_POST['addr'];
  $type=$_POST['type'];
  $cmt=$_POST['cmt'];
		try
		{
		$sql = "INSERT INTO marketing_status(client_name,location,ph_no,audit_type,comments) VALUES (?,?,?,?,?)";                             
		$stmt = $dbh->prepare($sql);
		$stmt->bindParam(1, $cname);      
		$stmt->bindParam(2, $loc);
		$stmt->bindParam(3, $cno);
		$stmt->bindParam(4, $type);
		$stmt->bindParam(5, $cmt);
		$q=$stmt->execute();
		$mess= "sucessful";
		}
		catch (Exception $ex) {
        		$mess = $ex;

			}

echo '<script type="text/javascript">'; 
				echo "alert('".$mess."');"; 
				echo 'window.location.href = "mrktclients.php";';
				echo '</script>';

	}
	}
?>
