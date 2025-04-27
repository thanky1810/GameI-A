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
    <link rel="shortcut icon" href="../assets/img/10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/game.css">
</head>

<body>

    <!-- Header -->

    <?php include "../includes/header.php" ?>

    <main>
        <h1>SUDOKU<span class="stars">⭐⭐⭐⭐⭐</span></h1>
        <div class="buttons">
            <button class="back" id="backButton">
                <<< /button>
                    <button class="btn rule-btn" id="showRulesBtn">Quy tắc</button>
                    <button class="btn play-btn" data-url="sodokuGame.php">Bắt đầu chơi</button>
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

        <h2>Bảng kỷ lục</h2>
        <?php

        $qr = "SELECT * FROM user 
                    ORDER BY Score DESC";

        $kq = mysqli_query($conn, $qr);

        ?>

        <div class="leaderboard">
            <ul id="leaderboard-list">
                <?php
                while ($d = mysqli_fetch_array($kq)) {
                ?>
                    <li><?= $d['userName']; ?> <span><?= $d['Score']; ?>đ</span></li>
                <?php
                }
                ?>
            </ul>
        </div>

        <div class="leaderboard">
            <ul id="leaderboard-list"></ul>
        </div>
        
        <!-- Modal chọn chế độ chơi -->
        <div class="mode-popup" id="modePopup">
            <div class="mode-content">
                <button id="closeModeBtn" class="close-btn">❌</button>
                <h2>Chọn chế độ chơi</h2>
                <div class="game-mode">
                    <button id="btn-easy-mode" data-mode="easy">Dễ (4x4)</button>
                    <button id="btn-medium-mode" data-mode="medium">Trung bình (6x6)</button>
                    <button id="btn-hard-mode" data-mode="hard">Khó (9x9)</button>
                </div>
                <button id="startGameBtn" class="start-btn">Bắt đầu</button>
            </div>
        </div>

        
        <div class="info">
            <div class="info-text">
                <p>Sudoku là một trò chơi trí tuệ đầy thử thách, đặc biệt ở các cấp độ khó, nơi bạn cần phải suy luận chính xác để điền số vào từng ô trống. Chính sự logic và chiến lược trong từng bước đi khiến Sudoku trở thành một trò chơi hấp dẫn và cuốn hút!</p>
                <p>Trên GAME I&R Online, bạn có thể trải nghiệm Sudoku hoàn toàn miễn phí chỉ với một lần đăng ký đơn giản. Học cách giải Sudoku thông qua các hướng dẫn chi tiết, video tổng quan, cũng như thực hành với nhiều cấp độ từ dễ đến khó, giúp bạn từng bước làm chủ trò chơi này.</p>
                <p>Hãy thử sức trong chế độ một người chơi để rèn luyện tư duy logic, phát triển kỹ năng quan sát và tìm ra những chiến thuật giải đố hiệu quả nhất. Càng chơi nhiều, bạn sẽ càng nâng cao khả năng suy luận, tích lũy kinh nghiệm quý báu và chinh phục những thử thách khó hơn, tiến xa hơn trên bảng xếp hạng Sudoku!</p>
            </div>
            <div class="game-image">
                <img src="../assets/img/8.jpg" alt="Min">
            </div>
        </div>
    </main>

    <!-- Footer -->

    <?php include "../includes/footer.php" ?>


</body>
<script src="../assets/js/game.js"></script>
<script src="../assets/js/sodoku.js"></script>

</html>