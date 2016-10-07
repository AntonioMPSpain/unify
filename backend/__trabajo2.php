<?
include("_seguridadfiltro.php"); 
include("_funciones.php"); 
include("_conemysql.php");
include("_cone.php");
 
$safe="configuracion";
$accion=$_GET['accion'];
$c_directorio = "/var/www/www.tuedificioenforma.es";


$accionborra=$_GET['accionborra'];
if($accionborra=="borrar"){
	$id=$_GET['id'];
	$link=conectar(); //Postgrepsql 
	$Query = pg_query($link,"UPDATE trabajo SET archivo='' WHERE $sql id='$id'") ;//or die (mysql_error()); 	
	$_SESSION[esterror]="Guardado correctamente.";
	header("Location: admin_trabajo.php");
	exit();
}
if ($_GET['accion']=='guardar'){	
	$nombre=($_POST['nombre']);
	//echo (strip_tags($_POST['nombre']))."--";
	$domicilio=($_POST['domicilio']);
	$localidad=($_POST['localidad']);
	$provincia=($_POST['provincia']);
	$cp=($_POST['cp']);
	$telefono=($_POST['telefono']);
	$fax=($_POST['fax']);
	$persona=($_POST['persona']);
	$denominacion=($_POST['denominacion']);
	$zona=($_POST['zona']);
	$fecha=cambiaf_a_mysql(($_POST['fecha']));
	$fecha_insercion=cambiaf_a_mysql(($_POST['fecha_insercion']));

	$otras_caracteristicas=nl2br(strip_tags($_POST['otras_caracteristicas']));
	$requisitos=nl2br(strip_tags($_POST['requisitos']));
	$otros_datos=nl2br(strip_tags($_POST['otros_datos']));

	$email=($_POST['email']);
	$link=conectar(); //Postgrepsql 
	$nombre_archivo = $_FILES['archivo']['name']; 
	$tipo_archivo = $_FILES['archivo']['type']; 
	$tamano_archivo = $_FILES['archivo']['size'];
	if (trim($_FILES['archivo']['tmp_name'])=="") { //Si no ha puesto archivo
		$Query =pg_query($link,"INSERT INTO trabajo (idcolegio,fecha_insercion,estado,nombre,domicilio,localidad,provincia,cp,telefono,fax,persona,denominacion,zona,fecha,email,otras_caracteristicas,requisitos,otros_datos) VALUES ('$idcolegio','$fecha_insercion','1','$nombre','$domicilio','$localidad','$provincia','$cp','$telefono','$fax','$persona','$denominacion','$zona','$fecha','$email','$otras_caracteristicas','$requisitos','$otros_datos')" );// or die ("error al inserat. ".mysql_error()); 
		//$idsafe=mysql_insert_id();
		if ($Query){
			$_SESSION[esterror]="Guardado correctamente.";
		}else{
			$_SESSION[esterror]="No Guardado correctamente. Hay parámetros obligatorios.";
		}
		header("Location: admin_trabajo.php");
		exit();
	}else{	
		//compruebo si las características del archivo son las que deseo 
		if (!( (strpos($tipo_archivo, "powerpoint"))||(strpos($tipo_archivo, "ods"))||(strpos($tipo_archivo, "xls"))||(strpos($tipo_archivo, "excel"))||(strpos($tipo_archivo, "bmp"))||(strpos($tipo_archivo, "octet-stream"))||(strpos($tipo_archivo, "rar"))||(strpos($tipo_archivo, "zip"))||(strpos($tipo_archivo, "rtf"))||(strpos($tipo_archivo, "ppt"))||(strpos($tipo_archivo, "tif"))||(strpos($tipo_archivo, "odt"))||(strpos($tipo_archivo, "pdf"))||(strpos($tipo_archivo, "doc"))||(strpos($tipo_archivo, "msword"))||(strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) && ($tamano_archivo < 5200000000)) {  
			$_SESSION[esterror]="Tipo o tamaño de archivo incorrecto ".$nombre_archivo.$tipo_archivo."- ".$tamano_archivo;
			header("Location: admin_trabajo.php");
			exit();
		}else{
			switch( $tipo_archivo ) 
			{ 
			  case "application/pdf": $extension="pdf"; break; 
			  case "application/msword": $extension="doc"; break; 
			  case "application/vnd.openxmlformats-officedocument.wordprocessingml.document";	$extension = "docx"; break; 
			  case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";	$extension = "xlsx"; break; 
			  case "application/vnd.ms-excel": $extension="xls"; break; 
			  case "application/vnd.ms-excel": $extension="xls"; break; 
			  case "image/JPG": 
			  case "image/JPEG": 
			  case "image/jpg": 
			  case "image/jpeg": $extension="jpg"; break; 
			  case "image/bmp": $extension="bmp"; break; 
			  case "application/rar": $extension="rar"; break; 
			  case "application/octet-stream": $extension="rar"; break; 
			  case "application/x-rar-compressed": $extension="rar"; break; 
			  case "application/x-zip-compressed": $extension="zip"; break; 
			  case "application/x-download": $extension="zip"; break; 				  
			  case "application/zip": $extension="zip"; break; 
			  case "application/rtf": $extension="rtf"; break; 
			  case "application/vnd.ms-powerpoint": $extension="ppt"; break; 
			  case "image/tiff": $extension="tif"; break; 
			  case "application/vnd.oasis.opendocument.text": $extension="odt"; break; 
			  case "application/vnd.oasis.opendocument.spreadsheet": $extension="ods"; break; 
			  
			} 
			if ($extension==""){
				if (( (strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) ) {  
					$extension="jpg";
				}else{
					$_SESSION[esterror]="Tipo o tamaño de archivo incorrecto ".$nombre_archivo.$tipo_archivo."- ".$tamano_archivo;
					header("Location: admin_trabajo.php");
					exit();
				}
			}
		}
		$archiv=sanear_string(utf8_encode($nombre_archivo))."_";
		$archivv=$archiv.=time();
		$archivv=$archivv.".".$extension;
		$destino = $c_directorio."/files/".$archivv;	
		if (move_uploaded_file ($_FILES['archivo']['tmp_name'],$destino)){			
			$link=conectar(); //Postgrepsql 
			$Query = pg_query($link,"INSERT INTO trabajo (idcolegio,fecha_insercion,estado,nombre,domicilio,localidad,provincia,cp,telefono,fax,persona,denominacion,zona,fecha,email,otras_caracteristicas,requisitos,otros_datos,archivo) VALUES ('$idcolegio','$fecha_insercion','1','$nombre','$domicilio','$localidad','$provincia','$cp','$telefono','$fax','$persona','$denominacion','$zona','$fecha','$email','$otras_caracteristicas','$requisitos','$otros_datos','$archivv')" );// or die ("error al inserat. ".mysql_error()); 
			if ($Query){
				$_SESSION[esterror]="Guardado correctamente.";
			}else{
				$_SESSION[esterror]="No Guardado correctamente. Hay parámetros obligatorios.";
			}
			header("Location: admin_trabajo.php");
			exit();
		}else{
					$_SESSION[esterror]="Tipo o tamaño de archivo incorrecto ";
					header("Location: admin_trabajo.php");
					exit();
		} 
	}//Fin no ha puesto archivo
	header("Location: admin_trabajo.php");
	exit();
}elseif($_GET['accion']=='guardarm'){
	$id=$_GET['id'];
	if (!is_numeric($id)){
		$id=$_POST['id'];
		if (!is_numeric($id)){
			header("Location: index.php?salir=true");
			exit();
		}
	}
	$nombre=($_POST['nombre']);
	//echo htmlentities(strip_tags($_POST['nombre']))."--";
	$domicilio=($_POST['domicilio']);
	$localidad=($_POST['localidad']);
	$provincia=($_POST['provincia']);
	$cp=($_POST['cp']);
	$telefono=($_POST['telefono']);
	$fax=($_POST['fax']);
	$persona=($_POST['persona']);
	$denominacion=($_POST['denominacion']);
	$zona=($_POST['zona']);
	$fecha=cambiaf_a_mysql(($_POST['fecha']));
	$fecha_insercion=cambiaf_a_mysql(($_POST['fecha_insercion']));
	$otras_caracteristicas=nl2br(strip_tags($_POST['otras_caracteristicas']));
	$requisitos=nl2br(strip_tags($_POST['requisitos']));
	$otros_datos=nl2br(strip_tags($_POST['otros_datos']));
	$email=($_POST['email']);
	$link=conectar(); //Postgrepsql 
	$nombre_archivo = $_FILES['archivo']['name']; 
	$tipo_archivo = $_FILES['archivo']['type']; 
	$tamano_archivo = $_FILES['archivo']['size'];
	if (trim($_FILES['archivo']['tmp_name'])=="") { //Si no ha puesto archivo
		//$link=conectamysql(); 
		$Query = pg_query($link,"UPDATE trabajo SET fecha_insercion='$fecha_insercion',nombre='$nombre',domicilio='$domicilio',localidad='$localidad',provincia='$provincia',cp='$cp',telefono='$telefono',fax='$fax',persona='$persona',denominacion='$denominacion',zona='$zona',fecha='$fecha',email='$email',otras_caracteristicas='$otras_caracteristicas',requisitos='$requisitos',otros_datos='$otros_datos' WHERE $sql id=$id; ") ;//or die ("error al inserat. ".mysql_error()); 
		if ($Query){
			$_SESSION[esterror]="Guardado correctamente.";
		}else{
			$_SESSION[esterror]="No Guardado correctamente. Hay parámetros obligatorios.";
		}
		header("Location: admin_trabajo.php");
		exit();
	}else{	
		//compruebo si las características del archivo son las que deseo 
		if (!( (strpos($tipo_archivo, "powerpoint"))||(strpos($tipo_archivo, "ods"))||(strpos($tipo_archivo, "xls"))||(strpos($tipo_archivo, "excel"))||(strpos($tipo_archivo, "bmp"))||(strpos($tipo_archivo, "octet-stream"))||(strpos($tipo_archivo, "rar"))||(strpos($tipo_archivo, "zip"))||(strpos($tipo_archivo, "rtf"))||(strpos($tipo_archivo, "ppt"))||(strpos($tipo_archivo, "tif"))||(strpos($tipo_archivo, "odt"))||(strpos($tipo_archivo, "pdf"))||(strpos($tipo_archivo, "doc"))||(strpos($tipo_archivo, "msword"))||(strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) && ($tamano_archivo < 5200000000)) {  
			$_SESSION[esterror]="Error en tipo o tamaño de archivo:-".$tipo_archivo."- ".$tamano_archivo;
			header("Location: admin_trabajo.php");
			exit();
		}else{
			switch( $tipo_archivo ) 
			{ 
			  case "application/pdf": $extension="pdf"; break; 
			  case "application/msword": $extension="doc"; break; 
			  case "application/vnd.openxmlformats-officedocument.wordprocessingml.document";	$extension = "docx"; break; 
			  case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";	$extension = "xlsx"; break; 
			  case "application/vnd.ms-excel": $extension="xls"; break; 
			  case "application/vnd.ms-excel": $extension="xls"; break; 
			  case "image/JPG": 
			  case "image/JPEG": 
			  case "image/jpg": 
			  case "image/jpeg": $extension="jpg"; break; 
			  case "image/bmp": $extension="bmp"; break; 
			  case "application/rar": $extension="rar"; break; 
			  case "application/octet-stream": $extension="rar"; break; 
			  case "application/x-rar-compressed": $extension="rar"; break; 
			  case "application/x-zip-compressed": $extension="zip"; break; 
			  case "application/x-download": $extension="zip"; break; 				  
			  case "application/zip": $extension="zip"; break; 
			  case "application/rtf": $extension="rtf"; break; 
			  case "application/vnd.ms-powerpoint": $extension="ppt"; break; 
			  case "image/tiff": $extension="tif"; break; 
			  case "application/vnd.oasis.opendocument.text": $extension="odt"; break; 
			  case "application/vnd.oasis.opendocument.spreadsheet": $extension="ods"; break; 
			  
			} 
			if ($extension==""){
				if (( (strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) ) {  
					$extension="jpg";
				}else{
					$_SESSION[esterror]="Error en tipo o tamaño de archivo:-".$tipo_archivo."- ".$tamano_archivo;
					header("Location: admin_trabajo.php");
					exit();
				}
			}
		}
		$archiv=sanear_string(utf8_encode($nombre_archivo))."_";
		$archivv=$archiv.=time();
		$archivv=$archivv.".".$extension;
		$destino = $c_directorio."/files/".$archivv;	
		if (move_uploaded_file ($_FILES['archivo']['tmp_name'],$destino)){
			$link=conectar(); //Postgrepsql 
			$Query = pg_query($link,"UPDATE trabajo SET archivo='$archivv',fecha_insercion='$fecha_insercion',nombre='$nombre',domicilio='$domicilio',localidad='$localidad',provincia='$provincia',cp='$cp',telefono='$telefono',fax='$fax',persona='$persona',denominacion='$denominacion',zona='$zona',fecha='$fecha',email='$email',otras_caracteristicas='$otras_caracteristicas',requisitos='$requisitos',otros_datos='$otros_datos' WHERE $sql id=$id; " );// or die ("error al inserat. ".mysql_error()); 
		}else{
			$_SESSION[esterror]="Error en tipo o tamaño de archivo";
			header("Location: admin_trabajo.php");
			exit();
		}
		
	} //Fin de si no hay archivo	
	if ($Query){
		//$enviado=mail("jose@newsisco.com","Coaatmu","Coaatmu","Coaatmu");
		/*if ($enviado){ 
			echo "OK";
			exit();
		}else{
			echo "KO";
			exit();
		}*/
		$_SESSION[esterror]="Guardado correctamente.";
		header("Location: admin_trabajo.php");
		exit();
	}else{
		$_SESSION[esterror]="No se ha podido guardar.";
		header("Location: admin_trabajo.php");
		exit();
	}
}
 
include("plantillaweb01admin.php"); 
if ((!isset($_GET['accion']))){	
	if (!$_GET['error']){	
	?>
	<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Insertar Nueva Oferta de Trabajo</h2>
			<?
			if ($_SESSION[nivel]==1) { //Admin Total
				echo "Accion desactivada temporalmente.";
				exit();
			}
			
				if ($_GET['est']=="ok") {
					?><br /><br /><h3 class="rojo">Guardado correctamente</h3><br /><br /><?
				}elseif($_GET['est']=="ko") {
					?><br /><br /><h3 class="rojo">No se ha guardado</h3><br /><br /><?
				}
			?>
					<legend><strong>Rellene los datos necesarios en el formulario adjunto</strong>:</legend>
					<form id="formcontacto" method="post" action="__trabajo2.php?accion=guardar" enctype="multipart/form-data" >
					<p>
						<label class="description"><strong><br />Datos de la empresa</strong><br /></label>
					</p>				
					<p>
						<label class="description" for="nombre">Nombre*:<br />
							<input id="nombre" name="nombre" type="text" maxlength="255"  size="80"  class="input-xxlarge"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="domicilio">Domicilio:<br />
							<input id="domicilio" name="domicilio" type="text" maxlength="255"  size="80" class="input-xxlarge" 	 />  
	 					</label>
					</p>
					<p>
						<label class="description" for="localidad">Localidad:<br />
							<input id="localidad" name="localidad" type="text" maxlength="255"  size="80" class="input-xxlarge" 	/>  
	 					</label>
					</p>
					<p>
						<label class="description" for="provincia">Ámbito/Provincia:<br />
							<select name="provincia" class="input-large" >
								<option class="input-large" value="Internacional">--Internacional--</option>
								<option class="input-large" value="Nacional">--Nacional--</option>
							<?
							// Generar listado 
								$consulta = "SELECT * FROM etiqueta_provincia WHERE borrado = 0 ORDER BY id;";
								$link=iConectarse(); 
								$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
								while($rowdg= pg_fetch_array($r_datos)) {	
									?>
									<option class="input-large" value="<?=$rowdg['deno']?>"><? echo ($rowdg['deno']); ?></option>
									<? 
								} ?>
						  </select>
	 					</label>
					</p>
					<p>
						<label class="description" for="cp">Código Postal:<br />
							<input id="cp" name="cp" type="text" maxlength="255"  class="input-xxlarge" 	 />  
	 					</label>
					</p>
					<p>
						<label class="description" for="telefono">Teléfono(s):<br />
							<input id="telefono" name="telefono" type="text" maxlength="255"  size="80" class="input-xxlarge"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="fax">Fax:<br />
							<input id="fax" name="fax" type="text" maxlength="255"  class="input-xxlarge"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="persona">Contacto:<br />
							<input id="persona" name="persona" type="text" maxlength="255" size="80"  class="input-xxlarge"  />  
	 					</label>
					</p>
					<hr>
					<p>
						<label class="description"><strong><br />Características del puesto de trabajo</strong><br /></label>
					</p>
					<p>
						<label class="description" for="denominacion">Denominación del puesto*:<br />
							<input id="denominacion" size="80" name="denominacion" type="text" maxlength="255"  class="input-xxlarge" 	 />  
	 					</label>
					</p>
					<p>
						<label class="description" for="zona">Zona de trabajo:<br />
							<input id="zona" size="80" name="zona" type="text" maxlength="255"  class="input-xxlarge"   />  
	 					</label>
					</p>
					<p>
						<label class="description" for="fecha">Fecha límite*:<br />
							<input id="fecha" name="fecha" type="text" maxlength="255"  class="input-xxlarge"  value="<?=$fecha?>"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="fecha_insercion">Fecha Inserción*:<br />
							<input id="fecha_insercion" name="fecha_insercion" type="text" maxlength="255"  class="input-xxlarge"  value="<?=$fecha?>"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="email">E-mail:<br />
							<input id="email" name="email" type="text" maxlength="255"  size="80"  class="input-xxlarge" 	 />  
	 					</label>
					</p>
					<p>
						<label class="description" for="otras_caracteristicas">Características:<br />
							<textarea id="otras_caracteristicas" name="otras_caracteristicas" rows="2" cols="45" class="input-xxlarge" ></textarea> 
						</label>
					</p>
					<p>
						<label class="description"><strong><br />Requisitos de los candidatos</strong><br /></label>
					</p>				
					<p>
						<label class="description" for="requisitos">Requisitos*:<br />
							<textarea id="requisitos" name="requisitos" rows="2" cols="45" class="input-xxlarge" ></textarea> 
						</label>
					</p>
					<p>
						<label class="description" for="otros_datos">Otros datos:<br />
							<textarea id="otros_datos" name="otros_datos" rows="2" cols="45" class="input-xxlarge" ></textarea> 
						</label>
					</p>
					<p>
						<label class="description" for="archivo">Archivo:<br />
							<input id="archivo" name="archivo" type="file" />
						</label>
					</p>
					<p>
						<label class="description" for="ostos">
							<input  class="btn btn-primary" name="enviar" value="Guardar" type="submit" />
						</label>
					</p>
					</form>					
				<!-- 				******************* -->
		<?
	}
}else{ //Modificar
	$id=$_REQUEST['id']+0;
	if (!is_numeric($id)){
		$id=$idsafe;
		header("Location: index.php?salir=true");
		exit();
	}
	$link=conectar(); //Postgrepsql 
	$result=pg_query($link,"SELECT * FROM trabajo WHERE $sql id=$id;") ;//or die (mysql_error());  
	$row = pg_fetch_array($result);
	//$row=utf8_encode($row);
	$otras_caracteristicas=br2nl($row["otras_caracteristicas"]);
	$requisitos=br2nl($row["requisitos"]);
	$otros_datos=br2nl($row["otros_datos"]);
	
	if (!$_GET['error']){	
		?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Modificar Oferta de Trabajo</h2>
			<?
				if ($_GET['est']=="ok") {
					?><br /><br /><h3 class="rojo">Guardado correctamente</h3><br /><br /><?
				}elseif($_GET['est']=="ko") {
					?><br /><br /><h3 class="rojo">No se ha podido guardar</h3><br /><br /><?
				}
			?>
					<legend><strong>Datos</strong>:</legend>
					<form id="formcontacto" method="post" action="__trabajo2.php?accion=guardarm&id=<?=$id?>" enctype="multipart/form-data" >
					<p>
						<label class="description"><strong><br />Datos de la empresa</strong><br /></label>
					</p>				
					<p>
						<label class="description" for="nombre">Nombre*:<br />
							<input size="80"  id="nombre" name="nombre" type="text" maxlength="255" value="<?=$row["nombre"]?>"  class="input-xxlarge"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="domicilio">Domicilio:<br />
							<input size="80"  id="domicilio" name="domicilio" type="text" maxlength="255" value="<?=$row["domicilio"]?>"  class="input-xxlarge" 	 />  
	 					</label>
					</p>
					<p>
						<label class="description" for="localidad">Localidad:<br />
							<input  size="80" id="localidad" name="localidad" type="text" maxlength="255" value="<?=$row["localidad"]?>"  class="input-xxlarge" 	/>  
	 					</label>
					</p>
					<p>
						<label class="description" for="provincia">Provincia:<br />
							<input size="80" id="provincia" name="provincia" type="text" maxlength="255" value="<?=$row["provincia"]?>"  class="input-xxlarge"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="cp">Código Postal:<br />
							<input size="80" id="cp" name="cp" type="text" maxlength="255" value="<?=$row["cp"]?>"  class="input-xxlarge" 	 />  
	 					</label>
					</p>
					<p>
						<label class="description" for="telefono">Teléfono(s):<br />
							<input size="80"  id="telefono" name="telefono" type="text" maxlength="255" value="<?=$row["telefono"]?>"  class="input-xxlarge"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="fax">Fax:<br />
							<input size="80" id="fax" name="fax" type="text" maxlength="255" value="<?=$row["fax"]?>"  class="input-xxlarge"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="persona">Contacto:<br />
							<input size="80" id="persona" name="persona" type="text" maxlength="255" value="<?=$row["persona"]?>"  class="input-xxlarge"  />  
	 					</label>
					</p>
					<hr>
					<p>
						<label class="description"><strong><br />Características del puesto de trabajo</strong><br /></label>
					</p>
					<p>
						<label class="description" for="denominacion">Denominación del puesto*:<br />
							<input size="80" id="denominacion" name="denominacion" type="text" maxlength="255" value="<?=$row["denominacion"]?>"  class="input-xxlarge" 	 />  
	 					</label>
					</p>
					<p>
						<label class="description" for="zona">Zona de trabajo:<br />
							<input size="80" id="zona" name="zona" type="text" maxlength="255" value="<?=$row["zona"]?>"  class="input-xxlarge"   />  
	 					</label>
					</p>
					<p>
						<label class="description" for="fecha">Fecha límite*:<br />
							<input size="10" id="fecha" name="fecha" type="text" maxlength="12"  class="input-xxlarge"  value="<?=cambiaf_a_normal($row["fecha"])?>"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="fecha_insercion">Fecha inserción*:<br />
							<input size="10" id="fecha_insercion" name="fecha_insercion" type="text" maxlength="12"  class="input-xxlarge"  value="<?=cambiaf_a_normal($row["fecha_insercion"])?>"  />  
	 					</label>
					</p>
					<p>
						<label class="description" for="email">E-mail:<br />
							<input size="80" id="email" name="email" type="text" maxlength="255"  class="input-xxlarge"  value="<?=$row["email"]?>" />  
	 					</label>
					</p>
					<p>
						<label class="description" for="otras_caracteristicas">Características:<br />
							<textarea id="otras_caracteristicas" name="otras_caracteristicas" rows="2" cols="45" class="input-xxlarge" ><?=$otras_caracteristicas?></textarea> 
						</label>
					</p>
					<? /*
					<p>
						<label class="description" for="archivo">Archivo:<br />
							<input id="archivo" name="archivo" type="file" />
						</label>
					</p>
			  <?
			$archivo = "/var/www/www.tuedificioenforma.es/files/".$row["archivo"];
			if (is_file ($archivo)){
				?>
					<p>
						<label class="description" for="archivo2">Archivo actual:<br />
							<a class="a3"  href="descargat.php?documento=<?=$row["archivo"]?>"> <i class="icon-zoom-in"></i> [Descargar] </a>
							<br /><a onclick="return confirmar('¿Eliminar elemento?')" class="a3" href="__trabajo2.php?accionborra=borrar&accion=<?=$accion?>&tipo=<?=$tipo?>&id=<?=$row["id"]?>"> <i class="icon-trash"></i>Eliminar</a>
						</label>
					</p>
				<? 	
			}
			*/?>		  
					
					<p>
						<label class="description"><strong><br />Requisitos de los candidatos</strong><br /></label>
					</p>				
					<p>
						<label class="description" for="requisitos">Requisitos*:<br />
							<textarea id="requisitos" name="requisitos" rows="2" cols="45" class="input-xxlarge" ><?=$requisitos?></textarea> 
						</label>
					</p>
					<p>
						<label class="description" for="otros_datos">Otros datos:<br />
							<textarea id="otros_datos" name="otros_datos" rows="2" cols="45" class="input-xxlarge" ><?=$otros_datos?></textarea> 
						</label>
					</p>
					<p>
						<label class="description" for="enviar"><br />
							<input  class="btn btn-primary" name="enviar" value="Guardar" type="submit" />
						</label>
					</p>
					</form>					
				<!-- 				******************* -->
		<?
	}
}
?>
	<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->


<?
include("plantillaweb02admin.php"); 
?>				
