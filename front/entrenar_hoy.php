<?php
// front/entrenar_hoy.php
session_start();
require_once 'inc/idioma.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

require_once 'inc/bd.php';
$user_id   = $_SESSION['user_id'];
$lang_code = $_SESSION['lang'] ?? 'es';

$dias_semana_es = [1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo'];
$dias_display   = ['Lunes'=>$lang['day_monday'],'Martes'=>$lang['day_tuesday'],'Miércoles'=>$lang['day_wednesday'],'Jueves'=>$lang['day_thursday'],'Viernes'=>$lang['day_friday'],'Sábado'=>$lang['day_saturday'],'Domingo'=>$lang['day_sunday']];
$dia_hoy         = $dias_semana_es[date('N')];
$dia_hoy_display = $dias_display[$dia_hoy];
$fecha_hoy       = date('Y-m-d');
$nombre_col      = ($lang_code === 'en') ? 'COALESCE(e.nombre_en, e.nombre)' : 'e.nombre';

try {
    $stmtH = $pdo->prepare("SELECT * FROM historial_entrenamientos WHERE usuario_id = ? AND fecha = ?");
    $stmtH->execute([$user_id, $fecha_hoy]);
    $entreno_completado = $stmtH->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM rutinas WHERE usuario_id = ? AND dia_semana = ?");
    $stmt->execute([$user_id, $dia_hoy]);
    $rutina_hoy = $stmt->fetch(PDO::FETCH_ASSOC);

    $ejercicios = [];
    if ($rutina_hoy && $rutina_hoy['es_descanso'] == 0) {
        $stmtEj = $pdo->prepare("SELECT dr.id, {$nombre_col} AS nombre FROM detalles_rutina dr JOIN ejercicios e ON dr.ejercicio_id = e.id WHERE dr.rutina_id = ? ORDER BY dr.orden");
        $stmtEj->execute([$rutina_hoy['id']]);
        $ejercicios = $stmtEj->fetchAll(PDO::FETCH_ASSOC);
        foreach ($ejercicios as &$ej) {
            $stmtSer = $pdo->prepare("SELECT numero_serie, reps_objetivo, peso_objetivo FROM rutina_series WHERE detalle_rutina_id = ? ORDER BY numero_serie");
            $stmtSer->execute([$ej['id']]);
            $ej['series'] = $stmtSer->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="<?= $lang_code ?>">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['training_today'] ?> | GymMetrics</title>
    <link rel="stylesheet" href="css/rutinas.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .workout-container{padding:20px;max-width:600px;margin:0 auto;text-align:center;padding-bottom:100px}
        .global-timer{font-size:12px;color:var(--text-muted);text-align:right;margin-bottom:10px}
        .rest-timer-box{background:#0d141b;border:2px solid #1f5b8b;border-radius:12px;padding:25px 20px;margin-bottom:25px;text-align:center}
        .rest-timer-box .title{color:#7f8c8d;font-size:13px;letter-spacing:1.5px;margin-bottom:10px;text-transform:uppercase}
        .timer-display-row{display:flex;justify-content:center;align-items:center;gap:15px;margin:15px 0}
        .rest-display{font-size:65px;font-weight:bold;color:#2ea1f8;font-family:monospace;line-height:1.2;margin-bottom:15px}
        .btn-adjust-time{background:transparent;border:2px solid var(--blue-neon);color:var(--blue-neon);width:45px;height:45px;border-radius:50%;font-size:16px;font-weight:bold;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:0.2s}
        .rest-controls{margin-bottom:20px}
        .rest-controls button{background:transparent;border:1px solid #1f5b8b;color:white;padding:8px 18px;border-radius:6px;cursor:pointer;margin:0 4px;font-size:14px}
        .rest-actions{display:flex;justify-content:center;gap:10px}
        #btnPlayRest{background:#0bd161;color:white;border:none;padding:12px 30px;border-radius:6px;font-size:16px;font-weight:bold;cursor:pointer;width:140px}
        .btn-reset-red{background:#f03a47;color:white;border:none;width:45px;border-radius:6px;cursor:pointer;font-size:16px}
        @keyframes pulse-alarm{0%{box-shadow:0 0 0 0 rgba(231,76,60,0.7)}70%{box-shadow:0 0 0 15px rgba(231,76,60,0)}100%{box-shadow:0 0 0 0 rgba(231,76,60,0)}}
        .btn-ringing{animation:pulse-alarm 1s infinite;background:#e74c3c !important}
        .ejercicio-card{background:#151b22;border:1px solid #34495e;border-radius:12px;padding:15px;margin-bottom:15px;text-align:left}
        .ejercicio-card h4{margin-top:0;color:white;border-bottom:1px solid #34495e;padding-bottom:10px}
        .serie-item{display:flex;justify-content:space-between;align-items:center;padding:10px;background:rgba(0,0,0,0.3);margin-bottom:5px;border-radius:6px;border-left:3px solid #34495e;transition:0.3s}
        .btn-check-serie{background:transparent;border:1px solid #34495e;color:var(--text-muted);width:30px;height:30px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:0.3s}
        .btn-check-serie.checked{background:var(--green-success);border-color:var(--green-success);color:white}
        .comments-box{width:100%;background:#0f141a;border:1px solid #34495e;color:white;border-radius:8px;padding:10px;margin-top:20px;resize:vertical;min-height:80px;font-family:'Roboto',sans-serif}
        .btn-finish{background:var(--green-success);color:white;border:none;padding:15px;width:100%;font-size:18px;font-weight:bold;border-radius:8px;cursor:pointer;margin-top:15px;box-shadow:0 4px 15px rgba(39,174,96,0.4)}
        .completed-badge{background:rgba(39,174,96,0.1);border:1px solid #2ecc71;padding:20px;border-radius:12px;margin-top:20px;font-size:16px}
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="exito.php" style="color:white;font-size:20px;margin-right:15px"><i class="fa-solid fa-arrow-left"></i></a>
        <span style="font-weight:bold;font-size:18px"><?= $lang['training_today'] ?> (<?= $dia_hoy_display ?>)</span>
    </nav>
    <div class="workout-container">
        <?php if (!$rutina_hoy): ?>
            <h3 style="color:white"><?= $lang['no_routine_configured'] ?> <?= $dia_hoy_display ?>.</h3>
            <a href="rutinas.php" style="color:var(--blue-neon)"><?= $lang['go_to_routines'] ?></a>
        <?php elseif ($rutina_hoy['es_descanso'] == 1): ?>
            <div style="font-size:60px;color:#95a5a6;margin:40px 0"><i class="fa-solid fa-bed"></i></div>
            <h2 style="color:white"><?= $lang['rest_day_message'] ?></h2>
            <p style="color:var(--text-muted)"><?= $lang['rest_day_submsg'] ?></p>
        <?php elseif ($entreno_completado): ?>
            <div class="completed-badge">
                <i class="fa-solid fa-trophy" style="font-size:40px;color:#2ecc71;margin-bottom:15px;display:block"></i>
                <span style="color:white;font-weight:bold;font-size:18px"><?= $lang['workout_completed'] ?></span><br>
                <div style="color:var(--text-muted);margin-top:10px">
                    <i class="fa-regular fa-clock"></i> <?= $entreno_completado['duracion_minutos'] ?> <?= $lang['minutes'] ?>
                    <?php if(!empty($entreno_completado['comentarios'])): ?>
                        <div style="background:rgba(0,0,0,0.3);padding:10px;border-radius:6px;margin-top:10px;font-style:italic">
                            "<?= htmlspecialchars($entreno_completado['comentarios']) ?>"
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="global-timer"><i class="fa-solid fa-stopwatch"></i> <?= $lang['total_time'] ?>: <span id="globalClock">00:00</span></div>
            <h3 style="color:white;margin-top:0;margin-bottom:20px"><?= htmlspecialchars($rutina_hoy['nombre_rutina']) ?></h3>
            <div class="rest-timer-box" id="restTimerBox">
                <div class="title"><?= $lang['rest_between_sets'] ?></div>
                <div class="timer-display-row">
                    <button type="button" class="btn-adjust-time" onclick="adjustRestTime(-15)">-15</button>
                    <div class="rest-display" id="restDisplay">01:30</div>
                    <button type="button" class="btn-adjust-time" onclick="adjustRestTime(15)">+15</button>
                </div>
                <div class="rest-controls">
                    <button type="button" onclick="presetRest(120)">2:00</button>
                    <button type="button" onclick="presetRest(180)">3:00</button>
                    <button type="button" onclick="presetRest(300)">5:00</button>
                </div>
                <div class="rest-actions">
                    <button type="button" id="btnPlayRest" onclick="togglePlayRest()"><i class="fa-solid fa-play"></i> <?= $lang['start_timer'] ?></button>
                    <button type="button" class="btn-reset-red" onclick="resetRestTimer()"><i class="fa-solid fa-rotate-right"></i></button>
                </div>
            </div>
            <?php foreach ($ejercicios as $ej): ?>
                <div class="ejercicio-card">
                    <h4><?= htmlspecialchars($ej['nombre']) ?></h4>
                    <?php foreach ($ej['series'] as $serie): ?>
                        <div class="serie-item">
                            <span style="color:var(--text-muted);width:60px">S<?= $serie['numero_serie'] ?></span>
                            <span style="color:white;flex:1;text-align:center"><strong><?= $serie['reps_objetivo'] ?></strong> <?= $lang['reps'] ?></span>
                            <span style="color:var(--blue-neon);flex:1;text-align:center"><?= $serie['peso_objetivo'] > 0 ? $serie['peso_objetivo'].' kg' : '-' ?></span>
                            <button type="button" class="btn-check-serie" onclick="toggleSerie(this)"><i class="fa-solid fa-check"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <form action="controladores/procesarcompletado.php" method="POST" onsubmit="prepararEnvio()">
                <input type="hidden" name="rutina_id" value="<?= $rutina_hoy['id'] ?>">
                <input type="hidden" name="duracion_minutos" id="inputTiempoTotal" value="0">
                <textarea name="comentarios" class="comments-box" placeholder="<?= htmlspecialchars($lang['how_you_felt']) ?>"></textarea>
                <button type="submit" class="btn-finish"><i class="fa-solid fa-check-double"></i> <?= $lang['finish_training'] ?></button>
            </form>
        <?php endif; ?>
    </div>
    <script>
        const txtStart = <?= json_encode('<i class="fa-solid fa-play"></i> '  . $lang['start_timer']) ?>;
        const txtPause = <?= json_encode('<i class="fa-solid fa-pause"></i> ' . $lang['pause_timer']) ?>;
        const txtStop  = <?= json_encode('<i class="fa-solid fa-bell-slash"></i> ' . $lang['stop_alarm']) ?>;

        function playNotificationSound(){const a=new(window.AudioContext||window.webkitAudioContext)(),o=a.createOscillator(),g=a.createGain();o.connect(g);g.connect(a.destination);o.type='sine';o.frequency.setValueAtTime(800,a.currentTime);g.gain.setValueAtTime(1,a.currentTime);g.gain.exponentialRampToValueAtTime(0.001,a.currentTime+0.8);o.start(a.currentTime);o.stop(a.currentTime+0.8)}
        let workoutStartTime=new Date().getTime(),globalClock=document.getElementById('globalClock');
        setInterval(()=>{let d=Math.floor((new Date().getTime()-workoutStartTime)/1000),m=Math.floor(d/60),s=d%60;if(globalClock)globalClock.innerText=(m<10?"0"+m:m)+":"+(s<10?"0"+s:s)},1000);
        function prepararEnvio(){let d=Math.floor((new Date().getTime()-workoutStartTime)/60000);if(d<1)d=1;document.getElementById('inputTiempoTotal').value=d}
        let restInterval=null,alarmInterval=null,isRestRunning=false,isAlarmRinging=false,currentRestSeconds=90,savedRestSeconds=90;
        const restDisplay=document.getElementById('restDisplay'),btnPlayRest=document.getElementById('btnPlayRest'),restTimerBox=document.getElementById('restTimerBox');
        function updateDisplayUI(s){let m=Math.floor(s/60),sec=s%60;restDisplay.innerText=(m<10?"0"+m:m)+":"+(sec<10?"0"+sec:sec)}
        function adjustRestTime(c){if(isRestRunning||isAlarmRinging)return;currentRestSeconds+=c;if(currentRestSeconds<0)currentRestSeconds=0;savedRestSeconds=currentRestSeconds;updateDisplayUI(currentRestSeconds)}
        function presetRest(s){if(isRestRunning)stopRestTimer();if(isAlarmRinging)stopAlarm();currentRestSeconds=s;savedRestSeconds=s;updateDisplayUI(s)}
        function togglePlayRest(){if(isAlarmRinging){stopAlarm()}else if(isRestRunning){stopRestTimer()}else{new(window.AudioContext||window.webkitAudioContext)().resume();startRestTimer()}}
        function startRestTimer(){if(currentRestSeconds<=0)return;isRestRunning=true;btnPlayRest.innerHTML=txtPause;btnPlayRest.style.background='#f39c12';btnPlayRest.classList.remove('btn-ringing');restTimerBox.style.borderColor='var(--blue-neon)';restInterval=setInterval(()=>{currentRestSeconds--;updateDisplayUI(currentRestSeconds);if(currentRestSeconds<=0){stopRestTimer();startAlarm()}},1000)}
        function stopRestTimer(){clearInterval(restInterval);isRestRunning=false;if(!isAlarmRinging){btnPlayRest.innerHTML=txtStart;btnPlayRest.style.background='var(--green-success)'}}
        function resetRestTimer(){if(isAlarmRinging)stopAlarm();stopRestTimer();currentRestSeconds=savedRestSeconds;updateDisplayUI(currentRestSeconds)}
        function startAlarm(){isAlarmRinging=true;currentRestSeconds=savedRestSeconds;updateDisplayUI(currentRestSeconds);btnPlayRest.innerHTML=txtStop;btnPlayRest.classList.add('btn-ringing');restTimerBox.style.borderColor='#e74c3c';playNotificationSound();alarmInterval=setInterval(()=>{playNotificationSound()},1000)}
        function stopAlarm(){clearInterval(alarmInterval);isAlarmRinging=false;btnPlayRest.innerHTML=txtStart;btnPlayRest.style.background='var(--green-success)';btnPlayRest.classList.remove('btn-ringing');restTimerBox.style.borderColor='var(--blue-neon)'}
        function toggleSerie(btn){btn.classList.toggle('checked');if(btn.classList.contains('checked')){btn.parentElement.style.borderLeftColor='var(--green-success)';btn.parentElement.style.background='rgba(46,204,113,0.1)';document.querySelector('.rest-timer-box').scrollIntoView({behavior:'smooth',block:'center'})}else{btn.parentElement.style.borderLeftColor='#34495e';btn.parentElement.style.background='rgba(0,0,0,0.3)'}}
        updateDisplayUI(currentRestSeconds);
    </script>
</body>
</html>
