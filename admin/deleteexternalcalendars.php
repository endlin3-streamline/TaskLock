<?php
include "includes/config.php"; 

if(isset($_GET['deleteexternalcalendars'])) 
{
    $calendar_id = $_GET["deleteexternalcalendars"]; 
    mysqli_query($conn, "DELETE FROM external_calendars WHERE calendar_id = '$calendar_id'");
    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputexternalcalendar.php'</script>"; 
}
?>