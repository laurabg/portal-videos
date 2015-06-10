<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;
global $extensionesValidas;

$renombrarVideo = 0;
$dir = '';
$msgError = '';
$error = 'success';

//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

if ($_POST['form'] == 'videos') {
	// Obtener la informacion del curso:
	$cursoData = getCursoData($_POST['IDcurso']);

	// Obtener la informacion del tema:
	$temaData = getTemaData($_POST['IDtema'], $_POST['IDcurso']);

	// Obtener la ubicacion del curso:
	if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
		$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
		$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
	}

	// Obtener el path completo del video:
	$dir = _DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/';

	// Eliminar el video y la imagen de portada:
	if (isset($_POST['formDel'])) {
		// Eliminar el archivo de video:
		if (file_exists($dir.$_POST['rutaVideo'])) {
			removeFile($dir.$_POST['rutaVideo']);
		}

		// Eliminar la portada:
		if (file_exists($dir.'img/'.$_POST['img'])) {
			removeFile($dir.'img/'.$_POST['img']);
		}

		// Borrar el registro de la bbdd:
		deleteVideo($_POST['IDvideo']);

		$msgError = 'V&iacute;deo eliminado correctamente';
		$error = 'danger';

	} else {
		if ($_POST['ocultar'] == 'on') {
			$_POST['ocultar'] = 1;
		} else {
			$_POST['ocultar'] = 0;
		}

		// Comprobar que la extension es valida:
		$extension = pathinfo($dir.$_POST['rutaVideo'], PATHINFO_EXTENSION);
		if (!is_int(array_search($extension, array_column($extensionesValidas, 'nombre')))) {
			$msgError = 'La extensi&oacute;n del archivo de v&iacute;deo no es v&aacute;lida.';
			$error = 'warning';
		}

		if ($error == 'success') {
			// Si el video es nuevo:
			if (!$_POST['IDvideo']) {
				// Comprobar que no exista el nombre, ni la ruta en el mismo tema y curso:
				if ( ($_POST['nombreVideo'] != '')&&( (checkVideo('nombre = "'.$_POST['nombreVideo'].'" AND IDtema = '.$_POST['IDtema'].' AND IDcurso = '.$_POST['IDcurso']) > 0)||(checkVideo('ruta = "'.$_POST['rutaVideo'].'" AND IDtema = '.$_POST['IDtema'].' AND IDcurso = '.$_POST['IDcurso']) > 0) ) )  {
					$msgError = 'El v&iacute;deo ya existe';
					$error = 'warning';
				}

				// Comprobar que el video existe:
				if ( ($_POST['rutaVideo'] != '')&&(!file_exists($dir.$_POST['rutaVideo'])) ) {
					$msgError = 'El archivo de v&iacute;deo no existe.';
					$error = 'warning';
				}

				if ($error == 'success') {
					$msgError = 'Datos guardados correctamente';

					// Obtener la portada del archivo de video:
					$_POST['img'] = getPortada($_POST['rutaVideo'], $dir);
					
					// Crear el video en la base de datos:
					crearVideo($_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['descripcion'], $_POST['rutaVideo'], $_POST['img'], $_POST['orden'], $_POST['ocultar']);
					
					$_POST['IDvideo'] = getIDvideo($_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['rutaVideo'], 0);
				}

			// Si se ha editado el video:
			} else {
				// Comprobar que no exista el nombre, ni la ruta en el mismo tema y curso:
				if ( ($_POST['nombreVideo'] != '')&&( (checkVideo('ID != '.$_POST['IDvideo'].' AND IDcurso = '.$_POST['IDcurso'].' AND IDtema = '.$_POST['IDtema'].' AND nombre = "'.$_POST['nombreVideo'].'"') > 0)||(checkVideo('ID != '.$_POST['IDvideo'].' AND IDcurso = '.$_POST['IDcurso'].' AND IDtema = '.$_POST['IDtema'].' AND ruta = "'.$_POST['rutaVideo'].'"') > 0) ) ) {
					$msgError = 'El v&iacute;deo ya existe';
					$error = 'warning';
				}

				// Si se cambia de video:
				if ( ($_POST['rutaVideo'] != '')&&($_POST['rutaVideo'] != $_POST['rutaVideoORI'])&&($_POST['renombrarVideo'] == '') ) {
					// Comprobar que el video destino existe:
					if (!file_exists($dir.$_POST['rutaVideo'])) {
						$msgError = 'El archivo de v&iacute;deo no existe.';
						$error = 'warning';
					}

				// Si se renombra el video:
				} else if ( ($_POST['rutaVideo'] != '')&&($_POST['rutaVideo'] != $_POST['rutaVideoORI'])&&($_POST['renombrarVideo'] != '') ) {
					// Comprobar que el video destino NO existe:
					if (file_exists($dir.$_POST['rutaVideo'])) {
						$msgError = 'El archivo de v&iacute;deo ya existe.';
						$error = 'warning';

					// Comprobar que el video origen existe:
					} else if (!file_exists($dir.$_POST['rutaVideoORI'])) {
						$msgError = 'El archivo de v&iacute;deo que intenta renombrar no existe.';
						$error = 'warning';

					// Renombrarlo:
					} else {
						rename($dir.$_POST['rutaVideoORI'], $dir.$_POST['rutaVideo']);
					}
				}

				// Si se cambia de imagen:
				if ( ($_POST['img'] != '')&&($_POST['img'] != $_POST['imgORI'])&&($_POST['renombrarImg'] == '') ) {
					// Comprobar que la imagen destino existe:
					if (!file_exists($dir.'img/'.$_POST['img'])) {
						$msgError = 'El archivo de imagen para la portada no existe.';
						$error = 'warning';
					}

				// Si se renombra la imagen:
				} else if ( ($_POST['img'] != '')&&($_POST['img'] != $_POST['imgORI'])&&($_POST['renombrarImg'] != '') ) {
					// Comprobar que la imagen destino NO existe:
					if (file_exists($dir.'img/'.$_POST['img'])) {
						$msgError = 'El archivo de imagen para la portada ya existe.';
						$error = 'warning';

					// Comprobar que la imagen origen existe:
					} else if (!file_exists($dir.'img/'.$_POST['imgORI'])) {
						$msgError = 'El archivo de imagen que intenta renombrar no existe.';
						$error = 'warning';

					// Renombrarlo:
					} else {
						rename($dir.'img/'.$_POST['imgORI'], $dir.'img/'.$_POST['img']);
					}
				}

				if ($error == 'success') {
					$msgError = 'Datos actualizados correctamente';

					// Actualizar el video en la base de datos:
					updateVideo($_POST['IDvideo'], $_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['descripcion'], $_POST['rutaVideo'], $_POST['img'], $_POST['orden'], $_POST['ocultar']);
				}
			}
		}
	}
}

?>