<?php
// front/perfil.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php';
$user_id = $_SESSION['user_id'];
$mensaje = '';

// Si el usuario pulsa el botón de "Guardar Cambios"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = isset($_POST['nombre_usuario']) ? trim($_POST['nombre_usuario']) : '';
    $nuevo_email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (!empty($nuevo_nombre) && !empty($nuevo_email)) {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET nombre_usuario = ?, email = ? WHERE id = ?");
            $stmtUpdate->execute([$nuevo_nombre, $nuevo_email, $user_id]);
            $mensaje = '<div class="alert success"><i class="fa-solid fa-check-circle"></i> Perfil actualizado correctamente.</div>';
        } catch (PDOException $e) {
            $mensaje = '<div class="alert error"><i class="fa-solid fa-triangle-exclamation"></i> Error: Ese nombre o correo ya podrían estar en uso.</div>';
        }
    } else {
        $mensaje = '<div class="alert error"><i class="fa-solid fa-triangle-exclamation"></i> Por favor, no dejes campos vacíos.</div>';
    }
}

// Obtener los datos actuales del usuario para mostrarlos en el formulario
try {
    $stmt = $pdo->prepare("SELECT nombre_usuario, email, fecha_registro FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Por si acaso están vacíos en la BD, evitamos errores
    $nombre_actual = !empty($user['nombre_usuario']) ? $user['nombre_usuario'] : '';
    $email_actual = !empty($user['email']) ? $user['email'] : '';
    $fecha_registro = !empty($user['fecha_registro']) ? date('d/m/Y', strtotime($user['fecha_registro'])) : 'Desconocida';
    
} catch (PDOException $e) {
    die("Error al cargar el perfil: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | GymMetrics</title>
    <link rel="stylesheet" href="css/exito.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-container { padding: 20px; max-width: 600px; margin: 0 auto; padding-bottom: 90px; }
        
        .profile-header-box { text-align: center; margin-bottom: 30px; }
        .profile-avatar-large { font-size: 70px; color: var(--blue-neon, #3498db); background: #151b22; width: 120px; height: 120px; line-height: 120px; border-radius: 50%; margin: 0 auto 15px auto; border: 2px solid #34495e; box-shadow: 0 0 20px rgba(52, 152, 219, 0.2); }
        .profile-header-box h2 { color: white; margin: 0 0 5px 0; font-size: 24px; }
        .profile-header-box p { color: var(--text-muted, #95a5a6); margin: 0; font-size: 14px; }

        .profile-card { background: #151b22; border: 1px solid #34495e; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .profile-card h3 { margin-top: 0; color: white; border-bottom: 1px solid #34495e; padding-bottom: 10px; font-size: 16px; margin-bottom: 20px;}
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: var(--text-muted, #95a5a6); margin-bottom: 8px; font-size: 13px; font-weight: bold; text-transform: uppercase;}
        .form-control { width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #34495e; background: #0f141a; color: white; font-family: 'Roboto', sans-serif; font-size: 15px; box-sizing: border-box; transition: 0.3s;}
        .form-control:focus { outline: none; border-color: var(--blue-neon, #3498db); background: #1a222a;}
        
        .btn-save { background: var(--blue-neon, #3498db); color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);}
        .btn-save:active { transform: scale(0.98); }

        .stats-row { display: flex; justify-content: space-between; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; border: 1px dashed #34495e; color: var(--text-muted); font-size: 14px;}
        .stats-row span { color: white; font-weight: bold; }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px;}
        .alert.success { background: rgba(46, 204, 113, 0.1); border: 1px solid #2ecc71; color: #2ecc71; }
        .alert.error { background: rgba(231, 76, 60, 0.1); border: 1px solid #e74c3c; color: #e74c3c; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="exito.php" style="color: white; font-size: 20px; margin-right: 15px;"><i class="fa-solid fa-arrow-left"></i></a>
        <span style="font-weight: bold; font-size: 18px; letter-spacing: 1px;">MI PERFIL</span>
    </nav>

    <div class="profile-container">
        
        <?php echo $mensaje; ?>

        <div class="profile-header-box">
            <div class="profile-avatar-large">
                <i class="fa-solid fa-user"></i>
            </div>
            <h2><?php echo htmlspecialchars($nombre_actual); ?></h2>
            <p>Atleta de GymMetrics</p>
        </div>

        <div class="profile-card">
            <h3><i class="fa-solid fa-id-card"></i> Datos Personales</h3>
            <form action="perfil.php" method="POST">
                
                <div class="form-group">
                    <label for="nombre_usuario">Nombre de Usuario</label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" value="<?php echo htmlspecialchars($nombre_actual); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email_actual); ?>" required>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> GUARDAR CAMBIOS
                </button>
            </form>
        </div>

        <div class="profile-card">
            <h3><i class="fa-solid fa-chart-pie"></i> Estadísticas de Cuenta</h3>
            <div class="stats-row">
                <div>Miembro desde:</div>
                <span><?php echo $fecha_registro; ?></span>
            </div>
            </div>

    </div>

    <nav class="bottom-nav" style="position: fixed; bottom: 0; left: 0; width: 100%; background: #0f141a; border-top: 1px solid #34495e; display: flex; justify-content: space-around; padding: 15px 0; z-index: 1000;">
        <a href="exito.php" class="nav-item" style="color: var(--text-muted); text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 5px; font-size: 12px; width: 33%;"><i class="fa-solid fa-house" style="font-size: 20px;"></i><span>Inicio</span></a>
        <a href="rutinas.php" class="nav-item" style="color: var(--text-muted); text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 5px; font-size: 12px; width: 33%;"><i class="fa-solid fa-book-journal-whills" style="font-size: 20px;"></i><span>Rutinas</span></a>
        <a href="perfil.php" class="nav-item" style="color: var(--blue-neon, #3498db); text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 5px; font-size: 12px; width: 33%;"><i class="fa-solid fa-gear" style="font-size: 20px;"></i><span>Ajustes</span></a>
    </nav>

</body>
</html>
