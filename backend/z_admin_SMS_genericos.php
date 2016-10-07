<?

include("_funciones.php"); 
include("_cone.php"); 

$accion="";
$accion=strip_tags($_GET['accion']); 

///////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
	$idcolegio=strip_tags($_SESSION[idcolegio]);
	$sqlmas=" AND idusuario='$idcolegio' ";
	//echo "Error: aqui no deberia entrar.";
	//exit();
}elseif ($_SESSION[nivel]==1) { //Admin Total
	//echo "ok: aqui deberia entrar."; 
	//exit();
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

if($accion=="guardar"){
	$titul = strip_tags($_POST['titul']);
	$textSMS = strip_tags($_POST['textSMS']);
	if (isset($_GET['idsms'])){
		$idsms = $_GET['idsms'];
		$sql = "UPDATE sms SET nombre='$titul', texto='$textSMS' WHERE id='$idsms'";
	}
	else{
		$sql = "INSERT INTO sms (nombre, texto,paracurso) VALUES ('$titul', '$textSMS',1)";
	}

	posgre_query($sql);
	
	header("Location: z_admin_SMS_genericos.php");
	exit();
}
elseif($accion=="eliminar"){
	if (isset($_GET['idsms'])){
		$idsms = $_GET['idsms'];
		$sql = "UPDATE sms SET borrado=1 WHERE id='$idsms'";
		posgre_query($sql);
		
		header("Location: z_admin_SMS_genericos.php");
		exit();
	}
}

$titulo1="textos ";
$titulo2="SMS";

include("plantillaweb01admin.php"); 
?>

<!--Arriba plantilla1-->
<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	
	<? if ($accion==""){?>
	
			<h2 class="titulonoticia">Textos gen&eacute;ricos de SMS para cursos</h2>
			<br />
						
			<!--Acciones-->
			<div class="acciones">		
			<p>
				<a href="z_admin_SMS_genericos.php?accion=editar" class="btn btn-success" type="button">Nuevo SMS <i class="icon-plus"></i></a> |
			</p>
			</div>
			<!--fin acciones-->
			
			<? include("_aya_mensaje.php"); ?>
			<table class="align-center">
			<tr>
				<th>T&Iacute;TULO</th>
				<th>TEXTO</th>
				<th>ACCI&Oacute;N</th>	
			</tr>
					
			<? 
			$result=posgre_query("SELECT * FROM sms WHERE paracurso=1 AND borrado=0 ORDER BY id") ;//or die (pg_error());  
			
			while($row = pg_fetch_array($result)) { 
				?>
				<tr>
					<td align="left"><?
						echo $row['nombre'];
						?>
					</td>
					<td align="left"><?
						echo $row['texto'];
						?>
					</td>
					<td>
						<a href="z_admin_SMS_genericos.php?accion=editar&idsms=<?=$row["id"];?>" class="btn btn-primary">editar</a>
						<a onclick="return confirmar('&iquest;Eliminar SMS?')" href="z_admin_SMS_genericos.php?accion=eliminar&idsms=<?=$row["id"];?>" class="btn btn-primary">eliminar</a>
					</td>
				</tr>
				<?
			}?>
			
			</table>
			
			
			<div id="volverarriba">
				<hr />
				<a href="#" title="Volver al inicio de la p�gina">Volver arriba <i class="icon-circle-arrow-up"></i></a>
			</div>
			<br />
		
		
	<? }
	
		
		else{
			if (isset($_GET['idsms'])){
				$idsms = $_GET['idsms'];
				$getidsms="&idsms=$idsms";
				$result=posgre_query("SELECT * FROM sms WHERE id='$idsms' AND borrado=0") ;//or die (pg_error());
				if ($row = pg_fetch_array($result)){
					$titulo = $row['nombre'];
					$textoSMS= $row['texto'];
				}  
					
			}
			else{
				$titulo="";
				$textoSMS="";
			}
		
			?>
			<form action="z_admin_SMS_genericos.php?accion=guardar<?=$getidsms?>" method="post">	
				<label>T&iacute;tulo: </label>
				<input class="input-xlarge" type="text" name="titul" id="titul" value="<?=$titulo?>">
				
				<label>Texto SMS: </label>
				<input class="input-xxlarge" type="text" name="textSMS" id="textSMS" value="<?=$textoSMS?>">
			
				<div class="form-actions">
					<button type="submit" class="btn btn-primary btn-large">Guardar</button>
				</div>
			
			</form>
			<?
		}
	?>

	</div>
	<!--fin pagina-->
	<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->
<?
include("plantillaweb02admin.php"); 
?>


