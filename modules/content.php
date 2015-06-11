<?php
	
	include_once(__DIR__.'/../config.php');
	include_once(_DOCUMENTROOT.'modules/functions.php');
	include_once(_DOCUMENTROOT.'db/db.php');
	
	if ( (isset($_GET['username']))&&(isset($_GET['email'])) ) {
		if (checkUsuario('email = "'.$_GET['email'].'" AND username = ""') > 0) {
			asociarUsernameEmail($_GET['email'], $_GET['username']);
		}
		$usuario = getUserData('', $_GET['username'], '');
		setcookie('MoodleUserSession', serialize($usuario), time() + (86400 * 30), _PORTALROOT);

		if (checkUsuario('username = "'.$_POST['userName'].'" AND esAdmin = 1') > 0) {
			setcookie('MoodleUserAdmin', 1, time() + (86400 * 30), _PORTALROOT);
		}

		header('Location: http://'.$_SERVER['HTTP_HOST']._PORTALROOT.'?IDcurso='.$_GET['IDcurso']);
		die();
	}

	// Listado cursos |-----------------------------------------------
	if (!isset($_GET['IDcurso'])) {
		include_once(_DOCUMENTROOT.'modules/templates/listadoCursos.php');

	// Listado temas |------------------------------------------------
	} elseif ( (isset($_GET['IDcurso']))&&(!isset($_GET['IDtema']))&&(!isset($_GET['IDvideo'])) ) {
		$IDcurso = $_GET['IDcurso'];
		include_once(_DOCUMENTROOT.'modules/templates/listadoTemas.php');

	// Listado vídeos |-----------------------------------------------
	} elseif ( (isset($_GET['IDcurso']))&&(isset($_GET['IDtema']))&&(!isset($_GET['IDvideo'])) ) {
		$IDcurso = $_GET['IDcurso'];
		$IDtema = $_GET['IDtema'];
		
		include_once(_DOCUMENTROOT.'modules/templates/listadoVideos.php');

	// Detalle vídeo |------------------------------------------------
	} elseif ( (isset($_GET['IDcurso']))&&(isset($_GET['IDtema']))&&(isset($_GET['IDvideo'])) ) {
		$IDcurso = $_GET['IDcurso'];
		$IDtema = $_GET['IDtema'];
		$IDvideo = $_GET['IDvideo'];
		
		include_once(_DOCUMENTROOT.'modules/templates/detalleVideo.php');
	}

?>