<?php
session_start();
if (!isset($_SESSION["user"])) {
    http_response_code(404);
    die("404 Not Found");
}
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../bootstrap.php');

// Lấy chế độ chơi từ URL
$type = isset($_GET['type']) ? $_GET['type'] : 'two_player';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAME I&R Online</title>
    <link rel="shortcut icon" href="10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/pGame.css">
    <link rel="stylesheet" href="../assets/css/caro.css">
    <script src="https://cdn.socket.io/4.5.0/socket.io.min.js"></script>
</head>

<body>
    <!-- Header -->
    <?php include "../includes/header.php" ?>

    <main>
        <h1>CỜ CARO <span class="stars">⭐⭐⭐⭐⭐</span></h1>
        <div class="buttons">
            <button class="back" id="backButton">
                << </button>
                    <button class="btn rule-btn" id="showRulesBtn">Quy tắc</button>
                    <button class="btn play-btn" id="surrenderBtn">Đầu hàng</button>
        </div>

        <div class="game-info">
            <div class="timer">
                <span>Thời gian: </span>
                <span id="gameTimer">00:00</span>
            </div>
            <div class="current-result">
                <span>Kết quả: </span>
                <span id="currentResult">Đang khởi tạo...</span>
            </div>
            <div class="player-turn">
                <span>Lượt: </span>
                <span id="playerTurn">-</span>
            </div>
            <div class="game-mode">
                <span>Chế độ: </span>
                <span id="gameMode"><?php echo $type === 'computer' ? 'Đấu với máy' : 'Hai người chơi'; ?></span>
            </div>
        </div>

        <!-- Bảng Quy tắc Modal -->
        <div class="rules-popup" id="rulesPopup">
            <div class="rules-content">
                <button id="closeRulesBtn" class="close-btn">❌</button>
                <h2>Quy tắc trò cờ ca rô</h2>
                <p>Trò chơi cờ ca rô phổ biến, về mặt logic tương tự như trò chơi bàn cờ có nguồn gốc từ Trung Quốc là Gomoku. Trò chơi được chơi trên một bàn cờ hình vuông, 15x15 ô vuông.</p>
                <h3>Mục tiêu của trò chơi</h3>
                <p>Trở thành người đầu tiên xếp một hàng bằng năm ký hiệu liên nhau (hoặc nhiều hơn) theo bất kỳ hướng nào: theo chiều ngang, chiều dọc, đường chéo.</p>
            </div>
        </div>

        <!-- Game Board -->
        <div class="game-container">
            <h2>Cờ Caro - Gomoku Game</h2>
            <table id="table_game"></table>
        </div>
    </main>

    <!-- Footer -->
    <?php include "../includes/footer.php" ?>

    <script src="../assets/js/contants.js"></script>
    <script src="../assets/js/pGame.js"></script>
    <script src="../assets/js/caro-game.js"></script>
    <script src="../assets/js/caro-ui.js"></script>


</body>

</html>