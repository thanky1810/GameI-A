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
        <!-- Nút Home -->
        <button class="home-btn" onclick="location.href='Home.html'">Home</button>
        
        <div class="container">
            <div class="form-box">
                <!-- Tiêu đề Form -->
                <h2 id="form-title">Log in</h2>
                
                <!-- Form Đăng nhập -->
                <form id="login-form">
                    <div class="form-group">
                        <input type="text" id="login-username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="login-password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn">Log in</button>
                    <p>Don't have an account? <a href="#" id="switch-to-register">Signup</a></p>
                </form>
                
                <!-- Form Đăng ký (Ẩn) --> 
                <form id="register-form" style="display: none;">
                    <div class="form-group">
                        <input type="text" id="register-username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="register-email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="register-password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn">Sign up</button>
                    <p>Already have an account? <a href="#" id="switch-to-login">Log in</a></p>
                </form>
            </div>
        </div>
    </div>
</body>

<script src="../assets/js/login.js"></script>
</html>
