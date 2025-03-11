/* script.js - Xử lý logic đăng nhập & đăng ký */

document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    const switchToRegister = document.getElementById("switch-to-register");
    const switchToLogin = document.getElementById("switch-to-login");
    const formTitle = document.getElementById("form-title");

    // Chuyển sang form đăng ký
    switchToRegister.addEventListener("click", (e) => {
        e.preventDefault();
        loginForm.style.display = "none";
        registerForm.style.display = "block";
        formTitle.textContent = "Sign up";
    });

    // Chuyển sang form đăng nhập
    switchToLogin.addEventListener("click", (e) => {
        e.preventDefault();
        registerForm.style.display = "none";
        loginForm.style.display = "block";
        formTitle.textContent = "Log in";
    });

    // Xử lý đăng ký
    registerForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const username = document.getElementById("register-username").value;
        const email = document.getElementById("register-email").value;
        const password = document.getElementById("register-password").value;
        
        if (username === "" || email === "" || password === "") {
            alert("Vui lòng điền đầy đủ thông tin!");
            return;
        }
        
        alert("Đăng ký thành công! Hãy đăng nhập.");
        registerForm.style.display = "none";
        loginForm.style.display = "block";
        formTitle.textContent = "Log in";
    });

    // Xử lý đăng nhập
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const username = document.getElementById("login-username").value;
        const password = document.getElementById("login-password").value;
        
        if (username === "" || password === "") {
            alert("Vui lòng nhập username và password!");
            return;
        }
        
        alert("Đăng nhập thành công! Chào mừng " + username + "!");
    });
});
