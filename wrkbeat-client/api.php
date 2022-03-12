<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once('./JWT.php');

$token = JWT::getBearerToken();
if ($token == null) {
    echo json_encode(['message'=>'No token provided']);
} else {

    $jwt = new JWT($token);
    $isValid = $jwt->isValid;
    $infoText = "token is valid: $isValid";


    echo json_encode(["is_valid"=>$isValid, 'postData' => $_POST ]);

}