DROP DATABASE IF EXISTS gymmetrics;
CREATE DATABASE gymmetrics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gymmetrics;

-- 1. TABLA DE USUARIOS
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE, -- NO puede haber dos iguales
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    avatar VARCHAR(255) DEFAULT 'default.png',
    -- Condición simple para asegurar formato de correo en la BD
    CONSTRAINT check_email_format CHECK (email LIKE '%@%')
);

-- 2. TABLA MAESTRA DE EJERCICIOS
CREATE TABLE ejercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    grupo_muscular VARCHAR(50),
    icono VARCHAR(50) DEFAULT 'dumbbell'
);

-- 3. TABLA DE RUTINAS (PLANIFICACIÓN SEMANAL)
CREATE TABLE rutinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    dia_semana ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo') NOT NULL,
    
    -- Columna Booleana: 1 = Es día de descanso, 0 = Es día de entreno
    es_descanso BOOLEAN DEFAULT 0,
    
    nombre_rutina VARCHAR(100), -- Ej: "Torso Fuerza" (Puede ser NULL si es descanso)
    creada_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    
    -- ESTO ES CLAVE: Impide duplicados. Un usuario solo puede tener UNA fila por día de la semana.
    UNIQUE KEY unique_rutina_dia (usuario_id, dia_semana)
);

-- 4. DETALLE DE LA RUTINA (Qué ejercicios tocan ese día)
CREATE TABLE detalles_rutina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rutina_id INT NOT NULL,
    ejercicio_id INT NOT NULL,
    series_objetivo INT DEFAULT 4,
    reps_objetivo VARCHAR(20) DEFAULT 12,
    orden INT DEFAULT 0, -- Para ordenar los ejercicios (1º, 2º, 3º...)
    
    FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE,
    FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE
);

-- 5. HISTORIAL DE ENTRENAMIENTOS (LO QUE REALMENTE HICISTE)
-- Esta tabla guarda el "Check" global del día.
CREATE TABLE historial_entrenamientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rutina_id INT, -- Qué rutina tocaba ese día (puede ser NULL si fue improvisado)
    fecha DATE DEFAULT (CURRENT_DATE),
    
    -- Booleano: 1 = Completado, 0 = No realizado/Saltado
    completado BOOLEAN DEFAULT 1,
    
    duracion_minutos INT, -- Opcional: cuánto tardaste
    comentarios TEXT, -- "Me sentí cansado", "Récord personal", etc.
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE SET NULL
);

USE gymmetrics;

-- 6. LOG DETALLADO (OPCIONAL PERO RECOMENDADO)
-- Si quieres guardar cuánto peso levantaste en cada serie específica
CREATE TABLE sets_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    historial_id INT NOT NULL, -- Vinculado al entreno de arriba
    ejercicio_id INT NOT NULL,
    numero_serie INT NOT NULL,
    peso_kg DECIMAL(5,2),
    reps_realizadas INT,
    
    FOREIGN KEY (historial_id) REFERENCES historial_entrenamientos(id) ON DELETE CASCADE,
    FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE
);

-- 1. Quitamos las columnas viejas de la tabla detalles_rutina
ALTER TABLE detalles_rutina DROP COLUMN series_objetivo;
ALTER TABLE detalles_rutina DROP COLUMN reps_objetivo;

-- 2. Creamos la nueva tabla para las series individuales
CREATE TABLE rutina_series (
    id INT AUTO_INCREMENT PRIMARY KEY,
    detalle_rutina_id INT NOT NULL, -- Se vincula al ejercicio de la rutina
    numero_serie INT NOT NULL,      -- Serie 1, Serie 2, Serie 3...
    reps_objetivo INT NOT NULL,     -- Cuántas reps quieres hacer
    peso_objetivo DECIMAL(5,2),     -- Con cuánto peso (puede ser 0 si es peso corporal)
    
    FOREIGN KEY (detalle_rutina_id) REFERENCES detalles_rutina(id) ON DELETE CASCADE
);

USE gymmetrics;

-- Añadimos la columna. Si ya la tenías, te dará error (no pasa nada, lo ignoras).
ALTER TABLE usuarios ADD COLUMN fecha_registro DATE DEFAULT (CURRENT_DATE);

-- Si tenías usuarios antiguos, les ponemos la fecha de hoy para que no haya fallos:
UPDATE usuarios SET fecha_registro = CURRENT_DATE WHERE fecha_registro IS NULL;

-- Tabla Ejercicios -----------------------------------

CREATE TABLE ejercicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    grupo_muscular VARCHAR(50),
    icono VARCHAR(50) DEFAULT 'dumbbell'
);

-- Vaciamos la tabla por si tenías alguno de prueba (opcional, pero recomendado para evitar duplicados)
TRUNCATE TABLE ejercicios;

-- Insertamos los 100 ejercicios
INSERT INTO ejercicios (nombre, grupo_muscular) VALUES 
-- PECHO (14)
('Press de Banca Plano con Barra', 'Pecho'),
('Press de Banca Inclinado con Barra', 'Pecho'),
('Press de Banca Declinado con Barra', 'Pecho'),
('Press de Banca Plano con Mancuernas', 'Pecho'),
('Press de Banca Inclinado con Mancuernas', 'Pecho'),
('Aperturas Planas con Mancuernas', 'Pecho'),
('Aperturas Inclinadas con Mancuernas', 'Pecho'),
('Cruces de Poleas Altas', 'Pecho'),
('Cruces de Poleas Bajas', 'Pecho'),
('Peck Deck (Máquina de Aperturas)', 'Pecho'),
('Fondos en Paralelas (Pecho)', 'Pecho'),
('Flexiones de Brazo (Push-ups)', 'Pecho'),
('Flexiones Declinadas', 'Pecho'),
('Pullover con Mancuerna', 'Pecho'),

-- ESPALDA (16)
('Dominadas (Agarre Prono)', 'Espalda'),
('Dominadas (Agarre Supino / Chin-ups)', 'Espalda'),
('Dominadas (Agarre Neutro)', 'Espalda'),
('Jalón al Pecho Agarre Abierto', 'Espalda'),
('Jalón al Pecho Agarre Estrecho', 'Espalda'),
('Remo con Barra (Pendlay)', 'Espalda'),
('Remo con Barra Agarre Supino', 'Espalda'),
('Remo con Mancuerna a Una Mano', 'Espalda'),
('Remo en Polea Baja (Gironda)', 'Espalda'),
('Remo en Máquina (Chest Supported)', 'Espalda'),
('Remo en T con Barra', 'Espalda'),
('Peso Muerto Convencional', 'Espalda'),
('Pull-down con Brazos Rígidos', 'Espalda'),
('Hiperextensiones', 'Espalda'),
('Encogimientos de Hombros con Barra (Trapecio)', 'Espalda'),
('Encogimientos con Mancuernas', 'Espalda'),

-- PIERNAS - CUÁDRICEPS Y GLÚTEOS (17)
('Sentadilla Libre con Barra', 'Pierna'),
('Sentadilla Frontal (Front Squat)', 'Pierna'),
('Sentadilla Búlgara con Mancuernas', 'Pierna'),
('Sentadilla Goblet', 'Pierna'),
('Sentadilla en Máquina Hack', 'Pierna'),
('Sentadilla en Multipower (Smith)', 'Pierna'),
('Prensa de Piernas (Leg Press)', 'Pierna'),
('Extensiones de Cuádriceps en Máquina', 'Pierna'),
('Zancadas Caminando (Lunges)', 'Pierna'),
('Zancadas Estáticas con Barra', 'Pierna'),
('Zancadas Inversas', 'Pierna'),
('Hip Thrust con Barra', 'Pierna'),
('Hip Thrust en Máquina', 'Pierna'),
('Puente de Glúteo', 'Pierna'),
('Patada de Glúteo en Polea', 'Pierna'),
('Abducción de Cadera en Máquina', 'Pierna'),
('Aducción de Cadera en Máquina', 'Pierna'),

-- PIERNAS - ISQUIOS Y GEMELOS (8)
('Peso Muerto Rumano con Barra', 'Pierna'),
('Peso Muerto Piernas Rígidas', 'Pierna'),
('Curl Femoral Tumbado', 'Pierna'),
('Curl Femoral Sentado', 'Pierna'),
('Buenos Días (Good Mornings)', 'Pierna'),
('Elevación de Talones de Pie (Gemelo)', 'Pierna'),
('Elevación de Talones Sentado', 'Pierna'),
('Elevación de Talones en Prensa', 'Pierna'),

-- HOMBROS (13)
('Press Militar de Pie con Barra', 'Hombro'),
('Press de Hombros Sentado con Mancuernas', 'Hombro'),
('Press Arnold', 'Hombro'),
('Press en Máquina para Hombros', 'Hombro'),
('Elevaciones Laterales con Mancuernas', 'Hombro'),
('Elevaciones Laterales en Polea', 'Hombro'),
('Elevaciones Laterales en Máquina', 'Hombro'),
('Elevaciones Frontales con Mancuernas', 'Hombro'),
('Elevaciones Frontales con Disco', 'Hombro'),
('Elevaciones Frontales en Polea', 'Hombro'),
('Pájaros (Elevaciones Posteriores) con Mancuernas', 'Hombro'),
('Pájaros en Máquina (Peck Deck Inverso)', 'Hombro'),
('Face Pull en Polea', 'Hombro'),

-- BÍCEPS (10)
('Curl de Bíceps con Barra Recta', 'Brazos'),
('Curl de Bíceps con Barra EZ', 'Brazos'),
('Curl de Bíceps Alterno con Mancuernas', 'Brazos'),
('Curl Martillo con Mancuernas', 'Brazos'),
('Curl Martillo en Polea con Cuerda', 'Brazos'),
('Curl Predicador (Banco Scott) con Barra EZ', 'Brazos'),
('Curl Predicador en Máquina', 'Brazos'),
('Curl Concentrado con Mancuerna', 'Brazos'),
('Curl en Polea Baja', 'Brazos'),
('Curl de Bíceps Inverso (Antebrazo)', 'Brazos'),

-- TRÍCEPS (10)
('Extensiones de Tríceps en Polea con Cuerda', 'Brazos'),
('Extensiones de Tríceps en Polea con Barra', 'Brazos'),
('Press Francés con Barra EZ (Rompecráneos)', 'Brazos'),
('Press de Banca con Agarre Estrecho', 'Brazos'),
('Fondos para Tríceps entre Bancos', 'Brazos'),
('Fondos en Paralelas (Tríceps)', 'Brazos'),
('Extensión Tras Nuca con Mancuerna', 'Brazos'),
('Extensión Tras Nuca en Polea', 'Brazos'),
('Patada de Tríceps con Mancuerna', 'Brazos'),
('Patada de Tríceps en Polea', 'Brazos'),

-- CORE / ABDOMINALES (12)
('Crunch Abdominal Tradicional', 'Core'),
('Crunch en Polea Alta', 'Core'),
('Plancha Abdominal (Plank)', 'Core'),
('Plancha Lateral', 'Core'),
('Elevación de Piernas Colgado', 'Core'),
('Elevación de Piernas Tumbado', 'Core'),
('Rodillas al Pecho en Paralelas', 'Core'),
('Rueda Abdominal (Ab Wheel)', 'Core'),
('Russian Twists (Giros Rusos)', 'Core'),
('Bicycle Crunches (Bicicletas)', 'Core'),
('V-Ups (Abdominales en V)', 'Core'),
('Woodchopper en Polea (Leñador)', 'Core');

-- Insertamos más ejercicios (Máquinas, Poleas, Kettlebells, etc.)
INSERT INTO ejercicios (nombre, grupo_muscular) VALUES 
-- PECHO (Variantes en Máquina y Polea)
('Press de Pecho en Máquina (Sentado)', 'Pecho'),
('Press de Pecho Inclinado en Máquina', 'Pecho'),
('Press de Pecho Declinado en Máquina', 'Pecho'),
('Cruces en Polea Media', 'Pecho'),
('Aperturas en Polea Baja (Ascendentes)', 'Pecho'),
('Pullover en Polea Alta', 'Pecho'),
('Press Hexagonal con Mancuerna (Hex Press)', 'Pecho'),
('Flexiones Diamante', 'Pecho'),

-- ESPALDA (Máquinas, Unilaterales y Poleas)
('Jalón al Pecho Unilateral en Máquina', 'Espalda'),
('Jalón al Pecho Agarre Invertido (Supino)', 'Espalda'),
('Jalón Tras Nuca', 'Espalda'),
('Remo en Máquina (Agarre Neutro)', 'Espalda'),
('Remo en Máquina (Agarre Prono Ancho)', 'Espalda'),
('Remo Gironda (Agarre Ancho)', 'Espalda'),
('Remo Unilateral en Polea Baja', 'Espalda'),
('Dominadas Asistidas en Máquina', 'Espalda'),
('Remo al Cuello / Mentón con Barra', 'Espalda'),
('Remo al Cuello / Mentón con Polea', 'Espalda'),

-- PIERNAS Y GLÚTEOS (Aislamiento, Máquinas y Kettlebells)
('Prensa Inclinada a Una Pierna', 'Pierna'),
('Prensa Horizontal', 'Pierna'),
('Extensiones de Cuádriceps a Una Pierna', 'Pierna'),
('Curl Femoral de Pie (Unilateral)', 'Pierna'),
('Máquina de Abductores (Sentado)', 'Pierna'),
('Máquina de Aductores (Sentado)', 'Pierna'),
('Sentadilla Sissy', 'Pierna'),
('Sentadilla Búlgara en Multipower', 'Pierna'),
('Peso Muerto Rumano con Mancuernas', 'Pierna'),
('Kettlebell Swing (Balanceo con Pesa Rusa)', 'Pierna'),
('Elevación de Gemelos en Máquina Costurera (Sentado)', 'Pierna'),
('Elevación de Gemelos en Multipower', 'Pierna'),
('Subidas al Cajón (Step-Ups) con Mancuernas', 'Pierna'),

-- HOMBROS (Máquinas, Multipower y Poleas)
('Press Militar en Multipower (Sentado)', 'Hombro'),
('Press Militar Tras Nuca en Multipower', 'Hombro'),
('Elevaciones Laterales Unilaterales en Polea', 'Hombro'),
('Elevaciones Laterales Sentado en Máquina', 'Hombro'),
('Elevaciones Frontales con Cuerda en Polea', 'Hombro'),
('Pájaros con Cables Cruzados en Polea Alta', 'Hombro'),
('Face Pull Sentado', 'Hombro'),
('Encogimientos de Trapecio en Multipower', 'Hombro'),

-- BÍCEPS (Poleas y Variaciones Específicas)
('Curl Araña (Spider Curl) con Barra EZ', 'Brazos'),
('Curl Bayesian (Polea Baja de Espaldas)', 'Brazos'),
('Curl de Bíceps en Polea Alta (Doble Bíceps)', 'Brazos'),
('Curl Zottman con Mancuernas', 'Brazos'),
('Curl Inverso en Polea Baja', 'Brazos'),
('Dominadas Supinas con Lastre', 'Brazos'),

-- TRÍCEPS (Agarres y Unilaterales en Polea)
('Extensión de Tríceps en Polea con Agarre V', 'Brazos'),
('Extensión de Tríceps Unilateral en Polea (Agarre Inverso)', 'Brazos'),
('Extensión de Tríceps Tras Nuca con Cuerda (Polea Baja)', 'Brazos'),
('Copa a Dos Manos con Mancuerna (Extensión Tras Nuca)', 'Brazos'),
('Press JM con Barra', 'Brazos'),
('Tate Press con Mancuernas', 'Brazos'),

-- CORE Y CUERPO COMPLETO (Máquinas y Funcional)
('Abdominales en Máquina Sentado', 'Core'),
('Rotación de Torso en Máquina', 'Core'),
('Elevación de Piernas en Silla Romana', 'Core'),
('Paseo del Granjero (Farmer Walk)', 'Core'),
('Turkish Get-Up (Levantamiento Turco)', 'Core'),
('Toes to Bar (Pies a la Barra)', 'Core'),
('L-Sit (Isométrico en Paralelas)', 'Core'),
('Dragon Flag', 'Core');

-- CREAR USUARIO --------------------------------------

CREATE USER 
'AdminGym'@'localhost' 
IDENTIFIED  BY 'AdminGym123$';

## LE DAMOS ACCESO AL USUARIO ##
GRANT USAGE ON *.* TO 'AdminGym'@'localhost';

## LE SACAMOS LAS RESTRICCIONES ##
ALTER USER 'AdminGym'@'localhost' 
REQUIRE NONE 
WITH MAX_QUERIES_PER_HOUR 0 
MAX_CONNECTIONS_PER_HOUR 0 
MAX_UPDATES_PER_HOUR 0 
MAX_USER_CONNECTIONS 0;

## LE DAMOS ACCESO A LA BD ##
GRANT ALL PRIVILEGES ON gymmetrics.* 
TO 'AdminGym'@'localhost';

## RECARGAMOS PRIVILEGIOS ##
FLUSH PRIVILEGES;

