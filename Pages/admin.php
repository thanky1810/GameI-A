<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    http_response_code(404);
    die("404 Not Found");
}
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../bootstrap.php');
include("../includes/database.php");

// Hàm tạo tên không trùng lặp (từ file login.php)
function randomName($conn)
{
    do {
        $name = "#" . rand(1000, 9999);
        $sql = "SELECT COUNT(*) as count FROM user WHERE name = '$name'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
    } while ($row['count'] > 0);
    return $name;
}

// Xử lý Thêm tài khoản
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add-user-submit'])) {
    $userName = filter_input(INPUT_POST, "add-username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['add-password'];
    $confirmPassword = $_POST['add-confirm-password'];
    $role = $_POST['add-role'];

    if ($password !== $confirmPassword) {
        echo "<script>alert('Mật khẩu không khớp! Vui lòng nhập lại.');</script>";
    } else {
        $sql = "SELECT * FROM user WHERE userName = '$userName'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Tên đăng nhập đã tồn tại! Vui lòng chọn tên khác.');</script>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $name = randomName($conn);

            $sql = "INSERT INTO user (userName, password, role, name) 
                    VALUES ('$userName', '$hashedPassword', '$role', '$name')";
            if (mysqli_query($conn, $sql)) {
                header('Location: admin.php');
                exit();
            } else {
                echo "<script>alert('Lỗi: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}

// Xử lý Xóa tài khoản
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $delete_query = "DELETE FROM user WHERE ID = '$delete_id'";
    mysqli_query($conn, $delete_query);
    header("Location: admin.php");
    exit();
}

// Xử lý Sửa tài khoản
if (isset($_POST['edit_id'])) {
    $edit_id = mysqli_real_escape_string($conn, $_POST['edit_id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $score = mysqli_real_escape_string($conn, $_POST['score']);
    $sumWin = mysqli_real_escape_string($conn, $_POST['sumWin']);

    $update_query = "UPDATE user SET userName = '$username', password = '$password', role = '$role', Score = '$score', sumWin = '$sumWin' WHERE ID = '$edit_id'";
    mysqli_query($conn, $update_query);
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin1</title>
    <link rel="shortcut icon" href="10.jpg" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/modal.css') ?>">
</head>

<body>

    <!-- Header -->
    <?php include "../includes/header.php" ?>

    <main class="dashboard">
        <aside class="sidebar">
            <ul>
                <li>🏠 Dashboard</li>
                <li>👤 Quản lý tài khoản</li>
                <li onclick="openAddUserModal()">➕ Thêm tài khoản</li>
            </ul>
        </aside>

        <section class="main-content">

            <div class="stats">
                <div class="stat-card">
                    <span class="emoji pink">👤</span>
                    <div>
                        <h2>87</h2>
                        <p>Người dùng</p>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="emoji yellow">🎮</span>
                    <div>
                        <h2>3</h2>
                        <p>Trò chơi</p>
                    </div>
                </div>
            </div>

            <div class="charts">
                <div class="chart-box">
                    <h3>SỐ LƯỢNG NGƯỜI DÙNG TRUY CẬP</h3>
                    <?php
                    $qr = "SELECT * FROM user";
                    $kq = mysqli_query($conn, $qr);
                    ?>

                    <div class="leaderboard">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User Name</th>
                                    <th>Password</th>
                                    <th>Role</th>
                                    <th>Score</th>
                                    <th>Win</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($d = mysqli_fetch_array($kq)) {
                                ?>
                                    <tr>
                                        <td><?= $d['ID'] ?></td>
                                        <td><?= $d['userName'] ?></td>
                                        <td><?= $d['password'] ?></td>
                                        <td><?= $d['role'] ?></td>
                                        <td><?= $d['Score'] ?></td>
                                        <td><?= $d['sumWin'] ?></td>
                                        <td>
                                            <button class="btn edit-btn" onclick="openEditModal(<?= $d['ID'] ?>, '<?= $d['userName'] ?>', '<?= $d['password'] ?>', '<?= $d['role'] ?>', '<?= $d['Score'] ?>', '<?= $d['sumWin'] ?>')">Sửa</button>
                                            <a href="admin.php?delete_id=<?= $d['ID'] ?>" class="btn delete-btn" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">Xóa</a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Sửa Thông Tin Người Dùng</h3>
            <form method="POST" action="admin.php">
                <input type="hidden" name="edit_id" id="edit_id">
                <div>
                    <label>User Name</label>
                    <input type="text" name="username" id="edit_username">
                </div>
                <div>
                    <label>Password</label>
                    <input type="text" name="password" id="edit_password">
                </div>
                <div>
                    <label>Role</label>
                    <input type="text" name="role" id="edit_role">
                </div>
                <div>
                    <label>Score</label>
                    <input type="number" name="score" id="edit_score">
                </div>
                <div>
                    <label>Win</label>
                    <input type="number" name="sumWin" id="edit_sumWin">
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn save-btn">Lưu</button>
                    <button type="button" class="btn cancel-btn" onclick="closeEditModal()">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <h3>Thêm Tài Khoản Mới</h3>
            <form method="POST" action="admin.php">
                <div>
                    <label>Username</label>
                    <input type="text" name="add-username" id="add-username" required>
                </div>
                <div>
                    <label>Password</label>
                    <input type="password" name="add-password" id="add-password" required>
                </div>
                <div>
                    <label>Confirm Password</label>
                    <input type="password" name="add-confirm-password" id="add-confirm-password" required>
                </div>
                <div>
                    <label>Role</label>
                    <select name="add-role" id="add-role" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="submit" name="add-user-submit" class="btn save-btn">Thêm</button>
                    <button type="button" class="btn cancel-btn" onclick="closeAddUserModal()">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include "../includes/footer.php"; ?>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>

</html>