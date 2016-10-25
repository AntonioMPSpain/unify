<?php
include("_funciones.php"); 
include("_cone.php"); 
require_once('lib_actv_api.php');
$safe="categorias";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
$id=strip_tags($_GET['id']);
//$id_categoria_padre=strip_tags($_GET['id_categoria_padre']);
$id_categoria_padre=2;//Significa que es la categoria principal donde estan todos los colegios
if($accion=="guardar"){ //guarda nueva
	$email=strip_tags($_POST['email']);
	$nombre=strip_tags($_POST['nombre']);
	$apellidos=strip_tags($_POST['apellidos']);
	$login=strip_tags($_POST['login']);
	$confirmado=strip_tags($_POST['confirmado']);
	$pass=strip_tags($_POST['pass']);
	$pass2=strip_tags($_POST['pass2']);
	$idprovincia = strip_tags($_POST['idprovincia']);
	$loginnumeros = solonumeros($login);
	
	
	if ($pass<>$pass2){
		$est_texto="* las contrase&ntilde;as no coinciden";
		$est="ko";
	}else{
		// comprobar que no exista
		$linka=iConectarse(); 
		$resulta=pg_query($linka,"SELECT id FROM usuario WHERE borrado=0 AND login='$loginnumeros';") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
		$cuantos=pg_num_rows($resulta);
		if (($nombre<>'')&&($cuantos==0)){
			$link=iConectarse(); 
			$Query = pg_query($link,"INSERT INTO usuario (email,apellidos,login,nombre,nif,nivel,confirmado,pass,idprovincia) VALUES ('$email','$apellidos','$loginnumeros','$nombre','$login','2','$confirmado','$pass','$idprovincia') RETURNING id;" );// or die (mysql_error()); 
			// RETURNING Currval('usuario_id_seq');";	
			$resulta = pg_fetch_array($Query);
			$id_categoria_externa = $resulta['id'];
			//$resulta=pg_query($link,"SELECT id FROM usuario ORDER BY id DESC;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
			//$rr=pg_fetch_array($resulta);
			//$id_categoria_externa=$rr["id"];
			
			if ($confirmado==1){
				$datos_categoria = array (
							"nombre" 	=> $nombre,
							"id_categoria_padre" => $id_categoria_padre,
							"id_categoria_externa" => $id_categoria_externa,
							"descripcion" => $nombre
				);
				//var_dump($datos_categoria);
				$id_categoria = crea_categoria_moodle($datos_categoria);
				if ($id_categoria>0) {
					$est="ok";
					$est_texto="Se ha guardado correctamente.";
					$est_texto2="Guardado";
					$ssql="UPDATE usuario SET  id_categoria_moodle = '$id_categoria'  WHERE id ='$id_categoria_externa';";	
					$link=iConectarse(); 
					$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
				} else {
					$est="ko";
					$est_texto="No se ha editado correctamente.";
					$est_texto2="NO Guardado";
				} 
			}else{
					$est="ok";
					$est_texto="Se ha guardado correctamente pero al no estar confirmado no se inserta en moodle.";
					$est_texto2="Guardado";
					
					exec("php /var/www/moodle/admin/cli/cron.php > /var/www/web/tmp/notas.txt");
					//exec("php /var/www/moodle/auth/db/cli/sync_users.php > /var/www/web/tmp/notas2.txt");
					exec("php /var/www/moodle/auth/db/cli/sync_user_actv.php $loginnumeros > /var/www/web/tmp/notas2.txt");
			}												
		}else{
			$est="ko";
			$est_texto="Login no es correcto.";
			$est_texto2="NO Guardado";
		}
	}
	header("Location: zona-privada_admin_cuentas.php?est=$est&est_texto=$est_texto&est_texto2=$est_texto2");
	exit();
}elseif($accion=="guardarm"){ //guarda modificado
	$email=strip_tags($_POST['email']);
	$nombre=strip_tags($_POST['nombre']);
	$apellidos=strip_tags($_POST['apellidos']);
	$confirmado=strip_tags($_POST['confirmado']);
	$pass=strip_tags($_POST['pass']);
	$pass2=strip_tags($_POST['pass2']);
	$idprovincia = strip_tags($_POST['idprovincia']);
	$est_texto="No se ha insertado correctamente";
	$est_texto2="No guardado";
	$est="ok";
	$id=strip_tags($_REQUEST['id']);
	if (($est=="ok")&&($id<>"")){
		if ($pass=="") {//Datos incorrectos
			$ssql="UPDATE usuario SET  email='$email', confirmado = '$confirmado', nombre = '$nombre', apellidos = '$apellidos', idprovincia = '$idprovincia'  WHERE id ='$id';";	
		}else{
			if ($pass<>$pass2){
				$est_texto="* las contrase&ntilde;as no coinciden";
				$est="ko";
			}else{			
				$est="ok";
				$ssql="UPDATE usuario SET email='$email',pass='$pass', confirmado = '$confirmado', nombre = '$nombre', apellidos = '$apellidos', idprovincia = '$idprovincia'  WHERE id ='$id';";	
			}	
		}
		$link=iConectarse(); 
		$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
		if ($Query){//
			$est_texto="Se ha editado correctamente.";
			$est_texto2="Guardado";
			exec("php /var/www/moodle/admin/cli/cron.php > /var/www/web/tmp/notas.txt");
			//exec("php /var/www/moodle/auth/db/cli/sync_users.php > /var/www/web/tmp/notas2.txt");
			exec("php /var/www/moodle/auth/db/cli/sync_user_actv.php $login > /var/www/web/tmp/notas2.txt");
		}
		//if ($confirmado==1){
		//}
	}else{
		$est="ko";
	}
	//echo $ssql.$est.$est_texto.$est_texto2; exit();
	header("Location: zona-privada_admin_cuentas.php?est=$est&est_texto=$est_texto&est_texto2=$est_texto2");
	exit();
}



if (($accion=="")){
	$titulo1="categoria";
	$titulo2="nueva";
	include("plantillaweb01admin.php"); 
	?>	
	<!--Arriba -->
	<div class="grid-8 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">
	<h2><?=$safe?></h2>
		<hr />
			<FORM METHOD="post" ACTION="categoria2.php?accion=guardar" enctype="multipart/form-data" >
				<fieldset>				    
					<legend>Datos</legend>
					<? if ($est=="ko"){ ?><span class="rojo">Error</span><? }?>
					<div class="control-group">
						<label class="control-label" for="inputName">Login(CIF):</label>
							<div class="controls">
								<input maxlength="9" type="text" id="inputName" class="input-xlarge" name="login" value="<?=$login?>" />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Nombre:</label>
							<div class="controls">
								<input type="text" id="inputName" class="input-xlarge" name="nombre" value="<?=$nombre?>" />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Apellidos:</label>
							<div class="controls">
								<input type="text" id="inputName" class="input-xlarge" name="apellidos" value="<?=$row["apellidos"]?>" />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inpuemae">Email:</label>
							<div class="controls">
								<input type="text" id="inputfgme" class="input-xlarge" name="email" value="<?=$email?>" />
							</div>
					</div>
					<div class="control-group">
					<label class="control-label" for="inputName">Provincia:</label>
						<div class="controls">
							<select name="idprovincia" class="input-large" >
							<?
							// Generar listado 
								$consulta = "SELECT * FROM etiqueta_provincia WHERE borrado = 0 ORDER BY id;";
								$link=iConectarse(); 
								$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
								while($rowdg= pg_fetch_array($r_datos)) {	
									?>
									<option class="input-large" value="<?=$rowdg['id']?>"<? if  ($idprovincia == $rowdg['id']) { echo " selected "; } ?>><? echo ($rowdg['deno']); ?></option>
									<? 
								} ?>
						  </select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Contrase&ntilde;a:
							<? if ($est=="ko2"){ ?> <span class="rojo"> * no puede estar en blanco</span><? }?>
							<? if ($est=="ko3"){ ?> <span class="rojo"> * las contrase&ntilde;as no coinciden</span><? }?> 
						</label>
							<div class="controls">
								<input id="inputName" class="input-small" type="password" name="pass"  />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Repetir Contrase&ntilde;a:
							<? if ($est=="ko2"){ ?> <span class="rojo"> * no puede estar en blanco</span><? }?>
							<? if ($est=="ko3"){ ?> <span class="rojo"> * las contrase&ntilde;as no coinciden</span><? }?> 
						</label>
							<div class="controls">
								<input id="inputName" class="input-small" type="password" name="pass2"  />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Estado:</label>
							<div class="controls">
								<select name="confirmado" class="input-xlarge" >
									<option class="input-xlarge" value="1" <? if ($confirmado==1) echo " selected "; ?>>[activo]</option>
									<option class="input-xlarge" value="0" <? if ($confirmado==0) echo " selected "; ?>>[no activo]</option>
								</select>
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
	$titulo1="categoria";
	$titulo2="editar";
	include("plantillaweb01admin.php"); 
	if ($id<>''){
		$link=iConectarse(); 
		$result=pg_query($link,"SELECT * FROM usuario WHERE nivel=2 AND id='$id';");//or die (mysql_error());
		$row = pg_fetch_array($result);
		$confirmado=$row["confirmado"];
		$idprovincia=$row["idprovincia"];
	}else{
		echo "Error: mal uso 1.";
		exit();
	}
	   	?>
	<!--Arriba -->
	<div class="grid-8 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">
	<h2><?=$safe?></h2>
		<hr />
		<FORM METHOD="post" ACTION="categoria2.php?accion=guardarm&id=<?=$id?>" enctype="multipart/form-data">
				<fieldset>				    
					<legend>Datos</legend>
					<? if ($est=="ko"){ ?><span class="rojo">Error</span><? }?>
					<div class="control-group">
						<label class="control-label" for="inputsme">Login(CIF):</label>
							<div class="controls">
								<input type="text" id="inputName" class="input-xlarge" disabled="disabled" name="login" value="<?=$row["nif"]?>" />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Nombre:</label>
							<div class="controls">
								<input type="text" id="inputName" class="input-xlarge" name="nombre" value="<?=$row["nombre"]?>" />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Apellidos:</label>
							<div class="controls">
								<input type="text" id="inputName" class="input-xlarge" name="apellidos" value="<?=$row["apellidos"]?>" />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inpsme">Email:</label>
							<div class="controls">
								<input type="text" id="inas" class="input-xlarge" name="email" value="<?=$row["email"]?>" />
							</div>
					</div>
					<div class="control-group">
					<label class="control-label" for="inputName">Provincia:</label>
						<div class="controls">
							<select name="idprovincia" class="input-large" >
							<?
							// Generar listado 
								$consulta = "SELECT * FROM etiqueta_provincia WHERE borrado = 0 ORDER BY id;";
								$link=iConectarse(); 
								$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
								while($rowdg= pg_fetch_array($r_datos)) {	
									?>
									<option class="input-large" value="<?=$rowdg['id']?>"<? if  ($idprovincia == $rowdg['id']) { echo " selected "; } ?>><? echo ($rowdg['deno']); ?></option>
									<? 
								} ?>
						  </select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Contrase&ntilde;a:
							<? if ($est=="ko2"){ ?> <span class="rojo"> * no puede estar en blanco</span><? }?>
							<? if ($est=="ko3"){ ?> <span class="rojo"> * las contrase&ntilde;as no coinciden</span><? }?> 
						</label>
							<div class="controls">
								<input id="inputName" class="input-small" type="password" name="pass"  />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Repetir Contrase&ntilde;a:
							<? if ($est=="ko2"){ ?> <span class="rojo"> * no puede estar en blanco</span><? }?>
							<? if ($est=="ko3"){ ?> <span class="rojo"> * las contrase&ntilde;as no coinciden</span><? }?> 
						</label>
							<div class="controls">
								<input id="inputName" class="input-small" type="password" name="pass2"  />
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Estado:</label>
							<div class="controls">
								<select name="confirmado" class="input-xlarge" >
									<option class="input-xlarge" value="1" <? if ($confirmado==1) { ?> selected="selected" <? }?>>[activo]</option>
									<option class="input-xlarge" value="0" <? if ($confirmado==0) { ?> selected="selected" <? }?>>[no activo]</option>
								</select>
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