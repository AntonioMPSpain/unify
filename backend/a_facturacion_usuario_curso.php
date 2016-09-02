<?
//error_reporting(-1);
include("_funciones.php"); 
include("_cone.php"); 
include_once "a_facturas.php"; 
$safe="Cursos de Usuario";
$accion=strip_tags($_GET['accion']); 

$titulo1="formación ";
$titulo2="administración";
$idusuario=strip_tags($_REQUEST['idusuario']); 
if ($idusuario=="") { //Alumno
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql=" (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////



if (isset($_GET['proforma'])){
	
	$idcurso=strip_tags($_REQUEST['idcurso']); 
	if ($idcurso=="") { 
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}		
	
	$InvoiceNumber = generarFactura($idusuario, 1, $idcurso, 2, 0, 0, "", 0);
		
	header("Location: a_facturacion.php?id=$InvoiceNumber");
	exit();
}






include("plantillaweb01admin.php"); 

	$linka=iConectarse(); 
	$resulta=pg_query($linka,"SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0") ;//or die (pg_error());  
	$rowcurso = pg_fetch_array($resulta);

?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$rowcurso["nombre"]?> <?=$rowcurso["apellidos"]?></h2>
		<br />
		<? include("_aya_mensaje_session.php"); 
		$_SESSION[error]="";
		$orden=strip_tags($_GET['orden']);
		if($orden==""){
			$sqlorden="ASC";
			$orden="ASC";
		}elseif($orden=="DESC"){
			$orden="DESC";
			$sqlorden="DESC";
		}
		?>
		<!--fin buscador-->
<h2>Listado de cursos realizados por el Alumno</h2>
		<table class="align-center">
		<tr>
			<th></th>
			<th>NOMBRE</th>
			<th>PRECIO</th>	
			<th>ACCIÓN</th>	
		</tr>
	<?

$campo=strip_tags($_GET['campo']);
if ($campo=="nif"){
	$sqlcampo="nif";
}elseif ($campo=="nombre"){
	$sqlcampo="nombre";
}elseif ($campo=="apellidos"){
	$sqlcampo="apellidos";
}elseif ($campo=="idcolegio"){
	$sqlcampo="idcolegio";
}else{
	$sqlcampo="fecha";
}
$sqlcampo="fechahora";



$link=iConectarse();
$result=pg_query($link,"SELECT * FROM curso_usuario WHERE nivel<>'3' AND estado=0 AND idusuario='$idusuario' AND borrado=0 ORDER BY  $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
$total_registros = pg_num_rows($result); 
$cuantos = $total_registros;
//fin Paginacion 1
	$cplazas=$sumatotal=0;
	while(($row = pg_fetch_array($result))) { 
	
	
		$estado = $row["estado"];
		$espera = $row["espera"];
		$pagado = $row["pagado"];
		$devolucion = $row["devolucion"];
		$modalidad = $row["modalidad"];
		$inscripciononlinepresencial = $row["inscripciononlinepresencial"];
		$getcursodual="";
		
		if ($modalidad==0){
			$modalidadtexto="Online";
		}
		elseif ($modalidad==1){
			$modalidadtexto = "Presencial";
		}
		elseif ($modalidad==2){
			if ($inscripciononlinepresencial==1){
				$modalidadtexto = "Presencial";
			}
			else{
				$modalidadtexto="Online";
				$getcursodual="&cursodual";
			}
			
		}
		elseif ($modalidad==3){
			$modalidadtexto = "Permanente";
		}
		
		if ($estado==0){
			if ($espera==1){
				$estadotexto = "Espera";
			}
			else{
				$estadotexto = "Inscrito";
			}
		}
		elseif ($estado==1){
			$estadotexto = "Baja";
		}
		
		if ($pagado==-1){
			$pagadotexto="-";
		}
		elseif ($pagado==0){
			$pagadotexto = "No pagado";
		}
		elseif ($pagado==1){
			$pagadotexto = "Pagado";
			
			if ($devolucion==1){
				$pagadotexto.=" y devuelto";
			}
			 
		}
	
		$idcurso=$row["idcurso"];
		$linka=iConectarse(); 
		$resulta=pg_query($linka,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0") ;//or die (pg_error());  
		$rowa = pg_fetch_array($resulta);
		$total = pg_num_rows($resulta); 					

		if ($total>0){
			?>
			<tr>
				<td><?=++$cplazas?></td>
				<td align="left"><? if ($total>0){ echo $rowa["nombre"]; }else{ echo "(eliminado)"; }?></td>
				<td><?=$row["precio"];?></td>
				<td><a href="a_facturacion_usuario_curso.php?proforma&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario?>" class="btn btn-primary" type="button">generar proforma</a></td>
			</tr>
			<?
		}
	}?>
		</table>
    <p class="align-center">Total: <?=$total_registros?> cursos</p>

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