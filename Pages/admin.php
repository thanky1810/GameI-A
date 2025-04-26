<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    http_response_code(404);
    die("404 Not Found");
}
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../bootstrap.php');
include("../includes/database.php");
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
                    <?php

                    $qr = "SELECT * FROM user";

                    $kq = mysqli_query($conn, $qr);

                    ?>


                    <div class="leaderboard">

                        <ul id="leaderboard-list">
                            <li>ID || User Name || Password || Role || Score || Win</li>
                            <?php
                            while ($d = mysqli_fetch_array($kq)) {
                            ?>
                                <li><?= $d['ID'];
                                    echo ' || ';
                                    $d['userName']; ?> <span><?= $d['Score']; ?></span></li>
                            <?php
                            }
                            ?>
                        </ul>
                    </div>
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