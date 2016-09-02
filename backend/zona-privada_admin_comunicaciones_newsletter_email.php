<?
include("_funciones.php"); 
include("_cone.php"); 
$safe="Gestión de Cursos";
$accion=strip_tags($_GET['accion']); 
$hueco=strip_tags($_GET['hueco']);

if (isset($_GET['varios'])){
	$varios_cursos=true;
	$varios="varios";
}
else{
	$varios="";
	$varios_cursos=false;
}
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	echo "Error: aqui no deberia entrar.";
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo sus cursos 
	if ($_SESSION[idcolegio]<>"") {
		$iddocente=strip_tags($_SESSION[idusuario]);
		$sql=" (iddocente='$iddocente') AND ";
		$textoinfo="Soy profe";
	}else{
		echo "Error de sesion1";
		exit();
	}
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		echo "Error de sesion2";
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
	echo "Error: aqui no deberia entrar.";
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$titulo1="formación ";
$titulo2="administración";


include("plantillaweb01admin.php"); 
?>
<!--Arriba pantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Inserta Curso</h2>
		<br />
		
		<div class="bloque-lateral acciones">		
			<p><strong>Acciones:</strong>
				<a class="btn btn-success" href="zona-privada_admin_comunicaciones_newsletter.php">Volver <i class="icon-circle-arrow-left"></i></a> <br />
			</p>
		</div>
		<!--fin acciones-->
		<? include("_aya_mensaje_session.php"); ?>
		<div class="bloque-lateral buscador">		
			<h4>Buscar cursos</h4>
			<form action="zona-privada_admin_comunicaciones_newsletter_email.php?accion=buscar" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="Cursos a buscar" />
    					<input class="btn" type="submit" value="Buscar Cursos" />
   				</div>		
			    </fieldset>
		    </form>
		</div>
		<!--fin buscador-->
		<table class="align-center">
		<tr>
			<th>REF.</th>
			<th>FECHA INICIO</th>
			<th><a href="zona-privada_admin_com1email.php?campo=titulo&orden=ASC">TÍTULO</a></th>
			<? if ($_SESSION[nivel]==1) { //Admin Total ?>
				<th>ORGANIZA</th>
			<? } ?>
			<th>TIPO</th>
			<th>ACCIÓN</th>	
		</tr>
	<? 

//Filtros de areas, etiquetas...
$ide_p=strip_tags($_GET['ide_p']); //Etiqueta Provincia
if ($ide_p<>""){
	$sql=" (idetiqueta_provincia='$ide_p') AND ";
	$textoh1="Filtro de etiqueta Provincia";
}

$curso1 = $_REQUEST['curso1'];
$curso2 = $_REQUEST['curso2'];
$curso3 = $_REQUEST['curso3'];
$curso4 = $_REQUEST['curso4'];
$curso5 = $_REQUEST['curso5'];
$curso6 = $_REQUEST['curso6'];
$publi1 = $_REQUEST['publi1'];
$publi2 = $_REQUEST['publi2'];
$publi3 = $_REQUEST['publi3'];
$trabajo1 = $_REQUEST['trabajo1'];
$trabajo2 = $_REQUEST['trabajo2'];
$trabajo3 = $_REQUEST['trabajo3'];

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
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql estado=2 AND borrado=0 AND nombre ILIKE '%$texto%' ORDER BY fecha_inicio DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql estado=2 AND borrado=0 AND nombre ILIKE '%$texto%' ORDER BY fecha_inicio DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}else{
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql estado=2 AND borrado=0 AND  ((modalidad!=3 AND fecha_inicio>='NOW()') OR modalidad=3) ORDER BY fecha_inicio DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql estado=2 AND  borrado=0 AND ((modalidad!=3 AND fecha_inicio>='NOW()') OR modalidad=3) ORDER BY fecha_inicio DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
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

			<td>
				<?
					if ($hueco==1){
						$curso1=$row['id'];
					}
					elseif ($hueco==2){
						$curso2=$row['id'];
					}
					elseif ($hueco==3){
						$curso3=$row['id'];
					}
					elseif ($hueco==4){
						$curso4=$row['id'];
					}
					elseif ($hueco==5){
						$curso5=$row['id'];
					}
					elseif ($hueco==6){
						$curso6=$row['id'];
					}
					
					
				if (!$varios_cursos){	
					?>
					<a href="zona-privada_admin_comunicaciones_newsletter.php?curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary">Insertar en newsletter</a>
				<? } else { ?>
					<a href="zona-privada_admin_comunicaciones_varios_cursos.php?curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>" class="btn btn-primary">Insertar en email</a>
					
					
					
				<? } ?>
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
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=<?=$hueco?>&<?=$varios?>&pagina=<?=(1)?>&texto=<?=$texto?>&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=<?=$hueco?>&<?=$varios?>&pagina=<?=($pagina-1)?>&texto=<?=$texto?>&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=<?=$hueco?>&<?=$varios?>&pagina=<?=$i?>&texto=<?=$texto?>&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=<?=$hueco?>&<?=$varios?>&pagina=<?=($pagina+1)?>&texto=<?=$texto?>&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=<?=$hueco?>&<?=$varios?>&pagina=<?=$total_paginas?>&texto=<?=$texto?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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