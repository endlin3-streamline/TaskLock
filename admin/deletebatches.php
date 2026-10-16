<?php
include "includes/config.php"; 

if(isset($_GET['deletebatches'])) 
{

    $batch_id = $_GET["deletebatches"]; 
    

    mysqli_query($conn, "DELETE FROM batches WHERE batch_id = '$batch_id'");


    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputbatch.php'</script>"; 
}
?>