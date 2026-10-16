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
                            <li class="breadcrumb-item active">Edit Commit Time</li>
                        </ol>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT COMMIT TIMES | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <?php
  include("includes/config.php");
  
  // 1. Validasi parameter untuk menghindari error Undefined Key
  if(isset($_GET["editcommittimes"])) {
      $commit_time_id = $_GET["editcommittimes"];
  } else {
      header("location:inputcommittime.php");
      exit;
  }

  // 2. Ambil data commit time lama
  $editct = mysqli_query($conn, "SELECT * FROM commit_times WHERE commit_time_id ='$commit_time_id'");
  $row_edit2 = mysqli_fetch_array($editct);

  if(!$row_edit2) {
      echo "<div class='container mt-5'><h3>Data tidak ditemukan.</h3><a href='inputcommittime.php'>Kembali</a></div>";
      exit;
  }

  // 3. Eksekusi Perubahan Data ketika tombol Ubah ditekan
  if(isset($_POST['Ubah']))
  {
      $task_id = $_POST['taskID'];
      $user_id = $_POST['userID'];
      $estimated_minutes = $_POST['estimatedMinutes'];
      $actual_minutes = $_POST['actualMinutes'];
      $note = $_POST['note'];

      mysqli_query($conn, "UPDATE commit_times SET 
          task_id='$task_id', 
          user_id='$user_id', 
          estimated_minutes='$estimated_minutes', 
          actual_minutes='$actual_minutes', 
          note='$note'
          WHERE commit_time_id='$commit_time_id'");
          
      header("location:inputcommittime.php");
  }
  ?>
  
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Commit Time ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" value="<?php echo $row_edit2["commit_time_id"]; ?>" disabled>
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
        <label for="estimatedMinutes" class="col-sm-2 col-form-label">Estimated Minutes</label>
        <div class="col-sm-10">
        <input type="number" class="form-control" id="estimatedMinutes" name="estimatedMinutes" placeholder="Input number..."
        value="<?php echo $row_edit2["estimated_minutes"]; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="actualMinutes" class="col-sm-2 col-form-label">Actual Minutes</label>
        <div class="col-sm-10">
        <input type="number" class="form-control" id="actualMinutes" name="actualMinutes" placeholder="Input number..."
        value="<?php echo $row_edit2["actual_minutes"]; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="note" class="col-sm-2 col-form-label">Note</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="note" name="note" placeholder="Input text..."
        value="<?php echo $row_edit2["note"]; ?>">
        </div>
    </div>

  <div class="form-group row mt-4">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputcommittime.php" class="btn btn-danger">Batal</a>
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