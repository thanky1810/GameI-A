<?php
session_start();
require_once __DIR__ . '/../includes/database.php';
header('Content-Type: application/json');

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    sendResponse(['error' => 'Invalid input data'], 400);
}

$gameId = $data['gameId'] ?? '';
$action = $data['action'] ?? 'move';

if (empty($gameId) || !isset($_SESSION['sudoku_games'][$gameId])) {
    sendResponse(['error' => 'Game not found'], 400);
}

$game = &$_SESSION['sudoku_games'][$gameId];

if ($action === 'move') {
    $row = $data['row'] ?? -1;
    $col = $data['col'] ?? -1;
    $value = $data['value'] ?? 0;

    if ($row < 0 || $col < 0 || $value < 1) {
        sendResponse(['error' => 'Invalid move'], 400);
    }

    $solution = $_SESSION['solution'];
    $correct = ($solution[$row][$col] == $value);
    $game['board'][$row][$col] = $value;

    $finished = true;
    $size = ($game['mode'] === 'easy') ? 4 : ($game['mode'] === 'medium') ? 6 : 9;
    for ($i = 0; $i < $size; $i++) {
        for ($j = 0; $j < $size; $j++) {
            if ($game['board'][$i][$j] != $solution[$i][$j]) {
                $finished = false;
                break 2;
            }
        }
    }

    if ($finished) {
        $game['status'] = 'ended';
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['ID'];
            $score = ($game['mode'] === 'easy') ? 3 : ($game['mode'] === 'medium') ? 5 : 10;
            $win = 1;

            try {
                $conn->begin_transaction();
                $stmt = $conn->prepare("UPDATE user SET Score = Score + ?, sumWin = sumWin + ?, sumScore = sumScore + ? WHERE ID = ?");
                $stmt->bind_param("iiii", $score, $win, $score, $userId);
                $stmt->execute();
                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
                sendResponse(['error' => 'Database error'], 500);
            }
        }
    }

    sendResponse([
        'correct' => $correct,
        'finished' => $finished,
        'board' => $game['board']
    ]);
} elseif ($action === 'surrender') {
    $game['status'] = 'ended';
    if (isset($_SESSION['user'])) {
        $userId = $_SESSION['user']['ID'];
        $score = 0;
        $win = 0;
        try {
            $stmt = $conn->prepare("UPDATE user SET Score = Score + ?, sumWin = sumWin + ?, sumScore = sumScore + ? WHERE ID = ?");
            $stmt->bind_param("iiii", $score, $win, $score, $userId);
            $stmt->execute();
            $conn->commit();
        } catch (Exception $e) {
            sendResponse(['error' => 'Database error'], 500);
        }
    }
    sendResponse([
        'result' => 'surrender'
    ]);
} else {
    sendResponse(['error' => 'Invalid action'], 400);
}
?>