<?
$id=strip_tags($_REQUEST['id']);
if ($id==''){
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}

define('MB', 1048576);
include("_seguridadfiltro.php"); 
include("_funciones.php"); 
include("_cone.php"); 
$c_directorio = '/var/www/web';

$accion=strip_tags($_GET['accion']); 
//	ACCIONES
$accionpdf=$_GET['accionpdf'];
 
if($accionpdf=='inserta'){
	$nombre_archivo = $_FILES['userfile']['name']; 
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$tipo_archivo = finfo_file($finfo, $_FILES['userfile']['tmp_name']);
	$tamano_archivo = $_FILES['userfile']['size'];
	$nombre=strip_tags(trim($_POST['nombre'])); 
	$privado=strip_tags(trim($_POST['privado'])); 
	
	if ($nombre==""){
		$nombre=$nombre_archivo;
	}

	$_SESSION[esterror]="No se ha podido insertar, datos incorrectos.";

	//compruebo si las caractersticas del archivo son las que deseo 
	if (!((strpos($tipo_archivo, "rar") )  || (strpos($tipo_archivo, "octet") ) || (strpos($tipo_archivo, "doc") )   || (strpos($tipo_archivo, "excel") )  || (strpos($tipo_archivo, "xls") )  || (strpos($tipo_archivo, "rtf") )  || (strpos($tipo_archivo, "ppt") )  || (strpos($tipo_archivo, "od") )  || (strpos($tipo_archivo, "zip") ) || (strpos($tipo_archivo, "pdf") ) && ($tamano_archivo < 1024*MB))) {     
			$_SESSION[esterror]="La extensión o el tamaño de los archivos no es correcta. (Max: 1024MB) (pdf,zip,rar,doc,docx,xlsx,xls,rtf,ppt,odt,ods)"; 
	}else{
	
			switch( $tipo_archivo ) 
			{ 
			  case "application/pdf": $extension="pdf"; break; 
			  case "application/msword": $extension="doc"; break; 
			  case "application/vnd.openxmlformats-officedocument.wordprocessingml.document";	$extension = "docx"; break; 
			  case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";	$extension = "xlsx"; break; 
			  case "application/vnd.ms-excel": $extension="xls"; break; 
			  case "application/rar": $extension="rar"; break; 
			  case "application/octet-stream": $extension="rar"; break; 
			  case "application/x-rar-compressed": $extension="rar"; break; 
			  case "application/x-zip-compressed": $extension="zip"; break; 
			  case "application/x-download": $extension="zip"; break; 				  
			  case "application/zip": $extension="zip"; break; 
			  case "application/rtf": $extension="rtf"; break; 
			  case "application/vnd.ms-powerpoint": $extension="ppt"; break; 
			  case "application/vnd.oasis.opendocument.text": $extension="odt"; break; 
			  case "application/vnd.oasis.opendocument.spreadsheet": $extension="ods"; break; 
			  
			} 
		if ($tipo_archivo==""){
			$extension="pdf";
		} 
		$archiv="pu";
		$archivv=$archiv.=time();
		$archivv=$archivv.".".$extension;
		$destino = $c_directorio."/files/".$archivv;	
		 if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$destino)){
			$link=conectar(); //Postgresql
			if ($idcolegio==""){
				$idcolegio=0;
			}
			$result=pg_query($link,"INSERT INTO archivo (archivo,nombre,padre,idcolegio,enventa) VALUES ('$archivv','$nombre','$id','$idcolegio','$privado')" );// or die (mysql_error()); 
			
			if ($result){
				$_SESSION[esterror]="Archivo guardado correctamente";
			}
			else{
				
				$_SESSION[esterror]="Error inesperado.".pg_last_error();
			}
		}else{
			$_SESSION[esterror]="Archivo no guardado. ";
		} 
	}
	header("Location: admin_contenido3_archivo.php?id=$id");
	exit();
}	
if($accionpdf=='borrar'){
	$archivo=$_GET['archivo']; 			//optativos pero obligatorio para eliminar archivos
	$directorio=$_GET['directorio'];	//optativos pero obligatorio para eliminar archivos
	$idpdf=$_GET['idpdf'];
	$_SESSION[esterror]="No se ha podido eliminar, datos incorrectos.";
	$link=conectar(); //Postgresql
	$result=pg_query($link,"delete from archivo where id ='$idpdf'") ;//or die ("Error:borrarpdf_".mysql_error()); 
	if (($archivo<>"")){ // Parametro optativo
		if ($directorio<>""){ // Parametro optativo
			//Eliminar archivo
			if ($archivo<>"") {
				$path1=$c_directorio."/".$directorio."/".$archivo; //Ruta
				chmod($path1,0777);		//Permisos
				if (!unlink($path1)){  //Elimina
						$_SESSION[esterror]="El archivo no existe: ".$archivo;  //Si error informa
				}else{
						$_SESSION[esterror]="Se ha eliminado el documento correctamente.";
				}
			}
		}
		//Eliminar de BD
	}		
	header("Location: admin_contenido3_archivo.php?id=$id");
	exit();
}

$safe="Gestion de publicaciones";
$tipo="publicacion";
$titulo1="publicaciones ";
$titulo2="administracion";

$sql = "SELECT precio FROM generica WHERE id='$id' AND BORRADO=0";
$result = posgre_query($sql);
$row = pg_fetch_array($result);
$precio = $row["precio"];
$disabled = "";
$disabledtexto = "";
if ($precio==""){
	$disabled = " readonly=\"readonly\" ";
}


include("plantillaweb01admin.php"); 
?>	<script language="javascript">
		function confirmar ( mensaje ) {
			return confirm( mensaje );
			}
	</script>
 	<script  type="text/javascript" src="../web/ckeditor/ckeditor.js"></script>
	<h2>Documentos de publicación:</h2>
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
	<br><h4>Documentos</h4>	
	<br>
	<?
	$link2=conectar(); //Postgrepsql
	$result2=pg_query($link2,"SELECT id,archivo,nombre,enventa FROM archivo WHERE padre='$id' AND borrado=0;") ;//or die (mysql_error());  
	if ($result2){
		while($row2= pg_fetch_array($result2)) {								
			?><ul><? 
					if ($row2["archivo"]<>""){
						$enventa = $row2['enventa'];
						
						$priv = "";
						if ($enventa=='1'){
							$priv = "[PRIVADO]";
						}
						else{
							$priv = "[PÚBLICO]";
						}
						
						if (trim($row2["nombre"])==""){
							$nombrear="Documento";
						}else{
							$nombrear=$row2["nombre"];
						}
						?>
						<li>
						<span class="actions"><?=$priv?> <?=ucfirst($nombrear)?> &middot; 
							<a href="descarga.php?documento=<?=$row2["archivo"]?>" ><i class="icon-zoom-in"></i> Ver</a> &middot; 
							<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_contenido3_archivo.php?accionpdf=borrar&accion=<?=$accion?>&tipo=<?=$tipo?>&idpdf=<?=$row2["id"]?>&id=<?=$id?>&archivo=<?=$row2["archivo"]?>&directorio=files"><i class="icon-trash"></i> Borrar</a>
						</span>
						</li>									
					<? }?>
				</ul>
			<? 
		}
	}?>
	
	<br>
	<hr>
	<form class="titulo" action="admin_contenido3_archivo.php?accionpdf=inserta&accion=modificar&id=<?=$id?>&tipo=<?=$tipo?>&pdf=<?=$pdf?>" method="post" enctype="multipart/form-data">     
	<h4>Nuevos documentos</h4>
	<span>Permite pdf,zip,rar,doc,docx,xlsx,xls,rtf,ppt,odt,ods y tamaño menor de 1024MB</span>
	<br><br><div><input name="userfile" type="file" /></div>
	<br><div>Descripción:</div>
	<div><input type="text" name="nombre" size=40 /></div>
	<br><div>¿Archivo en venta? (aparecerá al usuario después de realizar la compra):</div>
	<select <?=$disabled?> id="privado" name="privado"> 
	<option value="0">NO</option>
	<option value="1">SI</option>
	</select>
	<br><br><div><input type="submit" value="Insertar" class="btn btn-success"> </div>
	</form>								
	<? 
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