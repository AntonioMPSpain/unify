<?
include("_funciones.php"); 
include("_cone.php");
require_once('lib_actv_api.php');
$safe="Gestión de Cursos";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
////////// Filtros de nivel por usuario //////////////////////
session_start();

if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo sus cursos 
	header("Location:zona-privada_admin_cursos_1-profe.php");
	exit();
	/*if ($_SESSION[idcolegio]<>"") {
		$iddocente=strip_tags($_SESSION[idusuario]);
		$sql=" (iddocente='$iddocente') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[error]="No dispone de permisos";	
		header("Location: index.php?error=true&est=ko#1");
		exit();
	}*/
	
	
}elseif ($_SESSION[nivel]==5) { // Directivo
	$idcolegio=strip_tags($_SESSION[idcolegio]);
	if (($idcolegio<>'') && ($idcolegio<>0)){
		$sql="  (idcolegio='$idcolegio') AND ";
	}
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
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////
if($accion=="borrar"){
	$idcurso=strip_tags($_GET['idcurso']);
	$est="ko";
	if (($idcurso<>"")){
		$sqlasiog="UPDATE curso SET borrado='1',idmoodle='0' WHERE id= '$idcurso' ;";
		$linkb=iConectarse(); 
		$Query = pg_query($linkb,$sqlasiog); 
		if ($Query) $est="ok";
		$est_texto="Se ha eliminado de la web.";
		$idmoodle=strip_tags($_GET['idmoodle']);
		if (($idmoodle<>"") &&($idmoodle<>"0")){
			$estmod=borra_curso_moodle($idmoodle);
			if ($estmod=="1"){
				$est_texto="Se eliminado correctamente.";
				$est="ok";
			}elseif($estmod=="-1"){
				$est_texto="No se ha eliminado.";
				$est="ko";
			}
		}
		$_SESSION[error]=$est_texto;
	}
	header("Location: zona-privada_admin_cursos_1.php?est=$est");
	exit();
}


$titulo1="formación ";
$titulo2="administración";

$migas = array();
$migas[] = array('zona-privada_admin_cursos_1.php', 'Gestión de Cursos');

include("plantillaweb01admin.php");
 
$texto=strip_tags($_POST['texto']);

if (isset($_REQUEST['avanzada'])){
	$texto="avanzada";
}

$orden=strip_tags($_GET['orden']);
if($orden=="DESC"){
	$sqlorden="DESC";
}else{
	$orden="ASC";
	$sqlorden="ASC";
}
$sqlcampo="fecha_inicio";
$campo=strip_tags($_GET['campo']);
if ($campo<>""){
	$sqlcampo=$campo;
	if ($campo=="titulo"){ //tendriamos que comprobar todos los campos para que no hagan hack
		$sqlcampo="nombre";
	}
}

?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<? include("_aya_mensaje_session.php"); ?>
		
		<? if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2) ) { //Admin Total ?>
			<div class="bloque-lateral acciones">		
						<p>
							<a href="curso_alta.php" class="btn btn-success" type="button">Nuevo <i class="icon-plus"></i></a> |
							<!--<button class="btn btn-success" type="button">Nuevo desde plantilla <i class="icon-plus-sign"></i></button> |  
							<button class="btn " type="button">Seleccionar todo <i class="icon-ok"></i></button>  | 
							<button class="btn btn-warning" type="button">Eliminar <i class="icon-trash"></i></button> -->
						</p>
			</div>
			<!--fin acciones-->
			<? 
		} ?>
		
		
		<div class="bloque-lateral buscador">		
			<h4>Buscar curso</h4>
			<form action="zona-privada_admin_cursos_1.php?accion=buscar" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="título" value="<?=$texto?>" />
    					<input class="btn" type="submit" value="Buscar" />
						<a class="busqueda-avanzada-link" href="zona-privada_busqueda_avanzada.php">Búsqueda Avanzada <i class="icon-search"></i></a>
					
   				</div>		
			    </fieldset>
		    </form>
		</div>
		<br>
		<!--fin buscador-->

		<br>
		<h2>Cursos</h2>
		<table class="align-center">
		<tr>
			<th>IDCurso/IDCat.</th>
			<? /* <th><a href="zona-privada_admin_cursos_1.php?campo=fecha_fin_publicacion&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA FIN publicación</a></th> */ ?>
			<th><a href="zona-privada_admin_cursos_1.php?campo=fecha_inicio&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA INICIO</a></th>
			<th><a href="zona-privada_admin_cursos_1.php?campo=titulo&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">TÍTULO</a></th>
			<? if ($_SESSION[nivel]==1) { //Admin Total ?>
				<th>ORGANIZA</th>
			<? } ?>
			<th>MODALIDAD</th>
			<th>ESTADO</th>	
			<th>ALUMNOS</th>
			<th width='30%'>ACCIÓN</th>	
		</tr>
	<? 

//Paginacion 1
$pagina=strip_tags($_GET['pagina']);
$registros =25;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
} 
$link=iConectarse();
if (($accion=="buscar")&&($texto<>"")){
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 AND (sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')) ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 AND (sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')) ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros);	
}
elseif(isset($_REQUEST['avanzada'])){
	$avanzada = $_REQUEST['avanzada'];
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 $avanzada ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 $avanzada ORDER BY $sqlcampo  $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 
	
	echo pg_last_error();
	
	
}
else{
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 AND ((((fecha_fin + interval '1 day')>NOW())) OR (modalidad=3 AND estado=2)) ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 AND ((((fecha_fin + interval '1 day')>NOW())) OR (modalidad=3 AND estado=2)) ORDER BY $sqlcampo  $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}	
$cuantos = $total_registros;
//fin Paginacion 1
	while($row = pg_fetch_array($result)) { 
		$id=strip_tags($row["id"]);
		$idmoodle=$row["idmoodle"];				
		$linka=iConectarse(); 
		$resulta=pg_query($linka,"SELECT * FROM curso_usuario WHERE estado=0 AND espera=0 AND (modalidad<>2 OR modalidad is Null) AND nivel<>'3' AND idcurso='$id' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
		$cuantos=pg_num_rows($resulta);
		if ($row["modalidad"]==2){
		
			$linka2=iConectarse(); 
			$resulta2=pg_query($linka2,"SELECT * FROM curso_usuario WHERE estado=0 AND espera=0 AND modalidad=2 AND nivel<>'3' AND idcurso='$id' AND borrado=0 AND inscripciononlinepresencial=1;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
			$cuantos=pg_num_rows($resulta2);
			
			$linka2=iConectarse(); 
			$resulta2=pg_query($linka2,"SELECT * FROM curso_usuario WHERE estado=0 AND espera=0 AND modalidad=2 AND nivel<>'3' AND idcurso='$id' AND borrado=0 AND inscripciononlinepresencial=2;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
			$cuantos2=pg_num_rows($resulta2);
		}
		?>
		<tr>
			<td><?=$row["id"]?>/<?=$row["id_categoria_moodle"]?></td>
			<? /* <td><?=cambiaf_a_normal($row["fecha_fin_publicacion"])?></td> */ ?>
			<td><?=cambiaf_a_normal($row["fecha_inicio"])?></td>
			<td><a href='curso.php?id=<?=$id?>'><?=$row['nombre']?></a></td>
			<? if ($_SESSION[nivel]==1) { //Admin Total ?>
				<td><?
					$idcolegio=$row["idcolegio"];
						$consulta = "SELECT * FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;";
						$link=iConectarse(); 
						$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
						if($rowdg= pg_fetch_array($r_datos)) {	
							echo $rowdg['nombre'];
						}else{
							echo "No asignado";
						}?></td>
			<? }?>
			<td><? 
				$modalidad=$row["modalidad"];
				switch($modalidad) { 
				  case "0": $textomodalidad=" on-line "; break; 
				  case "1": $textomodalidad=" presencial "; break; 
				  case "2": $textomodalidad=" presencial y on-line "; break; 
				  case "3": $textomodalidad=" permanente "; break; 
				}
				echo $textomodalidad;
				?></td>
			<?
			/*
			<td><?
				if (($idmoodle<>"")&&($idmoodle<>"0")){	
					$iddocente=$row["iddocente"];
						$consulta = "SELECT * FROM usuario WHERE nivel='3' AND id='$iddocente' AND borrado = 0 ORDER BY id;";
						$link=iConectarse(); 
						$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
						if($rowdg= pg_fetch_array($r_datos)) {	
							echo $rowdg['nombre'];
							if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2) ) { //Admin Total ?>
								<br /><a href="asignar_docente.php?idcurso=<?=$row["id"]?>&idmoodle=<?=$row["idmoodle"]?>">[re-asignar]</a>
								<?
							}
						}else{
							if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2) ) { //Admin Total ?>
								<a href="asignar_docente.php?idcurso=<?=$row["id"]?>&idmoodle=<?=$row["idmoodle"]?>">[asignar]</a>
								<?
							}
						}
				}else{
							?><a>[asignar]</a><br />Debe estar activo en Moodle<?
				}?></td>
				*/?>
			<td><? 
			
				$estado=$row["estado"];
				$hoy = date('Y-m-d');
				$fecha_fin = $row["fecha_fin"];
				$fecha_inicio = $row["fecha_inicio"];
			
				if ($estado==2){	// Abierto
					if ($modalidad<>3){
						
						if (($fecha_inicio <= $hoy)&&($fecha_fin >= $hoy)){	
							$estadoabierto = 'En curso';
						}
						elseif ($fecha_inicio > $hoy){
							$estadoabierto = 'Pre curso';
						}
						elseif ($fecha_fin < $hoy){
							$estadoabierto = 'Finalizado';
						}
						
						
					}
					else{
						$estadoabierto = 'En curso';
					}
				}
			
				switch($estado) { 
				  case "0": echo " [OCULTO] "; break; 
				  case "1": echo " Cerrado "; break; 
				  case "2": echo " $estadoabierto "; break; 
				  case "5": echo " Cancelado "; break; 
				}
			?></td>
			<td><a href="zpa_usuario_curso.php?idcurso=<?=$row["id"]?>"><?
				if ($row["modalidad"]==2){
					?>Inscritos Presencial [<?
				}else{
					echo "Inscritos";
					?> [<?
				}
					if($cuantos>0) {	
						echo $cuantos;
					}else{
						echo "0";
					}?>]</a>
					<?
					if ($row["modalidad"]==2){
						?><br><a href="zpa_usuario_curso.php?cursodual&idcurso=<?=$row["id"]?>">Inscritos On-line [<?
						if($cuantos2>0) {	
							echo $cuantos2;
						}else{
							echo "0";
						}?>]</a>
						<?					
					}?></td>

			<td><? 
			if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2) ) { //Admin 
				if ($estado==0){ //cerrado o no activo 
						?>
						<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="zona-privada_admin_cursos_1.php?accion=borrar&idcurso=<?=$row["id"];?>&idmoodle=<?=$row["idmoodle"];?>" class="btn btn-primary">eliminar</a>
				<? }
				?>
				<a href="curso_alta.php?accion=editar&id=<?=$row["id"];?>&idmoodleduplica=<?=$row["idmoodle"];?>" class="btn btn-primary">editar</a>
				
				<? 
				$idcurso=$row["id"];
				//comprobamos que no esté ya asignado
				$consultapr = "SELECT * FROM curso_usuario WHERE nivel='3' AND  idcurso='$idcurso' AND idusuario='$idcolegio' AND borrado = 0;";
				$link=iConectarse(); 
				$r_datos=pg_query($link,$consultapr);// or die (mysql_error());  
				if(pg_num_rows($r_datos)>0){	//No se encuentra asignado a este curso, como profesor.			
					if (($idmoodle<>"")&&($idmoodle<>"0")){ //si el curso esta en moodle?>
							<a href="http://www.activatie.org/moodle/course/view.php?id=<?=$idmoodle?>" class="btn btn-primary">acceder moodle</a>
					<? }else{?>
							<a href="curso_activa_moodle.php?idcurso=<?=$row["id"]?>" class="btn btn-primary">activar moodle</a>
					<? }
				}
				if (($idmoodle<>"")&&($idmoodle<>"0")){	
					$iddocente=$row["iddocente"];
						$consulta = "SELECT * FROM usuario WHERE nivel='3' AND id='$iddocente' AND borrado = 0 ORDER BY id;";
						$link=iConectarse(); 
						$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
						if($rowdg= pg_fetch_array($r_datos)) {	
							//echo $rowdg['nombre'];
							if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2) ) { //Admin Total ?>
								<br /><a class="btn btn-primary" href="asignar_docente.php?idcurso=<?=$row["id"]?>&idmoodle=<?=$row["idmoodle"]?>">permisos moodle</a>
								<?
							}
						}else{
							if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2) ) { //Admin Total ?>
								<a class="btn btn-primary" href="asignar_docente.php?idcurso=<?=$row["id"]?>&idmoodle=<?=$row["idmoodle"]?>">permisos moodle</a>
								<?
							}
						}
				}else{
							/*?><a>[asignar]</a><br />Debe estar activo en Moodle<?*/
				}	
					
				$idcurso = $row["id"];	
				$sqlx = "SELECT * FROM encuestas WHERE borrado=0 AND idcurso='$idcurso' AND plantilla=1";
				$resultx = posgre_query($sqlx);
				
				if (pg_num_rows($resultx)==0){
					?> <a href="e_duplicar.php?idduplicar=1&c=<?=$row["id"];?>" class="btn btn-primary">generar encuesta</a> <?
				}

				?>
				
				<!--<a href="curso_asistencia.php?idcurso=<?=$row["id"];?>" class="btn btn-primary">asistencias</a>-->
				<a href="diploma.php?idcurso=<?=$row["id"];?>&idusuario=<?=14196?>" class="btn btn-primary">diploma</a>
				<!--<a onclick="return confirmar('&iquest;Publicar en TW y Facebook?')"   href="ay_tw_curso.php?idcurso=< ?=$row["id"];?>&accion=twitter&tipo=curso" class="btn btn-primary">TW y FACE</a>-->
				<!--<a href="admin_redes_sociales.php?tabla=curso&idtabla=<?=$row["id"];?>" class="btn btn-primary">redes sociales</a>-->
			<? }?>
			</td>
		</tr>
		<?
	}?>
		</table>

    <p class="align-center">Total: <?=$total_registros?> cursos</p>
			<?
			if($total_paginas > 1) { ?>
				<div class="pagination">
					<ul>
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="zona-privada_admin_cursos_1.php?avanzada=<?=$avanzada?>&pagina=<?=(1)?>&texto=<?=$texto?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="zona-privada_admin_cursos_1.php?avanzada=<?=$avanzada?>&pagina=<?=($pagina-1)?>&texto=<?=$texto?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="zona-privada_admin_cursos_1.php?avanzada=<?=$avanzada?>&pagina=<?=$i?>&texto=<?=$texto?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="zona-privada_admin_cursos_1.php?avanzada=<?=$avanzada?>&pagina=<?=($pagina+1)?>&texto=<?=$texto?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="zona-privada_admin_cursos_1.php?avanzada=<?=$avanzada?>&pagina=<?=$total_paginas?>&texto=<?=$texto?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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