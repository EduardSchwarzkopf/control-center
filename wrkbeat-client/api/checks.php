<?php

define('__ROOT__', dirname(__DIR__));
include(__ROOT__ . '/Autoloader.php');

// $token = JWT::getBearerToken();

// if ($token == null) {
//     $requiredTokenResponseCode = 404;
//     http_response_code($requiredTokenResponseCode);
//     echo json_encode(['message' => 'Token required']);
//     return;
// }

// $jwt = new JWT($token);
// $isValid = $jwt->isValid;

// if ($isValid == false) {
//     $invalidTokenResponseCode = 401;
//     http_response_code($invalidTokenResponseCode);
//     echo json_encode(['message' => 'Invalid token']);
//     return;
// }

$postData = $_POST;

$monitor = new Monitor;
$monitor->StartChecks($_POST);

echo $res;
