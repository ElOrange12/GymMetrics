<?php
// front/calendario.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php';
$user_id = $_SESSION['user_id'];

// 1. GESTIÓN DEL MES Y AÑO A MOSTRAR
$mes_actual = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('n');
$anio_actual = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');

// Variables para navegar
$mes_ant = $mes_actual - 1; $anio_ant = $anio_actual;
if ($mes_ant == 0) { $mes_ant = 12; $anio_ant--; }

$mes_sig = $mes_actual + 1; $anio_sig = $anio_actual;
if ($mes_sig == 13) { $mes_sig = 1; $anio_sig++; }

// Nombres para mostrar en pantalla
$meses_nombres = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$nombre_mes = $meses_nombres[$mes_actual];

// 2. CÁLCULOS DEL CALENDARIO
$hoy = date('Y-m-d');
$dias_en_mes = cal_days_in_month(CAL_GREGORIAN, $mes_actual, $anio_actual);
$primer_dia_mes = sprintf("%04d-%02d-01", $anio_actual, $mes_actual);
$ultimo_dia_mes = sprintf("%04d-%02d-%02d", $anio_actual, $mes_actual, $dias_en_mes);

// Saber qué día de la semana cae el día 1 (1 = Lunes, 7 = Domingo)
$dia_semana_inicio = date('N', strtotime($primer_dia_mes)); 

$dias_map = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

try {
    // 3. OBTENER LA FECHA DE REGISTRO DEL USUARIO
    $stmtRegistro = $pdo->prepare("SELECT fecha_registro FROM usuarios WHERE id = ?");
    $stmtRegistro->execute([$user_id]);
    $fecha_registro = $stmtRegistro->fetchColumn();
    if (!$fecha_registro) $fecha_registro = $hoy; // Por seguridad

    // 4. OBTENER RUTINAS DEL USUARIO
    $stmtRutinas = $pdo->prepare("SELECT dia_semana, es_descanso FROM rutinas WHERE usuario_id = ?");
    $stmtRutinas->execute([$user_id]);
    $rutinas = [];
    foreach ($stmtRutinas->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $rutinas[$r['dia_semana']] = $r['es_descanso'];
    }

    // 5. OBTENER HISTORIAL DE ENTRENAMIENTOS DE ESTE MES
    $stmtHistorial = $pdo->prepare("SELECT DATE(fecha) FROM historial_entrenamientos WHERE usuario_id = ? AND fecha >= ? AND fecha <= ? AND completado = 1");
    $stmtHistorial->execute([$user_id, $primer_dia_mes, $ultimo_dia_mes]);
    $completados = $stmtHistorial->fetchAll(PDO::FETCH_COLUMN);

    // 6. GENERAR LA ESTRUCTURA DEL CALENDARIO
    $calendario_html = "";
    
    // Rellenar espacios vacíos antes del día 1
    for ($i = 1; $i < $dia_semana_inicio; $i++) {
        $calendario_html .= '<div class="cal-day empty"></div>';
    }

    // Rellenar los días del mes
    for ($dia = 1; $dia <= $dias_en_mes; $dia++) {
        $fecha_iter = sprintf("%04d-%02d-%02d", $anio_actual, $mes_actual, $dia);
        $dia_semana_num = date('N', strtotime($fecha_iter));
        $nombre_dia_bd = $dias_map[$dia_semana_num];
        
        $status = 'none';
        $es_hoy_class = ($fecha_iter === $hoy) ? 'today' : '';

        // --- TU REGLA DE ORO ---
        // Si el día iterado es MENOR a la fecha de registro, no hacemos comprobaciones de fallos.
        if ($fecha_iter < $fecha_registro) {
            $status = 'none'; // Antes de existir, no hay color
        } 
        // Si ya estaba registrado, aplicamos la lógica normal
        elseif (isset($rutinas[$nombre_dia_bd])) {
            if ($rutinas[$nombre_dia_bd] == 1) {
                $status = 'rest'; // Descanso
            } else {
                if (in_array($fecha_iter, $completados)) {
                    $status = 'done'; // Verde (Realizado)
                } elseif ($fecha_iter < $hoy) {
                    $status = 'missed'; // Rojo (Pasado y no hecho)
                } else {
                    $status = 'pending'; // Azul (Hoy o Futuro)
                }
            }
        }

        $calendario_html .= "
            <div class='cal-day $es_hoy_class'>
                <span class='cal-num'>$dia</span>
                <div class='dot-indicator $status'></div>
            </div>
        ";
    }

} catch (PDOException $e) {
    die("Error al cargar el calendario: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario | GymMetrics</title>
    <link rel="stylesheet" href="css/exito.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .calendar-container { padding: 20px; max-width: 600px; margin: 0 auto; padding-bottom: 90px;}
        
        .header-cal { display: flex; justify-content: space-between; align-items: center; background: #151b22; padding: 15px 20px; border-radius: 12px 12px 0 0; border: 1px solid #34495e; border-bottom: none;}
        .header-cal h2 { margin: 0; color: white; font-size: 18px; }
        .header-cal a { color: var(--blue-neon); text-decoration: none; font-size: 20px; padding: 5px 10px; transition: 0.2s;}
        .header-cal a:hover { background: rgba(41, 128, 185, 0.2); border-radius: 6px;}

        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); background: #151b22; border: 1px solid #34495e; border-radius: 0 0 12px 12px; padding: 10px; gap: 5px; }
        
        .cal-head { text-align: center; color: var(--text-muted); font-size: 12px; font-weight: bold; padding-bottom: 10px; margin-bottom: 5px; border-bottom: 1px solid #34495e; }
        
        .cal-day { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px 0; border-radius: 8px; position: relative; }
        .cal-day.empty { background: transparent; }
        .cal-day.today { background: rgba(41, 128, 185, 0.15); border: 1px solid var(--blue-neon); }
        .cal-day.today .cal-num { color: var(--blue-neon); font-weight: bold; }
        
        .cal-num { font-size: 15px; color: white; margin-bottom: 5px; }
        
        /* ESTILOS DE LOS PUNTOS DE ESTADO */
        .dot-indicator { width: 6px; height: 6px; border-radius: 50%; margin: 0 auto; display: block; }
        .dot-indicator.rest { background-color: #95a5a6; } /* Gris - Descanso */
        .dot-indicator.pending { background-color: #3498db; } /* Azul - Pendiente */
        .dot-indicator.done { background-color: #2ecc71; } /* Verde - Realizado */
        .dot-indicator.missed { background-color: #e74c3c; } /* Rojo - Perdido */
        .dot-indicator.none { background-color: transparent; } /* Sin rutina o Antes de registro */

        /* Leyenda de colores */
        .legend-box { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-top: 25px; padding: 15px; background: rgba(0,0,0,0.2); border-radius: 8px; border: 1px dashed #34495e; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="exito.php" style="color: white; font-size: 20px; margin-right: 15px;"><i class="fa-solid fa-arrow-left"></i></a>
        <span style="font-weight: bold; font-size: 18px;">CALENDARIO</span>
    </nav>

    <div class="calendar-container">
        
        <div class="header-cal">
            <a href="?mes=<?= $mes_ant ?>&anio=<?= $anio_ant ?>"><i class="fa-solid fa-chevron-left"></i></a>
            <h2><?= mb_strtoupper($nombre_mes) ?> <?= $anio_actual ?></h2>
            <a href="?mes=<?= $mes_sig ?>&anio=<?= $anio_sig ?>"><i class="fa-solid fa-chevron-right"></i></a>
        </div>

        <div class="cal-grid">
            <div class="cal-head">Lun</div>
            <div class="cal-head">Mar</div>
            <div class="cal-head">Mié</div>
            <div class="cal-head">Jue</div>
            <div class="cal-head">Vie</div>
            <div class="cal-head">Sáb</div>
            <div class="cal-head">Dom</div>

            <?= $calendario_html ?>
        </div>

        <div class="legend-box">
            <div class="legend-item"><div class="dot-indicator done"></div> Completado</div>
            <div class="legend-item"><div class="dot-indicator missed"></div> No Hecho</div>
            <div class="legend-item"><div class="dot-indicator pending"></div> Pendiente</div>
            <div class="legend-item"><div class="dot-indicator rest"></div> Descanso</div>
        </div>

    </div>

    <nav class="bottom-nav" style="position: fixed; bottom: 0; left: 0; width: 100%; background: #0f141a; border-top: 1px solid #34495e; display: flex; justify-content: space-around; padding: 15px 0; z-index: 1000;">
        <a href="exito.php" class="nav-item" style="color: var(--text-muted); text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 5px; font-size: 12px; width: 33%;"><i class="fa-solid fa-house" style="font-size: 20px;"></i><span>Inicio</span></a>
        <a href="rutinas.php" class="nav-item" style="color: var(--text-muted); text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 5px; font-size: 12px; width: 33%;"><i class="fa-solid fa-book-journal-whills" style="font-size: 20px;"></i><span>Rutinas</span></a>
        <a href="calendario.php" class="nav-item" style="color: var(--blue-neon); text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 5px; font-size: 12px; width: 33%;"><i class="fa-regular fa-calendar-days" style="font-size: 20px;"></i><span>Calendario</span></a>
    </nav>

</body>
</html>
