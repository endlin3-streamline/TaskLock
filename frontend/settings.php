<?php
    session_start();
    ob_start();
    if(!isset($_SESSION['useremail'])){
        header("location:auth1.php");
        exit();
    }

    include 'includes/config.php';

    // 1. Fetch User
    $email = $_SESSION['useremail'];
    $sql_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    $user = mysqli_fetch_assoc($sql_user);
    $user_id = $user['user_id'];

    // 2. Fetch User Settings
    $sql_settings = mysqli_query($conn, "SELECT * FROM settings WHERE user_id = '$user_id'");
    $user_settings = mysqli_fetch_assoc($sql_settings);

    // If settings don't exist for this user yet, create default settings
    if (!$user_settings) {
        $setting_id = "SET" . str_pad(rand(1, 999999), 6, "0", STR_PAD_LEFT) . time();
        mysqli_query($conn, "INSERT INTO settings (setting_id, user_id, alarm_enabled, notifications_enabled, updated_at) 
                            VALUES ('$setting_id', '$user_id', 1, 1, NOW())");
        
        $sql_settings = mysqli_query($conn, "SELECT * FROM settings WHERE user_id = '$user_id'");
        $user_settings = mysqli_fetch_assoc($sql_settings);
    }

    // 3. Handle Auto-Save Form Submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
        // Checkboxes only send data if they are checked
        $alarm_enabled = isset($_POST['alarm_enabled']) ? 1 : 0;
        $notifications_enabled = isset($_POST['notifications_enabled']) ? 1 : 0;

        mysqli_query($conn, "UPDATE settings 
                             SET alarm_enabled = '$alarm_enabled', 
                                 notifications_enabled = '$notifications_enabled', 
                                 updated_at = NOW() 
                             WHERE user_id = '$user_id'");
        
        header("Location: settings.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskLock - Settings</title>
    <link href="images/icons.png" rel="shortcut icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="css/frontend.css">
    <link rel="stylesheet" href="css/settings.css">
</head>
<body>

    <div class="app">
        <nav class="navbar">
            <div class="logo">
                <i class="fa-solid fa-lock"></i> TaskLock
            </div>
            <div class="nav-links">
                <a href="frontend.php" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
                <a href="calendar.php" class="nav-item"><i class="fa-regular fa-calendar-days"></i> Calendar</a>
                <a href="settings.php" class="nav-item active"><i class="fa-solid fa-gear"></i> Settings</a>
                <a href="profile.php" class="nav-item"><i class="fa-regular fa-user"></i> Profile</a>
            </div>
        </nav>

        <div class="settings-wrapper">
            <div class="settings-card">
                <h2 style="color: white; text-align: center; font-size: 24px; font-weight: 600;">Preference Settings</h2>
                
                <form method="POST">
                    <input type="hidden" name="update_settings" value="1">
                    
                    <div class="setting-list">
                        <div class="setting-item">
                            <span>Alarm</span>
                            <label class="switch">
                                <input type="checkbox" name="alarm_enabled" onchange="this.form.submit()" <?php echo ($user_settings['alarm_enabled'] == 1) ? 'checked' : ''; ?>>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="setting-item">
                            <span>Notifications</span>
                            <label class="switch">
                                <input type="checkbox" name="notifications_enabled" onchange="this.form.submit()" <?php echo ($user_settings['notifications_enabled'] == 1) ? 'checked' : ''; ?>>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>
</html>