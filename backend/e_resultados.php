<?
include("_funciones.php"); 
include("_cone.php");

////////// Filtros de nivel por usuario //////////////////////
session_start();

if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$idusuario = $idcolegio;
		$sqlcolegio = " AND idcurso IN (SELECT id FROM curso WHERE idcolegio='$idcolegio') ";
	}else{
		$_SESSION[esterror]="Par�metros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
}
else{
	$_SESSION[esterror]="Par�metros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////  

$id=strip_tags(trim($_REQUEST['id'])); //idencuesta

if ($id==""){
	echo "Error de ID1";
	exit();
}


$idusuario=strip_tags(trim($_REQUEST['idusuario'])); //idencuesta
$sqlusuario="";
if ($idusuario<>""){
	$sqlusuario = " AND idusuario='$idusuario' ";
}

$sql = "SELECT * FROM encuestas WHERE borrado=0 AND id='$id';";
$result=posgre_query($sql);

$row = pg_fetch_array($result); 
$nombre = $row['nombre'];
$idcurso = $row['idcurso'];
$plantilla = $row['plantilla'];


if ($idcurso<>""){
	$sql2 = "SELECT * FROM curso WHERE id='$idcurso'";
	$result2 = posgre_query($sql2);
	$row2 = pg_fetch_array($result2);
	$nombrecurso = $row2['nombre'];
	$fecha_inicio = cambiaf_a_normal($row2['fecha_inicio']);
}

$sql3 = "SELECT * FROM encuestas_preguntas WHERE borrado=0 AND idencuesta='$id' ORDER BY orden";
$result3 = posgre_query($sql3);
if (pg_num_rows($result3)==0){
	echo "No hay preguntas para esta encuesta";
	exit();
}

$titulo1="encuestas";
$titulo2="activatie";
if (!isset($_GET['pdf'])){
include("plantillaweb01admin.php");
}
ob_start();
?>		

<div class="grid-12 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">	

	<h2>RESULTADOS: <?=$nombre?></h2>
	
	<?
	if (isset($_GET['pdf'])){ 
		$fecha = date("d-m-Y"); ?> 	
		<div>Fecha informe: <b><?=$fecha?></b></div>
	<? 
	}
	
	if (($idcurso<>0)&&($idcurso<>"")){ ?>
		<br>
		<h3>Curso <?=$nombrecurso?><br>
			<? if ($fecha_inicio<>""){ ?>
				<div style="font-weight:normal; font-size:12px;">Fecha inicio: <?=$fecha_inicio?></div>
			<? } ?>
			
		
		</h3>
	<? } 

	
	$sql = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE idpregunta IN (SELECT id FROM encuestas_preguntas WHERE idencuesta IN (SELECT id FROM encuestas WHERE borrado=0 AND id='$id'))) $sqlusuario GROUP BY idusuario";
	$result = posgre_query($sql);
	$numusuarios = pg_num_rows($result);
	
	$result=posgre_query("SELECT * FROM curso_usuario WHERE nivel='5' AND idcurso='$idcurso' AND borrado=0 AND estado=0 AND espera=0 AND (precio=0 OR pagado=1) ORDER BY (SELECT id FROM curso WHERE borrado=0 AND curso_usuario.id=curso.id ORDER BY fecha_inicio DESC)") ;
	$numinscritos = pg_num_rows($result);
	
	?>
	
	<? if ($plantilla==1){ ?>
		<p>Usuarios inscritos en el curso: <b><?=$numinscritos?> usuarios</b></p>
	<? } ?>
	<p>Han realizado la encuesta: <b><?=$numusuarios?> usuarios</b></p>
	
	<? if ($plantilla==1){ ?>
		<p>Usuarios que faltan por realizar la encuesta: <b><? echo $numinscritos-$numusuarios;?> usuarios</b></p>
	<? } ?>
	
	<form action="encuesta.php?accion=guardar" method="POST">
		<input type="hidden" name="id" value=<?=$id?>>
		<? 
		$j = 1;
		while ($rowpreguntas = pg_fetch_array($result3)){ 
		
			$idpregunta = $rowpreguntas['id'];
			$pregunta = $rowpreguntas['texto'];
			$tipo = $rowpreguntas['tipo'];
			$obligatorio = $rowpreguntas['obligatorio'];
			$respuesta = $rowpreguntas['respuestas'];
			
			if ($respuesta==2){
				$tipoinput = "checkbox";
			}
			else{
				$tipoinput = "radio";
			}	
			
			if ($obligatorio==1){
				$textoobligatorio="";
			}
			else{
				$textoobligatorio="[Opcional]";
			}
			
			$sql7 = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE borrado=0 AND idpregunta IN ($idpregunta)) $sqlusuario  GROUP BY idusuario";
			$result7 = posgre_query($sql7);
			$numusuarios = pg_num_rows($result7);
	
			$sql8 = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE borrado=0 AND idpregunta IN ($idpregunta)) $sqlusuario ";
			$result8 = posgre_query($sql8);
			$numrespuestas = pg_num_rows($result8);
	
			if ($tipo==3){
				$sql7 = "SELECT * FROM encuestas_respuestas WHERE textoabierto!='' AND borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE borrado=0 AND idpregunta='$idpregunta') $sqlusuario ";
				$result7 = posgre_query($sql7);
				$numusuarios = pg_num_rows($result7);
				$numrespuestas = pg_num_rows($result7);
			}
	
	
			?>
				
			<? if ($tipo<>"6"){	?>
				<page>
				<hr>	
				<h4><?=$j?>. <?=$textoobligatorio?> <?=$pregunta?></h4>
				<br>
			
			
			<p>Respondida por: <b><?=$numusuarios?> usuarios</b><br>
			Total respuestas: <b><?=$numrespuestas?> respuestas</b></p>
			
			<? } ?>
			
			<? if (!isset($_GET['pdf'])){ ?> 	
				<div style="margin-top:-90px; float:right;" id="chart_div_<?=$idpregunta?>"></div>
			<? 
			}
			
			$sql4 = "SELECT * FROM encuestas_opciones WHERE borrado=0 AND idpregunta='$idpregunta' ORDER BY orden";
			$result4 = posgre_query($sql4);
			if ($tipo==1){
				$textoe="";
				$total = 0;
				$i=0;
				while ($rowopciones = pg_fetch_array($result4)){
					$idopcion = $rowopciones['id'];
					$nombreopcion = $rowopciones['fila'];
					$opcioncontexto = $rowopciones['opcioncontexto'];
					$sqlx = "SELECT * FROM encuestas_respuestas WHERE borrado=0 AND idopcion='$idopcion' $sqlusuario ";
					$resultx = posgre_query($sqlx);
					$numopciones = pg_num_rows($resultx);
					$porcentaje = ($numopciones/$numrespuestas)*100;
					
					$i++;
					$total = $total + ($numopciones*$i);
					
					$respuesta=1;
					$sumamaxima=2;
					$textoe=$textoe."['".trim($nombreopcion)."', ".$numopciones."],";
					
					?>
					 &nbsp;&nbsp;&nbsp;&nbsp;<?=$nombreopcion?> - <b><?=$numopciones?> (<?=number_format($porcentaje, 2, '.', '');?>%)</b><br> 					
					<?
					
					if ($opcioncontexto==1){
						while ($opcionesx = pg_fetch_array($resultx)){
						
							$textoabierto = $opcionesx['textoabierto'];
					
							?>
							<p style="font-size:11px;">&nbsp;&nbsp;&nbsp;&nbsp; <b>&middot;</b> <?=$textoabierto?></p>
							<?
								
						}
					}
				}
				
				$total = ($total/$numrespuestas)*2*10;	
				
				
				
				if ($plantilla==1){
					?><br> Valoraci&oacute;n media: <b><?=number_format($total, 2, '.', '');?>%</b><br><?
				}
				if (!isset($_GET['pdf'])){ ?> 					
					<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">
					  google.load('visualization', '1.0', {'packages':['corechart']});
					  google.setOnLoadCallback(drawChart);
					  function drawChart() {
						var data = new google.visualization.DataTable();
						data.addColumn('string', 'Topping');
						data.addColumn('number', 'Slices');
						data.addRows([
						  <?=$textoe?>
						]);
						var options = {'title':'',
									   'width':300,
									   'height':200,
									   'is3D': true};
						var chart = new google.visualization.PieChart(document.getElementById('chart_div_<?=$idpregunta?>'));
						chart.draw(data, options);
					  }
					</script>
				<? 
				}
			}
			elseif (($tipo==2)||($tipo==4)){
				?>
				<table class="align-center" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<th>&nbsp;</th>
				<? 
				$sql5 = "SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='1' ORDER BY orden, id";
				$result5=posgre_query($sql5); 
				while($row = pg_fetch_array($result5)) { 
					$col = $row['fila'];
				?>
					<th style="width:65px;"><?=$col?></th>
				<?
				}
				
					
					?>
					<th>Valoraci&oacute;n</th> 
					<?
				
				?>
				</tr>
				<?
				$result=posgre_query("SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='0' ORDER BY orden, id"); 
				$sumtotal = 0;
				$k = 0;
				while($row = pg_fetch_array($result)) { 
					$idopcion = $row['id'];
					$fila = $row['fila'];
		
					$sqly = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN ($idopcion) $sqlusuario ";
					$resulty = posgre_query($sqly);
					$numrespuestas = pg_num_rows($resulty);
				?>
					<tr>
						<td ><?=$fila?> <b>(<?=$numrespuestas?>)</b>&nbsp;&nbsp;</td>
						
						<?
						$result5=posgre_query($sql5); 
						$total = 0;
						$i=0;
						while($rowcolumnas = pg_fetch_array($result5)) {
							$idopcioncolumna = $rowcolumnas['id'];
							$sqlx = "SELECT * FROM encuestas_respuestas WHERE borrado=0 AND idopcion='$idopcion' AND idopcioncolumna='$idopcioncolumna' $sqlusuario ";
							$resultx = posgre_query($sqlx);
							$numopciones = pg_num_rows($resultx);
							$porcentaje = ($numopciones/$numrespuestas)*100;
							$porcentaje = number_format($porcentaje, 2, '.', '');
							
							$i++;
							$total = $total + ($numopciones*$i);
						?>
							<td><b><?=$numopciones?></b> (<?=$porcentaje?>%)</td>
						<? 
						} 
						
							$total = number_format(($total/$numrespuestas)*2*10, 2, '.', '');
							?>
							<td><b><?=$total?>%</b></td>
							<?
						
							$sumtotal += $total;
						
								
					$k++;
						?>
					
					</tr>	
				<? } ?>
				</table>
				<?
				
				if ($plantilla==1){
					?><br> Valoraci&oacute;n media: <b><?=number_format($sumtotal/$k, 2, '.', '');?>%</b><br><?
				}
			}
			elseif ($tipo==3){
				
				$sql6 = "SELECT * FROM encuestas_respuestas WHERE textoabierto!='' AND borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE borrado=0 AND idpregunta='$idpregunta') $sqlusuario ";
				$result6 = posgre_query($sql6);
				while ($rowrespuestas = pg_fetch_array($result6)){
					
					$textoabierto = $rowrespuestas['textoabierto'];
					
					?>
					<p style="font-size:11px;"><?=$textoabierto?></p>
					<?
				}
			}
			elseif ($tipo==5){
				?>
				<table class="align-center" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<th>&nbsp;</th>
				<? 
				$sql5 = "SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='0' ORDER BY orden, id";
				$result7=posgre_query($sql5); 
				$numopcionestotal = pg_num_rows($result7);
				
				for ($l=1; $l<=$numopcionestotal; $l++){
					
					?> <th style="width:65px;"><?=$l?></th> <?
				}
				
				?>
				
				
				<td>Valoraci&oacute;n</td>
				
				</tr>
				
				<?
				$respuestasabiertas = "";
				$k = 0;				
				$result7=posgre_query($sql5); 
				while($row = pg_fetch_array($result7)) { 
					$idopcion = $row['id'];
					$fila = $row['fila'];
		
					$total = 0;
				
					$sqly = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN ($idopcion) $sqlusuario ";
					$resulty = posgre_query($sqly);
					$numrespuestas = pg_num_rows($resulty);
				?>
					<tr>
						<td ><?=$fila?> <b>(<?=$numrespuestas?>)</b>&nbsp;&nbsp;</td>
						
						<?
						$primero = true;
						for ($l=1; $l<=$numopcionestotal; $l++){
							$sqlx = "SELECT * FROM encuestas_respuestas WHERE borrado=0 AND idopcion='$idopcion' AND preferencia='$l' $sqlusuario ";
							$resultx = posgre_query($sqlx);
							$numopciones = pg_num_rows($resultx);
							$porcentaje = ($numopciones/$numrespuestas)*100;
							
							$total += (($numopciones/$numrespuestas)*$l);
							
							
							
							while ($rowx = pg_fetch_array($resultx)){
								$textabiert = $rowx['textoabierto'];
								
								if ($textabiert!=""){
									
									if ($primero){
										$respuestasabiertas .= "<b>".$fila."</b><br>";
										$primero = false;
									}
									$respuestasabiertas .= "- ".$textabiert."<br>";
								}
							}							
						
							?> <td><b><?=$numopciones?></b> (<?=number_format($porcentaje, 2, '.', '')?>%)</td> <?
						} 	
					$k++;
					
					
					?>
					<td><?=number_format($total, 2, '.', ''); ?></td>
					
					
					</tr>	
				<? } ?>
				</table>
				<?
				echo $respuestasabiertas;
				
				if ($plantilla==1){
					?><br> Valoraci&oacute;n media: <b><?=number_format($sumtotal/$k, 2, '.', '');?>%</b><br><?
				}
				
			}
			elseif ($tipo==6){ ?>
				<b><?=$pregunta?></b>
			<? } ?>
			
			

			<br>		
			<? 		
			if ($tipo<>6){
				$j++;	
				?> </page> <?
			}
		} 
		?> 
	</form>
	</div>
	<? if (!isset($_GET['pdf'])){ ?>
	<a href="e_resultados.php?pdf&id=<?=$id?>" class="btn btn-primary">Ver en PDF</a>
	<? } ?>
</div>

<? 

if (isset($_GET['pdf'])){ 
	$content = ob_get_clean();

	require_once('../librerias/html2pdf/html2pdf.class.php');
	try
	{
		$html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', 10,10,10,10);
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		$html2pdf->createIndex('', 0, 0, false, false, 1);
		$html2pdf->Output('Resultados_'.$nombre.'.pdf');
	}
	catch(HTML2PDF_exception $e) {
		echo $e;
		exit;
	}
}

include("plantillaweb02admin.php"); 
?>