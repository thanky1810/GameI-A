<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Bạn cần đăng nhập để lưu kết quả']);
    exit;
}

// Nhận dữ liệu từ client
$data = json_decode(file_get_contents('php://input'), true);
$gameId = $data['gameId'] ?? 1; // ID của game Caro trong database
$win = $data['win'] ?? 0;
$score = $data['score'] ?? 0;

// Lấy ID người dùng từ session
$userId = $_SESSION['user']['ID'];

// Lưu kết quả vào database
$stmt = $conn->prepare("INSERT INTO GameHistory (userId, gameID, score, win) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $userId, $gameId, $score, $win);

if ($stmt->execute()) {
    // Cập nhật thông tin người dùng
    if ($win) {
        $stmt = $conn->prepare("UPDATE User SET Score = Score + ?, sumWin = sumWin + 1, sumScore = sumScore + ? WHERE ID = ?");
        $stmt->bind_param("iis", $score, $score, $userId);
        $stmt->execute();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Kết quả đã được lưu'
    ]);
} else {
    echo json_encode([
        'error' => 'Không thể lưu kết quả: ' . $stmt->error
    ]);
}
