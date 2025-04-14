<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

// Yêu cầu autoload của Composer (đảm bảo Ratchet đã được cài đặt qua Composer)
require dirname(__DIR__) . '/vendor/autoload.php';

// Class CaroWebSocket xử lý các sự kiện WebSocket cho game Cờ Caro
class CaroWebSocket implements MessageComponentInterface
{
    protected $clients; // Lưu trữ tất cả client kết nối
    protected $games;   // Lưu trữ trạng thái các game (gameId => [player1, player2, board, ...])

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->games = [];
        echo "WebSocket server initialized.\n";
    }

    // Khi có client mới kết nối
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    // Khi nhận được tin nhắn từ client
    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Parse tin nhắn từ client (dạng JSON)
        $data = json_decode($msg, true);
        if (!$data) {
            echo "Invalid message received from {$from->resourceId}\n";
            return;
        }

        // Xử lý các loại tin nhắn
        switch ($data['type']) {
            case 'join':
                $this->handleJoin($from, $data);
                break;
            case 'move':
                $this->handleMove($from, $data);
                break;
            case 'surrender':
                $this->handleSurrender($from, $data);
                break;
            default:
                echo "Unknown message type: {$data['type']} from {$from->resourceId}\n";
        }
    }

    // Khi client ngắt kết nối
    public function onClose(ConnectionInterface $conn)
    {
        // Kiểm tra xem client có đang tham gia game nào không
        foreach ($this->games as $gameId => $game) {
            if (isset($game['player1']) && $game['player1']->resourceId === $conn->resourceId) {
                // Người chơi 1 rời game
                if (isset($game['player2'])) {
                    $game['player2']->send(json_encode([
                        'type' => 'opponent_disconnected',
                        'message' => 'Đối thủ đã rời game. Bạn thắng!'
                    ]));
                }
                unset($this->games[$gameId]);
            } elseif (isset($game['player2']) && $game['player2']->resourceId === $conn->resourceId) {
                // Người chơi 2 rời game
                if (isset($game['player1'])) {
                    $game['player1']->send(json_encode([
                        'type' => 'opponent_disconnected',
                        'message' => 'Đối thủ đã rời game. Bạn thắng!'
                    ]));
                }
                unset($this->games[$gameId]);
            }
        }

        $this->clients->detach($conn);
        echo "Connection closed! ({$conn->resourceId})\n";
    }

    // Khi xảy ra lỗi
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    // Xử lý khi client tham gia game
    protected function handleJoin(ConnectionInterface $from, $data)
    {
        $gameId = $data['gameId'] ?? null;
        $symbol = $data['symbol'] ?? 'X';

        if (!$gameId) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Game ID không hợp lệ'
            ]));
            return;
        }

        // Nếu game chưa tồn tại, tạo game mới
        if (!isset($this->games[$gameId])) {
            $this->games[$gameId] = [
                'player1' => $from,
                'player1Symbol' => $symbol,
                'player2' => null,
                'player2Symbol' => ($symbol === 'X') ? 'O' : 'X',
                'board' => $this->initBoard(), // Khởi tạo bàn cờ 15x15
                'currentPlayer' => 'X' // Người chơi X đi trước
            ];

            $from->send(json_encode([
                'type' => 'waiting',
                'message' => 'Đang chờ đối thủ...'
            ]));
            echo "Player {$from->resourceId} joined game $gameId as $symbol (waiting for opponent)\n";
            return;
        }

        // Nếu game đã có 1 người chơi, thêm người chơi thứ 2
        $game = &$this->games[$gameId];
        if ($game['player2'] === null) {
            $game['player2'] = $from;
            echo "Player {$from->resourceId} joined game $gameId as {$game['player2Symbol']}\n";

            // Gửi thông báo bắt đầu game cho cả 2 người chơi
            $game['player1']->send(json_encode([
                'type' => 'start',
                'gameId' => $gameId,
                'symbol' => $game['player1Symbol'],
                'opponentSymbol' => $game['player2Symbol'],
                'board' => $game['board'],
                'isYourTurn' => true
            ]));

            $game['player2']->send(json_encode([
                'type' => 'start',
                'gameId' => $gameId,
                'symbol' => $game['player2Symbol'],
                'opponentSymbol' => $game['player1Symbol'],
                'board' => $game['board'],
                'isYourTurn' => false
            ]));

            echo "Game $gameId started!\n";
        } else {
            // Game đã đầy
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Game đã đầy. Vui lòng tạo game mới.'
            ]));
        }
    }

    // Xử lý nước đi của người chơi
    protected function handleMove(ConnectionInterface $from, $data)
    {
        $gameId = $data['gameId'] ?? null;
        $row = $data['row'] ?? -1;
        $col = $data['col'] ?? -1;
        $symbol = $data['symbol'] ?? null;

        if (!$gameId || !isset($this->games[$gameId]) || $row < 0 || $row >= 15 || $col < 0 || $col >= 15 || !$symbol) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Nước đi không hợp lệ'
            ]));
            return;
        }

        $game = &$this->games[$gameId];
        $isPlayer1 = $game['player1']->resourceId === $from->resourceId;
        $opponent = $isPlayer1 ? $game['player2'] : $game['player1'];

        // Kiểm tra xem có phải lượt của người chơi này không
        if ($game['currentPlayer'] !== $symbol) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Không phải lượt của bạn'
            ]));
            return;
        }

        // Kiểm tra ô đã được đánh chưa
        if ($game['board'][$row][$col] !== '') {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Ô này đã được đánh'
            ]));
            return;
        }

        // Cập nhật bàn cờ
        $game['board'][$row][$col] = $symbol;

        // Kiểm tra thắng
        $winningCells = $this->checkWin($game['board'], $row, $col, $symbol);
        $isWin = !empty($winningCells);

        // Gửi nước đi cho cả 2 người chơi
        $message = [
            'type' => 'move',
            'row' => $row,
            'col' => $col,
            'symbol' => $symbol,
            'isWin' => $isWin,
            'winningCells' => $winningCells
        ];

        $from->send(json_encode($message));
        $opponent->send(json_encode($message));

        if ($isWin) {
            // Kết thúc game
            unset($this->games[$gameId]);
            return;
        }

        // Chuyển lượt
        $game['currentPlayer'] = ($symbol === 'X') ? 'O' : 'X';
        $opponent->send(json_encode([
            'type' => 'your_turn',
            'board' => $game['board']
        ]));
    }

    // Xử lý khi người chơi đầu hàng
    protected function handleSurrender(ConnectionInterface $from, $data)
    {
        $gameId = $data['gameId'] ?? null;

        if (!$gameId || !isset($this->games[$gameId])) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Game không tồn tại'
            ]));
            return;
        }

        $game = &$this->games[$gameId];
        $isPlayer1 = $game['player1']->resourceId === $from->resourceId;
        $opponent = $isPlayer1 ? $game['player2'] : $game['player1'];

        // Gửi thông báo đầu hàng cho đối thủ
        $opponent->send(json_encode([
            'type' => 'surrender',
            'message' => 'Đối thủ đã đầu hàng. Bạn thắng!'
        ]));

        $from->send(json_encode([
            'type' => 'game_over',
            'message' => 'Bạn đã đầu hàng.'
        ]));

        // Xóa game
        unset($this->games[$gameId]);
    }

    // Khởi tạo bàn cờ 15x15
    protected function initBoard()
    {
        $board = [];
        for ($i = 0; $i < 15; $i++) {
            $board[$i] = [];
            for ($j = 0; $j < 15; $j++) {
                $board[$i][$j] = '';
            }
        }
        return $board;
    }

    // Kiểm tra điều kiện thắng
    protected function checkWin($board, $row, $col, $symbol)
    {
        $directions = [
            [1, 0],  // Hàng ngang
            [0, 1],  // Cột dọc
            [1, 1],  // Đường chéo chính
            [1, -1]  // Đường chéo phụ
        ];

        $winningCells = [];
        foreach ($directions as $dir) {
            $count = 1;
            $cells = [[$row, $col]];

            // Kiểm tra theo hướng dương
            for ($i = 1; $i <= 4; $i++) {
                $r = $row + $i * $dir[0];
                $c = $col + $i * $dir[1];
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) {
                    break;
                }
                $count++;
                $cells[] = [$r, $c];
            }

            // Kiểm tra theo hướng âm
            for ($i = 1; $i <= 4; $i++) {
                $r = $row - $i * $dir[0];
                $c = $col - $i * $dir[1];
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) {
                    break;
                }
                $count++;
                $cells[] = [$r, $c];
            }

            if ($count >= 5) {
                return $cells; // Trả về danh sách các ô thắng
            }
        }

        return []; // Không thắng
    }
}

// Khởi chạy WebSocket server trên cổng 8080
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new CaroWebSocket()
        )
    ),
    8080
);

echo "WebSocket server running on port 8080\n";
$server->run();
