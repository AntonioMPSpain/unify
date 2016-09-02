<?
session_start();
include_once "_funciones.php"; 
include_once "_cone.php"; 
include_once "plantillaemail.php"; 



function getEmail($idusuario){

	$result = posgre_query("SELECT * FROM usuario WHERE id = '$idusuario'" );
	$usuario = pg_fetch_array($result);
	$email = $usuario["email"];
	
	return $email;
}


/* Función para enviar email */
function enviarEmail($para, $asunto ,$cuerpo, $dominio=1){
	
		$asunto = ($asunto);
		
		$cuerpoplano=strip_tags ($cuerpo);
		//para el envío en formato HTML 
		//creamos un identificador único
		//para indicar que las partes son idénticas
		if ($dominio==1){
			$uniqueid= uniqid('info@activatie.org');
		}
		elseif ($dominio==2){
			$uniqueid= uniqid('info@tuedificioenforma.es');
		}
		 
		//indicamos las cabeceras del correo
		$headers = "MIME-Version: 1.0\r\n";
		if ($dominio==1){
			$headers .= "From: info@activatie.org \r\n";
		}
		elseif ($dominio==2){
			$headers .= "From: info@tuedificioenforma.es \r\n";
		}
		//$headers .= "Subject: ".$asuntoemail."\r\n";
		//lo importante es indicarle que el Content-Type
		//es multipart/alternative para indicarle que existirá
		//un contenido alternativo
		
		//$headers .= "Content-Type: multipart/alternative;boundary=" . $uniqueid. "\r\n";
		$headers .= "Content-Type: text/html;charset=utf-8\r\n\r\n";
		$message = "";
		
		/*
		$message .= "\r\n\r\n--" . $uniqueid. "\r\n";
		$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
		$message .= $cuerpoplano;
		$message .= "\r\n\r\n--" . $uniqueid. "\r\n";
		$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
		*/
		
		$message .= $cuerpo;
		
		$exito=mail($para, $asunto, $message, $headers);	
		
		return $exito;

}

/* Envia email para confirmar/activar la cuenta al usuario que tiene idusuario */
function enviarEmailConfirmacion($idusuario){

	$email = getEmail($idusuario);
	
	$idalta = "at3se4daa".md5($email)."f3o5ladg";
	$sql = "UPDATE usuario SET idalta='$idalta' WHERE id='$idusuario'";
	posgre_query($sql);
	
	$asunto = getEmailConfirmacionAsunto();
	$textoemail = getEmailConfirmacionCuerpo($idusuario);
	$text = urlencode($textoemail);
	$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	
	$exito = enviarEmail($email, $asunto, $textoemailenvio);
	
	return $exito;
}

function enviarEmailListaDeEsperaAInscrito($idusuario, $idcurso){


	$email = getEmail($idusuario);
	$asunto = getAsuntoListaDeEsperaAInscrito($idcurso);
	$textoemail = getTextoListaDeEsperaAInscrito($idusuario, $idcurso);
	$text = urlencode($textoemail);
	$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	
	$exito = enviarEmail($email, $asunto, $textoemailenvio);

}

function enviarEmailDarDeBaja($idusuario, $idcurso){

	$email = getEmail($idusuario);
	$asunto = getAsuntoDarDeBaja($idcurso);
	$textoemail = getTextoDarDeBaja($idusuario, $idcurso);
	$text = urlencode($textoemail);
	$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	
	$exito = enviarEmail($email, $asunto, $textoemailenvio);


}


function enviarEmailOlvidoDePass($idusuario){

	$email = getEmail($idusuario);
	$asunto = getAsuntoOlvidoDePass();
	$textoemail = getTextoOlvidoDePass($idusuario);
	$text = urlencode($textoemail);
	$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	
	$exito = enviarEmail($email, $asunto, $textoemailenvio);

}

function enviarEmailAltaNewsletter($email){

	$asunto = getAsuntoAltaNewsletter();
	$textoemail = getTextoAltaNewsletter();
	$text = urlencode($textoemail);
	$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	
	$exito = enviarEmail($email, $asunto, $textoemailenvio);

}

function enviarEmailBajaNewsletter($email, $idborrado){

	$asunto = getAsuntoBajaNewsletter();
	$textoemail = getTextoBajaNewsletter($email, $idborrado);
	$text = urlencode($textoemail);
	$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	
	$exito = enviarEmail($email, $asunto, $textoemailenvio);

}

function enviarEmailPagoTransferenciaValidado($idusuario, $idcurso){

	$email = getEmail($idusuario);
	$asunto = getAsuntoPagoTransferenciaValidado();
	$textoemail = getTextoPagoTransferenciaValidado($idusuario,$idcurso);
	$text = urlencode($textoemail);
	$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	
	$exito = enviarEmail($email, $asunto, $textoemailenvio);

}

function enviarEmailDarDiploma($idusuario, $idcurso){

	$email = getEmail($idusuario);
	$asunto = getAsuntoDarDiploma($idcurso);
	$textoemail = getTextoDarDiploma($idusuario, $idcurso);
	$text = urlencode($textoemail);
	$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	
	$exito = enviarEmail($email, $asunto, $textoemailenvio);


}

function enviarEmailDarDiplomaNOApto($idusuario, $idcurso){

	$email = getEmail($idusuario);
	$asunto = getAsuntoDarDiplomaNOApto($idcurso);
	$textoemail = getTextoDarDiplomaNOApto($idusuario, $idcurso);
	$text = urlencode($textoemail);
	$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	
	$exito = enviarEmail($email, $asunto, $textoemailenvio);


}

?>