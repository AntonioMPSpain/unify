<?
include("_funciones.php"); 
include("_cone.php"); 

$safe="Informes";

$titulo1="informes ";
$titulo2="activatie";


////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo sus cursos 
	if ($_SESSION[idcolegio]<>"") {
		$iddocente=strip_tags($_SESSION[idusuario]);
		$sql=" (iddocente='$iddocente') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
//		$sql="  (idcolegio='$idcolegio') AND ";
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

<h1>Resumen de usuarios registrados</h1>
<TABLE   border="1" bordercolor="#999999"> 
	<TR>
		<th>Colegio</th>
		<th>Usuarios en la plataforma</th>
		<th>Usuarios activados</th>
		<th>% que representa cada colegio</th>
	</TR> 
	';  
	$link=conectar(); //Postgresql
	$result=pg_query($link,"SELECT * FROM usuario WHERE borrado=0 AND nivel=2 ORDER BY nombre DESC, id DESC");// or die (mysql_error());  
	$usuariosactivostotal=0;
	$usuariostotal=0;
	while($row = pg_fetch_array($result)) { 
		$id = $row["id"];
		$nombre = $row["nombre"];	
			
		$sql = "SELECT * FROM usuario WHERE borrado=0 AND idcolegio='$id' ";
		$result2 = posgre_query($sql);
		$usuarios = pg_num_rows($result2);	
			
						
		$sql = "SELECT * FROM usuario WHERE borrado=0 AND (idcolegio!=0 OR idcolegio IS NOT NULL) AND (pass IS NOT NULL OR pass!='')";
		$result4 = posgre_query($sql);
		$usuariosactivostotal2 = pg_num_rows($result4);	
		
		$sql = "SELECT * FROM usuario WHERE borrado=0 AND idcolegio='$id' AND (pass IS NOT NULL OR pass!='')";
		$result3 = posgre_query($sql);
		$usuariosactivos = pg_num_rows($result3);	
		
		$porcentajeactivados=number_format((($usuariosactivos/$usuarios)*100), 2, '.',',');
		$porcentajeactivatietotal=number_format((($usuariosactivos/$usuariosactivostotal2)*100), 2, '.',',');
			
		$html .='<tr bgcolor="'.$bgcolor.'">
			<td align="left">'.$nombre.'</td>
			<td align="center">'.$usuarios.'</td>
			<td align="center">'.$usuariosactivos.'('.$porcentajeactivados.'%)</td>
			<td align="center">'.$porcentajeactivatietotal.'%</td>
		</tr>
		'; 
		$usuariostotal+=$usuarios;
		$usuariosactivostotal+=$usuariosactivos;
		
	} 
	
	$porcentajeactivadostotal=number_format((($usuariosactivostotal/$usuariostotal)*100), 2, '.',',');
	
	$sql = "SELECT * FROM usuario WHERE borrado=0 AND (idcolegio=0 OR idcolegio IS NULL) ";
	$result4 = posgre_query($sql);
	$usuariosNocolegiados = pg_num_rows($result4);	
	
	$sql = "SELECT * FROM usuario WHERE borrado=0 AND (idcolegio=0 OR idcolegio IS NULL) AND (pass IS NOT NULL OR pass!='') ";
	$result5 = posgre_query($sql);
	$usuariosNocolegiadosActivos = pg_num_rows($result5);	
	
	$porcentajeactivosNocolegiados = number_format((($usuariosNocolegiadosActivos/$usuariosNocolegiados)*100), 2, '.',',');
	
	$usuariostotalactivatie = $usuariostotal+$usuariosNocolegiados;
	$usuariostotalactivatieactivos = $usuariosactivostotal + $usuariosNocolegiadosActivos;
	$porcentajeusuariostotalactivatie =number_format((($usuariostotalactivatieactivos/$usuariostotalactivatie)*100), 2, '.',',');
	
	$sql = "SELECT * FROM usuario WHERE baja=1";
	$resultb = posgre_query($sql);
	$usuariosBaja = pg_num_rows($resultb);
	
	
	$html .= '<tr bgcolor="'.$bgcolor.'">
		<td align="center">TOTAL COLEGIADOS</td>
		<td align="center">'.$usuariostotal.'</td>
		<td align="center">'.$usuariosactivostotal2.'('.$porcentajeactivadostotal.'%)</td>
		<td align="center">100%</td>
	</tr>
	
	<tr bgcolor="'.$bgcolor.'">
		<td align="center">NO COLEGIADOS</td>
		<td align="center">'.$usuariosNocolegiados.'</td>
		<td align="center">'.$usuariosNocolegiadosActivos.'('.$porcentajeactivosNocolegiados.'%)</td>
	</tr>
	
	<tr bgcolor="'.$bgcolor.'">
		<td align="center">TOTAL ACTIVATIE</td>
		<td align="center">'.$usuariostotalactivatie.'</td>
		<td align="center">'.$usuariostotalactivatieactivos.'('.$porcentajeusuariostotalactivatie.'%)</td>
	</tr>
	
	<tr bgcolor="'.$bgcolor.'">
		<td align="center">USUARIOS BAJA</td>
		<td align="center">'.$usuariosBaja.'</td>
	</tr>
	
</table>


		<br />
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
	$archivo='informe-'.$nb."-".$archivv.'.pdf';
	$enlace=$c_directorio.'/informes/'.$archivo;
	@$pdf -> Output($enlace, 'F');
	ob_end_flush();
	
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
header("Location: informe_usuarios_activos.php");  //No necesario

?>