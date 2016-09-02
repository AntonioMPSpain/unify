<?
include("_funciones.php"); 
include("_cone.php");
require_once('lib_actv_api.php');

$idencuesta = $_REQUEST['idencuesta'];
$idpregunta = $_REQUEST['idpregunta'];
$safe="Gestión de Profesores";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
$est_texto=strip_tags($_GET['est_texto']);
$est_texto2=strip_tags($_GET['est_texto2']);
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
/*
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[error]="No dispone de permisos";	
		header("Location: index.php?error=true&est=ko#2");
		exit();
	}
	*/
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql="  (idcolegio='$idcolegio') AND ";
		$sql="  ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[error]="No dispone de permisos";	
		header("Location: index.php?error=true&est=ko#2");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}
 
$titulo1="formación ";
$titulo2="administración";
$migas = array();
$migas[] = array('#', 'Gestión de Profesores');
include("plantillaweb01admin.php"); 
$texto=strip_tags($_POST['texto']);


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
	$sqlcampo="nombre";
}

?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
		<? include("_aya_mensaje_session.php"); 
		$_SESSION[error]="";
		?>
		<br />
		<div class="bloque-lateral buscador">		
			<h4>Buscar Profesores</h4>
			<form action="zona-privada_admin_profesores_1.php?accion=buscar" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="profesores a buscar" />
    					<input class="btn" type="submit" value="Buscar profesores" />
    				</div>		
			    </fieldset>
		    </form>
		</div>
		<!--fin buscador-->


		<table class="align-center">
		<tr>
			<th><a href="zona-privada_admin_profesores_1.php?orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>&campo=nombre">NOMBRE</a></th>
			<th><a href="zona-privada_admin_profesores_1.php?orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>&campo=apellidos">APELLIDOS</a></th>
			<th><a href="zona-privada_admin_profesores_1.php?orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>&campo=idcolegio">COLEGIO</a></th>	
			<th>CURSOS</th>
			<th>ACCIÓN</th>	
		</tr>
	<? 
//Paginacion 1
$pagina=strip_tags($_GET['pagina']);
$registros =10;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
} 
$link=iConectarse();
if (($accion=="buscar")&&($texto<>"")){
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND $sql borrado=0 AND ( sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')  OR  sp_asciipp(apellidos) ILIKE sp_asciipp('%$texto%') ) ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND  $sql borrado=0 AND ( sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')  OR  sp_asciipp(apellidos) ILIKE sp_asciipp('%$texto%'))  ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}else{
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND $sql borrado=0 ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND $sql borrado=0 ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}	
$cuantos = $total_registros;
//fin Paginacion 1

	while($row = pg_fetch_array($result)) { 
		?>
		<tr>
			<td><?=$row["nombre"]?></td>
			<td><?=$row["apellidos"]?></td>
			<td><?
				// Genera
				$idcolegio=$row["idcolegio"];
					$consulta = "SELECT * FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;";
					$link=iConectarse(); 
					$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
					if($rowdg= pg_fetch_array($r_datos)) {	
						echo $rowdg['nombre'];
					}else{
						echo "No asignado";
					}?></td>
			<td><?
				// Genera
				$id=strip_tags($row["id"]);
				$linka=iConectarse(); 
				$consulta = "SELECT c.id AS id,u.nombre AS nombre, u.apellidos AS apellidos FROM curso_docente_web AS c,usuario AS u WHERE c.idcurso IN (SELECT id FROM curso WHERE borrado=0) AND c.idusuario=u.id AND c.idusuario='$id' AND c.borrado = 0 AND u.borrado = 0 ORDER BY u.apellidos;";
				$resulta=pg_query($linka,$consulta) ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
				$cuantos=pg_num_rows($resulta);
				
				?> <a href='informe-cursosdeprofesor.php?idusuario=<?=$row["id"]?>'><?=$cuantos?></a> <?
				
				?></td>
			<td>
				
				<a href="e_opcion.php?p=<?=$row['id']?>&idencuesta=<?=$idencuesta?>&idpregunta=<?=$idpregunta?>" class="btn btn-primary">Seleccionar</a>				
			</td>
		</tr>
		<?
	}?>
		</table>

    <p class="align-center">Total: <?=$total_registros?> profesores</p>
			<?
			if($total_paginas > 1) { ?>
				<div class="pagination">
					<ul>
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="zona-privada_admin_profesores_1.php?pagina=<?=(1)?>&texto=<?=$texto?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="zona-privada_admin_profesores_1.php?pagina=<?=($pagina-1)?>&texto=<?=$texto?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="zona-privada_admin_profesores_1.php?pagina=<?=$i?>&texto=<?=$texto?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="zona-privada_admin_profesores_1.php?pagina=<?=($pagina+1)?>&texto=<?=$texto?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="zona-privada_admin_profesores_1.php?pagina=<?=$total_paginas?>&texto=<?=$texto?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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