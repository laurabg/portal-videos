<?php

$opt = '';
if (!$_GET['opt']) {
	$opt = 'config';
} else {
	$opt = $_GET['opt'];
}

if (!file_exists(_DOCUMENTROOT.'modules-admin/templates/'.$opt.'.php')) {
	include_once(_DOCUMENTROOT.'modules/error.php');
} else {
	include_once(_DOCUMENTROOT.'modules-admin/templates/'.$opt.'.php');
}

?>