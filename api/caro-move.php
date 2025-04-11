<?php
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../game/CaroGame.php';

header('Content-Type: application/json');

// Nhận dữ liệu từ client
$data = json_decode(file_get_contents('php://input'), true);
$gameId = $data['gameId'] ?? '';
$action = $data['action'] ?? 'move';

// Kiểm tra game có tồn tại không
if (empty($gameId) || !isset($_SESSION['caro_games'][$gameId])) {
    http_response_code(400);
    echo json_encode(['error' => 'Game không tồn tại']);
    exit;
}

$game = &$_SESSION['caro_games'][$gameId];

// Xử lý đầu hàng
if ($action === 'surrender') {
    $game['status'] = 'ended';
    $game['winner'] = $data['symbol'] === 'X' ? 'O' : 'X';
    
    // Lưu kết quả vào database
    if (isset($_SESSION['user'])) {
        $userId = $_SESSION['user']['ID'];
        $gameType = 1; // ID of Caro game in database
        $win = 0; // Player surrendered
        $score = 0;
        
        $stmt = $conn->prepare("INSERT INTO GameHistory (userId, gameID, score, win) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $userId, $gameType, $score, $win);
        $stmt->execute();
    }
    
    echo json_encode([
        'result' => 'surrender',
        'winner' => $game['winner']
    ]);
    exit;
}

// Xử lý nước đi của người chơi
if ($action === 'move') {
    $row = $data['row'] ?? -1;
    $col = $data['col'] ?? -1;
    $symbol = $data['symbol'] ?? '';
    
    // Kiểm tra nước đi hợp lệ
    if ($row < 0 || $row >= 15 || $col < 0 || $col >= 15 || $game['board'][$row][$col] !== '' || $game['currentPlayer'] !== $symbol) {
        http_response_code(400);
        echo json_encode(['error' => 'Nước đi không hợp lệ']);
        exit;
    }
    
    // Cập nhật bàn cờ
    $game['board'][$row][$col] = $symbol;
    
    // Kiểm tra thắng/thua
    $caroGame = new CaroGame();
    $isWin = $caroGame->checkWin($game['board'], $row, $col, $symbol);
    
    $result = 'continue';
    $computerMove = null;
    $computerResult = 'continue';
    
    if ($isWin) {
        $game['status'] = 'ended';
        $game['winner'] = $symbol;
        $result = 'WIN';
        
        // Lưu kết quả vào database nếu người chơi thắng
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['ID'];
            $gameType = 1; // ID of Caro game in database
            $win = 1;
            $score = 10;
            
            $stmt = $conn->prepare("INSERT INTO GameHistory (userId, gameID, score, win) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $userId, $gameType, $score, $win);
            $stmt->execute();
            
            // Cập nhật điểm cho người chơi
            $stmt = $conn->prepare("UPDATE User SET Score = Score + ?, sumWin = sumWin + ?, sumScore = sumScore + ? WHERE ID = ?");
            $stmt->bind_param("iiis", $score, $win, $score, $userId);
            $stmt->execute();
        }
    } else {
        // Đổi lượt chơi
        $game['currentPlayer'] = $symbol === 'X' ? 'O' : 'X';
        
        // Nếu đang chơi với máy, tạo nước đi của máy
        if ($game['type'] === 'player-computer' && $game['status'] === 'active') {
            $computerMove = $caroGame->computerMove($game['board']);
            if ($computerMove) {
                $compRow = $computerMove['row'];
                $compCol = $computerMove['col'];
                $game['board'][$compRow][$compCol] = 'O';
                
                // Kiểm tra máy có thắng không
                $compWin = $caroGame->checkWin($game['board'], $compRow, $compCol, 'O');
                if ($compWin) {
                    $game['status'] = 'ended';
                    $game['winner'] = 'O';
                    $computerResult = 'WIN';
                    
                    // Lưu kết quả thua vào database
                    if (isset($_SESSION['user'])) {
                        $userId = $_SESSION['user']['ID'];
                        $gameType = 1; // ID of Caro game in database
                        $win = 0;
                        $score = 0;
                        
                        $stmt = $conn->prepare("INSERT INTO GameHistory (userId, gameID, score, win) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("iiis", $userId, $gameType, $score, $win);
                        $stmt->execute();
                    }
                } else {
                    // Đổi lượt chơi về người chơi
                    $game['currentPlayer'] = 'X';
                }
            }
        }
    }
    
    // Trả về kết quả cho client
    echo json_encode([
        'result' => $result,
        'board' => $game['board'],
        'currentPlayer' => $game['currentPlayer'],
        'computerMove' => $computerMove,
        'computerResult' => $computerResult
    ]);
}