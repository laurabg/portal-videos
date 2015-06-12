<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');

global $dbAn;

$OUT = '';

if ($_POST['chartName'] == 'totalVisualizaciones') {
	$totalAnonimos = $dbAn->querySingle('SELECT COUNT(*) FROM analytics WHERE IDusuario = 0');
	$totalRegistrados = $dbAn->querySingle('SELECT COUNT(*) FROM analytics WHERE IDusuario != 0');

	$OUT .= '[';
		$OUT .= '[ "Anonimos", '.$totalAnonimos.' ],';
		$OUT .= '[ "Registrados", '.$totalRegistrados.' ]';
	$OUT .= ']';

} else if ( ($_POST['chartName'] == 'videosMasVistos')||($_GET['chartName'] == 'videosMasVistos') ) {
	$categories = array();
	$data = array();

	$res = $dbAn->query('SELECT IDcurso, IDtema, IDvideo, COUNT(*) AS total FROM analytics GROUP BY IDcurso, IDtema, IDvideo ORDER BY 4 DESC');
	while ($row = $res->fetchArray()) {
		$cursoData = getCursoData($row['IDcurso']);
		$temaData = getTemaData($row['IDcurso'], $row['IDtema']);
		$videoData = getVideoData($row['IDcurso'], $row['IDtema'], $row['IDvideo']);

		array_push($categories, $cursoData['nombre'].' / '.$temaData['nombre'].' / '.$videoData['nombre']);
		array_push($data, $row['total']);
	}

	$OUT .= '{';
		$OUT .= '"categories": ["'.implode('","', $categories).'"],';
		$OUT .= '"info": [{"name":"Total reproducciones","data":['.implode(',',$data).']}]';
	$OUT .= '}';
}

echo $OUT;

?>