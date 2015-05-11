<?php
// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
/**
 * REST client for Moodle 2
 * Return JSON or XML format
 *
 * @authorr Jerome Mouneyrac
 */
/// SETUP - NEED TO BE CHANGED
$token = '418a443a4b1696cb83716eb1eb106c64';
$domainname = 'http://localhost/moodle';
$functionname = 'core_enrol_get_users_courses';

// REST RETURNED VALUES FORMAT
$restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
                     //Setting it to 'json' will fail all calls on earlier Moodle version

$params = array('courseid' => 2);

/// REST CALL
header('Content-Type: text/plain');

$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;

require_once('./curl.php');

$curl = new curl;
/*
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';

// echo '**** '.$serverurl . $restformat.' ****   ';

$resp = $curl->post($serverurl . $restformat, $params);

print_r($resp);
*/

$functionname = 'core_enrol_get_enrolled_users';
$params = array('courseid' => 4);
$restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'

$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
echo $serverurl . $restformat.'<br /><br /><br />';

$resp = $curl->post($serverurl . $restformat, $params);

print_r($resp);


?>