const EMPTY = 0;
const MINE = -1;
const SIZE = 10;
const MINES_COUNT = 15;
let matrixGame = [];
let revealedCells = 0;
let flags = 0;

function init() {
    matrixGame = [];
    revealedCells = 0;
    flags = 0;

    let tableMin = document.getElementById("table_game");
    let tableContent = "";

    for (let row = 0; row < SIZE; row++) {
        let arr = [];
        let rowHTML = "<tr>";
        for (let col = 0; col < SIZE; col++) {
            arr.push(EMPTY);
            rowHTML += `<td class="td_game">
                <div id="${row}-${col}" onclick="handleClick(this.id)" class="fixed"></div>
            </td>`;
        }
        rowHTML += "</tr>";

        tableContent += rowHTML;
        matrixGame.push(arr);
    }

    tableMin.innerHTML = tableContent;

    placeMines();
    calculateNumbers();

    // Cập nhật số mìn còn lại khi bắt đầu game
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
    let [row, col] = id.split("-").map(Number);
    let cell = document.getElementById(id);

    if (!cell || cell.classList.contains("opened") || cell.innerHTML === "⚑") return;

    if (matrixGame[row][col] === MINE) {
        cell.innerHTML = "💣";
        cell.classList.add("mine");
        alert("💥 Bạn đã thua! Game Over!");
        revealAllMines();
        return;
    }

    openCell(row, col);
}

function openCell(row, col) {
    let cell = document.getElementById(`${row}-${col}`);
    if (!cell || cell.classList.contains("opened")) return;

    cell.classList.add("opened");
    revealedCells++;

    if (matrixGame[row][col] > 0) {
        cell.innerHTML = matrixGame[row][col];
    } else {
        cell.innerHTML = "";
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

function toggleFlag(event, id) {
    event.preventDefault(); // Ngăn menu chuột phải mặc định
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

function checkWin() {
    if (revealedCells + MINES_COUNT === SIZE * SIZE || flags === MINES_COUNT) {
        alert("🎉 Chúc mừng! Bạn đã thắng!");
        revealAllMines();
    }
}

function restartGame() {
    init();
}

// Ngăn menu chuột phải trên bảng game
document.getElementById("table_game").addEventListener("contextmenu", (event) => {
    event.preventDefault();
});

window.addEventListener("load", () => {
    let table = document.getElementById("table_game");
    table.addEventListener("contextmenu", (event) => {
        let target = event.target.closest("div");
        if (target) toggleFlag(event, target.id);
    });
});
