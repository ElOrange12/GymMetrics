<?php
// front/registro.php
session_start();
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
		<title>Registro | GymMetrics</title>
		<link rel="stylesheet" href="css/login.css">
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
		<style>
		    .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 14px; text-align: center; }
		    .alert-danger { background-color: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid #e74c3c; }
		</style>
	</head>
	<body>
		<div class="login-container">
		    <div class="login-box">
		        <h2>Crear Cuenta</h2>
		        <p class="subtitle">Únete a GymMetrics hoy</p>

		        <?php if(isset($_GET['error'])): ?>
		            <div class="alert alert-danger">
		                <?php 
		                    if($_GET['error'] == 'usuario_duplicado') echo "El usuario o correo ya existen.";
		                    if($_GET['error'] == 'email_invalido') echo "El correo no es válido.";
		                    if($_GET['error'] == 'campos_vacios') echo "Rellena todos los datos.";
		                    if($_GET['error'] == 'fallo_bd') echo "Error del sistema. Intenta más tarde.";
		                ?>
		            </div>
		        <?php endif; ?>

		        <form action="controladores/procesaregistro.php" method="POST">
		            
		            <div class="input-group">
		                <label for="usuario">Nombre de Usuario</label>
		                <input type="text" id="usuario" name="usuario" required>
		            </div>

		            <div class="input-group">
		                <label for="email">Correo Electrónico</label>
		                <input type="email" id="email" name="email" required>
		            </div>

		            <div class="input-group">
		                <label for="password">Contraseña</label>
		                <input type="password" id="password" name="password" required>
		            </div>

		            <button type="submit" class="btn-login">REGISTRARSE</button>
		            
		            <div class="form-footer">
		                <hr>
		                <p>¿Ya tienes cuenta? <a href="index.php" class="highlight">Inicia Sesión</a></p>
		            </div>
		        </form>
		    </div>
		</div>
	</body>
</html>
