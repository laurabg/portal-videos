<?php

// Definición de constantes
define("_PORTALROOT", "/portal-videos/");
define("_DOCUMENTROOT", $_SERVER["DOCUMENT_ROOT"]._PORTALROOT);
define("_BBDD", _DOCUMENTROOT."db/dbportalvideos.db");
define("_BBDDCONFIG", _DOCUMENTROOT."db/dbconfig.db");
define("_BBDDLOG", _DOCUMENTROOT."db/dblog.db");
define("_BBDDANALYTICS", _DOCUMENTROOT."db/analytics.db");
define("_DIRCURSOS", "data/");

include_once(_DOCUMENTROOT.'db/db.php');

$dbConfig = null;
dbConfigCreate(_BBDDCONFIG);

if (getAdminvar('showErrors') == 1) {
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

define("_WSTOKEN", getAdminvar('_WSTOKEN'));
define("_MOODLEURL", getAdminvar('_MOODLEURL'));

//define("_WSTOKEN", "418a443a4b1696cb83716eb1eb106c64");
//define("_MOODLEURL", "http://localhost/moodle");

// Lista de extensiones válidas:
$extensionesValidas = listaExtensiones(1);

// Lista de directorios desde los que leer los cursos:
$listaDirs = listaUbicaciones(1);
?>