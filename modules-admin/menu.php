<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');

$OUT = '';

function listaItemsCursos() {
	$opt = 'cursos';
	$OUT = '';

	$cls = '';
	if ( (!isset($_GET['IDcurso']))&&($_GET['opt'] == $opt) ) {
		$cls = ' active';
	}

	$OUT .= '<li class="firstChild'.$cls.'">';
		$OUT .= '<div class="item">';
			$OUT .= '<span class="glyphicon glyphicon-plus"></span>';
			$OUT .= '<a href="?opt='.$opt.'">';
				$OUT .= '<span class="txt" title="Crear nuevo curso">Crear nuevo curso</span>';
			$OUT .= '</a>';
		$OUT .= '</div>';
	$OUT .= '</li>';

	$listaCursos = getListaCursos();

	for ($i = 0; $i < sizeof($listaCursos); $i++) {
		$item = $listaCursos[$i];
		$cls = '';

		if ( ($item[0] == $_GET['IDcurso']) ) {
			$cls = ' expanded';
			if ( ($_GET['opt'] == $opt)&&(!isset($_GET['IDtema']))&&(!isset($_GET['IDvideo'])) )  {
				$cls .= ' active';
			}
		}

		$OUT .= '<li class="firstChild'.$cls.'">';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-folder-close"></span>';
				$OUT .= '<span class="txt" title="'.$item[1].'">'.$item[1].'</span>';
				$OUT .= '<a class="edit" href="?opt='.$opt.'&IDcurso='.$item[0].'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
			$OUT .= '<ul class="submenu">';
				$OUT .= listaItemsTemas($item[0]);
			$OUT .= '</ul>';
		$OUT .= '</li>';
	}

	return $OUT;
}

function listaItemsTemas($IDcurso) {
	$opt = 'temas';
	$OUT = '';

	$cls = '';
	if ( ($_GET['opt'] == $opt)&&($_GET['IDcurso'] == $IDcurso)&&(!isset($_GET['IDtema'])) ) {
		$cls = ' class="active"';
	}

	$OUT .= '<li'.$cls.'>';
		$OUT .= '<div class="item">';
			$OUT .= '<span class="glyphicon glyphicon-plus"></span>';
			$OUT .= '<a href="?opt='.$opt.'&IDcurso='.$IDcurso.'">';
				$OUT .= '<span class="txt" title="Añadir nuevo tema">Añadir nuevo tema</span>';
			$OUT .= '</a>';
		$OUT .= '</div>';
	$OUT .= '</li>';

	$listaTemas = getListaTemasByCurso($IDcurso);

	for ($i = 0; $i < sizeof($listaTemas); $i++) {
		$item = $listaTemas[$i];
		$cls = '';

		if ( ($item[0] == $_GET['IDtema']) ) {
			$cls = ' class="expanded';
			if ( ($_GET['opt'] == $opt)&&(!isset($_GET['IDvideo'])) ) {
				$cls .= ' active';
			}
			$cls .= '"';
		}

		$OUT .= '<li'.$cls.'>';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-folder-close"></span>';
				$OUT .= '<span class="txt" title="'.$item[1].'">'.$item[1].'</span>';
				$OUT .= '<a class="edit" href="?opt='.$opt.'&IDcurso='.$IDcurso.'&IDtema='.$item[0].'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
			$OUT .= '<ul class="submenu">';
				$OUT .= listaItemsVideos($IDcurso, $item[0]);
			$OUT .= '</ul>';
		$OUT .= '</li>';
	}

	return $OUT;
}

function listaItemsVideos($IDcurso, $IDtema) {
	$opt = 'videos';
	$OUT = '';

	$cls = '';
	if ( ($_GET['opt'] == $opt)&&($_GET['IDcurso'] == $IDcurso)&&($_GET['IDtema'] == $IDtema)&&(!isset($_GET['IDvideo'])) ) {
		$cls = ' class="active"';
	}

	$OUT .= '<li'.$cls.'>';
		$OUT .= '<div class="item">';
			$OUT .= '<span class="glyphicon glyphicon-plus"></span>';
			$OUT .= '<a href="?opt='.$opt.'&IDcurso='.$IDcurso.'&IDtema='.$IDtema.'">';
				$OUT .= '<span class="txt" title="Añadir nuevo vídeo">Añadir nuevo vídeo</span>';
			$OUT .= '</a>';
		$OUT .= '</div>';
	$OUT .= '</li>';

	$listaVideos = getListaVideosByTemaCurso($IDcurso, $IDtema);

	for ($i = 0; $i < sizeof($listaVideos); $i++) {
		$item = $listaVideos[$i];
		$cls = '';

		if ( ($_GET['opt'] == $opt)&&($item[0] == $_GET['IDvideo']) ) {
			$cls = ' class="active"';
		}

		$OUT .= '<li'.$cls.'>';
			$OUT .= '<div class="item">';
				$OUT .= '<span class="glyphicon glyphicon-facetime-video"></span>';
				$OUT .= '<span class="txt" title="'.$item[1].'">'.$item[1].'</span>';
				$OUT .= '<a class="edit" href="?opt='.$opt.'&IDcurso='.$IDcurso.'&IDtema='.$IDtema.'&IDvideo='.$item[0].'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
			$OUT .= '</div>';
		$OUT .= '</li>';
	}

	return $OUT;
}

$menu = array(
	array( 'nombre' => 'Configuración', 'url' => 'config', 'cierreMenu' => 1 ),
//	array( 'nombre' => 'Cursos', 'url' => 'cursos', 'cierreMenu' => 0 ),
//	array( 'nombre' => 'Temas', 'url' => 'temas', 'cierreMenu' => 0 ),
//	array( 'nombre' => 'Vídeos', 'url' => 'videos', 'cierreMenu' => 1 ),
	array( 'nombre' => 'Analytics', 'url' => 'analytics', 'cierreMenu' => 0 ),
	array( 'nombre' => 'Usuarios', 'url' => 'usuarios', 'cierreMenu' => 0 )
);

$OUT .= '<div class="tree"><ul class="nav nav-sidebar">';
	$OUT .= listaItemsCursos();
$OUT .= '</ul></div>';

$OUT .= '<ul class="nav nav-sidebar">';

for ($i = 0; $i < sizeof($menu); $i++) {
	$item = $menu[$i];

	$OUT .= '<li';
	if ( ($_GET['opt'] == $item['url']) ) {
		$OUT .=  ' class="active"';
	}
	$OUT .= '><a href="?opt='.$item['url'].'">'.$item['nombre'].'</a></li>';

	if ($item['cierreMenu']) {
		$OUT .= '</ul><ul class="nav nav-sidebar">';
	}
}
$OUT .= '</ul>';

echo $OUT;

?>