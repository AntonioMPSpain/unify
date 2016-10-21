<?
include("_seguridadfiltro.php"); 
include("_funciones.php"); 
include("_cone.php"); 

$titulo1="ofertas de ";
$titulo2="trabajo";
$safe="configuracion";
$accion=strip_tags($_REQUEST['accion']);
if($accion=='borrar'){
	$id=strip_tags($_REQUEST['id']);
	if ($id==''){
		header("Location: index.php?salir=true");
		exit();
	}
	$_SESSION[esterror]="No se ha podido eliminar, datos incorrectos.";
	//Eliminar de BD
	$link=conectar(); //Postgrepsql
	$Query = pg_query($link,"UPDATE trabajo SET borrado='1' WHERE $sql id='$id' ;");
	if ($Query){
		$_SESSION[esterror]="Se ha eliminado correctamente.";
	}
	header("Location: admin_trabajo.php");
	exit();
}
if ($_GET['accion']=="estadoa1"){ // Cambiamos a estado ACTIVO
	$id=$_GET['id'];
	if (!is_numeric($id)){
		$id=$_POST['id'];
		if (!is_numeric($id)){
			$_SESSION[esterror]="No se puede guardar";
			header("Location: admin_trabajo.php");
			exit();
		}
	}
	$link=conectar(); //Postgrepsql
	$Query = pg_query($link,"UPDATE trabajo SET estado='1' WHERE $sql id=$id");// or die (mysql_error()); 
	$_SESSION[esterror]="Guardado correctamente.";
	header("Location: admin_trabajo.php");
	exit();
}

if ($_GET['accion']=="estadoa0"){ // Cambiamos a estado NO ACTIVO
	$id=$_GET['id'];
	if (!is_numeric($id)){
		$id=$_POST['id'];
		if (!is_numeric($id)){
			$_SESSION[esterror]="No se puede guardar";
			header("Location: admin_trabajo.php");
			exit();
		}
	}
	$link=conectar(); //Postgrepsql
	$Query = pg_query($link,"UPDATE trabajo SET estado='0' WHERE $sql id=$id");// or die (mysql_error()); 
	$_SESSION[esterror]="Guardado correctamente.";
	header("Location: admin_trabajo.php");
	exit();
}
//Paginacion 1
$pagina=$_GET['pagina'];
$registros = 100;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
} 
$link=conectar(); //Postgrepsql
$resultaa=pg_query($link,"SELECT * FROM trabajo WHERE borrado=0 ORDER BY fecha_insercion DESC,fecha DESC;") ;//or die ("Error en consulta. Contacte con Admin.".mysql_error());  
$total_registros = pg_num_rows($resultaa); 
$resultaa=pg_query($link,"SELECT * FROM trabajo WHERE borrado=0 ORDER BY fecha_insercion DESC,fecha DESC limit $registros offset $inicio;");// or die ("Error en consulta. Contacte con Admin.".mysql_error());  
$total_paginas = ceil($total_registros / $registros); 		  			

$bgcolor="#FFE3DD";	
include("plantillaweb01admin.php"); 
	?>
	<!--Arriba pantilla1-->
	<? include("_aya_mensaje_session.php"); ?>
	<h2 class="titulonoticia">Administrador Ofertas de trabajo</h2>
		<div class="bloque-lateral acciones">		
   	 				<p>
   	 					<!--<a href="admin_trabajo_boe.php" class="btn btn-warning" type="button">BOE <i class="icon-edit"></i></a> 
   	 					<a href="admin_trabajo_rss.php" class="btn btn-warning" type="button">Infoempleo <i class="icon-edit"></i></a>-->
   	 					<a href="__trabajo2.php" class="btn btn-success" type="button">Nuevo <i class="icon-plus"></i></a> 
   	 				</p>
		</div>
		<!--fin acciones-->
		<TABLE > 
			<tr>
				<th>Denominación</th>
				<th>Zona de trabajo</th>
				<!--<th>Contacto</th>-->
				<? if ($_SESSION[nivel]==1){ ?>
					<th>Publicado por</th>
				<? 	} ?>
				<th>Fecha límite<br />(Verde: No publicada)</th>
				<th>Fecha inserción</th>
				<th>Acción</td>
			</tr>
				
			<?
			while($row = pg_fetch_array($resultaa)) { 
				if ($bgcolor=="#FFE3DD"){
					$bgcolor="#FFffff";
				}else{
					$bgcolor="#FFE3DD";
				}?>				
				<tr>
					<td><? 
					if ($row["lugar"]==1){
						?><img src="css/pics/h1-teef.png" /><? 
					}?>
					<?=($row["denominacion"])?></td>
					<td><?=($row["zona"])?></td>
					<!--<td bgcolor=""><?=$row["persona"]?></td>-->
					
					<? if ($_SESSION[nivel]==1){ 
						$idcolegiot = $row["idcolegio"];
						$sql = "SELECT * FROM usuario WHERE id='$idcolegiot'";
						$resultt = posgre_query($sql);
						echo pg_last_error();
						if ($rowt = pg_fetch_array($resultt)){
							$nombrecolegio = $rowt["nombre"];
						}
						else{
							$nombrecolegio = "Admin";
						}
					?>
						<td><?=$nombrecolegio?></td>
					<? 	} ?>
					
					<td<? 
					$fecha=strftime("%d/%m/%Y");
					$fechabd=cambiaf_a_normal($row["fecha"]);
					//echo"-------------";
					$fechamas1=suma_fechas($fecha,1);
					//exit();
					if (compara_fechas($fecha,$fechabd)>0) echo ' bgcolor="#5BB75B"';
					?>><?=cambiaf_a_normal($row["fecha"])?></td>
					<td><?=cambiaf_a_normal($row["fecha_insercion"])?></td>
					<td>
						<? if (($_SESSION[nivel]==2)&&($row["idcolegio"]!=$_SESSION[idcolegio])){ } else {?>
						
						<a href="__trabajo2.php?id=<?=$row["id"]?>&accion=modificar&tipo=<?=$tipo?>"  class="btn btn-primary"> editar </a><?
						if ($row["estado"]==0){ ?>
							<a href="admin_trabajo.php?id=<?=$row["id"]?>&accion=estadoa1&tipo=<?=$tipo?>"  class="btn btn-success"> NO activado </a>
							<?
						}else{ ?>
							<a href="admin_trabajo.php?id=<?=$row["id"]?>&accion=estadoa0&tipo=<?=$tipo?>"  class="btn btn-primary">  activado </a>
							<?	
						}
						?>
						<a href="admin_trabajo.php?id=<?=$row["id"]?>&accion=borrar" onclick="return confirmar('¿Eliminar elemento?')"  class="btn btn-primary"> eliminar</a> 
						
						<!--<a href="admin_redes_sociales.php?tabla=trabajo&idtabla=<?=$row["id"];?>" class="btn btn-primary">Redes Sociales</a>--> 
							<!-- <a href="__email2_ofertas_de_trabajo.php?id=< ?=$row["id"]?>" class="a3"> <img src="generica/envio_mail.png" border="0" alt="envio_mail" /> </a>
							- <a href="#__email2_contenido.php?id=< ?=$row["id"]?>&amp;tipo=ofertas_trabajo"  class="btn btn-primary"> envio_mail </a>-->
						<? } ?>
					</td>
				</tr>
				<?
			} ?>
			</table>
			<?
			if($total_registros>$registros) { ?>
				<div class="pagination">
					<ul>
					<li><a href="admin_trabajo.php?pagina=<?=(1)?>&tp=<?=$tp?>&texto=<?=$texto?>&accion=<?=$accion?>" title="Ver primeros">Primeros</a></li> <?
				if(($pagina - 1) > 0) { ?>
					 <li><a href="admin_trabajo.php?pagina=<?=($pagina-1)?>&tp=<?=$tp?>&texto=<?=$texto?>&accion=<?=$accion?>" title="Ver anteriores">Anteriores</a></li>
					<?
				}
				$a=$pagina-3;
				$b=$pagina+3;
				for ($i=1; $i<=$total_paginas; $i++){ 
					if ($pagina == $i) {
						echo '<li class="disabled"><a>'.$pagina.'</a></li>'; 
					} else {
						if (($a<$i)&&($b>$i)){
							?>
							 <li><a href="admin_trabajo.php?pagina=<?=$i?>&tp=<?=$tp?>&texto=<?=$texto?>&accion=<?=$accion?>"><?=$i?></a></li>
							<?
						}
					}	
				}
				if(($pagina + 1)<=$total_paginas) { ?>
					<li><a href="admin_trabajo.php?pagina=<?=($pagina+1)?>&tp=<?=$tp?>&texto=<?=$texto?>&accion=<?=$accion?>" title="Ver siguientes">Siguientes</a></li>
					<?
				} ?>
				 <li><a href="admin_trabajo.php?pagina=<?=$total_paginas?>&tp=<?=$tp?>&texto=<?=$texto?>&accion=<?=$accion?>" title="Ver &uacute;ltimos">Últimos</a></li>
				</ul>
				</div>
				<hr />
				<?
			}
			?>
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
