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
</head>

<body>
    <!-- Header -->
    <?php
    include "../includes/header.php"
    ?>

    <main>
        <h1>SUDOKU <span class="stars">⭐⭐⭐⭐⭐</span></h1>
        <div class="buttons">
            <button class="back" id="backButton">
                << </button>
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
        <table id="table_game"></table>

        <script src="/Project/game/sample.php"></script>
        <script src="/Project/game/suduku.php"></script>
        <script>
            const userData = <?php echo json_encode($_SESSION['user'] ?? null); ?>;
            const sessionId = '<?php echo session_id(); ?>'; // Lấy ID phiên
            if (userData && typeof userData === 'object' && 'ID' in userData && 'Username' in userData) {
                localStorage.setItem('userId', userData.ID || '');
                localStorage.setItem('username', userData.Username || '');
                localStorage.setItem('sessionId', sessionId); // Lưu ID phiên vào localStorage
            } else {
                console.error('Dữ liệu người dùng không hợp lệ:', userData);
            }
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