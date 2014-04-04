<?php

require("config.php");
require("functions.php");

// Conectarse a bbdd (si no existe, la creará):
$dbcon = sqlite_popen($bbdd, 0666, $err);
if (!$dbcon) {
	die ($err);
}

// Comprobar si la tabla existe:
createTable($dbcon, "cursos", $SQLCreateCursos);
createTable($dbcon, "videos", $SQLCreateVideos);
createTable($dbcon, "logVideos", $SQLCreateLogVideos);

logAction($dbcon, "Inicio robot.php", "PROC");

// Leer todos los archivos de la carpeta:
getDirInfo($dbcon, $dirVideos, $extensionesValidas);

// Listar los nuevos vídeos
//listVideos($dbcon);

logAction($dbcon, "Fin robot.php", "PROC");

// Cerrar la conexión a la bbdd:
sqlite_close($dbcon);
?>