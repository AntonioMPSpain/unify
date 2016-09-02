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
$orden = 1;

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
	$idpregunta = $row['id'];
	$tipo = $row['tipo'];
	$pregunta = $row['texto'];
	$obligatorio = $row['obligatorio'];
	$respuesta = $row['respuestas'];
	$orden = $row['orden'];
	
	$textoaccion="Editar";
}
else{
	$textoaccion="Nueva";
}

if ($accion=="guardar"){
	$pregunta = $_REQUEST['pregunta'];
	$tipo = $_REQUEST['tipo'];
	$obligatorio = $_REQUEST['obligatorio'];
	$respuesta = $_REQUEST['respuesta'];
	$orden = $_REQUEST['orden'];
	
	if ($tipo==4){
		$respuesta=1;
	}
	
	if ($idpregunta==0){
		$sql = "INSERT INTO encuestas_preguntas (idencuesta, texto, tipo, respuestas,obligatorio,orden, idusuariocreador) VALUES ('$idencuesta', '$pregunta', '$tipo', '$respuesta','$obligatorio','$orden','$idusuario') RETURNING id";
		$_SESSION[esterror]="Creada correctamente";	}
	else{
		$sql = "UPDATE encuestas_preguntas SET texto='$pregunta', obligatorio='$obligatorio', respuestas='$respuesta', orden='$orden' WHERE idencuesta='$idencuesta' AND id='$idpregunta' RETURNING id";
		$_SESSION[esterror]="Editada correctamente";	
	}
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$id = $row['id'];
		
	if ($result){
		if (($idpregunta==0)&&($tipo==3)){		// Respuesta abierta
			$sql ="INSERT INTO encuestas_opciones (idpregunta) VALUES ('$id')";
			posgre_query($sql);
		}
		elseif (($idpregunta==0)&&($tipo==4)){		// Respuesta abierta
			$sql ="INSERT INTO encuestas_opciones (idpregunta,fila,columna,orden) VALUES ('$id','1','1','1'),('$id','2','1','2'),('$id','3','1','3'),('$id','4','1','4'),('$id','5','1','5')";
			posgre_query($sql);
		}
		
		header("Location: e_preguntas.php?id=$idencuesta");
		exit();
	}
	else{
		// echo pg_last_error(); exit();
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
				<a href="e_preguntas.php?id=<?=$idencuesta?>" class="btn btn-success">Volver <i class="icon-circle-arrow-left"></i></a>		
				<? if (($idpregunta<>"")&&($tipo<>"3")){ ?>
					<a href="e_opciones.php?idencuesta=<?=$idencuesta?>&idpregunta=<?=$idpregunta?>" class="btn btn-success">Opciones <i class="icon-plus"></i></a>
				<? } ?>
			</p>
		</div>
		
		<h2><?=$textoaccion?> pregunta</h2>
		<form action="e_pregunta.php?accion=guardar&idencuesta=<?=$idencuesta?>&idpregunta=<?=$idpregunta?>" method="post" enctype="multipart/form-data">
			<br>
			
			<div class="control-group">
				<label class="control-label" for="pregunta">Texto pregunta:</label>
					<div class="controls">
						<input required type="text" id="pregunta" class="input-xxlarge" name="pregunta" value="<?=$pregunta?>"/>
					</div>
			</div>
			<? if ($idpregunta==""){ ?>
			<div class="control-group">
				<label class="control-label" for="tipo">Tipo:</label>
				<select name="tipo" class="input-xlarge" >
					<option class="input-xlarge" value="1" <? if ($tipo==1) echo " selected "; ?>>Respuesta simple</option>
					<option class="input-xlarge" value="2" <? if ($tipo==2) echo " selected "; ?>>Tabla(muchas respuestas simples)</option>
					<option class="input-xlarge" value="3" <? if ($tipo==3) echo " selected "; ?>>Respuesta cuadro de texto abierto</option>
					<option class="input-xlarge" value="4" <? if ($tipo==4) echo " selected "; ?>>Valorar docentes(formato tabla)</option>
					<option class="input-xlarge" value="5" <? if ($tipo==5) echo " selected "; ?>>Respuesta ordenando por preferencia</option>
					<option class="input-xlarge" value="6" <? if ($tipo==6) echo " selected "; ?>>Solo texto(no es una pregunta, no se numera)</option>
				</select>
			</div>
			<? } ?>
			
			<div class="control-group">
				<label class="control-label" for="obligatorio">Obligatoria:</label>
				<select name="obligatorio" class="input-xlarge" >
					<option class="input-xlarge" value="1" <? if ($obligatorio==1) echo " selected "; ?>>Si</option>
					<option class="input-xlarge" value="0" <? if ($obligatorio==0) echo " selected "; ?>>No</option>
				</select>
			</div>
			<div class="control-group">
				<label class="control-label" for="respuesta">N&uacute;mero de respuestas simult&aacute;neas:</label>
				<select name="respuesta" class="input-xlarge" >
					<option class="input-xlarge" value="1" <? if ($respuesta==1) echo " selected "; ?>>1 respuesta</option>
					<option class="input-xlarge" value="2" <? if ($respuesta==2) echo " selected "; ?>>1 o m&aacute;s respuestas</option>
				</select>
			</div>
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
