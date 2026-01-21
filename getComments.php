<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include_once './dbConnect.php';

if(isset($_GET['cbID']) && !empty($_GET['cbID'])){    
    try {
        $selectCbComments = "SELECT * FROM cert_comment WHERE cert_id = :cbId";
        $statement = $db->prepare($selectCbComments);
        $statement->bindParam(':cbId', $_GET['cbID']);
        $statement->execute();
        $allCbComments = $statement->fetchAll();
        
        echo json_encode($allCbComments);
    } catch (Exception $ex) {
        
    }
} else if(isset ($_GET['consultingID']) && !empty ($_GET['consultingID'])){
    try {
        $selectConsultingComment = "SELECT * FROM consulting_comment WHERE consulting_id = :consultingId";
        $statement = $db->prepare($selectConsultingComment);
        $statement->bindParam(':consultingId', $_GET['consultingID']);
        $statement->execute();
        $allConsultingComments = $statement->fetchAll();
        
        echo json_encode($allConsultingComments);
    } catch (Exception $ex) {
        
    }
}