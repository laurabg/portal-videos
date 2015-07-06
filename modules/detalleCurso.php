<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;

$OUT = '';
$numPag = 0;
$cont = 0;
$totalPorPag = 3;

$cursoData = getCursoData($_GET['IDcurso']);

if ( ($cursoData['publico'] == 0)&&(!isset($_COOKIE['MoodleUserSession'])) ) {
	$OUT .= '<div class="container">';
		$OUT .= '<div class="row">';
			$OUT .= '<div class="col-md-12 margin-bottom">';
				$OUT .= '<h1>Acceso restringido</h1>';
				$OUT .= '<p>Debes estar logueado para poder ver este curso</p>';
			$OUT .= '</div>';
		$OUT .= '</div>';
	$OUT .= '</div>';

} else if ( ($cursoData['publico'] == 0)&&(isset($_COOKIE['MoodleUserSession']))&&(decrypt($_COOKIE['MoodleUserSession'],1)['esAdmin'] == 0)&&(checkCursoUsuario('IDusuario = '.decrypt($_COOKIE['MoodleUserSession'],1)['IDusuario'].' AND IDcursoMoodle = '.$cursoData['IDcursoMoodle']) == 0) ) {
	$OUT .= '<div class="container">';
		$OUT .= '<div class="row">';
			$OUT .= '<div class="col-md-12 margin-bottom">';
				$OUT .= '<h1>Acceso restringido</h1>';
				$OUT .= '<p>Para poder ver este curso debes estar matriculado en &eacute;l</p>';
			$OUT .= '</div>';
		$OUT .= '</div>';
	$OUT .= '</div>';

} else {

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
				$OUT .= '<a href="?opt=1&IDcurso='.urlencode($cursoData['IDcurso']).'"><button class="btn btn-default pull-right';
				if ( ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 1) )||(!isset($_COOKIE['listMode'])) ) {
					$OUT .= ' active';
				}
				$OUT .= '" type="button"><span class="glyphicon glyphicon-th"></span></button></a>';
				$OUT .= '<a href="?opt=2&IDcurso='.urlencode($cursoData['IDcurso']).'"><button class="btn btn-default pull-right';
				if ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 2) ) {
					$OUT .= ' active';
				}
				$OUT .= '" type="button"><span class="glyphicon glyphicon-th-list"></span></button></a>';
			$OUT .= '</div>';
			$OUT .= '<div class="col-md-3">';
				
			$OUT .= '</div>';
			$OUT .= '<div class="col-md-6"></div>';
		$OUT .= '</div>';
	$OUT .= '</div>';
	
	$OUT .= '<div class="container listado-paginado">';
		$totalCursos = $db->querySingle('SELECT COUNT(*) FROM temas WHERE IDcurso = '.decrypt($cursoData['IDcurso']).' AND ID IN (SELECT IDtema FROM videos)');

		$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.decrypt($cursoData['IDcurso']).' AND ID IN (SELECT IDtema FROM videos)'.( (isset($_GET['last'])) ? ' AND orden < '.$_GET['last'] : '' ).' ORDER BY orden DESC, nombre LIMIT '.$totalPorPag);
		while ($row = $res->fetchArray()) {
			$OUT .= '<div class="panel panel-primary">';
				$OUT .= '<div class="panel-heading">'.$row['nombre'].'</div>';
				$OUT .= '<div class="panel-body">';
				// Listar videos de cada tema:
				$resVideo = $db->query('SELECT * FROM videos WHERE IDtema = '.$row['ID'].' AND IDcurso = '.decrypt($cursoData['IDcurso']).' ORDER BY orden DESC, nombre');
				while ($rowVideo = $resVideo->fetchArray()) {
					if ( ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 1) )||(!isset($_COOKIE['listMode'])) ) {
						$OUT .= '<div class="col-sm-6 col-md-3 video-col">';
							$OUT .= '<a class="ver-video" href="?IDcurso='.urlencode($cursoData['IDcurso']).'&IDtema='.urlencode($row['IDencriptado']).'&IDvideo='.urlencode($rowVideo['IDencriptado']).'">';
								$OUT .= '<img src="'._PORTALROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$row['ruta'].'/img/'.$rowVideo['img'].'" />';
								$OUT .= '<span>'.$rowVideo['nombre'].'</span>';
							$OUT .= '</a>';
						$OUT .= '</div>';
					} else if ( (isset($_COOKIE['listMode']))&&($_COOKIE['listMode'] == 2) ) {
						$OUT .= '<div class="row"><div class="col-md-3">';
							$OUT .= '<a class="ver-video" href="?IDcurso='.urlencode($cursoData['IDcurso']).'&IDtema='.urlencode($row['IDencriptado']).'&IDvideo='.urlencode($rowVideo['IDencriptado']).'">';
								$OUT .= '<img src="'._PORTALROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$row['ruta'].'/img/'.$rowVideo['img'].'" />';
							$OUT .= '</a>';
						$OUT .= '</div>';
						$OUT .= '<div class="col-md-9 caption">';
							$OUT .= '<a href="?IDcurso='.urlencode($cursoData['IDcurso']).'&IDtema='.urlencode($row['IDencriptado']).'&IDvideo='.urlencode($rowVideo['IDencriptado']).'">'.$rowVideo['nombre'].'</a>';
							$OUT .= '<p>'.$rowVideo['descripcion'].'</p>';
						$OUT .= '</div></div>';
					}
				}
				$OUT .= '</div>';
			$OUT .= '</div>';
			$cont++;
		}

		$numPag = ceil($totalCursos / $totalPorPag);
		$last = $totalCursos;

		if ($numPag > 1) {
			$OUT .= '<ul class="pagination">';

			for ($i = 1; $i <= $numPag; $i++) {
				if ($i > 1) {
					$last = $last - $totalPorPag;
				}
				if ( ($i == $_GET['pag'])||( (!isset($_GET['pag']))&&($i == 1)) ) {
					$OUT .= '<li class="active"><a href="#">'.$i.'</a></li>';
				} else {
					$OUT .= '<li><a href="?IDcurso='.urlencode($cursoData['IDcurso']).'&pag='.$i.'&last='.($last+1).'">'.$i.'</a></li>';
				}
			}

			$OUT .= '</ul>';
		}
	$OUT .= '</div>';
}

echo $OUT;
?>