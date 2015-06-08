<?php
global $db;

$OUT = '';
$cont = 0;

// Mostrar la cabecera del curso, con su nombre y descripción:
$res = $db->query('SELECT * FROM cursos WHERE ID = '.$IDcurso);
while ($row = $res->fetchArray()) {
	$OUT .= getCabecera($row['nombre'], $row['descripcion'], 1, '', '');
}
$res = null;

// Listar todos los temas, junto con su descripción:
$OUT .= '<div class="container">';
	$OUT .= '<div class="row">';
	$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.$IDcurso);
	while ($row = $res->fetchArray()) {
		if ($cont % 3 == 0) {
			$OUT .= '</div><div class="row">';
		} 
		$OUT .= '<div class="col-md-4">';
			$OUT .= '<h2>'.$row['nombre'].'</h2>';
			$OUT .= '<p>'.$row['descripcion'].'</p>';
			$OUT .= '<p><a href="?IDcurso='.$IDcurso.'&IDtema='.$row['ID'].'" class="btn btn-default" role="button">Ver contenido del tema</a></p>';
		$OUT .= '</div>';
		
		$cont++;
	}
	$OUT .= '</div>';
$OUT .= '</div>';

echo $OUT;
?>