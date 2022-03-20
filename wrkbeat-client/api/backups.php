<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__ . '/JWT.php');

$token = JWT::getBearerToken();

if ($token == null) {
    // No Token provided
    $requiredTokenResponseCode = 404;
    http_response_code($requiredTokenResponseCode);
    echo json_encode(['message' => 'Token required']);
    return;
}

$jwt = new JWT($token);
$isValid = $jwt->isValid;

if ($isValid == false) {
    $invalidTokenResponseCode = 401;
    http_response_code($invalidTokenResponseCode);
    echo json_encode(['message' => 'Invalid token']);
    return;
}

// All ok, lets make some checks

$postData = $_POST;



// Start all Process
// SQLConnectionCheck
// BackupProcess
// BackupCheck
// CheckSendingMail
// InodesCheck
// DiskusageCheck



if ($postData['inodes_enabled'] == '1') {

    // InodesCheck
}

if ($postData['backup_files_enabled'] == '1') {

    // InodesCheck
}

$checkResults = [];
if (in_array(false, $checkResults)) {
    $responseCode = 503; // Error
} else {
    $responseCode = 200; // Success
}

http_response_code($responseCode);

echo json_encode(["is_valid" => $isValid, 'postData' => $postData]);
