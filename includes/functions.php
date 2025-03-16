<?php

    function logining($address){
        session_start();
        if(!isset($_SESSION["ID"])){
            header("location: {$address}");
            exit();
        }
    }
?>