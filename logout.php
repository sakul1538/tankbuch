<?php
//Mysql query to delete user session
session_start();
require_once 'error_debug.php';
require_once  'sql_conn.php';

try {
    if (isset($_SESSION['user_id']))
    {
        $pdo_conn = connect_pdo();
        $sql = "UPDATE " . TB_USER . " SET LOGIN = :login WHERE ID = :id";
        $stmt = $pdo_conn->prepare($sql);
        $stmt->execute([
                ':login' => 0,
                ':id' => $_SESSION['user_id'],
        ]);

    }
} catch (Exception $e)
{
    error_log($e->getMessage());
    echo 'Ein Fehler ist aufgetreten.';
}
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
?>

