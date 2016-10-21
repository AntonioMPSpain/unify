<?php
//include("__seguridad01.php"); 
include("_funciones.php");
include("_cone.php");
$accion=$_GET['accion'];
$est=$_REQUEST['est'];
$id=trim($_REQUEST['id']);
if (($id=="")){
	$_SESSION[error]="Parametros incorrectos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}
$idcurso=trim($_REQUEST['idcurso']);
if (($idcurso=="")){
	$_SESSION[error]="Parametros incorrectos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	$idcolegio=strip_tags($_SESSION[idcolegio]);
	$sqlmas=" AND idusuario='$idcolegio' ";
	//echo "Error: aqui no deberia entrar.";
	//exit();
}elseif ($_SESSION[nivel]==1) { //Admin Total
	//echo "ok: aqui deberia entrar.";
	//exit();
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#3");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

if($accion=='guardarm'){
	//$texto=nl2br(strip_tags($_REQUEST['texto']));
	$fecha=cambiaf_a_mysql($_POST['fi']);
	$hora=$_REQUEST['hora'];
	$horafin=$_REQUEST['horafin'];
	if ($fecha<>'' ){
		 $link=iConectarse();  
		$Query = pg_query($link,"UPDATE curso_horario SET fecha='$fecha',hora='$hora',horafin='$horafin' WHERE  borrado=0 AND id='$id'  ;") ;//or die (mysql_error()); 
		header("Location: curso_horarios.php?est=ok&id=$id&idcurso=$idcurso"); 
		exit();
	}else{
		$_SESSION[error]="No dispone de permisos";	
		header("Location: index.php?error=true&est=ko");
		exit();
	}
}


if (($accion=="")){
	$titulo1="formación";
	$titulo2="administración";
	include("plantillaweb01admin.php");   
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM curso_horario WHERE borrado=0 AND id='$id' AND idcurso='$idcurso' ;");//or die (mysql_error());
	$row = pg_fetch_array($result);
	$fecha=cambiaf_a_normal($row["fecha"]);
	$hora=($row["hora"]);
	   	?>
	<!--Arriba -->
	<div class="grid-8 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">
	<h2><?=$titulo?></h2>
		<FORM METHOD="post" ACTION="curso_horarios_hora.php?accion=guardarm&id=<?=$id?>&idcurso=<?=$idcurso?>" enctype="multipart/form-data">
				<fieldset>				    
					<? if ($est=="ko"){ ?><span class="rojo">Error</span><? }?>
					<? if ($est=="ok"){ ?><span class="rojo">Guardado</span><? }?>
					<div class="control-group">
						<label class="control-label" for="fecha">Fecha:</label>
						<div class="controls">
							<input class="input-small" id="fecha" name="fi" type="text" value="<?=$fecha?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="hora">Hora inicio:</label>
						<div class="controls">
							<input class="input-mini" id="hora" name="hora" type="text" value="<?=$hora?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="horafin">Hora fin:</label>
						<div class="controls">
							<input class="input-mini" id="horafin" name="horafin" type="text" value="<?=$horafin?>" />
						</div>
					</div>
				</fieldset>
					<div class="form-actions">
						<? if ($titulo1=="editar") { $textboton="Guardar cambios";} else{ $textboton="Guardar";}?>
						<button type="submit" class="btn btn-primary btn-large"><?=$textboton?></button>
					</div>
					<?									   
					pg_free_result($result); 
				    pg_close($link); 
				   //session_destroy();
					?>
				</form>
<?
}
?>
 </div>
<!--fin pagina blog-->
<div class="clearfix"></div>
</div>
<!--fin grid-8 contenido-principal-->

<?
include("plantillaweb02admin.php"); 
?>