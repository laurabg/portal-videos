<?php


/*
 createTema: Crea un tema con los parámetros que se facilitan
 */
function createTema($IDcurso, $nombre, $descripcion, $ruta, $orden, $ocultar) {
	global $db;

	$SQL = 'INSERT INTO temas (';
	$SQL .= 'IDcurso';
	$SQL .= ',nombre';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ($ruta != '')?',ruta':'';
	$SQL .= ($orden != '')?',orden':'';
	$SQL .= ',ocultar';
	$SQL .= ') VALUES (';
	$SQL .= decrypt($IDcurso);
	$SQL .= ',"'.$nombre.'"';
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?',"'.$ruta.'"':'';
	$SQL .= ($orden != '')?','.$orden:'';
	$SQL .= ','.$ocultar;
	$SQL .= ')';
	//print $SQL;
	$db->exec($SQL);

	// Una vez creado el tema, obtener su ID y encriptarlo:
	$IDtema = $db->querySingle('SELECT ID FROM temas WHERE ruta = "'.$ruta.'" AND IDcurso = '.decrypt($IDcurso));

	// Actualizar el registro:
	$db->exec('UPDATE temas SET IDencriptado = "'.encrypt($IDtema).'" WHERE ID = '.$IDtema);
}


/*
 updateTema: Actualiza un tema existente
 */
function updateTema($IDtema, $IDcurso, $nombre, $descripcion, $ruta, $orden, $ocultar) {
	global $db;

	$SQL = 'UPDATE temas SET ';
	$SQL .= 'IDcurso = '.decrypt($IDcurso);
	$SQL .= ', nombre = "'.$nombre.'"';
	$SQL .= ($descripcion != '')?', descripcion = "'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?', ruta = "'.$ruta.'"':'';
	$SQL .= ($orden != '')?', orden = '.$orden:'';
	$SQL .= ', ocultar = '.$ocultar;
	$SQL .= ' WHERE ID = '.decrypt($IDtema);
	
	$db->exec($SQL);
}

/*
 deleteFullTema: Elimina un tema completo, con sus videos
 */
function deleteFullTema($IDtema) {
	global $db;

	$db->exec('DELETE FROM videos WHERE IDtema = '.decrypt($IDtema));
	$db->exec('DELETE FROM temas WHERE ID = '.decrypt($IDtema));
}

/*
 checkTema: Devuelve true si el tema existe, y false si no.
 */
function checkTema($condicion) {
	global $db;
	
	return ($db->querySingle('SELECT COUNT(*) FROM temas WHERE '.$condicion) > 0);
}

/*
 getIDtema: Devuelve ID encriptado tema por ruta e IDcurso.
 */
function getIDtema($IDcurso, $nombre, $ruta, $crearTema) {
	global $db;

	if ( ($crearTema == 1)&&(checkTema('ruta = "'.$ruta.'" AND IDcurso = '.decrypt($IDcurso)) == 0) ) {
		$orden = getNextOrdenTema($IDcurso);

		createTema($IDcurso, $nombre, $nombre, $ruta, $orden, _OCULTO);
	}

	return $db->querySingle('SELECT IDencriptado FROM temas WHERE ruta = "'.$ruta.'" AND IDcurso = '.decrypt($IDcurso));
}

/*
 * getNextOrdenTema: devuelve el siguiente orden en la tabla temas para un curso
 */
function getNextOrdenTema($IDcurso) {
	global $db;

	$orden = $db->querySingle('SELECT MAX(orden)+1 FROM temas WHERE IDcurso = '.decrypt($IDcurso));
	if (!$orden) {
		$orden = 1;
	}
	return $orden;
}

/*
 * getTemaData: devuelve un array con toda la información de un tema:
 */
function getTemaData($IDcurso, $IDtema) {
	global $db;
	
	$tema = array();

	$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.decrypt($IDcurso).' AND ID = '.decrypt($IDtema).' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		$tema = array(
			'IDcurso' => $IDcurso,
			'IDtema' => $row['IDencriptado'],
			'nombre' => $row['nombre'],
			'descripcion' => $row['descripcion'],
			'ruta' => $row['ruta'],
			'orden' => $row['orden'],
			'ocultar' => $row['ocultar']
		);
	}

	return $tema;
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
 * encriptarTemas: Encripta todos los IDs de los temas:
 */
function encriptarTemas($encriptarForzado = 0) {
	global $db;
	
	$res = $db->query('SELECT ID FROM temas');
	while ($row = $res->fetchArray()) {
		if ($encriptarForzado == 0) {
			$db->exec('UPDATE temas SET IDencriptado = "'.$row['ID'].'" WHERE ID = '.$row['ID']);
		} else {
			$db->exec('UPDATE temas SET IDencriptado = "'.encrypt($row['ID'], $encriptarForzado).'" WHERE ID = '.$row['ID']);
		}
	}
}
?>