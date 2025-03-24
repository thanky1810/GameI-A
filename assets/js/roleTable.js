let selectedType = TWO_PLAYER; // mặc định là chơi 2 người

// Khi nhấn "Bắt đầu chơi" -> hiện modal
document.getElementById("openModeModal").onclick = () => {
    document.getElementById("modeModal").style.display = "flex";
};

// Chọn 2 người chơi
document.getElementById("btn-2-players").onclick = () => {
    selectedType = TWO_PLAYER;
    document.getElementById("btn-2-players").classList.add("active");
    document.getElementById("btn-vs-computer").classList.remove("active");
};

// Chọn đấu với máy
document.getElementById("btn-vs-computer").onclick = () => {
    selectedType = COMPUTER;
    document.getElementById("btn-vs-computer").classList.add("active");
    document.getElementById("btn-2-players").classList.remove("active");
};

// Bắt đầu game sau khi chọn chế độ
document.getElementById("startGameBtn").onclick = () => {
    if (!selectedType) {
        alert("Vui lòng chọn chế độ chơi trước!");
        return;
    }

    // Điều hướng đến file chơi game
    window.location.href = "caroGame.php?type=" + selectedType;
};
