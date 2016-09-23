<?


function getColegios($orderby="nombre"){
	
	
	$sql = "";
	$colegios = "";
	
	
	$colegio1['id'] = 1;
	$colegio1['nombre'] = "Cole1";
	
	$colegio2['id'] = 2;
	$colegio2['nombre'] = "Cole2";
	
	$colegios[0] = $colegio1;
	$colegios[1] = $colegio2;
	
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






?>