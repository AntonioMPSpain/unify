<?
include("_seguridadfiltro.php"); 
include("_funciones.php"); 
include("_cone.php"); 

$titulo1="ofertas de ";
$titulo2="trabajo";
$safe="configuracion";
$accion=strip_tags($_REQUEST['accion']);
$hueco=strip_tags($_GET['hueco']);

$link=conectar(); //Postgrepsql
$resultaa=pg_query($link,"SELECT * FROM trabajo WHERE $sql borrado=0 AND estado=1 ORDER BY fecha_insercion DESC,id DESC;");// or die ("Error en consulta. Contacte con Admin.".mysql_error());  
$bgcolor="#FFE3DD";	


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
?>
<!--Arriba pantilla1-->

<? include("_aya_mensaje_session.php"); ?>
<h2 class="titulonoticia">Comunicaciones ofertas de trabajo</h2>
		<div class="bloque-lateral acciones">		
			<p><strong>Acciones:</strong>
				<a class="btn btn-success" href="zona-privada_admin_comunicaciones_newsletter.php">Volver <i class="icon-circle-arrow-left"></i></a> <br />
			</p>
		</div>
		<!--fin acciones-->

	<TABLE > 
		<tr>
			<th>Fecha inserción</th>
			<th>Denominación</th>
			<th>Zona de trabajo</th>
			<th>E-mail</th>
			<th>Fecha límite<br />(Verde: No publicada)</th>
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
			
				<td><?=cambiaf_a_normal($row["fecha_insercion"])?></td>
				<td><? 
				if ($row["lugar"]==1){
					?><img src="css/pics/h1-teef.png" /><? 
				}?>
				<?=($row["denominacion"])?></td>
				<td><?=($row["zona"])?></td>
				<td bgcolor=""><?=$row["email"]?></td>
				<td<? 
				$fecha=strftime("%d/%m/%Y");
				$fechabd=cambiaf_a_normal($row["fecha"]);
				//echo"-------------";
				$fechamas1=suma_fechas($fecha,1);
				//exit();
				if (compara_fechas($fechamas1,$fechabd)>0) echo ' bgcolor="#5BB75B"';
				?>><?=cambiaf_a_normal($row["fecha"])?></td>
				<td>
				<?				
				if ($hueco==1){
					$trabajo1=$row['id'];
				}
				elseif ($hueco==2){
					$trabajo2=$row['id'];
				
				}
				elseif ($hueco==3){
					$trabajo3=$row['id'];
				
				}
				
				?>
				<a href="zona-privada_admin_comunicaciones_newsletter.php?curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary">Insertar en newsletter</a>		</tr>
			<?
		} ?>
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
