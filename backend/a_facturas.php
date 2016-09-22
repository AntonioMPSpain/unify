<?

include_once "_cone.php";
include_once "_funciones.php";

if (isset($_GET['test'])){		

	/** CAMBIAR FORMATO INVOICE NUMBER **/
	/*
	$sql = "SELECT * FROM factura_factura WHERE borrado=0 ORDER BY \"InvoiceNumber\"";
	$result = posgre_query($sql);
	while ($row = pg_fetch_array($result)){
		$invoice = $row["InvoiceNumber"];
		$rectificativa = $row["rectificativa"];
		$tipo = $row["tipo"];
		
		$newinvoice = generateInvoiceNumber($tipo,$rectificativa);
		
		echo $invoice."  -  ".$newinvoice."<br>";
		
		$sql1 = "UPDATE factura_factura SET \"InvoiceNumber\"='$newinvoice' WHERE \"InvoiceNumber\"='$invoice'";
		$result1= posgre_query($sql1);	
		$sql2 = "UPDATE factura_subfactura SET \"InvoiceNumber\"='$newinvoice' WHERE \"InvoiceNumber\"='$invoice'";
		$result2 = posgre_query($sql2);
			
		
	}
	*/
	/**************************************/

}

function generarFactura($idusuario, $tipo, $idgenerica, $rectificativa, $tipopago ,$plazo=0, $numrectificativa="", $copia=0){

	if ($tipo==1){
		$sql = "SELECT * FROM curso WHERE id='$idgenerica'";
		$result = posgre_query($sql);
		echo pg_last_error()."<br>";
		if ($row = pg_fetch_array($result)){
			$TaxRate=0;
			$ItemDescription = $row['nombre'];
		}
		else{
			return "no existe curso";
		}
		
		$sql = "SELECT * FROM curso_usuario WHERE borrado=0 AND idusuario='$idusuario' AND idcurso='$idgenerica'";
		$result = posgre_query($sql);
		echo pg_last_error()."<br>";
		if ($row = pg_fetch_array($result)){
			$UnitPriceWithoutTax = $row['precio'];
		}
		else{
			return "no existe inscripción del usuario al curso";
		}
	}
	elseif ($tipo==2){
		$sql = "SELECT * FROM generica WHERE id='$idgenerica'";
		$result = posgre_query($sql);
		echo pg_last_error()."<br>";
		if ($row = pg_fetch_array($result)){
			$TaxRate=21;
			$ItemDescription = "Publicación: ".$row['titulo'];
			
		}
		else{
			return "no existe publicación";
		}
		
		$sql = "SELECT * FROM generica_comprar WHERE borrado=0 AND idusuario='$idusuario' AND idgenerica='$idgenerica'";
		$result = posgre_query($sql);
		echo pg_last_error()."<br>";
		if ($row = pg_fetch_array($result)){
			
			$UnitPriceWithoutTax = $row['precio'];
		}
		else{
			return "no existe compra de la publicación por el usuario";
		}

	}
	
	if ($copia==1){
		
		$sql2 = "SELECT * FROM factura_subfactura WHERE borrado=0 AND \"InvoiceNumber\"='$numrectificativa' ORDER BY \"InvoiceNumber\"";
		$result2 = posgre_query($sql2);
		$importeNeto = 0;
		while ($row2 = pg_fetch_array($result2)){
			$Quantity = $row2['Quantity'];
			$UnitPriceWithoutTax2 = $row2['UnitPriceWithoutTax'];
			
			$importeNeto += $Quantity*$UnitPriceWithoutTax2*(1+($TaxRate/100));
		}
		$UnitPriceWithoutTax=number_format($importeNeto, 2, '.', '');
		
	}
	
	$numfacturarectificativa="";
	if ($rectificativa==1){
		$numfacturarectificativa=$numrectificativa;
		$UnitPriceWithoutTax = -1*$UnitPriceWithoutTax;
		$idusuariofisico = $idusuario;
	}
	else{
		$sql3 = "SELECT * FROM usuario WHERE id='$idusuario'";
		$result3 = posgre_query($sql3);
		if ($row3 = pg_fetch_array($result3)){
			$idempresa = $row3['idempresa'];
			$idusuariofisico = 0;
			if (($idempresa!="")&&($idempresa>0)){
				$idusuariofisico = $idusuario;
				$idusuario = $idempresa;
			}
		}
	}
	// Generar factura_factura
	
	$InvoiceDocumentType = "";
	$InvoiceClass = "";
	$IssueDate = date("d-m-Y");
	$TaxTypeCode = "";
	
	$invoiceNumber = generateInvoiceNumber($tipo, $rectificativa);
	$sql = "INSERT INTO factura_factura (\"InvoiceNumber\", idusuario, \"InvoiceDocumentType\", idgenerica, rectificativa, \"IssueDate\", \"TaxRate\", tipo, formapago, plazo, numrectificativa, idusuariofisico) VALUES ('$invoiceNumber','$idusuario','$InvoiceDocumentType','$idgenerica','$rectificativa','$IssueDate','$TaxRate', '$tipo', '$tipopago', '$plazo', '$numfacturarectificativa', '$idusuariofisico') RETURNING \"InvoiceNumber\"";
	$result = posgre_query($sql);
	echo pg_last_error()."<br>"; 
	$row = pg_fetch_array($result);
	$InvoiceNumber = $row['InvoiceNumber'];
	
	// Generar facutra_subfactura con idgenerica
	
	$IssuerContractReference = "";
	$IssuerTransactionReference = "";
	$Quantity = 1;
	$TaxTypeCode = "";
	
	if (($tipopago==2)&&($copia<>1)){
		$UnitPriceWithoutTax = $UnitPriceWithoutTax/2;
	}
	
	$sql = "INSERT INTO factura_subfactura (\"InvoiceNumber\", \"IssuerContractReference\", \"IssuerTransactionReference\", \"ItemDescription\", \"Quantity\", \"UnitPriceWithoutTax\", \"TaxTypeCode\", \"TaxRate\") VALUES ('$InvoiceNumber','$IssuerContractReference','$IssuerTransactionReference','$ItemDescription','$Quantity','$UnitPriceWithoutTax','$TaxTypeCode','$TaxRate')";
	$result = posgre_query($sql);
	
	return $InvoiceNumber;
	
}

function generarExcelFacturaRangoFechas($fecha1, $fecha2){
	
	
	$sqlfecha1="";
	if ($fecha1<>""){
		$sqlfecha1 = " AND to_date(\"IssueDate\",'DD-MM-YYYY')>=to_date('$fecha1','DD-MM-YYYY') ";
	}

	$sqlfecha2="";
	if ($fecha2<>""){
		$sqlfecha2 = " AND to_date(\"IssueDate\",'DD-MM-YYYY')<=to_date('$fecha2','DD-MM-YYYY') ";
	}
	
	$sql = "SELECT * FROM factura_factura WHERE borrado=0 $sqlfecha1 $sqlfecha2 ORDER BY \"InvoiceNumber\"";
	generarExcelFacturas($sql, 2);
}

function generarExcelFacturaRango($inicio, $fin){
	$sql = "SELECT * FROM factura_factura WHERE \"InvoiceNumber\">='$inicio' AND \"InvoiceNumber\"<='$fin' ORDER BY \"InvoiceNumber\"";
	generarExcelFacturas($sql, 2);
}

function generarExcelFactura($numfactura){
	$sql = "SELECT * FROM factura_factura WHERE borrado=0 AND \"InvoiceNumber\"='$numfactura' ORDER BY \"InvoiceNumber\"";
	generarExcelFacturas($sql);
}


function generarExcelFacturaCurso($idcurso){
	
	$sql = "SELECT * FROM factura_factura WHERE borrado=0 AND idgenerica='$idcurso' ORDER BY \"InvoiceNumber\"";
	generarExcelFacturas($sql,1);
}
 
function generarExcelFacturas($sql,$tipoexportacion=0){

	$cursonombre="";
	if ($tipoexportacion==2){
		$iniciorango = explode(">='",$sql);
		$iniciorango = substr($iniciorango[1],0,8);
		$finrango = explode("<='",$sql);
		$finrango = substr($finrango[1],0,8);
		$cursonombre=$iniciorango."_a_".$finrango."_";
	}
	
	// Exportar facturas de activatie a Excel para importar en Factusol. Documentación en ./contabilidad/FactuSOL Importacion Excel Calc.pdf
	require_once dirname(__FILE__) . '/../librerias/PHPExcel/Classes/PHPExcel.php';
	
	// Facturas emitidas
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$result = posgre_query($sql);
	while($row = pg_fetch_array($result)){
	
		$idusuario = $row['idusuario'];
		$idgenerica  = $row['idgenerica'];
		$rectificativa = $row['rectificativa'];
		$InvoiceNumber = $row['InvoiceNumber'];
		$tipo = $row['tipo'];
		$IssueDate = $row['IssueDate'];
		$TaxRate = $row['TaxRate'];
		$formapago = $row['formapago'];
		$cerrada = $row['cerrada'];
		$exportada = $row['exportada'];
		
		if ($tipoexportacion==1){
			$sql5 = "SELECT nombre FROM curso WHERE id='$idgenerica'";
			$result5 = posgre_query($sql5);
			$row5 = pg_fetch_array($result5);
			$cursonombre = $row5['nombre']."_";
		}
				
		if ($exportada<>1){ 
			if ($tipo==1){
				$cod="C";
			}
			elseif($tipo==2){
				$cod="P";
			}
		
			$sql3 = "SELECT * FROM usuario WHERE id='$idusuario'";
			$result3 = posgre_query($sql3);
			if ($row3 = pg_fetch_array($result3)){
				$dni = $row3['nif'];
				$nombre = $row3['nombre'];
				$apellidos = $row3['apellidos'];
				$nombrecliente = $nombre." ".$apellidos;
				$direccion = $row3['direccion'];
				$idprovincia = $row3['idprovincia'];
				$telefono = $row3['telefono'];
				$municipio = $row3['municipio'];
				$cp = $row3['cp'];
			
			
				$sql4 = "SELECT deno FROM etiqueta_provincia WHERE id='$idprovincia'";
				$result4 = posgre_query($sql4);
				
				if ($row4 = pg_fetch_array($result4)){
					$provinciaNombre = $row4['deno'];
				}
			}
			
			$sql5 = "SELECT * FROM usuario WHERE id='$idusuario'";
			$result5 = posgre_query($sql5);
			if ($row5 = pg_fetch_array($result5)){
				$idempresa = $row5['idempresa'];
				if (($idempresa!="")&&($idempresa>0)){
					
					$sql5 = "SELECT * FROM usuario WHERE id='$idempresa'";
					$result5 = posgre_query($sql5);
					if ($row5 = pg_fetch_array($result5)){
						
						$dni = $row5['nif'];
						$nombrecliente = $row5['nombre'];
						$direccion = $row5['direccion'];
						$idprovincia = $row5['idprovincia'];
						$municipio = $row5['municipio'];
						$cp = $row5['cp'];
						$apellidos="";
						
						
						$consulta = "SELECT * FROM etiqueta_provincia WHERE id = '$idprovincia'";
						$r_datos=posgre_query($consulta);// or die (mysql_error());  
						if ($rowdg= pg_fetch_array($r_datos)) {	
							$provinciaNombre=$rowdg['deno'];
						}
					}
					
					
					
				}
			}
			
			
			if ($cerrada==1){
				$dni = $row["dni"];
				$nombrecliente = $row["nombre"];
				$direccion = $row["direccion"];
				$provinciaNombre = $row["provinciaNombre"];
				$municipio = $row["municipio"];
				$cp = $row["cp"];
				$apellidos="";
			}
			
			$sql2 = "SELECT * FROM factura_subfactura WHERE borrado=0 AND \"InvoiceNumber\"='$InvoiceNumber' ORDER BY \"InvoiceNumber\"";
			$result2 = posgre_query($sql2);
			echo pg_last_error();
			$posicion=1;
			$importeNeto1 = 0;
			$importeNeto = 0;
			while ($row2 = pg_fetch_array($result2)){
				$Quantity = $row2['Quantity'];
				$UnitPriceWithoutTax = $row2['UnitPriceWithoutTax'];
				
				$importeNeto1 += $Quantity*$UnitPriceWithoutTax;
				$importeNeto += $Quantity*$UnitPriceWithoutTax*(1+($TaxRate/100));
			}
			$importeNeto=number_format($importeNeto, 2, '.', '');
			
			$numsfacturas = explode("-",$InvoiceNumber);
			$tipoDocumento = $numsfacturas[0];
			$numeroDocumento = $numsfacturas[1];
		
			$fecha=$IssueDate;
			
			if (($formapago==2)||($rectificativa==1)){
				$estado=0;			// Pendiente
			}
			else{
				$estado=2;			// Cobrada
			}
			
			
			$almacen="";
			$agente="";
			$codidoProveedor="";
			$codigoCliente=$idusuario;
			$nombreCliente=$nombrecliente;
			$domicilioCliente=$direccion;
			$poblacion=$municipio;
			$codigoPostal=$cp;
			$provincia=$provinciaNombre;
			$nif=$dni;
			$tipoIVA=1;
			$recargoEquivalencia=0;
			$telefonoCliente=$telefono;
			$importeNeto1=$importeNeto1;
			$importeNeto2="";
			$importeNeto3="";
			$porcentajeDescuento1="";
			$porcentajeDescuento2="";
			$porcentajeDescuento3="";
			$importeDescuento1="";
			$importeDescuento2="";
			$importeDescuento3="";
			$porcentajeProntoPago1="";
			$porcentajeProntoPago2="";
			$porcentajeProntoPago3="";
			$importeProntoPago1="";
			$importeProntoPago2="";
			$importeProntoPago3="";
			$porcentajePortes1="";
			$porcentajePortes2="";
			$porcentajePortes3="";
			$importePortes1="";
			$importePortes2="";
			$importePortes3="";
			$porcentajeFinanciacion1="";
			$porcentajeFinanciacion2="";
			$porcentajeFinanciacion3="";
			$importeFinanciacion1="";
			$importeFinanciacion2="";
			$importeFinanciacion3="";
			$baseImponible1=$importeNeto1;
			$baseImponible2="";
			$baseImponible3="";
			$porcentajeIVA1=$TaxRate;
			$porcentajeIVA2="";
			$porcentajeIVA3="";
			$importeIVA1="";
			$importeIVA2="";
			$importeIVA3="";
			$porcentajeRecargoEquivalencia1="";
			$porcentajeRecargoEquivalencia2="";
			$porcentajeRecargoEquivalencia3="";
			$importeRecargoEquivalencia1="";
			$importeRecargoEquivalencia2="";
			$importeRecargoEquivalencia3="";
			$porcentajeRetencion="";
			$importeRetencion="";
			$total=$importeNeto;
			$formaPago=$formapago;
			$portes=0;
			$textoPortes="";
			$lineaObservaciones1="";
			$lineaObservaciones2="";
			$obraEntrega="";
			$remitidaPor="";
			$embaladoPor="";
			$atencionA="";
			$referencia=$InvoiceNumber;
			$numPedido="";
			$fechaPedido="";
			$cobrado="";
			$tipoCreacion="";
			$tipoRecibo="";
			$codigoReciboCreado="";
			$traspasada=0;
			$anotacionesPrivadas="";
			$documentosExternos="";
			$impresa="N";
			$bancoCliente="";
			$horaCreacion="";
			$comentariosImprimir="";
			$usuarioCreador="";
			$usuarioModificador="";
			$fax="";
			$imagen="";
			$importeNetoExento="";
			$porcentajeDescuentoExento="";
			$importeDescuentoExento="";
			$porcentajeProntoExento="";
			$importeProntoExento="";
			$porcentajePortesExento="";
			$importePortesExento="";
			$porcentajeFinanciacionExento="";
			$importeFinanciacionExento="";
			$baseImponibleExenta="";
			$enviadoPorEmail=0;
			$permisosYcontrasena="";
			$ticketPorcentajeDescuento="";
			$ticketImporteDescuento="";
			$caja="";
			$ibanBanco="";
			$bicBanco="";
			$nombreBanco="";
			$entidadCuentaCliente="";
			$oficinaCuentaCliente="";
			$digitosControlCliente="";
			$numeroCuentaCliente="";
		
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $tipoDocumento);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $numeroDocumento);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $referencia);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $fecha);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $estado); 
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $almacen);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $agente);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $codidoProveedor);
			*/
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $codigoCliente);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $nombreCliente);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $domicilioCliente);
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $poblacion);
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $codigoPostal, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $provincia);
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $nif);
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $tipoIVA);
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $recargoEquivalencia);
			*/
			$objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $telefonoCliente);
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $importeNeto1);
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $importeNeto2);
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $importeNeto3);
			$objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $porcentajeDescuento1);
			$objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $porcentajeDescuento2);
			$objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $porcentajeDescuento3);
			$objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $importeDescuento1);
			$objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, $importeDescuento2);
			$objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $importeDescuento3);
			$objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $porcentajeProntoPago1);
			$objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $porcentajeProntoPago2);
			$objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowCount, $porcentajeProntoPago3);
			$objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowCount, $importeProntoPago1);
			$objPHPExcel->getActiveSheet()->SetCellValue('AF'.$rowCount, $importeProntoPago2);
			$objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowCount, $importeProntoPago3);
			$objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, $porcentajePortes1);
			$objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, $porcentajePortes2);
			$objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, $porcentajePortes3);
			$objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, $importePortes1);
			$objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, $importePortes2);
			$objPHPExcel->getActiveSheet()->SetCellValue('AM'.$rowCount, $importePortes3);
			$objPHPExcel->getActiveSheet()->SetCellValue('AN'.$rowCount, $porcentajeFinanciacion1);
			$objPHPExcel->getActiveSheet()->SetCellValue('AO'.$rowCount, $porcentajeFinanciacion2);
			$objPHPExcel->getActiveSheet()->SetCellValue('AP'.$rowCount, $porcentajeFinanciacion3);
			$objPHPExcel->getActiveSheet()->SetCellValue('AQ'.$rowCount, $importeFinanciacion1);
			$objPHPExcel->getActiveSheet()->SetCellValue('AR'.$rowCount, $importeFinanciacion2);
			$objPHPExcel->getActiveSheet()->SetCellValue('AS'.$rowCount, $importeFinanciacion3);
			*/
			$objPHPExcel->getActiveSheet()->SetCellValue('AT'.$rowCount, $baseImponible1);
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('AU'.$rowCount, $baseImponible2);
			$objPHPExcel->getActiveSheet()->SetCellValue('AV'.$rowCount, $baseImponible3);
			*/
			$objPHPExcel->getActiveSheet()->SetCellValue('AW'.$rowCount, $porcentajeIVA1);
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('AX'.$rowCount, $porcentajeIVA2);
			$objPHPExcel->getActiveSheet()->SetCellValue('AY'.$rowCount, $porcentajeIVA3);	
			$objPHPExcel->getActiveSheet()->SetCellValue('AZ'.$rowCount, $importeIVA1);
			$objPHPExcel->getActiveSheet()->SetCellValue('BA'.$rowCount, $importeIVA2);
			$objPHPExcel->getActiveSheet()->SetCellValue('BB'.$rowCount, $importeIVA3);
			$objPHPExcel->getActiveSheet()->SetCellValue('BC'.$rowCount, $porcentajeRecargoEquivalencia1);
			$objPHPExcel->getActiveSheet()->SetCellValue('BD'.$rowCount, $porcentajeRecargoEquivalencia2);
			$objPHPExcel->getActiveSheet()->SetCellValue('BE'.$rowCount, $porcentajeRecargoEquivalencia3);
			$objPHPExcel->getActiveSheet()->SetCellValue('BF'.$rowCount, $importeRecargoEquivalencia1);
			$objPHPExcel->getActiveSheet()->SetCellValue('BG'.$rowCount, $importeRecargoEquivalencia2);
			$objPHPExcel->getActiveSheet()->SetCellValue('BH'.$rowCount, $importeRecargoEquivalencia3);
			$objPHPExcel->getActiveSheet()->SetCellValue('BI'.$rowCount, $porcentajeRetencion);
			$objPHPExcel->getActiveSheet()->SetCellValue('BJ'.$rowCount, $importeRetencion);
			*/
			$objPHPExcel->getActiveSheet()->SetCellValue('BK'.$rowCount, $total);
			$objPHPExcel->getActiveSheet()->SetCellValue('BL'.$rowCount, $formaPago);
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('BM'.$rowCount, $portes);
			$objPHPExcel->getActiveSheet()->SetCellValue('BN'.$rowCount, $textoPortes);
			$objPHPExcel->getActiveSheet()->SetCellValue('BO'.$rowCount, $lineaObservaciones1);
			$objPHPExcel->getActiveSheet()->SetCellValue('BP'.$rowCount, $lineaObservaciones2);
			$objPHPExcel->getActiveSheet()->SetCellValue('BQ'.$rowCount, $obraEntrega);
			$objPHPExcel->getActiveSheet()->SetCellValue('BR'.$rowCount, $remitidaPor);
			$objPHPExcel->getActiveSheet()->SetCellValue('BS'.$rowCount, $embaladoPor);
			$objPHPExcel->getActiveSheet()->SetCellValue('BT'.$rowCount, $atencionA);
			$objPHPExcel->getActiveSheet()->SetCellValue('BU'.$rowCount, $referencia);
			$objPHPExcel->getActiveSheet()->SetCellValue('BV'.$rowCount, $numPedido);
			$objPHPExcel->getActiveSheet()->SetCellValue('BW'.$rowCount, $fechaPedido);
			$objPHPExcel->getActiveSheet()->SetCellValue('BX'.$rowCount, $cobrado);
			$objPHPExcel->getActiveSheet()->SetCellValue('BY'.$rowCount, $tipoCreacion);
			$objPHPExcel->getActiveSheet()->SetCellValue('BZ'.$rowCount, $tipoRecibo);
			$objPHPExcel->getActiveSheet()->SetCellValue('CA'.$rowCount, $codigoReciboCreado); 
			*/
			
			$objPHPExcel->getActiveSheet()->SetCellValue('CB'.$rowCount, $traspasada);
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('CC'.$rowCount, $anotacionesPrivadas);
			$objPHPExcel->getActiveSheet()->SetCellValue('CD'.$rowCount, $documentosExternos);
			$objPHPExcel->getActiveSheet()->SetCellValue('CE'.$rowCount, $impresa);
			$objPHPExcel->getActiveSheet()->SetCellValue('CF'.$rowCount, $bancoCliente);
			$objPHPExcel->getActiveSheet()->SetCellValue('CG'.$rowCount, $horaCreacion);
			$objPHPExcel->getActiveSheet()->SetCellValue('CH'.$rowCount, $comentariosImprimir);
			$objPHPExcel->getActiveSheet()->SetCellValue('CI'.$rowCount, $usuarioCreador);
			$objPHPExcel->getActiveSheet()->SetCellValue('CJ'.$rowCount, $usuarioModificador);
			$objPHPExcel->getActiveSheet()->SetCellValue('CK'.$rowCount, $fax);
			$objPHPExcel->getActiveSheet()->SetCellValue('CL'.$rowCount, $imagen);
			$objPHPExcel->getActiveSheet()->SetCellValue('CM'.$rowCount, $importeNetoExento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CN'.$rowCount, $porcentajeDescuentoExento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CO'.$rowCount, $importeDescuentoExento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CP'.$rowCount, $porcentajeProntoExento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CQ'.$rowCount, $importeProntoExento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CR'.$rowCount, $porcentajePortesExento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CS'.$rowCount, $importePortesExento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CT'.$rowCount, $porcentajeFinanciacionExento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CU'.$rowCount, $importeFinanciacionExento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CV'.$rowCount, $baseImponibleExenta);
			$objPHPExcel->getActiveSheet()->SetCellValue('CW'.$rowCount, $enviadoPorEmail);
			$objPHPExcel->getActiveSheet()->SetCellValue('CX'.$rowCount, $permisosYcontrasena);
			$objPHPExcel->getActiveSheet()->SetCellValue('CY'.$rowCount, $ticketPorcentajeDescuento);
			$objPHPExcel->getActiveSheet()->SetCellValue('CZ'.$rowCount, $ticketImporteDescuento);		
			$objPHPExcel->getActiveSheet()->SetCellValue('DA'.$rowCount, $caja);
			$objPHPExcel->getActiveSheet()->SetCellValue('DB'.$rowCount, $ibanBanco);
			$objPHPExcel->getActiveSheet()->SetCellValue('DC'.$rowCount, $bicBanco);
			$objPHPExcel->getActiveSheet()->SetCellValue('DD'.$rowCount, $nombreBanco);
			$objPHPExcel->getActiveSheet()->SetCellValue('DE'.$rowCount, $entidadCuentaCliente);
			$objPHPExcel->getActiveSheet()->SetCellValue('DF'.$rowCount, $oficinaCuentaCliente);
			$objPHPExcel->getActiveSheet()->SetCellValue('DG'.$rowCount, $digitosControlCliente);
			$objPHPExcel->getActiveSheet()->SetCellValue('DH'.$rowCount, $numeroCuentaCliente); 
			*/
		
			$rowCount++;
			
			$direccion = pg_escape_string($direccion);
			$municipio = pg_escape_string($municipio);
			$nombreCliente = pg_escape_string($nombreCliente);
			
			$sql2 = "UPDATE factura_factura SET dni='$nif', nombre='$nombreCliente', direccion='$direccion', \"provinciaNombre\"='$provinciaNombre', cp='$cp', municipio='$municipio'  WHERE \"InvoiceNumber\"='$InvoiceNumber'";
			posgre_query($sql2);
		}
		
	}
	
	
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter->save('./files/FAC.xls');
	/*
	$path = './files/FAC.xls';
	ob_clean();
	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.basename($path).'"');
	header("Content-Transfer-Encoding: binary");
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Type: application/force-download");
	header("Content-Type: application/download");
	header("Content-Length: ".filesize($path));
	readfile($path);
	*/
	
	/*
	
	ob_clean();
	// We'll be outputting an excel file
	header('Content-type: application/vnd.ms-excel');
	
	// It will be called file.xls
	header('Content-Disposition: attachment; filename="FAC.xls"');
	
	// Write file to the browser
	$objWriter->save('php://output');
	*/
	
	// Líneas de facturas emitidas
	$objPHPExcel->disconnectWorksheets();
	unset($objPHPExcel);
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$result = posgre_query($sql);
	while($row = pg_fetch_array($result)){
		$InvoiceNumber = $row['InvoiceNumber'];
		
		$sql2 = "SELECT * FROM factura_subfactura WHERE borrado=0 AND \"InvoiceNumber\"='$InvoiceNumber' AND \"InvoiceNumber\" IN (SELECT \"InvoiceNumber\" FROM factura_factura WHERE exportada=0) ORDER BY \"InvoiceNumber\"";
		$result2 = posgre_query($sql2);
		
		$posicion=1;
		while ($row2 = pg_fetch_array($result2)){
			
			$ItemDescription = $row2['ItemDescription'];
			$Quantity = $row2['Quantity'];
			$UnitPriceWithoutTax = $row2['UnitPriceWithoutTax'];
					
			$numsfacturas = explode("-",$InvoiceNumber);
			$tipoDocumento = $numsfacturas[0];
			$numeroDocumento = $numsfacturas[1];
			
			$posicionLinea=$posicion;
			$articulo=$cod.$idgenerica;
			$descripcion=$ItemDescription;
			$cantidad=$Quantity;
			$descuento1="";
			$descuento2="";
			$descuento3="";
			$preciounidad=$UnitPriceWithoutTax;
			$preciototal=number_format($preciounidad*$cantidad, 2, '.', '');
			$tipoIVA=0;
			$documentoCreador="";
			$tipoDocumentoCreador="";
			$codigoDocumentoCreador="";
			$precioCosto="";
			$bultos="";
			$comisionAgente="";
			$usoInterno=""; // Vacio siempre
			$ejercicioValidacion = "";
			$alto="";
			$ancho="";
			$fondo="";
			// $usoInterno
			// $usoInterno
			$ivaIncluido=0;
			$precioIvaIncluido="";
			$totalIvaIncluido="";
			$talla="";
			$color="";
			$imagen="";
		
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $tipoDocumento);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $numeroDocumento);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $posicionLinea);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $articulo);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $descripcion);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $cantidad);
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $descuento1);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $descuento2);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $descuento3);
			*/
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $preciounidad);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $preciototal);
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $tipoIVA);
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $documentoCreador);
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $tipoDocumentoCreador);
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $codigoDocumentoCreador);
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $precioCosto);
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $bultos);
			$objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $comisionAgente);
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $usoInterno);
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $ejercicioValidacion);
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $alto);
			$objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $ancho);
			$objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $fondo);
			$objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $usoInterno);
			$objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $usoInterno);
			$objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, $ivaIncluido);
			$objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $precioIvaIncluido);
			$objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $totalIvaIncluido);
			$objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $talla);
			$objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowCount, $color);
			$objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowCount, $imagen);
			*/
			
			$posicion++;
			$rowCount++;
		
		}
		
		$sql2 = "UPDATE factura_factura SET cerrada='1', exportada=1 WHERE \"InvoiceNumber\"='$InvoiceNumber'";
		posgre_query($sql2);
	}
	
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter->save('./files/LFA.xls');
	
	
	// Clientes
	$objPHPExcel->disconnectWorksheets();
	unset($objPHPExcel);
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	$rowCount = 1;
	$result = posgre_query($sql);
	while($row = pg_fetch_array($result)){
		$InvoiceNumber = $row['InvoiceNumber'];
		$idusuario = $row['idusuario'];
		
		
		$sql3 = "SELECT * FROM usuario WHERE id='$idusuario' AND exportadofactusol=0";
		$result3 = posgre_query($sql3);
		if ($row3 = pg_fetch_array($result3)){
			
			$codigoUsuario = $idusuario;
			$pais = "ESPAÑA";
			
			$sql2 = "SELECT * FROM factura_factura WHERE borrado=0 AND \"InvoiceNumber\"='$InvoiceNumber'";
			$result2 = posgre_query($sql2);
			
			if ($row2 = pg_fetch_array($result2)){
				$nif = $row2["dni"];
				$nombreFiscal = $row2["nombre"];
				$domicilio = $row2["direccion"];
				$poblacion = $row2["municipio"];
				$codigoPostal = $row2["cp"];
				$provincia = $row2["provinciaNombre"];
					
			}
			
			$telefono = $row3["telefono2"];
			$movil = $row3["telefono"];
			$email = $row3["email"];
			$iban = $row3["iban"];
			include_once "../librerias/swift_codes.php";
			$ibanes = explode("-",$iban);
			$swift = getSwiftBicCode($ibanes[1]);
			$iban = implode($ibanes);
			$identificacionFiscal="1";
			
			$banco = "";
			$entidad = $ibanes[1];
			$oficina = $ibanes[2];
			$digitoControl = substr($ibanes[3],0,2);
			$cuenta = substr($ibanes[3],-2).$ibanes[4].$ibanes[5];
			
			
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $codigoUsuario);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $codigoUsuario);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $nif);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $nombreFiscal);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $domicilio);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $poblacion);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $codigoPostal, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $provincia);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $pais);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $telefono);
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $movil);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $banco);
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $entidad);
			$objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $oficina);
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $digitoControl);
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $cuenta);
			
			$objPHPExcel->getActiveSheet()->SetCellValue('AP'.$rowCount, $email);
			$objPHPExcel->getActiveSheet()->SetCellValue('BF'.$rowCount, $iban);
			$objPHPExcel->getActiveSheet()->SetCellValue('BG'.$rowCount, $swift);
			$objPHPExcel->getActiveSheet()->SetCellValue('CZ'.$rowCount, $identificacionFiscal);
			
			$rowCount++;
			
			$sql = "UPDATE usuario SET exportadofactusol=1 WHERE id='$idusuario'";
			posgre_query($sql);
		}
		
	}
	
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter->save('./files/CLI.xls');
	
	
	$path = "./files/activatie_facturacion_".$cursonombre.time().".zip";
	$zip = new ZipArchive();
	if ($zip->open($path, ZipArchive::CREATE)!==TRUE) {
		exit("cannot open <$path>\n");
	}
	$zip->addFile('./files/FAC.xls', "FAC.xls");
	$zip->addFile('./files/LFA.xls', "LFA.xls");
	$zip->addFile('./files/CLI.xls', "CLI.xls");
	$zip->close();
	
	ob_clean();
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=\"".basename($path)."\"");
	header("Content-type: application/octet-stream");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($path));
	ob_clean();
	readfile($path);

}


function imprimirXLS($idcurso=0, $fecha1, $fecha2, $rango1, $rango2){
	
	$sqlcurso = "";
	if (($idcurso<>0)||($idcurso<>"")){
		$sqlcurso .= " AND idgenerica='$idcurso' AND tipo=1 ";
	}
	
	$sqlfecha1="";
	if ($fecha1<>""){
		$sqlfecha1 = " AND to_date(\"IssueDate\",'DD-MM-YYYY')>=to_date('$fecha1','DD-MM-YYYY') ";
	}

	$sqlfecha2="";
	if ($fecha2<>""){
		$sqlfecha2 = " AND to_date(\"IssueDate\",'DD-MM-YYYY')<=to_date('$fecha2','DD-MM-YYYY') ";
	}
	
	$sqlrango1="";
	if ($rango1<>""){
		$sqlrango1 = " AND \"InvoiceNumber\">='$rango1' ";
	}
	
	$sqlrango2="";
	if ($rango2<>""){
		$sqlrango2 = " AND \"InvoiceNumber\"<='$rango2' ";
	}
	
	
	// Exportar facturas de activatie a Excel para importar en Factusol. Documentación en ./contabilidad/FactuSOL Importacion Excel Calc.pdf
	require_once dirname(__FILE__) . '/../librerias/PHPExcel/Classes/PHPExcel.php';
	
	// Facturas emitidas
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
	
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Nº Factura");
	$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Fecha");
	$objPHPExcel->getActiveSheet()->SetCellValue('C1', "Exportada");
	$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Código cliente");
	$objPHPExcel->getActiveSheet()->SetCellValue('E1', "NIF");
	$objPHPExcel->getActiveSheet()->SetCellValue('F1', "Nombre");
	$objPHPExcel->getActiveSheet()->SetCellValue('G1', "Forma de pago");
	$objPHPExcel->getActiveSheet()->SetCellValue('H1', "Producto");	
	$objPHPExcel->getActiveSheet()->SetCellValue('I1', "Organiza");
	$objPHPExcel->getActiveSheet()->SetCellValue('J1', "Base imp.");
	$objPHPExcel->getActiveSheet()->SetCellValue('K1', "IVA");
	$objPHPExcel->getActiveSheet()->SetCellValue('L1', "Total");
	
	$rowCount = 2;
	
	
	$sql = "SELECT * FROM factura_factura WHERE borrado=0 $sqlcurso $sqlfecha1 $sqlfecha2 $sqlrango1 $sqlrango2 ORDER BY \"InvoiceNumber\"";
	$result = posgre_query($sql);
	
	while ($row = pg_fetch_array($result)){
		
		
		$idusuario = $row['idusuario'];
		$idgenerica  = $row['idgenerica'];
		$invoiceNumber = $row['InvoiceNumber'];
		$issueDate = $row['IssueDate'];
		$iva = $row['TaxRate'];
		$formapago = $row['formapago'];
		$exportada = $row['exportada'];
		$plazo = $row['plazo'];
		$tipo = $row['tipo'];
		
		if ($tipo==1){
			$sql3 = "SELECT * FROM curso WHERE id='$idgenerica'";
			$result3 = posgre_query($sql3);
			$row3 = pg_fetch_array($result3);
			$colegioOrganizador = $row3["idcolegio"];
			
			$sql3 = "SELECT * FROM usuario WHERE id='$colegioOrganizador'";
			$result3 = posgre_query($sql3);
			$row3 = pg_fetch_array($result3);
			$nombreOrganizador = $row3["nombre"];
			
		}
		
		if ($exportada==1){
			$exportadaTexto="Si";
		}
		else{
			$exportadaTexto="No";
		}
		
		if ($formapago==0){
			$formapagotexto = "Transferencia";
		}
		elseif ($formapago==1){
			$formapagotexto = "Tarjeta";
		}
		elseif ($formapago==2){
			$formapagotexto = "Domiciliación. Cargo ".$plazo;
		}
		
		$sql2 = "SELECT * FROM factura_subfactura WHERE \"InvoiceNumber\"='$invoiceNumber' AND borrado=0";
		$result2 = posgre_query($sql2);
		$subtotal=0;
		
		while ($row2 = pg_fetch_array($result2)){
		
			$ItemDescription=($row2['ItemDescription']);
			$Quantity=$row2['Quantity'];
			$UnitPriceWithoutTax=$row2['UnitPriceWithoutTax'];
			$totalproducto = number_format($UnitPriceWithoutTax*$Quantity,2,'.','');
			$subtotal += $totalproducto;
			
		}
		
		
		$sql6 = "SELECT * FROM usuario WHERE id IN (SELECT idusuario FROM factura_factura WHERE \"InvoiceNumber\"='$invoiceNumber' AND borrado=0)";
		$result6 = posgre_query($sql6);
		while ($row6 = pg_fetch_array($result6)){

			$nombre=ucwords(strtolower($row6['nombre']));
			$nif=($row6['nif']);
			$apellidos=ucwords(strtolower($row6['apellidos']));
			$nombre = $nombre." ".$apellidos;
			$idempresa=$row6['idempresa'];
		}
		
		if ($idempresa>0){
			$sql5 = "SELECT * FROM usuario WHERE id='$idempresa'";
			$result5 = posgre_query($sql5);
			if ($row5 = pg_fetch_array($result5)){
				$nombre = $row5['nombre'];
				$nif=($row5['nif']);
				$apellidos="";
					
			}
		
		}
		
		if ($exportada==1){
			$nif = $row['dni'];
			$nombre = $row['nombre'];
			$apellidos="";
			
			if ($relleno<>1){
				$nombre = ucwords(strtolower($row['nombre']));
			}
		
		}
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $invoiceNumber);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $issueDate);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $exportadaTexto);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $idusuario);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $nif);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $nombre);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $formapagotexto);
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $ItemDescription);	
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $nombreOrganizador);
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $totalproducto);
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $iva);
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $subtotal);
		
		

		$rowCount++;
		
		
		
	}
	
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$date = date("d-m-Y");
	$nombre  = "activatie_facturación_XLS_".$date;
	$objWriter->save('./files/'.$nombre.'.xls');
	$path = './files/'.$nombre.'.xls';
	ob_clean();
	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.basename($path).'"');
	header("Content-Transfer-Encoding: binary");
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Type: application/force-download");
	header("Content-Type: application/download");
	header("Content-Length: ".filesize($path));
	readfile($path);
}

/** No se usa **/
function anularFactura($invoice){

	$invoiceBorrada = $invoice."_borrada";
	$sql = "UPDATE factura_factura SET borrado=1, \"InvoiceNumber\"='$invoiceBorrada' WHERE \"InvoiceNumber\"='$invoice' AND borrado=0";
	$result = posgre_query($sql);
	$sql = "UPDATE factura_subfactura SET borrado=1, \"InvoiceNumber\"='$invoiceBorrada' WHERE \"InvoiceNumber\"='$invoice' AND borrado=0";
	$result = posgre_query($sql);
	
	if ($result){
		return 1;
	}
	else{
		return 0;
	}
}

/** Genera nuevo número de factura **/

function generateInvoiceNumber($tipo, $rectificativa){

	if ($tipo==1){	// Cambiar a variable externa
		$pre = "1";
	}
	elseif ($tipo==2){
		$pre = "2";
	}
	
	if ($rectificativa==1){
		$pre = "9";
	}
	
	if ($rectificativa==2){
		$pre = "P";
	}
	
	$existe = true;
	$i=1;
	while ($existe){
	
		$tamaño = strlen($i);
		$ceros = 4 - $tamaño;
		
		$ano = date("y");
		
		$invoicenumber = $pre."-".$ano;
		for ($j=0;$j<$ceros;$j++){
			$invoicenumber.=0;
		}
		$invoicenumber.=$i;

		$sql = "SELECT * FROM factura_factura WHERE borrado=0 AND \"InvoiceNumber\"='$invoicenumber'";
		$result = pg_query($sql);

		if (pg_num_rows($result)==0){
			$existe=false;
		}

		$i++;	
	}
	
	return $invoicenumber;
	
}

function getInvoiceNumber($idusuario, $tipo, $idgenerica, $rectificativa=0){
	$sql = "SELECT \"InvoiceNumber\" FROM factura_factura WHERE borrado=0 AND idusuario='$idusuario' AND idgenerica='$idgenerica' AND tipo='$tipo' AND rectificativa='$rectificativa'";

	$result = posgre_query($sql);
	if ($row = pg_fetch_array($result)){
		$InvoiceNumber = $row['InvoiceNumber'];
		return $InvoiceNumber;
	}
	else return 0;
}


?>