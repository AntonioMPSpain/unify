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

$id = $_REQUEST['id'];
$accion = $_REQUEST['accion'];

if ($accion=="guardar"){
	$idcurso = $_REQUEST['idcurso'];
	$nombre = $_REQUEST['nombre'];
	$estado = $_REQUEST['estado'];
	
	
	if (($idcurso==0)||($idcurso=="")){
		$idcurso = 0;
	/*
		$_SESSION[esterror]="No ha elegido curso";	
		header("Location: e_nueva.php");
		exit();
	*/
	}
	
	
	if ($id==0){
		$tokenacceso = md5("token123".$idcurso.time()."token321");
		$sql = "INSERT INTO encuestas (idcurso, nombre, estado, idusuariocreador,tokenacceso, plantilla) VALUES ('$idcurso','$nombre','$estado','$idusuario', '$tokenacceso', 0) RETURNING id";
		$_SESSION[esterror]="Creado correctamente";	}
	else{
		$sql = "UPDATE encuestas SET nombre='$nombre', estado='$estado' WHERE id='$id' $sqlcolegio RETURNING id";
		$_SESSION[esterror]="Editado correctamente";	
	}
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$id = $row['id'];
	if ($result){
		header("Location: e_inicio.php");
		exit();
	}
	else{
		$_SESSION[esterror]="No se pudo crear/modificar encuesta";	
		header("Location: index.php");
		exit();
	}
	
}

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
	
	
	
	
	$textoaccion="Editar";
}
else{
	$idcurso=trim(strip_tags($_REQUEST['c']));
	$textoaccion="Nueva";
}

if ($idcurso<>""){
	$sql = "SELECT * FROM curso WHERE id='$idcurso'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$nombrecurso = $row['nombre'];
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
				<a href="e_inicio.php" class="btn btn-success">Volver <i class="icon-circle-arrow-left"></i></a>		
			<? if ($id<>""){ ?>
				<a href="e_preguntas.php?id=<?=$id?>" class="btn btn-success">Preguntas <i class="icon-plus"></i></a>
			<? } ?>
			</p>
		</div>
		
		<h2><?=$textoaccion?> encuesta</h2>
		<form action="e_nueva.php?accion=guardar" method="post" enctype="multipart/form-data">
			<br>
			<div class="control-group">
				<label class="control-label" for="curso">Curso(dejar vac&iacute;o para crear encuesta global):</label>
					<div class="controls">
						<input disabled type="text" id="curso" class="input-xxlarge" name="curso" value="<?=$nombrecurso?>"/><? if ($idcurso=="") { ?> <a class="btn btn-primary" href="e_curso.php">Seleccionar curso</a> <? } ?>
						<input type="hidden" id="idcurso" name="idcurso" value="<?=$idcurso?>"/>						
						<input type="hidden" id="id" name="id" value="<?=$id?>"/>
						
					</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="nombre">Nombre:</label>
					<div class="controls">
						<input required type="text" id="nombre" class="input-xxlarge" name="nombre" value="<?=$nombre?>"/>
					</div>
			</div>
			<label class="control-label" for="estado">Estado:</label>
			<div class="controls">
				<select name="estado" class="input-xlarge" >
					<option class="input-xlarge" value="0" <? if ($estado==0) echo " selected "; ?>>Cerrada</option>
					<option class="input-xlarge" value="1" <? if ($estado==1) echo " selected "; ?>>Abierta</option>
				</select>
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
