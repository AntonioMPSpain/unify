<?
include("_seguridadfiltro.php"); 

include("_funciones.php"); 
include("_cone.php"); 

$safe="Gestion de publicaciones";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
$tipo="publicacion";
$c_directorio = '/var/www/web';

$titulo1="publicaciones ";
$titulo2="administracion";

$accion=$_GET['accion'];
$accionfoto=strip_tags($_REQUEST['accionfoto']);

if($accionfoto=='borrar'){
	$id=strip_tags($_REQUEST['id']);
	if ($id==''){
		header("Location: index.php?salir=true");
		exit();
	} 
	$archivo=$_GET['archivo']; 			//optativos pero obligatorio para eliminar archivos
	$directorio=$_GET['directorio'];	//optativos pero obligatorio para eliminar archivos
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
		$link=conectar(); //Postgrepsql
		$Query = pg_query($link,"UPDATE generica SET $sql foto='' WHERE id='$id' ;");
	}		
	header("Location: admin_contenido2.php?id=$id&accion=modificar");
	exit();
}




if($accion=='guardarm'){
	$id=strip_tags($_REQUEST['id']);
	if ($id==''){
		header("Location: index.php?salir=true");
		exit();
	}
	$fecha=cambiaf_a_mysql($_POST['fecha']);	
	$destacado=$_POST['destacado']; 
	$destacado=0; 
	$titulo=$_POST['titulo']; 
	$informacion=$_POST['informacion']; 
	$foto= $_FILES['userfile']['name']; 
	$precio=trim($_POST['precio']); 
	$preciofisico = trim($_POST['preciofisico']);
	$permiso=$_POST['permiso']; 
	$tipopubli=$_POST['tipopubli']; 
	$activo = $_POST['oculto'];
	
	$precio = str_replace(",",".",$precio);
	$preciofisico = str_replace(",",".",$preciofisico);
	
	if ($precio=="") {
		$precioBBDD=",precio=NULL";
	}
	else{
		$precioBBDD=",precio='$precio'";
	}
	if ($preciofisico=="") {
		$preciofisicoBBDD=",preciofisico=NULL";
	}
	else{
		$preciofisicoBBDD=",preciofisico='$preciofisico'";
	}
	
	$tipo_archivo = $_FILES['userfile']['type'];
	if (!isset($foto) || (!strpos($tipo_archivo, "p"))){ //Entra si no hay imagen
		$sql="UPDATE generica SET tipopubli='$tipopubli',permiso='$permiso' $precioBBDD $preciofisicoBBDD,titulo='$titulo',informacion='$informacion',fecha='$fecha', activo='$activo' WHERE  $sql id='$id'";
		$link=conectar(); //Postgresql
		$Query = pg_query($link,$sql);
		$_SESSION[esterror]="Guardado correctamente";
	}else{	//El formulario conbtiene imagen principal
		$nombre_archivo = $_FILES['userfile']['name']; 
		$tipo_archivo = $_FILES['userfile']['type']; 
		$tamano_archivo = $_FILES['userfile']['size'];
		//compruebo si las caractersticas del archivo son las que deseo 
				//compruebo si las caractersticas del archivo son las que deseo 
			if (!( (strpos($tipo_archivo, "powerpoint"))||(strpos($tipo_archivo, "ods"))||(strpos($tipo_archivo, "xls"))||(strpos($tipo_archivo, "excel"))||(strpos($tipo_archivo, "bmp"))||(strpos($tipo_archivo, "octet-stream"))||(strpos($tipo_archivo, "rar"))||(strpos($tipo_archivo, "zip"))||(strpos($tipo_archivo, "rtf"))||(strpos($tipo_archivo, "ppt"))||(strpos($tipo_archivo, "tif"))||(strpos($tipo_archivo, "odt"))||(strpos($tipo_archivo, "pdf"))||(strpos($tipo_archivo, "doc"))||(strpos($tipo_archivo, "msword"))||(strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) && ($tamano_archivo < 5200000000)) {  
				$_SESSION[esterror]="Error en tipo o tamaño de archivo:-".$tipo_archivo."- ".$tamano_archivo;
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
						$_SESSION[esterror]="Error en tipo o tamaño de archivo:".$tipo_archivo." - ".$tamano_archivo;
					}
				}
			$archiv=sanear_string($nombre_archivo)."_";
			$archivv=$archiv.=time();
			$archivv=$archivv.".".$extension;
			$destino = $c_directorio."/imagen/".$archivv;	
			//$destino = $c_directorio."/files/".$archivv;	
			 if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$destino)){	
			 	$sql="UPDATE generica SET tipopubli='$tipopubli',permiso='$permiso',destacado='$destacado',titulo='$titulo',informacion='$informacion',fecha='$fecha' $precioBBDD $preciofisicoBBDD,activo='$activo' WHERE  $sql id= '$id'";
				$link=conectar(); //Postgresql
				$Query = pg_query($link,$sql );
				$_SESSION[esterror]="Se ha guardado correctamente. ";
					/*echo " <p class=ok>  Comenzamos a disminuir la imagen... </p>";
					include("_resize.php");
			 		if (resizeImg("imagen/".$archivv,"imagen/".$archi2,250,250)){
						echo " <p class=texto2 align=center> &nbsp; &nbsp; &nbsp; Reduccion perfecta. <br> <img SRC=images/ok.png ALT > </p>";
					}else{
						echo " <p class=texto2> &nbsp; &nbsp; &nbsp; ERROR en Reduccion de imagen. Contacte con ADMIN. </p>";
					}*/
			}else{
				$_SESSION[esterror]="Ocurrió algún error al subir el fichero. No pudo guardarse.";
			} 					
		}
	}
	
	
	header("Location: admin_contenido.php");
	exit();
}elseif($accion=='guardar'){
	$titulo=$_POST['titulo']; 
	$informacion=$_POST['informacion']; 
	$permiso=$_POST['permiso']; 
	$destacado=$_POST['destacado']; 
	$destacado=0; 
	$precio=$_POST['precio']; 
	$preciofisico=$_POST['preciofisico']; 
	$tipopubli=$_POST['tipopubli']; 
	$activo = $_POST['oculto'];	
	
	if ($precio=="") {
		$precioBBDD="NULL";
	}
	else{
		$precioBBDD="'".$precio."'";
	}
	if ($preciofisico=="") {
		$preciofisicoBBDD="NULL";
	}
	else{
		$preciofisicoBBDD="'".$preciofisico."'";
	}
	
	if ($idcolegio=="") $idcolegio=0;
	$foto= $_FILES['userfile']['name']; 
	$tipo_archivo = $_FILES['userfile']['type'];

	if (!isset($foto) || (!strpos($tipo_archivo, "p"))){
		$link=conectar(); //Postgresql
		$Query = pg_query($link,"INSERT INTO generica (idcolegio,tipopubli,permiso,titulo,informacion,fecha,tipo,precio,activo,preciofisico) VALUES ('$idcolegio','$tipopubli','$permiso','$titulo','$informacion',now(),'$tipo',$precioBBDD,'$activo',$preciofisicoBBDD)");

		$id=pg_last_oid($Query);
		

		
		$_SESSION[esterror]="Guardado correctamente. </p>";
		if ($Query){
			$_SESSION[esterror]=" Guardado correctamente. ";
		}
		else{		
			echo "No pudo guardarse. Contacte con el administrador:". pg_last_error();
			$_SESSION[esterror]=" No pudo guardarse. Contacte con el administrador";
			exit();
		}
			
	}else{	
		$nombre_archivo = $_FILES['userfile']['name']; 
		$tipo_archivo = $_FILES['userfile']['type']; 
		$tamano_archivo = $_FILES['userfile']['size'];
		//compruebo si las caractersticas del archivo son las que deseo 
		if (!((strpos($tipo_archivo, "jpg") || strpos($tipo_archivo, "jpeg") ) && ($tamano_archivo < 20000000000000))) {     
			$_SESSION[esterror]=" La extensión o el tamaño de los archivos no es correcta. (Max: 2000 Kb) (jpg)"; 
		}else{
			$archiv=sanear_string($nombre_archivo)."_";
			$archivv=$archiv.=time();
			$archivv=$archivv.".".$extension;
			$destino = $c_directorio."/imagen/".$archivv;	
			 if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$destino)){
					$link=conectar(); //Postgresql
					$Query = pg_query($link,"INSERT INTO generica (idcolegio,tipopubli,idpadre,titulo,informacion,fecha,tipo,precio,activo,preciofisico) VALUES ('$idcolegio','$tipopubli','$idcolegio','$titulo','$informacion',now(),'$tipo','$archivv',$precioBBDD,'$activo',$preciofisicoBBDD)");
					$id=pg_last_oid($Query);
					
					if ($Query){
						$_SESSION[esterror]=" Guardado correctamente. ";
					}
					else{
						$_SESSION[esterror]=" Ocurrió algún error al subir el fichero. No pudo guardarse. Contacte con el administrador";
					}
					
					
					/*echo " <p class=texto2> &nbsp; &nbsp; &nbsp; Comenzamos a disminuir la imagen... </p>";
					include("_resize.php");
			 		if (resizeImg("imagen/".$archivv,"imagen/".$archi2,250,250)){
						echo " <p class=texto2 align=center> &nbsp; &nbsp; &nbsp; Reduccion perfecta. <br><a href=admin_contenido.php?tipo=$tipo><img SRC=images/ok.png ALT ></a></p>";
					}else{
						echo " <p class=texto2 align=center> &nbsp; &nbsp; &nbsp; ERROR en Reduccion de imagen. Contacte con ADMIN. </p>";
					}*/
			}else{
				$_SESSION[esterror]=" Ocurrió algún error al subir el fichero. No pudo guardarse.";
			} 					
		}
	}
	
	header("Location: admin_contenido.php");
	exit();
}

include("plantillaweb01admin.php"); 
?>	<script language="javascript">
		function confirmar ( mensaje ) {
			return confirm( mensaje );
			}
	</script>
 	<script  type="text/javascript" src="ckeditor/ckeditor.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" /> 
<script src="https://code.jquery.com/jquery-1.9.1.js"></script> 
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script> 
$(function() { 
	$("#fecha").datepicker( { dateFormat: "dd/mm/yy", dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ], dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ], firstDay: 1, gotoCurrent: true, monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Deciembre" ] } ); 
}); 
</script> 
<?
if (!isset($accion)){
	?>	
	<FORM METHOD="post" ACTION="admin_contenido2.php?accion=guardar&tipo=<?=$tipo?>" enctype="multipart/form-data">
	<label>Título: (limitado a 140 caracteres)</label>
	<input type="text" name="titulo" maxlength="140" class="input-xxlarge" />
	<label>Información:</label>
	<textarea name="informacion" id="informacion"></textarea>
								<script>
									window.onload = function() {
										CKEDITOR.replace( 'informacion' );
									};
								</script>
	<br><label>Oculto:</label>
	<select name="oculto" >
								
			<option value="1">NO</option>
			<option value="0">SI</option>
	  </select>	
	 
	  <? /* 
	<label>Destacado:</label>
	<select name="destacado" >
								<option value="0">NO</option>
								<option value="1">SI</option>
	  </select>
	  */ ?>
	  
	<label for="permiso">Privado(solo lo verán colegiados):</label>
	<select name="permiso" id="permiso" >
								<option value="0">NO</option>
								<option value="1">SI</option>
	  </select>
	<label for="tipopubli">Tipo de Publicación:</label>
	<select name="tipopubli" id="tipopubli" >
								<option value="noticia">Noticia</option>
								<option value="articulo">Artículo</option>
								<option value="libro">Libro</option>
								<option value="app">APP</option>
								<option value="video">Video</option>
	 </select>

	<br>
	<label>Precio formato digital:</label>
	<input type="text" class="input-small" name="precio" size="10" value="<?=$precio;?>" />&nbsp;€
	
	<label>Precio formato papel:</label>
	<input type="text" class="input-small" name="preciofisico" size="10" value="<?=$preciofisico;?>" />&nbsp;€
	<br>
	<!--<input TYPE=SUBMIT VALUE="Guardar">       -->
		<button type="submit" class="btn btn-important">Guardar</button>
	</FORM>
	<?
}elseif(($accion=='modificar') || ($accion=='copiar')) {
	$id=strip_tags($_REQUEST['id']);
	if ($id==''){
		header("Location: index.php?salir=true");
		exit();
	}
	$sql="select * from generica WHERE $sql id='$id' AND borrado=0;";
	$link=conectar(); //Postgrepsql
	$result=pg_query($link,$sql); 
	$row = pg_fetch_array($result);
	$precio=$row["precio"];
	$preciofisico = $row["preciofisico"];
	if ($accion=='copiar'){ ?>
		<form action="admin_contenido2.php?accion=guardar&tipo=<?=$tipo?>" method="post" enctype="multipart/form-data">
	<? }else{ ?>
		<form action="admin_contenido2.php?accion=guardarm&id=<?=$id?>&tipo=<?=$tipo?>" method="post" enctype="multipart/form-data">
	<? }
	?>
	<fieldset>				    
		<legend>Editar</legend>
		<div class="control-group">
			<label class="control-label" for="inputName">Título:</label>
				<div class="controls">
					<input type="text" name="titulo" size="255" class="input-xxlarge" value="<?=$row["titulo"];?>" />
				</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputName">Información:</label>
				<div class="controls">
										<textarea name="informacion" id="informacion"><?=$row["informacion"];?></textarea>
										<script>
											window.onload = function() {
												CKEDITOR.replace( 'informacion' );
											};
										</script>
				</div>
		</div>
		<br><div class="control-group">
			<label class="control-label" for="inputName">Fecha publicación: (Formato: 01/01/<?=date(Y)?>)</label>
				<div class="controls">
					<input class="input-small" id="fecha" type="text" name="fecha" size="10" value="<?=cambiaf_a_normal($row["fecha"])?>" />
				</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputName">Oculto:</label>
				<div class="controls">
					<select name="oculto" >
						<option value="1"<? if ($row['activo']==1) {?> selected="selected" <? }?>>NO</option>
						<option value="0"<? if ($row['activo']==0) {?> selected="selected" <? }?>>SI</option>
					  </select>
				</div>
		</div>
		<? /*
		<div class="control-group">
			<label class="control-label" for="inputName">Destacado:</label>
				<div class="controls">
					<select name="destacado" >
						<option value="1"<? if ($row['destacado']==1) {?> selected="selected" <? }?>>SI</option>
						<option value="0"<? if ($row['destacado']==0) {?> selected="selected" <? }?>>NO</option>
					  </select>
				</div>
		</div>
		*/ ?>
		
		<div class="control-group">
			<label class="control-label" for="permiso">Privado(solo lo verán colegialos):</label>
				<div class="controls">
					<select name="permiso" id="permiso" >
						<option value="1"<? if ($row['permiso']==1) {?> selected="selected" <? }?>>SI</option>
						<option value="0"<? if ($row['permiso']==0) {?> selected="selected" <? }?>>NO</option>
					  </select>
				</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="permiso">Tipo de Publicación:</label>
				<div class="controls">
					<select name="tipopubli" id="tipopubli" >
						<option value="noticia"<? if ($row['tipopubli']=="noticia") {?> selected="selected" <? }?>>Noticia</option>
						<option value="articulo"<? if ($row['tipopubli']=="articulo") {?> selected="selected" <? }?>>Artículo</option>
						<option value="libro"<? if ($row['tipopubli']=="libro") {?> selected="selected" <? }?>>Libro</option>
						<option value="app"<? if ($row['tipopubli']=="app") {?> selected="selected" <? }?>>APP</option>
						<option value="video"<? if ($row['tipopubli']=="video") {?> selected="selected" <? }?>>Video</option>
					  </select>
				</div>
		</div>
		<? /* 
		<div class="control-group">
			<label class="control-label" for="permanente">Permanente:</label>
				<div class="controls">
					<select name="permanente" id="permanente" >
						<option value="1"<? if ($row['permanente']==1) {?> selected="selected" <? }?>>SI</option>
						<option value="0"<? if ($row['permanente']==0) {?> selected="selected" <? }?>>NO</option>
					  </select>
				</div>
		</div>
		*/ ?>
		<br>
		<div class="control-group">
			<label class="control-label" for="inputName">Precio formato digital:</label>
				<div class="controls">
					<input class="input-small"  type="text" name="precio" size="10" value="<?=$precio;?>" />&nbsp;€
				</div>
		<div class="control-group">
			<label class="control-label" for="inputName">Precio formato papel:</label>
				<div class="controls">
					<input class="input-small"  type="text" name="preciofisico" size="10" value="<?=$preciofisico;?>" />&nbsp;€
				</div>
		</div>
		
	</fieldset>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary btn-large">Guardar</button>
	</div>
	</form>
	<?									   
pg_free_result($result); 
pg_close($link); 
//session_destroy();
}
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
include("plantillaweb02admin.php"); 
?>