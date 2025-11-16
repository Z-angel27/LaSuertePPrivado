<?php
// fix_usuarios.php - Actualizar contraseñas con hashes válidos
$mysqli = new mysqli('127.0.0.1', 'root', '', 'la_suerte');
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$hashes = [
    'admin' => '$2y$12$aJ13pckVMYiZkCn8wLzuuOY5JmvKQ5a.c2tujUV.stooC5Ezx4yKC',
    'empleado1' => '$2y$12$tOobSUUF18ZPU7e81feuCuAuN0E7YhdrPUnLxIKw8TR7HXd3mYmjS'
];

foreach ($hashes as $user => $hash) {
    $stmt = $mysqli->prepare('UPDATE usuarios SET contrasena=? WHERE usuario=?');
    $stmt->bind_param('ss', $hash, $user);
    $stmt->execute();
    echo "✓ Usuario '$user' actualizado\n";
    $stmt->close();
}

// Verificar usuarios
$res = $mysqli->query('SELECT id, usuario, rol, activo FROM usuarios');
echo "\n=== Usuarios en BD ===\n";
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Usuario: {$row['usuario']} | Rol: {$row['rol']} | Activo: {$row['activo']}\n";
}

// Verificar conexión a BD (otras tablas)
$tablas = ['clientes', 'empleados', 'tipos_sorteo', 'sorteos', 'ventas'];
echo "\n=== Tablas en BD ===\n";
foreach ($tablas as $tabla) {
    $res = $mysqli->query("SELECT COUNT(*) as cnt FROM $tabla");
    $row = $res->fetch_assoc();
    echo "✓ $tabla: {$row['cnt']} registros\n";
}

$mysqli->close();
echo "\n✓ Conexión exitosa\n";
?>
