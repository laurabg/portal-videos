<?php


/*********************************************************************
 headerHTML: Devuelve el header para una página
 Parámetros:
	@titulo					Título de la página
 *********************************************************************/
function headerHTML($titulo) {
	$OUT = "";
	
	$OUT .= '<!DOCTYPE html>
	<html lang="es">
	<head>
		<meta charset="UTF-8">
		<!--meta http-equiv="X-UA-Compatible" content="IE=Edge" /-->
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="" />
		<meta name="author" content="" />
		<!--link rel="shortcut icon" href="favicon.ico" /-->
		<title>'.$titulo.'</title>
		<!-- Bootstrap core CSS -->
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/bootstrap-theme.min.css" rel="stylesheet" />
		<link href="css/style.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="js/flowplayer-5.4.4/skin/minimalist.css"></link>
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<header>
			<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="index.php">Portal Vídeos</a>
					</div>
					<div class="navbar-collapse collapse">
						<ul class="nav navbar-nav">
							<li><a href="admin.php">Admin</a></li>
						</ul>';
	$OUT .= '			'.loginForm();
						/*
						<form class="navbar-form navbar-right" role="form">
							<div class="form-group">
								<input type="text" placeholder="Email" class="form-control">
							</div>
							<div class="form-group">
								<input type="password" placeholder="Password" class="form-control">
							</div>
							<button type="submit" class="btn btn-success">Sign in</button>
						</form>
						*/
	$OUT .= '		</div><!--/.navbar-collapse -->
				</div>
			</div>
		</header>';
	
	echo $OUT;
}


/*********************************************************************
 loginForm: Devuelve el footer para una página
 *********************************************************************/
function loginForm() {
	$OUT = '';
	
	$OUT .= '<form class="navbar-form navbar-right" action="https://www.tlm.unavarra.es/login/index.php" method="post" id="login">
		<div class="form-group">
			<input class="form-control" type="text" name="username" id="username" size="15" value="" />
		</div>
		<div class="form-group">
			<input class="form-control" type="password" name="password" id="password" size="15" value=""  />
		</div>
		<button type="submit" class="btn btn-success">Sign in</button>
	</form>';
	
	return $OUT;
}


/*********************************************************************
 footerHTML: Devuelve el footer para una página
 *********************************************************************/
function footerHTML() {
	$OUT = '';
	
	$OUT .= '<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="js/jquery-1.11.0.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/flowplayer-5.4.4/flowplayer.min.js"></script>
	</body>
	</html>';
	
	echo $OUT;
}


/*********************************************************************
 listCursos: Lista los cursos de la BBDD
 *********************************************************************/
function listCursos() {
	$OUT = "";
	$dbcon = dbConnection();
	
	$SQL = "SELECT * FROM cursos";
	$res = sqlite_query($dbcon, $SQL);
	if (!$res) {
		die ("Cannot execute query<br />$SQL");
	}
	
	while ($row = sqlite_fetch_array($res, SQLITE_ASSOC)) {
		$OUT .= listVideos($row["id"], $row["nombre"]);
	}
	
	echo $OUT;
}


/*********************************************************************
 listVideos: Lista los videos de un curso
 Parámetros:
	@IDcurso				Identificador del curso
 *********************************************************************/
function listVideos($IDcurso, $nombreCurso) {
	$OUT = "";
	
	$dbcon = dbConnection();
	
	$SQL = "SELECT * FROM videos WHERE curso = '".$IDcurso."' ORDER BY id DESC LIMIT "._NUMVIDEOSHOME;
	$res = sqlite_query($dbcon, $SQL);
	if (!$res) {
		die ("Cannot execute query<br />$SQL");
	}
	
	if (sqlite_num_rows($res) > 0) {
		$OUT .= '<div class="panel panel-default">';
		$OUT .= '<div class="panel-heading"><h3 class="panel-title">'.$nombreCurso.'</h3></div>';
		$OUT .= '<div class="panel-body"><div class="row">';
	}
	
	while ($row = sqlite_fetch_array($res, SQLITE_ASSOC)) {
		$OUT .= '<div class="col col-md-3">';
			$OUT .= '<div class=""><a href="?IDcurso='.$IDcurso.'&IDvideo='.$row['id'].'">'.$row["nombre"].'</a></div>';
		//	$OUT .= '<div class="flowplayer" data-swf="js/flowplayer-5.4.4/flowplayer.swf">';
		//		$OUT .= '<video controls>';
		//			$OUT .= '<source src="'._DIRCURSOS.'/'.$row["ruta"].'/'.$row["nombre"].'" type="video/mp4" />';
		//		$OUT .= '</video>';
		//	$OUT .= '</div>';
		$OUT .= '</div>';
	}
	
	if (sqlite_num_rows($res) > 0) {
		$OUT .= '</div><div class="row"><a href="?IDcurso='.$IDcurso.'"><button class="btn btn-default">Ver curso completo</button></a></div>';
		$OUT .= '</div></div>';
	}
	
	return $OUT;
}

?>