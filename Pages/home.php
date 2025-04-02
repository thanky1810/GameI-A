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
    <link rel="shortcut icon" href="<?= asset('assets/img/10.jpg') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('assets/css/Home.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/caro.css') ?>">
</head>

<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <section class="popular-games">
            <h2>Sự Lựa Chọn Của Đa Số</h2>
            <div class="game-list">
                <!-- Game Caro -->
                <div class="game-item" data-id="caro">
                    <img src="<?= asset('assets/img/12.jpg') ?>" alt="Caro">
                    <a href="<?= htmlspecialchars(logining(getCorrectUrl('Pages/caro.php'))) ?>">
                        <div class="overlay">
                            <p class="players">👥 <span id="count-caro">35</span> người chơi</p>
                            <button class="play-button">🎮 Chơi Caro</button>
                        </div>
                    </a>
                </div>

                <!-- Game Sudoku -->
                <div class="game-item" data-id="sudoku">
                    <img src="<?= asset('assets/img/13.jpg') ?>" alt="Sudoku">
                    <a href="<?= htmlspecialchars(logining(getCorrectUrl('Pages/sodoku.php'))) ?>">
                        <div class="overlay">
                            <p class="players">👥 <span id="count-sudoku">25</span> người chơi</p>
                            <button class="play-button">🎮 Chơi Sudoku</button>
                        </div>
                    </a>
                </div>

                <!-- Game Minesweeper -->
                <div class="game-item" data-id="minesweeper">
                    <img src="<?= asset('assets/img/11.jpg') ?>" alt="Minesweeper">
                    <a href="<?= htmlspecialchars(logining(getCorrectUrl('Pages/min.php'))) ?>">
                        <div class="overlay">
                            <p class="players">👥 <span id="count-minesweeper">19</span> người chơi</p>
                            <button class="play-button">🎮 Dò Mìn</button>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <section class="new-game">
            <h2>Game Mới</h2>
            <div class="game-item">
                <a href="<?= htmlspecialchars(logining(getCorrectUrl('Pages/min.php'))) ?>">
                    <img src="<?= asset('assets/img/11.jpg') ?>" alt="Minesweeper">
                    <div class="new-badge">MỚI</div>
                </a>
            </div>
        </section>

        <section class="about">
            <h2>GAME I&R Online - Trang web trò chơi cổ điển hiện đại</h2>
            <p>GAME I&R Online là một cổng trò chơi thế hệ mới, nơi bạn có thể trải nghiệm những game kinh điển với giao diện hiện đại.</p>
        </section>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script>
        // JavaScript để xử lý lượt người chơi (nếu cần)
        document.querySelectorAll('.game-item').forEach(item => {
            item.addEventListener('click', () => {
                const gameId = item.getAttribute('data-id');
                console.log(`Đã chọn game: ${gameId}`);
            });
        });
    </script>
</body>

</html>