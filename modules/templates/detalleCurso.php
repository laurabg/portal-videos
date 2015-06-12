<?php

include_once(__DIR__.'/../../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;

$OUT = '';
$numPag = 0;
$cont = 0;

$cursoData = getCursoData($_GET['IDcurso']);

if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
	$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
	$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
}

$OUT .= '<div class="container">';
	$OUT .= '<div class="row">';
		$OUT .= '<div class="col-md-12 margin-bottom">';
			$OUT .= '<h1>'.$cursoData['nombre'].'</h1>';
			$OUT .= '<p>'.$cursoData['descripcion'].'</p>';
		$OUT .= '</div>';
	$OUT .= '</div>';
$OUT .= '</div>';

$OUT .= '<div class="container botones-visualizacion margin-bottom">';
	$OUT .= '<div class="row">';
		$OUT .= '<div class="col-md-3 pull-right">';
			$OUT .= '<a href="?opt=1&IDcurso='.$cursoData['IDcurso'].'"><button class="btn btn-default pull-right';
			if ( ( (isset($_GET['opt']))&&($_GET['opt'] == 1) )||(!isset($_GET['opt'])) ) {
				$OUT .= ' active';
			}
			$OUT .= '" type="button"><span class="glyphicon glyphicon-th"></span></button></a>';
			$OUT .= '<a href="?opt=2&IDcurso='.$cursoData['IDcurso'].'"><button class="btn btn-default pull-right';
			if ( (isset($_GET['opt']))&&($_GET['opt'] == 2) ) {
				$OUT .= ' active';
			}
			$OUT .= '" type="button"><span class="glyphicon glyphicon-th-list"></span></button></a>';
		$OUT .= '</div>';
		$OUT .= '<div class="col-md-3">';
			$OUT .= '<select class="form-control"><option value="DESC" selected>Orden descendente</option><option value="ASC">Orden ascendente</option></select>';
		$OUT .= '</div>';
		$OUT .= '<div class="col-md-6"></div>';
	$OUT .= '</div>';
$OUT .= '</div>';

$OUT .= '<div class="container listado-paginado">';
	$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.$cursoData['IDcurso'].' AND ID IN (SELECT IDtema FROM videos) ORDER BY orden DESC, nombre');
	while ($row = $res->fetchArray()) {
		if ($cont % 3 == 0) {
			$numPag++;
			if ($cont > 0) {
				$OUT .= '</div>';
			}
			$OUT .= '<div id="p'.$numPag.'" class="page'.( ($numPag == 1) ? ' _current' : '').'">';
		}
		
		$OUT .= '<div class="panel panel-primary">';
			$OUT .= '<div class="panel-heading">'.$row['nombre'].'</div>';
			$OUT .= '<div class="panel-body">';
			// Listar videos de cada tema:
			$resVideo = $db->query('SELECT * FROM videos WHERE IDtema = '.$row['ID'].' AND IDcurso = '.$cursoData['IDcurso'].' ORDER BY orden DESC, nombre');
			while ($rowVideo = $resVideo->fetchArray()) {
				if ( ( (isset($_GET['opt']))&&($_GET['opt'] == 1) )||(!isset($_GET['opt'])) ) {
					$OUT .= '<div class="col-sm-6 col-md-3 video-col">';
						$OUT .= '<a class="ver-video" href="?IDcurso='.$cursoData['IDcurso'].'&IDtema='.$row['ID'].'&IDvideo='.$rowVideo['ID'].'">';
							$OUT .= '<img src="'._PORTALROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$row['ruta'].'/img/'.$rowVideo['img'].'" />';
							$OUT .= '<span>'.$rowVideo['nombre'].'</span>';
						$OUT .= '</a>';
					$OUT .= '</div>';
				} else if ( (isset($_GET['opt']))&&($_GET['opt'] == 2) ) {
					$OUT .= '<div class="row"><div class="col-md-3">';
						$OUT .= '<a class="ver-video" href="?IDcurso='.$cursoData['IDcurso'].'&IDtema='.$row['ID'].'&IDvideo='.$rowVideo['ID'].'">';
							$OUT .= '<img src="'._PORTALROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$row['ruta'].'/img/'.$rowVideo['img'].'" />';
						$OUT .= '</a>';
					$OUT .= '</div>';
					$OUT .= '<div class="col-md-9 caption">';
						$OUT .= '<a href="?IDcurso='.$cursoData['IDcurso'].'&IDtema='.$row['ID'].'&IDvideo='.$rowVideo['ID'].'">'.$rowVideo['nombre'].'</a>';
						$OUT .= '<p>'.$rowVideo['descripcion'].'</p>';
					$OUT .= '</div></div>';
				}
			}
			$OUT .= '</div>';
		$OUT .= '</div>';
		$cont++;
	}
	$OUT .= '</div>';

	if ($numPag > 1) {
		$OUT .= '<ul class="pagination">';
		for ($i = 1; $i <= $numPag; $i++) {
			$OUT .= '<li'.( ($i == 1) ? ' class="active"' : '').'><a href="#">'.$i.'</a></li>';
		}
		$OUT .= '</ul>';
	}
$OUT .= '</div>';

echo $OUT;
?>