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
    <title>INPUT POINTS | Software Developments</title>
</head>

<style>
    body{ margin: 55px; }
    .table{ margin-top: 20px; }
</style>
<body>
<?php
include("includes/config.php");

if(isset($_POST['Save'])){

    // ----- INCREMENT POINT ID -----
    $result_pk = mysqli_query($conn, "SELECT MAX(point_id) as last_point FROM points");
    $row_pk = mysqli_fetch_assoc($result_pk);

    if ($row_pk['last_point']) {
        $last_number = intval(substr($row_pk['last_point'], 2));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }

    $point_id = "PT" . str_pad($new_number, 11, "0", STR_PAD_LEFT);
    $user_id = $_POST['userID'];
    $milestone_id = $_POST['milestoneID'];
    $amount = $_POST['amount'];
    $source = $_POST['source'];
    $description = $_POST['description'];
    $earned_at = date("Y-m-d H:i:s");

    mysqli_query($conn, "INSERT INTO points VALUES('$point_id', '$user_id', '$milestone_id', '$amount', '$source', '$description', '$earned_at')");
    header("location:inputpoint.php");
}

    // ------ DROPDOWN DATA ------
    $datauser = mysqli_query($conn, "SELECT * FROM users");
    $datamilestone = mysqli_query($conn, "SELECT * FROM milestones");

    // ------ SEARCH FILTER ------
    $query = mysqli_query($conn, "SELECT * FROM points");

    if(isset($_POST["search"]) && $_POST["search"] != ""){
        $search = $_POST["search"];
        $query = mysqli_query($conn, "SELECT * FROM points
            WHERE user_id LIKE '%$search%'
            OR milestone_id LIKE '%$search%'
            OR point_id LIKE '%$search%'
        ");
    }
?>

<div class="row"></div>
<div class="col-1"></div>
<div class="col-10">

<form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Point ID</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" placeholder="Auto increment" disabled>
        </div>
    </div>

    <div class="row mb-3">
        <label for="userID" class="col-sm-2 col-form-label">User ID</label>
        <div class="col-sm-10">
            <select class="form-control" id="userID" name="userID" required>
                <option value="">Select User...</option>
                <?php while ($row = mysqli_fetch_array($datauser)){ ?>
                <option value="<?php echo $row['user_id']; ?>">
                    <?php echo $row['user_id']; ?> <?php echo $row['username']; ?>
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
                <?php while ($row = mysqli_fetch_array($datamilestone)){ ?>
                <option value="<?php echo $row['milestone_id']; ?>">
                    <?php echo $row['milestone_id']; ?> <?php echo $row['mile_title']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="amount" class="col-sm-2 col-form-label">Amount</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" id="amount" name="amount" placeholder="Input number..." required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="source" class="col-sm-2 col-form-label">Source</label>
        <div class="col-sm-10">
            <select class="form-control" id="source" name="source" required>
                <option value="milestones_reached">Milestones Reached</option>
                <option value="bonus">Bonus</option>
                <option value="streak_bonus">Streak Bonus</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="description" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="description" name="description" placeholder="Input text...">
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Earned At</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" placeholder="Just now" disabled>
        </div>
    </div>

    <div class="form-group-row">
        <div class="col-2"></div>
        <div class="col-10">
            <button type="submit" class="btn btn-success" name="Save">Save</button>
            <button type="button" class="btn btn-danger">Cancel</button>
        </div>
    </div>
</form>

<div class="1"></div>
</div>

<form method="POST">
    <div class="form-group row mt-5 mb-3">
        <label for="search" class="col-sm-2">Cari ID</label>
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
        <th>Point ID</th>
        <th>User ID</th>
        <th>Milestone ID</th>
        <th>Amount</th>
        <th>Source</th>
        <th>Description</th>
        <th>Earned At</th>
        <th colspan="2" style="text-align:center;">Action</th>
    </tr>

    <?php { ?>
    <?php while ($row = mysqli_fetch_array($query)){ ?>
        <tr>
            <td><?php echo $row['point_id']; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['milestone_id']; ?></td>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['source']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo $row['earned_at']; ?></td>
            <td>
                <a href="editpoints.php?editpoints=<?php echo $row["point_id"]?>" class="btn btn-success" title="Edit">
                    <i class="bi bi-pencil-fill"></i>
                </a>
            </td>
            <td>
                <a href="deletepoints.php?deletepoints=<?php echo $row["point_id"] ?>" class="btn btn-danger" title="Delete">
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