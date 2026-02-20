<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php';
$user_id = $_SESSION['user_id'];

// 1. CÁLCULO DE FECHAS SEGURO EN PHP
$hoy = date('Y-m-d');
$dia_semana_actual = date('N'); // 1 (Lunes) a 7 (Domingo)
$distancia_lunes = $dia_semana_actual - 1;
$lunes_str = date('Y-m-d', strtotime("-$distancia_lunes days"));
$domingo_str = date('Y-m-d', strtotime("$lunes_str + 6 days"));

$dias_map = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
$dias_nombres_cortos = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

try {
    // 2. OBTENER RUTINAS
    $stmtRutinas = $pdo->prepare("SELECT dia_semana, es_descanso FROM rutinas WHERE usuario_id = ?");
    $stmtRutinas->execute([$user_id]);
    $rutinas = [];
    foreach ($stmtRutinas->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $rutinas[$r['dia_semana']] = $r['es_descanso'];
    }

    // 3. OBTENER HISTORIAL (Nos aseguramos de coger solo la fecha limpia)
    $stmtHistorial = $pdo->prepare("SELECT DATE(fecha) FROM historial_entrenamientos WHERE usuario_id = ? AND fecha >= ? AND fecha <= ? AND completado = 1");
    $stmtHistorial->execute([$user_id, $lunes_str, $domingo_str]);
    $completados = $stmtHistorial->fetchAll(PDO::FETCH_COLUMN);

    // 4. CONSTRUIR LOS 7 DÍAS CON SU ESTADO EXACTO
    $semana_data = [];
    for ($i = 0; $i < 7; $i++) {
        $fecha_iter = date('Y-m-d', strtotime("$lunes_str +$i days"));
        $nombre_dia_bd = $dias_map[$i + 1];
        
        $status = 'none'; // Vacío
        
        if (isset($rutinas[$nombre_dia_bd])) {
            if ($rutinas[$nombre_dia_bd] == 1) {
                $status = 'rest'; // Gris (Descanso)
            } else {
                if (in_array($fecha_iter, $completados)) {
                    $status = 'done'; // Verde (Hecho)
                } elseif ($fecha_iter < $hoy) {
                    $status = 'missed'; // Rojo (No hecho)
                } else {
                    $status = 'pending'; // Azul (Pendiente)
                }
            }
        }

        // Guardamos todo en un array que JS pueda leer fácilmente
        $semana_data[] = [
            'nombre_corto' => $dias_nombres_cortos[$i],
            'dia_numero' => date('j', strtotime($fecha_iter)),
            'is_today' => ($fecha_iter === $hoy),
            'status' => $status
        ];
    }

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GymMetrics</title>
    <link rel="stylesheet" href="css/exito.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ESTILOS DE LOS PUNTOS DE ESTADO */
        .dot-indicator { width: 6px; height: 6px; border-radius: 50%; margin: 0 auto; margin-top: 4px; display: block; }
        .dot-indicator.rest { background-color: #95a5a6; } /* Gris - Descanso */
        .dot-indicator.pending { background-color: #3498db; } /* Azul - Pendiente */
        .dot-indicator.done { background-color: #2ecc71; } /* Verde - Realizado */
        .dot-indicator.missed { background-color: #e74c3c; } /* Rojo - Perdido */
        .dot-indicator.none { background-color: transparent; } /* Sin rutina */
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand">
            <i class="fa-solid fa-dumbbell brand-icon"></i> GymMetrics
        </div>
        <div class="nav-profile">
            <span class="username">Atleta</span>
            <div class="avatar"><i class="fa-solid fa-user"></i></div>
        </div>
    </nav>

    <main class="container">
        
        <header class="dashboard-header">
            <h2>Bienvenido, <span class="text-blue">Daniel</span></h2>
            <p class="subtitle">¡¡Echa un vistazo a tu progreso!!</p>
        </header>

        <div class="card week-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-calendar-week"></i> Esta Semana</h3>
                <span class="month-label">Mes</span>
            </div>
            
            <div class="week-days-container">
                </div>
        </div>

        <div class="action-grid-square mt-20">
            <a href="entrenar_hoy.php" class="card square-card highlight-card">
                <i class="fa-solid fa-bolt square-icon text-blue"></i>
                <h4>Entrenar</h4>
                <p>Hoy</p>
            </a>

            <a href="calendario.php" class="card square-card">
                <i class="fa-regular fa-calendar-days square-icon"></i>
                <h4>Calendario</h4>
                <p>Mensual</p>
            </a>

            <a href="rutinas.php" class="card square-card">
                <i class="fa-solid fa-list-check square-icon"></i>
                <h4>Rutinas</h4>
                <p>Gestionar</p>
            </a>
        </div>
    </main>

    <nav class="bottom-nav">
        <a href="#" class="nav-item active"><i class="fa-solid fa-house"></i><span>Inicio</span></a>
        <a href="rutinas.php" class="nav-item"><i class="fa-solid fa-book-journal-whills"></i><span>Rutinas</span></a>
        <a href="#" class="nav-item"><i class="fa-solid fa-chart-simple"></i><span>Progreso</span></a>
        <a href="#" class="nav-item"><i class="fa-solid fa-gear"></i><span>Ajustes</span></a>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const weekContainer = document.querySelector('.week-days-container');
            const monthLabel = document.querySelector('.month-label');
            
            // Ponemos el mes actual en texto
            const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            monthLabel.textContent = monthNames[new Date().getMonth()];

            // 1. RECIBIMOS LOS DATOS EXACTOS DE PHP
            const semanaData = <?= json_encode($semana_data) ?>;
            
            // 2. CONSTRUIMOS EL HTML ITERANDO LOS DATOS DE PHP
            let htmlContent = '';
            
            semanaData.forEach(day => {
                htmlContent += `
                    <div class="day-box ${day.is_today ? 'active' : ''}">
                        <span class="day-name">${day.nombre_corto}</span>
                        <span class="day-num">${day.dia_numero}</span>
                        <div class="dot-indicator ${day.status}"></div>
                    </div>
                `;
            });

            // 3. INYECTAMOS EN LA VISTA
            weekContainer.innerHTML = htmlContent;
        });
    </script>
</body>
</html>
