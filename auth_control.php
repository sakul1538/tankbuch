<?php
session_start();
require_once 'error_debug.php';
require_once 'sql_conn.php';
require_once 'log.php';
if(!isset($_SESSION['login']))
    {
        $_SESSION['login'] = false;
        $_SESSION['user_id'] = null;
        write_log("User session not initialized, redirecting to login page","INFO");
        header('Location: login.php');
        exit;
    }
else {
    if ($_SESSION['login'] == false OR $_SESSION['user_id'] == null)
    {
        write_log("User session invalid, redirecting to login page","INFO");
        header('Location: login.php');
        exit;
    }

    //Auto logout nach 30min$

    $timestamp = time();
    $pdo_conn= connect_pdo();
    $sql = "SELECT LOGIN FROM ".TB_USER." WHERE ID = :user_id";
    $stmt = $pdo_conn->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $last_login = $user['LOGIN'];
    if($last_login ==0)
    {
        $_SESSION['login'] = false;
        write_log("User last login time is 0, redirecting to login page","INFO");
        header('Location: login.php');
        exit;
    }

    if ($timestamp - $last_login > AUTOLOGOUT_TIME)
    {
        $_SESSION['login'] = false;
        $sql= "UPDATE ".TB_USER." SET LOGIN = 0 WHERE ID = :user_id";
        $stmt = $pdo_conn->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();
        write_log("User auto-logged out due to inactivity","INFO");
        header('Location: login.php');
        exit;
    }

}
?>
