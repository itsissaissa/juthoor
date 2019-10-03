<?php
// Create connection
$conn = new mysqli($dbhostname, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

mysqli_query($conn, "SET NAMES 'utf8'");
mysqli_query($conn, "SET NAMES 'utf8'");
mysqli_query($conn, "SET SQL_BIG_SELECTS=1");
?>