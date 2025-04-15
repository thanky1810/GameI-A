class CaroUI {
    static showMessage(message) {
        const messageElement = document.getElementById('message');
        if (messageElement) {
            messageElement.textContent = `Kết cục: ${message}`;
        } else {
            console.error('Message element not found');
        }
    }

    static renderBoard(board) {
        const gameBoard = document.getElementById('table_game');
        if (!gameBoard) {
            console.error('Game board element not found');
            return;
        }

        console.log('Rendering board:', board);
        gameBoard.innerHTML = '';
        for (let row = 0; row < 15; row++) {
            const tr = document.createElement('tr');
            for (let col = 0; col < 15; col++) {
                const td = document.createElement('td');
                td.className = 'td_game';
                const div = document.createElement('div');
                div.id = `${row}-${col}`;
                div.className = 'fixed';
                div.textContent = board[row][col];
                if (board[row][col] === 'X') {
                    div.classList.add('x');
                } else if (board[row][col] === 'O') {
                    div.classList.add('o');
                }
                if (window.game) {
                    div.addEventListener('click', () => window.game.makeMove(row, col));
                } else {
                    console.error('window.game is not defined');
                }
                td.appendChild(div);
                tr.appendChild(td);
            }
            gameBoard.appendChild(tr);
        }
        console.log('Board rendered successfully with moves:', board.flat().filter(cell => cell !== '').length);
    }

    static highlightWinningCells(cells) {
        if (!cells || !Array.isArray(cells)) return;

        const gameBoard = document.getElementById('table_game');
        if (!gameBoard) return;

        for (const [row, col] of cells) {
            const div = document.getElementById(`${row}-${col}`);
            if (div) {
                div.classList.add('winning-cell');
            }
        }
    }
}