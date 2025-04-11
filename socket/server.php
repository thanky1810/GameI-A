<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../game/CaroGame.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class CaroWebSocket implements MessageComponentInterface
{
    protected $clients;
    protected $games = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        $gameId = $data['gameId'] ?? null;

        switch ($data['type']) {
            case 'join':
                // Xử lý khi người chơi tham gia game
                if (!isset($this->games[$gameId])) {
                    $this->games[$gameId] = [
                        'players' => [],
                        'board' => array_fill(0, 15, array_fill(0, 15, '')),
                        'currentPlayer' => 'X',
                        'status' => 'waiting'
                    ];
                }

                $this->games[$gameId]['players'][$data['symbol']] = $from;
                $from->send(json_encode(['type' => 'joined', 'symbol' => $data['symbol']]));

                // Kiểm tra nếu đã đủ 2 người chơi
                if (count($this->games[$gameId]['players']) === 2) {
                    $this->games[$gameId]['status'] = 'active';

                    // Thông báo cho cả 2 người chơi
                    foreach ($this->games[$gameId]['players'] as $symbol => $client) {
                        $client->send(json_encode([
                            'type' => 'start',
                            'gameId' => $gameId,
                            'symbol' => $symbol,
                            'opponentSymbol' => $symbol === 'X' ? 'O' : 'X',
                            'currentPlayer' => 'X'
                        ]));
                    }
                } else {
                    $from->send(json_encode(['type' => 'waiting']));
                }
                break;

            case 'move':
                // Xử lý nước đi
                if (
                    $this->games[$gameId]['status'] !== 'active' ||
                    $this->games[$gameId]['currentPlayer'] !== $data['symbol']
                ) {
                    return;
                }

                $row = $data['x'];
                $col = $data['y'];
                $symbol = $data['symbol'];

                // Kiểm tra nước đi hợp lệ
                if (
                    $row < 0 || $row >= 15 || $col < 0 || $col >= 15 ||
                    $this->games[$gameId]['board'][$row][$col] !== ''
                ) {
                    return;
                }

                // Cập nhật bàn cờ
                $this->games[$gameId]['board'][$row][$col] = $symbol;
                $this->games[$gameId]['currentPlayer'] = $symbol === 'X' ? 'O' : 'X';

                // Kiểm tra thắng/thua
                $caroGame = new CaroGame();
                $isWin = $caroGame->checkWin($this->games[$gameId]['board'], $row, $col, $symbol);

                // Gửi thông báo cho cả 2 người chơi
                foreach ($this->games[$gameId]['players'] as $playerSymbol => $client) {
                    $client->send(json_encode([
                        'type' => 'move',
                        'x' => $row,
                        'y' => $col,
                        'symbol' => $symbol,
                        'currentPlayer' => $this->games[$gameId]['currentPlayer'],
                        'result' => $isWin ? 'WIN' : 'continue'
                    ]));
                }

                // Kết thúc game nếu có người thắng
                if ($isWin) {
                    $this->games[$gameId]['status'] = 'ended';
                    $winnerMsg = json_encode([
                        'type' => 'end',
                        'winner' => $symbol
                    ]);
                    foreach ($this->games[$gameId]['players'] as $client) {
                        $client->send($winnerMsg);
                    }
                }
                break;

            case 'surrender':
                // Xử lý đầu hàng
                $this->games[$gameId]['status'] = 'ended';
                $winner = $data['symbol'] === 'X' ? 'O' : 'X';

                $surrenderMsg = json_encode([
                    'type' => 'end',
                    'winner' => $winner,
                    'reason' => 'surrender'
                ]);

                foreach ($this->games[$gameId]['players'] as $client) {
                    $client->send($surrenderMsg);
                }
                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";

        // Xử lý khi người chơi rời game
        foreach ($this->games as $gameId => $game) {
            foreach ($game['players'] as $symbol => $client) {
                if ($client === $conn) {
                    // Thông báo cho người chơi còn lại
                    $otherPlayerSymbol = $symbol === 'X' ? 'O' : 'X';
                    if (isset($game['players'][$otherPlayerSymbol])) {
                        $game['players'][$otherPlayerSymbol]->send(json_encode([
                            'type' => 'end',
                            'winner' => $otherPlayerSymbol,
                            'reason' => 'opponent_disconnected'
                        ]));
                    }

                    // Xóa game
                    unset($this->games[$gameId]);
                    break 2;
                }
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Khởi chạy WebSocket server
$server = IoServer::factory(
    new WsServer(new CaroWebSocket()),
    8080
);

echo "WebSocket server running on port 8080\n";
$server->run();
