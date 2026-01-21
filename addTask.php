<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
include_once './dbConnect.php';

if (isset($_POST["user-type"]) && !empty($_POST["user-type"])) {
    try {
        $taskList = explode('~', $_POST['taskList']);
        $commentList = explode('~', $_POST['commentList']);

        for ($i = 0; $i < count($taskList); ++$i) {
            if ($taskList[$i] != "") {
                $sql = "INSERT INTO `task_manager`(`userid`, `task`,update_date) VALUES (:id, :task, :date)";
                $statement = $db->prepare($sql);
                $statement->bindParam(":id", $_POST["user-type"]);
                $statement->bindParam(":task", $taskList[$i]);
                $statement->bindParam(":date", date('Y-m-d'));
                $statement->execute();
            }
        }
        header("Location: taskMgmt.php");
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET["id"]) && !empty($_GET["id"]) && isset($_GET["columnValue"]) && !empty($_GET["columnValue"])) {
    try {
        $sql1 = "UPDATE `task_manager` SET comment = :comment WHERE id = :id";
        $statement = $db->prepare($sql1);
        $statement->bindParam(":id", $_GET["id"]);
        $statement->bindParam(":comment", $_GET["columnValue"]);
        $statement->execute();
//        echo "execute";
        $val = "success";
        echo json_encode($val);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET["row"]) && !empty($_GET["row"]) && isset($_GET["status"]) && !empty($_GET["status"])) {
    try {
        if ($_GET["status"] == "Completed") {
            $sql1 = "UPDATE `task_manager` SET status = :status, complete_date = :date WHERE id = :id";
            $statement = $db->prepare($sql1);
            $statement->bindParam(":id", $_GET["row"]);
            $statement->bindParam(":status", $_GET["status"]);
            $statement->bindParam(":date", date('Y-m-d'));
            $statement->execute();
            $val = "success";
            echo json_encode($val);
        } else {
            $sql1 = "UPDATE `task_manager` SET status = :status WHERE id = :id";
            $statement = $db->prepare($sql1);
            $statement->bindParam(":id", $_GET["row"]);
            $statement->bindParam(":status", $_GET["status"]);
            $statement->execute();
            $val = "success";
            echo json_encode($val);
        }
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET["rowid"]) && !empty($_GET["rowid"]) && isset($_GET["columnVal"]) && !empty($_GET["columnVal"])) {
    try {
        $sql1 = "UPDATE `task_manager` SET task = :task, update_date = :date WHERE id = :id";
        $statement = $db->prepare($sql1);
        $statement->bindParam(":id", $_GET["rowid"]);
        $statement->bindParam(":date", date('Y-m-d'));
        $statement->bindParam(":task", $_GET["columnVal"]);
        $statement->execute();
//        echo "execute";
        $val = "success";
        echo json_encode($val);
    } catch (Exception $ex) {
        
    }
} else if (isset($_GET["rowID"]) && !empty($_GET["rowID"]) && isset($_GET["user"]) && !empty($_GET["user"])) {
    try {
        $sql1 = "UPDATE `task_manager` SET userid = :user, update_date = :date WHERE id = :id";
        $statement = $db->prepare($sql1);
        $statement->bindParam(":id", $_GET["rowID"]);
        $statement->bindParam(":date", date('Y-m-d'));
        $statement->bindParam(":user", $_GET["user"]);
        $statement->execute();
//        echo "execute";
        $val = "success";
        echo json_encode($val);
    } catch (Exception $ex) {
        
    }
} else if (isset($_POST["taskdlt"]) && !empty($_POST["taskdlt"])) {
    try {
        $sql1 = "UPDATE `task_manager` SET is_deleted = 'Y' WHERE id = :id";
        $statement = $db->prepare($sql1);
        $statement->bindParam(":id", $_POST["taskdlt"]);
        $statement->execute();
        header("Location: taskMgmt.php");
    } catch (Exception $ex) {
        
    }
} else if(isset($_GET["taskid"]) && !empty ($_GET["taskid"]) && isset ($_GET["priority"]) && !empty ($_GET["priority"])){
    try {
//        echo "in";
        $sql1 = "UPDATE `task_manager` SET priority = :prio WHERE id = :id";
        $statement = $db->prepare($sql1);
        $statement->bindParam(":id", $_GET["taskid"]);
        $statement->bindParam(":prio", $_GET["priority"]);
        $statement->execute();
        $val = $_GET["priority"];
        echo json_encode($val);
    } catch (Exception $ex) {
        
    }
}
