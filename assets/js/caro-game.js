class CaroGame {
    constructor(ui) {
        this.ui = ui;
        this.gameType = ui.gameType;
        this.gameId = null;
        this.socket = null;
        this.mySymbol = X;
        this.opponentSymbol = O;
        this.board = [];
        this.boardSize = 15;

        this.initGame();
    }

    initGame() {
        for (let i = 0; i < this.boardSize; i++) {
            this.board[i] = [];
            for (let j = 0; j < this.boardSize; j++) {
                this.board[i][j] = EMPTY;
            }
        }

        fetch('/Project/api/caro-state.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'new_game',
                type: this.gameType
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                this.gameId = data.gameId;
                this.mySymbol = data.symbol;
                this.opponentSymbol = data.opponentSymbol;
                this.ui.mySymbol = this.mySymbol;
                this.ui.opponentSymbol = this.opponentSymbol;

                this.ui.updateStatus("Trận đấu bắt đầu");
                this.ui.updatePlayerTurn(this.mySymbol === X);

                // Chỉ khởi tạo WebSocket trong chế độ 2 người chơi
                if (this.gameType === TWO_PLAYER) {
                    this.initSocket();
                }
            })
            .catch(error => {
                console.error('Error starting game:', error);
                this.ui.updateStatus("Không thể kết nối với máy chủ");
            });
    }

    initSocket() {
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const host = window.location.hostname;
        this.connectWebSocket(`${protocol}//${host}:8080`);
    }

    connectWebSocket(url) {
        this.socket = new WebSocket(url);
        this.socket.onopen = () => {
            console.log('Connected to WebSocket server');
            this.socket.send(JSON.stringify({
                type: 'join',
                gameId: this.gameId,
                symbol: this.mySymbol
            }));
        };

        this.socket.onmessage = (event) => {
            const message = JSON.parse(event.data);
            this.handleServerMessage(message);
        };

        this.socket.onclose = () => {
            console.log('Disconnected from WebSocket server');
            this.ui.updateStatus('Mất kết nối với máy chủ. Đang thử kết nối lại...');
            setTimeout(() => this.connectWebSocket(url), 3000);
        };

        this.socket.onerror = (error) => {
            console.error('WebSocket error:', error);
        };
    }

    handleServerMessage(message) {
        if (message.type === 'move') {
            const { row, col, symbol } = message;
            this.board[row][col] = symbol;
            this.ui.updateCell(row, col, symbol);

            const winningCells = [];
            if (this.checkWin(row, col, symbol, winningCells)) {
                this.ui.highlightWinningCells(winningCells);
                this.ui.updateStatus(`Người chơi ${symbol} thắng!`);
                this.ui.endGame();
                return;
            }

            if (this.isBoardFull()) {
                this.ui.updateStatus("Hòa!");
                this.ui.endGame();
                return;
            }

            this.ui.updatePlayerTurn(symbol === this.opponentSymbol);
        } else if (message.type === 'surrender') {
            this.ui.updateStatus(`Đối thủ (${message.symbol}) đã đầu hàng. Bạn thắng!`);
            this.ui.endGame();
        }
    }

    makeMove(row, col) {
        if (this.board[row][col] !== EMPTY) return;

        if (this.gameType === TWO_PLAYER && this.socket) {
            this.socket.send(JSON.stringify({
                type: 'move',
                gameId: this.gameId,
                row: row,
                col: col,
                symbol: this.mySymbol
            }));
        } else {
            fetch('/Project/api/caro-move.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'move',
                    gameId: this.gameId,
                    type: this.gameType,
                    row: row,
                    col: col,
                    symbol: this.mySymbol
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text(); // Dùng text() để kiểm tra phản hồi trước
                })
                .then(text => {
                    console.log('Raw response:', text); // Debug phản hồi
                    return JSON.parse(text); // Sau đó parse JSON
                })
                .then(data => {
                    if (data.error) {
                        console.error('Lỗi từ server:', data.error);
                        this.ui.updateStatus(data.error);
                        return;
                    }

                    this.board[row][col] = this.mySymbol;
                    this.ui.updateCell(row, col, this.mySymbol);

                    if (data.result === 'WIN') {
                        this.ui.highlightWinningCells(data.winningCells);
                        this.ui.updateStatus('Bạn thắng!');
                        this.ui.endGame();
                        return;
                    }

                    if (data.result === 'DRAW') {
                        this.ui.updateStatus("Hòa!");
                        this.ui.endGame();
                        return;
                    }

                    if (data.computerMove) {
                        const { row: compRow, col: compCol } = data.computerMove;
                        this.board[compRow][compCol] = this.opponentSymbol;
                        this.ui.updateCell(compRow, compCol, this.opponentSymbol);

                        if (data.computerResult === 'WIN') {
                            this.ui.highlightWinningCells(data.winningCells);
                            this.ui.updateStatus('Máy tính thắng!');
                            this.ui.endGame();
                            return;
                        }
                    }

                    this.ui.updatePlayerTurn(true);
                })
                .catch(error => {
                    console.error('Lỗi khi gửi nước đi:', error);
                    this.ui.updateStatus("Không thể gửi nước đi");
                });
        }
    }

    surrender() {
        if (this.gameType === TWO_PLAYER && this.socket) {
            this.socket.send(JSON.stringify({
                type: 'surrender',
                gameId: this.gameId,
                symbol: this.mySymbol
            }));
        } else {
            fetch('/Project/api/caro-move.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'surrender',
                    gameId: this.gameId,
                    symbol: this.mySymbol
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Lỗi từ server:', data.error);
                        this.ui.updateStatus(data.error);
                        return;
                    }
                    this.ui.updateStatus('Bạn đã đầu hàng. Máy tính thắng!');
                    this.ui.endGame();
                })
                .catch(error => {
                    console.error('Lỗi khi đầu hàng:', error);
                    this.ui.updateStatus("Không thể gửi yêu cầu đầu hàng");
                });
        }
    }

    checkWin(row, col, symbol, winningCells) {
        const directions = [
            [0, 1],  // Ngang
            [1, 0],  // Dọc
            [1, 1],  // Chéo chính
            [1, -1]  // Chéo phụ
        ];

        for (const [dr, dc] of directions) {
            let count = 1;
            let cells = [[row, col]];

            for (let i = 1; i <= 4; i++) {
                const r = row + dr * i;
                const c = col + dc * i;
                if (r < 0 || r >= this.boardSize || c < 0 || c >= this.boardSize || this.board[r][c] !== symbol) break;
                count++;
                cells.push([r, c]);
            }

            for (let i = 1; i <= 4; i++) {
                const r = row - dr * i;
                const c = col - dc * i;
                if (r < 0 || r >= this.boardSize || c < 0 || c >= this.boardSize || this.board[r][c] !== symbol) break;
                count++;
                cells.push([r, c]);
            }

            if (count >= 5) {
                winningCells.push(...cells);
                return true;
            }
        }
        return false;
    }

    isBoardFull() {
        for (let i = 0; i < this.boardSize; i++) {
            for (let j = 0; j < this.boardSize; j++) {
                if (this.board[i][j] === EMPTY) return false;
            }
        }
        return true;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const gameType = urlParams.get('type') || 'player-computer';
    const ui = new CaroUI(gameType);
    const game = new CaroGame(ui);
});