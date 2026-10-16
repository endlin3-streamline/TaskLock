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

	<title>INPUT TASKS | Software Developments</title>
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

    //CARA DAPET BATCH ID
    $result_pk = mysqli_query($conn, "SELECT MAX(task_id) as last_task FROM tasks");
    $row_pk = mysqli_fetch_assoc($result_pk);

    if ($row_pk['last_task']) {
        $last_number = intval(substr($row_pk['last_task'], 2)); // get number
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }

    
    $task_id = "TK" . str_pad($new_number, 11, "0", STR_PAD_LEFT);
    $user_id = $_POST['userID'];
    $batch_id = $_POST['batchID'];

    $task_title = $_POST['taskTitle'];
    $description = $_POST['taskDesc'];
    $priority = $_POST['taskPrior'];
    $status = $_POST['status'];
    $deadline = $_POST['deadline'];

    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");

    mysqli_query($conn, "insert into tasks values('$task_id', '$user_id', '$batch_id', '$task_title', '$description', '$priority', '$status', '$deadline', '$created_at', '$updated_at')");
    header("location:inputtask.php");
  }

    // ----- STORE BATCH-USER RELATION -----
    $relation = [];
    $rel_query = mysqli_query($conn, "SELECT batch_id, user_id FROM batches");
    while($r = mysqli_fetch_assoc($rel_query)){
        $relation[$r['batch_id']] = $r['user_id'];
    }

    // ------ DROPDOWN DATA ------
    $databatch = mysqli_query($conn, "SELECT * FROM batches");
    $datajoin = mysqli_query($conn, "
        SELECT DISTINCT u.user_id, u.username AS username
        FROM users u
        JOIN batches b ON u.user_id = b.user_id
        ORDER BY u.user_id
    ");

    // ------ SEARCH FILTER ------
    $query = mysqli_query($conn, "
        SELECT t.*, u.username 
        FROM tasks t
        JOIN users u ON t.user_id = u.user_id
    ");

    if(isset($_POST["search"]) && $_POST["search"] != ""){
        $search = $_POST["search"];
        $query = mysqli_query($conn, "
            SELECT t.*, u.username 
            FROM tasks t
            JOIN users u ON t.user_id = u.user_id
            WHERE t.batch_id LIKE '%$search%'
        ");
    }

?>

<div class="row"></div>
<div class="col-1"></div>
<div class="col-10">

<form method="POST">
  <!-- one input line -->
  <div class="row mb-3 mt-5">
    <label for="" class="col-sm-2 col-form-label">Task ID</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" placeholder="Auto increment" disabled>
    </div>
  </div>



  <div class="row mb-3">
    <label for="batchID" class="col-sm-2 col-form-label">Batch ID</label>
    <div class="col-sm-10">
        <select class="form-control" id="batchID" name="batchID" required onchange="fillUser(this.value)">
            <option value="">Select Batch...</option>
            <?php while ($row = mysqli_fetch_array($databatch)){ ?>
            <option value="<?php echo $row['batch_id']; ?>">
                <?php echo $row['batch_id']; ?> <?php echo $row['batch_name']; ?>
            </option>
            <?php } ?>
        </select>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userID" class="col-sm-2 col-form-label">User ID</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="userID" name="userID" placeholder="Auto fill" readonly>
    </div>
  </div>



  <div class="row mb-3">
    <label for="taskTitle" class="col-sm-2 col-form-label">Task Title</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="taskTitle" name="taskTitle" placeholder="Input text..." required>
    </div>
  </div>

  <div class="row mb-3">
    <label for="taskDesc" class="col-sm-2 col-form-label">Description</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="taskDesc" name="taskDesc" placeholder="Input text...">
    </div>
  </div>

  <div class="row mb-3">
    <label for="taskPrior" class="col-sm-2 col-form-label">Priority</label>    
    <div class="col-sm-10">
        <select class="form-control" id="taskPrior" name="taskPrior" required>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
        </select>
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
    <label for="deadline" class="col-sm-2 col-form-label">Deadline</label>
    <div class="col-sm-10">
      <input type="date" class="form-control" id="deadline" name="deadline" placeholder="Input text..." required>
    </div>
  </div>



  <div class="row mb-3">
    <label for="" class="col-sm-2 col-form-label">Task Created At</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" placeholder="Just now" disabled>
    </div>
  </div>

  <div class="row mb-3">
    <label for="" class="col-sm-2 col-form-label">Task Updated At</label>
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
  <label for="search" class="col-sm-2">Cari ID Batch</label>
  <div class="col-sm-6">
      <input type="text" name="search" class="form-control" id="search"
              value="<?php if(isset($_POST['search'])){ echo $_POST['search']; } ?>"
              placeholder="Cari ID...">
  </div>
  <button type="submit" class="col-sm-1 btn btn-primary">Search</button>
  </div>
</form>

<table class="table table-striped table-success">
    <tr class="info">
        <th>Task ID</th>
        <th>Batch ID</th>
        <th>User ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Deadline</th>
        <th>Created At</th>
        <th>Updated At</th>      
        <th colspan="2" style="text-align:center;">Action</th>
    </tr>


  <?php { ?>
  <?php while ($row = mysqli_fetch_array($query))
    { ?>
      <tr class="danger">
        <td><?php echo $row['task_id']; ?></td>
        <td><?php echo $row['batch_id']; ?></td>
        <td><?php echo $row['user_id']; ?></td>
        <td><?php echo $row['task_title']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['priority']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><?php echo $row['deadline']; ?></td>
        <td><?php echo $row['created_at']; ?></td>
        <td><?php echo $row['updated_at']; ?></td>
        <td>
            <a href="edittasks.php?edittasks=<?php echo $row["task_id"]?>" class="btn btn-success" title="Edit">
                <i class="bi bi-pencil-fill"></i>
            </a>
        </td>
        <td>
            <a href="deletetasks.php?deletetasks=<?php echo $row["task_id"] ?>" class="btn btn-danger" title="Delete">
                <i class="bi bi-trash3-fill"></i>
            </a>
        </td>
      </tr>
    <?php } ?>
  <?php }?>
</table>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        const batchUserMap = <?php echo json_encode($relation); ?>;
        // looks like: { "BA000001": "260001", "BA000002": "260001", "BA000003": "260002" }
    </script>

    <script>
        function fillUser(batch_id) {
            const user_id = batchUserMap[batch_id];
            document.getElementById("userID").value = user_id;
        }
    </script>
</body>



              </div>  
            </main>
            <?php include "bagiankode/footer.php"; ?>
        </div>
    </div>
    <?php include "bagiankode/jsscript.php"; ?>
</body>
</html>