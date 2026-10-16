<?php
    session_start();
    ob_start();
    if(!isset($_SESSION['useremail'])){
        header("location:auth1.php");
        exit();
    }

    include 'includes/config.php';

    // 1. Fetch User Data
    $email = $_SESSION['useremail'];
    $sql_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    $user = mysqli_fetch_assoc($sql_user);
    $user_id = $user['user_id'];

    // 2. Aggregate Data Metrics
    // Fetch Total Points
    $sql_points = mysqli_query($conn, "SELECT SUM(amount) as total_score FROM points WHERE user_id = '$user_id'");
    $points_row = mysqli_fetch_assoc($sql_points);
    $total_points = $points_row['total_score'] ? $points_row['total_score'] : 0;

    // Fetch Total Tasks Completed
    $sql_tasks_done = mysqli_query($conn, "SELECT COUNT(*) as tasks_completed FROM tasks WHERE user_id = '$user_id' AND status = 'completed'");
    $tasks_done_row = mysqli_fetch_assoc($sql_tasks_done);
    $tasks_completed = $tasks_done_row['tasks_completed'];

    // Fetch Total Milestones Completed
    $sql_milestones_done = mysqli_query($conn, "SELECT COUNT(*) as milestones_completed FROM milestones m JOIN tasks t ON m.task_id = t.task_id WHERE t.user_id = '$user_id' AND m.status = 'completed'");
    $milestones_done_row = mysqli_fetch_assoc($sql_milestones_done);
    $milestones_completed = $milestones_done_row['milestones_completed'];

    // 3. Fetch Commitment/Focus Time Records
    $sql_commitments = mysqli_query($conn, "SELECT ct.actual_minutes, t.task_title FROM commit_times ct JOIN tasks t ON ct.task_id = t.task_id WHERE ct.user_id = '$user_id' ORDER BY ct.recorded_at DESC");
    $commit_logs = [];
    while($row = mysqli_fetch_assoc($sql_commitments)) {
        $commit_logs[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskLock - Profile</title>
    <link href="images/icons.png" rel="shortcut icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/frontend.css">
    <link rel="stylesheet" href="css/profile.css">
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
                <a href="settings.php" class="nav-item"><i class="fa-solid fa-gear"></i> Settings</a>
                <a href="profile.php" class="nav-item active"><i class="fa-regular fa-user"></i> Profile</a>
            </div>
        </nav>

        <main class="main-content">
            <h1 class="page-title">User Profile</h1>

            <div class="card profile-header">
                <div class="avatar">
                    <i class="fa-regular fa-user"></i>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p><?php echo htmlspecialchars($user['major'] ?? ''); ?> Major, <?php echo htmlspecialchars($user['university'] ?? ''); ?></p>
                </div>
            </div>

            <div class="card stat-card">
                <h3>Completion Statistics</h3>
                <div class="stat-list">
                    <div class="stat-item">
                        <span class="label">Total Score Collected:</span>
                        <span class="value" style="color: #27AE60;"><?php echo $total_points; ?> Point(s)</span>
                    </div>
                    <div class="stat-item">
                        <span class="label">Total Tasks Completed:</span>
                        <span class="value"><?php echo $tasks_completed; ?> Tasks</span>
                    </div>
                    <div class="stat-item">
                        <span class="label">Total Milestone Completed:</span>
                        <span class="value"><?php echo $milestones_completed; ?> Milestone</span>
                    </div>
                </div>
            </div>

            <div class="card stat-card">
                <h3>Commitment Report</h3>
                <div class="log-list">
                    <?php if (empty($commit_logs)): ?>
                        <div style="color: #B0A8A6; font-style: italic; font-size: 14px;">No focus time tracking history available.</div>
                    <?php else: ?>
                        <?php foreach ($commit_logs as $log): ?>
                            <div class="log-item">
                                <i class="fa-regular fa-clock" style="margin-right: 8px; color: #e5b326;"></i> 
                                You spent <strong><?php echo intval($log['actual_minutes']); ?></strong> minutes on task "<?php echo htmlspecialchars($log['task_title']); ?>"
                            </div>
                        <?php endforeach; ?> <?php endif; ?>
                </div>

                <a class="logout-btn" href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket" style="margin-right: 8px;"></i> Logout</a>
            </div>
        </main>
    </div>

</body>
</html>