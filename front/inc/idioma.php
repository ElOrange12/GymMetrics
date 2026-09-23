<?php
	// front/inc/idioma.php
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	// Si el usuario hace clic en el botón de cambiar idioma, actualizamos la sesión
	if (isset($_GET['lang'])) {
		if ($_GET['lang'] == 'en' || $_GET['lang'] == 'es') {
		    $_SESSION['lang'] = $_GET['lang'];
		}
	}

	// Por defecto será español si no ha elegido nada
	$lang_code = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es';

	// Cargamos el diccionario correspondiente en la variable $lang
	$lang = require_once __DIR__ . "/lang/{$lang_code}.php";
?>
