<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;

$changeCursoMoodle = 0;
$renombrarCurso = 0;
$dirORI = '';
$dir = '';
$msgError = '';
$error = '';

foreach ($_POST as $key => $value)
	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

if ($_POST['form'] == 'cursos') {

	// Si el curso es nuevo
	if (!$_POST['IDcurso']) {
		$msgError = 'Datos guardados correctamente';
		$error = 'success';

	// Si se ha editado el curso:
	} else {
		$msgError = 'Datos actualizados correctamente';
		$error = 'success';

		$rutalimpia = clean($_POST['nombreCurso']);

		if ( ($rutalimpia != $_POST['rutaCursoORI'])||($_POST['ubicacion'] != $_POST['ubicacionORI']) ) {
			$renombrarCurso = 1;
			
			if (is_int(array_search($_POST['ubicacion'], array_column($listaDirs, 'ID')))) {
				$dir = $listaDirs[array_search($_POST['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
				$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
			}
		}

		if ($renombrarCurso == 1) {
			// Renombrar la carpeta en la ubicacion original:
			if (is_int(array_search($_POST['ubicacionORI'], array_column($listaDirs, 'ID')))) {
				$dirORI = $listaDirs[array_search($_POST['ubicacionORI'], array_column($listaDirs, 'ID'))]['ruta'];
				$dirORI = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dirORI);
			}
			if ($dir == '') {
				$dir = $dirORI;
			}

			echo "+".$dirORI." + ".$dir."+<br />";

			if ( ($dirORI != '')&&($dir != '') ) {
				echo '*****'._DOCUMENTROOT._DIRCURSOS.$dirORI.$_POST['rutaCursoORI'].' --> '._DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia.'****<br />';

				// Si existe el curso original, renombrarlo:
				if (is_dir(_DOCUMENTROOT._DIRCURSOS.$dirORI.$_POST['rutaCursoORI'])) {
					rename(_DOCUMENTROOT._DIRCURSOS.$dirORI.$_POST['rutaCursoORI'], _DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia);
				}
			}
		}

	}


	/*if ($_POST['formOption'] == 'del') {
		$msgError = 'Curso eliminado correctamente';
		$error = 'danger';
	} else {
		// Comprobar que el curso de Moodle no esté ya asociado:
		if (!$_POST['IDcurso']) {
			if ( ($_POST['nombreCurso'] != '')&&(checkCurso('nombre = "'.$_POST['nombreCurso'].'"') > 0) ) {
				$msgError = 'El curso ya existe';
				$error = 'warning';
			}
			if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('IDcursoMoodle = '.$_POST['IDcursoMoodle']) > 0) ) {
				$msgError = 'Este curso de Moodle ya está asociado a otro curso';
				$error = 'warning';
			}
		} else {
			if ( ($_POST['nombreCurso'] != '')&&(checkCurso('ID != '.$_POST['IDcurso'].' AND nombre = "'.$_POST['nombreCurso'].'"') > 0) ) {
				$msgError = 'El curso ya existe';
				$error = 'warning';
			}
			if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('ID != '.$_POST['IDcurso'].' AND IDcursoMoodle = '.$_POST['IDcursoMoodle']) > 0) ) {
				$msgError = 'Este curso de Moodle ya está asociado a otro curso';
				$error = 'warning';
			} else if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('ID = '.$_POST['IDcurso'].' AND IDcursoMoodle != '.$_POST['IDcursoMoodle']) > 0) ) {
				$changeCursoMoodle = 1;
			}
		}
		if ($_POST['fechaIni'] > $_POST['fechaFin']) {
		}
		if ($_POST['publico'] == 'on') {
			$_POST['publico'] = 1;
		} else {
			$_POST['publico'] = 0;
		}
		
		// Si no se ha producido error, crear el curso:
		if ($error == '') {
			if (!$_POST['IDcurso']) {
				// Crear el curso en la base de datos:
				crearCurso($_POST['nombreCurso'], $_POST['rutaCurso'], $_POST['descripcion'], $_POST['IDcursoMoodle'], $_POST['fechaIni'], $_POST['fechaFin'], $_POST['publico']);

				$IDcurso = getIDcurso($_POST['nombreCurso'], $_POST['rutaCurso'], 0);
				$_POST['IDcurso'] = $IDcurso;

				// Obtener los usuarios inscritos al curso:
				$usuariosEnCurso = connect('core_enrol_get_enrolled_users', array( 'courseid' => $_POST['IDcursoMoodle'] ));
				foreach ($usuariosEnCurso as $user) {
					echo '***'.$user->email.'<br />';
					registrarUsuarioCurso($_POST['IDcurso'], $_POST['IDcursoMoodle'], $user->email);
				}

				$msgError = 'Datos guardados correctamente';
				$error = 'success';
			} else {
				//checkCurso('ID = '.$_POST['IDcurso'].' AND IDcursoMoodle = '.$_POST['IDcursoMoodle']);

				// Si el nombre ha cambiado, renombrar la carpeta:
				if (checkCurso('nombre != "'.$_POST['nombreCurso'].'" AND IDcurso = '.$_POST['IDcurso']) > 0) {
					// Limpiar el nombre de la carpeta de caracteres extraños y espacios
					$rutaNEW = clean($_POST['nombreCurso']);
					rename($dir."/".$_POST['rutaCurso'], $dir."/".$rutaNEW);
					
				}

				// Actualizar el curso en la base de datos:
				updateCurso($_POST['IDcurso'], $_POST['nombreCurso'], $_POST['rutaCurso'], $_POST['descripcion'], $_POST['IDcursoMoodle'], $_POST['fechaIni'], $_POST['fechaFin'], $_POST['publico']);

				if ( ($changeCursoMoodle == 1)||($_POST['IDcursoMoodle'] != $_POST['IDcursoMoodleORI']) ) {
					desregistrarUsuariosCurso($_POST['IDcurso']);
					
					// Obtener los usuarios inscritos al curso:
					$usuariosEnCurso = connect('core_enrol_get_enrolled_users', array( 'courseid' => $_POST['IDcursoMoodle'] ));
					foreach ($usuariosEnCurso as $user) {
						echo $user->email.'<br />';
						registrarUsuarioCurso($_POST['IDcurso'], $_POST['IDcursoMoodle'], $user->email);
					}
				}
				
				if ($_POST['rutaCurso'] != $_POST['rutaCursoORI']) {
					echo 'Renombrar la carpeta de '.$_POST['rutaCursoORI'].' a '.$_POST['rutaCurso'].'<br />';
					rename($_POST['rutaCursoORI'], $_POST['rutaCurso']);
				}
				
				$msgError = 'Datos actualizados correctamente';
				$error = 'success';
			}
		}
	}*/
}


?>