<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'lib/curl.php');

function connect($functionName, $params) {
	$serverURL = _MOODLEURL . '/webservice/rest/server.php';
	$serverURL .= '?wstoken=' . _WSTOKEN;
	$serverURL .= '&wsfunction='.$functionName;
	$serverURL .= '&moodlewsrestformat=json';
	
	$curl = new curl;
	$rsp = $curl->post($serverURL, $params);
	
	return json_decode($rsp);
}

?>