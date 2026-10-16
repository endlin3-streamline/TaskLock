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
                            <li class="breadcrumb-item active">Edit User</li>
                        </ol>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT USERS | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>

<body>
  <?php
  include("includes/config.php");
  
 
  $user_id = $_GET["edituser"];

  $edituser = mysqli_query($conn, "SELECT * FROM users WHERE user_id ='$user_id'");
  $row_edit2 = mysqli_fetch_array($edituser);

  if(isset($_POST['Ubah']))
  {
      $username = $_POST['userName'];
      $email = $_POST['userEmail'];
      $password_hash = $_POST['userPasswordHash'];
      $university = $_POST['userUniv'];
      $major = $_POST['userMajor'];
      $auth_provider = $_POST['authProv'];
      $updated_at = date("Y-m-d H:i:s");

      mysqli_query($conn, "UPDATE users SET 
          username='$username', 
          email='$email', 
          password_hash='$password_hash', 
          university='$university', 
          major='$major', 
          auth_provider='$auth_provider', 
          updated_at='$updated_at' 
          WHERE user_id='$user_id'");
          
      header("location:inputuser.php");
  }
  ?>
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST">
    <div class="row mb-3 mt-5">
        <label for="userId" class="col-sm-2 col-form-label">User ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="userId" name="userId" 
        value="<?php echo $row_edit2["user_id"]; ?>" disabled>
        </div>
    </div>

    <div class="row mb-3">
        <label for="userName" class="col-sm-2 col-form-label">Username</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="userName" name="userName" placeholder="Isikan Username"
        value="<?php echo $row_edit2["username"]; ?>" required>
        </div>
    </div>

  <div class="row mb-3">
    <label for="userEmail" class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="userEmail" name="userEmail" placeholder="Isikan Email"
    value="<?php echo $row_edit2["email"]; ?>" required>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userPasswordHash" class="col-sm-2 col-form-label">Password</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="userPasswordHash" name="userPasswordHash" placeholder="Isikan Password"
    value="<?php echo $row_edit2["password_hash"]; ?>" required>
    </div>
  </div>

  <div class="row mb-3">
    <label for="userUniv" class="col-sm-2 col-form-label">University</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="userUniv" name="userUniv" placeholder="Isikan Universitas"
    value="<?php echo $row_edit2["university"]; ?>" required>
    </div>
  </div>
  
  <div class="row mb-3">
    <label for="userMajor" class="col-sm-2 col-form-label">Major</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="userMajor" name="userMajor" placeholder="Isikan Jurusan"
    value="<?php echo $row_edit2["major"]; ?>" required>
    </div>
  </div>

  <div class="row mb-3">
    <label for="authProv" class="col-sm-2 col-form-label">Auth Provider</label>    
    <div class="col-sm-10">
        <select class="form-control" id="authProv" name="authProv" required>
            <option value="Email" <?php if($row_edit2["auth_provider"] == "Email") echo "selected"; ?>>Email</option>
            <option value="Google" <?php if($row_edit2["auth_provider"] == "Google" || $row_edit2["auth_provider"] == "google") echo "selected"; ?>>Google</option>
            <option value="Github" <?php if($row_edit2["auth_provider"] == "Github") echo "selected"; ?>>Github</option>
            <option value="Facebook" <?php if($row_edit2["auth_provider"] == "Facebook") echo "selected"; ?>>Facebook</option>
        </select>
    </div>
  </div>

  <div class="form-group row">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputuser.php" class="btn btn-danger">Batal</a>
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