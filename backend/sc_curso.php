<?
include("_funciones.php"); 
include("_cone.php"); 
$safe="Gestión de Cursos";
////////// Filtros de nivel por usuario //////////////////////
session_start();

if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sqlcolegio = " AND idcolegio='$idcolegio' ";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
}
else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario ////////////////////// 

$accion = $_REQUEST['accion'];
$idduplicar = $_REQUEST['idduplicar'];


$titulo1="gesti&oacute;n";
$titulo2="encuestas";
include("plantillaweb01admin.php"); 
?>
<!--Arriba pantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Seleccionar Curso</h2>
		<br />
		
		<? include("_aya_mensaje_session.php"); ?>
	
		<table class="align-center">
		<tr>
			<th>REF.</th>
			<th>FECHA INICIO</th>
			<th><a href="sc_curso.php?campo=titulo&orden=ASC">TÍTULO</a></th>
			<? if ($_SESSION[nivel]==1) { //Admin Total ?>
				<th>ORGANIZA</th>
			<? } ?>
			<th>TIPO</th>
			<th>ESTADO</th>	
			<th>ACCIÓN</th>	
		</tr>
	<? 


//Paginacion 1
$pagina=strip_tags($_GET['pagina']);
$registros =20;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
} 
$link=iConectarse();
if (($accion=="buscar")&&($texto<>"")){
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql  borrado=0 AND nombre ILIKE '%$texto%' $sqlcolegio ORDER BY fecha_fin_inscripcion DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql  borrado=0 AND nombre ILIKE '%$texto%' $sqlcolegio ORDER BY fecha_fin_inscripcion DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}else{
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql  borrado=0 $sqlcolegio ORDER BY fecha_fin_inscripcion DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql   borrado=0 $sqlcolegio ORDER BY fecha_fin_inscripcion DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}	
$cuantos = $total_registros;
//fin Paginacion 1
	while($row = pg_fetch_array($result)) { 
		$id=strip_tags($row["id"]);
		$idmoodle=$row["idmoodle"];				
		$linka=iConectarse(); 
		$resulta=pg_query($linka,"SELECT * FROM curso_usuario WHERE nivel<>'3' AND idcurso='$id' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
		$cuantos=pg_num_rows($resulta);
		?>
		<tr>
			<td><?=$row["id"]?>/<?=$row["id_categoria_moodle"]?></td>
			<td><?=cambiaf_a_normal($row["fecha_inicio"])?></td>
			<td><?=$row["nombre"]?></td>
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
				  case "0": echo " on-line "; break; 
				  case "1": echo " presencial "; break; 
				  case "2": echo " presencial y on-line "; break; 
				  case "3": echo " permanente "; break; 
				}
				?></td>
			<td><? 
				$estado=$row["estado"];
				switch($estado) { 
				  case "0": echo " Oculto "; break; 
				  case "1": echo " Cerrado "; break; 
				  case "2": echo " Abierto "; break; 
				  case "5": echo " Cancelado "; break; 
				}
				?></td>
			<td>
				<a href="sc_noconformidad.php?idcurso=<?=$row['id']?>" class="btn btn-primary">Seleccionar</a> 
				
				
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
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="sc_curso.php?pagina=<?=(1)?>&texto=<?=$texto?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="sc_curso.php?accion=<?=$accion?>&idduplicar=<?=$idduplicar?>&pagina=<?=($pagina-1)?>&texto=<?=$texto?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="sc_curso.php?accion=<?=$accion?>&idduplicar=<?=$idduplicar?>&pagina=<?=$i?>&texto=<?=$texto?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="sc_curso.php?accion=<?=$accion?>&idduplicar=<?=$idduplicar?>&pagina=<?=($pagina+1)?>&texto=<?=$texto?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="sc_curso.php?accion=<?=$accion?>&idduplicar=<?=$idduplicar?>&pagina=<?=$total_paginas?>&texto=<?=$texto?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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