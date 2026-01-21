<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include_once './dbConnect.php';

if ($_POST['ischecked']=="false")  {
    try {
if ($_POST['file']=="non")
{
  $msApp = "UPDATE services SET ms_applicablity= 'no' WHERE service_id = :service";
        $statement = $db->prepare($msApp);
        $statement->bindParam(':service',$_POST['serviceID']);
        $statement->execute();
}
else if ($_POST['file']=="app")
{
        $msApp = "UPDATE services SET ms_applicablity= 'yes' WHERE service_id = :service";
        $statement = $db->prepare($msApp);
        $statement->bindParam(':service',$_POST['serviceID']);
        $statement->execute();
}
       
    } catch (Exception $ex) {
        echo $ex;
    }    
}

else if ($_POST['ischecked']=="true") {
    try {
if ($_POST['file']=="non")
{
	 $msApp = "UPDATE services SET ms_applicability= 'yes' WHERE service_id = :service";
        $statement = $db->prepare($msApp);
        $statement->bindParam(':service', $_POST['service']);
        $statement->execute();
}
else if ($_POST['file']=="app")
{	
        $msApp = "UPDATE services SET ms_applicability= 'no' WHERE service_id = :service";
        $statement = $db->prepare($msApp);
        $statement->bindParam(':service', $_POST['service']);
        $statement->execute();
}
      
    } catch (Exception $ex) {
        echo $ex;
    }
}

?>