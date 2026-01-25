<?php
include "./dbconnection/mysql.php";
include "./dbconnection/mongodb.php";
include "./dbconnection/redis.php";
include "./dbconnection/response.php";


// --------------------------------------------------
// Helper function: Fetch user from MongoDB
// --------------------------------------------------
function getUserDetails($mongodbId) {
    global $mongoClient, $mongodbDatabase, $mongodbCollection;

    $_id = new MongoDB\BSON\ObjectID($mongodbId);
    $filter = ['_id' => $_id];

    $query = new MongoDB\Driver\Query($filter, []);
    $cursor = $mongoClient->executeQuery("$mongodbDatabase.$mongodbCollection", $query);

    return current($cursor->toArray());
}


// --------------------------------------------------
// Create New User
// --------------------------------------------------
function CreateUser($email, $password, $insertStmt, $data) {
    global $mongoClient, $mongodbDatabase, $mongodbCollection, $redis;

    // Insert in MongoDB
    $bulk = new MongoDB\Driver\BulkWrite;
    $_id = $bulk->insert($data);

    $mongoClient->executeBulkWrite("$mongodbDatabase.$mongodbCollection", $bulk);
    $mongoDbId = (string)$_id;

    // Hash password & insert into MySQL
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($insertStmt, "sss", $email, $hashedPassword, $mongoDbId);

    if (!mysqli_stmt_execute($insertStmt)) {
        sendRespose(500, ["status" => "error", "message" => "Database insert failed"]);
    }

    // Create session
    $session_id = uniqid();
    $userDetails = getUserDetails($mongoDbId);

    $sessionData = [
        "userDetails" => $userDetails
    ];

    $redis->set("session:$session_id", json_encode($sessionData));
    $redis->expire("session:$session_id", 600);  // 10 minutes

    // FINAL SUCCESS RESPONSE
    sendRespose(200, [
        "status" => "success",
        "message" => "User Registered Successfully",
        "session_id" => $session_id,
        "data" => [
            "emailid" => $email,
            "mongoDbId" => $mongoDbId
        ]
    ]);
}


// --------------------------------------------------
// Handle Signup Request
// --------------------------------------------------
if (isset($_POST['input2']) && isset($_POST['input3'])) {

    $password = trim($_POST['input2']);
    $email = trim($_POST['input3']);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Email exists → return proper JSON
    if ($result->num_rows > 0) {
        sendRespose(409, [
            "status" => "error",
            "message" => "Email already exists"
        ]);
    }

    // Insert new user
    $insertSql = "INSERT INTO users (email, userpswd, mongodbId) VALUES (?, ?, ?)";
    $insertStmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($insertStmt, $insertSql)) {
        sendRespose(500, ["status" => "error", "message" => "SQL Prepare Failed"]);
    }

    // MongoDB initial data
    $mongoData = ["email" => $email];

    CreateUser($email, $password, $insertStmt, $mongoData);

    $stmt->close();
    $conn->close();
}

// --------------------------------------------------
// UPDATE USER (from profile page)
// --------------------------------------------------
if (isset($_POST["action"]) && $_POST["action"] == "update") {

    $data = $_POST['profiledata'];
    $email = $_POST['emailid'];
    $redisId = $_POST['redisID'];

    UpdateUser($email, $data, $redisId);
}

?>
