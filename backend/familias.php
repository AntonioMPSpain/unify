<?php
//include("__seguridad01.php"); 
include("_funciones.php");
include("_cone.php"); 
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
$est_texto=strip_tags($_GET['est_texto']);
$est_texto2=strip_tags($_GET['est_texto2']);
if($accion=="borrar"){
	$id=strip_tags($_GET['id']);
	$est_texto="error";
	$est="ko";
	$est_texto2="No guardado";
	if (($id<>"")){
		$sqlasiog="UPDATE materiales_familias SET borrado='1' WHERE $sql id= '$id' ;";
		$linkb=iConectarse(); 
		$Query = pg_query($linkb,$sqlasiog); 
		if ($Query) {
			$est="ok";
			$est_texto="Se ha eliminado correctamente.";
			$est_texto2="Guardado";
		}	
	}
}
$titulo1="familias";
$titulo2="materiales";

include("plantillaweb01admin.php"); 
?>
<!------------Arriba pantilla1---------->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Familias</h2>
		<br />
	<? include("_aya_mensaje.php"); ?>
		<div class="bloque-lateral acciones">		
   	 				<p>
   	 					<a href="familias2.php" class="btn btn-success" type="button">Nuevo <i class="icon-plus"></i></a> |
   	 					<!--<button class="btn btn-success" type="button">Nuevo desde plantilla <i class="icon-plus-sign"></i></button> |  
   	 					<button class="btn " type="button">Seleccionar todo <i class="icon-ok"></i></button>  | 
   	 					<button class="btn btn-warning" type="button">Eliminar <i class="icon-trash"></i></button> -->
   	 				</p>
		</div>
<?php 
   $link=iConectarse(); 
   $result=pg_query($link,"SELECT * FROM materiales_familias WHERE borrado=0 ORDER BY nombre;") ;//or die (mysql_error()); 
?>
<table class="align-center"> 
	<tr>
		<th>Etiqueta</th>
		<th>&nbsp;Acci&oacute;n&nbsp;</th>
	</tr> 
	<?php       
   $bgcolor="#ECF3FF";
   while($row = pg_fetch_array($result)) { 
      ?><tr>
	  	<td align="left"><?=$row["nombre"]?> </td>
		<td>
		<a href="familias.php?id=<?=$row["id"]?>&accion=borrar" onclick="return confirmar('&iquest;Eliminar elemento? \n\n')" class="btn btn-primary">eliminar</a> 
		<a href="familias2.php?id=<?=$row["id"]?>&accion=editar" class="btn btn-primary">editar</a>
		</td>
	</tr>
   <? 
   } 
   pg_free_result($result); 
   pg_close($link); 
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