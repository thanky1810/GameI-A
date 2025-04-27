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
    <link rel="stylesheet" href="<?= asset('css/minGame.css') ?>">
</head>

<body>
    <!-- Header -->
    <?php
    include "../includes/header.php";
    ?>
    <main>
        <h1>Dò mìn<span class="stars">⭐⭐⭐⭐⭐</span></h1>
        <div class="buttons">
            <button class="back" id="backButton">
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
                <h2>Quy tắc trò chơi Dò Mìn</h2>
                <p>Dò Mìn là một trong những trò chơi máy tính nổi tiếng nhất trong thể loại trí não giải đố.</p>
                <p>Kích thước của sân chơi thay đổi tùy thuộc vào độ khó đã chọn:</p>
                <ul>
                    <li>“Người mới bắt đầu” với sân chơi 10x10 ô;</li>
                    <li>“Nghiệp dư” với sân chơi 15x15 ô;</li>
                    <li>“Chuyên nghiệp” với sân chơi 20x20 ô.</li>
                </ul>
                delete <h3>Mục tiêu của trò chơi</h3>
                <p>Mở tất cả các ô không chứa mìn.</p>
                <h3>Tiến trình trận đấu</h3>
                <ul>
                    <li>Trên sân chơi chứa ba loại ô khác nhau: ô trống, “ô có gài mìn” và ô có chỉ dẫn số;</li>
                    <li>Mỗi chữ số tương ứng với số lượng mìn trong các ô tại những khu vực lân cận. Do đó, người chơi sẽ có thể xác định vị trí các ô trống và ô đã được gài mìn;</li>
                    <li>Số lượng mìn được chỉ ra trong cửa sổ bên cạnh sân chơi. Chúng có thể có 11, 29 hoặc 51;</li>
                    <li>Các quả mìn chỉ được phân phối đến ô sau nước đi đầu tiên, vì vậy không thể nào bị thua ngay sau nước đầu tiên;</li>
                    <li>Trong trường hợp khi có ô trống bên cạnh ô đã mở, thì ô đó sẽ tự động mở ra;</li>
                    <li>Để cho quá trình chơi thoải mái hơn, các ô “đã gài mìn” có thể được đánh dấu bằng những lá cờ. Để thực hiện việc này, hãy nhấn nút chuột phải (hoặc bấm vào ô trong 1 giây đối với thiết bị di động). Điều này sẽ ngăn chặn việc vô tình nhấp vào các ô có mìn.</li>
                </ul>
                <h3>Chung kết</h3>
                <p>Trò chơi chỉ được coi là kết thúc thành công khi tất cả các ô “không có mìn” đều được mở ra. Trò chơi coi như thua trong trường hợp khi mở phải ô có mìn.</p>
            </div>
        </div>

        <!-- Game Board -->
        <div class="game-container">
            <h2>Dò mìn - Gomoku Game</h2>
            <table id="table_game">
            </table>
        </div>
        <div style="text-align: center; margin-bottom: 10px;">
            <p>Mìn còn lại: <span id="mine_count">15</span></p>
            <button id="restartButton" onclick="restartGame()" style="display: none;">🔄 Chơi lại</button>
        </div>


    </main>

    <!-- Footer -->
    <?
    include "../includes/footer.php"
    ?>
    <script src="<?= asset('js/pGame.js') ?>pGame.js"></script>
    <script type="text/javascript" src="<?= asset('js/min-game.js') ?>"></script>
</body>

</html>