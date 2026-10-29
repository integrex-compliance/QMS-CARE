<?php

//session_start();
include_once './dbConnect.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Utitlity {

    static function checkIfLoggedIn() {
        if (isset($_SESSION ['username'])) {
            return 1;
        } else {
            return 0;
        }
    }

    static function checkForUserAdmin() {
        if (isset($_SESSION ['username']) && !empty($_SESSION ['username']) && $_SESSION ['role'] == "Admin")
            return 1;
        else
            return 0;
    }

    static function checkForUserMarketing() {
        if (isset($_SESSION ['username']) && !empty($_SESSION ['username']) && $_SESSION ['role'] == "Marketing")
            return 1;
        else
            return 0;
    }

    static function checkForUserConsultant() {
        if (isset($_SESSION ['username']) && !empty($_SESSION ['username']) && $_SESSION ['role'] == "Working Project")
            return 1;
        else
            return 0;
    }

    static function checkForUserFinance() {
        if (isset($_SESSION ['username']) && !empty($_SESSION ['username']) && $_SESSION ['role'] == "Finance")
            return 1;
        else
            return 0;
    }

    static function checkForOfficeStaff() {
        if (isset($_SESSION['username']) && !empty($_SESSION['username']) && $_SESSION['role'] == "Office Staff")
            return 1;
        else
            return 0;
    }
    
    static function checkForMainAdmin(){
        if (isset($_SESSION['username']) && !empty($_SESSION['username']) && $_SESSION['role'] == "Main Admin")
            return 1;
        else
            return 0;
    }
    
    static function checkForOfficeBDM(){
        if (isset($_SESSION['username']) && !empty($_SESSION['username']) && $_SESSION['role'] == "Office BDM")
            return 1;
        else
            return 0;
    }
    
    static function checkTaskManager(){
        if (isset($_SESSION['username']) && !empty($_SESSION['username']) && $_SESSION['role'] == "task manager")
            return 1;
        else
            return 0;
    }

    static function checkForConsultant() {
        if (isset($_SESSION['username']) && !empty($_SESSION['username']) && isset($_SESSION['role'])) {
            $r = strtolower(str_replace([' ', '_', '-'], '', (string)$_SESSION['role']));
            if ($r === 'consultant') {
                return 1;
            }
        }
        return 0;
    }

    static function compress($source, $destination, $quality) {
        $info = getimagesize($source);
        if ($info ['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);
        elseif ($info ['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);
        elseif ($info ['mime'] == 'image/png')
            $image = imagecreatefrompng($source);
        imagejpeg($image, $destination, $quality);
        return $destination;
    }

    static function getHomePageForUser() {
        if (Utitlity::checkForConsultant()) {
            return "myDashboard.php";
        }
        if (Utitlity::checkForUserAdmin() || Utitlity::checkForOfficeStaff() || Utitlity::checkForMainAdmin()) {
            return "homePage.php";
        }
        if (Utitlity::checkForUserConsultant() || Utitlity::checkForOfficeStaff()) {
            return "workingDetails.php";
        }
        if (Utitlity::checkForUserMarketing() || Utitlity::checkForOfficeStaff()) {
            return "marketingDetails.php";
        }
        if (Utitlity::checkForUserFinance() || Utitlity::checkForOfficeStaff()) {
            return "financeDetails.php";
        }
    }

}
