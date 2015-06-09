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
$error = 'success';

//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

if ($_POST['form'] == 'cursos') {
	$rutalimpia = clean($_POST['nombreCurso']);
	
	// Eliminar el curso, todos sus temas y videos, desregistrar usuarios y eliminar todas las carpetas hijas:
	if ($_POST['formOption'] == 'del') {
		// Crear la carpeta del curso:
		if (is_int(array_search($_POST['ubicacion'], array_column($listaDirs, 'ID')))) {
			$dir = $listaDirs[array_search($_POST['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
			$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
		}
		
		if (file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia)) {
			removeDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia);
		}

		deleteFullCurso($_POST['IDcurso']);

		$msgError = 'Curso eliminado correctamente';
		$error = 'danger';
		
	} else {
		if ($_POST['publico'] == 'on') {
			$_POST['publico'] = 1;
		} else {
			$_POST['publico'] = 0;
		}

		// Si el curso es nuevo
		if (!$_POST['IDcurso']) {
			$msgError = 'Datos guardados correctamente';
			
			// Comprobar que no exista el nombre, ni la ruta:
			if ( ($_POST['nombreCurso'] != '')&&( (checkCurso('nombre = "'.$_POST['nombreCurso'].'"') > 0)||(checkCurso('ruta = "'.$rutaLimpia.'"') > 0) ) )  {
				$msgError = 'El curso ya existe';
				$error = 'warning';
			}

			// Comprobar que no este asociado el curso de Moodle a otro curso:
			if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('IDcursoMoodle = '.$_POST['IDcursoMoodle']) > 0) ) {
				$msgError = 'Este curso de Moodle ya está asociado a otro curso';
				$error = 'warning';
			}

			if ($error == 'success') {
				// Crear la carpeta del curso:
				if (is_int(array_search($_POST['ubicacion'], array_column($listaDirs, 'ID')))) {
					$dir = $listaDirs[array_search($_POST['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
					$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
				}
				
				if (!file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia)) {
					createDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia);
				}
				
				// Crear el curso en la base de datos:
				crearCurso($_POST['nombreCurso'], $rutalimpia, $_POST['ubicacion'], $_POST['descripcion'], $_POST['IDcursoMoodle'], $_POST['fechaIni'], $_POST['fechaFin'], $_POST['publico']);

				$IDcurso = getIDcurso($_POST['nombreCurso'], $rutalimpia, $_POST['ubicacion'], 0);
				$_POST['IDcurso'] = $IDcurso;

				if ($_POST['IDcursoMoodle'] != '') {
					// Obtener los usuarios inscritos al curso:
					$usuariosEnCurso = connect('core_enrol_get_enrolled_users', array( 'courseid' => $_POST['IDcursoMoodle'] ));
					foreach ($usuariosEnCurso as $user) {
						registrarUsuarioCurso($IDcurso, $_POST['IDcursoMoodle'], $user->fullname, $user->email);
					}
				}
			}

		// Si se ha editado el curso:
		} else {
			$msgError = 'Datos actualizados correctamente';

			// Comprobar que no exista el nombre, ni la ruta:
			if ( ($_POST['nombreCurso'] != '')&&( (checkCurso('ID != '.$_POST['IDcurso'].' AND nombre = "'.$_POST['nombreCurso'].'"') > 0)||(checkCurso('ID != '.$_POST['IDcurso'].' AND ruta = "'.$rutaLimpia.'"') > 0) ) ) {
				$msgError = 'El curso ya existe';
				$error = 'warning';
			}

			// Comprobar que no este asociado el curso de Moodle a otro curso:
			if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('ID != '.$_POST['IDcurso'].' AND IDcursoMoodle = '.$_POST['IDcursoMoodle']) > 0) ) {
				$msgError = 'Este curso de Moodle ya está asociado a otro curso';
				$error = 'warning';
			}

			if ($error == 'success') {
				// Si ha cambiado la ubicacion o la ruta, renombrar/mover la carpeta:
				if ( ($rutalimpia != $_POST['rutaCursoORI'])||($_POST['ubicacion'] != $_POST['ubicacionORI']) ) {
					$renombrarCurso = 1;
					
					if (is_int(array_search($_POST['ubicacion'], array_column($listaDirs, 'ID')))) {
						$dir = $listaDirs[array_search($_POST['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
						$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
					}
				}

				if ($renombrarCurso == 1) {
					// Renombrar/mover la carpeta en la ubicacion original:
					if (is_int(array_search($_POST['ubicacionORI'], array_column($listaDirs, 'ID')))) {
						$dirORI = $listaDirs[array_search($_POST['ubicacionORI'], array_column($listaDirs, 'ID'))]['ruta'];
						$dirORI = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dirORI);
					}
					if ($dir == '') {
						$dir = $dirORI;
					}

					if ( ($dirORI != '')&&($dir != '') ) {
						// Si existe el curso original, renombrarlo:
						if (is_dir(_DOCUMENTROOT._DIRCURSOS.$dirORI.$_POST['rutaCursoORI'])) {
							rename(_DOCUMENTROOT._DIRCURSOS.$dirORI.$_POST['rutaCursoORI'], _DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia);
						}
					}
				}

				if ( ($_POST['IDcursoMoodle'] != '')&&($_POST['IDcursoMoodle'] != $_POST['IDcursoMoodleORI']) ) {
					desregistrarUsuariosCurso($_POST['IDcurso']);
					
					// Obtener los usuarios inscritos al curso:
					$usuariosEnCurso = connect('core_enrol_get_enrolled_users', array( 'courseid' => $_POST['IDcursoMoodle'] ));
					
					foreach ($usuariosEnCurso as $user) {
						registrarUsuarioCurso($_POST['IDcurso'], $_POST['IDcursoMoodle'], $user->fullname, $user->email);
					}
				}

				// Actualizar el curso en la base de datos:
				updateCurso($_POST['IDcurso'], $_POST['nombreCurso'], $rutalimpia, $_POST['ubicacion'], $_POST['descripcion'], $_POST['IDcursoMoodle'], $_POST['fechaIni'], $_POST['fechaFin'], $_POST['publico']);
			}
		}
	}
}

?>