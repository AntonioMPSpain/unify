<?
include("_funciones.php"); 
include("_cone.php"); 
$safe="Gestión de textos de correos para cursos";
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
$est_texto=strip_tags($_GET['est_texto']);
$est_texto2=strip_tags($_GET['est_texto2']);

$titulo1="formación ";
$titulo2="administración";
////////// Filtros de nivel por usuario //////////////////////
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

if ($accion=="eliminar"){

	$id=strip_tags($_GET['id']); 
	if ($id<>""){
		$sql = "UPDATE email SET borrado=1 WHERE id='$id'";
		posgre_query($sql);
	}

}


////////// FIN Filtros de nivel por usuario //////////////////////
$migas = array();
//$migas[] = array('zona-privada_admin_resumen.php', 'Gestión de cuentas');
include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
	<br />
	<? include("_aya_mensaje.php"); ?>
	<table class="align-center">
	<tr>
		<th>CORREO</th>
		<th>ACCIÓN</th>	
	</tr>
	<tr>
		<td align="left" bgcolor="#FFEDEA"><strong>Emails genérico para enviar manualmente:</strong></td>
		<td bgcolor="#FFEDEA">
			<a href="generica_correo2.php" class="btn btn-success">nuevo</a>
		</td>
	</tr>
		<? 
	$link=iConectarse();
	$result=pg_query($link,"SELECT * FROM email WHERE paracurso=1 AND borrado=0") ;//or die (pg_error());  

	while($row = pg_fetch_array($result)) { 
		?>
		<tr>
			<td align="left"><?
				echo $row['asunto'];
				?></td>
			<td>
				<a href="generica_correo2.php?id=<?=$row["id"];?>" class="btn btn-primary">editar</a>
				<a onclick="return confirm('&iquest;Desea eliminar la plantilla?')" href="generica_correo0.php?accion=eliminar&id=<?=$row["id"];?>" class="btn btn-primary">eliminar</a>
			</td>
		</tr>
		<?
	}?>	

	
	
	
	
			
	
		</table>


		<div id="volverarriba">
			<hr />
			<a href="#" title="Volver al inicio de la página">Volver arriba <i class="icon-circle-arrow-up"></i></a>
		</div>
		<br />
	</div>
	<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->
<?
include("plantillaweb02admin.php"); 
?>