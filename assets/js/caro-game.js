class CaroGame {
    constructor() {
        this.board = [];
        this.currentPlayer = 'X';
        const typeParam = new URLSearchParams(window.location.search).get('type') || 'player-computer';
        this.gameMode = (typeParam === '2-players') ? 'two-players' : typeParam;
        this.gameId = null;
        this.playerSymbol = null;
        this.opponentSymbol = null;
        this.isYourTurn = false;
        this.socket = null;
        this.gameOver = false;
        this.lastBoardState = null;
    }

    init() {
        console.log('Game mode:', this.gameMode);
        this.initBoard();
        if (this.gameMode === 'two-players') {
            this.initSocket();
        } else {
            this.isYourTurn = true;
        }
    }

    initBoard() {
        console.log('Initializing board...');
        this.board = Array(15).fill().map(() => Array(15).fill(''));
        this.lastBoardState = JSON.stringify(this.board);
        CaroUI.renderBoard(this.board);
    }

    initSocket() {
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const host = window.location.hostname;
        const wsUrl = `${protocol}//${host}:8080`;
        this.connectWebSocket(wsUrl);
    }

    connectWebSocket(url) {
        this.socket = new WebSocket(url);

        this.socket.onopen = () => {
            console.log('WebSocket connected');
            this.socket.send(JSON.stringify({
                type: 'join'
            }));
        };

        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            switch (data.type) {
                case 'waiting':
                    CaroUI.showMessage('Đang chờ đối thủ...');
                    this.gameId = data.gameId;
                    break;
                case 'start':
                    this.gameId = data.gameId;
                    this.playerSymbol = data.symbol;
                    this.opponentSymbol = data.opponentSymbol;
                    this.board = data.board;
                    this.isYourTurn = data.isYourTurn;
                    CaroUI.showMessage('Đang chơi...');
                    this.lastBoardState = JSON.stringify(this.board);
                    CaroUI.renderBoard(this.board);
                    break;
                case 'move':
                    console.log('Receiving move:', data);
                    this.board = data.board;
                    this.isYourTurn = data.currentPlayer === this.playerSymbol;
                    this.lastBoardState = JSON.stringify(this.board);
                    CaroUI.renderBoard(this.board);
                    if (data.isWin) {
                        this.gameOver = true;
                        CaroUI.highlightWinningCells(data.winningCells);
                        CaroUI.showMessage(`${data.symbol} thắng!`);
                    } else if (data.isDraw) {
                        this.gameOver = true;
                        CaroUI.showMessage('Hòa!');
                    }
                    break;
                case 'your_turn':
                    this.board = data.board;
                    const newBoardState = JSON.stringify(this.board);
                    console.log('Received your_turn - Last:', this.lastBoardState, 'Current:', newBoardState);
                    if (newBoardState !== this.lastBoardState) {
                        this.isYourTurn = true;
                        this.lastBoardState = newBoardState;
                        console.log('Board state changed, rendering board...');
                        CaroUI.renderBoard(this.board);
                    } else {
                        console.log('Board state unchanged, skipping render.');
                    }
                    break;
                case 'surrender':
                    this.gameOver = true;
                    CaroUI.showMessage('Đối thủ đã đầu hàng. Bạn thắng!');
                    break;
                case 'opponent_disconnected':
                    this.gameOver = true;
                    CaroUI.showMessage('Đối thủ đã rời game. Bạn thắng!');
                    break;
                case 'error':
                    CaroUI.showMessage(data.message);
                    break;
            }
        };

        this.socket.onclose = () => {
            console.log('WebSocket disconnected');
            if (!this.gameOver) {
                CaroUI.showMessage('Mất kết nối với server.');
            }
        };

        this.socket.onerror = (error) => {
            console.error('WebSocket error:', error);
            CaroUI.showMessage('Không thể kết nối đến server.');
        };
    }

    async makeMove(row, col) {
        if (this.gameOver || !this.isYourTurn || this.board[row][col] !== '') {
            return;
        }

        if (this.gameMode === 'two-players') {
            this.handleTwoPlayerMove(row, col);
        } else {
            await this.handlePlayerComputerMove(row, col);
        }
    }

    handleTwoPlayerMove(row, col) {
        this.board[row][col] = this.playerSymbol;
        this.lastBoardState = JSON.stringify(this.board);
        CaroUI.renderBoard(this.board);
        this.isYourTurn = false;

        console.log('Sending move:', { row, col, symbol: this.playerSymbol });
        this.socket.send(JSON.stringify({
            type: 'move',
            gameId: this.gameId,
            row: row,
            col: col,
            symbol: this.playerSymbol
        }));
    }

    async handlePlayerComputerMove(row, col) {
        this.board[row][col] = this.currentPlayer;
        this.lastBoardState = JSON.stringify(this.board);
        CaroUI.renderBoard(this.board);

        if (this.checkWin(row, col, this.currentPlayer)) {
            this.gameOver = true;
            CaroUI.showMessage('Bạn thắng!');
            return;
        }

        if (this.isBoardFull()) {
            this.gameOver = true;
            CaroUI.showMessage('Hòa!');
            return;
        }

        this.currentPlayer = 'O';
        this.isYourTurn = false;

        try {
            const response = await fetch('/Project/api/caro-state.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    board: this.board,
                    player: this.currentPlayer
                })
            });

            const data = await response.json();
            if (data.success && data.move) {
                const { row: botRow, col: botCol } = data.move;
                this.board[botRow][botCol] = this.currentPlayer;
                this.lastBoardState = JSON.stringify(this.board);
                CaroUI.renderBoard(this.board);

                if (this.checkWin(botRow, botCol, this.currentPlayer)) {
                    this.gameOver = true;
                    CaroUI.showMessage('Máy thắng!');
                    return;
                }

                if (this.isBoardFull()) {
                    this.gameOver = true;
                    CaroUI.showMessage('Hòa!');
                    return;
                }
            } else {
                CaroUI.showMessage('Máy không thể đánh. Có lỗi xảy ra.');
            }
        } catch (error) {
            console.error('Error calling API:', error);
            CaroUI.showMessage('Không thể kết nối đến API.');
        }

        this.currentPlayer = 'X';
        this.isYourTurn = true;
    }

    checkWin(row, col, symbol) {
        const directions = [
            [1, 0],  // Hàng ngang
            [0, 1],  // Cột dọc
            [1, 1],  // Đường chéo chính
            [1, -1]  // Đường chéo phụ
        ];

        for (const [dr, dc] of directions) {
            let count = 1;
            const winningCells = [[row, col]];

            for (let i = 1; i <= 4; i++) {
                const r = row + i * dr;
                const c = col + i * dc;
                if (r < 0 || r >= 15 || c < 0 || c >= 15 || this.board[r][c] !== symbol) {
                    break;
                }
                count++;
                winningCells.push([r, c]);
            }

            for (let i = 1; i <= 4; i++) {
                const r = row - i * dr;
                const c = col - i * dc;
                if (r < 0 || r >= 15 || c < 0 || c >= 15 || this.board[r][c] !== symbol) {
                    break;
                }
                count++;
                winningCells.push([r, c]);
            }

            if (count >= 5) {
                CaroUI.highlightWinningCells(winningCells);
                return true;
            }
        }
        return false;
    }

    isBoardFull() {
        return this.board.every(row => row.every(cell => cell !== ''));
    }

    surrender() {
        if (this.gameMode !== 'two-players' || this.gameOver) {
            return;
        }

        this.gameOver = true;
        this.socket.send(JSON.stringify({
            type: 'surrender',
            gameId: this.gameId
        }));
        CaroUI.showMessage('Bạn đã đầu hàng.');
    }

    reset() {
        this.board = Array(15).fill().map(() => Array(15).fill(''));
        this.currentPlayer = 'X';
        this.gameOver = false;
        this.lastBoardState = JSON.stringify(this.board);
        CaroUI.renderBoard(this.board);

        if (this.gameMode === 'two-players') {
            if (this.socket && this.socket.readyState === WebSocket.OPEN) {
                this.socket.close();
            }
            this.initSocket();
        } else {
            this.isYourTurn = true;
            CaroUI.showMessage('');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const game = new CaroGame();
    window.game = game;
    game.init();

    const surrenderButton = document.getElementById('surrender-button');
    if (surrenderButton) {
        surrenderButton.addEventListener('click', () => {
            game.surrender();
        });
    } else {
        console.error('Surrender button not found. Please check if the element with id="surrender-button" exists in the DOM.');
    }
});