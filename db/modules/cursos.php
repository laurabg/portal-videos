<?php


/*
 createCurso: Crea un curso con los parámetros que se facilitan
 */
function createCurso($nombre, $descripcion, $ruta, $ubicacion, $orden, $ocultar, $IDcursoMoodle, $fechaIni, $fechaFin, $publico) {
	global $db;

	$SQL = 'INSERT INTO cursos (';
	$SQL .= 'nombre';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ($ruta != '')?',ruta':'';
	$SQL .= ($ubicacion != '')?',ubicacion':'';
	$SQL .= ($orden != '')?',orden':'';
	$SQL .= ($ocultar != '')?',ocultar':'';
	$SQL .= ($IDcursoMoodle != '')?',IDcursoMoodle':'';
	$SQL .= ($fechaIni != '')?',fechaIni':'';
	$SQL .= ($fechaFin != '')?',fechaFin':'';
	$SQL .= ($publico != '')?',publico':'';
	$SQL .= ') VALUES (';
	$SQL .= '"'.$nombre.'"';
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?',"'.$ruta.'"':'';
	$SQL .= ($ubicacion != '')?','.$ubicacion:'';
	$SQL .= ($orden != '')?','.$orden:'';
	$SQL .= ($ocultar != '')?','.$ocultar:'';
	$SQL .= ($IDcursoMoodle != '')?','.$IDcursoMoodle:'';
	$SQL .= ($fechaIni != '')?',"'.$fechaIni.'"':'';
	$SQL .= ($fechaFin != '')?',"'.$fechaFin.'"':'';
	$SQL .= ($publico != '')?','.$publico:'';
	$SQL .= ')';
	
	$db->exec($SQL);

	// Una vez creado el curso, obtener su ID y encriptarlo:
	$IDcurso = $db->querySingle('SELECT ID FROM cursos WHERE ruta = "'.$ruta.'" AND ubicacion = '.$ubicacion);

	// Actualizar el registro:
	$db->exec('UPDATE cursos SET IDencriptado = "'.encrypt($IDcurso).'" WHERE ID = '.$IDcurso);
}

/*
 updateCurso: Crea un curso con los parámetros que se facilitan
 */
function updateCurso($IDcurso, $nombre, $descripcion, $ruta, $ubicacion, $orden, $ocultar, $IDcursoMoodle, $fechaIni, $fechaFin, $publico) {
	global $db;

	$SQL = 'UPDATE cursos SET ';
	$SQL .= 'nombre = "'.$nombre.'"';
	$SQL .= ($descripcion != '')?', descripcion = "'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?', ruta = "'.$ruta.'"':'';
	$SQL .= ($ubicacion != '')?', ubicacion = '.$ubicacion:'';
	$SQL .= ($orden != '')?', orden = '.$orden:'';
	$SQL .= ($ocultar != '')?', ocultar = '.$ocultar:'';
	$SQL .= ($IDcursoMoodle != '')?', IDcursoMoodle = '.$IDcursoMoodle:'';
	$SQL .= ($fechaIni != '')?', fechaIni = "'.$fechaIni.'"':'';
	$SQL .= ($fechaFin != '')?', fechaFin = "'.$fechaFin.'"':'';
	$SQL .= ($publico != '')?', publico = '.$publico:'';
	$SQL .= ' WHERE ID = '.decrypt($IDcurso);

	$db->exec($SQL);
}

/*
 deleteFullCurso: Elimina un curso completo, con sus temas y videos
 */
function deleteFullCurso($IDcurso) {
	global $db;

	desregistrarUsuariosCurso($IDcurso);
	
	$db->exec('DELETE FROM videos WHERE IDcurso = '.decrypt($IDcurso));
	$db->exec('DELETE FROM temas WHERE IDcurso = '.decrypt($IDcurso));
	$db->exec('DELETE FROM cursos WHERE ID = '.decrypt($IDcurso));
}

/*
 checkCurso: Devuelve true si el curso existe, y false si no.
 */
function checkCurso($condicion) {
	global $db;
	
	return ($db->querySingle('SELECT COUNT(*) FROM cursos WHERE '.$condicion) > 0);
}

/*
 getIDcurso: Devuelve el ID encriptado del curso por nombre y ruta
 */
function getIDcurso($nombre, $ruta, $IDubicacion, $crearCurso) {
	global $db;

	if ( ($crearCurso == 1)&&(checkCurso('ruta = "'.$ruta.'" AND ubicacion = '.$IDubicacion) == 0) ) {
		$orden = getNextOrdenCurso();

		createCurso($nombre, $nombre, $ruta, $IDubicacion, $orden, _OCULTO, '', '', '', 0);
	}
	
	return $db->querySingle('SELECT IDencriptado FROM cursos WHERE ruta = "'.$ruta.'" AND ubicacion = '.$IDubicacion);
}

/*
 * getNextOrdenCurso: devuelve el siguiente orden en la tabla cursos
 */
function getNextOrdenCurso() {
	global $db;

	$orden = $db->querySingle('SELECT MAX(orden)+1 FROM cursos');
	if (!$orden) {
		$orden = 1;
	}
	return $orden;
}

/*
 getIDcursoByIDcursoMoodle: Devuelve el ID encriptado del curso asociado al ID de moodle
 */
function getIDcursoByIDcursoMoodle($IDcursoMoodle) {
	global $db;

	return $db->querySingle('SELECT IDencriptado FROM cursos WHERE IDcursoMoodle = '.$IDcursoMoodle);
}

/*
 * getCursoData: devuelve un array con toda la información de un curso:
 */
function getCursoData($IDcurso) {
	global $db;
	
	$curso = array();
	
	$res = $db->query('SELECT * FROM cursos WHERE ID = '.decrypt($IDcurso));

	while ($row = $res->fetchArray()) {
		$curso = array(
			'IDcurso' => $row['IDencriptado'],
			'nombre' => $row['nombre'],
			'descripcion' => $row['descripcion'],
			'ruta' => $row['ruta'],
			'ubicacion' => $row['ubicacion'],
			'orden' => $row['orden'],
			'ocultar' => $row['ocultar'],
			'IDcursoMoodle' => $row['IDcursoMoodle'],
			'fechaIni' => $row['fechaIni'],
			'fechaFin' => $row['fechaFin'],
			'publico' => $row['publico'],
		);
	}

	return $curso;
}

/*
 getCursoUsuarios: Obtiene una lista de los usuarios inscritos a un curso
 */
function getUsuariosByCurso($IDcurso) {
	global $db;
	
	$listaUsuarios = array();

	$res = $db->query('SELECT * FROM usuarios WHERE ID IN (SELECT IDusuario FROM cursosUsuarios WHERE IDcurso = '.decrypt($IDcurso).')');
	
	while ($row = $res->fetchArray()) {
		array_push($listaUsuarios, array(
			'ID' => $row['ID'],
			'fullname' => $row['fullname'],
			'email' => $row['email']
		));
	}

	return $listaUsuarios;
}

/*
 * getListaCursos: devuelve un array con todos los cursos ordenados:
 */
function getListaCursos() {
	global $db;
	
	$listaCursos = array();

	$res = $db->query('SELECT * FROM cursos ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaCursos, array($row['IDencriptado'], $row['nombre']));
	}

	return $listaCursos;
}
/*
 * getListaTemasByCurso: devuelve un array con todos los temas de un curso:
 */
function getListaTemasByCurso($IDcurso) {
	global $db;
	
	$listaTemas = array();

	$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.decrypt($IDcurso).' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaTemas, array($row['IDencriptado'], $row['nombre']));
	}

	return $listaTemas;
}

/*
 * getListaVideosByTemaCurso: devuelve un array con todos los vídeos de un tema y un curso:
 */
function getListaVideosByTemaCurso($IDcurso, $IDtema) {
	global $db;
	
	$listaVideos = array();

	$res = $db->query('SELECT * FROM videos WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaVideos, array($row['IDencriptado'], $row['nombre']));
	}

	return $listaVideos;
}

/*
 * getListaAdjuntosByVideoTemaCurso: devuelve un array con todos los adjuntos de un tema, video y un curso:
 */
function getListaAdjuntosByVideoTemaCurso($IDcurso, $IDtema, $IDvideo) {
	global $db;
	
	$listaAdjuntos = array();

	$res = $db->query('SELECT * FROM videosAdjuntos WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo).' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaAdjuntos, array($row['ID'], $row['nombre']));
	}

	return $listaAdjuntos;
}

?>