<?
include("_funciones.php"); 
include("_cone.php"); 
$safe="Gastos";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
$est_texto=strip_tags($_GET['est_texto']);
$est_texto2=strip_tags($_GET['est_texto2']);

$idcurso=($_REQUEST['idcurso']);
if ($idcurso==""){
	$_SESSION[error]="Parametros incorrectos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}

$titulo1="formación ";
$titulo2="administración";
////////// Filtros de nivel por usuario //////////////////////
session_start();
$admin=false;
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$admin=true;
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}

if ($accion=="eliminar"){

	$id=strip_tags($_GET['id']); 
	if ($id<>""){
		$sql = "UPDATE curso_gastos SET borrado=1 WHERE id='$id'";
		posgre_query($sql);
	}

}

if ($accion=="comentarios"){

	$devolucioncomentario2=$_REQUEST['devolucioncomentario']; 
	$facturacomentario2=$_REQUEST['facturacomentario']; 
	$liquidacion=$_REQUEST['liquidacion']; 
	
	if ($liquidacion=="liquidacion"){
		$liquidacion=1;
	}
	else{
		$liquidacion=0;
	}
	
	$sql = "UPDATE curso SET liquidado='$liquidacion' , devolucioncomentario='$devolucioncomentario2',facturacomentario='$facturacomentario2' WHERE id='$idcurso'";
	posgre_query($sql);
	

}


$sql = "SELECT * FROM curso WHERE id='$idcurso'";
$result = posgre_query($sql);
if ($row = pg_fetch_array($result)){
	$nombrecurso = $row['nombre'];
	$facturacomentario = $row['facturacomentario'];
	$devolucioncomentario = $row['devolucioncomentario'];
	$liquidado = $row['liquidado'];
}


//$migas[] = array('zona-privada_admin_resumen.php', 'Gestión de cuentas');
include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?> <?=$nombrecurso?></h2>
	<br />
	<? include("_aya_mensaje.php"); ?>
	
	<h3>Gastos</h3>
	<? if ($admin){ ?>
		<div class="bloque-lateral acciones">		
			<p>
				<a href="cursos_gastos2.php?idcurso=<?=$idcurso?>" class="btn btn-success" type="button"> Nuevo gasto <i class="icon-plus"></i></a> |
			</p>
		</div>
	<? } ?>	
	<table class="align-center">
	<tr>
		<th>CONCEPTO</th>
		<th width='10%'>FECHA LIQUIDACIÓN</th>
		<th width='10%'>IMPORTE</th>
		<? if ($admin){ ?>
			<th width='10%'>ACCIÓN</th>	
		<? } ?>	
	</tr>
	
		<? 
	$link=iConectarse();
	$result=pg_query($link,"SELECT * FROM curso_gastos WHERE idcurso='$idcurso' AND borrado=0") ;//or die (pg_error());  

	while($row = pg_fetch_array($result)) { 
		$concepto = $row['concepto'];
		$importe = $row['importe'];
		$fecha = $row['fecha'];
		?>
		<tr>
			<td><?=$concepto?></td>
			<td><?=cambiaf_a_normal($fecha)?></td>
			<td><?=$importe?>€</td>
			
			<? if ($admin){ ?>
				<td>
					<a href="cursos_gastos2.php?idcurso=<?=$idcurso?>&id=<?=$row["id"];?>" class="btn btn-primary">editar</a>
					<a onclick="return confirm('&iquest;Desea eliminar el gasto?')" href="cursos_gastos.php?idcurso=<?=$idcurso?>&accion=eliminar&id=<?=$row["id"];?>" class="btn btn-primary">eliminar</a>
				</td>
			<? } ?>	
		</tr>
		<?
	}?>	

	</table>


	<? if ($admin){ ?> 
		<br>
		<form METHOD="post" ACTION="cursos_gastos.php?accion=comentarios&idcurso=<?=$idcurso?>">
			<h3>Liquidación</h3>
				<input <? if ($liquidado==1){ echo 'checked'; } ?> type="checkbox" value="liquidacion" name="liquidacion"> Liquidado
			<br><br>
			<h3>Comentario Facturas</h3>
				<textarea name="facturacomentario" style="width: 1000px;" rows="4"><?=$facturacomentario?></textarea>

			<br>
			<h3>Comentario Devoluciones</h3>
				
				<textarea name="devolucioncomentario" style="width: 1000px;" rows="4"><?=$devolucioncomentario?></textarea>
			<br><br>
			<button type="submit" class="btn btn-primary btn-large">Guardar</button>
		</form>	
		
	<? } ?>
	
	
	
	
	
	
	
	
	
	
	
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