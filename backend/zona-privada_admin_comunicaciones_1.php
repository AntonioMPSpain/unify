<?
session_start();
include("_funciones.php"); 
include("_cone.php"); 
include("plantillaemail.php");
include("a_insert_emailcron.php");
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



$accion=strip_tags($_GET['accion']);
$texto= $_POST['texto'];
$asunto= ($_POST['asunto']);
$para=strip_tags($_POST['para']);
$tipoEmail=0;
$tipoEmail = strip_tags($_POST['tipoEmail']);

if ($tipoEmail==""){
	$tipoEmail = 0;
}

$idcursoparametro="";
$idcurso="";
$tipoEmail = 4;
if (isset($_GET['idcurso'])){
	$tipoEmail=1;		
	$idcurso=strip_tags($_GET['idcurso']);
	$idcursoparametro="idcurso=$idcurso";
	$asunto= getAsuntoCurso($idcurso);
	$texto = getTextoCurso($idcurso);
}

$idtrabajoparametro="";
$idtrabajo="";
if (isset($_GET['idtrabajo'])){
	$tipoEmail=2;		
	$idtrabajo=strip_tags($_GET['idtrabajo']);
	$idofertaparametro="idtrabajo=$idtrabajo";
	$asunto= getAsuntoTrabajo($idtrabajo);
	$texto = getTextoTrabajo($idtrabajo);
}

$idpublicacionparametro="";
$idpublicacion="";
if (isset($_GET['idpublicacion'])){
	$tipoEmail=3;		
	$idpublicacion=strip_tags($_GET['idpublicacion']);
	$idpublicacionparametro="idpublicacion=$idpublicacion";
	$asunto= getAsuntoPublicacion($idpublicacion);
	$texto = getTextoPublicacion($idpublicacion);
}

if (($accion=="newsletter")||($accion=="varioscursos")){
	$tipoEmail = 4;
}

if (($para=="todos")) { //Todos los usuarios del sistema
	$paratexto=" Todos";
}
if (($para=="registrados")) { //todos los inscritos a un curso
	$paratexto=" Registrados web";
}
if (($para=="suscritos")) {
	$paratexto=" Suscritos web";
}
if (($para=="colegiados")) {
	$paratexto=" Colegiados";
}
if (($para=="nocolegiados")) {
	$paratexto=" No colegiados";
}
if (($para=="activos")) {
	$paratexto=" Activos";
}
if (($para=="noactivos")) {
	$paratexto=" No activos";
}
if (strpos($para,"colegio_")!== false){			// Colegio_ + ID colegio				
	$pieces = explode("_", $para);
	$idcolegio = $pieces[1];
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT * FROM usuario WHERE id='$idcolegio' AND nivel='2' AND borrado=0 ORDER BY nombre DESC, id DESC") ;//or die (pg_error());  
	$row2 = pg_fetch_array($result2);
	$paratexto=" Colegiados de ".$row2["nombre"];
}
if (($para>0)) {
	$paratexto = "Envío manual";
}			

if (isset($_POST['enviarEmail'])){
	
	$usuarios=$para;

	$link=iConectarse();
	$asunto = pg_escape_string($asunto);
	$texto = pg_escape_string($texto);
	$result = posgre_query("INSERT INTO emailhistorial (idusuario,asunto,texto,usuarios,tipo,dominio)
					VALUES ('$idcolegio','$asunto','$texto','$usuarios','$tipoEmail',1) RETURNING id");
				
	//echo pg_last_error(); exit();
				
	$row = pg_fetch_array($result);			
	$idultimo = $row["id"];
		
	procesarEmailCron($idultimo, 0);
	
	$_SESSION['textoEmail']="";	
	$_SESSION[esterror]="Email guardado. Acuda a la sección de 'Comunicaciones' para confirmar el envío.";	
	
	header("Location: zona-privada_admin_comunicaciones_5_historico-de-envios.php");
	exit();
	
}




elseif ($accion=="previsualizar"){


	include("plantillaweb01admin.php");

	$_SESSION['textoEmail']=$texto;	
	
	?>
	<script  type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
		<h2 class="titulonoticia">Comunicaciones</h2>
		<br />
		<br />
		<div class="bloque-lateral comunicacion">		
			<h4>Enviar nuevo mensaje</h4>
			<!-- <form class="form-horizontal" action="__email2_para-acciones.php?accion=< ?=$accion?>&id=< ?=$id?>" method="post" enctype="multipart/form-data">-->
			<form class="form-horizontal" action="zona-privada_admin_comunicaciones_1.php" method="post" enctype="multipart/form-data">
			<br><div class=\"bloque-lateral comunicacion\">		
				<div class=\"alert alert-info\">
					<p><strong>Asunto: </strong><?=$asunto?></p>
					<p><strong>Destinatarios: </strong><?=$paratexto?></p>
					<p><strong>Previsualización del mensaje:</strong></p>
					<iframe src="plantillaemail.php?plantilla"></iframe>
				</div>
			</div>
			
			<input type="hidden" id="textoEmail" name="texto" value="<?=htmlentities($texto)?>">
			<input type="hidden" id="asuntoEmail" name="asunto" value="<?=htmlentities($asunto)?>">
			<input type="hidden" id="paraEmail" name="para" value="<?=$para?>">
			<input type="hidden" id="tipoEmail" name="tipoEmail" value="<?=$tipoEmail?>">
			<br>
			<input id="modificarEmail" name="modificarEmail" type="submit" class="btn btn btn-primary" value="Modificar"></a>		
			<input id="enviarEmail" name="enviarEmail" onclick="return confirmar('&iquest;Desea guardar el email? Posteriormente tendrá que confirmar el envío en la sección comunicaciones')" class="btn btn-success" value="Guardar Email" type="submit">
	
	
	
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
}
else{

	$fecha = date("d/m/Y");
	
	if ($accion=="newsletter"){
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
		$asunto = "[activatie newsletter] Novedades Formación, Publicaciones y Trabajo. $fecha";
		$texto = getNewsletter($curso1, $curso2, $curso3, $curso4, $curso5, $curso6, $publi1, $publi2, $publi3, $trabajo1, $trabajo2, $trabajo3);
	
	}
	elseif ($accion=="varioscursos"){
		
		$curso1 = $_REQUEST['curso1'];
		$curso2 = $_REQUEST['curso2'];
		$curso3 = $_REQUEST['curso3'];
		$asunto = "[activatie cursos] Novedades Formación. $fecha";
		$texto = getVariosCursos($curso1, $curso2, $curso3);
	}
	
	
	
	include("plantillaweb01admin.php");

?>
	
	<script  type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<!--Arriba plantilla1-->
		<div class="grid-9 contenido-principal">
		<div class="clearfix"></div>
		<div class="pagina zonaprivada blog">
			<h2 class="titulonoticia">Comunicaciones</h2>
			<br />
			<div class="bloque-lateral acciones">		
						<p><strong>Acciones:</strong>
							<a class="btn btn-success" href="zona-privada_admin_comunicaciones_5_historico-de-envios.php">Histórico de Mensajes Enviados <i class="icon-calendar"></i></a> <br />
						</p>
			</div>
			<!--fin acciones-->
			<br />
			<div class="bloque-lateral comunicacion">		
				<h4>Enviar nuevo mensaje</h4>
				<!-- <form class="form-horizontal" action="__email2_para-acciones.php?accion=< ?=$accion?>&id=< ?=$id?>" method="post" enctype="multipart/form-data">-->
				<form class="form-horizontal" action="zona-privada_admin_comunicaciones_1.php?accion=previsualizar" method="post" enctype="multipart/form-data">
					<fieldset>
						
						 <div class="control-group">
							<label class="control-label" for="asunto"><strong>Título del mensaje:</strong></label>
						<div class="controls">
							<input class="input-xxlarge" type="text" id="asunto" name="asunto" placeholder="Título del mensaje" value="<?=$asunto?>" />
						</div>
					 </div>
						 <div class="control-group">
							<label class="control-label"><strong>Destinatarios:</strong></label>
						<div class="controls">
							<select name="para" class="input-xlarge">
									<!--<option value="s">Suscritos web</option>   
									<option value="nc">No colegiados</option>   -->
								<?
								//"para" contiene:
									//0-->TODOS
									//(interger)-->id del colegio --> A todos sus colegiado
									//s-->Suscritos web
									//nc-->No colegiados
								//"paracurso"
								//"parafiltro"
								
									if ($_SESSION[nivel]==2) { //Admin Colegio
										$link=iConectarse();
										$idcc=$_SESSION[idcolegio];
										$result=pg_query($link,"SELECT * FROM usuario WHERE id='$idcc' AND nivel='2' AND borrado=0 ORDER BY nombre DESC, id DESC") ;//or die (pg_error());  
										while($row = pg_fetch_array($result)) { 
											?><option <? if ($para==$row["id"]) {echo "selected";}?> value="colegio_<?=$row["id"]?>"><?=$row["nombre"]?></option><?
										}
									}elseif ($_SESSION[nivel]==1) { //Admin total
										$link=iConectarse();
										$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='2' AND borrado=0 ORDER BY nombre DESC, id DESC") ;//or die (pg_error());  
										?>  
										<option <? if ($para=="todos") {echo "selected";}?> value="todos">Todos los usuarios de ACTIVATIE</option>   
										<option <? if ($para=="registrados") {echo "selected";}?> value="registrados">Registrados web</option>   
										<option <? if ($para=="suscritos") {echo "selected";}?> value="suscritos">Suscritos web</option>  
										<option <? if ($para=="colegiados") {echo "selected";}?> value="colegiados">Colegiados</option> 
										<option <? if ($para=="nocolegiados") {echo "selected";}?> value="nocolegiados">No colegiados</option>
										<option <? if ($para=="activos") {echo "selected";}?> value="activos">Activos</option> 
										<option <? if ($para=="noactivos") {echo "selected";}?> value="noactivos">No activos</option>
										<?
										while($row = pg_fetch_array($result)) {
											$coleg=$row["id"]; 
											?><option <? if ($para=="colegio_$coleg") {echo "selected";}?> value="<?="colegio_".$coleg?>"><?=$row["nombre"]?></option><?
										}
									}
									?>	
							</select>
								
						</div>
					 </div>
						 <div class="control-group">
							<label class="control-label"><strong>Texto del Mensaje:</strong></label>
						<div class="controls">
							<textarea class="span5" name="texto"><?=$texto?></textarea>
												<script>
													window.onload = function() {
														
														CKEDITOR.replace( 'texto',{toolbar :[['Undo','Redo', '-','Bold', 'Italic', '-', 'NumberedList', '-', 'Link', '-','Image', '-', 'Source']]} );
													};
												</script>							
							<br><div><b>Comodines(solo en texto email):</b>
							<p>%%nombre%% : nombre del alumno<br>
							%%apellidos%% : apellidos del alumno</p>
							<div>
						</div>

					 </div>
					 	<div class="control-group">
						<div class="controls">
							<input type="hidden" id="tipoEmail" name="tipoEmail" value="<?=$tipoEmail?>" />
						</div>
						 <div class="control-group">
							<button class="btn  btn-primary" type="submit">Previsualizar mensaje</button>
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

<? } ?>

<?
include("plantillaweb02admin.php"); 
?>
