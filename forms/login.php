<?php

include_once(__DIR__.'/../config.php');

if ( ($_POST['userName'] != '')&&($_POST['userPass'] != '') ) {
	echo 'Comprobar datos de acceso';
} else {
	header('Location: http://localhost/portal-videos/?error=1');
	die();
}

?>