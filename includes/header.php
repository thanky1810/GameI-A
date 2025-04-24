<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../bootstrap.php');
$conn = mysqli_connect("localhost", "root", "", "game-a");
?>
<link rel="stylesheet" href="<?= asset('css/caro.css') ?>">
<link rel="stylesheet" href="<?= asset('css/header.css') ?>">
<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
<header>

    <a href="<?= getCorrectUrl('Pages/home.php') ?>">
        <div class="logo">
            <img src="<?= asset('img/10.jpg') ?>" alt="GAME I&R Online">
            <span>GAME I&R Online</span>
        </div>
    </a>
    <div class="login-btn">
        <?php
        if (isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            header("Location: " . getCorrectUrl('Pages/home.php'));
            exit();
        }

        // Kiểm tra tồn tại session["user"] trước khi gán biến
        if (!isset($_SESSION["user"])) {
            echo '<a href="' . getCorrectUrl('Pages/login.php') . '">🔑 Đăng nhập</a>';
        } else {
            $userName = $_SESSION["user"];
            $un = $userName['ID'];
            $qr = mysqli_query($conn, "SELECT avatar FROM user 
                    WHERE ID = $un");
            $row = mysqli_fetch_assoc($qr);
            $avatarPath = !empty($userName["avatar"]) ? htmlspecialchars($userName["avatar"]) : "assets/img/5.jpg";
            echo '
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="../' . $row['avatar'] . '" alt="Avatar">
                    </div>
                    <span id="user-top-name">' . htmlspecialchars($userName['Username']) . '</span>
                    <div class="menu-toggle" id="menuToggle">☰</div>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="' . getCorrectUrl('Pages/account.php') . '">Profile</a>
                        <a href="?logout=true">Đăng xuất' . $userName['ID'] . '</a>
                    </div>
                </div>';
        }
        ?>
    </div>
    <script src="<?= asset('js/header.js') ?>"></script>
</header>