<?php
require_once 'encryption.php';
require_once 'auth.php';

// Şifreleme testi
$key = "my_secret_key";  // Gerçek projede .env veya güvenli bir yerde tut
$url = "https://example.com";
$encrypted_url = encrypt_url($url, $key);
echo "Encrypted URL: " . $encrypted_url . "<br>";

$decrypted_url = decrypt_url($encrypted_url, $key);
echo "Decrypted URL: " . $decrypted_url . "<br>";

// JWT test
$user_data = array("user_id" => 1, "username" => "testuser");
$jwt = create_jwt($user_data);
echo "Generated JWT: " . $jwt . "<br>";

$decoded_data = decode_jwt($jwt);
if ($decoded_data) {
    echo "User ID: " . $decoded_data->data->user_id . "<br>";
    echo "Username: " . $decoded_data->data->username . "<br>";
} else {
    echo "Invalid token!<br>";
}
?>
