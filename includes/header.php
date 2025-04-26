<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../bootstrap.php');

$conn = mysqli_connect("localhost", "root", "", "game-a");
?>

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

            setcookie("remember_user", "", time() - 3600, "/");
            setcookie("remember_pass", "", time() - 3600, "/");

            header("Location: " . getCorrectUrl('Pages/home.php'));
            exit();
        }

        if (!isset($_SESSION["user"])) {
            echo '<a href="' . getCorrectUrl('Pages/login.php') . '">🔑 Đăng nhập</a>';
        } else {
            $userSession = $_SESSION["user"];
            $userID = (int) $userSession['ID'];

            $query = mysqli_query($conn, "SELECT * FROM user WHERE ID = $userID");
            $userData = mysqli_fetch_assoc($query);

            $avatarPath = !empty($userData["avatar"]) ? htmlspecialchars($userData["avatar"]) : "assets/img/5.jpg";
            $userNameDisplay = htmlspecialchars($userData['userName']);
            $userRole = $userData['Role'] ?? 'user';

            echo '
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="../' . $avatarPath . '" alt="Avatar">
                    </div>
                    <span id="user-top-name">' . $userNameDisplay . '</span>
                    <div class="menu-toggle" id="menuToggle">☰</div>
                    <div class="dropdown-menu" id="dropdownMenu">';

            if ($_SESSION['user']['Role'] === 'admin') {
                echo '<a href="' . getCorrectUrl('Pages/admin.php') . '">⚙️ Manage</a>';
            } else {
                echo '<a href="' . getCorrectUrl('Pages/account.php') . '">👤 Profile</a>';
                echo  $_SESSION['user']['Role'];
            }

            echo '<a href="?logout=true">🚪 Đăng xuất</a>
                    </div>
                </div>';
        }
        ?>
    </div>

    <script src="<?= asset('js/header.js') ?>"></script>
</header>