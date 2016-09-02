<?

include_once("_cone.php");
if (isset($_GET['test'])){
	$plazas = getPlazasLibresPresencial($_GET["test"]);
	echo $plazas;
}

/** CURSOS **/
/* Modalidades :
	- Online: 0
	- Presencial: 1
	- Presencial y online: 2
	- Permanente: 3
*/


/** Obtiene las plazas libres que hay para un curso. Tiene en cuenta a los usuarios que han pagado y están preinscritos.
	Devuelve un entero que indica las plazas que quedan libres.
	
**/
function getPlazasLibres($cursoID){

	$result =posgre_query("SELECT modalidad,plazas,plazaso,plazasperma FROM curso WHERE id='$cursoID'");
	if ($curso = pg_fetch_array($result)){
		$modalidad = $curso['modalidad'];
		$plazasOnline = $curso['plazaso'];
		$plazasPresencial = $curso['plazas'];
		$plazasPermanente = $curso['plazasperma'];
	}   
	
	if ($modalidad=="0"){
		$plazasTotales = $plazasOnline;
	}
	elseif ($modalidad=="1"){
		$plazasTotales = $plazasPresencial;
	}
	elseif ($modalidad=="2"){
		$plazasTotales = $plazasOnline + $plazasPresencial;
	}
	elseif ($modalidad=="3"){
		$plazasTotales = $plazasPermanente;
	}
	
	$numUsuariosCurso = getNumeroUsuariosCurso($cursoID);
	
	$plazasLibres = $plazasTotales - $numUsuariosCurso;
	
	return $plazasLibres;

}

function getNumeroUsuariosCurso($cursoID){
	$result = posgre_query("SELECT COUNT(*) AS numusuarios FROM curso_usuario WHERE espera=0 AND estado=0 AND nivel=5 AND borrado='0' AND idcurso='$cursoID'");
	if ($numUsuariosCurso = pg_fetch_array($result)){
		$numUsuariosCurso = $numUsuariosCurso['numusuarios'];
	}
	
	return $numUsuariosCurso;

}

/** Solo necesario para cursos de Modalidad 2 **/
function getPlazasLibresOnline($cursoID){

	$result =posgre_query("SELECT modalidad,plazaso FROM curso WHERE id='$cursoID'");
	if ($curso = pg_fetch_array($result)){
		$modalidad = $curso['modalidad'];
		$plazasOnline = $curso['plazaso'];
	}   
	
	$numUsuariosCurso = getNumeroUsuariosCursoOnline($cursoID);
	
	$plazasLibres = $plazasOnline - $numUsuariosCurso;
	
	return $plazasLibres;
	
}


/** Solo necesario para cursos de Modalidad 2 **/
function getPlazasLibresPresencial($cursoID){
	$result =posgre_query("SELECT modalidad,plazas FROM curso WHERE id='$cursoID'");
	if ($curso = pg_fetch_array($result)){
		$modalidad = $curso['modalidad'];
		$plazas = $curso['plazas'];
	}   
	
	$numUsuariosCurso = getNumeroUsuariosCursoPresencial($cursoID);
	
	$plazasLibres = $plazas - $numUsuariosCurso;
	
	return $plazasLibres;

}


/** Solo necesario para cursos de Modalidad 2 **/
function getNumeroUsuariosCursoOnline($cursoID){
	$result = posgre_query("SELECT COUNT(*) AS numusuarios FROM curso_usuario WHERE espera=0 AND estado=0 AND inscripciononlinepresencial=2 AND  nivel=5 AND borrado='0' AND idcurso='$cursoID'");
	if ($numUsuariosCurso = pg_fetch_array($result)){
		$numUsuariosCurso = $numUsuariosCurso['numusuarios'];
	}
	
	return $numUsuariosCurso;

}

/** Solo necesario para cursos de Modalidad 2 **/
function getNumeroUsuariosCursoPresencial($cursoID){
	$result = posgre_query("SELECT COUNT(*) AS numusuarios FROM curso_usuario WHERE espera=0 AND estado=0 AND inscripciononlinepresencial=1 AND nivel=5 AND borrado='0' AND idcurso='$cursoID'");
	if ($numUsuariosCurso = pg_fetch_array($result)){
		$numUsuariosCurso = $numUsuariosCurso['numusuarios'];
	}
	
	return $numUsuariosCurso;

}

function getPosicionListaEspera($idusuario, $idcurso){
	
	$result =posgre_query("SELECT modalidad FROM curso WHERE id='$idcurso'");
	if ($curso = pg_fetch_array($result)){
		$modalidad = $curso['modalidad'];
	}   
	
	if ($modalidad==2){
		$result =posgre_query("SELECT inscripciononlinepresencial FROM curso_usuario WHERE idcurso='$idcurso' AND idusuario='$idusuario'");
		
		if ($curso = pg_fetch_array($result)){
			$cursodual = $curso['inscripciononlinepresencial'];
		}  
		
		$result=posgre_query("SELECT * FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE borrado=0) AND espera=1 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial='$cursodual' AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ORDER BY fechahora") ;//or die (pg_error()); 	
	}else{
		$result=posgre_query("SELECT * FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE borrado=0) AND espera=1 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ORDER BY fechahora ") ;//or die (pg_error());  
	}
	
	$i = 1;
	while ($row = pg_fetch_array($result)){
		$idusuario2 = $row["idusuario"];
		
		if ($idusuario == $idusuario2){
			return $i;
		}
		$i++;
	
	}
	return 0;
	

}

?>