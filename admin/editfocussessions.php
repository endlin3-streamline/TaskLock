<!DOCTYPE html>
<html lang="id">
    <?php
        include ("bagiankode/head.php");
    ?>
    <?php
        ob_start();
        session_start();
        if(!isset($_SESSION["useremail"]))
            header("location:login.php");
    ?>
    <body class="sb-nav-fixed">
        <?php
        include("bagiankode/menunav.php");
        ?>
        <div id="layoutSidenav">
            <?php
                include("bagiankode/menu.php");
            ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Edit Focus Session</li>
                        </ol>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT FOCUS SESSIONS | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <?php
  include("includes/config.php");
  
  // 1. Validasi parameter untuk menghindari error Undefined Key
  if(isset($_GET["editfocussessions"])) {
      $focus_sessions_id = $_GET["editfocussessions"];
  } else {
      header("location:inputfocussession.php");
      exit;
  }

  // 2. Ambil data focus session lama
  $editfs = mysqli_query($conn, "SELECT * FROM focus_sessions WHERE focus_sessions_id ='$focus_sessions_id'");
  $row_edit2 = mysqli_fetch_array($editfs);

  if(!$row_edit2) {
      echo "<div class='container mt-5'><h3>Data tidak ditemukan.</h3><a href='inputfocussession.php'>Kembali</a></div>";
      exit;
  }

  // 3. Eksekusi Perubahan Data ketika tombol Ubah ditekan
  if(isset($_POST['Ubah']))
  {
      $user_id = $_POST['userID'];
      $task_id = $_POST['taskID'];
      $duration_seconds = $_POST['durationSeconds'];
      $started_at = $_POST['startedAt'];
      $ended_at = $_POST['endedAt'];

      mysqli_query($conn, "UPDATE focus_sessions SET 
          user_id='$user_id', 
          task_id='$task_id', 
          duration_seconds='$duration_seconds', 
          started_at='$started_at', 
          ended_at='$ended_at'
          WHERE focus_sessions_id='$focus_sessions_id'");
          
      header("location:inputfocussession.php");
  }
  ?>
  
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Focus Session ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" value="<?php echo $row_edit2["focus_sessions_id"]; ?>" disabled>
        </div>
    </div>

    <div class="row mb-3">
        <label for="userID" class="col-sm-2 col-form-label">User ID</label>
        <div class="col-sm-10">
            <select class="form-control" id="userID" name="userID" required>
                <option value="">Select User...</option>
                <?php 
                $datauser = mysqli_query($conn, "SELECT * FROM users");
                while ($row_user = mysqli_fetch_array($datauser)) { 
                    $selected = ($row_user['user_id'] == $row_edit2['user_id']) ? "selected" : "";
                ?>
                <option value="<?php echo $row_user['user_id']; ?>" <?php echo $selected; ?>>
                    <?php echo $row_user['user_id']; ?> - <?php echo $row_user['username']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="taskID" class="col-sm-2 col-form-label">Task ID</label>
        <div class="col-sm-10">
            <select class="form-control" id="taskID" name="taskID" required>
                <option value="">Select Task...</option>
                <?php 
                $datatask = mysqli_query($conn, "SELECT * FROM tasks");
                while ($row_task = mysqli_fetch_array($datatask)) { 
                    $selected = ($row_task['task_id'] == $row_edit2['task_id']) ? "selected" : "";
                ?>
                <option value="<?php echo $row_task['task_id']; ?>" <?php echo $selected; ?>>
                    <?php echo $row_task['task_id']; ?> - <?php echo $row_task['task_title']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="durationSeconds" class="col-sm-2 col-form-label">Duration (seconds)</label>
        <div class="col-sm-10">
        <input type="number" class="form-control" id="durationSeconds" name="durationSeconds" placeholder="Input number..."
        value="<?php echo $row_edit2["duration_seconds"]; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="startedAt" class="col-sm-2 col-form-label">Started At</label>
        <div class="col-sm-10">
        <?php 
            $started_val = date('Y-m-d\TH:i', strtotime($row_edit2["started_at"])); 
        ?>
        <input type="datetime-local" class="form-control" id="startedAt" name="startedAt" 
        value="<?php echo $started_val; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="endedAt" class="col-sm-2 col-form-label">Ended At</label>
        <div class="col-sm-10">
        <?php 
            $ended_val = date('Y-m-d\TH:i', strtotime($row_edit2["ended_at"])); 
        ?>
        <input type="datetime-local" class="form-control" id="endedAt" name="endedAt" 
        value="<?php echo $ended_val; ?>" required>
        </div>
    </div>

  <div class="form-group row mt-4">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputfocussession.php" class="btn btn-danger">Batal</a>
    </div>
  </div>
  </form>

    </div>
  </div>
</body>
                    </div>
                </main>
                <?php
                    include ("bagiankode/footer.php");
                ?>
            </div>
        </div>
        <?php
            include ("bagiankode/jsscript.php");
        ?>
    </body>
</html>