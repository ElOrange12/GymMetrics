<?php
// front/controladores/procesalogin.php
session_start();

// 1. Conexión a la base de datos
require_once '../inc/bd.php';

// Verificamos que los datos vengan por POST (RA5: Entrada de información)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recogemos los datos del formulario limpiando espacios en blanco
    $usuario_email = trim($_POST['usuario']);
    $password = $_POST['password'];

    // Comprobamos que no estén vacíos (RA3: Estructuras de control condicionales)
    if (empty($usuario_email) || empty($password)) {
        header("Location: ../index.php?error=campos_vacios");
        exit();
    }

    try {
        // Buscamos al usuario en la BD usando PDO para evitar Inyecciones SQL
        $stmt = $pdo->prepare("SELECT id, nombre_usuario, password FROM usuarios WHERE nombre_usuario = ? OR email = ?");
        $stmt->execute([$usuario_email, $usuario_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificamos si el usuario existe y si la contraseña coincide
        if ($user && password_verify($password, $user['password'])) {
            
            // Login correcto: Guardamos el ID en la sesión
            $_SESSION['user_id'] = $user['id'];

            // =================================================================
            // INICIO DE LA IMPLEMENTACIÓN DEL LOGGER (RA2, RA4 y RA8)
            // =================================================================
            
            // 1. Incluimos el archivo que contiene nuestra clase
            require_once '../clases/Logger.php';
            
            // 2. Instanciamos el objeto (Aplicando POO)
            $miLogger = new Logger();
            
            // 3. Llamamos al método registrar() para guardar el log en JSONL
            $miLogger->registrar($user['id'], 'INICIO_SESION_EXITOSO');
            
            // =================================================================
            // FIN DE LA IMPLEMENTACIÓN DEL LOGGER
            // =================================================================

            // Redirigimos al panel de control principal (RA5: Salida)
            header("Location: ../exito.php");
            exit();
            
        } else {
            // Si la contraseña o el usuario fallan, lo devolvemos al inicio con error
            header("Location: ../index.php?error=credenciales");
            exit();
        }
    } catch (PDOException $e) {
        // RA3: Control de excepciones (try/catch)
        die("Error en la consulta: " . $e->getMessage());
    }
} else {
    // Si alguien intenta entrar directamente a este archivo escribiendo la URL
    header("Location: ../index.php");
    exit();
}
?>
