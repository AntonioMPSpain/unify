<?
include("_seguridadfiltro.php"); 
include("_funciones.php"); 
include("_cone.php"); 

$safe="Publicaciones";
$accion=strip_tags($_GET['accion']); 
$hueco=strip_tags($_GET['hueco']);
$est=strip_tags($_GET['est']);
$tipo="publicacion";

$titulo1="publicaciones ";
$titulo2="administración";

$accion=strip_tags($_REQUEST['accion']);



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

include("plantillaweb01admin.php"); 
$texto=strip_tags($_POST['texto']);
$orden=$_GET['orden'];
$dir=$_GET['dir'];
if ($orden=="") $orden="fecha";
if ($dir=="") $dir="DESC";
?>
<!--Arriba plantilla1-->
<h2 class="titulonoticia">Comunicaciones publicaciones</h2>
		<div class="bloque-lateral acciones">		
			<p><strong>Acciones:</strong>
				<a class="btn btn-success" href="zona-privada_admin_comunicaciones_newsletter.php">Volver <i class="icon-circle-arrow-left"></i></a> <br />
			</p>
		</div>
		<!--fin acciones-->
<? include("_aya_mensaje_session.php"); ?>
<TABLE > 
	<TR>
		<th><a href="admin_contenido.php?tipo=Consulta&orden=fecha&dir=<? if ($dir=="DESC") echo "ASC";?>">Fecha</a></th>
		<th>Título</th>
		<th>Autor</th>
		<th>Destacado</th>
		<th>Privado</th>
		<th>Oculto</th>
		<th>Acción</th>
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
			<td class="align-center"><?=$row["titulo"]?></td>
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
				
				if ($hueco==1){
					$publi1=$row['id'];
				}
				elseif ($hueco==2){
					$publi2=$row['id'];
				
				}
				elseif ($hueco==3){
					$publi3=$row['id'];
				
				}
			
			?>
			
			</td>

			<td class="align-center"><? if ($row["destacado"]==0){ echo "NO"; }else{ echo "SI";};?></td>
			<td class="align-center"><? if ($row["permiso"]==0){ echo "NO"; }else{ echo "SI";};?></td>
			<td class="align-center"><? if ($row["activo"]==1){ echo "NO"; }else{ echo "SI";};?></td>			<td class="align-center">
				<a href="zona-privada_admin_comunicaciones_newsletter.php?curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary">Insertar en newsletter</a>		<!-- <a href="admin_contenido2.php?id=< ?=$row["id"]?>&accion=copiar&tipo=< ?=$tipo?>"  class="btn btn-primary"> <img src="generica/copiar.gif" border="0" alt="COPIAR" /> </a> -->
			</td>
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