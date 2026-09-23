-- =============================================================
-- GymMetrics - Migración: Traducciones al Inglés de Ejercicios
-- Ejecutar sobre la BD gymmetrics ya existente
-- =============================================================

USE gymmetrics;

-- 1. Añadir columnas de traducción (IF NOT EXISTS para evitar errores)
ALTER TABLE ejercicios 
    ADD COLUMN IF NOT EXISTS nombre_en VARCHAR(100) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS grupo_muscular_en VARCHAR(50) DEFAULT NULL;

-- 2. Traducir grupos musculares (en bloque, muy rápido)
UPDATE ejercicios SET grupo_muscular_en = 'Chest'     WHERE grupo_muscular = 'Pecho';
UPDATE ejercicios SET grupo_muscular_en = 'Back'      WHERE grupo_muscular = 'Espalda';
UPDATE ejercicios SET grupo_muscular_en = 'Legs'      WHERE grupo_muscular = 'Pierna';
UPDATE ejercicios SET grupo_muscular_en = 'Shoulders' WHERE grupo_muscular = 'Hombro';
UPDATE ejercicios SET grupo_muscular_en = 'Arms'      WHERE grupo_muscular = 'Brazos';
UPDATE ejercicios SET grupo_muscular_en = 'Core'      WHERE grupo_muscular = 'Core';

-- 3. Traducir ejercicios individualmente (por nombre ES, que es único)

-- PECHO / CHEST (14)
UPDATE ejercicios SET nombre_en = 'Flat Barbell Bench Press'             WHERE nombre = 'Press de Banca Plano con Barra';
UPDATE ejercicios SET nombre_en = 'Incline Barbell Bench Press'          WHERE nombre = 'Press de Banca Inclinado con Barra';
UPDATE ejercicios SET nombre_en = 'Decline Barbell Bench Press'          WHERE nombre = 'Press de Banca Declinado con Barra';
UPDATE ejercicios SET nombre_en = 'Flat Dumbbell Bench Press'            WHERE nombre = 'Press de Banca Plano con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Incline Dumbbell Bench Press'         WHERE nombre = 'Press de Banca Inclinado con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Flat Dumbbell Flyes'                  WHERE nombre = 'Aperturas Planas con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Incline Dumbbell Flyes'               WHERE nombre = 'Aperturas Inclinadas con Mancuernas';
UPDATE ejercicios SET nombre_en = 'High Cable Crossovers'                WHERE nombre = 'Cruces de Poleas Altas';
UPDATE ejercicios SET nombre_en = 'Low Cable Crossovers'                 WHERE nombre = 'Cruces de Poleas Bajas';
UPDATE ejercicios SET nombre_en = 'Peck Deck (Chest Fly Machine)'        WHERE nombre = 'Peck Deck (Máquina de Aperturas)';
UPDATE ejercicios SET nombre_en = 'Parallel Bar Dips (Chest)'            WHERE nombre = 'Fondos en Paralelas (Pecho)';
UPDATE ejercicios SET nombre_en = 'Push-ups'                             WHERE nombre = 'Flexiones de Brazo (Push-ups)';
UPDATE ejercicios SET nombre_en = 'Decline Push-ups'                     WHERE nombre = 'Flexiones Declinadas';
UPDATE ejercicios SET nombre_en = 'Dumbbell Pullover'                    WHERE nombre = 'Pullover con Mancuerna';

-- ESPALDA / BACK (16)
UPDATE ejercicios SET nombre_en = 'Pull-ups (Pronated Grip)'             WHERE nombre = 'Dominadas (Agarre Prono)';
UPDATE ejercicios SET nombre_en = 'Chin-ups (Supinated Grip)'            WHERE nombre = 'Dominadas (Agarre Supino / Chin-ups)';
UPDATE ejercicios SET nombre_en = 'Neutral Grip Pull-ups'                WHERE nombre = 'Dominadas (Agarre Neutro)';
UPDATE ejercicios SET nombre_en = 'Wide Grip Lat Pulldown'               WHERE nombre = 'Jalón al Pecho Agarre Abierto';
UPDATE ejercicios SET nombre_en = 'Close Grip Lat Pulldown'              WHERE nombre = 'Jalón al Pecho Agarre Estrecho';
UPDATE ejercicios SET nombre_en = 'Pendlay Row'                          WHERE nombre = 'Remo con Barra (Pendlay)';
UPDATE ejercicios SET nombre_en = 'Supinated Grip Barbell Row'           WHERE nombre = 'Remo con Barra Agarre Supino';
UPDATE ejercicios SET nombre_en = 'Single Arm Dumbbell Row'              WHERE nombre = 'Remo con Mancuerna a Una Mano';
UPDATE ejercicios SET nombre_en = 'Seated Cable Row (Gironda)'           WHERE nombre = 'Remo en Polea Baja (Gironda)';
UPDATE ejercicios SET nombre_en = 'Chest Supported Machine Row'          WHERE nombre = 'Remo en Máquina (Chest Supported)';
UPDATE ejercicios SET nombre_en = 'T-Bar Row'                            WHERE nombre = 'Remo en T con Barra';
UPDATE ejercicios SET nombre_en = 'Conventional Deadlift'                WHERE nombre = 'Peso Muerto Convencional';
UPDATE ejercicios SET nombre_en = 'Straight Arm Pulldown'                WHERE nombre = 'Pull-down con Brazos Rígidos';
UPDATE ejercicios SET nombre_en = 'Hyperextensions'                      WHERE nombre = 'Hiperextensiones';
UPDATE ejercicios SET nombre_en = 'Barbell Shrugs (Trapezius)'           WHERE nombre = 'Encogimientos de Hombros con Barra (Trapecio)';
UPDATE ejercicios SET nombre_en = 'Dumbbell Shrugs'                      WHERE nombre = 'Encogimientos con Mancuernas';

-- PIERNAS / LEGS - Cuádriceps y Glúteos (17)
UPDATE ejercicios SET nombre_en = 'Barbell Back Squat'                   WHERE nombre = 'Sentadilla Libre con Barra';
UPDATE ejercicios SET nombre_en = 'Front Squat'                          WHERE nombre = 'Sentadilla Frontal (Front Squat)';
UPDATE ejercicios SET nombre_en = 'Bulgarian Split Squat with Dumbbells' WHERE nombre = 'Sentadilla Búlgara con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Goblet Squat'                         WHERE nombre = 'Sentadilla Goblet';
UPDATE ejercicios SET nombre_en = 'Hack Squat Machine'                   WHERE nombre = 'Sentadilla en Máquina Hack';
UPDATE ejercicios SET nombre_en = 'Smith Machine Squat'                  WHERE nombre = 'Sentadilla en Multipower (Smith)';
UPDATE ejercicios SET nombre_en = 'Leg Press'                            WHERE nombre = 'Prensa de Piernas (Leg Press)';
UPDATE ejercicios SET nombre_en = 'Machine Leg Extensions'               WHERE nombre = 'Extensiones de Cuádriceps en Máquina';
UPDATE ejercicios SET nombre_en = 'Walking Lunges'                       WHERE nombre = 'Zancadas Caminando (Lunges)';
UPDATE ejercicios SET nombre_en = 'Barbell Static Lunges'                WHERE nombre = 'Zancadas Estáticas con Barra';
UPDATE ejercicios SET nombre_en = 'Reverse Lunges'                       WHERE nombre = 'Zancadas Inversas';
UPDATE ejercicios SET nombre_en = 'Barbell Hip Thrust'                   WHERE nombre = 'Hip Thrust con Barra';
UPDATE ejercicios SET nombre_en = 'Machine Hip Thrust'                   WHERE nombre = 'Hip Thrust en Máquina';
UPDATE ejercicios SET nombre_en = 'Glute Bridge'                         WHERE nombre = 'Puente de Glúteo';
UPDATE ejercicios SET nombre_en = 'Cable Glute Kickback'                 WHERE nombre = 'Patada de Glúteo en Polea';
UPDATE ejercicios SET nombre_en = 'Machine Hip Abduction'                WHERE nombre = 'Abducción de Cadera en Máquina';
UPDATE ejercicios SET nombre_en = 'Machine Hip Adduction'                WHERE nombre = 'Aducción de Cadera en Máquina';

-- PIERNAS / LEGS - Isquios y Gemelos (8)
UPDATE ejercicios SET nombre_en = 'Romanian Deadlift with Barbell'       WHERE nombre = 'Peso Muerto Rumano con Barra';
UPDATE ejercicios SET nombre_en = 'Stiff Leg Deadlift'                   WHERE nombre = 'Peso Muerto Piernas Rígidas';
UPDATE ejercicios SET nombre_en = 'Lying Leg Curl'                       WHERE nombre = 'Curl Femoral Tumbado';
UPDATE ejercicios SET nombre_en = 'Seated Leg Curl'                      WHERE nombre = 'Curl Femoral Sentado';
UPDATE ejercicios SET nombre_en = 'Good Mornings'                        WHERE nombre = 'Buenos Días (Good Mornings)';
UPDATE ejercicios SET nombre_en = 'Standing Calf Raises'                 WHERE nombre = 'Elevación de Talones de Pie (Gemelo)';
UPDATE ejercicios SET nombre_en = 'Seated Calf Raises'                   WHERE nombre = 'Elevación de Talones Sentado';
UPDATE ejercicios SET nombre_en = 'Calf Raises on Leg Press'             WHERE nombre = 'Elevación de Talones en Prensa';

-- HOMBROS / SHOULDERS (13)
UPDATE ejercicios SET nombre_en = 'Standing Military Press'              WHERE nombre = 'Press Militar de Pie con Barra';
UPDATE ejercicios SET nombre_en = 'Seated Dumbbell Shoulder Press'       WHERE nombre = 'Press de Hombros Sentado con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Arnold Press'                         WHERE nombre = 'Press Arnold';
UPDATE ejercicios SET nombre_en = 'Machine Shoulder Press'               WHERE nombre = 'Press en Máquina para Hombros';
UPDATE ejercicios SET nombre_en = 'Dumbbell Lateral Raises'              WHERE nombre = 'Elevaciones Laterales con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Cable Lateral Raises'                 WHERE nombre = 'Elevaciones Laterales en Polea';
UPDATE ejercicios SET nombre_en = 'Machine Lateral Raises'               WHERE nombre = 'Elevaciones Laterales en Máquina';
UPDATE ejercicios SET nombre_en = 'Dumbbell Front Raises'                WHERE nombre = 'Elevaciones Frontales con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Plate Front Raises'                   WHERE nombre = 'Elevaciones Frontales con Disco';
UPDATE ejercicios SET nombre_en = 'Cable Front Raises'                   WHERE nombre = 'Elevaciones Frontales en Polea';
UPDATE ejercicios SET nombre_en = 'Dumbbell Rear Delt Flyes'             WHERE nombre = 'Pájaros (Elevaciones Posteriores) con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Machine Rear Delt Flyes (Reverse Peck Deck)' WHERE nombre = 'Pájaros en Máquina (Peck Deck Inverso)';
UPDATE ejercicios SET nombre_en = 'Cable Face Pull'                      WHERE nombre = 'Face Pull en Polea';

-- BÍCEPS / BICEPS (10)
UPDATE ejercicios SET nombre_en = 'Straight Bar Bicep Curl'              WHERE nombre = 'Curl de Bíceps con Barra Recta';
UPDATE ejercicios SET nombre_en = 'EZ Bar Bicep Curl'                    WHERE nombre = 'Curl de Bíceps con Barra EZ';
UPDATE ejercicios SET nombre_en = 'Alternating Dumbbell Bicep Curl'      WHERE nombre = 'Curl de Bíceps Alterno con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Dumbbell Hammer Curl'                 WHERE nombre = 'Curl Martillo con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Cable Rope Hammer Curl'               WHERE nombre = 'Curl Martillo en Polea con Cuerda';
UPDATE ejercicios SET nombre_en = 'EZ Bar Preacher Curl (Scott Bench)'   WHERE nombre = 'Curl Predicador (Banco Scott) con Barra EZ';
UPDATE ejercicios SET nombre_en = 'Machine Preacher Curl'                WHERE nombre = 'Curl Predicador en Máquina';
UPDATE ejercicios SET nombre_en = 'Dumbbell Concentration Curl'          WHERE nombre = 'Curl Concentrado con Mancuerna';
UPDATE ejercicios SET nombre_en = 'Low Cable Curl'                       WHERE nombre = 'Curl en Polea Baja';
UPDATE ejercicios SET nombre_en = 'Reverse Bicep Curl (Forearm)'         WHERE nombre = 'Curl de Bíceps Inverso (Antebrazo)';

-- TRÍCEPS / TRICEPS (10)
UPDATE ejercicios SET nombre_en = 'Cable Rope Tricep Pushdown'           WHERE nombre = 'Extensiones de Tríceps en Polea con Cuerda';
UPDATE ejercicios SET nombre_en = 'Cable Bar Tricep Pushdown'            WHERE nombre = 'Extensiones de Tríceps en Polea con Barra';
UPDATE ejercicios SET nombre_en = 'EZ Bar Skull Crushers'                WHERE nombre = 'Press Francés con Barra EZ (Rompecráneos)';
UPDATE ejercicios SET nombre_en = 'Close Grip Bench Press'               WHERE nombre = 'Press de Banca con Agarre Estrecho';
UPDATE ejercicios SET nombre_en = 'Bench Dips (Triceps)'                 WHERE nombre = 'Fondos para Tríceps entre Bancos';
UPDATE ejercicios SET nombre_en = 'Parallel Bar Dips (Triceps)'          WHERE nombre = 'Fondos en Paralelas (Tríceps)';
UPDATE ejercicios SET nombre_en = 'Overhead Dumbbell Tricep Extension'   WHERE nombre = 'Extensión Tras Nuca con Mancuerna';
UPDATE ejercicios SET nombre_en = 'Overhead Cable Tricep Extension'      WHERE nombre = 'Extensión Tras Nuca en Polea';
UPDATE ejercicios SET nombre_en = 'Dumbbell Tricep Kickback'             WHERE nombre = 'Patada de Tríceps con Mancuerna';
UPDATE ejercicios SET nombre_en = 'Cable Tricep Kickback'                WHERE nombre = 'Patada de Tríceps en Polea';

-- CORE / ABDOMINALES (12)
UPDATE ejercicios SET nombre_en = 'Traditional Crunch'                   WHERE nombre = 'Crunch Abdominal Tradicional';
UPDATE ejercicios SET nombre_en = 'Cable Crunch'                         WHERE nombre = 'Crunch en Polea Alta';
UPDATE ejercicios SET nombre_en = 'Plank'                                WHERE nombre = 'Plancha Abdominal (Plank)';
UPDATE ejercicios SET nombre_en = 'Side Plank'                           WHERE nombre = 'Plancha Lateral';
UPDATE ejercicios SET nombre_en = 'Hanging Leg Raises'                   WHERE nombre = 'Elevación de Piernas Colgado';
UPDATE ejercicios SET nombre_en = 'Lying Leg Raises'                     WHERE nombre = 'Elevación de Piernas Tumbado';
UPDATE ejercicios SET nombre_en = 'Knee Raises on Parallel Bars'         WHERE nombre = 'Rodillas al Pecho en Paralelas';
UPDATE ejercicios SET nombre_en = 'Ab Wheel Rollout'                     WHERE nombre = 'Rueda Abdominal (Ab Wheel)';
UPDATE ejercicios SET nombre_en = 'Russian Twists'                       WHERE nombre = 'Russian Twists (Giros Rusos)';
UPDATE ejercicios SET nombre_en = 'Bicycle Crunches'                     WHERE nombre = 'Bicycle Crunches (Bicicletas)';
UPDATE ejercicios SET nombre_en = 'V-Ups'                                WHERE nombre = 'V-Ups (Abdominales en V)';
UPDATE ejercicios SET nombre_en = 'Cable Woodchopper'                    WHERE nombre = 'Woodchopper en Polea (Leñador)';

-- PECHO extra (8)
UPDATE ejercicios SET nombre_en = 'Seated Machine Chest Press'           WHERE nombre = 'Press de Pecho en Máquina (Sentado)';
UPDATE ejercicios SET nombre_en = 'Incline Machine Chest Press'          WHERE nombre = 'Press de Pecho Inclinado en Máquina';
UPDATE ejercicios SET nombre_en = 'Decline Machine Chest Press'          WHERE nombre = 'Press de Pecho Declinado en Máquina';
UPDATE ejercicios SET nombre_en = 'Mid Cable Crossovers'                 WHERE nombre = 'Cruces en Polea Media';
UPDATE ejercicios SET nombre_en = 'Low Cable Fly (Ascending)'            WHERE nombre = 'Aperturas en Polea Baja (Ascendentes)';
UPDATE ejercicios SET nombre_en = 'High Cable Pullover'                  WHERE nombre = 'Pullover en Polea Alta';
UPDATE ejercicios SET nombre_en = 'Hex Press (Dumbbell)'                 WHERE nombre = 'Press Hexagonal con Mancuerna (Hex Press)';
UPDATE ejercicios SET nombre_en = 'Diamond Push-ups'                     WHERE nombre = 'Flexiones Diamante';

-- ESPALDA extra (10)
UPDATE ejercicios SET nombre_en = 'Machine Unilateral Lat Pulldown'      WHERE nombre = 'Jalón al Pecho Unilateral en Máquina';
UPDATE ejercicios SET nombre_en = 'Reverse Grip Lat Pulldown'            WHERE nombre = 'Jalón al Pecho Agarre Invertido (Supino)';
UPDATE ejercicios SET nombre_en = 'Behind the Neck Lat Pulldown'         WHERE nombre = 'Jalón Tras Nuca';
UPDATE ejercicios SET nombre_en = 'Machine Row (Neutral Grip)'           WHERE nombre = 'Remo en Máquina (Agarre Neutro)';
UPDATE ejercicios SET nombre_en = 'Machine Row (Wide Pronated Grip)'     WHERE nombre = 'Remo en Máquina (Agarre Prono Ancho)';
UPDATE ejercicios SET nombre_en = 'Gironda Row (Wide Grip)'              WHERE nombre = 'Remo Gironda (Agarre Ancho)';
UPDATE ejercicios SET nombre_en = 'Single Arm Low Cable Row'             WHERE nombre = 'Remo Unilateral en Polea Baja';
UPDATE ejercicios SET nombre_en = 'Assisted Pull-ups Machine'            WHERE nombre = 'Dominadas Asistidas en Máquina';
UPDATE ejercicios SET nombre_en = 'Barbell Upright Row'                  WHERE nombre = 'Remo al Cuello / Mentón con Barra';
UPDATE ejercicios SET nombre_en = 'Cable Upright Row'                    WHERE nombre = 'Remo al Cuello / Mentón con Polea';

-- PIERNAS extra (13)
UPDATE ejercicios SET nombre_en = 'Single Leg Incline Leg Press'         WHERE nombre = 'Prensa Inclinada a Una Pierna';
UPDATE ejercicios SET nombre_en = 'Horizontal Leg Press'                 WHERE nombre = 'Prensa Horizontal';
UPDATE ejercicios SET nombre_en = 'Single Leg Quad Extension'            WHERE nombre = 'Extensiones de Cuádriceps a Una Pierna';
UPDATE ejercicios SET nombre_en = 'Standing Unilateral Leg Curl'         WHERE nombre = 'Curl Femoral de Pie (Unilateral)';
UPDATE ejercicios SET nombre_en = 'Seated Hip Abductor Machine'          WHERE nombre = 'Máquina de Abductores (Sentado)';
UPDATE ejercicios SET nombre_en = 'Seated Hip Adductor Machine'          WHERE nombre = 'Máquina de Aductores (Sentado)';
UPDATE ejercicios SET nombre_en = 'Sissy Squat'                          WHERE nombre = 'Sentadilla Sissy';
UPDATE ejercicios SET nombre_en = 'Smith Machine Bulgarian Split Squat'  WHERE nombre = 'Sentadilla Búlgara en Multipower';
UPDATE ejercicios SET nombre_en = 'Romanian Deadlift with Dumbbells'     WHERE nombre = 'Peso Muerto Rumano con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Kettlebell Swing'                     WHERE nombre = 'Kettlebell Swing (Balanceo con Pesa Rusa)';
UPDATE ejercicios SET nombre_en = 'Seated Calf Raise Machine'            WHERE nombre = 'Elevación de Gemelos en Máquina Costurera (Sentado)';
UPDATE ejercicios SET nombre_en = 'Smith Machine Calf Raises'            WHERE nombre = 'Elevación de Gemelos en Multipower';
UPDATE ejercicios SET nombre_en = 'Dumbbell Step-Ups'                    WHERE nombre = 'Subidas al Cajón (Step-Ups) con Mancuernas';

-- HOMBROS extra (8)
UPDATE ejercicios SET nombre_en = 'Seated Smith Machine Military Press'  WHERE nombre = 'Press Militar en Multipower (Sentado)';
UPDATE ejercicios SET nombre_en = 'Behind the Neck Smith Machine Press'  WHERE nombre = 'Press Militar Tras Nuca en Multipower';
UPDATE ejercicios SET nombre_en = 'Unilateral Cable Lateral Raises'      WHERE nombre = 'Elevaciones Laterales Unilaterales en Polea';
UPDATE ejercicios SET nombre_en = 'Seated Machine Lateral Raises'        WHERE nombre = 'Elevaciones Laterales Sentado en Máquina';
UPDATE ejercicios SET nombre_en = 'Cable Rope Front Raises'              WHERE nombre = 'Elevaciones Frontales con Cuerda en Polea';
UPDATE ejercicios SET nombre_en = 'High Cable Rear Delt Flyes'           WHERE nombre = 'Pájaros con Cables Cruzados en Polea Alta';
UPDATE ejercicios SET nombre_en = 'Seated Face Pull'                     WHERE nombre = 'Face Pull Sentado';
UPDATE ejercicios SET nombre_en = 'Smith Machine Trapezius Shrugs'       WHERE nombre = 'Encogimientos de Trapecio en Multipower';

-- BÍCEPS extra (6)
UPDATE ejercicios SET nombre_en = 'EZ Bar Spider Curl'                   WHERE nombre = 'Curl Araña (Spider Curl) con Barra EZ';
UPDATE ejercicios SET nombre_en = 'Bayesian Cable Curl'                  WHERE nombre = 'Curl Bayesian (Polea Baja de Espaldas)';
UPDATE ejercicios SET nombre_en = 'High Cable Bicep Curl (Double Bicep)' WHERE nombre = 'Curl de Bíceps en Polea Alta (Doble Bíceps)';
UPDATE ejercicios SET nombre_en = 'Dumbbell Zottman Curl'                WHERE nombre = 'Curl Zottman con Mancuernas';
UPDATE ejercicios SET nombre_en = 'Reverse Low Cable Curl'               WHERE nombre = 'Curl Inverso en Polea Baja';
UPDATE ejercicios SET nombre_en = 'Weighted Chin-ups'                    WHERE nombre = 'Dominadas Supinas con Lastre';

-- TRÍCEPS extra (6)
UPDATE ejercicios SET nombre_en = 'Cable Tricep Pushdown (V-Bar)'                  WHERE nombre = 'Extensión de Tríceps en Polea con Agarre V';
UPDATE ejercicios SET nombre_en = 'Unilateral Cable Tricep Extension (Reverse Grip)' WHERE nombre = 'Extensión de Tríceps Unilateral en Polea (Agarre Inverso)';
UPDATE ejercicios SET nombre_en = 'Overhead Cable Rope Tricep Extension'            WHERE nombre = 'Extensión de Tríceps Tras Nuca con Cuerda (Polea Baja)';
UPDATE ejercicios SET nombre_en = 'Two-Handed Overhead Dumbbell Extension'          WHERE nombre = 'Copa a Dos Manos con Mancuerna (Extensión Tras Nuca)';
UPDATE ejercicios SET nombre_en = 'JM Press'                                        WHERE nombre = 'Press JM con Barra';
UPDATE ejercicios SET nombre_en = 'Tate Press'                                      WHERE nombre = 'Tate Press con Mancuernas';

-- CORE extra (8)
UPDATE ejercicios SET nombre_en = 'Seated Ab Machine'                    WHERE nombre = 'Abdominales en Máquina Sentado';
UPDATE ejercicios SET nombre_en = 'Machine Torso Rotation'               WHERE nombre = 'Rotación de Torso en Máquina';
UPDATE ejercicios SET nombre_en = 'Roman Chair Leg Raises'               WHERE nombre = 'Elevación de Piernas en Silla Romana';
UPDATE ejercicios SET nombre_en = 'Farmer''s Walk'                       WHERE nombre = 'Paseo del Granjero (Farmer Walk)';
UPDATE ejercicios SET nombre_en = 'Turkish Get-Up'                       WHERE nombre = 'Turkish Get-Up (Levantamiento Turco)';
UPDATE ejercicios SET nombre_en = 'Toes to Bar'                          WHERE nombre = 'Toes to Bar (Pies a la Barra)';
UPDATE ejercicios SET nombre_en = 'L-Sit (Parallel Bar Isometric)'       WHERE nombre = 'L-Sit (Isométrico en Paralelas)';
UPDATE ejercicios SET nombre_en = 'Dragon Flag'                          WHERE nombre = 'Dragon Flag';

-- Verificación: mostrar ejercicios sin traducción
SELECT id, nombre FROM ejercicios WHERE nombre_en IS NULL;
