<?php
include "includes/config.php"; 

if(isset($_GET['deleteusers'])) 
{

    $user_id = $_GET["deleteusers"]; 
    
    mysqli_query($conn, "DELETE FROM users WHERE user_id = '$user_id'");

    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputuser.php'</script>"; 
}
?>