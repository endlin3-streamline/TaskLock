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
    <title>INPUT SETTINGS | Software Developments</title>
</head>

<style>
    body{ margin: 55px; }
    .table{ margin-top: 20px; }
</style>
<body>
<?php
include("includes/config.php");

if(isset($_POST['Save'])){

    // ----- INCREMENT SETTING ID -----
    $result_pk = mysqli_query($conn, "SELECT MAX(setting_id) as last_st FROM settings");
    $row_pk = mysqli_fetch_assoc($result_pk);

    if ($row_pk['last_st'] && strlen($row_pk['last_st']) > 2) {
        $last_number = intval(substr($row_pk['last_st'], 2));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }

    $setting_id = "ST" . str_pad($new_number, 4, "0", STR_PAD_LEFT);
    $user_id = $_POST['userID'];
    // checkboxes return nothing if unchecked, so default to 0
    $alarm_enabled = isset($_POST['alarmEnabled']) ? 1 : 0;
    $notifications_enabled = isset($_POST['notificationsEnabled']) ? 1 : 0;
    $updated_at = date("Y-m-d H:i:s");

    mysqli_query($conn, "INSERT INTO settings
        (setting_id, user_id, alarm_enabled, notifications_enabled, updated_at)
        VALUES('$setting_id', '$user_id', '$alarm_enabled', '$notifications_enabled', '$updated_at')
    ");
    header("location:inputsettings.php");
}

    // ------ DROPDOWN DATA ------
    $datauser = mysqli_query($conn, "SELECT * FROM users");

    // ------ SEARCH FILTER ------
    $query = mysqli_query($conn, "SELECT * FROM settings");

    if(isset($_POST["search"]) && $_POST["search"] != ""){
        $search = $_POST["search"];
        $query = mysqli_query($conn, "SELECT * FROM settings
            WHERE setting_id LIKE '%$search%'
            OR user_id LIKE '%$search%'
        ");
    }
?>

<div class="row"></div>
<div class="col-1"></div>
<div class="col-10">

<form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Setting ID</label>
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
        <label class="col-sm-2 col-form-label">Alarm Enabled</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="checkbox" id="alarmEnabled" name="alarmEnabled" value="1" style="width:20px; height:20px;">
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Notifications Enabled</label>
        <div class="col-sm-10 d-flex align-items-center">
            <input type="checkbox" id="notificationsEnabled" name="notificationsEnabled" value="1" style="width:20px; height:20px;">
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Updated At</label>
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
        <th>Setting ID</th>
        <th>User ID</th>
        <th>Alarm Enabled</th>
        <th>Notifications Enabled</th>
        <th>Updated At</th>
        <th colspan="2" style="text-align:center;">Action</th>
    </tr>

    <?php { ?>
    <?php while ($row = mysqli_fetch_array($query)){ ?>
        <tr>
            <td><?php echo $row['setting_id']; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['alarm_enabled'] ? 'Yes' : 'No'; ?></td>
            <td><?php echo $row['notifications_enabled'] ? 'Yes' : 'No'; ?></td>
            <td><?php echo $row['updated_at']; ?></td>
            <td>
                <a href="editsettings.php?editsettings=<?php echo $row["setting_id"]?>" class="btn btn-success" title="Edit">
                    <i class="bi bi-pencil-fill"></i>
                </a>
            </td>
            <td>
                <a href="deletesettings.php?deletesettings=<?php echo $row["setting_id"] ?>" class="btn btn-danger" title="Delete">
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