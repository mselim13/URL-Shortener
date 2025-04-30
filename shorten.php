<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $longUrl = $_POST['longUrl'] ?? '';

    if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
        echo json_encode(["error" => "Geçersiz URL."]);
        exit;
    }

    $code = substr(md5($longUrl . time()), 0, 6);
    $shortUrl = "http://localhost/url-shortener/" . $code;

    $stmt = $conn->prepare("INSERT INTO urls (code, long_url) VALUES (?, ?)");
    if ($stmt->execute([$code, $longUrl])) {
        echo json_encode(["shortUrl" => $shortUrl]);
    } else {
        echo json_encode(["error" => "Veritabanına kayıt yapılırken bir hata oluştu."]);
    }
} else {
    echo json_encode(["error" => "Geçersiz istek."]);
}
?>
