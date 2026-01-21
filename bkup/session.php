<?php
	include("dbcon.php");
	session_set_cookie_params(0);
   session_start();
  $user_check = $_SESSION['login_user'];
  
$sth = $dbh->query("SELECT * FROM `login` WHERE `username` ='".$user_check."'");
$result = $sth->fetch(PDO::FETCH_ASSOC);

   $login_session = $result['username'];
   $_SESSION['user_type']=$result['usertype'];
   if(!isset($_SESSION['login_user'])){
      header("location:http:index.php");
   }
?>