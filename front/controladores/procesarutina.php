<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../inc/bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    
    $user_id = $_SESSION['user_id'];
    // Si marcan descanso, forzamos el nombre a "Descanso" o dejamos el que pongan
    $es_descanso = isset($_POST['es_descanso']) ? 1 : 0;
    $nombre = $es_descanso ? "Día de Descanso" : trim($_POST['nombre_rutina']);
    $dia = $_POST['dia_semana'];

    try {
        $pdo->beginTransaction();

        // 1. Guardar la Rutina Base (Entreno o Descanso)
        $sql = "INSERT INTO rutinas (usuario_id, dia_semana, es_descanso, nombre_rutina) 
                VALUES (:uid, :dia, :descanso, :nombre) 
                ON DUPLICATE KEY UPDATE nombre_rutina = :nombre2, es_descanso = :descanso2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $user_id, ':dia' => $dia, ':descanso' => $es_descanso, 
            ':nombre' => $nombre, ':nombre2' => $nombre, ':descanso2' => $es_descanso
        ]);

        // 2. Obtener el ID de la rutina
        $stmt_id = $pdo->prepare("SELECT id FROM rutinas WHERE usuario_id = ? AND dia_semana = ?");
        $stmt_id->execute([$user_id, $dia]);
        $rutina_id = $stmt_id->fetchColumn();

        // 3. Limpiar la rutina vieja SIEMPRE (para que no se queden ejercicios huérfanos)
        $pdo->prepare("DELETE FROM detalles_rutina WHERE rutina_id = ?")->execute([$rutina_id]);

        // 4. Si NO es descanso, guardamos los ejercicios
        if (!$es_descanso && isset($_POST['ejercicios']) && is_array($_POST['ejercicios'])) {
            $stmt_ejercicio = $pdo->prepare("INSERT INTO detalles_rutina (rutina_id, ejercicio_id, orden) VALUES (?, ?, ?)");
            $stmt_serie = $pdo->prepare("INSERT INTO rutina_series (detalle_rutina_id, numero_serie, reps_objetivo, peso_objetivo) VALUES (?, ?, ?, ?)");

            foreach ($_POST['ejercicios'] as $indice => $ejercicio_id) {
                if (!empty($ejercicio_id)) {
                    $orden_ejercicio = $indice + 1;
                    $stmt_ejercicio->execute([$rutina_id, $ejercicio_id, $orden_ejercicio]);
                    $detalle_rutina_id = $pdo->lastInsertId();

                    if (isset($_POST['reps'][$indice]) && is_array($_POST['reps'][$indice])) {
                        foreach ($_POST['reps'][$indice] as $set_index => $reps) {
                            $peso = empty($_POST['pesos'][$indice][$set_index]) ? 0 : $_POST['pesos'][$indice][$set_index];
                            $numero_serie = $set_index + 1;
                            $stmt_serie->execute([$detalle_rutina_id, $numero_serie, $reps, $peso]);
                        }
                    }
                }
            }
        }

        $pdo->commit();
        header("Location: ../rutinas.php?creada=exito");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error al guardar: " . $e->getMessage());
    }
} else {
    header("Location: ../rutinas.php");
    exit();
}
?>
