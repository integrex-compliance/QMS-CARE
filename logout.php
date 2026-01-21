<?php
session_start();
session_destroy();
 if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: index.php");
    }