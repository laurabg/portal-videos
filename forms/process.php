<?php
include_once('../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'ws/connection.php');

global $db;

$ok = 0;
$changeCursoMoodle = 0;
$error = '';
//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

if ($_POST['form'] == 'cursos') {
	if ($_POST['formOption'] == 'del') {
		$ok = 3;
	} else {
		// Comprobar que el curso de Moodle no esté ya asociado:
		if ($_POST['nombreCurso'] == '') {
			$error = 'Faltan datos';
		}
		if (!$_POST['IDcurso']) {
			if ( ($_POST['nombreCurso'] != '')&&(checkCurso('nombre = "'.$_POST['nombreCurso'].'"') > 0) ) {
				$error = 'El curso ya existe';
			}
			if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('IDcursoMoodle = '.$_POST['IDcursoMoodle']) > 0) ) {
				$error = 'Este curso de Moodle ya está asociado a otro curso';
			}
		} else {
			if ( ($_POST['nombreCurso'] != '')&&(checkCurso('ID != '.$_POST['IDcurso'].' AND nombre = "'.$_POST['nombreCurso'].'"') > 0) ) {
				$error = 'El curso ya existe';
			}
			if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('ID != '.$_POST['IDcurso'].' AND IDcursoMoodle = '.$_POST['IDcursoMoodle']) > 0) ) {
				$error = 'Este curso de Moodle ya está asociado a otro curso';
			} else if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('ID = '.$_POST['IDcurso'].' AND IDcursoMoodle != '.$_POST['IDcursoMoodle']) > 0) ) {
				$changeCursoMoodle = 1;
			}
		}
		if ($_POST['fechaIni'] > $_POST['fechaFin']) {
		}
		
		// Si no se ha producido error, crear el curso:
		if ($error == '') {
			if (!$_POST['IDcurso']) {
				// Crear el curso en la base de datos:
				crearCurso($_POST['nombreCurso'], $_POST['descripcion'], $_POST['IDcursoMoodle'], $_POST['fechaIni'], $_POST['fechaFin'], $_POST['publico']);

				$IDcurso = getIDcurso($_POST['nombreCurso'], 0);
				$_POST['IDcurso'] = $IDcurso;

				// Obtener los usuarios inscritos al curso:
				$usuariosEnCurso = connect('core_enrol_get_enrolled_users', array( 'courseid' => $_POST['IDcursoMoodle'] ));
				foreach ($usuariosEnCurso as $user) {
					registrarUsuarioCurso($_POST['IDcurso'], $_POST['IDcursoMoodle'], $user->email);
				}

				$ok = 1;
			} else {
				//checkCurso('ID = '.$_POST['IDcurso'].' AND IDcursoMoodle = '.$_POST['IDcursoMoodle']);

				// Crear el curso en la base de datos:
				updateCurso($_POST['IDcurso'], $_POST['nombreCurso'], $_POST['descripcion'], $_POST['IDcursoMoodle'], $_POST['fechaIni'], $_POST['fechaFin'], $_POST['publico']);

				if ($changeCursoMoodle == 1) {
					desregistrarUsuariosCurso($_POST['IDcurso']);

					// Obtener los usuarios inscritos al curso:
					$usuariosEnCurso = connect('core_enrol_get_enrolled_users', array( 'courseid' => $_POST['IDcursoMoodle'] ));
					foreach ($usuariosEnCurso as $user) {
						registrarUsuarioCurso($_POST['IDcurso'], $_POST['IDcursoMoodle'], $user->email);
					}
				}

				$ok = 2;
			}
		}
	}
} else if ($_POST['form'] == 'temas') {
	if ($_POST['nombreTema'] == '') {
		$error = 'Faltan datos';
	}
	if (!$_POST['IDtema']) {
		if ( ($_POST['nombreTema'] != '')&&(checkTema('nombre = "'.$_POST['nombreTema'].'"') > 0) ) {
			$error = 'El tema ya existe';
		}
	} else {
		if ( ($_POST['nombreTema'] != '')&&(checkTema('ID != '.$_POST['IDtema'].' AND nombre = "'.$_POST['nombreTema'].'"') > 0) ) {
			$error = 'El tema ya existe';
		}
	}
	// Si no se ha producido error, crear el tema:
	if ($error == '') {
		if (!$_POST['IDtema']) {
			// Crear el curso en la base de datos:
			crearTema($_POST['IDcurso'], $_POST['nombreTema'], $_POST['descripcion']);

			$IDtema = getIDtema($_POST['IDcurso'], $_POST['nombreTema'], 0);
			$_POST['IDtema'] = $IDtema;

			$ok = 1;
		} else {
			// Crear el curso en la base de datos:
			updateTema($_POST['IDtema'], $_POST['IDcurso'], $_POST['nombreTema'], $_POST['descripcion']);

			$ok = 2;
		}
	}

} else if ($_POST['form'] == 'videos') {
	if ($_POST['nombreVideo'] == '') {
		$error = 'Faltan datos';
	}
	if (!$_POST['IDvideo']) {
		if ( ($_POST['nombreVideo'] != '')&&(checkVideo('nombre = "'.$_POST['nombreVideo'].'"') > 0) ) {
			$error = 'El vídeo ya existe';
		}
	} else {
		if ( ($_POST['nombreVideo'] != '')&&(checkVideo('ID != '.$_POST['IDvideo'].' AND nombre = "'.$_POST['nombreVideo'].'"') > 0) ) {
			$error = 'El vídeo ya existe';
		}
	}
	// Si no se ha producido error, crear el tema:
	if ($error == '') {
		if (!$_POST['IDvideo']) {
			// Crear el curso en la base de datos:
			crearVideo($_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['descripcion']);

			$IDvideo = getIDvideo($_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], 0);
			$_POST['IDvideo'] = $IDvideo;

			$ok = 1;
		} else {
			// Crear el curso en la base de datos:
			updateCurso($_POST['IDvideo'], $_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['descripcion']);

			$ok = 2;
		}
	}
}

?>