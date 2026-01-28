<?php
include "./dbconnection/mysql.php";
include "./dbconnection/mongodb.php";
include "./dbconnection/redis.php";
include "./dbconnection/response.php";

// --------------------------------------------------
// Helper: Fetch Mongo User
// --------------------------------------------------
function getUserDetails($mongodbId) {
    global $mongoClient, $mongodbDatabase, $mongodbCollection;

    $_id = new MongoDB\BSON\ObjectID($mongodbId);
    $query = new MongoDB\Driver\Query(['_id' => $_id]);
    $cursor = $mongoClient->executeQuery("$mongodbDatabase.$mongodbCollection", $query);
    return current($cursor->toArray());
}

// --------------------------------------------------
// Create User
// --------------------------------------------------
function CreateUser($email, $password, $fullname) {

    global $conn, $mongoClient, $mongodbDatabase, $mongodbCollection, $redis;

    // -----------------------
    // Insert into MongoDB
    // -----------------------
    $bulk = new MongoDB\Driver\BulkWrite;
    $mongoId = new MongoDB\BSON\ObjectID;

    $bulk->insert([
        "_id"      => $mongoId,
        "email"    => $email,
        "fullname" => $fullname,
        "age"      => "",
        "phone"    => ""
    ]);

    $mongoClient->executeBulkWrite("$mongodbDatabase.$mongodbCollection", $bulk);

    // -----------------------
    // Insert into MySQL
    // -----------------------
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (email, userpswd, mongodbId) VALUES (?, ?, ?)");
    $mid = (string)$mongoId;
    $stmt->bind_param("sss", $email, $hashed, $mid);
    $stmt->execute();

    // -----------------------
    // Create Redis Session
    // -----------------------
    $session_id = uniqid("sess_", true);

    $userDetails = [
        "email"    => $email,
        "fullname" => $fullname,
        "age"      => "",
        "phone"    => ""
    ];

    $redis->set("session:$session_id", json_encode($userDetails));
    $redis->expire("session:$session_id", 600);

    sendRespose(200, [
        "status"     => true,
        "message"    => "Signup Successful",
        "session_id" => $session_id,
        "data" => [
            "emailid"    => $email,
            "mongoDbId"  => (string)$mongoId
        ]
    ]);
}

// --------------------------------------------------
// Handle Signup Request
// --------------------------------------------------
if (isset($_POST["fullname"]) && isset($_POST["email"]) && isset($_POST["password"])) {

    $fullname = trim($_POST["fullname"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check duplicate email
    $stmt = $conn->prepare("SELECT email FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $exists = $stmt->get_result();

    if ($exists->num_rows > 0) {
        sendRespose(409, [
            "status" => false,
            "message" => "Email already exists"
        ]);
    }

    // Create User
    CreateUser($email, $password, $fullname);
}
?>
