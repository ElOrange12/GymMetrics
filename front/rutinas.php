<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php';
$user_id = $_SESSION['user_id'];

// Estructura fija de los 7 días
$dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
$rutinas_por_dia = [];
foreach ($dias_semana as $dia) { $rutinas_por_dia[$dia] = null; }

try {
    // 1. Traer las rutinas del usuario
    $sqlRutinas = "SELECT * FROM rutinas WHERE usuario_id = :uid";
    $stmtRutinas = $pdo->prepare($sqlRutinas);
    $stmtRutinas->execute([':uid' => $user_id]);
    $mis_rutinas = $stmtRutinas->fetchAll(PDO::FETCH_ASSOC);

    // Mapearlas al día correspondiente
    foreach ($mis_rutinas as $rutina) {
        // Solo buscamos ejercicios si NO es descanso
        if ($rutina['es_descanso'] == 0) {
            $sqlEjercicios = "SELECT dr.id, dr.orden, e.nombre 
                              FROM detalles_rutina dr 
                              JOIN ejercicios e ON dr.ejercicio_id = e.id 
                              WHERE dr.rutina_id = ? ORDER BY dr.orden";
            $stmtEj = $pdo->prepare($sqlEjercicios);
            $stmtEj->execute([$rutina['id']]);
            $rutina['ejercicios'] = $stmtEj->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rutina['ejercicios'] as &$ejercicio) {
                $sqlSeries = "SELECT numero_serie, reps_objetivo, peso_objetivo 
                              FROM rutina_series 
                              WHERE detalle_rutina_id = ? ORDER BY numero_serie";
                $stmtSer = $pdo->prepare($sqlSeries);
                $stmtSer->execute([$ejercicio['id']]);
                $ejercicio['series'] = $stmtSer->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        $rutinas_por_dia[$rutina['dia_semana']] = $rutina;
    }

    // Traer ejercicios maestros
    $sqlMaestros = "SELECT id, nombre FROM ejercicios ORDER BY nombre ASC";
    $ejercicios_maestros = $pdo->query($sqlMaestros)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Rutinas | GymMetrics</title>
    <link rel="stylesheet" href="css/rutinas.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .routine-card { cursor: pointer; position: relative; z-index: 2; margin-bottom: 10px; }
        .routine-details {
            background: #151b22; border: 1px solid #34495e; border-top: none; border-radius: 0 0 12px 12px;
            padding: 20px; margin-top: -15px; margin-bottom: 20px; display: none;
        }
        .ejercicio-titulo { color: var(--blue-neon); font-size: 15px; margin-bottom: 8px; border-bottom: 1px solid #34495e; padding-bottom: 5px; }
        .serie-badge { display: inline-block; background: #1a2229; border: 1px solid #34495e; padding: 5px 10px; border-radius: 6px; font-size: 12px; margin: 0 5px 5px 0; color: #ecf0f1; }
        .serie-badge span { color: var(--green-success); font-weight: bold; }
        .action-buttons { display: flex; gap: 10px; margin-top: 15px; }
        .btn-action { flex: 1; padding: 10px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; color: white; font-size: 13px; display: flex; align-items: center; justify-content: center; gap: 5px; }
        
        /* Nuevos estilos */
        .day-header { font-size: 14px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; margin-top: 15px; font-weight: bold;}
        .card-empty { background: transparent; border: 1px dashed #34495e; text-align: center; color: var(--text-muted); padding: 15px; border-radius: 12px; cursor: pointer; transition: 0.2s;}
        .card-empty:hover { border-color: var(--blue-neon); color: var(--blue-neon); }
        .card-rest { background: linear-gradient(145deg, #1e272e, #111418); border-color: #2f3640; opacity: 0.8;}
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="exito.php" style="color: white; font-size: 20px; margin-right: 15px;"><i class="fa-solid fa-arrow-left"></i></a>
        <span style="font-weight: bold; font-size: 18px;">SEMANA</span>
    </nav>

    <div class="routines-container" style="padding: 15px;">
        
        <?php foreach ($dias_semana as $dia): ?>
            <div class="day-header"><?= $dia ?></div>
            
            <?php $rutina = $rutinas_por_dia[$dia]; ?>
            
            <?php if (!$rutina): ?>
                <div class="card-empty" onclick="openModal('<?= $dia ?>')">
                    <i class="fa-solid fa-plus"></i> Configurar <?= $dia ?>
                </div>
            
            <?php elseif ($rutina['es_descanso'] == 1): ?>
                <div class="routine-card card-rest">
                    <div class="routine-info">
                        <h3 style="color: #95a5a6;"><i class="fa-solid fa-bed"></i> Día de Descanso</h3>
                    </div>
                    <form action="controladores/borrarrutina.php" method="POST" style="margin:0;" onsubmit="return confirm('¿Borrar descanso?');">
                        <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">
                        <button type="submit" class="btn-delete"><i class="fa-solid fa-xmark"></i></button>
                    </form>
                </div>

            <?php else: ?>
                <div class="routine-card" onclick="toggleDetails(<?= $rutina['id'] ?>)">
                    <div class="routine-info">
                        <h3><?= htmlspecialchars($rutina['nombre_rutina']) ?></h3>
                        <p style="color: var(--blue-neon);"><i class="fa-solid fa-dumbbell"></i> Entreno</p>
                    </div>
                    <form action="controladores/borrarrutina.php" method="POST" style="margin:0;" onsubmit="event.stopPropagation(); return confirm('¿Seguro que quieres borrar la rutina completa?');">
                        <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">
                        <button type="submit" class="btn-delete" onclick="event.stopPropagation();"><i class="fa-regular fa-trash-can"></i></button>
                    </form>
                </div>

                <div class="routine-details" id="detalles-<?= $rutina['id'] ?>">
                    <?php if (isset($rutina['ejercicios']) && count($rutina['ejercicios']) > 0): ?>
                        <?php foreach ($rutina['ejercicios'] as $ej): ?>
                            <div style="margin-bottom: 15px;">
                                <div class="ejercicio-titulo"><?= $ej['orden'] ?>. <?= htmlspecialchars($ej['nombre']) ?></div>
                                <div>
                                    <?php foreach ($ej['series'] as $serie): ?>
                                        <div class="serie-badge">
                                            S<?= $serie['numero_serie'] ?>: <span><?= $serie['reps_objetivo'] ?></span> reps 
                                            <?php if($serie['peso_objetivo'] > 0) echo "| <span>".$serie['peso_objetivo']."</span> kg"; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="action-buttons">
                        <button class="btn-action" style="background:#2980b9;" onclick="window.location.href='editar_rutina.php?id=<?= $rutina['id'] ?>'">
                            <i class="fa-solid fa-pen"></i> Estructura
                        </button>
                        <button class="btn-action" style="background:#27ae60;" onclick="window.location.href='actualizar_pesos.php?id=<?= $rutina['id'] ?>'">
                            <i class="fa-solid fa-weight-hanging"></i> Pesos
                        </button>
                    </div>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
    </div>

    <div class="modal-overlay" id="routineModal">
        <div class="modal-content">
            <button class="btn-close-modal" type="button" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            <h2 style="margin-top:0;">Configurar Día</h2>
            
            <form action="controladores/procesarutina.php" method="POST">
                
                <div class="form-group">
                    <label class="form-label">Día Asignado</label>
                    <select name="dia_semana" id="selectDia" class="form-select" required>
                        <?php foreach($dias_semana as $d): ?>
                            <option value="<?= $d ?>"><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="background: rgba(46, 204, 113, 0.1); padding: 10px; border-radius: 8px; border: 1px solid var(--green-success);">
                    <label style="display: flex; align-items: center; cursor: pointer; color: white;">
                        <input type="checkbox" name="es_descanso" id="checkDescanso" onchange="toggleDescanso()" style="width: 20px; height: 20px; margin-right: 10px;">
                        Marcar como Día de Descanso
                    </label>
                </div>

                <div id="zonaEntrenamiento">
                    <div class="form-group">
                        <label class="form-label">Nombre de la Rutina</label>
                        <input type="text" name="nombre_rutina" id="inputNombre" class="form-input" placeholder="Ej: Espalda y Bíceps">
                    </div>

                    <div class="exercises-list" id="exerciseContainer">
                        <label class="form-label">Ejercicios y Series</label>
                    </div>

                    <button type="button" class="btn-small-add" onclick="addExerciseField()" style="margin-top: 15px;">
                        <i class="fa-solid fa-plus"></i> Añadir Ejercicio
                    </button>
                </div>

                <button type="submit" class="btn-save" style="margin-top: 20px;">GUARDAR CAMBIOS</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('routineModal');
        const container = document.getElementById('exerciseContainer');
        const zonaEntreno = document.getElementById('zonaEntrenamiento');
        const selectDia = document.getElementById('selectDia');
        
        let exerciseIndex = 0;
        const opcionesEjercicios = `
            <option value="">Selecciona un ejercicio...</option>
            <?php foreach($ejercicios_maestros as $ej): ?>
                <option value="<?= $ej['id'] ?>"><?= htmlspecialchars(addslashes($ej['nombre'])) ?></option>
            <?php endforeach; ?>
        `;

        // Ahora le podemos pasar el día por parámetro para que se seleccione solo
        function openModal(diaPredefinido = 'Lunes') { 
            modal.style.display = 'flex'; 
            selectDia.value = diaPredefinido;
            if(container.children.length === 0) addExerciseField();
        }
        
        function closeModal() { modal.style.display = 'none'; }
        
        // Función para ocultar ejercicios si marcan descanso
        function toggleDescanso() {
            const isDescanso = document.getElementById('checkDescanso').checked;
            if (isDescanso) {
                zonaEntrenamiento.style.display = 'none';
                document.getElementById('inputNombre').removeAttribute('required');
            } else {
                zonaEntrenamiento.style.display = 'block';
                document.getElementById('inputNombre').setAttribute('required', 'required');
            }
        }

        function addExerciseField() {
            const div = document.createElement('div');
            div.style.background = 'rgba(0,0,0,0.3)'; div.style.padding = '10px'; div.style.borderRadius = '8px'; div.style.marginBottom = '15px'; div.style.border = '1px solid #34495e';
            div.innerHTML = `
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <select name="ejercicios[${exerciseIndex}]" class="form-select" required style="width: 85%;">${opcionesEjercicios}</select>
                    <i class="fa-solid fa-trash text-muted" onclick="this.parentElement.parentElement.remove()" style="cursor:pointer; padding: 10px;"></i>
                </div>
                <div id="series-container-${exerciseIndex}">${generarFilaSerie(exerciseIndex, 1)}</div>
                <button type="button" class="btn-small-add" style="border-style: solid; font-size: 11px; padding: 5px; margin-top:5px;" onclick="addSerie(${exerciseIndex})"><i class="fa-solid fa-plus"></i> Añadir Serie</button>
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
                <div class="serie-row" style="display: grid; grid-template-columns: 40px 1fr 1fr 30px; gap: 8px; margin-bottom: 5px; align-items: center;">
                    <span style="color:var(--text-muted); font-size:12px; font-weight:bold; text-align:center;">S${numSerie}</span>
                    <input type="number" name="reps[${idEjercicio}][]" class="form-input" placeholder="Reps" required style="padding: 8px;">
                    <input type="number" step="0.5" name="pesos[${idEjercicio}][]" class="form-input" placeholder="kg" style="padding: 8px;">
                    <i class="fa-solid fa-xmark text-muted" onclick="this.parentElement.remove()" style="cursor:pointer; text-align:center;"></i>
                </div>
            `;
        }
        
        // Función para abrir/cerrar el panel de detalles de la rutina
        function toggleDetails(rutinaId) {
            const panel = document.getElementById('detalles-' + rutinaId);
            if (panel.style.display === 'block') {
                panel.style.display = 'none';
            } else {
                panel.style.display = 'block';
            }
        }
    </script>
</body>
</html>
