<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;

$OUT = '';

$cursoData = getCursoData($_GET['IDcurso']);
$temaData = getTemaData($_GET['IDcurso'], $_GET['IDtema']);
$videoData = getVideoData($_GET['IDcurso'], $_GET['IDtema'], $_GET['IDvideo']);

if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
	$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
	$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
}

$OUT .= '<div class="container">';
	$OUT .= '<div class="row">';
		$OUT .= '<div class="col-md-12 margin-bottom">';
			$OUT .= '<h1>'.$cursoData['nombre'].': '.$temaData['nombre'].'</h1>';
			$OUT .= '<p>'.$temaData['descripcion'].'</p>';
		$OUT .= '</div>';
	$OUT .= '</div>';
	$OUT .= '<div class="row">';
		$OUT .= '<div class="col-md-3 hidden-xs">';
			$OUT .= '<div class="botones-visualizacion margin-bottom">';
				$OUT .= '<a href="?opt=1&IDcurso='.$cursoData['IDcurso'].'&IDtema='.$temaData['IDtema'].'&IDvideo='.$videoData['IDvideo'].'"><button class="btn btn-default';
				if ( ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 1) )||(!isset($_COOKIE['listMode'])) ) {
					$OUT .= ' active';
				}
				$OUT .= '" type="button"><span class="glyphicon glyphicon-th"></span></button></a>';
				$OUT .= '<a href="?opt=2&IDcurso='.$cursoData['IDcurso'].'&IDtema='.$temaData['IDtema'].'&IDvideo='.$videoData['IDvideo'].'"><button class="btn btn-default';
				if ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 2) ) {
					$OUT .= ' active';
				}
				$OUT .= '" type="button"><span class="glyphicon glyphicon-th-list"></span></button></a>';
			$OUT .= '</div>';

			$OUT .= '<div class="panel panel-primary listado-videos">';
				$OUT .= '<div class="panel-heading">'.$temaData['nombre'].'</div>';
				$OUT .= '<div class="panel-body">';
				// Listado del resto de vídeos del tema:
				$res = $db->query('SELECT * FROM videos WHERE IDcurso = '.$cursoData['IDcurso'].' AND IDtema = '.$temaData['IDtema'].' AND ID != '.$videoData['IDvideo'].' ORDER BY orden DESC, nombre LIMIT 5');
				while ($row = $res->fetchArray()) {
					if ( ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 1) )||(!isset($_COOKIE['listMode'])) ) {
						$OUT .= '<div class="item">';
							$OUT .= '<a class="ver-video" href="?IDcurso='.$cursoData['IDcurso'].'&IDtema='.$temaData['IDtema'].'&IDvideo='.$row['ID'].'">';
								$OUT .= '<img src="'._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/img/'.$row['img'].'" />';
							$OUT .= '</a>';
							$OUT .= '<div class="caption">';
								$OUT .= '<a href="?IDcurso='.$cursoData['IDcurso'].'&IDtema='.$temaData['IDtema'].'&IDvideo='.$row['ID'].'">'.$row['nombre'].'</a>';
							$OUT .= '</div>';
						$OUT .= '</div>';
					} else if ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 2) ) {
						$OUT .= '<div class="row"><div class="col-md-5">';
							$OUT .= '<a class="ver-video" href="?IDcurso='.$cursoData['IDcurso'].'&IDtema='.$temaData['IDtema'].'&IDvideo='.$row['ID'].'">';
								$OUT .= '<img src="'._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/img/'.$row['img'].'" />';
							$OUT .= '</a>';
						$OUT .= '</div>';
						$OUT .= '<div class="col-md-7">';
							$OUT .= '<a href="?IDcurso='.$cursoData['IDcurso'].'&IDtema='.$temaData['IDtema'].'&IDvideo='.$row['ID'].'">'.$row['nombre'].'</a>';
						$OUT .= '</div></div>';
					}
				}

				$OUT .= '<a href="?IDcurso='.$_GET['IDcurso'].'"><button class="btn btn-default pull-right"><span class="glyphicon glyphicon-chevron-left"></span> Ver m&aacute;s v&iacute;deos</button></a>';

				$OUT .= '</div>';
			$OUT .= '</div>';
		$OUT .= '</div>';

		$OUT .= '<div class="col-md-9">';
			// Detalle del vídeo seleccionado:
			$OUT .= '<h2>'.$videoData['nombre'].'</h2>';
			$OUT .= '<div class="video">';
				$OUT .= '<div class="flowplayer margin-bottom" data-swf="js/flowplayer-5.4.4/flowplayer.swf">';
					$OUT .= '<video controls preload="auto" width="100%" poster="'._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/img/'.$videoData['img'].'">';
						$OUT .= '<source src="'._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/'.$videoData['ruta'].'" type="video/mp4" />';
					$OUT .= '</video>';
				$OUT .= '</div>';
				$OUT .= '<p>'.$videoData['descripcion'].'</p>';
				$OUT .= '<p>Descargas</p>';
				$OUT .= '<ul class="list-group">';
					foreach ($videoData['adjuntos'] as $adjunto) {
						$OUT .= '<li class="list-group-item"><span class="glyphicon glyphicon-download-alt"></span><span class="badge">0</span> <a href="'._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/docs/'.$adjunto['ruta'].'" target="_blank">'.$adjunto['nombre'].'</a></li>';
					}
					$OUT .= '<li class="list-group-item"><span class="glyphicon glyphicon-download-alt"></span><span class="badge">0</span> <a download href="'._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/'.$videoData['ruta'].'" target="_blank">Descargar v&iacute;deo</a></li>';
				$OUT .= '</ul>';
			$OUT .= '</div>';
		$OUT .= '</div>';

	$OUT .= '</div>';
$OUT .= '</div>';

echo $OUT;

?>