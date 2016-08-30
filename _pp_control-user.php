<?
ini_set('session.cookie_httponly', 1); //Robo por Cross-Site Scripting (además de evitando los ataques XSS) 
ini_set( 'session_gc_maxlifetime' , 1800 );
//ini_set('session.cookie_domain','.178.63.70.165');
ini_set('session.cookie_domain','activatie.org');
ini_set('session.cookie_domain','tuedificioenforma.es');
session_start();
include("_cone.php"); 
include("_funciones.php"); 
include("_funciones_class.php"); 
require_once('lib_actv_api.php');

////////////////////////// SEGURIDAD ///////////////////////////////////
	// 1 SI EN NECESARIO AADIR CAPTCHA
	
	// 2 Poner tb un token secreto desde el formulario aqui
		//$_SESSION['SKey'] = uniqid(mt_rand(), true);
	// 3 Para ataques desde fuera. NO funciona en este server
		$weborigen=$_SERVER["HTTP_REFERER"]; 
		if ($weborigen<>$c_web){
				//header("Location: index.php?error=true&est=ataque#message");
				//exit();	
		}
		$_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['seguroses'] = md5($_SERVER['HTTP_USER_AGENT']."act".$_SERVER['REMOTE_ADDR']);

	// 4 LIMPIAR CADENA
		$usuario=cleanInput(strip_tags($_POST['usuario']));
		$pass=(strip_tags($_POST['contrasena']));
		//$usuario=recoge("usuario"); //Funcion recoge -->limpia carazteres
		$usuario=strtoupper($usuario); 
		//$usuario=ereg_replace("[^0-9]",'',$usuario); //Quito letras
		//$pass=recoge("contrasena"); //Funcion recoge -->limpia carazteres
		$idcurso=cleanInput(strip_tags($_GET['idcurso']));
		$idpublicacion=cleanInput(strip_tags($_GET['idpub']));
		$idencuesta=cleanInput(strip_tags($_GET['idencuesta']));
		$tipouser=cleanInput(strip_tags($_GET['tipouser']));
		$modalidadw=cleanInput(strip_tags($_GET['modalidadw']));
		/* Cogemos si el tipo de inscripción es preencial u online */
			$insc="";
			if ($modalidadw==2){
				$inscripcion=trim(strip_tags($_REQUEST['inscripcion']))+0;
				$insc="&inscripcion=$inscripcion";
			}
		/**/
	// 5 Cumplir requisitos de datos
////////////////////////// SEGURIDAD ///////////////////////////////////

$fechahora = date("Y-m-d H:i:s"); 
$fecha= date("Y-m-d"); 
$hora = date("H:i:s"); 
$ip_user = $_SERVER["REMOTE_ADDR"]; 
$http = $_SERVER["HTTP_REFERER"]; 
$ip="IP:".$ip_user." - ".$http;
//$usuario=solonumeros($usuario);
if (($usuario<>"")){ //
	$link=conectar(); 
	//$sql=pg_escape_string("SELECT * FROM usuario WHERE confirmado=1 AND login = '$usuario' AND borrado=0 ORDER BY id DESC;");
	$sql="SELECT * FROM usuario WHERE confirmado=1 AND login = '$usuario' AND borrado=0 ORDER BY id ASC;";
	$Query = pg_query($link,$sql ) ;//or die ("e1-".pg_error()); 
	if((pg_num_rows($Query) != 0)&&($usuario<>"")) { //Entrada ADMIN
			$data = pg_fetch_array($Query);
			$idmoo=get_iduser_moodle($data["id"]);
			/*if ($idmoo==0){
				$_SESSION[error]="Lo sentimos pero su usuario tiene incongruencia con el portal de cursos.<br />Debe contactar con su Colegio administrador.";	
				header("Location: index.php?error=true#1");
				exit();
			}
			*/
			if ($data[pass]==""){
				$_SESSION["nifrecuperar"]=$usuario;
				header("Location: a_activar_usuario.php");
				exit();
			}
			
			if ($pass==""){
				$_SESSION[error]="No ha introducido la contraseña.";	
				header("Location: index.php?error=true#2");
				exit();
			}
			
			if($data[pass]==$pass) { //
				//Poner un case para segun el nivel crear unas sesiones u otras
				/*
							<select name="nivel" class="input-xlarge" >
								<option class="input-xlarge" value="0">[sin acceso]</option>
								<option class="input-xlarge" value="1">Administrador Total</option>
								<option class="input-xlarge" value="2">Administrador Colegio</option>
								<option class="input-xlarge" value="3">Profesor</option>
								<option class="input-xlarge" value="4">Alumno</option>
						  	</select>
				
				*/
				if($data[nivel] == '1') { // Administrador Total
					$_SESSION[controlactiva] = true;
					$_SESSION[nivel] =$data["nivel"];
					$_SESSION[email] = $data["email"];
					$_SESSION[idusuario] = $data["id"]; 
					$_SESSION[nombre] = $data["nombre"];					
					$_SESSION[tipo] = $data["tipo"]; 
					$_SESSION[nif] = $data["nif"]; 
					$link=conectar(); 
					$Query = pg_query($link,"INSERT INTO controla (usuario,fechahora,ip,comentario) VALUES ('$usuario','$fechahora','$ip','okuser')");  // or die (pg_error())
					//header("Location: https://www.activatie.org/web/index.php");
					header("Location: https://www.activatie.org/web/index.php");
					exit();
				}elseif($data[nivel] == '2') { // Administrador Colegio
					$_SESSION[controlactiva] = true;
					$_SESSION[nivel] =$data["nivel"];
					$_SESSION[email] = $data["email"];
					$_SESSION[idcolegio] = $data["id"]; 
					$_SESSION[tipo] = $data["tipo"]; 
					$_SESSION[idprovincia] = $data["idprovincia"];
					$_SESSION[id_categoria_moodle] = $data["id_categoria_moodle"]; 
					$_SESSION[nombre] = $data["nombre"];					
					$_SESSION[nif] = $data["nif"]; 
					$link=conectar(); 
					$Query = pg_query($link,"INSERT INTO controla (usuario,fechahora,ip,comentario) VALUES ('$usuario','$fechahora','$ip','okuser')");  // or die (pg_error())
					if ($usuario=="2"){ //Pruebas
						//header("Location: https://www.activatie.org/web/index.php");
						header("Location: https://www.activatie.org/web/index.php");
					}else{
						//Metodo de cayetano
						$username=$usuario;
						$password=$pass;
						
						?>
						<br> 
						<form action="https://www.activatie.org/moodle/login/index.php" method="post" id="loginform" >
						<input name="username" type="hidden" value="<?=$username?>">
						<input name="password" type="hidden" value="<?=$password?>">
						</form>
						
						<script type="text/javascript">
							document.getElementById('loginform').submit();
						</script>
						<?
					}
					//header("Location: https://www.activatie.org/web/index.php");
					exit();
				}elseif($data[nivel] == '3') { // Profesor o docente
					$_SESSION[controlactiva] = true;
					$_SESSION[nivel] =$data["nivel"];
					$_SESSION[email] = $data["email"];
					$_SESSION[idusuario] = $data["id"]; 
					$_SESSION[tipo] = $data["tipo"]; 
					$_SESSION[idcolegio] = $data["idcolegio"]; 
					$_SESSION[idprovincia] = $data["idprovincia"];
					// Select para --> Por si quiere editar cursos --> $_SESSION[id_categoria_moodle] = $data["id_categoria_moodle"]; 
					$_SESSION[nombre] = $data["nombre"];					
					$_SESSION[nif] = $data["nif"]; 
					$link=conectar(); 
					$Query = pg_query($link,"INSERT INTO controla (usuario,fechahora,ip,comentario) VALUES ('$usuario','$fechahora','$ip','okuser')");  // or die (pg_error())
						//Metodo de cayetano
						$username=$usuario;
						$password=$pass;
						
						?>
						<br>
						<form action="https://www.activatie.org/moodle/login/index.php" method="post" id="loginform" >
						<input name="username" type="hidden" value="<?=$username?>">
						<input name="password" type="hidden" value="<?=$password?>">
						</form>
						
						<script type="text/javascript">
							document.getElementById('loginform').submit();
						</script>
						<?
					//header("Location: https://www.activatie.org/web/zona-privada_admin_cursos_1.php");
					if ($idcurso<>""){
					
						$sql = "SELECT * FROM curso WHERE id='$idcurso' AND borrado=0";
						$result = posgre_query($sql);

						if ($row = pg_fetch_array($result)){
							$modalidad=$row["modalidad"];
						}
						/* Cogemos si el tipo de inscripción es preencial u online */
							$insc="";
							if ($modalidad==2){
								$inscripcion=trim(strip_tags($_REQUEST['inscripcion']))+0;
								$insc="&inscripcion=$inscripcion";
							}
						/**/
						header("Location: https://www.activatie.org/web/inscripcion2.php?est=ok&idcurso=$idcurso$insc");
						exit();
					}
					elseif($idpublicacion<>""){
						$formato=cleanInput(strip_tags($_GET['formato']));
						header("Location: https://www.activatie.org/web/a_publicacionpago_2.php?id=$idpublicacion&formato=$formato");
						exit();	
					}
					elseif($idencuesta<>""){
						$token=cleanInput(strip_tags($_GET['t']));
						header("Location: https://www.activatie.org/web/encuesta.php?id=$idencuesta&t=$token");
						exit();	
					}
					
					exit();
				
				}elseif($data[nivel] == '5') { // Directivo
					$_SESSION[controlactiva] = true;
					$_SESSION[nivel] =$data["nivel"];
					$_SESSION[email] = $data["email"];
					$_SESSION[idusuario] = $data["id"]; 
					$_SESSION[tipo] = $data["tipo"]; 
					$_SESSION[idcolegio] = $data["idcolegio"]; 
					$_SESSION[idprovincia] = $data["idprovincia"];
					// Select para --> Por si quiere editar cursos --> $_SESSION[id_categoria_moodle] = $data["id_categoria_moodle"]; 
					$_SESSION[nombre] = $data["nombre"];					
					$_SESSION[nif] = $data["nif"]; 

					$link=conectar(); 
					$Query = pg_query($link,"INSERT INTO controla (usuario,fechahora,ip,comentario) VALUES ('$usuario','$fechahora','$ip','okuser')");  // or die (pg_error())
						//Metodo de cayetano
						$username=$usuario;
						$password=$pass;

					
					header("Location: https://www.activatie.org/web/index.php");
					exit();
				}elseif($data[nivel] == '4') { // Alumno
					$_SESSION[controlactiva] = true;
					$_SESSION[nivel] =$data["nivel"];
					$_SESSION[email] = $data["email"];
					$_SESSION[idusuario] = $data["id"]; 
					$_SESSION[idcolegio] = $data["idcolegio"]; 
					$_SESSION[tipo] = $data["tipo"]; 
					$_SESSION[idprovincia] = $data["idprovincia"];
					$_SESSION[nombre] = $data["nombre"];			
					$_SESSION[apellidos] = $data["apellidos"];	
					$_SESSION[telefono] = $data["telefono"];				
					$_SESSION[nif] = $data["nif"]; 
					$tipouser=$_SESSION[tipo] = $data["tipo"]; 
					$link=conectar(); 
					$Query = pg_query($link,"INSERT INTO controla (usuario,fechahora,ip,comentario) VALUES ('$usuario','$fechahora','$ip','okuser')");  // or die (pg_error())
					
					// Comprueba que los datos del usuario(Nombre, apellidos, dni, cp, municipio y dirección) están relleno para que el usuario pueda seguir comprando
					// Los datos son usados para la facturación de la compra
					
					$data_nif = $data["nif"];
					$data_nombre = $data["nombre"];
					$data_apellidos = $data["apellidos"];
					$data_municipio = $data["municipio"];
					$data_direccion = $data["direccion"];
					$data_telefono = $data["telefono"];
					$data_cp = $data["cp"];
					
					
					if ((($idcurso<>"")||($idpublicacion<>""))&&(($data_nif=="")||($data_nombre=="")||($data_apellidos=="")||($data_municipio=="")||($data_direccion=="")||($data_cp==""))){
						
						$_POST['idcurso']="";
						$_POST['idpublicacion']="";
						
						$_POST['data_nif']=$data_nif;
						$_POST['data_nombre']=$data_nombre;
						$_POST['data_apellidos']=$data_apellidos;
						$_POST['data_municipio']=$data_municipio;
						$_POST['data_direccion']=$data_direccion;
						$_POST['data_cp']=$data_cp;
					
						if ($idcurso<>""){
							$_POST['idcurso']=$idcurso;
							$sql = "SELECT * FROM curso WHERE id='$idcurso' AND borrado=0";
							$result = posgre_query($sql);
	
							if ($row = pg_fetch_array($result)){
								$modalidad=$row["modalidad"];
							}
							/* Cogemos si el tipo de inscripción es preencial u online */
								$insc="";
								if ($modalidad==2){
									$_POST['inscripcion']=trim(strip_tags($_REQUEST['inscripcion']))+0;
								}
							/**/
						}
						elseif($idpublicacion<>""){
							$_POST['idpublicacion']=$idpublicacion;
							$_POST['formato']=cleanInput(strip_tags($_GET['formato']));
						}
						
						
						//header("Location: a_completardatos_facturacompra.php");
						//exit();
					
					}
					
					
					if ($idcurso<>""){
					
						$sql = "SELECT * FROM curso WHERE id='$idcurso' AND borrado=0";
						$result = posgre_query($sql);

						if ($row = pg_fetch_array($result)){
							$modalidad=$row["modalidad"];
						}
						/* Cogemos si el tipo de inscripción es preencial u online */
							$insc="";
							if ($modalidad==2){
								$inscripcion=trim(strip_tags($_REQUEST['inscripcion']))+0;
								$insc="&inscripcion=$inscripcion";
							}
						/**/
						header("Location: https://www.activatie.org/web/inscripcion2.php?est=ok&idcurso=$idcurso$insc");
						exit();
					}
					elseif($idpublicacion<>""){
						$formato=cleanInput(strip_tags($_GET['formato']));
						header("Location: https://www.activatie.org/web/a_publicacionpago_2.php?id=$idpublicacion&formato=$formato");
						exit();	
					}
					elseif($idencuesta<>""){
						$token=cleanInput(strip_tags($_GET['t']));
						header("Location: https://www.activatie.org/web/encuesta.php?id=$idencuesta&t=$token");
						exit();	
					}
					
					
					else{
						/* $ch = curl_init();
								curl_setopt($ch, CURLOPT_URL,"https://www.activatie.org/moodle/login/index.php");
								curl_setopt($ch, CURLOPT_POST, 1);
								curl_setopt($ch, CURLOPT_HEADER, 1);
								curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
								curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$usuario&password=$pass&rememberusername=1&Login");
								curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
								curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
								curl_setopt($ch, CURLOPT_AUTOREFERER, true );
								$server_output = curl_exec ($ch);
								$response = curl_getinfo( $ch );
								curl_close ($ch);
								print_r($response);
								preg_match_all('/^Set-Cookie:\s*([^\r\n]*)/mi', $server_output, $ms);
								// print_r($result);
								$cookies = array();
							   // var_dump($ms);
								 foreach ($ms[1] as $m) {
									list($name, $value) = explode('=', $m, 2);
									list($namevalue) = explode(';', $value, 1);
									setcookie($name, $namevalue, time()+3600*24, 'https://www.activatie.org/moodle', 'localhost');
								}
								//print_r($cookies);
								//print_r( $server_output);
								$_SESSION['logged_in']="yes";
								$_SESSION['uid']=$row['id'];
								*/
								
								
						//Metodo de cayetano
						$username=$usuario;
						$password=$pass;
						
						?>
						<br>
						<form action="https://www.activatie.org/moodle/login/index.php" method="post" id="loginform" >
						<input name="username" type="hidden" value="<?=$username?>">
						<input name="password" type="hidden" value="<?=$password?>">
						</form>
						<script type="text/javascript">
							document.getElementById('loginform').submit();
						</script>
						<?
		
		
						//header("Location: https://www.activatie.org/web/zona-privada_usuario_1.php");
						exit();
					}
				}else{
					$_SESSION[error]="Error en login";	
					header("Location: index.php?error=true&3");
					exit();
				}
				
				
			}else { //No es la contrasena correcta
				$link=conectar(); 
				$fecha2=a_fechas($fecha,1);
				//$sqll=pg_escape_string ("SELECT * FROM controla WHERE comentario<>'ok' AND (fechahora between '$fecha' AND '$fecha2') AND usuario='$usuario' AND borrado=0;");
				$sqll="SELECT * FROM controla WHERE comentario<>'ok' AND (fechahora between '$fecha' AND '$fecha2') AND usuario='$usuario' AND borrado=0;";
				$Query = pg_query($link,$sqll) ;//or die ("M1".pg_error());
				//if(pg_num_rows($Query)>3){ //Bloquea usuario
					//$Query = pg_query("UPDATE usuario SET confirmado=0 WHERE login='$usuario';" ,$link) or die ("M2".pg_error());
					//$Query = pg_query("INSERT INTO controla (usuario,fecha,hora,ip,comentario) VALUES (1,'$usuario','$fecha','$hora','$ip','BLOQUEADO')" ,$link) or die ("M3a".pg_error()); 
					//echo $error = "El usuario/password ingresado es incorrecto. Demasiados intentos has sido bloqueado 24 horas.";
					//header("Location: index.php?error=true&est=ccab#message");
					//exit();
				//}else{
					//NO BLOQUEAMOS USUARIO PERO INCREMENTAMOS EL NUMERO DE INTENTOS
					$intento=pg_num_rows($Query);
					$link=conectar(); 
					$Query = pg_query("INSERT INTO controla (usuario,fechahora,ip,comentario) VALUES ('$usuario','$fechahora','$ip','ko $intento')" ,$link) ;//or die ("M3b".pg_error()); 
					$_SESSION[esterror]="Contraseña incorrecta";	
					//echo $error = "El usuario/password ingresado es incorrecto";
					header("Location: index.php?est=ko");
					exit();
				//}
			}
	} else { // No existe el usuario
		$link=conectar(); 
		$sql="SELECT * FROM usuario WHERE login = '$usuario' AND borrado=1";
		$Query = pg_query($link,$sql ) ;
		
		if (pg_num_rows($Query)>0){
			$row = pg_fetch_array($Query);
			$baja = $row["baja"];
			
			if ($baja==1){
				$_SESSION[esterror]="Usuario dado de baja. Puede darse de alta completando la información personal <a href='web_alta.php'>aquí</a>. Si usted es colegiado, debe informar a su colegio para que le de los permisos pertinentes.";
			}	
		}
		else{
		
			$link=conectar(); 
			$sql="SELECT * FROM usuario WHERE login = '$usuario' AND borrado=0 AND confirmado=0";
			$Query = pg_query($link,$sql ) ;

			if (pg_num_rows($Query)>0){
				$row = pg_fetch_array($Query);
				
				$_SESSION["loginconfirmar"]=$usuario;
				$_SESSION[esterror]="Usuario no confirmado. Pulse <a href=\"z_email_confirmacion.php?accion=enviar\">aquí</a> para realizar la activación.";	
			}
			else{
				$_SESSION[esterror]="No existe el usuario";	
			}
		}
		
		header("Location: index.php");
		exit();
	}	
}else{ // No ha escrito usuario
	$_SESSION[esterror]="No existe el usuario";	
	header("Location: index.php?est=ko");
	exit();
}
$_SESSION[error]="Error al loguearse";	
header("Location: index.php?error=true&7");
exit();

?>