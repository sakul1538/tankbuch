<?php
require_once 'auth_control.php';

    if ($_SESSION['login'] == true and $_SESSION['user_id'] != null)
    {
    header('Location: home.php');
    }
    else
    {
         header('Location: login.php');
    }
?>
