<?
session_start();
 
////////// Filtros de nivel por usuario //////////////////////
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql=" (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

include("_funciones.php"); 
include("_cone.php"); 

$safe="Reparto de beneficios";
$titulo1="informe ";
$titulo2="cursos";

$linka=iConectarse(); 
$rowcurso=pg_query($linka,"SELECT * FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
$curso= pg_fetch_array($rowcurso);

$fecha1 = $_POST['fecha1'];
$fecha2 = $_POST['fecha2'];

$liquidados = $_POST['liquidados'];

if ($liquidados=="liquidados"){
	$liquidadocheck = " checked ";
	$sqlliquidado = "";
}
else{
	$liquidadocheck = "";
	$sqlliquidado = " AND liquidado=0 ";
}

/*
if ($fecha1==""){
	$ano = date('Y');
	$fecha1 = $ano."-01-01";
}
*/

$fecha1copia = $fecha1;
$fecha2copia = $fecha2;

if ($fecha1[5]=="/"){
	$fechas = explode("/", $fecha1);
	$fecha1 = $fechas[2]."-".$fechas[1]."-".$fechas[0];
}

if ($fecha2[5]=="/"){
	$fechas = explode("/", $fecha2);
	$fecha2 = $fechas[2]."-".$fechas[1]."-".$fechas[0];
}

$sqlfecha1="";
if ($fecha1<>""){
	$sqlfecha1 = " AND fecha_fin>='$fecha1' ";
	$sqlfecha1b = " AND fechahora>='$fecha1' ";
	$sqlfechagastos1 = " AND fecha >='$fecha1' ";
}

$sqlfecha2="";
if ($fecha2<>""){
	$sqlfecha2 = " AND fecha_fin<='$fecha2' ";
	$sqlfecha2b = " AND fechahora<='$fecha2' ";
	$sqlfechagastos2 = " AND fecha<='$fecha2' ";
}

include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->

	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<div class="bloque-lateral acciones">		
	<p>
		<a href="zona-privada_admin_informes_1.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
	</div>
	
	
	
	<h2 class="titulonoticia">Reparto beneficios SOCIOS</h2>
		<br />
		<form  class="form-horizontal" action="informe_curso_repartoglobal.php" enctype="multipart/form-data" method="post">

		<div>
			<div class="control-group">
				<label class="control-label" for="fecha1"></label>
				<div class="controls">
				Fecha Inicio: <input style="width:130px;" type="date" id="fecha1" name="fecha1" value="<?=$fecha1copia?>" placeholder="00/00/0000">
				&nbsp;&nbsp;&nbsp;Fecha Fin: <input style="width:130px;" type="date" id="fecha2" name="fecha2" value="<?=$fecha2copia?>" placeholder="00/00/0000">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input <?=$liquidadocheck?> type="checkbox" id="liquidados" name="liquidados" value="liquidados" > Incluir cursos liquidados
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-important">Ver</button>
			</div>
		</div>
		</form>	
		<? 
		include("_aya_mensaje_session.php"); 
		$_SESSION[error]=""; 
		
		$inviable=false;
		$porcentajeorganizador = 0.3;
		$porcentajesocio = 0.7;
		$porcentajecolaborador = 0.35;
				
		$sql = "SELECT sum(precio) as recaudado FROM curso_usuario WHERE borrado=0 AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2)) AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado)";			
		$result = posgre_query($sql);
		echo pg_last_error();
		$recaudado=0;
		if ($row = pg_fetch_array($result)){
			$recaudado = $row['recaudado'];
		}
		
		/** Gastos **/
		$sqlg = "SELECT * FROM curso_gastos WHERE idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado) AND borrado=0 $sqlfechagastos1 $sqlfechagastos2 ";
		$resultg = posgre_query($sqlg);
				
		$gastos = 0;
		while ($rowg = pg_fetch_array($resultg)){
			$gastos += $rowg['importe'];
		}
		$copiagastos=$gastos;
		
		$porcentajegastos = ($gastos/$recaudado)*100;
		
		if (($porcentajegastos>90)||($recaudado==0)){
			$porcentajegastos=90;
		}
		
		$porcentajecaja = 10;
		
		
		$porcentajerestante = 100-$porcentajecaja-$porcentajegastos;
		
		?>
		
		<span>Total recaudado: <strong><? echo number_format($recaudado, 2, '.', ''); ?>€</strong></span><br>
		<span>Total gastos de organización: <strong><? echo number_format($gastos, 2, '.', ''); ?>€</strong></span><br><br>
		<span>Porcentaje de recaudado dedicados a activatie: <strong><? echo number_format($porcentajecaja, 2, '.', '');?>%</strong> sobre bruto</span><br>
		<span>Porcentaje dedicado a organizador: <strong><?=$porcentajeorganizador*100?>%</strong> sobre neto</span><br>
		<span>Porcentaje obtenido por socio: <strong><?=$porcentajesocio*100?>%</strong> sobre neto</span><br>
		<p>En el caso de no colegiados, el <?=$porcentajesocio*100?>% de beneficio por socio, se dedica a activatie</p><br>
		
		<TABLE style = "text-align:center;"> 

		<? 
		
		$totalalumnos = 0;
		$totalbruto = 0;
		$totalgastos = 0;
		$totalcaja = 0;
		$totalneto = 0;
		$totalorganizador = 0;
		$totalsocio = 0;
		$alumnosGastosSinCubrir=0;
			
		$sql3 = "SELECT * FROM usuario WHERE borrado=0 AND nivel=2 ORDER BY nombre";
		$result3 = posgre_query($sql3);
		
		while ($row3 = pg_fetch_array($result3)){
			$idcolegio = $row3['id'];
			$nombrecolegio = $row3['nombre'];
			
			$sql2 = "SELECT * FROM curso_usuario WHERE borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2 )) AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado)";
			$result2 = posgre_query($sql2);
			
			$pagados = pg_num_rows($result2);
			
			$precio = 0;
			while ($row2 = pg_fetch_array($result2)){
				$precio += $row2['precio'];
				
			}
				
			if ($pagados > 0){
					
				$sql2x = "SELECT id as idcurso FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado AND id IN (SELECT idcurso FROM curso_usuario WHERE borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2)) GROUP BY idcurso) ORDER BY fecha_inicio";
				$result2x = posgre_query($sql2x);
				
				$totalcprecio = 0;
				$totalccaja = 0;
				$totalcdedicadogastos = 0;
				$totalcneto = 0;
				$totalcorganizador = 0;
				$totalcsocio = 0;
				
				while ($row2x = pg_fetch_array($result2x)){
					$idcurso = $row2x['idcurso'];
					$sqlc = "SELECT nombre FROM curso WHERE id='$idcurso'";
					$resultc = posgre_query($sqlc);
					$rowc = pg_fetch_array($resultc);
					$nombrecurso = $rowc['nombre'];
					
					$sql3x = "SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2)) AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado)  ";
					$result3x = posgre_query($sql3x);
					$pagados2 = pg_num_rows($result3x);
					
					$precio2=0;
					while ($row3x = pg_fetch_array($result3x)){
						$precio2+=$row3x['precio'];
					}
					
					$sql = "SELECT sum(precio) as recaudado2 FROM curso_usuario WHERE idcurso='$idcurso' AND borrado=0 AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2)) AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado) ";			
					$result = posgre_query($sql);
					
					$recaudado2=0;
					if ($row = pg_fetch_array($result)){
						$recaudado2 = $row['recaudado2'];
					}
								
					$sqlgastos = "SELECT * FROM curso_gastos WHERE idcurso='$idcurso' AND borrado=0 $sqlfechagastos1 $sqlfechagastos2";
					$resultg = posgre_query($sqlgastos);
					echo pg_last_error();
					$gastos2 = 0;
					while ($rowg = pg_fetch_array($resultg)){
						$gastos2 += $rowg['importe'];
					}
					
					
					$sqlcurso = "SELECT idcolegio FROM curso WHERE id='$idcurso'";
					$resultcurso = posgre_query($sqlcurso);
					if ($rowcurso = pg_fetch_array($resultcurso)){
						$idcolegioorganizador = $rowcurso['idcolegio'];
					}
					
					$porcentajegastos=0;
					
					if ($gastos>0){
						
						$porcentajegastos2 = ($gastos2/$recaudado2)*100;
			
						if (($porcentajegastos2>90)||($recaudado2==0)){
							$porcentajegastos2=90;
						}
					}
					
					$porcentajerestante2 = 100-$porcentajecaja-$porcentajegastos2;
										
					$dedicadogastos2=($precio2*$porcentajegastos2)/100;
					$dedicadocaja2=($precio2*$porcentajecaja)/100;
					$neto2=($precio2*$porcentajerestante2)/100;
					$dedicadoorganizador2 = $neto2*$porcentajeorganizador;
					$dedicadoosocio2 = $neto2*$porcentajesocio;
					
					$totalcprecio += $precio2;
					$totalccaja += $dedicadocaja2;
					$totalcdedicadogastos += $dedicadogastos2;
					$totalcneto += $neto2;
					$totalcorganizador += $dedicadoorganizador2;
					$totalcsocio += $dedicadoosocio2;
					
					$totalalumnos += $pagados2;
					
					if (($neto2==0)&&($porcentajerestante2<=0)){
						$alumnosGastosSinCubrir+=$pagados2;
					}
				}
				
				$totalbruto += $totalcprecio;
				$totalgastos += $totalcdedicadogastos;
				$totalcaja += $totalccaja;
				$totalneto += $totalcneto;
				$totalorganizador += $totalcorganizador;
				$totalsocio += $totalcsocio;
				
			}
		}
		
		$sql2 = "SELECT * FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2)) AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado)";
		$result2 = posgre_query($sql2);
		
		$pagados = pg_num_rows($result2);
			
		$precio = 0;
		while ($row2 = pg_fetch_array($result2)){			
			$precio += $row2['precio'];
		}
		
		if ($pagados > 0){
				 			
				$sql2x = "SELECT idcurso FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2 ORDER BY fecha_inicio)) AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado) GROUP BY idcurso";
				$result2x = posgre_query($sql2x);
				
				$totalcprecio = 0;
				$totalccaja = 0;
				$totalcdedicadogastos = 0;
				$totalcneto = 0;
				$totalcorganizador = 0;
				$totalcsocio = 0;
				
				while ($row2x = pg_fetch_array($result2x)){
					$idcurso = $row2x['idcurso'];
					$sqlc = "SELECT nombre FROM curso WHERE id='$idcurso'";
					$resultc = posgre_query($sqlc);
					$rowc = pg_fetch_array($resultc);
					$nombrecurso = $rowc['nombre'];
					
					$sql3x = "SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2)) AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado)";
					$result3x = posgre_query($sql3x);
					$pagados2 = pg_num_rows($result3x);
					
					$precio2=0;
					while ($row3x = pg_fetch_array($result3x)){
						$precio2+=$row3x['precio'];
					}
					
					$sql = "SELECT sum(precio) as recaudado2 FROM curso_usuario WHERE idcurso='$idcurso' AND borrado=0 AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2))  AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado) ";			
					$result = posgre_query($sql);
					
					$recaudado2=0;
					if ($row = pg_fetch_array($result)){
						$recaudado2 = $row['recaudado2'];
					}
								
					$sqlgastos = "SELECT * FROM curso_gastos WHERE idcurso='$idcurso' AND borrado=0 $sqlfechagastos1 $sqlfechagastos2";
					$resultg = posgre_query($sqlgastos);
					echo pg_last_error();
					$gastos2 = 0;
					while ($rowg = pg_fetch_array($resultg)){
						$gastos2 += $rowg['importe'];
					}
					
					
					$sqlcurso = "SELECT idcolegio FROM curso WHERE id='$idcurso'";
					$resultcurso = posgre_query($sqlcurso);
					if ($rowcurso = pg_fetch_array($resultcurso)){
						$idcolegioorganizador = $rowcurso['idcolegio'];
					}
					
					$porcentajegastos=0;
					
					if ($gastos>0){
						
						$porcentajegastos2 = ($gastos2/$recaudado2)*100;
			
						if (($porcentajegastos2>90)||($recaudado2==0)){
							$porcentajegastos2=90;
						}
					}
					
					$porcentajerestante2 = 100-$porcentajecaja-$porcentajegastos2;
										
					$dedicadogastos2=($precio2*$porcentajegastos2)/100;
					$dedicadocaja2=($precio2*$porcentajecaja)/100;
					$neto2=($precio2*$porcentajerestante2)/100;
					$dedicadoorganizador2 = $neto2*$porcentajeorganizador;
					$dedicadoosocio2 = $neto2*$porcentajesocio;
					
					$totalcprecio += $precio2;
					$totalccaja += $dedicadocaja2;
					$totalcdedicadogastos += $dedicadogastos2;
					$totalcneto += $neto2;
					$totalcorganizador += $dedicadoorganizador2;
					$totalcsocio += $dedicadoosocio2;
					
					$totalalumnos += $pagados2;
					
					if (($neto2==0)&&($porcentajerestante2<=0)){
						$alumnosGastosSinCubrir+=$pagados2;
					}
				}	
			
			
		
			$totalbruto += $totalcprecio;
			$totalgastos += $totalcdedicadogastos;
			$totalcaja += $totalccaja;
			$totalsocio += $totalcsocio;
			$totalneto += $totalcneto;
			$totalorganizador += $totalcorganizador;
		}
		
		
		
		$cantidadOtrosCursosPorAlumno=0;
		if (trim($totalgastos)<trim($copiagastos)){ 
			$gastossincubrir = trim($copiagastos) - trim($totalgastos);
			$alumnosGastosCubiertos = $totalalumnos - $alumnosGastosSinCubrir;
			$cantidadOtrosCursosPorAlumno = $gastossincubrir/$alumnosGastosCubiertos;
		
		}
				
		/** HASTA AHORA SE CALCULAN LOS GASTOS SIN CUBRIR Y VOLVEMOS A HACER CALCULO **/
		?>
		
		
		
		
		<TABLE style = "text-align:center;"> 

		<? 
		
		
		
		$totalalumnos = 0;
		$totalbruto = 0;
		$totalgastos = 0;
		$totalcaja = 0;
		$totalneto = 0;
		$totalotrosgastos = 0;
		$totalorganizador = 0;
		$totalsocio = 0;
			
		$sql3 = "SELECT * FROM usuario WHERE borrado=0 AND nivel=2 ORDER BY nombre";
		$result3 = posgre_query($sql3);
		
		while ($row3 = pg_fetch_array($result3)){
			$idcolegio = $row3['id'];
			$nombrecolegio = $row3['nombre'];
			
			$sql2 = "SELECT * FROM curso_usuario WHERE borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2))  AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado)";
			$result2 = posgre_query($sql2);
			
			$pagados = pg_num_rows($result2);
			
			$precio = 0;
			while ($row2 = pg_fetch_array($result2)){
				$precio += $row2['precio'];
				
			}
				
			if ($pagados > 0){
			
				?>
				
				<TR>
					<td><b><?=$nombrecolegio?></b></td>
					<th>Fecha inicio</th>
					<th>Alumnos (que pagan)</th>
					<th>Importe bruto</th>
					<th>Dedicado a activatie</th>
					<th>Dedicado a gastos</th>
					<th>Gastos de otros cursos</th>
					<th>Importe neto</th>
					<? /* <th>Dedicado a organizadores</th> */ ?>
					<th>Beneficio socio</th>
				</TR> 
				
				
				
				<?
				
				 			
				$sql2x = "SELECT id as idcurso FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado AND id IN (SELECT idcurso FROM curso_usuario WHERE borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2 ORDER BY fecha_inicio)) GROUP BY idcurso) ORDER BY fecha_inicio";
				$result2x = posgre_query($sql2x);
				
				$totalcprecio = 0;
				$totalccaja = 0;
				$totalcdedicadogastos = 0;
				$totalcneto = 0;
				$totalcotrosgastos = 0;
				$totalcorganizador = 0;
				$totalcsocio = 0;
				
				while ($row2x = pg_fetch_array($result2x)){
					$idcurso = $row2x['idcurso'];
					$sqlc = "SELECT nombre, fecha_inicio FROM curso WHERE id='$idcurso'";
					$resultc = posgre_query($sqlc);
					$rowc = pg_fetch_array($resultc);
					$nombrecurso = $rowc['nombre'];
					$fechainiciocurso = $rowc['fecha_inicio'];
					
					$sql3x = "SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2))  AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado)  ";
					$result3x = posgre_query($sql3x);
					$pagados2 = pg_num_rows($result3x);
					
					$precio2=0;
					while ($row3x = pg_fetch_array($result3x)){
						$precio2+=$row3x['precio'];
					}
					
					$sql = "SELECT sum(precio) as recaudado2 FROM curso_usuario WHERE idcurso='$idcurso' AND borrado=0 AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2))  AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado) ";			
					$result = posgre_query($sql);
					
					$recaudado2=0;
					if ($row = pg_fetch_array($result)){
						$recaudado2 = $row['recaudado2'];
					}
								
					$sqlgastos = "SELECT * FROM curso_gastos WHERE idcurso='$idcurso' AND borrado=0 $sqlfechagastos1 $sqlfechagastos2";
					$resultg = posgre_query($sqlgastos);
					echo pg_last_error();
					$gastos2 = 0;
					while ($rowg = pg_fetch_array($resultg)){
						$gastos2 += $rowg['importe'];
					}
					
					
					$sqlcurso = "SELECT idcolegio FROM curso WHERE id='$idcurso'";
					$resultcurso = posgre_query($sqlcurso);
					if ($rowcurso = pg_fetch_array($resultcurso)){
						$idcolegioorganizador = $rowcurso['idcolegio'];
					}
					
					$porcentajegastos=0;
					
					if ($gastos>0){
						$porcentajegastos2 = ($gastos2/$recaudado2)*100;
			
						if (($porcentajegastos2>90)||($recaudado2==0)){
							$porcentajegastos2=90;
						}
					}
					
					$porcentajerestante2 = 100-$porcentajecaja-$porcentajegastos2;
										
					$dedicadogastos2=($precio2*$porcentajegastos2)/100;
					$dedicadocaja2=($precio2*$porcentajecaja)/100;
					
					$neto2=($precio2*$porcentajerestante2)/100;
					
					if (($neto2==0)&&($porcentajerestante2<=0)){
						$gastosotros = 0;
					}
					else{
						$gastosotros = $cantidadOtrosCursosPorAlumno*$pagados2;
					}
					
					$neto2 -= $gastosotros;

					$dedicadoorganizador2 = $neto2*$porcentajeorganizador;
					$dedicadoosocio2 = $neto2*$porcentajesocio;
					
					$totalcprecio += $precio2;
					$totalccaja += $dedicadocaja2;
					$totalcdedicadogastos += $dedicadogastos2;
					$totalcotrosgastos += $gastosotros;
					$totalcneto += $neto2;
					$totalcorganizador += $dedicadoorganizador2;
					$totalcsocio += $dedicadoosocio2;
					
					?>
					<TR>
					<td style="text-align: left !important;" ><?=$idcurso?> - <?=$nombrecurso?></td>
					<td><?=cambiaf_a_normal($fechainiciocurso)?></td>
					<td><?=$pagados2?></td>
					<td><? echo number_format($precio2, 2, '.', ''); ?>€</td>
					<td><? echo number_format($dedicadocaja2, 2, '.', ''); ?>€</td>
					<td><? echo number_format($dedicadogastos2, 2, '.', ''); ?>€</td>
					<td><? echo number_format($gastosotros, 2, '.', ''); ?>€</td>
					<td><? echo number_format($neto2, 2, '.', ''); ?>€</td>
					<? /* <td><? echo number_format($dedicadoorganizador2, 2, '.', ''); ?>€</td> */ ?>	
					<td><? echo number_format($dedicadoosocio2, 2, '.', ''); ?>€</td>
					</TR> 
					
					<?
					
					$totalalumnos += $pagados2;
				}
				
				
				?>
				<TR style="font-size:16px;">
					<td><b>Total <?=$nombrecolegio?></b></td>
					<td></td>
					<td><?=$pagados?></td>
					<td><? echo number_format($totalcprecio, 2, '.', ''); ?>€</td>
					<td><? echo number_format($totalccaja, 2, '.', ''); ?>€</td>
					<td><? echo number_format($totalcdedicadogastos, 2, '.', ''); ?>€</td>
					<td><? echo number_format($totalcotrosgastos, 2, '.', ''); ?>€</td>
					<td><? echo number_format($totalcneto, 2, '.', ''); ?>€</td>
					<? /* <td><b><? echo number_format($totalcorganizador, 2, '.', ''); ?>€</b></td> */ ?>	
					<td><b><? echo number_format($totalcsocio, 2, '.', ''); ?>€</b></td>
				</TR> 
				
				<TR height='50'>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<? /* <td></td> */ ?>		
					<td></td>
				</TR> 
				<?
				
				$totalbruto += $totalcprecio;
				$totalgastos += $totalcdedicadogastos;
				$totalcaja += $totalccaja;
				$totalotrosgastos += $totalcotrosgastos;
				$totalneto += $totalcneto;
				$totalorganizador += $totalcorganizador;
				$totalsocio += $totalcsocio;
				
			}
		}
		
		$sql2 = "SELECT * FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2)) AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado)";
		$result2 = posgre_query($sql2);
		
		$pagados = pg_num_rows($result2);
			
		$precio = 0;
		while ($row2 = pg_fetch_array($result2)){			
			$precio += $row2['precio'];
		}
		
		if ($pagados > 0){

		?>
		
		
				<TR>
					<td><b>No colegiados</b></td>
					<th>Fecha inicio</th>
					<th>Alumnos (que pagan)</th>
					<th>Importe bruto</th>
					<th>Dedicado a activatie</th>
					<th>Dedicado a gastos</th>
					<th>Gastos de otros cursos</th>
					<th>Importe neto</th>
					<? /* <th>Dedicado a organizadores</th> */ ?>	
					<th>Beneficio socio</th>
				</TR> 
				
		<?		
				
				 			
				$sql2x = "SELECT id as idcurso FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado AND id IN (SELECT idcurso FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2 ORDER BY fecha_inicio)) GROUP BY idcurso) ORDER BY fecha_inicio";
				$result2x = posgre_query($sql2x);
				
				$totalcprecio = 0;
				$totalccaja = 0;
				$totalcdedicadogastos = 0;
				$totalcneto = 0;
				$totalcotrosgastos = 0;
				$totalcorganizador = 0;
				$totalcsocio = 0;
				
				while ($row2x = pg_fetch_array($result2x)){
					$idcurso = $row2x['idcurso'];
					$sqlc = "SELECT nombre,fecha_inicio FROM curso WHERE id='$idcurso'";
					$resultc = posgre_query($sqlc);
					$rowc = pg_fetch_array($resultc);
					$nombrecurso = $rowc['nombre'];
					$fechainiciocurso = $rowc['fecha_inicio'];
					
					$sql3x = "SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2 ))  AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado)";
					$result3x = posgre_query($sql3x);
					$pagados2 = pg_num_rows($result3x);
					
					$precio2=0;
					while ($row3x = pg_fetch_array($result3x)){
						$precio2+=$row3x['precio'];
					}
					
					$sql = "SELECT sum(precio) as recaudado2 FROM curso_usuario WHERE idcurso='$idcurso' AND borrado=0 AND estado=0 AND espera=0 AND pagado=1 AND nivel='5' AND ((modalidad=3 $sqlfecha1b $sqlfecha2b) OR idcurso IN (SELECT id FROM curso WHERE modalidad!='3' $sqlfecha1 $sqlfecha2))  AND idcurso IN (SELECT id FROM curso WHERE borrado=0 AND estado<>5 $sqlliquidado) ";			
					$result = posgre_query($sql);
					
					$recaudado2=0;
					if ($row = pg_fetch_array($result)){
						$recaudado2 = $row['recaudado2'];
					}
								
					$sqlgastos = "SELECT * FROM curso_gastos WHERE idcurso='$idcurso' AND borrado=0 $sqlfechagastos1 $sqlfechagastos2";
					$resultg = posgre_query($sqlgastos);
					echo pg_last_error();
					$gastos2 = 0;
					while ($rowg = pg_fetch_array($resultg)){
						$gastos2 += $rowg['importe'];
					}
					
					
					$sqlcurso = "SELECT idcolegio FROM curso WHERE id='$idcurso'";
					$resultcurso = posgre_query($sqlcurso);
					if ($rowcurso = pg_fetch_array($resultcurso)){
						$idcolegioorganizador = $rowcurso['idcolegio'];
					}
					
					$porcentajegastos=0;
					
					if ($gastos>0){
						
						$porcentajegastos2 = ($gastos2/$recaudado2)*100;
			
						if (($porcentajegastos2>90)||($recaudado2==0)){
							$porcentajegastos2=90;
						}
					}
					
					$porcentajerestante2 = 100-$porcentajecaja-$porcentajegastos2;
										
					$dedicadogastos2=($precio2*$porcentajegastos2)/100;
					$dedicadocaja2=($precio2*$porcentajecaja)/100;
							
					$neto2=($precio2*$porcentajerestante2)/100;		
					
					if (($neto2==0)&&($porcentajerestante2<=0)){
						$gastosotros = 0;
					}
					else{
						$gastosotros = $cantidadOtrosCursosPorAlumno*$pagados2;
					}
								
					$neto2 -= $gastosotros;
					
					$dedicadoorganizador2 = $neto2*$porcentajeorganizador;
					$dedicadoosocio2 = $neto2*$porcentajesocio;
					
					$totalcprecio += $precio2;
					$totalccaja += $dedicadocaja2;
					$totalcdedicadogastos += $dedicadogastos2;
					$totalcneto += $neto2;
					$totalcotrosgastos += $gastosotros;
					$totalcorganizador += $dedicadoorganizador2;
					$totalcsocio += $dedicadoosocio2;
					
					?>
					<TR>
					<td style="text-align: left !important;" ><?=$idcurso?> - <?=$nombrecurso?></td>
					<td><?=cambiaf_a_normal($fechainiciocurso)?></td>
					<td><?=$pagados2?></td>
					<td><? echo number_format($precio2, 2, '.', ''); ?>€</td>
					<td><? echo number_format($dedicadocaja2, 2, '.', ''); ?>€</td>
					<td><? echo number_format($dedicadogastos2, 2, '.', ''); ?>€</td>
					<td><? echo number_format($gastosotros, 2, '.', ''); ?>€</td>
					<td><? echo number_format($neto2, 2, '.', ''); ?>€</td>
					<? /* <td><? echo number_format($dedicadoorganizador2, 2, '.', ''); ?>€</td> */ ?>		
					<td><? echo number_format($dedicadoosocio2, 2, '.', ''); ?>€</td>
					</TR> 
					
					<?
					
					$totalalumnos += $pagados2;
				}	
			
			?>
			<TR style="font-size:16px;">
				<td><b>Total No colegiados</b></td>
				<td></td>
				<td><?=$pagados?></td>
				<td><? echo number_format($totalcprecio, 2, '.', ''); ?>€</td>
				<td><? echo number_format($totalccaja, 2, '.', ''); ?>€</td>
				<td><? echo number_format($totalcdedicadogastos, 2, '.', ''); ?>€</td>
				<td><? echo number_format($totalcotrosgastos, 2, '.', ''); ?>€</td>
				<td><? echo number_format($totalcneto, 2, '.', ''); ?>€</td>
				<? /* <td><b><? echo number_format($totalcorganizador, 2, '.', ''); ?>€</b></td> */ ?>	
				<td><b><? echo number_format($totalcsocio, 2, '.', ''); ?>€</b></td>
			</TR> 
			
			<TR height='50'>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<? /* <td></td> */ ?>		
				<td></td>
			</TR> 
				
			<?
		
			$totalbruto += $totalcprecio;
			$totalgastos += $totalcdedicadogastos;
			$totalcaja += $totalccaja;
			$totalsocio += $totalcsocio;
			$totalotrosgastos += $totalcotrosgastos;
			$totalneto += $totalcneto;
			$totalorganizador += $totalcorganizador;
		}
		?>
		
		<TR style="font-size:16px;">
			<td><b>TOTAL</b></td>
			<td></td>
			<td><b><?=$totalalumnos?></b></td>
			<td><b><? echo number_format($totalbruto, 2, '.', ''); ?>€</b></td>
			<td><b><? echo number_format($totalcaja, 2, '.', ''); ?>€</b></td>
			<td><b><? echo number_format($totalgastos, 2, '.', ''); ?>€</b></td>
			<td><b><? echo number_format($totalotrosgastos, 2, '.', ''); ?>€</b></td>
			<td><b><? echo number_format($totalneto, 2, '.', ''); ?>€</b></td>
			<? /* <td><b><? echo number_format($totalorganizador, 2, '.', ''); ?>€</b></td> */ ?>	
			<td><b><? echo number_format($totalsocio, 2, '.', ''); ?>€</b></td>
		</TR> 
			
		</TABLE>

		
	</div>
	<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->
<?
include("plantillaweb02admin.php"); 
?>