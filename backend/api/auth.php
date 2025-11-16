<?php
// auth.php - Validar sesión y devolver usuario/rol
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

echo json_encode([
    'ok' => true,
    'usuario' => $_SESSION['usuario_nombre'],
    'rol' => $_SESSION['usuario_rol']
]);
