<?php


/*********************************************************************
 buscarCursos: Rastrea una ruta en busca de cursos
 Parámetros:
	dir				Ruta a rastrear
 *********************************************************************/
function buscarCursos($dir) {
	logAction("Buscando cursos en ".$dir);
	
	$IDcurso = 0;
	$cont = 0;

	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != "..") {
				if (is_dir($dir."/".$filename)) {
					$cont++;

					// Limpiar el nombre de la carpeta de caracteres extraños y espacios
					$filenameNEW = clean($filename);
					rename($dir."/".$filename, $dir."/".$filenameNEW);
					
					// Guardar el curso en la BBDD:
				//	echo "Encontrado curso ".$filenameNEW."<br />";
					logAction("Encontrado curso ".$dir."/".$filename.". Renombrado a ".$filenameNEW);
					$IDcurso = getIDcurso($filename);

					// Buscar temas dentro del curso:
					buscarTemas($IDcurso, $dir.$filenameNEW);
				} else {
					logAction($dir."/".$filename." no se procesará, ya que no es un directorio");
				//	echo "Los ficheros dentro de cursos no se procesarán <br />";
				}
			}
		}

		if ($cont == 0) {
			logAction($dir." no contiene cursos");
		//	echo "No existen cursos<br />";
		}
	} else {
		logAction("Error al leer ".$dir);
	//	echo "Error al leer de ".$dir."<br />";
	}
}



/*********************************************************************
 buscarTemas: Rastrea la ruta de un curso en busca de temas
 Parámetros:
	IDcurso				ID del curso
	dir					Ruta del tema
 *********************************************************************/
function buscarTemas($IDcurso, $dir) {
	logAction("Buscando tema en ".$dir);

	$IDtema = 0;
	$cont = 0;

	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != "..") {
				// Si se trata de una carpeta:
				if (is_dir($dir."/".$filename)) {
					$cont++;

					// Limpiar el nombre de la carpeta de caracteres extraños y espacios
					$filenameNEW = clean($filename);
					rename($dir."/".$filename, $dir."/".$filenameNEW);
					
					// Guardar el tema
				//	echo "&nbsp;&nbsp;&nbsp;Encontrado tema ".$filename."<br />";
					logAction("Encontrado tema ".$dir."/".$filename.". Renombrado a ".$filenameNEW);
					$IDtema = getIDtema($IDcurso, $filename);

					// Comprobar si existen las siguientes carpetas; sino crearlas:
					if (!file_exists($dir."/".$filenameNEW."/img")) {
						createDir($dir."/".$filenameNEW."/img");
					}
					if (!file_exists($dir."/".$filenameNEW."/docs")) {
						createDir($dir."/".$filenameNEW."/docs");
					}
					buscarVideos($IDcurso, $IDtema, $dir."/".$filenameNEW);
				} else {
					logAction($dir."/".$filename." no se procesará, ya que no es un directorio");
				//	echo "&nbsp;&nbsp;&nbsp;Los ficheros dentro de un curso no se procesarán <br />";
				}
			}
		}

		if ($cont == 0) {
			logAction($dir." no contiene temas");
		//	echo "&nbsp;&nbsp;&nbsp;No existen temas para ".$dir."<br />";
		}
	} else {
		logAction("Error al leer ".$dir);
	//	echo "&nbsp;&nbsp;&nbsp;Error al leer de ".$dir."<br />";
	}
}



/*********************************************************************
 buscarVideos: Rastrea la ruta de un tema en busca de vídeos
 Parámetros:
	IDcurso				ID del curso
	IDtema				ID del tema
	dir					Ruta del vídeo
 *********************************************************************/
function buscarVideos($IDcurso, $IDtema, $dir) {
	global $extensionesValidas;

	$cont = 0;
	$ubicacion = str_replace(_DOCUMENTROOT._DIRCURSOS, '', $dir);

	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != "..") {
				// Si existe la carpeta de INBOX, leer su contenido:
				if (is_dir($dir."/".$filename)) {
					logAction($dir."/".$filename." no se procesará, ya que no es un archivo");
					//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Las carpetas dentro de un tema no se procesarán <br />";
				} else {
					// Comprobar si el archivo tiene una extensión válida:
					$extension = pathinfo($dir."/".$filename, PATHINFO_EXTENSION);
					if (in_array($extension, $extensionesValidas)) {
						$cont++;
						
						// Limpiar el nombre de la carpeta de caracteres extraños y espacios
						$filenameNEW = clean($filename);
						rename($dir."/".$filename, $dir."/".$filenameNEW);

						// Guardar el vídeo:
					//	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".$IDcurso." - ".$IDtema.") Encontrado vídeo ".$filename."<br />";
						logAction("Encontrado vídeo ".$dir."/".$filename.". Renombrado a ".$filenameNEW);
						$IDvideo = getIDvideo($IDcurso, $IDtema, $filename, $ubicacion."/".$filenameNEW);

						$img = getPortada($filenameNEW, $dir);
						
						if ($img != '') {
							updateVideo($IDvideo, $ubicacion."/img/".$img);
						}
					//	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$ubicacion."/".$filenameNEW."<br />";
					//	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$ubicacion."/img/".$img."<br />";
					} else {
						logAction($dir."/".$filename." no tiene una extensión válida");
					}
				}
			}
		}

		if ($cont == 0) {
			logAction($dir." no contiene vídeos");
		//	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No existen vídeos para ".$dir."<br />";
		}
	} else {
		logAction("Error al leer ".$dir);
	//	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Error al leer de ".$dir."<br />";
	}
}


function getPortada($nombre, $ruta) {
	//echo $nombre."<br />";
	//echo $ruta."<br />";

	$ffmpeg = "/usr/bin/ffmpeg";
	$video = $ruta."/".$nombre;
	$img = $ruta."/img/".str_replace(".mp4","",$nombre).".jpg";
	$cmd = "$ffmpeg -i ".$video." -ss 3 -vframes 1 -f image2 ".$img;
	//echo "<br />".$cmd."<br /><br />";
	
	if (!shell_exec($cmd)) {
		chmod($img, 0777);
		
		logAction("Portada para ".$nombre." obtenida");
	//	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OK!!!<br />";
	} else {
		logAction("No se ha podido obtener la portada para ".$nombre);
	//	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ooohhh....<br />";
	}

	return str_replace($ruta."/img/","",$img);
}



/*********************************************************************
 createDir: Crea un directorio en la ruta indicada, con permisos 777
 Parámetros:
	rutaDir				Ruta + nombre del directorio a crear
 *********************************************************************/
 function createDir($rutaDir) {
	mkdir($rutaDir);
	chmod($rutaDir, 0777);

	logAction("Creada carpeta ".$rutaDir);
}


/*********************************************************************
 clean: Elimina todos los caracteres no deseados de un string
 Parámetros:
	string				Cadena de texto a limpiar
 *********************************************************************/
 function clean($string) {
	$string = strtolower($string);
	
	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	
	$string = str_replace('á', 'a', $string); // Replaces á with a
	$string = str_replace('é', 'e', $string); // Replaces é with e
	$string = str_replace('í', 'i', $string); // Replaces í with i
	$string = str_replace('ó', 'o', $string); // Replaces ó with o
	$string = str_replace('ú', 'u', $string); // Replaces ú with u
	$string = str_replace('ü', 'u', $string); // Replaces ü with u
	$string = str_replace('ñ', 'n', $string); // Replaces ñ with n

	return preg_replace('/[^A-Za-z0-9\-\.]/', '', $string); // Removes special chars.
}



/*********************************************************************
		 						ROBOT.php
 *********************************************************************/
$db = null;
$dbLog = null;

include_once('../config.php');
include_once(_DOCUMENTROOT.'db/db.php');

dbCreate(_BBDD);
dbLogCreate(_BBDDLOG);

if ( (isset($_GET['rehacer']))&&($_GET['rehacer'] == 1) ) {
	resetDB();
	resetDBLog();
}

logAction("Inicio Robot");

foreach ($listaDirs as $dir) {
	logAction("Analizando ".$dir);
	// Comprobar si se encuentra en la misma ruta que los cursos:
	if (!is_dir(_DOCUMENTROOT._DIRCURSOS."/".$dir)) {
		logAction($dir." no se encuentra en la misma ruta que el portal.");

		// Si está en otra dirección, crear un enlace:
		$link = split("/", $dir);
		$link = $link[count($link)-2];

		if (!is_link(_DOCUMENTROOT._DIRCURSOS.$link)) {
			logAction("Creando link de ".$dir." en "._DOCUMENTROOT._DIRCURSOS.$link);
		//	echo $dir." - ".$link."<br />";
			symlink($dir, _DOCUMENTROOT._DIRCURSOS.$link);
			$dir = $link;
		}

		buscarCursos(_DOCUMENTROOT._DIRCURSOS.$link."/");
	} else {
		buscarCursos(_DOCUMENTROOT._DIRCURSOS.$dir);
	}
}

logAction("Fin Robot");

?>