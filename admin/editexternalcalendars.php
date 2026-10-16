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
                            <li class="breadcrumb-item active">Edit External Calendar</li>
                        </ol>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT EXTERNAL CALENDARS | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <?php
  include("includes/config.php");
  
  // 1. Validasi parameter untuk menghindari error Undefined Key
  if(isset($_GET["editexternalcalendars"])) {
      $calendar_id = $_GET["editexternalcalendars"];
  } else {
      header("location:inputexternalcalendar.php");
      exit;
  }

  // 2. Ambil data calendar lama
  $editcal = mysqli_query($conn, "SELECT * FROM external_calendars WHERE calendar_id ='$calendar_id'");
  $row_edit2 = mysqli_fetch_array($editcal);

  if(!$row_edit2) {
      echo "<div class='container mt-5'><h3>Data tidak ditemukan.</h3><a href='inputexternalcalendar.php'>Kembali</a></div>";
      exit;
  }

  // 3. Eksekusi Perubahan Data ketika tombol Ubah ditekan
  if(isset($_POST['Ubah']))
  {
      $user_id = $_POST['userID'];
      $provider = $_POST['provider'];
      $access_token = $_POST['accessToken'];
      $refresh_token = $_POST['refreshToken'];
      $token_expires_at = $_POST['tokenExpiresAt'];
      $synced_at = date("Y-m-d H:i:s"); // Update waktu sinkronisasi ke waktu saat diedit

      mysqli_query($conn, "UPDATE external_calendars SET 
          user_id='$user_id', 
          provider='$provider', 
          access_token='$access_token', 
          refresh_token='$refresh_token', 
          token_expires_at='$token_expires_at',
          synced_at='$synced_at'
          WHERE calendar_id='$calendar_id'");
          
      header("location:inputexternalcalendar.php");
  }
  ?>
  
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Calendar ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" value="<?php echo $row_edit2["calendar_id"]; ?>" disabled>
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
        <label for="provider" class="col-sm-2 col-form-label">Provider</label>
        <div class="col-sm-10">
            <select class="form-control" id="provider" name="provider" required>
                <option value="Google" <?php if($row_edit2["provider"] == "Google") echo "selected"; ?>>Google</option>
                <option value="Outlook" <?php if($row_edit2["provider"] == "Outlook") echo "selected"; ?>>Outlook</option>
                <option value="Apple" <?php if($row_edit2["provider"] == "Apple") echo "selected"; ?>>Apple</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="accessToken" class="col-sm-2 col-form-label">Access Token</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="accessToken" name="accessToken" placeholder="Input access token..."
        value="<?php echo $row_edit2["access_token"]; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="refreshToken" class="col-sm-2 col-form-label">Refresh Token</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="refreshToken" name="refreshToken" placeholder="Input refresh token..."
        value="<?php echo $row_edit2["refresh_token"]; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="tokenExpiresAt" class="col-sm-2 col-form-label">Token Expires At</label>
        <div class="col-sm-10">
        <?php 
            // Format waktu ke format yang dibaca oleh datetime-local (YYYY-MM-DDThh:mm)
            $expires_val = date('Y-m-d\TH:i', strtotime($row_edit2["token_expires_at"])); 
        ?>
        <input type="datetime-local" class="form-control" id="tokenExpiresAt" name="tokenExpiresAt" 
        value="<?php echo $expires_val; ?>" required>
        </div>
    </div>

  <div class="form-group row mt-4">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputexternalcalendar.php" class="btn btn-danger">Batal</a>
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