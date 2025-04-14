<?php
session_start();
// Giả lập đăng nhập để có $_SESSION['user'] (sẽ bỏ sau khi bạn có hệ thống đăng nhập)
$_SESSION['user'] = ['ID' => 1];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cờ Caro - Gomoku Game</title>
    <link rel="stylesheet" href="../assets/css/caro.css">
</head>

<body>
    <div>
        <h1>Cờ Caro - Gomoku Game</h1>
        <h2 id="gameTimer">Thời gian: 00:00</h2>
        <h3 id="gameStatus">Đang khởi tạo...</h3>
        <h3 id="playerTurn">Lượt của bạn</h3>
        <table id="table_game"></table>
        <button id="backButton">Quay lại</button>
        <button id="surrenderBtn">Đầu hàng</button>
        <button id="showRulesBtn">Hiển thị luật chơi</button>
    </div>

    <div id="rulesModal" style="display: none;">
        <div class="modal-content">
            <span class="close">×</span>
            <p>Luật chơi: <br>
                - Mỗi người chơi lần lượt đánh ký hiệu X hoặc O. <br>
                - Người chơi nào xếp được 5 ký hiệu liên tiếp theo hàng ngang, dọc, hoặc chéo sẽ thắng.</p>
        </div>
    </div>

    <script src="../assets/js/contants.js"></script>
    <script src="../assets/js/caro-ui.js"></script>
    <script src="../assets/js/caro-game.js"></script>
</body>

</html>