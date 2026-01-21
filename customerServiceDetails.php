<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include_once './dbConnect.php';
include 'utility.php';

if(isset($_GET['customerID']) && !empty($_GET['customerID'])){
    try {
        if( isset($_GET['serviceType']) && !empty($_GET['serviceType'])){
        $serviceDetails = "SELECT * FROM services WHERE customer_id = :customer and service_type = :service";
        $statement = $db->prepare($serviceDetails);
        $statement->bindParam(':customer', $_GET['customerID']);
        $statement->bindParam(':service', $_GET['serviceType']);
        $statement->execute();
        }else{
           $serviceDetails = "SELECT * FROM services WHERE customer_id = :customer ";
        $statement = $db->prepare($serviceDetails);
        $statement->bindParam(':customer', $_GET['customerID']);
//        $statement->bindParam(':service', $_GET['serviceType']);
        $statement->execute(); 
        }
        $service = $statement->fetch();
        $serviceID = $service['service_id'];
        
        $certificationDetails = "SELECT * FROM certification_body WHERE service_id = :serviceId";
        $statement = $db->prepare($certificationDetails);
        $statement->bindParam(':serviceId',$serviceID);
        $statement->execute();
        $certification = $statement->fetchAll();
        
        $consultingDetails = "SELECT * FROM consulting WHERE service_id = :serviceId";
        $statement = $db->prepare($consultingDetails);
        $statement->bindParam(':serviceId',$serviceID);
        $statement->execute();
        $consulting = $statement->fetchAll();
        
        echo json_encode(array($service,$certification,$consulting));
        
    } catch (Exception $ex) {
        
    }
}