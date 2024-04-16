<?php
    session_start();
    unset($_SESSION["user_id"]);
    unset($_SESSION["completeProfile"]);
    unset($_SESSION["admin"]);
    echo "<script>window.location.replace('login.php')</script>";
?>