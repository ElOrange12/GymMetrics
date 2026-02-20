<?php
// front/controladores/procesarpesos.php
session_start();
require_once '../inc/bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    
    // El array $_POST['pesos'] viene con formato: [id_de_serie => nuevo_peso]
    $pesos_nuevos = $_POST['pesos'] ?? [];

    try {
        $pdo->beginTransaction();

        $sql = "UPDATE rutina_series SET peso_objetivo = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);

        // Recorremos cada input y actualizamos la base de datos
        foreach ($pesos_nuevos as $serie_id => $nuevo_peso) {
            // Asegurarnos de que el peso sea un número (0 si lo dejan vacío)
            $peso = empty($nuevo_peso) ? 0 : floatval($nuevo_peso);
            $stmt->execute([$peso, $serie_id]);
        }

        $pdo->commit();
        header("Location: ../rutinas.php?exito=pesos_actualizados");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error al actualizar pesos: " . $e->getMessage());
    }
} else {
    header("Location: ../rutinas.php");
    exit();
}
?>
