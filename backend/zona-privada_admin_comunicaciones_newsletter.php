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
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
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
$curso4 = $_REQUEST['curso4'];
$curso5 = $_REQUEST['curso5'];
$curso6 = $_REQUEST['curso6'];

$publi1 = $_REQUEST['publi1'];
$publi2 = $_REQUEST['publi2'];
$publi3 = $_REQUEST['publi3'];

$trabajo1 = $_REQUEST['trabajo1'];
$trabajo2 = $_REQUEST['trabajo2'];
$trabajo3 = $_REQUEST['trabajo3'];

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

if ($curso4<>""){
	$sql = "SELECT nombre FROM curso WHERE id='$curso4'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$curso4text=$row["nombre"];
}

if ($curso5<>""){
	$sql = "SELECT nombre FROM curso WHERE id='$curso5'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$curso5text=$row["nombre"];
}

if ($curso6<>""){
	$sql = "SELECT nombre FROM curso WHERE id='$curso6'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$curso6text=$row["nombre"];
}

if ($publi1<>""){
	$sql = "SELECT titulo FROM generica WHERE id='$publi1'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$publi1text=$row["titulo"];
}

if ($publi2<>""){
	$sql = "SELECT titulo FROM generica WHERE id='$publi2'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$publi2text=$row["titulo"];
}

if ($publi3<>""){
	$sql = "SELECT titulo FROM generica WHERE id='$publi3'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$publi3text=$row["titulo"];
}

if ($trabajo1<>""){
	$sql = "SELECT denominacion FROM trabajo WHERE id='$trabajo1'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$trabajo1text=$row["denominacion"];
}

if ($trabajo2<>""){
	$sql = "SELECT denominacion FROM trabajo WHERE id='$trabajo2'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$trabajo2text=$row["denominacion"];
}

if ($trabajo3<>""){
	$sql = "SELECT denominacion FROM trabajo WHERE id='$trabajo3'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$trabajo3text=$row["denominacion"];
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
				<h4>Generar newsletter</h4>
				<!-- <form class="form-horizontal" action="__email2_para-acciones.php?accion=< ?=$accion?>&id=< ?=$id?>" method="post" enctype="multipart/form-data">-->
				<form class="form-horizontal" action="zona-privada_admin_comunicaciones_1.php?accion=newsletter" method="post" enctype="multipart/form-data">
				
						</div>
					 </div>
						 <div class="control-group">
							<br><br><strong>Primera fila de cursos</strong>
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=1&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Curso 1</a>
							<input type="hidden" name="curso1" value="<?=$curso1?>"><span><?=$curso1text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=2&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Curso 2</a>
							<input type="hidden" name="curso2" value="<?=$curso2?>"><span><?=$curso2text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=3&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Curso 3</a>
							<input type="hidden" name="curso3" value="<?=$curso3?>"><span><?=$curso3text?></span>	
							<br />
							<br><br><strong>[OPCIONAL] Segunda fila de cursos</strong>
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=4&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Curso 4</a>
							<input type="hidden" name="curso4" value="<?=$curso4?>"><span><?=$curso4text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=5&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Curso 5</a>
							<input type="hidden" name="curso5" value="<?=$curso5?>"><span><?=$curso5text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_email.php?hueco=6&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Curso 6</a>
							<input type="hidden" name="curso6" value="<?=$curso6?>"><span><?=$curso6text?></span>	
							<br />
							<br><br><strong>Fila de publicaciones</strong>
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_publicacion.php?hueco=1&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Publicación 1</a>
							<input type="hidden" name="publi1" value="<?=$publi1?>"><span><?=$publi1text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_publicacion.php?hueco=2&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Publicación 2</a>
							<input type="hidden" name="publi2" value="<?=$publi2?>"><span><?=$publi2text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_publicacion.php?hueco=3&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Publicación 3</a>
							<input type="hidden" name="publi3" value="<?=$publi3?>"><span><?=$publi3text?></span>
							<br />
							<br><br><strong>Fila de ofertas de trabajo</strong>
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_trabajo.php?hueco=1&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Oferta de trabajo 1</a>
							<input type="hidden" name="trabajo1" value="<?=$trabajo1?>"><span><?=$trabajo1text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_trabajo.php?hueco=2&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Oferta de trabajo 2</a>
							<input type="hidden" name="trabajo2" value="<?=$trabajo2?>"><span><?=$trabajo2text?></span>
							<br />
							<br /><a href="zona-privada_admin_comunicaciones_newsletter_trabajo.php?hueco=3&curso1=<?=$curso1?>&curso2=<?=$curso2?>&curso3=<?=$curso3?>&curso4=<?=$curso4?>&curso5=<?=$curso5?>&curso6=<?=$curso6?>&publi1=<?=$publi1?>&publi2=<?=$publi2?>&publi3=<?=$publi3?>&trabajo1=<?=$trabajo1?>&trabajo2=<?=$trabajo2?>&trabajo3=<?=$trabajo3?>" class="btn btn-primary" >Oferta de trabajo 3</a>
							<input type="hidden" name="trabajo3" value="<?=$trabajo3?>"><span><?=$trabajo3text?></span>
							<br />
							<br />
							</label>
						
						</div>
					 </div>
					 	<div class="control-group">
						<div class="controls">
						</div>
						 <div class="control-group">
							<br><br><br><button class="btn btn-primary" type="submit">Generar email newsletter</button>
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
