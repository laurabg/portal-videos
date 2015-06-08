<!--h1 class="page-header">Configuración</h1>
<div class="row placeholders">
	<div class="col-xs-6 col-sm-3 placeholder">
		<img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		<h4>Label</h4>
		<span class="text-muted">Something else</span>
	</div>
	<div class="col-xs-6 col-sm-3 placeholder">
		<img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
		<h4>Label</h4>
		<span class="text-muted">Something else</span>
	</div>
	<div class="col-xs-6 col-sm-3 placeholder">
		<img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		<h4>Label</h4>
		<span class="text-muted">Something else</span>
	</div>
	<div class="col-xs-6 col-sm-3 placeholder">
		<img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
		<h4>Label</h4>
		<span class="text-muted">Something else</span>
	</div>
</div-->

<h1 class="page-header">Configuración</h1>
<div class="row">
	<div class="col-12">

<?php
include_once(__DIR__.'/../../config.php');
include_once(_DOCUMENTROOT.'forms/adminConfig.php');

//$listaUbicaciones = listaUbicaciones(1);

$configData = getConfigData();

$_POST['listaUbicaciones'] = $configData['listaUbicaciones'];
$_POST['listaExtensiones'] = $configData['listaExtensiones'];
$_POST['showErrors'] = $configData['showErrors'];
$_POST['_MOODLEURL'] = $configData['_MOODLEURL'];
$_POST['_WSTOKEN'] = $configData['_WSTOKEN'];

$OUT = '';

if ($error != '') {
	$OUT .= '<div class="alert alert-'.$error.'">'.$msgError.'</div>';
}

$OUT .= '<form name="'.$_GET['opt'].'" role="form" method="POST" action="">';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<label></label><input name="showErrors" type="checkbox"';
		if ($_POST['showErrors'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Mostrar errores de PHP</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="_MOODLEURL">URL de Moodle:</label>';
		$OUT .= '<input type="text" name="_MOODLEURL" class="form-control" id="_MOODLEURL" placeholder="URL de Moodle" value="'.$_POST['_MOODLEURL'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="_WSTOKEN">Token para servicio web de Moodle:</label>';
		$OUT .= '<input type="text" name="_WSTOKEN" class="form-control" id="_WSTOKEN" placeholder="Token para servicio web de Moodle" value="'.$_POST['_WSTOKEN'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group ubicaciones">';
		$OUT .= '<label for="ubicaciones">Listado de ubicaciones: </label>';
		$OUT .= '<div class="listado listaUbicaciones">';
		foreach ($_POST['listaUbicaciones'] as $ub) {
			$OUT .= '<div class="row">';
				$OUT .= '<div class="col-md-2">';
					$OUT .= '<input class="check-delete" type="checkbox" name="del-ubicacion[]" value="'.$ub['ID'].'" /> Eliminar';
				$OUT .= '</div>';
				$OUT .= '<div class="col-md-10">';
					$OUT .= '<input type="text" class="form-control" name="ubicacion['.$ub['ID'].']" id="ubicacion" value="'.$ub['ruta'].'" />';
				$OUT .= '</div>';
			$OUT .= '</div>';
		}
		$OUT .= '</div>';
		$OUT .= '<button type="button" class="add-ub btn btn-success">Añadir una ubicaci&oacute;n</button>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group ubicaciones">';
		$OUT .= '<label for="ubicaciones">Listado de extensiones v&aacute;lidas: </label>';
		$OUT .= '<div class="listado listaExtensiones">';
		foreach ($_POST['listaExtensiones'] as $ext) {
			$OUT .= '<div class="row">';
				$OUT .= '<div class="col-md-2">';
					$OUT .= '<input class="check-delete" type="checkbox" name="del-extension[]" value="'.$ext['ID'].'" /> Eliminar';
				$OUT .= '</div>';
				$OUT .= '<div class="col-md-10">';
					$OUT .= '<input type="text" class="form-control" name="extension['.$ext['ID'].']" id="extension" value="'.$ext['nombre'].'" />';
				$OUT .= '</div>';
			$OUT .= '</div>';
		}
		$OUT .= '</div>';
		$OUT .= '<button type="button" class="add-ext btn btn-success">Añadir una extensi&oacute;n</button>';
	$OUT .= '</div>';
	$OUT .= '<button type="submit" value="save" name="formOption" class="btn btn-default">Guardar</button>';
	$OUT .= '<button type="button" value="cancel" class="btn btn-default btn-cancel">Cancelar</button>';
	if ($_POST['IDcurso'] != '') {
		$OUT .= '<button type="submit" value="del" name="formOption" class="btn btn-danger">Eliminar</button>';
	}
	$OUT .= '<input type="hidden" value="'.$_GET['opt'].'" name="form" />';
$OUT .= '</form>';

print($OUT);
?>


	</div>
</div>