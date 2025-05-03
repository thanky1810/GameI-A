<?php
// Tắt hiển thị lỗi và ghi lỗi vào log
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'D:/App/XAMPP/logs/php_error_log');

// Kiểm tra autoload.php
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    error_log("File autoload.php không tồn tại. Chạy 'composer install' để cài đặt thư viện.");
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Thư viện Composer chưa được cài đặt']);
    exit;
}

// Load thư viện phpdotenv
require_once __DIR__ . '/../vendor/autoload.php';

// Kiểm tra file .env
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    error_log("File .env không tồn tại tại: $envPath");
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'File .env không tồn tại']);
    exit;
}

// Khởi tạo Dotenv và load file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Kiểm tra các biến môi trường
if (!isset($_ENV['DB_SERVER']) || !isset($_ENV['DB_NAME']) || !isset($_ENV['DB_USER']) || !isset($_ENV['DB_PASS'])) {
    error_log("Một hoặc nhiều biến môi trường (DB_SERVER, DB_NAME, DB_USER, DB_PASS) không được định nghĩa trong file .env");
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Biến môi trường không được định nghĩa']);
    exit;
}

// Lấy thông tin từ file .env
$host = $_ENV['DB_SERVER'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

// Log thông tin kết nối
error_log("DB_SERVER: $host, DB_NAME: $dbname, DB_USER: $username, DB_PASS: $password");

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
