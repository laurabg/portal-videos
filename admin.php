<?php include_once('config.php'); ?>
<?php headerHTML('Portal Vídeos - Administración'); ?>
		
		<div class="jumbotron">
			<div class="container">
				<h1>Administración</h1>
				<p class="lead">Herramientas para gestionar cursos y vídeos.</p>
			</div><!-- /.container -->
		</div>
		<div class="container">
			<?php echo cursosFormHTML(); ?>
		</div>
		
<?php footerHTML(); ?>