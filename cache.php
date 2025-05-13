<?php
session_start();

function get_from_cache($key) {
    if (isset($_SESSION['cache'][$key]) && $_SESSION['cache'][$key]['expire'] > time()) {
        return $_SESSION['cache'][$key]['value'];
    }
    return null;
}

function set_to_cache($key, $value, $ttl = 60) {
    $_SESSION['cache'][$key] = [
        'value' => $value,
        'expire' => time() + $ttl
    ];
}
