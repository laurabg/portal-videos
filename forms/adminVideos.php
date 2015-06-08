<?php

include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');

global $db;

$ok = 0;
$msgError = '';
$error = '';
//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

if ($_POST['form'] == 'videos') {
	if ($_POST['nombreVideo'] == '') {
		$msgError = 'Faltan datos';
		$error = 'warning';
	}
	if (!$_POST['IDvideo']) {
		if ( ($_POST['nombreVideo'] != '')&&(checkVideo('nombre = "'.$_POST['nombreVideo'].'"') > 0) ) {
			$msgError = 'El vídeo ya existe';
			$error = 'warning';
		}
	} else {
		if ( ($_POST['nombreVideo'] != '')&&(checkVideo('ID != '.$_POST['IDvideo'].' AND nombre = "'.$_POST['nombreVideo'].'"') > 0) ) {
			$msgError = 'El vídeo ya existe';
			$error = 'warning';
		}
	}
	// Si no se ha producido error, crear el tema:
	if ($error == '') {
		if (!$_POST['IDvideo']) {
			// Crear el curso en la base de datos:
			crearVideo($_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['descripcion'], '');

			$IDvideo = getIDvideo($_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], '', 0);
			$_POST['IDvideo'] = $IDvideo;

			$msgError = 'Datos guardados correctamente';
			$error = 'success';
		} else {
			// Crear el curso en la base de datos:
			updateCurso($_POST['IDvideo'], $_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['descripcion']);

			$msgError = 'Datos actualizados correctamente';
			$error = 'success';
		}
	}
}

?>