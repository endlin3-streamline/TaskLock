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
                            <li class="breadcrumb-item active">Edit Notification</li>
                        </ol>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT NOTIFICATIONS | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <?php
  include("includes/config.php");
  
  // 1. Validasi parameter untuk menghindari error Undefined Key
  if(isset($_GET["editnotifications"])) {
      $notification_id = $_GET["editnotifications"];
  } else {
      header("location:inputnotif.php");
      exit;
  }

  // 2. Ambil data notifikasi lama
  $editnotif = mysqli_query($conn, "SELECT * FROM notifications WHERE notification_id ='$notification_id'");
  $row_edit2 = mysqli_fetch_array($editnotif);

  if(!$row_edit2) {
      echo "<div class='container mt-5'><h3>Data tidak ditemukan.</h3><a href='inputnotif.php'>Kembali</a></div>";
      exit;
  }

  // 3. Eksekusi Perubahan Data ketika tombol Ubah ditekan
  if(isset($_POST['Ubah']))
  {
      $user_id = $_POST['userID'];
      $task_id = $_POST['taskID'];
      $message = $_POST['message'];
      $scheduled_at = $_POST['scheduledAt'];

      mysqli_query($conn, "UPDATE notifications SET 
          user_id='$user_id', 
          task_id='$task_id', 
          message='$message', 
          scheduled_at='$scheduled_at'
          WHERE notification_id='$notification_id'");
          
      header("location:inputnotif.php");
  }
  ?>
  
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Notification ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" value="<?php echo $row_edit2["notification_id"]; ?>" disabled>
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
        <label for="message" class="col-sm-2 col-form-label">Message</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="message" name="message" placeholder="Input message..."
        value="<?php echo htmlspecialchars($row_edit2["message"]); ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="scheduledAt" class="col-sm-2 col-form-label">Scheduled At</label>
        <div class="col-sm-10">
        <?php 
            // Format datetime untuk input tipe datetime-local
            $scheduled_val = date('Y-m-d\TH:i', strtotime($row_edit2["scheduled_at"])); 
        ?>
        <input type="datetime-local" class="form-control" id="scheduledAt" name="scheduledAt" 
        value="<?php echo $scheduled_val; ?>" required>
        </div>
    </div>

  <div class="form-group row mt-4">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputnotif.php" class="btn btn-danger">Batal</a>
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