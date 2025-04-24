<?php
session_start();
if (!isset($_SESSION["user"])) {
    http_response_code(404);
    die("404 Not Found");
}
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../bootstrap.php');

$userName = $_SESSION["user"];
$conn = mysqli_connect("localhost", "root", "", "game-a");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["new_username"])) {
        $newUsername = mysqli_real_escape_string($conn, $_POST["new_username"]);
        $id = $userName["ID"];
        mysqli_query($conn, "UPDATE user SET Username='$newUsername' WHERE ID=$id");
        $_SESSION["user"]["Username"] = $newUsername;
        header("Location: account.php");
        exit;
    }

    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $fileName = uniqid() . "_" . basename($_FILES["avatar"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
            $avatarPath = "uploads/" . $fileName;
            $id = $userName["ID"];
            mysqli_query($conn, "UPDATE user SET avatar='$avatarPath' WHERE ID=$id");
            $_SESSION["user"]["avatar"] = $avatarPath;
            header("Location: account.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAME I&R Online</title>
    <link rel="shortcut icon" href="../assets/img/10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/account.css">
    <style>
        .avatar {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .avatar img {
            border-radius: 50%;
            width: 80px;
            height: 80px;
        }

        .edit-avatar {
            position: absolute;
            top: 50%;
            /* Move to the vertical center */
            left: 50%;
            /* Move to the horizontal center */
            transform: translate(-50%, -50%);
            /* Center the icon by offsetting its own dimensions */
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            padding: 5px;
            display: none;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .avatar:hover .edit-avatar {
            display: block;
        }

        .edit-avatar img {
            width: 20px;
            height: 20px;
            transition: transform 0.3s;
        }

        .avatar:hover .edit-avatar img {
            transform: scale(2);
            /* Làm cho biểu tượng sửa nhỏ lại khi hover vào ảnh */
        }

        .edit-icon {
            cursor: pointer;
            margin-left: 10px;
        }

        .user-details {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #username {
            display: flex;
            align-items: center;
        }

        #username .edit-icon {
            margin-left: 10px;
        }

        .edit-username-form {
            display: inline;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <section id="account-info">
            <div class="account-header">
                <div class="avatar">
                    <img src="../<?php echo htmlspecialchars($userName["avatar"] ?? "assets/img/5.jpg"); ?>" alt="Avatar">
                    <form method="POST" enctype="multipart/form-data" class="edit-avatar">
                        <label for="avatarInput">
                            <img src="../assets/img/edit-icon.png" alt="Edit" width="20">
                        </label>
                        <input type="file" id="avatarInput" name="avatar" style="display: none;" onchange="this.form.submit()">
                    </form>
                </div>
                <div class="user-details">
                    <h2 id="username">
                        <?php echo htmlspecialchars($userName["Username"]); ?>
                        <img src="../assets/img/edit-icon.png" class="edit-icon" onclick="toggleEdit()" width="16" title="Sửa tên">
                    </h2>
                    <form method="POST" class="edit-username-form hidden" id="editForm">
                        <input type="text" name="new_username" value="<?php echo htmlspecialchars($userName["Username"]); ?>">
                        <button type="submit">Lưu</button>
                    </form>
                    <p id="status">Trực tuyến</p>
                </div>
                <div class="back-home">
                    <a href="../Pages/home.php" class="back-btn">Home</a>
                </div>
            </div>

            <?php
            $id = $userName["ID"];
            $rs = mysqli_query($conn, "SELECT * FROM user WHERE ID = $id");
            $row = mysqli_fetch_assoc($rs);
            ?>

            <div class="stats">
                <h3>SỐ LIỆU THỐNG KÊ</h3>
                <ul>
                    <li><span class="label">Số lượng trò chơi:</span> <span id="gamesPlayed">4</span></li>
                    <li><span class="label">Số lượng trận thắng:</span> <span id="matchesWon"><?php echo $row['sumWin']; ?></span></li>
                    <li><span class="label">Thời gian chơi trên trang web:</span> <span id="playTime">2 giờ</span></li>
                    <li><span class="label">Đã đăng ký:</span> <span id="registrationTime">10:30, 04/03/2025</span></li>
                </ul>
            </div>
        </section>
    </main>

    <?php include "../includes/footer.php"; ?>

    <script>
        function toggleEdit() {
            document.getElementById("editForm").classList.toggle("hidden");
        }
    </script>

</body>

</html>