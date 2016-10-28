<?

include_once "_funciones.php"; 
include_once "_cone.php"; 


/*
if (isset($_GET['convertirEmpresas'])){
	$sql = "SELECT * FROM factura_comprador WHERE relleno='1' ORDER BY idusuario";
	$result = posgre_query($sql);
	while ($row = pg_fetch_array($result)){
		$idusuario = $row['idusuario'];
		$cif = $row['buy_TaxIdentificationNumber'];
		$nombre = $row['Individual_Name'];
		$direccion = $row['Individual_Address'];
		$provincia = $row['Individual_Province'];
		$municipio = $row['Individual_Town'];
		$cp = $row['Individual_PostCodeAndTown'];
		
		$sql4 = "SELECT id FROM etiqueta_provincia WHERE deno='$provincia'";
		$result4 = posgre_query($sql4);
		if ($row4 = pg_fetch_array($result4)){
			$idprovincia = $row4['id'];
		}
		
		echo $idusuario."<br>";
		echo $cif."<br>";
		echo $nombre."<br>";
		echo $direccion."<br>";
		echo $idprovincia."<br>";
		echo $municipio."<br>";
		echo $cp."<br>";
		
		
		$idempresa = crearActualizarEmpresa($cif, $nombre, $direccion, $idprovincia, $municipio, $cp);
		echo "<br>".$idempresa."<br>";
		
		echo "<br><br><br><br><br>";
		if ($idempresa>0){
			$sql = "UPDATE usuario SET idempresa='$idempresa' WHERE id='$idusuario'";
			posgre_query($sql);
		}
	}
}
*/

/*
if (isset($_GET['test'])){
	echo generarIDempresa(); 
}
*/



/**
* crea empresa como un usuario más, con sus propiedades y genera sus datos de facturación
*/
function crearActualizarEmpresa($cif, $nombre, $direccion, $idprovincia, $municipio, $cp, $pais){
	
	if (!existeEmpresa($cif)){
		
		$idempresa = generarIDempresa();
		$login = solonumeros($cif);
		
		$sql="INSERT INTO usuario (id, nivel, confirmado, nombre, nif, login, direccion, idprovincia, municipio, cp, pais) VALUES ('$idempresa','9','0','$nombre','$cif','$login', '$direccion', '$idprovincia', '$municipio', '$cp', '$pais')";
		
		if ($result = posgre_query($sql)){
			return $idempresa;
		}
		else {
			return 0;
		}
	}
	else{
		$idempresa = getIDempresa($cif);
		
		$sql="UPDATE usuario SET nombre='$nombre', direccion='$direccion', idprovincia='$idprovincia', municipio='$municipio', cp='$cp', pais='$pais' WHERE id='$idempresa' AND nivel='9'";
		posgre_query($sql);
		
		$sql = "UPDATE usuario SET exportadofactusol=0 WHERE id='$idempresa' AND nivel='9'";
		posgre_query($sql);
		
		return $idempresa;
	}
}

/**
* Existe empresa, devuelve true si existe, false si no
*/
function existeEmpresa($cif){
	$sql = "SELECT * FROM usuario WHERE nif='$cif'";
	$result = posgre_query($sql);
	if (pg_num_rows($result)>0){
		return true;
	}
	else{
		return false;
	}
}


/**
* get identificador empresa, 0 si no existe
*/
function getIDempresa($cif){
	if (existeEmpresa($cif)){
		
		$sql = "SELECT * FROM usuario WHERE nif='$cif' AND nivel='9'";
		$result = posgre_query($sql);
		if ($row = pg_fetch_array($result)){
			return $row['id'];
		}
	}
	else{
		return 0;
	}
}

/**
* Genera id empresa, tabla usuarios empezando por 99.999 para abajo(por razones de compatibilidad con contabilidad facturación)
*/
function generarIDempresa(){
	$id = 99999;
	$existe = true;
	
	while ($existe){
		$sql = "SELECT * FROM usuario WHERE id='$id'";
		$result = posgre_query($sql);
		if (pg_num_rows($result)==0){
			$existe = false;
		}
		else{
			$id--;
		}
	}
	
	return $id;
	
}

?>