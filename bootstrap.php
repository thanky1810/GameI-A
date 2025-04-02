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

        // Bỏ qua dòng trống
        if (empty(trim($line))) continue;

        // Phân tích dòng cấu hình
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Xử lý giá trị có dấu ngoặc kép
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

// Định nghĩa APP_ENV từ file .env
define('APP_ENV', getenv('APP_ENV') ?? 'local');

// Tự động xác định nếu đang chạy trên localhost
if (!defined('IS_LOCAL')) {
    $server_name = $_SERVER['SERVER_NAME'] ?? '';
    define(
        'IS_LOCAL',
        APP_ENV === 'local' ||
            $server_name === 'localhost' ||
            $server_name === '192.168.1.10' ||
            strpos($server_name, '192.168.') === 0
    );
}

// Khởi động session nếu chưa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
