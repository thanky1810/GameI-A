<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;


session_start();
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
        $this->logEnvironmentVariables();
    }

    protected function logEnvironmentVariables()
    {
        echo "DB_SERVER: " . ($_ENV['DB_SERVER'] ?? 'not set') . "\n";
        echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'not set') . "\n";
        echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'not set') . "\n";
        echo "DB_PASS: " . ($_ENV['DB_PASS'] ? '*****' : 'not set') . "\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        parse_str($conn->httpRequest->getUri()->getQuery(), $query);

        if (empty($query['userId'])) {
            echo "Connection rejected: Missing userId\n";
            $conn->close();
            return;
        }

        $this->clients->attach($conn);
        $conn->userData = [
            'userId' => $query['userId'],
            'username' => $query['username'] ?? 'Anonymous_' . $conn->resourceId
        ];

        echo "New authenticated connection! ({$conn->resourceId}) User: {$conn->userData['userId']}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Message from {$from->resourceId}: {$msg}\n";

        $data = json_decode($msg, true);
        if (!$data) {
            echo "Invalid JSON data\n";
            return;
        }

        echo "Processing message type: {$data['type']}\n";

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
            case 'ping':
                $from->send(json_encode(['type' => 'pong']));
                break;
            default:
                echo "Unknown message type: {$data['type']}\n";
        }
    }

    protected function handleJoin(ConnectionInterface $from)
    {
        $userId = $from->userData['userId'] ?? 'guest';
        echo "Player {$from->resourceId} (UserID: {$userId}) requesting to join game\n";

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

            echo "Game {$gameId} started between:\n";
            echo "- Player1: {$game['player1']->resourceId} (UserID: " . ($game['player1']->userData['userId'] ?? 'guest') . ")\n";
            echo "- Player2: {$from->resourceId} (UserID: {$userId})\n";

            // Notify player 1 (X)
            $game['player1']->send(json_encode([
                'type' => 'start',
                'gameId' => $gameId,
                'symbol' => 'X',
                'opponentSymbol' => 'O',
                'opponentId' => $userId,
                'opponentName' => $from->userData['username'] ?? 'Player 2',
                'board' => $this->games[$gameId]['board'],
                'isYourTurn' => true
            ]));

            // Notify player 2 (O)
            $from->send(json_encode([
                'type' => 'start',
                'gameId' => $gameId,
                'symbol' => 'O',
                'opponentSymbol' => 'X',
                'opponentId' => $game['player1']->userData['userId'] ?? 'guest',
                'opponentName' => $game['player1']->userData['username'] ?? 'Player 1',
                'board' => $this->games[$gameId]['board'],
                'isYourTurn' => false
            ]));
        } else {
            $gameId = uniqid();
            $this->waitingGames[$gameId] = [
                'player1' => $from
            ];

            echo "Player {$from->resourceId} (UserID: {$userId}) created game {$gameId} (waiting for opponent)\n";

            $from->send(json_encode([
                'type' => 'waiting',
                'gameId' => $gameId
            ]));
        }
    }

    protected function handleMove(ConnectionInterface $from, $data)
    {
        $gameId = $data['gameId'];
        $userId = $from->userData['userId'] ?? 'guest';

        echo "Move request from {$from->resourceId} (UserID: {$userId}) in game {$gameId}\n";

        if (!isset($this->games[$gameId])) {
            echo "Game {$gameId} not found\n";
            return;
        }

        $game = $this->games[$gameId];
        $symbol = ($from === $game['player1']) ? 'X' : 'O';

        if ($game['currentPlayer'] !== $symbol) {
            echo "Not player's turn (current: {$game['currentPlayer']}, player: {$symbol})\n";
            return;
        }

        $row = $data['row'];
        $col = $data['col'];

        if ($row < 0 || $row >= 15 || $col < 0 || $col >= 15 || $game['board'][$row][$col] !== '') {
            echo "Invalid move position ({$row}, {$col})\n";
            return;
        }

        // Make the move
        $game['board'][$row][$col] = $symbol;
        $nextPlayer = ($symbol === 'X') ? 'O' : 'X';
        $game['currentPlayer'] = $nextPlayer;

        // Check game status
        $isWin = $this->checkWin($game['board'], $row, $col, $symbol);
        $isDraw = !$isWin && $this->isBoardFull($game['board']);
        $winningCells = $isWin ? $this->getWinningCells($game['board'], $row, $col, $symbol) : [];

        // Save game state
        $this->games[$gameId] = $game;

        // Prepare move message
        $message = [
            'type' => 'move',
            'gameId' => $gameId,
            'row' => $row,
            'col' => $col,
            'board' => $game['board'],
            'currentPlayer' => $nextPlayer,
            'symbol' => $symbol,
            'isWin' => $isWin,
            'isDraw' => $isDraw,
            'winningCells' => $winningCells,
            'playerId' => $userId
        ];

        // Send move to both players
        echo "Sending move to both players:\n" . json_encode($message, JSON_PRETTY_PRINT) . "\n";
        $game['player1']->send(json_encode($message));
        $game['player2']->send(json_encode($message));

        // Update database if game ended
        if ($isWin || $isDraw) {
            $this->updateGameResult($game, $isWin, $symbol);
            unset($this->games[$gameId]);
            echo "Game {$gameId} ended - " . ($isWin ? "Player {$symbol} won" : "Draw") . "\n";
        }
    }

    protected function handleSurrender(ConnectionInterface $from, $data)
    {
        $gameId = $data['gameId'];
        $userId = $from->userData['userId'] ?? 'guest';

        echo "Surrender request from {$from->resourceId} (UserID: {$userId}) in game {$gameId}\n";

        if (!isset($this->games[$gameId])) {
            echo "Game {$gameId} not found\n";
            return;
        }

        $game = $this->games[$gameId];
        $opponent = ($from === $game['player1']) ? $game['player2'] : $game['player1'];

        // Update scores
        $winnerSymbol = ($from === $game['player1']) ? 'O' : 'X';
        $this->updateGameResult($game, true, $winnerSymbol);

        // Notify opponent
        $opponent->send(json_encode([
            'type' => 'surrender',
            'gameId' => $gameId,
            'playerId' => $userId
        ]));

        unset($this->games[$gameId]);
        echo "Game {$gameId} ended by surrender\n";
    }

    protected function checkWin($board, $row, $col, $symbol)
    {
        $directions = [
            [1, 0],  // Horizontal
            [0, 1],  // Vertical
            [1, 1],  // Diagonal down-right
            [1, -1]  // Diagonal down-left
        ];

        foreach ($directions as $dir) {
            $count = 1;
            $dr = $dir[0];
            $dc = $dir[1];

            // Check in positive direction
            for ($i = 1; $i <= 4; $i++) {
                $r = $row + $i * $dr;
                $c = $col + $i * $dc;
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) break;
                $count++;
            }

            // Check in negative direction
            for ($i = 1; $i <= 4; $i++) {
                $r = $row - $i * $dr;
                $c = $col - $i * $dc;
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) break;
                $count++;
            }

            if ($count >= 5) {
                echo "Win detected for {$symbol} at ({$row}, {$col}) direction: [" . implode(',', $dir) . "]\n";
                return true;
            }
        }

        return false;
    }

    protected function getWinningCells($board, $row, $col, $symbol)
    {
        $directions = [
            [1, 0],
            [0, 1],
            [1, 1],
            [1, -1]
        ];

        foreach ($directions as $dir) {
            $cells = [[$row, $col]];
            $dr = $dir[0];
            $dc = $dir[1];

            // Check in positive direction
            for ($i = 1; $i <= 4; $i++) {
                $r = $row + $i * $dr;
                $c = $col + $i * $dc;
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) break;
                $cells[] = [$r, $c];
            }

            // Check in negative direction
            for ($i = 1; $i <= 4; $i++) {
                $r = $row - $i * $dr;
                $c = $col - $i * $dc;
                if ($r < 0 || $r >= 15 || $c < 0 || $c >= 15 || $board[$r][$c] !== $symbol) break;
                $cells[] = [$r, $c];
            }

            if (count($cells) >= 5) {
                echo "Winning cells found: " . json_encode($cells) . "\n";
                return $cells;
            }
        }

        return [];
    }

    protected function updateGameResult($game, $isWin, $symbol)
    {
        $player1Id = $game['player1']->userData['userId'] ?? null;
        $player2Id = $game['player2']->userData['userId'] ?? null;

        echo "Updating game result:\n";
        echo "- Player1 ID: " . ($player1Id ?? 'guest') . "\n";
        echo "- Player2 ID: " . ($player2Id ?? 'guest') . "\n";
        echo "- Result: " . ($isWin ? "Win for {$symbol}" : "Draw") . "\n";

        if ($isWin) {
            $winner = ($symbol === 'X') ? $player1Id : $player2Id;
            $loser = ($symbol === 'X') ? $player2Id : $player1Id;

            if ($winner) {
                $this->updateScore($winner, 5, 1); // +5 points, +1 win
            }
            if ($loser) {
                $this->updateScore($loser, 0, 0); // No points
            }
        } else {
            // Draw - both players get points
            if ($player1Id) {
                $this->updateScore($player1Id, 2, 0); // +2 points
            }
            if ($player2Id) {
                $this->updateScore($player2Id, 2, 0); // +2 points
            }
        }
    }

    protected function updateScore($userId, $score, $win)
    {
        if (!$userId || $userId === 'guest') {
            echo "Skipping score update for guest user\n";
            return;
        }

        try {
            echo "Updating score for user {$userId}: +{$score} points, +{$win} wins\n";

            $host = $_ENV['DB_SERVER'];
            $dbname = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];

            $conn = new mysqli($host, $username, $password, $dbname);
            if ($conn->connect_error) {
                echo "Database connection failed: " . $conn->connect_error . "\n";
                return;
            }

            $query = "UPDATE user SET 
                      Score = Score + ?, 
                      sumWin = sumWin + ?, 
                      sumScore = sumScore + ? 
                      WHERE ID = ?";

            echo "Executing query: {$query} with params: {$score}, {$win}, {$score}, {$userId}\n";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiii", $score, $win, $score, $userId);
            $stmt->execute();

            echo "Rows affected: " . $stmt->affected_rows . "\n";

            $stmt->close();
            $conn->close();

            echo "Score updated successfully for user {$userId}\n";
        } catch (Exception $e) {
            echo "Error updating score: " . $e->getMessage() . "\n";
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
        $userId = $conn->userData['userId'] ?? 'guest';
        echo "Connection closed! ({$conn->resourceId}, UserID: {$userId})\n";

        $this->clients->detach($conn);

        // Check active games
        foreach ($this->games as $gameId => $game) {
            if ($game['player1'] === $conn || $game['player2'] === $conn) {
                $opponent = ($game['player1'] === $conn) ? $game['player2'] : $game['player1'];

                // Update score - opponent wins by disconnect
                $winnerSymbol = ($game['player1'] === $conn) ? 'O' : 'X';
                $this->updateGameResult($game, true, $winnerSymbol);

                // Notify opponent
                $opponent->send(json_encode([
                    'type' => 'opponent_disconnected',
                    'gameId' => $gameId
                ]));

                unset($this->games[$gameId]);
                echo "Game {$gameId} ended: Player {$conn->resourceId} disconnected\n";
                break;
            }
        }

        // Check waiting games
        foreach ($this->waitingGames as $gameId => $game) {
            if ($game['player1'] === $conn) {
                unset($this->waitingGames[$gameId]);
                echo "Waiting game {$gameId} removed: Player {$conn->resourceId} disconnected\n";
                break;
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $userId = $conn->userData['userId'] ?? 'guest';
        echo "Error for connection {$conn->resourceId} (UserID: {$userId}): {$e->getMessage()}\n";
        $conn->close();
    }
}

// Start server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new CaroWebSocket()
        )
    ),
    8080,
    '0.0.0.0'
);

echo "WebSocket server running on ws://0.0.0.0:8080\n";
$server->run();
