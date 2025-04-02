<?php
function logining($redirectPage)
{
    if (!isset($_SESSION["user"])) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/Project/Pages/login.php';
        if (!file_exists($path)) {
            die("File not found: " . $path); // Hiển thị đường dẫn thực tế
        }
        return $path;
    }
    return $redirectPage;
}
