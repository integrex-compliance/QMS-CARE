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
		
		$sql = "UPDATE `marketing_status` SET comments = '". $_POST['cmt'] ."', audit_type = '" .$_POST['type']. "', location ='". $_POST['addr'] ."', ph_no='". $_POST['cno'] ."' WHERE client_name = '". $_POST['name'] ."' AND location = '". $_POST['oldaddr'] ."'";                             
		$result=$dbh->query($sql);
		//echo $result;
		}
		catch(PDOException $e){
    			echo $e->getMessage();
		}
		$dbh = null;
		echo '<script type="text/javascript">'; 
				echo 'window.location.href = "mrktclients.php";';
				echo '</script>';

	}	
	if($_POST['action']=="Remove")
	{
		try{
   		
		$sql="DELETE FROM `marketing_status` WHERE client_name = '". $_POST['name'] . "' AND location = '". $_POST['addr'] ."'";
		$result=$dbh->query($sql);
		
		}
		catch(PDOException $e){
    			echo $e->getMessage();
		}
		$dbh = null;
		echo '<script type="text/javascript">'; 
				echo 'window.location.href = "mrktclients.php";';
				echo '</script>';
	}
	else
	{
		try{
   		
		$stmt = $dbh->query("SELECT * FROM `marketing_status`");
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$ec=0;
		foreach( $results as $r)//<-------line 14
		{
			if($r['client_name']!="")
			{
				if($ec==$cnt)
				{
					$cname=$r['client_name'];
					$loc=$r['location'];
					$phno=$r['ph_no'];
					$type=$r['audit_type'];
					$comm=$r['comments'];
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
}
?>

<div class="one_full">
    
		<div class="punchline_text_box">
        <div class="left">
        <strong>New Client Details</strong>
        
        </div>
        <div class="left">
		<form action="./editmrktclient.php" method="post" enctype="multipart/form-data">
		<p><label for="name">Client Name:</label><input type="text" name="name" id="name" placeholder="Name" value="<?php echo $cname?>"></p><br/>
		<p><label for="cno">Contact No:</label><input type="text" name="cno" id="cno" placeholder="Contact No" value="<?php echo $phno?>"></p><br/>
		<p><label for="addr">Address:</label><input type="text" name="addr" id="addr" placeholder="Address" value="<?php echo $loc?>"></p><br/>
		<p><label for="type">Audit type:</label><input type="type" name="type" id="type" placeholder="audit type" value="<?php echo $type?>"></p><br/>
		<p><label for="cmt">Comments:</label><textarea name="cmt" id="cmt"  placeholder="Comments" autocomplete="off"; height: 50px; width: 600px> <?php echo $comm?> </textarea></p><br/>
   		
		<input name="action" type="submit" value="Save" alt="Save"/>
		
		<input type="hidden", name="oldaddr", value="<?php echo $loc?>"/>
		<input name="action" type="submit" value="Remove" alt="Remove"/>
  		<p>
		</form>
	  </div>
    </div>

