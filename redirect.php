<?php
require 'db.php';

function getUserIP() {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Bilinmiyor';
}

function getCountryFromIP($ip) {
    // Basit çözüm: ip-api.com'dan ülke çek
    $details = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country");
    if ($details) {
        $json = json_decode($details, true);
        return $json['country'] ?? 'Bilinmiyor';
    }
    return 'Bilinmiyor';
}

if (isset($_GET['code'])) {
    $shortCode = $_GET['code'];

    $stmt = $conn->prepare("SELECT long_url FROM urls WHERE code = :shortCode");
    $stmt->bindParam(':shortCode', $shortCode);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kullanıcı bilgilerini al
        $ip = getUserIP();
        $userAgent = getUserAgent();
        $country = getCountryFromIP($ip);

        // Analytics tablosuna ekle
        $log = $conn->prepare("INSERT INTO analytics (code, ip_address, country, user_agent) VALUES (?, ?, ?, ?)");
        $log->execute([$shortCode, $ip, $country, $userAgent]);

        // Yönlendir
        header("Location: " . $row['long_url']);
        exit;
    } else {
        echo "Kısa URL geçersiz.";
    }
} else {
    echo "Geçersiz kısa URL.";
}
require_once 'cache.php';
require_once 'db.php'; // URL'leri çektiğin yer

$short_code = $_GET['c'] ?? null;

if (!$short_code) {
    die("Kod belirtilmedi.");
}

// 1. Önce cache'e bak
$long_url = get_from_cache($short_code);

if (!$long_url) {
    // 2. Yoksa veritabanından çek
    $stmt = $pdo->prepare("SELECT long_url FROM urls WHERE short_code = ?");
    $stmt->execute([$short_code]);
    $row = $stmt->fetch();

    if (!$row) {
        die("URL bulunamadı.");
    }

    $long_url = $row['long_url'];

    // 3. Cache'e ekle
    set_to_cache($short_code, $long_url, 120); // 2 dakika cache'le
}

// 4. Yönlendir
header("Location: $long_url");
exit;
