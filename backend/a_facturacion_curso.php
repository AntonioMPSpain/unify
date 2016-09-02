<?
include("_funciones.php"); 
include("_cone.php"); 
include_once "a_facturas.php"; 
require_once('lib_actv_api.php');
$safe="Facturación: Cursos";
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


if (isset($_GET['exportar'])){
	$idcurso = $_GET['idcurso'];
	generarExcelFacturaCurso($idcurso);
		
	header("Location: a_facturacion.php?idcurso=$idcurso");
	exit();
}

if (isset($_GET['generar'])){
	$idcurso = $_GET['idcurso'];
	
	if ($idcurso<>""){
		$sql3="SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND espera=0 AND estado=0 AND pagado=1  AND (tipoinscripcion=1 OR tipoinscripcion=0) AND cargo1=0";
		$result3 = posgre_query($sql3);
		if (pg_num_rows($result3)>0){
			while ($row3 = pg_fetch_array($result3)){
				$idcursousuario = $row3['id'];
				$idusuario = $row3['idusuario'];
				$tipoinscripcion = $row3['tipoinscripcion'];
				
				$sql4 = "UPDATE curso_usuario SET cargo1='-1' WHERE id='$idcursousuario'";
				posgre_query($sql4);
				
				generarFactura($idusuario, 1, $idcurso, 0, $tipoinscripcion, 0);
			}
		}
	}
	
	header("Location: a_facturacion.php?idcurso=$idcurso");
	exit();
}


if (isset($_GET['cargo1'])){
	$idcurso = $_GET['idcurso'];
	
	if ($idcurso<>""){
		$sql3="SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND espera=0 AND estado=0 AND pagado=1 AND tipoinscripcion=2 AND cargo1=0";
		$result3 = posgre_query($sql3);
		if (pg_num_rows($result3)>0){
			while ($row3 = pg_fetch_array($result3)){
				$idcursousuario = $row3['id'];
				$idusuario = $row3['idusuario'];
				
				$sql4 = "UPDATE curso_usuario SET cargo1='1' WHERE id='$idcursousuario'";
				posgre_query($sql4);
				
				generarFactura($idusuario, 1, $idcurso, 0, 2, 1);
			}
		}
	}
	
	header("Location: a_facturacion.php?idcurso=$idcurso");
	exit();
}

if (isset($_GET['cargo2'])){
	$idcurso = $_GET['idcurso'];
	if ($idcurso<>""){
		$sql3="SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND espera=0 AND estado=0 AND pagado=1 AND tipoinscripcion=2 AND cargo2=0";
		$result3 = posgre_query($sql3);
		if (pg_num_rows($result3)>0){
			while ($row3 = pg_fetch_array($result3)){
				$idcursousuario = $row3['id'];
				$idusuario = $row3['idusuario'];
				
				$sql4 = "UPDATE curso_usuario SET cargo2='1' WHERE id='$idcursousuario'";
				posgre_query($sql4);
				
				generarFactura($idusuario, 1, $idcurso, 0, 2, 2);
			}
		}
	}
	
	header("Location: a_facturacion.php?idcurso=$idcurso");
	exit();
}

$titulo1="formación ";
$titulo2="administración";


include("plantillaweb01admin.php");
 
$texto=strip_tags($_POST['texto']);

if (isset($_REQUEST['avanzada'])){
	$texto="avanzada";
}

$orden=strip_tags($_GET['orden']);
if($orden=="ASC"){
	$sqlorden="ASC";
}else{
	$orden="DESC";
	$sqlorden="DESC";
}
$sqlcampo="fecha_inicio";
$campo=strip_tags($_GET['campo']);
if ($campo<>""){
	$sqlcampo=$campo;
	if ($campo=="titulo"){ //tendriamos que comprobar todos los campos para que no hagan hack
		$sqlcampo="nombre";
	}
	elseif ($campo=="fecha_inicio"){
		$sqlcampo="fecha_inicio";
	}
}

$sqlcurso = "";

if (isset($_GET['idcurso'])){
	$sqlcurso =  " AND id='".$_GET['idcurso']."' ";
}

?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<? include("_aya_mensaje_session.php"); ?>
		
		<div class="bloque-lateral buscador">		
			<h4>Buscar curso</h4>
			<form action="a_facturacion_curso.php?accion=buscar" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="título" value="<?=$texto?>" />
    					<input class="btn" type="submit" value="Buscar" />
						<a class="busqueda-avanzada-link" href="zona-privada_busqueda_avanzada.php?a=facturacion">Búsqueda Avanzada <i class="icon-search"></i></a>
					
   				</div>		
			    </fieldset>
		    </form>
		</div>
		<br>
		<!--Acciones-->
		<div class="bloque-lateral acciones">		
			<p>
				<a href="a_facturacion.php" class="btn btn-success" type="button">Todas las facturas </a> |
				<a href="a_facturacion_curso.php" class="btn btn-success" type="button">Facturas por curso </a> |
				<a href="a_facturacion_usuario.php" class="btn btn-success" type="button">Facturas por usuario </a> |

			</p>
		</div>
		<!--fin acciones-->
		
		<br>
		<!--fin buscador-->
		
		<br>
		<h2>Cursos</h2>
		<table class="align-center">
		<tr>
			<th>IDCurso/IDCat.</th>
			<th><a href="a_facturacion_curso.php?campo=fecha_inicio&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA INICIO</a></th>
			<th><a href="a_facturacion_curso.php?campo=titulo&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">TÍTULO</a></th>
			<? if ($_SESSION[nivel]==1) { //Admin Total ?>
				<th>ORGANIZA</th>
			<? } ?>
			<th>MODALIDAD</th>
			<th>ALUMNOS</th>
			<th width='25%'>ACCIÓN</th>	
		</tr>
	<? 

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
if (($accion=="buscar")&&($texto<>"")){
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 AND estado<>5 AND (sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')) ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 AND estado<>5 AND (sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')) ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros);	
}
elseif(isset($_REQUEST['avanzada'])){
	$avanzada = $_REQUEST['avanzada'];
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 $avanzada AND estado<>5 ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	
	echo pg_last_error();
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 $avanzada AND estado<>5 ORDER BY $sqlcampo  $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 
	
	
}
else{
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 $sqlcurso AND estado<>5 ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql borrado=0 $sqlcurso AND estado<>5 ORDER BY $sqlcampo  $sqlorden, id DESC") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}	
$cuantos = $total_registros;
//fin Paginacion 1
	while($row = pg_fetch_array($result)) { 
	
		$idcurso = $row["id"];
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
		
		
		$sql4 = "SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND pagado=1 AND precio>0";
		$result4 = posgre_query($sql4);
		
		if (pg_num_rows($result4)>0){ 
				
		
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
					$exportadofacturacion = $row["exportadofacturacion"];
					switch($modalidad) { 
					  case "0": $textomodalidad=" on-line "; break; 
					  case "1": $textomodalidad=" presencial "; break; 
					  case "2": $textomodalidad=" presencial y on-line "; break; 
					  case "3": $textomodalidad=" permanente "; break; 
					}
					echo $textomodalidad;
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

				
				<td> 
					<?
					
					$sql = "SELECT * FROM factura_factura WHERE idgenerica='$idcurso'";
					$result5 = posgre_query($sql);
					$numfacturas = pg_num_rows($result5);
					
					?>
					
					<a href="informe_curso_ingresos.php?idcurso=<?=$idcurso;?>" class="btn btn-success">informe</a>
					<a href="a_facturacion.php?idcurso=<?=$idcurso;?>" class="btn btn-success">ver <?=$numfacturas?> facturas</a>
					<a href="a_facturacion.php?idcurso=<?=$idcurso;?>&exportarrangosexcel" class="btn btn-success">descargar <?=$numfacturas?> facturas excel</a>
					<br>
					<?		
						$sql3="SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND espera=0 AND estado=0 AND pagado=1  AND (tipoinscripcion=1 OR tipoinscripcion=0) AND cargo1=0";
						$result3 = posgre_query($sql3);
						$numsingenerar = pg_num_rows($result3);
					
						if ($numsingenerar>0){
							?> <a onclick="return confirm('&iquest;Desea generar facturas?')" href="a_facturacion_curso.php?generar&idcurso=<?=$idcurso;?>" class="btn btn-primary">generar <?=$numsingenerar?> facturas transferencia y tarjeta</a> <?
						}
					
						$sql3="SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND espera=0 AND estado=0 AND pagado=1 AND tipoinscripcion=2 AND cargo1=0";
						$result3 = posgre_query($sql3);
						$numcargo1 = pg_num_rows($result3);
						if ($numcargo1>0){
							?> <a onclick="return confirm('&iquest;Desea generar facturas domiciliación cargo 1?')" href="a_facturacion_curso.php?cargo1&idcurso=<?=$idcurso;?>" class="btn btn-primary">generar <?=$numcargo1?> facturas domiciliación cargo 1</a> <?
					
						}
						
					
						$sql3="SELECT * FROM curso_usuario WHERE idcurso='$idcurso' AND espera=0 AND estado=0 AND pagado=1 AND tipoinscripcion=2 AND cargo2=0";
						$result3 = posgre_query($sql3);
						$numcargo2 = pg_num_rows($result3);
						
						if ($numcargo2>0){
							?> <a onclick="return confirm('&iquest;Desea generar facturas domiciliación cargo 2?')" href="a_facturacion_curso.php?cargo2&idcurso=<?=$idcurso;?>" class="btn btn-primary">generar <?=$numcargo2?> facturas domiciliación cargo 2</a> <?
				
						}
						
					
					?>
					
					
					<? 
						$sql = "SELECT * FROM factura_factura WHERE idgenerica='$idcurso' AND exportada=0 AND rectificativa!=2";						
						$result3 = posgre_query($sql);
						$numexportar = pg_num_rows($result3);
						if ($numexportar>0){
							?> <a onclick="return confirm('&iquest;Desea exportar?')" href="a_facturacion_curso.php?exportar&idcurso=<?=$row["id"];?>" class="btn btn-primary">exportar <?=$numexportar?> facturas</a> <?
						}
					 ?>
					
					
				
				</td>
			</tr>
		<? } ?>
		<?
	}?>
		</table>

	<? /*	
		
    <p class="align-center">Total: <?=$total_registros?> cursos</p>
			<?
			if($total_paginas > 1) { ?>
				<div class="pagination">
					<ul>
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="zona-privada_admin_cursos_1.php?avanzada=<?=$avanzada?>&pagina=<?=(1)?>&texto=<?=$texto?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="a_facturacion_curso.php?avanzada=<?=$avanzada?>&pagina=<?=($pagina-1)?>&texto=<?=$texto?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="a_facturacion_curso.php?avanzada=<?=$avanzada?>&pagina=<?=$i?>&texto=<?=$texto?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="zona-privada_admin_cursos_1.php?avanzada=<?=$avanzada?>&pagina=<?=($pagina+1)?>&texto=<?=$texto?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="a_facturacion_curso.php?avanzada=<?=$avanzada?>&pagina=<?=$total_paginas?>&texto=<?=$texto?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
					</ul>
				</div>
				<?
			}?>
			<!--FIN PAGINADOR-->			
		*/ ?>
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