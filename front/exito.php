<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Dashboard | GymMetrics</title>
		<link rel="stylesheet" href="css/exito.css">
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	</head>
	<body>

		<nav class="navbar">
		    <div class="nav-brand">
		        <i class="fa-solid fa-dumbbell brand-icon"></i> GymMetrics
		    </div>
		    <div class="nav-profile">
		        <span class="username">ElOrange12</span>
		        <div class="avatar"><i class="fa-solid fa-user"></i></div>
		    </div>
		</nav>

		<main class="container">
			
			<header class="dashboard-header">
				<h2>Bienvenido, <span class="text-blue">Daniel</span></h2>
				<p class="subtitle">Tu progreso de un vistazo</p>
			</header>

			<div class="card week-card">
				<div class="card-header">
				    <h3><i class="fa-solid fa-calendar-week"></i> Esta Semana</h3>
				    <span class="month-label">Febrero</span>
				</div>
				
				<div class="week-days-container">
				    <div class="day-box">
				        <span class="day-name">Lun</span>
				        <span class="day-num">9</span>
				        <div class="dot-indicator done"></div> </div>
				    <div class="day-box">
				        <span class="day-name">Mar</span>
				        <span class="day-num">10</span>
				        <div class="dot-indicator done"></div>
				    </div>
				    <div class="day-box">
				        <span class="day-name">Mié</span>
				        <span class="day-num">11</span>
				        <div class="dot-indicator rest"></div> </div>
				    <div class="day-box active">
				        <span class="day-name">Jue</span>
				        <span class="day-num">12</span>
				        <div class="dot-indicator pending"></div> </div>
				    <div class="day-box">
				        <span class="day-name">Vie</span>
				        <span class="day-num">13</span>
				        <div class="dot-indicator"></div>
				    </div>
				    <div class="day-box">
				        <span class="day-name">Sáb</span>
				        <span class="day-num">14</span>
				        <div class="dot-indicator"></div>
				    </div>
				    <div class="day-box">
				        <span class="day-name">Dom</span>
				        <span class="day-num">15</span>
				        <div class="dot-indicator"></div>
				    </div>
				</div>
			</div>

			<div class="action-grid-square mt-20">
            
		        <a href="#" class="card square-card highlight-card">
		            <i class="fa-solid fa-bolt square-icon text-blue"></i>
		            <h4>Entrenar</h4>
		            <p>Hoy</p>
		        </a>

		        <a href="#" class="card square-card">
		            <i class="fa-regular fa-calendar-days square-icon"></i>
		            <h4>Calendario</h4>
		            <p>Mensual</p>
		        </a>

		        <a href="#" class="card square-card">
		            <i class="fa-solid fa-list-check square-icon"></i>
		            <h4>Rutinas</h4>
		            <p>Gestionar</p>
		        </a>

		    </div>
		</main>

		<nav class="bottom-nav">
		    <a href="#" class="nav-item active"><i class="fa-solid fa-house"></i><span>Inicio</span></a>
		    <a href="#" class="nav-item"><i class="fa-solid fa-book-journal-whills"></i><span>Rutinas</span></a>
		    <a href="#" class="nav-item"><i class="fa-solid fa-chart-simple"></i><span>Progreso</span></a>
		    <a href="#" class="nav-item"><i class="fa-solid fa-gear"></i><span>Ajustes</span></a>
		</nav>
	</body>
</html>
