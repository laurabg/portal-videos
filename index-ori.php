<?php include("config.php"); ?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" media="screen" />
		<!--link rel="stylesheet" type="text/css" href="css/default.css" media="screen" /-->
		
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<title>Portal Vídeos</title>
	</head>
	<body>
		<div id="main">
			<header>
				<div class="content">
					<nav id="menu">
						<ul>
							<li><a href="admin.php" title="Administración">Administración</a></li>
						</ul>
					</nav>
				</div>
			</header>
			<div class="content">
				<h1>Portal Gestión Vídeos</h1>
				
				<section class="cursos">
					<?php echo listCursos(); ?>
				</section>
			</div>
		</div>
		<footer id="footer">
			Footer
		</footer>
	</body>
</html>