<?php
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../game/CaroGame.php';
header('Content-Type: application/json');

// Tắt hiển thị lỗi và ghi lỗi vào log
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'D:/App/XAMPP/logs/php_error_log');

function sendResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    sendResponse(['error' => 'Dữ liệu đầu vào không hợp lệ'], 400);
}

$action = $data['action'] ?? '';
$type = $data['type'] ?? 'player-computer';

if ($action === 'new_game') {
    $gameId = uniqid();
    $board = array_fill(0, 15, array_fill(0, 15, ''));
    $symbol = 'X';
    $opponentSymbol = 'O';

    if (!isset($_SESSION['caro_games'])) {
        $_SESSION['caro_games'] = [];
    }

    $_SESSION['caro_games'][$gameId] = [
        'type' => $type,
        'board' => $board,
        'currentPlayer' => 'X',
        'status' => 'active',
        'winner' => null
    ];

    sendResponse([
        'gameId' => $gameId,
        'symbol' => $symbol,
        'opponentSymbol' => $opponentSymbol
    ]);
} else {
    sendResponse(['error' => 'Invalid action'], 400);
}
