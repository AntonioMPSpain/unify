<?php
//include("__seguridad01.php"); 
include("_funciones.php");
include("_cone.php");
$accion=$_GET['accion'];
$id=strip_tags($_GET['id']);
if($accion==guardar){
	$etiqueta=strip_tags($_POST['etiqueta']); 
	$color=strip_tags($_POST['color']); 
	if ($etiqueta<>''){
		 $link=iConectarse();  
		$Query = pg_query($link,"INSERT INTO etiqueta (texto, color) VALUES ('$etiqueta', '$color');") ;//or die (mysql_error()); 
		header("Location: etiqueta.php?est=ok"); 
	}else{
		echo "Error: mal uso. 11";
	}
}elseif($accion==guardarm){
	$etiqueta=strip_tags($_POST['etiqueta']); 
	$color=strip_tags($_POST['color']); 
	if ($etiqueta<>'' && $id<>''){
		 $link=iConectarse();  
		$Query = pg_query($link,"UPDATE etiqueta SET texto='$etiqueta', color='$color' WHERE id=$id;") ;//or die (mysql_error()); 
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
						<label class="control-label" for="inputName">Nombre:</label>
							<div class="controls">
								<input type="text" id="etiqueta" class="input-xlarge" name="etiqueta" value="<?=$etiqueta?>" />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputsme">Color:</label>
						
							<select name="color">								
								
								<option <? if ($row['color']=="#72c02c"){ echo 'selected'; } ?> style="background:#72c02c" value="#72c02c">Verde</option>
								<option <? if ($row['color']=="#3498db"){ echo 'selected'; } ?> style="background:#3498db" value="#3498db">Azul</option>
								<option <? if ($row['color']=="#e67e22"){ echo 'selected'; } ?> style="background:#e67e22" value="#e67e22">Naranja</option>
								<option <? if ($row['color']=="#e74c3c"){ echo 'selected'; } ?> style="background:#e74c3c" value="#e74c3c">Rojo</option>
								<option <? if ($row['color']=="#ecf0f1"){ echo 'selected'; } ?> style="background:#ecf0f1" value="#ecf0f1">Blanco</option>
								<option <? if ($row['color']=="#9b6bcc"){ echo 'selected'; } ?> style="background:#9b6bcc" value="#9b6bcc">Purpura</option>
								<option <? if ($row['color']=="#27d7e7"){ echo 'selected'; } ?> style="background:#27d7e7" value="#27d7e7">Azul agua</option>
								<option <? if ($row['color']=="#9c8061"){ echo 'selected'; } ?> style="background:#9c8061" value="#9c8061">Marrón</option>
								<option <? if ($row['color']=="#4765a0"){ echo 'selected'; } ?> style="background:#4765a0" value="#4765a0">Azul oscuro</option>
								<option <? if ($row['color']=="#79d5b3"){ echo 'selected'; } ?> style="background:#79d5b3" value="#79d5b3">Verde claro</option>
								<option <? if ($row['color']=="#a10f2b"){ echo 'selected'; } ?> style="background:#a10f2b" value="#a10f2b">Rojo oscuro</option>
								<option <? if ($row['color']=="#18ba9b"){ echo 'selected'; } ?> style="background:#18ba9b" value="#18ba9b">Verde azulado</option>
								
								
							</select>	
							
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
						<label class="control-label" for="inputsme">Nombre:</label>
							<div class="controls">
								<input type="text" id="etiqueta" class="input-xlarge" name="etiqueta" value="<?=$row["texto"]?>" />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputsme">Color:</label>
							<select name="color">
								<option <? if ($row['color']=="#72c02c"){ echo 'selected'; } ?> style="background:#72c02c" value="#72c02c">Verde</option>
								<option <? if ($row['color']=="#3498db"){ echo 'selected'; } ?> style="background:#3498db" value="#3498db">Azul</option>
								<option <? if ($row['color']=="#e67e22"){ echo 'selected'; } ?> style="background:#e67e22" value="#e67e22">Naranja</option>
								<option <? if ($row['color']=="#e74c3c"){ echo 'selected'; } ?> style="background:#e74c3c" value="#e74c3c">Rojo</option>
								<option <? if ($row['color']=="#ecf0f1"){ echo 'selected'; } ?> style="background:#ecf0f1" value="#ecf0f1">Blanco</option>
								<option <? if ($row['color']=="#9b6bcc"){ echo 'selected'; } ?> style="background:#9b6bcc" value="#9b6bcc">Purpura</option>
								<option <? if ($row['color']=="#27d7e7"){ echo 'selected'; } ?> style="background:#27d7e7" value="#27d7e7">Azul agua</option>
								<option <? if ($row['color']=="#9c8061"){ echo 'selected'; } ?> style="background:#9c8061" value="#9c8061">Marrón</option>
								<option <? if ($row['color']=="#4765a0"){ echo 'selected'; } ?> style="background:#4765a0" value="#4765a0">Azul oscuro</option>
								<option <? if ($row['color']=="#79d5b3"){ echo 'selected'; } ?> style="background:#79d5b3" value="#79d5b3">Verde claro</option>
								<option <? if ($row['color']=="#a10f2b"){ echo 'selected'; } ?> style="background:#a10f2b" value="#a10f2b">Rojo oscuro</option>
								<option <? if ($row['color']=="#18ba9b"){ echo 'selected'; } ?> style="background:#18ba9b" value="#18ba9b">Verde azulado</option>
								
							</select>	
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