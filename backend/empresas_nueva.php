<?

include("_funciones.php"); 
include("_cone.php");

////////// Filtros de nivel por usuario //////////////////////
session_start();

if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sqlcolegio="";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$idcolegio=0;
	$sqlcolegio="";
}
else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario ////////////////////// 

$id = $_REQUEST['id'];
$accion = $_REQUEST['accion'];



if ($accion=="guardar"){
	
	$error="";
	
	$nombre = pg_escape_string(strip_tags($_REQUEST['nombre']));
	$cif = pg_escape_string(strip_tags($_REQUEST['cif']));
	$domicilio = pg_escape_string(strip_tags($_REQUEST['domicilio']));
	$localidad = pg_escape_string(strip_tags($_REQUEST['localidad']));
	$cp = pg_escape_string(strip_tags($_REQUEST['cp']));
	$provincia = pg_escape_string(strip_tags($_REQUEST['provincia']));
	$persona = pg_escape_string(strip_tags($_REQUEST['persona']));
	$email = pg_escape_string(strip_tags($_REQUEST['email']));
	$movil = pg_escape_string(strip_tags($_REQUEST['movil']));
	$telefono = pg_escape_string(strip_tags($_REQUEST['telefono']));
	$fax = pg_escape_string(strip_tags($_REQUEST['fax']));
	$web = pg_escape_string(strip_tags($_REQUEST['web']));
	
	if ($nombre == ""){
		$error= "Nombre obligatorio";	
	}
	
	if ($cif != ""){
		// Validate CIF
	}
	
	if ($error==""){
	
		if ($id<=0){
			$sql = "INSERT INTO empresas_marketing(idcolegio, nombre, cif, domicilio, localidad, cp, provincia, persona, email, movil, telefono, fax, web) 
			VALUES ('$idcolegio','$nombre','$cif', '$domicilio', '$localidad', '$cp','$provincia' , '$persona', '$email', '$movil', '$telefono', '$fax', '$web') RETURNING id;";
			$result = posgre_query($sql);
			echo pg_last_error();
			$row = pg_fetch_array($result);
			$id = $row['id'];
		}
		else{
			$sql = "UPDATE empresas_marketing SET nombre='$nombre',cif='$cif',domicilio='$domicilio',localidad='$localidad',cp='$cp',provincia='$provincia',
					persona='$persona',email='$email',movil='$movil',telefono='$telefono',fax='$fax',web='$web' WHERE id='$id'";	
			posgre_query($sql);
			
			$sql2 = "DELETE FROM empresas_marketing_familias WHERE idempresa='$id'";
			posgre_query($sql2);
		}	
		
		
		foreach($_POST as $key => $value){
			$pos=strpos($key,"fam_");
			if($pos!==false){
				$pieces = explode("fam_", $key);
				$idfamilia = $pieces[1];
				
				$sql = "INSERT INTO empresas_marketing_familias(idempresa, idfamilia) VALUES ($id, $idfamilia)";
				posgre_query($sql);
				
			}
		}
	}	
}
elseif ($accion=="guardarcomentario"){
			
	$comentario = $_REQUEST['comentario'];
	
	$sql = "INSERT INTO empresas_marketing_comentarios(idcolegio, idempresa, comentario) VALUES ('$idcolegio', '$id', '$comentario')";
	posgre_query($sql);
}

if ($id>0){
				
	$sql = "SELECT * FROM empresas_marketing WHERE id='$id'";
	$result = posgre_query($sql);
	if ($row = pg_fetch_array($result)){
	
		$nombre = $row['nombre'];
		$cif = $row['cif'];
		$domicilio = $row['domicilio'];
		$localidad = $row['localidad'];
		$cp = $row['cp'];
		$provincia = $row['provincia'];
		$persona = $row['persona'];
		$email = $row['email'];
		$movil = $row['movil'];
		$telefono = $row['telefono'];
		$fax = $row['fax'];
		$web = $row['web'];
		$fechainserccion = cambiaf_a_normal($row['fecha']);
		$idcolegiocreador = $row['idcolegio'];
		
		if ($idcolegiocreador==0){
			$colegiocreador="Admin";
		}
		else{
			
			$sql = "SELECT nombre FROM usuario WHERE id='$idcolegiocreador'";
			$result = posgre_query($sql);
			if ($row = pg_fetch_array($result)){
				$colegiocreador=$row['nombre'];
			}
		}
			
						
			
		
	}
			
		
	
	
}

$titulo1="nueva";
$titulo2="empresa";
$safe="Marketing Empresas";
include("plantillaweb01admin.php");

?>
<!--Arriba plantilla1-->
<div class="grid-9 contenido-principal">
<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><? if ($id==0) { echo 'Insertar';} else {echo 'Editar';}?> empresa</h2>
		<div class="bloque-lateral acciones">		
			<p>
				<a href="empresas_marketing.php" class="btn btn-success">Volver <i class="icon-circle-arrow-left"></i></a>
			</p>
		</div>
		<span style="font-size:20px; color:red;"><? echo $error; $error=""; ?></span>
		<form id="formcontacto" method="post" action="empresas_nueva.php?accion=guardar&id=<?=$id?>" enctype="multipart/form-data" >
		<p>
			<label class="description"><strong><br />Datos de la empresa</strong><br /></label>
		</p>		
		<p>
			<label class="description" for="nombre">Nombre*:<br />
				<input id="nombre" name="nombre" type="text" maxlength="255"  size="80"  class="input-xxlarge" value="<?=$nombre?>" />  
			</label>
		</p>
							
		<p>
			<label class="description" for="cif">CIF:<br />
				<input id="cif" name="cif" type="text" maxlength="255"  size="80" value="<?=$cif?>" class="input-xxlarge"  />  
			</label>
		</p>	
		
		<p>
			<label class="description" for="domicilio">Domicilio:<br />
				<input id="domicilio" name="domicilio" type="text" maxlength="255"  size="80" value="<?=$domicilio?>" class="input-xxlarge" 	 />  
			</label>
		</p>
		<p>
			<label class="description" for="localidad">Localidad:<br />
				<input id="localidad" name="localidad" type="text" maxlength="255"  size="80" value="<?=$localidad?>" class="input-xxlarge" 	/>  
			</label>
		</p>
		
		<p>
			<label class="description" for="cp">Código Postal:<br />
				<input id="cp" name="cp" type="text" maxlength="255" value="<?=$cp?>" class="input-xxlarge"  />  
			</label>
		</p>
		
		<p>
			<label class="description" for="provincia">Ámbito/Provincia:<br />
				<select name="provincia" class="input-large" >
					<option <? if ($provincia=="Internacional"){echo 'selected';} ?> class="input-large" value="Internacional">--Internacional--</option>
					<option  <? if ($provincia=="Nacional"){echo 'selected';} ?> class="input-large" value="Nacional">--Nacional--</option>
				<?
				// Generar listado 
					$consulta = "SELECT * FROM etiqueta_provincia WHERE borrado = 0 ORDER BY id,deno;";
					$link=iConectarse(); 
					$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
					while($rowdg= pg_fetch_array($r_datos)) {	
						?>
						<option <? if ($provincia==$rowdg['deno']){echo 'selected';} ?> class="input-xxlarge" value="<?=$rowdg['deno']?>"><? echo ($rowdg['deno']); ?></option>
						<? 
					} ?>
			  </select>
			</label>
		</p>
		
		<hr>
		
		<p>
			<label class="description"><strong><br />Datos de contacto</strong><br /></label>
		</p>
		<p>
			<label class="description" for="persona">Persona de contacto:<br />
				<input id="persona" name="persona" type="text" maxlength="255" size="80" value="<?=$persona?>" class="input-xxlarge"  />  
			</label>
		</p>
		 
		<p>
			<label class="description" for="fax">Email:<br />
				<input id="email" name="email" type="text" maxlength="255" value="<?=$email?>" class="input-xxlarge"  />  
			</label>
		</p>
		
		<p>
			<label class="description" for="telefono">Móvil:<br />
				<input id="movil" name="movil" type="text" maxlength="255" value="<?=$movil?>" size="80" class="input-xxlarge"  />  
			</label>
		</p>
		<p>
			<label class="description" for="telefono">Teléfono:<br />
				<input id="telefono" name="telefono" type="text" maxlength="255" size="80" value="<?=$telefono?>" class="input-xxlarge"  />  
			</label>
		</p>
		<p>
			<label class="description" for="fax">Fax:<br />
				<input id="fax" name="fax" type="text" maxlength="255" value="<?=$fax?>" class="input-xxlarge"  />  
			</label>
		</p>
		
		<p>
			<label class="description" for="fax">Web:<br />
				<input id="web" name="web" type="text" maxlength="255" value="<?=$web?>" class="input-xxlarge"  />  
			</label>
		</p>
		<hr>
		
		<p>
			<label class="description"><strong><br />Familias</strong><br /></label>
		</p>
		
		<p>
			<? $resultfam=posgre_query("SELECT * FROM materiales_familias WHERE borrado=0 ORDER BY nombre;") ;//or die (mysql_error()); 
			
			while ($rowfam = pg_fetch_array($resultfam)){ 
				$idfam = $rowfam['id'];	
				$nombrefam = $rowfam['nombre'];	
				
				
				$selected = "";
				$sql = "SELECT * FROM empresas_marketing_familias WHERE idfamilia='$idfam' AND idempresa='$id'";
				$result = posgre_query($sql); 
				if ($row = pg_fetch_array($result)){
					$selected="checked";
				}
				
				
			?> &nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;&nbsp;<input <?=$selected?> type="checkbox" name="fam_<?=$idfam?>" value="<?=$idfam?>" /><?=$nombrefam?> <br><? } ?>
		</p>
		
		<hr>
	
		<? if ($id>0){ ?>
				
			<p>
				<label class="description"><strong><br />Metadatos</strong><br /></label>
			</p>		
					
			<p>
				<label class="description" for="fecha_insercion">Creador:<br /> 
					<input disabled size="10" id="colegio" name="colegio" type="text" maxlength="12"  class="input-xlarge"  value="<?=$colegiocreador?>"  />  
				</label>
			</p>
			
			<p>
				<label class="description" for="fecha_insercion">Fecha inserción:<br /> 
					<input disabled size="10" id="fecha_insercion" name="fecha_insercion" type="text" maxlength="12"  class="input-xlarge"  value="<?=$fechainserccion?>"  /> 
				</label>
			</p>
			
			<hr>
			
		<? } ?> 
		
		<? if (($idcolegio==0)||($idcolegio==$idcolegiocreador)) { ?>
			
			<p>
				<label class="description" for="ostos">
					<input  class="btn btn-primary" name="enviar" value="Guardar" type="submit" />
				</label>
			</p>
		
		<? } ?>
		
		</form>	
		
		<br>
		<form method="post" action="empresas_nueva.php?accion=guardarcomentario&id=<?=$id?>" enctype="multipart/form-data">
			
		<p>
			<label style="font-size:22px;" id="comentarios" class="description"><strong><br />Comentarios</strong><br /></label>
		</p>	
		
		<? 
			$sql = "SELECT * FROM empresas_marketing_comentarios WHERE idempresa='$id' AND borrado=0 ORDER BY fecha";
			$result = posgre_query($sql);
			
			$i=1;
			while ($row = pg_fetch_array($result)){
				$comentario = $row['comentario'];
				$idcolegiocomentario = $row['idcolegio'];
				$fechacomentario = cambiaf_a_normal($row['fecha']);
				
				if ($idcolegiocomentario==0){
					$colegiocreadorcomentario="Admin";
				}
				else{
					
					$sql = "SELECT nombre FROM usuario WHERE id='$idcolegiocomentario'";
					$result = posgre_query($sql);
					if ($row = pg_fetch_array($result)){
						$colegiocreadorcomentario=$row['nombre'];
					}
				}
				
				
				?>
					Comentario <strong><?=$i?></strong>:  por <?=$colegiocreadorcomentario?> el <?=$fechacomentario?><br>
					<textarea disabled> <?=$comentario?></textarea><br><br>
				<?
				$i++;
			}
		?>
		
		<br><br>
		<p> 
			<strong>Nuevo comentario</strong><br>
			<textarea id="comentario" name="comentario" rows="4" cols="45" class="input-xxlarge" ></textarea> 
			
			<label class="comentarios" for="comentarios">
				<input style="margin-left:450px;" <? if ($id==0) echo 'disabled' ?> class="btn btn-primary" name="enviar" value="Comentar" type="submit" />
				<? if ($id==0){ echo 'Para comentar es necesario guardar la empresa previamente';} ?>
			</label>
		</p>
		
		</form>	
		
			
<div class="clearfix"></div>
	</div>
</div>
<!--fin contenido-principal-->

<?

include("plantillaweb02admin.php"); 
?>