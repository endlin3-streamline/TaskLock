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

	<title>INPUT USERS | Software Developments</title>
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

    //CARA DAPET USER ID
    $year = date("y"); // "26" for 2026

    // Find the highest existing ID for this year
    $result = mysqli_query($conn, "SELECT MAX(user_id) as last_id FROM users WHERE user_id LIKE '$year%'");
    $row_id = mysqli_fetch_assoc($result);
    if ($row_id['last_id']) {
        // Extract the last 4 digits and increment
        $last_number = intval(substr($row_id['last_id'], 2)); // e.g. "0001" → 1
        $new_number = $last_number + 1;
    } else {
        // No user this year yet, start from 1
        $new_number = 1;
    }


    // Combine year + padded 4-digit number
    $user_id = $year . str_pad($new_number, 4, "0", STR_PAD_LEFT);
    $username = $_POST['userName'];
    $email = $_POST['userEmail'];
    $password_hash = $_POST['userPasswordHash'];
    $university = $_POST['userUniv'];
    $major = $_POST['userMajor'];
    $auth_provider = $_POST['authProv'];
    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");

    mysqli_query($conn, "insert into users values('$user_id', '$username', '$email', '$password_hash', '$university', '$major', '$auth_provider', '$created_at', '$updated_at')");
    header("location:inputuser.php");
  }

  // ------ SEARCH FILTER ------
  $query = mysqli_query($conn, "select * from users");
  
  if(isset($_POST["search"]) && $_POST["search"] != ""){
    $search = $_POST["search"];
    $query = mysqli_query($conn, "SELECT * FROM users
        WHERE user_id LIKE '%$search%'");
  }
?>

<div class="row"></div>
<div class="col-1"></div>
<div class="col-10">

<form method="POST">
  <!-- one input line -->
  <div class="row mb-3 mt-5">
    <label for="" class="col-sm-2 col-form-label">User ID</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" placeholder="Auto increment" disabled>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userName" class="col-sm-2 col-form-label">Username</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="userName" name="userName" placeholder="Input text..."required>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userEmail" class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="userEmail" name="userEmail" placeholder="Input text..." required>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userPasswordHash" class="col-sm-2 col-form-label">Password</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="userPasswordHash" name="userPasswordHash" placeholder="Input text..." required>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userUniv" class="col-sm-2 col-form-label">University</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="userUniv" name="userUniv" placeholder="Input text..." required>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userMajor" class="col-sm-2 col-form-label">Major</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="userMajor" name="userMajor" placeholder="Input text..." required>
    </div>
  </div>

  <div class="row mb-3">
    <label for="authProv" class="col-sm-2 col-form-label">Auth Provider</label>    
    <div class="col-sm-10">
        <select class="form-control" id="authProv" name="authProv" required>
            <option>Input selection...</option>
            <option value="Email">Email</option>
            <option value="Google">Google</option>
            <option value="Github">Github</option>
            <option value="Facebook">Facebook</option>
        </select>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userUniv" class="col-sm-2 col-form-label">User Created At</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" placeholder="Just now" disabled>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userUniv" class="col-sm-2 col-form-label">User Updated At</label>
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
  <label for="search" class="col-sm-2">Cari ID User</label>
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
    <th>User ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Password</th>
    <th>University</th>
    <th>Major</th>
    <th>Auth Provider</th>
    <th>Created At</th>
    <th>Updated At</th>
    <th colspan="2" style="text-align:center;">Action</th>
  </tr>


  <?php { ?>
  <?php while ($row = mysqli_fetch_array($query))
    { ?>
      <tr class="danger">
        <td><?php echo $row['user_id']; ?></td>
        <td><?php echo $row['username']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['password_hash']; ?></td>
        <td><?php echo $row['university']; ?></td>
        <td><?php echo $row['major']; ?></td>
        <td><?php echo $row['auth_provider']; ?></td>
        <td><?php echo $row['created_at']; ?></td>
        <td><?php echo $row['updated_at']; ?></td>
        <td>
            <a href="edituser.php?edituser=<?php echo $row["user_id"]?>" class="btn btn-success" title="Edit">
                <i class="bi bi-pencil-fill"></i>
            </a>
        </td>
        <td>
            <a href="deleteuser.php?deleteusers=<?php echo $row["user_id"] ?>" class="btn btn-danger" title="Delete">
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