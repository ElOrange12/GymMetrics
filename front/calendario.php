<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario | GymMetrics</title>
    <link rel="stylesheet" href="css/calendario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav class="navbar">
        <a href="exito.php" class="nav-link">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span style="font-weight: bold; letter-spacing: 1px;">HISTORIAL</span>
        <div style="width: 24px;"></div> </nav>

    <div class="calendar-header">
        <button class="btn-nav-month" id="prevMonth"><i class="fa-solid fa-chevron-left"></i></button>
        <span class="month-label" id="monthDisplay">...</span>
        <button class="btn-nav-month" id="nextMonth"><i class="fa-solid fa-chevron-right"></i></button>
    </div>

    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-val">12</div>
            <div class="stat-lbl">Entrenos</div>
        </div>
        <div class="stat-item">
            <div class="stat-val">85%</div>
            <div class="stat-lbl">Cumplido</div>
        </div>
        <div class="stat-item">
            <div class="stat-val">14h</div>
            <div class="stat-lbl">Tiempo</div>
        </div>
    </div>

    <div class="calendar-container">
        <div class="calendar-grid">
            <div class="weekday">Lun</div>
            <div class="weekday">Mar</div>
            <div class="weekday">Mié</div>
            <div class="weekday">Jue</div>
            <div class="weekday">Vie</div>
            <div class="weekday">Sáb</div>
            <div class="weekday">Dom</div>
        </div>
        
        <div class="calendar-grid" id="calendarDays"></div>
    </div>

    <script>
        const monthDisplay = document.getElementById('monthDisplay');
        const calendarDays = document.getElementById('calendarDays');
        const prevBtn = document.getElementById('prevMonth');
        const nextBtn = document.getElementById('nextMonth');

        // Fecha actual (referencia)
        let currentDate = new Date();
        
        // Mes que estamos visualizando (empieza siendo el actual)
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();

        const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", 
                            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

        function renderCalendar() {
            // 1. Poner nombre del mes
            monthDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;

            // 2. Limpiar días anteriores
            calendarDays.innerHTML = "";

            // 3. Cálculos de fechas
            // Primer día del mes (ej: 1 de Febrero)
            const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
            // Cuántos días tiene el mes (truco: día 0 del siguiente mes es el último de este)
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            
            // Ajuste para que la semana empiece en Lunes (JS devuelve Domingo=0)
            // Queremos: Lunes=0 ... Domingo=6
            let startingDay = firstDayOfMonth.getDay() - 1; 
            if (startingDay === -1) startingDay = 6; // Si es domingo (0 - 1 = -1), lo pasamos a 6

            // 4. Generar celdas vacías previas
            for (let i = 0; i < startingDay; i++) {
                const emptyDiv = document.createElement('div');
                emptyDiv.classList.add('day', 'empty');
                calendarDays.appendChild(emptyDiv);
            }

            // 5. Generar días del mes
            for (let i = 1; i <= daysInMonth; i++) {
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('day');
                dayDiv.textContent = i;

                // Comprobar si es HOY
                if (i === currentDate.getDate() && 
                    currentMonth === currentDate.getMonth() && 
                    currentYear === currentDate.getFullYear()) {
                    dayDiv.classList.add('today');
                } 
                // Simulación visual: Marcar días pasados como "completados" (verde)
                // Solo si el día es menor que hoy en el mes actual, o si es un mes pasado
                else if (
                    (currentYear < currentDate.getFullYear()) || 
                    (currentYear === currentDate.getFullYear() && currentMonth < currentDate.getMonth()) ||
                    (currentYear === currentDate.getFullYear() && currentMonth === currentDate.getMonth() && i < currentDate.getDate())
                ) {
                    dayDiv.classList.add('past');
                }

                calendarDays.appendChild(dayDiv);
            }
        }

        // Eventos de botones
        prevBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });

        nextBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });

        // Iniciar
        renderCalendar();
    </script>
</body>
</html>
