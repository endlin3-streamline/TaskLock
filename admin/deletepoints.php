<?php
include "includes/config.php"; 
if(isset($_GET['deletepoints'])) 
{
    $point_id = $_GET["deletepoints"]; 
    mysqli_query($conn, "DELETE FROM points WHERE point_id = '$point_id'");
    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputpoint.php'</script>"; 
}
?>