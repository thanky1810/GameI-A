class CaroUI {
    static showMessage(message) {
        const messageElement = document.getElementById('message');
        if (messageElement) {
            messageElement.textContent = message;
        } else {
            console.error('Message element not found');
        }
    }

    static renderBoard(board) {
        const gameBoard = document.getElementById('game-board');
        if (!gameBoard) {
            console.error('Game board element not found');
            return;
        }

        gameBoard.innerHTML = '';
        for (let row = 0; row < 15; row++) {
            for (let col = 0; col < 15; col++) {
                const cell = document.createElement('div');
                cell.className = 'cell';
                cell.textContent = board[row][col];
                if (window.game) { // Kiểm tra window.game tồn tại
                    cell.addEventListener('click', () => window.game.makeMove(row, col));
                } else {
                    console.error('window.game is not defined');
                }
                gameBoard.appendChild(cell);
            }
        }
    }

    static updateTurn(isYourTurn) {
        const turnElement = document.getElementById('turn');
        if (turnElement) {
            turnElement.textContent = isYourTurn ? 'Đến lượt bạn' : 'Đến lượt đối thủ';
        } else {
            console.error('Turn element not found');
        }
    }

    static highlightWinningCells(cells) {
        if (!cells || !Array.isArray(cells)) return;

        for (const [row, col] of cells) {
            const index = row * 15 + col;
            const cell = document.getElementsByClassName('cell')[index];
            if (cell) {
                cell.style.backgroundColor = '#ffeb3b'; // Màu vàng để highlight
            }
        }
    }
}