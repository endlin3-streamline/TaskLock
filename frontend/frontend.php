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

    // 2. Fetch Data
    $sql_batches = mysqli_query($conn, "SELECT * FROM batches WHERE user_id = '$user_id'");
    $batches = [];
    while($row = mysqli_fetch_assoc($sql_batches)){ $batches[] = $row; }

    $sql_tasks = mysqli_query($conn, "SELECT * FROM tasks WHERE user_id = '$user_id'");
    $tasks = [];
    while($row = mysqli_fetch_assoc($sql_tasks)){ $tasks[] = $row; }
    
    $sql_milestones = mysqli_query($conn, "SELECT m.* FROM milestones m JOIN tasks t ON m.task_id = t.task_id WHERE t.user_id = '$user_id'");
    $milestones = [];
    while($row = mysqli_fetch_assoc($sql_milestones)){ $milestones[] = $row; }
    
    // 3. Structure Data for Accordion
    $groupedData = [];
    foreach($batches as $b) {
        $b_id = isset($b['id']) ? $b['id'] : $b['batch_id']; 
        $groupedData[$b_id] = ['id' => $b_id, 'name' => $b['batch_name'], 'tasks' => []];
    }
    foreach($tasks as $t) {
        $t['milestones'] = [];
        $t_id = isset($t['id']) ? $t['id'] : $t['task_id'];
        if(isset($groupedData[$t['batch_id']])) {
            $groupedData[$t['batch_id']]['tasks'][$t_id] = $t;
        }
    }
    foreach($milestones as $m) {
        foreach($groupedData as $bId => &$b) {
            if(isset($b['tasks'][$m['task_id']])) {
                $b['tasks'][$m['task_id']]['milestones'][] = $m;
            }
        }
    }

    // 4. Handle Form Submissions
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        if (isset($_POST['create_batch'])) {
            $batch_name = mysqli_real_escape_string($conn, $_POST['batch_name']);
            $result_batch = mysqli_query($conn, "SELECT MAX(batch_id) as last_batch FROM batches");
            $row_batch = mysqli_fetch_assoc($result_batch);
            $new_number = $row_batch['last_batch'] ? intval(substr($row_batch['last_batch'], 2)) + 1 : 1;
            
            $batch_id = "BA" . str_pad($new_number, 6, "0", STR_PAD_LEFT);
            $created_at = date("Y-m-d H:i:s");
            
            mysqli_query($conn, "INSERT INTO batches (batch_id, user_id, batch_name, created_at, updated_at) VALUES ('$batch_id', '$user_id', '$batch_name', '$created_at', '$created_at')");
            header("Location: frontend.php"); exit();
        }
        
        if (isset($_POST['create_task'])) {
            $batch_id = mysqli_real_escape_string($conn, $_POST['batch_id']);
            $task_title = mysqli_real_escape_string($conn, $_POST['task_name']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $priority = mysqli_real_escape_string($conn, $_POST['priority']);
            $deadline = isset($_POST['deadline']) ? mysqli_real_escape_string($conn, $_POST['deadline']) : date('Y-m-d'); 
            
            $result_task = mysqli_query($conn, "SELECT MAX(task_id) as last_task FROM tasks");
            $row_task = mysqli_fetch_assoc($result_task);
            $new_number = $row_task['last_task'] ? intval(substr($row_task['last_task'], 2)) + 1 : 1;
            $task_id = "TK" . str_pad($new_number, 11, "0", STR_PAD_LEFT);
            
            mysqli_query($conn, "INSERT INTO tasks (task_id, user_id, batch_id, task_title, description, priority, status, deadline) VALUES ('$task_id', '$user_id', '$batch_id', '$task_title', '$description', '$priority', 'pending', '$deadline')");
            header("Location: frontend.php"); exit();
        }
        
        if (isset($_POST['create_milestone'])) {
            $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
            $mile_title = mysqli_real_escape_string($conn, $_POST['milestone_title']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            
            $result_mile = mysqli_query($conn, "SELECT MAX(milestone_id) as last_mile FROM milestones");
            $row_mile = mysqli_fetch_assoc($result_mile);
            $new_number = $row_mile['last_mile'] ? intval(substr($row_mile['last_mile'], 4)) + 1 : 1;
            $milestone_id = "MILE" . str_pad($new_number, 11, "0", STR_PAD_LEFT);
            
            mysqli_query($conn, "INSERT INTO milestones (milestone_id, task_id, mile_title, description, status, file_url) VALUES ('$milestone_id', '$task_id', '$mile_title', '$description', 'pending', 'nofile.png')");
            header("Location: frontend.php"); exit();
        }

        if (isset($_POST['start_milestone'])) {
            $ms_id = mysqli_real_escape_string($conn, $_POST['milestone_id']);
            mysqli_query($conn, "UPDATE milestones SET status = 'in_progress' WHERE milestone_id = '$ms_id'");
            
            $get_task = mysqli_query($conn, "SELECT task_id FROM milestones WHERE milestone_id = '$ms_id'");
            $row = mysqli_fetch_assoc($get_task);
            if ($row) {
                $t_id = $row['task_id'];
                mysqli_query($conn, "UPDATE tasks SET status = 'in_progress' WHERE task_id = '$t_id' AND status = 'pending'");
            }
            header("Location: frontend.php"); exit();
        }
        
        if (isset($_POST['upload_milestone']) && isset($_FILES['milestone_img'])) {
            $ms_id = mysqli_real_escape_string($conn, $_POST['milestone_id']);
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

            $new_file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["milestone_img"]["name"]));
            $target_file = $target_dir . $new_file_name;

            if (move_uploaded_file($_FILES["milestone_img"]["tmp_name"], $target_file)) {
                
                mysqli_query($conn, "UPDATE milestones SET status = 'completed', file_url = '$new_file_name' WHERE milestone_id = '$ms_id'");

                $points_awarded = rand(2, 8);
                // Generating a custom point ID using "PT" as a prefix
                $point_id = "PT" . str_pad(rand(1, 999999), 6, "0", STR_PAD_LEFT) . time();
                $earned_at = date('Y-m-d H:i:s');
                
                // Note: Ensure 'milestone' matches one of your allowed ENUM values in the database!
                mysqli_query($conn, "INSERT INTO points (point_id, user_id, milestone_id, amount, source, description, earned_at) 
                    VALUES ('$point_id', '$user_id', '$ms_id', '$points_awarded', 'milestones_reached', 'Completed milestone screenshot upload', '$earned_at')");

                // 3. Auto-complete parent task logic
                $get_task = mysqli_query($conn, "SELECT task_id FROM milestones WHERE milestone_id = '$ms_id'");
                $row = mysqli_fetch_assoc($get_task);
                if ($row) {
                    $t_id = $row['task_id'];
                    $check_uncompleted = mysqli_query($conn, "SELECT COUNT(*) as pending FROM milestones WHERE task_id = '$t_id' AND status != 'completed'");
                    $pending_row = mysqli_fetch_assoc($check_uncompleted);
                    if ($pending_row['pending'] == 0) {
                        mysqli_query($conn, "UPDATE tasks SET status = 'completed' WHERE task_id = '$t_id'");
                    }
                }
                header("Location: frontend.php");
                exit();
            }
        }

        // --- 5. FOCUS SESSION LOGIC ---
        // START FOCUS SESSION
        if (isset($_POST['start_focus'])) {
            $task_id = mysqli_real_escape_string($conn, $_POST['focus_task_id']);
            $estimated = isset($_POST['estimated_minutes']) && $_POST['estimated_minutes'] !== '' 
                        ? intval($_POST['estimated_minutes']) : 0;  // ← add this
            $started_at = date('Y-m-d H:i:s');
            $session_id = "FS" . str_pad(rand(1, 999999), 6, "0", STR_PAD_LEFT) . time();
            
            mysqli_query($conn, "INSERT INTO focus_sessions (focus_sessions_id, user_id, task_id, started_at)
                VALUES ('$session_id', '$user_id', '$task_id', '$started_at')");

            // Save estimated_minutes to commit_times straight away
            $check = mysqli_query($conn, "SELECT commit_time_id FROM commit_times WHERE task_id='$task_id' AND user_id='$user_id'");
            if (mysqli_num_rows($check) > 0) {
                mysqli_query($conn, "UPDATE commit_times SET estimated_minutes = '$estimated' WHERE task_id='$task_id' AND user_id='$user_id'");
            } else {
                $commit_id = "CT" . str_pad(rand(1,999999), 6, "0", STR_PAD_LEFT) . time();
                mysqli_query($conn, "INSERT INTO commit_times (commit_time_id, task_id, user_id, estimated_minutes, actual_minutes, recorded_at)
                    VALUES ('$commit_id', '$task_id', '$user_id', '$estimated', 0, '$started_at')");
            }

            $_SESSION['active_focus_session'] = $session_id;
            $_SESSION['active_focus_task'] = $task_id;
            header("Location: frontend.php"); exit();
        }

        // STOP FOCUS SESSION
        if (isset($_POST['stop_focus'])) {
            $session_id = mysqli_real_escape_string($conn, $_POST['focus_sessions_id']);
            $task_id = mysqli_real_escape_string($conn, $_POST['focus_task_id']);
            $ended_at = date('Y-m-d H:i:s');

            mysqli_query($conn, "UPDATE focus_sessions 
                SET ended_at = '$ended_at',
                    duration_seconds = TIMESTAMPDIFF(SECOND, started_at, '$ended_at')
                WHERE focus_sessions_id = '$session_id'");

            // Update actual_minutes in commit_times if row exists, else insert
            $check = mysqli_query($conn, "SELECT commit_time_id FROM commit_times WHERE task_id = '$task_id' AND user_id = '$user_id'");
            if (mysqli_num_rows($check) > 0) {
                mysqli_query($conn, "UPDATE commit_times SET
                    actual_minutes = (SELECT CEIL(SUM(duration_seconds)/60) FROM focus_sessions WHERE task_id='$task_id' AND user_id='$user_id' AND ended_at IS NOT NULL),
                    recorded_at = '$ended_at'
                    WHERE task_id = '$task_id' AND user_id = '$user_id'");
            } else {
                $commit_id = "CT" . str_pad(rand(1,999999), 6, "0", STR_PAD_LEFT) . time();
                mysqli_query($conn, "INSERT INTO commit_times (commit_time_id, task_id, user_id, actual_minutes, recorded_at)
                    VALUES ('$commit_id', '$task_id', '$user_id',
                    (SELECT CEIL(SUM(duration_seconds)/60) FROM focus_sessions WHERE task_id='$task_id' AND user_id='$user_id' AND ended_at IS NOT NULL),
                    '$ended_at')");
            }
            unset($_SESSION['active_focus_session']);
            unset($_SESSION['active_focus_task']);
            header("Location: frontend.php"); exit();
        }
    }

    // --- 6. TIMER STILL GOING AFTER RESFERESH ---
    $active_session = isset($_SESSION['active_focus_session']) ? $_SESSION['active_focus_session'] : null;
    $active_task_id = isset($_SESSION['active_focus_task']) ? $_SESSION['active_focus_task'] : null;
    $elapsed_seconds = 0;

    if ($active_session) {
        $get_start = mysqli_query($conn, "SELECT started_at FROM focus_sessions WHERE focus_sessions_id = '$active_session'");
        $start_row = mysqli_fetch_assoc($get_start);
        if ($start_row) {
            $elapsed_seconds = time() - strtotime($start_row['started_at']);
        }
    }

    // 7. Waktu fokus terakhir dalam menit
    $sql_focus_time = mysqli_query($conn, "SELECT actual_minutes FROM commit_times WHERE user_id = '$user_id' ORDER BY recorded_at DESC LIMIT 1");
    $focus_row = mysqli_fetch_assoc($sql_focus_time);
    $recent_focus = $focus_row ? $focus_row['actual_minutes'] : 0;


    // 8. Ambil total points
    $sql_points = mysqli_query($conn, "SELECT SUM(amount) as total_score FROM points WHERE user_id = '$user_id'");
    $points_row = mysqli_fetch_assoc($sql_points);
    $total_points = $points_row['total_score'] ? $points_row['total_score'] : 0;


    // --- 9. Fetch Pending Notifications ---
    // Fetch User Settings for Alarms/Notifications
    $sql_settings = mysqli_query($conn, "SELECT * FROM settings WHERE user_id = '$user_id'");
    $user_settings = mysqli_fetch_assoc($sql_settings);
    
    // Default to true (1) if they haven't visited settings yet
    $alarm_enabled = ($user_settings) ? $user_settings['alarm_enabled'] : 1;
    $notif_enabled = ($user_settings) ? $user_settings['notifications_enabled'] : 1;


    // 10. EIF IMPLEMENTATION: Fetch Daily Productivity Quote
    $quote_text = "Focus on being productive instead of busy.";
    $quote_author = "Tim Ferriss"; // Default fallbacks in case of no internet
    
    $quote_api_url = "https://zenquotes.io/api/random";
    $quote_json = @file_get_contents($quote_api_url); 
    
    if ($quote_json) {
        $quote_data = json_decode($quote_json, true);
        if (isset($quote_data[0]['q']) && isset($quote_data[0]['a'])) {
            $quote_text = $quote_data[0]['q'];
            $quote_author = $quote_data[0]['a'];
        }
    }

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskLock — Dashboard</title>
    <link href="images/icons.png" rel="shortcut icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/frontend.css">
</head>
<body>
    <audio id="alarm-sound" src="audio/alarm.mp3" preload="auto"></audio>

    <div class="app">
        <nav class="navbar">
            <div class="logo"><i class="fa-solid fa-lock"></i> TaskLock</div>
            <div class="nav-links">
                <a href="frontend.php" class="nav-item active"><i class="fa-solid fa-border-all"></i> Dashboard</a>
                <a href="calendar.php" class="nav-item"><i class="fa-regular fa-calendar-days"></i> Calendar</a>
                <a href="settings.php" class="nav-item"><i class="fa-solid fa-gear"></i> Settings</a>
                <a href="profile.php" class="nav-item"><i class="fa-regular fa-user"></i> Profile</a>
            </div>
        </nav>

        <div class="page-header">
            <div>
                <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
                <div class="date" id="today-date"></div>
            </div>
            <div class="score-badge">Your Points: <span><?php echo $total_points; ?> Point(s)</span></div>
        </div>

        <div class="main-grid">
            <div class="left-panel">
                <div class="focus-card">
                    <svg class="focus-topo" viewBox="0 0 220 260" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                        <path d="M10,0 Q60,40 30,90 Q0,140 50,180 Q100,220 70,260" fill="none" stroke="#b5a99f" stroke-width="20" stroke-linecap="round"/>
                        <path d="M50,0 Q100,50 70,110 Q40,170 90,210 Q140,250 110,280" fill="none" stroke="#b5a99f" stroke-width="16" stroke-linecap="round"/>
                        <path d="M100,0 Q150,60 120,120 Q90,180 130,220 Q170,260 150,280" fill="none" stroke="#c9bfb8" stroke-width="18" stroke-linecap="round"/>
                    </svg>

                    <div class="focus-inner">
                        <div class="focus-label"><i class="ti ti-bolt"></i> Focus Session</div>
                        <div class="focus-timer-wrap">
                            <div class="focus-timer">
                                <svg viewBox="0 0 96 96" width="96" height="96">
                                    <circle class="timer-track" cx="48" cy="48" r="42"/>
                                    <circle class="timer-progress" cx="48" cy="48" r="42" id="timer-ring"/>
                                </svg>
                                <div class="timer-text" id="timer-display">30:00</div>
                            </div>
                        </div>

                        <?php
                        $active_session = isset($_SESSION['active_focus_session']) ? $_SESSION['active_focus_session'] : null;
                        $active_task_id = isset($_SESSION['active_focus_task']) ? $_SESSION['active_focus_task'] : null;
                        $active_task_name = 'Ready to Focus!';
                        if ($active_task_id) {
                            foreach($tasks as $t) {
                                if ($t['task_id'] == $active_task_id) { $active_task_name = $t['task_title']; break; }
                            }
                        }
                        ?>

                        <div class="focus-task-name" id="focus-task-name"><?php echo htmlspecialchars($active_task_name); ?></div>

                        <?php if (!$active_session): ?>
                            <!-- START FORM -->
                            <form method="POST" style="width:100%;">
                                <select name="focus_task_id" required style="width:100%; padding:8px; border-radius:6px; border:none; margin-bottom:8px; background:#2e2a28; color:#fff;">
                                    <option value="">Select task...</option>
                                    <?php foreach($tasks as $t): ?>
                                        <?php if($t['status'] != 'completed'): ?>
                                            <option value="<?php echo $t['task_id']; ?>"><?php echo htmlspecialchars($t['task_title']); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" name="estimated_minutes" placeholder="Estimated Minutes (optional)" min="1"
                                    style="width:100%; padding:8px; border-radius:6px; border:none; margin-bottom:8px; background:#2e2a28; color:#fff;">
                                <button type="submit" name="start_focus" class="btn-focus primary" id="btn-start">Start Focus Mode</button>
                            </form>
                        <?php else: ?>
                            <!-- STOP FORM -->
                            <form method="POST" style="width:100%;">
                                <input type="hidden" name="focus_sessions_id" value="<?php echo $active_session; ?>">
                                <input type="hidden" name="focus_task_id" value="<?php echo $active_task_id; ?>">
                                <button type="submit" name="stop_focus" class="btn-focus primary" id="btn-start" style="background:#e05c5c;">Stop Focus Mode</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div class="summary-card">
                        <div class="summary-title">Today's Summary</div>
                        <div class="summary-row">
                            <div class="summary-item">
                                <div class="summary-item-label">Recent Focus Time:</div>
                                <div class="summary-item-value">
                                    <span id="focus-time"><?php echo $recent_focus; ?>m</span> <i class="ti ti-lock icon-lock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EIF Daily Quote Section -->
                <div class="quote-box" style="margin-top: 15px; padding: 15px; background-color: #3e3836; border-radius: 8px; border-left: 3px solid #e5b326; display: flex; align-items: center; justify-content: space-between; gap: 12px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                    <div style="font-size: 13px; color: #e0d4d2; font-style: italic; line-height: 1.5; flex-grow: 1;">
                        "<?php echo htmlspecialchars($quote_text); ?>" 
                        <div style="color: #B0A8A6; font-size: 11px; margin-top: 4px; font-weight: bold; font-style: normal;">
                            — <?php echo htmlspecialchars($quote_author); ?>
                        </div>
                    </div>
                    
                    <!-- Refresh Button -->
                    <button onclick="window.location.reload();" style="background: rgba(255,255,255,0.05); border: 1px solid #524a48; color: #e5b326; cursor: pointer; font-size: 14px; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;" onmouseover="this.style.background='#524a48'; this.style.transform='rotate(90deg)';" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.transform='rotate(0deg)';" title="Get a new quote">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>

            <div class="task-card">
                <div class="task-card-header">Task List</div>
                        
                <div class="task-list-header">
                    <div>Status</div>
                    <div>Task Name</div>
                    <div>Progress Status</div>
                    <div>Priority</div>
                    <div>Deadline</div>
                </div>

                <div class="accordion-wrapper">
                    <?php if (empty($groupedData)): ?>
                        <div style="text-align: center; padding: 30px; color: #B0A8A6;">No tasks available. Create one below!</div>
                    <?php else: ?>
                        
                        <?php foreach($groupedData as $batch): ?>
                            <div class="batch-group">
                                <div class="batch-trigger" onclick="toggleAccordion('batch-<?php echo $batch['id']; ?>', this)">
                                    <i class="ti ti-chevron-right"></i> Batch: <?php echo htmlspecialchars($batch['name']); ?>
                                </div>
                                
                                <div class="batch-content" id="batch-<?php echo $batch['id']; ?>">
                                    <?php if (empty($batch['tasks'])): ?>
                                        <div style="color: gray; font-size: 12px;">No tasks available in this batch.</div>
                                    <?php else: ?>
                                        
                                        <?php foreach($batch['tasks'] as $task_id => $task): ?>
                                            <div class="task-row" onclick="toggleAccordion('milestone-<?php echo $task_id; ?>', null)">
                                                <div class="custom-checkbox <?php echo ($task['status'] == 'completed') ? 'checked' : ''; ?>"><i class="ti ti-check"></i></div>
                                                <div style="min-width: 0;">
                                                    <div style="font-weight: 600; color: #FFFFFF; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($task['task_title']); ?></div>
                                                    <div style="font-size: 11px; color: #B0A8A6; margin-top: 2px; font-style: italic; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($task['description'] ?: 'No description avaailable'); ?></div>
                                                </div>
                                                <div>
                                                    <span style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: <?php echo ($task['status'] == 'completed') ? '#27AE60' : (($task['status'] == 'in_progress') ? '#F5A623' : '#B0A8A6'); ?>;">
                                                        <?php echo str_replace('_', ' ', $task['status']); ?>
                                                    </span>
                                                </div>
                                                <div><span class="badge badge-<?php echo htmlspecialchars($task['priority']); ?>"><?php echo htmlspecialchars($task['priority']); ?></span></div>
                                                <div style="font-size: 13px; color: #e5b326; font-weight: 500; text-align: left;"><?php echo htmlspecialchars($task['deadline']); ?></div>
                                            </div>
                                            
                                            <div class="milestone-container" id="milestone-<?php echo $task_id; ?>">
                                                <?php if(empty($task['milestones'])): ?>
                                                    <div class="milestone-item" style="color: gray;"><i>No milestone yet.</i></div>
                                                <?php else: ?>
                                                    <?php foreach($task['milestones'] as $ms): ?>
                                                        <div class="milestone-item">
                                                            <div style="min-width: 0;">
                                                                <span style="font-weight: 600; color: #FFFFFF;">&#x21B3; <?php echo htmlspecialchars($ms['mile_title']); ?></span>
                                                                <div style="font-size: 11px; color: #A09896; padding-left: 15px; margin-top: 2px; font-style: italic; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($ms['description'] ?: 'No description available'); ?></div>
                                                            </div>
                                                            <div>
                                                                <span style="font-size: 10px; padding: 3px 8px; border-radius: 4px; background: rgba(255,255,255,0.1); color: <?php echo ($ms['status'] == 'completed') ? '#27AE60' : (($ms['status'] == 'in_progress') ? '#F5A623' : '#B0A8A6'); ?>; font-weight: bold; text-transform: uppercase;">
                                                                    <?php echo str_replace('_', ' ', $ms['status']); ?>
                                                                </span>
                                                            </div>
                                                            <div style="display: flex; justify-content: flex-end; align-items: center;">
                                                                <?php if($ms['status'] == 'completed'): ?>
                                                                    <span style="color: #27AE60; font-size: 12px;"><i class="ti ti-check"></i> Selesai</span>
                                                                <?php elseif($ms['status'] == 'pending'): ?>
                                                                    <form method="POST" style="margin: 0;">
                                                                        <input type="hidden" name="milestone_id" value="<?php echo $ms['milestone_id']; ?>">
                                                                        <button type="submit" name="start_milestone" style="padding: 5px 12px; border-radius: 4px; border: none; background: #F5A623; color: #362C2A; font-weight: bold; cursor: pointer; font-size: 11px;">Mulai Kerjakan</button>
                                                                    </form>
                                                                <?php else: ?>
                                                                    <form method="POST" enctype="multipart/form-data" style="display: flex; gap: 5px; margin: 0;">
                                                                        <input type="hidden" name="milestone_id" value="<?php echo $ms['milestone_id']; ?>">
                                                                        <input type="file" name="milestone_img" accept="image/*" required style="font-size: 11px; width: 170px; background: rgba(255,255,255,0.05); padding: 5px; border-radius: 4px; color: #fff;">
                                                                        <button type="submit" name="upload_milestone" style="padding: 5px 10px; border-radius: 4px; border: none; background: #4A90E2; color: white; font-weight: bold; cursor: pointer; font-size: 11px;">Upload</button>
                                                                    </form>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button class="add-task-btn" onclick="toggleForms()">+ Add New Task</button>
                
                <div class="creation-forms" id="form-container">
                    <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 20px; padding: 15px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                        <div style="flex: 1;">
                            <label style="color: #e5b326; font-weight: 600; font-size: 14px; display: block; margin-bottom: 8px;">What do you want to create?</label>
                            <select id="form-selector" onchange="switchForm(this.value)" style="width: 100%; padding: 10px; border-radius: 6px; border: none; background: #524a48; color: white; cursor: pointer;">
                                <option value="">-- Select an option --</option>
                                <option value="batch-form">1. New Batch (Category)</option>
                                <option value="task-form">2. New Task (Main Goal)</option>
                                <option value="milestone-form">3. New Milestone (Action Step)</option>
                            </select>
                        </div>
                        <div style="flex: 2; font-size: 12px; color: #B0A8A6; line-height: 1.5; border-left: 2px solid #524a48; padding-left: 15px;">
                            <strong style="color: white; font-size: 13px;">Terminology Guide:</strong><br>
                            <span style="color: #e5b326;">• Batch:</span> A broad subject or project (e.g., "Web Development", "Skripsi").<br>
                            <span style="color: #e5b326;">• Task:</span> A specific goal within a batch (e.g., "Build Dashboard UI").<br>
                            <span style="color: #e5b326;">• Milestone:</span> The smallest step of a task. Upload screenshots here to earn points!
                        </div>
                    </div>

                    <form method="POST" class="form-row form-section" id="batch-form" style="display: none;">
                        <input type="text" name="batch_name" placeholder="Enter Batch Name..." required style="padding: 10px; border-radius: 6px; border: none; flex: 1;">
                        <button type="submit" name="create_batch" style="padding: 10px 20px; border-radius: 6px; border: none; background: #F5A623; color: #3b322d; font-weight: bold; cursor: pointer;">+ Create Batch</button>
                    </form>

                    <form method="POST" class="form-row form-section" id="task-form" style="display: none;">
                        <select name="batch_id" required style="padding: 10px; border-radius: 6px; border: none; min-width: 150px;">
                            <option value="">Select Parent Batch...</option>
                            <?php foreach($batches as $b): ?>
                                <option value="<?php echo isset($b['batch_id']) ? $b['batch_id'] : $b['id']; ?>"><?php echo htmlspecialchars($b['batch_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="task_name" placeholder="Task Name" required style="padding: 10px; border-radius: 6px; border: none; flex: 1;">
                        <input type="text" name="description" placeholder="Task Description" style="padding: 10px; border-radius: 6px; border: none; flex: 1;">
                        <input type="date" name="deadline" required style="padding: 10px; border-radius: 6px; border: none;">
                        <select name="priority" style="padding: 10px; border-radius: 6px; border: none;">
                            <option value="low">Low Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="high">High Priority</option>
                        </select>
                        <button type="submit" name="create_task" style="padding: 10px 20px; border-radius: 6px; border: none; background: #F5A623; color: #3b322d; font-weight: bold; cursor: pointer;">+ Create Task</button>
                    </form>

                    <form method="POST" class="form-row form-section" id="milestone-form" style="display: none;">
                        <select name="task_id" required style="padding: 10px; border-radius: 6px; border: none; min-width: 150px;">
                            <option value="">Select Parent Task...</option>
                            <?php foreach($tasks as $t): ?>
                                <option value="<?php echo isset($t['task_id']) ? $t['task_id'] : $t['id']; ?>"><?php echo htmlspecialchars($t['task_title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="milestone_title" placeholder="Milestone Name" required style="padding: 10px; border-radius: 6px; border: none; flex: 1;">
                        <input type="text" name="description" placeholder="Milestone Description" style="padding: 10px; border-radius: 6px; border: none; flex: 1;">
                        <button type="submit" name="create_milestone" style="padding: 10px 20px; border-radius: 6px; border: none; background: #F5A623; color: #3b322d; font-weight: bold; cursor: pointer;">+ Create Milestone</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        (function () {
            var d = new Date();
            var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            document.getElementById('today-date').textContent = days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        })();

  
        var elapsed = <?php echo $elapsed_seconds; ?>, running = false, interval = null;
        var circumference = 2 * Math.PI * 42;
        document.getElementById('timer-ring').style.strokeDasharray = circumference;
        document.getElementById('timer-ring').style.strokeDashoffset = circumference; // starts empty

        function formatTime(s) {
            var h = Math.floor(s / 3600);
            var m = Math.floor((s % 3600) / 60);
            var sec = s % 60;
            if (h > 0) return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m + ':' + (sec < 10 ? '0' : '') + sec;
            return (m < 10 ? '0' : '') + m + ':' + (sec < 10 ? '0' : '') + sec;
        }

        function updateRing() {
            // Ring fills up every 30 minutes, loops around
            var cycle = 30 * 60;
            var progress = (elapsed % cycle) / cycle;
            document.getElementById('timer-ring').style.strokeDashoffset = circumference - (progress * circumference);
        }

        // Pull settings from PHP into JavaScript
        var alarmEnabled = <?php echo $alarm_enabled; ?>;
        var notifEnabled = <?php echo $notif_enabled; ?>;
        var activeTaskId = "<?php echo $active_task_id; ?>";

        // Request browser notification permission on page load if enabled
        if (notifEnabled && Notification.permission !== "granted") {
            Notification.requestPermission();
        }

        function toggleTimer() {
            if (!running) {
                running = true;
                interval = setInterval(function () {
                    elapsed++;
                    document.getElementById('timer-display').textContent = formatTime(elapsed);
                    document.getElementById('focus-time').textContent = 
                        (Math.floor(elapsed/3600) > 0 ? Math.floor(elapsed/3600)+'j ' : '') + 
                        Math.floor((elapsed%3600)/60) + 'm';
                    updateRing();

                    <?php if ($active_session): ?>
                    var estimatedSeconds = <?php 
                        $est = 0;
                        if ($active_task_id) {
                            $ct = mysqli_query($conn, "SELECT estimated_minutes FROM commit_times WHERE task_id='$active_task_id' AND user_id='$user_id'");
                            $ct_row = mysqli_fetch_assoc($ct);
                            $est = $ct_row ? intval($ct_row['estimated_minutes']) * 60 : 0;
                        }
                        echo $est;
                    ?>;
                    
                    if (estimatedSeconds > 0) {
                        var ring = document.getElementById('timer-ring');
                        ring.style.stroke = elapsed > estimatedSeconds ? '#e05c5c' : '';

                        // THE TRIGGER LOGIC: Exactly when elapsed hits the estimate
                        if (elapsed === estimatedSeconds) {
                            var alertMsg = "You passed your estimated minute, you may continue if you would like.";
                            
                            // 1. Play Local Audio Alarm
                            if (alarmEnabled) {
                                document.getElementById('alarm-sound').play().catch(function(e){
                                    console.log("Browser blocked auto-play audio.");
                                });
                            }

                            // 2. Show Native Desktop Notification
                            if (notifEnabled && Notification.permission === "granted") {
                                new Notification("TaskLock Timer", {
                                    body: alertMsg,
                                    icon: "images/icons.png"
                                });
                            }

                            // 3. Save to ILF (Database) silently via AJAX
                            var formData = new URLSearchParams();
                            formData.append("task_id", activeTaskId);
                            formData.append("message", alertMsg);

                            fetch('save_notification.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: formData.toString()
                            });
                        }
                    }
                    <?php endif; ?>

                }, 1000);
            } else {
                clearInterval(interval); running = false;
            }
        }

        updateRing();

        <?php if ($active_session): ?>
        toggleTimer(); // auto-start if session is active
        <?php endif; ?>

        function toggleAccordion(targetId, triggerElement) {
            var target = document.getElementById(targetId);
            if(target.classList.contains('open')) {
                target.classList.remove('open');
                if(triggerElement) triggerElement.classList.remove('active');
            } else {
                target.classList.add('open');
                if(triggerElement) triggerElement.classList.add('active');
            }
        }

        function toggleForms() {
            document.getElementById('form-container').classList.toggle('open');
        }

        // New logic to handle the dropdown selector
        function switchForm(formId) {
            // 1. Hide all forms first
            var forms = document.querySelectorAll('.form-section');
            forms.forEach(function(form) {
                form.style.display = 'none';
            });
            
            // 2. Show the specific form selected by the user
            if(formId) {
                document.getElementById(formId).style.display = 'flex';
            }
        }
    </script>
</body>
</html>