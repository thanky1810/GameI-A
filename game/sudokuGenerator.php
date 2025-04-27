<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Hàm kiểm tra số hợp lệ
function isValid($board, $row, $col, $num) {
    for ($i = 0; $i < 9; $i++) {
        if ($board[$row][$i] == $num || $board[$i][$col] == $num) {
            return false;
        }
    }
    $startRow = $row - $row % 3;
    $startCol = $col - $col % 3;
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
            if ($board[$startRow + $i][$startCol + $j] == $num) {
                return false;
            }
        }
    }
    return true;
}

// Hàm giải để sinh bảng hoàn chỉnh
function solve(&$board) {
    for ($row = 0; $row < 9; $row++) {
        for ($col = 0; $col < 9; $col++) {
            if ($board[$row][$col] == 0) {
                $nums = range(1,9);
                shuffle($nums);
                foreach ($nums as $num) {
                    if (isValid($board, $row, $col, $num)) {
                        $board[$row][$col] = $num;
                        if (solve($board)) {
                            return true;
                        }
                        $board[$row][$col] = 0;
                    }
                }
                return false;
            }
        }
    }
    return true;
}

// Tạo bảng Sudoku hoàn chỉnh
$board = array_fill(0, 9, array_fill(0, 9, 0));
solve($board);

// Lưu lời giải vào session
$_SESSION['solution'] = $board;

// Ẩn ngẫu nhiên 45 ô
$hiddenCells = 45;
while ($hiddenCells > 0) {
    $row = rand(0,8);
    $col = rand(0,8);
    if ($board[$row][$col] !== 0) {
        $board[$row][$col] = 0;
        $hiddenCells--;
    }
}

// CSS nội bộ
echo '<style>
.sudoku-board {
    display: grid;
    grid-template-columns: repeat(9, 50px);
    grid-template-rows: repeat(9, 50px);
    gap: 2px;
    justify-content: center;
    margin: 20px auto;
}
.sudoku-cell {
    width: 48px;
    height: 48px;
    text-align: center;
    font-size: 20px;
    border: 1px solid #999;
    background-color: #fff;
}
.fixed {
    background-color: #e0e0e0;
    font-weight: bold;
}
</style>';

// HTML bảng Sudoku
echo '<div class="sudoku-board">';
for ($row = 0; $row < 9; $row++) {
    for ($col = 0; $col < 9; $col++) {
        $value = $board[$row][$col];
        if ($value === 0) {
            echo '<input type="text" maxlength="1" pattern="[1-9]" inputmode="numeric" ';
            echo 'class="sudoku-cell" id="cell-'.$row.'-'.$col.'" data-row="'.$row.'" data-col="'.$col.'">';
        } else {
            echo '<div class="sudoku-cell fixed">' . $value . '</div>';
        }
    }
}
echo '</div>';
?>
