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
                            <li class="breadcrumb-item active">Edit Milestone</li>
                        </ol>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT MILESTONE | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <?php
  include("includes/config.php");
  
  // 1. Cek parameter editmilestones di URL
  if(isset($_GET["editmilestones"])) {
      $milestone_id = $_GET["editmilestones"];
  } else {
      header("location:inputmilestone.php");
      exit;
  }

  // 2. Ambil data milestone yang akan diedit
  $editmile = mysqli_query($conn, "SELECT * FROM milestones WHERE milestone_id ='$milestone_id'");
  $row_edit2 = mysqli_fetch_array($editmile);

  if(!$row_edit2) {
      echo "<div class='container mt-5'><h3>Data milestone tidak ditemukan.</h3><a href='inputmilestone.php'>Kembali</a></div>";
      exit;
  }

  // 3. Logika Update Data
  if(isset($_POST['Ubah']))
  {
      $task_id = $_POST['taskID'];
      $mile_title = $_POST['mileTitle'];
      $description = $_POST['mileDesc'];
      $status = $_POST['status'];
      $updated_at = date("Y-m-d H:i:s");

      // Menangkap file baru jika ada
      $file_url = $_FILES['milestoneFILE']['name'];
      $dokumen_tmp = $_FILES['milestoneFILE']['tmp_name'];

      // Jika user TIDAK mengupload file baru
      if (empty($file_url)) {
          mysqli_query($conn, "UPDATE milestones SET 
              task_id='$task_id', 
              mile_title='$mile_title', 
              description='$description', 
              status='$status', 
              updated_at='$updated_at' 
              WHERE milestone_id='$milestone_id'");
              
          header("location:inputmilestone.php");
      } 
      // Jika user MENGUPLOAD file baru
      else {
          $typefile = pathinfo($file_url, PATHINFO_EXTENSION);
          $ukuranfile = $_FILES["milestoneFILE"]["size"];
          
          if ($ukuranfile <= 10000000) { // Maks 10MB
              if ($typefile == "png" || $typefile == "jpg" || $typefile == "jpeg" || $typefile == "pdf") {
                  move_uploaded_file($dokumen_tmp, 'images/' . $file_url);

                  mysqli_query($conn, "UPDATE milestones SET 
                      task_id='$task_id', 
                      mile_title='$mile_title', 
                      description='$description', 
                      status='$status', 
                      file_url='$file_url', 
                      updated_at='$updated_at' 
                      WHERE milestone_id='$milestone_id'");
                      
                  header("location:inputmilestone.php");
              } else {
                  echo "<script>alert('File HARUS dalam bentuk PNG, JPG, JPEG, atau PDF!');</script>";
              }
          } else {
              echo "<script>alert('Ukuran File TIDAK boleh lebih dari 10MB!');</script>";
          }
      }
  }
  ?>
  
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST" enctype="multipart/form-data">
    <div class="row mb-3 mt-5">
        <label for="milestoneID" class="col-sm-2 col-form-label">Milestone ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="milestoneID" name="milestoneID" 
        value="<?php echo $row_edit2["milestone_id"]; ?>" disabled>
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
        <label for="mileTitle" class="col-sm-2 col-form-label">Milestone Title</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="mileTitle" name="mileTitle" placeholder="Input Title"
        value="<?php echo $row_edit2["mile_title"]; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="mileDesc" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="mileDesc" name="mileDesc" placeholder="Input Description"
        value="<?php echo $row_edit2["description"]; ?>">
        </div>
    </div>

    <div class="row mb-3">
        <label for="status" class="col-sm-2 col-form-label">Status</label>    
        <div class="col-sm-10">
            <select class="form-control" id="status" name="status" required>
                <option value="pending" <?php if($row_edit2["status"] == "pending") echo "selected"; ?>>Pending</option>
                <option value="in_progress" <?php if($row_edit2["status"] == "in_progress") echo "selected"; ?>>In Progress</option>
                <option value="completed" <?php if($row_edit2["status"] == "completed") echo "selected"; ?>>Completed</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="milestoneFILE" class="col-sm-2 col-form-label">Proof File</label>
        <div class="col-sm-10">
            <input type="file" class="form-control" id="milestoneFILE" name="milestoneFILE" accept=".png,.jpg,.jpeg,.pdf">
            <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah file saat ini. File saat ini: <strong><?php echo $row_edit2["file_url"]; ?></strong></small>
        </div>
    </div>

  <div class="form-group row mt-4">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputmilestone.php" class="btn btn-danger">Batal</a>
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