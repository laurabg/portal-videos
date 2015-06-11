<?php

// Inicio: Crear la base de datos y la conexión a ésta:
dbCreate(_BBDD);


/*
 dbCreate: Crea la base de datos y una conexión a ésta:
 */
function dbCreate($dbName) {
	global $db;
	
	if (!file_exists($dbName)) {
	//	echo 'Creando bbdd...<br />';
		$db = new SQLite3($dbName);
		chmod($dbName, 0777);

	//	echo 'Creando tablas...<br />';
		crearTablas();
	} else {
	//	echo 'La base de datos ya existe<br />';
		$db = new SQLite3($dbName);
	}
}

function dbConfigCreate($dbConfigName) {
	global $dbConfig;

	if (!file_exists($dbConfigName)) {
	//	echo 'Creando bbddLog...<br />';
		$dbConfig = new SQLite3($dbConfigName);
		chmod($dbConfigName, 0777);

	//	echo 'Creando tablasLog...<br />';
		crearTablasConfig();
	} else {
	//	echo 'La base de datos ya existe<br />';
		$dbConfig = new SQLite3($dbConfigName);
	}
}

function dbLogCreate($dbLogName) {
	global $dbLog;

	if (!file_exists($dbLogName)) {
	//	echo 'Creando bbddLog...<br />';
		$dbLog = new SQLite3($dbLogName);
		chmod($dbLogName, 0777);

	//	echo 'Creando tablasLog...<br />';
		crearTablasLog();
	} else {
	//	echo 'La base de datos ya existe<br />';
		$dbLog = new SQLite3($dbLogName);
	}
}

function dbAnalyticsCreate($dbAnName) {
	global $dbAn;

	if (!file_exists($dbAnName)) {
	//	echo 'Creando bbddLog...<br />';
		$dbAn = new SQLite3($dbAnName);
		chmod($dbAnName, 0777);

	//	echo 'Creando tablasLog...<br />';
		crearTablasAnalytics();
	} else {
	//	echo 'La base de datos ya existe<br />';
		$dbAn = new SQLite3($dbAnName);
	}
}

function crearTablas() {
	global $db;

	$db->exec('CREATE TABLE cursos (
		ID INTEGER PRIMARY KEY, 
		nombre TEXT, 
		descripcion TEXT,
		ruta TEXT,
		ubicacion INTEGER,
		orden INTEGER,
		ocultar INTEGER,
		IDcursoMoodle INTEGER,
		fechaIni DATE,
		fechaFin DATE,
		publico TEXT);'
	);
	$db->exec('CREATE TABLE temas (
		ID INTEGER PRIMARY KEY, 
		IDcurso INTEGER,
		nombre TEXT, 
		descripcion TEXT, 
		ruta TEXT,
		orden INTEGER,
		ocultar INTEGER);'
	);
	$db->exec('CREATE TABLE videos (
		ID INTEGER PRIMARY KEY, 
		IDcurso INTEGER,
		IDtema INTEGER, 
		nombre TEXT, 
		descripcion TEXT, 
		ruta TEXT, 
		img TEXT, 
		orden INTEGER,
		ocultar INTEGER);'
	);
	$db->exec('CREATE TABLE videosAdjuntos (
		ID INTEGER PRIMARY KEY, 
		IDcurso INTEGER,
		IDtema INTEGER, 
		IDvideo INTEGER, 
		nombre TEXT, 
		descripcion TEXT, 
		ruta TEXT, 
		orden INTEGER,
		ocultar INTEGER);'
	);
	$db->exec('CREATE TABLE usuarios (
		ID INTEGER PRIMARY KEY, 
		fullname TEXT, 
		email TEXT,
		bloqueado INTEGER);'
	);
	$db->exec('CREATE TABLE cursosUsuarios (
		ID INTEGER PRIMARY KEY, 
		IDusuario INTEGER,
		IDcurso INTEGER,
		IDcursoMoodle INTEGER);'
	);
}


function crearTablasConfig() {
	global $dbConfig;

	$dbConfig->exec('CREATE TABLE adminvars (
		ID INTEGER PRIMARY KEY, 
		nombre TEXT,
		valor TEXT);'
	);

	crearAdminvar('showErrors',1);
	crearAdminvar('_WSTOKEN','');
	crearAdminvar('_MOODLEURL','');
	
	$dbConfig->exec('CREATE TABLE ubicaciones (
		ID INTEGER PRIMARY KEY, 
		ruta TEXT);'
	);

	crearUbicacion('cursos/');

	$dbConfig->exec('CREATE TABLE extensionesValidas (
		ID INTEGER PRIMARY KEY, 
		nombre TEXT);'
	);

	crearExtension('mp4');
}

function crearTablasLog() {
	global $dbLog;

	$dbLog->exec('CREATE TABLE log (
		ID INTEGER PRIMARY KEY, 
		descripcion TEXT,
		"timestamp" DATETIME DEFAULT CURRENT_TIMESTAMP);'
	);
}

function crearTablasAnalytics() {
	global $dbAn;

	$dbAn->exec('CREATE TABLE analytics (
		ID INTEGER PRIMARY KEY, 
		IDcurso INTEGER,
		IDtema INTEGER,
		IDvideo INTEGER,
		IDusuario INTEGER,
		"timestamp" DATETIME DEFAULT CURRENT_TIMESTAMP);'
	);
}

function resetDB() {
	global $db;
	$db->exec('DELETE FROM videos');
	$db->exec('DELETE FROM temas');
	$db->exec('DELETE FROM cursos');
}

function resetDBLog() {
	global $dbLog;
	$dbLog->exec('DELETE FROM log');
}

function resetDBAnalytics() {
	global $dbAn;
	$dbAn->exec('DELETE FROM analytics');
}





/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* -----------------------------------------------------    CURSOS    ----------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

/*
 crearCurso: Crea un curso con los parámetros que se facilitan
 */
function crearCurso($nombre, $descripcion, $ruta, $ubicacion, $orden, $ocultar, $IDcursoMoodle, $fechaIni, $fechaFin, $publico) {
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
	$SQL .= ' WHERE ID = '.$IDcurso;

	$db->exec($SQL);
}

/*
 checkCurso: Devuelve true si el curso existe, y false si no.
 */
function checkCurso($condicion) {
	global $db;
	
	return $db->querySingle('SELECT COUNT(*) FROM cursos WHERE '.$condicion);
}

/*
 getIDcurso: Devuelve el ID del curso por nombre y ruta
 */
function getIDcurso($nombre, $ruta, $IDubicacion, $crearCurso) {
	global $db;

	if ( ($crearCurso == 1)&&(checkCurso('ruta = "'.$ruta.'" AND ubicacion = '.$IDubicacion) == 0) ) {
		$orden = getNextOrdenCurso();

		crearCurso($nombre, $nombre, $ruta, $IDubicacion, ( $orden=='' ? 1 : $orden ), _OCULTO, '', '', '', 0);
	}
	
	return $db->querySingle('SELECT ID FROM cursos WHERE ruta = "'.$ruta.'" AND ubicacion = '.$IDubicacion);
}

/*
 getIDcursoByIDcursoMoodle: Devuelve el ID del curso asociado al ID de moodle
 */
function getIDcursoByIDcursoMoodle($IDcursoMoodle) {
	global $db;

	return $db->querySingle('SELECT ID FROM cursos WHERE IDcursoMoodle = '.$IDcursoMoodle);
}

/*
 * getCursoData: devuelve un array con toda la información de un curso:
 */
function getCursoData($IDcurso) {
	global $db;
	
	$curso = array();

	$res = $db->query('SELECT * FROM cursos WHERE ID = '.$IDcurso);
	while ($row = $res->fetchArray()) {
		$curso = array(
			'IDcurso' => $row['ID'],
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
 * getNextOrdenCurso: devuelve el siguiente orden en la tabla cursos
 */
function getNextOrdenCurso() {
	global $db;

	return $db->querySingle('SELECT MAX(orden)+1 FROM cursos');
}

/*
 deleteFullCurso: Elimina un curso completo, con sus temas y videos
 */
function deleteFullCurso($IDcurso) {
	global $db;

	desregistrarUsuariosCurso($IDcurso);
	
	$db->exec('DELETE FROM videos WHERE IDcurso = '.$IDcurso);
	$db->exec('DELETE FROM temas WHERE IDcurso = '.$IDcurso);
	$db->exec('DELETE FROM cursos WHERE ID = '.$IDcurso);
}

/*
 * getListaCursos: devuelve un array con todos los cursos ordenados:
 */
function getListaCursos() {
	global $db;
	
	$listaCursos = array();

	$res = $db->query('SELECT * FROM cursos ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaCursos, array($row['ID'], $row['nombre']));
	}

	return $listaCursos;
}

/*
 getCursoUsuarios: Obtiene una lista de los usuarios inscritos a un curso
 */
function getUsuariosByCurso($IDcurso) {
	global $db;
	
	$listaUsuarios = array();

	$res = $db->query('SELECT * FROM usuarios WHERE ID IN (SELECT IDusuario FROM cursosUsuarios WHERE IDcurso = '.$IDcurso.')');
	while ($row = $res->fetchArray()) {
		array_push($listaUsuarios, array( 'ID' => $row['ID'], 'fullname' => $row['fullname'], 'email' => $row['email']));
	}

	return $listaUsuarios;
}

/*
 * getListaTemasByCurso: devuelve un array con todos los temas de un curso:
 */
function getListaTemasByCurso($IDcurso) {
	global $db;
	
	$listaTemas = array();

	$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.$IDcurso.' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaTemas, array($row['ID'], $row['nombre']));
	}

	return $listaTemas;
}

/*
 * getListaVideosByTemaCurso: devuelve un array con todos los vídeos de un tema y un curso:
 */
function getListaVideosByTemaCurso($IDcurso, $IDtema) {
	global $db;
	
	$listaVideos = array();

	$res = $db->query('SELECT * FROM videos WHERE IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema.' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaVideos, array($row['ID'], $row['nombre']));
	}

	return $listaVideos;
}





/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* -----------------------------------------------------    TEMAS    ------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

/*
 crearTema: Crea un tema con los parámetros que se facilitan
 */
function crearTema($IDcurso, $nombre, $descripcion, $ruta, $orden, $ocultar) {
	global $db;

	$SQL = 'INSERT INTO temas (';
	$SQL .= 'IDcurso';
	$SQL .= ',nombre';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ($ruta != '')?',ruta':'';
	$SQL .= ($orden != '')?',orden':'';
	$SQL .= ($ocultar != '')?',ocultar':'';
	$SQL .= ') VALUES (';
	$SQL .= $IDcurso;
	$SQL .= ',"'.$nombre.'"';
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?',"'.$ruta.'"':'';
	$SQL .= ($orden != '')?','.$orden:'';
	$SQL .= ($ocultar != '')?','.$ocultar:'';
	$SQL .= ')';
	//print $SQL;
	$db->exec($SQL);
}


/*
 updateTema: Actualiza un tema existente
 */
function updateTema($IDtema, $IDcurso, $nombre, $descripcion, $ruta, $orden, $ocultar) {
	global $db;

	$SQL = 'UPDATE temas SET ';
	$SQL .= 'IDcurso = '.$IDcurso;
	$SQL .= ', nombre = "'.$nombre.'"';
	$SQL .= ($descripcion != '')?', descripcion = "'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?', ruta = "'.$ruta.'"':'';
	$SQL .= ($orden != '')?', orden = '.$orden:'';
	$SQL .= ($ocultar != '')?', ocultar = '.$ocultar:'';
	$SQL .= ' WHERE ID = '.$IDtema;
	
	$db->exec($SQL);
}

/*
 checkTema: Devuelve true si el tema existe, y false si no.
 */
function checkTema($condicion) {
	global $db;
	
	return $db->querySingle('SELECT COUNT(*) FROM temas WHERE '.$condicion);
}

/*
 getIDtema: Devuelve ID tema por ruta e IDcurso.
 */
function getIDtema($IDcurso, $nombre, $ruta, $crearTema) {
	global $db;

	if ( ($crearTema == 1)&&(checkTema('ruta = "'.$ruta.'" AND IDcurso = '.$IDcurso) == 0) ) {
		$orden = getNextOrdenTema($IDcurso);

		crearTema($IDcurso, $nombre, $nombre, $ruta, ( $orden=='' ? 1 : $orden ), _OCULTO);
	}

	return $db->querySingle('SELECT ID FROM temas WHERE ruta = "'.$ruta.'" AND IDcurso = '.$IDcurso);
}

/*
 deleteFullTema: Elimina un tema completo, con sus videos
 */
function deleteFullTema($IDtema) {
	global $db;

	$db->exec('DELETE FROM videos WHERE IDtema = '.$IDtema);
	$db->exec('DELETE FROM temas WHERE ID = '.$IDtema);
}

/*
 * getTemaData: devuelve un array con toda la información de un tema:
 */
function getTemaData($IDcurso, $IDtema) {
	global $db;
	
	$tema = array();

	$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.$IDcurso.' AND ID = '.$IDtema.' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		$tema = array(
			'IDcurso' => $row['IDcurso'],
			'IDtema' => $row['ID'],
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
 * getNextOrdenTema: devuelve el siguiente orden en la tabla temas para un curso
 */
function getNextOrdenTema($IDcurso) {
	global $db;

	return $db->querySingle('SELECT MAX(orden)+1 FROM temas WHERE IDcurso = '.$IDcurso);
}



/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* -----------------------------------------------------    VIDEOS    ----------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

/*
 crearVideo: Crea un video con los parámetros que se facilitan
 */
function crearVideo($IDcurso, $IDtema, $nombre, $descripcion, $ruta, $img, $orden, $ocultar) {
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
	$SQL .= ') VALUES ('; //.$nombre.'",'.$IDcurso.','.$IDtema.','.$orden;
	$SQL .= $IDcurso;
	$SQL .= ','.$IDtema;
	$SQL .= ',"'.$nombre.'"';
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?',"'.$ruta.'"':'';
	$SQL .= ($img != '')?',"'.$img.'"':'';
	$SQL .= ($orden != '')?','.$orden:'';
	$SQL .= ($ocultar != '')?','.$ocultar:'';
	$SQL .= ')';
	
	$db->exec($SQL);
}

/*
 updateVideo: Actualiza un video existente
 */
function updateVideo($IDvideo, $IDcurso, $IDtema, $nombre, $descripcion, $ruta, $img, $orden, $ocultar) {
	global $db;

	$SQL = 'UPDATE videos SET ';
	$SQL .= 'IDcurso = '.$IDcurso;
	$SQL .= ', IDtema = '.$IDtema;
	$SQL .= ', nombre = "'.$nombre.'"';
	$SQL .= ($descripcion != '')?', descripcion = "'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?', ruta = "'.$ruta.'"':'';
	$SQL .= ($img != '')?', img = "'.$img.'"':'';
	$SQL .= ($orden != '')?', orden = '.$orden:'';
	$SQL .= ($ocultar != '')?', ocultar = '.$ocultar:'';
	$SQL .= ' WHERE ID = '.$IDvideo;
	
	$db->exec($SQL);
}

/*
 * updateVideoIMG: Actualiza el registro del video para asociarle una imagen
 */
function updateVideoIMG($IDvideo, $img) {
	global $db;

	$db->exec('UPDATE videos SET img = "'.$img.'" WHERE ID = '.$IDvideo.';');
}

/*
 checkVideo: Devuelve true si el video existe, y false si no.
 */
function checkVideo($condicion) {
	global $db;
	
	return $db->querySingle('SELECT COUNT(*) FROM videos WHERE '.$condicion);
}

/*
 deleteVideo: Elimina un video
 */
function deleteVideo($IDvideo) {
	global $db;

	$db->exec('DELETE FROM videos WHERE ID = '.$IDvideo);
}

/*
 getIDvideo: Devuelve el ID del video.
 */
function getIDvideo($IDcurso, $IDtema, $nombre, $ruta, $crearVideo) {
	global $db;

	if ( ($crearVideo == 1)&&($db->querySingle('SELECT COUNT(*) FROM videos WHERE nombre = "'.$nombre.'" AND IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema) == 0) ) {
		$orden = getNextOrdenVideo($IDcurso, $IDtema);

		crearVideo($IDcurso, $IDtema, $nombre, $nombre, $ruta, $img, ( $orden=='' ? 1 : $orden ), _OCULTO);
	}

	return $db->querySingle('SELECT ID FROM videos WHERE nombre = "'.$nombre.'" AND IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema);
}

/*
 * getVideoData: devuelve un array con toda la información de un vídeo:
 */
function getVideoData($IDcurso, $IDtema, $IDvideo) {
	global $db;
	
	$video = array();

	$res = $db->query('SELECT * FROM videos WHERE IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema.' AND ID = '.$IDvideo.' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		$video = array(
			'IDcurso' => $row['IDcurso'],
			'IDtema' => $row['IDtema'],
			'IDvideo' => $row['ID'],
			'nombre' => $row['nombre'],
			'descripcion' => $row['descripcion'],
			'ruta' => $row['ruta'],
			'img' => $row['img'],
			'orden' => $row['orden'],
			'ocultar' => $row['ocultar']
		);
	}

	return $video;
}

/*
 * getNextOrdenVideo: devuelve el siguiente orden en la tabla videos para un curso y tema
 */
function getNextOrdenVideo($IDcurso, $IDtema) {
	global $db;

	return $db->querySingle('SELECT MAX(orden)+1 FROM videos WHERE IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema);
}








function logAction($action) {
	global $dbLog;

	$dbLog->exec('INSERT INTO log (descripcion) VALUES ("'.$action.'")');
}


function videoPlayed($IDcurso, $IDtema, $IDvideo, $IDusuario) {
	global $dbAn;

	$dbAn->exec('INSERT INTO analytics (IDcurso, IDtema, IDvideo, IDusuario) VALUES ('.$IDcurso.','.$IDtema.','.$IDvideo.','.$IDusuario.')');
}



/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* -----------------------------------------------------    CONFIG    ----------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */


function getConfigData() {
	global $dbConfig;
	
	$config = array();
	
	$res = $dbConfig->query('SELECT * FROM adminvars');
	while ($row = $res->fetchArray()) {
		$config[$row['nombre']] = $row['valor'];
	}
	$config['listaUbicaciones'] = listaUbicaciones(1);
	$config['listaExtensiones'] = listaExtensiones(1);
	
	return $config;
}

function getAdminvar($varName) {
	global $dbConfig;

	return $dbConfig->querySingle('SELECT valor FROM adminvars WHERE nombre = "'.$varName.'"');
}

function crearAdminvar($nombre, $valor) {
	global $dbConfig;

	$dbConfig->exec('INSERT INTO adminvars (nombre, valor) VALUES ("'.$nombre.'", "'.$valor.'");');
}

function updateAdminvar($nombre, $valor) {
	global $dbConfig;

	$dbConfig->exec('UPDATE adminvars SET valor = "'.$valor.'" WHERE nombre = "'.$nombre.'";');
}

function deleteAdminvar($ID) {
	global $dbConfig;

	$dbConfig->exec('DELETE FROM adminvars WHERE ID = '.$ID.';');
}

function listaUbicaciones($returnID) {
	global $dbConfig;

	$ubicaciones = array();

	$res = $dbConfig->query('SELECT * FROM ubicaciones');
	while ($row = $res->fetchArray()) {
		if ($returnID == 1) {
			array_push($ubicaciones, array( 'ID' => $row['ID'], 'ruta' => $row['ruta']));
		} else {
			array_push($ubicaciones, $row['ruta']);
		}
	}

	return $ubicaciones;
}

function crearUbicacion($ruta) {
	global $dbConfig;

	if (checkUbicacion('ruta = "'.$ruta.'"') == 0) {
		$dbConfig->exec('INSERT INTO ubicaciones (ruta) VALUES ("'.$ruta.'");');
	}
}

function updateUbicacion($ID, $ruta) {
	global $dbConfig;

	$dbConfig->exec('UPDATE ubicaciones SET ruta = "'.$ruta.'" WHERE ID = '.$ID.';');
}

function deleteUbicacion($ID) {
	global $dbConfig;

	$dbConfig->exec('DELETE FROM ubicaciones WHERE ID = '.$ID.';');
}

function checkUbicacion($condicion) {
	global $dbConfig;
	
	$SQL = 'SELECT COUNT(*) FROM ubicaciones WHERE '.$condicion;

	$existe = $dbConfig->querySingle($SQL);
	
	return $existe;
}

function listaExtensiones($returnID) {
	global $dbConfig;

	$extensiones = array();

	$res = $dbConfig->query('SELECT * FROM extensionesValidas');
	while ($row = $res->fetchArray()) {
		if ($returnID == 1) {
			array_push($extensiones, array( 'ID' => $row['ID'], 'nombre' => $row['nombre']));
		} else {
			array_push($extensiones, $row['ruta']);
		}
	}

	return $extensiones;
}

function crearExtension($nombre) {
	global $dbConfig;

	if (checkExtension('nombre = "'.$nombre.'"') == 0) {
		$dbConfig->exec('INSERT INTO extensionesValidas (nombre) VALUES ("'.$nombre.'");');
	}
}

function updateExtension($ID, $nombre) {
	global $dbConfig;

	$dbConfig->exec('UPDATE extensionesValidas SET nombre = "'.$nombre.'" WHERE ID = '.$ID.';');
}

function deleteExtension($ID) {
	global $dbConfig;

	$dbConfig->exec('DELETE FROM extensionesValidas WHERE ID = '.$ID.';');
}


function checkExtension($condicion) {
	global $dbConfig;

	return $dbConfig->querySingle('SELECT COUNT(*) FROM extensionesValidas WHERE '.$condicion);
}



/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ----------------------------------------------------    USUARIOS    ---------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

/*
 crearUsuario: Crea un usuario
 */
function crearUsuario($fullname, $email, $bloqueado) {
	global $db;

	$db->exec('INSERT INTO usuarios (fullname, email, bloqueado) VALUES ("'.$fullname.'", "'.$email.'", '.$bloqueado.')');
}

/*
 bloquearUsuario: Cambia el estado de bloqueo de un usuario
 */
function bloquearUsuario($IDusuario, $bloqueado) {
	global $db;

	$db->exec('UPDATE usuarios SET bloqueado = '.$bloqueado.' WHERE ID = '.$IDusuario);
}

/*
 deleteUsuario: Elimina un usuario
 */
function deleteUsuario($IDusuario) {
	global $db;
	
	$db->exec('DELETE FROM cursosUsuarios WHERE IDusuario = '.$IDusuario);
	$db->exec('DELETE FROM usuarios WHERE ID = '.$IDusuario);
}

/*
 registrarUsuarioCurso: Registra una serie de usuarios en un curso
 */
function registrarUsuarioCurso($IDcurso, $IDcursoMoodle, $fullname, $email) {
	global $db;

	// Comprobar si el usuario existe en la BBDD:
	if ($db->querySingle('SELECT COUNT(*) FROM usuarios WHERE email = "'.$email.'"') == 0) {
		crearUsuario($fullname, $email, 0);
	}

	// Obtener el ID de usuario:
	$IDusuario = $db->querySingle('SELECT ID FROM usuarios WHERE email = "'.$email.'"');

	// Añadir usuario a curso:
	crearCursoUsuario($IDcurso, $IDcursoMoodle, $IDusuario);
}

/*
 desregistrarUsuariosCurso: Registra una serie de usuarios en un curso
 */
function desregistrarUsuariosCurso($IDcurso) {
	global $db;

	$db->exec('DELETE FROM cursosUsuarios WHERE IDcurso = '.$IDcurso);
}

/*
 getAllUsuarios: Obtener la lista completa de usuarios
 */
function getAllUsuarios() {
	global $db;
	
	$listaUsuarios = array();

	$res = $db->query('SELECT * FROM usuarios ORDER BY fullname');
	while ($row = $res->fetchArray()) {
		$cursos = array();

		$resCursos = $db->query('SELECT * FROM cursosUsuarios WHERE IDusuario = '.$row['ID']);
		while ($rowCursos = $resCursos->fetchArray()) {
			array_push($cursos, $rowCursos['IDcursoMoodle']);
		}
		
		array_push($listaUsuarios, array( 'ID' => $row['ID'], 'fullname' => $row['fullname'], 'email' => $row['email'], 'bloqueado' => $row['bloqueado'], 'cursos' => $cursos ));
	}

	return $listaUsuarios;
}

/*
 crearCursoUsuario: Crea un registro en la tabla cursosUsuarios
 */
function crearCursoUsuario($IDcurso, $IDcursoMoodle, $IDusuario) {
	global $db;
	
	if ($db->querySingle('SELECT COUNT(*) FROM cursosUsuarios WHERE IDcurso = '.$IDcurso.' AND IDcursoMoodle = '.$IDcursoMoodle.' AND IDusuario = '.$IDusuario) == 0) {
		$db->exec('INSERT INTO cursosUsuarios (IDcurso, IDcursoMoodle, IDusuario) VALUES ('.$IDcurso.', '.$IDcursoMoodle.', '.$IDusuario.')');
	}
}

/*
 deleteCursoUsuario: Elimina un registro de cursosUsuarios
 */
function deleteCursoUsuario($IDcurso, $IDcursoMoodle, $IDusuario) {
	global $db;

	$db->exec('DELETE FROM cursosUsuarios WHERE IDcurso = '.$IDcurso.' AND IDcursoMoodle = '.$IDcursoMoodle.' AND IDusuario = '.$IDusuario);
}

?>