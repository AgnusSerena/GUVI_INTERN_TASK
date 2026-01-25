<?php
$host = "localhost";
$user = "root";
$pass = "";      // empty password on Mac
$db   = "GuviTask";  

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("MySQL Connection Failed: " . $conn->connect_error);
}
?>
