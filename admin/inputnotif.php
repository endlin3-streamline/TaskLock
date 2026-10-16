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
    <title>INPUT NOTIFICATIONS | Software Developments</title>
</head>

<style>
    body{ margin: 55px; }
    .table{ margin-top: 20px; }
</style>
<body>
<?php
include("includes/config.php");

if(isset($_POST['Save'])){

    // ----- INCREMENT NOTIFICATION ID -----
    $result_pk = mysqli_query($conn, "SELECT MAX(notification_id) as last_notif FROM notifications");
    $row_pk = mysqli_fetch_assoc($result_pk);

    if ($row_pk['last_notif']) {
        $last_number = intval(substr($row_pk['last_notif'], 2));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }

    $notification_id = "NF" . str_pad($new_number, 11, "0", STR_PAD_LEFT);
    $user_id = $_POST['userID'];
    $task_id = $_POST['taskID'];
    $message = $_POST['message'];
    $scheduled_at = $_POST['scheduledAt'];
    $created_at = date("Y-m-d H:i:s");

    mysqli_query($conn, "INSERT INTO notifications VALUES('$notification_id', '$user_id', '$task_id', '$message', '$scheduled_at', '$created_at')");
    header("location:inputnotif.php");
}

    // ------ DROPDOWN DATA ------
    $datauser = mysqli_query($conn, "SELECT * FROM users");
    $datatask = mysqli_query($conn, "SELECT * FROM tasks");

    // ------ SEARCH FILTER ------
    $query = mysqli_query($conn, "SELECT * FROM notifications");

    if(isset($_POST["search"]) && $_POST["search"] != ""){
        $search = $_POST["search"];
        $query = mysqli_query($conn, "SELECT * FROM notifications
            WHERE user_id LIKE '%$search%'
            OR task_id LIKE '%$search%'
            OR notification_id LIKE '%$search%'
        ");
    }
?>

<div class="row"></div>
<div class="col-1"></div>
<div class="col-10">

<form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Notification ID</label>
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
        <label for="taskID" class="col-sm-2 col-form-label">Task ID</label>
        <div class="col-sm-10">
            <select class="form-control" id="taskID" name="taskID" required>
                <option value="">Select Task...</option>
                <?php while ($row = mysqli_fetch_array($datatask)){ ?>
                <option value="<?php echo $row['task_id']; ?>">
                    <?php echo $row['task_id']; ?> <?php echo $row['task_title']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="message" class="col-sm-2 col-form-label">Message</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="message" name="message" placeholder="Input text..." required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="scheduledAt" class="col-sm-2 col-form-label">Scheduled At</label>
        <div class="col-sm-10">
            <input type="datetime-local" class="form-control" id="scheduledAt" name="scheduledAt" required>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Created At</label>
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
        <th>Notification ID</th>
        <th>User ID</th>
        <th>Task ID</th>
        <th>Message</th>
        <th>Scheduled At</th>
        <th>Created At</th>
        <th colspan="2" style="text-align:center;">Action</th>
    </tr>

    <?php { ?>
    <?php while ($row = mysqli_fetch_array($query)){ ?>
        <tr>
            <td><?php echo $row['notification_id']; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['task_id']; ?></td>
            <td><?php echo $row['message']; ?></td>
            <td><?php echo $row['scheduled_at']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a href="editnotifications.php?editnotifications=<?php echo $row["notification_id"]?>" class="btn btn-success" title="Edit">
                    <i class="bi bi-pencil-fill"></i>
                </a>
            </td>
            <td>
                <a href="deletenotifications.php?deletenotifications=<?php echo $row["notification_id"] ?>" class="btn btn-danger" title="Delete">
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