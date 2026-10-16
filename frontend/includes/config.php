<?php 
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "sofdev";

	//buat koneksi baru
	$conn = new mysqli($servername, $username, $password, $dbname);

	//memeriksa jika koneksi error
	if ($conn->connect_error){
		die("connection failed: " . $conn->connect_error);
	}
 ?>