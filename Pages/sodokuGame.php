<?php
session_start();
if (!isset($_SESSION["user"])) {
    http_response_code(404); 
    die("404 Not Found"); 
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAME I&R Online</title>
    <link rel="shortcut icon" href="10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/pGame.css">
</head>
<body>
    <!-- Header -->
    <?php
        include "../includes/header.php"
    ?>

    <main>
        <h1>SUDOKU <span class="stars">⭐⭐⭐⭐⭐</span></h1>
        <div class="buttons">
            <button class="back" id="backButton"><<</button>
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
                <h2>Quy tắc trò chơi Sudoku</h2>
                <p>Sudoku là một trò chơi giải đố số học phổ biến, thử thách trí tuệ và kỹ năng logic của người chơi.</p>
                <p>Kích thước của lưới Sudoku thay đổi tùy theo phiên bản, nhưng phổ biến nhất là:</p>
                <ul>
                    <li>Lưới 4x4 dành cho người mới bắt đầu;</li>
                    <li>Lưới 6x6 dành cho người chơi trung cấp;</li>
                    <li>Lưới 9x9 dành cho người chơi chuyên nghiệp.</li>
                </ul>
                <h3>Mục tiêu của trò chơi</h3>
                <p>Điền số vào lưới sao cho mỗi hàng, mỗi cột và mỗi khối vuông con đều chứa đầy đủ các số từ 1 đến kích thước lưới mà không lặp lại.</p>
                <h3>Tiến trình trận đấu</h3>
                <ul>
                    <li>Bảng Sudoku bắt đầu với một số ô đã được điền sẵn các số;</li>
                    <li>Người chơi cần suy luận để điền các số còn thiếu vào các ô trống sao cho không vi phạm nguyên tắc không lặp số;</li>
                    <li>Mỗi số chỉ được xuất hiện duy nhất một lần trong mỗi hàng, mỗi cột và mỗi khối vuông nhỏ;</li>
                    <li>Trò chơi yêu cầu kỹ năng tư duy logic để xác định số phù hợp cho từng ô;</li>
                    <li>Một số phiên bản Sudoku cho phép sử dụng gợi ý hoặc đánh dấu tạm thời các số có thể đúng.</li>
                </ul>
                <h3>Chung kết</h3>
                <p>Trò chơi được coi là hoàn thành thành công khi tất cả các ô trên lưới đều được điền đúng theo quy tắc. Trò chơi kết thúc không thành công nếu có số bị lặp lại trong hàng, cột hoặc khối vuông nhỏ.</p>
            </div>
        </div>

        <!-- Game Board -->
        <div class="game-container">
            <h2>Cờ Caro - Gomoku Game</h2>
            <div class="board" id="board"></div> <!-- Grid for the game -->
        </div>
    </main>

    <!-- Footer -->
    <?
    include "../includes/footer.php"    
    ?>

<script src="../assets/js/pGame.js"></script>
</body>
</html>