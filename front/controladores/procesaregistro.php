<?php
	session_start();
	// Fíjate que en Linux las barras son hacia adelante /
	require_once '../inc/bd.php'; 

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$usuario = trim($_POST['usuario']);
		$email = trim($_POST['email']);
		$password = $_POST['password'];

		// ... (resto de validaciones igual que te pasé antes) ...

		try {
		    // Verificar duplicados
		    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ? OR email = ?");
		    $stmt->execute([$usuario, $email]);

		    if ($stmt->rowCount() > 0) {
		        header("Location: ../registro.php?error=usuario_duplicado");
		        exit();
		    }

		    // Insertar
		    $passHash = password_hash($password, PASSWORD_DEFAULT);
		    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, email, password) VALUES (?, ?, ?)");
		    
		    if ($stmt->execute([$usuario, $email, $passHash])) {
		        header("Location:../index.php?registro=exito");
		        exit();
		    }
		} catch (PDOException $e) {
		    die("Error: " . $e->getMessage());
		}
	}
?>
