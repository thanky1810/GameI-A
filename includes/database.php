<?php
require_once __DIR__ . '/../config.php';

loadEnv(__DIR__ . '/../.env');

$db_server = getenv('DB_SERVER');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);


if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
} 

?>
