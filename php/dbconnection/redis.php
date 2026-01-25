<?php
$redis = new Redis();

$host = "redis-17170.c278.us-east-1-4.ec2.cloud.redislabs.com";
$port = 17170;
$password = "13BE3LIeqOoT5toh4yvxsPRuJWtlQhm6";   // <-- REPLACE xxx with your actual password

try {
    // Redis Cloud requires TLS/SSL
    $redis->connect($host, $port, 2.5, NULL, 0, 0, ['ssl' => ['verify_peer' => false]]);

    // Authenticate
    $redis->auth(["default", $password]);

} catch (Exception $e) {
    die("Redis Cloud Connection Failed: " . $e->getMessage());
}
?>
