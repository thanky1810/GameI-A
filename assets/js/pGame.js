
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

    // Tạo bảng cờ
    const boardSize = 15;
    let currentPlayer = 'o'; // 'o' là người chơi màu đỏ, 'x' là người chơi màu xanh
    let gameTime = 0; // Thời gian trò chơi
    let timerInterval; // Biến lưu trữ interval của bộ đếm thời gian
    const board = document.getElementById('board');
    const gameTimer = document.getElementById('gameTimer');
    const currentResult = document.getElementById('currentResult');

    // Khởi tạo bảng cờ
    function createBoard() {
        board.innerHTML = ''; // Xóa bảng cũ
        for (let i = 0; i < boardSize; i++) {
            for (let j = 0; j < boardSize; j++) {
                const cell = document.createElement('div');
                cell.addEventListener('click', () => handleCellClick(cell));
                board.appendChild(cell);
            }
        }
    }

    // Xử lý khi click vào ô
    function handleCellClick(cell) {
        if (cell.classList.contains('x') || cell.classList.contains('o')) return; // Nếu ô đã có dấu
        cell.classList.add(currentPlayer); // Đặt dấu cho ô
        checkWin(); // Kiểm tra chiến thắng
        currentPlayer = currentPlayer === 'o' ? 'x' : 'o'; // Chuyển lượt
    }

    // Kiểm tra chiến thắng
    function checkWin() {
        // Logic kiểm tra chiến thắng (bạn có thể thêm logic này sau)
        // Ví dụ: Kiểm tra 5 ô liên tiếp cùng dấu
    }

    // Reset trò chơi
    function resetGame() {
        clearInterval(timerInterval); // Dừng bộ đếm thời gian
        gameTime = 0;
        gameTimer.textContent = '00:00';
        currentResult.textContent = 'Đang chơi...';
        createBoard(); // Tạo lại bảng cờ
        startTimer(); // Bắt đầu lại bộ đếm thời gian
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

    // Khởi tạo trò chơi
    createBoard();
    startTimer();
});