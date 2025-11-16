<?php
require_once __DIR__ . '/bd.php';

$date = $_GET['date'] ?? date('Y-m-d');

$res = $mysqli->prepare('SELECT d.id, d.ocurrencia AS occurrence, d.numero_ganador AS winning_number, dt.nombre AS draw_name, dt.tasa_pago AS payout_rate, d.tipo_sorteo_id AS draw_type_id FROM sorteos d JOIN tipos_sorteo dt ON dt.id=d.tipo_sorteo_id WHERE d.fecha_sorteo=? ORDER BY dt.id, d.ocurrencia');
$res->bind_param('s',$date);
$res->execute(); $r = $res->get_result();
$draws = [];
while($row = $r->fetch_assoc()){
    $draw_id = $row['id'];
    // buscar ganadores
    $q = $mysqli->prepare('SELECT s.monto AS amount, c.nombre AS name, c.telefono AS phone, c.fecha_nacimiento AS birthdate FROM ventas s JOIN clientes c ON c.id=s.cliente_id WHERE s.sorteo_id=? AND s.numero=?');
    // but we must fetch using winning_number; if null then no winners
    $winners = [];
    if($row['winning_number']){
        $q->bind_param('is',$draw_id,$row['winning_number']);
        $q->execute(); $q->bind_result($amount,$cname,$cphone,$cbirth);
        while($q->fetch()){
            $prize = $amount * floatval($row['payout_rate']);
            $dmd = (new DateTime($date))->format('m-d');
            $bd = $cbirth ? (new DateTime($cbirth))->format('m-d') : '';
            if($bd && $bd === $dmd) $prize *= 1.10;
            $winners[] = ['client_name'=>$cname,'client_phone'=>$cphone,'amount'=>$amount,'prize'=>round($prize,2)];
        }
    }
    $draws[] = ['id'=>$draw_id,'draw_name'=>$row['draw_name'],'occurrence'=>$row['occurrence'],'winning_number'=>$row['winning_number'],'winners'=>$winners];
    if(isset($q)) $q->close();
}
$res->close();

echo json_encode(['date'=>$date,'draws'=>$draws]);
