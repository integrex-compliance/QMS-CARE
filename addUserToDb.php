<?php

session_start();
include_once './dbConnect.php';
include_once './utility.php';

// ADD USER
if (isset($_POST['userName']) && !empty($_POST['userName']) && isset($_POST['userEmail']) && !empty($_POST['userEmail']) && isset($_POST['userRole']) && !empty($_POST['userRole']) && isset($_POST['userPassword']) && !empty($_POST['userPassword'])) {
    try {
        $hashedpwd = password_hash($_POST['userPassword'], PASSWORD_DEFAULT);
        $userName = trim(isset($_POST['userName']) ? $_POST['userName'] : '');
        $userAbbr = trim(isset($_POST['userAbbr']) ? $_POST['userAbbr'] : '');
        $userEmail = trim(isset($_POST['userEmail']) ? $_POST['userEmail'] : '');
        $userRole = trim(isset($_POST['userRole']) ? $_POST['userRole'] : '');
        $userEid = trim(isset($_POST['userEid']) ? $_POST['userEid'] : '');
        $userPcId = trim(isset($_POST['userPcId']) ? $_POST['userPcId'] : '');
        $designation = trim(isset($_POST['designationAssigned']) ? $_POST['designationAssigned'] : '');
        $applicableRecords = trim(isset($_POST['applicableRecords']) ? $_POST['applicableRecords'] : '');
        $resourceIssueRecord = trim(isset($_POST['resourceIssueRecord']) ? $_POST['resourceIssueRecord'] : '');
        $workDescription = trim(isset($_POST['workDescription']) ? $_POST['workDescription'] : '');

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
        $userName = trim(isset($_POST['editUserName']) ? $_POST['editUserName'] : '');
        $userAbbr = trim(isset($_POST['editUserAbbr']) ? $_POST['editUserAbbr'] : '');
        $userEmail = trim(isset($_POST['editUserEmail']) ? $_POST['editUserEmail'] : '');
        $userRole = trim(isset($_POST['editUserRole']) ? $_POST['editUserRole'] : '');
        $userEid = trim(isset($_POST['editUserEid']) ? $_POST['editUserEid'] : '');
        $userPcId = trim(isset($_POST['editUserPcId']) ? $_POST['editUserPcId'] : '');
        $designation = trim(isset($_POST['editDesignationAssigned']) ? $_POST['editDesignationAssigned'] : '');
        $applicableRecords = trim(isset($_POST['editApplicableRecords']) ? $_POST['editApplicableRecords'] : '');
        $resourceIssueRecord = trim(isset($_POST['editResourceIssueRecord']) ? $_POST['editResourceIssueRecord'] : '');
        $workDescription = trim(isset($_POST['editWorkDescription']) ? $_POST['editWorkDescription'] : '');

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
