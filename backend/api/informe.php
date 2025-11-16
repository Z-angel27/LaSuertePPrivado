<?php
require_once __DIR__ . '/bd.php';

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) json_err('JSON inválido');

$from = $input['from'] ?? null;
$to = $input['to'] ?? null;
$draw_type = !empty($input['draw_type']) ? intval($input['draw_type']) : null;

if(!$from || !$to) json_err('Se requieren fechas desde y hasta');

$params = [];
$where = ' WHERE s.fecha_venta >= ? AND s.fecha_venta <= ? ';
$params[] = $from . ' 00:00:00';
$params[] = $to . ' 23:59:59';
$sql = '';
if($draw_type){ $where .= ' AND d.tipo_sorteo_id = ? '; $params[] = $draw_type; }

$sql = 'SELECT SUM(s.monto) AS total FROM ventas s JOIN sorteos d ON d.id=s.sorteo_id ' . $where;
$stmt = $mysqli->prepare($sql);
// bind params dynamically
{
    $types = '';
    foreach($params as $p){ $types .= is_int($p)?'i':'s'; }
    $tmp = array_merge([$types], $params);
    // call_user_func_array requires references
    $refArr = [];
    foreach($tmp as $k => $v) $refArr[$k] = &$tmp[$k];
    if(!empty($params)) call_user_func_array([$stmt, 'bind_param'], $refArr);
}
$stmt->execute(); $stmt->bind_result($total); $stmt->fetch(); $stmt->close();

$by_type = [];
// agrupado por tipo
$sql2 = 'SELECT dt.id, dt.nombre AS name, SUM(s.monto) as collected FROM ventas s JOIN sorteos d ON d.id=s.sorteo_id JOIN tipos_sorteo dt ON dt.id=d.tipo_sorteo_id ' . $where . ' GROUP BY dt.id, dt.nombre';
$stmt2 = $mysqli->prepare($sql2);
// bind same params
{
    $types = '';
    foreach($params as $p){ $types .= is_int($p)?'i':'s'; }
    $tmp = array_merge([$types], $params);
    $refArr = [];
    foreach($tmp as $k => $v) $refArr[$k] = &$tmp[$k];
    if(!empty($params)) call_user_func_array([$stmt2, 'bind_param'], $refArr);
}
$stmt2->execute(); $res2 = $stmt2->get_result();
while($r = $res2->fetch_assoc()) $by_type[] = $r;
$stmt2->close();

echo json_encode(['total'=>floatval($total),'by_type'=>$by_type]);
