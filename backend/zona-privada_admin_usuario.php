<?
header("Expires: Mon, 26 Jul 2000 05:00:00 GMT"); // La pagina ya expiró
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Fue modificada
header("Cache-Control: no-cache, must-revalidate"); // Evitar guardado en cache del cliente HTTP/1.1
header("Pragma: no-cache"); // Evitar guardado en cache del cliente HTTP/1.0

include("_funciones.php"); 
include("_cone.php");
require_once('lib_actv_api.php');
//exec("php /var/www/moodle/admin/cli/cron.php > /var/www/web/tmp/notas.txt");
//exec("php /var/www/moodle/auth/db/cli/sync_users.php > /var/www/web/tmp/notas2.txt");

$safe="Gestión de Usuarios";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[esterror]="Su sesion ha expirado";	
	header("Location: index.php?salir=true");
	exit();
}elseif ($_SESSION[nivel]==3) { //03/2015 Profes Ya no pueden ver. Profe puede ver solo los alumnos de su colegio
	$_SESSION[esterror]="Su sesion ha expirado";	
	header("Location: index.php?salir=true");
	exit();
	/*if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql=" (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
	}*/
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sql="";
}else{
	$_SESSION[esterror]="Su sesion ha expirado";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

if($accion=="borrar"){
	$id=strip_tags($_GET['id']);
	$a=strip_tags($_GET['e']);
	$b=strip_tags($_GET['n']);
	$est="ko";
	$est_texto="No eliminado.";
	if (($id<>"")){
		$est_texto=$est_texto." No puede eliminar a un usuario que no pertenece a su colegio.";
		$email=$a.'borrado'.date("YmdHis");
		$nif=$b.'borrado'.date("YmdHis");
		$sqlasiog="UPDATE usuario SET borrado='1',nif='$nif',email='$email' WHERE $sql id= '$id' ;";
		//exit();
		$linkb=iConectarse(); 
		$Query = pg_query($linkb,$sqlasiog); 
		if ($Query) {
			$est="ok";
			$est_texto="Se ha eliminado correctamente.";
			exec("php /var/www/moodle/admin/cli/cron.php > /var/www/web/tmp/notas.txt");
			exec("php /var/www/moodle/auth/db/cli/sync_users.php > /var/www/web/tmp/notas2.txt");
		}else{
			$est_texto="No se ha podido eliminar por restricciones de Moodle.";
		}	
	}
	$_SESSION[error]=$est_texto;

}
elseif ($accion=="xls-inactivos"){

	require_once dirname(__FILE__) . '/../librerias/PHPExcel/Classes/PHPExcel.php';
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$sql = "SELECT * FROM usuario WHERE borrado=0 AND pass IS NULL AND idcolegio='$idcolegio'";
	$result = posgre_query($sql);
	while($row = pg_fetch_array($result)){
	
		$idusuario = $row['idusuario'];
		$nif = $row['nif'];
		$nombre = $row['nombre'];
		$apellidos = $row['apellidos'];
		$email = $row['email'];
		$telefono = $row['telefono'];
		$telefono2 = $row['telefono2'];
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $nif);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nombre);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $apellidos);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $email);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $telefono);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $telefono2);
		$rowCount++;
		
	}
	
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$nombre = "usuarios-inactivos-".time();
	$objWriter->save('./files/'.$nombre.'.xls');
	
	$path = './files/'.$nombre.'.xls';
	ob_clean();
	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.basename($path).'"');
	header("Content-Transfer-Encoding: binary");
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Type: application/force-download");
	header("Content-Type: application/download");
	header("Content-Length: ".filesize($path));
	readfile($path);

}


$orden=strip_tags($_GET['orden']);
if($orden=="ASC"){
	$sqlorden="";
}else{
	$orden="DESC";
	$sqlorden="DESC";
}
$campo=strip_tags($_GET['campo']);
if ($campo=="nombre"){
	$sqlcampo="nombre";
}elseif ($campo=="apellidos"){
	$sqlcampo="apellidos";
}elseif ($campo=="idcolegio"){
	$sqlcampo="idcolegio";
}else{
	$sqlcampo="apellidos";
	$sqlorden="ASC";
}



$titulo1="formación ";
$titulo2="administración";
$migas = array();
$migas[] = array('#', 'Gestión de Usuarios');
include("plantillaweb01admin.php"); 
$texto=strip_tags($_REQUEST['texto']); 
$_SESSION[alumnoalta]="";
?>
<!--Arriba pantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<!--fin acciones-->
		<? include("_aya_mensaje_session.php"); ?>
		<br />
		<div class="bloque-lateral buscador">		
			<h4>Buscar usuario</h4>
			<form action="zona-privada_admin_usuario.php" method="post" enctype="multipart/form-data" >
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="nombre, apellidos, nif o email" value="<?=$texto?>" />
    					<input class="btn" type="submit" value="Buscar" />
						<? 
						if ($_SESSION[nivel]==1) { //
							?>
							<!--<a class="busqueda-avanzada-link" href="#zona-privada_admin_alumnos_2_busqueda-avanzada.php">Búsqueda Avanzada <i class="icon-search"></i></a>-->
							<?
						}?>
   					</div>		
			    </fieldset>
		    </form>
			<div>Nota: sólo puede editar usuarios de su colegio y usuarios no colegiados.</div>
		</div>
		<br>
		<!--fin buscador-->
		<? 
		if ($_SESSION[nivel]<>3) { //
			?>
			<div class="bloque-lateral acciones">		
   	 				<p>
   	 					<a href="alumno_alta.php" class="btn btn-success" type="button">Nuevo <i class="icon-plus"></i></a> 
						<? if ($_SESSION['nivel']==2){ ?>
   	 					<a href="zona-privada_admin_usuario_altas.php" class="btn btn-success" type="button">Altas masivas <i class="icon-plus"></i></a> 
   	 					<a href="zona-privada_admin_usuario_altas.php?tipo=baja" class="btn btn-success" type="button">Bajas masivas <i class="icon-minus"></i></a> 
   	 					<a href="zona-privada_admin_usuario.php?accion=xls-inactivos" class="btn btn-success" type="button">Descargar XLS usuarios inactivos <i class="icon-minus"></i></a>
						<? } ?>
   	 				</p>
			</div>
			<!--fin acciones-->
			<?
		}?>
		<table class="align-center">
		<tr>
			<th><a href="zona-privada_admin_usuario.php?orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>&campo=nif">NIF</a></th>
			<th><a href="zona-privada_admin_usuario.php?orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>&campo=nombre">NOMBRE</a></th>
			<th><a href="zona-privada_admin_usuario.php?orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>&campo=apellidos">APELLIDOS</a></th>
			<th>EMAIL</th>
			<th>TLF</th>
			<th>TIPO</th>
			<th>CURSOS</th>
			<th>ESTADO</th>	
			<th>ACCIÓN</th>	
		</tr>
	<? 
$ver=strip_tags($_REQUEST['ver']); 
$sqlver="";
if ($ide_p<>""){
	$sqlver=" (idmoodle='0') AND ";
}
//Filtros de areas, etiquetas...
$ide_p=strip_tags($_GET['ide_p']); //Etiqueta Provincia
if ($ide_p<>""){
	$sql=" (idetiqueta_provincia='$ide_p') AND ";
	$textoh1="Filtro de etiqueta Provincia";
}

//Paginacion 1
$pagina=strip_tags($_GET['pagina']);
$registros =50;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
} 
$link=iConectarse();
if ($texto<>""){
	$texto=strval($texto);
 	$sqlp="  ( sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')  OR  sp_asciipp(apellidos) ILIKE sp_asciipp('%$texto%') OR  sp_asciipp(nif) ILIKE sp_asciipp('%$texto%') OR  sp_asciipp(email) ILIKE sp_asciipp('%$texto%') ) ";
	//$sqlp=" ( sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')  OR  sp_asciipp(apellidos) ILIKE sp_asciipp('%$texto%') OR    TRIM(TO_CHAR(nif, '9999999999')) LIKE '%$texto%' ) ";
//	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='4' AND $sql borrado=0 $sqlp  ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$result=pg_query($link,"SELECT * FROM usuario WHERE (nivel='4' OR nivel='3' OR nivel='0')  AND  $sqlp  ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
//	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='4' AND $sql borrado=0 $sqlp  ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$result=pg_query($link,"SELECT * FROM usuario WHERE (nivel='4' OR nivel='3' OR nivel='0') AND $sqlp  ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}else{
	$result=pg_query($link,"SELECT * FROM usuario WHERE (nivel='4' OR nivel='3' OR nivel='0')  AND $sql borrado=0 ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM usuario WHERE (nivel='4' OR nivel='3' OR nivel='0')  AND $sql borrado=0 ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}	
$cuantos = $total_registros;
//fin Paginacion 1

	while($row = pg_fetch_array($result)) { 
		?>
		<tr>
			<td><?=$row["nif"]?></td>
			<td><?=$row["nombre"]?></td>
			<td><?=$row["apellidos"]?></td>
			<td><?=$row["email"]?></td>
			<td><?=$row["telefono"]?></td>
			<td><? 
				if ($row["tipo"]==1){
					?>Colegiado<?
				}elseif ($row["tipo"]==2){
					?>Precolegiado<?
				}elseif ($row["tipo"]==3){
					?>Estudiante<?
				}elseif ($row["tipo"]==4){
					?>No colegiado<?
				}else{
					?>[sin activar]<?
				}				
				if ($_SESSION[nivel]==1) { //Admin Total
					$idcolegio=$row["idcolegio"];
					$linka=iConectarse(); 
					$resulta=pg_query($linka,"SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0") ;//or die (pg_error());  
					$rowa = pg_fetch_array($resulta);
					echo " ".$rowa["nombre"];
				}
				?>
			</td>
			<td><?
				// Genera
				$id=strip_tags($row["id"]);
				$linka=iConectarse(); 
				$resulta=pg_query($linka,"SELECT cu.id FROM curso_usuario as cu,curso as c WHERE cu.idcurso=c.id AND cu.idusuario='$id' AND cu.borrado=0 AND c.borrado=0 AND cu.nivel=5") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
				$cuantos=pg_num_rows($resulta);
				if ($cuantos>0){
					?><a href="informe-cursosdeusuario.php?idusuario=<?=$id?>"><?=$cuantos?></a><?
				}else{
					echo "0";
				}
				?></td>
			<td><? 
				if (($row["pass"]<>"")&&($row["confirmado"]==1)){
					?><i class="icon-ok-sign" title="Activo">&nbsp;</i><?
				}else{
					?><i class="icon-ban-circle" title="Cuenta no activada">&nbsp;</i><?
				}				
				?></td>
			<td>
				<? 
				if (($row["idcolegio"]==$_SESSION[idcolegio])||(($_SESSION[nivel]==1))){ ?>
						<? if ($row["borrado"]==0){ ?>
							<a href="alumno_alta.php?accion=editar&id=<?=$row["id"];?>" class="btn btn-primary">editar</a>	

							<? /*if ($row["confirmado"]==0){ ?>
								<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="zona-privada_admin_usuario.php?accion=borrar&id=<?=$row["id"];?>&e=<?=$row["email"];?>&n=<?=$row["nif"];?>" class="btn btn-primary">eliminar</a>
							<? }*/?>	
							
									
						<? }elseif(($row["borrado"]==1) && ($row["baja"]==1)){?>		
								(baja el <?=cambiaf_a_normal($row["fechabaja"])?>)
						<? }else{?>	
								(eliminado)			
						<? }?>				
					<? }else{
						 if (($row["borrado"]==0)||($row["baja"]==0)){ 
							$idcolegio=$row["idcolegio"];
							if ($idcolegio>0){
								$linka=iConectarse(); 
								$resulta=pg_query($linka,"SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0") ;//or die (pg_error());  
								$rowa = pg_fetch_array($resulta);
								echo " ".$rowa["nombre"];
							}	
							else{
								 if ($_SESSION[nivel]==2){ ?>
									<a href="alumno_alta.php?accion=editar&id=<?=$row["id"];?>" class="btn btn-primary">editar</a>	
								<?
								}
							}			
								
						}else{?>	
							(eliminado)			
						<? }			
				}?>				
			</td>
		</tr>
		<?
	}?>
		</table>

    <p class="align-center">Total: <?=$total_registros?> usuarios</p>
			<?
			if($total_paginas > 1) { ?>
				<div class="pagination">
					<ul>
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="zona-privada_admin_usuario.php?pagina=<?=(1)?>&texto=<?=$texto?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="zona-privada_admin_usuario.php?pagina=<?=($pagina-1)?>&texto=<?=$texto?>" title="Ver anteriores Resultados">Anteriores</a></li>
							<?
						}
						$a=$pagina-2;
						$b=$pagina+2;
						for ($i=1; $i<=$total_paginas; $i++){ 
							if ($pagina == $i) {
								?><li class="disabled"><a><?=$pagina?></a></li><?
							} else {
								if (($a<$i)&&($b>$i)){
									?>
									<li><a href="zona-privada_admin_usuario.php?pagina=<?=$i?>&texto=<?=$texto?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="zona-privada_admin_usuario.php?pagina=<?=($pagina+1)?>&texto=<?=$texto?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="zona-privada_admin_usuario.php?pagina=<?=$total_paginas?>&texto=<?=$texto?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
					</ul>
				</div>
				<?
			}?>
			<!--FIN PAGINADOR-->			

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