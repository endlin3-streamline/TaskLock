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
                            <li class="breadcrumb-item active">Edit Setting</li>
                        </ol>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT SETTINGS | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <?php
  include("includes/config.php");
  
  // 1. Validasi parameter
  if(isset($_GET["editsettings"])) {
      $setting_id = $_GET["editsettings"];
  } else {
      header("location:inputsettings.php");
      exit;
  }

  // 2. Ambil data setting lama
  $editst = mysqli_query($conn, "SELECT * FROM settings WHERE setting_id ='$setting_id'");
  $row_edit2 = mysqli_fetch_array($editst);

  if(!$row_edit2) {
      echo "<div class='container mt-5'><h3>Data tidak ditemukan.</h3><a href='inputsettings.php'>Kembali</a></div>";
      exit;
  }

  // 3. Eksekusi Perubahan Data
  if(isset($_POST['Ubah']))
  {
      $user_id = $_POST['userID'];
      // Jika checkbox dicentang maka nilainya 1, jika tidak maka 0
      $alarm_enabled = isset($_POST['alarmEnabled']) ? 1 : 0;
      $notifications_enabled = isset($_POST['notificationsEnabled']) ? 1 : 0;
      $updated_at = date("Y-m-d H:i:s");

      mysqli_query($conn, "UPDATE settings SET 
          user_id='$user_id', 
          alarm_enabled='$alarm_enabled', 
          notifications_enabled='$notifications_enabled', 
          updated_at='$updated_at'
          WHERE setting_id='$setting_id'");
          
      header("location:inputsettings.php");
  }
  ?>
  
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Setting ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" value="<?php echo $row_edit2["setting_id"]; ?>" disabled>
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
        <label class="col-sm-2 col-form-label">Alarm Enabled</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="checkbox" id="alarmEnabled" name="alarmEnabled" value="1" style="width:20px; height:20px;"
            <?php if($row_edit2["alarm_enabled"] == 1) echo "checked"; ?>>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Notifications Enabled</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="checkbox" id="notificationsEnabled" name="notificationsEnabled" value="1" style="width:20px; height:20px;"
            <?php if($row_edit2["notifications_enabled"] == 1) echo "checked"; ?>>
        </div>
    </div>

  <div class="form-group row mt-4">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputsettings.php" class="btn btn-danger">Batal</a>
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