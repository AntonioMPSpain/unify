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

$safe="Resumen de ingresos";
$titulo1="informe ";
$titulo2="cursos";

$linka=iConectarse(); 
$rowcurso=pg_query($linka,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
$curso= pg_fetch_array($rowcurso);



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



	<h1> Resumen de ingresos de <strong>'.$curso["nombre"].'</strong></h1>
		<br />
		<h3>Ingresos inscritos</h3>
		<TABLE  border="1" bordercolor="#999999"> 
		<TR>
			<th>Alumnos inscritos</th>
			<th>Pagos realizados</th>
			<th>Importe</th>
			<th>Potencial inscritos</th>
			<th>Total</th>
		</TR> 
		';
		
		$totalpagado = 0;
		$sql = "SELECT precio, count(*) as alumnos FROM curso_usuario WHERE estado=0 AND espera=0 AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
		$result = posgre_query($sql);
		
		while ($row = pg_fetch_array($result)){
		
			$alumnos = $row['alumnos'];
			$precio = $row['precio'];
			
		
			$sql2 = "SELECT count(*) as pagados FROM curso_usuario WHERE estado=0 AND espera=0 AND pagado=1 AND precio='$precio' AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
			$result2 = posgre_query($sql2);
			
			$pagados = 0;
			if ($row2 = pg_fetch_array($result2)){
				$pagados = $row2['pagados'];
			}
			
			$html .= '
			<TR>
				<td align="center">'.$alumnos.'</td>
				<td align="center">'.$pagados.'</td>
				<td align="center">'.$precio.'&euro;</td>
				<td align="center">'.$precio*$alumnos.'&euro;</td>
				<td align="center">'.$precio*$pagados.'&euro;</td>
			</TR> 
			
			';
			
			$totalpagado += $precio*$pagados;
		}
		$html .= '

		</TABLE>

		<strong>Total: '.$totalpagado.'&euro;</strong><br /><br /><br />	
		
		
		<h3>Ingresos inscritos por colegio</h3>
		<TABLE  border="1" bordercolor="#999999"> 
		<TR>
			<th>Colegio</th>
			<th>Alumnos inscritos</th>
			<th>Pagos realizados</th>
			<th>Importe</th>
			<th>Potencial inscritos</th>
			<th>Total</th>
		</TR> 
		';
		
		
		
		$sql3 = "SELECT * FROM usuario WHERE borrado=0 AND nivel=2 ORDER BY nombre";
		$result3 = posgre_query($sql3);
		
		$totalpagado = 0;
			
		while ($row3 = pg_fetch_array($result3)){
			$idcolegio = $row3['id'];
			$nombrecolegio = $row3['nombre'];
			
			$sql = "SELECT precio, count(*) as alumnos FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
			$result = posgre_query($sql);
		
			while ($row = pg_fetch_array($result)){
			
				$alumnos = $row['alumnos'];
				$precio = $row['precio'];
				
			
				$sql2 = "SELECT count(*) as pagados FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND pagado=1 AND precio='$precio' AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
				$result2 = posgre_query($sql2);
				
				$pagados = 0;
				if ($row2 = pg_fetch_array($result2)){
					$pagados = $row2['pagados'];
				}
							
				$html .= '
				<TR>
					<td>'.$nombrecolegio.'</td>
					<td align="center">'.$alumnos.'</td>
					<td align="center">'.$pagados.'</td>
					<td align="center">'.$precio.'&euro;</td>
					<td align="center">'.$precio*$alumnos.'&euro;</td>
					<td align="center">'.$precio*$pagados.'&euro;</td>
				</TR> 
				
				'.
				
				$totalpagado += $precio*$pagados;
			}
		}
		
		
		$sql = "SELECT precio, count(*) as alumnos FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
		$result = posgre_query($sql);
	
		while ($row = pg_fetch_array($result)){
		
			$alumnos = $row['alumnos'];
			$precio = $row['precio'];
			
		
			$sql2 = "SELECT count(*) as pagados FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND pagado=1 AND precio='$precio' AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
			$result2 = posgre_query($sql2);
			
			$pagados = 0;
			if ($row2 = pg_fetch_array($result2)){
				$pagados = $row2['pagados'];
			}
						
			$html .= '
			
			
			
			<TR>
				<td>No colegiados</td>
				<td align="center">'.$alumnos.'</td>
				<td align="center">'.$pagados.'</td>
				<td align="center">'.$precio.'&euro;</td>
				<td align="center">'.$precio*$alumnos.'&euro;</td>
				<td align="center">'.$precio*$pagados.'&euro;</td>
			</TR> 
			
			';
			
			$totalpagado += $precio*$pagados;
		}
		
		
		
		$html .= '
				
		</TABLE>


		<strong>Total: '.$totalpagado.'&euro;</strong><br /><br /><br />';
		/*
		<h3>Devoluciones</h3>
		<TABLE  border="1" bordercolor="#999999"> 
		<TR>
			<th>Alumnos baja</th>
			<th>Devolución realizadas</th>
			<th>Importe</th>
			<th>Total</th>
		</TR> 
		'; 
		$totalpagado = 0;
		$sql = "SELECT precio, count(*) as alumnos FROM curso_usuario WHERE estado=1 AND pagado=1 AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
		$result = posgre_query($sql);
		
		while ($row = pg_fetch_array($result)){
		
			$alumnos = $row['alumnos'];
			$precio = $row['precio'];
			
		
			$sql2 = "SELECT count(*) as pagados FROM curso_usuario WHERE estado=1 AND pagado=1 AND devolucion=1 AND precio='$precio' AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
			$result2 = posgre_query($sql2);
			
			$pagados = 0;
			if ($row2 = pg_fetch_array($result2)){
				$pagados = $row2['pagados'];
			}
			
			$html .= '
			<TR>
				<td align="center">'.$alumnos.'</td>
				<td align="center">'.$pagados.'</td>
				<td align="center">-'.$precio.'&euro;</td>
				<td align="center">-'.$precio*$pagados.'&euro;</td>
			</TR> 
			
			';
			
			$totalpagado += $precio*$pagados;
		}
		$html .= '

		</TABLE>
		
		<strong>Total devuelto: '.$totalpagado.' &euro;</strong><br /><br /><br />
		*/

$html .= '	</div>

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
	$archivo='ingresos'.$idcurso."-".$archivv.'.pdf';
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
header("Location: informe_curso_ingresos.php?idcurso=$idcurso");  //No necesario

?>