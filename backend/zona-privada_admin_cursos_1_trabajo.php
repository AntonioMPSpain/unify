<?
include("_seguridadfiltro.php"); 
include("_funciones.php"); 
include("_cone.php"); 

$titulo1="ofertas de ";
$titulo2="trabajo";
$safe="configuracion";
$accion=strip_tags($_REQUEST['accion']);

$link=conectar(); //Postgrepsql
$resultaa=pg_query($link,"SELECT * FROM trabajo WHERE $sql borrado=0 ORDER BY fecha_insercion DESC,id DESC;");// or die ("Error en consulta. Contacte con Admin.".mysql_error());  
$bgcolor="#FFE3DD";	
include("plantillaweb01admin.php"); 
?>
<!--Arriba pantilla1-->

<? include("_aya_mensaje_session.php"); ?>
<h2 class="titulonoticia">Comunicaciones ofertas de trabajo</h2>
<div class="bloque-lateral acciones">		
	<p><strong>Acciones:</strong>
		<a class="btn btn-success" href="zona-privada_admin_comunicaciones_5_historico-de-envios.php">Histórico de Mensajes Enviados <i class="icon-calendar"></i></a> <br />
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
			
					<a href="zona-privada_admin_comunicaciones_1.php?idtrabajo=<?=$row["id"]?>"  class="btn btn-primary"> enviar correo </a> </td>
			</tr>
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
