<?php

//OJO:  Dependiendo de la cantidad de etiquetas, este metodo puede hacer que el indice sea mayor que la tabla,
// Si es as habr que cambiarlo.

//include("__seguridad01.php"); 
$idpublicacion=strip_tags($_GET['id']);
if ($idpublicacion==''){
	header("Location: index.php?est=error_id.01");
	exit();
}
$tipo=strip_tags($_GET['tipo']);
$accion=strip_tags($_GET['accion']);
include("_cone.php");
include("_funciones.php");
$titulo1="área";
$titulo2="asignar"; 

if($accion=="inserta"){
	$idetiqueta=strip_tags($_GET['idetiqueta']);
	if ($idetiqueta==''){
		echo "Error de idetiqueta";
		//header("Location: __index.php?est=error_tipo.01");
		exit();
	}
	$idetiqueta=$_GET['idetiqueta']; // id eti
	$link=iConectarse(); 
	$Query = pg_query($link,"INSERT INTO generica_etiqueta (idgenerica,idetiqueta) VALUES ('$idpublicacion','$idetiqueta')");// or die (mysql_error()); 
	if ($Query){
		$est_texto="Se ha insertado correctamente.";
		$est_texto2="Guardado";
		$est="ok";
	}else{
		$est_texto="No se ha eliminado.";
		$est_texto2="No guardado";
		$est="ko";
	}
	header("Location: admin_contenido3_areas.php?est=$est&est_texto=$est_texto&est_texto2=$est_texto2&id=$idpublicacion");
	exit();
}
if($accion=="borrar"){
	$idetiqueta=$_GET['idetiqueta']; // id eti
	$link=iConectarse(); 
	$Query = pg_query($link,"DELETE FROM generica_etiqueta WHERE idgenerica ='$idpublicacion' AND idetiqueta='$idetiqueta';") ;//or die (mysql_error());  
	if ($Query){
		$est_texto="Se eliminado correctamente.";
		$est_texto2="Guardado";
		$est="ok";
	}else{
		$est_texto="No se ha eliminado.";
		$est_texto2="No guardado";
		$est="ko";
	}
	header("Location: admin_contenido3_areas.php?est=$est&est_texto=$est_texto&est_texto2=$est_texto2&id=$idpublicacion");
	exit();
}
include("plantillaweb01admin.php"); 

?>
<!--Arriba pantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">ETIQUETAS/ÁREAS</h2>
	<div class="bloque-lateral acciones">		
		<p><strong>Acciones:</strong>
			<a href="admin_contenido.php" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> 
		</p>
	</div>
		<br />
	<? include("_aya_mensaje.php"); 
	

		$link=iConectarse(); 
		$result=pg_query($link,"SELECT * FROM generica WHERE id='$idpublicacion'") ;//or die ("Erro_".mysql_error()); 
		$row = pg_fetch_array($result);
		$nombre = $row["titulo"];
	?>
<div class="row">
	<h3><?=$nombre?></h3>
	<div class="grid-6">
		<?php 
		
		
		   $link=iConectarse(); 
		   $result=pg_query($link,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,generica_etiqueta WHERE generica_etiqueta.idgenerica=$idpublicacion AND etiqueta.id=generica_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		?>
		<div align="center">
		<TABLE cellpadding="2" cellspacing="2" id="nodefinida" style="padding:2px;"> 
			<tr>
				<TD>Etiquetas Asignadas</TD>
				<TD>&nbsp;Acci&oacute;n&nbsp;</TD>
			</TR> 
			<?php    
			while($row = pg_fetch_array($result)) { 
					?><tr>
						<td align="left"><?=$row["texto"]?></td>
						<td>
						<a href="admin_contenido3_areas.php?idetiqueta=<?=$row["id"]?>&accion=borrar&id=<?=$idpublicacion?>" onclick="return confirmar('&iquest;Eliminar elemento? \n\n')" class="btn btn-primary">eliminar</a> 
						</td>
					</tr>
					<? 
			}
			pg_free_result($result); 
			pg_close($link); 
		?> 
		</table>
		<br /></div>
	</div>
	<div class="grid-6">
		<?php 
		   $link=iConectarse(); 
		   $result=pg_query($link,"SELECT * FROM etiqueta WHERE borrado=0 AND id NOT IN (SELECT idetiqueta FROM generica_etiqueta WHERE idgenerica='$idpublicacion') ORDER BY tipo, texto;") ;//or die (mysql_error()); 
		?>
		<div align="center">
		<TABLE > 
			<tr>
				<TD>&nbsp;Acci&oacute;n&nbsp;</TD>
				<TD>Etiquetas disponibles</TD>
			</TR> 
			<?php       
		   while($row = pg_fetch_array($result)) { 
			  ?><tr>
				<td align="left">
					<a href="admin_contenido3_areas.php?id=<?=$idpublicacion?>&idetiqueta=<?=$row["id"]?>&accion=inserta" class="btn btn-primary">asignar</a>
				</td>
				<td align="left"><?=$row["texto"]?></td>
			</tr>
		   <? 
		   } 
		   pg_free_result($result); 
		   pg_close($link); 
		?> 
		</table>
		<br /></div>
</div>
</div>
		<div id="volverarriba">
			<hr />
			<a href="admin_contenido.php"title="Volver al inicio de la página">Volver a Curso <i class="icon-circle-arrow-up"></i></a>
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