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
		$idcolegio="";
		//$sqlcolegio = " AND idcurso IN (SELECT id FROM curso WHERE idcolegio='$idcolegio') ";
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

$idduplicar = $_REQUEST['idduplicar'];
$idcurso=trim(strip_tags($_REQUEST['c']));
$accion = $_REQUEST['accion'];
$estado = 1;

if ($accion=="duplicar"){
	$idcurso = $_REQUEST['idcurso'];
	$idduplicar = $_REQUEST['idduplicar'];
	$nombre = $_REQUEST['nombre'];
	$estado = $_REQUEST['estado'];
	
	if ($idcurso==0){
		$_SESSION[esterror]="No ha elegido curso";	
		header("Location: e_duplicar.php?idduplicar=$idduplicar");
		exit();
		
	}
	
	$sql = "SELECT * FROM encuestas WHERE idcurso='$idcurso' AND borrado=0";
	$result = posgre_query($sql);
	if (pg_num_rows($result)>0){
		$_SESSION[esterror]="Ya existe una encuesta para este curso";	
		header("Location: e_duplicar.php?idduplicar=$idduplicar");
		exit();
	}
	
	
	$tokenacceso = md5("token123".$idcurso.time()."token321");
	$sql = "INSERT INTO encuestas (idcurso, nombre, estado, idusuariocreador, tokenacceso, plantilla) VALUES ('$idcurso','$nombre','$estado','$idusuario', '$tokenacceso', 1) RETURNING id";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$id = $row['id'];
	
	$sql = "SELECT * FROM encuestas_preguntas WHERE borrado=0 AND idencuesta='$idduplicar'";
	$result2 = posgre_query($sql);
	
	while ($rowpreguntas = pg_fetch_array($result2)){
		$idpregunta=$rowpreguntas['id'];
		$idencuesta=$id;
		$tipo=$rowpreguntas['tipo'];
		$texto=$rowpreguntas['texto'];
		$obligatorio=$rowpreguntas['obligatorio'];
		$orden=$rowpreguntas['orden'];
		$respuestas=$rowpreguntas['respuestas'];
		$sql3= "INSERT INTO encuestas_preguntas (idencuesta,tipo,texto,obligatorio,orden,respuestas) VALUES ('$idencuesta','$tipo','$texto','$obligatorio','$orden','$respuestas') RETURNING id;";
		$result3 = posgre_query($sql3);
		$row = pg_fetch_array($result3);
		$idpreguntanueva = $row['id'];
		
		$sql4 = "SELECT * FROM encuestas_opciones WHERE borrado=0 AND idpregunta='$idpregunta' AND idpregunta NOT IN (SELECT id FROM encuestas_preguntas WHERE tipo IN(3,4))";
		$result4 = posgre_query($sql4);
		while ($rowopciones = pg_fetch_array($result4)){
			$fila=$rowopciones['fila'];
			$columna=$rowopciones['columna'];
			$idprofesor=$rowopciones['idprofesor'];
			$orden=$rowopciones['orden'];
			$sql5 = "INSERT INTO encuestas_opciones (idpregunta,fila,columna,idprofesor,orden) VALUES ('$idpreguntanueva','$fila','$columna','$idprofesor','$orden')";
			$result5 = posgre_query($sql5);
			
			
		}
		
		if ($tipo==3){		// Respuesta abierta
			$sql ="INSERT INTO encuestas_opciones (idpregunta) VALUES ('$idpreguntanueva')";
			posgre_query($sql);
		}
		
		if ($tipo==4){
			$sql8 ="INSERT INTO encuestas_opciones (idpregunta,fila,columna,orden) VALUES ('$idpreguntanueva','1','1','1'),('$idpreguntanueva','2','1','2'),('$idpreguntanueva','3','1','3'),('$idpreguntanueva','4','1','4'),('$idpreguntanueva','5','1','5')";
			posgre_query($sql8);
			$sql6 = "SELECT * FROM curso_docente_web WHERE borrado=0 AND idcurso='$idcurso'";
			$result6 = posgre_query($sql6);
			while ($rowprofesores = pg_fetch_array($result6)){
				$idprofesor = $rowprofesores['idusuario'];
			
				$sql7 = "SELECT * FROM usuario WHERE id='$idprofesor'";
				$result7 = posgre_query($sql7);
				while ($rowprofesor = pg_fetch_array($result7)){
					$nombre = $rowprofesor['nombre'];
					$apellidos = $rowprofesor['apellidos'];
					$fila = $nombre." ".$apellidos; 
				}
			
				$sql5 = "INSERT INTO encuestas_opciones (idpregunta,fila,columna,idprofesor,orden) VALUES ('$idpreguntanueva','$fila',0,'$idprofesor',1)";
				$result5 = posgre_query($sql5);
			}
		}
			
	}
	
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

if ($idduplicar<>""){
	$sql = "SELECT * FROM encuestas WHERE borrado=0 AND id='$idduplicar' $sqlcolegio";
	$result = posgre_query($sql);
	
	if (pg_num_rows($result)==0){
		$_SESSION[esterror]="Par�metros incorrectos";	
		header("Location: index.php");
		exit();
	}
	
	$row = pg_fetch_array($result);
	$idcursoduplicado = $row['idcurso'];
	$estadoduplicado = $row['estado'];
	$nombreduplicado = $row['nombre'];
	
	
}
else{
	
}

$idcurso = $_REQUEST['c'];

if ($idcurso<>""){
	$sql = "SELECT * FROM curso WHERE id='$idcurso'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$nombrecurso = $row['nombre'];
	$id_categoria_moodle = $row["id_categoria_moodle"];
	$nombreencuesta = "Encuesta ".$idcurso."/".$id_categoria_moodle;
}

if ($idcursoduplicado<>""){
	$sql = "SELECT * FROM curso WHERE id='$idcursoduplicado'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$nombrecursoduplicado = $row['nombre'];
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
			</p>
		</div>
		
		<h2><? if ($idduplicar==1) {echo 'Generar';} else {echo 'Duplicar';} ?> encuesta</h2>
		<form action="e_duplicar.php?accion=duplicar" method="post" enctype="multipart/form-data">
			<br>
			<p>Antes de generar la encuesta, asegurese de haber <b>ASIGNADO LOS DOCENTES</b> del curso</p>
			<p> Encuesta que se duplica(preguntas y opciones): <b><?=$nombreduplicado?> <? if ($nombrecursoduplicado<>"") { ?> (Curso: <?=$nombrecursoduplicado?>) <? } ?></b></p>
			
			<div class="control-group">
				<label class="control-label" for="curso">Curso:</label>
					<div class="controls">
						<input disabled type="text" id="curso" class="input-xxlarge" name="curso" value="<?=$nombrecurso?>"/><? if ($idcurso=="") { ?> <a class="btn btn-primary" href="e_curso.php?accion=duplicar&idduplicar=<?=$idduplicar?>">Seleccionar curso</a> <? } ?>
						<input type="hidden" id="idcurso" name="idcurso" value="<?=$idcurso?>"/>						
						<input type="hidden" id="idduplicar" name="idduplicar" value="<?=$idduplicar?>"/>
						
					</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="nombre">Nombre:</label>
					<div class="controls">
						<input required type="text" id="nombre" class="input-xxlarge" name="nombre" value="<?=$nombreencuesta?>"/>
					</div>
			</div>
			<label class="control-label" for="estado">Estado:</label>
			<div class="controls">
				<select name="estado" class="input-xlarge" >
					<option class="input-xlarge" value="1" <? if ($estado==1) echo " selected "; ?>>Abierta</option>
					<option class="input-xlarge" value="0" <? if ($estado==0) echo " selected "; ?>>Cerrada</option>
				</select>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary btn-large">Duplicar</button>
			</div>
		</form>
	</div>
	<!--fin pagina blog-->
	<div class="clearfix"></div>
</div>

<?
include("plantillaweb02admin.php"); 
?>		
