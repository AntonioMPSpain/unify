<?
include("_seguridadfiltro.php"); 
include("_funciones.php"); 
include("_cone.php"); 

$safe="Publicaciones";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
$tipo="publicacion";

$titulo1="publicaciones ";
$titulo2="administración";

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
	$Query = pg_query($link,"UPDATE generica SET borrado='1' WHERE $sql id='$id' ;");
	if ($Query){
		$_SESSION[esterror]="Se ha eliminado correctamente.";
	}
	header("Location: admin_contenido.php");
	exit();
}


include("plantillaweb01admin.php"); 
$texto=strip_tags($_POST['texto']);
$orden=$_GET['orden'];
$dir=$_GET['dir'];
if ($orden=="") $orden="fecha";
if ($dir=="") $dir="DESC";
?>
<!--Arriba plantilla1-->
<h2 class="titulonoticia">Administrador Publicaciones</h2>
<div class="bloque-lateral acciones">		
	<p>
		<a href="admin_contenido2.php?tipo=<?=$tipo?>" class="btn btn-success" type="button">Nueva <i class="icon-plus"></i></a> |
		<a href="admin_contenido_historico.php?tipo=<?=$tipo?>" class="btn btn-success" type="button">Historico de Ventas <i class="icon-plus"></i></a> |
	</p>
</div>
<? include("_aya_mensaje_session.php"); ?>
<TABLE > 
	<TR>
		<th><a href="admin_contenido.php?tipo=Consulta&orden=fecha&dir=<? if ($dir=="DESC") echo "ASC";?>">Fecha</a></th>
		<th>Título</th>
		<th>Autor</th>
		<th>Precio digital</th>
		<th>Precio papel</th>
		<th>Acción</th>
		<th>Destacado (mínimo 2)</th>
		<th>Privado</th>
		<th>Oculto</th>
	</TR> 
	<?  
	$link=conectar(); //Postgresql
	$result=pg_query($link,"SELECT * FROM generica WHERE $sql tipo='$tipo' AND borrado=0 ORDER BY fecha DESC, id DESC");// or die (mysql_error());  
	while($row = pg_fetch_array($result)) { 
		$precio=$row["precio"];
		$preciofisico=$row["preciofisico"];
		$id=$row["id"];
		$idempresa=$row["idcolegio"];
		?><tr bgcolor="<?=$bgcolor?>">
			<td class="align-center"><?=cambiaf_a_normal($row["fecha"])?></td>
			<td class="align-center"><a href="publicacion.php?id=<?=$row["id"]?>&accion=compra"  class=""><?=$row["titulo"]?></a></td>
			<td class="align-center"><?
				if ($idempresa=='0'){
						echo "Admin";
				}else{
					if ($idempresa<>""){
						$link2=conectar(); //Postgrepsql
						$sql="SELECT * FROM usuario WHERE confirmado=1 AND id = '$idempresa' AND borrado=0;";
						$Queryuser = pg_query($link2,$sql );// or die ("e1-".mysql_error()); 
						if((pg_num_rows($Queryuser) != 0)) { // usuarios
							$roww = pg_fetch_array($Queryuser);
							$autor=trim($roww["nombre"]);	
						}else{
							$autor="Autores anteriores";
						}
						?><?=$autor?><? 
					}else{
						echo "Anónimo";
					}
				}
			?></td>
			<td class="align-center"><?=$precio?></a></td>
			<td class="align-center"><?=$preciofisico?></a></td>
			<td class="align-center">
			<a href="admin_contenido2.php?id=<?=$row["id"]?>&accion=modificar&tipo=<?=$tipo?>"  class="btn btn-primary"> editar </a> 
			<a href="admin_contenido3_imagen.php?id=<?=$row["id"]?>&tipo=<?=$tipo?>"  class="btn btn-primary"> imágenes y videos</a> 
			<a href="admin_contenido3_archivo.php?id=<?=$row["id"]?>&tipo=<?=$tipo?>"  class="btn btn-primary"> documentos </a> 
			<a href="admin_contenido3_areas.php?id=<?=$row["id"]?>"  class="btn btn-primary"> áreas </a> 	 
			<a href="admin_contenido.php?id=<?=$row["id"]?>&accion=borrar&tipo=<?=$tipo?>" onclick="return confirmar('¿Eliminar elemento?')"  class="btn btn-primary"> eliminar </a> 
			<a href="admin_redes_sociales.php?tabla=generica&idtabla=<?=$row["id"];?>" class="btn btn-primary">redes sociales</a>
			<!-- 
			<a href="admin_contenido2.php?id=< ?=$row["id"]?>&accion=copiar&tipo=< ?=$tipo?>"  class="btn btn-primary"> copiar </a>
			<a href="admin_contenido2.php?id=< ?=$row["id"]?>&accion=copiar&tipo=< ?=$tipo?>"  class="btn btn-primary"> <img src="generica/copiar.gif" border="0" alt="COPIAR" /> </a> -->
			</td>
			<td class="align-center"><? if ($row["destacado"]==0){ echo "NO"; }else{ echo "SI";};?></td>
			<td class="align-center"><? if ($row["permiso"]==0){ echo "NO"; }else{ echo "SI";};?></td>
			<td class="align-center"><? if ($row["activo"]==1){ echo "NO"; }else{ echo "SI";};?></td>
		</tr>
		<? 
	} 
	?> 
</table>
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