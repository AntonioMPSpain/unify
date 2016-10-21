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

/** Cambiar usuario a pagado o no pagado  **/
if 	((isset($_GET['id']))&&(isset($_GET['idusuario']))&&(isset($_GET['setpago']))){
	
	$iduser =  $_GET['idusuario'];
	$id = $_GET['id'];
	 
	if ($_GET['setpago']==1){
		$pago=1;
	}
	else{
		$pago=0;
	}
	
	$result = posgre_query("UPDATE generica_comprar SET estadopago='$pago' WHERE id='$id' AND idusuario='$iduser' AND borrado='0'");
	header ("Location: admin_contenido_historico.php");
	exit();
}

if($accion=='borrar'){
	$id=strip_tags($_REQUEST['id']);
	if ($id==''){
		header("Location: index.php?salir=true");
		exit();
	}
	$_SESSION[esterror]="No se ha podido eliminar, datos incorrectos.";
	//Eliminar de BD
	$link=conectar(); //Postgrepsql
	$Query = pg_query($link,"UPDATE generica_comprar SET borrado='1' WHERE id='$id' ;");
	if ($Query){
		$_SESSION[esterror]="Se ha eliminado correctamente.";
	}
	header("Location: admin_contenido_historico.php");
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
<h2 class="titulonoticia">Administrador Publicaciones Histórico de Compras</h2>
<div class="bloque-lateral acciones">		
	<p>
		<a href="admin_contenido.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
</div>
<? include("_aya_mensaje_session.php"); ?>
<TABLE > 
	<TR>
		<th>Fecha</th>
		<th>Título</th>
		<th>Nombre Apellidos | Email | Telefonos</th>
		<th>PRECO</th>	
		<th>FORMATO</th>	
		<th>PAGO</th>	
		<th>MODO PAGO</th>
		<th>RESGUARDO</th>
	</TR> 
	<?  
	$link=conectar(); //Postgresql
	$result=pg_query($link,"SELECT *,to_char(fechahora, 'dd/mm/YYYY') as fecha  FROM generica_comprar WHERE borrado=0 ORDER BY fechahora DESC, id DESC");// or die (mysql_error());  
	while($row = pg_fetch_array($result)) { 
		$id=$row["id"];
		$precio=$row["precio"];
		$idusuario=$row["idusuario"];
		$idgenerica=$row["idgenerica"];
		$estadopago = $row['estadopago'];
		$tipopago = $row['tipopago'];
		$formato = $row['formato'];
		
		if ($estadopago==1){
			$pagado = "SI";
		}
		else{
			$pagado = "PENDIENTE";
		}
		
		if ($tipopago==1){
			$tipopago2="TARJETA";
			$resguardo=false;
		}
		else{
			$tipopago2="TRANSFERENCIA";
			$resguardo=true;
		
		}
		
		if ($formato==1){
			$format="DIGITAL";
		}
		else{
			$format="FÍSICO Y DIGITAL";
		}
				
		?><tr bgcolor="<?=$bgcolor?>">
			<td ><?=($row["fecha"])?></td>
			<td ><?
				$link2=conectar(); //Postgrepsql
				$sqlw="SELECT * FROM generica WHERE id = '$idgenerica';";
				$Queryw = pg_query($link2,$sqlw );// or die ("e1-".mysql_error()); 
				if((pg_num_rows($Queryw) != 0)) { // usuarios
					$roww = pg_fetch_array($Queryw);
					 echo trim($roww["titulo"]);	
				}else{ 
					echo "No existe";
				}
			?></td>
			<td ><?
				$link2=conectar(); //Postgrepsql
				$sql="SELECT * FROM usuario WHERE id = '$idusuario' AND borrado=0;";
				$Query = pg_query($link2,$sql );// or die ("e1-".mysql_error()); 
				if((pg_num_rows($Query) != 0)) { // usuarios
					$roww = pg_fetch_array($Query);
					echo trim($roww["nombre"])." ".trim($roww["apellidos"])." | ".trim($roww["email"])." |  ".trim($roww["telefono"])." | ".trim($roww["telefono2"]);
				}
			?></td>
			<td><?=$precio?></td>
			<td><?=$format?></td>
			<td>
			<?
			if ($precio>0){
				if ($estadopago==1){
					?>
					<i class="icon-ok-sign" title="Pagado">&nbsp;</i><br>
					<a href="admin_contenido_historico.php?setpago=0&id=<?=$id?>&idusuario=<?=$idusuario?>"  onclick="return confirmar('&iquest;Est&aacute; seguro de cancelar el pago? \n\n')" class="btn btn-primary">Cancelar Pago</a>
					<?
					
				}
				else{
					?>
					<i class="icon-ban-circle" title="No pagado">&nbsp;</i><br>
					<a href="admin_contenido_historico.php?setpago=1&id=<?=$id?>&idusuario=<?=$idusuario?>" onclick="return confirmar('&iquest;Est&aacute; seguro de validar el pago?\n\n')"  class="btn btn-primary">Validar Pago</a>
							
					<?
				}	
			}	
			?></td>
			<td><?
			if ($precio>0){ 
				echo $tipopago2;			
			}?></td>
			<td><?
			if ($precio>0){
					if ($resguardo){
						$result2 = posgre_query("SELECT id,archivo,nombre FROM archivo WHERE idgenerica='$idgenerica' AND idusuario='$idusuario' AND borrado=0 ORDER BY fecha DESC") ;//or die (mysql_error());  
						if($row2= pg_fetch_array($result2)) {								
							if ($row2["archivo"]<>""){
								if (trim($row2["nombre"])==""){
									$nombrear="Documento";
								}else{
									$nombrear=$row2["nombre"];
								}
								?>
								<span class="actions"> <?=ucfirst($nombrear)?> &middot; 
									<a href="descarga.php?documento=<?=$row2["archivo"]?>" ><i class="icon-zoom-in"></i> Ver</a>
									
								<? if ($estadopago==1) {} else{?>
									&middot; <a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_archivos.php?resguardopubli&accionpdf=borrar&idpub=<?=$idgenerica?>&id=<?=$row2["id"]?>&idpdf=<?=$row2["id"]?>"><i class="icon-trash"></i> Borrar</a>
								</span>								
							<?
								} 
							}
						}
						else {?>
							<h4>Debe insertar el resguardo</h4>
							<form class="titulo" action="admin_archivos.php?accionpdf=inserta&resguardopubli&id=<?=$idusuario?>&idpub=<?=$idgenerica?>" method="post" enctype="multipart/form-data">     
							<input name="userfile" type="file" />
							<input TYPE=SUBMIT VALUE="Guardar" class="btn btn-primary"> 
							</form>			
						<? }
					
					}
			}
			?></td>
			<!--<td >
			<a href="admin_contenido_historico.php?id=< ?=$row["id"]?>&accion=borrar&tipo=< ?=$tipo?>" onclick="return confirmar('¿Eliminar elemento?')"  class="btn btn-primary"> eliminar </a> 
			</td>-->
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