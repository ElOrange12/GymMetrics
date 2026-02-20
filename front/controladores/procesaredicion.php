<?php
// front/controladores/procesaredicion.php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../inc/bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    
    $rutina_id = $_POST['rutina_id'];
    $nuevo_nombre = trim($_POST['nombre_rutina']);
    $user_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // 1. Actualizamos el nombre por si lo ha cambiado
        $stmt_rutina = $pdo->prepare("UPDATE rutinas SET nombre_rutina = ? WHERE id = ? AND usuario_id = ?");
        $stmt_rutina->execute([$nuevo_nombre, $rutina_id, $user_id]);

        // 2. Limpieza total: Borramos los ejercicios antiguos (Las series se borran solas por el CASCADE)
        $stmt_delete = $pdo->prepare("DELETE FROM detalles_rutina WHERE rutina_id = ?");
        $stmt_delete->execute([$rutina_id]);

        // 3. Insertar la nueva estructura
        if (isset($_POST['ejercicios']) && is_array($_POST['ejercicios'])) {
            $stmt_ejercicio = $pdo->prepare("INSERT INTO detalles_rutina (rutina_id, ejercicio_id, orden) VALUES (?, ?, ?)");
            $stmt_serie = $pdo->prepare("INSERT INTO rutina_series (detalle_rutina_id, numero_serie, reps_objetivo, peso_objetivo) VALUES (?, ?, ?, ?)");

            // Recorremos los bloques de ejercicios
            foreach ($_POST['ejercicios'] as $indice => $ejercicio_id) {
                if (!empty($ejercicio_id)) {
                    $orden_ejercicio = $indice + 1;
                    
                    // Insertamos el ejercicio
                    $stmt_ejercicio->execute([$rutina_id, $ejercicio_id, $orden_ejercicio]);
                    $detalle_rutina_id = $pdo->lastInsertId();

                    // Recorremos y guardamos sus series
                    if (isset($_POST['reps'][$indice]) && is_array($_POST['reps'][$indice])) {
                        foreach ($_POST['reps'][$indice] as $set_index => $reps) {
                            // Recuperamos el peso secreto, o le ponemos 0 si es una serie nueva
                            $peso = isset($_POST['pesos'][$indice][$set_index]) ? $_POST['pesos'][$indice][$set_index] : 0;
                            $numero_serie = $set_index + 1;
                            
                            $stmt_serie->execute([$detalle_rutina_id, $numero_serie, $reps, $peso]);
                        }
                    }
                }
            }
        }

        $pdo->commit();
        header("Location: ../rutinas.php?exito=rutina_editada");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error al editar: " . $e->getMessage());
    }
} else {
    header("Location: ../rutinas.php");
    exit();
}
?>
