<?php
	// front/index.php
	session_start();

    // 1. CARGAMOS EL MOTOR DE IDIOMAS
    require_once 'inc/idioma.php';

	// Si ya está logueado, lo mandamos directo al dashboard
	if (isset($_SESSION['user_id'])) {
		header("Location: exito.php");
		exit();
	}
?>

<!DOCTYPE html>
<html lang="<?= $lang_code ?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= $lang['login_title'] ?> | GymMetrics</title>
		<link rel="stylesheet" href="css/login.css">
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
		<style>
		    .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 14px; text-align: center; }
		    .alert-danger { background-color: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid #e74c3c; }
		    .alert-success { background-color: rgba(46, 204, 113, 0.2); color: #2ecc71; border: 1px solid #2ecc71; }
            
            /* Selector de idioma flotante */
            /* Interruptor de idioma estilo píldora moderna */
            .lang-switch {
                position: absolute;
                top: 20px;
                right: 20px;
                display: flex;
                background: rgba(21, 27, 34, 0.7);
                border: 1px solid #34495e;
                border-radius: 30px;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(8px);
                z-index: 100;
            }
            
            .lang-switch a {
                padding: 8px 16px;
                font-size: 13px;
                font-weight: bold;
                color: #7f8c8d;
                text-decoration: none;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 5px;
            }
            
            .lang-switch a.active {
                background: #3498db; /* Tu azul neón */
                color: white;
                box-shadow: 0 0 15px rgba(52, 152, 219, 0.4);
            }
            
            .lang-switch a:not(.active):hover {
                color: white;
                background: rgba(255, 255, 255, 0.05);
            }
		</style>
	</head>
	<body>

        <div class="lang-switch">
            <a href="?lang=es" class="<?= $lang_code == 'es' ? 'active' : '' ?>">
                ES
            </a>
            <a href="?lang=en" class="<?= $lang_code == 'en' ? 'active' : '' ?>">
                EN
            </a>
        </div>

		<div class="login-container">
		    <div class="login-box">
		        <div class="logo-space">
		            <img src="img/LogoGymMetrics.png" alt="GymMetrics" class="logo" style="max-width: 80px;">
		        </div>
		        
		        <h2><?= $lang['login_title'] ?></h2>
		        <p class="subtitle"><?= $lang['login_subtitle'] ?></p>

		        <?php if(isset($_GET['error'])): ?>
		            <div class="alert alert-danger">
		                <?php 
		                    if($_GET['error'] == 'credenciales') echo $lang['err_credentials'];
		                    if($_GET['error'] == 'campos_vacios') echo $lang['err_empty'];
		                    if($_GET['error'] == 'nologin') echo $lang['err_nologin'];
		                ?>
		            </div>
		        <?php endif; ?>

		        <?php if(isset($_GET['registro']) && $_GET['registro'] == 'exito'): ?>
		            <div class="alert alert-success">
		                <?= $lang['msg_registered'] ?>
		            </div>
		        <?php endif; ?>

		        <form action="controladores/procesalogin.php" method="POST">
		            <div class="input-group">
		                <label for="usuario"><?= $lang['user_or_email'] ?></label>
		                <input type="text" id="usuario" name="usuario" required>
		            </div>

		            <div class="input-group">
		                <label for="password"><?= $lang['password'] ?></label>
		                <input type="password" id="password" name="password" required>
		            </div>

		            <button type="submit" class="btn-login"><?= $lang['btn_access'] ?></button>
		            
		            <div class="form-footer">
		                <hr>
		                <p><?= $lang['no_account'] ?> <a href="registro.php" class="highlight"><?= $lang['register_here'] ?></a></p>
		            </div>
		        </form>
		    </div>
		</div>
	</body>
</html>
