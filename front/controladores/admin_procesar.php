<?php
// front/controladores/admin_procesar.php
session_start();
require_once '../inc/bd.php';

// Verificación de seguridad extrema
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmtCheck = $pdo->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmtCheck->execute([$user_id]);
if ($stmtCheck->fetchColumn() !== 'admin') {
    die("Acceso denegado.");
}

$accion = $_POST['accion'] ?? '';

try {
    if ($accion === 'borrar_usuario') {
        $id_borrar = $_POST['id_usuario'];
        // Evitar que el admin se borre a sí mismo
        if ($id_borrar != $user_id) {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id_borrar]);
        }
        header("Location: ../admin.php?msg=user_deleted");
        exit();
    }

    if ($accion === 'nuevo_ejercicio') {
        $nombre = trim($_POST['nombre_ejercicio']);
        $grupo = trim($_POST['grupo_muscular']);
        
        if (!empty($nombre)) {
            $stmt = $pdo->prepare("INSERT INTO ejercicios (nombre, grupo_muscular) VALUES (?, ?)");
            $stmt->execute([$nombre, $grupo]);
        }
        header("Location: ../admin.php?msg=ej_added");
        exit();
    }

    if ($accion === 'borrar_ejercicio') {
        $id_ejercicio = $_POST['id_ejercicio'];
        $stmt = $pdo->prepare("DELETE FROM ejercicios WHERE id = ?");
        $stmt->execute([$id_ejercicio]);
        header("Location: ../admin.php?msg=ej_deleted");
        exit();
    }

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

header("Location: ../admin.php");
exit();
?>
