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

