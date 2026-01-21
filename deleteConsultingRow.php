<?php
session_start();
include_once './dbConnect.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode(array('success' => false, 'message' => 'Invalid consulting id'));
        return;
    }

    $consultingId = $_POST['id'];

    $deleteSql = "DELETE FROM consulting WHERE consulting_id = :id";
    $statement = $db->prepare($deleteSql);
    $statement->bindParam(':id', $consultingId);
    $statement->execute();

    echo json_encode(array('success' => true));
} catch (Exception $ex) {
    echo json_encode(array('success' => false, 'message' => $ex->getMessage()));
}
