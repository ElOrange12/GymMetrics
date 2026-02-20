<?php
// front/entrenar_hoy.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php';
$user_id = $_SESSION['user_id'];

$dias_semana_es = [
    1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
    4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
];
$dia_actual_num = date('N');
$dia_hoy = $dias_semana_es[$dia_actual_num];
$fecha_hoy = date('Y-m-d');

try {
    $stmtHistorial = $pdo->prepare("SELECT * FROM historial_entrenamientos WHERE usuario_id = ? AND fecha = ?");
    $stmtHistorial->execute([$user_id, $fecha_hoy]);
    $entreno_completado = $stmtHistorial->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM rutinas WHERE usuario_id = ? AND dia_semana = ?");
    $stmt->execute([$user_id, $dia_hoy]);
    $rutina_hoy = $stmt->fetch(PDO::FETCH_ASSOC);

    $ejercicios = [];
    if ($rutina_hoy && $rutina_hoy['es_descanso'] == 0) {
        $sqlEj = "SELECT dr.id, e.nombre FROM detalles_rutina dr JOIN ejercicios e ON dr.ejercicio_id = e.id WHERE dr.rutina_id = ? ORDER BY dr.orden";
        $stmtEj = $pdo->prepare($sqlEj);
        $stmtEj->execute([$rutina_hoy['id']]);
        $ejercicios = $stmtEj->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ejercicios as &$ej) {
            $stmtSer = $pdo->prepare("SELECT numero_serie, reps_objetivo, peso_objetivo FROM rutina_series WHERE detalle_rutina_id = ? ORDER BY numero_serie");
            $stmtSer->execute([$ej['id']]);
            $ej['series'] = $stmtSer->fetchAll(PDO::FETCH_ASSOC);
        }
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
    <title>Entrenar Hoy | GymMetrics</title>
    <link rel="stylesheet" href="css/rutinas.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .workout-container { padding: 20px; max-width: 600px; margin: 0 auto; text-align: center; padding-bottom: 100px; }
        
        .rest-timer-box { background: rgba(41, 128, 185, 0.1); border: 2px solid var(--blue-neon); border-radius: 12px; padding: 20px; margin-bottom: 25px; box-shadow: 0 0 15px rgba(41, 128, 185, 0.2); transition: 0.3s; }
        .timer-display-row { display: flex; justify-content: center; align-items: center; gap: 15px; margin: 15px 0; }
        .rest-display { font-size: 56px; font-weight: bold; color: var(--blue-neon); font-family: monospace; line-height: 1; }
        
        .btn-adjust-time { background: transparent; border: 2px solid var(--blue-neon); color: var(--blue-neon); width: 45px; height: 45px; border-radius: 50%; font-size: 16px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .btn-adjust-time:active { background: rgba(41, 128, 185, 0.3); transform: scale(0.95); }

        .rest-controls button { background: transparent; border: 1px solid var(--blue-neon); color: var(--blue-neon); padding: 8px 15px; border-radius: 6px; cursor: pointer; margin: 5px; font-size: 14px; transition: 0.2s; }
        .rest-controls button:hover { background: rgba(41, 128, 185, 0.2); }
        
        .rest-actions button { padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; margin: 0 5px; transition: 0.2s; color: white; display: flex; align-items: center; justify-content: center; gap: 8px;}
        .rest-actions button:active { transform: scale(0.98); }
        
        /* Animación para cuando suena la alarma */
        @keyframes pulse-alarm {
            0% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(231, 76, 60, 0); }
            100% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); }
        }
        .btn-ringing { animation: pulse-alarm 1s infinite; background: #e74c3c !important; }
        
        .global-timer { font-size: 12px; color: var(--text-muted); text-align: right; margin-bottom: 10px; }
        
        .ejercicio-card { background: #151b22; border: 1px solid #34495e; border-radius: 12px; padding: 15px; margin-bottom: 15px; text-align: left; }
        .ejercicio-card h4 { margin-top: 0; color: white; border-bottom: 1px solid #34495e; padding-bottom: 10px; }
        
        .serie-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; background: rgba(0,0,0,0.3); margin-bottom: 5px; border-radius: 6px; border-left: 3px solid #34495e; transition: 0.3s;}
        .btn-check-serie { background: transparent; border: 1px solid #34495e; color: var(--text-muted); width: 30px; height: 30px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
        .btn-check-serie.checked { background: var(--green-success); border-color: var(--green-success); color: white; }
        
        .comments-box { width: 100%; background: #0f141a; border: 1px solid #34495e; color: white; border-radius: 8px; padding: 10px; margin-top: 20px; resize: vertical; min-height: 80px; font-family: 'Roboto', sans-serif;}
        .btn-finish { background: var(--green-success); color: white; border: none; padding: 15px; width: 100%; font-size: 18px; font-weight: bold; border-radius: 8px; cursor: pointer; margin-top: 15px; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4); }
        .completed-badge { background: rgba(39, 174, 96, 0.1); border: 1px solid #2ecc71; padding: 20px; border-radius: 12px; margin-top: 20px; font-size: 16px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="exito.php" style="color: white; font-size: 20px; margin-right: 15px;"><i class="fa-solid fa-arrow-left"></i></a>
        <span style="font-weight: bold; font-size: 18px;">ENTRENAMIENTO (<?= $dia_hoy ?>)</span>
    </nav>

    <div class="workout-container">
        
        <?php if (!$rutina_hoy): ?>
            <h3 style="color: white;">Aún no has configurado tu rutina del <?= $dia_hoy ?>.</h3>
            <a href="rutinas.php" style="color: var(--blue-neon);">Ir a Mis Rutinas</a>
            
        <?php elseif ($rutina_hoy['es_descanso'] == 1): ?>
            <div style="font-size: 60px; color: #95a5a6; margin: 40px 0;"><i class="fa-solid fa-bed"></i></div>
            <h2 style="color: white;">Hoy es día de descanso</h2>
            <p style="color: var(--text-muted);">¡Recupera fuerzas para mañana!</p>

        <?php elseif ($entreno_completado): ?>
            <div class="completed-badge">
                <i class="fa-solid fa-trophy" style="font-size: 40px; color: #2ecc71; margin-bottom: 15px; display: block;"></i>
                <span style="color: white; font-weight: bold; font-size: 18px;">¡Entrenamiento Completado!</span><br>
                <div style="color: var(--text-muted); margin-top: 10px;">
                    <i class="fa-regular fa-clock"></i> <?= $entreno_completado['duracion_minutos'] ?> minutos<br>
                    <?php if(!empty($entreno_completado['comentarios'])): ?>
                        <div style="background: rgba(0,0,0,0.3); padding: 10px; border-radius: 6px; margin-top: 10px; font-style: italic;">
                            "<?= htmlspecialchars($entreno_completado['comentarios']) ?>"
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            
            <div class="global-timer">
                <i class="fa-solid fa-stopwatch"></i> Tiempo total: <span id="globalClock">00:00</span>
            </div>

            <h3 style="color: white; margin-top: 0; margin-bottom: 20px;"><?= htmlspecialchars($rutina_hoy['nombre_rutina']) ?></h3>
            
            <div class="rest-timer-box" id="restTimerBox">
                <div style="color: var(--text-muted); font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Descanso entre series</div>
                
                <div class="timer-display-row">
                    <button type="button" class="btn-adjust-time" onclick="adjustRestTime(-15)">-15</button>
                    <div class="rest-display" id="restDisplay">01:30</div>
                    <button type="button" class="btn-adjust-time" onclick="adjustRestTime(15)">+15</button>
                </div>

                <div class="rest-controls" style="margin-bottom: 20px;">
                    <button type="button" onclick="presetRest(120)">2:00</button>
                    <button type="button" onclick="presetRest(180)">3:00</button>
                    <button type="button" onclick="presetRest(300)">5:00</button>
                </div>

                <div class="rest-actions" style="display: flex; justify-content: center;">
                    <button type="button" id="btnPlayRest" onclick="togglePlayRest()" style="background: var(--green-success); width: 170px;">
                        <i class="fa-solid fa-play"></i> INICIAR
                    </button>
                    <button type="button" onclick="resetRestTimer()" style="background: #e74c3c; width: 60px;">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>

            <?php foreach ($ejercicios as $ej): ?>
                <div class="ejercicio-card">
                    <h4><?= htmlspecialchars($ej['nombre']) ?></h4>
                    <?php foreach ($ej['series'] as $serie): ?>
                        <div class="serie-item">
                            <span style="color: var(--text-muted); width: 60px;">S<?= $serie['numero_serie'] ?></span>
                            <span style="color: white; flex: 1; text-align: center;"><strong><?= $serie['reps_objetivo'] ?></strong> reps</span>
                            <span style="color: var(--blue-neon); flex: 1; text-align: center;">
                                <?php if($serie['peso_objetivo'] > 0) echo $serie['peso_objetivo'] . " kg"; else echo "-"; ?>
                            </span>
                            <button type="button" class="btn-check-serie" onclick="toggleSerie(this)">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <form action="controladores/procesarcompletado.php" method="POST" onsubmit="prepararEnvio()">
                <input type="hidden" name="rutina_id" value="<?= $rutina_hoy['id'] ?>">
                <input type="hidden" name="duracion_minutos" id="inputTiempoTotal" value="0">
                
                <textarea name="comentarios" class="comments-box" placeholder="¿Cómo te sentiste hoy? Ej: Nuevo récord, sentí molestia en el hombro..."></textarea>
                
                <button type="submit" class="btn-finish"><i class="fa-solid fa-check-double"></i> FINALIZAR ENTRENAMIENTO</button>
            </form>
        <?php endif; ?>

    </div>

    <script>
        // --- SONIDO CON WEB AUDIO API ---
        function playNotificationSound() {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(800, audioCtx.currentTime); 
            
            gainNode.gain.setValueAtTime(1, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.8);

            oscillator.start(audioCtx.currentTime);
            oscillator.stop(audioCtx.currentTime + 0.8);
        }

        // --- LÓGICA DEL TIEMPO TOTAL ---
        let workoutStartTime = new Date().getTime();
        let globalClock = document.getElementById('globalClock');
        
        setInterval(() => {
            let now = new Date().getTime();
            let diff = Math.floor((now - workoutStartTime) / 1000);
            let m = Math.floor(diff / 60);
            let s = diff % 60;
            if(globalClock) {
                globalClock.innerText = (m < 10 ? "0"+m : m) + ":" + (s < 10 ? "0"+s : s);
            }
        }, 1000);

        function prepararEnvio() {
            let now = new Date().getTime();
            let diffMinutes = Math.floor((now - workoutStartTime) / (1000 * 60));
            if(diffMinutes < 1) diffMinutes = 1; 
            document.getElementById('inputTiempoTotal').value = diffMinutes;
        }

        // --- LÓGICA DEL TEMPORIZADOR DE DESCANSO MÓVIL ---
        let restInterval = null;
        let alarmInterval = null;
        let isRestRunning = false;
        let isAlarmRinging = false; // Nuevo estado: La alarma está sonando
        
        let currentRestSeconds = 90; 
        let savedRestSeconds = 90; 

        const restDisplay = document.getElementById('restDisplay');
        const btnPlayRest = document.getElementById('btnPlayRest');
        const restTimerBox = document.getElementById('restTimerBox');

        function updateDisplayUI(seconds) {
            let m = Math.floor(seconds / 60);
            let s = seconds % 60;
            restDisplay.innerText = (m < 10 ? "0" + m : m) + ":" + (s < 10 ? "0" + s : s);
        }

        function adjustRestTime(change) {
            if (isRestRunning || isAlarmRinging) return; // Bloquear si está sonando o corriendo
            currentRestSeconds += change;
            if (currentRestSeconds < 0) currentRestSeconds = 0; 
            savedRestSeconds = currentRestSeconds; 
            updateDisplayUI(currentRestSeconds);
        }

        function presetRest(seconds) {
            if (isRestRunning) stopRestTimer();
            if (isAlarmRinging) stopAlarm();
            currentRestSeconds = seconds;
            savedRestSeconds = seconds;
            updateDisplayUI(currentRestSeconds);
        }

        function togglePlayRest() {
            if (isAlarmRinging) {
                // Si está sonando, este botón sirve para APAGARLA
                stopAlarm();
            } else if (isRestRunning) {
                stopRestTimer();
            } else {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                audioCtx.resume();
                startRestTimer();
            }
        }

        function startRestTimer() {
            if (currentRestSeconds <= 0) return;

            isRestRunning = true;
            btnPlayRest.innerHTML = '<i class="fa-solid fa-pause"></i> PAUSAR';
            btnPlayRest.style.background = '#f39c12';
            btnPlayRest.classList.remove('btn-ringing');
            restTimerBox.style.borderColor = 'var(--blue-neon)';

            restInterval = setInterval(() => {
                currentRestSeconds--;
                updateDisplayUI(currentRestSeconds);

                if (currentRestSeconds <= 0) {
                    // SE ACABÓ EL TIEMPO: Iniciar la alarma continua
                    stopRestTimer();
                    startAlarm();
                }
            }, 1000);
        }

        function stopRestTimer() {
            clearInterval(restInterval);
            isRestRunning = false;
            if (!isAlarmRinging) {
                btnPlayRest.innerHTML = '<i class="fa-solid fa-play"></i> INICIAR';
                btnPlayRest.style.background = 'var(--green-success)';
            }
        }

        function resetRestTimer() {
            if (isAlarmRinging) stopAlarm();
            stopRestTimer();
            currentRestSeconds = savedRestSeconds; 
            updateDisplayUI(currentRestSeconds);
        }

        // --- FUNCIONES NUEVAS DE ALARMA CONTINUA ---
        function startAlarm() {
            isAlarmRinging = true;
            currentRestSeconds = savedRestSeconds; // Preparamos el reloj para el próximo uso
            updateDisplayUI(currentRestSeconds);
            
            // Cambios visuales para que sepa que tiene que apagarlo
            btnPlayRest.innerHTML = '<i class="fa-solid fa-bell-slash"></i> APAGAR';
            btnPlayRest.classList.add('btn-ringing');
            restTimerBox.style.borderColor = '#e74c3c';

            // Reproducimos el primer pitido al instante
            playNotificationSound();
            
            // Hacemos que pite cada segundo hasta que lo apague
            alarmInterval = setInterval(() => {
                playNotificationSound();
            }, 1000);
        }

        function stopAlarm() {
            clearInterval(alarmInterval);
            isAlarmRinging = false;
            
            // Volvemos a la normalidad
            btnPlayRest.innerHTML = '<i class="fa-solid fa-play"></i> INICIAR';
            btnPlayRest.style.background = 'var(--green-success)';
            btnPlayRest.classList.remove('btn-ringing');
            restTimerBox.style.borderColor = 'var(--blue-neon)';
        }

        // --- LÓGICA DE LAS SERIES (UX) ---
        function toggleSerie(btn) {
            btn.classList.toggle('checked');
            
            if(btn.classList.contains('checked')) {
                btn.parentElement.style.borderLeftColor = 'var(--green-success)';
                btn.parentElement.style.background = 'rgba(46, 204, 113, 0.1)';
                document.querySelector('.rest-timer-box').scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                btn.parentElement.style.borderLeftColor = '#34495e';
                btn.parentElement.style.background = 'rgba(0,0,0,0.3)';
            }
        }

        updateDisplayUI(currentRestSeconds);
    </script>
</body>
</html>
