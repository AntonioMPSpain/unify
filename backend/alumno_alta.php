<?
session_start();
include("_funciones.php"); 
include("_email-plantilla-funcion.php"); 
include("_cone.php"); 
//$c_email="info@tuedificioenforma.es";
//$c_web="tuedificioenforma.es";  
$estaccion="";
$safe="alta";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);

$alumno["acepto"]=$acepto=strip_tags($_POST['acepto']);
$alumno["tipo"]=$tipo=strip_tags($_POST['tipo']); if ($tipo=="") $tipo="0";
$nif=strip_tags($_POST['nif']); if ($nif=="") $nif="";
$alumno["nif"]=$nif=strtoupper($nif);
if ($_SESSION[nivel]==1) { //Admin Total 
	$nivel=strip_tags($_REQUEST['nivel']); //Puede venir del formulario usuarioalta.php o de nuevo profe de zona-privada_admin_profesores_1.php
	$idcolegio=strip_tags($_POST['idcolegio']);
	$alumno["idcolegio"]=$idcolegio=strip_tags($_POST['idcolegio']);
	$sqladmin=" idcolegio='$idcolegio', ";
}elseif ($_SESSION[nivel]==2) { //Admin cole
	$nivel=strip_tags($_REQUEST['nivel']); //Puede venir del formulario usuarioalta.php o de nuevo profe de zona-privada_admin_profesores_1.php
	$idcolegio=strip_tags($_POST['idcolegio']);
	$alumno["idcolegio"]=$idcolegio=strip_tags($_POST['idcolegio']);
	$sqladmin=" idcolegio='$idcolegio', ";
	if (($nivel==3)||($nivel==4)){
		$alumno["nivel"]=$nivel=strip_tags($_POST['nivel']);
	}else{
		$nivel=4; //alumno
	}
}else{
	$nivel=4; //alumno
	$idcolegio=$_SESSION[idcolegio];
}



$nuevo=$_GET['nuevo'];
//$confirmado=1;
$alumno["confirmado"]=$confirmado=strip_tags($_POST['confirmado']);
$pass=strip_tags($_POST['pass']);
$pass2=strip_tags($_POST['pass2']);
$alumno["nombre"]=$nombre=strip_tags($_POST['nombre']);
$alumno["apellidos"]=$apellidos=strip_tags($_POST['apellidos']);
$alumno["telefono"]=$telefono=strip_tags($_POST['telefono']);
$alumno["telefono2"]=$telefono2=strip_tags($_POST['telefono2']);
$alumno["direccion"]=$direccion=strip_tags($_POST['direccion']);
$alumno["pais"]=$pais=strip_tags($_POST['pais']);
$alumno["idprovincia"]=$idprovincia=strip_tags($_POST['idprovincia']);
$alumno["municipio"]=$municipio=strip_tags($_POST['municipio']);
$alumno["cp"]=$cp=strip_tags($_POST['cp']);
$alumno["email"]=$email=strip_tags($_POST['email']);
$alumno["ncolegiado"]=$ncolegiado=strip_tags($_POST['ncolegiado']);

$alumno["titulacion"]=$titulacion=cleanInput(strip_tags($_POST['titulacion']));
$alumno["universidad"]=$universidad=cleanInput(strip_tags($_POST['universidad']));
$alumno["procedencia"]=$procedencia=cleanInput(strip_tags($_POST['procedencia']));
$alumno["tipo"]=$tipo=cleanInput(strip_tags($_POST['tipo']));
$alumno["tipodeusuario"]=$tipodeusuario=cleanInput(strip_tags($_POST['tipodeusuario']));
$alumno["experiencia"]=$curriculum=strip_tags($_POST['curriculum']);

if ($idcolegio==0){
	$alumno["tipo"]=$tipo=4;
}
else{
	$alumno["tipo"]=$tipo=1;
}

if (isset($_POST['baja'])){

	$idusuario=strip_tags($_REQUEST['id']);
	$email = $email."_borrado";
	$sql = "UPDATE usuario SET borrado=1, baja=1, fechabaja=NOW(), pass='baja', imagen='',confirmado=0, email='$email', idcolegio=0, tipo=4, ncolegiado=0, universidad='', procedencia='', titulacion='', experiencia='' WHERE id='$idusuario'";
	posgre_query($sql);

	header("Location: zona-privada_admin_usuario.php");
	exit();
}

//Por si viene desde prescripcion
$alumno["idcurso"]=$idcurso=strip_tags($_GET['idcurso']);

/*<strong><?=$tipodeusuario?> | <?=$procedencia?> | <?=$universidad?></strong>*/

if($accion=="guardar"){
	$_SESSION[error]="Error";
	$accion="";
	$est="ok";
	if (!$acepto){
		$_SESSION[error]="* Debes aceptar las condiciones del servicio";
		$est="ko";
	}
	//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok,	
	if (valida_nif_cif_nie($nif)<1) { //si el nif_cif_nie es incorrecto
		$_SESSION[error]="* $nif nif/cif/nie incorrecto";
		$est="ko";
	}
	if (comprobar_email($email)==0) { //si el emial es incorrecto
		$_SESSION[error]="* E-mail incorrecto";
		$est="ko";
	}
	
	if ($nombre==""){
		$_SESSION[error]="* Nombre obligatorio";
		$est="ko";
	}
	
	if ($apellidos==""){
		$_SESSION[error]="* Apellidos obligatorio";
		$est="ko";
	}
	/*
	if (($pass=="")){ //Datos incorrectos
		$_SESSION[error]=" * no pueden estar en blanco las contraseñas";
		$est="ko";
		$est2="ko6";
	}
	if ($pass<>$pass2){
		$_SESSION[error]="* las contrase&ntilde;as no coinciden";
		$est="ko";
		$est2="ko3";
	}
	*/
	if (($email=="")){ //Datos incorrectos
		$_SESSION[error]=" * no puede estar en blanco el email";
		$est="ko";
		$est2="ko4";
	}
	$link=iConectarse(); 
	$Query = pg_query($link,"SELECT * FROM usuario WHERE nif = '$nif' ORDER BY id;" ); 
	//$Query = pg_query($link,"SELECT * FROM usuario WHERE nif = '$nif' AND confirmado=1 AND borrado=0 ORDER BY id;" ); 
	if((pg_num_rows($Query) != 0)) { //el usuario login existe
		$row = pg_fetch_array($Query);
		$idcolegioencontrado=$row["idcolegio"];
		if ($idcolegioencontrado>0){
			$link2=iConectarse(); 
			$Query2 = pg_query($link2,"SELECT * FROM usuario WHERE id= '$idcolegioencontrado';" ); 
			if((pg_num_rows($Query) != 0)) { //el usuario login existe
				$row2 = pg_fetch_array($Query2);
				$texencontrado=". Pertenece a ".$row2["nombre"];
			}
		}else{
				$texencontrado=". No pertenece a ningún colegio.";
		}
		$_SESSION[error]=" * el nif (login) ya existe en otro usuario".$texencontrado;
		$est="ko";
	}
	//$Query = pg_query($link,"SELECT * FROM usuario WHERE email = '$email' AND confirmado=1 AND borrado=0 ORDER BY id;" ); 
	$Query = pg_query($link,"SELECT * FROM usuario WHERE email = '$email' AND borrado=0 ORDER BY id;" ); 
	if((pg_num_rows($Query) != 0)) { //el usuario login existe
		$row = pg_fetch_array($Query);
		$idcolegioencontrado=$row["idcolegio"];
		if ($idcolegioencontrado>0){
			$link2=iConectarse(); 
			$Query2 = pg_query($link2,"SELECT * FROM usuario WHERE id= '$idcolegioencontrado';" ); 
			if((pg_num_rows($Query) != 0)) { //el usuario login existe
				$row2 = pg_fetch_array($Query2);
				$texencontrado=". Pertenece a ".$row2["nombre"];
			}
		}else{
				$texencontrado=". No pertenece a ningún colegio.";
		}
		$est_texto=" * el email ya existe en otro usuario";
		$est="ko";
		$_SESSION[error]=$est_texto;
	}
	$_SESSION[alumnoalta]=$alumno;	
	if ($est=="ok"){
		$login=solonumeros($nif);
		//$ssql="INSERT INTO usuario (tipo,idcolegio,login, nivel, confirmado, pass, nombre,apellidos, nif, telefono, telefono2, direccion, idprovincia, municipio, cp, email,  ncolegiado) VALUES ('$tipo','$idcolegio','$login', '$nivel', '$confirmado','$pass', '$nombre','$apellidos', '$nif', '$telefono', '$telefono2', '$direccion', '$idprovincia', '$municipio', '$cp', '$email', '$ncolegiado');";
		$ssql="INSERT INTO usuario (tipo,idcolegio,login, nivel, confirmado, nombre,apellidos, nif, telefono, telefono2, direccion, idprovincia, municipio, cp, email,  ncolegiado, experiencia, pais) VALUES ('$tipo','$idcolegio','$login', '$nivel', '$confirmado', '$nombre','$apellidos', '$nif', '$telefono', '$telefono2', '$direccion', '$idprovincia', '$municipio', '$cp', '$email', '$ncolegiado', '$curriculum', '$pais');";
		$link=iConectarse(); 
		$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
		if ($Query){
			$_SESSION[error]="Se ha insertado correctamente.";
			$est="ok";
			exec("php /var/www/moodle/admin/cli/cron.php > /var/www/web/tmp/notas.txt");
			//exec("php /var/www/moodle/auth/db/cli/sync_users.php > /var/www/web/tmp/notas2.txt");
			exec("php /var/www/moodle/auth/db/cli/sync_user_actv.php $login > /var/www/web/tmp/notas2.txt");


		}

		
	}
	if ($est=="ok"){
		if ($nivel == 3){
			header("Location: zona-privada_admin_profesores_1.php?est=$est");
		}
		else{
			header("Location: zona-privada_admin_usuario.php?est=$est");
		}
	}
	else{
		
		header("Location: alumno_alta.php?est=$est");
	}
	exit();
}//Fin de accion==guardar

if($accion=="guardarm"){
	$accion="";
	$_SESSION[error]="No se ha guardado correctamente";
	$est="ok";
	$id=strip_tags($_REQUEST['id']);
	//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok,	
	/*if (valida_nif_cif_nie($nif)<1) { //si el nif_cif_nie es incorrecto
		$_SESSION[error]="* nif/cif/nie incorrecto";
		$est="ko";
	}*/
	if (comprobar_email($email)==0) { //si el emial es incorrecto
		$_SESSION[error]="* E-mail incorrecto";
		$est="ko";
	}
	
	if ($nombre==""){
		$_SESSION[error]="* Nombre obligatorio";
		$est="ko";
	}
	
	if ($apellidos==""){
		$_SESSION[error]="* Apellidos obligatorio";
		$est="ko";
	}
	$nifnumeros = solonumeros($nif);
	$link=iConectarse(); 
	//$Query = pg_query($link,"SELECT * FROM usuario WHERE nif = '$nif' AND confirmado=1 AND borrado=0 AND id<>'$id' ORDER BY id;" ); 
	$Query = pg_query($link,"SELECT * FROM usuario WHERE (nif = '$nif' OR login='$nifnumeros')  AND borrado=0 AND id<>'$id' ORDER BY id;" ); 
	if((pg_num_rows($Query) != 0)) { //el usuario login existe
		$confirmadotmp=$row["confirmado"];
		$est="ko";
		$row = pg_fetch_array($Query);
		$idcolegioencontrado=$row["idcolegio"];
		if ($idcolegioencontrado>0){
			$link2=iConectarse(); 
			$Query2 = pg_query($link2,"SELECT * FROM usuario WHERE id= '$idcolegioencontrado';" ); 
			if((pg_num_rows($Query) != 0)) { //el usuario login existe
				$row2 = pg_fetch_array($Query2);
				$texencontrado=". Pertenece a ".$row2["nombre"];
			}
		}else{
				$texencontrado=". No pertenece a ningún colegio.";
		}
		$_SESSION[error]=" * el nif o login ya existe en otro usuario".$texencontrado;
	}
	//$Query = pg_query($link,"SELECT * FROM usuario WHERE email = '$email' AND confirmado=1 AND borrado=0 AND id<>'$id' ORDER BY id;" ); 
	$Query = pg_query($link,"SELECT * FROM usuario WHERE email = '$email' AND borrado=0 AND id<>'$id' ORDER BY id;" ); 
	if((pg_num_rows($Query) != 0)) { //el usuario login existe
		$est="ko";
		$row = pg_fetch_array($Query);
		$idcolegioencontrado=$row["idcolegio"];
		if ($idcolegioencontrado>0){
			$link2=iConectarse(); 
			$Query2 = pg_query($link2,"SELECT * FROM usuario WHERE id= '$idcolegioencontrado';" ); 
			if((pg_num_rows($Query) != 0)) { //el usuario login existe
				$row2 = pg_fetch_array($Query2);
				$texencontrado=". Pertenece a ".$row2["nombre"];
			}
		}else{
				$texencontrado=". No pertenece a ningún colegio.";
		}
		$_SESSION[error]=" * el email ya existe en otro usuario".$texencontrado;
	}
	if (($est=="ok")&&($id<>"")){
		$login=solonumeros($nif);
		
		if ($pass=="") {//Datos incorrectos

		
			$ssql="UPDATE usuario SET  $sqladmin nivel='$nivel',tipo='$tipo', login = '$login', confirmado = '$confirmado', nombre = '$nombre', apellidos='$apellidos', nif = '$nif', telefono = '$telefono',telefono2 = '$telefono2', direccion = '$direccion', idprovincia = '$idprovincia', municipio = '$municipio', cp = '$cp', email = '$email', ncolegiado = '$ncolegiado', experiencia='$curriculum', pais='$pais'   WHERE id ='$id';";	

		}else{
			if ($pass<>$pass2){
				$_SESSION[error]="* las contrase&ntilde;as no coinciden";
				$est="ko";
			}else{			
				$est="ok";
				//$ssql="UPDATE usuario SET $sqladmin  nivel='$nivel',pass='$pass',tipo='$tipo', login = '$login', confirmado = '$confirmado', nombre = '$nombre',apellidos='$apellidos',  nif = '$nif', telefono = '$telefono',telefono2 = '$telefono2', direccion = '$direccion', idprovincia = '$idprovincia', municipio = '$municipio', cp = '$cp', email = '$email', ncolegiado = '$ncolegiado'  WHERE id ='$id';";	
			$ssql="UPDATE usuario SET  $sqladmin nivel='$nivel',pass='$pass',tipo='$tipo', login = '$login', confirmado = '$confirmado', nombre = '$nombre', apellidos='$apellidos', nif = '$nif', telefono = '$telefono',telefono2 = '$telefono2', direccion = '$direccion', idprovincia = '$idprovincia', municipio = '$municipio', cp = '$cp', email = '$email', ncolegiado = '$ncolegiado', experiencia='$curriculum'   WHERE id ='$id';";		
			}	
		}
		$link=iConectarse(); 
		$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
		if ($Query){
			$_SESSION[error]="Se ha editado correctamente.";
			$est_texto2="Guardado";
			
			exec("php /var/www/moodle/admin/cli/cron.php > /var/www/web/tmp/notas.txt");
			//exec("php /var/www/moodle/auth/db/cli/sync_users.php > /var/www/web/tmp/notas2.txt");
			exec("php /var/www/moodle/auth/db/cli/sync_user_actv.php $login > /var/www/web/tmp/notas2.txt");
			

		}else{
			//echo $ssql;
			//echo pg_last_error();
			//exit();
		
			$est="ko";
		}
	}else{
		//$_SESSION[curso]=$curso;	
		$est="ko";
		header("Location: alumno_alta.php?accion=editar&id=$id&est=$est");
		exit();
	}
	//Para la cache del navegador al volver atras
	//meter en sesion los datos POST
	if ($nivel == 3){
		header("Location: zona-privada_admin_profesores_1.php?est=$est");
	}
	else{
		header("Location: zona-privada_admin_usuario.php?est=$est");
	}
	exit();
	
}//Fin de accion==guardarm

if($accion==""){
	$titulo1="alta";
}elseif($accion=="editar"){
	$titulo1="editar";
}
$titulo2="alumnos";
$migas[] = array('zona-privada_admin_usuario.php', 'Gestión de Alumnos');
include("plantillaweb01admin.php"); 
?>
<!--Arriba -->
<div class="grid-8 contenido-principal">
<div class="clearfix"></div>
<div class="pagina blog">
		<? include("_aya_mensaje_session.php"); ?>
		<br />
		<? if ($accion=="activar"){ 
				if ($estaccion==0){ 
					?>No se ha podido activar<?					
				}else{
					?>Su activaci&oacute;n ha sido procesada correctamente.<?					
				}
		   }elseif(($accion=="")){ ?>
				<!--fin acciones-->
				<h2>Alta de Usuario</h2>
				<?
					$row=$_SESSION[alumnoalta];	
					$tipo=$row["tipo"];
					$nivel=$row["nivel"]; //
					$confirmado=$row["confirmado"];
					//$pass=$row["pass"];
					$nombre=$row["nombre"];
					$apellidos=$row["apellidos"];
					$nif=$row["nif"];
					$nif=strtoupper($nif);
					$telefono=$row["telefono"];
					$telefono2=$row["telefono2"];
					$direccion=$row["direccion"];
					$pais=$row["pais"];
					$idprovincia=$row["idprovincia"];
					$idcolegio=$row["idcolegio"];
					$municipio=$row["municipio"];
					$cp=$row["cp"];
					$email=$row["email"];
					$ncolegiado=$row["ncolegiado"];
					$idcurso=$row["idcurso"];//
					
					$titulacion=$row["titulacion"];//
					$procedencia=$row["procedencia"];//
					$universidad=$row["universidad"];//
					$tipodeusuario=$row["tipodeusuario"];//
					$curriculum=$row["experiencia"];//

					$actionform="&";
					$legendform="Datos";
					require("alumno_alta-form.php");
					$_SESSION[alumnoalta]="";
				?>
		<? }elseif($accion=="editar"){ ?>
			<h2>Editar Usuario</h2>
				<?
				$id=strip_tags($_REQUEST['id']);
				if ($id<>"") {
					$link=iConectarse(); 
					$result=pg_query($link,"SELECT * FROM usuario WHERE borrado=0 AND id='$id' ORDER BY id DESC LIMIT 1;"); 
					$row = pg_fetch_array($result);						
					//$i=$row["id"];
					$tipo=$row["tipo"];
					$nivel=$row["nivel"]; //
					$confirmado=$row["confirmado"];
					//$pass=$row["pass"];
					$nombre=$row["nombre"];
					$apellidos=$row["apellidos"];
					$nif=$row["nif"];
					$nif=strtoupper($nif);
					$telefono=$row["telefono"];
					$telefono2=$row["telefono2"];
					$direccion=$row["direccion"];
					$pais=$row["pais"];
					$idprovincia=$row["idprovincia"];
					$idcolegio=$row["idcolegio"];
					$municipio=$row["municipio"];
					$cp=$row["cp"];
					$email=$row["email"];
					$ncolegiado=$row["ncolegiado"];
					$idcurso=$row["idcurso"];//

					$titulacion=$row["titulacion"];//
					$procedencia=$row["procedencia"];//
					$universidad=$row["universidad"];//
					$tipodeusuario=$row["tipodeusuario"];//
					$curriculum=$row["experiencia"];//
				}else{
					echo"Parametros incorrectos";
					exit();							
				}		
				$actionform="m&id=".$id; //Para el accion==guardarm --> Update
				$legendform="Datos";
				require("alumno_alta-form.php");
				?>
		<? }?>
</div>
<!--fin pagina blog-->
<div class="clearfix"></div>
</div>
<!--fin grid-8 contenido-principal-->
<!--Abajo-->
	<?
include("plantillaweb02admin.php"); 
?>
