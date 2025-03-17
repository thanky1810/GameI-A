<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "game-a";

// Kết nối tới MySQL
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
} 
?>