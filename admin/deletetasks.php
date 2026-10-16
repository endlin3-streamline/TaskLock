<?php
include "includes/config.php"; 
    
if(isset($_GET['deletetasks'])) 
{
    $task_id = $_GET["deletetasks"]; 
    
    mysqli_query($conn, "DELETE FROM tasks WHERE task_id = '$task_id'");

    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputtask.php'</script>"; 
}
?>