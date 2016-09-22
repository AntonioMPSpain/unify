<? 

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

include_once "_config.php";

$accion=($_REQUEST['accion']); 
$texto=$_REQUEST['texto'];
$m=$_REQUEST['m'];

if ($m=="historico"){
	$asc=" DESC ";
	$sqlquitar=" AND fecha_fin_publicacion<'NOW()' ";
}
else{
	$asc=" ASC ";
	$sqlquitar=" AND fecha_publicacion<='NOW()' AND fecha_fin_publicacion>='NOW()' ";
}

$numCursos = 0;
$numCursosPermanentes = 0;
$cadencia = INF;
$rand = 0;

if ($m<>"permanente"){
	$result=posgre_query("SELECT * FROM curso WHERE modalidad!=3 AND borrado=0 AND (estado=1 OR estado=2) $sqlquitar $busqueda ORDER BY fecha_fin_publicacion $asc, fecha_inicio $asc, RANDOM();") ;
	$numCursos = pg_num_rows($result);
}

if ($m<>"online"){
	$resultPermanentes=posgre_query("SELECT * FROM curso WHERE modalidad=3 AND borrado=0 AND (estado=1 OR estado=2) $sqlquitar $busqueda ORDER BY RANDOM();") ;
	$numCursosPermanentes = pg_num_rows($resultPermanentes);
	
	if ($numCursosPermanentes > 0){
		$cadencia = ceil($numCursos/$numCursosPermanentes);
		$rand = rand(0,$cadencia-1);
	}
	
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
	
	$cursos[$i] = $curso;
}

/** BANNERS **/
include_once($backendpath."p_funciones.php"); 
$idbanner = 4;
$banner1 = getBanner($idbanner);
$idbanner = 6;
$banner2 = getBanner($idbanner);
/** FIN BANNERS **/

/** BREADSCRUMBS **/

$breadcrumbs = "";

$breadcrumb1["titulo"] = "Formación";
$breadcrumbs["p1"] = $breadcrumb1;

/** FIN BREADCRUMBS **/

include ($templatepath."header.php");
$twig->display('formacion.php', array('cursos'=>$cursos, 'breadcrumbs'=>$breadcrumbs,'banner1'=>$banner1, 'banner2'=>$banner2));
include ($templatepath."footer.php");




	
		
		if (($accion=="buscar")&&($texto<>"")){
			$busqueda = " AND sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')"; 
		}
		
		$numcursos=0;
		$numcursospermanente=0;
		$numcursospermanente2=0;
		$numcursospermanente3=0;
		
		
		if ($m<>"online"){
			
			$result2=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad=3 $sqlquitar $busqueda ORDER BY RANDOM();") ;
			$numcursospermanente3 = pg_num_rows($result2);
			$result2=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad=3 $sqlquitar $busqueda ORDER BY RANDOM() limit $registros offset $inicio;") ;
			$numcursospermanente = pg_num_rows($result2);
			
			$numcursospermanente2=0;
			if ($pagina>1){
				$inicio2 = ($pagina - 2) * $registros;
				$result2=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad=3 $sqlquitar $busqueda ORDER BY RANDOM() limit $registros offset $inicio2;") ;
				$numcursospermanente2 = pg_num_rows($result2);
			}
		}
		
		if ($m<>"permanente"){
			
			$result=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad<>3 $sqlquitar $busqueda ORDER BY fecha_inicio $asc, RANDOM();") ;
			$numcursos2 = pg_num_rows($result);
			$result=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad<>3 $sqlquitar $busqueda ORDER BY fecha_inicio $asc, RANDOM() limit ($registros-$numcursospermanente) offset ($inicio-$numcursospermanente2);") ;
			$numcursos = pg_num_rows($result);
		}
		
		$totalcursos2 = $numcursos2+$numcursospermanente3;
		$total_paginas = ceil($totalcursos2 / $registros); 
		$totalcursos = $numcursos+$numcursospermanente;
		$cadencia = ceil($numcursos/$numcursospermanente);
		
		$contadorpermanente=0;
		$permas=0;
		$rand = rand(0,$cadencia);
		$cursos = $numcursos;	
			
		if ($totalcursos>$registros){
			$totalcursos=$registros;
		}
				
		for ($i=0;$i<$totalcursos;$i++){

			if (($contadorpermanente==$rand)&&($permas<$numcursospermanente)||($m=="permanente")||($cursos==0)){
				$permas++;
				$row = pg_fetch_array($result2);
				//$contadorpermanente=1;
			}
			else{
				$row = pg_fetch_array($result);
				$cursos--;
			}
		
		
			if ($contadorpermanente==$cadencia){
				$contadorpermanente=-1;
			}
			
			$contadorpermanente++;
			
			
			$id=strip_tags($row["id"]);
			$presentacion=strip_tags($row["presentacion"], '<a>,<br />,<br>');
			$cantidadCaracteres = 35; 
			$cantidadCaracteres2 = 340; 
			$modalidad=$row["modalidad"];
			$fecha_inicio=$row['fecha_inicio'];
			
			$precio = "";
			if ($modalidad==0){
				$preciotachadoc = $row['preciotachadooc'];
				$preciotachadon = $row['preciotachadoon'];
				$precio = $row["precioco"];
			}
			elseif ($modalidad==1){
				$preciotachadoc = $row['preciotachadoc'];
				$preciotachadon = $row['preciotachadon'];
				$precio = $row["precioc"];
			}
			elseif ($modalidad==3){
			
				$preciotachadoc = $row['preciotachadopc'];
				$preciotachadon = $row['preciotachadopn'];
				$precio = $row["preciocp"];
			}
			else{
				$preciotachadoc = $row['preciotachadooc'];
				$preciotachadon = $row['preciotachadoon'];
				
				if (($preciotachadoc==0)||($preciotachadon==0)){
					$preciotachadoc = $row['preciotachadoc'];
					$preciotachadon = $row['preciotachadon'];
				}
				
				$precio = $row["precioco"];
			}
			
			$preciotexto="";
			if ($precio==0){
				$preciotexto = "<b>¡Gratuito!</b>";
			}
			
			$preciopromociontexto = "";
			if (($preciotachadoc<>0)||($preciotachadon<>0)){
				$preciopromociontexto = "<b>¡Precio en promoción!</b>";
			}
			
			$privado = $row["privado"];
			
			$privadotexto="";
			if ($privado==1){
				$privadotexto = " privado ";
			}
			
			
			/* include ("_cortaparrafo.php"); */
			$enlace=substr($row["nombre"],0,strrpos(substr($row["nombre"],0,$cantidadCaracteres)," "));
			$descri=substr($presentacion,0,strrpos(substr($presentacion,0,$cantidadCaracteres2)," "))." ";
	
			
			$video=trim($row["video"]);
			if ($video<>"") {
				$masvideo='<span class="video"></span>';
			}else{
				$masvideo='';
			}
			
			
		} //fin del while
		

		