<?
include("_cone.php"); 
include("_funciones.php");
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

$usuarioPruebas = 14196;

$idcurso=strip_tags(trim($_REQUEST['idcurso']));

if ($idcurso==""){ 
	$_SESSION[esterror]="Error0";	
	header("Location: index.php");
	exit();
}

////////// Filtros de nivel por usuario //////////////////////
session_start();

if (($_SESSION[nivel]==4)||($_SESSION[nivel]==3)) { //Admin Total
	$idusuario = $_SESSION['idusuario'];
	$sql = "SELECT * FROM curso_usuario WHERE borrado=0 AND idusuario='$idusuario' AND idcurso='$idcurso' AND (pagado=1 OR precio=0) AND espera=0 AND estado=0 AND devolucion=0 AND diploma=1 AND idcurso IN (SELECT id FROM curso WHERE borrado=0)";
	$result=posgre_query($sql);
	if (pg_num_rows($result)==0){
		$_SESSION[esterror]= "Error1";
		header("Location: index.php");
		exit();
	}
}
elseif (($_SESSION[nivel]==1)||($_SESSION[nivel]==2)){
	$idusuario = $_GET['idusuario'];
	if ($idusuario!=$usuarioPruebas){
		$sql = "SELECT * FROM curso_usuario WHERE borrado=0 AND idusuario='$idusuario' AND idcurso='$idcurso' AND (pagado=1 OR precio=0) AND espera=0 AND estado=0 AND devolucion=0 AND diploma=1 AND idcurso IN (SELECT id FROM curso WHERE borrado=0)";
		$result=posgre_query($sql);
		if (pg_num_rows($result)==0){
			$_SESSION[esterror]= "Error2";
			header("Location: index.php");
			exit();
		}
	}
}
else{
	$_SESSION[esterror]="Error3";	
	header("Location: index.php");
	exit();
}

if ($idusuario!=$usuarioPruebas){

	$sql = "SELECT * FROM registros_salida_diplomas WHERE borrado=0 AND idusuario='$idusuario' AND idcurso='$idcurso'";
	$result=posgre_query($sql);

	if (pg_num_rows($result)>0){
		
		$row = pg_fetch_array($result);
		$filename = $row['archivo'];
		
		header("Content-type: application/pdf"); 
		header("Content-Disposition: attachment; filename=$filename"); 
		readfile("files/$filename");
		exit();
	}

}


// FIN FILTROS //

$sql = "SELECT * FROM usuario WHERE id='$idusuario'";
$result = posgre_query($sql);
if ($row = pg_fetch_array($result)){
	$nombre=tildesmayusculas(ucwords(strtolower($row['nombre'])));
	$apellidos=tildesmayusculas(ucwords(strtolower($row['apellidos'])));
}

$sql = "SELECT * FROM curso WHERE id='$idcurso'";
$result = posgre_query($sql);
if ($row = pg_fetch_array($result)){

	$nombrecurso=($row['nombre']);
	$idcolegio=($row['idcolegio']);
	$duracion=($row['duracion']);
	$duracionminutos=($row['duracionminutos']);
	$modalidad=$row['modalidad'];
	$programa=($row['programa']);
	
	$sqlb = "SELECT * FROM usuario WHERE id='$idcolegio'";
	$resultb = posgre_query($sqlb);
	$rowb = pg_fetch_array($resultb);
	$nombrecolegio = $rowb['nombre'];
	$id_categoria_moodle = $rowb['id_categoria_moodle'];
	
	if ($programa<>""){
		
		$programa_array = explode('<br />',$programa);
		$lineas = count($programa_array); 
		$parte1 = round($lineas/2);
		$parte2 = $lineas-$parte1;
		
		$programa1 = array_slice($programa_array, 0, $parte1);
		$programa1 = implode('<br />',$programa1);
		
		$programa2 = array_slice($programa_array, $parte1, $parte2);
		$programa2 = implode('<br />',$programa2);
		$programa2 = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $programa2);
			
	}
	
	if ($modalidad<>3){
		
		$fecha_inicio=$row['fecha_inicio'];
		$fecha_fin=($row['fecha_fin']);
		
	}
	else{
		$sql = "SELECT * FROM curso_usuario WHERE borrado=0 AND idusuario='$idusuario' AND idcurso='$idcurso'";
		$result2=posgre_query($sql);
		if ($row2 = pg_fetch_array($result2)){
			$fecha_fin = $row2['fechalimitepermanente'];
			$fecha_inicio = $row2['fechahora'];
		}
		
	}
	setlocale(LC_TIME, 'es_ES.UTF-8');
	$fechainicio = (strftime("%d de %B de %Y", strtotime($fecha_inicio)));
	$fechafin = (strftime("%d de %B de %Y", strtotime($fecha_fin)));
	$fechafin2 = (strftime("%A %d de %B de %Y", strtotime($fecha_fin)));
}

$sql = "SELECT * FROM curso_docente_web WHERE idcurso='$idcurso' AND borrado='0'";
$result = posgre_query($sql);
$ponentes="";
while ($row = pg_fetch_array($result)){
	$idponente=($row['idusuario']);
	
	$sql = "SELECT * FROM usuario WHERE id='$idponente'";
	$result2 = posgre_query($sql);
	if ($row2 = pg_fetch_array($result2)){
		$nombreponente=ucwords(strtolower($row2['nombre']));
		$apellidosponente=ucwords(strtolower($row2['apellidos']));
		$ponentes.=$nombreponente." ".$apellidosponente."<br />";
	}
}

if ($ponentes<>""){
	$ponentes_array = explode('<br />',$ponentes);
	$lineas = count($ponentes_array); 
	$parte1 = round($lineas/2);
	$parte2 = $lineas-$parte1;
	
	$ponentes1 = array_slice($ponentes_array, 0, $parte1);
	$ponentes1 = implode('<br />',$ponentes1);
	
	$ponentes2 = array_slice($ponentes_array, $parte1, $parte2);
	$ponentes2 = implode('<br />',$ponentes2);
}



$sql = "SELECT id FROM registros_salida_diplomas ORDER BY id DESC LIMIT 1";	
$result = posgre_query($sql);
if ($row = pg_fetch_array($result)){
	$idregsalida = $row['id'];
}

$ano = date("y");		
$regsalida = $ano."/".($idregsalida+1);	
	



ob_start(); 
?>


<style type="text/css" media="all">




	#diploma
	{
		font-family: Arial,Helvetica,FreeSans,"Liberation Sans","Nimbus Sans L",sans-serif;
		font-size : 12pt;
	}

	#cabecera, #contenido , #pie
	{
		margin-left: 20mm;
		margin-right: 20mm;
		width: 250mm;
	}

	#cabecera
	{
		margin-bottom: 6mm;
	}

	.cabecera
	{
		height: 30mm;
		vertical-align: bottom;
	}

	#cabecera td.logo, #cabecera td.logocolegios,
	{
		width: 33%;
	}
	
	#cabecera td.space
	{
		width: 60%;
	}

	#cabecera td.logo img
	{
		width: 50mm;
		height: auto;
	}

	#cabecera td.logocolegios img
	{
		width: 100mm;
		height: auto;
	}


	#contenido
	{
		margin-top: 0mm;
		text-align: center;
	}

	.contenido
	{
		height: 92mm;
		vertical-align: middle;
	}

	#contenido .detalle
	{
		margin-top: 5mm;
	}

	#contenido .detalle p strong
	{
		font-size: 24pt;
		margin-top: 0mm;
		display: block;
	}
	
	#contenido .titulo
	{
		text-align: left;
		font-size: 14pt;
	}

	#contenido .fechaexpedicion
	{
		text-align: left;
	}

	#contenido .titulocontenidos
	{
		text-align: left;
		padding-top: 5mm;
		padding-bottom: 5mm;
	}
	
	#contenido .detalle2
	{
		width:430px;
		padding-left:20px;
		font-size: 8pt;
		text-align:justify;
		vertical-align:top
	}

	#pie
	{
		margin-top: 0mm;
		margin-bottom: 5mm;
	}

	.pie
	{
		padding-top:50px;
		vertical-align: bottom;
		height: 45mm;
	}


	#pie .firmas
	{
		width: 100%;
		text-align: center;
		vertical-align: top;
		margin-bottom: 0mm;
	}

	#pie .firmas img
	{
		width: 40mm;
		height: auto;
	}

	#pie .firmas td span
	{
		font-size: 85%;
		color: #333;
	}

	#pie .logocolegios img
	{
		width: 100%;
	}


</style>


<page>
<div style="position:absolute; "><img width="1122" src="<?=$imgdiplomapath?>diploma-back-02.png" alt="watermark"></div>
<div style="position:absolute; "><img height="793" src="<?=$imgdiplomapath?>diploma-firmalateral.jpg" alt="watermark"></div>

<table id="diploma">

	<tr>
		<td class="cabecera">
				<table id="cabecera">
				<tr>
					<td class="logo"><img src="<?=$imgdiplomapath?>diploma-logo-activatie.png" alt="ACTIVATIE"></td>
				</tr>
				</table>


		</td>
	</tr>



	<tr>
		<td class="contenido">

			<table id="contenido">
			<tr>
				<td>
					<h1><?=$nombrecurso?></h1>
				</td>
			</tr>
			<tr>
				<td class="nombre">
					<h2><?=$nombre." ".$apellidos?></h2>
				</td>
			</tr>
			<tr>
				<td class="detalle">
					<p>ha realizado el curso de <?=$duracion?> horas<? if ($duracionminutos<>0) echo ' y '.$duracionminutos. ' minutos'; ?>, organizado por la Plataforma Colegial Activatie
						<!--entre el <?=$fechainicio?> y el <?=$fechafin?>,--> habiendo obtenido
						la calificación de APTO, por lo que se expide el presente</p>

					<p><strong>DIPLOMA</strong></p>
					<p><?=$fechafin2?></p>

				</td>
			</tr>

			</table>
		</td>
	</tr>



	<tr>
		<td class="pie">

			<table id="pie">

			<tr>
				<td>
					<table class="firmas">
					<tr>
					
						<td><span style="margin-left:200px;">Antonio Luis M&aacute;rmol Ortu&ntilde;o <br> <span style="margin-left:200px;">Presidente de activatie</span></span></td>
						<td></td>
						<td><span style="margin-left:200px;">Gregorio Alem&aacute;n Garc&iacute;a <br> <span style="margin-left:200px;">Secretario de activatie</span></span></td>
						
					</tr>
					<tr>
						<td><img style="margin-left:200px;" src="<?=$imgdiplomapath?>diploma-presidente.png" alt="Firma"></td>
						<td></td>
						<td><img style="margin-left:200px;" height="100" src="<?=$imgdiplomapath?>diploma-secretario.png" alt="Firma"></td>
					</tr>
					</table>
				</td>
			</tr>

			<tr>
					<td class="logocolegios"><img src="<?=$imgdiplomapath?>diploma-logo-colegios-02.png" alt="Colegios Profesionales"></td>
			</tr>
			</table>
		</td>
	</tr>


</table>
</page>


<page>
<div style="position:absolute; "><img height="793" src="<?=$imgdiplomapath?>diploma-firmalateral.jpg" alt="watermark"></div>

<table id="diploma">

	<tr>
		<td class="cabecera">
				<table id="cabecera">
				<tr>
					<td class="logo"><img src="<?=$imgdiplomapath?>diploma-logo-activatie.png" alt="ACTIVATIE"></td>
					<td class="space"></td>
					<td class="logocolegios">
						<p>
							Reg. Slda. <?=$regsalida?><br/>
						</p>
					</td>
				</tr>
				</table>


		</td>
	</tr>



	<tr>
		<td class="contenido">

			<table id="contenido">

			<tr>
				<td class="titulo" colspan="2" rowspan="1" ><strong>Ref.<?=$idcurso?>/<?=$id_categoria_moodle?> - <?=$nombrecurso?></strong></td>
				
			</tr>
			<tr>
				<td class="fechaexpedicion"><strong><?=$fechafin?></strong></td>
				<td></td>
			</tr>
			
			<? if ($programa<>""){ ?>
			
				<tr>
					<td class="titulocontenidos"><strong>CONTENIDOS:</strong></td>
					<td></td>
				</tr>

				<tr class="detalles">
					<td class="detalle2 col1">
					
					<!--SE PUEDEN INCLUIR HASTA 30 ITEM EN LA LISTA
					ALGUNO MÁS, PERO YO NO METERÍA MÁS, DEBIDO A QUE DEPENDIENDO DE LA LONGITUD DE CADA ITEM, SERÍA ARRIESGADO.-->
					
						<?=$programa1?>
					</td>
					
					<td class="detalle2 col2">
						<?=$programa2?>
					</td>
				</tr>
			<? } ?>
			<? if ($ponentes<>""){ ?>
				
				<tr>
					<td class="titulocontenidos"><strong>PONENTES:</strong></td>
					<td></td>
				</tr>
				<tr class="detalles">
					<td class="detalle2 col1">
					
					<!--SE PUEDEN INCLUIR HASTA 30 ITEM EN LA LISTA
					ALGUNO MÁS, PERO YO NO METERÍA MÁS, DEBIDO A QUE DEPENDIENDO DE LA LONGITUD DE CADA ITEM, SERÍA ARRIESGADO.-->
					
						<?=$ponentes1?>
					</td>
					
					<td class="detalle2 col2">
						<?=$ponentes2?>
					</td>
				</tr>
			
			<? } ?>
			
			</table>
		</td>
	</tr>

	
</table>

</page>


<?

$content = ob_get_clean();

//require_once('../librerias/html2pdf/_tcpdf_5.0.002/tcpdf.php';
require_once('../librerias/html2pdf/html2pdf.class.php');

try
{
	$nombrecurso = str_replace(",","",$nombrecurso);
	$nombrecurso = str_replace(" ","",$nombrecurso);
	$nombrecurso = str_replace(".","",$nombrecurso);
	
	$filename= "Diploma-".$nombrecurso.".pdf";
	
	
	$archivv=substr(md5(time()),0,12);
	$filename='Diploma'.$idcurso.'-'.$idusuario."1a".$archivv.'.pdf';
	
	$margin = array(0,0,35,0);
	$html2pdf = new HTML2PDF('L', 'A4', 'es', true, 'UTF-8', $margin);
	
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	$html2pdf->createIndex('', 0, 0, false, false, 1);
	$html2pdf->Output('files/'.$filename, 'F');
	
	file_get_contents('https://www.activatie.org/web/signer.php?filename='.$filename); 
	
	
		
	$sql = "INSERT INTO registros_salida_diplomas (idusuario,archivo,regsalida, idcurso) VALUES ('$idusuario','$filename','$regsalida', '$idcurso')";
	posgre_query($sql);
		
	header("Content-type: application/pdf"); 
	header("Content-Disposition: attachment; filename=$filename"); 
	readfile("files/$filename");
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}


?>
