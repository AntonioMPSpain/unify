<? 
		
$result=posgre_query("SELECT * FROM curso WHERE modalidad!=3 AND borrado=0 AND (estado=1 OR estado=2) AND fecha_publicacion<='NOW()' AND fecha_fin_publicacion>='NOW()' ORDER BY fecha_fin_publicacion ASC, fecha_inicio ASC, RANDOM();") ;
$numCursos = pg_num_rows($result);

$resultPermanentes=posgre_query("SELECT * FROM curso WHERE modalidad=3 AND borrado=0 AND (estado=1 OR estado=2) AND fecha_publicacion<='NOW()' AND fecha_fin_publicacion>='NOW()' ORDER BY RANDOM();") ;
$numCursosPermanentes = pg_num_rows($resultPermanentes);

if ($numCursosPermanentes > 0){
	$cadencia = ceil($numCursos/$numCursosPermanentes);
	$rand = rand(0,$cadencia-1);
}

$totalCursos = $numCursos + $numCursosPermanentes;

for ($i=0;$i<$totalCursos;$i++){
	
	if ((($i % $cadencia)==$rand) && ($row = pg_fetch_array($resultPermanentes))){
		
	}
	else{
		$row = pg_fetch_array($result);
	}

	$idcurso = 	$row['id'];
	$nombre = $row['nombre'];
	$modalidad = $row['modalidad'];	
	$imagen = $row['imagen'];
	$privado = $row['privado'];
	$fecha_inicio = $row['fecha_inicio'];
	$fecha_fin_inscripcion=$row["fecha_fin_publicacion"];	
	$modalidadtexto = getModalidadTexto($modalidad);
	$plazopermanente = $row["plazopermanente"];
	$curso = "";
	
	$curso['id'] = $idcurso;
	$curso['nombre'] = $nombre;
	$curso['modalidad'] = $modalidad;
	$curso['privado'] = $privado;
	$curso['modalidadtexto'] = $modalidadtexto;
	$curso['imagen'] = $imgcursosbackendpath.$imagen;
	$curso['link'] = "curso.php";
	
	if ($modalidad==3){
		$curso['fecha_inicio'] = "Inmediato";
		$curso['realizacion'] = $plazopermanente;
	}
	else{
		$curso['fecha_inicio'] = getFechaConMes($fecha_inicio);
		$curso['realizacion'] = diasRestantes($fecha_fin_inscripcion);
	}
	
	$resultet=posgre_query("SELECT etiqueta.color, etiqueta.tipo,etiqueta.texto FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$idcurso' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ; 
	if ($rowet = pg_fetch_array($resultet)){
		$curso['area'] = $rowet["texto"];
		$curso['color'] = $rowet["color"];
	}
	else{
		global $coloractivatie;
		$curso['color'] = $coloractivatie;
	}
	
	$cursos[$i] = $curso;
}	
		
	$twig->display('index-formacion.php', array('cursos'=>$cursos));
	
?>
	