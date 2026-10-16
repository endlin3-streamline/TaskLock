<?php
    session_start();
    $_SESSION["admin_USER"];
    unset($_SESSION["admin_USER"]);

    session_unset();

    session_destroy();
    header('location:login.php');
?>