<?
//error_reporting(-1);
include("_funciones.php"); 
include("_cone.php"); 
set_time_limit (300);
$safe="Usuarios en Cursos";

$idcurso=strip_tags($_REQUEST['idcurso']); 

session_start();
if ($idcurso=="") { //Alumno
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// Filtros de nivel por usuario //////////////////////

if ($_SESSION[nivel]==2) { //Admin Colegio

}elseif ($_SESSION[nivel]==1) { //Admin Total

}
else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

/** VARIABLE **/
$registros = 14; // registros que caben en una página
/**/

$fechases = $_GET['fec'];
$ses = $_GET['ses'];

$link=iConectarse();
$rowcurso=pg_query($link,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
$curso= pg_fetch_array($rowcurso);
$idcursomoodle=$curso["id_categoria_moodle"];

$resultc=pg_query($link,"SELECT * FROM usuario u, curso_usuario cu WHERE ((cu.modalidad=2 AND cu.inscripciononlinepresencial=1) OR (cu.modalidad=1)) AND cu.idusuario=u.id AND cu.estado=0 AND cu.espera=0 AND cu.pagado!=0 AND cu.nivel<>'3' AND cu.idcurso='$idcurso' AND $sql cu.borrado=0 ORDER BY u.apellidos") ;//or die (pg_error());  
$total_alumnos = pg_num_rows($resultc); 

$paginas = ceil($total_alumnos/$registros);

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

</style>

<? 

for ($i=1; $i<=$paginas; $i++){


	$html='
		<table style="border-collapse: collapse;" width="100%" border="1">
		<tr ">
			<th style="width:15%;">NIF</th>
			<th style="width:40%;">APELLIDOS</th>
			<th style="width:20%;">NOMBRE</th>
			<th style="width:25%;">FIRMA</th>	
		</tr>';
	
	$inicio = ($i-1)*$registros;	
	$result=pg_query($link,"SELECT * FROM usuario u, curso_usuario cu WHERE ((cu.modalidad=2 AND cu.inscripciononlinepresencial=1) OR (cu.modalidad=1)) AND cu.idusuario=u.id AND cu.estado=0 AND cu.espera=0 AND cu.pagado!=0 AND cu.nivel<>'3' AND cu.idcurso='$idcurso' AND $sql cu.borrado=0 ORDER BY u.apellidos LIMIT $registros OFFSET $inicio; ") ;//or die (pg_error());  

	while(($row = pg_fetch_array($result))) { 
				// Genera
		$idusuario=$row["idusuario"];
		$consulta = "SELECT * FROM usuario WHERE id='$idusuario' AND borrado = 0 ORDER BY id;";
		$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
		if($rowdg= pg_fetch_array($r_datos)) {	
			$nif=$row["nif"];
			$nombre=ucwords(strtolower($rowdg['nombre']));
			$apellidos=ucwords(strtolower($rowdg['apellidos']));
		}
		$html=$html.'

		<tr style="background-color : white;height:40px;">
			<td style="background-color : white;height:40px;">'.$nif.'</td>
			<td>'.$apellidos.'</td>
			<td>'.$nombre.'</td>
			<td>&nbsp;</td>
		</tr>';
	}

	$html.='</table>';
	
?>
<page>

<table id="diploma">

	<tr>
		<td class="cabecera">
				<table id="cabecera">
				<tr>
					<td class="logo"><img src="http://www.activatie.org/web/img/diploma-logo-activatie.png" alt="ACTIVATIE"></td>
					<td class="space"></td>
					<td class="logocolegios">
						<p>
							Listado de firmas. P&aacute;gina <?=$i?>/<?=$paginas?><br/>
						</p>
					</td>
				</tr>
				</table>


		</td>
	</tr>

	<tr>
		<td>

			<table>
				<tr>
					<td>
						<p style="margin-left:80px; font-size:18px"><b>Ref. <?=$idcurso?>/<?=$idcursomoodle?>. <?=$curso["nombre"]?></b></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr>
		<td>

			<table>
				<tr>
					<td>
						<p style="margin-left:80px; font-size:16px"><b>Sesi&oacute;n: <?=$ses?>. Fecha: <?=$fechases?></b></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr>
		<td class="contenido">
			<br>
			<p style="margin-left:80px; font-size:16px"><b>Alumnos</b></p>
			<table id="contenido">
				<tr>
					<td>	
					
						<?=$html?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	
</table>
	
<div style="position:absolute; bottom:20;">
	<img style="margin-left:45px;" height='45' src="http://www.activatie.org/web/img/diploma-logo-colegios-02.png" alt="Colegios Profesionales">
</div>
</page>

<?
}

$content = ob_get_clean();

require_once('../librerias/html2pdf/html2pdf.class.php');
try
{
	$margin = array(0,0,10,5);
	$html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', $margin);
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	$html2pdf->createIndex('', 0, 0, true, false, 1);
	$html2pdf->Output('hoja_firmas'.$idcurso.'.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}

?>