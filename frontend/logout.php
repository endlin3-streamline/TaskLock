<?php
    session_start();
    $_SESSION["email"];
    unset($_SESSION["email"]);

    session_unset();

    session_destroy();
    header('location:auth1.php');
?>