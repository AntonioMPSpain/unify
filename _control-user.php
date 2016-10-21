<?php

include("_config.php"); 
include_once "api_meneame.php";

if (isset($_REQUEST['logout'])){
	
	$_SESSION[controlactiva] = false;
	$_SESSION[nivel] = false;
	$_SESSION[email] = false;
	$_SESSION[idusuario] = false;
	$_SESSION[nombre] = false;	
	$_SESSION[nif] = false;
	$_SESSION[idcolegio] = false;
	$_SESSION[idprovincia] = false;
	
	session_unset();
	session_destroy();
	session_start();
	session_regenerate_id(true);
	
	// logoutUser();
	
	header("Location: ".$baseUrl.$path);
	exit();
	
}
else{
	
	$usuario=strtolower($_POST['signin-login']);
	$pass=$_POST['signin-password'];
	
	if (($usuario<>"")){ 
		$sql="SELECT * FROM usuario WHERE confirmado=1 AND LOWER(login)='$usuario' AND borrado=0 ORDER BY id ASC;";
		$result = posgre_query($sql);
	
		if ($data = pg_fetch_array($result)){
			
			/*
			if ($data[passmd5]==""){
				$_SESSION["nifrecuperar"]=$usuario;
				header("Location: a_activar_usuario.php");
				exit();
			}
			*/
			if ($pass==""){
				$_SESSION[error]="No ha introducido la contraseña.";	
				header("Location: index.php?error=true#2");
				exit();
			}
			
			if($data[pass]==$pass) {
				
				if($data[nivel] == '1') { // Administrador Total
				
				}
				elseif($data[nivel] == '2') { // Administrador Colegio
					$_SESSION[id_categoria_moodle] = $data["id_categoria_moodle"]; 
					
				}
				elseif($data[nivel] == '3') {	// Docente
					
				}
				elseif($data[nivel] == '4') {	// Alumno
				
				}
				elseif($data[nivel] == '5') {	// Directivo
				
				}
				elseif($data[nivel] == '9') {	// Empresa
				
				}
				else{
					exit();
				}
						
				authenticateUser($usuario, $pass);		/* Meneame */
							
				$_SESSION[controlactiva] = true;
				$_SESSION[nivel] =$data["nivel"];
				$_SESSION[email] = $data["email"];
				$_SESSION[idusuario] = $data["id"]; 
				$_SESSION[nombre] = $data["nombre"];	
				$_SESSION[nif] = $data["nif"]; 
				$_SESSION[idcolegio] = $data["idcolegio"]; 
				$_SESSION[idprovincia] = $data["idprovincia"];
				
				if ($data[nivel] == '2'){
					$_SESSION[idcolegio] = $data["id"];
				}
				
				$query = posgre_query("INSERT INTO controla (usuario,fechahora,ip,comentario) VALUES ('$usuario','$fechahora','$ip','okuser')");  // or die (pg_error())
				
				if (($data[nivel] == '2')||($data[nivel] == '3')||($data[nivel] == '4')){
					//Metodo de cayetano
					$urlmoodle = $baseUrl."moodle/login/index.php";
					?>
					<br> 
					<form action= "<?=$urlmoodle?>" method="post" id="loginform" >
					<input name="username" type="hidden" value="<?=$usuario?>">
					<input name="password" type="hidden" value="<?=$pass?>">
					</form>
					
					<script type="text/javascript">
						document.getElementById('loginform').submit();
					</script>
					<?
					
					exit();
				}
				else{
					
					header("Location: ".$baseUrl.$path);
					exit();
				}
					
			}
		}
	}

}


?>