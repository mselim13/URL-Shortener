<?php
require_once __DIR__ . '/vendor/autoload.php';  // Otomatik yükleme

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "your_secret_key";
$issued_at = time();
$expiration_time = $issued_at + 3600;
$issuer = "localhost";

function create_jwt($user_data) {
    global $secret_key, $issued_at, $expiration_time, $issuer;

    $payload = array(
        "iat" => $issued_at,
        "exp" => $expiration_time,
        "iss" => $issuer,
        "data" => $user_data
    );

    return JWT::encode($payload, $secret_key, 'HS256');
}

function decode_jwt($jwt) {
    global $secret_key;
    try {
        return JWT::decode($jwt, new Key($secret_key, 'HS256'));
    } catch (Exception $e) {
        return null;
    }
}
?>
