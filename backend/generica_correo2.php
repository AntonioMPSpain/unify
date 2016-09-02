<?php
//include("__seguridad01.php"); 
include("_funciones.php");
include("_cone.php");
$accion=$_GET['accion'];
$est=$_REQUEST['est'];
$id=trim($_REQUEST['id']);
if ((id=="")){
	$_SESSION[error]="Parametros incorrectos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
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
	header("Location: index.php?error=true&est=ko#2");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

if($accion==guardarm){
	//$texto=nl2br(strip_tags($_REQUEST['texto']));
	$texto=($_REQUEST['texto']);
	$asunto=($_REQUEST['asunto']);
	
	if ($id==0){
		if ($texto<>'' ){
			$link=iConectarse();  
			$Query = pg_query($link,"INSERT INTO email (asunto,texto,paracurso) VALUES ('$asunto','$texto',1);") ;//or die (mysql_error()); 
			header("Location: generica_correo0.php"); 
			exit();
		}else{
			echo "Error: mal uso. 22";
		}
	}
	else{
		if ($texto<>'' ){
			 $link=iConectarse();  
			$Query = pg_query($link,"UPDATE email SET asunto='$asunto', texto='$texto' WHERE  borrado=0 AND id='$id' $sqlmas ;") ;//or die (mysql_error()); 
			header("Location: generica_correo0.php"); 
			exit();
		}else{
			echo "Error: mal uso. 22";
		}
	}
}


if (($accion=="")){
	$titulo1="formación";
	$titulo2="administración";
	include("plantillaweb01admin.php"); 
	?>	<script language="javascript">
			function confirmar ( mensaje ) {
				return confirm( mensaje );
				}
		</script>
		<script  type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<?
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM email WHERE borrado=0 AND id='$id' $sqlmas;");//or die (mysql_error());
	$row = pg_fetch_array($result);
	$texto=br2nl($row["texto"]);
	$asunto=($row["asunto"]);
	   	?>
	<!--Arriba -->
	<div class="grid-8 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog">
	<h2 class="titulonoticia"><?=$asunto?></h2>
	<br>
	<!--Acciones-->
	<div class="acciones">		
	<p>
		<a href="generica_correo0.php?idcurso=<?=$idcurso.$getcursodual?>" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
	</div>
	<!--fin acciones-->
		<FORM METHOD="post" ACTION="generica_correo2.php?accion=guardarm&id=<?=$id?>" enctype="multipart/form-data">
				<fieldset>				    
					<? if ($est=="ko"){ ?><span class="rojo">Error</span><? }?>
					<? if ($est=="ok"){ ?><span class="rojo">Guardado</span><? }?>
					<div class="control-group">
						<label class="control-label" for="inputsme">Asunto email:</label>
							<div class="controls">
								<input type="text" class="input-xxlarge" name="asunto" value="<?=$asunto?>">
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputsme">Texto email:</label>
							<div class="controls">
								<textarea class="span5" rows="10" name="texto"><?=$texto?></textarea>
											<script>
												window.onload = function() {
													CKEDITOR.replace( 'texto',{toolbar :[['Bold', 'Italic', '-', 'NumberedList', '-', 'Link', '-']]} );
												};
											</script>
							</div>
					</div>
					</fieldset>
					<br><div><b>Comodines(solo en texto email):</b>
					<p>%%nombre%% : nombre del alumno<br>
					%%apellidos%% : apellidos del alumno<br>
					%%curso%% : nombre del curso<br>
					%%horas%% : duración del curso<br>
					%%colegio%% : organizador del curso<br>
					%%emailcolegio%% : email del organizador del curso<br>
					%%fechainicio%% : fecha de inicio del curso<br>
					%%idcurso%% : identificador del curso</p>
					<div>
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