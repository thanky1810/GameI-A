<?php
    function logining($address){
        session_start();
        if(!isset($_SESSION["ID"])){
            header("location: {$address}");
            exit();
        }
    }

    function logout(){
        session_destroy();
        header("Location: home.php"); 
        exit();
    }
    if (isset($_GET['logout'])) {
        logout();
    }
?>