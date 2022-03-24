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


$createSQLBackup = false;
if ( array_key_exists('sql_dump', $postData) && $postData['sql_dump'] == true) {
    $createSQLBackup = true;
}

$createFilesBackup = false;

if (array_key_exists('file_dump', $postData)) {

    $exludePatternList = $postData['file_dump'];
    if (is_array($exludePatternList) == false) {
        echo json_encode(['message' => 'file_dump must be an array']);
        return;
    }

    $createFilesBackup = true;
}

if ($createFilesBackup) {
    $response['files_dump'] = $platform->CreateFilesBackup($exludePatternList);
}

if ($createSQLBackup) {
    $response['sql_dump'] = $platform->CreateSQLDump();
}


echo json_encode($response);
