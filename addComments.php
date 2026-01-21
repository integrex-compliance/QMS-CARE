<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include_once './dbConnect.php';

if (isset($_POST['cbId']) && !empty($_POST['cbId']) && isset($_POST['certificationComment']) && !empty($_POST['certificationComment'])) {
    try {
        $cbComment = "UPDATE certification_body SET cert_comment = :cbComment WHERE certification_id = :cbId";
        $statement = $db->prepare($cbComment);
        $statement->bindParam(':cbId', $_POST['cbId']);
        $statement->bindParam(':cbComment', $_POST['certificationComment']);
        $statement->execute();
    } catch (Exception $ex) {
        
    }
} else if (isset($_POST['consultId']) && !empty($_POST['consultId']) && isset($_POST['consultComment']) && !empty($_POST['consultComment'])) {
    try {
        $consultingComment = "UPDATE consulting SET consulting_comment = :consultingComment  WHERE consulting_id = :consultingId";
        $statement = $db->prepare($consultingComment);
        $statement->bindParam(':consultingId', $_POST['consultId']);
        $statement->bindParam(':consultingComment', $_POST['consultComment']);
        $statement->execute();
    } catch (Exception $ex) {
        echo $ex;
    }
} else if (isset($_POST['rowId']) && !empty($_POST['rowId']) && isset($_POST['rowComment'])) {
    try {
        $certCommentCount = "SELECT count(*) as total from certification_body where certification_id = :certid ";
        $statement = $db->prepare($certCommentCount);
        $statement->bindParam(':certid', $_POST['rowId']);
        $statement->execute();
        $rowCount = $statement->fetch();
//        echo $rowCount['total']." -> ".$_POST['rowId'] ." -> ". $_POST['rowComment'];
        if ($_POST['pay'] == "Consulting") {
            $consultCmtUpdate = "UPDATE consulting SET consulting_comment = :cmt WHERE consulting_id = :cmtVal and consulting_fees= :cd";
            $statement = $db->prepare($consultCmtUpdate);
            $statement->bindParam(':cmt', $_POST['rowComment']);
            $statement->bindParam(':cmtVal', $_POST['rowId']);
            $statement->bindParam(':cd', $_POST['contractd']);
            $statement->execute();
            echo "in consult";
        }
        else{
//            echo "success";
            $certCmtUpdate = "UPDATE certification_body SET cert_comment = :cmt WHERE certification_id = :cmtVal and contract_details= :cd";
            $statement = $db->prepare($certCmtUpdate);
            $statement->bindParam(':cmt', $_POST['rowComment']);
            $statement->bindParam(':cmtVal', $_POST['rowId']);
            $statement->bindParam(':cd', $_POST['contractd']);
            $statement->execute();
            echo "in cert";
        } 
    } catch (Exception $ex) {
        echo $ex;
    }
}