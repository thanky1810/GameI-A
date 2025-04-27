<?php
session_start();

if (!isset($_SESSION['solution'])) {
    echo json_encode(["error" => "No solution found"]);
    exit;
}

$solution = $_SESSION['solution'];
$row = intval($_POST['row']);
$col = intval($_POST['col']);
$value = intval($_POST['value']);

// Kiểm tra ô người chơi nhập có đúng không
$correct = ($solution[$row][$col] == $value);

// Kiểm tra toàn bộ bảng đã đúng chưa
$finished = true;
foreach ($_POST as $key => $val) {
    if (!preg_match('/^cell-/', $key)) continue;
    if (intval($val) !== $solution[intval(substr($key, 5, 1))][intval(substr($key, 7, 1))]) {
        $finished = false;
        break;
    }
}

echo json_encode([
    "correct" => $correct,
    "finished" => $finished
]);
?>
