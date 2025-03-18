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
    <link rel="shortcut icon" href="../assets/img/10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/game.css">
</head>
<body>

    <!-- Header -->
    <?php include "../includes/header.php"?>

    <main>
        <h1>CỜ CARO <span class="stars">⭐⭐⭐⭐⭐</span></h1>
        <div class="buttons">
            <button class="back" id="backButton"><<</button>
            <button class="btn rule-btn" id="showRulesBtn">Quy tắc</button>
            <button class="btn play-btn">Bắt đầu chơi</button>
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

        <h2>Bảng kỷ lục</h2>
        <div class="leaderboard">
            <ul id="leaderboard-list"></ul>
        </div>

        <div class="info">
            <div class="info-text">
                <p>Chiến thắng trong trò chơi cờ caro trở nên khó khăn hơn nếu đó là trò chơi gomoku trên bàn cờ 15×15. Tuy nhiên, điều này khiến quá trình chơi trở nên thú vị hơn!</p>
                <p>Trên GAME I&R Online, chơi trò chơi cờ caro với một người bạn hoàn toàn miễn phí chỉ sau một lần đăng ký đơn giản.</p>
                <p>Học cách chơi Gomoku (học 5 quân một hàng) bạn sẽ đọc giúp đỡ bảng các quy tắc trên trang, video tổng quan ngắn và có hội đấu với máy tính (nó là một bậc thầy về cờ caro).</p>
                <p>Hãy thực hành & đối mặt với người chơi, hãy phát triển các chiến lược và chiến thuật của mình, hãy tìm kiếm các đối thủ chuyên thắng — bảng xếp hạng này. Bạn sẽ giành được nhiều chiến thắng hơn trong các trò chơi với những đối thủ thực, kiếm được nhiều viên pha lê hơn, hãy thu thập nhiều kinh nghiệm và tiến lên đầu bảng xếp hạng cờ caro!</p>
            </div>
            <div class="game-image">
                <img src="../assets/img/7.jpg" alt="Cờ Caro">
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include "../includes/footer.php"?>
</body>
<script src="../assets/js/game.js"></script>
</html>



