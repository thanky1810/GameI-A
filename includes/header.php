<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
        if (!isset($_SESSION["user"])) {
            echo '<a href="' . getCorrectUrl('Pages/login.php') . '">🔑 Đăng nhập</a>';
        } else {
            $userName = $_SESSION["user"];
            echo '
                    <div class="user-info">
                        <div class="user-avatar">
                            <img src=" ' . asset('img/5.jpg') . '" alt="User Avatar">
                            </div>
                            <span id="user-top-name">' . $userName['Username'] . '</span>
                            <div class="menu-toggle" id="menuToggle">☰</div>
                            <div class="dropdown-menu" id="dropdownMenu">
                                <a href="' . getCorrectUrl('Pages/account.php') . '">Profile</a>
                                <a href="?logout=true">Đăng xuất</a>
                            </div>
                        </div> ';
        }
        ?>
    </div>
    <script src="<?= asset('js/header.js') ?>"></script>
</header>