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

$idencuesta = $_REQUEST['idencuesta'];
$idpregunta = $_REQUEST['idpregunta'];
$accion = $_REQUEST['accion'];

if ($idencuesta<>""){
	$sql = "SELECT * FROM encuestas WHERE borrado=0 AND id='$idencuesta' $sqlcolegio";
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

if ($idcurso<>""){
	$sql = "SELECT * FROM curso WHERE id='$idcurso'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$nombrecurso = $row['nombre'];
}

if ($idpregunta<>""){
	$sql = "SELECT * FROM encuestas_preguntas WHERE borrado=0 AND id='$idpregunta' AND idencuesta='$idencuesta'";
	$result = posgre_query($sql);
	
	if (pg_num_rows($result)==0){
		$_SESSION[esterror]="Par�metros incorrectos";	
		header("Location: index.php");
		exit();
	}
	
	$row = pg_fetch_array($result);
	$tipo = $row['tipo'];
	$pregunta = $row['texto'];
	$obligatorio = $row['obligatorio'];
	$respuesta = $row['respuestas'];
	$orden = $row['orden'];
	
	if ($respuesta==2){
		$tipoinput = "checkbox";
	}
	else{
		$tipoinput = "radio";
	}
}

if ($accion=="eliminar"){
	$idopcion = $_REQUEST['idopcion'];
	if ($idopcion<>""){
		$sql = "UPDATE encuestas_opciones SET borrado=1 WHERE id='$idopcion' AND idpregunta='$idpregunta' AND borrado=0";
		posgre_query($sql);
	}
	
	header("Location: e_opciones.php?idencuesta=$idencuesta&idpregunta=$idpregunta");
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
				<a href="e_pregunta.php?idencuesta=<?=$idencuesta?>&idpregunta=<?=$idpregunta?>" class="btn btn-success">Volver <i class="icon-circle-arrow-left"></i></a>		
				<a href="e_opcion.php?idencuesta=<?=$idencuesta?>&idpregunta=<?=$idpregunta?>" class="btn btn-success">Nueva opci&oacute;n <i class="icon-plus"></i></a>

			</p>
		</div>
		<? 
		if (($tipo==2)||($tipo==4)){
			$sql = "SELECT * FROM encuestas_opciones WHERE borrado=0 AND idpregunta='$idpregunta' AND columna='1'";
			$result = posgre_query($sql);
			$columnas = pg_num_rows($result);
		}
		
		?>
		<h2>Opciones</h2>
		<table class="align-center" border="0" cellpadding="0" cellspacing="0">
			<tbody><tr>
				<th> </th>
				<? 
				if ($columnas>0){
					$result=posgre_query("SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='1' ORDER BY orden, id"); 
					while($row = pg_fetch_array($result)) { 
						$col = $row['fila'];
					?>
						<th><?=$col?></th>
				<? } 
				} else {
					echo '<th> </th>';
				}?>
				<th style="width:50px;">Orden</th>
				<th style="width:150px;">Acciones</th>
			</tr>
			<? 
			
			$asteriscos=1;
			$result=posgre_query("SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='0' ORDER BY orden, id"); 
			while($row = pg_fetch_array($result)) { 
				$idopcion = $row['id'];
				$fila = $row['fila'];
				$columna = $row['columna'];
				$orden = $row['orden'];
				
				/** Asteriscos **/
				$notaasterisco = $row['notaasterisco'];
				$textoasterisco1="";
				$textoasterisco2="";
				if ($notaasterisco<>""){
					$textoasterisco1="<div title='$notaasterisco'>";
					for ($i=0;$i<$asteriscos;$i++){
						
						$textoasterisco2.="*";
					}
					$textoasterisco2.="</div>";
					$asteriscos++;
				}
			?>
				<tr>
					<td><?=$textoasterisco1?><?=$fila?><?=$textoasterisco2?></td>
					<? if ($tipo==2){
						?> <td><input name="radio<?=$idopcion?>" type="<?=$tipoinput?>"></td> <?
					}
					else{ 
						?> <td><input name="radio" type="<?=$tipoinput?>"></td> <?
					} 
					
					for ($i=0; $i<$columnas-1;$i++){ 
					?>
						<td><input name="radio<?=$idopcion?>" type="<?=$tipoinput?>"></td>
					<? 
					} 
					?>
					<td><?=$orden?></td>
					<td>
						<a href="e_opcion.php?idencuesta=<?=$idencuesta;?>&idpregunta=<?=$idpregunta?>&idopcion=<?=$idopcion?>" class="btn btn-primary">editar</a>		
						<a onclick="return confirm('iquest;Desea eliminar la opci&oacute;n?')" href="e_opciones.php?accion=eliminar&idencuesta=<?=$idencuesta;?>&idpregunta=<?=$idpregunta?>&idopcion=<?=$idopcion?>" class="btn btn-primary">eliminar</a>	
					
					</td>
						
			
				</tr>
				<? }
				if (($tipo==2)&&($columnas>0)){ 
				
				
				?>
				
					<tr>
					<td><b>Orden</b></td>
					<?
						$result=posgre_query("SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='1' ORDER BY orden, id"); 
						while($row = pg_fetch_array($result)) { 
							?>
							<td> 
							<?=$row['orden']?>
							</td>
							
							<?
						}
					
					?>
					<td> </td>
					<td> </td>
					</tr>
					<tr>
					<td><b>Acciones</b></td>
					<?
						$result=posgre_query("SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='1' ORDER BY orden, id"); 
						while($row = pg_fetch_array($result)) { 
							$idopcion = $row['id'];
							?>
							<td> 
							<a href="e_opcion.php?idencuesta=<?=$idencuesta;?>&idpregunta=<?=$idpregunta?>&idopcion=<?=$idopcion?>" class="btn btn-primary">editar</a>		
							<a onclick="return confirm('iquest;Desea eliminar la opci&oacute;n?')" href="e_opciones.php?accion=eliminar&idencuesta=<?=$idencuesta;?>&idpregunta=<?=$idpregunta?>&idopcion=<?=$idopcion?>" class="btn btn-primary">eliminar</a>	
							</td>
							
							<?
						}
					
					?>
					<td> </td>
					<td> </td>
					</tr>
				<? 
				} 
				?>
				
			</tbody>
			</table>
	</div>
	<!--fin pagina blog-->
	<div class="clearfix"></div>
</div>

<?
include("plantillaweb02admin.php"); 
?>		
