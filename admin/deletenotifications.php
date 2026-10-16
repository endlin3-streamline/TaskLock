<?php
include "includes/config.php"; 

if(isset($_GET['deletenotifications'])) 
{
    $notification_id = $_GET["deletenotifications"]; 
    mysqli_query($conn, "DELETE FROM notifications WHERE notification_id = '$notification_id'");
    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputnotif.php'</script>"; 
}
?>