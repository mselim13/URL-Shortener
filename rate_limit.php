<?php
// Sadece oturum başlatılmadıysa başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Belirtilen süre içinde belirtilen sayıda isteği sınırlar.
 *
 * @param int $limit          Maksimum istek sayısı (örn: 10)
 * @param int $window_seconds Zaman penceresi saniye cinsinden (örn: 60 saniye)
 * @return bool               Limit aşıldıysa true döner
 */
function is_rate_limited($limit = 10, $window_seconds = 60) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $now = time();

    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }

    if (!isset($_SESSION['rate_limit'][$ip])) {
        $_SESSION['rate_limit'][$ip] = [];
    }

    $_SESSION['rate_limit'][$ip] = array_filter(
        $_SESSION['rate_limit'][$ip],
        fn($timestamp) => ($now - $timestamp) < $window_seconds
    );

    if (count($_SESSION['rate_limit'][$ip]) >= $limit) {
        return true;
    }

    $_SESSION['rate_limit'][$ip][] = $now;
    return false;
}
?>
