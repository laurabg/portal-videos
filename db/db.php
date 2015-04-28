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
		IDcursoMoodle INTEGER,
		fechaIni DATE,
		fechaFin DATE,
		publico TEXT);'
	);
	$db->exec('CREATE TABLE temas (
		ID INTEGER PRIMARY KEY, 
		nombre TEXT, 
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
		email TEXT, 
		IDcurso INTEGER,
		IDcursoMoodle INTEGER);'
	);
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


/*
 crearCurso: Crea un curso con los parámetros que se facilitan
 */
function crearCurso($nombreCurso, $descripcion, $IDcursoMoodle, $fechaIni, $fechaFin, $publico) {
	global $db;

	$SQL = 'INSERT INTO cursos (nombre';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ($IDcursoMoodle != '')?',IDcursoMoodle':'';
	$SQL .= ($publico != '')?',publico':'';
	$SQL .= ($fechaIni != '')?',fechaIni':'';
	$SQL .= ($fechaFin != '')?',fechaFin':'';
	$SQL .= ') VALUES ("'.$nombreCurso.'"';
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ($IDcursoMoodle != '')?','.$IDcursoMoodle:'';
	$SQL .= ($publico != '')?',"'.$publico.'"':'';
	$SQL .= ($fechaIni != '')?',"'.$fechaIni.'"':'';
	$SQL .= ($fechaFin != '')?',"'.$fechaFin.'"':'';
	$SQL .= ')';
	
	$db->exec($SQL);
}


/*
 updateCurso: Crea un curso con los parámetros que se facilitan
 */
function updateCurso($IDcurso, $nombreCurso, $descripcion, $IDcursoMoodle, $fechaIni, $fechaFin, $publico) {
	global $db;

	$SQL = 'UPDATE cursos SET ';
	$SQL .= 'nombre = "'.$nombreCurso.'"';
	$SQL .= ($descripcion != '')?', descripcion = "'.$descripcion.'"':'';
	$SQL .= ($IDcursoMoodle != '')?', IDcursoMoodle = '.$IDcursoMoodle:'';
	$SQL .= ($publico != '')?', publico = "'.$publico.'"':'';
	$SQL .= ($fechaIni != '')?', fechaIni = "'.$fechaIni.'"':'';
	$SQL .= ($fechaFin != '')?', fechaFin = "'.$fechaFin.'"':'';
	$SQL .= ' WHERE ID = '.$IDcurso;
	
	$db->exec($SQL);
}

/*
 checkCurso: Devuelve true si el curso existe, y false si no.
 */
function checkCurso($condicion) {
	global $db;
	
	$SQL = 'SELECT COUNT(*) FROM cursos WHERE '.$condicion;

	$existe = $db->querySingle($SQL);
	//print $SQL.' ('.$existe.')<br />';
	return $existe;
}


/*
 registrarUsuarioCurso: Registra una serie de usuarios en un curso
 */
function registrarUsuarioCurso($IDcurso, $IDcursoMoodle, $email) {
	global $db;

	$SQL = 'INSERT INTO usuarios (IDcurso, IDcursoMoodle, email) VALUES ('.$IDcurso.', '.$IDcursoMoodle.', "'.$email.'")';
	//print $SQL.'<br />';
	$db->exec($SQL);
}


/*
 desregistrarUsuariosCurso: Registra una serie de usuarios en un curso
 */
function desregistrarUsuariosCurso($IDcurso) {
	global $db;

	$SQL = 'DELETE FROM usuarios WHERE IDcurso = '.$IDcurso;
	//print $SQL.'<br />';
	$db->exec($SQL);
}


function getIDcurso($nombreCurso, $crearCurso) {
	global $db;

	if ($crearCurso == 1) {
		if ($db->querySingle('SELECT COUNT(*) FROM cursos WHERE nombre = "'.$nombreCurso.'"') == 0) {
			crearCurso($nombreCurso, '', '', '', '', '');
		}
	}

	$IDcurso = $db->querySingle('SELECT ID FROM cursos WHERE nombre = "'.$nombreCurso.'"');
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


/*
 crearTema: Crea un tema con los parámetros que se facilitan
 */
function crearTema($IDcurso, $nombreTema, $descripcion) {
	global $db;

	$SQL = 'INSERT INTO temas (nombre, IDcurso';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ') VALUES ("'.$nombreTema.'",'.$IDcurso;
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
	$SQL .= ')';
	print $SQL;
	$db->exec($SQL);
}


/*
 updateTema: Actualiza un tema existente
 */
function updateTema($IDtema, $IDcurso, $nombreTema, $descripcion) {
	global $db;

	$SQL = 'UPDATE temas SET ';
	$SQL .= 'nombre = "'.$nombreTema.'"';
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


function getIDtema($IDcurso, $nombreTema, $crearTema) {
	global $db;

	if ($crearTema == 1) {
		if ($db->querySingle('SELECT COUNT(*) FROM temas WHERE nombre = "'.$nombreTema.'" AND IDcurso = '.$IDcurso) == 0) {
			crearTema($IDcurso, $nombreTema, '');
		}
	}

	$IDtema = $db->querySingle('SELECT ID FROM temas WHERE nombre = "'.$nombreTema.'" AND IDcurso = '.$IDcurso);
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
			'descripcion' => $row['descripcion'],
			'IDcurso' => $row['IDcurso']
		);
	}

	return $tema;
}



/*
 crearVideo: Crea un video con los parámetros que se facilitan
 */
function crearVideo($IDcurso, $IDtema, $nombre, $descripcion) {
	global $db;

	$SQL = 'INSERT INTO videos (nombre, IDcurso, IDtema';
	$SQL .= ($descripcion != '')?',descripcion':'';
	$SQL .= ') VALUES ("'.$nombre.'",'.$IDcurso.','.$IDtema;
	$SQL .= ($descripcion != '')?',"'.$descripcion.'"':'';
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


function getIDvideo($IDcurso, $IDtema, $nombre, $crearVideo) {
	global $db;

	if ($crearVideo == 1) {
		if ($db->querySingle('SELECT COUNT(*) FROM videos WHERE nombre = "'.$nombre.'" AND IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema) == 0) {
			crearVideo($IDcurso, $IDtema, $nombre, '');
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

/*
function getIDvideo($IDcurso, $IDtema, $nombre, $ruta) {
	global $db;

	if ($db->querySingle('SELECT COUNT(*) FROM videos WHERE nombre = "'.$nombre.'" AND IDtema = '.$IDtema) == 0) {
	//	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crear video ".$nombre."<br />";
		$db->exec('INSERT INTO videos (nombre, descripcion, ruta, IDtema, IDcurso) VALUES ("'.$nombre.'", "Descripción del vídeo '.$nombre.'", "'.$ruta.'", '.$IDtema.', '.$IDcurso.')');
	} else {
	//	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;El video ".$nombre." existe<br />";
	}

	$IDvideo = $db->querySingle('SELECT ID FROM videos WHERE nombre = "'.$nombre.'" AND IDtema = '.$IDtema.' AND IDcurso = '.$IDcurso);
	return $IDvideo;
}

function updateVideo($IDvideo, $img) {
	global $db;

	$db->exec('UPDATE videos SET img = "'.$img.'" WHERE ID = '.$IDvideo.';');
}


function checkTema($nombre, $IDcurso) {
	global $db;

	$existe = $db->querySingle('SELECT COUNT(*) FROM temas WHERE nombre = "'.$nombre.'" AND IDcurso = '.$IDcurso);
	return $existe;
}

function checkVideo($nombre, $IDtema, $IDcurso) {
	global $db;

	$existe = $db->querySingle('SELECT COUNT(*) FROM videos WHERE nombre = "'.$nombre.'" AND IDcurso = '.$IDcurso.' AND IDtema = '.$IDtema);
	return $existe;
}*/


function logAction($action) {
	global $dbLog;

	$dbLog->exec('INSERT INTO log (descripcion) VALUES ("'.$action.'")');
}


function videoPlayed($IDcurso, $IDtema, $IDvideo, $IDusuario) {
	global $dbAn;

	$dbAn->exec('INSERT INTO analytics (IDcurso, IDtema, IDvideo, IDusuario) VALUES ('.$IDcurso.','.$IDtema.','.$IDvideo.','.$IDusuario.')');
}

?>