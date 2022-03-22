<?php

define('__ROOT__', dirname(dirname(__FILE__)));
include(__ROOT__ . '/Autoloader.php');

// $token = JWT::getBearerToken();

// if ($token == null) {
//     // No Token provided
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

// All ok, lets make some checks

$postData = $_POST;

// SQLConnectionCheck
// BackupCheck
// CheckSendingMail

$monitor = new Monitor;
$monitor->StartChecks($_POST);


$checkResults = [];
if (in_array(false, $checkResults)) {
    $responseCode = 503; // Error
} else {
    $responseCode = 200; // Success
}

http_response_code($responseCode);

echo json_encode(["is_valid" => $isValid, 'postData' => $postData]);
