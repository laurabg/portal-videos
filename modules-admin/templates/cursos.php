<h1 class="page-header">Cursos</h1>
<div class="row">
	<div class="col-12">

<?php
include_once(__DIR__.'/../../config.php');
include_once(_DOCUMENTROOT.'forms/adminCursos.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');

$listaCursosMoodle = connect('core_course_get_courses', '');

// Si se ha eliminado el curso, borrar sus datos:
if ($error == 'danger') {
	$_POST['IDcurso'] = '';
	$_POST['IDcursoMoodle'] = '';
	$_POST['nombreCurso'] = '';
	$_POST['rutaCurso'] = '';
	$_POST['ubicacion'] = '';
	$_POST['descripcion'] = '';
	$_POST['fechaIni'] = '';
	$_POST['fechaFin'] = '';
	$_POST['publico'] = '';
// Si estamos viendo un curso, pero no se ha enviado el formulario, mostrar sus datos:
} else if ( ($_GET['IDcurso'] != '')&&($_POST['form'] == '') ) {
	$cursoData = getCursoData($_GET['IDcurso']);
	$_POST['IDcurso'] = $_GET['IDcurso'];
	$_POST['IDcursoMoodle'] = $cursoData['IDcursoMoodle'];
	$_POST['nombreCurso'] = $cursoData['nombre'];
	$_POST['rutaCurso'] = $cursoData['ruta'];
	$_POST['ubicacion'] = $cursoData['ubicacion'];
	$_POST['descripcion'] = $cursoData['descripcion'];
	$_POST['fechaIni'] = $cursoData['fechaIni'];
	$_POST['fechaFin'] = $cursoData['fechaFin'];
	$_POST['publico'] = $cursoData['publico'];
}
$OUT = '';

if ($msgError != '') {
	$OUT .= '<div class="alert alert-'.$error.'">'.$msgError.'</div>';
}

$OUT .= '<form role="form" method="POST" action="">';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="nombreCurso">* Nombre del curso:</label>';
		$OUT .= '<input required type="text" name="nombreCurso" class="form-control" id="nombreCurso" placeholder="Nombre del curso" value="'.$_POST['nombreCurso'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="IDcursoMoodle">* Seleccione el curso de Moodle asociado:</label>';
		$OUT .= '<select class="form-control" name="IDcursoMoodle" id="IDcursoMoodle" >';
			$OUT .= '<option value="">Seleccione un curso</option>';
			if (sizeof($listaCursosMoodle) > 0) {
				foreach ($listaCursosMoodle as $c) {
					if ($c->categoryid > 0) {
						$OUT .= '<option value="'.$c->id.'"';
						if ($_POST['IDcursoMoodle'] == $c->id) {
							$OUT .= ' selected';
						}
						$OUT .= '>'.$c->fullname.'</option>';
					}
				}
			}
		$OUT .= '</select>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="ubicacion">* Seleccione una ubicaci&oacute;n:</label>';
		$OUT .= '<select class="form-control" name="ubicacion" id="ubicacion" >';
			$OUT .= '<option value="">Seleccione una ubicaci&oacute;n </option>';
			if (sizeof($listaDirs) > 0) {
				foreach ($listaDirs as $ub) {
					$OUT .= '<option value="'.$ub['ID'].'"';
					if ($_POST['ubicacion'] == $ub['ID']) {
						$OUT .= ' selected';
					}
					$OUT .= '>'.$ub['ruta'].'</option>';
				}
			}
		$OUT .= '</select>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<div class="row">';
			$OUT .= '<div class="col-md-6">';
				$OUT .= '<label for="fechaIni">Fecha a partir de la que mostrar el curso:</label>';
			$OUT .= '</div>';
			$OUT .= '<div class="col-md-6">';
				$OUT .= '<label for="fechaIni">Fecha a partir de la que dejar de mostrar el curso:</label>';
			$OUT .= '</div>';
		$OUT .= '</div>';
		$OUT .= '<div class="row">';
			$OUT .= '<div class="col-md-6">';
				$OUT .= '<input type="text" class="form-control datepicker" value="'.$_POST['fechaIni'].'" name="fechaIni" id="fechaIni" />';
			$OUT .= '</div>';
			$OUT .= '<div class="col-md-6">';
				$OUT .= '<input type="text" class="form-control datepicker" value="'.$_POST['fechaFin'].'" name="fechaFin" id="fechaFin" />';
			$OUT .= '</div>';
		$OUT .= '</div>';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<label></label><input name="publico" type="checkbox"';
		if ($_POST['publico'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Curso público (visible para usuarios no conectados)</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="descripcion">Descripción del curso:</label>';
		$OUT .= '<textarea class="form-control" name="descripcion" rows="3">'.$_POST['descripcion'].'</textarea>';
	$OUT .= '</div>';
	$OUT .= '<button type="submit" value="save" name="formOption" class="btn btn-default">Guardar</button>';
	if ($_POST['IDcurso'] != '') {
		$OUT .= '<button type="submit" value="del" name="formOption" class="btn btn-danger">Eliminar</button>';
	}
	$OUT .= '<input type="hidden" value="'.$_GET['opt'].'" name="form" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDcurso'].'" name="IDcurso" />';
	$OUT .= '<input type="hidden" value="'.$_POST['rutaCurso'].'" name="rutaCursoORI" />';
	$OUT .= '<input type="hidden" value="'.$_POST['ubicacion'].'" name="ubicacionORI" />';
	$OUT .= '<input type="hidden" value="'.$_POST['IDcursoMoodle'].'" name="IDcursoMoodleORI" />';
$OUT .= '</form>';

print($OUT);
?>
	</div>
</div>