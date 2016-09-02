<?

include("_funciones.php"); 
include("_cone.php"); 

$safe="Facturación: Editar factura";
$accion=strip_tags($_GET['accion']); 
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[esterror]="Su sesion ha expirado";	
	header("Location: index.php?salir=true");
	exit();
}elseif ($_SESSION[nivel]==3) { 
	$_SESSION[esterror]="Su sesion ha expirado";	
	header("Location: index.php?salir=true");
	exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	
}elseif ($_SESSION[nivel]==1) { //Admin Total
	
}else{
	$_SESSION[esterror]="Su sesion ha expirado";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$invoice = $_GET['invoice'];
if ($invoice==""){
	$_SESSION[esterror]="No existe número de factura";	
	header("Location: a_facturacion.php");
	exit();
}

$accion = $_GET['accion'];
if ($accion=="guardar"){
	foreach($_POST as $key => $value){

	
		$pos=strpos($key,"cantidad-");
		if($pos!==false){
			$numUsuarios++;
			$pieces = explode("-", $key);
			$id = $pieces[1];
			
			$sql = "UPDATE factura_subfactura SET \"UnitPriceWithoutTax\"='$value' WHERE id='$id'";
			posgre_query($sql);
		}
	}
}

if ($accion=="guardarf"){
	
	$fecha = $_REQUEST['fecha'];
	$sql = "UPDATE factura_factura SET \"IssueDate\"='$fecha' WHERE \"InvoiceNumber\"='$invoice'";
	posgre_query($sql);
	
}


$titulo1="formación ";
$titulo2="administración";

include("plantillaweb01admin.php"); 
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

		<!--Acciones-->
		<div class="bloque-lateral acciones">		
			<p>
	
				<a href="a_facturacion.php?id=<?=$invoice?>" class="btn btn-success" type="button">Volver</a> 

			</p>
		</div>
		<!--fin acciones-->
		<br>
		<h2>Factura <?=$invoice?></h2>
		<h3>Fecha</h3>
		
		<? 
			$sql = "SELECT * FROM factura_factura WHERE \"InvoiceNumber\"='$invoice'";
			$result = posgre_query($sql);
			$row = pg_fetch_array($result);
			$fecha = $row["IssueDate"];
		?>
		
		<div><form action='a_factura_editar.php?invoice=<?=$invoice?>&accion=guardarf' method="post" ><input class="input-small" name='fecha' value='<?=$fecha?>'><br><br><input  class="btn btn-primary"  type='submit' value='Guardar'></form></div>
		<h3>Productos</h3>
		<table class="align-center">
		<tr>
			<th>Descripción</th>
			<th>Unidades</th>
			<th>Precio</th>
		</tr>
		<?
		
		$sql = "SELECT * FROM factura_subfactura WHERE \"InvoiceNumber\"='$invoice'";
		$result = posgre_query($sql);
		
		while ($row = pg_fetch_array($result)){
			$id = $row["id"];
			$cantidad = $row['UnitPriceWithoutTax'];
			$descripcion = $row['ItemDescription'];
			$unidad = $row['Quantity'];
			
			?> 
			<tr>
				<td><?=$descripcion?></td>
				<td><?=$unidad?></td>
				<td><form action='a_factura_editar.php?invoice=<?=$invoice?>&accion=guardar' method="post" ><input class="input-small" name='cantidad-<?=$id?>' value='<?=$cantidad?>'><input  class="btn btn-primary"  type='submit' value='Guardar'></form></td>
				
				
			
			</tr> 
			
		<? } ?>
		
		
	
		</table>

	<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->
<?
include("plantillaweb02admin.php"); 
?>