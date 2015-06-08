<h1 class="page-header">Vídeos</h1>
<div class="row">
	<div class="col-12">

<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'forms/adminVideos.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');

if ($_GET['IDcurso'] != '') {
	$_POST['IDcurso'] = $_GET['IDcurso'];
}
if ($_GET['IDtema'] != '') {
	$_POST['IDtema'] = $_GET['IDtema'];
}
// Si estamos viendo un curso, pero no se ha enviado el formulario, mostrar sus datos:
if ( ($_GET['IDcurso'] != '')&&($_GET['IDtema'] != '')&&($_GET['IDvideo'] != '')&&($_POST['form'] == '') ) {
	$videoData = getVideoData($_GET['IDvideo'], $_GET['IDtema'], $_GET['IDcurso']);
	$_POST['IDvideo'] = $_GET['IDvideo'];
	$_POST['nombreVideo'] = $videoData['nombre'];
	$_POST['descripcion'] = $videoData['descripcion'];
}

$OUT = '';

if ($error != '') {
	$OUT .= '<div class="alert alert-'.$error.'">'.$msgError.'</div>';
}

$OUT .= '<form role="form" method="POST" action="">';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="nombreVideo">* Título del vídeo:</label>';
		$OUT .= '<input required type="text" name="nombreVideo" class="form-control" id="nombreVideo" placeholder="Título del vídeo" value="'.$_POST['nombreVideo'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="descripcion">Descripción del vídeo:</label>';
		$OUT .= '<textarea class="form-control" name="descripcion" rows="3">'.$_POST['descripcion'].'</textarea>';
	$OUT .= '</div>';
	$OUT .= '<button type="submit" class="btn btn-default">Guardar</button>';
	$OUT .= '<input type="hidden" value="'.$_GET['opt'].'" name="form" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDcurso'].'" name="IDcurso" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDtema'].'" name="IDtema" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDvideo'].'" name="IDvideo" />';
$OUT .= '</form>';

print($OUT);
?>
	</div>
</div>