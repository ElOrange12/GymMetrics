<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Rutinas | GymMetrics</title>
    <link rel="stylesheet" href="css/rutinas.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav class="navbar">
        <a href="exito.php" style="color: white; font-size: 20px; margin-right: 15px;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <span style="font-weight: bold; font-size: 18px;">MIS RUTINAS</span>
    </nav>

    <div class="routines-container">
        
        <div class="routine-card">
            <div class="routine-info">
                <h3>Pecho y Tríceps</h3>
                <p><i class="fa-regular fa-calendar"></i> Lunes</p>
            </div>
            <button class="btn-delete"><i class="fa-regular fa-trash-can"></i></button>
        </div>

        <div class="routine-card">
            <div class="routine-info">
                <h3>Espalda y Bíceps</h3>
                <p><i class="fa-regular fa-calendar"></i> Martes</p>
            </div>
            <button class="btn-delete"><i class="fa-regular fa-trash-can"></i></button>
        </div>

        <div class="routine-card">
            <div class="routine-info">
                <h3>Pierna Completa</h3>
                <p><i class="fa-regular fa-calendar"></i> Jueves</p>
            </div>
            <button class="btn-delete"><i class="fa-regular fa-trash-can"></i></button>
        </div>

    </div>

    <button class="fab-add" onclick="openModal()">
        <i class="fa-solid fa-plus"></i>
    </button>

    <div class="modal-overlay" id="routineModal">
        <div class="modal-content">
            <button class="btn-close-modal" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            
            <h2 style="margin-top:0;">Nueva Rutina</h2>
            
            <form action="#" method="POST"> <div class="form-group">
                    <label class="form-label">Nombre de la Rutina</label>
                    <input type="text" class="form-input" placeholder="Ej: Hombro y Abdomen">
                </div>

                <div class="form-group">
                    <label class="form-label">Día Asignado</label>
                    <select class="form-select">
                        <option>Lunes</option>
                        <option>Martes</option>
                        <option>Miércoles</option>
                        <option>Jueves</option>
                        <option>Viernes</option>
                        <option>Sábado</option>
                        <option>Domingo</option>
                    </select>
                </div>

                <div class="exercises-list" id="exerciseContainer">
                    <label class="form-label">Ejercicios</label>
                    
                    <div class="exercise-row">
                        <input type="text" class="form-input" placeholder="Nombre Ejercicio">
                        <input type="number" class="form-input" placeholder="Series">
                        <i class="fa-solid fa-trash text-muted"></i>
                    </div>
                </div>

                <button type="button" class="btn-small-add" onclick="addExerciseField()">
                    <i class="fa-solid fa-plus"></i> Añadir otro ejercicio
                </button>

                <button type="submit" class="btn-save">GUARDAR RUTINA</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('routineModal');
        const container = document.getElementById('exerciseContainer');

        // Abrir Modal
        function openModal() {
            modal.style.display = 'flex';
        }

        // Cerrar Modal
        function closeModal() {
            modal.style.display = 'none';
        }

        // Cerrar si clicamos fuera del contenido
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Función para añadir campos dinámicos de ejercicio
        function addExerciseField() {
            // Creamos un div nuevo
            const div = document.createElement('div');
            div.className = 'exercise-row';
            
            // Inyectamos el HTML de los inputs
            div.innerHTML = `
                <input type="text" class="form-input" placeholder="Nombre Ejercicio">
                <input type="number" class="form-input" placeholder="Series">
                <i class="fa-solid fa-trash text-muted" onclick="this.parentElement.remove()" style="cursor:pointer;"></i>
            `;
            
            // Añadimos al contenedor
            container.appendChild(div);
        }
    </script>

</body>
</html>
