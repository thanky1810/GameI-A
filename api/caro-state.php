<?php
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../game/CaroGame.php';

header('Content-Type: application/json');

// Nhận dữ liệu từ client
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? 'new_game';
$gameType = $data['type'] ?? 'player-computer';

// Khởi tạo game mới
if ($action === 'new_game') {
    $gameId = uniqid('game_');
    $symbol = 'X'; // Người chơi luôn là X trong PvE
    $opponentSymbol = 'O';
    
    // Tạo bàn cờ 15x15 với tất cả ô trống
    $board = array_fill(0, 15, array_fill(0, 15, ''));
    
    // Lưu trạng thái game vào session
    $_SESSION['caro_games'][$gameId] = [
        'id' => $gameId,
        'type' => $gameType,
        'board' => $board,
        'currentPlayer' => 'X',
        'winnner' => null,
        'status' => 'active',
        'startTime' => time()
    ];
    
    // Trả về thông tin game cho client
    echo json_encode([
        'gameId' => $gameId,
        'symbol' => $symbol,
        'opponentSymbol' => $opponentSymbol,
        'currentPlayer' => 'X',
        'status' => 'active'
    ]);
} else if ($action === 'get_state') {
    // Lấy trạng thái hiện tại của game từ session
    $gameId = $data['gameId'] ?? '';
    
    if (!empty($gameId) && isset($_SESSION['caro_games'][$gameId])) {
        $game = $_SESSION['caro_games'][$gameId];
        echo json_encode([
            'gameId' => $game['id'],
            'board' => $game['board'],
            'currentPlayer' => $game['currentPlayer'],
            'status' => $game['status'],
            'winner' => $game['winner'] ?? null
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Game không tồn tại']);
    }
}