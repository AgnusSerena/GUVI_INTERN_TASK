<?php
include "./dbconnection/mysql.php";
include "./dbconnection/mongodb.php";
include "./dbconnection/redis.php";
include "./dbconnection/response.php";

if (isset($_POST["input1"]) && isset($_POST["input2"])) {

    $email = trim($_POST["input1"]);
    $password = trim($_POST["input2"]);

    // Fetch user from MySQL
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // No user found
    if ($result->num_rows == 0) {
        sendRespose(200, [
            "status" => false,
            "message" => "No user found"
        ]);
    }

    $row = $result->fetch_assoc();

    // Password incorrect
    if (!password_verify($password, $row["userpswd"])) {
        sendRespose(200, [
            "status" => false,
            "message" => "Incorrect password"
        ]);
    }

    // Fetch full user data from MongoDB
    $mongoId = $row["mongodbId"];

    try {
        $_id = new MongoDB\BSON\ObjectID($mongoId);
    } catch (Exception $e) {
        sendRespose(200, [
            "status" => false,
            "message" => "Invalid MongoDB ID format"
        ]);
    }

    $query = new MongoDB\Driver\Query(["_id" => $_id]);
    $cursor = $mongoClient->executeQuery("$mongodbDatabase.$mongodbCollection", $query);

    // Convert cursor to array safely
    $mongoArray = $cursor->toArray();

    if (empty($mongoArray)) {
        sendRespose(200, [
            "status" => false,
            "message" => "MongoDB user data not found"
        ]);
    }

    $userData = $mongoArray[0];

    // Create Redis session
    $session_id = uniqid("sess_", true);

    try {
        $redis->set("session:$session_id", json_encode($userData));
        $redis->expire("session:$session_id", 600); // expires in 10 minutes
    } catch (Exception $e) {
        sendRespose(200, [
            "status" => false,
            "message" => "Redis error: " . $e->getMessage()
        ]);
    }

    // Final response
    sendRespose(200, [
        "status" => true,
        "message" => "Login Success",
        "session_id" => $session_id,
        "data" => [
            "emailid" => $email,
            "mongoDbId" => $mongoId
        ]
    ]);
}
?>
