<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAME I&R Online</title>
    <link rel="shortcut icon" href="../assets/img/10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/game.css">
</head>
<body>

    <!-- Header -->
    <?php include "../includes/header.php"?>

    <main>
        <h1>DÒ MÌN<span class="stars">⭐⭐⭐⭐⭐</span></h1>
        <div class="buttons">
            <button class="back" id="backButton"><<</button>
            <button class="btn rule-btn" id="showRulesBtn">Quy tắc</button>
            <button class="btn play-btn">Bắt đầu chơi</button>
        </div>

        <!-- Bảng Quy tắc Modal -->
        <div class="rules-popup" id="rulesPopup">
            <div class="rules-content">
                <button id="closeRulesBtn" class="close-btn">❌</button>
                <h2>Quy tắc trò chơi Dò Mìn</h2>
                <p>Dò Mìn là một trong những trò chơi máy tính nổi tiếng nhất trong thể loại trí não giải đố.</p>
                <p>Kích thước của sân chơi thay đổi tùy thuộc vào độ khó đã chọn:</p>
                <ul>
                    <li>“Người mới bắt đầu” với sân chơi 10x10 ô;</li>
                    <li>“Nghiệp dư” với sân chơi 15x15 ô;</li>
                    <li>“Chuyên nghiệp” với sân chơi 20x20 ô.</li>
                </ul>
                <h3>Mục tiêu của trò chơi</h3>
                <p>Mở tất cả các ô không chứa mìn.</p>
                <h3>Tiến trình trận đấu</h3>
                <ul>
                    <li>Trên sân chơi chứa ba loại ô khác nhau: ô trống, “ô có gài mìn” và ô có chỉ dẫn số;</li>
                    <li>Mỗi chữ số tương ứng với số lượng mìn trong các ô tại những khu vực lân cận. Do đó, người chơi sẽ có thể xác định vị trí các ô trống và ô đã được gài mìn;</li>
                    <li>Số lượng mìn được chỉ ra trong cửa sổ bên cạnh sân chơi. Chúng có thể có 11, 29 hoặc 51;</li>
                    <li>Các quả mìn chỉ được phân phối đến ô sau nước đi đầu tiên, vì vậy không thể nào bị thua ngay sau nước đầu tiên;</li>
                    <li>Trong trường hợp khi có ô trống bên cạnh ô đã mở, thì ô đó sẽ tự động mở ra;</li>
                    <li>Để cho quá trình chơi thoải mái hơn, các ô “đã gài mìn” có thể được đánh dấu bằng những lá cờ. Để thực hiện việc này, hãy nhấn nút chuột phải (hoặc bấm vào ô trong 1 giây đối với thiết bị di động). Điều này sẽ ngăn chặn việc vô tình nhấp vào các ô có mìn.</li>
                </ul>
                <h3>Chung kết</h3>
                <p>Trò chơi chỉ được coi là kết thúc thành công khi tất cả các ô “không có mìn” đều được mở ra. Trò chơi coi như thua trong trường hợp khi mở phải ô có mìn.</p>
            </div>
        </div>

        <h2>Bảng kỷ lục</h2>
        <div class="leaderboard">
            <ul id="leaderboard-list"></ul>
        </div>

        <div class="info">
            <div class="info-text">
                <p>Trò chơi Dò Mìn, xuất hiện từ rất lâu trước khi phát minh ra máy tính, cũng là một trong những trò chơi trí não logic phổ biến nhất đối với người dùng Windows.</p>
                <p>Phiên bản từ GAME I&R Online mang đến cơ hội chơi dò Mìn miễn phí trên cả PC và điện thoại. Hãy xem các quy tắc và hãy thực hành trong phần chơi một mình để tìm hiểu</p>
                <p>Cách học cách chơi dò mìn cổ điển và vượt qua nó thành công, tránh được các ô có gài mìn.</p>
                <p>Những con số trong trò Dò Mìn có nghĩa là gì, những ô nào nên được mở và tại sao nên đặt các lá cờ trên sân chơi.</p>
            </div>
            <div class="game-image">
                <img src="../assets/img/6.jpg" alt="Min">
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include "../includes/footer.php"?>

</body>
<script src="../assets/css/game.css"></script>

</html>