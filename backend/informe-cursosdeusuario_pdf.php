<?

include("_funciones.php"); 
include("_cone.php"); 
include_once "_config.php";


$safe="Cursos de Usuario";
$titulo1="formación ";
$titulo2="administración";

$idusuario=strip_tags($_REQUEST['idusuario']); 

if ($idusuario=="") { //Alumno
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}

////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==2) { //Admin Colegio
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
}
elseif (($_SESSION[nivel]==4)||($_SESSION[nivel]==3)) { //Admin Total
	$idusuario = $_SESSION['idusuario'];
}
else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$sqlcampo="fechahora";
$link=iConectarse();


$idcursocert=strip_tags($_REQUEST['idcurso']); 
if ($idcursocert<>""){
	$sqlcurso = " AND idcurso='$idcursocert' ";
	$singularplurar = "curso o jornada organizado/a";
}
else{
	$singularplurar = "los curso/s y/o jornada/s organizado/s";
}

$profe=false;
if (isset($_GET['profe'])){
	$profe=true;
}

if ($profe){
	$result=pg_query($link,"SELECT * FROM curso_docente_web WHERE idusuario='$idusuario' AND borrado=0 $sqlcurso") ;//or die (pg_error()); 
	$textoprofe="participado como ponente en ";
	
	if ($idcursocert==""){
		$textoprofe.="el/";
	}
	else{
		$textoprofe.="el ";
	}
}
else{	

	$result=pg_query($link,"SELECT * FROM curso_usuario WHERE (pagado=1 OR precio=0) AND devolucion=0 AND estado=0 AND espera=0 AND nivel<>'3' AND idusuario='$idusuario' AND borrado=0 $sqlcurso ORDER BY  $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$textoprofe="asistido ";
		
	if ($idcursocert==""){
		$textoprofe.="al/";
	}
	else{
		
		$textoprofe.="al ";
	}
}

$total_registros = pg_num_rows($result); 
$cuantos = $total_registros;
//fin Paginacion 1
	$cplazas=$sumatotal=0;
	while(($row = pg_fetch_array($result))) { 
		$idcurso=$row["idcurso"];
		$linka=iConectarse(); 
		$resulta=pg_query($linka,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0") ;//or die (pg_error());  
		$rowa = pg_fetch_array($resulta);
		$total = pg_num_rows($resulta);
		if ($total>0){ 
		
			$idcolegio = $rowa['idcolegio'];
			$modalidad = $rowa['modalidad'];
			$minutos = $rowa['minutos'];
			
			$sqlb = "SELECT * FROM usuario WHERE id='$idcolegio'";
			$resultb = posgre_query($sqlb);
			$rowb = pg_fetch_array($resultb);
			$nombrecolegio = $rowb['nombre'];
			$id_categoria_moodle = $rowb['id_categoria_moodle'];
			
			$andminutos = "";
			if ($minutos>0){
				$andminutos = " y $minutos minutos";
			}
		
			if ($modalidad==3){
				$fechas = "";	
			}
			else{
				$fechas = "del ".cambiaf_a_normal($rowa["fecha_inicio"])." al ".cambiaf_a_normal($rowa["fecha_fin"])." - ";
			}
		
			$htmlcursos.="<li>Ref.".$idcurso."/".$id_categoria_moodle." ".$rowa["nombre"]." (".$fechas.$rowa["duracion"]." horas".$andminutos."). Coorganizado con ".$nombrecolegio.".<br><br></li>";
		
		}
	}

$result=posgre_query("SELECT * FROM usuario WHERE id='$idusuario'") ;
if ($row = pg_fetch_array($result)){
	$nombre=tildesmayusculas(ucwords(strtolower($row['nombre'])));
	$apellidos=tildesmayusculas(ucwords(strtolower($row['apellidos'])));
	$nif = $row['nif'];
}
	
setlocale(LC_TIME, 'es_ES.UTF-8');
$fechalarga = (strftime("%d de %B de %Y", time()));
	
$sql = "SELECT id FROM registros_salida_certificados ORDER BY id DESC LIMIT 1";	
$result = posgre_query($sql);
if ($row = pg_fetch_array($result)){
	$idregsalida = $row['id'];
}

$ano = date("y");		
$regsalida = $ano."/".($idregsalida+1);	
	
ob_start(); 
?>
	
	
<style type="text/css" media="all">



	@page {
	  size: A4;
	  margin: 0;
	  padding: 0;
	}

	@media print
	{
  		html, body {
    		width: 150mm;
    		height: 297mm;
  		}


	   #pie {page-break-after: always;}


  	}

	#diploma
	{
		width: 100%;
		height: 100%;
		/*background-size: cover;*/
		background-size: 210mm 297mm;
    	width: 210mm;
    	height: 297mm;
	}

	body {
		color : #000000;
		background : #ffffff;
		/*font-family : "Times New Roman", Times, serif;*/
		font-family: Arial,Helvetica,FreeSans,"Liberation Sans","Nimbus Sans L",sans-serif;
		font-size : 12pt;
    	width: 210mm;
    	height: 297mm;
/*		overflow: hidden;*/
	}

	body
	{
		width: auto; margin: 0 0;
		padding: 0;
		border: 0;
		float: none !important;
		color: black;
		background: #fff;

	}


	#cabecera, #contenido , #pie
	{
		margin-left: 20mm;
		margin-right: 20mm;
/*border: 1px solid blue;*/
		width: 170mm;
	}

	#cabecera
	{
		margin-bottom: 15mm;
		margin-top: 15mm;
	}

	.cabecera
	{
		height: 15mm;
		vertical-align: top;
/*border:1px solid green;*/
	}

	#cabecera td.logo, #cabecera td.space, #cabecera td.logocolegios
	{
		width: 33%;
	}

	#cabecera td.logo img
	{
		width: 50mm;
		height: auto;
	}

	#cabecera td.space
	{
		width: 50mm;
	}

	#cabecera td.logocolegios
	{
		text-align: right;
	}

	#cabecera td.logocolegios img
	{
		width: 100mm;
		height: auto;
		text-align: right;
	}


	#contenido
	{
		margin-top: 0mm;
		text-align: center;
		border-collapse: collapse;
	}

	.contenido
	{
		height: 92mm;
		vertical-align: top;
/*border: 1px solid orange;*/
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

	#contenido .detalle
	{
		margin-top: 5mm;
		vertical-align: top;
	}

	#contenido .detalle p strong
	{
		font-size: 24pt;
		margin-top: 10mm;
		display: block;
	}

	#contenido .detalle ul.contenidos
	{
		text-align: left;
		padding-top: 5mm;
		padding-bottom: 5mm;
		text-align:justify;
	}
	
	#contenido .detalle ul.contenidos li
	{
		font-size: 12px;
		text-align:justify; 
	}
	
	#pie
	{
		position:absolute;
		bottom:10;
		height: 45px;
	}

	.pie
	{
		height: 45px;
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



	.page-break
	{
		page-break-after: always;
	}


</style>



<page>
<div style="position:absolute; "><img style="margin-top:150px;" width="795" src="http://www.activatie.org/web/img/diploma-back-02.png" alt="watermark"></div>
<div style="position:absolute; "><img height="1122" src="http://www.activatie.org/web/img/diploma-firmalateral.jpg" alt="watermark"></div>

<table id="diploma">

	<tr>
		<td class="cabecera">
				<table id="cabecera">
				<tr>
					<td class="logo"><img src="http://www.activatie.org/web/img/diploma-logo-activatie.png" alt="ACTIVATIE"></td>
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
				<td class="titulo" colspan="2" rowspan="1" ><br><strong>Julián Pérez Navarro con DNI: 34793598A, Secretario Técnico de la Plataforma Colegial Activatie S.L. con CIF: B73879850</strong></td>
			</tr>
			<tr>
				<td class="fechaexpedicion"><br/><strong>CERTIFICA:</strong></td>
				<td></td>
			</tr>
			<tr>
				<td class="titulocontenidos" colspan="2" rowspan="1"><strong>Que <?=$nombre?> <?=$apellidos?>, con NIF <?=$nif?>, ha <?=$textoprofe?><?=$singularplurar?> por esta Plataforma, según la relación que a continuación se detalla:</strong></td>
			</tr>

			<tr class="detalles">
				<td class="detalle">

				<!--SE PUEDEN INCLUIR HASTA 25 ITEMs EN LA LISTA
				ALGUNO MÁS, PERO YO NO METERÍA MÁS, DEBIDO A QUE DEPENDIENDO DE LA LONGITUD DE CADA ITEM, SERÍA ARRIESGADO.-->
				<!--En el cado de tener más de 25 items, usar la plantilla de varias páginas-->

					<ul class="contenidos">
						<?=$htmlcursos?>
					</ul>
				</td>

			</tr>

			<tr>
				<td class="titulocontenidos" colspan="2" rowspan="1"><strong>Y para que conste y surta sus efectos oportunos donde convenga al interesado/a, expido el presente, a <?=$fechalarga?>.</strong></td>
			
			</tr>
						
			
			</table>
		</td>
	</tr>



</table>
<div style="position:absolute; bottom:20;">
	<div style="font-size:9px; text-align:center; margin-left:50px;">
	Plataforma Colegial Activ<span style="color:#D13100">atie</span>, Av. Alfonso X el Sabio nº 2, 30008 Murcia – Tel. 968274411 – <a style="color:#D13100" href='www.activatie.org'>www.activatie.org</a><br><br>

	&nbsp;&nbsp;&nbsp;&nbsp;Registro Mercantil de Murcia, hoja MU-88624, tomo 3119, folio 44           CIF B-73879850
	<br><br>
	</div>		
<img style="margin-left:45px;" height='45' src="http://www.activatie.org/web/img/diploma-logo-colegios-02.png" alt="Colegios Profesionales">

</div>
			


</page>



<?


$content = ob_get_clean();

require_once($b_libspath.'html2pdf/html2pdf.class.php');
try
{
	
	$archivv=substr(md5(time()),0,12);
	$filename='certificado'.'-'.$idusuario."c".$archivv.'.pdf';
	
	$sql = "INSERT INTO registros_salida_certificados (idusuario,archivo,regsalida) VALUES ('$idusuario','$filename','$regsalida')";
	posgre_query($sql);
	
	$enlace=$b_wwwpath.'informes/'.$filename;
	$margin = array(0,0,60,0);
	$html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', $margin);
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	$html2pdf->createIndex('', 0, 0, false, false, 1);
	$html2pdf->Output($enlace, 'F');
	
	
	file_get_contents('http://www.activatie.org/web/signer.php?informe&filename='.$filename); 
	
	header ("Content-Disposition: attachment; filename=".$filename."\n\n"); 
	header ("Content-Type: application/octet-stream");
	header ("Content-Length: ".filesize($enlace));
	@readfile($enlace);
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}
	
?>
	