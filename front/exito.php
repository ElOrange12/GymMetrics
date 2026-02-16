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
				<p class="subtitle">¡¡Echa un vistazo a tu progreso!!</p>
			</header>

			<div class="card week-card">
				<div class="card-header">
				    <h3><i class="fa-solid fa-calendar-week"></i> Esta Semana</h3>
				    <span class="month-label">Febrero</span>
				</div>
				
				<div class="week-days-container">
				    <div class="day-box">
				    
				    </div>
				</div>
			</div>

			<div class="action-grid-square mt-20">
            
		        <a href="ejercicioshoy.php" class="card square-card highlight-card">
		            <i class="fa-solid fa-bolt square-icon text-blue"></i>
		            <h4>Entrenar</h4>
		            <p>Hoy</p>
		        </a>

		        <a href="calendario.php" class="card square-card">
		            <i class="fa-regular fa-calendar-days square-icon"></i>
		            <h4>Calendario</h4>
		            <p>Mensual</p>
		        </a>

		        <a href="rutinas.php" class="card square-card">
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
		<script>
			document.addEventListener('DOMContentLoaded', () => {
				// 1. Seleccionamos los contenedores
				const weekContainer = document.querySelector('.week-days-container');
				const monthLabel = document.querySelector('.month-label');
				
				// 2. Obtenemos la fecha actual
				const today = new Date();
				
				// Arrays para nombres en español
				const dayNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
				const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

				// 3. Ponemos el nombre del mes actual en la cabecera
				monthLabel.textContent = monthNames[today.getMonth()];

				// 4. Calcular la fecha del LUNES de esta semana
				const currentDay = today.getDay(); // 0 es Domingo, 1 es Lunes...
				// Si hoy es domingo (0), le restamos 6 días para llegar al lunes pasado. 
				// Si es otro día, restamos (dia - 1).
				const distanceToMonday = currentDay === 0 ? 6 : currentDay - 1;
				
				const mondayDate = new Date(today);
				mondayDate.setDate(today.getDate() - distanceToMonday);

				// 5. Generar el HTML para los 7 días
				let htmlContent = '';

				for (let i = 0; i < 7; i++) {
				    // Crear una copia de la fecha del lunes y sumar 'i' días
				    const loopDate = new Date(mondayDate);
				    loopDate.setDate(mondayDate.getDate() + i);

				    const dayNumber = loopDate.getDate();
				    const dayNameStr = dayNames[i];
				    
				    // Comprobar si 'loopDate' es HOY (comparamos strings para ignorar la hora)
				    const isToday = loopDate.toDateString() === today.toDateString();
				    
				    // Lógica visual para los puntos (Simulación)
				    let dotClass = '';
				    if (loopDate < today && !isToday) {
				        dotClass = 'done'; // Días pasados = Verde
				    } else if (isToday) {
				        dotClass = 'pending'; // Hoy = Azul
				    } else {
				        dotClass = 'rest'; // Futuro = Gris
				    }

				    // Construir el HTML
				    htmlContent += `
				        <div class="day-box ${isToday ? 'active' : ''}">
				            <span class="day-name">${dayNameStr}</span>
				            <span class="day-num">${dayNumber}</span>
				            <div class="dot-indicator ${dotClass}"></div>
				        </div>
				    `;
				}

				// 6. Insertar el HTML en la página
				weekContainer.innerHTML = htmlContent;
			});
		</script>
	</body>
</html>
