<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include_once './dbConnect.php';

 if(isset ($_POST['customerid']) && !empty ($_POST['customerid'])){
    try {
        $serviceList = "SELECT * FROM services WHERE customer_id = :id";
        $statement = $db->prepare($serviceList);
        $statement->bindParam(':id', $_POST['customerid']);
        $statement->execute();
        $services = $statement->fetchAll();
        
        foreach ($services as $service){
            
        $certDlt = "update certification_body set is_delete = 'N' WHERE service_id = :id";
        $statement = $db->prepare($certDlt);
        $statement->bindParam(':id', $service['service_id']);
        $statement->execute();
        
        $consultingDlt = "update consulting set is_delete = 'N' WHERE service_id = :id";
        $statement = $db->prepare($consultingDlt);
        $statement->bindParam(':id', $service['service_id']);
        $statement->execute();
        
        }
        
        $serviceDlt = "update services set is_delete='N' WHERE customer_id = :id";
        $statement = $db->prepare($serviceDlt);
        $statement->bindParam(':id', $_POST['customerid']);
        $statement->execute();
        
        $customerDlt = "update customer_details set is_delete='N' WHERE customer_id = :id";
        $statement = $db->prepare($customerDlt);
        $statement->bindParam(':id', $_POST['customerid']);
        $statement->execute();
        
         header("Location: recycleCustomers.php");
        
    } catch (Exception $ex) {
        
    }
}