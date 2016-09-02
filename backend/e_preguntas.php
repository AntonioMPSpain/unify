<?

include("_funciones.php"); 
include("_cone.php");
$safe="encuestas";

////////// Filtros de nivel por usuario //////////////////////
session_start();

if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$idusuario = $idcolegio;
		$sqlcolegio = " AND idcurso IN (SELECT id FROM curso WHERE idcolegio='$idcolegio') ";
	}else{
		$_SESSION[esterror]="Par�metros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
	$idusuario = $_SESSION['idusuario'];
}
else{
	$_SESSION[esterror]="Par�metros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario ////////////////////// 

$id = $_REQUEST['id'];			//idencuesta
$accion = $_REQUEST['accion'];

if ($id<>""){
	$sql = "SELECT * FROM encuestas WHERE borrado=0 AND id='$id' $sqlcolegio";
	$result = posgre_query($sql);
	
	if (pg_num_rows($result)==0){
		$_SESSION[esterror]="Par�metros incorrectos";	
		header("Location: index.php");
		exit();
	}
	
	$row = pg_fetch_array($result);
	$idcurso = $row['idcurso'];
	$estado = $row['estado'];
	$nombre = $row['nombre'];
	
}
else{
	
}

if ($idcurso<>""){
	$sql = "SELECT * FROM curso WHERE id='$idcurso'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$nombrecurso = $row['nombre'];
}

if ($accion=="eliminar"){
	$idpregunta = $_REQUEST['idpregunta'];
	if ($idpregunta<>""){
		$sql = "UPDATE encuestas_preguntas SET borrado=1 WHERE idencuesta='$id' AND id='$idpregunta' AND borrado=0";
		posgre_query($sql);
	}
	
	header("Location: e_preguntas.php?id=$id");
	exit();
}

$titulo1="gesti&oacute;n";
$titulo2="encuestas";
include("plantillaweb01admin.php");
?>

<div class="grid-12 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">	
	
		<? include("_aya_mensaje_session.php");?>
		<div class="bloque-lateral acciones">	
			<p><strong>Acciones:</strong>
				<a href="e_nueva.php?id=<?=$id?>" class="btn btn-success">Volver <i class="icon-circle-arrow-left"></i></a>		
			<? if ($id<>""){ ?>
				<a href="e_pregunta.php?idencuesta=<?=$id?>" class="btn btn-success">Nueva pregunta <i class="icon-plus"></i></a>
			<? } ?>
			</p>
		</div>
		
		<h2>Preguntas</h2>
		<table class="align-center" border="0" cellpadding="0" cellspacing="0">
			<tbody><tr>
				<th>Orden</th>
				<th>Pregunta</th>
				<th>Tipo</th>
				<th>Obligatoria</th>
				<th>Respuestas</th>
				<th>Acciones</th>
			</tr>
			<? 
			
			$result=posgre_query("SELECT * FROM encuestas_preguntas WHERE idencuesta='$id' AND borrado=0 ORDER BY orden, fechacreacion"); 
			while($row = pg_fetch_array($result)) { 
				$idpregunta = $row['id'];
				$tipo = $row['tipo'];
				$texto = $row['texto'];
				$obligatorio = $row['obligatorio'];
				$respuesta = $row['respuestas'];
				$orden = $row['orden'];
				
				if ($tipo==1){
					$textotipo="Respuesta simple";
				}
				elseif ($tipo==2){
					$textotipo="Tabla(muchas respuestas simples)";
				}
				elseif ($tipo==3){
					$textotipo="Respuesta cuadro de texto abierto";
				}
				elseif ($tipo==4){
					$textotipo="Valorar docentes(formato tabla)";
				}
				elseif ($tipo==5){
					$textotipo="Respuesta ordenando por preferencia";
				}				
				elseif ($tipo==6){
					$textotipo="Solo texto(no es una pregunta, no se numera)";
				}
				else{
					$textotipo="Indefinido";
				}
				
				if ($obligatorio==0){
					$textoobligatorio="No";
				}
				elseif ($obligatorio==1){
					$textoobligatorio="Si";
				}
				else{
					$textoobligatorio="Indefinido";
				}
				
				if ($respuesta==1){
					$textorespuesta="1 respuesta";
				}
				elseif ($respuesta==2){
					$textorespuesta="1 o m&aacute;s respuestas";
				}
				else{
					$textorespuesta="Indefinido";
				}
				
			?>
				<tr>
					<td><?=$orden?></td>
					<td><?=$texto?></td>
					<td><?=$textotipo?></td>
					<td><?=$textoobligatorio?></td>
					<td><?=$textorespuesta?></td>
					<td>
						<a href="e_pregunta.php?idencuesta=<?=$id;?>&idpregunta=<?=$idpregunta?>" class="btn btn-primary">editar</a>		
						<a onclick="return confirm('&iquest;Desea eliminar la pregunta?')" href="e_preguntas.php?accion=eliminar&id=<?=$id;?>&idpregunta=<?=$idpregunta?>" class="btn btn-primary">eliminar</a>	
					
					</td>
						
			
				</tr>
				<? }?>
			</tbody>
			</table>
	</div>
	<!--fin pagina blog-->
	<div class="clearfix"></div>
</div>

<?
include("plantillaweb02admin.php"); 
?>		
