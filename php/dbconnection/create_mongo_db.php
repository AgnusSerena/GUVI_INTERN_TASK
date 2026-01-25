<?php
require "php/dbconnection/mongodb.php";

// Create insertion
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert([
    "init" => "MongoDB created automatically bro!"
]);

// Execute insert → database + collection gets created
$mongoClient->executeBulkWrite("$mongodbDatabase.$mongodbCollection", $bulk);

echo "MongoDB Database & Collection Created Successfully!";
?>
