<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../inc/bd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    
    $user_id = $_SESSION['user_id'];
    $nombre = trim($_POST['nombre_rutina']);
    $dia = $_POST['dia_semana'];

    try {
        // Iniciar una "Transacción" (Si falla algo, no se guarda nada a medias)
        $pdo->beginTransaction();

        // 1. Crear o actualizar la rutina base
        $sql = "INSERT INTO rutinas (usuario_id, dia_semana, es_descanso, nombre_rutina) 
                VALUES (:uid, :dia, 0, :nombre) 
                ON DUPLICATE KEY UPDATE nombre_rutina = :nombre2, es_descanso = 0";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uid' => $user_id, ':dia' => $dia, ':nombre' => $nombre, ':nombre2' => $nombre]);

        // 2. Obtener el ID de la rutina que acabamos de tocar
        $stmt_id = $pdo->prepare("SELECT id FROM rutinas WHERE usuario_id = ? AND dia_semana = ?");
        $stmt_id->execute([$user_id, $dia]);
        $rutina_id = $stmt_id->fetchColumn();

        // 3. Borrar ejercicios viejos si estamos sobreescribiendo una rutina de ese día
        $pdo->prepare("DELETE FROM detalles_rutina WHERE rutina_id = ?")->execute([$rutina_id]);

        // 4. Guardar los nuevos ejercicios (Si el usuario ha añadido alguno)
        if (isset($_POST['ejercicios']) && is_array($_POST['ejercicios'])) {
            $sql_detalles = "INSERT INTO detalles_rutina (rutina_id, ejercicio_id, series_objetivo, reps_objetivo, orden) 
                             VALUES (?, ?, ?, ?, ?)";
            $stmt_detalles = $pdo->prepare($sql_detalles);

            foreach ($_POST['ejercicios'] as $indice => $ejercicio_id) {
                // Verificamos que no hayan enviado una fila vacía
                if (!empty($ejercicio_id)) {
                    $series = $_POST['series'][$indice];
                    $reps = $_POST['reps'][$indice];
                    $orden = $indice + 1; // Para que salgan en el orden que los puso
                    
                    $stmt_detalles->execute([$rutina_id, $ejercicio_id, $series, $reps, $orden]);
                }
            }
        }

        // Si todo ha ido bien, confirmamos los cambios
        $pdo->commit();

        header("Location: ../rutinas.php?creada=exito");
        exit();

    } catch (PDOException $e) {
        // Si hay un error, cancelamos todos los cambios en la BD
        $pdo->rollBack();
        die("Error al guardar la rutina: " . $e->getMessage());
    }
} else {
    header("Location: ../rutinas.php");
    exit();
}
?>
