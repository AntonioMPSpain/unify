<?php
include_once("_funciones.php"); 
include_once("_cone.php"); 
//include_once("p_funciones.php"); 

$safe="Publicidad Banners";
$titulo1="publicidad";
$titulo2="banners";

////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==1) { //Admin Total

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
		$sql = "UPDATE p_anuncios SET borrado=1 WHERE id='$id'";
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

$sqlcampo="id";
$campo=strip_tags($_GET['campo']);
if ($campo<>""){
	$sqlcampo=$campo;
}

$result=posgre_query("SELECT * FROM p_anuncios WHERE borrado=0 ORDER BY $sqlcampo $sqlorden;");  
	
	
include("plantillaweb01admin.php");

?>

<!--Arriba plantilla1-->
<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<? include("_aya_mensaje_session.php"); ?>
		
		
		<h2>Anuncios</h2>
		
		<div class="bloque-lateral acciones">		
			<p>
				<a href="p_anuncio.php" class="btn btn-success" type="button">Nuevo <i class="icon-plus"></i></a>
			</p>
		</div>
		
		<table class="align-center">
		<tr>
			<th>Nombre</th>
			<th><a href="p_anuncios.php?campo=estado&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">Estado</a></th>
			<th><a href="p_anuncios.php?campo=fechainicio&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">Fecha inicio</a></th>
			<th><a href="p_anuncios.php?campo=fechafin&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">Fecha fin</a></th>
			<th>Cliks</th>
			<th>Acción</th>	
		</tr>
		
		
		
		<? 
		while($row = pg_fetch_array($result)) { 
		
			$id = $row['id'];
			$nombre = $row['nombre'];
			$fechainicio = $row['fechainicio'];
			$fechafin = $row['fechafin'];
			$estado = $row['estado'];
			
			$hoy = date("Y-m-d");

			$fuerafechas = false;
			if ($fechainicio<=$hoy){
				$bgcolorfechainicio = "#A8FFAE";		// Verde
			}
			else{
				$bgcolorfechainicio = "#FFD2C6";		// Rojo
				$fuerafechas = true;
			}
			
			if ($fechafin>=$hoy){
				$bgcolorfechafin = "#A8FFAE";		// Verde
			}
			else{
				$bgcolorfechafin = "#FFD2C6";		// Rojo
				$fuerafechas = true;
			}
					
			if ($estado==0){
				$textoestado = "Activado";		// Verde
				$bgcolorestado = "#A8FFAE";
			}
			else{
				$textoestado = "Desactivado";	
				$bgcolorestado = "#FFD2C6";		// Rojo
			}
			
			if ($fuerafechas){
				$textoestado .= " - Fuera de fecha";
				$bgcolorestado = "#FFD2C6";	
			}
		
			$sqls = "SELECT * FROM p_stats WHERE idanuncio='$id'";
			$results = posgre_query($sqls);
			$visitas = pg_num_rows($results);
		
		?>
		
		
		
		
			<tr>	
				<td><?=$nombre?></td>
				<td bgcolor="<?=$bgcolorestado?>"><?=$textoestado?></td>
				<td bgcolor="<?=$bgcolorfechainicio?>"><?=cambiaf_a_normal($fechainicio)?></td>	
				<td bgcolor="<?=$bgcolorfechafin?>"><?=cambiaf_a_normal($fechafin)?></td>
				<td><?=$visitas?></td>
				
				<td>
					<a href="p_anuncio.php?id=<?=$id;?>" class="btn btn-primary">editar</a>
					<a onclick="return confirm('&iquest;Desea eliminar?')" href="p_anuncios.php?id=<?=$id?>&accion=eliminar" class="btn btn-primary">eliminar</a>
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
