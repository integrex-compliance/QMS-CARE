<?php

session_start();
include_once './dbConnect.php';
include_once './utility.php';

// ADD USER
if (isset($_POST['userName']) && !empty($_POST['userName']) && isset($_POST['userEmail']) && !empty($_POST['userEmail']) && isset($_POST['userRole']) && !empty($_POST['userRole']) && isset($_POST['userPassword']) && !empty($_POST['userPassword'])) {
    try {
        $hashedpwd = password_hash($_POST['userPassword'], PASSWORD_DEFAULT);
        $userName = trim($_POST['userName'] ?? '');
        $userAbbr = trim($_POST['userAbbr'] ?? '');
        $userEmail = trim($_POST['userEmail'] ?? '');
        $userRole = trim($_POST['userRole'] ?? '');
        $userEid = trim($_POST['userEid'] ?? '');
        $userPcId = trim($_POST['userPcId'] ?? '');
        $designation = trim($_POST['designationAssigned'] ?? '');
        $applicableRecords = trim($_POST['applicableRecords'] ?? '');
        $resourceIssueRecord = trim($_POST['resourceIssueRecord'] ?? '');
        $workDescription = trim($_POST['workDescription'] ?? '');

        $addUser = "INSERT INTO user(user_name, abbreviation, email_id, password, role, e_id, pc_id, designation_assigned, applicable_records, resource_issue_record, work_description, is_deleted) 
                    VALUES (:name, :userAbbr, :email, :password, :role, :eid, :pcid, :designation, :applicableRecords, :resourceIssueRecord, :workDescription, '0')";
        $statement = $db->prepare($addUser);
        $statement->bindParam(':name', $userName);
        $statement->bindParam(':userAbbr', $userAbbr);
        $statement->bindParam(':email', $userEmail);
        $statement->bindParam(':password', $hashedpwd);
        $statement->bindParam(':role', $userRole);
        $statement->bindParam(':eid', $userEid);
        $statement->bindParam(':pcid', $userPcId);
        $statement->bindParam(':designation', $designation);
        $statement->bindParam(':applicableRecords', $applicableRecords);
        $statement->bindParam(':resourceIssueRecord', $resourceIssueRecord);
        $statement->bindParam(':workDescription', $workDescription);
        $statement->execute();
        header("Location: addUsers.php?msg=user_added");
        exit();
    } catch (Exception $ex) {
        header("Location: addUsers.php?error=" . urlencode($ex->getMessage()));
        exit();
    }
} 
// UPDATE USER
else if (isset($_POST['userId']) && !empty($_POST['userId']) && isset($_POST['editUserName']) && !empty($_POST['editUserName']) && isset($_POST['editUserEmail']) && !empty($_POST['editUserEmail']) && isset($_POST['editUserRole']) && !empty($_POST['editUserRole'])) {
    try {
        $userId = (int)$_POST['userId'];
        $userName = trim($_POST['editUserName'] ?? '');
        $userAbbr = trim($_POST['editUserAbbr'] ?? '');
        $userEmail = trim($_POST['editUserEmail'] ?? '');
        $userRole = trim($_POST['editUserRole'] ?? '');
        $userEid = trim($_POST['editUserEid'] ?? '');
        $userPcId = trim($_POST['editUserPcId'] ?? '');
        $designation = trim($_POST['editDesignationAssigned'] ?? '');
        $applicableRecords = trim($_POST['editApplicableRecords'] ?? '');
        $resourceIssueRecord = trim($_POST['editResourceIssueRecord'] ?? '');
        $workDescription = trim($_POST['editWorkDescription'] ?? '');

        $updateUser = "UPDATE user SET 
            user_name = :name, 
            abbreviation = :abbr, 
            email_id = :email, 
            role = :role, 
            e_id = :eid, 
            pc_id = :pcid, 
            designation_assigned = :designation, 
            applicable_records = :applicableRecords, 
            resource_issue_record = :resourceIssueRecord, 
            work_description = :workDescription 
            WHERE user_id = :id";
        $statement = $db->prepare($updateUser);
        $statement->bindParam(':name', $userName);
        $statement->bindParam(':abbr', $userAbbr);
        $statement->bindParam(':email', $userEmail);
        $statement->bindParam(':role', $userRole);
        $statement->bindParam(':eid', $userEid);
        $statement->bindParam(':pcid', $userPcId);
        $statement->bindParam(':designation', $designation);
        $statement->bindParam(':applicableRecords', $applicableRecords);
        $statement->bindParam(':resourceIssueRecord', $resourceIssueRecord);
        $statement->bindParam(':workDescription', $workDescription);
        $statement->bindParam(':id', $userId);
        $statement->execute();
        header("Location: addUsers.php?msg=user_updated");
        exit();
    } catch (Exception $ex) {
        header("Location: addUsers.php?error=" . urlencode($ex->getMessage()));
        exit();
    }
} 
// DELETE USER
else {
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        try {
            $userId = (int)$_POST['user_id'];
            $deleteUser = "UPDATE user SET is_deleted = '1' WHERE user_id = :id";
            $statement = $db->prepare($deleteUser);
            $statement->bindParam(':id', $userId);
            $statement->execute();
            header("Location: addUsers.php?msg=user_deleted");
            exit();
        } catch (Exception $ex) {
            header("Location: addUsers.php?error=" . urlencode($ex->getMessage()));
            exit();
        }
    }
}
