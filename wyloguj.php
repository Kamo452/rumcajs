<?php 
    session_start();
    session_destroy();
    session_start();
    $_SESSION['wylogowano'] = "wylogowano";
    header("Location: login.php");

?>