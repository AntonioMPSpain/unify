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
if($accion=="borrar"){
	$idcurso=strip_tags($_GET['idcurso']);
	$est="ko";
	if (($idcurso<>"")){
		$sqlasiog="UPDATE curso SET borrado='1',idmoodle='0' WHERE $sql id= '$idcurso' ;";
		$linkb=iConectarse(); 
		$Query = pg_query($linkb,$sqlasiog); 
		if ($Query) $est="ok";
		$est_texto="Se ha eliminado de la web.";
		$est_texto2="No se ha encontrado en moodle";		
		$idmoodle=strip_tags($_GET['idmoodle']);
		if (($idmoodle<>"") &&($idmoodle<>"0")){
			$estmod=borra_curso_moodle($idmoodle);
			if ($estmod=="1"){
				$est_texto="Se eliminado correctamente.";
				$est_texto2="Guardado";
				$est="ok";
			}elseif($estmod=="-1"){
				$est_texto="No se ha eliminado.";
				$est_texto2="No guardado";
				$est="ko";
			}
		}
	}
	header("Location: zona-privada_admin_cursos_1_email.php?est=$est&est_texto=$est_texto&est_texto2=$est_texto2");
	exit();
}


$titulo1="formación ";
$titulo2="administración";


include("plantillaweb01admin.php"); 
$texto=strip_tags($_POST['texto']);
?>
<!------------Arriba pantilla1---------->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Comunicaciones Cursos</h2>
		<br />
		
		<div class="bloque-lateral acciones">		
			<p><strong>Acciones:</strong>
				<a class="btn btn-success" href="zona-privada_admin_comunicaciones_5_historico-de-envios.php">Histórico de Mensajes Enviados <i class="icon-calendar"></i></a> <br />
			</p>
		</div>
		<!--fin acciones-->
		<? include("_aya_mensaje_session.php"); ?>
		<div class="bloque-lateral buscador">		
			<h4>Buscar cursos</h4>
			<form action="zona-privada_admin_cursos_1_email.php?accion=buscar" method="post">
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
			<th><a href="zona-privada_admin_cursos_1_email.php?campo=titulo&orden=ASC">TÍTULO</a></th>
			<? if ($_SESSION[nivel]==1) { //Admin Total ?>
				<th>ORGANIZA</th>
			<? } ?>
			<th>TIPO</th>
			<th>ESTADO</th>	
			<th>ACCIÓN</th>	
		</tr>
	<? 

//Filtros de areas, etiquetas...
$ide_p=strip_tags($_GET['ide_p']); //Etiqueta Provincia
if ($ide_p<>""){
	$sql=" (idetiqueta_provincia='$ide_p') AND ";
	$textoh1="Filtro de etiqueta Provincia";
}

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
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql estado=2 AND borrado=0 AND nombre ILIKE '%$texto%' ORDER BY fecha_fin_inscripcion DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql estado=2 AND borrado=0 AND nombre ILIKE '%$texto%' ORDER BY fecha_fin_inscripcion DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}else{
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql estado=2 AND borrado=0 ORDER BY fecha_fin_inscripcion DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM curso WHERE $sql estado=2 AND  borrado=0 ORDER BY fecha_fin_inscripcion DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
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
				}
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
				switch($estado) { 
				  case "0": echo " [precurso] "; break; 
				  case "1": echo " Cerrado "; break; 
				  case "2": echo " Abierto "; break; 
				  case "3": echo " En curso "; break; 
				}
				?></td>
			<td>
				<a href="zona-privada_admin_comunicaciones_1.php?idcurso=<?=$row["id"]?>" class="btn btn-primary">Enviar correo</a>
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
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="zona-privada_admin_cursos_1_email.php?pagina=<?=(1)?>&texto=<?=$texto?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="zona-privada_admin_cursos_1_email.php?pagina=<?=($pagina-1)?>&texto=<?=$texto?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="zona-privada_admin_cursos_1_email.php?pagina=<?=$i?>&texto=<?=$texto?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="zona-privada_admin_cursos_1_email.php?pagina=<?=($pagina+1)?>&texto=<?=$texto?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="zona-privada_admin_cursos_1_email.php?pagina=<?=$total_paginas?>&texto=<?=$texto?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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