<?php
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../game/CaroGame.php';

header('Content-Type: application/json');

// Tắt hiển thị lỗi và ghi lỗi vào log
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'D:/App/XAMPP/logs/php_error_log');


// Hàm trả về JSON và thoát
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

$gameId = $data['gameId'] ?? '';
$action = $data['action'] ?? 'move';

if (empty($gameId)) {
    sendResponse(['error' => 'Game không tồn tại'], 400);
}

if ($action === 'move') {
    $row = $data['row'] ?? -1;
    $col = $data['col'] ?? -1;
    $symbol = $data['symbol'] ?? '';
    $type = $data['type'] ?? 'player-computer';

    if ($row < 0 || $row >= 15 || $col < 0 || $col >= 15 || !in_array($symbol, ['X', 'O'])) {
        sendResponse(['error' => 'Nước đi không hợp lệ'], 400);
    }

    // PvE: Lấy trạng thái game từ session
    if ($type === 'player-computer') {
        if (!isset($_SESSION['caro_games']) || !isset($_SESSION['caro_games'][$gameId])) {
            sendResponse(['error' => 'Game không tồn tại trong session'], 400);
        }

        $game = &$_SESSION['caro_games'][$gameId];
        if ($game['board'][$row][$col] !== '' || $game['currentPlayer'] !== $symbol) {
            sendResponse(['error' => 'Nước đi không hợp lệ'], 400);
        }

        // Cập nhật nước đi của người chơi
        $game['board'][$row][$col] = $symbol;
        $caroGame = new CaroGame();
        $winningCells = [];
        $isWin = $caroGame->checkWin($game['board'], $row, $col, $symbol, $winningCells);
        $result = 'continue';
        $computerMove = null;
        $computerResult = 'continue';

        if ($isWin) {
            $game['status'] = 'ended';
            $game['winner'] = $symbol;
            $result = 'WIN';
            $debugMessages = ["Người chơi thắng, chuẩn bị lưu điểm..."];
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['ID'];
                $score = 5; // Cộng 5 điểm khi thắng
                $win = 1;

                try {
                    $conn->begin_transaction();

                    // Cập nhật điểm
                    $stmt = $conn->prepare("UPDATE user SET Score = Score + ?, sumWin = sumWin + ?, sumScore = sumScore + ? WHERE ID = ?");
                    $stmt->bind_param("iiii", $score, $win, $score, $userId);

                    if (!$stmt->execute()) {
                        throw new Exception("Execute failed: " . $stmt->error);
                    }

                    // Kiểm tra có cập nhật được không
                    if ($stmt->affected_rows === 0) {
                        throw new Exception("No rows affected - user may not exist");
                    }

                    $conn->commit();
                    $debugMessages[] = "Đã cập nhật điểm thành công";
                } catch (Exception $e) {
                    $conn->rollback();
                    $debugMessages[] = "Lỗi database: " . $e->getMessage();
                    error_log("Database error: " . $e->getMessage());
                }
            } else {
                $debugMessages[] = "Người dùng chưa đăng nhập, không lưu điểm.";
            }
        } else {
            $game['currentPlayer'] = $symbol === 'X' ? 'O' : 'X';
            if ($game['type'] === 'player-computer' && $game['status'] === 'active') {
                $computerMove = $caroGame->computerMove($game['board']);
                if ($computerMove) {
                    $compRow = $computerMove['row'];
                    $compCol = $computerMove['col'];
                    $game['board'][$compRow][$compCol] = 'O';
                    $compWin = $caroGame->checkWin($game['board'], $compRow, $compCol, 'O', $winningCells);
                    if ($compWin) {
                        $game['status'] = 'ended';
                        $game['winner'] = 'O';
                        $computerResult = 'WIN';
                        if (isset($_SESSION['user'])) {
                            $userId = $_SESSION['user']['ID'];
                            $score = 0; // Không cộng điểm khi thua
                            $win = 0;
                            try {
                                $stmt = $conn->prepare("UPDATE user SET Score = Score + ?, sumWin = sumWin + ?, sumScore = sumScore + ? WHERE ID = ?");
                                $stmt->bind_param("iiii", $score, $win, $score, $userId);
                                $stmt->execute();
                                $conn->commit();
                                error_log("Đã cập nhật bảng user (máy thắng)");
                            } catch (Exception $e) {
                                error_log("Lỗi cập nhật điểm: " . $e->getMessage());
                                sendResponse(['error' => 'Lỗi cập nhật điểm'], 500);
                            }
                        }
                    } else {
                        $game['currentPlayer'] = 'X';
                    }
                }
            }
        }

        // Kiểm tra hòa
        $isBoardFull = true;
        for ($i = 0; $i < 15; $i++) {
            for ($j = 0; $j < 15; $j++) {
                if ($game['board'][$i][$j] === '') {
                    $isBoardFull = false;
                    break 2;
                }
            }
        }

        if ($isBoardFull && $result !== 'WIN' && $computerResult !== 'WIN') {
            $game['status'] = 'ended';
            $game['winner'] = null;
            $result = 'DRAW';
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['ID'];
                $score = 5; // Cộng 5 điểm khi hòa
                $win = 0;
                try {
                    $stmt = $conn->prepare("UPDATE user SET Score = Score + ?, sumWin = sumWin + ?, sumScore = sumScore + ? WHERE ID = ?");
                    $stmt->bind_param("iiii", $score, $win, $score, $userId);
                    $stmt->execute();
                    $conn->commit();
                    error_log("Đã cập nhật bảng user (hòa)");
                } catch (Exception $e) {
                    error_log("Lỗi cập nhật điểm: " . $e->getMessage());
                    sendResponse(['error' => 'Lỗi cập nhật điểm'], 500);
                }
            }
        }

        sendResponse([
            'result' => $result,
            'board' => $game['board'],
            'currentPlayer' => $game['currentPlayer'],
            'computerMove' => $computerMove,
            'computerResult' => $computerResult,
            'winningCells' => $winningCells,
            'debug' => $debugMessages
        ]);
    }
} elseif ($action === 'surrender') {
    if (isset($_SESSION['caro_games']) && isset($_SESSION['caro_games'][$gameId])) {
        $game = &$_SESSION['caro_games'][$gameId];
        $game['status'] = 'ended';
        $game['winner'] = $data['symbol'] === 'X' ? 'O' : 'X';
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['ID'];
            $score = 0; // Không cộng điểm khi đầu hàng
            $win = 0;
            try {
                $stmt = $conn->prepare("UPDATE user SET Score = Score + ?, sumWin = sumWin + ?, sumScore = sumScore + ? WHERE ID = ?");
                $stmt->bind_param("iiii", $score, $win, $score, $userId);
                $stmt->execute();
                $conn->commit();
                error_log("Đã cập nhật bảng user (đầu hàng)");
            } catch (Exception $e) {
                error_log("Lỗi cập nhật điểm: " . $e->getMessage());
                sendResponse(['error' => 'Lỗi cập nhật điểm'], 500);
            }
        }
        sendResponse([
            'result' => 'surrender',
            'winner' => $game['winner']
        ]);
    } else {
        sendResponse(['error' => 'Game không tồn tại'], 400);
    }
} else {
    sendResponse(['error' => 'Invalid action'], 400);
}
