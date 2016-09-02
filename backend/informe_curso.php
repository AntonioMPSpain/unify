<?
include("_funciones.php"); 
include("_cone.php"); 
include_once("a_curso_plazas_libres.php");
$safe="Gestión de Cursos";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);

////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo sus cursos 
	if ($_SESSION[idcolegio]<>"") {
		$iddocente=strip_tags($_SESSION[idusuario]);  
		$sql=" (iddocente='$iddocente') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
//		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////


$titulo1="formación ";
$titulo2="administración";

$migas = array();
$migas[] = array('zona-privada_admin_cursos_1.php', 'Gestión de Cursos');

include("plantillaweb01admin.php"); 
$texto=strip_tags($_POST['texto']);

$orden=strip_tags($_GET['orden']);
if($orden=="ASC NULLS FIRST"){
	$sqlorden="";
}else{
	$orden="DESC";
	$sqlorden="DESC NULLS FIRST";
}
$campo=strip_tags($_GET['campo']);
if ($campo=="titulo"){
	$sqlcampo="nombre";
}elseif ($campo=="fecha_inicio"){
	$sqlcampo="fecha_inicio";
}else{
	$sqlcampo="fecha_inicio";
}

 
//Paginacion 1
$pagina=strip_tags($_GET['pagina']);
$registros =30;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
}

$link=iConectarse();

if (($accion=="buscar")&&($texto<>"")){
	$sql1 = "SELECT * FROM curso WHERE $sql borrado=0 AND (sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')) ORDER BY $sqlcampo $sqlorden, id DESC";
	$result=pg_query($link,$sql1) ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$sql2 = "SELECT * FROM curso WHERE $sql borrado=0 AND (sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')) ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;";
	$result=pg_query($link,$sql2) ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros);	
}
elseif(isset($_REQUEST['avanzada'])){
	$avanzada = $_REQUEST['avanzada'];
	$sql1 = "SELECT * FROM curso WHERE $sql borrado=0 $avanzada ORDER BY $sqlcampo $sqlorden, id DESC";
	$result=pg_query($link,$sql1) ;//or die (pg_error());  
	$total_registros = pg_num_rows($result);
	$sql2 = "SELECT * FROM curso WHERE $sql borrado=0 $avanzada ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;";	
	$result=pg_query($link,$sql2) ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 
}
else{
	
	$sql1 = "SELECT * FROM curso WHERE $sql borrado=0 ORDER BY $sqlcampo $sqlorden, id DESC";
	$result=pg_query($link,$sql1) ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$sql2 = "SELECT * FROM curso WHERE $sql borrado=0 ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;";
	$result=pg_query($link,$sql2) ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}	

?>
<!--Arriba plantilla1-->
	<script>
		$(document).ready(function(){
			$('#checkinforme').change(function() {
				if($(this).is(":checked")) {
					$(".thinforme").css("display", "table-cell");
				}
				else{
					$(".thinforme").css("display", "none");
				}
		});
		});	
	</script>
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<div class="bloque-lateral acciones">		
	<p>
		<a href="zona-privada_admin_informes_1.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
	</div>
	
	<h2 class="titulonoticia">Informes por curso </h2>
		<br />
		<? include("_aya_mensaje_session.php"); ?>
		<div class="bloque-lateral buscador">		
			<h4>Buscar curso</h4>
			<form action="informe_curso.php?accion=buscar" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="título" value="<?=$texto?>" />
    					<input class="btn" type="submit" value="Buscar" />
						<a class="busqueda-avanzada-link" href="zona-privada_busqueda_avanzada.php?a=informe">Búsqueda Avanzada <i class="icon-search"></i></a>
					
   				</div>		
			    </fieldset>
		    </form>
		</div>
		
		<input checked type="checkbox" id="checkinforme" name="checkinforme" value="checkinforme">Mostrar informes<br>
		<a href="informe_curso_pdf.php?sql=<?=$sql1?>" title="resumen" class="btn btn-primary">Descargar PDF</a>
		
		<table class="align-center">
		<tr>
			<th>REF.</th>
			<th><a href="informe_curso.php?campo=fecha_inicio&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA INICIO</a></th>
			<th><a href="informe_curso.php?campo=titulo&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">TÍTULO</a></th>
			<th>ORGANIZA</th>
			<th>CONVOCATORIA</th>
			<th>ESTADO</th>	
			<th>TIPO</th>
			<th>INSCRITOS</th>
			<th>NºVISTOS</th>
			<th>GASTOS</th>
			<th>INGRESOS</th>
			<th class="thinforme" width='15%'>INFORMES</th>	
		</tr>
	<? 





$cuantos = $total_registros;
//fin Paginacion 1
	while($row = pg_fetch_array($result)) { 
		$id=strip_tags($row["id"]);
		$idmoodle=$row["idmoodle"];	
		$facturacomentario=$row["facturacomentario"];
		$devolucioncomentario=$row["devolucioncomentario"];				
		$linka=iConectarse(); 
		//$resulta=pg_query($linka,"SELECT * FROM curso_usuario WHERE estado=1 AND nivel='5' AND idcurso='$id' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
		$resulta=pg_query($linka,"SELECT * FROM curso_usuario WHERE nivel='5' AND estado=0 AND espera=0 AND (pagado=1 OR precio=0) AND idcurso='$id' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
		$cuantos=pg_num_rows($resulta);
		?>
		<tr>
			<td><?=$row["id"]?>/<?=$row["id_categoria_moodle"]?></td>
			<td><?=cambiaf_a_normal($row["fecha_inicio"])?></td>
			<td><?=$row["nombre"]?></td>
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
		
			<td>
			
			<?  if ($row["convocatoria"]==1) echo " Cíclico "; 
				if ($row["convocatoria"]==2) echo " Nuevo "; 
				if ($row["convocatoria"]==3) echo " Aplazado "; 
				if ($row["convocatoria"]==4) echo " Nuevo Aplazado "; 
			?>
			
			</td>
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
			<td><? 
				$modalidad=$row["modalidad"];
				switch($modalidad) { 
				  case "0": echo " on-line "; break; 
				  case "1": echo " presencial "; break; 
				  case "2": echo " presencial y on-line "; break; 
				  case "3": echo " permanente "; break; 
				}
				?></td>
		
			
			<td> <?
			
			
				if ($modalidad==2){
					$inscritosPresencial = getNumeroUsuariosCursoPresencial($row["id"]);
					$inscritosOnline= getNumeroUsuariosCursoOnline($row["id"]);
					
					echo "Presecial: $inscritosPresencial<br>";
					echo "On-line: $inscritosOnline<br>";
				}
				else{
					$inscritos = getNumeroUsuariosCurso($row["id"]);
					
					echo $inscritos;
				}
			?></td>
			<td>
			<?
				$idcurso = $row["id"];
				$rsv = posgre_query("SELECT * FROM visto WHERE idcurso='$idcurso'");
				$cv= pg_num_rows($rsv); 
				echo $cv;
			
			?>
			</td>
			<td>
			<?
				$sqlg = "SELECT * FROM curso_gastos WHERE borrado=0 AND idcurso='$idcurso'";
				$resultg = posgre_query($sqlg);
				
				$gastos = 0;
				while ($rowg = pg_fetch_array($resultg)){
					$gastos += $rowg['importe'];
				}
				
				echo $gastos."€";
			
				if ($_SESSION[nivel]==1){		
					if ($facturacomentario<>""){
						echo '<br><div style="margin-top:5px;" title="'.$facturacomentario.'"><em>comentario facturas</em></div>';
					}
				}
			
			?>
			</td>
			<td>
			<?
					
				$sql = "SELECT sum(precio) as recaudado FROM curso_usuario WHERE idcurso='$idcurso' AND estado=0 AND espera=0 AND pagado=1 AND nivel='5'  ";			
				$resultb = posgre_query($sql);
				
				if ($rowb = pg_fetch_array($resultb)){
					$recaudado = $rowb['recaudado'];

				}
				
				if ($recaudado==""){
					$recaudado=0;
				}
				
				echo $recaudado."€";
				
				if ($_SESSION[nivel]==1){
					if ($devolucioncomentario<>""){
						echo '<br><div style="margin-top:5px;" title="'.$devolucioncomentario.'"><em>comentario devolución</em></div>';
					}
				}
			?>
			</td>
			<td class="thinforme">
				<? if ($modalidad==2){ ?>
					
					<a href="informe_curso_inscritos.php?idcurso=<?=$row["id"]?>" class="btn btn-primary">Inscritos presencial</a><br />
					<a href="informe_curso_inscritos.php?cursodual=1&idcurso=<?=$row["id"]?>" class="btn btn-primary">Inscritos online</a><br />
				<? } 
				else { ?> 
				
					<a href="informe_curso_inscritos.php?idcurso=<?=$row["id"]?>" class="btn btn-primary">Inscritos</a><br />
				<? } ?>
			
				<a href="informe_curso_ingresos.php?idcurso=<?=$row["id"]?>" class="btn btn-primary">Ingresos</a><br />
				<a href="cursos_gastos.php?idcurso=<?=$row["id"]?>" class="btn btn-primary">Gastos</a><br />
				
				
				<?	if ($_SESSION[nivel]==1){	?>	
					<a href="informe_curso_reparto.php?idcurso=<?=$row["id"]?>" class="btn btn-primary">Reparto de beneficios</a><br />
				<? } ?>	
				<?
				
				/** Resultados encuesta **/
				
				$sql = "SELECT * FROM encuestas WHERE idcurso='$idcurso' AND borrado=0 AND plantilla=1";
				$resulte = posgre_query($sql);
				if ($rowe = pg_fetch_array($resulte)){
					$idencuesta = $rowe["id"];
					
					?><a href="e_resultados.php?id=<?=$idencuesta?>" class="btn btn-primary">Encuesta</a><br /><?
				}
				
				?>
			
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
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="informe_curso.php?avanzada=<?=$avanzada?>&pagina=<?=(1)?>&texto=<?=$texto?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="informe_curso.php?avanzada=<?=$avanzada?>&pagina=<?=($pagina-1)?>&texto=<?=$texto?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="informe_curso.php?avanzada=<?=$avanzada?>&pagina=<?=$i?>&texto=<?=$texto?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="informe_curso.php?avanzada=<?=$avanzada?>&pagina=<?=($pagina+1)?>&texto=<?=$texto?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="informe_curso.php?pagina=<?=$total_paginas?>&texto=<?=$texto?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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