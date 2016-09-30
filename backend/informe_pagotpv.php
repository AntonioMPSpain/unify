<?
include("_funciones.php"); 
include("_cone.php"); 

$safe="Informes";

$titulo1="informes ";
$titulo2="activatie";

////////// Filtros de nivel por usuario //////////////////////
session_start(); 
$sqlcolegio="";
if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sqlcolegio=" AND CU.idcurso IN ( SELECT id FROM curso WHERE idcolegio='$idcolegio') ";
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

$orden=strip_tags($_GET['orden']);
if($orden=="ASC"){
	$orden="ASC";
}else{
	$orden="DESC";
}

$getcurso="";
if (isset($_GET['idcurso'])){
	$idcurso = $_GET['idcurso'];
	$sqlcurso = " AND CU.idcurso='$idcurso' ";
	$getcurso="&idcurso=$idcurso";
}

$getusuario="";
if (isset($_GET['idusuario'])){
	$idusuario = $_GET['idusuario'];
	$sqlusuario = " AND CU.idusuario='$idusuario' ";
	$getusuario = "&idusuario=$idusuario";
}

if (isset($_REQUEST['xls'])){
		
	require_once dirname(__FILE__) . '/../librerias/PHPExcel/Classes/PHPExcel.php';
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
			
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Código autor. Sabadell");
	$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Fecha");
	$objPHPExcel->getActiveSheet()->SetCellValue('C1', "Importe");
	$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Curso");
	$objPHPExcel->getActiveSheet()->SetCellValue('E1', "Usuario");
	$objPHPExcel->getActiveSheet()->SetCellValue('F1', "Num. Tarjeta");
	
	$sql = "SELECT * FROM curso_usuario CU, pedidostarjeta PT WHERE CU.tipoinscripcion=1 AND CU.borrado=0 AND CU.idcurso=PT.idcurso AND PT.tipopago=1 AND CU.idusuario=PT.idusuario $sqlcurso $sqlusuario $sqlcolegio ORDER BY PT.fechafin $orden";
	$result = posgre_query($sql);
	$rowCount = 2;
	while ($row = pg_fetch_array($result)){
		
		
		$idusuario = $row['idusuario'];
		$idcurso = $row['idcurso'];
		$numtarjeta = $row['numtarjeta'];
		$cantidad = $row['cantidad'];
		$fechahora = explode(".", $row['fechafin'])[0];
		$fecha = explode(" ", $fechahora)[0];
		$hora = explode(" ", $fechahora)[1];
		
		if ($cantidad==""){
			$precio = $row['precio'];
		}
		else{
			$precio = $cantidad/100;
		}
		
		$codoperacion = $row['codigoautorizacionsabadell'];
		
		$sql2 = "SELECT * FROM usuario WHERE id='$idusuario'";
		$result2 = posgre_query($sql2);
		if ($row2 = pg_fetch_array($result2)){
			$nombre = $row2['nombre'];
			$apellidos = $row2['apellidos'];
		}
		
		$sql2 = "SELECT * FROM curso WHERE id='$idcurso'";
		$result2 = posgre_query($sql2);
		if ($row2 = pg_fetch_array($result2)){
			$nombrecurso = $row2['nombre'];
		}
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $codoperacion);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, cambiaf_a_normal($fecha));
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $precio);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $nombrecurso);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $nombre." ".$apellidos);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $numtarjeta);
		$rowCount++;
	}
	
	
	
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$nombre = "informeTPV-".time();
	$objWriter->save('./files/'.$nombre.'.xls');
	
	$path = './files/'.$nombre.'.xls';
	ob_clean();
	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.basename($path).'"');
	header("Content-Transfer-Encoding: binary");
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Type: application/force-download");
	header("Content-Type: application/download");
	header("Content-Length: ".filesize($path));
	readfile($path);

}

$textobusqueda=strip_tags($_REQUEST['textobusqueda']); 
if ((isset($_REQUEST['buscar']))&&($textobusqueda<>"")){
	$textobusqueda=strval($textobusqueda);
}

include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->
<h2 class="titulonoticia">Resumen de pagos con tarjeta de crédito</h2>
<div class="bloque-lateral acciones">		
	<p>
		<a href="zona-privada_admin_informes_1.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
		<a href="informe_pagotpv.php?xls" class="btn btn-success" type="button"> Descargar Excel</a> 
	</p>
</div>

<div class="bloque-lateral buscador">
	<h4>Buscar usuario</h4>
	<form action="informe_pagotpv.php?buscar" method="post" enctype="multipart/form-data" >
		<fieldset>
			<div class="input-append">(nombre, apellidos)
			<input type="text" class="span5" id="terminobusqueda" name="textobusqueda" placeholder="búsqueda" value="<?=$textobusqueda?>" />
				<input class="btn" type="submit" value="Buscar" />

			</div>		
		</fieldset>
	</form>
</div>	


<? include("_aya_mensaje_session.php"); ?>
<TABLE > 
	<TR>
		<th width='7%'>Código autor. Sabadell</th>
		<th width='7%'><a href="informe_pagotpv.php?orden=<? if ($orden=="ASC"){ echo "DESC"; } else{ echo "ASC"; } ?><?=$getcurso?><?=$getusuario?>">Fecha</a></th>
		<th>Importe</th>
		<th>Curso</th>
		<th>Usuario</th>
		<th>Num. Tarjeta</th>
	</TR> 
	
	<?
	
	$sql = "SELECT * FROM curso_usuario CU, pedidostarjeta PT WHERE CU.tipoinscripcion=1 AND CU.borrado=0 AND CU.idcurso=PT.idcurso AND PT.tipopago=1 AND CU.idusuario=PT.idusuario $sqlcurso $sqlusuario $sqlcolegio ORDER BY PT.fechafin $orden";
	$result = posgre_query($sql);
	echo pg_last_error();
	while ($row = pg_fetch_array($result)){
		$idusuario = $row['idusuario'];
		$idcurso = $row['idcurso'];
		$numtarjeta = $row['numtarjeta'];
		$cantidad = $row['cantidad'];
		$fechahora = explode(".", $row['fechafin'])[0];
		$fecha = explode(" ", $fechahora)[0];
		$hora = explode(" ", $fechahora)[1];
		
		if ($cantidad==""){
			$precio = $row['precio'];
		}
		else{
			$precio = $cantidad/100;
		}
		
		$codoperacion = $row['codigoautorizacionsabadell'];
		
		$sql2 = "SELECT * FROM usuario WHERE id='$idusuario'";
		$result2 = posgre_query($sql2);
		if ($row2 = pg_fetch_array($result2)){
			$nombre = $row2['nombre'];
			$apellidos = $row2['apellidos'];
			$nombre = $nombre." ".$apellidos;
		}
		
		$sql2 = "SELECT * FROM curso WHERE id='$idcurso'";
		$result2 = posgre_query($sql2);
		if ($row2 = pg_fetch_array($result2)){
			$nombrecurso = $row2['nombre'];
		}
		if ((strpos(strtolower($nombre), strtolower($textobusqueda)) !== false) || ($textobusqueda=="")) {
			
			?>
			<tr>
				<td><?=$codoperacion?></td>
				<td><?=cambiaf_a_normal($fecha); ?> <?=$hora?></td>
				<td><strong><?=$precio?></strong>€</td>
				<td><a href="informe_pagotpv.php?idcurso=<?=$idcurso?>"><?=$nombrecurso?></td>
				<td><a href="informe_pagotpv.php?idusuario=<?=$idusuario?>"><?=$nombre?></td> 
				<td><?=$numtarjeta?></td>
			
			</tr>
			<?
		
		}
	}
	
	
	?>
	
	
</table>
		<div id="volverarriba">
			<hr />
			<a href="#" title="Volver al inicio de la página">Volver arriba <i class="icon-circle-arrow-up"></i></a>
		</div>
		<br />
	</div>
	<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->
<?

include("plantillaweb02admin.php"); 
?>