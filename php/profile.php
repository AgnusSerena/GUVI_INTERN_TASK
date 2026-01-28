<?php

include "./dbconnection/mongodb.php";
include "./dbconnection/redis.php";
include "./dbconnection/response.php";


if (isset($_POST["action"]) && $_POST["action"] === "fetch") {

    $redisID = $_POST["redisID"] ?? "";

    if (!$redisID) {
        sendRespose(401, ["error" => "Invalid session"]);
    }


    $session = $redis->get("session:$redisID");

    if (!$session) {
        sendRespose(401, ["error" => "Session expired"]);
    }

    $sessionData = json_decode($session, true);


    sendRespose(200, [
        "email"    => $sessionData["email"]        ?? "",
        "fullname" => $sessionData["fullname"]     ?? "",
        "age"      => $sessionData["age"]          ?? "",
        "phone"    => $sessionData["phone"]        ?? ""
    ]);
}



if (isset($_POST["action"]) && $_POST["action"] === "update") {

    $redisID = $_POST["redisID"] ?? "";
    $email = $_POST["emailid"] ?? "";
    $profileData = json_decode($_POST["profiledata"] ?? "{}", true);

    if (!$redisID || !$email) {
        sendRespose(401, ["error" => "Invalid update request"]);
    }


    $session = $redis->get("session:$redisID");

    if (!$session) {
        sendRespose(401, ["error" => "Session expired"]);
    }

    $sessionArr = json_decode($session, true);


    foreach ($profileData as $key => $value) {
        $sessionArr[$key] = $value;
    }

    $redis->set("session:$redisID", json_encode($sessionArr));


    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ["email" => $email],
        ['$set' => $profileData]
    );

    $mongoClient->executeBulkWrite("$mongodbDatabase.$mongodbCollection", $bulk);

    sendRespose(200, ["status" => true, "message" => "Profile updated"]);
}

?>
