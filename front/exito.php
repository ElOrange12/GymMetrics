<?php
session_start();
require_once 'inc/idioma.php'; // <--- MOTOR DE IDIOMAS AÑADIDO

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php';
$user_id = $_SESSION['user_id'];

// [TODA LA LÓGICA PHP DE FECHAS Y CONSULTAS SE MANTIENE EXACTAMENTE IGUAL AQUÍ...]
$hoy = date('Y-m-d');
$dia_semana_actual = date('N'); 
$distancia_lunes = $dia_semana_actual - 1;
$lunes_str = date('Y-m-d', strtotime("-$distancia_lunes days"));
$domingo_str = date('Y-m-d', strtotime("$lunes_str + 6 days"));

$dias_map = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
$dias_nombres_cortos = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

try {
    $stmtUser = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);
    
    $nombre_limpio = isset($userData['nombre_usuario']) ? trim($userData['nombre_usuario']) : '';
    if ($nombre_limpio === '') {
        $nombre_usuario = 'Atleta';
    } else {
        $nombre_usuario = $nombre_limpio;
    }

    $fecha_registro = (!empty($userData['fecha_registro'])) ? $userData['fecha_registro'] : $hoy;

    $stmtRutinas = $pdo->prepare("SELECT dia_semana, es_descanso FROM rutinas WHERE usuario_id = ?");
    $stmtRutinas->execute([$user_id]);
    $rutinas = [];
    foreach ($stmtRutinas->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $rutinas[$r['dia_semana']] = $r['es_descanso'];
    }

    $stmtHistorial = $pdo->prepare("SELECT DATE(fecha) FROM historial_entrenamientos WHERE usuario_id = ? AND fecha >= ? AND fecha <= ? AND completado = 1");
    $stmtHistorial->execute([$user_id, $lunes_str, $domingo_str]);
    $completados = $stmtHistorial->fetchAll(PDO::FETCH_COLUMN);

    $semana_data = [];
    for ($i = 0; $i < 7; $i++) {
        $fecha_iter = date('Y-m-d', strtotime("$lunes_str +$i days"));
        $nombre_dia_bd = $dias_map[$i + 1];
        $status = 'none'; 
        
        if ($fecha_iter < $fecha_registro) {
            $status = 'none'; 
        } elseif (isset($rutinas[$nombre_dia_bd])) {
            if ($rutinas[$nombre_dia_bd] == 1) {
                $status = 'rest'; 
            } else {
                if (in_array($fecha_iter, $completados)) {
                    $status = 'done'; 
                } elseif ($fecha_iter < $hoy) {
                    $status = 'missed'; 
                } else {
                    $status = 'pending'; 
                }
            }
        }

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
<html lang="<?= $lang_code ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GymMetrics</title>
    <link rel="stylesheet" href="css/exito.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dot-indicator { width: 6px; height: 6px; border-radius: 50%; margin: 0 auto; margin-top: 4px; display: block; }
        .dot-indicator.rest { background-color: #95a5a6; } 
        .dot-indicator.pending { background-color: #3498db; } 
        .dot-indicator.done { background-color: #2ecc71; } 
        .dot-indicator.missed { background-color: #e74c3c; } 
        .dot-indicator.none { background-color: transparent; } 
        
        /* Interruptor de idioma en Navbar */
        .lang-switch-nav { display: flex; background: rgba(0,0,0,0.3); border-radius: 20px; overflow: hidden; border: 1px solid #34495e; }
        .lang-switch-nav a { padding: 4px 8px; font-size: 11px; color: #7f8c8d; text-decoration: none; font-weight: bold; }
        .lang-switch-nav a.active { background: var(--blue-neon, #3498db); color: white; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand">
            <i class="fa-solid fa-dumbbell brand-icon"></i> GymMetrics
        </div>
        
        <div class="nav-profile" style="display: flex; align-items: center; gap: 15px;">
            <div class="lang-switch-nav">
                <a href="?lang=es" class="<?= $lang_code == 'es' ? 'active' : '' ?>">ES</a>
                <a href="?lang=en" class="<?= $lang_code == 'en' ? 'active' : '' ?>">EN</a>
            </div>

            <a href="perfil.php" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit; transition: 0.2s;">
                <span class="username"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                <div class="avatar"><i class="fa-solid fa-user"></i></div>
            </a>
            <a href="controladores/logout.php" style="color: #e74c3c; font-size: 20px; text-decoration: none; padding: 5px;" title="<?= $lang['logout'] ?>">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
        </div>
    </nav>

    <main class="container">
        
        <header class="dashboard-header">
            <h2><?= $lang['welcome_user'] ?>, <span class="text-blue"><?php echo htmlspecialchars($nombre_usuario); ?></span></h2>
            <p class="subtitle"><?= $lang['dashboard_subtitle'] ?></p>
        </header>

        <div class="card week-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-calendar-week"></i> <?= $lang['this_week'] ?></h3>
                <span class="month-label"><?= $lang['month_label'] ?></span>
            </div>
            
            <div class="week-days-container"></div>
        </div>

        <div class="action-grid-square mt-20">
            <a href="entrenar_hoy.php" class="card square-card highlight-card">
                <i class="fa-solid fa-bolt square-icon text-blue"></i>
                <h4><?= $lang['train_action'] ?></h4>
                <p><?= $lang['today'] ?></p>
            </a>

            <a href="calendario.php" class="card square-card">
                <i class="fa-regular fa-calendar-days square-icon"></i>
                <h4><?= $lang['calendar_action'] ?></h4>
                <p><?= $lang['monthly'] ?></p>
            </a>

            <a href="rutinas.php" class="card square-card">
                <i class="fa-solid fa-list-check square-icon"></i>
                <h4><?= $lang['routines_action'] ?></h4>
                <p><?= $lang['manage'] ?></p>
            </a>
        </div>
    </main>

    <nav class="bottom-nav">
        <a href="exito.php" class="nav-item active"><i class="fa-solid fa-house"></i><span><?= $lang['nav_home'] ?></span></a>
        <a href="rutinas.php" class="nav-item"><i class="fa-solid fa-book-journal-whills"></i><span><?= $lang['nav_routines'] ?></span></a>
        <a href="calendario.php" class="nav-item"><i class="fa-regular fa-calendar-days"></i><span><?= $lang['nav_calendar'] ?></span></a>
        <a href="perfil.php" class="nav-item"><i class="fa-solid fa-gear"></i><span><?= $lang['nav_settings'] ?></span></a>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const weekContainer = document.querySelector('.week-days-container');
            const monthLabel = document.querySelector('.month-label');
            
            // Aquí podríamos traducir los meses también en un futuro
            const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            monthLabel.textContent = monthNames[new Date().getMonth()];

            const semanaData = <?php echo json_encode($semana_data); ?>;
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

            weekContainer.innerHTML = htmlContent;
        });
    </script>
</body>
</html>
