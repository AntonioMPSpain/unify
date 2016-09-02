<?php
include_once("_funciones.php"); 
include_once("_cone.php"); 
include_once("p_funciones.php"); 

$safe="Publicidad Banners";
$titulo1="publicidad";
$titulo2="banners";

////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==1) { //Admin Total

}
else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$url="";
$accion=strip_tags($_GET['accion']); 
$id=strip_tags($_GET['id']); 

if ($accion=="borrarfoto1"){
	$sql = "UPDATE p_anuncios SET imagen0='' WHERE id='$id'";
	posgre_query($sql);
}

if ($accion=="borrarfoto2"){
	$sql = "UPDATE p_anuncios SET imagen1='' WHERE id='$id'";
	posgre_query($sql);
}

if ($accion=="guardar"){
		
	$nombre=($_POST['nombre']);
	$url=($_POST['url']);
	
	if (!(strpos($url, 'http') !== false)) {
		$_SESSION[esterror]=" No se ha incluido HTTP en la URL ";
		
		header("Location: p_anuncio.php?id=$id");
		exit();
	}
	
	
	
	$fechainicio=($_POST['fechainicio']);
	$fechafin=($_POST['fechafin']);
	$estado=($_POST['estado']);
	$imagen1=($_POST['imagen1']);
	$imagen2=($_POST['imagen2']);
	
	$c_directorio_img = "/var/www/web";
	
	/** IMAGEN 1
	pasar a función**/
	
	
	$nombre_archivo = $_FILES['userfile']['name']; 
	if (!isset($nombre_archivo)){
		
	}else{
		$tipo_archivo = $_FILES['userfile']['type']; 
		$tamano_archivo = $_FILES['userfile']['size'];
		//compruebo si las caractersticas del archivo son las que deseo 
		if (!( (strpos($tipo_archivo, "bmp"))||(strpos($tipo_archivo, "tif"))||(strpos($tipo_archivo, "gif"))||(strpos($tipo_archivo, "png"))||(strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) && ($tamano_archivo < 50000)) {  
			$_SESSION[esterror]="Error en tipo o tamaño de archivo:-".$tipo_archivo."- ".$tamano_archivo;
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
			
			$archiv=sanear_string($nombre_archivo)."_";
			$archivv=$archiv.=time();
			$archivv=$archivv.".".$extension;
			$destino = $c_directorio_img."/imagen/".$archivv;	
			
			
			 if (move_uploaded_file ($_FILES['userfile']['tmp_name'],$destino)){	
				$_SESSION[esterror]="Se ha guardado correctamente.";	
				$imagen1=$archivv;
				
			}else{
				$_SESSION[esterror]=" No pudo subir el fichero.";
				$imagen1="";
			} 					
		}
	}
	
	/** IMAGEN 2
	pasar a función**/
	$nombre_archivo2 = $_FILES['userfile2']['name']; 
	if (!isset($nombre_archivo2)){
		
	}else{
		$nombre_archivo = $_FILES['userfile2']['name']; 
		$tipo_archivo = $_FILES['userfile2']['type']; 
		$tamano_archivo = $_FILES['userfile2']['size'];
		//compruebo si las caractersticas del archivo son las que deseo 
		if (!( (strpos($tipo_archivo, "bmp"))||(strpos($tipo_archivo, "tif"))||(strpos($tipo_archivo, "gif"))||(strpos($tipo_archivo, "png"))||(strpos($tipo_archivo, "jpg"))||(strpos($tipo_archivo, "JPEG"))||(strpos($tipo_archivo, "JPG"))||(strpos($tipo_archivo, "jpeg")) ) && ($tamano_archivo < 50000)) {  
			$_SESSION[esterror]="Error en tipo o tamaño de archivo:-".$tipo_archivo."- ".$tamano_archivo;
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
			
			$archiv=sanear_string($nombre_archivo)."_";
			$archivv=$archiv.=time();
			$archivv=$archivv.".".$extension;
			$destino = $c_directorio_img."/imagen/".$archivv;	
			
			
			 if (move_uploaded_file ($_FILES['userfile2']['tmp_name'],$destino)){	
				$_SESSION[esterror]="Se ha guardado correctamente.";	
				$imagen2=$archivv;
				
			}else{
				$_SESSION[esterror]=" No pudo subir el fichero.";
				$imagen2="";
			} 					
		}
	}
	
	
		
	if ($id>0){				// UPDATE
	
		$sqlupdate = "";
		if ($imagen1<>""){
			$sqlupdate .= " , imagen0 = '$imagen1', imagen1 = '$imagen2' ";
		}
		
		if ($imagen2<>""){
			$sqlupdate .= " , imagen1 = '$imagen2' ";
		}
	
		$Query = posgre_query("UPDATE p_anuncios SET nombre='$nombre' ,url = '$url', fechainicio = '$fechainicio', fechafin = '$fechafin', estado = '$estado' $sqlupdate WHERE id ='$id'; "); 
	}
	else{					// INSERT
		$Query = posgre_query("INSERT INTO p_anuncios (nombre, url, fechainicio, fechafin, estado, imagen0, imagen1) VALUES ('$nombre','$url', '$fechainicio', '$fechafin', '$estado', '$imagen1', '$imagen2') RETURNING id;");
		$rowid = pg_fetch_array($Query);
		$id = $rowid["id"];
	}
	
	/** Desactivamos el anuncio de todos los banners */
	$sqlbanneranuncio = "UPDATE p_bannersanuncios SET borrado='1' WHERE idanuncio='$id'";
	posgre_query($sqlbanneranuncio);
		
	foreach($_POST as $key => $value){
		$pos=strpos($key,"banner_");
		if($pos!==false){
			$pieces = explode("banner_", $key);
			$idbanner = $pieces[1];
			
			
			/** Vemos los checkbox seleccionados y activamos */
			$sqlbanneranuncio = "SELECT * FROM p_bannersanuncios WHERE idbanner='$idbanner' AND idanuncio='$id'";
			$resultbanneranuncio = posgre_query($sqlbanneranuncio);
	
			if (pg_num_rows($resultbanneranuncio)>0){
				$sqlbanneranuncio = "UPDATE p_bannersanuncios SET borrado='0' WHERE idbanner='$idbanner' AND idanuncio='$id'";
			}
			else{
				$sqlbanneranuncio = "INSERT INTO p_bannersanuncios(idbanner, idanuncio, borrado) VALUES ('$idbanner','$id','0') ";
			}
			posgre_query($sqlbanneranuncio);
			
			
		}	
	}
			
			
	if ($Query){
		$_SESSION[esterror]="Guardado correctamente";	
	}
	else{
		
		$_SESSION[esterror]="Fallo al guardar".pg_last_error();	
	}
	
	header("Location: p_anuncios.php");
	exit();
	
}

if ($id>0){
	
	$sql = "SELECT * FROM p_anuncios WHERE borrado=0 AND id='$id'";
	$result = posgre_query($sql);
	if ($row = pg_fetch_array($result)){
		
		$nombre=($row['nombre']);
		$url=($row['url']);
		$fechainicio=($row['fechainicio']);
		$fechafin=($row['fechafin']);
		$estado=($row['estado']);
		$imagen1=($row['imagen0']);
		$imagen2=($row['imagen1']);
		
	}
}
else{
	$fechainicio = date("Y-m-d");
	$fechafin = date("Y-m-d");
}

include("plantillaweb01admin.php");

?>


<!--Arriba plantilla1-->
<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<? include("_aya_mensaje_session.php"); ?>



		<div class="bloque-lateral acciones">		
			<p>
				<a href="p_anuncios.php" class="btn btn-success" type="button">Volver</a>
			</p>
		</div>
		<br>
		
		<form id="formcontacto" method="post" action="p_anuncio.php?accion=guardar&id=<?=$id?>" enctype="multipart/form-data" >
		<h3>Datos</h3>			
		
		<p>
			<label class="description" for="nombre">Nombre:<br />
				<input id="nombre" name="nombre" type="text" maxlength="255"  size="80" class="inputtextarea input-xxlarge" value="<?=$nombre?>" />  
			</label>
		</p>
		<p>
			<label class="description" for="url">URL(incluir http:// o https://):<br />
				<input id="url" name="url" type="text" class="inputtextarea input-xxlarge" value="<?=$url?>" />  
			</label>
		</p>
		<p>
			<label class="description" for="imagen1">Banner superior o inferior (Alto:90px, Ancho:790px, Tamaño: ~50KB, permite GIFS):<br />
				<? 
				if ($imagen1<>""){?>
					<span class="actions">
						<a href="imagen/<?=$imagen1?>" ><img SRC="imagen/<?=$imagen1?>" ><i class="icon-zoom-in"></i> Ver</a>
						<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="p_anuncio.php?accion=borrarfoto1&id=<?=$id?>"><i class="icon-trash"></i> Eliminar</a>
					</span>
				<? } else{ ?> 
					<input name="userfile" type="file" > 
				<? } ?>
				
			</label>
		</p>
		<p>
			<label class="description" for="imagen2">Banner lateral (Alto:250px, Ancho:250px, Tamaño: ~20KB, permite GIFS):<br />
				<? 
				if ($imagen2<>""){?>
					<span class="actions">
						<a href="imagen/<?=$imagen2?>" rel="shadowbox[galery]" ><img SRC="imagen/<?=$imagen2?>"><i class="icon-zoom-in"></i> Ver</a>
						<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="p_anuncio.php?accion=borrarfoto2&id=<?=$id?>"><i class="icon-trash"></i> Eliminar</a>
					</span>
				<? } else{ ?> 
					<input name="userfile2" type="file" > 
				<? } ?>
			</label>
		</p>
				
		<br>
		<h3>Visibilidad</h3>			
		
		<p>
			<label class="description" for="estado">Estado:<br />
				<select name="estado">
				  <option <? if ($estado==1){ echo 'selected'; }?> value='1'>Desactivado</option>
				  <option <? if ($estado==0){ echo 'selected'; }?> value='0'>Activado</option>
				</select>									
			</label>
		</p>
		
		<p>
			<label class="description" for="fechainicio">Fecha inicio:<br />
				<input id="fechainicio" name="fechainicio" type="date" value="<?=$fechainicio?>"  />  
			</label>
		</p>
		
		<p>
			<label class="description" for="fechafin">Fecha final:<br />
				<input id="fechafin" name="fechafin" type="date" value="<?=$fechafin?>"  />  
			</label>
		</p>			
		
		<h4>Banners</h4>	
		<?
		
			$sqlbanners = "SELECT * FROM p_banners WHERE activo=1 ORDER BY seccion,orden";
			$resultbanners = posgre_query($sqlbanners);
			
			$anteriorseccion="";
			while ($rowbanner = pg_fetch_array($resultbanners)){
				$idbanner = $rowbanner['id'];
				$nombre = $rowbanner['nombre'];
				$seccion = $rowbanner['seccion'];
				$tipo = $rowbanner['tipo'];
				
				
				if ($id>0){
					$sqlbanneranuncio = "SELECT * FROM p_bannersanuncios WHERE idbanner='$idbanner' AND idanuncio='$id' AND borrado=0";
					$resultbanneranuncio = posgre_query($sqlbanneranuncio);
					$selected = "";
					if (pg_num_rows($resultbanneranuncio)>0){
						$selected = " checked ";
					}
				}
				
				if ($seccion!=$anteriorseccion){
					echo "<br><b>".$seccion."</b><br>";
					$anteriorseccion=$seccion;
				}
				
				$sqls = "SELECT * FROM p_stats WHERE idanuncio='$id' AND idbanner='$idbanner'";
				$results = posgre_query($sqls);
				$visitas = pg_num_rows($results);
				
				
				?> 
				<input <?=$selected?> id="banner_<?=$idbanner?>" name="banner_<?=$idbanner?>" type="checkbox"> <?=$nombre?>(<?=$visitas?>)<br>
				
				<? 
			}
		
		?>
		
		<br>
		<p>
			<label class="description" for="ostos">
				<input class="btn btn-primary btn-large" name="enviar" value="Guardar" type="submit" />
			</label>
		</p>
		</form>					
		
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