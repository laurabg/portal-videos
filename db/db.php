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
		ubicacion INTEGER,
		ruta TEXT,
		descripcion TEXT,
		IDcursoMoodle INTEGER,
		fechaIni DATE,
		fechaFin DATE,
		publico TEXT);'
	);
	$db->exec('CREATE TABLE temas (
		ID INTEGER PRIMARY KEY, 
		nombre TEXT, 
		ruta TEXT,
		descripcion TEXT, 
		IDcurso INTEGER);'
	);
	$db->exec('CREATE TABLE videos (
		ID INTEGER PRIMARY KEY, 
		nombre TEXT, 
		ruta TEXT, 
		descripcion TEXT, 
		img TEXT, 
		IDtema INTEGER, 
		IDcurso INTEGER);'
	);
	$db->exec('CREATE TABLE usuarios (
		ID INTEGER PRIMARY KEY, 
		fullname TEXT, 
		email TEXT);'
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
function crearCurso($nombre, $ruta, $ubicacion, $descripcion, $IDcursoMoodle, $fechaIni, $fechaFin, $publico) {
	global $db;

	$SQL = 'INSERT INTO cursos (nombre, ruta, ubicacion';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ($IDcursoMoodle != '')?',IDcursoMoodle':'';
	$SQL .= ($fechaIni != '')?',fechaIni':'';
	$SQL .= ($fechaFin != '')?',fechaFin':'';
	$SQL .= ',publico';
	$SQL .= ') VALUES ("'.$nombre.'", "'.$ruta.'", '.$ubicacion;
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ($IDcursoMoodle != '')?','.$IDcursoMoodle:'';
	$SQL .= ($fechaIni != '')?',"'.$fechaIni.'"':'';
	$SQL .= ($fechaFin != '')?',"'.$fechaFin.'"':'';
	$SQL .= ',"'.$publico.'"';
	$SQL .= ')';
	
	$db->exec($SQL);
}

/*
 updateCurso: Crea un curso con los parámetros que se facilitan
 */
function updateCurso($IDcurso, $nombre, $ruta, $ubicacion, $descripcion, $IDcursoMoodle, $fechaIni, $fechaFin, $publico) {
	global $db;

	$SQL = 'UPDATE cursos SET ';
	$SQL .= 'nombre = "'.$nombre.'", ruta = "'.$ruta.'", ubicacion = '.$ubicacion;
	$SQL .= ($descripcion != '')?', descripcion = "'.$descripcion.'"':'';
	$SQL .= ($IDcursoMoodle != '')?', IDcursoMoodle = '.$IDcursoMoodle:'';
	$SQL .= ($fechaIni != '')?', fechaIni = "'.$fechaIni.'"':'';
	$SQL .= ($fechaFin != '')?', fechaFin = "'.$fechaFin.'"':'';
	$SQL .= ', publico = "'.$publico.'"';
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

	if ($crearCurso == 1) {
		if (checkCurso('ruta = "'.$ruta.'" AND ubicacion = '.$IDubicacion) == 0) {
			crearCurso($nombre, $ruta, $IDubicacion, '', '', '', '', 0);
		}
	}

	$IDcurso = $db->querySingle('SELECT ID FROM cursos WHERE ruta = "'.$ruta.'" AND ubicacion = '.$IDubicacion);

	return $IDcurso;
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
			'nombre' => $row['nombre'],
			'ruta' => $row['ruta'],
			'ubicacion' => $row['ubicacion'],
			'descripcion' => $row['descripcion'],
			'fechaIni' => $row['fechaIni'],
			'fechaFin' => $row['fechaFin'],
			'IDcursoMoodle' => $row['IDcursoMoodle'],
			'publico' => $row['publico']
		);
	}

	return $curso;
}

/*
 * getListaCursos: devuelve un array con todos los cursos:
 */
function getListaCursos() {
	global $db;
	
	$listaCursos = array();

	$res = $db->query('SELECT * FROM cursos');
	while ($row = $res->fetchArray()) {
		array_push($listaCursos, array($row['ID'], $row['nombre']));
	}

	return $listaCursos;
}


/*
 registrarUsuarioCurso: Registra una serie de usuarios en un curso
 */
function registrarUsuarioCurso($IDcurso, $IDcursoMoodle, $fullname, $email) {
	global $db;

	// Comprobar si el usuario existe en la BBDD:
	if ($db->querySingle('SELECT COUNT(*) FROM usuarios WHERE email = "'.$email.'"') == 0) {
		$db->exec('INSERT INTO usuarios (fullname, email) VALUES ("'.$fullname.'", "'.$email.'")');
	}

	// Obtener el ID de usuario:
	$IDusuario = $db->querySingle('SELECT ID FROM usuarios WHERE email = "'.$email.'"');

	// Añadir usuario a curso:
	if ($db->querySingle('SELECT COUNT(*) FROM cursosUsuarios WHERE IDcurso = '.$IDcurso.' AND IDcursoMoodle = '.$IDcursoMoodle.' AND IDusuario = '.$IDusuario) == 0) {
		$db->exec('INSERT INTO cursosUsuarios (IDcurso, IDcursoMoodle, IDusuario) VALUES ('.$IDcurso.', '.$IDcursoMoodle.', '.$IDusuario.')');
	}
}

/*
 desregistrarUsuariosCurso: Registra una serie de usuarios en un curso
 */
function desregistrarUsuariosCurso($IDcurso) {
	global $db;

	$db->exec('DELETE FROM cursosUsuarios WHERE IDcurso = '.$IDcurso);
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
 * getListaTemasByCurso: devuelve un array con todos los temas de un curso:
 */
function getListaTemasByCurso($IDcurso) {
	global $db;
	
	$listaTemas = array();

	$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.$IDcurso);
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

	$res = $db->query('SELECT * FROM videos WHERE IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema);
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
function crearTema($IDcurso, $nombre, $ruta, $descripcion) {
	global $db;

	$SQL = 'INSERT INTO temas (nombre, ruta, IDcurso';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ') VALUES ("'.$nombre.'","'.$ruta.'",'.$IDcurso;
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ')';
	//print $SQL;
	$db->exec($SQL);
}


/*
 updateTema: Actualiza un tema existente
 */
function updateTema($IDtema, $IDcurso, $nombre, $ruta, $descripcion) {
	global $db;

	$SQL = 'UPDATE temas SET nombre = "'.$nombre.'", ruta = "'.$ruta.'"';
	$SQL .= ($descripcion != '')?', descripcion = "'.$descripcion.'"':'';
	$SQL .= ' WHERE ID = '.$IDtema.' AND IDcurso = '.$IDcurso;
	
	$db->exec($SQL);
}

/*
 checkTema: Devuelve true si el tema existe, y false si no.
 */
function checkTema($condicion) {
	global $db;
	
	$SQL = 'SELECT COUNT(*) FROM temas WHERE '.$condicion;

	$existe = $db->querySingle($SQL);
	//print $SQL.' ('.$existe.')<br />';
	return $existe;
}


/*
 getIDtema: Devuelve ID tema por ruta e IDcurso.
 */
function getIDtema($IDcurso, $nombre, $ruta, $crearTema) {
	global $db;

	if (checkTema('ruta = "'.$ruta.'" AND IDcurso = '.$IDcurso) == 0) {
		if ($crearTema == 1) {
			crearTema($IDcurso, $nombre, $ruta, '');
		}
	}

	$IDtema = $db->querySingle('SELECT ID FROM temas WHERE ruta = "'.$ruta.'" AND IDcurso = '.$IDcurso);
	return $IDtema;
}

/*
 * getTemaData: devuelve un array con toda la información de un tema:
 */
function getTemaData($IDtema, $IDcurso) {
	global $db;
	
	$tema = array();

	$res = $db->query('SELECT * FROM temas WHERE ID = '.$IDtema.' AND IDcurso = '.$IDcurso);
	while ($row = $res->fetchArray()) {
		$tema = array(
			'nombre' => $row['nombre'],
			'ruta' => $row['ruta'],
			'descripcion' => $row['descripcion'],
			'IDcurso' => $row['IDcurso']
		);
	}

	return $tema;
}




/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* -----------------------------------------------------    VIDEOS    ----------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

/*
 crearVideo: Crea un video con los parámetros que se facilitan
 */
function crearVideo($IDcurso, $IDtema, $nombre, $descripcion, $ruta) {
	global $db;

	$SQL = 'INSERT INTO videos (nombre, IDcurso, IDtema';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ($ruta != '')?',ruta':'';
	$SQL .= ') VALUES ("'.$nombre.'",'.$IDcurso.','.$IDtema;
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ($ruta != '')?',"'.$ruta.'"':'';
	$SQL .= ')';
	
	$db->exec($SQL);
}


/*
 updateVideo: Actualiza un video existente
 */
function updateVideo($IDvideo, $IDcurso, $IDtema, $nombre, $descripcion) {
	global $db;

	$SQL = 'UPDATE videos SET ';
	$SQL .= 'nombre = "'.$nombre.'"';
	$SQL .= ($descripcion != '')?', descripcion = "'.$descripcion.'"':'';
	$SQL .= ' WHERE ID = '.$IDvideo.' AND IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema;
	
	$db->exec($SQL);
}

/*
 checkVideo: Devuelve true si el video existe, y false si no.
 */
function checkVideo($condicion) {
	global $db;
	
	$SQL = 'SELECT COUNT(*) FROM videos WHERE '.$condicion;

	$existe = $db->querySingle($SQL);
	//print $SQL.' ('.$existe.')<br />';
	return $existe;
}


function getIDvideo($IDcurso, $IDtema, $nombre, $ruta, $crearVideo) {
	global $db;

	if ($crearVideo == 1) {
		if ($db->querySingle('SELECT COUNT(*) FROM videos WHERE nombre = "'.$nombre.'" AND IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema) == 0) {
			crearVideo($IDcurso, $IDtema, $nombre, '', $ruta);
		}
	}

	$IDvideo = $db->querySingle('SELECT ID FROM videos WHERE nombre = "'.$nombre.'" AND IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema);
	return $IDvideo;
}

/*
 * getVideoData: devuelve un array con toda la información de un vídeo:
 */
function getVideoData($IDvideo, $IDtema, $IDcurso) {
	global $db;
	
	$video = array();

	$res = $db->query('SELECT * FROM videos WHERE ID = '.$IDvideo.' AND IDtema = '.$IDtema.' AND IDcurso = '.$IDcurso);
	while ($row = $res->fetchArray()) {
		$video = array(
			'nombre' => $row['nombre'],
			'descripcion' => $row['descripcion'],
			'IDcurso' => $row['IDcurso'],
			'IDtema' => $row['IDtema']
		);
	}

	return $video;
}

function updateVideoIMG($IDvideo, $img) {
	global $db;

	$db->exec('UPDATE videos SET img = "'.$img.'" WHERE ID = '.$IDvideo.';');
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

?>