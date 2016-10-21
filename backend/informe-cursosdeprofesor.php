<?
//error_reporting(-1);
include("_funciones.php"); 
include("_cone.php"); 
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
<h2>Listado de cursos realizados por el Profesor</h2>
		<table class="align-center">
		<tr>
			<th>N</th>
			<th>NOMBRE</th>
			<th>PDF</th>	
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
$result=pg_query($link,"SELECT * FROM curso_docente_web WHERE idusuario='$idusuario' AND borrado=0 ") ;//or die (pg_error());  


$total_registros = pg_num_rows($result); 
$cuantos = $total_registros;
//fin Paginacion 1
	$cplazas=$sumatotal=0;
	while(($row = pg_fetch_array($result))) { 
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
				<td>
					<!--<a class="btn btn-primary" href="diploma_profe.php?idusuario=<?=$idusuario?>&idcurso=<?=$idcurso?>" title="pdf">Diploma</a>-->
					<a class="btn btn-primary" href="informe-cursosdeusuario_pdf.php?idcurso=<?=$idcurso?>&profe&idusuario=<?=$idusuario?>" title="pdf">Certificado curso impartido</a>
 
				</td>
			</tr>
			<?
		}
	}?>
		</table>
			<a class="btn btn-primary" href="informe-cursosdeusuario_pdf.php?profe&idusuario=<?=$idusuario?>" title="pdf">Certificado cursos impartidos</a>
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