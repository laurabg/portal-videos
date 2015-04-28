<?php

require_once('../config.php');
require_once('./curl.php');

$functionName = 'core_course_get_courses';

/// REST RETURNED VALUES FORMAT
$restformat = 'json';
$params = '';

/// REST CALL
//header('Content-Type: text/plain');

$serverurl = _MOODLEURL . '/webservice/rest/server.php'. '?wstoken=' . _WSTOKEN . '&wsfunction='.$functionName;
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';

$curl = new curl;
//	$curl->setHeader(array('Content-Type: text/plain'));

$resp = $curl->post($serverurl . $restformat, $params);

print_r($resp);
$res = json_decode($resp);

/*
Estructura de la respuesta:
[
	{
		"id": 1,
		"shortname": "Nombre corto",
		"categoryid": 0,
		"fullname": "Nombre completo del curso",
		"summary": "<p>Resumen del curso.</p>",
		"summaryformat": 1,
		"format": "site",
		"startdate": 0,
		"numsections": 1
    }
]
*/

//echo 'Total de cursos: '.sizeof($res);
if (sizeof($res) > 0) {
	echo $res[0]->id." ".$res[0]->shortname;
}

?>