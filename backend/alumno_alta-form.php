<?
include_once "_funciones.php"; 
?>
<form action="alumno_alta.php?accion=guardar<?=$actionform?>" method="post" enctype="multipart/form-data">
			<fieldset>				    
				<legend><?=$legendform?></legend>
				<? if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2)) { //Admin Total ?>				
					<div class="control-group">
						<label class="control-label" for="inputName">Colegio profesional:</label>
							<div class="controls">
								<select name="idcolegio" class="input-large" >
								<?
								// Generar listado 
									if ($_SESSION[nivel]==2){
										$consulta = "SELECT * FROM usuario WHERE borrado=0 AND id IN('$_SESSION[idcolegio]', '$idcolegio')";
									}
									else{
										$consulta = "SELECT * FROM usuario WHERE nivel=2 AND borrado = 0 ORDER BY nombre,id;";
									}
									$link=iConectarse(); 
									$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
									while($rowdg= pg_fetch_array($r_datos)) {	
										?>
										<option class="input-large" value="<?=$rowdg['id']?>"<? if  ($idcolegio == $rowdg['id']) { echo " selected "; } ?>><? echo ($rowdg['nombre']); ?></option>
										<? 
									} ?>
							  
									<option <? if  ($idcolegio == 0) { echo " selected "; } ?> class="input-large" value="0">[sin asignar] NO COLEGIADO</option> 
							  </select>
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Nivel:</label>
							<div class="controls">
								<select name="nivel" class="input-large" >
									<option class="input-large" <? if ($nivel==4) echo " selected "; ?>  value="4">Alumno</option>
									<option class="input-large" <? if (($nivel==3)||($nuevo==3)) echo " selected "; ?>  value="3">Profesor</option>
									
									<? if ($_SESSION[nivel]==1){ ?>
									
										<option class="input-large" <? if ($nivel==1) echo " selected "; ?>  value="1">Administrador Total</option>
										<option class="input-large" <? if ($nivel==2) echo " selected "; ?>  value="2">Administrador Colegio</option>
										
									<? } ?>
								</select>
							</div>
					</div>
				<? } ?>
				 <? /*
				  <div class="control-group">
					<label class="control-label" for="inputName">Tipo:</label>
						<div class="controls">
							<select name="tipo" class="input-large" >
								<option class="input-large" <? if ($tipo==0) echo " selected "; ?> value="0">[sin definir]</option>
								<option class="input-large" <? if ($tipo==1) echo " selected "; ?>  value="1">Colegiado</option>
								<option class="input-large" <? if ($tipo==2) echo " selected "; ?>  value="2">Pre-colegiado</option>
								<option class="input-large" <? if ($tipo==3) echo " selected "; ?>  value="3">Estudiante</option>
								<option class="input-large" <? if ($tipo==4) echo " selected "; ?>  value="4">No colegiado</option>
							</select>
						</div>
				</div>  */ ?>
				<? if ($confirmado==0){ ?>
				<div class="control-group">
					<label class="control-label" for="inputName">Confirmado:</label>
						<div class="controls">
							<select name="confirmado" class="input-large" >
								<option class="input-large" <? if ($confirmado==1) echo " selected "; ?>  value="1">SI</option>
								<option class="input-large" <? if ($confirmado==0) echo " selected "; ?> value="0">NO</option>
							</select>
						</div>
				</div>
				<? } 
				else {?>
					<input type="hidden" name="confirmado" id="confirmado" value="1" />	
				
				<? } ?>
				<hr />
				<div class="control-group">
					<label class="control-label" for="inputName">NIF: [login] (obligatorio)</label>
						<div class="controls">
							<input type="text" id="inputName" placeholder="NIF" class="input-small" name="nif" value="<?=$nif?>" />
						<? 
						//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok,	
						if (($nif!="") && (valida_nif_cif_nie($nif)<1)) { //si el nif_cif_nie es incorrecto
							?> <span style="color:#FF0000">* nif/cif/nie no es correcto</span><?
						}
						if ($est2=="ko5"){ ?> <span class="rojo"> * error</span><? }?>

						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Nombre: (obligatorio)</label>
						<div class="controls">
							<input type="text" id="inputName" placeholder="Nombre" class="input-xlarge" name="nombre" value="<?=$nombre?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Apellidos: (obligatorio)</label>
						<div class="controls">
							<input type="text" id="inputa" placeholder="Apellidos" class="input-xlarge" name="apellidos" value="<?=$apellidos?>" />
						</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputName">E-mail: (obligatorio)
						<? if ($est2=="ko2"){ ?> <span class="rojo"> * error</span><? }?>
						<? if ($est2=="ko4"){ ?> <span class="rojo"> * error</span><? }?>
					</label>
					<div class="controls">
						<input type="text" id="inputEmail" placeholder="correo@electronico.com" class="input-xlarge" name="email" value="<?=$email?>" />
					</div>
					<?
					if (($email!="") && (comprobar_email($email)<1)) { //si el nif_cif_nie es incorrecto
						?> <span style="color:#FF0000">* email no es correcto</span><?
					}
					?>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputName">Tel&eacute;fono:</label>
						<div class="controls">
							<input type="text" id="inputName" class="input-small" placeholder="Teléfono" name="telefono" value="<?=$telefono?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputte2">Tel&eacute;fono 2:</label>
						<div class="controls">
							<input type="text" id="input2e" class="input-small" placeholder="Teléfono" name="telefono2" value="<?=$telefono2?>" />
						</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputName">Direcci&oacute;n:</label>
						<div class="controls">
							<input type="text" id="inputName" class="input-xxlarge" placeholder="Dirección" name="direccion" value="<?=$direccion?>" />
						</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputName">Municipio:</label>
						<div class="controls">
							<input type="text" id="inputName" class="input-xlarge" placeholder="Municipio" name="municipio" value="<?=$municipio?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Código postal:</label>
					<div class="controls">
						<input type="text" id="inputName" class="input-small" placeholder="Código postal" name="cp" value="<?=$cp?>" />
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
						<? if ($est2=="ko3"){ ?> <span class="rojo"> * error</span><? }?>
						<? if ($est2=="ko6"){ ?> <span class="rojo"> * error</span><? }?>
					</label>
						<div class="controls">
							<input id="inputName" class="input-small" type="password" name="pass"  />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Repetir Contrase&ntilde;a:
						<? if ($est2=="ko3"){ ?> <span class="rojo"> * error</span><? }?>
						<? if ($est2=="ko6"){ ?> <span class="rojo"> * error</span><? }?>
					</label>
					<div class="controls">
						<input id="inputName" class="input-small" type="password" name="pass2"  />
					</div>
				</div>
				
				<hr>
				<div class="control-group">
					<label class="control-label" for="inputName">N. Colegiado: (si dispone)</label>
					<div class="controls">
						<input type="text" id="inputName" class="input-mini" name="ncolegiado" value="<?=$ncolegiado?>" />
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="procedencia">Colegio de procedencia: </label>
						<div class="controls">
							<input type="text" id="procedencia" class="input-xxlarge" name="procedencia" value="<?=$procedencia?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="universidad">Universidad de procedencia: </label>
						<div class="controls">
							<input type="text" id="universidad" class="input-xxlarge" name="universidad" value="<?=$universidad?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="titulacion">Titulación: </label>
						<div class="controls">
							<input type="text" id="titulacion" class="input-xxlarge" name="titulacion" value="<?=$titulacion?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="titulacion">Breve curriculum / Titulaciones: </label>
						<div class="controls">
							<textarea name="curriculum" id="curriculum" class="inputtextarea input-xxlarge" cols="45" rows="4" ><?=$curriculum?></textarea>
						</div>
				</div>

				<div class="control-group">
				    <label class="checkbox">
				    <input type="checkbox" name="acepto" checked="checked" /> Acepto las <a href="aviso_legal.php">condiciones del servicio</a>.
						<? if ($est2=="ko1"){ ?> <span class="rojo"> * error</span><? }?>
				    </label>
				</div>
			</fieldset>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary btn-large">Guardar</button>
				<? if ($_SESSION['nivel']==1){ ?>
					<button style="float:right;" type="submit" name="baja" id="baja" onclick="return confirm('Seguro que desea dar de baja al usuario?')" class="btn btn-primary btn-large">Dar de baja</button>
				<? } ?>
			</div>
			</form>
