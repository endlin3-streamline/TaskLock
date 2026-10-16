<?php
    session_start();
    include 'includes/config.php';

    if(!isset($_SESSION['useremail']) || !isset($_POST['task_id']) || !isset($_POST['message'])){
        exit();
    }

    $email = $_SESSION['useremail'];
    $sql_user = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email'");
    $user = mysqli_fetch_assoc($sql_user);
    $user_id = $user['user_id'];
    
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $notif_id = "NOTIF" . str_pad(rand(1, 999999), 6, "0", STR_PAD_LEFT) . time();
    $now = date('Y-m-d H:i:s');

    // Insert into the ILF (Internal Logical File)
    mysqli_query($conn, "INSERT INTO notifications (notification_id, user_id, task_id, message, scheduled_at, created_at) 
                         VALUES ('$notif_id', '$user_id', '$task_id', '$message', '$now', '$now')");
?>