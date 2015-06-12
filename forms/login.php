<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/login-moodle.php');

//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";


if (isset($_POST['login'])) {
	if ( ($_POST['userName'] != '')&&($_POST['userPass'] != '') ) {
		if ( ($_POST['userName'] == 'admin')&&($_POST['userPass'] != _ADMINPASS) ) {
			echo 'Los datos de acceso son incorrectos';

		} else if ( ($_POST['userName'] == 'admin')&&($_POST['userPass'] == _ADMINPASS) ) {
			setcookie('MoodleUserSession', 'Administrador', time() + (86400 * 30), _PORTALROOT);
			setcookie('MoodleUserAdmin', 1, time() + (86400 * 30), _PORTALROOT);

		} else {
			$rsp = login($_POST['userName'], $_POST['userPass']);

			if ($rsp == '') {
				// Comprobar si el usuario esta asociado al email:
				if (checkUsuario('username = "'.$_POST['userName'].'"') == 0) {
					setcookie('MoodleUserFaltaCorreo', $_POST['userName'], time() + (86400 * 30), _PORTALROOT); // 86400 = 1 day
				} else {
					$usuario = getUserData('', $_POST['userName'], '');
					
					setcookie('MoodleUserSession', serialize($usuario), time() + (86400 * 30), _PORTALROOT);

					if (checkUsuario('username = "'.$_POST['userName'].'" AND esAdmin = 1') > 0) {
						setcookie('MoodleUserAdmin', 1, time() + (86400 * 30), _PORTALROOT);
					}
				}
			}

			echo $rsp;
		}

	} else {
		echo 'Los datos de acceso son obligatorios';
	}

} else if (isset($_POST['asociar-correo'])) {
	if ($_POST['email'] == '') {
		echo 'El email es obligatorio';

	} else if ( ($_POST['email'] != '')&&(isset($_COOKIE['MoodleUserFaltaCorreo'])) ) {
		asociarUsernameEmail($_POST['email'], $_COOKIE['MoodleUserFaltaCorreo']);
		$usuario = getUserData('', '', $_POST['email']);
		
		setcookie('MoodleUserSession', serialize($usuario), time() + (86400 * 30), _PORTALROOT);

		if (checkUsuario('username = "'.$usuario['username'].'" AND esAdmin = 1') > 0) {
			setcookie('MoodleUserAdmin', 1, time() + (86400 * 30), _PORTALROOT);
		}

		unset($_COOKIE['MoodleUserFaltaCorreo']);
		setcookie('MoodleUserFaltaCorreo', null, -1, _PORTALROOT);
	}

} else if (isset($_POST['logout'])) {
	logout();

	if (isset($_COOKIE['MoodleUserFaltaCorreo'])) {
		unset($_COOKIE['MoodleUserFaltaCorreo']);
		setcookie('MoodleUserFaltaCorreo', null, -1, _PORTALROOT);
	}
	
	if (isset($_COOKIE['MoodleUserAdmin'])) {
		unset($_COOKIE['MoodleUserAdmin']);
		setcookie('MoodleUserAdmin', null, -1, _PORTALROOT);
	}
}


?>