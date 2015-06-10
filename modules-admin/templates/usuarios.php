<h1 class="page-header">Usuarios</h1>
<div class="row">
	<div class="col-12">

<?php
include_once(__DIR__.'/../../config.php');
include_once(_DOCUMENTROOT.'forms/admin-usuarios.php');

$OUT = '';

if ($msgError != '') {
	$OUT .= '<div class="alert alert-'.$error.'">'.$msgError.'</div>';
}

$OUT .= '<form role="form" method="POST" action="'._PORTALROOT.'modules-admin/templates/usuarios.php">';
	$listaUsuarios = getAllUsuarios();

	$OUT .= '<div class="form-group">';
		$OUT .= '<div class="table-responsive">';
			$OUT .= '<table class="table">';
				$OUT .= '<thead>';
					$OUT .= '<tr>';
						$OUT .= '<th>Nombre completo</th>';
						$OUT .= '<th>Email</th>';
						$OUT .= '<th></th>';
						$OUT .= '<th></th>';
					$OUT .= '</tr>';
				$OUT .= '</thead>';
				$OUT .= '<tbody>';
					foreach ($listaUsuarios as $usuario) {
						$OUT .= '<tr'.( ($usuario['bloqueado'] == 1) ? ' class="blocked-user" title="Usuario bloqueado"' : '' ).'>';
							$OUT .= '<td>'.$usuario['fullname'].'</td>';
							$OUT .= '<td>'.$usuario['email'].'</td>';
							if ($usuario['bloqueado'] == 0) {
								$OUT .= '<td><button title="Bloquear acceso a cursos" type="submit" value="'.$usuario['ID'].'" name="block-access-user" class="btn btn-xs btn-warning"><span class="glyphicon glyphicon-lock"></span></button></td>';
							} else {
								$OUT .= '<td><button title="Desbloquear acceso a cursos" type="submit" value="'.$usuario['ID'].'" name="unblock-access-user" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-lock"></span></button></td>';
							}
							$OUT .= '<td><button title="Eliminar usuarios" type="submit" value="'.$usuario['ID'].'" name="del-user" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-minus"></span></button></td>';
						$OUT .= '</tr>';
					}
					$OUT .= '<tr>';
						$OUT .= '<td><input type="text" class="form-control" name="fullname" value="'.$_POST['fullname'].'" placeholder="Nombre completo" /></td>';
						$OUT .= '<td><input type="text" class="form-control" name="email" value="'.$_POST['email'].'" placeholder="Email" /></td>';
						$OUT .= '<td><button type="submit" name="add-user" class="btn btn-xs btn-success center"><span class="glyphicon glyphicon-floppy-disk"></span></button></td>';
						$OUT .= '<td></td>';
						$OUT .= '<td></td>';
					$OUT .= '</tr>';
				$OUT .= '</tbody>';
			$OUT .= '</table>';
		$OUT .= '</div>';
	$OUT .= '</div>';
	$OUT .= '<input type="hidden" value="usuarios" name="form" />';
$OUT .= '</form>';

print($OUT);
?>
	</div>
</div>