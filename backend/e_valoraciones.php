<?
include("_funciones.php"); 
include("_cone.php");

////////// Filtros de nivel por usuario //////////////////////
session_start();

if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$idusuario = $idcolegio;
	}else{
		$_SESSION[esterror]="Par�metros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
}
else{
	$_SESSION[esterror]="Par�metros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////  

$id=strip_tags(trim($_REQUEST['id'])); //idencuesta

if ($id==""){
	echo "Error de ID1";
	exit();
}

$sql = "SELECT * FROM usuario WHERE borrado=0 AND id='$id';";
$result=posgre_query($sql);
$row = pg_fetch_array($result); 
$nombre = $row['nombre'];
$apellidos = $row['apellidos'];

$titulo1="encuestas";
$titulo2="activatie";
include("plantillaweb01admin.php");
?>		

<div class="grid-12 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">	

	<h2>Valoraciones obtenidas en encuestas</h2>
	<h3><?=$nombre?> <?=$apellidos?></h3>
	<?
	
	$sql = "SELECT id FROM encuestas_opciones WHERE borrado=0 AND idprofesor='$id' AND idpregunta IN (SELECT id FROM encuestas_preguntas WHERE borrado=0 AND idencuesta IN (SELECT id FROM encuestas WHERE borrado=0) GROUP BY idencuesta,id)";
	$result = posgre_query($sql);
	echo pg_last_error();
	$numencuestas = pg_num_rows($result);
	
	$sql = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE idprofesor='$id' AND idpregunta IN (SELECT id FROM encuestas_preguntas WHERE borrado=0 AND idencuesta IN (SELECT id FROM encuestas WHERE borrado=0 ))) GROUP BY idusuario";
	$result = posgre_query($sql);
	$numusuarios = pg_num_rows($result);
	
	$sql = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE idprofesor='$id' AND idpregunta IN (SELECT id FROM encuestas_preguntas WHERE borrado=0 AND idencuesta IN (SELECT id FROM encuestas WHERE borrado=0 ))) ";
	$result = posgre_query($sql);
	$numrespuestas = pg_num_rows($result);
	
	?>
	<p>Encuestas en las que se ha valorado: <b><?=$numencuestas?> encuestas</b><br>
	Usuarios que han valorado: <b><?=$numusuarios?> usuarios</b><br>
	Valoraciones recibidas: <b><?=$numrespuestas?> usuarios</b></p>
	
	<div class="alert">
		Escala de valoraci&oacute;n:<br />
		1 - Muy deficiente<br />
		2 - Deficiente<br />
		3 - Regular<br />
		4 - Bueno<br />
		5 - Excelente<br />
	</div>
	
	<table class="align-center" border="0" cellpadding="0" cellspacing="0">
	<tbody>
	<tr>
		<th></th>
		<th style="width:70px;" >1</th>
		<th style="width:70px;">2</th>
		<th style="width:70px;">3</th>
		<th style="width:70px;">4</th>
		<th style="width:70px;">5</th>
		<th> Valoraci&oacute;n </th>
	</tr>
	
	<? 
	
	$sql = "SELECT e.id,e.nombre, e.idcurso FROM encuestas e, encuestas_preguntas ep, encuestas_opciones eo WHERE e.borrado=0 AND e.id=ep.idencuesta AND ep.id=eo.idpregunta AND eo.borrado=0 AND ep.borrado=0 AND eo.idprofesor='$id' GROUP BY e.idcurso, e.nombre, e.id";
	$result = posgre_query($sql);
	echo pg_last_error();
	while ($row = pg_fetch_array($result)){
	
		$idencuesta = $row['id'];
		$idcurso = $row['idcurso'];
		$nombre = $row['nombre'];
		if ($idcurso<>""){
			$sql = "SELECT * FROM curso WHERE id='$idcurso'";
			$result2 = posgre_query($sql);
			$row = pg_fetch_array($result2);
			$nombrecurso = $row['nombre'];
			$id_categoria_moodle = $row["id_categoria_moodle"];
		}
		
		$sql = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE idprofesor='$id' AND idpregunta IN (SELECT id FROM encuestas_preguntas WHERE borrado=0 AND idencuesta IN (SELECT id FROM encuestas WHERE borrado=0 AND id='$idencuesta')))";
		$result3 = posgre_query($sql);
		$numrespuestascurso = pg_num_rows($result3);
		
		?>
		<tr>
		<td><!--<b>(<?=$numrespuestascurso?>)</b><br>--><?=$nombre?><br><b><?=$nombrecurso?></b><br><?=$idcurso?>/<?=$id_categoria_moodle?></td>
		<?
		$totalnota=0;
		for ($i=1;$i<=5;$i++){
			$sqlx = "SELECT * FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM  encuestas_opciones WHERE borrado=0 AND idprofesor='$id' AND idpregunta IN (SELECT id FROM encuestas_preguntas WHERE borrado=0 AND idencuesta IN (SELECT id FROM encuestas WHERE borrado=0 AND id='$idencuesta'))) AND idopcioncolumna IN (SELECT id FROM encuestas_opciones WHERE borrado=0 AND fila='$i')";
			$resultx = posgre_query($sqlx);
			$numopciones=0;
			$numopciones = pg_num_rows($resultx);
			$porcentaje = ($numopciones/$numrespuestascurso)*100;
			$porcentaje = number_format($porcentaje, 2, '.', '');
			$totalnota = $totalnota + ($numopciones*$i);
			?>
			<td><b><?=$numopciones?></b> (<?=$porcentaje?>%)</td>
		<? 
		} 
		
		$totalnota = number_format(($totalnota/$numrespuestascurso)*2*10, 2, '.', '');
		?>	
		<td><b><?=$totalnota?>%</b></td>
		</tr>
	<? } ?>
	<tr>
		<td><b>Valoraci&oacute;n media</b></td>
		<?
		$sum = 0;
		for ($i=1;$i<=5;$i++){
			$sqlx = "SELECT * FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM  encuestas_opciones WHERE borrado=0 AND idprofesor='$id') AND idopcioncolumna IN (SELECT id FROM encuestas_opciones WHERE borrado=0 AND fila='$i') AND idopcion IN (SELECT id FROM  encuestas_opciones WHERE borrado=0 AND idprofesor='$id' AND idpregunta IN (SELECT id FROM encuestas_preguntas WHERE borrado=0 AND idencuesta IN (SELECT id FROM encuestas WHERE borrado=0)))";
			$resultx = posgre_query($sqlx);
			$numopciones=0;
			$numopciones = pg_num_rows($resultx);
			$sum= $sum + ($i*$numopciones);
			$porcentaje = ($numopciones/$numrespuestas)*100;
			$porcentaje = number_format($porcentaje, 2, '.', '');
			?>
			<td><b><?=$numopciones?></b> (<?=$porcentaje?>%)</td>
			<?
		} 
		$valoracionmedia = $sum/$numrespuestas;
		$valoracionmedia = number_format($valoracionmedia*10 , 2, '.', '')*2;
		?>
	
		<td><b><?=$valoracionmedia?>%</b></td>
	</tr>	
	</tbody>
	</table>
	
	<br>
	</div>
</div>

<? 
include("plantillaweb02admin.php"); 
?>