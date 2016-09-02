<?php
include("_funciones.php"); 
include("_cone.php"); 

$safe="Sistema de calidad";
$titulo1="sistema";
$titulo2="calidad";

////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sqlcolegio=" AND idcolegio='$idcolegio' ";
	}else{
		$_SESSION[error]="No dispone de permisos";	
		header("Location: index.php?error=true&est=ko#1");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$accion=strip_tags($_GET['accion']); 

if ($accion=="eliminar"){
	$id=strip_tags($_GET['id']); 
	if ($id<>""){
		$sql = "UPDATE sc_noconformidades SET borrado=1 WHERE id='$id' $sqlcolegio";
		posgre_query($sql);
	}
}



$orden=strip_tags($_GET['orden']);
if($orden=="DESC"){
	$sqlorden="DESC";
}else{
	$orden="ASC";
	$sqlorden="ASC";
}

$idprofesor = $_GET['idprofesor'];
$sqlprofesor="";
if (($idprofesor<>0)&&($idprofesor<>"")){
	$sqlprofesor = " AND idprofesor='$idprofesor' ";
}


$sqlcampo="id";
$campo=strip_tags($_GET['campo']);
if ($campo<>""){
	$sqlcampo=$campo;
}

$result=posgre_query("SELECT * FROM sc_noconformidades WHERE borrado=0 $sqlcolegio $sqlprofesor ORDER BY $sqlcampo $sqlorden;");  
	
	
include("plantillaweb01admin.php");

?>

<!--Arriba plantilla1-->
<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<? include("_aya_mensaje_session.php"); ?>
		
		
		<h2>No conformidades</h2>
		
		<div class="bloque-lateral acciones">		
			<p>
				<a href="sc_noconformidad.php" class="btn btn-success" type="button">Nuevo <i class="icon-plus"></i></a>
			</p>
		</div>
		
		<table class="align-center">
		<tr>
			<th><a href="sc_noconformidades.php?campo=id&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">NºInforme</a></th>
			<th><a href="sc_noconformidades.php?campo=fecha&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">Fecha</a></th>
			<th><a href="sc_noconformidades.php?campo=revision&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">Revisión de cierre</a></th>
			<th>Ref. curso</th>
			<th>Curso</th>
				<? if ($_SESSION[nivel]==1) { //Admin Total ?>
				<th>Colegio</th>
			<? } ?>
			<th>Área de trabajo</th>
			<th>Acción</th>	
		</tr>
		
		
		
		<? 
		while($row = pg_fetch_array($result)) { 
		
			$id = $row['id'];
			$fecha = $row['fecha'];
			$revision = $row['revision'];
			$idcolegio = $row['idcolegio'];
			$area = $row['area'];
			$idcurso = $row['idcurso'];
			$ninforme = $row['ninforme'];
			
			
			$sql = "SELECT nombre FROM usuario WHERE id='$idcolegio'";
			$resultcolegio = posgre_query($sql);
			$nombrecolegio = "";
			if ($rowcolegio = pg_fetch_array($resultcolegio)){
				$nombrecolegio = $rowcolegio['nombre'];
			}
			
			if ($revision==1){
				$revisiontexto = "Cerrado";
			}
			else{
				$revisiontexto = "Abierto";
			}
			
			$sql = "SELECT * FROM curso WHERE id='$idcurso'";
			$result2 = posgre_query($sql);
			$row2 = pg_fetch_array($result2);
			$nombrecurso = $row2['nombre'];
			

		
		?>
		
		
		
		
			<tr>
				<td><?=$ninforme?></td>
				<td><?=cambiaf_a_normal($fecha)?></td>	
				<td><?=$revisiontexto?></td>
				<td><?=$idcurso?></td>
				<td><?=$nombrecurso?></td>
				<? if ($_SESSION[nivel]==1) { ?>
					<td><?=$nombrecolegio?></td>
				<? } ?>
				<td><?=$area?></td>
				<td>
					<a href="sc_noconformidad.php?id=<?=$id;?>" class="btn btn-primary">editar</a>
					<a onclick="return confirm('&iquest;Desea eliminar?')" href="sc_noconformidades.php?id=<?=$id?>&accion=eliminar" class="btn btn-primary">eliminar</a>
				</td>
			</tr>
		
		<? } ?>

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
