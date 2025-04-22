<?php
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../bootstrap.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Portal - I&R Online</title>
    <link rel="shortcut icon" href="<?= asset('img/10.jpg') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('css/home.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
</head>

<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <section class="popular-games">
            <h2>Sự Lựa Chọn Của Đa Số</h2>
            <div class="game-list">
                <!-- Game Caro -->
                <div class="game-item" data-id="caro">
                    <img src="<?= asset('img/12.jpg') ?>" alt="Caro">
                    <a href="<?= logining(getCorrectUrl('Pages/caro.php')) ?>">
                        <div class="overlay">
                            <p class="players">👥 <span id="count-caro">35</span> người chơi</p>
                            <button class="play-button">🎮 Chơi Game</button>
                        </div>
                    </a>
                </div>

                <!-- Game Sudoku -->
                <div class="game-item" data-id="sudoku">
                    <img src="<?= asset('img/13.jpg') ?>" alt="Sudoku">
                    <a href="<?= htmlspecialchars(logining(getCorrectUrl('Pages/sodoku.php'))) ?>">
                        <div class="overlay">
                            <p class="players">👥 <span id="count-sudoku">25</span> người chơi</p>
                            <button class="play-button">🎮 Chơi Game</button>
                        </div>
                    </a>
                </div>

                <!-- Game Minesweeper -->
                <div class="game-item" data-id="minesweeper">
                    <img src="<?= asset('img/11.jpg') ?>" alt="Minesweeper">
                    <a href="<?= htmlspecialchars(logining(getCorrectUrl('Pages/min.php'))) ?>">
                        <div class="overlay">
                            <p class="players">👥 <span id="count-minesweeper">19</span> người chơi</p>
                            <button class="play-button">🎮 Chơi Game</button>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <section class="new-game">
            <h2>Game Mới</h2>
            <div class="game-item">
                <a href="<?= htmlspecialchars(logining(getCorrectUrl('Pages/min.php'))) ?>">
                    <img src="<?= asset('img/11.jpg') ?>" alt="Minesweeper">
                </a>
            </div>
        </section>

        <section class="about">
            <h2>GAME I&R Online - Trang web trò chơi cổ điển hiện đại</h2>
            <p>GAME I&R Online là một cổng trò chơi thế hệ mới, nơi bạn có thể trải nghiệm những game kinh điển với giao diện hiện đại.</p>
        </section>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>