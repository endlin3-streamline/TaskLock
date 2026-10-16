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
                            <li class="breadcrumb-item active">Edit Point</li>
                        </ol>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT POINTS | Software Developments</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <?php
  include("includes/config.php");
  
  // 1. Validasi parameter
  if(isset($_GET["editpoints"])) {
      $point_id = $_GET["editpoints"];
  } else {
      header("location:inputpoint.php");
      exit;
  }

  // 2. Ambil data point lama
  $editpt = mysqli_query($conn, "SELECT * FROM points WHERE point_id ='$point_id'");
  $row_edit2 = mysqli_fetch_array($editpt);

  if(!$row_edit2) {
      echo "<div class='container mt-5'><h3>Data tidak ditemukan.</h3><a href='inputpoint.php'>Kembali</a></div>";
      exit;
  }

  // 3. Eksekusi Perubahan Data
  if(isset($_POST['Ubah']))
  {
      $user_id = $_POST['userID'];
      $milestone_id = $_POST['milestoneID'];
      $amount = $_POST['amount'];
      $source = $_POST['source'];
      $description = $_POST['description'];

      mysqli_query($conn, "UPDATE points SET 
          user_id='$user_id', 
          milestone_id='$milestone_id', 
          amount='$amount', 
          source='$source', 
          description='$description'
          WHERE point_id='$point_id'");
          
      header("location:inputpoint.php");
  }
  ?>
  
  <div class="row">
    <div class="col-1"></div>
    <div class="col-10">

    <form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Point ID</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" value="<?php echo $row_edit2["point_id"]; ?>" disabled>
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
        <label for="milestoneID" class="col-sm-2 col-form-label">Milestone ID</label>
        <div class="col-sm-10">
            <select class="form-control" id="milestoneID" name="milestoneID" required>
                <option value="">Select Milestone...</option>
                <?php 
                $datamilestone = mysqli_query($conn, "SELECT * FROM milestones");
                while ($row_mile = mysqli_fetch_array($datamilestone)) { 
                    $selected = ($row_mile['milestone_id'] == $row_edit2['milestone_id']) ? "selected" : "";
                ?>
                <option value="<?php echo $row_mile['milestone_id']; ?>" <?php echo $selected; ?>>
                    <?php echo $row_mile['milestone_id']; ?> - <?php echo $row_mile['mile_title']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="amount" class="col-sm-2 col-form-label">Amount</label>
        <div class="col-sm-10">
        <input type="number" class="form-control" id="amount" name="amount" placeholder="Input number..."
        value="<?php echo $row_edit2["amount"]; ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="source" class="col-sm-2 col-form-label">Source</label>
        <div class="col-sm-10">
            <select class="form-control" id="source" name="source" required>
                <option value="milestones_reached" <?php if($row_edit2["source"] == "milestones_reached") echo "selected"; ?>>Milestones Reached</option>
                <option value="bonus" <?php if($row_edit2["source"] == "bonus") echo "selected"; ?>>Bonus</option>
                <option value="streak_bonus" <?php if($row_edit2["source"] == "streak_bonus") echo "selected"; ?>>Streak Bonus</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="description" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="description" name="description" placeholder="Input text..."
        value="<?php echo htmlspecialchars($row_edit2["description"]); ?>">
        </div>
    </div>

  <div class="form-group row mt-4">
    <div class="col-2"></div>
    <div class="col-10">
      <input type="submit" class="btn btn-success" value="Ubah" name="Ubah">
      <a href="inputpoint.php" class="btn btn-danger">Batal</a>
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