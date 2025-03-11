////      THAM KHẢO

document.addEventListener('DOMContentLoaded', function() {
    const backButton = document.getElementById('backButton');
    const showRulesBtn = document.getElementById('showRulesBtn');
    const rulesPopup = document.getElementById('rulesPopup');
    const closeRulesBtn = document.getElementById('closeRulesBtn');

    // Xử lý sự kiện click khi người dùng nhấn nút Lùi
    backButton.addEventListener('click', function() {
        window.history.back();
    });

    // Hiển thị bảng Quy tắc khi nhấn nút "Quy tắc"
    showRulesBtn.addEventListener('click', function() {
        rulesPopup.style.display = 'flex';  // Hiển thị bảng quy tắc
    });

    // Đóng bảng Quy tắc khi nhấn nút đóng
    closeRulesBtn.addEventListener('click', function() {
        rulesPopup.style.display = 'none';  // Ẩn bảng quy tắc
    });

    // Dữ liệu bảng kỷ lục mẫu
    const leaderboardData = [
        { name: "guest0452", time: "0.01 giây" },
        { name: "guest0563", time: "0.01 giây" },
        { name: "guest0123", time: "0.01 giây" },
        { name: "guest0459", time: "0.02 giây" },
        { name: "guest0573", time: "0.02 giây" },
        { name: "guest0132", time: "0.02 giây" },
        { name: "guest0401", time: "0.02 giây" },
        { name: "guest0324", time: "0.03 giây" },
        { name: "guest0456", time: "0.03 giây" },
        { name: "guest0823", time: "0.03 giây" },
        { name: "guest0999", time: "0.03 giây" },
        { name: "guest0712", time: "0.04 giây" }
    ];

    // Hiển thị bảng kỷ lục
    const leaderboardList = document.getElementById("leaderboard-list");
    leaderboardData.forEach(player => {
        const li = document.createElement('li');
        li.innerHTML = `${player.name} <span>${player.time}</span>`;
        leaderboardList.appendChild(li);
    });

    // Xử lý sự kiện nút "Bắt đầu chơi"
    document.querySelector('.play-btn').addEventListener('click', function () {
        alert("Bắt đầu chơi!");
    });
});

