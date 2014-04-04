<?php

// Definición de variables
$root = $_SERVER["DOCUMENT_ROOT"]."/pfc/";

$bbdd = $root."db/dbportalvideos.db";
$bbddlog = $root."db/dblog.db";
$dirVideos = $root."cursos";

$extensionesValidas = array("mp4");

// SQL de creación de tablas
$SQLCreateCursos = "CREATE TABLE cursos (id TEXT PRIMARY KEY, nombre TEXT, desc TEXT, ruta TEXT)";
$SQLCreateVideos = "CREATE TABLE videos (id TEXT PRIMARY KEY, nombre TEXT, ruta TEXT, titulo TEXT, desc TEXT, curso TEXT);";
$SQLCreateLogVideos = "CREATE TABLE logVideos (id TEXT PRIMARY KEY, log TEXT, tipo TEXT);";

// Acceso SQLITE admin:
// http://localhost/pfc/db/manager/phpliteadmin.php

// Codificación original: ISO-8859-1
//echo mb_internal_encoding()."*****<br />";

?>