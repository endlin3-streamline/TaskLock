<!DOCTYPE html>
<html>
    <?php
        include "bagiankode/head.php";
    ?>
    <?php
        ob_start();
        session_start();
        if(!isset($_SESSION['useremail']))
        header("location:login.php");
    ?>

<body class="sb-nav-fixed">
    <?php include "bagiankode/menunav.php" ?>
    <div id="layoutSidenav">
        <?php include "bagiankode/menu.php" ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

	<title>INPUT MILESTONE | Software Developments</title>
</head>

<style>
	body{
		margin: 55px;
	}

  .table{
    margin-top: 20px;
  }
</style>
<body>
<?php
include("includes/config.php");

if(isset($_POST['Save'])){
    //----- INCREMENT DATA ID -----
    $result_batch = mysqli_query($conn, "SELECT MAX(milestone_id) as last_mile FROM milestones");
    $row_batch = mysqli_fetch_assoc($result_batch);

    if ($row_batch['last_mile']) {
        $last_number = intval(substr($row_batch['last_mile'], 4)); // get number
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    // --- VARIABLES ---
    $milestone_id = "MILE" . str_pad($new_number, 11, "0", STR_PAD_LEFT);
    $task_id = $_POST['taskID'];
    $mile_title = $_POST['mileTitle'];
    $description = $_POST['mileDesc'];
    $status = $_POST['status'];
    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");

    // --- VARIABLE FOR IMAGE ---
    $file_url = $_FILES['milestoneFILE']['name'];
    $dokumen_tmp = $_FILES['milestoneFILE']['tmp_name'];

    $typefile = pathinfo($file_url, PATHINFO_EXTENSION);
    $ukuranfile = $_FILES["milestoneFILE"]["size"];
    $path = "images/" . $file_url;

    if (!empty($file_url)) {
        // jika ukuran file kurang atau sama dengan 10MB
        if ($ukuranfile <= 10000000) {
            // jika tipe file adalah png, jpg, jpeg, atau pdf
            if ($typefile == "png" || $typefile == "jpg" || $typefile == "jpeg" || $typefile == "pdf") {
                // jika file belum ada di folder
                if (!file_exists($path)) {
                    move_uploaded_file($dokumen_tmp, 'images/' . $file_url);

                    mysqli_query($conn, "INSERT INTO milestones VALUES(
                        '$milestone_id', '$task_id', '$mile_title','$description',
                        '$status', '$file_url', '$created_at', '$updated_at'
                    )");
                    header('Location: inputmilestone.php');
                    exit;

                } else {
                    echo "<script>alert('File dengan nama ini sudah ada!');</script>";
                }
            } else {
                echo "<script>alert('File HARUS dalam bentuk PNG, JPG, JPEG, atau PDF!');</script>";
            }
        } else {
            echo "<script>alert('Ukuran File TIDAK boleh lebih dari 10MB!');</script>";
        }
    } else {
        // jika user tidak upload file
        $file_url = "nofile.png";

        mysqli_query($conn, "INSERT INTO milestones VALUES(
            '$milestone_id', '$task_id', '$mile_title','$description',
            '$status', '$file_url', '$created_at', '$updated_at'
        )");

        header('Location: inputmilestone.php');
        exit;
    }

}

    // ------ DROPDOWN DATA ------
    $datatask = mysqli_query($conn, "SELECT * FROM tasks");

    // ------ SEARCH FILTER ------
    $query = mysqli_query($conn, "select * from milestones");

    if(isset($_POST["search"]) && $_POST["search"] != ""){
        $search = $_POST["search"];
        $query = mysqli_query($conn, "SELECT * FROM milestones
            WHERE task_id LIKE '%$search%'");
    }
?>

<div class="row"></div>
<div class="col-1"></div>
<div class="col-10">

<form method="POST" enctype="multipart/form-data">
    <!-- one input line -->
    <div class="row mb-3 mt-5">
        <label for="" class="col-sm-2 col-form-label">Milestone ID</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" placeholder="Auto increment" disabled>
        </div>
    </div>

    <div class="row mb-3">
        <label for="taskID" class="col-sm-2 col-form-label">Task ID</label>
        <div class="col-sm-10">
            <select class="form-control" id="taskID" name="taskID" required>   
                <option>Input task...</option>
                <?php while ($row = mysqli_fetch_array($datatask))
                { ?>
                <option value="<?php echo $row['task_id']; ?>">
                    <?php echo $row['task_id']; ?> 
                    <?php echo $row['task_title']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="mileTitle" class="col-sm-2 col-form-label">Milestone title</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="mileTitle" name="mileTitle" placeholder="Input text..." required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="mileDesc" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="mileDesc" name="mileDesc" placeholder="Input text...">
        </div>
    </div>

    <div class="row mb-3">
        <label for="status" class="col-sm-2 col-form-label">Status</label>
        <div class="col-sm-10">
            <select class="form-control" id="status" name="status" required>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="milestoneFILE" class="col-sm-2 col-form-label">Proof File</label>
        <div class="col-sm-10">
        <input type="file" class="form-control" id="milestoneFILE" name="milestoneFILE" accept=".png,.jpg,.jpeg,.pdf">
        </div>
    </div>

    <div class="row mb-3">
        <label for="" class="col-sm-2 col-form-label">Milestone Created At</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" placeholder="Just now" disabled>
        </div>
    </div>

    <div class="row mb-3">
        <label for="" class="col-sm-2 col-form-label">Milestone Updated At</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" placeholder="Just now" disabled>
        </div>
    </div>

    <!-- button -->
    <div class="form-group-row">
        <div class="col-2"></div>
        <div class="col-10">
            <button type="submit" class="btn btn-success" name="Save">Save</button>
            <button type="danger" class="btn btn-danger" name="danger">Cancel</button>
        </div>
    </div>
</form>

<div class="1"></div>
</div>

<form method="POST">
  <div class="form-group row mt-5 mb-3">
    <label for="search" class="col-sm-2">Cari Task ID</label>
    <div class="col-sm-6">
        <input type="text" name="search" class="form-control" id="search"
                value="<?php if(isset($_POST['search'])){ echo $_POST['search']; } ?>"
                placeholder="Cari Task ID...">
    </div>
    <button type="submit" class="col-sm-1 btn btn-primary">Search</button>
  </div>
</form>

<table class="table table-striped table-success">
  <tr class="info">
    <th>Milestone ID</th>
    <th>Task ID</th>
    <th>Title</th>
    <th>Description</th>
    <th>Status</th>
    <th>File Proof</th>
    <th>Created At</th>
    <th>Updated At</th>
    <th colspan="2" style="text-align:center;">Action</th>
  </tr>


  <?php { ?>
  <?php while ($row = mysqli_fetch_array($query))
    { ?>
      <tr class="danger">
        <td><?php echo $row['milestone_id']; ?></td>
        <td><?php echo $row['task_id']; ?></td>
        <td><?php echo $row['mile_title']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <?php 
            if ($row['file_url'] == "") {
                echo "<img src='images/nofile.png' width='88'/>";
            } else { 
            ?>
                <img src="images/<?php echo $row['file_url']; ?>" width="88" class="img-responsive" />
            <?php } ?>
        </td>
        <td><?php echo $row['created_at']; ?></td>
        <td><?php echo $row['updated_at']; ?></td>
        <td>
            <a href="editmilestones.php?editmilestones=<?php echo $row["milestone_id"]?>" class="btn btn-success" title="Edit">
                <i class="bi bi-pencil-fill"></i>
            </a>
        </td>
        <td>
            <a href="deletemilestones.php?deletemilestones=<?php echo $row["milestone_id"] ?>" class="btn btn-danger" title="Delete">
                <i class="bi bi-trash3-fill"></i>
            </a>
        </td>
      </tr>
    <?php } ?>
  <?php }?>
</table>

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</body>



              </div>  
            </main>
            <?php include "bagiankode/footer.php"; ?>
        </div>
    </div>
    <?php include "bagiankode/jsscript.php"; ?>
</body>
</html>