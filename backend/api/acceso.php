<?php
// login.php - Autenticar usuario
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/bd.php';

$input = json_decode(file_get_contents('php://input'), true);
if(!$input) json_err('JSON inválido');

$usuario = trim($input['usuario'] ?? '');
$contrasena = trim($input['contrasena'] ?? '');

if(!$usuario || !$contrasena) json_err('Usuario y contraseña requeridos');

// Buscar usuario en base de datos
$stmt = $mysqli->prepare('SELECT id, usuario, contrasena, rol, activo FROM usuarios WHERE usuario=? LIMIT 1');
$stmt->bind_param('s', $usuario);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    http_response_code(401);
    json_err('Usuario no encontrado');
}

$user = $result->fetch_assoc();
$stmt->close();

// Verificar contraseña
if(!password_verify($contrasena, $user['contrasena'])) {
    http_response_code(401);
    json_err('Contraseña incorrecta');
}

// Verificar si usuario está activo
if(!$user['activo']) {
    http_response_code(403);
    json_err('Usuario desactivado');
}

// Crear sesión
$_SESSION['usuario_id'] = $user['id'];
$_SESSION['usuario_nombre'] = $user['usuario'];
$_SESSION['usuario_rol'] = $user['rol'];

echo json_encode([
    'ok' => true,
    'usuario' => $user['usuario'],
    'rol' => $user['rol'],
    'mensaje' => 'Sesión iniciada correctamente'
]);
