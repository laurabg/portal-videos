<?php
global $db;

$OUT = '';
$cont = 0;

$OUT .= getCabecera('Portal de vídeos','Selecciona el curso que desea ver.', 0);

$OUT .= '<div class="container">';
	$OUT .= '<div class="panel panel-primary">';
		$OUT .= '<div class="panel-heading">Cursos disponibles</div>';
		$OUT .= '<div class="panel-body">';
			$OUT .= '<div class="row">';
			
			$res = $db->query('SELECT * FROM cursos');
			while ($row = $res->fetchArray()) {
				if ($cont % 3 == 0) {
					$OUT .= '</div><div class="row">';
				} 
				$OUT .= '<div class="col-sm-6 col-md-4">';
					$OUT .= '<div class="thumbnail">';
						$OUT .= '<div class="caption">';
							$OUT .= '<h3>'.$row['nombre'].'</h3>';
							$OUT .= '<p>'.$row['descripcion'].'</p>';
							$OUT .= '<p><a href="?IDcurso='.$row['ID'].'" class="btn btn-primary" role="button">Acceder al curso</a></p>';
						$OUT .= '</div>';
					$OUT .= '</div>';
				$OUT .= '</div>';
				
				$cont++;
			}
			$OUT .= '</div>';
		$OUT .= '</div>';
	$OUT .= '</div>';
$OUT .= '</div>';

echo $OUT;
?>