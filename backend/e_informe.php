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

$fecha1 = $_POST['fecha1'];
$fecha2 = $_POST['fecha2'];

$sqlfecha1="";
if ($fecha1<>""){
	$sqlfecha1 = " AND ((modalidad!=3 AND fecha_inicio>='$fecha1') OR (modalidad=3 AND fecha_publicacion>='$fecha1')) ";
}

$sqlfecha2="";
if ($fecha2<>""){	
	$sqlfecha2 = " AND ((modalidad!=3 AND fecha_inicio<='$fecha2') OR (modalidad=3 AND fecha_publicacion<='$fecha2')) ";

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

	<h2>Informe encuestas</h2>
	<br>
	<? if (!isset($_GET['pdf'])){ ?>
		<form  class="form-horizontal" action="e_informe.php" enctype="multipart/form-data" method="post">
		
		<div>
			Fecha Inicio: <input style="width:130px;" type="date" id="fecha1" name="fecha1" value="<?=$fecha1?>" placeholder="01/01/2000">
			&nbsp;Fecha Fin: <input style="width:130px;" type="date" id="fecha2" name="fecha2" value="<?=$fecha2?>" placeholder="01/01/2000">
			<button type="submit" class="btn btn-important">Ver</button>
				
		</div>
		</form>	
	<? } ?>
	<?
	if (isset($_GET['pdf'])){ 
		$fecha = date("d-m-Y"); ?> 	
		<div>Fecha informe: <b><?=$fecha?></b></div>
	<? 
	}
		
	$sql = "SELECT * FROM encuestas WHERE borrado=0 AND plantilla=1 AND idcurso IN (SELECT id FROM curso WHERE borrado=0 $sqlfecha1 $sqlfecha2) $sqlcolegio ORDER BY fechacreacion DESC";
	$result = posgre_query($sql);
	$numcursos = pg_num_rows($result);
	
	?>
	
	<p>N&uacute;mero de encuestas: <b><?=$numcursos?></b></p>
	<table class="align-center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<th style="width:40%">Actividad</th>
			<th>Opini&oacute;n global</th>
			<th>Exposici&oacute;n <br>de los profesores</th>
			<th>Opini&oacute;n <br>sobre los contenidos</th>
			<th>Cumplimiento <br>de expectativas</th>
		</tr>
	
	
	<?
	
	$sum1=0;
	$sum2=0;
	$sum3=0;
	$sum4=0;
	
	$numcursos1=$numcursos;
	$numcursos2=$numcursos;
	$numcursos3=$numcursos;
	$numcursos4=$numcursos;
	
	while ($row = pg_fetch_array($result)){
		$idcurso = $row["idcurso"];
		$id = $row["id"];
		$sqlcurso = "SELECT * FROM curso WHERE id='$idcurso'";
		$resultcurso = posgre_query($sqlcurso);
		$rowcurso = pg_fetch_array($resultcurso);
		$nombrecurso = $rowcurso['nombre'];
		$id_categoria_moodle = $rowcurso["id_categoria_moodle"];
		
		if (isset($_GET['pdf'])){ 
			$nombrecurso = substr($nombrecurso, 0, 40)."...";  
		}
		?> 
		
		<tr>
			<td><a href="e_resultados.php?id=<?=$id?>"><?=$idcurso?>/<?=$id_categoria_moodle?><br><?=$nombrecurso?></a></td>
			
		<?
		
		$sql3 = "SELECT * FROM encuestas_preguntas WHERE borrado=0 AND idencuesta='$id' ORDER BY orden LIMIT 4";
		$result3 = posgre_query($sql3);
		$resul = 0;
		while ($rowpreguntas = pg_fetch_array($result3)){ 
	
			$idpregunta = $rowpreguntas['id'];
			$pregunta = $rowpreguntas['texto'];
			$tipo = $rowpreguntas['tipo'];
			$obligatorio = $rowpreguntas['obligatorio'];
			$respuesta = $rowpreguntas['respuestas'];
			$orden = $rowpreguntas['orden'];
			
			$sql8 = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE borrado=0 AND idpregunta IN ($idpregunta))";
			$result8 = posgre_query($sql8);
			$numrespuestas = pg_num_rows($result8);
			
			$sql4 = "SELECT * FROM encuestas_opciones WHERE borrado=0 AND idpregunta='$idpregunta' ORDER BY orden";
			$result4 = posgre_query($sql4);
			$resul = 0;
			if ($tipo==1){
				
				$textoe="";
				$i=0;
				while ($rowopciones = pg_fetch_array($result4)){
					$idopcion = $rowopciones['id'];
					$nombreopcion = $rowopciones['fila'];
					$sqlx = "SELECT * FROM encuestas_respuestas WHERE borrado=0 AND idopcion='$idopcion'";
					$resultx = posgre_query($sqlx);
					$numopciones = pg_num_rows($resultx);
					$porcentaje = ($numopciones/$numrespuestas)*100;
					
					$i++;
					$resul = $resul + ($numopciones*$i);
					
					$respuesta=1;
					$sumamaxima=2;
					$textoe=$textoe."['".trim($nombreopcion)."', ".$numopciones."],";
					
				}
				
				$resul = ($resul/$numrespuestas)*2*10;	
				
			}
			elseif (($tipo==2)||($tipo==4)){
				 
				$sql5 = "SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='1' ORDER BY orden, id";
				$result5=posgre_query($sql5); 
				while($row = pg_fetch_array($result5)) { 
					$col = $row['fila'];
			
				}
				
				$resulten=posgre_query("SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='0' ORDER BY orden, id"); 
				$sumtotal = 0;
				$k = 0;
				while($row = pg_fetch_array($resulten)) { 
					$idopcion = $row['id'];
					$fila = $row['fila'];
		
					$sqly = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN ($idopcion)";
					$resulty = posgre_query($sqly);
					$numrespuestas = pg_num_rows($resulty);
				
					$result5=posgre_query($sql5); 
					$total = 0;
					$i=0;
					
					while($rowcolumnas = pg_fetch_array($result5)) {
						$idopcioncolumna = $rowcolumnas['id'];
						$sqlx = "SELECT * FROM encuestas_respuestas WHERE borrado=0 AND idopcion='$idopcion' AND idopcioncolumna='$idopcioncolumna'";
						$resultx = posgre_query($sqlx);
						$numopciones = pg_num_rows($resultx);
						$porcentaje = ($numopciones/$numrespuestas)*100;
						$porcentaje = number_format($porcentaje, 2, '.', '');
						
						$i++;
						$total = $total + ($numopciones*$i);
					 
					} 
					
					$total = number_format(($total/$numrespuestas)*2*10, 2, '.', '');
					
				
					$sumtotal += $total;
					
								
					$k++;
						
				} 
				
				$resul = $sumtotal/$k;
				
			}
			if ($resul>0){
				
				?> <td><?=number_format($resul, 2, '.', '');?>%</td> <?
				$variable = "sum".$orden;
				$$variable+=$resul;
			}
			else{
				?> <td></td> <?
				$variable = "numcursos".$orden;
				$$variable--;
			}
		} 
		
		?> </tr> <?
	}

	?>
	<tr>
		<td><b>Total</b></td>
		<td><b><?=number_format($sum1/$numcursos1, 2, '.', '')?>%</b></td>
		<td><b><?=number_format($sum2/$numcursos2, 2, '.', '')?>%</b></td>
		<td><b><?=number_format($sum3/$numcursos3, 2, '.', '')?>%</b></td>
		<td><b><?=number_format($sum4/$numcursos4, 2, '.', '')?>%</b></td>
	</tr>
	</table>
	</div>
	<? if (!isset($_GET['pdf'])){ ?>
	<a href="e_informe.php?pdf" class="btn btn-primary">Ver en PDF</a>
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