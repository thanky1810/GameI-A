<?php

/**
 * File functions.php - Các hàm tiện ích cho ứng dụng
 */

// Hàm kiểm tra đăng nhập và điều hướng URL
function logining($url)
{
    // Nếu chưa đăng nhập và không phải trang đăng nhập, chuyển hướng đến trang đăng nhập
    if (!isset($_SESSION['user_id']) && strpos($url, 'login.php') === false) {
        return getCorrectUrl('Pages/login.php') . '?redirect=' . urlencode($url);
    }

    return $url;
}

// Hàm xác định đường dẫn dựa vào môi trường
function getCorrectUrl($path)
{
    // Loại bỏ dấu / ở đầu và cuối nếu có
    $path = trim($path, '/');

    // Xác định môi trường từ biến APP_ENV
    $isLocal = (defined('APP_ENV') && APP_ENV === 'local');

    if ($isLocal) {
        // Trong môi trường local, sử dụng đường dẫn tương đối
        return '../' . $path;
    } else {
        // Trong môi trường server, sử dụng đường dẫn tuyệt đối
        return '/Project/' . $path;
    }
}

// Hàm tạo đường dẫn tới tài nguyên tĩnh (assets)
function asset($path)
{
    // Loại bỏ dấu / ở đầu nếu có
    $path = ltrim($path, '/');

    return getCorrectUrl($path);
}
