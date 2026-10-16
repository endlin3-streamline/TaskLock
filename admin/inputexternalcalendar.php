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
    <title>INPUT EXTERNAL CALENDAR | Software Developments</title>
</head>

<style>
    body{ margin: 55px; }
    .table{ margin-top: 20px; }
</style>
<body>
<?php
include("includes/config.php");

if(isset($_POST['Save'])){

    // ----- INCREMENT CALENDAR ID -----
    $result_pk = mysqli_query($conn, "SELECT MAX(calendar_id) as last_id FROM external_calendars");
    $row_pk = mysqli_fetch_assoc($result_pk);

    if ($row_pk['last_id'] && strlen($row_pk['last_id']) > 2) {
        $last_number = intval(substr($row_pk['last_id'], 2));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }

    $calendar_id = "EC" . str_pad($new_number, 6, "0", STR_PAD_LEFT);
    $user_id = $_POST['userID'];
    $provider = $_POST['provider'];
    $access_token = $_POST['accessToken'];
    $refresh_token = $_POST['refreshToken'];
    $token_expires_at = $_POST['tokenExpiresAt'];
    $synced_at = date("Y-m-d H:i:s");

    mysqli_query($conn, "INSERT INTO external_calendars
        (calendar_id, user_id, provider, access_token, refresh_token, token_expires_at, synced_at)
        VALUES('$calendar_id', '$user_id', '$provider', '$access_token', '$refresh_token', '$token_expires_at', '$synced_at')
    ");
    header("location:inputexternalcalendar.php");
}

    // ------ DROPDOWN DATA ------
    $datauser = mysqli_query($conn, "SELECT * FROM users");

    // ------ SEARCH FILTER ------
    $query = mysqli_query($conn, "SELECT * FROM external_calendars");

    if(isset($_POST["search"]) && $_POST["search"] != ""){
        $search = $_POST["search"];
        $query = mysqli_query($conn, "SELECT * FROM external_calendars
            WHERE calendar_id LIKE '%$search%'
            OR user_id LIKE '%$search%'
            OR provider LIKE '%$search%'
        ");
    }
?>

<div class="row"></div>
<div class="col-1"></div>
<div class="col-10">

<form method="POST">
    <div class="row mb-3 mt-5">
        <label class="col-sm-2 col-form-label">Calendar ID</label>
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
                    <?php echo $row['user_id']; ?> - <?php echo $row['username']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="provider" class="col-sm-2 col-form-label">Provider</label>
        <div class="col-sm-10">
            <select class="form-control" id="provider" name="provider" required>
                <option value="">Select Provider...</option>
                <option value="Google">Google</option>
                <option value="Outlook">Outlook</option>
                <option value="Apple">Apple</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <label for="accessToken" class="col-sm-2 col-form-label">Access Token</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="accessToken" name="accessToken" placeholder="Input access token..." required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="refreshToken" class="col-sm-2 col-form-label">Refresh Token</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="refreshToken" name="refreshToken" placeholder="Input refresh token..." required>
        </div>
    </div>

    <div class="row mb-3">
        <label for="tokenExpiresAt" class="col-sm-2 col-form-label">Token Expires At</label>
        <div class="col-sm-10">
            <input type="datetime-local" class="form-control" id="tokenExpiresAt" name="tokenExpiresAt" required>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Synced At</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" placeholder="Just now" disabled>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-2"></div>
        <div class="col-10">
            <button type="submit" class="btn btn-success" name="Save">Save</button>
            <button type="reset" class="btn btn-danger">Cancel</button>
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
                placeholder="Cari ID Calendar / User ID...">
        </div>
        <button type="submit" class="col-sm-1 btn btn-primary">Search</button>
    </div>
</form>

<table class="table table-striped table-success">
    <tr class="info">
        <th>Calendar ID</th>
        <th>User ID</th>
        <th>Provider</th>
        <th>Access Token</th>
        <th>Refresh Token</th>
        <th>Expires At</th>
        <th>Synced At</th>
        <th colspan="2" style="text-align:center;">Action</th>
    </tr>

    <?php { ?>
    <?php while ($row = mysqli_fetch_array($query)){ ?>
        <tr>
            <td><?php echo $row['calendar_id']; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['provider']; ?></td>
            <td><?php echo $row['access_token']; ?></td>
            <td><?php echo $row['refresh_token']; ?></td>
            <td><?php echo $row['token_expires_at']; ?></td>
            <td><?php echo $row['synced_at']; ?></td>
            <td>
                <a href="editexternalcalendars.php?editexternalcalendars=<?php echo $row["calendar_id"]?>" class="btn btn-success" title="Edit">
                    <i class="bi bi-pencil-fill"></i>
                </a>
            </td>
            <td>
                <a href="deleteexternalcalendars.php?deleteexternalcalendars=<?php echo $row["calendar_id"] ?>" class="btn btn-danger" title="Delete">
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