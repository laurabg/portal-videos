<?php include_once('config.php'); ?>
<?php headerHTML('Portal Vídeos'); ?>
		
		<?php
		if ( ($_GET["IDcurso"] != '')&&($_GET["IDvideo"] != '') ) {
			include('mod/detalle-video.php');
		} elseif ( ($_GET["IDcurso"] != '')&&($_GET["IDvideo"] == '') ) {
			include('mod/detalle-curso.php');
		} else {
		?>
		
		<div class="jumbotron">
			<div class="container">
				<h1>Portal Vídeos</h1>
				<p class="lead">Portal para visualización de vídeos agrupados por cursos.</p>
			</div><!-- /.container -->
		</div>
		<div class="container">
			<?php listCursos(); ?>
		</div>
		
		<?php
		}
		?>
		
<?php footerHTML(); ?>