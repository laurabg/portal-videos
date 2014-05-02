<?php


/*********************************************************************
 dbConnection: Conecta a una base de datos
 *********************************************************************/
function dbConnection() {
	// Conectarse a bbdd (si no existe, la creará):
	$dbcon = sqlite_popen(_BBDD, 0666, $err);
	if (!$dbcon) {
		die ($err);
	}
	return $dbcon;
}


/*********************************************************************
 dbDisconnect: Desconecta una base de datos
 Parámetros:
	dbcon				Conexión a la base de datos
 *********************************************************************/
function dbDisconnect($dbcon) {
	sqlite_close($dbcon);
}


/*********************************************************************
 createTable: Comprueba si una tabla existe, sino, la crea
 Parámetros:
	dbcon				Conexión a la base de datos
	tableName			Nombre de la tabla
	SQLCreateTable		Instrucción para crear la tabla
 *********************************************************************/
function createTable($dbcon, $tableName, $SQLCreateTable) {
	$SQL = "SELECT name FROM sqlite_master WHERE type = 'table' AND name = '".$tableName."'";
	$res = sqlite_query($dbcon, $SQL);
	if (!$res) {
		die ("Cannot execute query<br />.$SQL");
	}
	
	// Si ls tabla no existe, crearla:
	if (sqlite_num_rows($res) == 0) {
		$ok = sqlite_exec($dbcon, $SQLCreateTable);
		if (!$ok) {
			die ("Cannot execute query<br />$SQL");
		}
	}
}


/*********************************************************************
 addCurso: Añade un registro en la tabla "cursos" si no existe
 Parámetros:
	dbcon				Conexión a la base de datos
	dirName				Nombre del directorio
 *********************************************************************/
 function addCurso($dbcon, $fileDate, $fileName, $fileDir) {
	$sc = 1;
	
	$SQL = "SELECT * FROM cursos WHERE id = '".$fileDate."' AND nombre = '".$fileName."' AND ruta = '".$fileDir."'";
	$res = sqlite_query($dbcon, $SQL);
	
	if (!$res) {
		die ("Cannot execute query for cursos<br />$SQL");
	} else if (sqlite_num_rows($res) == 0) {
		$SQL = "INSERT INTO cursos (id, nombre, ruta) VALUES ('".$fileDate."', '".$fileName."', '".$fileDir."')";
		$sc = sqlite_exec($dbcon, $SQL);
		if (!$sc) {
			die ("Cannot execute Insert curso<br />$SQL");
		}
		logAction($dbcon, "Insertado curso ".$fileName, "SQL");
	}
	
	return $sc;
	/*$listaVal = stat($dir."/".$filename);
	foreach ($listaVal as &$valor) {
		echo $valor."<br />";
	}*/
}


/*********************************************************************
 addVideo: Añade un registro en la tabla "videos" si no existe
 Parámetros:
	dbcon				Conexión a la base de datos
	fileDate			Identificador del archivo (fecha modificación)
	fileName			Nombre del archivo
	fileDir				Ruta del archivo
 *********************************************************************/
function addVideo($dbcon, $IDcurso, $fileDate, $fileName, $fileDir) {
	$sc = 1;
	
	$SQL = "SELECT * FROM videos WHERE id = '".$fileDate."' AND nombre = '".$fileName."' AND ruta = '".$fileDir."'";
	$res = sqlite_query($dbcon, $SQL);
	
	if (!$res) {
		die ("Cannot execute query<br />$SQL");
	} else if (sqlite_num_rows($res) == 0) {
		$SQL = "INSERT INTO videos (id, nombre, ruta, curso) VALUES ('".$fileDate."', '".$fileName."', '".$fileDir."', '".$IDcurso."')";
		$sc = sqlite_exec($dbcon, $SQL);
		if (!$sc) {
			die ("Cannot execute Insert<br />$SQL");
		}
		logAction($dbcon, "Insertado vídeo ".$fileName, "SQL");
	}
	
	return $sc;
}


/*********************************************************************
 logAction: Añade un registro a la tabla "logVideos!
 Parámetros:
	dbcon				Conexión a la base de datos
	content				Descripción de la acción
	tipo				Tipo de acción
 *********************************************************************/
 function logAction($dbcon, $desc, $tipo) {
	$SQL = "INSERT INTO logVideos (id, log, tipo) VALUES ('".date("Y-m-d H:i:s").substr((string)microtime(), 1, 8)."', '".$desc."','".$tipo."')";
	$sc = sqlite_exec($dbcon, $SQL);
	if (!$sc) {
		die ("Cannot execute Insert Log<br />$SQL");
	}
}


/*********************************************************************
 clean: Elimina todos los caracteres no deseados de un string
 Parámetros:
	string				Cadena de texto a limpiar
 *********************************************************************/
 function clean($string) {
	$string = strtolower($string);
	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	return preg_replace('/[^A-Za-z0-9\-\.]/', '', $string); // Removes special chars.
}


function readDirCursos() {
	if ($handle = opendir(_DIRCURSOS)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != ".." && is_dir(_DIRCURSOS."/".$filename)) {
				// Si existe la carpeta de INBOX, leer su contenido:
				if (is_dir(_DIRCURSOS."/".$filename."/inbox")) {
					readDirCursosINBOX($filename);
				}
			}
		}
	}
}

function readDirCursosINBOX($dir) {
	$dbcon = dbConnection();
	
	global $extensionesValidas;
	
	// Obtener el ID del curso:
	$IDcurso = getIDcurso($dbcon, $dir);
	
	if ($handle = opendir(_DIRCURSOS."/".$dir."/inbox")) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != ".." && !is_dir(_DIRCURSOS."/".$dir."/inbox/".$filename)) {
				// Comprobar que se trata de un vídeo:
				$extension = pathinfo(_DIRCURSOS."/".$dir."/inbox/".$filename, PATHINFO_EXTENSION);
				if (in_array($extension, $extensionesValidas)) {
					// Limpiar el nombre del archivo para renombrarlo:
					$cleanFilename = clean($filename);
					
					// Mover el archivo a la carpeta vídeos::
					$rutaORI = _DIRCURSOS."/".$dir."/inbox/".$filename;
					$rutaNEW = _DIRCURSOS."/".$dir."/videos/".$cleanFilename;
					
					// Comprobar que el archivo no exista en el destino:
					if (!file_exists($rutaNEW)) {
						rename($rutaORI, $rutaNEW);
						
						$fileDate = date("Y-m-d H:i:s", filemtime(_DIRCURSOS."/".$dir."/videos/".$cleanFilename));
						addVideo($dbcon, $IDcurso, $fileDate, $cleanFilename, $dir."/videos");
					}
				}
			}
		}
	}
	
	dbDisconnect();
}


function getIDcurso($dbcon, $dir) {
	$IDcurso = 0;
	
	$SQL = "SELECT ID FROM cursos WHERE ruta = '".$dir."'";
	$res = sqlite_query($dbcon, $SQL);
	
	if (!$res) {
		die ("Cannot execute query<br />$SQL");
	} else if (sqlite_num_rows($res) > 0) {
		$IDcurso = sqlite_fetch_single($res);
	}
	
	return $IDcurso;
}


/*********************************************************************
 getNewVideos: Lee los archivos de una carpeta, e inserta los nuevos
 en una tabla
 Parámetros:
	dbcon				Conexión a la base de datos
	dir					Directorio donde se encuentran los archivos
	extensionesValidas	Lista de extensiones válidas para los vídeos
 *********************************************************************/
function getDirInfo($dbcon, $dir) {
	// Leer los archivos del directorio
	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != "..") {
				$fileFullDir = $dir."/".$filename;
				$fileDir = str_replace($_SERVER["DOCUMENT_ROOT"],"", $fileFullDir);
				$fileDate = date("Y-m-d H:i:s", filemtime($fileFullDir));
				
				// Si es un directorio, añadirlo a cursos:
				if (is_dir($fileFullDir)) {
					addCurso($dbcon, $fileDate, $filename, $fileDir);
					
					// Si existe la carpeta INBOX, rastrear los vídeos nuevos:
					if (is_dir($fileFullDir."/inbox")) {
						getDirInfo($dbcon, $fileFullDir."/inbox");
					}
				} else {
					// Añadir vídeos a la BBDD:
					addVideosBBDD($dir, $filename);
				}
			}
		}
		closedir($handle);
	} else {
		echo "No se puede abrir el directorio ".$dir;
	}
}


function addVideosBBDD($dir, $filename) {
	// Comprobar que se trata de un vídeo:
	$fileElements = split("\.", $filename);
	$pos = count($fileElements) - 1;
	$extension = $fileElements[$pos];
	
	// Si se trata de un vídeo válido, moverlo a la carpeta "videos" e insertar registro en BBDD:
	if (in_array($extension, _EXTENSIONESVALIDAS)) {
		// Limpiar el nombre del archivo para renombrarlo:
		$cleanFilename = clean($filename);
		
		// Mover el archivo a la carpeta vídeos::
		$cleanFileFullDir = $dir."/".$cleanFilename;
		$cleanFileFullDir = str_replace("/inbox", "/videos", $cleanFileFullDir);
		$cleanFileDir = str_replace($_SERVER["DOCUMENT_ROOT"],"", $cleanFileFullDir);
		
		// Comprobar que el archivo no exista en el destino:
		if (!file_exists($cleanFileFullDir)) {
			rename($fileFullDir, $cleanFileFullDir);
			
			// Añadir los datos a la bbdd:
			addVideo($dbcon, $fileDate, $cleanFilename, $cleanFileDir);
		} else {
			logAction($dbcon, "El archivo ".$fileDir." ya existe en ".$cleanFileDir, "VIDEODUP");
		}
	}
}
?>