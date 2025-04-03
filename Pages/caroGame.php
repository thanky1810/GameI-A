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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAME I&R Online</title>
    <link rel="shortcut icon" href="10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/pGame.css">
    <link rel="stylesheet" href="../assets/css/caro.css">
</head>

<body>
    <!-- Header -->
    <?php
    include "../includes/header.php"
    ?>
    <main>
        <h1>CỜ CARO <span class="stars">⭐⭐⭐⭐⭐</span></h1>
        <div class="buttons">
            <button class="back" id="backButton">
                <<< /button>
                    <button class="btn rule-btn" id="showRulesBtn">Quy tắc</button>
                    <button class="btn play-btn">Đầu hàng</button>
        </div>

        <!-- Thêm phần hiển thị thời gian và kết quả -->
        <div class="game-info">
            <div class="timer">
                <span>Thời gian: </span>
                <span id="gameTimer">00:00</span>
            </div>
            <div class="current-result">
                <span>Kết quả: </span>
                <span id="currentResult">Đang chơi...</span>
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
                <h3>Tiến trình trò chơi</h3>
                <ul>
                    <li>Người chơi nhận được các ký hiệu từ hai lựa chọn có thể: chữ thập và dấu hình tròn.</li>
                    <li>Người chơi đầu tiên đi bất kỳ ô nào của bàn cờ là người chơi có chữ thập.</li>
                    <li>Những nước đi tiếp theo được thực hiện luân phiên bởi người chơi.</li>
                </ul>
                <h3>Chung kết</h3>
                <p>Trò chơi có thể kết thúc trong hai trường hợp: khi năm dấu chữ thập hoặc năm dấu hình tròn được thu thập trong một hàng hoặc khi không còn ô trống trên bàn cờ.</p>
                <p>Người chiến thắng là người đầu tiên xây dựng một hàng gồm năm ký hiệu liên nhau trở lên. Trong trường hợp bàn cờ được lấp kín hoàn toàn và người chơi vẫn chưa xếp được hàng, thì sẽ có kết quả là hòa.</p>
            </div>
        </div>

        <!-- Game Board -->
        <div class="game-container">
            <h2>Cờ Caro - Gomoku Game</h2>
            <table id="table_game">
            </table>
        </div>
    </main>

    <!-- Footer -->
    <?
    include "../includes/footer.php"
    ?>
    <script src="../assets/js/pGame.js"></script>
    <script type="text/javascript" src="../assets/js/caro-main.js"></script>
    <script type="text/javascript" src="../assets/js/contants.js"></script>
</body>

</html>