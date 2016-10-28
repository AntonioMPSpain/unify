<?

include_once "_config.php";

include($backendpath."_funciones.php");

////////// Filtros de nivel por usuario //////////////////////
session_start();

$id=strip_tags(trim($_REQUEST['id'])); //idfactura

if ($id==""){

	$_SESSION[esterror]="Acceso denegado";	
	header("Location: index.php");
	exit();
}

if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2)) { //Admin y Admin Colegio 
	$idusuario = $_SESSION['idusuario'];
}
elseif (($_SESSION[nivel]==4)||($_SESSION[nivel]==3)) { //Admin Total
	$idusuario = $_SESSION['idusuario'];
	$sql = "SELECT * FROM factura_factura WHERE (idusuario='$idusuario' OR idusuariofisico='$idusuario') AND borrado=0 AND \"InvoiceNumber\"='$id'";
	$result=posgre_query($sql);
	if (pg_num_rows($result)==0){
		$_SESSION[esterror]= "Acceso denegado";
		header("Location: index.php");
		exit();
	}
}
else{
	$_SESSION[esterror]="Acceso denegado";	
	header("Location: index.php");
	exit();
}

$sql = "SELECT * FROM usuario WHERE id IN (SELECT idusuario FROM factura_factura WHERE \"InvoiceNumber\"='$id' AND borrado=0)";
$result = posgre_query($sql);
while ($row = pg_fetch_array($result)){

	$idusuario = $row['id'];
	$nivel = $row['nivel'];
	
	$nombre = $row['nombre'];
	$apellidos = $row['apellidos'];
	if ($nivel<>"9"){
		$nombre=tildesmayusculas(ucwords(strtolower($nombre)));
		$apellidos=tildesmayusculas(ucwords(strtolower($apellidos)));
	}
	
	$nif=($row['nif']);
	$municipio=($row['municipio']);
	$direccion=($row['direccion']);
	$cp=$row['cp'];
	$email=$row['email'];
	$idprovincia=$row['idprovincia'];
	$pais=$row['pais'];
	
	$paisNombre = getPais($pais);
	
	$sql4 = "SELECT deno FROM etiqueta_provincia WHERE id='$idprovincia'";
	$result4 = posgre_query($sql4);
	
	if ($row4 = pg_fetch_array($result4)){
		$provinciaNombre = $row4['deno'];
	}
}

$sql = "SELECT * FROM factura_factura WHERE \"InvoiceNumber\"='$id' AND borrado=0";
$result = posgre_query($sql);
if ($row = pg_fetch_array($result)){

	$IssueDate=$row['IssueDate'];
	$idgenerica=$row['idgenerica'];
	$tipo=$row['tipo'];
	$iva=$row['TaxRate'];
	$formapago=$row['formapago'];
	$exportada=$row['exportada'];
	$rectificativa=$row['rectificativa'];
	$numfacturarectif=$row['numrectificativa'];
	$cerrada=$row['cerrada'];
	$idusuariofisico=$row['idusuariofisico'];
	
	$usuariofisico = false;
	$nombrealumno="";
	if (($idusuariofisico>0)&&($idusuariofisico!=$idusuario)){
		$sqlu = "SELECT * FROM usuario WHERE id='$idusuariofisico'";
		$resultu = posgre_query($sqlu);
		if ($rowu = pg_fetch_array($resultu)){
			$nombrealumno = $rowu['nombre']. " " . $rowu['apellidos'];
			$nombrealumno = tildesmayusculas(ucwords(strtolower($nombrealumno)));
			$usuariofisico = true;
		}
	}
	
	if (($exportada==1)||($cerrada==1)){
		$nif = $row['dni'];
		$nombre = $row['nombre'];
		$direccion = $row['direccion'];
		$provinciaNombre = $row['provinciaNombre'];
		$municipio = $row['municipio'];
		$cp = $row['cp'];
		$apellidos="";
		
	}
	
	if (!$usuariofisico){
		$nombre=tildesmayusculas(ucwords(strtolower($nombre)));
		$apellidos=tildesmayusculas(ucwords(strtolower($apellidos)));
	}
	
	if ($formapago==0){
		$formapagotexto="Transferencia";
	}
	elseif ($formapago==1){
		$formapagotexto="Tarjeta";
	}
	elseif ($formapago==2){
		$formapagotexto="Domiciliaci&oacute;n";
		$numcargo=$row['plazo'];
	}
	
}
else{
	$_SESSION[esterror]="Acceso denegado";	
	header("Location: index.php");
	exit();
}


////////// FIN Filtros de nivel por usuario ////////////////////// 

ob_start(); 
?>

 <? /* 
 <!DOCTYPE html>
<html lang="en">
  <head>
    <title>Factura | Activatie</title>
    <link rel="stylesheet" href="css/facturastyle.css" media="all" />
  </head>

  <body>
    <main> */ ?>
    
<style type="text/css">
	@font-face {
	  font-family: SourceSansPro;
	  src: url(SourceSansPro-Regular.ttf);
	}
	
	.clearfix:after {
	  content: "";
	  display: table;
	  clear: both;
	}
	
	a {
	  color: silver;
	  text-decoration: none;
	}
	
	.body {
	  position: relative;
	  width: 100%;  
	  height:100%; 
	  margin: 0 auto; 
	  color: #555555;
	  background: #FFFFFF; 
	  font-family: Arial, sans-serif; 
	  font-size: 14px; 
	}
	
	.header {
	  padding: 10px 0;
	  margin-bottom: 20px;
	  border-bottom: 1px solid #AAAAAA;
	}
	
	#logo {
	  float: left;
	  margin-top: 0px;
	}
	
	#logo img {
	  height: 70px;
	}
	
	#company {
	  position:absolute;
	  left:270px;
	  text-align: left;
	}
	
	
	#details {
	  margin-bottom: 50px;
	}
	
	#client {
	  padding-left: 6px;
	  border-left: 6px solid silver;
	  float: left;
	}
	
	#client .to {
	  color: #777777;
	}
	
	h2.name {
	  font-size: 1.4em;
	  font-weight: normal;
	  margin: 0;
	}
	
	#invoice {
	  position:relative;
	  text-align: left;
	  margin-top:50px;
	}
	
	#invoice h1 {
	  color: silver;
	  font-size: 2.4em;
	  line-height: 1em;
	  font-weight: normal;
	  margin: 0  0 10px 0;
	}
	
	#invoice .date {
	  margin-top:20px;
	  position:relative;
	  font-size: 1.1em;
	  color: #777777;
	  text-align: left;
	}
	
	table {
	  width: 100%;
	  border-collapse: collapse;
	  border-spacing: 0;
	  margin-bottom: 20px;
	}
	
	table th,
	table td {
	  padding: 20px;
	  background: #EEEEEE;
	  text-align: center;
	  border-bottom: 1px solid #FFFFFF;
	}
	
	table th {
	  white-space: nowrap;        
	  font-weight: normal;
	}
	
	table td {
	  text-align: right;
	}
	
	table td h3{
	  color: #D13100;
	  font-size: 1.2em;
	  font-weight: normal;
	  margin: 0 0 0.2em 0;
	}
	
	table .no {
	  color: #FFFFFF;
	  font-size: 1.6em;
	  background: #D13100;
	}
	
	table .desc {
	  text-align: left;
	  width:230px;
	}
	
	table .unit {
	  background: #DDDDDD;
	}
	
	table .qty {
	}
	
	table .total {
	  background: #D13100;
	  color: #FFFFFF;
	}
	
	table td.unit,
	table td.qty,
	table td.total {
	  font-size: 1.2em;
	}
	
	table tbody tr:last-child td {
	  border: none;
	}
	
	table tfoot td {
	  padding: 10px 20px;
	  background: #FFFFFF;
	  border-bottom: none;
	  font-size: 1.2em;
	  white-space: nowrap; 
	  border-top: 1px solid #AAAAAA; 
	}
	
	table tfoot tr:first-child td {
	  border-top: none; 
	}
	
	table tfoot tr:last-child td {
	  color: #D13100;
	  font-size: 1.4em;
	  border-top: 1px solid #D13100; 
	
	}
	
	table tfoot tr td:first-child {
	  border: none;
	}
	
	#footer {
	  color: #777777;
	  width: 100%;
	  height: 30px;
	  position: absolute;
	  bottom: 0;
	  border-top: 1px solid #AAAAAA;
	  padding: 8px 0;
	  text-align: center;
	}

	

</style>
<div class="body">
<div class="header clearfix">
  <div id="logo">
		<img src="<?=$imgdiplomapath?>diploma-logo-activatie.png">
  </div>
  
  <div id="company">
	<h2 class="name">PLATAFORMA COLEGIAL ACTIVATIE SL</h2>
	<div class="name">CIF B73879850</div>
	<div class="name">Avda. Alfonso X El Sabio 2 </div>
	<div class="address">30008 - Murcia</div>
	<div class="email"><a href="mailto:info@activatie.org">info@activatie.org</a></div> 
  </div>
</div>

<div id="details" class="clearfix">
<div id="client">
  <div class="to">FACTURA A:</div>
  <h2 class="name"><?=$nombre?> <?=$apellidos?></h2>
  <div class="address"><?=strtoupper($nif)?> </div>
  <div class="address"><?=tildesmayusculas(ucwords(strtolower($direccion)));?></div>
  <div class="address"><?=tildesmayusculas(ucwords(strtolower($municipio)));?> </div> 
  <div class="address"><?=$cp?> <? if ($pais=="ES") { echo $provinciaNombre; } ?> <?=$paisNombre?> </div> 
  <? /* <div class="email"><a href="mailto:<?=$email?>"><?=$email?></a></div> */ ?>
</div>
<div id="invoice">
  <h1>FACTURA <? if ($rectificativa==2){ echo 'PROFORMA '; } ?><?=$id?></h1>
  <div class="date">Fecha: <?=$IssueDate?><br>
  <? if ($rectificativa==1){ ?>
	<div> Factura rectificativa de la factura <?=$numfacturarectif?> </div>
  <? } 
  else{ ?>
	  Forma de pago: <?=$formapagotexto?>
	  <? if ($formapago==2){ if ($numcargo>0) {?>. Cargo <?=$numcargo?> <? }} ?>
  <? } ?>
  <? if ($rectificativa==2){ echo '<br>IBAN: ES04 0081 1016 1800 0154 6461 '; } ?>
  </div>

</div>
</div>
<table border="0" cellspacing="0" cellpadding="0">
<thead>
  <tr>
	<th class="no"></th>
	<th class="desc">DESCRIPCI&Oacute;N</th>
	<th class="unit">PRECIO UNIDAD</th>
	<th class="qty">CANTIDAD</th>
	<th class="total">TOTAL</th>
  </tr>
</thead>
<tbody>

  <? 
	
	$sql = "SELECT * FROM factura_subfactura WHERE \"InvoiceNumber\"='$id' AND borrado=0";
	$result = posgre_query($sql);
	$i=1;
	$subtotal=0;
	while ($row = pg_fetch_array($result)){
	
		$ItemDescription=($row['ItemDescription']);
		$Quantity=$row['Quantity'];
		$UnitPriceWithoutTax=$row['UnitPriceWithoutTax'];
		$totalproducto = number_format($UnitPriceWithoutTax*$Quantity,2,'.','');
		$subtotal += $totalproducto;
		?>
		  <tr>
			<td class="no"><?=$idgenerica?></td>
			<td class="desc"><h3><?=$ItemDescription?></h3></td>
			<td class="unit"><?=$UnitPriceWithoutTax?>€</td>
			<td class="qty"><?=$Quantity?></td>
			<td class="total"><?=$totalproducto?>€</td>
		  </tr>
		<?
		
		$i++;
	}
  
  ?>


</tbody>
<tfoot>
  <tr>
	<td colspan="2"></td>
	<td colspan="2">SUBTOTAL</td>
	<td><?=number_format($subtotal,2,'.','')?>€</td>
  </tr>
  <tr>
	<td colspan="2"></td>
	<td colspan="2">IVA <?=$iva?>%</td>
	<td><?=number_format($subtotal*($iva/100),2,'.','');?>€</td>
  </tr>
  <tr>
	<td colspan="2"></td>
	<td colspan="2">TOTAL (Euros)</td>
	<td><?=number_format(($subtotal*($iva/100))+$subtotal,2,'.','');?>€</td>
  </tr>
</tfoot>
</table>
<?  if ($usuariofisico){ ?>
<div id="notices">
	<br>
	<br>
	<div>Asistente al curso: <?=$nombrealumno?></div>
</div>
<? }  ?>
<? if ($iva==0){ ?>
     <br>
	 <br>
     <br>
	 <br>
	 <div id="notices">
        <div>Factura exenta de IVA:</div>
        <div class="notice">Seg&uacute;n art, 20, apartado uno, n&ordf; 92 de la ley 37/1992 Y art 44 Reglamento de ejecuci&oacute;n (UE) n&ordm; 282/2011 Consejo de 15 marzo 2011 y Consulta Vinculante D.G.T. V1873-12.</div>
      </div>
	</div>
<? } ?>


<div id="footer">
  Inscrita en el Registro Mercantil de Murcia, hoja MU-88624, tomo 3119, folio 44
</div>

<? /*
    </main>
    <footer>
      
    </footer>
  </body>
</html>
*/ 


$content = ob_get_clean();

require_once($libspath.'html2pdf/html2pdf.class.php');
try
{
	$margin = array(7,10,10,10);
	$html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', $margin);
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
	$html2pdf->createIndex('', 0, 0, false, false, 1);
	$html2pdf->Output('activatie_factura_'.$id.'.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}


?>
