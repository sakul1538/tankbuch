<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'lukas');
define('DB_PASSWORD', 'passwort');
define('DB_DATABASE', 'tankprotokoll_db');
define("AUTOLOGOUT_TIME",900); // 15min

define('TB_USER', 'user');
define('TB_TANK', 'tankprotokoll_einträge');


function connect_db()
{
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    if (!$conn)
    {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;

}

function connect_pdo()
{
    $DB_SERVER=DB_SERVER;
    $DB_USERNAME=DB_USERNAME;
    $DB_PASSWORD=DB_PASSWORD;
    $DB_DATABASE=DB_DATABASE;

    try {
        $pdo = new PDO("mysql:host=$DB_SERVER;dbname=$DB_DATABASE", $DB_USERNAME, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function close_db($conn)
{
    mysqli_close($conn);
}
?>