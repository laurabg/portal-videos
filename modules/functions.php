<?php

//dbCreate(_BBDD);

function getCabecera($nombre, $desc, $showButtons, $IDcurso, $IDtema) {
	$OUT = '';

	$OUT .= '<div class="jumbotron">';
		$OUT .= '<div class="container hidden-xs">';
			$OUT .= '<h1>'.$nombre.'</h1>';
			$OUT .= '<p class="lead">'.$desc.'</p>';
			if ($showButtons == 1) {
				$OUT .= '<div class="btn-group pull-right">';
					$OUT .= '<a class="btn btn-default" href="'._PORTALROOT.'">Volver al listado de cursos</a>';
					if ($IDcurso != '') {
						$OUT .= '<a class="btn btn-default" href="?IDcurso='.$IDcurso.'">Volver al curso</a>';
					}
					if ($IDtema != '') {
						$OUT .= '<a class="btn btn-default" href="?IDcurso='.$IDcurso.'&IDtema='.$IDtema.'">Volver al tema</a>';
					}
				$OUT .= '</div>';
			}
		$OUT .= '</div>';
	$OUT .= '</div>';

	echo $OUT;
}


?>