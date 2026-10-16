<?php
include "includes/config.php"; 

if(isset($_GET['deletesettings'])) 
{
    $setting_id = $_GET["deletesettings"]; 
    mysqli_query($conn, "DELETE FROM settings WHERE setting_id = '$setting_id'");
    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputsettings.php'</script>"; 
}
?>