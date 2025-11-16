<?php
// bd.php - conexión a MySQL (ajusta credenciales si es necesario)
header('Content-Type: application/json; charset=utf-8');
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'la_suerte';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connect error: ' . $mysqli->connect_error]);
    exit;
}
$mysqli->set_charset('utf8mb4');

function json_err($msg){ echo json_encode(['error'=>$msg]); exit; }

function addBusinessDays($dateStr, $days){
    $d = new DateTime($dateStr);
    $added = 0;
    while($added < $days){
        $d->modify('+1 day');
        $wd = (int)$d->format('N'); // 6,7 weekend
        if($wd < 6) $added++;
    }
    return $d->format('Y-m-d');
}
