<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link rel="stylesheet" href="/Project/assets/css/header.css">
<link rel="stylesheet" href="/Project/assets/css/main.css">
<header>
    <a href="/Project/Pages/home.php">
        <div class="logo">
            <img src="/Project/assets/img/10.jpg" alt="GAME I&R Online">
            <span>GAME I&R Online</span>
        </div>
    </a>
    <div class="login-btn">
        <?php
        if (isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            header("Location: /Project/Pages/home.php");
            exit();
        }
        if (!isset($_SESSION["user"])) {
            echo '<a href="/Project/Pages/login.php">🔑 Đăng nhập</a>';
        } else {
            $userName = $_SESSION["user"];
            echo '
                        <div class="user-info">
                            <div class="user-avatar">
                                <img src="/Project/assets/img/5.jpg" alt="User Avatar">
                            </div>
                            <span id="user-top-name">' . $userName . '</span>
                            <div class="menu-toggle" id="menuToggle">☰</div>
                            <div class="dropdown-menu" id="dropdownMenu">
                                <a href="/Project/Pages/account.php">Profile</a>
                                <a href="?logout=true">Đăng xuất</a>
                            </div>
                        </div> ';
        }
        ?>
    </div>
    <script src="/Project/assets/js/header.js"></script>
</header>