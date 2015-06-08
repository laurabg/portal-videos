<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'lib/curl.php');
include_once(_DOCUMENTROOT.'lib/simple_html_dom.php');
include_once('ws-connection.php');

function login($userName, $userPass) {
	$errorText = '';

	$serverURL = _MOODLEURL . '/login/index.php';
	
	$curl = new curl;
	$rsp = $curl->post($serverURL, 'username='.$userName.'&password='.$userPass);
	
	$html = str_get_html($rsp);

	foreach($html->find('span') as $element) {
		if ($element->class == 'error') {
			$errorText .= $element->plaintext.'<br />';
		}
	}

	if ($errorText == '') {
		$cookie_name = 'UserSession';
		$cookie_value = time();
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), _PORTALROOT); // 86400 = 1 day
	}
}

function logout() {
	if (isset($_COOKIE['UserSession'])) {
		unset($_COOKIE['UserSession']);
		setcookie('UserSession', null, -1, _PORTALROOT);
	}
}

/*
var_dump($_COOKIE);
logout();
echo '<br /><br />';
echo time().'<br /><br />';

$cookie_name = 'UserSession';

if(!isset($_COOKIE[$cookie_name])) {
    echo "Cookie named '" . $cookie_name . "' is not set!<br>Hacer login...<br />";
	login('laura','$Laura312');
} else {
    echo "Cookie '" . $cookie_name . "' is set!<br>";
    echo "Value is: " . $_COOKIE[$cookie_name];
}*/



?>