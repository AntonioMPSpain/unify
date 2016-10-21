<?
$id=strip_tags($_GET['id']);
if ($id==''){
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
 
include("_seguridadfiltro.php"); 
include("_funciones.php"); 
include("_cone.php"); 
$c_directorio = '/var/www/web';

$accion=strip_tags($_GET['accion']); 
//	ACCIONES
$accionfoto=$_GET['accionfoto'];
 
if($accionfoto=='borrar2'){
	$idfoto=$_GET['idfoto'];
	$archivo=$_GET['archivo']; 			//optativos pero obligatorio para eliminar archivos
	$directorio=$_GET['directorio'];	//optativos pero obligatorio para eliminar archivos
	$link=conectar(); //Postgresql
	$result=pg_query($link,"DELETE FROM foto where id ='$idfoto'");// or die (mysql_error()); 
	$_SESSION[esterror]="No se ha podido eliminar, datos incorrectos.";
	if (($archivo<>"")){ // Parametro optativo
		if ($directorio<>""){ // Parametro optativo
			//Eliminar archivo
			if ($archivo<>"") {
				$path1=$c_directorio."/".$directorio."/".$archivo; //Ruta
				chmod($path1,0777);		//Permisos
				if (!unlink($path1)){  //Elimina
						$_SESSION[esterror]="El archivo no existe: ".$archivo;  //Si error informa
				}else{
						$_SESSION[esterror]="Se ha eliminado la imagen correctamente.";
				}
			}
		}
		//Eliminar de BD
	}		
	header("Location: admin_contenido3_imagen.php?id=$id");
	exit();
}
if($accionfoto=='eli1'){
	$link=conectar(); //Postgresql
	$sql="UPDATE generica SET img1='' WHERE  $sql id= '$id'";
	$archivo=$_GET['archivo']; 			//optativos pero obligatorio para eliminar archivos
	$result=pg_query($link,$sql);// or die (mysql_error()); 
	$_SESSION[esterror]="No se ha podido eliminar, datos incorrectos.";
	$directorio='imagen';
	if (($archivo<>"")){ // Parametro optativo
		if ($directorio<>""){ // Parametro optativo
			//Eliminar archivo
			if ($archivo<>"") {
				$path1=$c_directorio."/".$directorio."/".$archivo; //Ruta
				chmod($path1,0777);		//Permisos
				if (!unlink($path1)){  //Elimina
						$_SESSION[esterror]="El archivo no existe: ".$archivo;  //Si error informa
				}else{
						$_SESSION[esterror]="Se ha eliminado la imagen correctamente.";
				}
			}
		}
		//Eliminar de BD
	}		
	header("Location: admin_contenido3_imagen.php?id=$id");
	exit();
}
if($accionfoto=='eli3'){
	$link=conectar(); //Postgresql
	$sql="UPDATE generica SET img3='' WHERE  $sql id= '$id'";
	$archivo=$_GET['archivo']; 			//optativos pero obligatorio para eliminar archivos
	$result=pg_query($link,$sql);// or die (mysql_error()); 
	$_SESSION[esterror]="No se ha podido eliminar, datos incorrectos.";
	$directorio='imagen';
	if (($archivo<>"")){ // Parametro optativo
		if ($directorio<>""){ // Parametro optativo
			//Eliminar archivo
			if ($archivo<>"") {
				$path1=$c_directorio."/".$directorio."/".$archivo; //Ruta
				chmod($path1,0777);		//Permisos
				if (!unlink($path1)){  //Elimina
						$_SESSION[esterror]="El archivo no existe: ".$archivo;  //Si error informa
				}else{
						$_SESSION[esterror]="Se ha eliminado la imagen correctamente.";
				}
			}
		}
		//Eliminar de BD
	}		
	header("Location: admin_contenido3_imagen.php?id=$id");
	exit();
}

if($accionfoto=='eli2'){
	$link=conectar(); //Postgresql
	$sql="UPDATE generica SET img2='' WHERE  $sql id= '$id'";
	$archivo=$_GET['archivo']; 			//optativos pero obligatorio para eliminar archivos
	$result=pg_query($link,$sql);// or die (mysql_error()); 
	$_SESSION[esterror]="No se ha podido eliminar, datos incorrectos.";
	$directorio='imagen';
	if (($archivo<>"")){ // Parametro optativo
		if ($directorio<>""){ // Parametro optativo
			//Eliminar archivo
			if ($archivo<>"") {
				$path1=$c_directorio."/".$directorio."/".$archivo; //Ruta
				chmod($path1,0777);		//Permisos
				if (!unlink($path1)){  //Elimina
						$_SESSION[esterror]="El archivo no existe: ".$archivo;  //Si error informa
				}else{
						$_SESSION[esterror]="Se ha eliminado la imagen correctamente.";
				}
			}
		}
		//Eliminar de BD
	}		
	header("Location: admin_contenido3_imagen.php?id=$id");
	exit();
}

if($accionfoto=='inserta'){
	$foto= $_FILES['userfile']['name']; 
	$tipo_archivo = $_FILES['userfile']['type'];
	$_SESSION[esterror]="No se ha podido insertar, datos incorrectos.";
	if (!isset($foto) || (!strpos($tipo_archivo, "p"))){
		$_SESSION[esterror]="Informe: No imagen.";
	}else{	
		$comentario=strip_tags(trim($_POST['comentario'])); 
		if ($comentario==""){
			$comentario=$foto;
		}
		$nombre_archivo = $_FILES['userfile']['name']; 
		$tipo_archivo = $_FILES['userfile']['type']; 
		$tamano_archivo = $_FILES['userfile']['size'];
		//compruebo si las caractersticas del archivo son las que deseo 
		if (!((strpos($tipo_archivo, "jpg") || strpos($tipo_archivo, "jpeg") ) && ($tamano_archivo < 200000000))) {     
			$_SESSION[esterror]="La extensión o el tamaño de los archivos no es correcta. (Max: 2000 Kb) (jpg)"; 
		}else{
			$archiv="pu";
			$archivv=$archiv.=time();
			$archi2=$archivv."p.jpg";
			$archivv=$archivv.".jpg";
			$destino = $c_directorio."/imagen/".$archivv;	
			
			 if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$destino)){
			 	$sql="INSERT INTO foto (foto,comentario,padre,tipo,idcolegio) VALUES ('$archivv','$comentario','$id','$tipo','$idcolegio')";
				$link=conectar(); //Postgresql
				$result=pg_query($link,$sql );// or die (mysql_error()); 
					/*include("_resize.php");
					if (resizeImg("imagen/".$archivv,"imagen/".$archi2,250,250)){
						echo " <p class=texto2 align=center> &nbsp; &nbsp; &nbsp; Imagen guardada y reducida. <br> <img SRC=images/ok.png ALT  width=20> </p>";
					}else{
						echo " <p class=texto2 align=center> &nbsp; &nbsp; &nbsp; ERROR en Reduccion de imagen. Contacte con ADMIN. </p>";
					}*/
				$_SESSION[esterror]="Imagen guardada correctamente";
			}else{
				$_SESSION[esterror]="Ocurrió algún error al subir el fichero. No pudo guardarse.";
			} 					
		}
	}
	header("Location: admin_contenido3_imagen.php?id=$id");
	exit();
} // FIN $accionfoto==inserta






$videoprincipal=strip_tags($_GET['videoprincipal']);
if($videoprincipal=='insertar'){
	$codigo=$_POST['codigo']; 
	$link=iConectarse(); 
	$Query = pg_query($link,"UPDATE generica SET video='$codigo' where id='$id'");// or die (mysql_error()); 

 	
	if ($Query){
		$_SESSION[esterror]="Video guardado.";
		$est="ok";
	}else{
		$_SESSION[esterror]="No se ha guardado el Video.</p>";
		$est="ko";
	} 
	header("Location: admin_contenido3_imagen.php?id=$id&tipo=publicacion"); 
	exit();
}
if($videoprincipal=='borrar'){
	$link=iConectarse(); 
	$Query = pg_query($link,"UPDATE generica SET video='' where id='$id'");// or die (mysql_error());  	
	if ($Query){
		$_SESSION[esterror]="Video eliminado.";
		$est="ok";
	}else{
		$_SESSION[esterror]="No se ha eliminado el Video.</p>";
		$est="ko";
	} 
	header("Location: admin_contenido3_imagen.php?id=$id&tipo=publicacion"); 
	exit();
}














$idimg=$_GET['idimg'];
if(($accionfoto=='ins')&&(($idimg==1)||($idimg==2)||($idimg==3))){
	$foto= $_FILES['userfile']['name']; 
	$tipo_archivo = $_FILES['userfile']['type'];
	$_SESSION[esterror]="No se ha podido insertar, datos incorrectos.";
	if (!isset($foto) || (!strpos($tipo_archivo, "p"))){
		$_SESSION[esterror]="Informe: No imagen.";
	}else{	
		$comentario=strip_tags(trim($_POST['comentario'])); 
		if ($comentario==""){
			$comentario=$foto;
		}
		$nombre_archivo = $_FILES['userfile']['name']; 
		$tipo_archivo = $_FILES['userfile']['type']; 
		$tamano_archivo = $_FILES['userfile']['size'];
		//compruebo si las caractersticas del archivo son las que deseo 
		if (!((strpos($tipo_archivo, "jpg") || strpos($tipo_archivo, "jpeg") ) && ($tamano_archivo < 200000000))) {     
			$_SESSION[esterror]="La extensión o el tamaño de los archivos no es correcta. (Max: 2000 Kb) (jpg)"; 
		}else{
			$archiv="pu";
			$archivv=$archiv.=time();
			$archi2=$archivv."p.jpg";
			$archivv=$archivv.".jpg";
			$destino = $c_directorio."/imagen/".$archivv;	
			
			 if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$destino)){
			 	$sql="UPDATE generica SET img$idimg='$archivv' WHERE  $sql id= '$id'";			 
				$link=conectar(); //Postgresql
				$result=pg_query($link,$sql );// or die (mysql_error()); 
					/*include("_resize.php");
					if (resizeImg("imagen/".$archivv,"imagen/".$archi2,250,250)){
						echo " <p class=texto2 align=center> &nbsp; &nbsp; &nbsp; Imagen guardada y reducida. <br> <img SRC=images/ok.png ALT  width=20> </p>";
					}else{
						echo " <p class=texto2 align=center> &nbsp; &nbsp; &nbsp; ERROR en Reduccion de imagen. Contacte con ADMIN. </p>";
					}*/
				$_SESSION[esterror]="Imagen guardada correctamente";
			}else{
				$_SESSION[esterror]="Ocurrió algún error al subir el fichero. No pudo guardarse.";
			} 					
		}
	}
	header("Location: admin_contenido3_imagen.php?id=$id");
	exit();
} // FIN $accionfoto==inserta1


$safe="Gestion de publicaciones";
$tipo="publicacion";
$titulo1="publicaciones ";
$titulo2="administracion";

include("plantillaweb01admin.php"); 
?>	<script language="javascript">
		function confirmar ( mensaje ) {
			return confirm( mensaje );
			}
	</script>
 	<script  type="text/javascript" src="../web/ckeditor/ckeditor.js"></script>
	<h2>Imágenes de publicación</h2>
	<div class="bloque-lateral acciones">		
				<p>
					<a href="admin_contenido.php" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a>
				</p>
	</div>
	<!--fin acciones-->
	<?
	include("_aya_mensaje_session.php");
	?>
	<div class="grid-12">
	<div class="grid-12">
		<br>
		<h3>[obligatoria] Foto portada(imagen antes de entrar a publicación)</h3>
		<?
		$idpadre=$id;
		$link2=conectar(); //Postgrepsql
		$result2=pg_query($link2,"SELECT img2 FROM generica WHERE $sql id='$id' AND borrado=0;");// or die (mysql_error());  
		if ($result2){
			while($row2= pg_fetch_array($result2)) {							
						if ($row2["img2"]<>""){?>
								<span class="actions">
									<a href="imagen/<?=$row2["img2"]?>" rel="shadowbox[galery]" ><img SRC="imagen/<?=$row2["img2"]?>" ALT width=60><i class="icon-zoom-in"></i> Ver</a> 
									<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_contenido3_imagen.php?accionfoto=eli2&accion=<?=$accion?>&tipo=<?=$tipo?>&id=<?=$idpadre?>&archivo=<?=$row2["img2"]?>"><i class="icon-trash"></i> Eliminar</a>
								</span>
						<? }?>					
				<? 
			}?>
		<? 
		}?>
		
		<br/><br/><a href="admin_contenido3_crop.php?tipo=portadapublicacion&id=<?=$id?>"  class="btn btn-primary"> Subir/Cambiar imagen</a> 
		
		<? /* 
		
				<br/><br/><a href="imagen/cropimagen/index.php?id=<?=$id?>&tipo=portadapublicacion"  class="btn btn-primary"> Subir/Cambiar imagen </a> 

		
		<FORM METHOD="post" ACTION="admin_contenido3_imagen.php?accionfoto=ins&idimg=2&id=<?=$id?>&tipo=<?=$tipo?>" enctype="multipart/form-data">
		<fieldset>				    
			<legend>Cambiar Pequeña</legend>
			<div class="control-group">
				<label class="control-label" for="inputName">Seleccione imagen:</label>
					<div class="controls">
						<input name="userfile" type="file" />
					</div>
			</div>
			<div>Imagen  JPG y tamaño menor de 2MB. </div>
		</fieldset>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary btn-large">Guardar</button>
		</div>
		</form>--> 
		*/
		?>
	</div>
	<div class="grid-12">
	<br>
		<h3>[obligatoria] Foto principal(dentro de la publicación)</h3>
		
		<?
		$idpadre=$id;
		$link2=conectar(); //Postgrepsql
		$result2=pg_query($link2,"SELECT img1 FROM generica WHERE $sql id='$id' AND borrado=0;");// or die (mysql_error());  
		if ($result2){
			while($row2= pg_fetch_array($result2)) {								
						if ($row2["img1"]<>""){?>
								<span class="actions">
									<a href="imagen/<?=$row2["img1"]?>" rel="shadowbox[galery]" ><img SRC="imagen/<?=$row2["img1"]?>" ALT width=60><i class="icon-zoom-in"></i> Ver</a>
									<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_contenido3_imagen.php?accionfoto=eli1&accion=<?=$accion?>&tipo=<?=$tipo?>&id=<?=$idpadre?>&archivo=<?=$row2["img1"]?>"><i class="icon-trash"></i> Eliminar</a>
								</span>
						<? }?>					
				<? 
			}?>
		<? 
		}?>
		
		<br/><br/><a href="admin_contenido3_crop.php?tipo=fichapublicacion&id=<?=$id?>"  class="btn btn-primary"> Subir/Cambiar imagen</a> 
		
		
		<? /* <FORM METHOD="post" ACTION="admin_contenido3_imagen.php?accionfoto=ins&idimg=1&id=<?=$id?>&tipo=<?=$tipo?>" enctype="multipart/form-data">
		<fieldset>				    
			<legend>Cambiar Imagen Destacada</legend>
			<div class="control-group">
				<label class="control-label" for="inputName">Seleccione imagen:</label>
					<div class="controls">
						<input name="userfile" type="file" />
					</div>
			</div>
			<div>Imagen  JPG y tamaño menor de 2MB. </div>
		</fieldset>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary btn-large">Guardar</button>
		</div>
		</form>
		*/
		?>
	
	</div>

	<? /*
	<div class="grid-4">
		<h3>Imagen Normal: (1000x700)</h3>
		<?
		$idpadre=$id;
		$link2=conectar(); //Postgrepsql
		$result2=pg_query($link2,"SELECT img3 FROM generica WHERE $sql id='$id' AND borrado=0;");// or die (mysql_error());  
		if ($result2){
			while($row2= pg_fetch_array($result2)) {	
				?><ul><?							
						if ($row2["img3"]<>""){?>
							<li>
								<span class="actions">
									<a href="imagen/<?=$row2["img3"]?>" rel="shadowbox[galery]" ><img SRC="imagen/<?=$row2["img3"]?>" ALT width=60><i class="icon-zoom-in"></i> Ver</a> &middot; 
									<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_contenido3_imagen.php?accionfoto=eli3&accion=<?=$accion?>&tipo=<?=$tipo?>&id=<?=$idpadre?>&archivo=<?=$row2["img3"]?>"><i class="icon-trash"></i> Eliminar</a>
								</span>
							</li>
						<? }?>
				</ul>						
				<? 
			}?>
		<? 
		}?>
		<FORM METHOD="post" ACTION="admin_contenido3_imagen.php?accionfoto=ins&idimg=3&id=<?=$id?>&tipo=<?=$tipo?>" enctype="multipart/form-data">
		<fieldset>				    
			<legend>Cambiar Imagen Normal</legend>
			<div class="control-group">
				<label class="control-label" for="inputName">Seleccione imagen:</label>
					<div class="controls">
						<input name="userfile" type="file" />
					</div>
			</div>
			<div>Imagen  JPG y tamaño menor de 2MB. </div>
		</fieldset>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary btn-large">Guardar</button>
		</div>
		</form>
	</div>
	*/?>
	
	
	
	<div class="grid-12">
		<h3><br>[opcional] Vídeo(sustituye imagen principal):</h3>
		 <?php
		 
		$result2=pg_query($link2,"SELECT video FROM generica WHERE id='$id' AND borrado=0;");// or die (mysql_error());  
		if ($result2){
			if($row2= pg_fetch_array($result2)) {
				$video=$row2["video"];
			}
		}
					
		if ($video<>""){
			?>
				<span class="actions">
					<div class="videocontainer">
						<?=$video?>											
					</div>
				<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_contenido3_imagen.php?videoprincipal=borrar&accion=<?=$accion?>&tipo=<?=$tipo?>&id=<?=$id?>"><i class="icon-trash"></i> Eliminar Video</a>
				</span>
			
			<? 
		}?>
			<form class="titulo" action="admin_contenido3_imagen.php?videoprincipal=insertar&accion=<?=$accion?>&id=<?=$id?>&tipo=<?=$tipo?>" method="post" enctype="multipart/form-data">     
			<div>Código: (Recomendado width="560" height="315") (&autoplay=1)</div>
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
	</div>
		
	</div>
	<? /* 
	
	<h2>Imágenes Secundarias:</h2>
	<?
	$idpadre=$id;
	$link2=conectar(); //Postgrepsql
	$result2=pg_query($link2,"SELECT id,foto FROM foto WHERE $sql padre='$idpadre' AND borrado=0;");// or die (mysql_error());  
	if ($result2){
		while($row2= pg_fetch_array($result2)) {	
			?><ul><?							
					if ($row2["foto"]<>""){?>
						<li>
							<span class="actions">
								<a href="imagen/<?=$row2["foto"]?>" rel="shadowbox[galery]" ><img SRC="imagen/<?=$row2["foto"]?>" ALT width=60><i class="icon-zoom-in"></i> Ver</a> &middot; 
								<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_contenido3_imagen.php?accionfoto=borrar2&accion=<?=$accion?>&tipo=<?=$tipo?>&idfoto=<?=$row2["id"]?>&id=<?=$idpadre?>&archivo=<?=str_replace("p.jpg", ".jpg",$row2["foto"])?>&directorio=imagen"><i class="icon-trash"></i> Eliminar</a>
							</span>
						</li>
					<? }?>
			</ul>						
			<? 
		}?>
	<? 
	}?>
	<FORM METHOD="post" ACTION="admin_contenido3_imagen.php?accionfoto=inserta&id=<?=$id?>&tipo=<?=$tipo?>" enctype="multipart/form-data">
	<fieldset>				    
		<legend>Nueva Imagen Secundaria</legend>
		<div class="control-group">
			<label class="control-label" for="inputName">Seleccione imagen:</label>
				<div class="controls">
					<input name="userfile" type="file" />
				</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputName">Descripción:</label>
				<div class="controls">
					<input type="text" name="comentario" size="40" />
				</div>
		</div>
		<div>Imagen  JPG y tamaño menor de 2MB. </div>
	</fieldset>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary btn-large">Guardar</button>
	</div>
	</form>
		
	*/ 
	?>
		

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
pg_free_result($result2); 
pg_close($link2); 
//session_destroy();
include("plantillaweb02admin.php"); 
?>