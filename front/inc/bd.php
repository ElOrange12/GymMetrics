<?php
	// controladores/bd.php

	$host = 'localhost';
	$dbname = 'gymmetrics';
	$user = 'admin_gym'; // El usuario que acabamos de crear
	$pass = '1234';      // La contraseña que le pusiste

	try {
		$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("¡Error de conexión!: " . $e->getMessage());
	}
?>
