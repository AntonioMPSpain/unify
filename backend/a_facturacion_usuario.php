<?
header("Expires: Mon, 26 Jul 2000 05:00:00 GMT"); // La pagina ya expiró
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Fue modificada
header("Cache-Control: no-cache, must-revalidate"); // Evitar guardado en cache del cliente HTTP/1.1
header("Pragma: no-cache"); // Evitar guardado en cache del cliente HTTP/1.0

include("_funciones.php"); 
include("_cone.php"); 
require_once('lib_actv_api.php');

$safe="Facturación: Usuarios";
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


$orden=strip_tags($_GET['orden']);
if($orden=="ASC"){
	$sqlorden="";
}else{
	$orden="DESC";
	$sqlorden="DESC";
}
$campo=strip_tags($_GET['campo']);
if ($campo=="nombre"){
	$sqlcampo="U.nombre";
}elseif ($campo=="apellidos"){
	$sqlcampo="U.apellidos";
}elseif ($campo=="idcolegio"){
	$sqlcampo="U.idcolegio";
}else{
	$sqlcampo="U.apellidos";
	$sqlorden="ASC";
}

$sqlusuario="";
if (isset($_GET['idusuario'])){
	$idusuario = $_GET['idusuario'];
	$sqlusuario = " AND id='$idusuario' ";
}

$titulo1="formación ";
$titulo2="administración";

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
			<h4>Buscar usuario o empresa</h4>
			<form action="a_facturacion_usuario.php" method="post" enctype="multipart/form-data" >
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="Usuario: nombre, apellidos, nif o email. Empresa: cif o nombre" value="<?=$texto?>" />
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
		<h2>Usuarios</h2>
		<table class="align-center">
		<tr>
			<th>CÓDIGO</th>
			<th><a href="a_facturacion_usuario.php?orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>&campo=nif">NIF</a></th>
			<th><a href="a_facturacion_usuario.php?orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>&campo=nombre">NOMBRE</a></th>
			<th><a href="a_facturacion_usuario.php?orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>&campo=apellidos">APELLIDOS</a></th>
			<th>EMAIL</th>
			<th>TLF</th>
			<th>TIPO</th>
			<th>DATOS DE FACTURACIÓN</th>
			<!--<th>DATOS DE DOMICILIACIÓN</th>-->
			<th>FACTURAS</th>
			<th>CURSOS</th>
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
if ($texto<>""){
	$texto=strval($texto);
 	$sqlp="  ( sp_asciipp(U.nombre) ILIKE sp_asciipp('%$texto%')  OR  sp_asciipp(U.apellidos) ILIKE sp_asciipp('%$texto%') OR  sp_asciipp(U.nif) ILIKE sp_asciipp('%$texto%') OR  sp_asciipp(U.email) ILIKE sp_asciipp('%$texto%') ) ";
	//$sqlp=" ( sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')  OR  sp_asciipp(apellidos) ILIKE sp_asciipp('%$texto%') OR    TRIM(TO_CHAR(nif, '9999999999')) LIKE '%$texto%' ) ";
//	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='4' AND $sql borrado=0 $sqlp  ORDER BY $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	
	$sql = "(SELECT * FROM usuario U WHERE (U.nivel='4' OR U.nivel='3' OR nivel='9') AND $sqlp ORDER BY $sqlcampo $sqlorden, U.id DESC) UNION (SELECT * FROM usuario U2 WHERE U2.id IN (SELECT idusuario FROM factura_comprador F WHERE sp_asciipp(F.\"Individual_Name\") ILIKE sp_asciipp('%$texto%') OR sp_asciipp(F.\"buy_TaxIdentificationNumber\") ILIKE sp_asciipp('%$texto%')))   ;";
	$result=pg_query($link,$sql) ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
//	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='4' AND $sql borrado=0 $sqlp  ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$sql = "(SELECT * FROM usuario U WHERE (U.nivel='4' OR U.nivel='3' OR nivel='9') AND $sqlp ORDER BY $sqlcampo $sqlorden, U.id DESC OFFSET $inicio LIMIT $registros) UNION (SELECT * FROM usuario U2 WHERE U2.id IN (SELECT idusuario FROM factura_comprador F WHERE sp_asciipp(F.\"Individual_Name\") ILIKE sp_asciipp('%$texto%') OR sp_asciipp(F.\"buy_TaxIdentificationNumber\") ILIKE sp_asciipp('%$texto%')))  ;";
	$result=pg_query($link,$sql) ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}else{
	$sql = "SELECT * FROM usuario U WHERE (nivel='4' OR nivel='3' OR nivel='9') $sqlusuario AND borrado=0 ORDER BY $sqlcampo $sqlorden, id DESC";
	$result=pg_query($link,$sql) ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM usuario U WHERE (nivel='4' OR nivel='3' OR nivel='9') $sqlusuario AND borrado=0 ORDER BY $sqlcampo $sqlorden, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 
}	
$cuantos = $total_registros;
//fin Paginacion 1

	while($row = pg_fetch_array($result)) { 
		$id=strip_tags($row["id"]);
		?>
		<tr>
			<td><?=$id?></td>
			<td><?=$row["nif"]?></td>
			<td><?=$row["nombre"]?></td>
			<td><?=$row["apellidos"]?></td>
			<td><?=$row["email"]?></td>
			<td><?=$row["telefono"]?></td>
			<td><? 
				if ($row["tipo"]==1){
					?>Colegiado<?
				}elseif ($row["tipo"]==4){
					?>No colegiado<?
				}else{
					?>[sin definir]<?
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
			<td> 
			
			<? if ($row['nivel']!='9'){ ?> 
			<?
				$relleno='';
				$rellenotexto = 'No';
				$idempresa = $row['idempresa'];
				if ($idempresa>0){
						$rellenotexto = 'Si';
				} 
				
				echo $rellenotexto;
				
				?> &nbsp;<a class="btn btn-primary" href="zona-privada_usuario_facturacion.php?idusuario=<?=$id?>">editar</a><?
			?>	
			<? } ?>
			</td>
			<!--<td>
			<?
				$relleno2='';
				$relleno2texto = 'No';
				$sql = "SELECT * FROM usuario WHERE id='$id'";
				$resultfc = posgre_query($sql);
				if ($rowfc = pg_fetch_array($resultfc)){
					$relleno2 = $rowfc['domiciliacionvalida'];
					
					if ($relleno2==1){
						$relleno2texto = 'Si';
					}
				}
				
				echo $relleno2texto;
				
				?> &nbsp;<a class="btn btn-primary" href="zona-privada_usuario_domiciliacion.php?idusuario=<?=$id?>">editar</a><?
				
			?>-->
			
			</td>
			<td><?
				// Genera
				$linka=iConectarse(); 
				$resulta=pg_query($linka,"SELECT * FROM factura_factura WHERE (idusuario='$id' OR idusuariofisico='$id') AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
				$cuantos=pg_num_rows($resulta);
				if ($cuantos>0){
					?><a href="a_facturacion.php?idusuario=<?=$id?>"><?=$cuantos?></a><?
				}else{
					echo "0";
				}
				?></td>
				
				<? $result3=posgre_query("SELECT * FROM curso_usuario WHERE nivel<>'3' AND estado=0 AND idusuario='$id' AND borrado=0 ") ;//or die (pg_error()); 
				   $cuantoscursos = pg_num_rows($result3)?>
			<td><a href="a_facturacion_usuario_curso.php?idusuario=<?=$id?>" ><?=$cuantoscursos?></a></td>
	
		</tr>
		<?
	}?>
		</table>

    <p class="align-center">Total: <?=$total_registros?> usuarios</p>
			<?
			if($total_paginas > 1) { ?>
				<div class="pagination">
					<ul>
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="a_facturacion_usuario.php?pagina=<?=(1)?>&texto=<?=$texto?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="a_facturacion_usuario.php?pagina=<?=($pagina-1)?>&texto=<?=$texto?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="a_facturacion_usuario.php?pagina=<?=$i?>&texto=<?=$texto?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="a_facturacion_usuario.php?pagina=<?=($pagina+1)?>&texto=<?=$texto?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="a_facturacion_usuario.php?pagina=<?=$total_paginas?>&texto=<?=$texto?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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