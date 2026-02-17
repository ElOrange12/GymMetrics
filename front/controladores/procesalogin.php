<?php
	session_start();
	require_once __DIR__ . '../inc/bd.php';

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$userEntry = trim($_POST['usuario']);
		$passEntry = $_POST['password'];

		try {
		    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ? OR email = ?");
		    $stmt->execute([$userEntry, $userEntry]);
		    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

		    if ($usuario && password_verify($passEntry, $usuario['password'])) {
		        $_SESSION['user_id'] = $usuario['id'];
		        $_SESSION['user_name'] = $usuario['nombre_usuario'];
		        
		        // Redirección en Linux: asegúrate que exito.php existe y se llama así (minúsculas)
		        header("Location: ../exito.php");
		        exit();
		    } else {
		        header("Location: ../index.php?error=credenciales");
		        exit();
		    }
		} catch (PDOException $e) {
		    die("Error: " . $e->getMessage());
		}
	}
?>
