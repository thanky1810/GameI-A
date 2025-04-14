<?php
// Tắt hiển thị lỗi và ghi lỗi vào log
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'D:/App/XAMPP/logs/php_error_log');

// Load thư viện phpdotenv
require_once __DIR__ . '/../vendor/autoload.php';

// Khởi tạo Dotenv và load file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Lấy thông tin từ file .env
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        error_log("Kết nối thất bại: " . $conn->connect_error);
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Lỗi kết nối cơ sở dữ liệu']);
        exit;
    }
} catch (Exception $e) {
    error_log("Lỗi kết nối: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Lỗi kết nối cơ sở dữ liệu']);
    exit;
}
