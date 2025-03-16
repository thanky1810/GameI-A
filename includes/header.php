<?php
    session_start();

?>
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/main.css">
<header>
        <div class="logo">
            <img src="../assets/img/10.jpg" alt="GAME I&R Online">
            <span>GAME I&R Online</span>
        </div>
        <div class="login-btn">
            <?php
                if (isset($_GET['logout'])) {
                    session_unset();
                    session_destroy();
                    header("Location: ../Pages/login.php"); // Chuyển hướng về trang đăng nhập
                    exit();
                }
                if(!isset($_SESSION["user"])){
                    echo '<a href="../Pages/login.php">🔑 Đăng nhập</a>';
                }else{
                    $userName = $_SESSION["user"];
                    echo '
                        <div class="user-info">
                            <div class="user-avatar">
                                <img src="../assets/img/5.jpg" alt="User Avatar">
                            </div>
                            <span id="user-top-name">' . $userName . '</span>
                            <div class="menu-toggle" id="menuToggle">☰</div>
                            <div class="dropdown-menu" id="dropdownMenu">
                                <a href="../Pages/account.php">Profile</a>
                                <a href="?logout=true">Đăng xuất</a>
                            </div>
                        </div> ';
                }                
            ?>
        </div>
<script src="../assets/js/header.js"></script>
</header>