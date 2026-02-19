<?php
session_start();

// 1. SEGURIDAD: Si no hay sesión, al login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php'; // Conectamos a la BD

$user_id = $_SESSION['user_id'];

try {
    // 2. Traer las rutinas del usuario ordenadas por día de la semana
    $sqlRutinas = "SELECT * FROM rutinas WHERE usuario_id = :uid ORDER BY FIELD(dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')";
    $stmtRutinas = $pdo->prepare($sqlRutinas);
    $stmtRutinas->execute([':uid' => $user_id]);
    $mis_rutinas = $stmtRutinas->fetchAll(PDO::FETCH_ASSOC);

    // 3. Traer los ejercicios maestros para el formulario
    $sqlEjercicios = "SELECT id, nombre FROM ejercicios ORDER BY nombre ASC";
    $ejercicios_maestros = $pdo->query($sqlEjercicios)->fetchAll(PDO::FETCH_ASSOC);

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
</head>
<body>

    <nav class="navbar">
        <a href="exito.php" style="color: white; font-size: 20px; margin-right: 15px;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span style="font-weight: bold; font-size: 18px;">MIS RUTINAS</span>
    </nav>

    <div class="routines-container">
        <?php if (count($mis_rutinas) > 0): ?>
            <?php foreach ($mis_rutinas as $rutina): ?>
                <div class="routine-card">
                    <div class="routine-info">
                        <h3><?= htmlspecialchars($rutina['nombre_rutina']) ?></h3>
                        <p><i class="fa-regular fa-calendar"></i> <?= $rutina['dia_semana'] ?></p>
                    </div>
                    <button class="btn-delete"><i class="fa-regular fa-trash-can"></i></button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: var(--text-muted); padding: 20px;">
                Aún no tienes rutinas. ¡Pulsa el botón + para empezar!
            </p>
        <?php endif; ?>
    </div>

    <button class="fab-add" onclick="openModal()">
        <i class="fa-solid fa-plus"></i>
    </button>

    <div class="modal-overlay" id="routineModal">
        <div class="modal-content">
            <button class="btn-close-modal" type="button" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            
            <h2 style="margin-top:0;">Nueva Rutina</h2>
            
            <form action="controladores/procesarutina.php" method="POST">
                
                <div class="form-group">
                    <label class="form-label">Nombre de la Rutina</label>
                    <input type="text" name="nombre_rutina" class="form-input" placeholder="Ej: Pecho Fuerte" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Día Asignado</label>
                    <select name="dia_semana" class="form-select" required>
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                </div>

                <div class="exercises-list" id="exerciseContainer">
                    <label class="form-label">Ejercicios</label>
                    </div>

                <button type="button" class="btn-small-add" onclick="addExerciseField()">
                    <i class="fa-solid fa-plus"></i> Añadir Ejercicio
                </button>

                <button type="submit" class="btn-save" style="margin-top: 20px;">GUARDAR RUTINA COMPLETA</button>
            </form>
        </div>
    </div>

    <template id="ejercicioTemplate">
        <div class="exercise-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr 30px; gap: 10px; margin-bottom: 10px; align-items: center;">
            <select name="ejercicios[]" class="form-select" required style="font-size: 13px; padding: 10px;">
                <option value="">Selecciona...</option>
                <?php foreach($ejercicios_maestros as $ej): ?>
                    <option value="<?= $ej['id'] ?>"><?= htmlspecialchars($ej['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="series[]" class="form-input" placeholder="Series" required style="font-size: 13px; padding: 10px;">
            <input type="text" name="reps[]" class="form-input" placeholder="Reps" required style="font-size: 13px; padding: 10px;">
            <i class="fa-solid fa-trash text-muted" onclick="this.parentElement.remove()" style="cursor:pointer; text-align: center;"></i>
        </div>
    </template>

    <script>
        const modal = document.getElementById('routineModal');
        const container = document.getElementById('exerciseContainer');
        const template = document.getElementById('ejercicioTemplate');

        function openModal() { 
            modal.style.display = 'flex'; 
            // Si está vacío, añadimos el primer ejercicio automáticamente
            if(container.children.length === 1) addExerciseField();
        }
        
        function closeModal() { modal.style.display = 'none'; }
        
        window.onclick = function(event) {
            if (event.target == modal) closeModal();
        }

        function addExerciseField() {
            // Clonamos el contenido de la plantilla oculta y lo pegamos en el contenedor
            const clon = template.content.cloneNode(true);
            container.appendChild(clon);
        }
    </script>
</body>
</html>
