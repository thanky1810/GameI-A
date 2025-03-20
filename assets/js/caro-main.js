
let player;
let matrixGame;
let typeGame;

function init() {
    player = X; // Người chơi đầu tiên là X
    matrixGame = [];
    typeGame = TWO_PLAYER; // Mặc định là chế độ hai người chơi

    // Khởi tạo bàn cờ 10x10
    let rows = 10;
    let columns = 10;

    // Data table
    let tableXO = document.getElementById("table_game");
    let tableContent = "";

    for (let row = 0; row < rows; row++) {
        let arr = [];
        let rowHTML = "<tr>";
        for (let col = 0; col < columns; col++) {
            arr.push(EMPTY);
            rowHTML += `<td class="td_game"><div id="` + row.toString() + "-" + col.toString() + `" onclick="handleClick(this.id)" class="fixed"></div></td>`;
        }
        rowHTML += "</tr>";

        tableContent += rowHTML;
        matrixGame.push(arr);
    }

    tableXO.innerHTML = tableContent;
}

window.addEventListener("load", (event) => {
    init();
});

function checkDraw() {
    for (let i = 0; i < matrixGame.length; i++) {
        for (let j = 0; j < matrixGame[0].length; j++) {
            if (matrixGame[i][j] === EMPTY) {
                return false;
            }
        }
    }

    return true;
}

function getHorizontal(x, y, player) {
    let count = 1;
    for (let i = 1; i < 5; i++) {
        if (y + i < matrixGame[0].length && matrixGame[x][y + i] === player) {
            count++;
        } else {
            break;
        }
    }

    for (let i = 1; i < 5; i++) {
        if (y - i >= 0 && y - i < matrixGame[0].length && matrixGame[x][y - i] === player) {
            count++;
        } else {
            break;
        }
    }

    return count;
}

function getVertical(x, y, player) {
    let count = 1;
    for (let i = 1; i < 5; i++) {
        if (x + i < matrixGame.length && matrixGame[x + i][y] === player) {
            count++;
        } else {
            break;
        }
    }

    for (let i = 1; i < 5; i++) {
        if (x - i >= 0 && x - i < matrixGame.length && matrixGame[x - i][y] === player) {
            count++;
        } else {
            break;
        }
    }

    return count;
}

function getRightDiagonal(x, y, player) {
    let count = 1;
    for (let i = 1; i < 5; i++) {
        if (x - i >= 0 && x - i < matrixGame.length && y + i < matrixGame[0].length && matrixGame[x - i][y + i] === player) {
            count++;
        } else {
            break;
        }
    }

    for (let i = 1; i < 5; i++) {
        if (x + i < matrixGame.length && y - i >= 0 && y - i < matrixGame[0].length && matrixGame[x + i][y - i] === player) {
            count++;
        } else {
            break;
        }
    }

    return count;
}

function getLeftDiagonal(x, y, player) {
    let count = 1;
    for (let i = 1; i < 5; i++) {
        if (x - i >= 0 && x - i < matrixGame.length && y - i >= 0 && y - i < matrixGame[0].length && matrixGame[x - i][y - i] === player) {
            count++;
        } else {
            break;
        }
    }

    for (let i = 1; i < 5; i++) {
        if (x + i < matrixGame.length && y + i < matrixGame[0].length && matrixGame[x + i][y + i] === player) {
            count++;
        } else {
            break;
        }
    }

    return count;
}

function checkWin(points) {
    return getHorizontal(Number(points[0]), Number(points[1]), player) >= 5
        || getVertical(Number(points[0]), Number(points[1]), player) >= 5
        || getRightDiagonal(Number(points[0]), Number(points[1]), player) >= 5
        || getLeftDiagonal(Number(points[0]), Number(points[1]), player) >= 5;
}

function handleClick(id) {
    switch (processClick(id)) {
        case WIN:
            setTimeout(function () {
                alert("Player: " + player + " is winner");

                // reset game
                init();
            }, 100);
            break;
        case DRAW:
            setTimeout(function () {
                alert("Draw");

                // reset game
                init();
            }, 100);
            break;
    }
}

function processClick(id) {
    let points = id.split("-");

    switch (typeGame) {
        case TWO_PLAYER:
            if (matrixGame[Number(points[0])][Number(points[1])] === X || matrixGame[Number(points[0])][Number(points[1])] === O) {
                return;
            }

            if (player === X) {
                matrixGame[Number(points[0])][Number(points[1])] = X;
                document.getElementById(id).innerHTML = XText;
            }

            if (player === O) {
                matrixGame[Number(points[0])][Number(points[1])] = O;
                document.getElementById(id).innerHTML = OText;
            }

            if (checkWin(points)) {
                return WIN;
            }

            // check draw
            if (checkDraw()) {
                return DRAW;
            }

            player = player === X ? O : X;
            break;
        case COMPUTER:
        // source code to process play with computer
    }
}