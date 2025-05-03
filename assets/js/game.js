////      THAM KHẢO

document.addEventListener('DOMContentLoaded', function () {
    const backButton = document.getElementById('backButton');
    const showRulesBtn = document.getElementById('showRulesBtn');
    const rulesPopup = document.getElementById('rulesPopup');
    const closeRulesBtn = document.getElementById('closeRulesBtn');

    // Xử lý sự kiện click khi người dùng nhấn nút Lùi
    backButton.addEventListener('click', function () {
        window.history.back();
    });

    // Hiển thị bảng Quy tắc khi nhấn nút "Quy tắc"
    showRulesBtn.addEventListener('click', function () {
        rulesPopup.style.display = 'flex';  // Hiển thị bảng quy tắc
    });

    // Đóng bảng Quy tắc khi nhấn nút đóng
    closeRulesBtn.addEventListener('click', function () {
        rulesPopup.style.display = 'none';  // Ẩn bảng quy tắc
    });

    // Dữ liệu bảng kỷ lục mẫu

    // Hiển thị bảng kỷ lục
    const leaderboardList = document.getElementById("leaderboard-list");
    leaderboardData.forEach(player => {
        const li = document.createElement('li');
        li.innerHTML = `${player.name} <span>${player.time}</span>`;
        leaderboardList.appendChild(li);
    });

    // Xử lý sự kiện nút "Bắt đầu chơi"
    document.querySelector('.play-btn').addEventListener('click', function () {
        const url = this.getAttribute('data-url');
        window.location.href = url;
    });
});

