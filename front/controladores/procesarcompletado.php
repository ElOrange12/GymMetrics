<?php
// front/controladores/procesarcompletado.php
session_start();
require_once '../inc/bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    
    $rutina_id = $_POST['rutina_id'];
    $duracion_minutos = (int) $_POST['duracion_minutos'];
    $comentarios = trim($_POST['comentarios']);
    $user_id = $_SESSION['user_id'];
    $fecha_hoy = date('Y-m-d');

    try {
        // Por seguridad, verificamos si ya existe un registro para hoy
        $stmt_check = $pdo->prepare("SELECT id FROM historial_entrenamientos WHERE usuario_id = ? AND fecha = ?");
        $stmt_check->execute([$user_id, $fecha_hoy]);
        
        if ($stmt_check->rowCount() > 0) {
            header("Location: ../entrenar_hoy.php?exito=ya_completado");
            exit();
        }

        // Insertar en la BD usando la estructura de tu tabla
        $sql = "INSERT INTO historial_entrenamientos (usuario_id, rutina_id, fecha, completado, duracion_minutos, comentarios) 
                VALUES (?, ?, ?, 1, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $rutina_id, $fecha_hoy, $duracion_minutos, $comentarios]);

        header("Location: ../entrenar_hoy.php?exito=completado");
        exit();

    } catch (PDOException $e) {
        die("Error al guardar el entrenamiento: " . $e->getMessage());
    }
} else {
    header("Location: ../exito.php");
    exit();
}
?>
