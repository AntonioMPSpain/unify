<?
session_start();

$idcurso=strip_tags($_REQUEST['idcurso']); 
if ($idcurso=="") {
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}

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
$rowcurso=pg_query($linka,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
$curso= pg_fetch_array($rowcurso);

include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<div class="bloque-lateral acciones">		
	<p>
		<a href="informe_curso.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
	</div>
	<h2 class="titulonoticia">Reparto de gastos y beneficios <?=$curso["nombre"];?></h2>
		<br />
		<? 
		include("_aya_mensaje_session.php"); 
		$_SESSION[error]=""; 
		
		$inviable=false;
		$porcentajeorganizador = 0.3;
		$porcentajesocio = 0.7;
		$porcentajecolaborador = 0.35;
		
		$idcolegioOrganizador = $curso['idcolegio'];
		$sql = "SELECT * FROM usuario WHERE id='$idcolegioOrganizador'";			
		$result = posgre_query($sql);
		
		if ($row = pg_fetch_array($result)){
			$organizador = $row['nombre'];
		}
		
		$sql = "SELECT sum(precio) as recaudado FROM curso_usuario WHERE idcurso='$idcurso' AND estado=0 AND espera=0 AND pagado=1 AND nivel='5'  ";			
		$result = posgre_query($sql);
		
		$recaudado=0;
		if ($row = pg_fetch_array($result)){
			$recaudado = $row['recaudado'];

		}
		
		/** Gastos **/
		$sqlg = "SELECT * FROM curso_gastos WHERE borrado=0 AND idcurso='$idcurso'";
		$resultg = posgre_query($sqlg);
		
		$gastos = 0;
		while ($rowg = pg_fetch_array($resultg)){
			$gastos += $rowg['importe'];
		}
		
		
		$porcentajegastos = ($gastos/$recaudado)*100;
		
		if (($porcentajegastos>90)||($recaudado==0)){
			$porcentajegastos=90;
		}
		
		$porcentajecaja = 10;
		
		
		$porcentajerestante = 100-$porcentajecaja-$porcentajegastos;
		
		?>
		<span>Organizador: <strong><?=$organizador?></strong></span><br>
		<span>Gastos de organización: <a href='cursos_gastos.php?idcurso=<?=$idcurso?>'><strong><? echo number_format($gastos, 2, '.', ''); ?>€</strong></a></span><br>
		<span>Total recaudado: <strong><? echo number_format($recaudado, 2, '.', ''); ?>€</strong></span><br><br>
		<span>Porcentaje de recaudado dedicados a activatie: <strong><? echo number_format($porcentajecaja, 2, '.', '');?>%</strong> sobre bruto</span><br>
		<span>Porcentaje de recaudado dedicados a gastos: <strong><? echo number_format($porcentajegastos, 2, '.', '');?>%</strong> sobre bruto</span><br>
		<? if ($recaudado < ($gastos+($recaudado*($porcentajecaja/100)))){ 
			$inviable=true;
			?>
			<strong style="color:red;"> AVISO : Lo recaudado no supera la cantidad para cubrir gastos de organización</strong><br>
		<? } ?>
		
		<br><span>Porcentaje dedicado a neto: <strong><? echo number_format(100-(($porcentajecaja + $porcentajegastos)), 2, '.', '');?>%</strong> sobre bruto</span><br>
		<br><span>Porcentaje dedicado a organizador: <strong><?=$porcentajeorganizador*100?>%</strong> sobre neto</span><br>
		<span>Porcentaje obtenido por socio: <strong><?=$porcentajesocio*100?>%</strong> sobre neto</span><br>
		<p>En el caso de no colegiados, el <?=$porcentajesocio*100?>% de beneficio por socio, se dedica a activatie</p><br>
		<br>
		
		<h3>Beneficios por colegio</h3>
		<TABLE style = "text-align:center;"> 
		<TR>
			<th>Colegio</th>
			<th>Alumnos</th>
			<th>Importe bruto</th>
			<th>Dedicado a activatie</th>
			<th>Dedicado a gastos</th>
			<th>Importe neto</th>
			<th>Dedicado a organizador</th>
			<th>Beneficio por socio</th>
		</TR> 
		<? 
		
		$totalalumnos = 0;
		$totalbruto = 0;
		$totalgastos = 0;
		$totalcaja = 0;
		$totalneto = 0;
		$totalorganizador = 0;
		$totalsocio = 0;
		
		$sql3 = "SELECT * FROM usuario WHERE borrado=0 AND nivel=2 ORDER BY nombre";
		$result3 = posgre_query($sql3);
					
		while ($row3 = pg_fetch_array($result3)){
			$idcolegio = $row3['id'];
			$nombrecolegio = $row3['nombre'];
			
			$sql2 = "SELECT precio FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND pagado=1 AND idcurso='$idcurso' AND nivel='5' ";
			$result2 = posgre_query($sql2);
			
			$pagados = pg_num_rows($result2);
			
			$precio = 0;
			while ($row2 = pg_fetch_array($result2)){
				$precio += $row2['precio'];
	
			}
						
			if ($pagados > 0){
			
				$dedicadogastos=($precio*$porcentajegastos)/100;
				$dedicadocaja=($precio*$porcentajecaja)/100;
				$neto=($precio*$porcentajerestante)/100;
				$dedicadoorganizador = $neto*$porcentajeorganizador;
				$dedicadoosocio = $neto*$porcentajesocio;
			
				?>
				<TR>
					<td><?=$nombrecolegio?></td>
					<td><?=$pagados?></td>
					<td><? echo number_format($precio, 2, '.', ''); ?>€</td>
					<td><? echo number_format($dedicadocaja, 2, '.', ''); ?>€</td>
					<td><? echo number_format($dedicadogastos, 2, '.', ''); ?>€</td>
					<td><? echo number_format($neto, 2, '.', ''); ?>€</td>
					<td><? echo number_format($dedicadoorganizador, 2, '.', ''); ?>€</td>
					<td><? echo number_format($dedicadoosocio, 2, '.', ''); ?>€</td>
				</TR> 
				
				<?
				
				$totalalumnos += $pagados;
				$totalbruto += $precio;
				$totalgastos += $dedicadogastos;
				$totalcaja += $dedicadocaja;
				$totalneto += $neto;
				$totalorganizador += $dedicadoorganizador;
				$totalsocio += $dedicadoosocio;
				
				
			}
		}
			
		$sql2 = "SELECT precio FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND pagado=1 AND idcurso='$idcurso' AND nivel='5'";
		$result2 = posgre_query($sql2);
		
		$pagados = pg_num_rows($result2);
			
		$precio = 0;
		while ($row2 = pg_fetch_array($result2)){			
			$precio += $row2['precio'];

		}
		
		if ($pagados > 0){
		
			$dedicadogastos=($precio*$porcentajegastos)/100;
			$dedicadocaja=($precio*$porcentajecaja)/100;
			$neto=($precio*$porcentajerestante)/100;
			$dedicadoorganizador = $neto*$porcentajeorganizador;
			$dedicadosocio = $neto*$porcentajesocio;
			$totalactivatie = $dedicadocaja+$dedicadosocio;
			?>
			<TR>
				<td>No colegiados</td>
				<td><?=$pagados?></td>
				<td><? echo number_format($precio, 2, '.', ''); ?>€</td>
				<td><? echo number_format($dedicadocaja, 2, '.', ''); ?>€</td>
				<td><? echo number_format($dedicadogastos, 2, '.', ''); ?>€</td>
				<td><? echo number_format($neto, 2, '.', ''); ?>€</td>
				<td><? echo number_format($dedicadoorganizador, 2, '.', ''); ?>€</td>
				<td><? echo number_format($dedicadosocio, 2, '.', ''); ?>€</td>
			</TR> 
			
			<?
		
			$totalalumnos += $pagados;
			$totalbruto += $precio;
			$totalgastos += $dedicadogastos;
			$totalcaja += $dedicadocaja;
			$totalsocio += $dedicadosocio;
			$totalneto += $neto;
			$totalorganizador += $dedicadoorganizador;
		}
		?>
		
		<TR>
			<td><b>Total</b></td>
			<td><b><?=$totalalumnos?></b></td>
			<td><b><? echo number_format($totalbruto, 2, '.', ''); ?>€</b></td>
			<td><b><? echo number_format($totalcaja, 2, '.', ''); ?>€</b></td>
			<td><b <? if ($inviable) { ?> style="color:red;" <? } ?> ><? echo number_format($totalgastos, 2, '.', ''); ?>€</b></td>
			<td><b><? echo number_format($totalneto, 2, '.', ''); ?>€</b></td>
			<td><b><? echo number_format($totalorganizador, 2, '.', ''); ?>€</b></td>
			<td><b><? echo number_format($totalsocio, 2, '.', ''); ?>€</b></td>
		</TR> 
			
		</TABLE>
		
		<br />			<br />
		<a href="informe_curso_reparto_pdf.php?idcurso=<?=$idcurso?>" title="resumen" class="btn btn-primary">Descargar PDF</a>
		<br />			

	</div>
	<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->
<?
include("plantillaweb02admin.php"); 
?>