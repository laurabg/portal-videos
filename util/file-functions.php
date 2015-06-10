<?php


/*
 getRutaOrSymlink: Obtiene el nombre del directorio o link simbolico de una carpeta
 Parámetros:
	ruta 				Directorio donde se encuentra la carpeta
	dir 				Nombre de la carpeta
 */
function getRutaOrSymlink($ruta, $dir) {
	if (!is_dir($ruta."/".$dir)) {
		// Si está en otra dirección, crear un enlace:
		$link = explode("/", $dir);
		$link = $link[count($link)-2];

		if (!is_link($ruta.$link)) {
			symlink($dir, $ruta.$link);
		}
		return $link.'/';
	} else {
		return $dir;
	}
}

/*
 getPortada: Obtiene la imagen de portada de un video
 Parámetros:
	nombre				Nombre del video
	ruta 				Directorio donde se encuentra el video
 */
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
	} else {
	}

	return str_replace($ruta."/img/","",$img);
}


/*
 createDir: Crea un directorio en la ruta indicada, con permisos 777
 Parámetros:
	rutaDir				Ruta + nombre del directorio a crear
 */
 function createDir($rutaDir) {
	mkdir($rutaDir);
	chmod($rutaDir, 0777);
}


/*
 removeDir: Elimina un directorio y todos sus archivos recursivamente
 Parámetros:
	rutaDir				Ruta + nombre del directorio a borrar
 */
function removeDir($rutaDir) { 
	if (is_dir($rutaDir)) { 
		$objects = scandir($rutaDir); 
		foreach ($objects as $object) { 
			if ($object != "." && $object != "..") { 
				if (filetype($rutaDir."/".$object) == "dir") {
					removeDir($rutaDir."/".$object);
				} else {
					unlink($rutaDir."/".$object); 
				}
			} 
		} 
		reset($objects); 
		rmdir($rutaDir); 
	} 
} 



/*
 removeFile: Elimina un fichero
 Parámetros:
	rutaFile			Ruta + nombre del archivo a borrar
 */
function removeFile($rutaFile) { 
	if ( (!is_dir($rutaFile))&&(file_exists($rutaFile)) ) { 
		unlink($rutaFile); 
	} 
} 


/*
 clean: Elimina todos los caracteres no deseados de un string
 Parámetros:
	string				Cadena de texto a limpiar
 */
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

?>