<?php
	// front/index.php
	session_start();

	// Si ya está logueado, lo mandamos directo al dashboard
	if (isset($_SESSION['user_id'])) {
		header("Location: exito.php");
		exit();
	}
?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Login | GymMetrics</title>
		<link rel="stylesheet" href="css/login.css">
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
		<style>
		    /* Pequeño estilo para los mensajes de error/éxito */
		    .alert {
		        padding: 10px;
		        margin-bottom: 15px;
		        border-radius: 5px;
		        font-size: 14px;
		        text-align: center;
		    }
		    .alert-danger { background-color: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid #e74c3c; }
		    .alert-success { background-color: rgba(46, 204, 113, 0.2); color: #2ecc71; border: 1px solid #2ecc71; }
		</style>
	</head>
	<body>

		<div class="login-container">
		    <div class="login-box">
		        <div class="logo-space">
		            <img src="../img/logo.png" alt="GymMetrics" class="logo" style="max-width: 80px;">
		        </div>
		        
		        <h2>Bienvenido</h2>
		        <p class="subtitle">Gestiona tu entreno con eficiencia</p>

		        <?php if(isset($_GET['error'])): ?>
		            <div class="alert alert-danger">
		                <?php 
		                    if($_GET['error'] == 'credenciales') echo "Usuario o contraseña incorrectos.";
		                    if($_GET['error'] == 'campos_vacios') echo "Por favor, rellena todos los campos.";
		                    if($_GET['error'] == 'nologin') echo "Inicia sesión para continuar.";
		                ?>
		            </div>
		        <?php endif; ?>

		        <?php if(isset($_GET['registro']) && $_GET['registro'] == 'exito'): ?>
		            <div class="alert alert-success">
		                ¡Cuenta creada! Inicia sesión ahora.
		            </div>
		        <?php endif; ?>

		        <form action="../controladores/login_controller.php" method="POST">
		            <div class="input-group">
		                <label for="usuario">Usuario o Email</label>
		                <input type="text" id="usuario" name="usuario" placeholder="Ej: ElOrange12" required>
		            </div>

		            <div class="input-group">
		                <label for="password">Contraseña</label>
		                <input type="password" id="password" name="password" placeholder="••••••••" required>
		            </div>

		            <button type="submit" class="btn-login">ACCEDER</button>
		            
		            <div class="form-footer">
		                <a href="#">¿Olvidaste tu contraseña?</a>
		                <hr>
		                <p>¿No tienes cuenta? <a href="registro.php" class="highlight">Regístrate aquí</a></p>
		            </div>
		        </form>
		    </div>
		</div>
	</body>
</html>
