<?php
error_reporting(E_ALL);
session_start();
if(!isset($_SESSION['login']))
    {
        $_SESSION['login'] = false;
        $_SESSION['user_id'] = null;
        header('Location: login.php');
        exit;
    }
else {
    if ($_SESSION['login'] == false OR $_SESSION['user_id'] == null)
    {
        header('Location: login.php');
        exit;
    }
}
?>
