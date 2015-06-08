<?php

include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');

global $db;

$msgError = '';
$error = '';
//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

if ($_POST['form'] == 'temas') {
	if ($_POST['nombreTema'] == '') {
		$msgError = 'Faltan datos';
		$error = 'warning';
	}
	if (!$_POST['IDtema']) {
		if ( ($_POST['nombreTema'] != '')&&(checkTema('nombre = "'.$_POST['nombreTema'].'"') > 0) ) {
			$msgError = 'El tema ya existe';
			$error = 'warning';
		}
	} else {
		if ( ($_POST['nombreTema'] != '')&&(checkTema('ID != '.$_POST['IDtema'].' AND nombre = "'.$_POST['nombreTema'].'"') > 0) ) {
			$msgError = 'El tema ya existe';
			$error = 'warning';
		}
	}
	// Si no se ha producido error, crear el tema:
	if ($error == '') {
		if (!$_POST['IDtema']) {
			// Crear el curso en la base de datos:
			crearTema($_POST['IDcurso'], $_POST['nombreTema'], $_POST['ruta'], $_POST['descripcion']);

			$IDtema = getIDtema($_POST['IDcurso'], $_POST['nombreTema'], $_POST['ruta'], 0);
			$_POST['IDtema'] = $IDtema;

			$msgError = 'Datos guardados correctamente';
			$error = 'success';
		} else {
			// Crear el curso en la base de datos:
			updateTema($_POST['IDtema'], $_POST['IDcurso'], $_POST['nombreTema'], $_POST['ruta'], $_POST['descripcion']);
				
			$msgError = 'Datos actualizados correctamente';
			$error = 'success';
		}
	}
}

?>