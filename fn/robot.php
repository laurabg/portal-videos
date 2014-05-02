<?php

require("config.php");

$dbcon = dbConnection();

// Comprobar si la tabla existe:
createTable($dbcon, "cursos", $SQLCreateCursos);
createTable($dbcon, "videos", $SQLCreateVideos);
createTable($dbcon, "logVideos", $SQLCreateLogVideos);

logAction($dbcon, "Inicio robot.php", "PROC");

readDirCursos();

// Leer todos los archivos de la carpeta:
//getDirInfo($dbcon, _DIRCURSOS);

// Listar los nuevos vídeos
//listVideos($dbcon);

phpinfo();

    if (! extension_loaded (ffmpeg)) exit ("ffmpeg was not loaded");


echo "Ok1";

extension_loaded('ffmpeg');
$movie_file = _DIRCURSOS."/ingenieria-del-software/videos/01-el-alquimista-de-acero.mp4";
// Instantiates the class ffmpeg_movie so we can get the information you want the video  
$movie = new ffmpeg_movie($movie_file);  

//exec("ffmpeg -i "._DIRCURSOS."/ingenieria-del-software/videos/01-el-alquimista-de-acero.mp4 -ss 0 -vframes 1 "._DIRCURSOS."/ingenieria-del-software/capturas/shot.png");

echo "Ok2";
echo $video;

logAction($dbcon, "Fin robot.php", "PROC");

// Cerrar la conexión a la bbdd:
dbDisconnect($dbcon);
?>