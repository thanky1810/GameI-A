const EMPTY = 0;
const MINE = -1;
const SIZE = 10;
const MINES_COUNT = 15;
let matrixGame = [];
let revealedCells = 0;
let flags = 0;
let gameOver = false; // thêm biến trạng thái game
let currentScore = 0;

function init() {
    matrixGame = [];
    revealedCells = 0;
    flags = 0;
    gameOver = false; // reset trạng thái

    let tableMin = document.getElementById("table_game");
    let tableContent = "";

    for (let row = 0; row < SIZE; row++) {
        let arr = [];
        let rowHTML = "<tr>";
        for (let col = 0; col < SIZE; col++) {
            arr.push(EMPTY);
            rowHTML += `<td class="td_game">
                <div id="${row}-${col}" onclick="handleClick(this.id)" oncontextmenu="toggleFlag(event, this.id)" class="fixed"></div>
            </td>`;
        }
        rowHTML += "</tr>";
        tableContent += rowHTML;
        matrixGame.push(arr);
    }

    tableMin.innerHTML = tableContent;
    placeMines();
    calculateNumbers();
    document.getElementById("mine_count").innerText = MINES_COUNT;
}

function placeMines() {
    let placedMines = 0;
    while (placedMines < MINES_COUNT) {
        let row = Math.floor(Math.random() * SIZE);
        let col = Math.floor(Math.random() * SIZE);
        if (matrixGame[row][col] !== MINE) {
            matrixGame[row][col] = MINE;
            placedMines++;
        }
    }
}

function calculateNumbers() {
    for (let row = 0; row < SIZE; row++) {
        for (let col = 0; col < SIZE; col++) {
            if (matrixGame[row][col] === MINE) continue;
            let count = 0;
            for (let i = -1; i <= 1; i++) {
                for (let j = -1; j <= 1; j++) {
                    let newRow = row + i;
                    let newCol = col + j;
                    if (newRow >= 0 && newRow < SIZE && newCol >= 0 && newCol < SIZE) {
                        if (matrixGame[newRow][newCol] === MINE) {
                            count++;
                        }
                    }
                }
            }
            matrixGame[row][col] = count;
        }
    }
}

function handleClick(id) {
    if (gameOver) return; // ⛔ Nếu đã thua thì không cho click

    let [row, col] = id.split("-").map(Number);
    let cell = document.getElementById(id);
    if (!cell || cell.classList.contains("opened") || cell.innerHTML === "⚑") return;

    if (matrixGame[row][col] === MINE) {
        cell.innerHTML = "💣";
        cell.classList.add("mine");
        document.getElementById("currentResult").innerText = "Thua 😭";
        revealAllMines();
        document.getElementById("restartButton").style.display = "inline-block"; // Hiện nút chơi lại
        gameOver = true; // ⛔ Đánh dấu kết thúc
        return;
    }

    openCell(row, col);
}

function openCell(row, col) {
    let cell = document.getElementById(`${row}-${col}`);
    if (!cell || cell.classList.contains("opened") || cell.innerHTML === "⚑") return;

    cell.classList.add("opened");
    revealedCells++;

    if (matrixGame[row][col] > 0) {
        cell.innerHTML = matrixGame[row][col];
    } else {
        for (let i = -1; i <= 1; i++) {
            for (let j = -1; j <= 1; j++) {
                let newRow = row + i;
                let newCol = col + j;
                if (newRow >= 0 && newRow < SIZE && newCol >= 0 && newCol < SIZE) {
                    openCell(newRow, newCol);
                }
            }
        }
    }

    checkWin();
}

function toggleFlag(event, id) {
    event.preventDefault();
    if (gameOver) return; // ⛔ Không cho cắm cờ sau khi thua

    let cell = document.getElementById(id);
    if (!cell || cell.classList.contains("opened")) return;

    if (cell.innerHTML === "⚑") {
        cell.innerHTML = "";
        flags--;
    } else if (flags < MINES_COUNT) {
        cell.innerHTML = "⚑";
        flags++;
    }

    document.getElementById("mine_count").innerText = MINES_COUNT - flags;
    checkWin();
}

function revealAllMines() {
    for (let row = 0; row < SIZE; row++) {
        for (let col = 0; col < SIZE; col++) {
            let cell = document.getElementById(`${row}-${col}`);
            if (matrixGame[row][col] === MINE) {
                cell.innerHTML = "💣";
                cell.classList.add("mine");
            }
        }
    }
}

function checkWin() {
    if (revealedCells + MINES_COUNT === SIZE * SIZE) {
        document.getElementById("currentResult").innerText = "Thắng 🎉";
        revealAllMines();
        document.getElementById("restartButton").style.display = "inline-block"; // Hiện nút chơi lại
        gameOver = true; // ⛔ Thắng rồi cũng không cho chơi tiếp
        saveScoreToDB(5);
    }
}

function saveScoreToDB(score) {
    const baseUrl = 'http://192.168.1.6/Project/'; // BASE_URL từ .env
    fetch(`${baseUrl}game/minGameScore.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `score=${score}`
    })
        .then(response => response.text())
        .then(data => {
            console.log('Phản hồi từ server:', data); // Hiển thị phản hồi từ PHP
            alert(data); // Thông báo kết quả cho người chơi
        })
        .catch(error => {
            console.error('Lỗi khi lưu điểm:', error);
            alert('Không thể lưu điểm. Vui lòng thử lại!');
        });
}

function restartGame() {
    init();
    document.getElementById("currentResult").innerText = "Đang chơi...";
    document.getElementById("restartButton").style.display = "none";
    gameOver = false;
}

// Khởi động lần đầu
init();