<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Login | OrangeByte Gym App</title>
		<link rel="stylesheet" href="css/login.css">
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
	</head>
	<body>
		<div class="login-container">
		    <div class="login-box">
		        <div class="logo-space">
		            <img src="img/LogoGymMetrics.png" alt="GymMetrics Logo" class="logo">
		        </div>
		        
		        <h2>Bienvenido</h2>
		        <p class="subtitle">Gestiona tu entreno con eficiencia</p>

		        <form>
		            <div class="input-group">
		                <label for="usuario">Usuario</label>
		                <input type="text" id="usuario" required>
		            </div>
		            
		            <div class="input-group">
		                <label for="usuario">Email</label>
		                <input type="text" id="usuario" required>
		            </div>

		            <div class="input-group">
		                <label for="password">Contraseña</label>
		                <input type="password" id="password" required>
		            </div>

		            <button type="button" class="btn-login">ACCEDER</button>
		            
		            <div class="form-footer">
		                <a href="#">¿Olvidaste tu contraseña?</a>
		                <hr>
		                <p>¿Tienes cuenta? <a href="index.php" class="highlight">Inicia sesión aquí</a></p>
		            </div>
		        </form>
		    </div>
		</div>
	</body>
</html>
