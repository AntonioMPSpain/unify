<?

/* CURSOS */

$result=posgre_query("SELECT * FROM curso WHERE modalidad!=3 AND borrado=0 AND (estado=1 OR estado=2) AND fecha_publicacion<='NOW()' AND fecha_fin_publicacion>='NOW()' ORDER BY fecha_fin_publicacion ASC, fecha_inicio ASC, RANDOM() LIMIT 4;") ;
$numCursos = pg_num_rows($result);

$resultPermanentes=posgre_query("SELECT * FROM curso WHERE modalidad=3 AND borrado=0 AND (estado=1 OR estado=2) AND fecha_publicacion<='NOW()' AND fecha_fin_publicacion>='NOW()' ORDER BY RANDOM() LIMIT 0;") ;
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
	$curso['imagen'] = $imgcursospath.$imagen;
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

/* END CURSOS */


/* MATERIALES */

$materiales = "";

$material["nombre"] = "Lámina de impermeabilización exterior Dry80 30";
$material["imagen"] = $imgmaterialespath."i1.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Láminas impermeables";
$material["precio"] = "20";
$material["preciotachado"] = "30";
$material["estrellas"] = "5";
$material["nuevo"] = "0";

$materiales[0] = $material;


$material["nombre"] = "Banda de unión perimetral Dry80 Banda 50";
$material["imagen"] = $imgmaterialespath."i2.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Láminas impermeables";
$material["precio"] = "0";
$material["preciotachado"] = "0";
$material["estrellas"] = "2";
$material["nuevo"] = "0";

$materiales[1] = $material;



$material["nombre"] = "Chimenea de ventilación para cubiertas";
$material["imagen"] = $imgmaterialespath."i3.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Conductos y chimeneas";
$material["precio"] = "155";
$material["preciotachado"] = "0";
$material["estrellas"] = "4";
$material["nuevo"] = "1";

$materiales[2] = $material;


$material["nombre"] = "Dry50 Sumisquare 144";
$material["imagen"] = $imgmaterialespath."i4.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Canales y rejillas";
$material["precio"] = "123";
$material["preciotachado"] = "999";
$material["estrellas"] = "3";
$material["nuevo"] = "1";

$materiales[3] = $material;


/* END MATERIALES */





/** PUBLICACIONES **/

	$publicaciones = "";
	
	$result=posgre_query("SELECT * FROM generica WHERE borrado=0  AND activo=1 AND tipo='publicacion' ORDER BY RANDOM() LIMIT 4;") ;//or die (mysqli_error());  

	$i=0;
	while($row = pg_fetch_array($result)) {
		
		$publicacion["id"] = $row["id"];
		$publicacion["titulo"] = $row["titulo"];
		$publicacion["informacion"] = $row["informacion"];
		$publicacion["imagen"] = $row["img2"];
		
		$publicaciones[$i]= $publicacion;				
		$i++;	
	}
	
/** END PUBLICACIONES **/

/** TRABAJOS **/

$trabajos = "";

$resultt=posgre_query("SELECT * FROM trabajo WHERE fecha>=(NOW()  - '1 day'::interval) AND estado=1 AND borrado=0 ORDER BY fecha_insercion DESC,fecha DESC, id limit 4;");// or die ("Error en consulta. Contacte con Admin.".mysql_error());  

$i=0;
while($rowt = pg_fetch_array($resultt)) {
	
	$trabajo["titulo"] = $rowt["denominacion"];
	$trabajo["descripcion"] = recortarPalabras($rowt["otras_caracteristicas"],150,' ','...');		
	$trabajo["fecha"] = getFechaConMes($rowt["fecha"]);
	$trabajo["zona"] = $rowt["zona"];	
	
	
	$trabajos[$i]= $trabajo;				
	$i++;			
}
		
	 


/** END TRABAJOS **/




$twig->display('index-navegacion.php', array('cursos'=>$cursos, 'materiales'=>$materiales, 'trabajos'=>$trabajos, 'publicaciones'=>$publicaciones));


?>