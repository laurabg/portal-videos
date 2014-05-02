<?php


/*********************************************************************
 cursosFormHTML: Devuelve el HTML del formulario para crear cursos.
 *********************************************************************/
function cursosFormHTML() {
	$OUT = '';
	$msg = '';
	
	if ($_POST["formName"] == "curso-create") {
		// Obtener el nombre del curso:
		$curso = $_POST["nombreCurso"];
		
		$msg .= createCurso($curso);
	}
	
	$OUT .= '<div class="msg">'.$msg.'</div>';
	$OUT .= '<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">';
		$OUT .= '<input type="text" name="nombreCurso" value="" />';
		$OUT .= '<button type="submit">Crear curso</button>';
		$OUT .= '<input type="hidden" name="formName" value="curso-create" />';
	$OUT .= '</form>';
	
	return $OUT;
}


/*********************************************************************
 createCurso: Crea un nuevo curso
	@curso --> Nombre del curso
 *********************************************************************/
function createCurso($curso) {
	$msg = "";
	
	// 1. Comprobar que no exista el curso:
	$dbcon = dbConnection();
	
	$SQL = "SELECT * FROM cursos WHERE nombre = '".$curso."'";
	$res = sqlite_query($dbcon, $SQL);
	if (!$res) {
		die ("Cannot execute query<br />$SQL");
	}
	
	// Si el curso no existe, crearlo:
	if (sqlite_num_rows($res) == 0) {
		// 2. Limpiar nombre del curso y crear carpeta en su sitio:
		$cleanCursoName = clean($curso);
		
		if (!is_dir(_DIRCURSOS."/".$cleanCursoName)) {
			$ok = mkdir(_DIRCURSOS."/".$cleanCursoName);
			logAction($dbcon, "Carpeta "._DIRCURSOS."/".$cleanCursoName." creada (".$ok.")", "MKDIR");
			$ok = mkdir(_DIRCURSOS."/".$cleanCursoName."/inbox");
			logAction($dbcon, "Carpeta "._DIRCURSOS."/".$cleanCursoName."/inbox"." creada (".$ok.")", "MKDIR");
			$ok = mkdir(_DIRCURSOS."/".$cleanCursoName."/videos");
			logAction($dbcon, "Carpeta "._DIRCURSOS."/".$cleanCursoName."/videos"." creada (".$ok.")", "MKDIR");
			$ok = mkdir(_DIRCURSOS."/".$cleanCursoName."/capturas");
			logAction($dbcon, "Carpeta "._DIRCURSOS."/".$cleanCursoName."/capturas"." creada (".$ok.")", "MKDIR");
			
			if ($ok) {
				$dirDate = date("Y-m-d H:i:s", filemtime(_DIRCURSOS."/".$cleanCursoName));
				$ok = addCurso($dbcon, $dirDate, $curso, $cleanCursoName);
				
				if ($ok) {
					$msg = "Curso creado con éxito";
				} else {
					$msg = "Ha ocurrido un error al intentar crear el curso (".$ok.")";
				}
			}
		} else {
			$msg = "Ya existe la carpeta del curso";
		}
	} else {
		$msg = "Ya existe el curso";
	}
	
	dbDisconnect($dbcon);
	
	return $msg;
}
?>