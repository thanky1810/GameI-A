assets / js / sodoku.js
class SudokuGame {
    constructor() {
        this.board = [];
        this.size = 9;
        this.gameId = null;
        this.mode = 'hard';
        this.gameOver = false;
    }

    init() {
        const mode = new URLSearchParams(window.location.search).get('mode') || 'hard';
        this.mode = mode;
        this.size = (mode === 'easy') ? 4 : (mode === 'medium') ? 6 : 9;
        this.fetchNewGame();
    }

    async fetchNewGame() {
        try {
            const response = await fetch('/Project/api/sudoku-state.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'new_game', mode: this.mode })
            });
            const data = await response.json();
            this.gameId = data.gameId;
            this.board = data.board;
            SudokuUI.renderBoard(this.board, this.size);
        } catch (error) {
            SudokuUI.showMessage('Không thể tạo game mới.');
        }
    }

    async makeMove(row, col, value) {
        if (this.gameOver || this.board[row][col] !== 0) return;

        try {
            const response = await fetch('/Project/api/sudoku-move.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    gameId: this.gameId,
                    action: 'move',
                    row: row,
                    col: col,
                    value: parseInt(value)
                })
            });
            const data = await response.json();
            this.board = data.board;
            SudokuUI.renderBoard(this.board, this.size);
            if (!data.correct) {
                SudokuUI.highlightError(row, col);
            }
            if (data.finished) {
                this.gameOver = true;
                SudokuUI.showMessage('Bạn thắng!');
            }
        } catch (error) {
            SudokuUI.showMessage('Không thể thực hiện nước đi.');
        }
    }

    async surrender() {
        if (this.gameOver) return;
        try {
            const response = await fetch('/Project/api/sudoku-move.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ gameId: this.gameId, action: 'surrender' })
            });
            const data = await response.json();
            this.gameOver = true;
            SudokuUI.showMessage('Bạn đã đầu hàng.');
        } catch (error) {
            SudokuUI.showMessage('Không thể thực hiện đầu hàng.');
        }
    }
}

class SudokuUI {
    static showMessage(message) {
        const resultElement = document.getElementById('currentResult');
        if (resultElement) {
            resultElement.textContent = message;
        }
    }

    static renderBoard(board, size) {
        const gameBoard = document.getElementById('table_game');
        if (!gameBoard) return;

        gameBoard.innerHTML = '';
        for (let row = 0; row < size; row++) {
            const tr = document.createElement('tr');
            for (let col = 0; col < size; col++) {
                const td = document.createElement('td');
                td.className = 'sudoku-cell';
                const div = document.createElement('div');
                div.id = `cell-${row}-${col}`;
                if (board[row][col] === 0) {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.maxLength = 1;
                    input.pattern = '[1-9]';
                    input.inputMode = 'numeric';
                    input.addEventListener('input', (e) => {
                        const value = e.target.value;
                        if (value && value >= 1 && value <= size) {
                            window.game.makeMove(row, col, value);
                        }
                    });
                    div.appendChild(input);
                } else {
                    div.className = 'fixed';
                    div.textContent = board[row][col];
                }
                td.appendChild(div);
                tr.appendChild(td);
            }
            gameBoard.appendChild(tr);
        }
    }

    static highlightError(row, col) {
        const cell = document.getElementById(`cell-${row}-${col}`);
        if (cell) {
            cell.classList.add('error');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const game = new SudokuGame();
    window.game = game;
    game.init();

    const surrenderButton = document.querySelector('.play-btn');
    if (surrenderButton) {
        surrenderButton.addEventListener('click', () => {
            game.surrender();
        });
    }
});