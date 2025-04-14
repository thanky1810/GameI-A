class CaroUI {
    constructor(gameType) {
        this.gameType = gameType;
        this.mySymbol = null;
        this.opponentSymbol = null;
        this.table = document.getElementById('table_game');
        this.statusElement = document.getElementById('gameStatus');
        this.playerTurnElement = document.getElementById('playerTurn');
        this.timerElement = document.getElementById('gameTimer');
        this.backButton = document.getElementById('backButton');
        this.surrenderBtn = document.getElementById('surrenderBtn');
        this.showRulesBtn = document.getElementById('showRulesBtn');
        this.rulesModal = document.getElementById('rulesModal');
        this.closeModal = null; // Khởi tạo ban đầu là null
        this.gameEnded = false;
        this.startTime = Date.now();

        this.initBoard();
        this.initModal(); // Khởi tạo modal riêng
        this.initEventListeners();
        this.updateTimer();
    }

    initBoard() {
        for (let i = 0; i < 15; i++) {
            const row = document.createElement('tr');
            for (let j = 0; j < 15; j++) {
                const cell = document.createElement('td');
                cell.dataset.row = i;
                cell.dataset.col = j;
                row.appendChild(cell);
            }
            this.table.appendChild(row);
        }
    }

    initModal() {
        if (this.rulesModal) {
            this.closeModal = this.rulesModal.querySelector('.close');
        } else {
            console.error("Không tìm thấy phần tử rulesModal trong DOM");
        }
    }

    initEventListeners() {
        this.table.addEventListener('click', (event) => {
            if (this.gameEnded) return;
            const cell = event.target;
            if (cell.tagName !== 'TD') return;
            const row = parseInt(cell.dataset.row);
            const col = parseInt(cell.dataset.col);
            const game = window.game;
            game.makeMove(row, col);
        });

        this.surrenderBtn.addEventListener('click', () => {
            if (this.gameEnded) return;
            const game = window.game;
            game.surrender();
        });

        this.backButton.addEventListener('click', () => {
            window.location.href = '/';
        });

        if (this.showRulesBtn && this.rulesModal && this.closeModal) {
            this.showRulesBtn.addEventListener('click', () => {
                this.rulesModal.style.display = 'block';
            });

            this.closeModal.addEventListener('click', () => {
                this.rulesModal.style.display = 'none';
            });

            window.addEventListener('click', (event) => {
                if (event.target === this.rulesModal) {
                    this.rulesModal.style.display = 'none';
                }
            });
        } else {
            console.warn("Không thể gắn sự kiện cho modal vì một số phần tử không tồn tại");
        }
    }

    updateCell(row, col, symbol) {
        const cell = this.table.rows[row].cells[col];
        cell.textContent = symbol;
        cell.className = symbol === X ? 'x' : 'o';
    }

    updateStatus(message) {
        this.statusElement.textContent = message;
    }

    updatePlayerTurn(isMyTurn) {
        this.playerTurnElement.textContent = isMyTurn ? 'Lượt của bạn' : 'Lượt của máy';
    }

    highlightWinningCells(cells) {
        for (const [row, col] of cells) {
            const cell = this.table.rows[row].cells[col];
            cell.classList.add('win');
        }
    }

    endGame() {
        this.gameEnded = true;
        this.surrenderBtn.disabled = true;
    }

    updateTimer() {
        setInterval(() => {
            if (this.gameEnded) return;
            const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
            const minutes = String(Math.floor(elapsed / 60)).padStart(2, '0');
            const seconds = String(elapsed % 60).padStart(2, '0');
            this.timerElement.textContent = `Thời gian: ${minutes}:${seconds}`;
        }, 1000);
    }
}