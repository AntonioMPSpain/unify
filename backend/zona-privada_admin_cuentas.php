<?
include("_funciones.php"); 
include("_cone.php"); 
require_once('lib_actv_api.php');

$safe="Gestión de cuentas";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
$est_texto=strip_tags($_GET['est_texto']);
$est_texto2=strip_tags($_GET['est_texto2']);

$titulo1="formación ";
$titulo2="administración";
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	echo "Error: aqui no deberia entrar.";
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	echo "Error: aqui no deberia entrar.";
	exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	echo "Error: aqui no deberia entrar.";
	exit();
}elseif ($_SESSION[nivel]==1) { //Admin Total
	//echo "ok: aqui deberia entrar.";
	//exit();
}else{
	echo "Error: aqui no deberia entrar.";
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////
if($accion=="borrar"){
	$idc=strip_tags($_GET['idc']);
	$id_categoria_moodle=strip_tags($_GET['id_categoria_moodle']);
	$id_categoria_padre=2;
	$est="ko";
	if (($idc<>"")&&($id_categoria_moodle<>"")){
		/*$categoria =  lista_categorias_moodle(2);
		foreach($categoria as $row){
			echo $row['id']." - ".$row['nombre']." - ".$row['descripcion']."<br>";
		}*/
		$recursivo=0;
		$borrado = borra_categoria_moodle($id_categoria_moodle, $recursivo);
		$sqlasiog="UPDATE usuario SET borrado='1' WHERE $sql id= '$idc' ;";
		$linkb=iConectarse(); 
		$Query = pg_query($linkb,$sqlasiog); 
		if (($Query)&&($borrado==1)) {
			$est="ok";
			$est_texto="Se ha eliminado correctamente.";
			$est_texto2="Guardado";
		}	
	}elseif($idc<>""){
		$sqlasiog="UPDATE usuario SET borrado='1' WHERE $sql id= '$idc' ;";
		$linkb=iConectarse(); 
		$Query = pg_query($linkb,$sqlasiog); 
		if (($Query)&&($borrado==1)) {
			$est="ok";
			$est_texto="Se ha eliminado correctamente.";
			$est_texto2="Guardado";
		}	
	}
}
$migas = array();
$migas[] = array('zona-privada_admin_resumen.php', 'Gestión de cuentas');
include("plantillaweb01admin.php"); 
$texto=strip_tags($_POST['texto']);
?>
<!--Arriba pantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<? include("_aya_mensaje.php"); ?>
		<div class="bloque-lateral buscador">		
			<h4>Buscar Cuentas</h4>
			<form action="zona-privada_admin_cuentas.php?accion=buscar" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="profesores a buscar" />
    					<input class="btn" type="submit" value="Buscar" />
   				</div>		
			    </fieldset>
		    </form>
		</div>
		<!--fin buscador-->

		<div class="bloque-lateral acciones">		
   	 				<p>
   	 					<a href="categoria2.php" class="btn btn-success" type="button">Nuevo <i class="icon-plus"></i></a> |
   						<strong>Nota: Categorias que obliga Moodle</strong>
   	 				</p>
		</div>
		<!--fin acciones-->
		<table class="align-center">
		<tr>
			<th>ID/IDMOODLE</th>
			<th>LOGIN</th>
			<th>NOMBRE</th>
			<th>CURSOS</th>
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
$registros =50;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
} 
$link=iConectarse();
if (($accion=="buscar")&&($texto<>"")){
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='2' AND $sql borrado=0 AND nif ILIKE '%$texto%' ORDER BY nombre DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='2' AND  $sql borrado=0 AND nif ILIKE '%$texto%' ORDER BY nombre DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}else{
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='2' AND $sql borrado=0 ORDER BY nif DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='2' AND $sql borrado=0 ORDER BY nif DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}	
$cuantos = $total_registros;
//fin Paginacion 1

	while($row = pg_fetch_array($result)) { 
		?>
		<tr>
			<td><?=$row["id"]?>/<?=$row["id_categoria_moodle"]?>/<?=get_iduser_moodle($row["id"])?></td>
			<td><?=$row["nif"]?></td>
			<td><?
				// Genera
				/*$idcolegio=$row["idcolegio"];
					$consulta = "SELECT * FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;";
					$link=iConectarse(); 
					$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
					if($rowdg= pg_fetch_array($r_datos)) {	*/
						echo $row['nombre'];
					/*}else{
						echo "No asignado";
					}*/?></td>
			<td><?
				// Genera
				$id=strip_tags($row["id"]);
				$linka=iConectarse(); 
				$resulta=pg_query($linka,"SELECT id FROM curso WHERE idcolegio='$id' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
				echo $cuantos=pg_num_rows($resulta);
				?></td>
			<td><? 
				if ($row["confirmado"]==1){
					?><i class="icon-ok-sign" title="Activo">&nbsp;</i><?
				}else{
					?><i class="icon-ban-circle" title="Deshabilitado">&nbsp;</i><?
				}				
				?></td>
			<td>
				<a href="categoria2.php?accion=editar&id=<?=$row["id"];?>" class="btn btn-primary">editar</a>
				<? if ($row["confirmado"]==0){ ?>
					<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="zona-privada_admin_cuentas.php?accion=borrar&idc=<?=$row["id"];?>&id_categoria_moodle=<?=$row["id_categoria_moodle"];?>" class="btn btn-primary">eliminar</a>
				<? }?>				
			</td>
		</tr>
		<?
	}?>
		</table>

    <p class="align-center">Total: <?=$total_registros?> cuentas</p>
			<?
			if($total_paginas > 1) { ?>
				<div class="pagination">
					<ul>
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="zona-privada_admin_cuentas.php?pagina=<?=(1)?>&texto=<?=$texto?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="zona-privada_admin_cuentas.php?pagina=<?=($pagina-1)?>&texto=<?=$texto?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="zona-privada_admin_cuentas.php?pagina=<?=$i?>&texto=<?=$texto?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="zona-privada_admin_cuentas.php?pagina=<?=($pagina+1)?>&texto=<?=$texto?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="zona-privada_admin_cuentas.php?pagina=<?=$total_paginas?>&texto=<?=$texto?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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