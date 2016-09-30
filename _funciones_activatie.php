<?


function getColegios($orderby="nombre"){
	
	$colegios = "";

	$i = 0;
	
	$result = posgre_query("SELECT * FROM usuario WHERE nivel=2 AND borrado = 0 ORDER BY ".$orderby);
	while($row= pg_fetch_array($result)) {
				
		
		$colegio['id'] = $row['id'];
		$colegio['nombre'] = $row['nombre'];					
									
		
		$colegios[$i] = $colegio;
		
		$i++;
	} 
	
	return $colegios;
	
}


function getCategorias($orderby="texto"){
	
	$etiquetas = "";
	
	$i = 0;
		
	$result=posgre_query("SELECT * FROM etiqueta WHERE borrado=0 ORDER BY ".$orderby) ; 
	while($row= pg_fetch_array($result)) {
				
		
		$etiqueta['id'] = $row["id"];
		$etiqueta['texto'] = $row["texto"];
		$etiquetas[$i] = $etiqueta;
		
		$i++;
	} 
	
	return $etiquetas;
	
}

function getFamilias($orderby="nombre"){
	
	$familias = "";
	
	$i = 0;
		
	$result=posgre_query("SELECT * FROM materiales_familias WHERE borrado=0 ORDER BY ".$orderby) ; 
	while($row= pg_fetch_array($result)) {
		$familia['id'] = $row["id"];
		$familia['texto'] = $row["texto"];
		$familias[$i] = $familia;
		
		$i++;
	} 
	
	return $familias;
	
}

function getModalidadTexto($modalidad){
		
	if ($modalidad==0){ 
		$modalidadtexto="On-line";
	}
	if ($modalidad==1){
		$modalidadtexto="Presencial";
	}
	if ($modalidad==2){
		$modalidadtexto="Presencial y On-line";
	}
	if ($modalidad==3){
		$modalidadtexto="Permanente";
	}
	
	return $modalidadtexto;
}






?>