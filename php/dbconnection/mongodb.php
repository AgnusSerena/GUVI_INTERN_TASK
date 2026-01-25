<?php
$mongoHost = 'localhost';
$mongoPort = 27017;
$mongodbDatabase = "GUVITASK";
$mongodbCollection = 'UserDetails';

$connectionString = "mongodb+srv://GuviTask:Agnus%400912@guvi.rkisxdg.mongodb.net/?appName=guvi";
try {
    $mongoClient = new MongoDB\Driver\Manager($connectionString);
} catch (Exception $e) {
    die("MongoDB Connection Failed: " . $e->getMessage());
}
?>
