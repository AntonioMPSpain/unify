<?php
include("_funciones.php");
include("_cone.php");
include("a_insert_emailcron.php");

$accion=$_GET['accion'];
$est=$_REQUEST['est'];
$idcurso=trim($_REQUEST['idcurso']);

if (isset($_GET['cursodual'])){
	$cursodual=1;
	$getcursodual="&cursodual";
}
else{
	$cursodual=0;
	$getcursodual="";
}

if (($idcurso=="")){
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
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$idcolegio=strip_tags($_SESSION[idusuario]);
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$envioGenerico = $_POST['envioGenerico'];		// email o sms
$idtipo=strip_tags($_REQUEST['tipo']);			// idemail o idsms

$titulo1="Usuarios";
$titulo2="cursos";
	
if ($envioGenerico=="Envío Email"){
	$submit="Email";
	$thTipo = "Email";
}
elseif($envioGenerico=="Envío SMS"){
	$submit="SMS";
	$thTipo = "Móvil";
}


/** REMITENTES **/
$tabla="
	<table class=\"align-center\">
	<tr>
		<th>Nombre</th>
		<th>Apellidos</th>
		<th>$thTipo</th>	
	</tr>
";

$numUsuarios=0;
$postsUsuarios="";
foreach($_POST as $key => $value){

   $pos=strpos($key,"EM_");
   if($pos!==false){
		
		$numUsuarios++;
		$pieces = explode("_", $key);
		$idusuario = $pieces[1];
		
		$result = posgre_query("SELECT * FROM usuario WHERE id='$idusuario'");
		if ($row = pg_fetch_array($result)){
			$nombre = $row["nombre"];
			$apellidos = $row["apellidos"];
			$email = $row["email"];
			$telefono = $row["telefono"];
			if ($submit=="Email"){
				$campoenvio=$email;
			}
			elseif($submit=="SMS"){
				$campoenvio=$telefono;
			}
			
			
			$postsUsuarios.="<input type=\"hidden\" name=\"EM_$idusuario\" id=\"EM_$idusuario\">";
			
			$tabla.="
				<tr>
				<td>$nombre</td>
				<td>$apellidos</td>
				<td>$campoenvio</td>
				</tr>
			";
		}	
   }
}

$tabla.="</table>";
	

/** ENVIO SMSs **/

if (isset($_POST['enviarSMS'])){
	
	$idsms=$idtipo;
	$texto=trim($_REQUEST['textoSMS']);
	$usuarios="";
	$telefonos="";
	$usuarioactivo=$_SESSION[idusuario];
	
	foreach($_POST as $key => $value){

   		$pos=strpos($key,"EM_");
   		if($pos!==false){
   		
			$pieces = explode("_", $key);
			$idusuario = $pieces[1];
			
			$result = posgre_query("SELECT telefono FROM usuario WHERE id='$idusuario'");
			if ($row = pg_fetch_array($result)){
				$telefono = $row["telefono"];
			}
			
			if ($telefono<>""){
				$textosms = utf8_decode($texto);
				$textosms = urlencode($textosms);	
				$url = "https://sms.arsys.es/smsarsys/accion/enviarSms2.jsp?id=activatie@coaatmu.es&phoneNumber=".$telefono."&psw=activa15B2&textSms=".$textosms."&remite=activatie";
				$response = file_get_contents($url);

				$usuarios.=$idusuario.";";
				$telefonos.=$telefono.";";
				
			}
		}
	}	
			
	posgre_query("INSERT INTO smshistorial (idsms,idusuario,texto,usuarios,telefonos)
					VALUES ('$idsms','$usuarioactivo','$texto','$usuarios','$telefonos')");
	
	$_SESSION[esterror]="SMS enviados correctamente.";	
	
	header("Location: zpa_usuario_curso.php?idcurso=$idcurso$getcursodual");
	exit();
	
}

/** ENVIO Emails **/

elseif (isset($_POST['enviarEmail'])){
	
	$idemail=$idtipo;
	$asunto=($_REQUEST['asuntoEmail']);
	$texto=($_REQUEST['textoEmail']);
	$usuarios="";
	
	foreach($_POST as $key => $value){

   		$pos=strpos($key,"EM_");
   		if($pos!==false){
   		
			$pieces = explode("_", $key);
			$idusuario = $pieces[1];
			
			$result = posgre_query("SELECT nombre FROM usuario WHERE id='$idusuario'");
			if ($row = pg_fetch_array($result)){
				$nombre = $row["nombre"];
			}
			
			$usuarios.=$idusuario.",";				// Separado por comas para poder hacer luego un WHERE IN (...)
		
		}
	}	
	
	$ultimo = substr($usuarios, -1);
	if ($ultimo==","){
		$usuarios = substr($usuarios, 0, -1);
	}
	
	
	$result = posgre_query("INSERT INTO emailhistorial (idusuario,asunto,texto,usuarios,tipo,dominio)
					VALUES ('$idcolegio','$asunto','$texto','$usuarios',1,1) RETURNING id");
				
	$row = pg_fetch_array($result);			
	$idultimo = $row["id"];
	
	procesarEmailCron($idultimo, 1);
	
	$_SESSION[esterror]="Email guardado. Acuda a la sección de 'Comunicaciones' para confirmar el envío.";	
	
	header("Location: zpa_usuario_curso.php?idcurso=$idcurso$getcursodual");
	exit();
	
}

/** Modificar Email **/

elseif (isset($_POST['modificarEmail'])){
	
	
	$asuntoEmail=html_entity_decode($_POST['asuntoEmail']);
	$textoEmail=html_entity_decode($_POST['textoEmail']);

	include("plantillaweb01admin.php"); 
	?>
	<!--Arriba plantilla1-->
	
		<script  type="text/javascript" src="ckeditor/ckeditor.js"></script>
		<div class="grid-9 contenido-principal">
		<div class="clearfix"></div>
		<div class="pagina zonaprivada blog">
			<h2 class="titulonoticia">Comunicaciones</h2>
			<form class="form-horizontal" action="z_usuario_curso_comunicacion.php?modificado&idcurso=<?=$idcurso.$getcursodual?>" method="post" enctype="multipart/form-data">
			<br>
			<input  type="submit" id="nomodificar" name="nomodificar" value="Volver" class="btn btn-primary">
			<br>
			<br />
			<div class="bloque-lateral comunicacion">		
				<h4>Modificar mensaje</h4>
				<!-- <form class="form-horizontal" action="__email2_para-acciones.php?accion=< ?=$accion?>&id=< ?=$id?>" method="post" enctype="multipart/form-data">-->
				<fieldset>				    
					<? if ($est=="ko"){ ?><span class="rojo">Error</span><? }?>
					<? if ($est=="ok"){ ?><span class="rojo">Guardado</span><? }?>
					<div class="control-group">
						<label class="control-label" for="inputsme">Asunto email:</label>
							<div class="controls">
								<input type="text" class="input-xxlarge" name="asunto" value="<?=$asuntoEmail?>">
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputsme">Texto email:</label>
							<div class="controls">
								<textarea class="span5" rows="10" name="texto"><?=$textoEmail?></textarea>
											<script>
												window.onload = function() {
													CKEDITOR.replace( 'texto',{toolbar :[['Undo','Redo', '-', 'Bold', 'Italic', '-','NumberedList', '-', 'Link', '-', 'Source']]} );
												};
											</script>
							<br><div><b>Comodines(solo en texto email):</b>
							<p>%%nombre%% : nombre del alumno<br>
							%%apellidos%% : apellidos del alumno<br>
							%%curso%% : nombre del curso<br>
							%%horas%% : duración del curso<br>
							%%colegio%% : organizador del curso<br>
							%%emailcolegio%% : email del organizador del curso<br>
							%%fechainicio%% : fecha de inicio del curso</p>
							<div>
							</div>
							</div>
							
					<input type="hidden" name="envioGenerico" value="<?=$envioGenerico?>" />
					<input type="hidden" name="tipo" value="<?=$idtipo?>" />
					<input type="hidden" name="asuntoAntiguo" value="<?=htmlentities($asuntoEmail)?>" />
					<input type="hidden" name="textoAntiguo" value="<?=htmlentities($textoEmail)?>" />
					<?=$postsUsuarios?>

						 <div class="control-group">
						 <input  type="submit" id="nomodificar" name="nomodificar" value="Volver sin modificar" class="btn btn-primary">
						 <input type="submit" value="Previsualizar email"  class="btn  btn-success"/>
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
}

else{


	
	
	
	/** CONTENIDO **/
	
	$select="";
	$textoEnvio="";
	
	if ($submit=="Email"){
		
		$selected="";
		if ($idtipo=='-1'){
			$selected="selected";
		}
		
		$email=posgre_query("SELECT * FROM email WHERE paracurso=1 AND borrado=0;") ;
		$select.="<option $selected value=\"-1\">Nuevo Email</option>";
		while ($emailrow = pg_fetch_array($email)){
			$nombre = $emailrow['asunto'];
			$idemail = $emailrow['id'];
			$selected="";
			if ($idemail==$idtipo){
				$selected="selected";
			}
			$select.="<option $selected value=\"$idemail\">$nombre</option>";
		}
		

		
		if ($idtipo!=0){
			
			if (isset($_GET['modificado'])){
				
				if (isset($_POST['nomodificar'])){
					$asunto = html_entity_decode($_POST['asuntoAntiguo']);
					$texto = html_entity_decode($_POST['textoAntiguo']);
				}
				else{				
					$asunto = html_entity_decode($_POST['asunto']);
					$texto = html_entity_decode($_POST['texto']);
				}
		    }
			elseif ($idtipo=='-1'){
				$asunto="[activatie] ¡¡¡¡¡¡INSERTAR ASUNTO DESEADO!!!!";
				$texto="NUEVO EMAIL. PULSE MODIFICAR PARA ESCRIBIR EL ASUNTO Y TEXTO QUE DESEE";
			}

			else{
				$email=posgre_query("SELECT * FROM email WHERE id='$idtipo' AND paracurso=1 AND borrado=0;") ;
				if ($email = pg_fetch_array($email)){
					$asunto = html_entity_decode($email['asunto']);
					$texto = html_entity_decode($email['texto']);
				}
			}
			
			$result = posgre_query("SELECT * FROM curso WHERE id='$idcurso' AND borrado=0");
			if ($curso = pg_fetch_array($result)){
				$nombreCurso = $curso['nombre'];
				$duracionCurso = $curso['duracion'];
				$fechaInicioCurso = cambiaf_a_normal($curso['fecha_inicio']);
				$idcolegioCurso = $curso['idcolegio'];
				$result2 = posgre_query("SELECT * FROM encuestas WHERE estado=1 AND idcurso='$idcurso' AND borrado=0");
				$numencuestas = pg_num_rows($result2);
				if ($encuesta = pg_fetch_array($result2)){
					$idencuesta = $encuesta['id'];
					$tokenacceso = $encuesta['tokenacceso'];
					$linkencuesta = "https://www.activatie.org/web/encuesta.php?id=$idencuesta&t=$tokenacceso";
				}
			}
			
			if (strpos($texto,"%%curso%%") !== false) {
				$texto = str_replace("%%curso%%", $nombreCurso, $texto);
			}
			if (strpos($texto,"%%idcurso%%") !== false) {
				$texto = str_replace("%%idcurso%%", $idcurso, $texto);
			}
			if (strpos($texto,"%%horas%%") !== false) {
				$texto = str_replace("%%horas%%", $duracionCurso, $texto);
			}
			if (strpos($texto,"%%fechainicio%%") !== false) {
				$texto = str_replace("%%fechainicio%%", $fechaInicioCurso, $texto);
			}	
			if (strpos($texto,"%%linkencuesta%%") !== false) {
				if ($numencuestas>1){
					$texto = str_replace($texto, "ERROR!. MÁS DE UNA ENCUESTAS EN EL MISMO CURSO. VAYA A LA ZONA DE ADMINISTRACIÓN DE ENCUESTAS Y DEJE SOLO 1 ENCUESTA ABIERTA", $texto);
				}
				elseif ($numencuestas==0){	
					$texto = str_replace($texto, "ERROR!. NO HAY NINGUNA ENCUESTA ACTIVA PARA ESTE CURSO", $texto);				
				}
				else{
					$texto = str_replace("%%linkencuesta%%", $linkencuesta, $texto);
				}
			}	
			
			$result = posgre_query("SELECT * FROM usuario WHERE id='$idcolegioCurso'");
				if ($cole = pg_fetch_array($result)){
					$coleNombre = $cole['nombre'];
					$coleEmail = $cole['email'];
			}
					
			if (strpos($texto,"%%colegio%%") !== false) {
				$texto = str_replace("%%colegio%%", $coleNombre, $texto);
			}				
			
			if (strpos($texto,"%%emailcolegio%%") !== false) {
			
				$coleEmail = "<a href=\"mailto:$coleEmail\">$coleEmail</a>";
				$texto = str_replace("%%emailcolegio%%", $coleEmail, $texto);
			}				
			
			
			
			$_SESSION['textoEmail']=$texto;	
			$textoEnvio .= "<br><div class=\"bloque-lateral comunicacion\">		
				<div class=\"alert alert-info\">
					<p><strong>Asunto:</strong> $asunto</p>
					<p><strong>Previsualización del mensaje:</strong></p>
					<iframe src=\"plantillaemail.php?plantilla\"></iframe>
				</div>
			</div>
			
			<input type=\"hidden\" id=\"asuntoEmail\" name=\"asuntoEmail\" value=\"".htmlentities($asunto)."\">
			<input type=\"hidden\" id=\"textoEmail\" name=\"textoEmail\" value=\"".htmlentities($texto)."\">
			<input id=\"modificarEmail\" name=\"modificarEmail\" type=\"submit\" class=\"btn btn btn-primary\" value=\"Modificar\"></a>		
			<input id=\"enviarEmail\" name=\"enviarEmail\" onclick=\"return confirmar('&iquest;Desea guardar el email? Posteriormente tendrá que confirmar el envío en la sección comunicaciones')\" class=\"btn btn-success\" value=\"Guardar Email\" type=\"submit\">
			
			
			";
		
		}
	}
	elseif($submit=="SMS"){
		$sms=posgre_query("SELECT * FROM sms WHERE paracurso=1 AND borrado=0;") ;
		$selected="";
		while ($smsrow = pg_fetch_array($sms)){
			$nombre = $smsrow['nombre'];
			$idsms = $smsrow['id'];
			$selected="";
			if ($idsms==$idtipo){
				$selected="selected";
			}
			$select.="<option $selected value=\"$idsms\">$nombre</option>";
		}
		$selected="";
		if ($idtipo=='-1'){
			$selected="selected";
		}
		
		$select.="<option $selected value=\"-1\">Nuevo SMS</option>";
		
		if ($idtipo!=0){
		
			if ($idtipo=='-1'){
				$texto="";
			}
			else{
				$sms=posgre_query("SELECT * FROM sms WHERE id='$idtipo' AND paracurso=1 AND borrado=0;") ;
				if ($sms = pg_fetch_array($sms)){
					$texto = $sms['texto'];
				}
			}
			
			$textlen = strlen($texto);
			$caracteresRestantes=140-$textlen;
			
			$textoEnvio = "<br><label>Texto SMS: </label>";
			$textoEnvio .= "<input type=\"text\" style=\"width:80%;\" id=\"textoSMS\" name=\"textoSMS\" value=\"$texto\" class=\"input-xxlarge\">";
			$textoEnvio .= "<br><div id=\"characterLeft\">$caracteresRestantes caracteres restantes</div>";		
			$textoEnvio .= "<br><br><input id=\"enviarSMS\" name=\"enviarSMS\" onclick=\"return confirmar('&iquest;Seguro que desea enviar $numUsuarios SMS?')\" class=\"btn btn-success\" value=\"Enviar $numUsuarios SMS\" type=\"submit\">";
				
		}
	}
	
	
	
	
	include("plantillaweb01admin.php"); 
	
	
	?>
	<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Envío de <?=$submit;?></h2>
	<br>
	<!--Acciones-->
	<div class="acciones">		
	<p>
		<a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
	</div>
	<!--fin acciones-->
	
	
	<?
	
	if ($numUsuarios>0){								// No ha seleccionado ningun usuario
		?>
		

		<h2>Contenido</h2>
		<br>
		<form enctype="multipart/form-data" method="post" action="z_usuario_curso_comunicacion.php?idcurso=<?=$idcurso.$getcursodual?>" class="form-horizontal">
			<fieldset>
			<label>Plantillas de <?=$submit?>:</label>
					<select class="input-xlarge" name="tipo" onchange="this.form.submit()" >
					<option value="0"></option>
					<?=$select?>
					</select>
			</fieldset>
			<input type="hidden" name="envioGenerico" value="<?=$envioGenerico?>" />
			<?=$postsUsuarios?>
			<?	
				if ($textoEnvio!=""){
					echo $textoEnvio;
				}
			?>
		</form>
		<br><h2>Remitentes</h2>
		<?=$tabla?>
		<?
	
	}
	else{
		echo "No ha seleccionado ningún usuario. Vuelva atrás y seleccione a los usuarios deseados.";
	}

?>



<div id="volverarriba">
	<hr />
	<a href="#" title="Volver al inicio de la página">Volver arriba <i class="icon-circle-arrow-up"></i></a>
</div>
<br />
<? } ?>
</div>
<div class="clearfix"></div>
</div>
<!--fin pagina-->
<?

include("plantillaweb02admin.php"); 



?>