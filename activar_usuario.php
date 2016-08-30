<?
session_start();
include("_funciones.php"); 
include("_cone.php"); 

$nif2 = $_REQUEST["n"];
$nif = $_SESSION["nifrecuperar"];
$_SESSION["nifrecuperar"]="";

if ($nif<>""){

}
else if($nif2<>""){
	$nif=$nif2;
}
else{
	exit();
}


$result = posgre_query("SELECT * FROM usuario WHERE login = '$nif' AND borrado=0 ORDER BY id;" );
$usuario = pg_fetch_array($result);

$idcolegio = $usuario["idcolegio"];
$email = $usuario["email"];
$pass = $usuario["pass"];

$result = posgre_query("SELECT * FROM usuario WHERE id = '$idcolegio' ORDER BY id;" );
$colegio = pg_fetch_array($result);

$nombrecolegio = $colegio["nombre"];
$emailcolegio = $colegio["email"];

if ($nombrecolegio==""){
	$textocolegio = "Ha sido registrado previamente en la plataforma.";
}
else{
	$textocolegio = "Su colegio($nombrecolegio) lo registró anteriormente como colegiado.";
}


$titulo2="alta";
include("plantillaweb01.php"); 
?>
<!--Arriba pantilla1-->
<div class="grid-8 contenido-principal">
<div class="clearfix"></div>
<div class="pagina blog">
		<h2 class="titulonoticia">Activar usuario</h2>
		<!--fin acciones-->
		<? if ($pass<>"") {?>
		
			<br>
			<h4>¡Cuenta activada!</h4>
			<br><p> Parece que su cuenta ya se encuentra activada. Puede acceder en "Acceso a usuarios" o si no recuerda su contraseña, puede cambiarla pulsando <a href="usuarioolvidapass.php?accion=envia&email=<?=$email?>">aquí</a>.</p>
			<p> Si tiene algún problema pongase en contacto en la sección <a href="contacto.php">Contacto</a>.</p>.
		
		
		<? } 
		else{ ?>
			<br>
			<h4>¡Bienvenido a activatie!</h4>
			<br><p> <?=$textocolegio?> Si desea activar la cuenta solicite un cambio de contraseña en su email <strong><?=$email?></strong> pulsando <a href="usuarioolvidapass.php?accion=envia&email=<?=$email?>">aquí</a>.</p>
			<p> Si algún dato es incorrecto o desea darse de baja, puede realizarlo posteriormente en la zona personal "Mis Datos".</p>.
		<? } ?>
</div>
<!--fin pagina blog-->
<div class="clearfix"></div>
</div>
<!--fin grid-8 contenido-principal-->
<!--Abajo-->
	<?
include("plantillaweb02.php"); 
?>