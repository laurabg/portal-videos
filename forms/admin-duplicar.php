<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

$renombrarAdjunto = 0;
$dir = '';
$rsp = '';
$error = 'success';


//foreach ($_POST as $key => $value)
//	print "Field ".($key)." is ".($value)."<br>";

//if ( (!empty($_FILES['rutaAdjunto']))&&($_FILES['rutaAdjunto']['name'] != '')&&($_FILES['rutaAdjunto']['name'] != $_POST['rutaAdjuntoORI']) ) {
//	echo 'Hay un adjunto para subir!!! '.$_FILES['rutaAdjunto']['name'].' de '.($_FILES['rutaAdjunto']['size']/1024).' kb<br />';
//}
if ($_POST['form'] == 'duplicar') {
	if ($_POST['btn-dup'] == 'duplicar-todo') {
		$rsp = 'Duplicando todo el contenido de '.$_POST['opt'];
		$error = 'warning';
	} else if ($_POST['btn-dup'] == 'duplicar-solo-reg') {
		if ($_POST['opt'] == 'cursos') {
			$cursoData = getCursoData($_POST['IDcurso']);

			$ordenCurso = getNextOrdenCurso();
			$nuevoNombreCurso = $cursoData['nombre'].'-copy';
			
			if (checkCurso('nombre = "'.$nuevoNombreCurso.'"') > 0) {
				$rsp = 'Ya hay una copia de este curso';
				$error = 'warning';
			} else {
				$rutalimpia = clean($nuevoNombreCurso);

				createOrRenameCursoDir($cursoData['ubicacion'], $rutalimpia, '');
				
				// Crear el curso en la base de datos:	
				createCurso($nuevoNombreCurso, $cursoData['descripcion'], $rutalimpia, $cursoData['ubicacion'], $ordenCurso, $cursoData['ocultar'], '', $cursoData['fechaIni'], $cursoData['fechaFin'], $cursoData['publico']);

				$_POST['IDcurso'] = getIDcurso($nuevoNombreCurso, $rutalimpia, $cursoData['ubicacion'], 0);
			}
		} else if ($_POST['opt'] == 'temas') {
			$cursoData = getCursoData($_POST['IDcurso']);
			$temaData = getTemaData($_POST['IDcurso'], $_POST['IDtema']);

			$ordenTema = getNextOrdenTema($_POST['IDcurso']);
			$nuevoNombreTema = $temaData['nombre'].'-copy';

			if (checkTema('nombre = "'.$nuevoNombreTema.'" AND IDcurso = '.decrypt($_POST['IDcurso'])) > 0) {
				$rsp = 'Ya hay una copia de este tema';
				$error = 'warning';
			} else {
				$rutalimpia = clean($nuevoNombreTema);
				
				createOrRenameTemaDir($cursoData['ubicacion'], $cursoData['ruta'], $rutalimpia, '');
				
				// Crear el tema en la base de datos:
				createTema($_POST['IDcurso'], $nuevoNombreTema, $temaData['descripcion'], $rutalimpia, $ordenTema, $temaData['ocultar']);
				
				$_POST['IDtema'] = getIDtema($_POST['IDcurso'], $nuevoNombreTema, $rutalimpia, 0);
			}
		} else if ($_POST['opt'] == 'videos') {
			/*$cursoData = getCursoData($_POST['IDcurso']);
			$temaData = getTemaData($_POST['IDcurso'], $_POST['IDtema']);
			$videoData = getVideoData($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo']);

			$ordenVideo = getNextOrdenVideo($_POST['IDcurso'], $_POST['IDtema']);
			$nuevoNombreVideo = $videoData['nombre'].'-copy';

			if (checkVideo('nombre = "'.$nuevoNombreVideo.'" AND IDcurso = '.decrypt($_POST['IDcurso']).' AND IDtema = '.decrypt($_POST['IDtema'])) > 0) {
				$rsp = 'Ya hay una copia de este video';
				$error = 'warning';
			} else {
				$rutalimpia = clean($nuevoNombreVideo);
				
				createOrRenameVideoDir($cursoData['ubicacion'], $cursoData['ruta'], $temaData['ruta'], $rutalimpia, '');
				
				// Crear el tema en la base de datos:
				createTema($_POST['IDcurso'], $nuevoNombreTema, $temaData['descripcion'], $rutalimpia, $ordenTema, $temaData['ocultar']);
				
				$_POST['IDtema'] = getIDtema($_POST['IDcurso'], $nuevoNombreTema, $rutalimpia, 0);
			}*/
		} else {
			$rsp = 'Duplicando solo el registro de '.$_POST['opt'];
			$error = 'warning';
		}
	} else if ($_POST['btn-dup'] == 'cancel') {
		$error = 'warning';
		$rsp = '';
	} else {
		$rsp = 'No se conoce la opci&oacute;n '.$_POST['btn-dup'];
		$error = 'danger';
	}

	if ($error == 'success') {
		$rsp = '?opt='.$_POST['opt'].'&IDcurso='.$_POST['IDcurso'].'&IDtema='.$_POST['IDtema'].'&IDvideo='.$_POST['IDvideo'].'&IDadjunto='.$_POST['IDadjunto'];
	}
}

echo $rsp;
?>