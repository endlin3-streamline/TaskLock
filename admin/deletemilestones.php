<?php
include "includes/config.php"; 

if(isset($_GET['deletemilestones'])) 
{
    $milestone_id = $_GET["deletemilestones"]; 
    
    $check_sql = "SELECT file_url FROM milestones WHERE milestone_id = '$milestone_id'";
    $check_result = mysqli_query($conn, $check_sql);
    $existing_file = mysqli_fetch_assoc($check_result);
    
    if ($existing_file && $existing_file['file_url'] != '' && $existing_file['file_url'] != 'nofile.png') {
        $path_to_file = 'images/' . $existing_file['file_url'];
        if (file_exists($path_to_file)) {
            unlink($path_to_file);
        }
    }

    mysqli_query($conn, "DELETE FROM milestones WHERE milestone_id = '$milestone_id'");

    echo "<script>alert('DATA BERHASIL DIHAPUS');
    document.location='inputmilestone.php'</script>"; 
}
?>