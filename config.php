<?php

// Definición de variables
define("_PORTALROOT", $_SERVER["DOCUMENT_ROOT"]."/portal-videos/");
define("_BBDD", _PORTALROOT."db/dbportalvideos.db");
define("_BBDDLOG", _PORTALROOT."db/dblog.db");
define("_DIRCURSOS", _PORTALROOT."cursos");
//define("_DIRCURSOS", "C:/pfc-videos");

define("_NUMVIDEOSHOME", 4);


$extensionesValidas = array("mp4");

// SQL de creación de tablas
define("_SQLCreateCursos", "CREATE TABLE cursos (id TEXT PRIMARY KEY, nombre TEXT, desc TEXT, rutaABS TEXT, ruta TEXT)");
define("_SQLCreateVideos", "CREATE TABLE videos (id TEXT PRIMARY KEY, nombre TEXT, ruta TEXT, titulo TEXT, desc TEXT, curso TEXT);");
define("_SQLCreateLogVideos", "CREATE TABLE logVideos (id TEXT PRIMARY KEY, log TEXT, tipo TEXT);");



// Incluir páginas que contengan funciones PHP a las que invocar:
require_once("fn/functions.php");
require_once("fn/forms.php");
require_once("fn/content.php");



// Acceso SQLITE admin:
// http://localhost/portal-videos/db/manager/phpliteadmin.php

// Codificación original: ISO-8859-1
//echo mb_internal_encoding()."*****<br />";

?>