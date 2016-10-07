<? 
include("_funciones.php"); 
include("_cone.php"); 
include_once "a_facturas.php"; 

$safe="Facturación"; 

////////// Filtros de nivel por usuario //////////////////////
//include("_seguridadfiltro.php"); //sale $ssql y $sql
session_start();
if ($_SESSION[nivel]==4) { //Alumno
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
}elseif ($_SESSION[nivel]==3) { //Profe 
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
}elseif ($_SESSION[nivel]==5) { // Directivo
	$idcolegio=strip_tags($_SESSION[idcolegio]);
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		if ($idcolegio<>111){
			$sqlcolegio="  (idcolegio='$idcolegio') AND ";
		}
	}else{
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total

}else{
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$texto=strip_tags($_POST['texto']);
$accion=strip_tags($_GET['accion']); 
		
$orden=strip_tags($_GET['orden']);
if($orden=="ASC"){
	$sqlorden="ASC";
}else{
	$orden="DESC";
	$sqlorden="DESC";
}

$campo = strip_tags($_GET['campo']);
if ($campo=="exportada"){
	$sqlcampo = "exportada";
}
elseif ($campo=="formapago"){
	$sqlcampo = "formapago";
}
elseif($campo=="numfactura"){
	$sqlcampo = "\"InvoiceNumber\"";
}
else{
	$sqlcampo =" to_date(\"IssueDate\",'DD-MM-YYYY') ";
	//$sqlcampo="\"IssueDate\"";
}

$sqlusuario = "";
if (isset($_GET['idusuario']) && ($_GET['idusuario']<>"")){
	$idusuario = $_GET['idusuario'];
	$sqlusuario = " AND (idusuario='$idusuario' OR idusuariofisico='$idusuario') ";
}

$sqlcurso = "";
if (isset($_GET['idcurso']) && ($_GET['idcurso']<>"")){
	$idcurso = $_GET['idcurso'];
	$sqlcurso = " AND tipo=1 AND idgenerica='$idcurso' ";
}

$sqlfactura = "";
if (isset($_GET['id']) && ($_GET['id']<>"")){
	$invoicenumber = $_GET['id'];
	$sqlfactura = " AND \"InvoiceNumber\"='$invoicenumber'";
}

if (isset($_GET['exportarrangos'])){
	$rango1 = $_POST['rango1'];
	$rango12 = $_POST['rango12'];
	$rango2 = $_POST['rango2'];
	$rango22 = $_POST['rango22'];
	
	$rango1 = $rango1."-".$rango12;
	$rango2 = $rango2."-".$rango22;
	
	generarExcelFacturaRango($rango1,$rango2);
	
	header("Location: a_facturacion.php");
	exit();
}

if (isset($_GET['exportarrangosfechas'])){
	
	$fecha1 = $_POST['fecha1'];
	$fecha2 = $_POST['fecha2'];

	$fecha1 = date("d-m-Y", strtotime($fecha1));
	$fecha2 = date("d-m-Y", strtotime($fecha2));
	
	if ($fecha1=="01-01-1970"){
		$fecha1="";
	}
	
	if ($fecha2=="01-01-1970"){
		$fecha2="";
	}
	generarExcelFacturaRangoFechas($fecha1,$fecha2);
	
	header("Location: a_facturacion.php");
	exit();
}

if (isset($_GET['exportarrangosexcel'])){
	$idcurso = $_REQUEST['idcurso'];

	$fecha1 = $_POST['fecha1'];
	$fecha2 = $_POST['fecha2'];

	$fecha1 = date("d-m-Y", strtotime($fecha1));
	$fecha2 = date("d-m-Y", strtotime($fecha2));
	
	if ($fecha1=="01-01-1970"){
		$fecha1="";
	}
	
	if ($fecha2=="01-01-1970"){
		$fecha2="";
	}
	
	$rango1 = $_POST['rango1'];
	$rango12 = $_POST['rango12'];
	$rango2 = $_POST['rango2'];
	$rango22 = $_POST['rango22'];
	
	if (($rango1<>"")&&($rango12<>"")){
		$rango1 = $rango1."-".$rango12;
	}
	else{
		$rango1="";
	}
	
	if (($rango2<>"")&&($rango22<>"")){
		$rango2 = $rango2."-".$rango22;
	}
	else{
		$rango2="";
	}
	imprimirXLS($idcurso, $fecha1, $fecha2, $rango1, $rango2);
	
	header("Location: a_facturacion.php");
	exit();
}


if (isset($_GET['exportar'])){
	$invoicenumber = $_GET['invoicenumber'];
	generarExcelFactura($invoicenumber);
	
	header("Location: a_facturacion.php");
	exit();
}

if (isset($_GET['rectificativa'])){
	$invoicenumber = $_GET['invoicenumber'];
	$sql = "SELECT * FROM factura_factura WHERE \"InvoiceNumber\"='$invoicenumber'";
	$result = posgre_query($sql);
	if ($row = pg_fetch_array($result)){
		$idusuario = $row['idusuario'];
		$idgenerica = $row['idgenerica'];
		$tipo = $row['tipo'];
		$formapago = $row['formapago'];
		$exportada = $row['exportada'];
		$dni = $row["dni"];
		$nombre = $row["nombre"];
		$direccion = $row["direccion"];
		$provinciaNombre = $row["provinciaNombre"];
		$municipio = $row["municipio"];
		$cp = $row["cp"];
		
	}
	
	$InvoiceNumber2 = generarFactura($idusuario, $tipo, $idgenerica, 1, $formapago, 0, $invoicenumber, 1);
	
	$sql = "UPDATE factura_factura SET cerrada='$exportada', dni='$dni', nombre='$nombre', direccion='$direccion', \"provinciaNombre\"='$provinciaNombre', municipio='$municipio', cp='$cp' WHERE \"InvoiceNumber\"='$InvoiceNumber2'";
	posgre_query($sql);
	
	header("Location: a_facturacion.php?id=$InvoiceNumber2");
	exit();
	
}

if (isset($_GET['copia'])){
	$invoicenumber = $_GET['invoicenumber'];
	$sql = "SELECT * FROM factura_factura WHERE \"InvoiceNumber\"='$invoicenumber'";
	$result = posgre_query($sql);
	if ($row = pg_fetch_array($result)){
		$idusuario = $row['idusuario'];
		$idgenerica = $row['idgenerica'];
		$tipo = $row['tipo'];
		$formapago = $row['formapago'];
	}
	$InvoiceNumber2 = generarFactura($idusuario, $tipo, $idgenerica, 0, $formapago, 0, $invoicenumber, 1);
	
	header("Location: a_facturacion.php?id=$InvoiceNumber2");
	exit();
}


include("plantillaweb01admin.php"); 
include("_aya_mensaje_session.php"); 

?>
	<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Facturación</h2>
	<br />
	<a href="files/manual facturacion.docx">Manual</a>
	<br>
	<br>
	<div class="bloque-lateral buscador">
		<h4>Buscar factura</h4>
		<form action="a_facturacion.php?accion=buscar" method="post" enctype="multipart/form-data" >
			<fieldset>
				<div class="input-append">(num factura)
				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="búsqueda" value="<?=$texto?>" />
					<input class="btn" type="submit" value="Buscar" />
	
				</div>		
			</fieldset>
		</form>
	</div>	


	<div class="bloque-lateral ">
		<h4>Descargar Facturas en EXCEL</h4>
		<div style="margin-bottom:10px;">Todas los campos son opcionales. Si no se selecciona ninguno se descargarán todas las facturas.</div>
		<form method="post" action="a_facturacion.php?exportarrangosexcel">
			Número de factura 
			desde: <input style="width:10px;" id="rango1" name="rango1" type="text" maxlength="1" placeholder="1" class="input-small"/> 
			<input style="width:45px;" id="rango12" name="rango12" type="text" maxlength="6" placeholder="000000" class="input-small"/> 
			&nbsp; hasta: <input style="width:10px;" id="rango2" name="rango2" type="text" maxlength="1" placeholder="1" class="input-small"/> 
			<input style="width:45px;" id="rango22" name="rango22" type="text" maxlength="6" placeholder="000000" class="input-small"/>
			<br>
			Fecha de factura desde: <input style="width:130px;" type="date" id="fecha1" name="fecha1" value="<?=$fecha1?>" placeholder="01/01/2000">
			&nbsp;
			hasta: <input style="width:130px;" type="date" id="fecha2" name="fecha2" value="<?=$fecha2?>" placeholder="01/01/2000">
			<br><button type="submit" class="inputbutton btn btn-success">Descargar</button>		
		
		</form>
	</div>	
	
	<div class="bloque-lateral ">
		<h4>Exportar Facturas a FactuSOL</h4>
		<form method="post" action="a_facturacion.php?exportarrangos">
			Número de factura 
			desde: <input required style="width:10px;" id="rango1" name="rango1" type="text" maxlength="1" placeholder="1" class="input-small"/> 
			<input required style="width:45px;" id="rango12" name="rango12" type="text" maxlength="6" placeholder="000000" class="input-small"/> 
			&nbsp; hasta: <input required style="width:10px;" id="rango2" name="rango2" type="text" maxlength="1" placeholder="1" class="input-small"/> 
			<input required style="width:45px;" id="rango22" name="rango22" type="text" maxlength="6" placeholder="000000" class="input-small"/>
			<button onclick="return confirm('&iquest;Desea exportar?')" type="submit" class="inputbutton btn btn-primary">Exportar</button>		
		
		</form>
		
		
		<form method="post" action="a_facturacion.php?exportarrangosfechas">
		<br>
			Fecha de factura desde: <input style="width:130px;" type="date" id="fecha1" name="fecha1" value="<?=$fecha1?>" placeholder="01/01/2000">
			&nbsp;
			hasta: <input style="width:130px;" type="date" id="fecha2" name="fecha2" value="<?=$fecha2?>" placeholder="01/01/2000">
			<button onclick="return confirm('&iquest;Desea exportar?')" type="submit" class="inputbutton btn btn-primary">Exportar</button>		
		
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
		<br />
		<h2>Facturas</h2>
		<table class="align-center">
		<tr>
			<th><a href="a_facturacion.php?idcurso=<?=$idcurso?>&campo=numfactura&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">Num factura</a></th>
			<th width='7%'><a href="a_facturacion.php?idcurso=<?=$idcurso?>&campo=fecha&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">Fecha</a></th>
			<th><a href="a_facturacion.php?idcurso=<?=$idcurso?>&campo=formapago&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">Pago</a></th>
			<th>Cantidad</th>
			<th>A</th>
			<th>Curso</th>
			<th>Organiza</th>
			<th><a href="a_facturacion.php?idcurso=<?=$idcurso?>&campo=exportada&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">Exportada</a></th>
			<th width='15%'>Acción</th>	
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
if (($accion=="buscar")&&($texto<>"")){
	$result=posgre_query("SELECT * FROM factura_factura WHERE borrado=0 AND (sp_asciipp(\"InvoiceNumber\") ILIKE sp_asciipp('%$texto%')) $sqlusuario ORDER BY $sqlcampo $sqlorden") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=posgre_query("SELECT * FROM factura_factura WHERE borrado=0 AND (sp_asciipp(\"InvoiceNumber\") ILIKE sp_asciipp('%$texto%')) $sqlusuario ORDER BY $sqlcampo $sqlorden OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros);	
}
else{
	$result=posgre_query("SELECT * FROM factura_factura WHERE borrado=0 $sqlfactura $sqlusuario $sqlcurso ORDER BY $sqlcampo $sqlorden, \"InvoiceNumber\" DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=posgre_query("SELECT * FROM factura_factura WHERE borrado=0 $sqlfactura $sqlusuario $sqlcurso ORDER BY $sqlcampo $sqlorden, \"InvoiceNumber\" DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		  			
}	
$cuantos = $total_registros;
//fin Paginacion 1
	while($row = pg_fetch_array($result)) { 
		$invoiceNumber= $row["InvoiceNumber"]; 
		$issueDate = $row["IssueDate"];
		$exportada = $row["exportada"];
		$formapago = $row["formapago"];
		$rectificativa = $row["rectificativa"];
		$tipo = $row["tipo"];	
		$idgenerica = $row["idgenerica"];
		$usuarioid = $row['idusuario'];
		$iva=$row['TaxRate'];
		$plazo = $row['plazo'];
		$cerrada = $row['cerrada'];
		$idusuariofisico = $row['idusuariofisico'];
		
		if ($cerrada==1){
			$nombre = "<a href='a_facturacion_usuario.php?idusuario=$usuarioid'>".$row['nombre']."</a>";
		}
		else{
			$sql = "SELECT * FROM usuario WHERE id IN (SELECT idusuario FROM factura_factura WHERE \"InvoiceNumber\"='$invoiceNumber' AND borrado=0)";
			$result3 = posgre_query($sql);
			while ($row3 = pg_fetch_array($result3)){
				$nombre=($row3['nombre']);
				$apellidos=($row3['apellidos']);
				$nombre = "<a href='a_facturacion_usuario.php?idusuario=$usuarioid'>".$nombre." ".$apellidos."</a>";
				$idempresa=$row3['idempresa'];
			}


		}	
		
		if ($idusuariofisico>0){
			$sql5 = "SELECT * FROM usuario WHERE id='$idusuariofisico'";
			$result5 = posgre_query($sql5);
			if ($row5 = pg_fetch_array($result5)){
				$nombre = $nombre .'<br>(<a href="a_facturacion_usuario.php?idusuario='.$idusuariofisico.'">'.$row5['nombre'].' '.$row5['apellidos'].'</a>)';
				$apellidos="";
			}
		}
		
		
		if ($tipo==1){
			$sql = "SELECT * FROM curso WHERE id='$idgenerica'";
			$result2 = posgre_query($sql);
			$row2 = pg_fetch_array($result2);
			$nombrecurso = $row2['nombre'];
			$cursoorganizador = $row2['idcolegio'];
			
			$sql = "SELECT * FROM usuario WHERE id='$cursoorganizador'";
			$result3 = posgre_query($sql);
			$row3 = pg_fetch_array($result3);
			$nombreorganizador = $row3['nombre'];
			
		}
		
		if ($formapago==0){
			$formapagotexto = "Transferencia";
		}
		elseif ($formapago==1){
			$formapagotexto = "Tarjeta";
		}
		elseif ($formapago==2){
			$formapagotexto = "Domiciliación";
			if ($plazo>0){
				$formapagotexto .= ". Cargo ".$plazo;
			}
			
		}
		
		$sql = "SELECT * FROM factura_subfactura WHERE \"InvoiceNumber\"='$invoiceNumber' AND borrado=0";
		$result5 = posgre_query($sql);
		$subtotal=0;
		while ($row5 = pg_fetch_array($result5)){
			$Quantity=$row5['Quantity'];
			$UnitPriceWithoutTax=$row5['UnitPriceWithoutTax'];
			$totalproducto = number_format($UnitPriceWithoutTax*$Quantity,2,'.','');
			$subtotal += $totalproducto;
		}
		$total = number_format(($subtotal*($iva/100))+$subtotal,2,'.','');
		
		?>
		<tr>
			<td><?=$invoiceNumber?></td>
			<td><?=$issueDate?></td>
			<td><?=$formapagotexto?></td>
			<td><?=$total?>€</td>
			<td><?=$nombre?></td>
			<td><a href='a_facturacion_curso.php?idcurso=<?=$idgenerica?>'><?=$nombrecurso?></td>
			<td><?=$nombreorganizador?></td>
			<td>
			<? if ($rectificativa<>2) { ?>
			
				<? if ($exportada==1){ ?>
					<i class="icon-ok-sign" title="Exportada">&nbsp;</i><br>
				<? } 
				else { ?>
					<i class="icon-ban-circle" title="No exportada">&nbsp;</i><br>
				<? } ?>
			<? } ?>
			</td>
			<td>
				<a href="../factura.php?id=<?=$invoiceNumber;?>" class="btn btn-success">ver</a>
				
			<? if ($rectificativa<>2){ ?>	
				<? if ($exportada<>1){ ?>	
					
					<a href="a_factura_editar.php?invoice=<?=$invoiceNumber;?>" class="btn btn-success">editar</a>
					<br>
					<a onclick="return confirm('&iquest;Desea exportar?')" href="a_facturacion.php?exportar&invoicenumber=<?=$invoiceNumber;?>" class="btn btn-primary">exportar</a>

				<? } 
				else { ?>

				<? }?>
								
				<?  //if ($rectificativa<>1){ ?>
					<a onclick="return confirm('&iquest;Desea generar copia sin exportar?')" href="a_facturacion.php?copia&invoicenumber=<?=$invoiceNumber;?>" class="btn btn-primary">generar nueva factura</a>		
					<a onclick="return confirm('&iquest;Desea generar rectificativa sin exportar?')" href="a_facturacion.php?rectificativa&invoicenumber=<?=$invoiceNumber;?>" class="btn btn-primary">generar rectificativa</a>
				<? // } else{ ?>
			<? } ?>
			<? // } ?>
			</td>
		</tr>
		<?
	}?>
		</table>

    <p class="align-center">Total: <?=$total_registros?> facturas</p>
			<?
			if($total_paginas > 1) { ?>
				<div class="pagination">
					<ul>
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="a_facturacion.php?campo=<?=$campo?>&pagina=<?=(1)?>&texto=<?=$texto?>&orden=<?=$orden?>&idusuario=<?=$idusuario?>&idcurso=<?=$idcurso?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="a_facturacion.php?campo=<?=$campo?>&pagina=<?=($pagina-1)?>&texto=<?=$texto?>&orden=<?=$orden?>&idusuario=<?=$idusuario?>&idcurso=<?=$idcurso?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="a_facturacion.php?campo=<?=$campo?>&pagina=<?=$i?>&texto=<?=$texto?>&orden=<?=$orden?>&idusuario=<?=$idusuario?>&idcurso=<?=$idcurso?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="a_facturacion.php?campo=<?=$campo?>&pagina=<?=($pagina+1)?>&texto=<?=$texto?>&orden=<?=$orden?>&idusuario=<?=$idusuario?>&idcurso=<?=$idcurso?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="a_facturacion.php?campo=<?=$campo?>&pagina=<?=$total_paginas?>&texto=<?=$texto?>&orden=<?=$orden?>&idusuario=<?=$idusuario?>&idcurso=<?=$idcurso?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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