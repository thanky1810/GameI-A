/* script.js - Xử lý logic đăng nhập & đăng ký */

document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    const switchToRegister = document.getElementById("switch-to-register");
    const switchToLogin = document.getElementById("switch-to-login");
    const formTitle = document.getElementById("form-title");
    const loginError = document.getElementById("login-error");
    const registerError = document.getElementById("register-error");

    // Hiển thị thông báo lỗi nếu có từ PHP
    if (loginError.textContent.trim() !== "") {
        loginError.classList.add("active");
    }
    if (registerError.textContent.trim() !== "") {
        registerError.classList.add("active");
    }

    // Chuyển sang form đăng ký
    switchToRegister.addEventListener("click", (e) => {
        e.preventDefault();
        loginForm.style.display = "none";
        registerForm.style.display = "block";
        formTitle.textContent = "Sign up";
        loginError.textContent = "";
        loginError.classList.remove("active");
    });

    // Chuyển sang form đăng nhập
    switchToLogin.addEventListener("click", (e) => {
        e.preventDefault();
        registerForm.style.display = "none";
        loginForm.style.display = "block";
        formTitle.textContent = "Log in";
        registerError.textContent = "";
        registerError.classList.remove("active");
    });
});