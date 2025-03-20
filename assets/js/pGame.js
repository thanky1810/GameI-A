
//Tham khảo
document.addEventListener("DOMContentLoaded", function () {
    // Xử lý menu toggle
    const menuToggle = document.getElementById("menuToggle");
    const dropdownMenu = document.getElementById("dropdownMenu");
    menuToggle.addEventListener("click", function (event) {
        event.stopPropagation(); // Ngăn chặn sự kiện click lan truyền lên
        dropdownMenu.classList.toggle("show");
    });
    document.addEventListener("click", function (event) {
        if (!menuToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove("show");
        }
    });

    // Xử lý nút quay lại và bảng quy tắc
    document.getElementById('backButton').addEventListener('click', () => window.history.back());
    document.getElementById('showRulesBtn').addEventListener('click', () => document.getElementById('rulesPopup').style.display = 'flex');
    document.getElementById('closeRulesBtn').addEventListener('click', () => document.getElementById('rulesPopup').style.display = 'none');

    // Xử lý nút "Đầu hàng"
    document.querySelector('.play-btn').addEventListener('click', () => {
        alert("Bạn đã đầu hàng!");
        resetGame();
    });


    // Xử lý khi click vào ô
    let timerInterval; // Biến toàn cục dùng để lưu bộ đếm thời gian
    let gameTime = 0; // Biến lưu thời gian chơi

    // Reset trò chơi
    function resetGame() {
        clearInterval(timerInterval);
        gameTime = 0;
        document.getElementById("gameTimer").textContent = '00:00';
        document.getElementById("currentResult").textContent = 'Đang chơi...';
        createBoard();
        startTimer();
    }


    // Bắt đầu bộ đếm thời gian
    function startTimer() {
        timerInterval = setInterval(() => {
            gameTime++;
            const minutes = Math.floor(gameTime / 60);
            const seconds = gameTime % 60;
            gameTimer.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }, 1000);
    }
    startTimer();
});