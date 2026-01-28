<?php
$host = "localhost";
$user = "root";
$pass = "";   
$db   = "GuviTask";  

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("MySQL Connection Failed: " . $conn->connect_error);
}
?>
