<?
session_start();
include("_funciones.php"); 
include("_cone.php"); 
include("a_insert_emailcron.php");
include("plantillaemail.php");
$titulo1="formación ";
$titulo2="administración";



////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	header("Location: index.php?salir=true&1");
	exit();
}elseif ($_SESSION[nivel]==3) { 
	header("Location: index.php?salir=true&12");
	exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
		$idcolegio=strip_tags($_SESSION[idusuario]);
}else{
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////


$curso1 = $_REQUEST['curso1'];
$curso2 = $_REQUEST['curso2'];
$curso3 = $_REQUEST['curso3'];

if ($curso1<>""){
	$sql = "SELECT nombre FROM curso WHERE id='$curso1'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$curso1text=$row["nombre"];
}

if ($curso2<>""){
	$sql = "SELECT nombre FROM curso WHERE id='$curso2'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$curso2text=$row["nombre"];
}

if ($curso3<>""){
	$sql = "SELECT nombre FROM curso WHERE id='$curso3'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$curso3text=$row["nombre"];
}

	include("plantillaweb01admin.php");
	?>
	<script  type="text/javascript" src="../ckeditor/ckeditor.js"></script>
	<!--Arriba plantilla1-->
		<div class="grid-9 contenido-principal">
		<div class="clearfix"></div>
		<div class="pagina zonaprivada blog">
			<h2 class="titulonoticia">Comunicaciones</h2>
			<br />
			<div class="bloque-lateral acciones">		
						<p><strong>Acciones:</strong>
							<a class="btn btn-success" href="zona-privada_admin_comunicaciones_5_historico-de-envios.php">Volver a Histórico de Mensajes Enviados <i class="icon-calendar"></i></a> <br />
						</p>
			</div>
			<!--fin acciones-->
			<br />
			<div class="bloque-lateral comunicacion">		
				<h4>Generar email varios cursos</h4>
				<!-- <form class="form-horizontal" action="__email2_para-acciones.php?accion=< ?=$accion?>&id=< ?=$id?>" method="post" enctype="multipart/form-data">-->
				<form class="form-horizontal" action="zona-privada_admin_comunicaciones_1.php?accion=varioscursos" method="post" enctype="multipart/form-data">
				
						</div>
					 </div>
						 <div class="control-group">
							<br /><br /><strong>Añadir/Cambiar</strong>
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_email.php?varios&hueco=1&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>" class="btn btn-primary" >Curso 1</a>
							<input type="hidden" name="curso1" value="<?=$curso1?>"><span><?=$curso1text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_email.php?varios&hueco=2&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>" class="btn btn-primary" >Curso 2</a>
							<input type="hidden" name="curso2" value="<?=$curso2?>"><span><?=$curso2text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_email.php?varios&hueco=3&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>" class="btn btn-primary" >Curso 3</a>
							<input type="hidden" name="curso3" value="<?=$curso3?>"><span><?=$curso3text?></span>
							</label>
						
						</div>

					 </div>
					 	<div class="control-group">
						<div class="controls">
						</div>
						 <div class="control-group">
							<br><br><button class="btn btn-primary" type="submit">Generar email varios cursos</button>
					 </div>
					</fieldset>
				</form>
			</div>
			<!--fin comunicacion-->
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
