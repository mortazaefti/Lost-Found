<?php
// database connection settings
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "lost_found";

// connect to mysql
$conn = new mysqli($servername, $username, $password, $dbname);

// stop if connection fails
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
