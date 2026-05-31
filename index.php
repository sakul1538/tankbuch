<?php
include 'auth_control.php';
require_once 'error_debug.php';

switch ($_SESSION['login'])
{
    case true:
        if($_SESSION['user_id'] != null)
        {
            header('Location: home.php');
        }

        break;
    default:
        header('Location: login.php');
}
?>

