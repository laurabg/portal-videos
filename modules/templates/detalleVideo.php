<?php

global $db;

$OUT = '';

// Mostrar la cabecera del curso, con su nombre y descripción:
$res = $db->query('SELECT nombre, descripcion FROM temas WHERE IDcurso = '.$IDcurso.' AND ID = '.$IDtema);
while ($row = $res->fetchArray()) {
	$OUT .= getCabecera($row['nombre'], $row['descripcion'], 1, $IDcurso, $IDtema);
}
$res = null;

$OUT .= '<div class="container">';
	$OUT .= '<div class="row">';
		$OUT .= '<div class="col-md-3 hidden-xs">';

		// Listado del resto de vídeos del tema:
		$res = $db->query('SELECT * FROM videos WHERE IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema.' AND ID != '.$IDvideo);
		while ($row = $res->fetchArray()) {
			$OUT .= '<div class="thumbnail">';
				$OUT .= '<a class="ver-video" href="?IDcurso='.$IDcurso.'&IDtema='.$IDtema.'&IDvideo='.$row['ID'].'"><span class="glyphicon glyphicon-play play-video"></span>';
				$OUT .= '<img src="'._DIRCURSOS.$row['img'].'" /></a>';
				$OUT .= '<div class="caption">';
					$OUT .= '<a href="?IDcurso='.$IDcurso.'&IDtema='.$IDtema.'&IDvideo='.$row['ID'].'"><h3>'.$row['nombre'].'</h3></a>';
				$OUT .= '</div>';
			$OUT .= '</div>';
		}

		$OUT .= '</div>';
		$OUT .= '<div class="col-md-9">';

		// Detalle del vídeo seleccionado:
		$res = $db->query('SELECT * FROM videos WHERE IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema.' AND ID = '.$IDvideo);
		while ($row = $res->fetchArray()) {
			$OUT .= '<div class="video">';
				$OUT .= '<div class="flowplayer" data-swf="js/flowplayer-5.4.4/flowplayer.swf">';
					$OUT .= '<video controls preload="auto" width="100%" poster="'._DIRCURSOS.$row['img'].'">';
						$OUT .= '<source src="'._DIRCURSOS.$row['ruta'].'" type="video/mp4" />';
					$OUT .= '</video>';
				$OUT .= '</div>';
				$OUT .= '<div class="thumbnail">';
					$OUT .= '<div class="caption">';
						$OUT .= '<h3>'.$row['nombre'].'</h3>';
						$OUT .= '<p>'.$row['descripcion'].'</p>';
					$OUT .= '</div>';
				$OUT .= '</div>';
			$OUT .= '</div>';
		}

		$OUT .= '</div>';

	$OUT .= '</div>';
$OUT .= '</div>';

echo $OUT;

?>