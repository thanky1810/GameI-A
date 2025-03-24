let selectedType = null;

document.getElementById("openModeModal").onclick = () => {
    document.getElementById("modeModal").style.display = "flex";
};


document.getElementById("btn-2-players").onclick = () => {
    selectedType = "2-players";
    document.getElementById("btn-2-players").classList.add("active");
    document.getElementById("btn-vs-computer").classList.remove("active");
};

document.getElementById("btn-vs-computer").onclick = () => {
    selectedType = "player-computer";
    document.getElementById("btn-vs-computer").classList.add("active");
    document.getElementById("btn-2-players").classList.remove("active");
};

document.getElementById("startGameBtn").onclick = () => {
    if (!selectedType) {
        alert("Vui lòng chọn chế độ chơi trước!");
        return;
    }

    window.location.href = "caroGame.php?type=" + selectedType;
};
