<?php

/*
 createVideo: Crea un video con los parámetros que se facilitan
 */
function createVideo($IDcurso, $IDtema, $nombre, $descripcion, $ruta, $img, $fechaCaducidad, $orden, $ocultar) {
	global $db;

	$SQL = 'INSERT INTO videos (IDcurso, IDtema, nombre, descripcion, ruta, img, fechaCaducidad, orden, ocultar) ';
	$SQL .= 'VALUES ('.decrypt($IDcurso).', '.decrypt($IDtema).', "'.$nombre.'", "'.$descripcion.'", "'.$ruta.'", "'.$img.'", "'.$fechaCaducidad.'", '.$orden.', '.$ocultar.')';
	
	$db->exec($SQL);

	// Una vez creado el video, obtener su ID y encriptarlo:
	$IDvideo = $db->querySingle('SELECT ID FROM videos WHERE ruta = "'.$ruta.'" AND IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema));

	// Actualizar el registro:
	$db->exec('UPDATE videos SET IDencriptado = "'.encrypt($IDvideo).'" WHERE ID = '.$IDvideo);
}

/*
 updateVideo: Actualiza un video existente
 */
function updateVideo($IDvideo, $IDcurso, $IDtema, $nombre, $descripcion, $ruta, $img, $fechaCaducidad, $orden, $ocultar) {
	global $db;

	$SQL = 'UPDATE videos SET ';
	$SQL .= 'IDcurso = '.decrypt($IDcurso);
	$SQL .= ', IDtema = '.decrypt($IDtema);
	$SQL .= ', nombre = "'.$nombre.'"';
	$SQL .= ', descripcion = "'.$descripcion.'"';
	$SQL .= ', ruta = "'.$ruta.'"';
	$SQL .= ', img = "'.$img.'"';
	$SQL .= ', fechaCaducidad = "'.$fechaCaducidad.'"';
	$SQL .= ', orden = '.$orden;
	$SQL .= ', ocultar = '.$ocultar;
	$SQL .= ' WHERE ID = '.decrypt($IDvideo);
	
	$db->exec($SQL);
}

/*
 * updateVideoIMG: Actualiza el registro del video para asociarle una imagen
 */
function updateVideoIMG($IDvideo, $img) {
	global $db;

	$db->exec('UPDATE videos SET img = "'.$img.'" WHERE ID = '.decrypt($IDvideo).';');
}

/*
 deleteVideo: Elimina un video
 */
function deleteVideo($IDvideo) {
	global $db;

	$db->exec('DELETE FROM videos WHERE ID = '.decrypt($IDvideo));
}

/*
 checkVideo: Devuelve true si el video existe, y false si no.
 */
function checkVideo($condicion) {
	global $db;
	
	return ($db->querySingle('SELECT COUNT(*) FROM videos WHERE '.$condicion) > 0);
}

/*
 getIDvideo: Devuelve el ID del video.
 */
function getIDvideo($IDcurso, $IDtema, $nombre, $ruta, $crearVideo) {
	global $db;

	if ( ($crearVideo == 1)&&(checkVideo('nombre = "'.$nombre.'" AND IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema)) == 0) ) {
		$orden = getNextOrdenVideo($IDcurso, $IDtema);

		createVideo($IDcurso, $IDtema, $nombre, '', $ruta, $img, '', $orden, _OCULTO);
	}

	return $db->querySingle('SELECT IDencriptado FROM videos WHERE nombre = "'.$nombre.'" AND IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema));
}

/*
 * getNextOrdenVideo: devuelve el siguiente orden en la tabla videos para un curso y tema
 */
function getNextOrdenVideo($IDcurso, $IDtema) {
	global $db;

	$orden = $db->querySingle('SELECT MAX(orden)+1 FROM videos WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema));
	if (!$orden) {
		$orden = 1;
	}
	return $orden;
}

/*
 * getVideoData: devuelve un array con toda la información de un vídeo:
 */
function getVideoData($IDcurso, $IDtema, $IDvideo) {
	global $db;
	
	$video = array();

	$adjuntos = getAllAdjuntos($IDcurso, $IDtema, $IDvideo);
	$categorias = getAllCategorias($IDcurso, $IDtema, $IDvideo);

	$res = $db->query('SELECT * FROM videos WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND ID = '.decrypt($IDvideo).' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		$video = array(
			'IDcurso' => $IDcurso,
			'IDtema' => $IDtema,
			'IDvideo' => $row['IDencriptado'],
			'nombre' => $row['nombre'],
			'descripcion' => $row['descripcion'],
			'ruta' => $row['ruta'],
			'img' => $row['img'],
			'fechaCaducidad' => $row['fechaCaducidad'],
			'orden' => $row['orden'],
			'ocultar' => $row['ocultar'],
			'adjuntos' => $adjuntos,
			'categorias' => $categorias
		);
	}

	return $video;
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
 * encriptarVideos: Encripta todos los IDs de los videos:
 */
function encriptarVideos($encriptarForzado = 0) {
	global $db;
	
	$res = $db->query('SELECT ID FROM videos');
	while ($row = $res->fetchArray()) {
		if ($encriptarForzado == 0) {
			$db->exec('UPDATE videos SET IDencriptado = "'.$row['ID'].'" WHERE ID = '.$row['ID']);
		} else {
			$db->exec('UPDATE videos SET IDencriptado = "'.encrypt($row['ID'], $encriptarForzado).'" WHERE ID = '.$row['ID']);
		}
	}
}
?>