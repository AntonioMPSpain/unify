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

include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->
<h2 class="titulonoticia">Resumen de pagos con tarjeta de crédito</h2>
<div class="bloque-lateral acciones">		
	<p>
		<a href="zona-privada_admin_informes_1.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
</div>
<? include("_aya_mensaje_session.php"); ?>
<TABLE > 
	<TR>
		<th width='7%'>Código autor. Sabadell</th>
		<th><a href="informe_pagotpv.php?orden=<? if ($orden=="ASC"){ echo "DESC"; } else{ echo "ASC"; } ?><?=$getcurso?><?=$getusuario?>">Fecha</a></th>
		<th>Importe</th>
		<th>Curso</th>
		<th>Usuario</th>
	</TR> 
	
	<?
	
	$sql = "SELECT * FROM curso_usuario CU, pedidostarjeta PT WHERE CU.tipoinscripcion=1 AND CU.borrado=0 AND CU.idcurso=PT.idcurso AND PT.tipopago=1 AND CU.idusuario=PT.idusuario $sqlcurso $sqlusuario $sqlcolegio ORDER BY PT.fechafin $orden";
	$result = posgre_query($sql);
	echo pg_last_error();
	while ($row = pg_fetch_array($result)){
		$precio = $row['precio'];
		$idusuario = $row['idusuario'];
		$idcurso = $row['idcurso'];
		$fechahora = explode(".", $row['fechafin'])[0];
		$fecha = explode(" ", $fechahora)[0];
		$hora = explode(" ", $fechahora)[1];
		
		
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
		
		?>
		<tr>
			<td><?=$codoperacion?></td>
			<td><?=cambiaf_a_normal($fecha); ?> <?=$hora?></td>
			<td><?=$precio?></td>
			<td><a href="informe_pagotpv.php?idcurso=<?=$idcurso?>"><?=$nombrecurso?></td>
			<td><a href="informe_pagotpv.php?idusuario=<?=$idusuario?>"><?=$nombre?> <?=$apellidos?></td> 
		
		</tr>
		
		<?
		
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