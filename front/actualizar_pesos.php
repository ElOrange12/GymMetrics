<?php
// front/actualizar_pesos.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php';

// Verificamos que nos pasen un ID válido por la URL
$rutina_id = $_GET['id'] ?? null;
if (!$rutina_id) {
    header("Location: rutinas.php");
    exit();
}

try {
    // 1. Verificar que la rutina existe y pertenece al usuario actual
    $stmt = $pdo->prepare("SELECT * FROM rutinas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$rutina_id, $_SESSION['user_id']]);
    $rutina = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rutina) {
        header("Location: rutinas.php"); // Si intenta editar la de otro, lo echamos
        exit();
    }

    // 2. Traer los ejercicios y sus series
    $sqlEjercicios = "SELECT dr.id, e.nombre 
                      FROM detalles_rutina dr 
                      JOIN ejercicios e ON dr.ejercicio_id = e.id 
                      WHERE dr.rutina_id = ? ORDER BY dr.orden";
    $stmtEj = $pdo->prepare($sqlEjercicios);
    $stmtEj->execute([$rutina_id]);
    $ejercicios = $stmtEj->fetchAll(PDO::FETCH_ASSOC);

    foreach ($ejercicios as &$ej) {
        $sqlSeries = "SELECT id, numero_serie, reps_objetivo, peso_objetivo 
                      FROM rutina_series 
                      WHERE detalle_rutina_id = ? ORDER BY numero_serie";
        $stmtSer = $pdo->prepare($sqlSeries);
        $stmtSer->execute([$ej['id']]);
        $ej['series'] = $stmtSer->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Pesos | GymMetrics</title>
    <link rel="stylesheet" href="css/rutinas.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .edit-container { padding: 20px; max-width: 600px; margin: 0 auto; }
        .ejercicio-bloque { background: #151b22; border: 1px solid #34495e; border-radius: 12px; padding: 15px; margin-bottom: 15px; }
        .ej-titulo { color: var(--blue-neon); font-size: 16px; font-weight: bold; border-bottom: 1px solid #34495e; padding-bottom: 10px; margin-bottom: 15px; margin-top: 0; }
        .serie-fila { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; background: rgba(0,0,0,0.2); padding: 8px; border-radius: 8px; }
        .serie-info { color: var(--text-muted); font-size: 14px; }
        .serie-info strong { color: white; }
        .input-peso { width: 80px; padding: 8px; border-radius: 6px; border: 1px solid var(--blue-neon); background: #0f141a; color: white; text-align: center; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="rutinas.php" style="color: white; font-size: 20px; margin-right: 15px;"><i class="fa-solid fa-arrow-left"></i></a>
        <span style="font-weight: bold; font-size: 18px;">ACTUALIZAR PESOS</span>
    </nav>

    <div class="edit-container">
        <h3 style="text-align: center; color: white; margin-bottom: 20px;">
            <?= htmlspecialchars($rutina['nombre_rutina']) ?>
        </h3>

        <form action="controladores/procesarpesos.php" method="POST">
            <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">

            <?php foreach ($ejercicios as $ej): ?>
                <div class="ejercicio-bloque">
                    <h4 class="ej-titulo"><?= htmlspecialchars($ej['nombre']) ?></h4>
                    
                    <?php foreach ($ej['series'] as $serie): ?>
                        <div class="serie-fila">
                            <div class="serie-info">
                                S<?= $serie['numero_serie'] ?>: <strong><?= $serie['reps_objetivo'] ?> reps</strong>
                            </div>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <input type="number" step="0.5" name="pesos[<?= $serie['id'] ?>]" class="input-peso" value="<?= $serie['peso_objetivo'] ?>" required>
                                <span style="color: var(--text-muted); font-size: 12px;">kg</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-save" style="margin-top: 20px; background: #27ae60;">
                <i class="fa-solid fa-check"></i> GUARDAR NUEVOS PESOS
            </button>
        </form>
    </div>

</body>
</html>
