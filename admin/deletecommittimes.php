<?php
include "includes/config.php"; 

// Memeriksa parameter hapus yang dikirim melalui URL
if(isset($_GET['deletecommittimes'])) 
{
    // Ambil nilai primary key
    $commit_time_id = $_GET["deletecommittimes"]; 
    
    // Eksekusi hapus baris data berdasarkan commit_time_id
    mysqli_query($conn, "DELETE FROM commit_times WHERE commit_time_id = '$commit_time_id'");

    // Beri umpan balik lalu alihkan kembali halaman utama
    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputcommittime.php'</script>"; 
}
?>