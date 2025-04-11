class CaroUI {
    constructor() {
        this.boardSize = 15;
        this.board = [];
        this.gameType = this.getGameType();
        this.timer = null;
        this.startTime = new Date();
        this.mySymbol = X;
        this.opponentSymbol = O;
        this.isMyTurn = false;
        this.isGameOver = false;

        // Khởi tạo UI
        this.initBoard();
        this.setupEventListeners();
        this.updateStatus("Đang khởi tạo...");
        this.startTimer();

        // Khởi tạo kết nối với backend
        this.gameHandler = new CaroGame(this);
    }

    getGameType() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('type') || COMPUTER;
    }

    initBoard() {
        const table = document.getElementById('table_game');
        table.innerHTML = '';

        for (let i = 0; i < this.boardSize; i++) {
            const row = document.createElement('tr');
            this.board[i] = [];

            for (let j = 0; j < this.boardSize; j++) {
                const cell = document.createElement('td');
                cell.dataset.row = i;
                cell.dataset.col = j;
                cell.addEventListener('click', () => this.handleCellClick(i, j));
                row.appendChild(cell);
                this.board[i][j] = EMPTY;
            }

            table.appendChild(row);
        }
    }

    handleCellClick(row, col) {
        // Kiểm tra trạng thái game
        if (this.isGameOver || this.board[row][col] !== EMPTY || !this.isMyTurn) {
            return;
        }

        // Gửi nước đi tới game handler
        this.gameHandler.makeMove(row, col);
    }

    // Cập nhật UI sau mỗi nước đi
    updateBoard(row, col, symbol) {
        this.board[row][col] = symbol;
        this.updateCell(row, col);
    }

    updateCell(row, col) {
        const cell = document.querySelector(`td[data-row="${row}"][data-col="${col}"]`);
        if (cell) {
            cell.textContent = this.board[row][col];
            cell.classList.add(this.board[row][col].toLowerCase());
        }
    }

    updateStatus(message) {
        document.getElementById('currentResult').textContent = message;
    }

    updatePlayerTurn(isMyTurn) {
        this.isMyTurn = isMyTurn;
        const playerTurnElement = document.getElementById('playerTurn');
        if (this.gameType === TWO_PLAYER) {
            playerTurnElement.textContent = this.isMyTurn ? 'Lượt của bạn' : 'Lượt đối thủ';
        } else {
            playerTurnElement.textContent = this.isMyTurn ? 'Lượt của bạn' : 'Lượt của máy';
        }
    }

    startTimer() {
        this.timer = setInterval(() => {
            const now = new Date();
            const diffInSeconds = Math.floor((now - this.startTime) / 1000);
            const minutes = Math.floor(diffInSeconds / 60).toString().padStart(2, '0');
            const seconds = (diffInSeconds % 60).toString().padStart(2, '0');
            document.getElementById('gameTimer').textContent = `${minutes}:${seconds}`;
        }, 1000);
    }

    stopTimer() {
        clearInterval(this.timer);
    }

    endGame(isWinner) {
        this.isGameOver = true;
        this.stopTimer();

        if (isWinner === null) {
            this.updateStatus('Trận đấu hòa!');
        } else if (isWinner) {
            this.updateStatus('Bạn đã thắng! 🎉');
        } else {
            this.updateStatus(this.gameType === TWO_PLAYER ? 'Bạn đã thua! 😢' : 'Máy tính đã thắng! 😢');
        }
    }

    setupEventListeners() {
        // Quay lại menu chính
        document.getElementById('backButton').addEventListener('click', () => {
            window.location.href = 'caro.php';
        });

        // Hiển thị quy tắc
        document.getElementById('showRulesBtn').addEventListener('click', () => {
            document.getElementById('rulesPopup').style.display = 'flex';
        });

        // Đóng quy tắc
        document.getElementById('closeRulesBtn').addEventListener('click', () => {
            document.getElementById('rulesPopup').style.display = 'none';
        });

        // Đầu hàng
        document.getElementById('surrenderBtn').addEventListener('click', () => {
            if (confirm('Bạn có chắc chắn muốn đầu hàng?')) {
                this.gameHandler.surrender();
                this.endGame(false);
            }
        });
    }
}

// Khởi tạo UI khi trang đã tải xong
document.addEventListener('DOMContentLoaded', () => {
    window.caroUI = new CaroUI();
});