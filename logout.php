<?php
    session_start();
    unset($_SESSION['logged_in']); //if current session varialbe loggin_in==true, unset it to false
    setcookie(session_name(), "", time() - 360);
    session_destroy();
    header("Location: login.php"); //Navigate back to log in page
?>