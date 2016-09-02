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
$idopcion = $_REQUEST['idopcion'];
$accion = $_REQUEST['accion'];
$orden = 1;

if ($idencuesta<>""){
	$sql = "SELECT * FROM encuestas WHERE borrado=0 AND id='$idencuesta' $sqlcolegio";
	$result = posgre_query($sql);
	
	if (pg_num_rows($result)==0){
		$_SESSION[esterror]="Par�metros incorrectos. E1";	
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
		$_SESSION[esterror]="Par�metros incorrectos. E2";	
		header("Location: index.php");
		exit();
	}
	
	$row = pg_fetch_array($result);
	$idpregunta = $row['id'];
	$tipo = $row['tipo'];
	$pregunta = $row['texto'];
	$respuesta = $row['obligatorio'];
}

if ($idopcion<>""){
	$sql = "SELECT * FROM encuestas_opciones WHERE borrado=0 AND id='$idopcion' AND idpregunta='$idpregunta'";
	$result = posgre_query($sql);
	
	if (pg_num_rows($result)==0){
		$_SESSION[esterror]="Par�metros incorrectos. E3";	
		header("Location: index.php");
		exit();
	}
	
	$row = pg_fetch_array($result);
	$idopcion = $row['id'];
	$idprofesor = $row['idprofesor'];
	$fila = $row['fila'];
	$columna = $row['columna'];
	$orden = $row['orden'];
	$opcioncontexto = $row['opcioncontexto'];
	$notaasterisco = $row['notaasterisco'];
	
	$textoaccion="Editar";
}
else{
	$idprofesor=trim(strip_tags($_REQUEST['p']));
	$textoaccion="Nueva";
}

if ($idprofesor<>""){
	$sql = "SELECT * FROM usuario WHERE id='$idprofesor'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$nombreprofesor = $row['nombre'];
	$nombreprofesor .= " ".$row['apellidos'];
}

if ($accion=="guardar"){
	$fila = $_REQUEST['opcion'];
	$columna = $_REQUEST['posicion'];
	$orden = $_REQUEST['orden'];
	$idprofesor = $_REQUEST['idprofesor'];
	$checkboxOpcionabierta = $_REQUEST['opcioncontexto'];
	$notaasterisco = trim($_REQUEST['notaasterisco']);
	
	if ($checkboxOpcionabierta=="opcioncontexto"){
		$opcioncontexto=1;
	}
	else{
		$opcioncontexto=0;
	}
	
	if ($columna==""){
		$columna=0;
	}
	if ($idprofesor==""){
		$idprofesor=0;
	}
	
	if ($idopcion==0){
		$sql = "INSERT INTO encuestas_opciones (idpregunta, fila, columna ,orden, idprofesor,opcioncontexto,notaasterisco) VALUES ('$idpregunta', '$fila', '$columna','$orden', '$idprofesor', '$opcioncontexto', '$notaasterisco') RETURNING id";
		$_SESSION[esterror]="Creada correctamente";	}
	else{
		$sql = "UPDATE encuestas_opciones SET notaasterisco='$notaasterisco', opcioncontexto='$opcioncontexto', fila='$fila', columna='$columna', orden='$orden' WHERE id='$idopcion' AND idpregunta='$idpregunta' RETURNING id";
		$_SESSION[esterror]="Editada correctamente";	
	}
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$id = $row['id'];
		
	if ($result){
		
		header("Location: e_opciones.php?idencuesta=$idencuesta&idpregunta=$idpregunta");
		exit();
	}
	else{
		$_SESSION[esterror]="No se pudo crear/modificar encuesta";	
		header("Location: index.php");
		exit();
	}
	
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
				<a href="e_opciones.php?idencuesta=<?=$idencuesta?>&idpregunta=<?=$idpregunta?>" class="btn btn-success">Volver <i class="icon-circle-arrow-left"></i></a>		
			</p>
		</div>
		
		<h2><?=$textoaccion?> opci&oacute;n</h2>
		<form action="e_opcion.php?accion=guardar&idencuesta=<?=$idencuesta?>&idpregunta=<?=$idpregunta?>&idopcion=<?=$idopcion?>" method="post" enctype="multipart/form-data">
			<br>
			<? if ($tipo==4){ ?>
				
				<div class="control-group">
					<label class="control-label" for="profesor">Profesor:</label>
						<div class="controls">
							<input readonly type="text" id="opcion" class="input-xxlarge" name="opcion" value="<?=$nombreprofesor?>"/><? if ($idprofesor=="") { ?> <a class="btn btn-primary" href="e_profesor.php?idencuesta=<?=$idencuesta?>&idpregunta=<?=$idpregunta?>">Seleccionar profesor</a> <? } ?>
							<input type="hidden" id="idprofesor" name="idprofesor" value="<?=$idprofesor?>"/>	
							
						</div>
				</div>
			<? 
			} 
			else { 
			?>
				
				<div class="control-group">
					<label class="control-label" for="pregunta">Texto opci&oacute;n:</label>
						<div class="controls">
							<input required type="text" id="opcion" class="input-xxlarge" name="opcion" value="<?=$fila?>"/>
						</div>
				</div>
				
				<? if (($tipo==1)||($tipo==5)){ ?> 
					<div class="control-group">
						<label class="control-label" for="pregunta">Opci&oacute;n con texto libre:</label>
							<div class="controls">
								<input type="checkbox" id="opcioncontexto" class="input-xxlarge" name="opcioncontexto" value="opcioncontexto" <? if ($opcioncontexto==1){ echo 'checked'; } ?> />
							</div>
					</div>
					
					<div class="control-group">
						<label class="control-label" for="pregunta">[Opcional] Nota con asterisco(*) al final de la pregunta. El * se pone solo en la opci&oacute;n.</label>
							<div class="controls">
								<input type="text" id="notaasterisco" class="input-xxlarge" name="notaasterisco" value="<?=$notaasterisco?>"/>
							</div>
					</div>
				<? } ?>	
			<? } ?>	
			
			<? if ($tipo==2){ ?> 
				
				<div class="control-group">
					<label class="control-label" for="respuesta">Posici&oacute;n en matriz:</label>
					<select name="posicion" class="input-xlarge" >
						<option class="input-xlarge" value="0" <? if ($columna==0) echo " selected "; ?>>Fila</option>
						<option class="input-xlarge" value="1" <? if ($columna==1) echo " selected "; ?>>Columna</option>
					</select>
				</div>
				
			<? } ?>
			
			
			
			<div class="control-group">
				<label class="control-label" for="nombre">Orden:</label>
					<div class="controls">
						<input required type="number" id="orden" class="input" name="orden" value="<?=$orden?>"/>
					</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary btn-large">Guardar</button>
			</div>
			
		</form>
	</div>
	<!--fin pagina blog-->
	<div class="clearfix"></div>
</div>

<?
include("plantillaweb02admin.php"); 
?>		
