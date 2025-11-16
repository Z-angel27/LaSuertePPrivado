<?php
require_once __DIR__ . '/bd.php';

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) json_err('JSON inválido');

$draw_date = $input['draw_date'] ?? null;
$draw_type = intval($input['draw_type'] ?? 0);
$occurrence = intval($input['occurrence'] ?? 1);
$winning_number = str_pad(preg_replace('/\D/','',$input['winning_number'] ?? ''),2,'0',STR_PAD_LEFT);

if(!$draw_date || !$draw_type || !$winning_number) json_err('Faltan datos para establecer ganador');

// asegurar draw existe
$s = $mysqli->prepare('SELECT id FROM sorteos WHERE fecha_sorteo=? AND tipo_sorteo_id=? AND ocurrencia=? LIMIT 1');
$s->bind_param('sii',$draw_date,$draw_type,$occurrence);
$s->execute(); $s->bind_result($draw_id);
if($s->fetch()){ $s->close();
    $upd = $mysqli->prepare('UPDATE sorteos SET numero_ganador=?, fijado_en=NOW() WHERE id=?');
    $upd->bind_param('si',$winning_number,$draw_id);
    $upd->execute();
    $upd->close();
} else {
    $s->close();
    $ins = $mysqli->prepare('INSERT INTO sorteos (fecha_sorteo, tipo_sorteo_id, ocurrencia, numero_ganador, fijado_en, creado_en) VALUES (?,?,?,?,NOW(),NOW())');
    $ins->bind_param('siis',$draw_date,$draw_type,$occurrence,$winning_number);
    $ins->execute();
    $draw_id = $ins->insert_id;
    $ins->close();
}

// obtener info del tipo de sorteo
$dt = $mysqli->prepare('SELECT nombre, tasa_pago FROM tipos_sorteo WHERE id=? LIMIT 1');
$dt->bind_param('i',$draw_type); $dt->execute(); $dt->bind_result($draw_name,$payout_rate); $dt->fetch(); $dt->close();

// buscar ganadores
$q = $mysqli->prepare('SELECT s.id,s.monto AS amount,c.nombre AS name,c.telefono AS phone,c.fecha_nacimiento AS birthdate FROM ventas s JOIN clientes c ON c.id=s.cliente_id WHERE s.sorteo_id=? AND s.numero=?');
$q->bind_param('is',$draw_id,$winning_number);
$q->execute(); $q->bind_result($sale_id,$amount,$cname,$cphone,$cbirth);
$winners = [];
while($q->fetch()){
    $prize = $amount * floatval($payout_rate);
    // bono 10% si cumple años en la fecha del sorteo
    $dmd = (new DateTime($draw_date))->format('m-d');
    $bd = $cbirth ? (new DateTime($cbirth))->format('m-d') : '';
    if($bd && $bd === $dmd) $prize *= 1.10;
    $winners[] = ['sale_id'=>$sale_id,'client_name'=>$cname,'client_phone'=>$cphone,'amount'=>$amount,'prize'=>round($prize,2)];
}
$q->close();

if(count($winners)===0){
    echo json_encode(['ok'=>true,'message'=>'Ganador desierto','draw'=>['id'=>$draw_id,'draw_date'=>$draw_date,'draw_type'=>$draw_type,'occurrence'=>$occurrence,'winning_number'=>$winning_number]]);
} else {
    echo json_encode(['ok'=>true,'winners'=>$winners,'draw'=>['id'=>$draw_id,'draw_date'=>$draw_date,'draw_type'=>$draw_type,'occurrence'=>$occurrence,'winning_number'=>$winning_number]]);
}
