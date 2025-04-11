<?php

class CaroGame
{
    private $boardSize = 15;

    public function __construct()
    {
        // Constructor
    }

    /**
     * Kiểm tra thắng/thua
     */
    public function checkWin($board, $row, $col, $symbol)
    {
        $directions = [
            [1, 0],  // ngang
            [0, 1],  // dọc
            [1, 1],  // chéo xuống
            [1, -1]  // chéo lên
        ];

        foreach ($directions as $dir) {
            $dx = $dir[0];
            $dy = $dir[1];
            $count = 1;

            // Đếm theo hướng dương
            for ($i = 1; $i < 5; $i++) {
                $newRow = $row + $dx * $i;
                $newCol = $col + $dy * $i;

                if ($newRow < 0 || $newRow >= $this->boardSize || $newCol < 0 || $newCol >= $this->boardSize) {
                    break;
                }

                if ($board[$newRow][$newCol] === $symbol) {
                    $count++;
                } else {
                    break;
                }
            }

            // Đếm theo hướng âm
            for ($i = 1; $i < 5; $i++) {
                $newRow = $row - $dx * $i;
                $newCol = $col - $dy * $i;

                if ($newRow < 0 || $newRow >= $this->boardSize || $newCol < 0 || $newCol >= $this->boardSize) {
                    break;
                }

                if ($board[$newRow][$newCol] === $symbol) {
                    $count++;
                } else {
                    break;
                }
            }

            if ($count >= 5) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra hòa
     */
    public function checkDraw($board)
    {
        for ($i = 0; $i < $this->boardSize; $i++) {
            for ($j = 0; $j < $this->boardSize; $j++) {
                if ($board[$i][$j] === '') {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * AI đánh nước cho máy
     */
    public function computerMove($board)
    {
        $symbol = 'O';
        $opponentSymbol = 'X';

        // Kiểm tra nếu máy có thể thắng trong một nước
        for ($i = 0; $i < $this->boardSize; $i++) {
            for ($j = 0; $j < $this->boardSize; $j++) {
                if ($board[$i][$j] === '') {
                    $board[$i][$j] = $symbol;
                    if ($this->checkWin($board, $i, $j, $symbol)) {
                        return ['row' => $i, 'col' => $j];
                    }
                    $board[$i][$j] = '';
                }
            }
        }

        // Kiểm tra nếu cần chặn người chơi thắng
        for ($i = 0; $i < $this->boardSize; $i++) {
            for ($j = 0; $j < $this->boardSize; $j++) {
                if ($board[$i][$j] === '') {
                    $board[$i][$j] = $opponentSymbol;
                    if ($this->checkWin($board, $i, $j, $opponentSymbol)) {
                        return ['row' => $i, 'col' => $j];
                    }
                    $board[$i][$j] = '';
                }
            }
        }

        // Đánh vào vị trí có tiềm năng cao
        $scores = $this->evaluateBoard($board);
        $maxScore = -1;
        $bestMoves = [];

        for ($i = 0; $i < $this->boardSize; $i++) {
            for ($j = 0; $j < $this->boardSize; $j++) {
                if ($board[$i][$j] === '' && $scores[$i][$j] > $maxScore) {
                    $maxScore = $scores[$i][$j];
                    $bestMoves = [['row' => $i, 'col' => $j]];
                } else if ($board[$i][$j] === '' && $scores[$i][$j] === $maxScore) {
                    $bestMoves[] = ['row' => $i, 'col' => $j];
                }
            }
        }

        // Chọn ngẫu nhiên một trong các nước đi tốt nhất
        if (count($bestMoves) > 0) {
            $randIndex = rand(0, count($bestMoves) - 1);
            return $bestMoves[$randIndex];
        }

        // Nếu không tìm thấy nước đi nào tốt, chọn vị trí ngẫu nhiên
        $emptyPositions = [];
        for ($i = 0; $i < $this->boardSize; $i++) {
            for ($j = 0; $j < $this->boardSize; $j++) {
                if ($board[$i][$j] === '') {
                    $emptyPositions[] = ['row' => $i, 'col' => $j];
                }
            }
        }

        if (count($emptyPositions) > 0) {
            $randIndex = rand(0, count($emptyPositions) - 1);
            return $emptyPositions[$randIndex];
        }

        return null;
    }

    /**
     * Đánh giá giá trị của từng ô trên bàn cờ
     */
    private function evaluateBoard($board)
    {
        $scores = array_fill(0, $this->boardSize, array_fill(0, $this->boardSize, 0));

        // Các hướng đánh giá
        $directions = [
            [1, 0],  // ngang
            [0, 1],  // dọc
            [1, 1],  // chéo xuống
            [1, -1]  // chéo lên
        ];

        // Tăng điểm cho các ô gần trung tâm bàn cờ
        $center = floor($this->boardSize / 2);
        for ($i = 0; $i < $this->boardSize; $i++) {
            for ($j = 0; $j < $this->boardSize; $j++) {
                if ($board[$i][$j] === '') {
                    // Khoảng cách đến trung tâm
                    $distanceToCenter = max(abs($i - $center), abs($j - $center));
                    $scores[$i][$j] += (5 - min(5, $distanceToCenter));

                    // Đánh giá theo từng hướng
                    foreach ($directions as $dir) {
                        $scores[$i][$j] += $this->evaluateDirection($board, $i, $j, $dir[0], $dir[1]);
                    }
                }
            }
        }

        return $scores;
    }

    /**
     * Đánh giá giá trị của một ô theo hướng cụ thể
     */
    private function evaluateDirection($board, $row, $col, $dx, $dy)
    {
        $score = 0;
        $computerCount = 0;
        $playerCount = 0;
        $emptyCount = 0;

        // Kiểm tra 4 ô mỗi hướng
        for ($i = -4; $i <= 4; $i++) {
            $newRow = $row + $dx * $i;
            $newCol = $col + $dy * $i;

            // Bỏ qua các ô nằm ngoài bàn cờ
            if ($newRow < 0 || $newRow >= $this->boardSize || $newCol < 0 || $newCol >= $this->boardSize) {
                continue;
            }

            if ($board[$newRow][$newCol] === 'O') {
                $computerCount++;
            } else if ($board[$newRow][$newCol] === 'X') {
                $playerCount++;
            } else {
                $emptyCount++;
            }
        }

        // Đánh giá dựa trên số quân liên tiếp
        if ($playerCount == 0 && $computerCount > 0) {
            // Chỉ có quân máy
            $score += $computerCount * 3;
        } else if ($computerCount == 0 && $playerCount > 0) {
            // Chỉ có quân người
            $score += $playerCount * 2;
        }

        return $score;
    }
}
