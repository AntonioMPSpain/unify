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
		$Query = pg_query($link,"INSERT INTO etiqueta (texto) VALUES ('$etiqueta');") ;//or die (mysql_error()); 
		header("Location: etiqueta.php?est=ok"); 
	}else{
		echo "Error: mal uso. 11";
	}
}elseif($accion==guardarm){
	$etiqueta=strip_tags($_POST['etiqueta']); 
	if ($etiqueta<>'' && $id<>''){
		 $link=iConectarse();  
		$Query = pg_query($link,"UPDATE etiqueta SET texto='$etiqueta' WHERE id=$id;") ;//or die (mysql_error()); 
		header("Location: etiqueta.php?est=ok"); 
		exit();
	}else{
		echo "Error: mal uso. 22";
	}
}


if (($accion=="")){
	$titulo1="área";
	$titulo2="nueva";
	include("plantillaweb01admin.php"); 
	?>	
	<!------------Arriba ---------->
	<div class="grid-8 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">
	<h2>ETIQUETAS/ÁREAS</h2>
		<hr />
			<FORM METHOD="post" ACTION="etiqueta2.php?accion=guardar" enctype="multipart/form-data" >
				<fieldset>				    
					<legend>Datos</legend>
					<div class="control-group">
						<label class="control-label" for="inputName">Etiqueta:</label>
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
	$titulo1="área";
	$titulo2="editar";
	include("plantillaweb01admin.php"); 
	if ($id<>''){
		$link=iConectarse(); 
		$result=pg_query($link,"SELECT * FROM etiqueta WHERE id='$id';");//or die (mysql_error());
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
	<h2>ETIQUETAS/ÁREAS</h2>
		<hr />
		<FORM METHOD="post" ACTION="etiqueta2.php?accion=guardarm&id=<?=$id?>" enctype="multipart/form-data">
				<fieldset>				    
					<legend>Datos</legend>
					<? if ($est=="ko"){ ?><span class="rojo">Error</span><? }?>
					<div class="control-group">
						<label class="control-label" for="inputsme">Etiqueta:</label>
							<div class="controls">
								<input type="text" id="etiqueta" class="input-xlarge" name="etiqueta" value="<?=$row["texto"]?>" />
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