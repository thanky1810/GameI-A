<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/includes/database.php';
require dirname(__DIR__) . '/vendor/autoload.php';

class CaroWebSocket implements MessageComponentInterface
{
    protected $clients;
    protected $games;
    protected $waitingGames;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        $this->games = [];
        $this->waitingGames = [];
        echo "WebSocket server initialized.\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        if (!$data) return;

        switch ($data['type']) {
            case 'join':
                $this->handleJoin($from);
                break;
            case 'move':
                $this->handleMove($from, $data);
                break;
            case 'surrender':
                $this->handleSurrender($from, $data);
                break;
        }
    }

    protected function handleJoin(ConnectionInterface $from)
    {
        if (!empty($this->waitingGames)) {
            $gameId = array_key_first($this->waitingGames);
            $game = $this->waitingGames[$gameId];
            unset($this->waitingGames[$gameId]);

            $game['player2'] = $from;
            $this->games[$gameId] = [
                'player1' => $game['player1'],
                'player2' => $from,
                'board' => array_fill(0, 15, array_fill(0, 15, '')),
                'currentPlayer' => 'X'
            ];

            echo "Player {$from->resourceId} joined game $gameId as O\n";
            echo "Game $gameId started!\n";

            $game['player1']->send(json_encode([
                'type' => 'start',
                'gameId' => $gameId,
                'symbol' => 'X',
                'opponentSymbol' => 'O',
                'board' => $this->games[$gameId]['board'],
                'isYourTurn' => true
            ]));

            $from->send(json_encode([
                'type' => 'start',
                'gameId' => $gameId,
                'symbol' => 'O',
                'opponentSymbol' => 'X',
                'board' => $this->games[$gameId]['board'],
                'isYourTurn' => false
            ]));
        } else {
            $gameId = uniqid();
            $this->waitingGames[$gameId] = [
                'player1' => $from
            ];

            echo "Player {$from->resourceId} joined game $gameId as X (waiting for opponent)\n";

            $from->send(json_encode([
                'type' => 'waiting',
                'gameId' => $gameId
            ]));
        }
    }

    protected function handleMove(ConnectionInterface $from, $data)
    {
        $gameId = $data['gameId'];
        if (!isset($this->games[$gameId])) return;

        $game = $this->games[$gameId];
        $symbol = ($from === $game['player1']) ? 'X' : 'O';
        if ($game['currentPlayer'] !== $symbol) return;

        $row = $data['row'];
        $col = $data['col'];
        if ($row < 0 || $row >= 15 || $col < 0 || $col >= 15 || $game['board'][$row][$col] !== '') return;

        $game['board'][$row][$col] = $symbol;
        $nextPlayer = ($symbol === 'X') ? 'O' : 'X';
        $game['currentPlayer'] = $nextPlayer;

        $isWin = $this->checkWin($game['board'], $row, $col, $symbol);
        $isDraw = $this->isBoardFull($game['board']);
        $winningCells = $isWin ? $this->getWinningCells($game['board'], $row, $col, $symbol) : [];

        // Lưu lại trạng thái game
        $this->games[$gameId] = $game;


        // Gửi thông tin nước đi cho cả hai người chơi
        $message = [
            'type' => 'move',
            'gameId' => $gameId,
            'board' => $game['board'],
            'currentPlayer' => $nextPlayer,
            'symbol' => $symbol,
            'isWin' => $isWin,
            'isDraw' => $isDraw,
            'winningCells' => $winningCells
        ];

        echo "Sending move to player1 ({$game['player1']->resourceId}): " . json_encode($message) . "\n";
        echo "Sending move to player2 ({$game['player2']->resourceId}): " . json_encode($message) . "\n";

        $game['player1']->send(json_encode($message));
        $game['player2']->send(json_encode($message));

        if ($isWin || $isDraw) {
            $this->updateGameResult($game, $isWin, $symbol); // Thêm dòng này
            unset($this->games[$gameId]);
        }
    }

    protected function handleSurrender(ConnectionInterface $from, $data)
    {
        $gameId = $data['gameId'];
        if (!isset($this->games[$gameId])) return;

        $game = $this->games[$gameId];
        $opponent = ($from === $game['player1']) ? $game['player2'] : $game['player1'];

        $opponent->send(json_encode([
            'type' => 'surrender',
            'gameId' => $gameId
        ]));

        unset($this->games[$gameId]);
    }

    protected function checkWin($board, $row, $col, $symbol)
    {
        $directions = [
            [1, 0],  // Hàng ngang
            [0, 1],  // Cột dọc
            [1, 1],  // Đường chéo chính
            [1, -1]  // Đường chéo phụ
        ];

        foreach ($directions as $dir) {
            $dr = $dir[0];
            $dc = $dir[1];
            $count = 1;

            for ($i = 1; $i <= 4; $i++) {
                $r = $row + $i * $dr;
                $c = $col + $i * $dc;
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) {
                    break;
                }
                $count++;
            }

            for ($i = 1; $i <= 4; $i++) {
                $r = $row - $i * $dr;
                $c = $col - $i * $dc;
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) {
                    break;
                }
                $count++;
            }

            if ($count >= 5) {
                return true;
            }
        }
        return false;
    }

    protected function getWinningCells($board, $row, $col, $symbol)
    {
        $directions = [
            [1, 0],  // Hàng ngang
            [0, 1],  // Cột dọc
            [1, 1],  // Đường chéo chính
            [1, -1]  // Đường chéo phụ
        ];

        foreach ($directions as $dir) {
            $dr = $dir[0];
            $dc = $dir[1];
            $count = 1;
            $cells = [[$row, $col]];

            for ($i = 1; $i <= 4; $i++) {
                $r = $row + $i * $dr;
                $c = $col + $i * $dc;
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) {
                    break;
                }
                $count++;
                $cells[] = [$r, $c];
            }

            for ($i = 1; $i <= 4; $i++) {
                $r = $row - $i * $dr;
                $c = $col - $i * $dc;
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) {
                    break;
                }
                $count++;
                $cells[] = [$r, $c];
            }

            if ($count >= 5) {
                return $cells;
            }
        }
        return [];
    }
    protected function updateGameResult($game, $isWin, $symbol)
    {
        if ($isWin) {
            $winner = ($symbol === 'X') ? $game['player1'] : $game['player2'];
            $loser = ($symbol === 'X') ? $game['player2'] : $game['player1'];

            if (isset($winner->userData['userId'])) {
                $this->updateScore($winner->userData['userId'], 5, 1); // +5 điểm, +1 win
            }
            if (isset($loser->userData['userId'])) {
                $this->updateScore($loser->userData['userId'], 0, 0); // Không cộng điểm
            }
        } else {
            // Hòa
            if (isset($game['player1']->userData['userId'])) {
                $this->updateScore($game['player1']->userData['userId'], 2, 0); // +2 điểm
            }
            if (isset($game['player2']->userData['userId'])) {
                $this->updateScore($game['player2']->userData['userId'], 2, 0); // +2 điểm
            }
        }
    }
    protected function updateScore($userId, $score, $win)
    {
        try {
            $host = $_ENV['DB_SERVER'];
            $dbname = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];

            $conn = new mysqli($host, $username, $password, $dbname);
            if ($conn->connect_error) {
                error_log("Database connection failed: " . $conn->connect_error);
                return;
            }

            $stmt = $conn->prepare("UPDATE user SET Score = Score + ?, sumWin = sumWin + ?, sumScore = sumScore + ? WHERE ID = ?");
            $stmt->bind_param("iiii", $score, $win, $score, $userId);
            $stmt->execute();
            $conn->close();
        } catch (Exception $e) {
            error_log("Error updating score: " . $e->getMessage());
        }
    }
    protected function isBoardFull($board)
    {
        foreach ($board as $row) {
            foreach ($row as $cell) {
                if ($cell === '') return false;
            }
        }
        return true;
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        foreach ($this->games as $gameId => $game) {
            if ($game['player1'] === $conn || $game['player2'] === $conn) {
                $opponent = ($game['player1'] === $conn) ? $game['player2'] : $game['player1'];
                $opponent->send(json_encode([
                    'type' => 'opponent_disconnected',
                    'gameId' => $gameId
                ]));
                unset($this->games[$gameId]);
                echo "Game $gameId ended: Player {$conn->resourceId} disconnected\n";
                break;
            }
        }

        foreach ($this->waitingGames as $gameId => $game) {
            if ($game['player1'] === $conn) {
                unset($this->waitingGames[$gameId]);
                echo "Game $gameId ended: Player {$conn->resourceId} disconnected while waiting\n";
                break;
            }
        }

        echo "Connection closed! ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new CaroWebSocket()
        )
    ),
    8080,
    '0.0.0.0'
);

echo "WebSocket server running on port 8080\n";
$server->run();
