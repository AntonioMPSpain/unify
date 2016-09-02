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

$safe="Resumen de inscritos";
$titulo1="informe ";
$titulo2="cursos";

$link=iConectarse();
$sql = "SELECT * FROM curso WHERE id='$idcurso'";
$result = posgre_query($sql);
$row = pg_fetch_array($result);
$nombrecurso = $row['nombre'];
$modalidad = $row['modalidad'];
$plazas=$row["plazas"];
$plazaso=$row["plazaso"];
$plazasperma=$row["plazasperma"];

switch($modalidad) { 
  case "0": $plazas=$plazaso; break; //" on-line ";
  case "1": $plazas; break; //" presencial ";
  case "2": $plazas=$plazaso; break; // " presencial y on-line ";
  case "3": $plazas=$plazasperma; break; //" permanente ";
}


$c_directorio = '/var/www/web/backend';
include_once ('../../html2fpdf-3.0.2b/html2fpdf.php');
define('FPDF_FONTPATH','/var/www/html2fpdf-3.0.2b/font/');
require('../../html2fpdf-3.0.2b/fpdf.php'); 
//ob_start(); 

$html = '
<html>
<body>
<img src="../img/activatie-logo.png">
<br /><br />

<h2>Resumen inscritos en '.$nombrecurso.'</h2>
<br /><br />
<span>Plazas: '.$plazas.'</span><br>

<h2>Inscritos</h2>
<TABLE  border="1" bordercolor="#999999"> 
	<TR>
		<th>Estado</th>
		<th>Número</th>
		<th>% número</th>
	</TR> 
	';
	
	if ($modalidad==2){
		if ($cursodual==1){	// Saca modo online
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		$totaltotal_registros = pg_num_rows($result); 
	}else{
		$result=pg_query($link,"SELECT * FROM curso_usuario WHERE (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		$totaltotal_registros = pg_num_rows($result); 
	}
	
	if ($modalidad==2){
		if ($cursodual==1){	// Saca modo online
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		$total_registros = pg_num_rows($result); 
	}else{
		$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		$total_registros = pg_num_rows($result); 
	}
	$totalinscritos = $total_registros;
	if ($total_registros=="") $total_registros=0;
	$porcentaje=number_format((($total_registros/$totaltotal_registros)*100), 2, '.',',');
	$html .= '
	
	<TR>
		<td>Inscritos</th>
		<td align="center">'.$total_registros.'</th>
		<td align="center">'.$porcentaje.'</th>
	</TR> 
	
	';
	
	if ($modalidad==2){
	
		if ($cursodual==1){	// Saca modo online
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=1 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=1 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		$plazas=$plazaso;
		$total_registros = pg_num_rows($result); 
	}
	else{
		$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=1 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ") ;//or die (pg_error());  
		$total_registros = pg_num_rows($result); 
	}
	
	$porcentaje=number_format((($total_registros/$totaltotal_registros)*100), 2, '.',',');
	if ((trim($total_registros)=="")||($total_registros==0)) $total_registros="0";
	$html .= '
	
	<TR>
		<td>Lista de espera</th>
		<td align="center">'.$total_registros.'</th>
		<td align="center">'.$porcentaje.'</th>
	</TR> 
	
		
	';
	
	if ($modalidad==2){
	
		if ($cursodual==1){	// Saca modo online
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE AND estado<>0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0   ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE AND estado<>0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0    ") ;//or die (pg_error());  
		}
		$plazas=$plazaso;
		$total_registros = pg_num_rows($result); 
	}
	else{
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE estado<>0 AND  (modalidad<>2 OR modalidad is Null) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
			$total_registros = pg_num_rows($result); 
	}
	if ($total_registros=="") $total_registros=0;
	$porcentaje=number_format((($total_registros/$totaltotal_registros)*100), 2, '.',',');
	$html .= '
	
	<TR>
		<td>Bajas</th>
		<td align="center">'.$total_registros.'</th>
		<td align="center">'.$porcentaje.'</th>
	</TR> 
		
	<TR>
		<td>Total</th>
		<td align="center">'.$totaltotal_registros.'</th>
		<td align="center">100</th>
	</TR> 

</TABLE>

<h3>Inscritos por colegio (no incluye lista de espera ni bajas)</h3>
<TABLE   border="1" bordercolor="#999999"  width="98%"> 
	<TR>
		<th>Colegio</th>
		<th align="center">Num inscritos</th>
		<th align="center">% inscritos</th>
	</TR> 

	';

	$result=posgre_query("SELECT * FROM usuario WHERE borrado=0 AND nivel=2 ORDER BY nombre, id DESC");// or die (mysql_error());  

	while($row = pg_fetch_array($result)) { 
		$idcolegio = $row["id"];
		$nombrecolegiado = $row["nombre"];	
	
		if ($modalidad==2){
			if ($cursodual==1){	// Saca modo online
				$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio')  ") ;//or die (pg_error());  
			}
			else{				// Presencial
				$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio')  ") ;//or die (pg_error());  
			}
			$total_registros = pg_num_rows($result2); 
		}else{
			$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') ") ;//or die (pg_error());  
			$total_registros = pg_num_rows($result2); 
		}
		
		$porcentaje=number_format((($total_registros/$totalinscritos)*100), 2, '.',',');
		if ($total_registros>0){
		
			$html .= '
			<TR>
				<td>'.$nombrecolegiado.'</th>
				<td align="center">'.$total_registros.'</th>
				<td align="center">'.$porcentaje.'</th>
			</TR> 
			
			';
		}
	
	} 
	
	
	if ($modalidad==2){
		if ($cursodual==1){	// Saca modo online
			$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL)  ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL)  ") ;//or die (pg_error());  
		}
		$total_registros = pg_num_rows($result2); 
	}else{
		$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) ") ;//or die (pg_error());  
		$total_registros = pg_num_rows($result2); 
	}
	
	$porcentaje=number_format((($total_registros/$totalinscritos)*100), 2, '.',',');
	
	$html .= '
	
	<TR>
		<td>No colegiados</th>
		<td align="center">'.$total_registros.'</th>
		<td align="center">'.$porcentaje.'</th>
	</TR> 
	
	<TR>
		<td>Total</th>
		<td align="center">'.$totalinscritos.'</th>
		<td align="center">100</th>
	</TR> 

</TABLE>



</div>

</div>

<br /><br />Fecha de informe: '.$fecha.'
</body>
</html>';
$html = utf8_decode($html);
	// Output-Buffer in variable: 
	//$html=ob_get_contents(); 
	// delete Output-Buffer 
	//ob_end_clean(); 
	$pdf = new HTML2FPDF(); 
	$pdf->DisplayPreferences('HideWindowUI'); 
	$pdf->AddPage(); 
	$pdf->UseCSS($opt==true); 
	@$pdf->WriteHTML($html); 
	$pdf->ReadCSS($html); 
	$nb=rand(123, 999);
	$archivv=time();
	$archivo='inf-curso'.$idcurso."-".$archivv.'.pdf';
	$enlace=$c_directorio.'/informes/'.$archivo;
	@$pdf -> Output($enlace, 'F');
	ob_end_flush();
	//echo "ok pdf <br><br>";
	//$c_directorio = '/var/www/web';
	
if (is_file ($enlace)){
	header ("Content-Disposition: attachment; filename=".$archivo."\n\n"); 
	header ("Content-Type: application/octet-stream");
	header ("Content-Length: ".filesize($enlace));
	readfile($enlace);
	$esttexto="Archivo generado.";
}else{
	$esttexto="Archivo no encontrado.";
}
//$_SESSION[esterror]=$esttexto;
header("Location: informe_curso_inscritos.php?idcurso=$idcurso");  //No necesario

?>