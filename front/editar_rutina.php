<?php
// front/editar_rutina.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php';

$rutina_id = $_GET['id'] ?? null;
if (!$rutina_id) {
    header("Location: rutinas.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM rutinas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$rutina_id, $_SESSION['user_id']]);
    $rutina = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rutina) {
        header("Location: rutinas.php");
        exit();
    }

    $sqlEjercicios = "SELECT dr.id as detalle_id, dr.ejercicio_id, dr.orden, e.nombre 
                      FROM detalles_rutina dr 
                      JOIN ejercicios e ON dr.ejercicio_id = e.id 
                      WHERE dr.rutina_id = ? ORDER BY dr.orden";
    $stmtEj = $pdo->prepare($sqlEjercicios);
    $stmtEj->execute([$rutina_id]);
    $ejercicios_actuales = $stmtEj->fetchAll(PDO::FETCH_ASSOC);

    foreach ($ejercicios_actuales as &$ej) {
        $sqlSeries = "SELECT id, numero_serie, reps_objetivo, peso_objetivo 
                      FROM rutina_series WHERE detalle_rutina_id = ? ORDER BY numero_serie";
        $stmtSer = $pdo->prepare($sqlSeries);
        $stmtSer->execute([$ej['detalle_id']]);
        $ej['series'] = $stmtSer->fetchAll(PDO::FETCH_ASSOC);
    }

    $ejercicios_maestros = $pdo->query("SELECT id, nombre FROM ejercicios ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estructura | GymMetrics</title>
    <link rel="stylesheet" href="css/rutinas.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .edit-container { padding: 20px; max-width: 600px; margin: 0 auto; padding-bottom: 80px;}
        .exercise-block { background: rgba(0,0,0,0.3); padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #34495e; }
        .serie-row { display: grid; grid-template-columns: 40px 1fr 30px; gap: 8px; margin-bottom: 8px; align-items: center; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="rutinas.php" style="color: white; font-size: 20px; margin-right: 15px;"><i class="fa-solid fa-arrow-left"></i></a>
        <span style="font-weight: bold; font-size: 18px;">EDITAR ESTRUCTURA</span>
    </nav>

    <div class="edit-container">
        
        <form action="controladores/procesaredicion.php" method="POST" id="formEdicion">
            <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">
            
            <div class="form-group">
                <label class="form-label">Nombre de la Rutina (<?= $rutina['dia_semana'] ?>)</label>
                <input type="text" name="nombre_rutina" class="form-input" value="<?= htmlspecialchars($rutina['nombre_rutina']) ?>" required>
            </div>

            <div id="exerciseContainer">
                <?php foreach ($ejercicios_actuales as $index => $ej): ?>
                    <div class="exercise-block">
                        <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                            <select name="ejercicios[<?= $index ?>]" class="form-select" required style="width: 85%;">
                                <option value="">Selecciona un ejercicio...</option>
                                <?php foreach($ejercicios_maestros as $ejm): ?>
                                    <option value="<?= $ejm['id'] ?>" <?= ($ejm['id'] == $ej['ejercicio_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ejm['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-trash text-muted" onclick="this.parentElement.parentElement.remove()" style="cursor:pointer; padding: 10px;"></i>
                        </div>
                        
                        <div id="series-container-<?= $index ?>">
                            <?php foreach ($ej['series'] as $sIndex => $serie): ?>
                                <div class="serie-row">
                                    <span style="color:var(--text-muted); font-size:12px; font-weight:bold; text-align:center;">S<?= $sIndex + 1 ?></span>
                                    <input type="number" name="reps[<?= $index ?>][]" class="form-input" value="<?= $serie['reps_objetivo'] ?>" placeholder="Reps" required>
                                    
                                    <input type="hidden" name="pesos[<?= $index ?>][]" value="<?= $serie['peso_objetivo'] ?>">
                                    
                                    <i class="fa-solid fa-xmark text-muted" onclick="this.parentElement.remove()" style="cursor:pointer; text-align:center;"></i>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" class="btn-small-add" style="border-style: solid; font-size: 11px; padding: 5px; margin-top:10px;" onclick="addSerie(<?= $index ?>)">
                            <i class="fa-solid fa-plus"></i> Añadir Serie
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="btn-small-add" onclick="addExerciseField()" style="margin-top: 15px; margin-bottom: 20px;">
                <i class="fa-solid fa-plus"></i> Añadir Ejercicio
            </button>

            <button type="submit" class="btn-save" style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 560px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); z-index: 100;">
                GUARDAR ESTRUCTURA
            </button>
        </form>
    </div>

    <script>
        const container = document.getElementById('exerciseContainer');
        
        // Empezamos el índice de JS justo donde terminó PHP
        let exerciseIndex = <?= count($ejercicios_actuales) ?>;
        
        const opcionesEjercicios = `
            <option value="">Selecciona un ejercicio...</option>
            <?php foreach($ejercicios_maestros as $ej): ?>
                <option value="<?= $ej['id'] ?>"><?= htmlspecialchars(addslashes($ej['nombre'])) ?></option>
            <?php endforeach; ?>
        `;

        function addExerciseField() {
            const div = document.createElement('div');
            div.className = 'exercise-block';
            div.innerHTML = `
                <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                    <select name="ejercicios[${exerciseIndex}]" class="form-select" required style="width: 85%;">${opcionesEjercicios}</select>
                    <i class="fa-solid fa-trash text-muted" onclick="this.parentElement.parentElement.remove()" style="cursor:pointer; padding: 10px;"></i>
                </div>
                <div id="series-container-${exerciseIndex}">
                    ${generarFilaSerie(exerciseIndex, 1)}
                </div>
                <button type="button" class="btn-small-add" style="border-style: solid; font-size: 11px; padding: 5px; margin-top:10px;" onclick="addSerie(${exerciseIndex})">
                    <i class="fa-solid fa-plus"></i> Añadir Serie
                </button>
            `;
            container.appendChild(div);
            exerciseIndex++;
        }

        function addSerie(idEjercicio) {
            const seriesContainer = document.getElementById(`series-container-${idEjercicio}`);
            const numSerieActual = seriesContainer.children.length + 1;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = generarFilaSerie(idEjercicio, numSerieActual);
            seriesContainer.appendChild(tempDiv.firstElementChild);
        }

        function generarFilaSerie(idEjercicio, numSerie) {
            return `
                <div class="serie-row">
                    <span style="color:var(--text-muted); font-size:12px; font-weight:bold; text-align:center;">S${numSerie}</span>
                    <input type="number" name="reps[${idEjercicio}][]" class="form-input" placeholder="Reps" required>
                    <input type="hidden" name="pesos[${idEjercicio}][]" value="0">
                    <i class="fa-solid fa-xmark text-muted" onclick="this.parentElement.remove()" style="cursor:pointer; text-align:center;"></i>
                </div>
            `;
        }
    </script>
</body>
</html>
