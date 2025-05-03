<?php
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../game/sudokuGenerator.php';
header('Content-Type: application/json');

function sendResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    sendResponse(['error' => 'Invalid input data'], 400);
}

$action = $data['action'] ?? '';
$mode = $data['mode'] ?? 'hard';

if ($action === 'new_game') {
    $gameId = uniqid();
    $hiddenCells = ($mode === 'easy') ? 8 : ($mode === 'medium') ? 16 : 45;
    $generator = new SudokuGenerator($mode);
    $board = $generator->generate($hiddenCells);

    if (!isset($_SESSION['sudoku_games'])) {
        $_SESSION['sudoku_games'] = [];
    }

    $_SESSION['sudoku_games'][$gameId] = [
        'mode' => $mode,
        'board' => $board,
        'status' => 'active'
    ];

    sendResponse([
        'gameId' => $gameId,
        'board' => $board
    ]);
} else {
    sendResponse(['error' => 'Invalid action'], 400);
}
