<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class SudokuGenerator {
    private $size;
    private $subGridSize;

    public function __construct($mode = 'hard') {
        if ($mode === 'easy') {
            $this->size = 4;
            $this->subGridSize = 2;
        } elseif ($mode === 'medium') {
            $this->size = 6;
            $this->subGridSize = 2;
        } else {
            $this->size = 9;
            $this->subGridSize = 3;
        }
    }

    private function isValid($board, $row, $col, $num) {
        for ($i = 0; $i < $this->size; $i++) {
            if ($board[$row][$i] == $num || $board[$i][$col] == $num) {
                return false;
            }
        }
        $startRow = $row - $row % $this->subGridSize;
        $startCol = $col - $col % $this->subGridSize;
        for ($i = 0; $i < $this->subGridSize; $i++) {
            for ($j = 0; $j < $this->subGridSize; $j++) {
                if ($board[$startRow + $i][$startCol + $j] == $num) {
                    return false;
                }
            }
        }
        return true;
    }

    private function solve(&$board) {
        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                if ($board[$row][$col] == 0) {
                    $nums = range(1, $this->size);
                    shuffle($nums);
                    foreach ($nums as $num) {
                        if ($this->isValid($board, $row, $col, $num)) {
                            $board[$row][$col] = $num;
                            if ($this->solve($board)) {
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

    public function generate($hiddenCells) {
        $board = array_fill(0, $this->size, array_fill(0, $this->size, 0));
        $this->solve($board);
        $_SESSION['solution'] = $board;

        $cellsToHide = $hiddenCells;
        while ($cellsToHide > 0) {
            $row = rand(0, $this->size - 1);
            $col = rand(0, $this->size - 1);
            if ($board[$row][$col] !== 0) {
                $board[$row][$col] = 0;
                $cellsToHide--;
            }
        }
        $_SESSION['sudoku_board'] = $board;
        return $board;
    }
}

// Số ô ẩn tùy theo cấp độ
$mode = $_GET['mode'] ?? 'hard';
$generator = new SudokuGenerator($mode);
$hiddenCells = ($mode === 'easy') ? 8 : ($mode === 'medium') ? 16 : 45;
$board = $generator->generate($hiddenCells);
?>