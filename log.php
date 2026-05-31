<?php

include_once 'error_debug.php';
include_once 'sql_conn.php';

function write_log($message,$typ)

{
    try {
        $datum = date('d.m.Y');
        $zeit = date('H:m:s');
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'unknown';
        $remote_host = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : 'unknown';
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest';
        $userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';
        $pdo = connect_pdo();
        $sql = "INSERT INTO " . TB_LOG . " (DATUM, ZEIT,IP,REF,REM_HOST,USER_ID,USER_NAME,MESSAGE,TYP) VALUES (:datum, :zeit, :ip, :ref, :remote_host, :user_id, :user_name, :message, :typ)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':datum', $datum);
        $stmt->bindParam(':zeit', $zeit);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':typ', $typ);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':ref', $ref);
        $stmt->bindParam(':remote_host', $remote_host);
        $stmt->bindParam(':user_name', $userName);
        $stmt->execute();
        $stmt = null;
        $pdo = null;
    } catch (Exception $e)
    {
        echo 'Error writing log: ' . $e->getMessage();
        exit;
    }

}


?>