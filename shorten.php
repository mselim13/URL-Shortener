<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'rate_limit.php';
if (is_rate_limited()) {
    http_response_code(429);
    echo json_encode(["error" => "Çok fazla istek attınız. Lütfen daha sonra tekrar deneyin."]);
    exit;
}

require 'db.php';

function generate_hash($url) {
    return substr(md5($url . time()), 0, 6);
}

$nodes = ['node1', 'node2', 'node3'];

function get_cache_node($short_code, $nodes) {
    $hash = crc32($short_code);
    return $nodes[$hash % count($nodes)];
}

function set_distributed_cache($short_code, $long_url) {
    global $nodes;
    $node = get_cache_node($short_code, $nodes);
    $_SESSION['distributed_cache'][$node][$short_code] = [
        'value' => $long_url,
        'expire' => time() + 120
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $longUrl = $_POST['longUrl'] ?? '';

    if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
        echo json_encode(["error" => "Geçersiz URL."]);
        exit;
    }

    $code = generate_hash($longUrl);
    $shortUrl = "http://localhost/url-shortener/redirect.php?code=" . $code;

    $stmt = $conn->prepare("INSERT INTO urls (code, long_url) VALUES (?, ?)");
    if ($stmt->execute([$code, $longUrl])) {
        set_distributed_cache($code, $longUrl);

        // 🎯 Yeni veriyle birlikte geri dön
        echo json_encode([
            "shortUrl" => $shortUrl,
            "code" => $code,
            "longUrl" => $longUrl,
            "created_at" => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode(["error" => "Veritabanına kayıt yapılırken bir hata oluştu."]);
    }
} else {
    echo json_encode(["error" => "Geçersiz istek."]);
}
