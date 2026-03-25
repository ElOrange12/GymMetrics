<?php
// front/admin.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'inc/bd.php';
$user_id = $_SESSION['user_id'];

try {
    // 1. Verificar rol
    $stmtCheck = $pdo->prepare("SELECT rol FROM usuarios WHERE id = ?");
    $stmtCheck->execute([$user_id]);
    $rol = $stmtCheck->fetchColumn();

    if ($rol !== 'admin') {
        header("Location: exito.php"); // Expulsar si no es admin
        exit();
    }

    // 2. Traer datos
    $total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $total_entrenos = $pdo->query("SELECT COUNT(*) FROM historial_entrenamientos WHERE completado = 1")->fetchColumn();
    
    $lista_usuarios = $pdo->query("SELECT id, nombre_usuario, email, fecha_registro, rol FROM usuarios ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    $lista_ejercicios = $pdo->query("SELECT id, nombre, grupo_muscular FROM ejercicios ORDER BY grupo_muscular, nombre")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error crítico: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control | GymMetrics</title>
    <link rel="stylesheet" href="css/exito.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-wrap { max-width: 1000px; margin: 0 auto; padding: 20px; padding-bottom: 100px;}
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-box { background: #151b22; border: 1px solid var(--blue-neon); padding: 20px; border-radius: 12px; text-align: center; }
        .stat-box i { font-size: 28px; color: var(--blue-neon); margin-bottom: 10px; }
        .stat-box .num { font-size: 30px; font-weight: bold; color: white; }
        
        .admin-section { background: #151b22; border: 1px solid #34495e; border-radius: 12px; padding: 20px; margin-bottom: 30px; }
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 15px; text-align: left;}
        .admin-table th { padding: 12px; border-bottom: 2px solid #34495e; color: var(--text-muted); text-transform: uppercase; font-size: 12px; }
        .admin-table td { padding: 12px; border-bottom: 1px solid #232d39; color: white; font-size: 14px; }
        
        .btn-red { background: transparent; border: 1px solid #e74c3c; color: #e74c3c; padding: 5px 10px; border-radius: 5px; cursor: pointer; transition: 0.3s;}
        .btn-red:hover { background: #e74c3c; color: white; }
        
        .form-add { display: flex; gap: 10px; margin-top: 15px; align-items: center;}
        .form-add input, .form-add select { padding: 10px; background: #0f141a; border: 1px solid #34495e; color: white; border-radius: 5px; flex: 1;}
        .btn-green { background: #2ecc71; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;}
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand" style="color: #e74c3c;">
            <i class="fa-solid fa-unlock-keyhole"></i> ADMIN
        </div>
        <a href="exito.php" style="color: white; text-decoration: none; font-size: 14px; border: 1px solid #34495e; padding: 5px 15px; border-radius: 20px;">
            <i class="fa-solid fa-arrow-left"></i> Volver a GymMetrics
        </a>
    </nav>

    <div class="admin-wrap">
        <h2>Panel de Control</h2>
        <p style="color: var(--text-muted); margin-bottom: 25px;">Gestión de datos del sistema.</p>

        <div class="grid-stats">
            <div class="stat-box">
                <i class="fa-solid fa-users"></i>
                <div class="num"><?= $total_usuarios ?></div>
                <div style="color: var(--text-muted); font-size: 12px;">USUARIOS</div>
            </div>
            <div class="stat-box">
                <i class="fa-solid fa-trophy"></i>
                <div class="num"><?= $total_entrenos ?></div>
                <div style="color: var(--text-muted); font-size: 12px;">ENTRENOS COMPLETADOS</div>
            </div>
            <div class="stat-box">
                <i class="fa-solid fa-dumbbell"></i>
                <div class="num"><?= count($lista_ejercicios) ?></div>
                <div style="color: var(--text-muted); font-size: 12px;">EJERCICIOS EN BD</div>
            </div>
        </div>

        <div class="admin-section">
            <h3><i class="fa-solid fa-users-gear"></i> Gestión de Usuarios</h3>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <tr><th>ID</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Acción</th></tr>
                    <?php foreach($lista_usuarios as $u): ?>
                    <tr>
                        <td style="color: var(--text-muted);">#<?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td style="color: <?= $u['rol']=='admin' ? '#e74c3c' : '#2ecc71' ?>; text-transform: uppercase; font-size: 11px; font-weight: bold;">
                            <?= $u['rol'] ?>
                        </td>
                        <td>
                            <?php if($u['id'] != $user_id): ?>
                            <form action="controladores/admin_procesar.php" method="POST" style="margin:0;" onsubmit="return confirm('¿Borrar usuario y TODO su historial?');">
                                <input type="hidden" name="accion" value="borrar_usuario">
                                <input type="hidden" name="id_usuario" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn-red"><i class="fa-solid fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div class="admin-section">
            <h3><i class="fa-solid fa-dumbbell"></i> Catálogo de Ejercicios</h3>
            
            <form action="controladores/admin_procesar.php" method="POST" class="form-add">
                <input type="hidden" name="accion" value="nuevo_ejercicio">
                <input type="text" name="nombre_ejercicio" placeholder="Nombre del nuevo ejercicio..." required>
                <select name="grupo_muscular" required>
                    <option value="Pecho">Pecho</option>
                    <option value="Espalda">Espalda</option>
                    <option value="Pierna">Pierna</option>
                    <option value="Hombro">Hombro</option>
                    <option value="Brazos">Brazos</option>
                    <option value="Core">Core</option>
                </select>
                <button type="submit" class="btn-green"><i class="fa-solid fa-plus"></i> Añadir</button>
            </form>

            <div style="max-height: 400px; overflow-y: auto; margin-top: 20px; border-top: 1px solid #34495e; padding-top: 10px;">
                <table class="admin-table">
                    <tr><th>ID</th><th>Nombre</th><th>Grupo</th><th>Acción</th></tr>
                    <?php foreach($lista_ejercicios as $ej): ?>
                    <tr>
                        <td style="color: var(--text-muted);">#<?= $ej['id'] ?></td>
                        <td><?= htmlspecialchars($ej['nombre']) ?></td>
                        <td><?= htmlspecialchars($ej['grupo_muscular']) ?></td>
                        <td>
                            <form action="controladores/admin_procesar.php" method="POST" style="margin:0;" onsubmit="return confirm('¿Seguro? Si alguien lo tiene en su rutina, se borrará en cascada.');">
                                <input type="hidden" name="accion" value="borrar_ejercicio">
                                <input type="hidden" name="id_ejercicio" value="<?= $ej['id'] ?>">
                                <button type="submit" class="btn-red"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
