<?php
include 'auth_control.php';

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

