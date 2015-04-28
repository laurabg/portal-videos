<?php

// Definición de constantes
define("_PORTALROOT", "/portal-videos/");
define("_DOCUMENTROOT", $_SERVER["DOCUMENT_ROOT"]._PORTALROOT);
define("_BBDD", _DOCUMENTROOT."db/dbportalvideos.db");
define("_BBDDLOG", _DOCUMENTROOT."db/dblog.db");
define("_BBDDANALYTICS", _DOCUMENTROOT."db/analytics.db");
define("_DIRCURSOS", "data/");

define("_WSTOKEN", "17b86aa656d4e44ce43f8146fb75dd05");
define("_MOODLEURL", "http://localhost/moodle");

// Lista de extensiones válidas:
$extensionesValidas = array("mp4");

// Lista de directorios desde los que leer los cursos:
$listaDirs = array('cursos/');

?>