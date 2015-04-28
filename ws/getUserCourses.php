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
$token = '17b86aa656d4e44ce43f8146fb75dd05';
$domainname = 'http://localhost/moodle';
$functionname = 'core_enrol_get_users_courses';

// REST RETURNED VALUES FORMAT
$restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
                     //Setting it to 'json' will fail all calls on earlier Moodle version

$params = array('userid' => 3);

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


$resp = $curl->post($serverurl . $restformat, $params);

print_r($resp);


?>