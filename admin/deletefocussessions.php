<?php
include "includes/config.php"; 

if(isset($_GET['deletefocussessions'])) 
{
    $focus_sessions_id = $_GET["deletefocussessions"]; 
    mysqli_query($conn, "DELETE FROM focus_sessions WHERE focus_sessions_id = '$focus_sessions_id'");
    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputfocussession.php'</script>"; 
}
?>