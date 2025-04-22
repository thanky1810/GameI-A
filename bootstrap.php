<?php
// Load environment variables
function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new RuntimeException('.env file not found');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (empty(trim($line))) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load .env file
loadEnv(__DIR__ . '/.env');

// Hàm lấy địa chỉ IP của máy chủ
function getServerIP()
{
    $ip = '127.0.0.1'; // Mặc định
    if (!empty($_SERVER['SERVER_ADDR'])) {
        $ip = $_SERVER['SERVER_ADDR'];
    } elseif (!empty($_SERVER['LOCAL_ADDR'])) {
        $ip = $_SERVER['LOCAL_ADDR'];
    } else {
        $ip = gethostbyname(gethostname());
    }
    return $ip;
}

// Định nghĩa BASE_URL và WEBSOCKET_URL động
$serverIP = getServerIP();
define('BASE_URL', 'http://' . $serverIP . '/Project/');
define('WEBSOCKET_URL', 'ws://' . $serverIP . ':8080');

// Tự động xác định nếu đang chạy trên localhost
if (!defined('IS_LOCAL')) {
    $server_name = $_SERVER['SERVER_NAME'] ?? '';
    $server_addr = $_SERVER['SERVER_ADDR'] ?? '';
    define(
        'IS_LOCAL',
        $server_name === 'localhost' ||
            $server_name === '192.168.1.7' ||
            strpos($server_name, '192.168.') === 0 ||
            strpos($server_addr, '192.168.') === 0
    );
}

// Khởi động session nếu chưa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
