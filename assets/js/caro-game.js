class CaroGame {
    constructor(ui) {
        this.ui = ui;
        this.gameType = ui.gameType;
        this.gameId = null;
        this.socket = null;
        this.mySymbol = X;
        this.opponentSymbol = O;

        // Khởi tạo game
        this.initGame();
    }

    initGame() {
        // Gửi yêu cầu tạo game mới
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
            .then(response => response.json())
            .then(data => {
                this.gameId = data.gameId;
                this.mySymbol = data.symbol;
                this.opponentSymbol = data.opponentSymbol;
                this.ui.mySymbol = this.mySymbol;
                this.ui.opponentSymbol = this.opponentSymbol;

                // Cập nhật trạng thái UI
                this.ui.updateStatus("Trận đấu bắt đầu");
                this.ui.updatePlayerTurn(this.mySymbol === X);

                // Khởi tạo websocket nếu chơi 2 người
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
        this.socket = new WebSocket(`${protocol}//${host}:8080`);

        // Hiển thị trạng thái kết nối
        const connectionStatus = document.getElementById('connectionStatus');

        this.socket.onopen = () => {
            console.log('Connected to WebSocket server');
            connectionStatus.textContent = 'Đã kết nối';
            connectionStatus.className = 'connection-status connected';

            // Gửi thông báo tham gia game
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
            connectionStatus.textContent = 'Mất kết nối';
            connectionStatus.className = 'connection-status disconnected';
            this.ui.updateStatus('Mất kết nối với máy chủ');
        };

        this.socket.onerror = (error) => {
            console.error('WebSocket error:', error);
            connectionStatus.textContent = 'Lỗi kết nối';
            connectionStatus.className = 'connection-status disconnected';
            this.ui.updateStatus('Lỗi kết nối');
        };
    }

    handleServerMessage(message) {
        switch (message.type) {
            case 'waiting':
                this.ui.updateStatus('Đang chờ đối thủ...');
                break;

            case 'start':
                this.gameId = message.gameId;
                this.ui.updateStatus('Trận đấu bắt đầu');
                this.ui.updatePlayerTurn(this.mySymbol === X);
                break;

            case 'move':
                if (message.symbol !== this.mySymbol) {
                    const row = message.x;
                    const col = message.y;
                    this.ui.updateBoard(row, col, message.symbol);
                    this.ui.updatePlayerTurn(true);

                    // Kiểm tra thắng/thua
                    if (message.result === WIN) {
                        this.ui.endGame(false);
                    }
                }
                break;

            case 'end':
                const isWinner = message.winner === this.mySymbol;
                this.ui.endGame(isWinner);
                break;
        }
    }

    makeMove(row, col) {
        // Cập nhật UI trước
        this.ui.updateBoard(row, col, this.mySymbol);
        this.ui.updatePlayerTurn(false);

        if (this.gameType === TWO_PLAYER) {
            // Gửi nước đi qua WebSocket
            if (this.socket && this.socket.readyState === WebSocket.OPEN) {
                this.socket.send(JSON.stringify({
                    type: 'move',
                    gameId: this.gameId,
                    x: row,
                    y: col,
                    symbol: this.mySymbol
                }));
            }
        } else {
            // Gửi nước đi qua AJAX cho chế độ chơi với máy
            fetch('/Project/api/caro-move.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    gameId: this.gameId,
                    row: row,
                    col: col,
                    symbol: this.mySymbol
                })
            })
                .then(response => response.json())
                .then(data => {
                    // Kiểm tra kết quả nước đi của người chơi
                    if (data.result === WIN) {
                        this.ui.endGame(true);
                        return;
                    }

                    // Xử lý nước đi của máy
                    if (data.computerMove) {
                        setTimeout(() => {
                            const compRow = data.computerMove.row;
                            const compCol = data.computerMove.col;
                            this.ui.updateBoard(compRow, compCol, this.opponentSymbol);

                            // Kiểm tra kết quả nước đi của máy
                            if (data.computerResult === WIN) {
                                this.ui.endGame(false);
                            } else {
                                this.ui.updatePlayerTurn(true);
                            }
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('Error processing move:', error);
                });
        }
    }

    surrender() {
        if (this.gameType === TWO_PLAYER && this.socket && this.socket.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify({
                type: 'surrender',
                gameId: this.gameId,
                symbol: this.mySymbol
            }));
        } else {
            // Ghi nhận đầu hàng với server trong chế độ chơi với máy
            fetch('/Project/api/caro-move.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    gameId: this.gameId,
                    action: 'surrender',
                    symbol: this.mySymbol
                })
            })
                .then(response => response.json())
                .then(data => {
                    this.ui.endGame(false);
                });
        }
    }
}