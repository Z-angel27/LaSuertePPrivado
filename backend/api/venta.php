<?php
require_once __DIR__ . '/bd.php';

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) json_err('JSON inválido');

$client_name = trim($input['client_name'] ?? '');
$client_phone = trim($input['client_phone'] ?? '');
$client_birth = trim($input['client_birth'] ?? null);
$draw_type = intval($input['draw_type'] ?? 0);
$occurrence = intval($input['occurrence'] ?? 1);
$number = str_pad(preg_replace('/\D/', '', $input['number'] ?? '0'),2,'0',STR_PAD_LEFT);
$amount = floatval($input['amount'] ?? 0);
$employee = trim($input['employee'] ?? '');
$draw_date = $input['draw_date'] ?? date('Y-m-d');

if(!$client_name || !$client_phone || !$draw_type || !$number || $amount <= 0) json_err('Faltan datos requeridos');

$mysqli->begin_transaction();
try {
    // buscar o crear cliente por teléfono
    $stmt = $mysqli->prepare('SELECT id FROM clientes WHERE telefono = ? LIMIT 1');
    $stmt->bind_param('s',$client_phone);
    $stmt->execute();
    $stmt->bind_result($client_id);
    if($stmt->fetch()){
        $stmt->close();
        // actualizar nombre y birth si vacío
    $u = $mysqli->prepare('UPDATE clientes SET nombre=?, fecha_nacimiento=? WHERE id=?');
    $u->bind_param('ssi',$client_name,$client_birth,$client_id);
        $u->execute(); $u->close();
    } else {
        $stmt->close();
    $ins = $mysqli->prepare('INSERT INTO clientes (nombre,telefono,fecha_nacimiento,creado_en) VALUES (?,?,?,NOW())');
    $ins->bind_param('sss',$client_name,$client_phone,$client_birth);
        $ins->execute();
        $client_id = $ins->insert_id;
        $ins->close();
    }

    // obtener draw (crear si no existe)
    $s = $mysqli->prepare('SELECT id FROM sorteos WHERE fecha_sorteo=? AND tipo_sorteo_id=? AND ocurrencia=? LIMIT 1');
    $s->bind_param('sii',$draw_date,$draw_type,$occurrence);
    $s->execute(); $s->bind_result($draw_id);
    if($s->fetch()) { $s->close(); }
    else {
        $s->close();
    $insd = $mysqli->prepare('INSERT INTO sorteos (fecha_sorteo, tipo_sorteo_id, ocurrencia, creado_en) VALUES (?,?,?,NOW())');
    $insd->bind_param('sii',$draw_date,$draw_type,$occurrence);
        $insd->execute();
        $draw_id = $insd->insert_id;
        $insd->close();
    }

    // calcular claim deadline: 5 días hábiles desde la fecha del sorteo
    $claim_deadline = addBusinessDays($draw_date,5);

    // insertar venta
    $insv = $mysqli->prepare('INSERT INTO ventas (cliente_id, sorteo_id, numero, monto, empleado, fecha_venta, fecha_limite_reclamo) VALUES (?,?,?,?,?,NOW(),?)');
    $insv->bind_param('iisdss',$client_id,$draw_id,$number,$amount,$employee,$claim_deadline);
    $insv->execute();
    $sale_id = $insv->insert_id;
    $insv->close();

    $mysqli->commit();

    // devolver voucher
    $dt = $mysqli->query('SELECT dt.nombre as draw_name FROM tipos_sorteo dt JOIN sorteos d ON d.tipo_sorteo_id=dt.id WHERE d.id='.intval($draw_id).' LIMIT 1')->fetch_assoc();
    $voucher = [
        'sale_id'=>$sale_id,
        'client_name'=>$client_name,
        'client_phone'=>$client_phone,
        'draw_name'=>$dt['draw_name'] ?? '',
        'draw_date'=>$draw_date,
        'occurrence'=>$occurrence,
        'number'=>$number,
        'amount'=>$amount,
        'employee'=>$employee,
        'claim_deadline'=>$claim_deadline
    ];
    echo json_encode(['ok'=>true,'voucher'=>$voucher]);

} catch (Exception $e){
    $mysqli->rollback();
    json_err('Error al guardar venta: '.$e->getMessage());
}
