<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
include_once './dbConnect.php';
include 'utility.php';

function uploadFile($file) {
    $uploaddir = 'uploads/customers/' . $_POST["customerid"] . '/';

    $uploadfile = $uploaddir . basename($_FILES['customerAttachemnt']['name']);
    if (!file_exists($uploaddir)) {
        mkdir($uploaddir, 0777, true);
    }
    //insert into document table
    if (move_uploaded_file($_FILES['customerAttachemnt']['tmp_name'], $uploadfile)) {        
    }
    // process file upload - end
    return $uploadfile;
}

$file = "Image";
if (isset($_FILES ["customerAttachemnt"]) && !( $_FILES['customerAttachemnt']['size'] == 0 ) && isset($_FILES ["customerAttachemnt"] ["name"])) {
    try {
        $tf = uploadFile($file);
        $sql = "INSERT INTO document_upload(cstomer_id, upload_path, description) VALUES (:customer, :path, :desc)";
        $statement = $db->prepare($sql);
        $statement->bindParam(':customer', $_POST["customerid"]);
        $statement->bindParam(':path', $tf);
        $statement->bindParam(':desc', $_POST['attachmentinfo']);
        $statement->execute();
        
        header("Location: customerAccountDetails.php?customerid=".$_POST["customerid"]);
    } catch (Exception $ex) {
        
    }
} else if(isset ($_POST["attachemnt"]) && !empty ($_POST["attachemnt"]) && isset ($_POST["customer-id"]) && !empty ($_POST["customer-id"])){
    try {
        $sql = "DELETE FROM `document_upload` WHERE `id` = :id";
        $statement = $db->prepare($sql);
        $statement->bindParam(":id", $_POST["attachemnt"]);
        $statement->execute();
        header("Location: customerAccountDetails.php?customerid=".$_POST["customer-id"]);
    } catch (Exception $ex) {
        
    }
}