<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $dbConfig;

$msgError = '';
$error = '';
//foreach ($_POST as $key => $value)
//	print "Field ".$key." is ".$value."<br>";

if ($_POST['form'] == 'config') {
	$msgError = 'Datos guardados correctamente.';
	$error = 'success';

	if ($_POST['showErrors'] == 'on') {
		updateAdminvar('showErrors', 1);
	} else {
		updateAdminvar('showErrors', 0);
	}

	if ($_POST['_OCULTO'] == 'on') {
		updateAdminvar('_OCULTO', 1);
	} else {
		updateAdminvar('_OCULTO', 0);
	}

	if ($_POST['_MOODLEALLUSERS'] == 'on') {
		updateAdminvar('_MOODLEALLUSERS', 1);
	} else {
		updateAdminvar('_MOODLEALLUSERS', 0);
	}

	updateAdminvar('_DIRCURSOS', $_POST['_DIRCURSOS']);
	updateAdminvar('_ADMINDEF', $_POST['_ADMINDEF']);
	updateAdminvar('_ADMINPASS', $_POST['_ADMINPASS']);
	updateAdminvar('_MOODLEURL', $_POST['_MOODLEURL']);
	updateAdminvar('_WSTOKEN', $_POST['_WSTOKEN']);

	if ($_POST['_MOODLEURL'] == '') {
		$msgError .= ' Ha borrado la URL de Moodle. Ahora no se podr&aacute; acceder a los servicios web.';
		$error = 'warning';
	}

	if ($_POST['_WSTOKEN'] == '') {
		$msgError .= ' Ha borrado la Token de los servicios web de Moodle. Ahora no se podr&aacute; acceder a los servicios web.';
		$error = 'warning';
	}

	if (sizeof($_POST['ubicacion']) > 0) {
		foreach ($_POST['ubicacion'] as $ID => $ruta) {
			if (checkUbicacion('ruta = "'.$ruta.'" AND ID != '.$ID) == 0) {
				updateUbicacion($ID, $ruta);
			} else {
				$msgError .= ' No se ha actualizado la ruta a '.$ruta.' porque ya existe.';
				$error = 'warning';
			}
		}
	}
	
	if (sizeof($_POST['del-ubicacion']) > 0) {
		foreach ($_POST['del-ubicacion'] as $ub) {
			deleteUbicacion($ub);
		}
	}

	if (sizeof($_POST['ubicacion-new']) > 0) {
		foreach ($_POST['ubicacion-new'] as $ub) {
			if ($ub != '') {
				if (substr($ub, -1) != '/') {
					$ub = $ub.'/';
				}
				crearUbicacion($ub);
			}
		}
	}

	if (sizeof($_POST['extension']) > 0) {
		foreach ($_POST['extension'] as $ID => $nombre) {
			if (checkExtension('nombre = "'.$nombre.'" AND ID != '.$ID) == 0) {
				updateExtension($ID, $nombre);
			} else {
				$msgError .= ' No se ha actualizado la extensi&oacute;n a '.$nombre.' porque ya existe.';
				$error = 'warning';
			}
		}
	}
	
	if (sizeof($_POST['del-extension']) > 0) {
		foreach ($_POST['del-extension'] as $ext) {
			deleteExtension($ext);
		}
	}

	if (sizeof($_POST['extension-new']) > 0) {
		foreach ($_POST['extension-new'] as $ext) {
			if ($ext != '') {
				crearExtension($ext);
			}
		}
	}
}

?>