<?php
define('__ROOT__', dirname(__FILE__, 2));
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

$platform = new WordpressPlatform();
// $sqlResult = $platform->CreateSQLDump();
$backupResult = $platform->CreateFilesBackup();

$a = 1;
