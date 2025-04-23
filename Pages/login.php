<?php
session_start();
include "../includes/database.php";
// Hàm tạo tên không trùng lặp
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!$conn) {
        die("Lỗi kết nối MySQL: " . mysqli_connect_error());
    }

    // Kiểm tra nếu là đăng ký
    if (isset($_POST['register-submit'])) {
        $userName = filter_input(INPUT_POST, "register-userName", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $_POST['register-password'];
        $confirmPassword = $_POST['register-confirm-password'];

        // Kiểm tra mật khẩu có trùng nhau không
        if ($password !== $confirmPassword) {
            echo "Mật khẩu không khớp! Vui lòng nhập lại.";
        } else {
            // Kiểm tra xem tên đăng nhập đã tồn tại chưa
            $sql = "SELECT * FROM user WHERE userName = '$userName'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                echo "Tên đăng nhập đã tồn tại! Vui lòng chọn tên khác.";
            } else {
                // Mã hóa mật khẩu trước khi lưu vào database
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $name = randomName($conn);
                $userRole = "user";

                // Thêm người dùng vào database
                $sql = "INSERT INTO user (userName, password, role, name) VALUES ('$userName', '$hashedPassword', '$userRole', '$name')";
                if (mysqli_query($conn, $sql)) {
                    echo "Đăng ký thành công!";
                    header('Location: home.php');
                    $_SESSION['user'] = $userName;
                    $_SESSION['ID'] = $row['ID'];
                    exit();
                } else {
                    echo "Lỗi: " . $sql . "<br>" . mysqli_error($conn);
                }
            }
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login-submit'])) {
        // Lấy dữ liệu từ form
        $userName = filter_input(INPUT_POST, "login-userName", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $_POST['login-password'];

        // Kiểm tra xem tài khoản có tồn tại không
        $sql = "SELECT * FROM user WHERE userName = '$userName'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hashedPassword = $row['password']; // Mật khẩu đã hash trong CSDL

            // So sánh mật khẩu nhập vào với mật khẩu trong CSDL
            if (password_verify($password, $hashedPassword)) {
                // Đăng nhập thành công
                $_SESSION['user'] = $userName;
                $_SESSION['ID'] = $row['ID'];
                $_SESSION['userId'] = $row['ID'];
                $_SESSION['username'] = $userName;

                header("Location: home.php");
                exit();
            } else {
                $error = "Mật khẩu không đúng!";
            }
        } else {
            $error = "Tên đăng nhập không tồn tại!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>
    <div class="background">
        <button class="home-btn" onclick="location.href='Home.php'">Home</button>

        <div class="container">
            <div class="form-box">
                <h2 id="form-title">Log in</h2>

                <!-- Form Đăng nhập -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="login-form" method="post">
                    <div class="form-group">
                        <input name="login-userName" type="text" id="login-username" class="input-field" required>
                        <label class="label" for="login-username">Username</label>
                    </div>
                    <div class="form-group">
                        <input name="login-password" type="password" id="login-password" class="input-field" required>
                        <label class="label" for="login-password">Password</label>
                    </div>
                    <button name="login-submit" type="submit" class="btn">Log in</button>
                    <p>Don't have an account? <a href="#" id="switch-to-register">Signup</a></p>
                </form>

                <!-- Form Đăng ký -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="register-form" style="display: none;" method="post">
                    <div class="form-group">
                        <input name="register-userName" type="text" id="register-username" class="input-field" required>
                        <label class="label" for="register-userName">Username</label>
                    </div>
                    <div class="form-group">
                        <input name="register-password" type="password" id="register-password" class="input-field" required>
                        <label class="label" for="register-password">Password</label>
                    </div>
                    <div class="form-group">
                        <input name="register-confirm-password" type="password" id="register-confirm-password" class="input-field" required>
                        <label class="label" for="register-confirm-password">Confirm Password</label>
                    </div>
                    <button name="register-submit" type="submit" class="btn">Sign up</button>
                    <p>Already have an account? <a href="#" id="switch-to-login">Log in</a></p>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/login.js"></script>
</body>

</html>