<?php
include_once 'sql_conn.php';
include_once 'auth_control.php';
require_once 'error_debug.php';

$pdo_con = connect_pdo();

$sql = "SELECT * FROM `" . TB_TANK . "`";
$stmt = $pdo_con->prepare($sql);
$stmt->execute();
$datestamp= date("Ymd");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="tankprotokoll_' . $datestamp . '.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, ['ID', 'DATUM', 'ZEIT', 'ORT', 'MENGE', 'PREIS', 'KM_STAND']);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
echo file_get_contents("output.csv");

?>

