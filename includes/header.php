<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/main.css">
<header>
        <div class="logo">
            <img src="../assets/img/10.jpg" alt="GAME I&R Online">
            <span>GAME I&R Online</span>
        </div>
        <div class="login-btn">
            <?php
                


                if(!isset($_SESSION["ID"])){
                    echo '<a href="../Pages/login.php">🔑 Đăng nhập</a>';
                }else{

                    echo '
                        <div class="user-info">
                            <div class="user-avatar">
                                <img src="imgs/5.jpg" alt="User Avatar">
                            </div>
                            <span id="user-top-name">GUEST0232</span>
                            <div class="menu-toggle" id="menuToggle">☰</div>
                            <div class="dropdown-menu" id="dropdownMenu">
                                <a href="Taikhoan.html">Profile</a>
                                <a href="logout.html">Đăng xuất</a>
                            </div>
                        </div> ';
                }
            ?>
        </div>
</header>