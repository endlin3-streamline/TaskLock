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

    // 2. Fetch Total Points
    $sql_points = mysqli_query($conn, "SELECT SUM(amount) as total_score FROM points WHERE user_id = '$user_id'");
    $points_row = mysqli_fetch_assoc($sql_points);
    $total_points = $points_row['total_score'] ? $points_row['total_score'] : 0;

    // 3. Setup Calendar Month & Year Variables
    $selected_month = isset($_GET['m']) ? intval($_GET['m']) : date('n');
    $selected_year = isset($_GET['y']) ? intval($_GET['y']) : date('Y');

    // 4. Fetch Tasks for the Selected Month
    $sql_tasks = mysqli_query($conn, "SELECT task_title, deadline, priority, status FROM tasks 
                                      WHERE user_id = '$user_id' 
                                      AND MONTH(deadline) = $selected_month 
                                      AND YEAR(deadline) = $selected_year");

    //5. Calendar Grid Calculation
    // Fetch Public Holidays API for EIF Implementation
    $holiday_api_url = "https://date.nager.at/api/v3/PublicHolidays/$selected_year/ID";
    $holiday_json = @file_get_contents($holiday_api_url); 
    $holidays_by_day = [];
    
    if ($holiday_json) {
        $holidays_data = json_decode($holiday_json, true);
        foreach ($holidays_data as $holiday) {
            $h_time = strtotime($holiday['date']);
            // Only save holidays that belong to the currently selected month
            if (date('n', $h_time) == $selected_month) {
                $h_day = (int)date('j', $h_time);
                $holidays_by_day[$h_day][] = $holiday['localName'];
            }
        }
    }
    
    // Group tasks by day and separate pending tasks for the sidebar
    $tasks_by_day = [];
    $pending_tasks = [];
    $total_tasks_this_month = 0;
    
    while($row = mysqli_fetch_assoc($sql_tasks)) {
        if ($row['deadline'] && $row['deadline'] != '0000-00-00') {
            $day = (int)date('j', strtotime($row['deadline']));
            $tasks_by_day[$day][] = $row;
            $total_tasks_this_month++;
            
            if ($row['status'] != 'completed') {
                $pending_tasks[] = $row;
            }
        }
    }

    // 5. Calendar Grid Calculation
    $first_day_of_month = date('w', strtotime("$selected_year-$selected_month-01"));
    $first_day_of_month = ($first_day_of_month == 0) ? 6 : $first_day_of_month - 1; // Mon=0, Sun=6
    $days_in_month = date('t', strtotime("$selected_year-$selected_month-01"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskLock - Calendar</title>
    <link href="images/icons.png" rel="shortcut icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/frontend.css">
    <link rel="stylesheet" href="css/calendar.css">
</head>
<body>

    <div class="app">
        <nav class="navbar">
            <div class="logo"><i class="fa-solid fa-lock"></i> TaskLock</div>
            <div class="nav-links">
                <a href="frontend.php" class="nav-item"><i class="fa-solid fa-border-all"></i> Dashboard</a>
                <a href="calendar.php" class="nav-item active"><i class="fa-regular fa-calendar-days"></i> Calendar</a>
                <a href="settings.php" class="nav-item"><i class="fa-solid fa-gear"></i> Settings</a>
                <a href="profile.php" class="nav-item"><i class="fa-regular fa-user"></i> Profile</a>
            </div>
        </nav>

        <header class="page-header">
            <div>
                <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
                <div class="date" id="today-date"></div>
            </div>
            <div class="score-badge">Your Points: <span><?php echo $total_points; ?> Point(s)</span></div>
        </header>

        <main class="main-content">
            
            <aside class="control-panel">
                <h3>Deadline Calendar</h3>
                <form method="GET" class="dropdown-form">
                    <select name="m" onchange="this.form.submit()">
                        <?php 
                        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        foreach($months as $index => $month_name): 
                            $m_val = $index + 1;
                        ?>
                            <option value="<?php echo $m_val; ?>" <?php echo ($selected_month == $m_val) ? 'selected' : ''; ?>>
                                <?php echo $month_name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="y" onchange="this.form.submit()">
                        <?php 
                        $current_year = date('Y');
                        for($y = $current_year - 1; $y <= $current_year + 3; $y++): 
                        ?>
                            <option value="<?php echo $y; ?>" <?php echo ($selected_year == $y) ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </form>

                <div style="margin-top: 30px;">
                    <p style="font-size: 13px; color: #b5b5b5; margin-bottom: 5px;">Pending Tasks</p>
                    <div class="pending-task-list">
                        <?php if(empty($pending_tasks)): ?>
                            <div style="font-size: 13px; color: #B0A8A6; font-style: italic;">All tasks for this month are completed!</div>
                        <?php else: ?>
                            <?php foreach($pending_tasks as $pt): ?>
                                <div class="pending-task-item">
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;"><?php echo htmlspecialchars($pt['task_title']); ?></span>
                                    <?php 
                                        // Set the color and text based on priority
                                        $bg = ($pt['priority'] == 'high') ? '#e33030' : (($pt['priority'] == 'medium') ? '#eba417' : '#3fba46');
                                        $label = ($pt['priority'] == 'high') ? 'High' : (($pt['priority'] == 'medium') ? 'Medium' : 'Low');
                                    ?>
                                    <span class="priority-badge" style="background-color: <?php echo $bg; ?>;"><?php echo $label; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="summary-box">
                    <div style="font-size: 12px; font-weight: 600; color: #333;">Total Tasks This Month</div>
                    <div style="font-size: 24px; font-weight: bold; color: #1a1a1a; margin-top: 5px;">
                        <?php echo $total_tasks_this_month; ?> <i class="fa-solid fa-list-check" style="font-size: 20px; color: #3b82f6;"></i>
                    </div>
                </div>
            </aside>

            <section class="calendar-card">
                <div class="calendar-header-days">
                    <div>Monday</div><div>Tuesday</div><div>Wednesday</div><div>Thursday</div>
                    <div>Friday</div><div>Saturday</div><div>Sunday</div>
                </div>

                <div class="calendar-grid">
                    <?php 
                        // Render empty slots before the 1st of the month
                        for ($i = 0; $i < $first_day_of_month; $i++) {
                            echo '<div class="day-cell empty"></div>';
                        }

                        // Render days of the month
                        for ($day = 1; $day <= $days_in_month; $day++) {
                            // Mark today's date if viewing current month/year
                            $is_today = ($day == date('j') && $selected_month == date('n') && $selected_year == date('Y')) ? 'today' : '';
                            
                            echo "<div class='day-cell $is_today'>";
                            echo "<div class='day-number'>$day</div>";

                            // Render Holidays (EIF Data)
                            if(isset($holidays_by_day[$day])) {
                                foreach($holidays_by_day[$day] as $holiday_name) {
                                    $h_title = htmlspecialchars($holiday_name);
                                    echo "<div class='task-badge badge-holiday' title='$h_title'><i class='fa-solid fa-flag'></i> $h_title</div>";
                                }
                            }

                            // Render task badges for this specific day
                            if(isset($tasks_by_day[$day])) {
                                foreach($tasks_by_day[$day] as $task) {
                                    $title = htmlspecialchars($task['task_title']);
                                    
                                    if($task['status'] == 'completed') {
                                        echo "<div class='task-badge badge-completed' title='$title'><i class='fa-solid fa-check'></i> $title</div>";
                                    } else {
                                        $prio_class = ($task['priority'] == 'high') ? 'badge-high' : (($task['priority'] == 'medium') ? 'badge-medium' : 'badge-low');
                                        echo "<div class='task-badge $prio_class' title='$title'>$title</div>";
                                    }
                                }
                            }
                            echo "</div>";
                        }
                    ?>
                </div>
            </section>

        </main>
    </div>

    <script>
        // Real-time Date format for header
        (function () {
            var d = new Date();
            var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            document.getElementById('today-date').textContent = days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        })();
    </script>
</body>
</html>