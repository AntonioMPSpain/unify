<?php
//include("__seguridad01.php"); 
include("_funciones.php");
include("_cone.php");
$accion=$_GET['accion'];
$id=strip_tags($_GET['id']);
if($accion==guardar){
	$etiqueta=strip_tags($_POST['etiqueta']); 
	if ($etiqueta<>''){
		 $link=iConectarse();  
		$Query = pg_query($link,"INSERT INTO materiales_familias (nombre) VALUES ('$etiqueta');") ;//or die (mysql_error()); 
		header("Location: familias.php?est=ok"); 
	}else{
		echo "Error: mal uso. 11";
	}
}elseif($accion==guardarm){
	$etiqueta=strip_tags($_POST['etiqueta']); 
	if ($etiqueta<>'' && $id<>''){
		 $link=iConectarse();  
		$Query = pg_query($link,"UPDATE materiales_familias SET nombre='$etiqueta' WHERE id=$id;") ;//or die (mysql_error()); 
		header("Location: familias.php?est=ok"); 
		exit();
	}else{
		echo "Error: mal uso. 22";
	}
}


if (($accion=="")){
	$titulo1="familia";
	$titulo2="nueva";
	include("plantillaweb01admin.php"); 
	?>	
	<!------------Arriba ---------->
	<div class="grid-8 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">
	<h2>Familias</h2>
		<hr />
			<FORM METHOD="post" ACTION="familias2.php?accion=guardar" enctype="multipart/form-data" >
				<fieldset>				    
					<legend>Datos</legend>
					<div class="control-group">
						<label class="control-label" for="inputName">Nombre:</label>
							<div class="controls">
								<input type="text" id="etiqueta" class="input-xlarge" name="etiqueta" value="<?=$etiqueta?>" />
							</div>
					</div>
					
					</fieldset>
					<div class="form-actions">
						<? if ($titulo1=="editar") { $textboton="Guardar cambios";} else{ $textboton="Guardar";}?>
						<button type="submit" class="btn btn-primary btn-large"><?=$textboton?></button>
					</div>
					</form>
	<?php 
}elseif($accion=="editar") {
	$titulo1="familia";
	$titulo2="editar";
	include("plantillaweb01admin.php"); 
	if ($id<>''){
		$link=iConectarse(); 
		$result=pg_query($link,"SELECT * FROM materiales_familias WHERE id='$id';");//or die (mysql_error());
		$row = pg_fetch_array($result);
	}else{
		echo "Error: mal uso 1.";
		exit();
	}
	   	?>
	<!------------Arriba ---------->
	<div class="grid-8 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">
	<h2>Familias</h2>
		<hr />
		<FORM METHOD="post" ACTION="familias2.php?accion=guardarm&id=<?=$id?>" enctype="multipart/form-data">
				<fieldset>				    
					<legend>Datos</legend>
					<? if ($est=="ko"){ ?><span class="rojo">Error</span><? }?>
					<div class="control-group">
						<label class="control-label" for="inputsme">Nombre:</label>
							<div class="controls">
								<input type="text" id="etiqueta" class="input-xlarge" name="etiqueta" value="<?=$row["nombre"]?>" />
							</div>
					</div>
					</fieldset>
					<div class="form-actions">
						<? if ($titulo1=="editar") { $textboton="Guardar cambios";} else{ $textboton="Guardar";}?>
						<button type="submit" class="btn btn-primary btn-large"><?=$textboton?></button>
					</div>
					<?									   
					pg_free_result($result); 
				    pg_close($link); 
				   //session_destroy();
					?>
				</form>
<?
}
?>
 </div>
<!--fin pagina blog-->
<div class="clearfix"></div>
</div>
<!--fin grid-8 contenido-principal-->

<?
include("plantillaweb02admin.php"); 
?>