<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;

$OUT = '';
$found = 0;

$SQL = 'SELECT * FROM cursos WHERE publico = 1';

if ( (isset($_COOKIE['MoodleUserSession']))&&(decrypt($_COOKIE['MoodleUserSession'],1)['esAdmin'] == 1) ) {
	$SQL .= ' OR (IDcursoMoodle IS NOT NULL AND IDcursoMoodle != "")';
} else if ( (isset($_COOKIE['MoodleUserSession']))&&(!isset($_COOKIE['MoodleUserFaltaCorreo'])) ) {
	$SQL .= ' OR IDcursoMoodle IN (SELECT IDcursoMoodle FROM cursosUsuarios WHERE IDusuario = '.decrypt($_COOKIE['MoodleUserSession'],1)['IDusuario'].')';
}

$SQL .= ' ORDER BY orden, nombre';

$OUT .= '<div class="container">';
	$OUT .= '<div class="row">';
		$OUT .= '<div class="col-md-12 margin-bottom">';
			$OUT .= '<h1>Portal v&iacute;deos</h1>';
			$OUT .= '<p>Bienvenido al portal de v&iacute;deos. Aqu&iacute; podr&aacute; ver los cursos en los que est&aacute; matriculado.</p>';
		$OUT .= '</div>';

		// Listar los cursos
		$res = $db->query($SQL);
		while ($row = $res->fetchArray()) {
			$found = 1;
			if (is_int(array_search($row['ubicacion'], array_column($listaDirs, 'ID')))) {
				$dir = $listaDirs[array_search($row['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
				$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
			}

			$OUT .= '<div class="col-md-12">';
				$OUT .= '<div class="panel panel-primary">';
					$OUT .= '<div class="panel-heading">Novedades en: <a href="?IDcurso='.urlencode($row['IDencriptado']).'"><b>'.$row['nombre'].'</b></a></div>';
					$OUT .= '<div class="panel-body">';
						$OUT .= '<div class="row">';
							// Listar videos del tema:
							$resVideo = $db->query('SELECT a.IDencriptado AS IDvideo, a.nombre AS nombreVideo, a.img, a.descripcion AS descVideo, a.ruta AS rutaVideo, b.IDencriptado AS IDtema, b.nombre AS nombreTema, b.ruta AS rutaTema FROM videos a, temas b WHERE a.IDtema = b.ID AND a.IDcurso = '.$row['ID'].' ORDER BY b.orden DESC, b.nombre, a.orden DESC, a.nombre LIMIT 4');
							while ($rowVideo = $resVideo->fetchArray()) {
								$OUT .= '<div class="col-sm-6 col-md-3">';
									$OUT .= '<a class="ver-video" href="?IDcurso='.urlencode($row['IDencriptado']).'&IDtema='.urlencode($rowVideo['IDtema']).'&IDvideo='.urlencode($rowVideo['IDvideo']).'">';
										//$OUT .= '<span class="glyphicon glyphicon-play play-video"></span>';
										$OUT .= '<img src="'._DIRCURSOS.$dir.$row['ruta'].'/'.$rowVideo['rutaTema'].'/img/'.$rowVideo['img'].'" />';
										$OUT .= '<p>'.$rowVideo['nombreTema'].': '.$rowVideo['nombreVideo'].'</p>';
									$OUT .= '</a>';
								$OUT .= '</div>';
							}
						$OUT .= '</div>';
						$OUT .= '<p><a href="?IDcurso='.urlencode($row['IDencriptado']).'" class="btn btn-default pull-right" role="button">Ver curso completo</a></p>';
					$OUT .= '</div>';
				$OUT .= '</div>';
			$OUT .= '</div>';
		}

		if ($found == 0) {
			$OUT .= '<div class="col-md-12">';
				$OUT .= '<p>En estos momentos no hay cursos publicados.</p>';
			$OUT .= '</div>';
		}
		
	$OUT .= '</div>';
$OUT .= '</div>';

echo $OUT;

?>