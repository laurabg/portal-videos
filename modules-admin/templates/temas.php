<h1 class="page-header">Temas</h1>
<div class="row">
	<div class="col-12">

<?php
include_once('../config.php');
include_once(_DOCUMENTROOT.'forms/process.php');
include_once(_DOCUMENTROOT.'ws/connection.php');

if ($_GET['IDcurso'] != '') {
	$_POST['IDcurso'] = $_GET['IDcurso'];
}
// Si estamos viendo un curso, pero no se ha enviado el formulario, mostrar sus datos:
if ( ($_GET['IDcurso'] != '')&&($_GET['IDtema'] != '')&&($_POST['form'] == '') ) {
	$temaData = getTemaData($_GET['IDtema'], $_GET['IDcurso']);
	$_POST['IDtema'] = $_GET['IDtema'];
	$_POST['nombreTema'] = $temaData['nombre'];
	$_POST['descripcion'] = $temaData['descripcion'];
}

$OUT = '';

if ( ($error == '')&&($ok == 1) ) {
	$OUT .= '<div class="alert alert-success">Datos guardados correctamente</div>';
} else if ( ($error == '')&&($ok == 2) ) {
	$OUT .= '<div class="alert alert-success">Datos actualizados correctamente</div>';
} else if ($error != '') {
	$OUT .= '<div class="alert alert-danger">'.$error.'</div>';
}

$OUT .= '<form role="form" method="POST" action="">';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="nombreTema">* Nombre del tema:</label>';
		$OUT .= '<input required type="text" name="nombreTema" class="form-control" id="nombreTema" placeholder="Nombre del tema" value="'.$_POST['nombreTema'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="descripcion">Descripción del tema:</label>';
		$OUT .= '<textarea class="form-control" name="descripcion" rows="3">'.$_POST['descripcion'].'</textarea>';
	$OUT .= '</div>';
	$OUT .= '<button type="submit" class="btn btn-default">Guardar</button>';
	$OUT .= '<input type="hidden" value="'.$_GET['opt'].'" name="form" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDcurso'].'" name="IDcurso" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDtema'].'" name="IDtema" />';
$OUT .= '</form>';

print($OUT);
?>
	</div>
</div>