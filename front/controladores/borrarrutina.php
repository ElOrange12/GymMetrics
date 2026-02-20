<?php
// front/controladores/borrarrutina.php
session_start();
require_once '../inc/bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    
    $rutina_id = $_POST['rutina_id'];
    $user_id = $_SESSION['user_id']; // Validamos que el usuario sea el dueño

    try {
        // Al borrar la rutina, MySQL borra automáticamente sus ejercicios y series (CASCADE)
        $sql = "DELETE FROM rutinas WHERE id = ? AND usuario_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$rutina_id, $user_id]);

        header("Location: ../rutinas.php?borrado=exito");
        exit();

    } catch (PDOException $e) {
        die("Error al borrar: " . $e->getMessage());
    }
} else {
    header("Location: ../rutinas.php");
    exit();
}
?>
