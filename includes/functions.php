<?php
function getProjectBasePath()
{
    // Lấy từ biến môi trường nếu đã thiết lập
    $baseUrl = getenv('BASE_URL');
    if (!empty($baseUrl)) {
        return rtrim($baseUrl, '/');
    }

    // Xác định tự động dựa trên cấu trúc server
    $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
    $scriptName = $_SERVER['SCRIPT_NAME'];

    // Check if the project name is already in DOCUMENT_ROOT
    if (strpos($docRoot, 'Project') !== false) {
        // Local environment - Project is part of DOCUMENT_ROOT
        return '';  // Empty base path for relative URLs
    } else {
        // Server environment - Project is a subfolder
        if (strpos($scriptName, '/Project/') !== false) {
            return '/Project';
        }

        // Try to detect project folder from script path
        $scriptParts = explode('/', $scriptName);
        if (count($scriptParts) > 1) {
            return '/' . $scriptParts[1];  // Return the first directory in path
        }

        return '';  // Default empty if we can't determine
    }
}


function getCurrentPathDepth()
{
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $parts = explode('/', trim($scriptName, '/'));

    // Nếu DOCUMENT_ROOT đã chứa Project (môi trường local)
    if (strpos($_SERVER['DOCUMENT_ROOT'], 'Project') !== false) {
        return count($parts);
    } else {
        // Bỏ qua phần tử đầu tiên (tên dự án) trong môi trường server
        return count($parts) - 1;
    }
}


function getRelativePathToRoot()
{
    $depth = getCurrentPathDepth();
    if ($depth <= 1) {
        return './';
    }

    return str_repeat('../', $depth - 1);
}

function asset($path)
{
    // Loại bỏ dấu / ở đầu path nếu có
    $path = ltrim($path, '/');

    $basePath = getProjectBasePath();
    if (!empty($basePath)) {
        // Sử dụng đường dẫn tuyệt đối cho server
        return "{$basePath}/assets/{$path}";
    } else {
        // Sử dụng đường dẫn tương đối cho local
        $relativePath = getRelativePathToRoot();
        return "{$relativePath}assets/{$path}";
    }
}


function getCorrectUrl($path)
{
    // Loại bỏ dấu / ở đầu path nếu có
    $path = ltrim($path, '/');

    $basePath = getProjectBasePath();
    if (!empty($basePath)) {
        // Sử dụng đường dẫn tuyệt đối cho server
        return "{$basePath}/{$path}";
    } else {
        // Sử dụng đường dẫn tương đối cho local
        $relativePath = getRelativePathToRoot();
        return "{$relativePath}{$path}";
    }
}

function logining($redirectPage)
{
    if (!isset($_SESSION["user"])) {
        return getCorrectUrl('Pages/login.php');
    }
    return $redirectPage;
}

/**
 * Hàm debug hiển thị thông tin đường dẫn
 */
// function debugPaths()
// {
//     echo "<div style='background:#f8f9fa;padding:10px;margin:10px;border-radius:5px;'>";
//     echo "<h3>Debug Path Information</h3>";
//     echo "<pre>";
//     echo "PROJECT_BASE_PATH: " . getProjectBasePath() . "\n";
//     echo "CURRENT_PATH_DEPTH: " . getCurrentPathDepth() . "\n";
//     echo "RELATIVE_PATH_TO_ROOT: " . getRelativePathToRoot() . "\n";
//     echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'not set') . "\n";
//     echo "SERVER_ADDR: " . ($_SERVER['SERVER_ADDR'] ?? 'not set') . "\n";
//     echo "APP_ENV: " . getenv('APP_ENV') . "\n";
//     echo "BASE_URL from .env: " . getenv('BASE_URL') . "\n";
//     echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
//     echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
//     echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
//     echo "Example asset path: " . asset('css/main.css') . "\n";
//     echo "Example URL path: " . getCorrectUrl('Pages/caro.php') . "\n";
//     echo "</pre>";
//     echo "</div>";
// }
