<!DOCTYPE html>
<html lang="en">
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
                            <li class="breadcrumb-item active">Edit Batch</li>
                        </ol>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT BATCHES | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <?php
  include("includes/config.php");
  
  if(isset($_GET["editbatches"])) {
      $batch_id = $_GET["editbatches"];
  } else {
      header("location:inputbatch.php");
      exit;
  }

  $editbatch = mysqli_query($conn, "SELECT * FROM batches WHERE batch_id ='$batch_id'");
  $row_edit2 = mysqli_fetch_array($editbatch);

  if(!$row_edit2) {
      echo "<div class='container mt-5'><h3>Data batch tidak ditemukan.</h3><a href='inputbatch.php'>Kembali</a></div>";
      exit;
  }

  if(isset($_POST['Ubah']))
  {
      $user_id = $_POST['userID'];
      $batch_name = $_POST['batchName'];
      $updated_at = date("Y-m-d H:i:s"); 

      mysqli_query($conn, "UPDATE batches SET 
          user_id='$user_id', 
          batch_name='$batch_name', 
          updated_at='$updated_at' 
          WHERE batch_id='$batch_id'");
          
      header("location:inputbatch.php");
  }
  ?>
  
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST">
    <div class="row mb-3 mt-5">
        <label for="batchID" class="col-sm-2 col-form-label">Batch ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="batchID" name="batchID" 
        value="<?php echo $row_edit2["batch_id"]; ?>" disabled>
        </div>
    </div>

    <div class="row mb-3">
        <label for="userID" class="col-sm-2 col-form-label">User ID</label>
        <div class="col-sm-10">
            <select class="form-control" id="userID" name="userID" required>
                <option value="">Pilih User...</option>
                <?php 
                // Mengambil data user untuk dropdown
                $datauser = mysqli_query($conn, "SELECT * FROM users");
                while ($row_user = mysqli_fetch_array($datauser)) { 
                    // Menandai opsi yang dipilih (selected) sesuai data di database
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
        <label for="batchName" class="col-sm-2 col-form-label">Batch Name</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="batchName" name="batchName" placeholder="Input Batch Name"
        value="<?php echo $row_edit2["batch_name"]; ?>" required>
        </div>
    </div>

  <div class="form-group row mt-4">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputbatch.php" class="btn btn-danger">Batal</a>
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
