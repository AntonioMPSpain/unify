<?
include("_cone.php");
include("_funciones.php");
include_once "a_api_emails.php";

$sql="SELECT email_cron.dominio AS dominio, email_cron.id AS id,email_cron.idemail AS idemail,email_cron.correo AS correo, email.texto AS texto, email.asunto AS asunto, email_cron.idusuario FROM email_cron,emailhistorial as email WHERE email.id=email_cron.idemail AND email.borrado=0 AND email_cron.estado='0' AND email_cron.dominio='1' AND enviar=1 ORDER BY email_cron.preferencia DESC, random() LIMIT 49;";

$result2= posgre_query($sql); 


if (pg_num_rows($result2)==0){
	$sql = "SELECT * FROM email_cron WHERE estado='0' AND dominio='2' ORDER BY preferencia DESC, random() LIMIT 49;";
	$result2= posgre_query($sql); 
}

if(pg_num_rows($result2) > 0) {
	
	while($row = pg_fetch_array($result2)) {
	
		$id=$row["id"];
		$idemail=$row["idemail"];
		$idusuario = $row["idusuario"];
		$asunto = $row["asunto"];
		$dominio = $row["dominio"];
		
		if ($dominio==1){
			$body = file_get_contents('https://www.activatie.org/web/plantillaemail.php?plantilla&idemail='.$idemail);
		}
		elseif ($dominio==2){	//TEEF
			include_once "../www.tuedificioenforma.es/_funcion_email.php";
			include_once "../www.tuedificioenforma.es/_cone.php";
			$link=Conectarse();
			$sql = "SELECT * FROM email WHERE id='$idemail'";
			$result3 = mysql_query($sql, $link);
			$row2 = mysql_fetch_array($result3);
			$texto=$row2["texto"];
			$asunto = utf8_decode($row2["asunto"]);
			$body = utf8_decode(dame_plantilla_email('Newsletter',$texto));	
		}
		
		
		if ($idusuario=="-1"){		// Suscrito web
			$email = $row["correo"];
			$nombre = "";
			$apellidos = "";
		}
		else{						// Usuario registrado. Sacamos su email
			$sql = "SELECT nombre, apellidos, email,login FROM usuario WHERE id='$idusuario'";
			$result = posgre_query($sql);
			if ($row = pg_fetch_array($result)){
				$email = $row["email"];
				$nombre = $row["nombre"];
				$apellidos = $row["apellidos"];
				$login=$row["login"];
			}
		}
		
		$comodinnombre=strpos($body,"%%nombre%%");
   		if($comodinnombre!==false){
			$body = str_replace("%%nombre%%", $nombre, $body);
		}
		
		$comodinidemail=strpos($body,"%%idemail%%");
   		if($comodinidemail!==false){
			$body = str_replace("%%idemail%%", $idemail, $body);
			$comodinhash=strpos($body,"%%hash%%");
			if($comodinhash!==false){
				$hash = md5($idemail."ahksl23mz015");
				$body = str_replace("%%hash%%", $hash, $body);
			}
		}
		
		$comodinapellidos=strpos($body,"%%apellidos%%");
   		if($comodinapellidos!==false){
			$body = str_replace("%%apellidos%%", $apellidos, $body);
		}

		$comodinenlaceactivacion=strpos($body,"%%enlaceactivacion%%");
		if($comodinenlaceactivacion!==false){
			$enlaceactivacion="https://www.activatie.org/web/a_activar_usuario.php?n=".$login;
			$enlaceactivacion="<a href=\"$enlaceactivacion\">enlace</a>";

			$body = str_replace("%%enlaceactivacion%%", $enlaceactivacion, $body);
		}

		//$exito= enviarEmail($email, $asunto ,$body, $dominio);	
		
		if($exito){
			echo "OK<br>";
			$Query = posgre_query("UPDATE email_cron SET estado='$exito',exito='$exito',fechaenvio=NOW() WHERE id='$id';") ;//or die (mysql_error());

		}
		else{
			echo "ERROR:".$exito."<br>";
		}
		
		if ($exito<>'1'){
	   		$Query = posgre_query("UPDATE email_cron SET textoerror='$exito' WHERE id='$id';");
		}
	}
}
if (date('h:i:s')=="00:00:00"){
	include("_rss_a_html_infoempleo_cron.php");
}

?>