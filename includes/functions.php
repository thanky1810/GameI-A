<?php
function logining($redirectPage) {
    if (!isset($_SESSION["user"])) {
        return "../Pages/login.php";
    }
    return $redirectPage;
}
?>