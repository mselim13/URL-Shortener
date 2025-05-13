<?php

// AES ile şifreleme fonksiyonu
function encrypt_url($url, $key) {
    $method = "aes-256-cbc";  // AES-256 CBC modunu kullanıyoruz
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method)); // IV rastgele oluşturuluyor

    // Şifreleme işlemi
    $encrypted_url = openssl_encrypt($url, $method, $key, 0, $iv);
    // IV ve şifreli veriyi base64 encode ediyoruz
    return base64_encode($encrypted_url . '::' . $iv);
}

// AES ile şifre çözme fonksiyonu
function decrypt_url($encrypted_url, $key) {
    $method = "aes-256-cbc";
    list($encrypted_data, $iv) = explode('::', base64_decode($encrypted_url), 2);  // IV ve şifreyi ayırıyoruz
    // Şifreyi çözme işlemi
    return openssl_decrypt($encrypted_data, $method, $key, 0, $iv);
}

?>
