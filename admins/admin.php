<?php
session_start();

require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../bootstrap.php');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin1</title>
    <link rel="shortcut icon" href="10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>

<body>

    <!-- Header -->
    <?php include "../includes/header.php" ?>

    <main class="dashboard">
        <aside class="sidebar">
            <ul>
                <li>🏠 Dashboard</li>
                <li>👤 Quản lý tài khoản</li>
            </ul>
        </aside>

        <section class="main-content">
            <div class="top-bar">
                <button class="logout-btn">📁 Đăng xuất</button>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <span class="emoji pink">👤</span>
                    <div>
                        <h2>87</h2>
                        <p>Người dùng</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="emoji yellow">🎮</span>
                    <div>
                        <h2>3</h2>
                        <p>Trò chơi</p>
                    </div>
                </div>
            </div>

            <div class="charts">
                <div class="chart-box">
                    <h3>SỐ LƯỢNG NGƯỜI DÙNG TRUY CẬP</h3>
                    <canvas id="chartUsers"></canvas>
                </div>

                <div class="chart-box">
                    <h3>TỔNG SỐ TRẬN TRONG THÁNG</h3>
                    <canvas id="chartMatches"></canvas>
                </div>

                <div class="chart-box">
                    <h3>ĐÁNH GIÁ CỦA NGƯỜI DÙNG</h3>
                    <canvas id="chartRatings"></canvas>
                </div>
            </div>
        </section>
    </main>



    <!-- Footer -->
    <?php include "../includes/footer.php"; ?>

    <script src="Admin.js"></script>
</body>

</html>