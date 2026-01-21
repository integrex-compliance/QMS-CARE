<?php
	try{
		$hostname='localhost';
		$username='qmsadmin';
		$password='qmsadmin';
		$dbh = new PDO("mysql:host=$hostname;dbname=qmscare",$username,$password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e){
    		echo $e->getMessage();
	}

?>