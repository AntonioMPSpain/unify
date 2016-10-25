<?php
session_start();
include_once "_cone.php";
include_once "a_curso_plazas_libres.php";

if (isset($_GET['plantilla'])){
	
	/* 
		Para coger con file_get_contents y el id de email (cron50)
		Ejemplo:
		$body = file_get_contents('https://www.activatie.org/web/plantillaemail.php?plantilla&idemail='.$idemail); 
	*/
	
	if (isset($_GET['novisorweb'])){
		$visorweb=0;
	}
	else{
		$visorweb=1;
	}
	
	if (isset($_GET['idemail'])){
		getPlantillaEmail($_GET['idemail'],"",$visorweb);
	}
	/*
		Para coger con file_get_contents y el texto en parámetro get
		Ejemplo:
		$text = urlencode($textoemail);
		$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
	*/
	elseif (isset($_GET['texto'])){
		getPlantillaEmail(0,$_GET['texto']);
	}
	/* 
		Para coger para un iframe. Imprime html directamente 
	*/
	else{
		getPlantillaEmail();
	}
	
}



function getAsuntoOlvidoDePass(){
	$asunto="[activatie] Nueva contraseña";
	return $asunto;
}

function getTextoOlvidoDePass($idusuario){

	$sql="SELECT * FROM usuario WHERE id='$idusuario'";
	$result = posgre_query($sql);
	if ($row = pg_fetch_array($result)){
		$dni = $row["login"];
		$email = $row['email'];
		$idpass = $row['idpass'];
		
		$texto='<p>Pulse en el siguiente link para establecer una nueva contrase&ntilde;a para su usuario con nif '.$dni.'.
		 <a href="http://'.$_SERVER['HTTP_HOST'].'/web/usuarioolvidapass.php?accion=nueva&amp;email='.$email.'&amp;idp='.$idpass.'">Nueva contrase&ntilde;a</a></p>';

		return $texto;
	}




}

function getEmailConfirmacionAsunto(){
	$asunto="[activatie] Confirmar usuario";
	return $asunto;
}

function getEmailConfirmacionCuerpo($idusuario){

	$sql="SELECT * FROM usuario WHERE id='$idusuario'";
	$result = posgre_query($sql);
	if ($row = pg_fetch_array($result)){
		$idalta=$row['idalta'];
		$email=$row['email'];
		
		$texto='<h3>¡Bienvenido a la red profesional activatie!</h3>
		<p>Para poder utilizar nuestros servicios debe confirmar su cuenta pinchando <a href="http://www.activatie.org/web/web_activa.php?accion=activar&ide='.$idalta.'&email='.$email.'">aquí</a><br /></p>';

		return $texto;
	}



}

function getAsuntoAltaNewsletter(){
	$asunto="[activatie] Alta de suscripción";
	return $asunto;
}

function getTextoAltaNewsletter(){
	$texto='<p>Alta de suscripción realizada correctamente.</p>
	<p>Bienvenido a activatie, le mantendremos informado.</p>
	<br />';
	return $texto;
}

function getAsuntoBajaNewsletter(){
	$asunto="[activatie] Baja de suscripción";
	return $asunto;
}

function getTextoBajaNewsletter($email, $idborrado){
	$texto='<p>Baja de suscripción</p>
	<p><a href="http://www.activatie.org/web/index.php?accion=baja&email='.$email.'&idp='.$idborrado.'">Pinche aquí para confirmar su baja</a></p>
	<br />';
	return $texto;
}



function getAsuntoPagoTransferenciaValidado(){
	$asunto="[activatie formación] Confirmación de pago";
	return $asunto;
}	

function getTextoPagoTransferenciaValidado($idusuario, $idcurso){
	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$modalidad = $curso["modalidad"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$idcolegio = $curso["idcolegio"];
	$lugar="";
	$lugar = $curso["lugar"];
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);
	
	$nombreusuario = $usuario["nombre"];
	
	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	
	if ($precio==0){
		$preciotexto = "Gratuito";
	}
	else{
		$preciotexto = $precio."€";
	}
	
	$rowcolegio=posgre_query("SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0;") ;
	$colegio= pg_fetch_array($rowcolegio);
	
	$correocolegio = $colegio["email"];

	$texto = "";
	$texto .= "<p>Hola $nombreusuario!</p>"; 
	 
	 
	 
	$texto .= "<p>Le informamos que el documento solicitado ha sido validado, por lo que su inscripción en la siguiente actividad se ha realizado correctamente. <br><br>";
	$texto .= "ACTIVIDAD: <b>$nombrecurso</b><br>";
	if ($fecha_inicio<>""){
		$texto .= "FECHA DE INICIO: <b>$fecha_inicio</b><br>";	 
	}


	if ($modalidad<>3){	
		$texto .= "<p><b>El día de inicio de la actividad</b> podrá acceder a la información para conectarse a la sesión online así como al contenido del curso, identificandose en Acceso a usuarios de la página principal de activatie, y en la pestaña Mis cursos, le aparecerá el botón Acceder. </p>";
		$texto .= "<p>Condiciones de baja:<strong></strong><br>Las bajas en la actividad deberán ser comunicadas por escrito  al correo <a href=\"mailto:info@activatie.org\">$correocolegio</a>, como mínimo 5 días antes del inicio de la misma, para optar a la devolución del importe abonado.</p>";
	}  
	else{
		$texto .= "<p>Podrá acceder al contenido (documentación, grabación de las sesiones que se irán subiendo conforme se desarrolle la actividad, ejercicios, etc.), debe identificarse en Acceso a usuarios de la página principal de activatie, y en la pestaña Mis cursos, le aparecerá el botón Acceder. </p>";	
	}
	 
	$texto .= "<p>Recuerde revisar sus datos de facturación <b>en caso de que sean distintos a sus datos personales</b>, desde su \"Área personal\" de activatie, en el apartado \"Mis datos\", \"Datos de facturación\". </p>";
	 
	$texto .= "<p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p><br><br>";

	return $texto;
}

function getNewsletter($curso1, $curso2, $curso3, $curso4, $curso5, $curso6, $publi1, $publi2, $publi3, $trabajo1, $trabajo2, $trabajo3){

	
	if ($curso1<>""){
		$sql = "SELECT * FROM curso WHERE id='$curso1'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$curso1nombre=$row["nombre"];
		$curso1imagen=$row["imagen"];
		$curso1fechainicio=cambiaf_a_normal($row["fecha_inicio"]);
		if ($curso1fechainicio<>""){
			$curso1fechainicio.="<br>";
		}
		
		if ($row["modalidad"]==0) $curso1modalidad='on-line'; 
		if ($row["modalidad"]==1) $curso1modalidad='presencial';
		if ($row["modalidad"]==2) $curso1modalidad='presencial y on-line';
		if ($row["modalidad"]==3) $curso1modalidad='permanente';
		
		$linket=iConectarse(); 
		$resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$curso1' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		if (pg_num_rows($resultet)>0){
				$rowet = pg_fetch_array($resultet);
				$curso1etiqueta="";
				if ($rowet["texto"]<>""){
					$curso1etiqueta=="[".$rowet["texto"]."]<br>"; 	
				} 			
		 }
		 
		 $precio = "";
		if ($row["modalidad"]==0){
			$precio = $row["precioco"];
		}
		elseif ($row["modalidad"]==1){
			$precio = $row["precioc"];
		}
		elseif ($row["modalidad"]==3){
		
			$precio = $row["preciocp"];
		}
		else{
			$precio = $row["precioco"];
		}
		
		$preciotexto="";
		if ($precio==0){
			$curso1preciotexto = "¡Gratuito!";
		}
	}
	
	if ($curso2<>""){
		$sql = "SELECT * FROM curso WHERE id='$curso2'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$curso2nombre=$row["nombre"];
		$curso2imagen=$row["imagen"];
		$curso2fechainicio=cambiaf_a_normal($row["fecha_inicio"]);
		if ($curso2fechainicio<>""){
			$curso2fechainicio.="<br>";
		}
		
		if ($row["modalidad"]==0) $curso2modalidad='on-line'; 
		if ($row["modalidad"]==1) $curso2modalidad='presencial';
		if ($row["modalidad"]==2) $curso2modalidad='presencial y on-line';
		if ($row["modalidad"]==3) $curso2modalidad='permanente';
		
		$linket=iConectarse(); 
		$resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$curso2' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		if (pg_num_rows($resultet)>0){
				$rowet = pg_fetch_array($resultet);
				$curso2etiqueta="";
				if ($rowet["texto"]<>""){
					$curso2etiqueta=="[".$rowet["texto"]."]<br>"; 	
				}			
		 }
		 
		  $precio = "";
		if ($row["modalidad"]==0){
			$precio = $row["precioco"];
		}
		elseif ($row["modalidad"]==1){
			$precio = $row["precioc"];
		}
		elseif ($row["modalidad"]==3){
		
			$precio = $row["preciocp"];
		}
		else{
			$precio = $row["precioco"];
		}
		
		$preciotexto="";
		if ($precio==0){
			$curso2preciotexto = "¡Gratuito!";
		}
	}
	
	if ($curso3<>""){
		$sql = "SELECT * FROM curso WHERE id='$curso3'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$curso3nombre=$row["nombre"];
		$curso3imagen=$row["imagen"];
		$curso3fechainicio=cambiaf_a_normal($row["fecha_inicio"]);
		if ($curso3fechainicio<>""){
			$curso3fechainicio.="<br>";
		}
		
		if ($row["modalidad"]==0) $curso3modalidad='on-line'; 
		if ($row["modalidad"]==1) $curso3modalidad='presencial';
		if ($row["modalidad"]==2) $curso3modalidad='presencial y on-line';
		if ($row["modalidad"]==3) $curso3modalidad='permanente';
		
		$linket=iConectarse(); 
		$resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$curso3' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		if (pg_num_rows($resultet)>0){
				$rowet = pg_fetch_array($resultet);
				$curso3etiqueta="";
				if ($rowet["texto"]<>""){
					$curso3etiqueta=="[".$rowet["texto"]."]<br>"; 	
				}			
		}
		 
		 $precio = "";
		if ($row["modalidad"]==0){
			$precio = $row["precioco"]."€";
		}
		elseif ($row["modalidad"]==1){
			$precio = $row["precioc"]."€";
		}
		elseif ($row["modalidad"]==3){
		
			$precio = $row["preciocp"]."€";
		}
		else{
			$precio = $row["precioco"]."€";
		}
		
		$preciotexto="";
		if ($precio==0){
			$curso3preciotexto = "¡Gratuito!";
		}
	}
	
	if ($curso4<>""){
		$sql = "SELECT * FROM curso WHERE id='$curso4'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$curso4nombre=$row["nombre"];
		$curso4imagen=$row["imagen"];
		$curso4fechainicio=cambiaf_a_normal($row["fecha_inicio"]);
		if ($curso4fechainicio<>""){
			$curso4fechainicio.="<br>";
		}
		
		if ($row["modalidad"]==0) $curso4modalidad='on-line'; 
		if ($row["modalidad"]==1) $curso4modalidad='presencial';
		if ($row["modalidad"]==2) $curso4modalidad='presencial y on-line';
		if ($row["modalidad"]==3) $curso4modalidad='permanente';
		
		$linket=iConectarse(); 
		$resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$curso4' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		if (pg_num_rows($resultet)>0){
				$rowet = pg_fetch_array($resultet);
				$curso4etiqueta="";
				if ($rowet["texto"]<>""){
					$curso4etiqueta=="[".$rowet["texto"]."]<br>"; 	
				} 			
		 }
		 
		 $precio = "";
		if ($row["modalidad"]==0){
			$precio = $row["precioco"];
		}
		elseif ($row["modalidad"]==1){
			$precio = $row["precioc"];
		}
		elseif ($row["modalidad"]==3){
		
			$precio = $row["preciocp"];
		}
		else{
			$precio = $row["precioco"];
		}
		
		$preciotexto="";
		if ($precio==0){
			$curso4preciotexto = "¡Gratuito!";
		}
	}
	
	if ($curso5<>""){
		$sql = "SELECT * FROM curso WHERE id='$curso5'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$curso5nombre=$row["nombre"];
		$curso5imagen=$row["imagen"];
		$curso5fechainicio=cambiaf_a_normal($row["fecha_inicio"]);
		if ($curso5fechainicio<>""){
			$curso5fechainicio.="<br>";
		}
		
		if ($row["modalidad"]==0) $curso5modalidad='on-line'; 
		if ($row["modalidad"]==1) $curso5modalidad='presencial';
		if ($row["modalidad"]==2) $curso5modalidad='presencial y on-line';
		if ($row["modalidad"]==3) $curso5modalidad='permanente';
		
		$linket=iConectarse(); 
		$resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$curso5' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		if (pg_num_rows($resultet)>0){
				$rowet = pg_fetch_array($resultet);
				$curso5etiqueta="";
				if ($rowet["texto"]<>""){
					$curso5etiqueta=="[".$rowet["texto"]."]<br>"; 	
				} 			
		 }
		 
		 $precio = "";
		if ($row["modalidad"]==0){
			$precio = $row["precioco"];
		}
		elseif ($row["modalidad"]==1){
			$precio = $row["precioc"];
		}
		elseif ($row["modalidad"]==3){
		
			$precio = $row["preciocp"];
		}
		else{
			$precio = $row["precioco"];
		}
		
		$preciotexto="";
		if ($precio==0){
			$curso5preciotexto = "¡Gratuito!";
		}
	}
	
	if ($curso6<>""){
		$sql = "SELECT * FROM curso WHERE id='$curso6'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$curso6nombre=$row["nombre"];
		$curso6imagen=$row["imagen"];
		$curso6fechainicio=cambiaf_a_normal($row["fecha_inicio"]);
		if ($curso6fechainicio<>""){
			$curso6fechainicio.="<br>";
		}
		
		if ($row["modalidad"]==0) $curso6modalidad='on-line'; 
		if ($row["modalidad"]==1) $curso6modalidad='presencial';
		if ($row["modalidad"]==2) $curso6modalidad='presencial y on-line';
		if ($row["modalidad"]==3) $curso6modalidad='permanente';
		
		$linket=iConectarse(); 
		$resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$curso6' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		if (pg_num_rows($resultet)>0){
				$rowet = pg_fetch_array($resultet);
				$curso6etiqueta="";
				if ($rowet["texto"]<>""){
					$curso6etiqueta=="[".$rowet["texto"]."]<br>"; 	
				} 			
		 }
		 
		 $precio = "";
		if ($row["modalidad"]==0){
			$precio = $row["precioco"];
		}
		elseif ($row["modalidad"]==1){
			$precio = $row["precioc"];
		}
		elseif ($row["modalidad"]==3){
		
			$precio = $row["preciocp"];
		}
		else{
			$precio = $row["precioco"];
		}
		
		$preciotexto="";
		if ($precio==0){
			$curso6preciotexto = "¡Gratuito!";
		}
	}
		
	if ($publi1<>""){
		$sql = "SELECT * FROM generica WHERE id='$publi1'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$publi1nombre=$row["titulo"];
		$publi1precio=$row["precio"];
		$publi1imagen=$row["img2"];
		
		$publi1preciotexto="";
		if (($publi1precio!="") && ($publi1precio==0)){
			$publi1preciotexto="¡Gratuito!";
		}
		elseif ($publi1precio>0){
			$publi1preciotexto=$publi1precio."€";
		}
		
	}
	
	if ($publi2<>""){
		$sql = "SELECT * FROM generica WHERE id='$publi2'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$publi2nombre=$row["titulo"];
		$publi2precio=$row["precio"];
		$publi2imagen=$row["img2"];
		
		$publi2preciotexto="";
		if (($publi2precio!="") && ($publi2precio==0)){
			$publi2preciotexto="¡Gratuito!";
		}
		elseif ($publi2precio>0){
			$publi2preciotexto=$publi2precio."€";
		}
	}
	
	if ($publi3<>""){
		$sql = "SELECT * FROM generica WHERE id='$publi3'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$publi3nombre=$row["titulo"];
		$publi3precio=$row["precio"];
		$publi3imagen=$row["img2"];
		
		$publi3preciotexto="";
		if (($publi3precio!="") && ($publi3precio==0)){
			$publi3preciotexto="¡Gratuito!";
		}
		elseif ($publi3precio>0){
			$publi3preciotexto=$publi3precio."€";
		}
	}
	
	if ($trabajo1<>""){
		$sql = "SELECT * FROM trabajo WHERE id='$trabajo1'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$trabajo1nombre=$row["denominacion"];
		$trabajo1zona=$row["zona"];
		$trabajo1fecha=cambiaf_a_normal($row["fecha"]);
	}
	
	if ($trabajo2<>""){
		$sql = "SELECT * FROM trabajo WHERE id='$trabajo2'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$trabajo2nombre=$row["denominacion"];
		$trabajo2zona=$row["zona"];
		$trabajo2fecha=cambiaf_a_normal($row["fecha"]);
	}
	
	if ($trabajo3<>""){
		$sql = "SELECT * FROM trabajo WHERE id='$trabajo3'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$trabajo3nombre=$row["denominacion"];
		$trabajo3zona=$row["zona"];
		$trabajo3fecha=cambiaf_a_normal($row["fecha"]);
	}
	
$destacadosmedium='http://www.activatie.org/web/css/pics/h2-destacadosmedium.png';
$texto="";	
$texto.="	

				<!--Si la plantilla lleva imagen, no es necesario indicar el título.-->
			<!--<h2 style=\"font-family:Arial,Helvetica,sans-serif; font-size:22px; font-weight:bold;\">
					<b>Título del mensaje:</b>
			</h2><br>-->

<table align=\"left\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
	<tbody>

		<tr>
			<td align=\"justify\" width=\"100%\" height=\"100\" bgcolor=\"#ffffff\" >


						<!--Esta sería la forma de indicar enlaces-->
						<!--En este caso, como el link de comprar es una imagen, no es necesario.-->
						<!--<a href=\"#\" style=\"font-family: Arial,Helvetica,sans-serif; font-size:15px;background-color:#ffffff;color:#D72D13; text-decoration:none;\">LOREM IPSUM DOLOR SIT AMET</a>-->

			<br>
			<h2 style=\"display: block; -webkit-margin-before: 0.83em; -webkit-margin-after: 0.83em; -webkit-margin-start: 0px; -webkit-margin-end: 0px;  background: url($destacadosmedium) no-repeat scroll left top rgba(0, 0, 0, 0); color: #fff; font-size: 150%; line-height: 1em; margin-left: -0.95em !important; padding: 3px 0 13px 0.85em; font-family: Arial,Helvetica,FreeSans,'Liberation Sans','Nimbus Sans L',sans-serif; font-weight: bolder;\">Formación</h2>
			<table>
			<tr>
				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:189px; height:120px;\" src=\"http://www.activatie.org/web/imagen/$curso1imagen\" width:189 height:120 alt=\"$curso1nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a style=\"color:#D72D13; text-decoration:none;\" href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso1\" ><span style=\"font-size: 14px;\"><b>$curso1nombre</b></span><br></a>
					<b>[$curso1modalidad]</b><br>
					$curso1etiqueta
					$curso1fechainicio
					$curso1preciotexto<br><br>
					<a href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso1\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>

				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:189px; height:120px;\" src=\"http://www.activatie.org/web/imagen/$curso2imagen\" width:\"189\" height:\"120\" alt=\"$curso2nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a style=\"color:#ee1717; text-decoration:none;\"  href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso2\" ><span style=\"font-size: 14px;\"><b>$curso2nombre</b></span><br></a>
					<b>[$curso2modalidad]</b><br>
					$curso2etiqueta
					$curso2fechainicio
					$curso2preciotexto<br><br>
					<a href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso2\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>

				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:189px; height:120px;\" src=\"http://www.activatie.org/web/imagen/$curso3imagen\" width:\"189\" height:\"120\" alt=\"$curso3nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a  style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso3\" ><span style=\"font-size: 14px;\"><b>$curso3nombre</b></span><br></a>
					<b>[$curso3modalidad]</b><br>
					$curso3etiqueta
					$curso3fechainicio
					$curso3preciotexto<br><br>
					<a href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso3\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>";
				
			if ($curso4<>""){		
				$texto.="	
			<table>
			<tr>			
				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:189px; height:120px;\" src=\"http://www.activatie.org/web/imagen/$curso4imagen\" width:\"189\" height:\"120\" alt=\"$curso4nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a  style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso4\" ><span style=\"font-size: 14px;\"><b>$curso4nombre</b></span><br></a>
					<b>[$curso4modalidad]</b><br>
					$curso4etiqueta
					$curso4fechainicio
					$curso4preciotexto<br><br>
					<a href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso4\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>
				
				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:189px; height:120px;\" src=\"http://www.activatie.org/web/imagen/$curso5imagen\" width:\"189\" height:\"120\" alt=\"$curso5nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a  style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso5\" ><span style=\"font-size: 14px;\"><b>$curso5nombre</b></span><br></a>
					<b>[$curso5modalidad]</b><br>
					$curso5etiqueta
					$curso5fechainicio
					$curso5preciotexto<br><br>
					<a href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso5\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>
				
				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:189px; height:120px;\" src=\"http://www.activatie.org/web/imagen/$curso6imagen\" width:\"189\" height:\"120\" alt=\"$curso6nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a  style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso6\" ><span style=\"font-size: 14px;\"><b>$curso6nombre</b></span><br></a>
					<b>[$curso6modalidad]</b><br>
					$curso6etiqueta
					$curso6fechainicio
					$curso6preciotexto<br><br>
					<a href=\"https://www.activatie.org/web/curso.php?utm_campaign=newsletter&id=$curso6\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>
			</tr>
			</table>";
			}
$texto.="						
			</tr>
			</table>	
			<p style=\"text-align:right;\"><a style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/formacion.php\"> Ver más</a></p>
			
		</td>
	</tr>







		<tr>
			<td align=\"justify\" width=\"100%\" height=\"100\" bgcolor=\"#ffffff\" >
			
			<br>
			<h2 style=\"display: block; -webkit-margin-before: 0.83em; -webkit-margin-after: 0.83em; -webkit-margin-start: 0px; -webkit-margin-end: 0px;  background: url($destacadosmedium) no-repeat scroll left top rgba(0, 0, 0, 0); color: #fff; font-size: 150%; line-height: 1em; margin-left: -0.95em !important; padding: 3px 0 13px 0.85em; font-family: Arial,Helvetica,FreeSans,'Liberation Sans','Nimbus Sans L',sans-serif; font-weight: bolder;\">Publicaciones</h2>
			<table>
			<tr>
				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:104px; height:125px;\" src=\"http://www.activatie.org/web/imagen/$publi1imagen\" width:\"104\" height:\"125\" alt=\"$publi1nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a  style=\"color:#ee1717; text-decoration:none;\"  href=\"https://www.activatie.org/web/publicacion.php?utm_campaign=newsletter&id=$publi1\" ><span style=\"font-size: 14px;\"><b>$publi1nombre</b><br></a>
					$publi1preciotexto<br><br>
					<a href=\"https://www.activatie.org/web/publicacion.php?utm_campaign=newsletter&id=$publi1\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>

				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:104px; height:125px;\" src=\"http://www.activatie.org/web/imagen/$publi2imagen\" width:\"104\" height:\"125\" alt=\"$publi2nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					
					<a  style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/publicacion.php?utm_campaign=newsletter&id=$publi2\" ><span style=\"font-size: 14px;\"><b>$publi2nombre</b><br></a>
					$publi2preciotexto<br><br>
					<a href=\"https://www.activatie.org/web/publicacion.php?utm_campaign=newsletter&id=$publi2\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>

				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:104px; height:125px;\" src=\"http://www.activatie.org/web/imagen/$publi3imagen\" width:\"104\" height:\"125\" alt=\"$publi3nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a  style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/publicacion.php?utm_campaign=newsletter&id=$publi3\" ><span style=\"font-size: 14px;\"><b>$publi3nombre</b><br></a>
					$publi3preciotexto<br><br>
					<a href=\"https://www.activatie.org/web/publicacion.php?utm_campaign=newsletter&id=$publi3\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>
			</tr>

			</table>
			
			<p style=\"text-align:right;\"><a style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/publicaciones.php\"> Ver más</a></p>
		</td>
	</tr>


		<tr>
			<td align=\"justify\" width=\"100%\" height=\"100\" bgcolor=\"#ffffff\" >
			
			<br>
			<h2 style=\"display: block; -webkit-margin-before: 0.83em; -webkit-margin-after: 0.83em; -webkit-margin-start: 0px; -webkit-margin-end: 0px; background: url($destacadosmedium) no-repeat scroll left top rgba(0, 0, 0, 0); color: #fff; font-size: 150%; line-height: 1em; margin-left: -0.95em !important; padding: 3px 0 13px 0.85em; font-family: Arial,Helvetica,FreeSans,'Liberation Sans','Nimbus Sans L',sans-serif; font-weight: bolder;\">Ofertas de trabajo</h2>
			<table>";
			
			if ($trabajo1<>""){
				
				$texto.="<tr>
					<td align=\"left\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
					<a style=\"color:#ee1717; text-decoration:none;\"  href=\"https://www.activatie.org/web/trabajo.php?utm_campaign=newsletter&id=$trabajo1\" ><span style=\"font-size: 14px;\"><b>$trabajo1nombre</b></span></a>  <span style=\"font-size: 12px;\">[<strong>Zona:</strong> $trabajo1zona · <strong>Fecha límite:</strong> $trabajo1fecha]</span>
					</td>
				</tr>";

			}

			if ($trabajo2<>""){
				$texto.="<tr>
					<td align=\"left\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
					<a  style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/trabajo.php?utm_campaign=newsletter&id=$trabajo2\" ><span style=\"font-size: 14px;\"><b>$trabajo2nombre</b></span></a>  <span style=\"font-size: 12px;\">[<strong>Zona:</strong> $trabajo2zona · <strong>Fecha límite:</strong> $trabajo2fecha]</span>
					</td>
				</tr>";
			}
			
			if ($trabajo3<>""){
				$texto.="<tr>
					<td align=\"left\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
					<a  style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/trabajo.php?utm_campaign=newsletter&id=$trabajo3\" ><span style=\"font-size: 14px;\"><b>$trabajo3nombre</b></span></a> <span style=\"font-size: 12px;\">[<strong>Zona:</strong> $trabajo3zona · <strong>Fecha límite:</strong> $trabajo3fecha]</span>
					</td>
					</td>
				</tr>";
			}
	$texto.="
			</table>
			
			<p style=\"text-align:right;\"><a style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/trabajos.php\"> Ver más</a></p>
		</td>
	</tr>




	  </tbody>
	</table>";

return $texto;

}


function getVariosCursos($curso1, $curso2, $curso3){

	
	if ($curso1<>""){
		$sql = "SELECT * FROM curso WHERE id='$curso1'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$curso1nombre=$row["nombre"];
		$curso1imagen=$row["imagen"];
		$curso1fechainicio=cambiaf_a_normal($row["fecha_inicio"]);
		if ($curso1fechainicio<>""){
			$curso1fechainicio.="<br>";
		}
		
		
		if ($row["modalidad"]==0) $curso1modalidad='on-line'; 
		if ($row["modalidad"]==1) $curso1modalidad='presencial';
		if ($row["modalidad"]==2) $curso1modalidad='presencial y on-line';
		if ($row["modalidad"]==3) $curso1modalidad='permanente';
		
		$linket=iConectarse(); 
		$resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$curso1' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		if (pg_num_rows($resultet)>0){
				$rowet = pg_fetch_array($resultet);
				$curso1etiqueta="";
				if ($rowet["texto"]<>""){
					$curso1etiqueta=="[".$rowet["texto"]."]<br>"; 	
				}					
		 }
		 
		$precio = "";
		if ($row["modalidad"]==0){
			$precio = $row["precioco"];
			$preciotachado = ($row['preciotachadooc']);

		}
		elseif ($row["modalidad"]==1){
			$preciotachado = ($row['preciotachadoc']);
			$precio = $row["precioc"];
		}
		elseif ($row["modalidad"]==3){
			$preciotachado = ($row['preciotachadopc']);
			$precio = $row["preciocp"];
		}
		else{
			$preciotachado = ($row['preciotachadooc']);
			$precio = $row["precioco"];
		}
		
		$precio = recortardecimales($precio);
		$preciotachado = recortardecimales($preciotachado);

		
		$preciotexto="";
		if ($precio==0){
			$curso1precio = "¡Gratuito!";
		}
		else{
			$curso1precio = $precio."€";
		}
		
		$preciotachadotexto1 = "";
		if ($preciotachado<>0){
			$preciotachadotexto1 = "<strike>".$preciotachado."€</strike>";
		}
	}
	
	if ($curso2<>""){
		$sql = "SELECT * FROM curso WHERE id='$curso2'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$curso2nombre=$row["nombre"];
		$curso2imagen=$row["imagen"];
		$curso2fechainicio=cambiaf_a_normal($row["fecha_inicio"]);
		if ($curso2fechainicio<>""){
			$curso2fechainicio.="<br>";
		}
		
		if ($row["modalidad"]==0) $curso2modalidad='on-line'; 
		if ($row["modalidad"]==1) $curso2modalidad='presencial';
		if ($row["modalidad"]==2) $curso2modalidad='presencial y on-line';
		if ($row["modalidad"]==3) $curso2modalidad='permanente';
		
		$linket=iConectarse(); 
		$resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$curso2' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		if (pg_num_rows($resultet)>0){
				$rowet = pg_fetch_array($resultet);
				$curso2etiqueta="";
				if ($rowet["texto"]<>""){
					$curso2etiqueta=="[".$rowet["texto"]."]<br>"; 	
				}					
		 }
		 
		$precio = "";
		if ($row["modalidad"]==0){
			$precio = $row["precioco"];
			$preciotachado = ($row['preciotachadooc']);
		}
		elseif ($row["modalidad"]==1){
			$precio = $row["precioc"];
			$preciotachado = ($row['preciotachadoc']);
		}
		elseif ($row["modalidad"]==3){
			$precio = $row["preciocp"];
			$preciotachado = ($row['preciotachadopc']);
		}
		else{
			$precio = $row["precioco"];
			$preciotachado = ($row['preciotachadooc']);
		}
		
		$precio = recortardecimales($precio);
		$preciotachado = recortardecimales($preciotachado);
		
		$preciotexto="";
		if ($precio==0){
			$curso2precio = "¡Gratuito!";
		}
		else{
			$curso2precio = $precio."€";
		}
		
		$preciotachadotexto2 = "";
		if ($preciotachado<>0){
			$preciotachadotexto2 = "<strike>".$preciotachado."€</strike>";
		}
	}
	
	if ($curso3<>""){
		$sql = "SELECT * FROM curso WHERE id='$curso3'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$curso3nombre=$row["nombre"];
		$curso3imagen=$row["imagen"];
		$curso3fechainicio=cambiaf_a_normal($row["fecha_inicio"]);
		if ($curso3fechainicio<>""){
			$curso3fechainicio.="<br>";
		}
		
		if ($row["modalidad"]==0) $curso3modalidad='on-line'; 
		if ($row["modalidad"]==1) $curso3modalidad='presencial';
		if ($row["modalidad"]==2) $curso3modalidad='presencial y on-line';
		if ($row["modalidad"]==3) $curso3modalidad='permanente';
		
		$linket=iConectarse(); 
		$resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$curso3' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
		if (pg_num_rows($resultet)>0){
				$rowet = pg_fetch_array($resultet);
				$curso3etiqueta="";
				if ($rowet["texto"]<>""){
					$curso3etiqueta="[".$rowet["texto"]."]<br>"; 	
				}					
		}
		 
		$precio = "";
		if ($row["modalidad"]==0){
			$precio = $row["precioco"];
			$preciotachado = ($row['preciotachadooc']);
		}
		elseif ($row["modalidad"]==1){
			$precio = $row["precioc"];
			$preciotachado = ($row['preciotachadoc']);
		}
		elseif ($row["modalidad"]==3){
			$precio = $row["preciocp"];
			$preciotachado = ($row['preciotachadooc']);
		}
		else{
			$precio = $row["precioco"];
			$preciotachado = ($row['preciotachadooc']);
		}
		
		$precio = recortardecimales($precio);
		$preciotachado = recortardecimales($preciotachado);
		
		$preciotexto="";
		if ($precio==0){
			$curso3precio = "¡Gratuito!";
		}
		else{
			$curso3precio = $precio."€";
		}
		
		$preciotachadotexto3 = "";
		if ($preciotachado<>0){
			$preciotachadotexto3 = "<strike>".$preciotachado."€</strike>";
		}
	}
	
$texto="";	
$texto.="	
<table align=\"left\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">
	<tbody>

		<tr>
			<td align=\"justify\" width=\"100%\" height=\"100\" bgcolor=\"#ffffff\" >

			<table>
			<tr>
				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:189px; height:120px;\" src=\"http://www.activatie.org/web/imagen/$curso1imagen\" width:\"189\" height:\"120\" alt=\"$curso1nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a style=\"color:#D72D13; text-decoration:none;\" href=\"https://www.activatie.org/web/curso.php?id=$curso1\" ><span style=\"font-size: 14px;\"><b>$curso1nombre</b></span><br></a>
					<br><b>[$curso1modalidad]</b><br>
					$curso1etiqueta
					$curso1fechainicio
					<br><span style=\"margin:20px 0 20px 0;\" ><b style=\"font-size:17px\">$curso1precio $preciotachadotexto1</b></span><br><br>
					<a href=\"https://www.activatie.org/web/curso.php?id=$curso1\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>

				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:189px; height:120px;\" src=\"http://www.activatie.org/web/imagen/$curso2imagen\" width:\"189\" height:\"120\" alt=\"$curso2nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a style=\"color:#ee1717; text-decoration:none;\"  href=\"https://www.activatie.org/web/curso.php?id=$curso2\" ><span style=\"font-size: 14px;\"><b>$curso2nombre</b></span><br></a>
					<br><b>[$curso2modalidad]</b><br>
					$curso2etiqueta
					$curso2fechainicio
					<br><span style=\"margin:20px 0 20px 0;\" ><b style=\"font-size:17px\">$curso2precio $preciotachadotexto2</b></span><br><br>
					<a href=\"https://www.activatie.org/web/curso.php?id=$curso2\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>

				<td align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"266\">
				<img style=\"width:189px; height:120px;\" src=\"http://www.activatie.org/web/imagen/$curso3imagen\" width:\"189\" height:\"120\" alt=\"$curso3nombre\" />
				<p align=\"center\" style=\"text-align: center; font-size: 12px;\">
					<a  style=\"color:#ee1717; text-decoration:none;\" href=\"https://www.activatie.org/web/curso.php?id=$curso3\" ><span style=\"font-size: 14px;\"><b>$curso3nombre</b></span><br></a>
					<br><b>[$curso3modalidad]</b><br>
					$curso3etiqueta
					$curso3fechainicio
					<br><span style=\"margin:20px 0 20px 0;\" ><b style=\"font-size:17px\">$curso3precio $preciotachadotexto3</b></span><br><br>
					<a href=\"https://www.activatie.org/web/curso.php?id=$curso3\" > <img style=\"width:145px; height:30px;\" src=\"http://www.activatie.org/web/imagen/verinfo-small.png\" width:\"145\" \"30\" alt=\"Ver Información\"></a></p>
				</td>
			</tr>

			</table>
		</td>
	</tr>


	  </tbody>
	</table>";

return $texto;

}



function getInscripcionCursoGratuito($idcurso, $idusuario){
	
	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$idcolegio = $curso["idcolegio"];
	$lugar="";
	$lugar = $curso["lugar"];
	$modalidad = $curso["modalidad"];
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);
	
	$nombreusuario = $usuario["nombre"];
	
	$rowcolegio=posgre_query("SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0;") ;
	$colegio= pg_fetch_array($rowcolegio);
	
	$correocolegio = $colegio["email"];

	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	$inscripciononlinepresencial = $cursousuario['inscripciononlinepresencial'];
	
	$textomodo="";
	if ($modalidad==2){
		if ($inscripciononlinepresencial==1){
			$textomodo="MODALIDAD: Presencial<br>";
		}
		elseif ($inscripciononlinepresencial==2){
			$textomodo="MODALIDAD: On-line<br>";
		}
		
	}
	
	$texto = "";
	$texto .= "<p>Hola $nombreusuario,</p>"; 
	 
	$texto .= "<p>Le confirmamos que su solicitud de inscripción para la siguiente actividad se ha realizado correctamente:<br><br>";
	$texto .= "ACTIVIDAD: <b>$nombrecurso</b><br>";	 
	if ($fecha_inicio<>""){
		$texto .= "FECHA DE INICIO: <b>$fecha_inicio</b><br>";	 
	}	 
	
	$texto.=$textomodo;
	
	$texto .= "DURACIÓN: $duracion horas";
	if ($duracionminutos>0) { 
		$texto .= " y ".$duracionminutos." minutos";
	}
	$texto.=".<br>";
	
	if (($lugar<>"")&&($inscripciononlinepresencial<>2)){
		$texto .= "LUGAR: $lugar<br>";
	}
	
	if ($modalidad<>3){	
	
		$texto .= "<p><b>El día de inicio de la actividad</b> podrá acceder a la información para conectarse a la sesión online así como al contenido del curso, identificandose en Acceso a usuarios de la página principal de activatie, y en la pestaña Mis cursos, le aparecerá el botón Acceder. </p>";
	}  
	else{
		$texto .= "<p>Podrá acceder al contenido (documentación, grabación de las sesiones que se irán subiendo conforme se desarrolle la actividad, ejercicios, etc.), debe identificarse en Acceso a usuarios de la página principal de activatie, y en la pestaña Mis cursos, le aparecerá el botón Acceder. </p>";
	
	}
	 
	$texto .= "<p>Condiciones de baja de la actividad:<strong></strong><br>Las bajas en la actividad deberán ser comunicadas por escrito  al correo <a href=\"mailto:info@activatie.org\">$correocolegio</a>,  como mínimo 5 días antes del inicio de la misma.</p>";
	  
	$texto .= "<p>Le hemos enviado un email con esta información. </p>"; 
	$texto .= "<p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p><br><br>";

	return $texto;

}

function getInscripcionPagoConTarjeta($idcurso, $idusuario){
	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$idcolegio = $curso["idcolegio"];
	$lugar="";
	$lugar = $curso["lugar"];
	$modalidad = $curso["modalidad"];
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);
	
	$nombreusuario = $usuario["nombre"];
	
	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	$inscripciononlinepresencial = $cursousuario['inscripciononlinepresencial'];
		
	$textomodo="";
	if ($modalidad==2){
		if ($inscripciononlinepresencial==1){
			$textomodo="MODALIDAD: Presencial<br>";
		}
		elseif ($inscripciononlinepresencial==2){
			$textomodo="MODALIDAD: On-line<br>";
		}
		
	}
	
	
	if ($precio==0){
		$preciotexto = "Gratuito";
	}
	else{
		$preciotexto = $precio."€";
	}
	
	$rowcolegio=posgre_query("SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0;") ;
	$colegio= pg_fetch_array($rowcolegio);
	
	$correocolegio = $colegio["email"];

	$texto = "";
	$texto .= "<p>Hola $nombreusuario,</p>"; 
	 
	$texto .= "<p>Le confirmamos que su inscripción para la siguiente actividad se ha realizado correctamente:<br><br>";
	$texto .= "ACTIVIDAD: <b>$nombrecurso</b><br>";
	if ($fecha_inicio<>""){
		$texto .= "FECHA DE INICIO: <b>$fecha_inicio</b><br>";	 
	}
	
	$texto.=$textomodo;
	$texto .= "DURACIÓN: $duracion horas";
	if ($duracionminutos>0) { 
		$texto .= " y ".$duracionminutos." minutos";
	}
	$texto.=".<br>";
	
	if (($lugar<>"")&&($inscripciononlinepresencial<>2)){
		$texto .= "LUGAR: $lugar<br>";
	}
	$texto .= "MATRICULA: $preciotexto</p>";
	 
	
	if ($modalidad<>3){	
		$texto .= "<p><b>El día de inicio de la actividad</b> podrá acceder a la información para conectarse a la sesión online así como al contenido del curso, identificandose en Acceso a usuarios de la página principal de activatie, y en la pestaña Mis cursos, le aparecerá el botón Acceder. </p>";

	}  
	else{
		$texto .= "<p>Podrá acceder al contenido (documentación, grabación de las sesiones que se irán subiendo conforme se desarrolle la actividad, ejercicios, etc.), debe identificarse en Acceso a usuarios de la página principal de activatie, y en la pestaña Mis cursos, le aparecerá el botón Acceder. </p>";
	
	}
	  
	$texto .= "<p>Recuerde revisar sus datos de facturación <b>en caso de que sean distintos a sus datos personales</b>, desde su \"Área personal\" de activatie, en el apartado \"Mis datos\", \"Datos de facturación\". </p>";
	 
	$texto .= "<p>Condiciones de baja de la actividad:<strong></strong><br>Las bajas en la actividad deberán ser comunicadas por escrito  al correo <a href=\"mailto:info@activatie.org\">$correocolegio</a>, como mínimo 5 días antes del inicio de la misma, para optar a la devolución del importe abonado.</p>";
	  
	$texto .= "<p>Le hemos enviado un email con esta información. </p>"; 
	$texto .= "<p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p><br><br>";

	return $texto;


}

function getInscripcionPagoConTransferencia($idcurso, $idusuario){
	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$idcolegio = $curso["idcolegio"];
	$lugar="";
	$lugar = $curso["lugar"];
	$modalidad = $curso["modalidad"];
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);
	
	$nombreusuario = $usuario["nombre"];
	$apellidosusuario = $usuario["apellidos"];
	
	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	$inscripciononlinepresencial = $cursousuario['inscripciononlinepresencial'];
		
	$textomodo="";
	if ($modalidad==2){
		if ($inscripciononlinepresencial==1){
			$textomodo="MODALIDAD: Presencial<br>";
		}
		elseif ($inscripciononlinepresencial==2){
			$textomodo="MODALIDAD: On-line<br>";
		}
		
	}
	
	
	if ($precio==0){
		$preciotexto = "Gratuito";
	}
	else{
		$preciotexto = $precio."€";
	}
	
	$rowcolegio=posgre_query("SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0;") ;
	$colegio= pg_fetch_array($rowcolegio);
	
	$correocolegio = $colegio["email"];

	$texto = "";
	$texto .= "<p>Hola $nombreusuario,</p>"; 
	 
	$texto .= "<p>Le confirmamos que hemos realizado una reserva temporal de su plaza en la actividad:<br><br>";
	$texto .= "ACTIVIDAD: <b>$nombrecurso</b><br>";	 
	if ($fecha_inicio<>""){
		$texto .= "FECHA DE INICIO: <b>$fecha_inicio</b><br>";	 
	} 
	
	$texto.=$textomodo;
	$texto .= "DURACIÓN: $duracion horas";
	if ($duracionminutos>0) { 
		$texto .= " y ".$duracionminutos." minutos";
	}
	$texto.=".<br>";
	
	if (($lugar<>"")&&($inscripciononlinepresencial<>2)){
		$texto .= "LUGAR: $lugar<br>";
	}
	$texto .= "MATRICULA: $preciotexto</p>";
	 
	$texto .= "<p><strong>Condiciones de inscripción:</strong><br>
	Para poder formalizar su inscripción a la actividad deberá abonar el importe de la matrícula, mediante transferencia bancaria a la cuenta que se indica. Incluir obligatoriamente en el concepto el código indicado y el nombre y apellidos , tal y como se muestra a continuación:<br>
	&nbsp;&nbsp;&nbsp;Entidad: <b>Banco Sabadell</b><br>
	&nbsp;&nbsp;&nbsp;Número de cuenta: <b>ES04 0081 1016 1800 0154 6461</b><br>
	&nbsp;&nbsp;&nbsp;Concepto: <b>$idcurso - $nombreusuario $apellidosusuario</b><br>
	y adjuntar el comprobante de ingreso en la casilla correspondiente de la actividad que aparece en el apartado “Mis compras”, dentro de su área personal de activatie.
	</p>";
	 
	$texto .= "<p>Le hemos enviado un email con esta información. </p>"; 
	$texto .= "<p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p><br><br>";

	return $texto;


}

function getInscripcionPagoConDomiciliacion($idcurso, $idusuario){
	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$idcolegio = $curso["idcolegio"];
	$lugar="";
	$lugar = $curso["lugar"];
	$modalidad = $curso["modalidad"];
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);
	
	$nombreusuario = $usuario["nombre"];
	
	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	$inscripciononlinepresencial = $cursousuario['inscripciononlinepresencial'];
		
	$textomodo="";
	if ($modalidad==2){
		if ($inscripciononlinepresencial==1){
			$textomodo="MODALIDAD: Presencial<br>";
		}
		elseif ($inscripciononlinepresencial==2){
			$textomodo="MODALIDAD: On-line<br>";
		}
		
	}
	
	
	if ($precio==0){
		$preciotexto = "Gratuito";
	}
	else{
		$preciotexto = $precio."€";
	}
	
	$rowcolegio=posgre_query("SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0;") ;
	$colegio= pg_fetch_array($rowcolegio);
	
	$correocolegio = $colegio["email"];

	$texto = "";
	$texto .= "<p>Hola $nombreusuario,</p>"; 
	 
	$texto .= "<p>Le confirmamos que hemos realizado una reserva temporal de su plaza en la actividad:<br><br>";
	$texto .= "ACTIVIDAD: <b>$nombrecurso</b><br>";	 
	if ($fecha_inicio<>""){
		$texto .= "FECHA DE INICIO: <b>$fecha_inicio</b><br>";	 
	} 
	
	$texto.=$textomodo;
	$texto .= "DURACIÓN: $duracion horas";
	if ($duracionminutos>0) { 
		$texto .= " y ".$duracionminutos." minutos";
	}
	$texto.=".<br>";
	
	if (($lugar<>"")&&($inscripciononlinepresencial<>2)){
		$texto .= "LUGAR: $lugar<br>";
	}
	$texto .= "MATRICULA: <b>$preciotexto</b></p>";
	
	$texto .="<p>Para poder formalizar su inscripción en la actividad deberá completar y firmar el siguiente documento de <a target=\"_blank\" href=\"https://www.activatie.org/web/files/Autorización de domiciliación bancaria.pdf\">\"Autorización de domiciliación bancaria\"</a> y adjuntarlo en la casilla correspondiente del curso que aparece en el apartado \"Mis compras\", dentro de su área personal de activatie. </p>";
	 
	$texto .="<p>Le informamos que el fraccionamiento se realizará en dos plazos: 50% del importe de la actividad al formalizar la inscripción antes del inicio de la misma y el resto, una semana antes de la fecha de finalización.<p>"; 
	
	$texto .= "<p><strong>Condiciones de baja de la actividad:</strong><br>Las bajas en la actividad deberán ser comunicadas por escrito  al correo <a href=\"mailto:info@activatie.org\">$correocolegio</a>, como mínimo 5 días antes del inicio de la misma.</p>";
	
	$texto .= "<p>Le hemos enviado un email con esta información. </p>"; 
	$texto .= "<p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p><br><br>";

	return $texto;


}


function getInscripcionListaEspera($idcurso, $idusuario){
	
	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$lugar = $curso["lugar"];
	$modalidad = $curso["modalidad"];
	
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);

	
	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	$inscripciononlinepresencial = $cursousuario["inscripciononlinepresencial"];
		
	$textomodo="";
	if ($modalidad==2){
		if ($inscripciononlinepresencial==1){
			$textomodo="MODALIDAD: Presencial<br>";
		}
		elseif ($inscripciononlinepresencial==2){
			$textomodo="MODALIDAD: On-line<br>";
		}
		
	}
	
	if ($precio==0){
		$preciotexto = "Gratuito";
	}
	else{
		$preciotexto = $precio."€";
	}
	
	
	$nombreusuario = $usuario["nombre"];

	$posicionlista = getPosicionListaEspera($idusuario, $idcurso);
	
	$texto = "";
	$texto .= "<p>Hola $nombreusuario,</p>"; 
	 
	$texto .= "<p>Le confirmamos que hemos realizado una reserva de su plaza en la siguiente actividad, en el que ocupa la posición $posicionlista en la lista de espera. Puede consultar la posición actualizada en su zona personal.<br><br>";
	$texto .= "ACTIVIDAD: <b>$nombrecurso</b><br>";	 
	if ($fecha_inicio<>""){
		$texto .= "FECHA DE INICIO: <b>$fecha_inicio</b><br>";	 
	}	 
	
	$texto.=$textomodo;
	$texto .= "DURACIÓN: $duracion horas";
	if ($duracionminutos>0) { 
		$texto .= " y ".$duracionminutos." minutos";
	}
	$texto.=".<br>";
	
	if (($lugar<>"")&&($inscripciononlinepresencial<>2)){
		$texto .= "LUGAR: $lugar<br>";
	}
	
	$texto .= "MATRICULA: $preciotexto</p>";
	 
	$texto .= "<p>En el caso de quedar alguna plaza disponible o ante una próxima convocatoria de la misma actividad, le informaremos por si continúa interesado en inscribirse.</p>";
	  	
	$texto .= "<p>Le hemos enviado un email con esta información. </p>"; 
	$texto .= "<p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p><br><br>";

	return $texto;
}


function getRecordatorioInicioCurso($idcurso, $idusuario){



}



function getAsuntoListaDeEsperaAInscrito($idcurso){

	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$curso= pg_fetch_array($rowcurso);
	
	$asunto="[activatie formación] - De lista de espera a inscrito en ".$curso["nombre"];
	return $asunto;


}

function getTextoListaDeEsperaAInscrito($idusuario, $idcurso){

	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$idcolegio = $curso["idcolegio"];
	$modalidad = $curso["modalidad"];
	$lugar="";
	$lugar = $curso["lugar"];
	$modalidad = $curso["modalidad"];
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);
	
	$nombreusuario = $usuario["nombre"];
	
	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	$inscripciononlinepresencial = $cursousuario["inscripciononlinepresencial"];
		
	$textomodo="";
	if ($modalidad==2){
		if ($inscripciononlinepresencial==1){
			$textomodo="MODALIDAD: Presencial<br>";
		}
		elseif ($inscripciononlinepresencial==2){
			$textomodo="MODALIDAD: On-line<br>";
		}
		
	}
	
	$insc="";
	if ($modalidad==2){
		$inscripciononlinepresencial = $cursousuario["inscripciononlinepresencial"];
		$insc="&inscripcion=$inscripciononlinepresencial";
	}
	if ($precio==0){
		$preciotexto = "Gratuito";
	}
	else{
		$preciotexto = $precio."€";
	}
	
	$rowcolegio=posgre_query("SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0;") ;
	$colegio= pg_fetch_array($rowcolegio);
	
	$correocolegio = $colegio["email"];

	$texto = "";
	$texto .= "<p>Hola $nombreusuario,</p>"; 
	 
	$texto .= "<p>Le informamos que ha quedado disponible una plaza en la siguiente actividad en el que estaba inscrito en lista de espera, por lo que ha pasado a la lista de inscritos. <br><br>";
	$texto .= "ACTIVIDAD: <b>$nombrecurso</b><br><br>";	 
	if ($fecha_inicio<>""){
		$texto .= "FECHA DE INICIO: <b>$fecha_inicio</b><br><br>";	 
	} 
	
	$texto.=$textomodo;
	$texto .= "DURACIÓN: $duracion horas";
	if ($duracionminutos>0) { 
		$texto .= " y ".$duracionminutos." minutos";
	}
	$texto.=".<br>";
	
	if (($lugar<>"")&&($inscripciononlinepresencial<>2)){
		$texto .= "LUGAR: $lugar<br><br>";
	}
	$texto .= "MATRICULA: $preciotexto</p>";
	 
	if ($precio>0){ 
		$texto .= "<p>Debe entrar en el siguiente <a href='https://www.activatie.org/web/prescripcion.php?id=$idcurso$insc'>enlace</a> para seleccionar el método de pago.</p>";
	}
	$texto .= "<br><p>Condiciones de baja de la actividad:<strong></strong><br>Las bajas en la actividad deberán ser comunicadas por escrito  al correo <a href=\"mailto:info@activatie.org\">$correocolegio</a>,  como mínimo 5 días antes del inicio de la misma.</p>";
	
	$texto .= "<br><p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p>";

	return $texto;


}


function getAsuntoDarDeBaja($idcurso){

	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$curso= pg_fetch_array($rowcurso);
	
	$asunto="[activatie formación] - Baja en ".$curso["nombre"];
	return $asunto;


}

function getTextoDarDeBaja($idusuario,$idcurso){

	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$idcolegio = $curso["idcolegio"];
	$lugar="";
	$lugar = $curso["lugar"];
	$modalidad = $curso["modalidad"];
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);
	
	$nombreusuario = $usuario["nombre"];
	
	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	$inscripciononlinepresencial = $cursousuario["inscripciononlinepresencial"];
	
	if ($precio==0){
		$preciotexto = "Gratuito";
	}
	else{
		$preciotexto = $precio."€";
	}
	
	$rowcolegio=posgre_query("SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0;") ;
	$colegio= pg_fetch_array($rowcolegio);
	
	$correocolegio = $colegio["email"];

	$texto = "";
	$texto .= "<p>Hola $nombreusuario,</p>"; 
	 
	$texto .= "<p>Le confirmamos su baja en la siguiente actividad en el que estaba inscrito.<br><br>";
	$texto .= "ACTIVIDAD: <b>$nombrecurso</b><br><br>";	 
	if ($fecha_inicio<>""){
		$texto .= "FECHA DE INICIO: <b>$fecha_inicio</b><br><br>";	 
	} 
	
	$texto .= "DURACIÓN: $duracion horas";
	if ($duracionminutos>0) { 
		$texto .= " y ".$duracionminutos." minutos";
	}
	$texto.=".<br>";
	
	if (($lugar<>"")&&($inscripciononlinepresencial<>2)){
		$texto .= "LUGAR: $lugar<br><br>";
	}
	$texto .= "MATRICULA: $preciotexto</p>";
	 
	$texto.="<p></p>"; 

	$texto .= "<p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p>";

	return $texto;

}

function getAsuntoDarDiploma($idcurso){

	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$curso= pg_fetch_array($rowcurso);
	
	$asunto="[activatie formación] - Diploma de la actividad ".$curso["nombre"];
	return $asunto;


}

function getTextoDarDiploma($idusuario,$idcurso){

	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$idcolegio = $curso["idcolegio"];
	$lugar="";
	$lugar = $curso["lugar"];
	$modalidad = $curso["modalidad"];
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);
	
	$nombreusuario = $usuario["nombre"];
	
	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	$inscripciononlinepresencial = $cursousuario["inscripciononlinepresencial"];
	
	if ($precio==0){
		$preciotexto = "Gratuito";
	}
	else{
		$preciotexto = $precio."€";
	}
	
	$rowcolegio=posgre_query("SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0;") ;
	$colegio= pg_fetch_array($rowcolegio);
	
	$correocolegio = $colegio["email"];

	$texto = "";
	$texto .= "<p>Hola $nombreusuario,</p>"; 
	 
	$texto .= "<p>le informamos que <b>ha superado</b> de manera favorable la evaluación prevista en nuestro Sistema de Calidad de la actividad <b>$nombrecurso</b>, por lo que ya puede descargar su Diploma en el apartado “Mis Cursos” de su Área Personal de activatie. Tenga en cuenta que debe rellenar la encuesta para que se le active el botón de descarga (serán solo unos minutos, su opinión es muy importante para ayudarnos a mejorar, gracias!).<br><br>";

	$texto.="<p></p>"; 

	$texto .= "<p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p>";

	return $texto;

}

function getAsuntoDarDiplomaNOApto($idcurso){

	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$curso= pg_fetch_array($rowcurso);
	
	$asunto="[activatie formación] - Resultado evaluación en la actividad ".$curso["nombre"];
	return $asunto;


}

function getTextoDarDiplomaNOApto($idusuario,$idcurso){

	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;
	$curso= pg_fetch_array($rowcurso);

	$nombrecurso = $curso["nombre"];
	$fecha_inicio = cambiaf_a_normal($curso["fecha_inicio"]);
	$duracion = $curso["duracion"];
	$duracionminutos = $curso["duracionminutos"];
	$idcolegio = $curso["idcolegio"];
	$lugar="";
	$lugar = $curso["lugar"];
	$modalidad = $curso["modalidad"];
		
	$rowusuario=posgre_query("SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0;") ;
	$usuario= pg_fetch_array($rowusuario);
	
	$nombreusuario = $usuario["nombre"];
	
	$rowcursousuario=posgre_query("SELECT * FROM curso_usuario WHERE idusuario='$idusuario' AND idcurso='$idcurso' AND borrado=0;") ;
	$cursousuario= pg_fetch_array($rowcursousuario);
	
	$precio = $cursousuario["precio"];
	$inscripciononlinepresencial = $cursousuario["inscripciononlinepresencial"];
	
	if ($precio==0){
		$preciotexto = "Gratuito";
	}
	else{
		$preciotexto = $precio."€";
	}
	
	$rowcolegio=posgre_query("SELECT * FROM usuario WHERE id='$idcolegio' AND borrado=0;") ;
	$colegio= pg_fetch_array($rowcolegio);
	
	$correocolegio = $colegio["email"];

	$texto = "";
	$texto .= "<p>Hola $nombreusuario,</p>"; 
	 
	$texto .= "<p>le informamos que no ha superado la fase de evaluación prevista en nuestro Sistema de Calidad de la actividad <b>$nombrecurso</b>, en base a alguno/s del/los requisitos para obtención del Diploma, que puede consultar en el Programa del curso.<br><br>";


	 
	$texto.="<p></p>"; 

	$texto .= "<p>Reciba un cordial saludo,</p>";
	 
	$texto .= "<p>Plataforma activatie S.L.<br>";
	$texto .= "<a href=\"mailto:info@activatie.org\">info@activatie.org</a></p>";

	return $texto;

}

function getAsuntoCurso($idcurso){

	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$curso= pg_fetch_array($rowcurso);

	$asunto="[activatie formación] - ".$curso["nombre"];
	
	return $asunto;
}

function getTextoCurso($idcurso){
	$rowcurso=posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$curso= pg_fetch_array($rowcurso);

	$presentacion = $curso['presentacion'];
	$cursonombre = $curso['nombre'];
	if ($curso["tipo"]==0) $tipo=' Curso '; 
	if ($curso["tipo"]==1) $tipo=' Curso universitario '; 
	if ($curso["tipo"]==2) $tipo=' Taller '; 
	if ($curso["tipo"]==3) $tipo=' Seminario '; 
	if ($curso["tipo"]==4) $tipo=' Jornada '; 
	if ($curso["modalidad"]==0) $modalidad=' On-line '; 
	if ($curso["modalidad"]==1) $modalidad=' Presencial ';
	if ($curso["modalidad"]==2) $modalidad=' Presencial y On-line ';
	if ($curso["modalidad"]==3) $modalidad=' Permanente ';
	$fecha_inicio=cambiaf_a_normal($curso['fecha_inicio']);
	$fecha_fin=cambiaf_a_normal($curso['fecha_fin_publicacion']);
	
	$duracion = $curso['duracion'];
	$duracionminutos = $curso['duracionminutos'];
	
	$duraciontexto = $duracion." horas";
	if ($duracionminutos<>0){
		$duraciontexto .= " y $duracionminutos minutos";
	}
	
	$imagen = $curso["imagen2"];
	
	$mod = $curso["modalidad"];
	$privado = $curso["privado"];
	
	$precioc=$curso["precioc"]; if ($precioc==0){ $precioc="GRATIS"; } else { $precioc = recortardecimales($precioc); $precioc.="€"; }
	$precion=$curso["precion"]; if ($precion==0){ $precion="GRATIS"; } else { $precion = recortardecimales($precion); $precion.="€"; }

	$precioco=$curso["precioco"]; if ($precioco==0){ $precioco="GRATIS"; } else { $precioco = recortardecimales($precioco); $precioco.="€"; }
	$preciono=$curso["preciono"]; if ($preciono==0){ $preciono="GRATIS"; } else { $preciono = recortardecimales($preciono); $preciono.="€"; }

	$preciocp=$curso["preciocp"]; if ($preciocp==0){ $preciocp="GRATIS"; } else { $preciocp = recortardecimales($preciocp); $preciocp.="€"; }
	$precionp=$curso["precionp"]; if ($precionp==0){ $precionp="GRATIS"; } else { $precionp = recortardecimales($precionp); $precionp.="€"; }
	
	if ($mod==2){ // Curso dual: On-Line
		
		$preciotachado = recortardecimales($curso['preciotachadoc']);
		$preciotachadotexto = "";
		if ($preciotachado<>0){
			$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
		}
				
		$textoprecio = "Presencial: <b>$precioc $preciotachadotexto para Colegiados</b>";
		if ($privado==0){
			$preciotachado = recortardecimales($curso['preciotachadon']);
			$preciotachadotexto = "";
			if ($preciotachado<>0){
				$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
			}
			$textoprecio .= ". $precion $preciotachadotexto para el resto.";
		}
		
		$preciotachado = recortardecimales($curso['preciotachadooc']);
		$preciotachadotexto = "";
		if ($preciotachado<>0){
			$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
		}
		
		$textoprecio.="<br>";
		$textoprecio .= "On-line: <b>$precioco $preciotachadotexto para Colegiados</b>";
		if ($privado==0){
				
			$preciotachado = recortardecimales($curso['preciotachadoon']);
			$preciotachadotexto = "";
			if ($preciotachado<>0){
				$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
			}
			
			$textoprecio .= ". $preciono $preciotachadotexto para el resto.";
		}
	}
	elseif ($mod==3){ //Permanente
			
		$preciotachado = recortardecimales($curso['preciotachadopc']);
		$preciotachadotexto = "";
		if ($preciotachado<>0){
			$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
		}
		$textoprecio .= "Permanente: <b>$preciocp $preciotachadotexto para Colegiados</b>";
		if ($privado==0){
				
			$preciotachado = recortardecimales($curso['preciotachadopc']);
			$preciotachadotexto = "";
			if ($preciotachado<>0){
				$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
			}
			$textoprecio .= ". $precionp $preciotachadotexto para el resto.";
		}
	}
	elseif ($mod==1){
		$preciotachado = recortardecimales($curso['preciotachadoc']);
		$preciotachadotexto = "";
		if ($preciotachado<>0){
			$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
		}
		$textoprecio = "Presencial: <b>$precioc $preciotachadotexto para Colegiados</b>";
		if ($privado==0){
			$preciotachado = recortardecimales($curso['preciotachadon']);
			$preciotachadotexto = "";
			if ($preciotachado<>0){
				$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
			}
			$textoprecio .= ". $precion $preciotachadotexto para el resto.";
		}
	}
	else{	
		$preciotachado = recortardecimales($curso['preciotachadoc']);
		$preciotachadotexto = "";
		if ($preciotachado<>0){
			$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
		}
		$textoprecio .= "On-line: <b>$precioco $preciotachadotexto para Colegiados</b>";
		if ($privado==0){
			$preciotachado = recortardecimales($curso['preciotachadoon']);
			$preciotachadotexto = "";
			if ($preciotachado<>0){
				$preciotachadotexto = "<strike>".$preciotachado."€</strike>";
			}
			$textoprecio .= ". $preciono $preciotachadotexto para el resto.";
		}
	}
	$observaciones="";

	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT id,archivo,nombre FROM archivo WHERE padre='$idcurso' AND borrado='0' AND programa=1;");// or die (mysql_error());  
	if (($result2)&&(pg_num_rows($result2)!=0)) {

			while($row2= pg_fetch_array($result2)) {
				$archivo = "http://www.activatie.org/web/files/".$row2["archivo"];
				//--------------------------------------------------------------------------------------
				
				$extension = filetype ($archivo);
				$exte = explode(".",$row2["archivo"]);
				$extension=$exte[1];
				$arch = $row2['archivo'];
				$botonprograma = "<br><a class=\"btn\" href=\"http://www.activatie.org/web/descarga.php?documento=$arch\">Programa de la actividad</a>";
				
				$botonprograma = "<tr>
					
				<td>
				
				</td>
				<td width=\"50%\"><img src=\"http://www.activatie.org/web/email-correo/pics/icon-type.jpg\" width=\"16\" height=\"16\" alt=\"\">   <a class=\"btn\" href=\"http://www.activatie.org/web/descarga.php?documento=$arch\"><b>Programa de la actividad</b></a></td>
					
					
				</tr>";
				
				
			}
			?>
		<!--fin adjuntosnoticia-->
		<?
	}
	
	/*
	$observaciones.="<p align=\"center\">
 <a style=\"font-family: Arial,Helvetica,sans-serif; color: #505050; text-decoration:none; font-size: 12px;\" href=\"#\">
 	Si no visualizas correctamente este correo, <span style=\"color: #D72D13\"><a href=\"https://www.activatie.org/web/email.php?idemail=%%idemail%%&h=1%%hash%%\">pulse  aquí</a></span></a>
 	</p>";
	*/
	

	
	$observaciones.="
		<style type=\"text/css\">
			a {color:#D72D13}
			a:link {text-decoration:none;}    /* unvisited link */
			a:visited {text-decoration:none;} /* visited link */
			a:hover {text-decoration:underline;}   /* mouse over link */
			a:active {text-decoration:underline;}  /* selected link */
		</style>";
	
	$observaciones.=
		'<img style="width:765px; height:300px;" height="300" width="765" src="http://www.activatie.org/web/imagen/'.$imagen.'" alt="imagen curso" />';
		
	$observaciones.="
		
		<p style=\"text-align: center; font-family: Arial,Helvetica,sans-serif; font-size:25px;background-color:#ffffff;color:#D72D13; text-decoration:none;\"><a style=\"color:#D72D13\" href=\"https://www.activatie.org/web/curso.php?id=$idcurso\" >
			<b>$cursonombre</b></a>
		</p>
		
		
		
		
		";
		
	$observaciones .= "

		<table align=\"left\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\" style=\"text-align: left;\">
			<tr>
				
				<td>
				
				</td>
				<td width=\"50%\"><img src=\"http://www.activatie.org/web/email-correo/pics/icon-type.jpg\" width=\"16\" height=\"16\" alt=\"\"> Tipo: <b style=\"color:#d13100\"> $tipo</b></td>
				
				<td></td><td width=\"50%\"><img src=\"http://www.activatie.org/web/email-correo/pics/icon-class.jpg\" width=\"16\" height=\"16\" alt=\"\"> Modalidad: <b style=\"color:#d13100\"> $modalidad</b></td>
			</tr>
			<tr>
				<td>
				
				</td>
				<td><img src=\"http://www.activatie.org/web/email-correo/pics/icon-duration.jpg\" width=\"16\" height=\"16\" alt=\"\"> Duración: <b style=\"color:#d13100\"> $duraciontexto</b></td>
				<td></td><td><img src=\"http://www.activatie.org/web/email-correo/pics/icon-status.jpg\" width=\"16\" height=\"16\" alt=\"\"> Estado: <b style=\"color:#d13100\"> Plazas Libres</b></td>
			</tr>";
			
			if ($mod<>"3"){
				
			
				$observaciones.="<tr></tr><tr>
					
				<td>
				
				</td>
				<td width=\"50%\"><img src=\"http://www.activatie.org/web/email-correo/pics/icon-class.jpg\" width=\"16\" height=\"16\" alt=\"\"> Fecha de Inicio: <b style=\"color:#d13100\"> $fecha_inicio</b></td>
					
					<td></td><br><td width=\"50%\"><img src=\"http://www.activatie.org/web/email-correo/pics/icon-class.jpg\" width=\"16\" height=\"16\" alt=\"\"> Fin de Inscripción: <b style=\"color:#d13100\"> $fecha_fin</b></td>
					
				</tr>";
			}
		$observaciones.=$botonprograma;	
		$observaciones.="</table>";
		$observaciones.="
		<br>
		<p style=\" text-align: center; font-size: 20px\">PRECIO: <br>
			$textoprecio
		</p>
	";	

	/* Más información */
	$observaciones.="<p align=\"center\" style=\"text-align: center;\"><a href=\"https://www.activatie.org/web/curso.php?id=$idcurso\" > <img src=\"http://www.activatie.org/web/email-correo/pics/action-inscripcion.png\" alt=\"Inscribirme\"></a></p>
		
			</td>
		</tr>";		
	/* Presentación */
	$observaciones=$observaciones.'
		<div class="cuerponoticia">
			<h4>Presentación</h4>
			'.$curso["presentacion"].'
		</div>
		';
	

		






	$observaciones.="
	  </tbody>
	</table>";
	
	$texto=$observaciones;
	
	return $texto;
}

function getAsuntoPublicacion($idpublicacion){

	$rowpublicacion=posgre_query("SELECT * FROM generica WHERE id='$idpublicacion' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$publicacion= pg_fetch_array($rowpublicacion);

	$asunto="[activatie publicaciones] - ".$publicacion["titulo"];
	
	return $asunto;
}

function getTextoPublicacion($idpublicacion){

	$rowpublicacion=posgre_query("SELECT * FROM generica WHERE id='$idpublicacion' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$publicacion= pg_fetch_array($rowpublicacion);
	$titulo=$publicacion["titulo"];
	$precio=$publicacion["precio"];
	$informacion=$publicacion["informacion"];
	$tipopubli=$publicacion["tipopubli"];
	$fecha=cambiaf_a_normal($publicacion["fecha"]);
	$texto='
		<p>
			Publicación: <strong>"'.$titulo.'"</strong><br>
			Precio: '.$precio.' Euros <br>
			Descripción: '.$informacion.' <br>
		</p>
		';	
	
	return $texto;

}

function getAsuntoTrabajo($idtrabajo){

	$rowtrabajo=posgre_query("SELECT * FROM trabajo WHERE id='$idtrabajo' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$trabajo= pg_fetch_array($rowtrabajo);

	$asunto="[activatie trabajos] - ".$trabajo["denominacion"];
	
	return $asunto;
}

function getTextoTrabajo($idtrabajo){

	$rowtrabajo=posgre_query("SELECT * FROM trabajo WHERE id='$idtrabajo' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
	$trabajo= pg_fetch_array($rowtrabajo);
	$denominacion=$trabajo["denominacion"];
	$zona=$trabajo["zona"];
	$otras_caracteristicas=$trabajo["otras_caracteristicas"];
	$requisitos=$trabajo["requisitos"];
	$otros_datos=$trabajo["otros_datos"];
	$requisitos=$trabajo["requisitos"];
	$nombre=$trabajo["nombre"];
	$localidad=$trabajo["localidad"];
	$domicilio=$trabajo["domicilio"];
	$provincia=$trabajo["provincia"];
	$cp=$trabajo["cp"];
	$email=$trabajo["email"];
	$telefono=$trabajo["telefono"];
	$fax=$trabajo["fax"];
	$persona=$trabajo["persona"];
	$fecha_inserccion=cambiaf_a_normal($trabajo["fecha_inserccion"]);
	
	
	$texto='
		<p>
			Denominación: <strong>"'.$denominacion.'"</strong><br>
			Zona: '.$zona.' <br>
			Características: '.$otras_caracteristicas.' <br>
			Requisitos: '.$requisitos.' <br>
			Otros datos: '.$otros_datos.' <br>
			Email: '.$email.' <br>
			Teléfono: '.$telefono.' <br>
			Fax: '.$fax.' <br>
			Persona de contacto: '.$persona.' <br>
			Empresa: '.$nombre.' <br>
			Localidad: '.$localidad.' <br>
			Provincia: '.$provincia.' <br>
		</p>
		';	
	
	return $texto;

}
function getPlantillaEmail($idemail=0, $texto="",$visorweb=0){
	$year = date("Y", time());
	if ($idemail==0){
		if ($texto==""){
			$texto=$_SESSION['textoEmail'];
		}
		else{
			// Texto en parametro
		}
	}
	else{
		$sql = "SELECT texto FROM emailhistorial WHERE id='$idemail'";
		$result = posgre_query($sql);
		if ($row=pg_fetch_array($result)){
			$texto = $row['texto'];
		}
	}
	
	

	?>
	
	<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>ACTIVATIE</title>

</head>

<body bgcolor="#f1f1f1" style="text-align:center;">


<style type="text/css">
	a {color:#D72D13}
	a:link {text-decoration:none;}    /* unvisited link */
	a:visited {text-decoration:none;} /* visited link */
	a:hover {text-decoration:underline;}   /* mouse over link */
	a:active {text-decoration:underline;}  /* selected link */
</style>



 	
 	
<? if ($visorweb==1){ ?>
 <p align="center">
 <a style="font-family: Arial,Helvetica,sans-serif; color: #505050; text-decoration:none; font-size: 12px;" href="https://www.activatie.org/web/email.php?idemail=%%idemail%%&h=1%%hash%%">
 	Si no visualiza correctamente este correo, <span style="color: #D72D13">pulse aquí</span></a>
 </p>
 <? } ?>
	<table style="width: 800px; background-color: #fff;" align="center" border="0" width="800" bgcolor="#ffffff">

<tbody>
	<tr>
		<td>

			<table width="800" style="width: 800px; padding: 1em; padding-bottom:0;">
			<tbody>
					<tr>
						<td><a href="https://www.activatie.org"><img height="90" width="247" style="border:none;display:block;"  border="0" title="Red Social ACTIVATIE"
				              alt="Red Social ACTIVATIE"
				              src="http://www.activatie.org/web/img/activatie-logo.png" /></a></td>

				      <td style="text-align: right; color: #888; padding-top: 10px;">
							<p style="font-size: 11px;">La red social y de servicios de<br>Aparejadores y Arquitectos Técnicos</p> 
				      </td>

					</tr>
			</tbody>
			</table>

		</td>
	</tr>


	<tr>
		<td bgcolor="#ffffff" style="text-align: justify; background-color: #ffffff; color:#505050; font-family: Arial,Helvetica,sans-serif; padding: 0 1em 1em 1em;" align="justify">


<!--*********************************************-->

<?=$texto?>

<!--*********************************************-->

            
          </td>
        </tr>
      </tbody>
    </table>


<br/>


<table style="width: 800px; background-color: white;" align="center" border="0" width="800" bgcolor="white">

        <tr>
          <td bgcolor="white" style="color: #505050; font-family: Arial,Helvetica,sans-serif; text-align: justify; font-size: 11px; padding: 1em;">

          	<table align="center">
				<tr>
					<td><a href="https://www.facebook.com/pages/Activatie/783928711720244?fref=ts" title="Facebook"><img alt="facebook" src="http://www.activatie.org/web/email-correo/pics/icon-social_facebook.jpg"></a>&nbsp;</td>
					<td>&nbsp;<a href="https://es.linkedin.com/pub/activatie/ba/7b/ab9" title="Linkedin" ><img alt="linkedin" src="http://www.activatie.org/web/email-correo/pics/icon-social_linkedin.jpg"></a>&nbsp;</td>
					<td>&nbsp;<a href="https://www.twitter.com/activatie" title="Twitter"><img alt="twitter" src="http://www.activatie.org/web/email-correo/pics/icon-social_twitter.jpg"></a>&nbsp;</td>
					<td>&nbsp;<a href="https://www.youtube.com/channel/UCFwdS1lgUFVRIejymj-8UYA" title="YouTube"><img alt="youtube" src="http://www.activatie.org/web/email-correo/pics/icon-social_youtube.jpg"></a>&nbsp;</td>
					<td>&nbsp;<a href="mailto:info@activatie.org" title="E-Mail"><img alt="mail" src="http://www.activatie.org/web/email-correo/pics/icon-social_mail.jpg"></a>&nbsp;</td>
					<td>&nbsp;<a href="https://www.activatie.org/web/rss/rss.xml" title="Feed RSS"><img alt="rss" src="http://www.activatie.org/web/email-correo/pics/icon-social_rss.jpg"></a></td>
					
				
					
				</tr>
				

          	</table>
          </td>
        </tr>
		
		<tr>
			<td align="justify" style="text-align: justify;  padding-left: 0 !important; padding-right: 0 !important; min-height: 40px; list-style: none;">
			<ul style="display: table; margin: auto; width: auto; list-style: disc; padding: 0;">
					<div style="text-align: center; font-size:11px; margin-bottom:5px;">Integrado por:<div>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; margin-bottom:5px;" href="http://www.aparejadoresalbacete.es" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-albacete.png" alt="APAREJADORES ALBACETE" title="COAATIIE ALBACETE"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatalicante.org" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-alicante.png" alt="APAREJADORES ALICANTE" title="COAATIE ALICANTE"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatba.com" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-badajoz.png" alt="APAREJADORES BADAJOZ" title="COAATIE BAJADOZ"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatburgos.com/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/BURGOS.png" alt="APAREJADORES BURGOS" title="COAATIE BURGOS"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatcaceres.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/CACERES.jpg" alt="APAREJADORES CACERES" title="COAATIE CACERES"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatcan.com" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-cantabria.png" alt="APAREJADORES CANTABRIA" title="COAATIE CANTABRIA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatcuenca.com" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-cuenca.png" alt="APAREJADORES CUENCA" title="COAATIE CUENCA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatfuerteventura.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/FUERTEVENTURA.jpg" alt="APAREJADORES FUERTEVENTURA" title="COAATIE FUERTEVENTURA"></a></li>				
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatcr.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/CIUDADREAL.png" alt="APAREJADORES CIUDAD REAL" title="COAATIE CIUDAD REAL"></a></li>		
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatcordoba.com" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-cordoba.png" alt="APAREJADORES CÓRDOBA" title="COAATIE CÓRDOBA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatgr.es" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-granada.png" alt="APAREJADORES GRANADA" title="COAATIE GRANADA"></a></li>	
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatgrancanaria.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/GRANCANARIA.jpg" alt="APAREJADORES GRANCANARIA" title="COAATIE GRANCANARIA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.aparejadoresguadalajara.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/GUADALAJARA.png" alt="APAREJADORES GUADALAJARA" title="COAATIE GUADALAJARA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaathuesca.com/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/HUESCA.jpg" alt="APAREJADORES HUESCA" title="COAATIE HUESCA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="https://coaatlanz.org" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/LANZAROTE.png" alt="APAREJADORES LANZAROTE" title="COAATIE LANZAROTE"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatleon.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/LEON.png" alt="APAREJADORES LEÓN" title="COAATIE LEÓN"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatlugo.com/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/LUGO.jpg" alt="APAREJADORES LUGO" title="COAATIE LUGO"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.aparejadoresmadrid.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/MADRID.png" alt="APAREJADORES MADRID" title="COAATIE MADRID"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatiemu.es" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-murcia.png" alt="APAREJADORES MURCIA" title="COAATIE MURCIA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.aparejadoresou.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/OURENSE.jpg" alt="APAREJADORES OURENSE" title="COAATIE OURENSE"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatpo.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/PONTEVEDRA.jpg" alt="APAREJADORES PONTEVEDRA" title="COAATIE PONTEVEDRA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatsa.org" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-salamanca.png" alt="APAREJADORES SALAMANCA"  title="COAATIE SALAMANCA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaat-tfe.com/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/TENERIFE.png" alt="APAREJADORES TENERIFE" title="COAATIE TENERIFE"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://coaatteruel.es/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/TERUEL.jpg" alt="APAREJADORES TERUEL" title="COAATIE TERUEL"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.caatvalencia.es" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/logo-valencia.png" alt="APAREJADORES VALENCIA"  title="COAATIE VALENCIA"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.coaatz.org/" ><img style="width:80px;" width="80" src="http://www.activatie.org/web/img/ZARAGOZA.jpg" alt="APAREJADORES ZARAGOZA" title="COAATIE ZARAGOZA"></a></li>
					
					<div style="text-align: center; font-size:11px; margin-top:10px; margin-bottom:5px;">Colaboración de:<div>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.arquitectura-tecnica.com/" ><img style="width:90px;" width="100" src="http://www.activatie.org/web/img/cgatelogo.png" alt="CGATE" title="Consejo General de la Arquitectura Técnica de España"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.musaat.es/" ><img style="width:90px;" width="100" src="http://www.activatie.org/web/img/musaatlogo.png" alt="MUSAAT"  title="Mutua de seguros a prima fija"></a></li>
					<li style="display: inline; list-style: disc;"><a style="display: inline-block; text-align: center; width: 100px; " href="http://www.premaat.es/" ><img style="width:90px;" width="100" src="http://www.activatie.org/web/img/premaatlogo.png" alt="PREMAAT"  title="Mutua de la arquitectura técnica"></a></li>
						
			
			</ul>
			</td>
		</tr>
		<tr>
				<td class="footerContent" style="padding-top:20px; padding-bottom:5px; text-align:center;" valign="top">
			
					<span style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;">Copyright © <?=$year?> ACTIVATIE.</span><br>
					<a style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" href="http://www.activatie.org">www.activatie.org</a><br>
					<span style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;">Contacta con nosotros:</span><br>
					<a style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" href="mailto:info@activatie.org">info@activatie.org</a>

				</td>
			</tr>
		<tr>
				<td colspan="3" class="footerContent" style="text-align:center;" valign="top">

					<a style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" href="https://www.activatie.org/web/index.php?infobaja=true" style="color:#D72D13; text-decoration:none;">desinscribirme de la lista</a>&nbsp;&nbsp;&nbsp;<a style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" href="https://www.activatie.org/web/index.php?infoalertas=true" style="color:#D72D13; text-decoration:none;">actualizar mis alertas</a>&nbsp;
					<br>
				</td>
		</tr>

        <tr>
          <td bgcolor="white" align="justify" style="color: #505050; font-family: Arial,Helvetica,sans-serif; text-align: justify; font-size: 11px; padding: 1em; padding-top:5px;">

          	El presente correo le ha sido enviado como parte del proceso informativo a los usuarios de la web <a style="text-decoration:none;" href="http://www.activatie.es">www.activatie.es</a> atendiendo a los datos que la plataforma ACTIVATIE tiene sobre usted. De conformidad con el artículo 5 de la Ley 15/1999, de 13 de diciembre de Protección de Datos de Carácter Personal, Ud. tiene derecho a acceder a esta información, a rectificarla si los datos son erróneos y darse de baja del fichero comunicándonos su solicitud a la dirección <a style="text-decoration:none;" href="http://www.activatie.org/web/contacto.php">www.activatie.org/contacto/</a>, cumpliéndose así la notificación prevista en el artículo 5.4 de la misma. Asimismo, usted puede configurar la dirección de envío de estos correos y notificaciones, así como otros detalles, accediendo a su perfil personal a través de la web de <a style="text-decoration:none;" href="http://www.activatie.org">www.activatie.org</a> con su usuario y contraseña.<br>
          </td>
        </tr>

</table>



<br/>








</body>
</html>

<? } 








































/** Copia plantilla 25-06 */
function COPIAgetPlantillaEmail($idemail=0, $texto=""){
	
	if ($idemail==0){
		if ($texto==""){
			$texto=$_SESSION['textoEmail'];
		}
		else{
			// Texto en parametro
		}
	}
	else{
		$sql = "SELECT texto FROM emailhistorial WHERE id='$idemail'";
		$result = posgre_query($sql);
		if ($row=pg_fetch_array($result)){
			$texto = $row['texto'];
		}
	}
	
	

	?>
	
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
			<title>ACTIVATIE</title>		
			<style type="text/css">
			
			#outlook a{
				padding:0;
				
			}
			a {
				color:#ee1717;
			}
			.ReadMsgBody{
				width:100%;
			}
			.ExternalClass{
				width:100%;
			}
			body{
				margin:0;
				padding:0;
			}
			img{
				border:0;
				height:auto;
				line-height:100%;
				outline:none;
				text-decoration:none;
			}
			table,td{
				border-collapse:collapse;
				mso-table-lspace:0pt;
				mso-table-rspace:0pt;
			}
			#bodyTable{
				height:100% !important;
				margin:0;
				padding:0;
				width:100% !important;
			}
			td.contentblock p { 
			color: #505050;
			font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
			font-size: 15px;
			line-height: 150%;
			text-align: left;
			padding-right:20px;
		}
			#productDescription {
			color: #505050;
			font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
			font-size: 15px;
			line-height: 150%;
			text-align:center !important;
			}
			h1{
				color:#000000;
				display:block;
				font-family:Helvetica;
				font-size:26px;
				font-style:normal;
				font-weight:bold;
				line-height:100%;
				letter-spacing:normal;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				text-align:left;
			}
			h2{
				color:#dddddd;
				display:block;
				font-family:Helvetica;
				font-size:20px;
				font-style:normal;
				font-weight:bold;
				line-height:100%;
				letter-spacing:normal;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				
			}
			h3{
				color:#606060;
				display:block;
				font-family:Helvetica;
				font-size:16px;
				font-style:normal;
				font-weight:bold;
				line-height:100%;
				letter-spacing:normal;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				text-align:left;
			}
			h4{
				color:#808080;
				display:block;
				font-family:Helvetica;
				font-size:12px;
				font-style:normal;
				font-weight:bold;
				line-height:100%;
				letter-spacing:normal;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				text-align:left;
			}
			#templatePreheader{
				background-color:#f1f1f3;
				
			}
			.preheaderContent{
				
				color:#000000;
				font-family:Helvetica;
				font-size:10px;
				line-height:125%;
				text-align:left;
			}
			.preheaderContent a:link,.preheaderContent a:visited,.preheaderContent a .yshortcuts{
				color:#D13100;
				font-weight:normal;
				text-decoration:underline;
			}
			#templateHeader{
				background-color:#f1f1f3;
				border-top:0;
				border-bottom:0;
			}
			.headerContent{
				background-color:#f1f1f3;
				color:#E2E2E2;
				font-family:Helvetica;
				font-size:20px;
				font-weight:bold;
				line-height:100%;
				padding-top:20px;
				padding-right:0;
				padding-bottom:20px;
				padding-left:0;
				text-align:center;
				vertical-align:middle;
			}
			.headerContent a:link,.headerContent a:visited,.headerContent a .yshortcuts{
				color:#EE3A23;
				font-weight:normal;
				text-decoration:underline;
			}
			.titleContent{
				background-color:#f1f1f3;
				color:#a6a6a6;
				font-size:12px;
				font-family:Helvetica Neue ,Helvetica,Arial,sans-serif;
			   text-shadow: 0 1px 0 #ffffff;
				
			}
			.promoCell{
				background-color:#000000;
				color:#FFFFFF;
				font-family:Helvetica Neue,Helvetica,Arial,sans-serif;
				font-size:16px;
				line-height:26px;
				text-align:left;
				font-weight:normal;
				margin-top:0;
				margin-bottom:0;
				padding-top:0;
				padding:25px;
				
			}
			#templateBody{
				background-color:#f1f1f3;
				border-top:0;
				border-bottom:0;
				padding:20px;
			}
			.bodyContent{
		color: #505050;
		font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
		font-size: 15px;
		line-height: 150%;
		text-align: left;
		padding: 20px;
			}
			.bodyContent a:link,.bodyContent a:visited,.bodyContent a .yshortcuts{
				color:#EE3A23;
				font-weight:normal;
				text-decoration:underline;
			}
			.bodyContent img{
				display:inline;
				height:auto;
				max-width:600px !important;
			}
			
			
			.productDescriptionCell{
				padding-left:20px;
				color: #505050;
				font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
			font-size: 15px;
			line-height: 150%;
			text-align: left;
			}
			.bodyColumn{
				color:#505050;
				font-family:Helvetica Neue,Helvetica,Arial,sans-serif;
				font-size:15px;
				line-height:150%;
				text-align:left;
			}
		.bodyColumn3{
				padding-left:20px;
				color:#505050;
				font-family:Helvetica Neue,Helvetica,Arial,sans-serif;
				font-size:15px;
				line-height:150%;
				text-align:left;
			}
			.bodyColumn4{
				padding-left:20px;
				color:#505050;
				font-family:Helvetica Neue,Helvetica,Arial,sans-serif;
				font-size:15px;
				line-height:150%;
				text-align:left;
			}
			.templateButton{
				-moz-border-radius:0px;
				-webkit-border-radius:0px;
				background-color:#000000;
				border:0;
				border-radius:0px;
			}
			.templateButtonContent,.templateButtonContent a:link,.templateButtonContent a:visited,.templateButtonContent a .yshortcuts{
				color:#FFFFFF;
				font-family:Helvetica;
				font-size:11px;
				font-weight:bold;
				letter-spacing:-.5px;
				line-height:100%;
				text-align:center;
				text-decoration:none;
			}
			.bodyContent img{
				display:inline;
				height:auto;
			}
			#templateSeparator{
				background-color:#f1f1f3;
				
			}
			#templateFooter{
				padding:20px;
			}
			body,#bodyTable{
				background-color:#f1f1f3;
			}
			.footerContent{
				color:#a6a6a6;
				font-family:Helvetica;
				font-size:10px;
				line-height:150%;
				text-align:left;
			}
			.footerContent a:link,.footerContent a:visited,.footerContent a .yshortcuts{
				color:#8d8c8c;
				font-weight:normal;
				text-decoration:underline;
			}
			.footerContent img{
				display:inline;
			}
			#monkeyRewards img{
				max-width:190px !important;
			}
			td.bodyColumn h2 {
			color:#000000;
			display:block;
			font-family:Helvetica;
			font-size:20px;
			font-style:normal;
			font-weight:bold;
			line-height:100%;
			letter-spacing:normal;
			margin-top:0;
			margin-right:0;
			margin-bottom:10px;
			margin-left:0;
			
		}
		td.bodyColumn h3 {
			color:#505050;
			display:block;
			font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
			font-size:15px;
			font-style:normal;
			font-weight:bold;
			line-height:150%;
			letter-spacing:normal;
			margin-top:0;
			margin-right:0;
			margin-bottom:10px;
			margin-left:0;
			
		}
		td.bodyColumn2 h2 {
			color:#000000;
			display:block;
			font-family:Helvetica;
			font-size:20px;
			font-style:normal;
			font-weight:bold;
			line-height:100%;
			letter-spacing:normal;
			margin-top:0;
			margin-right:0;
			margin-bottom:10px;
			margin-left:0;
			
		}
		td.bodyColumn3 h2 {
			color:#000000;
			display:block;
			font-family:Helvetica;
			font-size:20px;
			font-style:normal;
			font-weight:bold;
			line-height:100%;
			letter-spacing:normal;
			margin-top:0;
			margin-right:0;
			margin-bottom:10px;
			margin-left:0;
			
		}
		td.bodyColumn4 h2 {
			color:#000000;
			display:block;
			font-family:Helvetica;
			font-size:20px;
			font-style:normal;
			font-weight:bold;
			line-height:100%;
			letter-spacing:normal;
			margin-top:0;
			margin-right:0;
			margin-bottom:10px;
			margin-left:0;
			
		}
		td.bodyColumn2 p { 
			color: #505050;
			font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
			font-size: 15px;
			line-height: 150%;
			
			
		}
		td.bodyColumn3 p { 
			color: #505050;
			font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
			font-size: 15px;
			line-height: 150%;
			
			
		}
			
		
			
		@media only screen and (max-width: 600px){
			body,table,td,p,a,li,blockquote{
				-webkit-text-size-adjust:none !important;
			}
	
	}	@media only screen and (max-width: 600px){
			body{
				width:100% !important;
				min-width:100% !important;
			}
	
	}	@media only screen and (max-width: 600px){
			td[id=templateBody],td[id=templateFooter]{
				padding-right:10px !important;
				padding-left:10px !important;
			}
	
	}	@media only screen and (max-width: 600px){
	
			table[class=templateContainer]{
				max-width:600px !important;
				width:100% !important;
			}
	
	}	@media only screen and (max-width: 600px){
	
			table[class=tableContainer]{
				height:auto;
			 max-width:260px !important;
				width:100% !important;
				
			}		
	
	
	}	@media only screen and (max-width: 600px){
			table[id=templatePreheader]{
				display:none;
			}
	
	}	@media only screen and (max-width: 600px){
	
			h1{
				font-size:28px !important;
				line-height:100% !important;
			}
	
	}	@media only screen and (max-width: 600px){
	
			h2{
				font-size:24px !important;
				line-height:100% !important;
			}
	
	}	@media only screen and (max-width: 600px){
	
			h3{
				font-size:20px !important;
				line-height:100% !important;
			}
	
	}	@media only screen and (max-width: 600px){
			h4{
				font-size:16px !important;
				line-height:100% !important;
			}
	
	}	@media only screen and (max-width: 600px){
	
			img[class=logoimage]{
				height:auto !important;
				max-width:200px !important;
				width:100% !important;
			}
	
	}	@media only screen and (max-width: 600px){
			.headerContent{
				font-size:21px !important;
				line-height:150% !important;
				padding-top:20px !important;
				padding-right:10px !important;
				padding-bottom:20px !important;
				padding-left:10px !important;
			}
	
	}	@media only screen and (max-width: 600px){
			img[class=bodyImage]{
				height:auto !important;
				max-width:600px !important;
				width:100% !important;
				
			}
	
	}	@media only screen and (max-width: 600px){
	
			img[class=contentImage]{
				height:auto !important;
				max-width:560px !important;
				width:100% !important;
				
			}
	
	}	@media only screen and (max-width: 600px){
	
			img[class=galleryImage]{
				height:auto !important;
				max-width:267px !important;
				width:100% !important;
							
			}
			
	
	}  @media only screen and (max-width: 600px){
	
			img[class=executiveImage]{
				height:auto !important;
				max-width:200px !important;
				width:100% !important;
							
			}
			
	
	} 
		@media only screen and (max-width: 600px){
	
			.bodyContent{
				font-size:17px !important;
				line-height:150% !important;
			}
			.bodyColumn{
				font-size:17px !important;
				line-height:150% !important;
			}
			.bodyColumn3{
				font-size:17px !important;
				line-height:150% !important;
			}
			.bodyColumn4{
				font-size:17px !important;
				line-height:150% !important;
			}
			td.contentblock p {
				font-size:17px !important;
				line-height:150% !important;
			}
			td.bodyColumn p {
				font-size:17px !important;
				line-height:150% !important;
			}
			
	
	}	@media only screen and (max-width: 600px){
	
			.footerContent{
				font-size:13px !important;
				line-height:150% !important;
			}
	
	}	@media only screen and (max-width: 600px){
			table[class=productImageTable]{
				width:100% !important;
				
			}
			td[class=productImageCell]{
				width:267px !important;
				padding-left:20px !important;
				padding-bottom:20px !important;
				padding-top:20px !important;
				
			}
			table[class=productDescriptionTable]{
				width:100% !important;
				padding-left:5px !important;
				padding-right:10px !important;
			}
			td[class=bodyColumn]{
				padding-left:0px !important;
						
			}
			td[class=bodyColumn2]{
				text-align:center !important;
				padding-left:0px !important;
						
			}
			td[class=bodyColumn3]{
				padding-left:0px !important;
						
			}
			td[class=bodyColumn4]{
				padding-left:10px !important;
				padding-right:10px !important;
						
			}
			div[class="date"] {
				 text-align:center !important;
			 }
	
	}	@media only screen and (max-width: 600px){
			td[class=footerContent] a{
				display:block !important;
			}
	
	}		.preheaderContent a:link,.preheaderContent a:visited,.preheaderContent a .yshortcuts{
				color:#D13100;
			}
			body,#bodyTable{
				background-color:#f1f1f3;
			}
			.footerContent a:link,.footerContent a:visited,.footerContent a .yshortcuts{
				color:#8d8c8c;
			}
	
			@media only screen and (max-width : 600px) {
	
	
				td[class="force-col"] {
					display: block;
					padding-right: 0 !important;
				}
				table[class="col-2"] {
					float: none !important;
					width: 100% !important;
	
	
					margin-bottom: 15px;
					padding-bottom: 15px;
					
				}
				table[class="col-3"] {
	
					float: none !important;
					width: 100% !important;
	
					margin-bottom: 12px;
					padding-bottom: 12px;
					border-bottom: 1px solid #eee;
				}
	
	
				table[id="last-col-3"] {
					border-bottom: none !important;
					margin-bottom: 0;
				}
	
	
				img[class="col-3-img"] {
					float: right;
					margin-left: 6px;
					max-width: 130px;
				}
			}
		</style>
	
	</head>
	<body leftmargin="0" topmargin="0" offset="0" marginheight="0" marginwidth="0">
	
		<center>
		<br><br>
			<table id="bodyTable" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
			<tbody>
			<tr>
				<td align="center" valign="top">
	
				<? /* <table id="templatePreheader" border="0" cellpadding="10" cellspacing="0" width="100%">
				<tbody>
				<tr>
					<td align="center" valign="top">
	
						<table border="0" cellpadding="0" cellspacing="0" width="600">
						<tbody>
							<tr>
								<!-- // BOTONES SOCIALES -->
								<td style="max-width:32px; text-align:left"><a href="https://www.facebook.com/pages/Activatie/783928711720244?fref=ts" target="new"><img src="https://www.activatie.org/web/email-correo/pics/fb_1.png" alt="Síguenos en Facebook" border="0" width="32"></a></td>
								<td style="max-width:32px; text-align:left"><a href="https://www.twitter.com/activatie" target="new"><img src="https://www.activatie.org/web/email-correo/pics/twitter_2.png" alt="Síguenos en Twitter" border="0" width="32"></a></td>
								<td style="max-width:32px; text-align:left"><a href="https://www.activatie.org/web/rss/rss.xml" target="new"><img src="https://www.activatie.org/web/email-correo/pics/icon-social-rss.jpg" alt="rss" border="0" width="32"></a></td>
								<td style="max-width:32px; text-align:left"><a href="mailto:info@activatie.org" target="new"><img src="https://www.activatie.org/web/email-correo/pics/email.png" alt="Reenvíalo a tus amigos" style="max-width:32px;" border="0"></a></td>
						  <!-- // FIN BOTONES SOCIALES -->
		  
		  
				
					   </tr>
					  </tbody></table>
						</td>
					</tr>
				</tbody>
				</table> */ ?>
	
	
	
	
	
	
	
	
							
			<!-- // LOGO Y TITULO -->
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
			<tr>
				<td align="center" valign="top">
				</td>
			</tr>
			<tr>
				<td align="center" valign="top">
									
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tbody><tr>
						<td id="templateBody" align="center" valign="top">
				   
					  <!-- //LOGO Y TITULO -->
							<table class="templateContainer" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="600">
	
								<tbody><tr>
							
									<td style="background-color: #ffffff; padding-left: 18px; padding-right: 17px;padding-top:10px;border-top:1px solid #DDDDDD;" align="left" bgcolor="#ffffff" width="100%">
		
		
								<table class="columns-container" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
									<tbody><tr>
										<td class="force-col" style="padding-right: 0px;" valign="top">
		
											<table class="col-2" style="border-collapse:collapse; border-left: 1px solid #ffffff; border-right: 1px solid #ffffff;" align="left" border="0" cellpadding="0" cellspacing="0" width="199">
												<tbody><tr>
													<td class="bodyColumn2" bgcolor="#ffffff"><p style="mso-table-lspace:0pt; mso-table-rspace:0pt;"><a href="https://www.activatie.org" title="ACTIVATIE"><img src="http://www.activatie.org/web/img/activatie-logo.png" style="padding-bottom:10px;-ms-interpolation-mode:bicubic;" class="executiveImage" width="200"></a></p></td>
		  </tr>
		</tbody></table>
		<table class="col-2" border="0" cellpadding="0" cellspacing="0" width="360">
		  <tbody><tr>
				<td class="bodyColumn" bgcolor="#ffffff"><p style="mso-table-lspace:0pt; mso-table-rspace:0pt;">
				   </p>
				   <div class="date" align="right">
					
						<h2>ACTIVATIE</h2> 
						COMUNICACIONES
						</div>
						<p></p>
				</td>
		  </tr>
			</tbody>
		</table>
		</td>
		</tr>
	</tbody>
	</table>
		</td>
	  </tr>
			<tr>
				<td bgcolor="#FFFFFF" height="15px" width="100%">&nbsp;</td>
			</tr>
	</tbody></table>
	
	<!-- // FIN LOGO Y TÍTULO -->
	
	
	
	
	
	
	
	
	
	
	
	<!-- // MENSAJE -->
	
	<table class="templateContainer" border="0" cellpadding="0" cellspacing="0" width="600">
	
	<tbody>
	<!-- // FIN MENSAJE IMAGEN -->
	
	<!-- // CONTENIDO DEL MENSAJE -->
	
	<tr>
		<td class="bodyContent" style="padding-top:20px;" bgcolor="#FFFFFF" valign="top">
		<?=$texto?>
		</td>
	</tr>
	<!-- // FIN CONTENIDO -->
	
	
	<!-- // BOTON (SI ES NECESARIO) -->
															
	<!-- // FIN BOTON -->
										   
	</tbody>
	</table>
	
	</td></tr></tbody></table>
	</td></tr></tbody></table>
	<!-- // FIN CONTENIDO -->
										
										
									 
	
	
	
	
	
	
	
	
	
	   
						 
										
	
	</td>
	</tr>
	<tr>
	  
		<td align="center" bgcolor="#f1f1f3" valign="top">
		<!-- // FOOTER -->
	
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
			<tr>
				<td id="templateFooter" style="padding-bottom:40px;" align="center" valign="top">
					<table class="templateContainer" border="0" cellpadding="0" cellspacing="0" width="600">
					<tbody><tr>
	
						<td style="text-align:center;" valign="top" width="100%"><br/>
							<a href="https://www.facebook.com/pages/Activatie/783928711720244?fref=ts" target="new"><img src="http://www.activatie.org/web/email-correo/pics/fb_1.png" alt="Síguenos en Facebook" style="vertical-align:top; padding-bottom:12px; padding-right:10px;" border="0" hspace="0" vspace="0" width="32"></a>
							<a href="https://www.twitter.com/activatie" target="new"><img src="http://www.activatie.org/web/email-correo/pics/twitter_2.png" alt="Síguenos en Twitter" style="vertical-align:top; padding-bottom:12px; padding-right:10px;" border="0" hspace="0" vspace="0" width="32"></a>
							<a href="https://www.activatie.org/web/rss/rss.xml" target="new"><img src="http://www.activatie.org/web/email-correo/pics/icon-social-rss.jpg" alt="rss" style="vertical-align:top; padding-bottom:12px; padding-right:10px;" border="0" hspace="0" vspace="0" width="32"></a>
							<a href="mailto:info@activatie.org" target="new"><img src="http://www.activatie.org/web/email-correo/pics/email.png" alt="Compártelo con tus amigos" style="vertical-align:top; padding-bottom:12px; padding-right:10px;" border="0" hspace="0" vspace="0" width="32"></a>
						</td>
				</tr>
				<tr>
						<td class="footerContent" style="padding-top:20px; padding-bottom:20px; text-align:center;" valign="top">
	
				<em>Copyright © 2015 ACTIVATIE.</em><br>
				<a href="http://www.activatie.org">www.activatie.org</a><br><br>
				<strong>Contacta con nosotros:</strong><br>
				<a href="mailto:info@activatie.org">info@activatie.org</a><br><br>
	
						</td>
					</tr>
			 	<tr>
						<td colspan="3" class="footerContent" style="text-align:center;" valign="top">
	
							<a href="https://www.activatie.org/web/index.php?infobaja=true" style="color:#ee1717; text-decoration:none;">desinscribirme de la lista</a>&nbsp;&nbsp;&nbsp;<a href="https://www.activatie.org/web/index.php?infoalertas=true" style="color:#ee1717; text-decoration:none;">actualizar mis alertas</a>&nbsp;
							<br><br>
						</td>
				</tr>
				<tr>
				<td bgcolor="#F7F7F7" align="justify"
				style="background-color:#F7F7F7; font-family:
				Arial,Helvetica,sans-serif; text-align: justify; font-size: 9px; color:#999999;
				padding: 1em;">

						<span>El presente correo le ha sido enviado como parte del proceso informativo a los usuarios de la web <a href="www.activatie.org"  style="color:#ee1717; text-decoration:none;">www.activatie.org</a> atendiendo a los datos que el Plataforma Colegial activatie tiene sobre usted. De conformidad con el artículo 5 de la Ley 15/1999, de 13 de diciembre de Protección de Datos de Carácter Personal, Ud. tiene derecho a acceder a esta información, a rectificarla si los datos son erróneos y darse de baja del fichero accediendo al apartado “Mis Datos” una vez identificado, cumpliéndose así la notificación prevista en el artóculo 5.4 de la misma. Asimismo, usted puede configurar la dirección de envío de estos correos y notificaciones, así como otros detalles, accediendo a su perfil personal a través de la web de <a href="www.activatie.org"  style="color:#ee1717; text-decoration:none;">www.activatie.org</a> con su usuario y contraseña.</span>
					</td>
				</tr>
					</tbody></table>
				</td>
			</tr>
		</tbody></table>
	
	
	
	
	
	
	<!-- FIN FOOTER \\ -->
	</td>
	</tr>
	</tbody></table>
	
	</center>
	  
	</body>
	</html>

<? } ?>