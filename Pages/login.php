<?php
session_start();
include "../includes/database.php";

// Tự động đăng nhập nếu có cookie và chưa có session
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_pass'])) {
    $cookieUser = $_COOKIE['remember_user'];
    $cookiePass = $_COOKIE['remember_pass'];

    $sql = "SELECT * FROM user WHERE userName = '$cookieUser'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($cookiePass === $row['password']) {
            $_SESSION['user'] = [
                'ID' => $row['ID'],
                'Username' => $cookieUser,
                'Role' => $row['role']
            ];
            header("Location: home.php");
            exit();
        }
    }
}

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

// Xử lý Đăng ký
$register_error = '';
$show_register_form = false; // Biến để kiểm soát hiển thị form đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register-submit'])) {
    $userName = filter_input(INPUT_POST, "register-userName", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['register-password'];
    $confirmPassword = $_POST['register-confirm-password'];

    if ($password !== $confirmPassword) {
        $register_error = "Mật khẩu không khớp! Vui lòng nhập lại.";
        $show_register_form = true;
    } else {
        $sql = "SELECT * FROM user WHERE userName = '$userName'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $register_error = "Tên đăng nhập đã tồn tại! Vui lòng chọn tên khác.";
            $show_register_form = true;
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $name = randomName($conn);
            $userRole = "user";

            $sql = "INSERT INTO user (userName, password, role, name) 
                    VALUES ('$userName', '$hashedPassword', '$userRole', '$name')";
            if (mysqli_query($conn, $sql)) {
                $newID = mysqli_insert_id($conn);
                $_SESSION['user'] = [
                    'ID' => $newID,
                    'Username' => $userName,
                    'Role' => $userRole,
                ];
                header('Location: home.php');
                exit();
            } else {
                $register_error = "Lỗi: " . mysqli_error($conn);
                $show_register_form = true;
            }
        }
    }
}

// Xử lý Đăng nhập
$login_error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login-submit'])) {
    $userName = filter_input(INPUT_POST, "login-userName", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST['login-password'];

    $sql = "SELECT * FROM user WHERE userName = '$userName'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user'] = [
                'ID' => $row['ID'],
                'Username' => $userName,
                'Role' => $row['role']
            ];

            if (isset($_POST['remember'])) {
                setcookie("remember_user", $userName, time() + 86400, "/");
                setcookie("remember_pass", $row['password'], time() + 86400, "/");
            }

            if ($row['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $login_error = "Tài khoản hoặc mật khẩu không đúng!";
        }
    } else {
        $login_error = "Tên đăng nhập không tồn tại!";
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
                <h2 id="form-title"><?php echo $show_register_form ? 'Sign up' : 'Log in'; ?></h2>

                <!-- Form Đăng nhập -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="login-form" method="post" style="<?php echo $show_register_form ? 'display: none;' : 'display: block;'; ?>">
                    <div class="error-message" id="login-error"><?php echo $login_error; ?></div>
                    <div class="form-group">
                        <input name="login-userName" type="text" id="login-username" class="input-field" required>
                        <label class="label" for="login-username">Username</label>
                    </div>
                    <div class="form-group">
                        <input name="login-password" type="password" id="login-password" class="input-field" required>
                        <label class="label" for="login-password">Password</label>
                    </div>
                    <div class="form-group remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <button name="login-submit" type="submit" class="btn">Log in</button>
                    <p>Don't have an account? <a href="#" id="switch-to-register">Signup</a></p>
                </form>

                <!-- Form Đăng ký -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="register-form" method="post" style="<?php echo $show_register_form ? 'display: block;' : 'display: none;'; ?>">
                    <div class="error-message" id="register-error"><?php echo $register_error; ?></div>
                    <div class="form-group">
                        <input name="register-userName" type="text" id="register-username" class="input-field" required>
                        <label class="label" for="register-username">Username</label>
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