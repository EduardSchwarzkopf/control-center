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

// Example:
$postData = [
    'platform' => 'wordpress',
    'sql_dump' => true,
    'file_dump' => []
];

$platformField = $postData['platform'];

if ($platformField == null) {
    echo json_encode(['message' => 'platform field required']);
    return;
}

if (is_string($platformField) == false) {
    echo json_encode(['message' => 'platform field must be a string']);
    return;
}

$platformName = ucfirst($platformField) . 'Platform';
try {

    $platform = new $platformName;
} catch (Exception $e) {
    echo json_encode(['message' => "platform $platformField not found"]);
    return;
}

$platform->CreateJSONResponse();

$createSQLBackup = $postData['sql_dump'] == true ? true : false;

$createFilesBackup = false;
$exludePatternList = $postData['file_dump'];
if (array_key_exists('file_dump', $postData) && is_array($exludePatternList) == false) {
    echo json_encode(['message' => 'file_dump must be an array']);
    return;
} else {
    $createFilesBackup = true;
}

if ($createFilesBackup) {
    $backupResult = $platform->CreateFilesBackup($exludePatternList);
}

if ($createSQLBackup) {
    $sqlResult = $platform->CreateSQLDump();
}



echo ApiResponse::CreateResponse($platform);
