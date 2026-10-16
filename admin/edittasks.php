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
                            <li class="breadcrumb-item active">Edit Task</li>
                        </ol>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT TASKS | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <?php
  include("includes/config.php");
  
  // 1. Cek parameter edittasks di URL
  if(isset($_GET["edittasks"])) {
      $task_id = $_GET["edittasks"];
  } else {
      header("location:inputtask.php");
      exit;
  }

  // 2. Ambil data task yang akan diedit
  $edittask = mysqli_query($conn, "SELECT * FROM tasks WHERE task_id ='$task_id'");
  $row_edit2 = mysqli_fetch_array($edittask);

  if(!$row_edit2) {
      echo "<div class='container mt-5'><h3>Data task tidak ditemukan.</h3><a href='inputtask.php'>Kembali</a></div>";
      exit;
  }

  // 3. Logika Update Data
  if(isset($_POST['Ubah']))
  {
      $batch_id = $_POST['batchID'];
      $user_id = $_POST['userID'];
      $task_title = $_POST['taskTitle'];
      $description = $_POST['taskDesc'];
      $priority = $_POST['taskPrior'];
      $status = $_POST['status'];
      $deadline = $_POST['deadline'];
      $updated_at = date("Y-m-d H:i:s");

      mysqli_query($conn, "UPDATE tasks SET 
          batch_id='$batch_id', 
          user_id='$user_id', 
          task_title='$task_title', 
          description='$description', 
          priority='$priority', 
          status='$status', 
          deadline='$deadline', 
          updated_at='$updated_at' 
          WHERE task_id='$task_id'");
          
      header("location:inputtask.php");
  }

  // ----- STORE BATCH-USER RELATION UNTUK JAVASCRIPT -----
  $relation = [];
  $rel_query = mysqli_query($conn, "SELECT batch_id, user_id FROM batches");
  while($r = mysqli_fetch_assoc($rel_query)){
      $relation[$r['batch_id']] = $r['user_id'];
  }
  ?>
  
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST">
    <div class="row mb-3 mt-5">
        <label for="taskID" class="col-sm-2 col-form-label">Task ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="taskID" name="taskID" 
        value="<?php echo $row_edit2["task_id"]; ?>" disabled>
        </div>
    </div>

    <div class="row mb-3">
        <label for="batchID" class="col-sm-2 col-form-label">Batch ID</label>
        <div class="col-sm-10">
            <select class="form-control" id="batchID" name="batchID" required onchange="fillUser(this.value)">
                <option value="">Select Batch...</option>
                <?php 
                $databatch = mysqli_query($conn, "SELECT * FROM batches");
                while ($row_batch = mysqli_fetch_array($databatch)) { 
                    $selected = ($row_batch['batch_id'] == $row_edit2['batch_id']) ? "selected" : "";
                ?>
                <option value="<?php echo $row_batch['batch_id']; ?>" <?php echo $selected; ?>>
                    <?php echo $row_batch['batch_id']; ?> - <?php echo $row_batch['batch_name']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="userID" class="col-sm-2 col-form-label">User ID</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="userID" name="userID" 
            value="<?php echo $row_edit2["user_id"]; ?>" readonly>
        </div>
    </div>

    <div class="row mb-3">
        <label for="taskTitle" class="col-sm-2 col-form-label">Task Title</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="taskTitle" name="taskTitle" placeholder="Input Task Title"
        value="<?php echo $row_edit2["task_title"]; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="taskDesc" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="taskDesc" name="taskDesc" placeholder="Input Description"
        value="<?php echo $row_edit2["description"]; ?>">
        </div>
    </div>

    <div class="row mb-3">
        <label for="taskPrior" class="col-sm-2 col-form-label">Priority</label>    
        <div class="col-sm-10">
            <select class="form-control" id="taskPrior" name="taskPrior" required>
                <option value="low" <?php if($row_edit2["priority"] == "low") echo "selected"; ?>>Low</option>
                <option value="medium" <?php if($row_edit2["priority"] == "medium") echo "selected"; ?>>Medium</option>
                <option value="high" <?php if($row_edit2["priority"] == "high") echo "selected"; ?>>High</option>
            </select>
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
        <label for="deadline" class="col-sm-2 col-form-label">Deadline</label>
        <div class="col-sm-10">
        <input type="date" class="form-control" id="deadline" name="deadline" 
        value="<?php echo $row_edit2["deadline"]; ?>" required>
        </div>
    </div>

  <div class="form-group row mt-4">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputtask.php" class="btn btn-danger">Batal</a>
    </div>
  </div>
  </form>

    </div>
  </div>

    <script>
        const batchUserMap = <?php echo json_encode($relation); ?>;
        function fillUser(batch_id) {
            if(batchUserMap[batch_id]) {
                document.getElementById("userID").value = batchUserMap[batch_id];
            } else {
                document.getElementById("userID").value = "";
            }
        }
    </script>
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