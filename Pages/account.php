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
    <link rel="stylesheet" href="../assets/css/account.css">
</head>

<body>

    <!-- Header -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <section id="account-info">
            <?php
            $userName = $_SESSION["user"];
            ?>
            <div class="account-header">
                <div class="avatar">
                    <img src="../assets/img/5.jpg" alt="Avatar">
                </div>
                <div class="user-details">
                    <h2 id="username"> <?php echo $userName['Username']; ?> </h2>
                    <p id="status">Trực tuyến</p>
                </div>
                <div class="back-home">
                    <a href="../Pages/home.php" class="back-btn">Home</a>
                </div>
            </div>
            <div class="stats">

                <?php
                $conn = mysqli_connect("localhost", "root", "", "game-a");

                $sq = "SELECT * FROM user WHERE ID = {$userName['ID']}";

                $rs = mysqli_query($conn, $sq);
                if ($rs && mysqli_num_rows($rs) > 0) {
                    $row = mysqli_fetch_assoc($rs);
                }
                ?>

                <h3>SỐ LIỆU THỐNG KÊ</h3>
                <ul>
                    <li><span class="label">Số lượng trò chơi:</span> <span id="gamesPlayed">4</span></li>
                    <li><span class="label">Số lượng trận thắng:</span> <span id="matchesWon"><?php echo $row['sumWin'] ?></span></li>
                    <li><span class="label">Thời gian chơi trên trang web:</span> <span id="playTime">2 giờ</span></li>
                    <li><span class="label">Đã đăng ký:</span> <span id="registrationTime">10:30, 04/03/2025</span></li>
                </ul>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include "../includes/footer.php" ?>

</body>

</html>