<?php
session_start();
if (!isset($_SESSION["user"])) {
    http_response_code(404);
    die("404 Not Found");
}
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../bootstrap.php');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta​​ charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cờ Caro</title>
        <link rel="stylesheet" href="/Project/assets/css/caro.css">
</head>

<body>
    <h1>CỜ CARO<span class="stars">⭐⭐⭐⭐⭐</span></h1>
    <div class="game-controls">
        <button id="back-button">Quay lại</button>
        <button id="surrender-button">Đầu hàng</button>
    </div>
    <div class="game-info">
        <div id="timer">Thời gian: 00:00</div>
        <div id="message">Kết cục: Đang chơi...</div>
    </div>
    <table id="table_game"></table>

    <script src="/Project/assets/js/caro-game.js"></script>
    <script src="/Project/assets/js/caro-ui.js"></script>
    <script>
        // Logic đếm thời gian
        let seconds = 0;
        const timerElement = document.getElementById('timer');
        setInterval(() => {
            seconds++;
            const minutes = Math.floor(seconds / 60).toString().padStart(2, '0');
            const secs = (seconds % 60).toString().padStart(2, '0');
            timerElement.textContent = `Thời gian: ${minutes}:${secs}`;
        }, 1000);

        // Xử lý nút "Quay lại"
        document.getElementById('back-button').addEventListener('click', () => {
            window.location.href = '/Project/index.php'; // Điều hướng về trang chủ hoặc trang trước đó
        });
    </script>
</body>

</html>