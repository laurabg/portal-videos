<h1 class="page-header">Vídeos</h1>
<div class="row">
	<div class="col-12">

<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'forms/admin-videos.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

if (!isset($_POST['IDcurso'])) {
	$_POST['IDcurso'] = ( ($_GET['IDcurso'] != '') ? $_GET['IDcurso'] : '' );
}
if (!isset($_POST['IDtema'])) {
	$_POST['IDtema'] = ( ($_GET['IDtema'] != '') ? $_GET['IDtema'] : '' );
}
if (!isset($_POST['IDvideo'])) {
	$_POST['IDvideo'] = ( ($_GET['IDvideo'] != '') ? $_GET['IDvideo'] : '' );
}

$dir = '';

// Si se ha eliminado el video, borrar sus datos:
if ($error == 'danger') {
	$_POST['IDvideo'] = '';
	$_POST['nombreVideo'] = '';
	$_POST['rutaVideo'] = '';
	$_POST['descripcion'] = '';
	$_POST['img'] = '';
	$_POST['fechaCaducidad'] = '';
	$_POST['orden'] = '';
	$_POST['ocultar'] = '';

// Si estamos viendo un curso, pero no se ha enviado el formulario, mostrar sus datos:
} else if ( ($_POST['IDcurso'] != '')&&($_POST['IDtema'] != '')&&($_POST['IDvideo'] != '') ) {
	$cursoData = getCursoData($_POST['IDcurso']);
	$temaData = getTemaData($_POST['IDcurso'], $_POST['IDtema']);
	$videoData = getVideoData($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo']);

	if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
		$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
		$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
	}

	$_POST['nombreVideo'] = $videoData['nombre'];
	$_POST['descripcion'] = $videoData['descripcion'];
	$_POST['rutaVideo'] = $videoData['ruta'];
	$_POST['img'] = $videoData['img'];
	$_POST['fechaCaducidad'] = $videoData['fechaCaducidad'];
	$_POST['orden'] = $videoData['orden'];
	$_POST['ocultar'] = $videoData['ocultar'];

}

$_POST['orden'] = ( $_POST['orden']=='' ? getNextOrdenVideo($_POST['IDcurso'], $_POST['IDtema']) : $_POST['orden'] );

$OUT = '';

if ($msgError != '') {
	$OUT .= '<div class="alert alert-'.$error.'">'.$msgError.'</div>';
}

$OUT .= '<form name="videos" role="form" method="POST" action="'._PORTALROOT.'modules-admin/videos.php" enctype="multipart/form-data">';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="nombreVideo">* Título del vídeo:</label>';
		$OUT .= '<input required type="text" name="nombreVideo" class="form-control" id="nombreVideo" placeholder="Título del vídeo" value="'.$_POST['nombreVideo'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="ocultar" type="checkbox"';
		if ($_POST['ocultar'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> V&iacute;deo oculto, no se mostrar&aacute; a ning&uacute;n usuario</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="orden">* Posici&oacute;n en la que se mostrar&aacute; el v&iacute;deo:</label>';
		$OUT .= '<input required type="number" name="orden" class="form-control" id="orden" placeholder="Posici&oacute;n en la que se mostrar&aacute; el v&iacute;deo" value="'.$_POST['orden'].'" min="1" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="descripcion">Descripción del vídeo:</label>';
		$OUT .= '<textarea class="form-control" name="descripcion" rows="3">'.$_POST['descripcion'].'</textarea>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group input-group input-daterange">';
		$OUT .= '<label for="fechaCaducidad">Fecha en la que dejar de mostrar el v&iacute;deo:</label>';
		$OUT .= '<input type="text" class="form-control" name="fechaCaducidad" value="'.$_POST['fechaCaducidad'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		if (_ALLOWVIDEOUPLOAD == 1) {
			$OUT .= '<label for="rutaVideo" class="control-label">* Archivo de v&iacute;deo:</label> <span class="obtenerCaptura"><input type="checkbox" name="obtenerCaptura" /> Obtener portada del v&iacute;deo autom&aacute;ticamente</span>';
			$OUT .= '<input id="rutaVideo" name="rutaVideo" type="file" accept="video/*" placeholder="Nombre del archivo de v&iacute;deo" data-preview-file-type="text" class="file-loadinga" value="'._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/'.$_POST['rutaVideo'].'" />';
		} else {
			$OUT .= '<label for="rutaVideo">* Nombre del archivo de v&iacute;deo:</label>'.( ($_POST['IDvideo'] != '') ? ' <input class="checkbox-con-margen" type="checkbox" name="renombrarVideo" /> Marcar para renombrar' : '' );
			$OUT .= '<input required type="text" name="rutaVideo" class="form-control" id="rutaVideo-noUpload" placeholder="Nombre del archivo de v&iacute;deo" value="'.$_POST['rutaVideo'].'" />';
		}
	$OUT .= '</div>';
	if ($_POST['IDvideo'] != '') {
		$OUT .= '<div class="form-group">';
			if (_ALLOWIMGUPLOAD == 1) {
				$OUT .= '<label for="img" class="control-label">* Imagen de portada del v&iacute;deo:</label>';
				$OUT .= '<input id="img" name="img" type="file" accept="*" placeholder="Imagen de portada del v&iacute;deo" data-preview-file-type="text" class="file-loadinga" value="'._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/img/'.$_POST['img'].'" />';
			} else {
				$OUT .= '<label for="img">* Imagen de portada del v&iacute;deo:</label>'.( ($_POST['IDvideo'] != '') ? ' <input class="checkbox-con-margen" type="checkbox" name="renombrarImg" /> Marcar para renombrar' : '' );
				$OUT .= '<div class="clearfix"></div>';
				$OUT .= '<img src="'._PORTALROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/img/'.$_POST['img'].'" alt="" class="img-thumbnail img-responsive file-preview-image" />';
				$OUT .= '<input required type="text" name="img" class="form-control" id="img-noUpload" placeholder="Nombre del archivo de imagen" value="'.$_POST['img'].'" />';
			}
		$OUT .= '</div>';
	}
	$OUT .= '<button type="submit" class="btn btn-default">Guardar</button>';
	if ($_POST['IDvideo'] != '') {
		$OUT .= '<button type="submit" value="del" name="formDel" class="btn btn-danger">Eliminar</button>';
	}
	$OUT .= '<input type="hidden" value="videos" name="form" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDcurso'].'" name="IDcurso" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDtema'].'" name="IDtema" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDvideo'].'" name="IDvideo" />';
	$OUT .= '<input type="hidden" value="'.$_POST['rutaVideo'].'" name="rutaVideoORI" />';
	$OUT .= '<input type="hidden" value="'.$_POST['img'].'" name="imgORI" />';
$OUT .= '</form>';

print($OUT);
?>
	</div>
</div>