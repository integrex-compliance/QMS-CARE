<?php
include("dbcon.php");
session_set_cookie_params(0);
 session_start();
if(isset($_POST['action']))
{          
    if($_POST['action']=="login")
    {
try
{
$sth = $dbh->query("SELECT * FROM `login` WHERE `username` ='".$_POST['username']."' and `password` ='".$_POST['password']."'");
$result = $sth->fetchAll(PDO::FETCH_ASSOC);
if(count($result)==1)
{
	$_SESSION['login_user'] = $_POST['username'];
	$message = "login Sucessfull";
	header("Location:http:qmsclients.php? msg=".urlencode(""));
}
else
{
	$message = "Invalid username or password";
	 echo "<script type='text/javascript'>alert('login failed!')</script>";
}
}
catch (Exception $ex) {
        echo $ex;
		echo $dbName;
    }
}
}
 
?>
<html>
<title>QMS CARE</title>
<form action="" method="post">
    <p>Username :      <input id="username" name="username" type="text" placeholder="username"></p>
    <p>Password :      <input id="password" name="password" type="password" placeholder="Password">
    <input name="action" type="hidden" value="login" /></p>
    <p><input type="submit" value="Login" /></p>
  </form>
</html>
