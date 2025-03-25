
let player;
let matrixGame;
let typeGame;
let gameOver = false;

function getTypeFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get("type");

    if (type === TWO_PLAYER || type === COMPUTER || type === COMPUTER_COMPUTER) {
        return type;
    }

    return TWO_PLAYER; // mặc định
}

function init() {
    player = X;
    matrixGame = [];
    typeGame = getTypeFromURL();

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
        if (y - i >= 0 && matrixGame[x][y - i] === player) {
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
        if (x - i >= 0 && matrixGame[x - i][y] === player) {
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
                init();
            }, 100);
            break;
        case DRAW:
            setTimeout(function () {
                alert("Draw");
                init();
            }, 100);
            break;
    }
}


function processClick(id) {
    if (gameOver) return;

    let points = id.split("-");
    let x = Number(points[0]);
    let y = Number(points[1]);

    if (matrixGame[x][y] !== EMPTY) {
        return;
    }

    if (player === X) {
        matrixGame[x][y] = X;
        const cell = document.getElementById(id);
        cell.innerHTML = XText;
        cell.classList.add("x");
    } else {
        matrixGame[x][y] = O;
        const cell = document.getElementById(id);
        cell.innerHTML = OText;
        cell.classList.add("o");
    }


    if (checkWin([x, y])) {
        gameOver = true;
        setTimeout(function () {
            alert("Player: " + player + " is winner");
            init();
            gameOver = false;
        }, 100);
        return WIN;
    }


    if (checkDraw()) {
        gameOver = true;
        setTimeout(function () {
            alert("Draw");
            init();
            gameOver = false;
        }, 100);
        return DRAW;
    }

    player = (player === X) ? O : X;
}
