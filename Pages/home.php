<?php
    require_once '../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" href="../assets/img/10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/Home.css">
    <link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/caro.css">

</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main>
        <section class="popular-games">
            <h2>Sự Lựa Chọn Của Đa Số</h2>
            <div class="game-list">
                <div class="game-item" data-id="caro">
                    <img src="../assets/img/12.jpg" alt="Caro">
                    <a href="<?php echo logining('../Pages/caro.php'); ?>">
                        <div class="overlay">
                            <p class="players">👥 <span id="count-caro">35</span> người chơi</p>
                            <button class="play-button">🎮 Chơi</button>
                        </div>
                    </a>
                </div>
                <div class="game-item" data-id="sudoku">
                    <img src="../assets/img/13.jpg" alt="Sudoku">
                    <a href="<?php echo logining('../Pages/sodoku.php'); ?>">
                        <div class="overlay">
                            <p class="players">👥 <span id="count-sudoku">25</span> người chơi</p>
                            <button class="play-button">🎮 Chơi</button>
                        </div>
                    </a>
                </div>
                <div class="game-item" data-id="minesweeper">
                    <img src="../assets/img/11.jpg" alt="Minesweeper">
                    <a href="<?php echo logining('../Pages/min.php'); ?>">
                        <div class="overlay">
                            <p class="players">👥 <span id="count-minesweeper">19</span> người chơi</p>
                            <button class="play-button">🎮 Chơi</button>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <section class="new-game">
            <h2>Mới</h2>
            <div class="game-item">
                <a href="<?php echo logining('../Pages/min.php'); ?>">
                    <img src="../assets/img/11.jpg" alt="Minesweeper">
                </a>
            </div>
        </section>

        <section class="about">
            <h2>GAME I&R Online – Trang web trò chơi cổ điển hiện đại</h2>
            <p>GAME I&R Online là một cổng trò chơi thế hệ mới, nơi mà tất cả những trò chơi phổ biến và quen thuộc nhất với nhiều người được kết hợp trong một không gian chơi game độc đáo.</p>
            <p>
                Sau khi đăng ký đơn giản, mỗi người tham gia có quyền truy cập vào nhiều trò chơi trực tuyến cổ điển thuộc nhiều 
                thể loại và cấp bậc khác nhau với chất lượng tuyệt tốt, phù hợp với mọi thiết bị mà khôgn cần tải xuống và cài đặt. 
                Sự tiến bộ của bnaj trong trò chơi sẽ được phản ánh trên thứ hạng của bạn.
            </p>
        </section>
    </main>
    <div>
        <div class="options">
            <label for="list-type-play"></label><select id="list-type-play" class="hide-option option">
                <option selected="selected" disabled="disabled" value="">Select type play</option>
                <option value="2-players">2 players</option>
                <option value="player-computer">Player and computer</option>
                <option value="computer-computer">Computer and computer</option>
            </select>
        </div>

    </div>

    <div class="button" id="button" onclick="handleLetGo()">Let's go!</div>

    <?php include '../includes/footer.php'?>
</body>
</html>