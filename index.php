<?php
require_once('config.php');
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<!--meta http-equiv="X-UA-Compatible" content="IE=Edge" /-->
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="" />
		<meta name="author" content="" />
		<!--link rel="shortcut icon" href="favicon.ico" /-->
		<title>Portal Vídeos</title>
		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" />
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link rel="stylesheet" type="text/css" href="js/flowplayer-5.4.4/skin/minimalist.css" />
		
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
						</ul>
						<form class="navbar-form navbar-right" role="form" method="POST" action="forms/login.php">
							<div class="form-group">
								<input name="userName" type="text" placeholder="Email" class="form-control">
							</div>
							<div class="form-group">
								<input name="userPass" type="password" placeholder="Password" class="form-control">
							</div>
							<button type="submit" class="btn btn-success">Sign in</button>
						</form>
					</div><!--/.navbar-collapse -->
				</div>
			</div>
		</header>
		<?php
			require_once(_DOCUMENTROOT.'modules/content.php');
		?>
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/flowplayer-5.4.4/flowplayer.min.js"></script>
		<script type="text/javascript" src="js/main.js"></script>
	</body>
</html>