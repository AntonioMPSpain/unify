<?
$safe="Multimedia de curso";
include("_cone.php");
include("_funciones.php");
$c_directorio_img = "/var/www/web";
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[error]="No dispone de permisos";	
		header("Location: index.php?error=true&est=ko#1");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$idcolegio=0;
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$est=($_REQUEST['est']);
$id=($_REQUEST['id']);
if (($id<>'')){
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $ssql id='$id' AND borrado=0;");// or die (pg_error());
	if ($result) {
		$row = pg_fetch_array($result);
		$nombre=$row["nombre"];
		$imagen=$row["imagen"];
	}else{
		$_SESSION[error]="Error en curso 2-1";	
		header("Location: index.php?salir=true&est=ko"); 
		exit();
	}
}else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?error=true#1");
	exit();
}



$accion=$_GET['accion'];
if($accion=='guardarm'){
	$foto= $_FILES['userfile']['name']; 
	$tipo_archivo = $_FILES['userfile']['type'];
	//$informacion = str_replace("\n", "<BR>",$informacion);
	if (!isset($foto)){
		$_SESSION[error]="<div class=wrap-col><div class=ok> No hay datos a guardar. </div></div>";
		$est="ko";
	}else{	 
		$nombre_archivo = $_FILES['userfile']['name']; 
		$tipo_archivo = $_FILES['userfile']['type']; 
		$tamano_archivo = $_FILES['userfile']['size'];
		//compruebo si las caractersticas del archivo son las que deseo 
		if (!( (strpos($tipo_archivo, "bmp"))||(strpos($tipo_archivo, "tif"))||(strpos($tipo_archivo, "png"))||(strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) && ($tamano_archivo < 5200000000)) {  
			$_SESSION[error]="Error en tipo o tamaño de archivo:-".$tipo_archivo."- ".$tamano_archivo;
			$est="ko";
		}else{
			switch( $tipo_archivo ) 
			{ 
			  case "image/png": $extension="png"; break; 
			  case "image/JPG": 
			  case "image/JPEG": 
			  case "image/jpg": 
			  case "image/jpeg": $extension="jpg"; break; 
			  case "image/bmp": $extension="bmp"; break; 
			  case "image/tiff": $extension="tif"; break; 
			  
			} 
			if ($extension==""){
				if (( (strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) ) {  
					$extension="jpg";
				}else{
					$_SESSION[error]="Error en tipo o tamaño de archivo:".$tipo_archivo." - ".$tamano_archivo;
					$est="ko";
				}
			}
			$archiv=sanear_string($nombre_archivo)."_";
			$archivv=$archiv.=time();
			$archivv=$archivv.".".$extension;
			$destino = $c_directorio_img."/imagen/".$archivv;	
			$archi2=$archivv."p.jpg";
			//$destino = $c_directorio."/files/".$archivv;	
			 if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$destino)){	
					$link=iConectarse(); 
					$Query = pg_query($link,"UPDATE curso SET imagen='$archivv' WHERE $sql id='$id'");
					$est="ok";
					//echo " <p class=ok>  Comenzamos a disminuir la imagen... </p>";
					include("_resize.php");
					if (resizeImg("imagen/".$archivv,"imagen/".$archi2,561,374)){
						$_SESSION[error]="Se ha guardado correctamente.";	
						$est="ok";
						$link=iConectarse(); 
						$Query = pg_query($link,"UPDATE curso SET imagen='$archi2' WHERE $sql id='$id'");
						//echo " <p class=texto2 align=center> &nbsp; &nbsp; &nbsp; Reduccion perfecta. <br> <img SRC=images/ok.png ALT > </p>";
					}else{
						$est="ko";
						$_SESSION[error]="No se ha guardado correctamente al reducir imagen.";	
						//echo " <p class=texto2> &nbsp; &nbsp; &nbsp; ERROR en Reduccion de imagen. Contacte con ADMIN. </p>";
					}
			}else{
				$_SESSION[error]=" Ocurrió alg&uacute;n error al subir el fichero. No pudo guardarse.";
				$est="ko";
			} 					
		}
	}
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	//echo $_SESSION[error];
	exit();
	
}

$accionfoto=strip_tags($_GET['accionfoto']);
$accionpdf=strip_tags($_GET['accionpdf']);
if($accionfoto=='borrar'){
	$archivo=strip_tags($_GET['archivo']); 			//optativos pero obligatorio para eliminar archivos
	$directorio=strip_tags($_GET['directorio']);	//optativos pero obligatorio para eliminar archivos
	//$c_directorio='/home/.../public_html';//optativos pero obligatorio para eliminar archivos. Este suele estar en configuracion
	$_SESSION[error]="No se ha eliminado la imagen";
	$est="ko";
	if (($archivo<>"")){ // Parametro optativo
		if ($directorio<>""){ // Parametro optativo
			//Eliminar archivo
			if ($archivo<>"") {
				//$path1=$c_directorio."/".$directorio."/".$archivo; //Ruta
				//chmod($path1,0777);		//Permisos
				//if (!unlink($path1)){  //Elimina
					//echo ("<div class=error>Error, archivo no existe, no eliminado:</div> ".$archivo);  //Si error informa
				//}else{
					$_SESSION[error]="Se ha eliminado la imagen correctamente";
					$est="ok";
				// Ahora borramos la pequeña:
				/*$archivop = str_replace("p.jpg", ".jpg",$archivo);
				$path2=$c_directorio."/".$directorio."/".$archivop; //Ruta
				chmod($path2,0777);		//Permisos
				if (!unlink($path2)){  //Elimina
					die("Error, archivo no eliminado: ".$archivop);  //Si error informa*/
				//}
				$link=iConectarse();
				$Query = pg_query($link,"UPDATE curso SET imagen='' WHERE $sql id='$id';");
			}
		}
		//Eliminar de BD
	}		
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	//echo $_SESSION[error];
	exit();
}
if($accionfoto=='borrar2'){
	$idfoto=strip_tags($_GET['idfoto']);
	$archivo=strip_tags($_GET['archivo']); 			//optativos pero obligatorio para eliminar archivos
	$directorio=strip_tags($_GET['directorio']);	//optativos pero obligatorio para eliminar archivos
	$link=iConectarse(); 
	pg_query($link,"delete FROM foto where $sql id = '$idfoto'") ;//or die (mysql_error()); 
	if (($archivo<>"")){ // Parametro optativo
		if ($directorio<>""){ // Parametro optativo
			//Eliminar archivo
			if ($archivo<>"") {
				$path1=$c_directorio_img."/".$directorio."/".$archivo; //Ruta
				chmod($path1,0777);		//Permisos
				if (!unlink($path1)){  //Elimina
					$_SESSION[error]="Error, archivo no existe, no eliminado ";  //Si error informa
					$est="ko";
				}else{
					$_SESSION[error]="Se ha eliminado la imagen correctamente.";
					$est="ok";
				}
			}
		}
		//Eliminar de BD
	}		
}
if($accionfoto=='borrar3'){
	$link=iConectarse();
	$Query = pg_query($link,"UPDATE curso SET imagen2='' WHERE id='$id';");
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	exit();
}
if($accionfoto=='inserta'){
	$foto= $_FILES['userfile']['name']; 
	$tipo_archivo = $_FILES['userfile']['type'];
	//$informacion = str_replace("\n", "<BR>",$informacion);
	if (!isset($foto)){
		$_SESSION[error]="<div class=wrap-col><div class=ok> No hay datos a guardar. </div></div>";
		$est="ko";
	}else{	
		$nombre_archivo = $_FILES['userfile']['name']; 
		$tipo_archivo = $_FILES['userfile']['type']; 
		$tamano_archivo = $_FILES['userfile']['size'];
		$comentario=strip_tags($_POST['comentario']); 
		//compruebo si las caractersticas del archivo son las que deseo 
		if (!( (strpos($tipo_archivo, "bmp"))||(strpos($tipo_archivo, "tif"))||(strpos($tipo_archivo, "png"))||(strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) && ($tamano_archivo < 5200000000)) {  
			$_SESSION[error]="Error en tipo o tamaño de archivo:-".$tipo_archivo."- ".$tamano_archivo;
			$est="ko";
		}else{
			switch( $tipo_archivo ) 
			{ 
			  case "image/png": $extension="png"; break; 
			  case "image/JPG": 
			  case "image/JPEG": 
			  case "image/jpg": 
			  case "image/jpeg": $extension="jpg"; break; 
			  case "image/bmp": $extension="bmp"; break; 
			  case "image/tiff": $extension="tif"; break; 
			  
			} 
			if ($extension==""){
				if (( (strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) ) {  
					$extension="jpg";
				}else{
					$_SESSION[error]="Error en tipo o tamaño de archivo:".$tipo_archivo." - ".$tamano_archivo;
					$est="ko";
				}
			}
			$archiv=sanear_string($nombre_archivo)."_";
			$archivv=$archiv.=time();
			$archivv=$archivv.".".$extension;
			$destino = $c_directorio_img."/imagen/".$archivv;	
			//$destino = $c_directorio."/files/".$archivv;	
			 if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$destino)){	
					if ($comentario=="") $comentario="imagen".rand(0,999);
					$link=iConectarse(); 
					$Query = pg_query($link,"INSERT INTO foto (foto,comentario,padre,tipo,idcolegio) VALUES ('$archivv','$comentario','$id','curso','$idcolegio')" ) ;//or die (mysql_error()); 
					$_SESSION[error]="Se ha guardado correctamente.";	
					$est="ok";
					//echo "INSERT INTO foto (foto,comentario,padre,tipo,idcolegio) VALUES ('$archivv','$comentario','$id','curso','$idcolegio')" ;
					//exit();
					/*echo " <p class=ok>  Comenzamos a disminuir la imagen... </p>";
					include("_resize.php");
					if (resizeImg("imagen/".$archivv,"imagen/".$archi2,250,250)){
						echo " <p class=texto2 align=center> &nbsp; &nbsp; &nbsp; Reduccion perfecta. <br> <img SRC=images/ok.png ALT > </p>";
					}else{
						echo " <p class=texto2> &nbsp; &nbsp; &nbsp; ERROR en Reduccion de imagen. Contacte con ADMIN. </p>";
					}*/
			}else{
				$_SESSION[error]=" No pudo subir el fichero.";
				$est="ko";
			} 					
		}
	}
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	//echo $_SESSION[error];
	exit();
} // FIN $accionfoto==inserta
if($accionpdf=='inserta'){
	$nombre=strip_tags($_POST['nombre']); 
	if ($nombre=="") $nombre="pdf".rand(0,999);
	$nombre_archivo = $_FILES['userfile']['name']; 
	$tipo_archivo = $_FILES['userfile']['type'];
	$tipo_archivo2 = finfo_file($finfo, $_FILES['userfile']['tmp_name']); 
	$tamano_archivo = $_FILES['userfile']['size'];
	
	if (isset($_REQUEST['programa'])){
		$programa = 1;
	}
	else{
		$programa = 0;
	}
	
	//compruebo si las caractersticas del archivo son las que deseo 
	if (!(((strpos($tipo_archivo, "pdf") )||  (strpos($tipo_archivo2, "pdf"))) && ($tamano_archivo < 2000000000))) {     
			$_SESSION[error]="La extensión o el tamaño del archivo no es correcta. (pdf)";
			$est="ko"; 
	}else{
		$archiv=sanear_string($nombre_archivo)."_";
		$archivv=$archiv.=time();
		$archivv=$archivv.".pdf";
		$destino = $c_directorio_img."/files/".$archivv;	
		 if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$destino)){
			$link=iConectarse(); 
			//echo "INSERT INTO archivo (archivo,nombre,padre,idcolegio) VALUES ('$archivv','$nombre','$id','$idcolegio')";
			//exit();
			$Query = pg_query($link,"INSERT INTO archivo (archivo,nombre,padre,idcolegio,programa) VALUES ('$archivv','$nombre','$id','$idcolegio','$programa')" );// or die (mysql_error()); 
			
			
			$_SESSION[error]="Guardado.";
			$est="ok";
		}else{
			$_SESSION[error]="PDF erróneo.";
			$est="ko";
		} 
	}
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	//echo $_SESSION[error];
	exit();
}	
if($accionpdf=='borrar'){
	$archivo=strip_tags($_GET['archivo']); 			//optativos pero obligatorio para eliminar archivos
	$directorio=strip_tags($_GET['directorio']);	//optativos pero obligatorio para eliminar archivos
	$idpdf=strip_tags($_GET['idpdf']);
	$link=iConectarse(); 
	pg_query($link,"delete FROM archivo where $sql id = $idpdf");// or die ("Error:borrarpdf_".mysql_error()); 
	if (($archivo<>"")){ // Parametro optativo
		if ($directorio<>""){ // Parametro optativo
			//Eliminar archivo
			if ($archivo<>"") {
				$path1=$c_directorio_img."/".$directorio."/".$archivo; //Ruta
				chmod($path1,0777);		//Permisos
				if (!unlink($path1)){  //Elimina
					$_SESSION[error]="El archivo no existe, no eliminado";  //Si error informa
					$est="ko";
				}else{
					$_SESSION[error]="Se ha eliminado el PDF correctamente.";
					$est="ok";
				}
			}
		}
		//Eliminar de BD
	}		
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	//echo $_SESSION[error];
	exit();
}
$accionvideo=strip_tags($_GET['accionvideo']);

if($accionvideo=='inserta'){
	$nombre=strip_tags($_POST['nombre']); 
	$codigo=$_POST['codigo']; 
	$link=iConectarse(); 
	$Query = pg_query($link,"INSERT INTO video (codigo,nombre,padre,idcolegio) VALUES ('$codigo','$nombre','$id','$idcolegio')"); 
	if ($Query){
		$_SESSION[error]="Video guardado.";
		$est="ok";
	}else{
		$_SESSION[error]="No se ha guardado el Video.</p>";
		$est="ko";
	} 
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	exit();
}	
if($accionvideo=='borrar'){
	$idvideo=$_GET['idvideo'];
	$link=iConectarse(); 
	$Query = pg_query($link,"UPDATE video SET borrado=1 where $sql id='$idvideo'");// or die (mysql_error());  	
	if ($Query){
		$_SESSION[error]="Video eliminado.";
		$est="ok";
	}else{
		$_SESSION[error]="No se ha eliminado el Video.</p>";
		$est="ko";
	} 
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	exit();
}


$videoprincipal=strip_tags($_GET['videoprincipal']);
if($videoprincipal=='insertar'){
	$codigo=$_POST['codigo']; 
	$link=iConectarse(); 
	$Query = pg_query($link,"UPDATE curso SET video='$codigo' where $sql id='$id'");// or die (mysql_error());  	
	if ($Query){
		$_SESSION[error]="Video guardado.";
		$est="ok";
	}else{
		$_SESSION[error]="No se ha guardado el Video.</p>";
		$est="ko";
	} 
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	exit();
}
if($videoprincipal=='borrar'){
	$link=iConectarse(); 
	$Query = pg_query($link,"UPDATE curso SET video='' where $sql id='$id'");// or die (mysql_error());  	
	if ($Query){
		$_SESSION[error]="Video eliminado.";
		$est="ok";
	}else{
		$_SESSION[error]="No se ha eliminado el Video.</p>";
		$est="ko";
	} 
	header("Location: curso_alta_mas.php?id=$id&est=$est"); 
	exit();
}

//Repito esto para tenerlo cerca
$link=iConectarse(); 
$result=pg_query($link,"SELECT * FROM curso WHERE $ssql id='$id' AND borrado=0;");// or die (pg_error());
if ($result) {
	$row = pg_fetch_array($result);
	$nombre=$row["nombre"];
	$imagen=$row["imagen"];
	$video=$row["video"];
	//exit();
}else{
	echo "Error";
	exit();
}

$migas = array();
$migas[] = array('zona-privada_admin_cursos_1.php', 'Gestión de Cursos');
$titulo1="curso";
$titulo2="gestión";
include("plantillaweb01admin.php");
?>
<script language="javascript">
		function confirmar ( mensaje ) {
			return confirm( mensaje );
			}
</script>
<!--Arriba -->
<div class="grid-9 contenido-principal">
<div class="clearfix"></div>
<div class="pagina blog">
	<h2 class="titulonoticia">Multimedia curso</h2>
	<br />
	<div class="bloque-lateral acciones">		
		<p><strong>Acciones:</strong>
			<a href="curso_alta.php?accion=editar&id=<?=$id?>" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> 
		</p>
	</div>
	<!--fin acciones-->
	<? include("_aya_mensaje_session.php"); ?>
	<br />
	<div class="clearfix"></div>
			
			
		<h3>Foto portada(imagen antes de entrar al curso)</h3>
		<?
		$idpadre=$id;
		$link2=conectar(); //Postgrepsql
		$result2=pg_query($link2,"SELECT imagen FROM curso WHERE id='$id' AND borrado=0;");// or die (mysql_error());  
		if ($result2){
			while($row2= pg_fetch_array($result2)) {							
						if ($row2["imagen"]<>""){?>
								<span class="actions">
									<a href="imagen/<?=$row2["imagen"]?>" rel="shadowbox[galery]" ><img SRC="imagen/<?=$row2["imagen"]?>" ALT width=60><i class="icon-zoom-in"></i> Ver</a> 
									<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_alta_mas.php?accionfoto=borrar&accion=<?=$accion?>&tipo=<?=$tipo?>&id=<?=$row["id"]?>&archivo=<?=$imagen?>&directorio=imagen"></i> Eliminar</a>
								</span>
						<? }?>					
				<? 
			}?>
		<? 
		}?>
		
		<br/><br/><a href="admin_contenido3_crop.php?tipo=portadacurso&id=<?=$id?>"  class="btn btn-primary"> Subir/Cambiar imagen</a> 
		

		<h3><br>Foto principal(dentro del curso)</h3>
		
		<?
		$idpadre=$id;
		$link2=conectar(); //Postgrepsql
		$result2=pg_query($link2,"SELECT imagen2 FROM curso WHERE id='$id' AND borrado=0;");// or die (mysql_error());  
		if ($result2){
			while($row2= pg_fetch_array($result2)) {								
						if ($row2["imagen2"]<>""){?>
								<span class="actions">
									<a href="imagen/<?=$row2["imagen2"]?>" rel="shadowbox[galery]" ><img SRC="imagen/<?=$row2["imagen2"]?>" ALT width=60><i class="icon-zoom-in"></i> Ver</a>
									<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_alta_mas.php?accionfoto=borrar3&accion=<?=$accion?>&tipo=<?=$tipo?>&id=<?=$row["id"]?>&archivo=<?=$imagen?>&directorio=imagen"><i class="icon-trash"></i> Eliminar</a>
								</span>
						<? }?>					
				<? 
			}?>
		<? 
		}?>
		
		<br/><br/><a href="admin_contenido3_crop.php?tipo=fichacurso&id=<?=$id?>"  class="btn btn-primary"> Subir/Cambiar imagen</a> 
	
			
			
			
		
		<? /*
		
			<h3>Imagen:</h3>
			<FORM METHOD="post" ACTION="curso_alta_mas.php?accion=guardarm&id=<?=$id?>&videoprincipal=insertar" enctype="multipart/form-data">
				<div class="grid-12 colegiado-pics">
					<p class="colegiado-foto">
					<?
					$destino =$c_directorio_img."/imagen/".$imagen;
					if(file_exists($destino)&&($imagen<>"")){ //comprobamos que existe la foto
						?>
						<img src="imagen/<?=$imagen?>"  alt="<?=$nombre?>" title="<?=$nombre?>" />
						<br />
						<a class="btn btn-primary" onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_alta_mas.php?accionfoto=borrar&accion=<?=$accion?>&tipo=<?=$tipo?>&id=<?=$row["id"]?>&archivo=<?=$imagen?>&directorio=imagen">Eliminar Imagen</a>
						<?
					}else{
						?>
						No dispone de imagen
						<input name="userfile" type="file" class="input-small"  />
						<br />
						<input type="submit" value="Guardar imagen" class="btn btn-primary">
						<?
					}?>
					</p>
				</div>
			</FORM>
		</div>
		
		*/ ?>
		<h3><br>Vídeo principal(sustituye imagen principal):</h3>
			 <?php
			if ($video<>""){
				?><ul>
						<li>
							<span class="actions">
								<div class="videocontainer">
									<?=$video?>											
								</div>
							<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_alta_mas.php?videoprincipal=borrar&accion=<?=$accion?>&tipo=<?=$tipo?>&id=<?=$padre?>"><i class="icon-trash"></i> Eliminar Video</a>
							</span>
						</li>									
				</ul>
				<? 
			}?>
				<form class="titulo" action="curso_alta_mas.php?videoprincipal=insertar&accion=<?=$accion?>&id=<?=$id?>&tipo=<?=$tipo?>" method="post" enctype="multipart/form-data">     
				<div>Código: (Recomendado width="636" height="400") (&autoplay=1)</div>
				<div><textarea name="codigo" cols="30" class="input-xxlarge" ><? if ($video<>""){ echo $video ; }?></textarea></div>
				<div><input type="submit" value="<? if ($video<>""){ echo "Modificar" ; }else{ echo"Insertar"; }?> Vídeo" class="btn btn-primary"> </div>
				<div><a href="http://support.google.com/youtube/bin/answer.py?hl=es&answer=171780" target="_blank">¿Cómo insertar un video?</a></div>
				</form>
				<div>
					Opciones:<br />
						&nbsp;&nbsp;&nbsp;Autoplay en la inserción del video: Simplemente sumándole al código el 'autoplay=1'<br />
						&nbsp;&nbsp;&nbsp;Si quieres que se inicie en un momento distinto al de inicio inserta 'start=100' (el tiempo en segundos)<br />
						&nbsp;&nbsp;&nbsp;Puedes hacer que desaparezcan los controles de la parte de abajo con el código 'controls=1'<br />
						&nbsp;&nbsp;&nbsp;Para evitar los molestos videos relacionados al final del video debes de poner 'rel=0'<br />
						&nbsp;&nbsp;&nbsp;Para evitar que aparezca la información del video en la parte superior del reproductor 'showinfo=0'<br />
				</div>						
	
	<div class="clearfix"></div>
	<h3><br>Programa del curso PDF:</h3>
	<?
	$padre=$row["id"];
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT id,archivo,nombre FROM archivo WHERE $sql padre=$padre AND borrado=0 AND programa=1;");// or die (mysql_error());  
	if ($result2){
		if ($row2= pg_fetch_array($result2)) {								
			?><ul><? 
				if ($row2["archivo"]<>""){?>
					<li>
						<span class="actions">
						<?=$row2["nombre"]?> 
						<a href="descarga.php?documento=<?=$row2["archivo"]?>" > <i class="icon-zoom-in"></i> Ver</a> &middot; 
						<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_alta_mas.php?programa&accionpdf=borrar&accion=<?=$accion?>&tipo=<?=$tipo?>&idpdf=<?=$row2["id"]?>&id=<?=$idpadre?>&archivo=<?=$row2["archivo"]?>&directorio=files"><i class="icon-trash"></i> Borrar</a>
						</span>
						</li>									
				<? } ?>

			</ul>
			<? 
		}
		else { ?>
			<form class="titulo" action="curso_alta_mas.php?programa&accionpdf=inserta&accion=modificar&id=<?=$id?>&tipo=<?=$tipo?>&pdf=<?=$pdf?>" method="post" enctype="multipart/form-data">     
				<div><input name="userfile" type="file" /></div>
				<div><input type="submit" value="Insertar programa" class="btn btn-primary"> </div>
			</form>		
	<? } 
	} 
	?>
	<br>
	
	
	
	<br>
	<h3>Documentos adjuntos PDF:</h3>
	<?
	$padre=$row["id"];
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT id,archivo,nombre FROM archivo WHERE $sql padre=$padre AND borrado=0 AND (programa!=1 OR programa IS NULL);");// or die (mysql_error());  
	if ($result2){
		while($row2= pg_fetch_array($result2)) {								
			?><ul><? 
				if ($row2["archivo"]<>""){?>
					<li>
						<span class="actions">
						<?=$row2["nombre"]?> 
						<a href="descarga.php?documento=<?=$row2["archivo"]?>" > <i class="icon-zoom-in"></i> Ver</a> &middot; 
						<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_alta_mas.php?accionpdf=borrar&accion=<?=$accion?>&tipo=<?=$tipo?>&idpdf=<?=$row2["id"]?>&id=<?=$idpadre?>&archivo=<?=$row2["archivo"]?>&directorio=files"><i class="icon-trash"></i> Borrar</a>
						</span>
						</li>									
				<? }?>
			</ul>
			<? 
		}
	}?>
	<form class="titulo" action="curso_alta_mas.php?accionpdf=inserta&accion=modificar&id=<?=$id?>&tipo=<?=$tipo?>&pdf=<?=$pdf?>" method="post" enctype="multipart/form-data">     
		Nuevo Archivo PDF
		<div><input name="userfile" type="file" /></div>
		<div>Descripción:</div>
		<div><input type="text" name="nombre" /></div>
		<div><input type="submit" value="Insertar PDF" class="btn btn-primary"> </div>
	</form>		
					<h3>Imágenes Secundarias:</h3>
	<?
	$idpadre=$id;
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT id,foto FROM foto WHERE $sql padre='$idpadre' AND borrado=0;");// or die (mysql_error());  
	if ($result2){
		while($row2= pg_fetch_array($result2)) {								
			if ($row2["foto"]<>""){?>
				<li>
					<span class="actions">
						<a href="imagen/<?=str_replace("p.jpg", ".jpg",$row2["foto"])?>" rel="shadowbox[galery]" ><img SRC="imagen/<?=$row2["foto"]?>" ALT width=60><i class="icon-zoom-in"></i> Ver</a> &middot; 
						<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_alta_mas.php?accionfoto=borrar2&accion=<?=$accion?>&tipo=<?=$tipo?>&idfoto=<?=$row2["id"]?>&id=<?=$idpadre?>&archivo=<?=str_replace("p.jpg", ".jpg",$row2["foto"])?>&directorio=imagen"><i class="icon-trash"></i> Borrar</a>
					</span>
				</li>
			<? }?>
			<? 
		}?>
	<? 
	}?>
	<FORM METHOD="post" ACTION="curso_alta_mas.php?accionfoto=inserta&accion=modificar&id=<?=$id?>&tipo=<?=$tipo?>" enctype="multipart/form-data">
		Nueva Imagen Secundaria
		<div><input name="userfile" type="file" /></div>
		<div>Descripción:</div>
		<div><input type="text" name="comentario" /></div>
		<div>Permite imagen  JPG,PNG,BMP y tamaño menor de 2MB. </div>
		<div><input type="submit" value="Insertar imagen" class="btn btn-primary">  </div>
	</FORM>		
	
						
					<h3>Vídeos Secundarios:</h3>
						 <?php
						$link2=iConectarse(); 
						$result2=pg_query($link2,"SELECT id,nombre,codigo FROM video WHERE $sql padre='$padre' AND borrado=0;");// or die (mysql_error());  
						if ($result2){
							while($row2= pg_fetch_array($result2)) {								
								?><ul><? 
									if ($row2["id"]<>""){?>
										<li>
											<span class="actions">
											<?=$row2["nombre"]?> 
												<div style="width:420px; height:315px"  class="videocontainer">
													<?=$row2["codigo"]?>											
												</div>
											<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_alta_mas.php?accionvideo=borrar&tipo=<?=$tipo?>&idvideo=<?=$row2["id"]?>&id=<?=$padre?>"><i class="icon-trash"></i> Eliminar Video</a>
											</span>
											</li>									
									<? }?>
								</ul>
								<? 
							}
						}?>
							<form class="titulo" action="curso_alta_mas.php?accionvideo=inserta&accion=modificar&id=<?=$id?>&tipo=<?=$tipo?>" method="post" enctype="multipart/form-data">     
							<div>Nombre:</div>
							<div><input class="form" type="text" name="nombre" size=30 /></div>
							<div>Código:</div>
							<div><textarea name="codigo" cols="30" class="form"></textarea></div>
							<div><input type="submit" value="Insertar Vídeo" class="btn btn-primary"> </div>
							<div><a href="http://support.google.com/youtube/bin/answer.py?hl=es&answer=171780" target="_blank">¿Cómo insertar un video?</a></div>
							</form>								
	
	
	
	<div class="bloque-lateral acciones">		
		<p><strong>Acciones:</strong>
			<a href="curso_alta.php?accion=editar&id=<?=$id?>" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> 
		</p>
	</div>
							
 </div>
<!--fin pagina blog-->
<div class="clearfix"></div>
</div>
<!--fin grid-8 contenido-principal-->
<? 
include("plantillaweb02admin.php");
?>