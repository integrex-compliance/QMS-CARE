<?php
//include_once 'site_header_end.php';

try{
$hostname='mysql:host=localhost;dbname=qmscare;charset=utf8';
$username='root';  // Default XAMPP MySQL username
$password='';      // Default XAMPP MySQL password (empty)
$db = new PDO($hostname,$username,$password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//var_dump($db);
}
catch(Exception $e)
{
	echo "Database Connection Error: " . $e->getMessage();
	echo "<br>Please check if:<br>";
	echo "1. XAMPP MySQL is running<br>";
	echo "2. Database 'qmscare' exists<br>";
	echo "3. MySQL credentials are correct<br>";
	die();
}

?>