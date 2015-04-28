<?php
	
	include_once(_DOCUMENTROOT.'config.php');
	include_once(_DOCUMENTROOT.'modules/functions.php');
	include_once(_DOCUMENTROOT.'db/db.php');
	
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