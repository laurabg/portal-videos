<?php

/*
 createVideo: Crea un video con los parámetros que se facilitan
 */
function createVideo($IDcurso, $IDtema, $nombre, $descripcion, $ruta, $img, $orden, $ocultar) {
	global $db;

	$SQL = 'INSERT INTO videos (';
	$SQL .= 'IDcurso';
	$SQL .= ',IDtema';
	$SQL .= ',nombre';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ($ruta != '')?',ruta':'';
	$SQL .= ($img != '')?',img':'';
	$SQL .= ($orden != '')?',orden':'';
	$SQL .= ($ocultar != '')?',ocultar':'';
	$SQL .= ') VALUES ('; 
	$SQL .= decrypt($IDcurso);
	$SQL .= ','.decrypt($IDtema);
	$SQL .= ',"'.$nombre.'"';
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?',"'.$ruta.'"':'';
	$SQL .= ($img != '')?',"'.$img.'"':'';
	$SQL .= ($orden != '')?','.$orden:'';
	$SQL .= ($ocultar != '')?','.$ocultar:'';
	$SQL .= ')';
	
	$db->exec($SQL);

	// Una vez creado el video, obtener su ID y encriptarlo:
	$IDvideo = $db->querySingle('SELECT ID FROM videos WHERE ruta = "'.$ruta.'" AND IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema));

	// Actualizar el registro:
	$db->exec('UPDATE videos SET IDencriptado = "'.encrypt($IDvideo).'" WHERE ID = '.$IDvideo);
}

/*
 updateVideo: Actualiza un video existente
 */
function updateVideo($IDvideo, $IDcurso, $IDtema, $nombre, $descripcion, $ruta, $img, $orden, $ocultar) {
	global $db;

	$SQL = 'UPDATE videos SET ';
	$SQL .= 'IDcurso = '.decrypt($IDcurso);
	$SQL .= ', IDtema = '.decrypt($IDtema);
	$SQL .= ', nombre = "'.$nombre.'"';
	$SQL .= ($descripcion != '')?', descripcion = "'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?', ruta = "'.$ruta.'"':'';
	$SQL .= ($img != '')?', img = "'.$img.'"':'';
	$SQL .= ($orden != '')?', orden = '.$orden:'';
	$SQL .= ($ocultar != '')?', ocultar = '.$ocultar:'';
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

		createVideo($IDcurso, $IDtema, $nombre, $nombre, $ruta, $img, $orden, _OCULTO);
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
			'orden' => $row['orden'],
			'ocultar' => $row['ocultar'],
			'adjuntos' => $adjuntos
		);
	}

	return $video;
}


?>