<?
/*
 * function matricula_usuario_curso matricula un usuario a curso  o suspende matricula anterior
 * Recibe: id moodle del curso --> int 
 * Recibe: id moodle del usuario --> int
 * Recibe: rol con el que se quieren dar los permisos -->int
 * Devuelve: 1 si se ha asignado y 0 si ha habido error
 * @param $id_curso int identificador del curso en moodle
 * @param $id_usuario int identificador del usuario en moodle
 * @param $rol int (5 estudiante, 3 profesoreditor, 4 profesor normal) rol que se le da al usuario
 * @param $f_inicio timestamp (opcional) fecha inicio de la matricula/permisos
 * @param $f_fin timestamp (opcional) fecha fin de la matricula/permisos
 * @param $suspendido int opcional (1 suspendido, 0 activo)
 * @return integer devuelve 1 si éxito y 0 si ha habido error
*/
  
session_start();
////////// Filtros de nivel por usuario //////////////////////
if ($_SESSION[nivel]==2) { //Admin Colegio

}
elseif ($_SESSION[nivel]==1) { //Admin Total

}
else{
	$_SESSION[esterror]="Su sesion ha expirado";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

require_once('lib_actv_api.php');
include("_funciones.php"); 
include("_cone.php"); 
include_once "a_api_emails.php";
include_once "plantillaemail.php";

$safe="gestión de curso usuario";
$idusuario=strip_tags($_REQUEST['idusuario']);
$idcurso=strip_tags($_REQUEST['idcurso']);
if ($idcurso=="") {
	echo "Error en parametros";
	exit();
}

if (isset($_GET['cursodual'])){
	$cursodual=1;
	$getcursodual="&cursodual";
}
else{
	$cursodual=0;
	$getcursodual="";
}



$estadoDistinto = false;
$cambioMultiple = false;
$cambioMultipleDiploma=false;
$cambioMultipleFinAcceso=false;
/** REMITENTES **/
$tabla="
	<table class=\"align-center\">
	<tr>
		<th>Nombre</th>
		<th>Apellidos</th>
	</tr>
";

$numUsuarios=0;
$postsUsuarios="";
foreach($_POST as $key => $value){
   $pos=strpos($key,"EM_");
   if($pos!==false){
		
		if (isset($_REQUEST['estadoDiploma'])){
			$cambioMultipleDiploma=true;
		}
		elseif(isset($_REQUEST['cambiarFinAcceso'])){
			$cambioMultipleFinAcceso=true;
		}
		else{
			$cambioMultiple=true;
		}
		
		$numUsuarios++;
		$pieces = explode("_", $key);
		$idusuario = $pieces[1];
		
		$postsUsuarios.="<input type=\"hidden\" name=\"EM_$idusuario\" id=\"EM_$idusuario\">";
		$result = posgre_query("SELECT * FROM usuario WHERE id='$idusuario'");
		if ($row = pg_fetch_array($result)){
			$nombre = $row["nombre"];
			$apellidos = $row["apellidos"];
			
			$tabla.="
				<tr>
				<td>$nombre</td>
				<td>$apellidos</td>
				</tr>
			";
		}

		$result = posgre_query("SELECT * FROM curso_usuario WHERE borrado=0 AND idcurso='$idcurso' AND idusuario='$idusuario'");
		if ($row = pg_fetch_array($result)){
			$estadoActual = $row["estado"];
			$esperaActual = $row["espera"];
			if ($numUsuarios==1){
				$estado = $estadoActual;
				$espera = $esperaActual;
			}
			if (($estado != $estadoActual)||($espera != $esperaActual)){
				$estadoDistinto=true;
			}
			
		}
   }
}
$tabla.="</table>";


$idusuariomoodle=strip_tags($_REQUEST['idusuariomoodle']);
$idcursomoodle=strip_tags($_REQUEST['idcursomoodle']);

$linka=iConectarse(); 
$rowcurso=pg_query($linka,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
$curso= pg_fetch_array($rowcurso);
$modalidad = $curso["modalidad"];

$accion=strip_tags($_REQUEST['accion']); 
$est=strip_tags($_GET['est']);
$est_texto=strip_tags($_GET['est_texto']);
$enmoodle=strip_tags($_REQUEST['enmoodle']);
$envioemail=strip_tags($_REQUEST['envioemail']);
$checkboxIframeEspera=strip_tags($_REQUEST['checkboxIframeEspera']);
$checkboxIframeBaja=strip_tags($_REQUEST['checkboxIframeBaja']);
$checkboxIframeDiploma=strip_tags($_REQUEST['checkboxIframeDiploma']);
$checkboxIframeDiplomaNOApto=strip_tags($_REQUEST['checkboxIframeDiplomaNOApto']);

if (($accion=="guardarm") && ($cambioMultipleFinAcceso)){
	
	foreach($_POST as $key => $value){
		$accion="";
		$est_texto="Cambio de fecha fin de acceso múltiple: No se ha guardado correctamente";
		
		$fechalimiterealizacion=strip_tags($_POST['fechalimiterealizacion']); if ($fechalimiterealizacion=="") { $fechalimiterealizacion='NULL'; } else { $fechalimiterealizacion="'".$fechalimiterealizacion."'"; }
		
		$pos=strpos($key,"EM_");
		if($pos!==false){
			
			$pieces = explode("_", $key);
			$idusuario = $pieces[1];	
	
			$ssql="UPDATE curso_usuario SET fechalimitepermanente=$fechalimiterealizacion WHERE borrado=0 AND idcurso ='$idcurso' AND idusuario='$idusuario';";	
			$link=iConectarse(); 
			$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
	
		}
	}
	if ($Query){
		$est_texto="Cambio de fecha fin de acceso múltiple: Se ha editado correctamente.";
	}
	$_SESSION[esterror]=$est_texto;
	header("Location: zpa_usuario_curso.php?idcurso=$idcurso$getcursodual"); 
	exit();	
	
	
	
	
	
}
if (($accion=="guardarm") && ($cambioMultipleDiploma)){
	foreach($_POST as $key => $value){
		$accion="";
		$est_texto="Cambio de estado múltiple de diploma: No se ha guardado correctamente";
		$diploma=strip_tags($_POST['diploma']);
		
		$pos=strpos($key,"EM_");
		if($pos!==false){
			
			$pieces = explode("_", $key);
			$idusuario = $pieces[1];	
	
	
	
	
			$ssql="UPDATE curso_usuario SET diploma='$diploma' WHERE borrado=0 AND idcurso ='$idcurso' AND idusuario='$idusuario';";	
			$link=iConectarse(); 
			$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
	
	
			if ($Query){
				
				$diplomaAntiguo=strip_tags($_POST['diplomaAntiguo']);
		
				if (($checkboxIframeDiploma=="checkboxIframeDiploma")&&($diplomaAntiguo<>1)&&($diploma==1)){		// Enviar email automático dado de baja
					$exito = enviarEmailDarDiploma($idusuario, $idcurso);
				}
				
				if (($checkboxIframeDiplomaNOApto=="checkboxIframeDiplomaNOApto")&&($diplomaAntiguo<>-1)&&($diploma==-1)){		// Enviar email automático dado de baja
					$exito = enviarEmailDarDiplomaNOApto($idusuario, $idcurso);
				}
				
			}
	
	
		}
	}
	if ($Query){
		$est_texto="Cambio de estado múltiple de diploma: Se ha editado correctamente.";
	}
	$_SESSION[esterror]=$est_texto;
	header("Location: zpa_usuario_curso.php?idcurso=$idcurso$getcursodual"); 
	exit();	
	
	
}

if (($accion=="guardarm") && ($cambioMultiple)){
	
	$accion="";
	$est_texto="Cambio de estado múltiple: No se ha guardado correctamente";
	$estado=strip_tags($_POST['estado']);
	$estadoAntiguo=strip_tags($_POST['estadoAntiguo']);
	$esperaAntiguo=strip_tags($_POST['esperaAntiguo']);

	if ($estado==2){
		$estado=0;
		$espera=1;
	}
	else{
		$espera=0;
	}
	
	foreach($_POST as $key => $value){
		$pos=strpos($key,"EM_");
		if($pos!==false){
			
			$pieces = explode("_", $key);
			$idusuario = $pieces[1];	
			
				if (($checkboxIframeBaja=="checkboxIframeBaja")&&($estadoAntiguo=="0")&&($estado==1)){		// Enviar email automático dado de baja
					$exito = enviarEmailDarDeBaja($idusuario, $idcurso);

				}
				elseif (($checkboxIframeEspera=="checkboxIframeEspera")&&($esperaAntiguo==1)&&($espera==0)&&($estado==0)){	// Enviar email automático pasa a inscrito
					$exito = enviarEmailListaDeEsperaAInscrito($idusuario, $idcurso);
				}

				$ssql="UPDATE curso_usuario SET espera = '$espera', estado = '$estado' WHERE borrado=0 AND idcurso ='$idcurso' AND idusuario='$idusuario';";	
				$link=iConectarse(); 
				$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
			
		}
	}
	if ($Query){
		$est_texto="Cambio de estado múltiple: Se ha editado correctamente.";
	}
	$_SESSION[esterror]=$est_texto;
	header("Location: zpa_usuario_curso.php?idcurso=$idcurso$getcursodual"); 
	exit();
}
elseif($accion=="guardarm"){
	$accion="";
	$est_texto="No se ha guardado correctamente";
	$precio=strip_tags($_POST['precio']); if ($precio=="") $precio=0;
	$pagado=strip_tags($_POST['pagado']);
	$estado=strip_tags($_POST['estado']);
	$estadoAntiguo=strip_tags($_POST['estadoAntiguo']);
	$esperaAntiguo=strip_tags($_POST['esperaAntiguo']);
	$observaciones=strip_tags($_POST['observaciones']);
	$diploma=strip_tags($_POST['diploma']);
	$modopago=strip_tags($_POST['modopago']);
	$presencialonline=strip_tags($_POST['presencialonline']);
	$fechalimiterealizacion=strip_tags($_POST['fechalimiterealizacion']); if ($fechalimiterealizacion=="") { $fechalimiterealizacion='NULL'; } else { $fechalimiterealizacion="'".$fechalimiterealizacion."'"; }
	$fechahora="'".($_POST['fechahora'])."'";
	if ($fechahora=="''") $fechahora="NULL";
	if ($presencialonline==""){ $presencialonline=0; }
	if ($estado==2){
		$estado=0;
		$espera=1;
	}
	else{
		$espera=0;
	}
		
	if (($checkboxIframeBaja=="checkboxIframeBaja")&&($estadoAntiguo=="0")&&($estado==1)){		// Enviar email automático dado de baja
		$exito = enviarEmailDarDeBaja($idusuario, $idcurso);

	}
	elseif (($checkboxIframeEspera=="checkboxIframeEspera")&&($esperaAntiguo==1)&&($espera==0)&&($estado==0)){	// Enviar email automático pasa a inscrito
		$exito = enviarEmailListaDeEsperaAInscrito($idusuario, $idcurso);
	}
	
	$diplomaAntiguo=strip_tags($_POST['diplomaAntiguo']);
	
	if (($checkboxIframeDiploma=="checkboxIframeDiploma")&&($diplomaAntiguo<>1)&&($diploma==1)){		// Enviar email automático dado de baja
		$exito = enviarEmailDarDiploma($idusuario, $idcurso);
	}
	
	if (($checkboxIframeDiplomaNOApto=="checkboxIframeDiplomaNOApto")&&($diplomaAntiguo<>-1)&&($diploma==-1)){		// Enviar email automático dado de baja
		$exito = enviarEmailDarDiplomaNOApto($idusuario, $idcurso);
	}
	
	//$ssql="UPDATE curso_usuario SET fechahora=$fechahora,observaciones='$observaciones', pagado = '$pagado',estado = '$estado', precio = '$precio', idmoodle='$idcursomoodle' WHERE borrado=0 AND idcurso ='$idcurso' AND idusuario='$idusuario';";	
	$ssql="UPDATE curso_usuario SET inscripciononlinepresencial='$presencialonline', diploma='$diploma', fechahora=$fechahora,observaciones='$observaciones',espera = '$espera', estado = '$estado', precio = '$precio', fechalimitepermanente=$fechalimiterealizacion, tipoinscripcion='$modopago' WHERE borrado=0 AND idcurso ='$idcurso' AND idusuario='$idusuario';";	
	$link=iConectarse(); 
	$Query = pg_query($link, $ssql);// or die ("E1".mysql_error()); 
$error = pg_last_error();
	if ($Query){
		if ($precio==0){
		$ssql="UPDATE curso_usuario SET pagado='-1', tipoinscripcion='-1' WHERE borrado=0 AND idcurso ='$idcurso' AND idusuario='$idusuario';";	
		$link=iConectarse(); 
		$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
		}
		
		if (($precio>0)&&($pagado=="-1")){
			
			$ssql="UPDATE curso_usuario SET pagado='0', tipoinscripcion='$modopago' WHERE borrado=0 AND idcurso ='$idcurso' AND idusuario='$idusuario';";	
			$link=iConectarse(); 
			$Query = pg_query($link, $ssql);// or die ("E1".mysql_error()); 

		}
		$est_texto="Se ha editado correctamente.";
	}
	
	$_SESSION[esterror]=$est_texto.$error ;
	header("Location: zpa_usuario_curso.php?idcurso=$idcurso$getcursodual"); 
	exit();
	
}//Fin de accion==guardar






if($accion==""){
	$titulo1="alta";
}elseif($accion=="editar"){
	$titulo1="editar";
}
$titulo1="formación ";
$titulo2="administración";
include("plantillaweb01admin.php"); 
?>
			<!--Arriba pantilla1-->
			<div class="grid-8 contenido-principal">
				<div class="clearfix"></div>
				<div class="pagina blog">
				<h2 class="titulonoticia"><?=$curso["nombre"];?></h2>
				<br>
				<!--Acciones-->
				<div class="acciones">		
				<p>
					<a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> |
				</p>
				</div>
				<!--fin acciones-->
					<? include("_aya_mensaje_session.php"); ?>
					<? 
					
					if (($idcurso<>"")&&($idusuario<>"")) {
						$link=iConectarse(); 
						$result=pg_query($link,"SELECT * FROM curso_usuario WHERE borrado=0 AND idcurso='$idcurso' AND idusuario='$idusuario' ORDER BY id DESC LIMIT 1;"); 
						$cur = pg_fetch_array($result);						
						//$i=$row["id"];
						$pagado=$cur["pagado"];
						$estado=$cur["estado"];
						$precio=$cur["precio"];
						$espera=$cur["espera"];
						$observaciones=$cur["observaciones"];
						$fechahora=($cur["fechahora"]);
						$diploma = $cur["diploma"];
						$presencialonline = $cur["inscripciononlinepresencial"];
						$modopago = $cur["tipoinscripcion"];
						$fechalimiterealizacion = $cur["fechalimitepermanente"];
						
						$sql = "SELECT * FROM usuario WHERE id='$idusuario'";
						$result = posgre_query($sql);
						$row = pg_fetch_array($result);
						$nombre = $row["nombre"];
						$apellidos = $row["apellidos"];
						
						
						
					}else{
						echo"No ha seleccionado ningún usuario. Vuelva atrás y seleccione los usuarios deseados.";
						exit();							
					}	
					
					if ($estadoDistinto){
						echo "No puede seleccionar usuarios con estados distintos. Vuelva a atrás y seleccione usuarios Inscritos, En lista de espera o Baja por separado.";
					}
					else{
							
					?>
						<form action="zona-privada_admin_usuario_curso2.php?accion=guardarm&enmoodle=<?=$enmoodle?>&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario?>&idcursomoodle=<?=$idcursomoodle?>&idusuariomoodle=<?=$idusuario.$getcursodual?>" method="post" enctype="multipart/form-data">										   
						<fieldset>				    
							
							<? if (($cambioMultiple)||($cambioMultipleDiploma)||($cambioMultipleFinAcceso)){ ?>			
								<legend>Usuarios a cambiar estado <? if ($cambioMultipleDiploma) { echo ' del diploma'; } elseif($cambioMultipleFinAcceso){ echo ' fin de acceso'; } ?></legend>
								<?=$tabla?>
								<legend>Datos</legend>
							<? } ?>
							
							<? if ((!$cambioMultiple)&&(!$cambioMultipleDiploma)&&(!$cambioMultipleFinAcceso)){ ?>
							<legend>Datos <?=$nombre." ".$apellidos?></legend>
							<? if ($est=="ko"){ ?><span class="rojo">Error</span><? }?>
							<? if ($pagado==1) { $disabled=""; } else { $disabled=""; }?>
							<div class="control-group">
								<label class="control-label" for="inputName">Precio:</label>
									<div class="controls">
										<input <?=$disabled?> type="number" min=0 id="inputName" class="input-mini" name="precio" value="<?=$precio?>" />
									</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="inputName">Forma de pago:</label>
								<select <?=$disabled?> name="modopago" class="input-large" >
									<option class="input-large" value="0" <? if ($modopago==0) echo " selected "; ?>>Transferencia</option>
									<option class="input-large" value="1" <? if ($modopago==1) echo " selected "; ?>>Tarjeta</option>
									<option class="input-large" value="2" <? if ($modopago==2) echo " selected "; ?>>Domicializacion</option>
									<option class="input-large" value="-1" <? if ($modopago==-1) echo " selected "; ?>>Ninguno</option>
								</select>
							</div>
								
							<? } ?>


							<? if ((!$cambioMultiple)&&(!$cambioMultipleDiploma)&&(!$cambioMultipleFinAcceso)){ ?>						
									<div class="control-group">
										<label class="control-label" for="2">Fecha inscripción:</label>
											<div class="controls">
												<input id="fechahora" type="datetime" id="2" class="input-normal" name="fechahora" value="<?=$fechahora?>"  />
											</div>
									</div>
									
								<?  if ($modalidad==2){ ?>
													
									<div class="control-group">
									<label class="control-label" for="inputName">Modalidad:</label>
										<div class="controls">
											<select id="presencialonline" name="presencialonline" class="input-xlarge" >
												<option class="input-xlarge" value="1" <? if ($presencialonline==1) echo " selected "; ?> >PRESENCIAL</option>
												<option class="input-xlarge" value="2" <? if ($presencialonline==2) echo " selected "; ?> >ON-LINE</option>
												
											</select>
										</div>
									</div>	
								
								
								<? } ?>
							
							<? } ?>
							<? if ((!$cambioMultiple)&&(!$cambioMultipleDiploma)){ ?>					
								<div class="control-group">
									<label class="control-label" for="2"> Fecha fin de acceso: (si aquí no existe fecha se tendrá en cuenta la fecha fin del curso. hay que tener en cuenta que a la fecha se sumarán 30 días de margen de acceso)</label>
										<div class="controls">
											<input <? if ($modalidad==3) { echo 'required'; } ?> id="fechalimiterealizacion" type="date" class="input-normal" name="fechalimiterealizacion" value="<?=$fechalimiterealizacion?>"  />
										</div>
								</div>
								
							<? } ?>

							<? if ((!$cambioMultipleDiploma)&&(!$cambioMultipleFinAcceso)){ ?>
								<div class="control-group">
									<label class="control-label" for="inputName">Estado:</label>
										<div class="controls">
											<select id="select_estado" name="estado" class="input-xlarge" >
												<option class="input-xlarge" value="0" <? if (($estado==0) AND ($espera==0)) echo " selected "; ?>>INSCRITO</option>
												<option class="input-xlarge" value="2" <? if ($espera==1) echo " selected "; ?>>LISTA DE ESPERA</option>
												<option class="input-xlarge" value="1" <? if ($estado==1) echo " selected "; ?>>BAJA</option>
											</select>
										</div>
								</div>	
							<? } ?>
							<? if ((!$cambioMultiple)&&(!$cambioMultipleFinAcceso)){ ?> 
								<div class="control-group">
									<label class="control-label" for="2"></label>
										<div class="controls">

											Diploma: 
												&nbsp;<input <? if ($diploma==0) echo 'checked' ?> id="diploma" class="diploma"  type="radio" class="input-normal" name="diploma" value="0"  /> Sin calificar<br>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input <? if ($diploma==1) echo 'checked' ?> id="diploma" class="diploma" type="radio" class="input-normal" name="diploma" value="1"  /> Apto<br>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input <? if ($diploma==-1) echo 'checked' ?> id="diploma" class="diploma" type="radio" class="input-normal" name="diploma" value="-1"  /> NO Apto<br>
										
										</div>
								</div>
								
								
								
								
								
								
							<? } ?>
							
							<? if ((!$cambioMultiple)&&(!$cambioMultipleDiploma)&&(!$cambioMultipleFinAcceso)){ ?> 
								<div class="control-group">
									<label class="control-label" for="observaciones">Observaciones(no visible por el usuario):</label>
										<div class="controls">
											<textarea name="observaciones" id="observaciones" class="inputtextarea input-xxlarge" cols="45" rows="3" ><?=$observaciones?></textarea>
										</div>
								</div>
							<? } ?>
							<? 
							
								
								$asuntoDiploma = getAsuntoDarDiploma($idcurso);
								$textoDiploma = getTextoDarDiploma($idusuario, $idcurso);
														
								$text = urlencode($textoDiploma);
								$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
								 ?>	
								<br><div hidden="hidden" id="iframeDiploma" class="bloque-lateral comunicacion">	
									<br><br><label>Está otorgando al usuario <b>calificación APTO</b>. El usuario recibirá el siguiente email automático:</label>
									<br>
									<input checked value="checkboxIframeDiploma" type="checkbox" id="checkboxIframeDiploma" name="checkboxIframeDiploma"> 
									<span>Enviar email automático. Si no desea que lo reciba, desmarque esta opción.</span>
									<br>	
									<br>	
									<br>	
									<div class="alert alert-info">
										<p><strong>Asunto:</strong> <?=$asuntoDiploma?></p>
										<p><strong>Mensaje:</strong></p>
										<iframe srcdoc="<?=htmlentities($textoemailenvio)?>"></iframe>
									</div>
								</div>
								
								<? 
								
								$asuntoDiplomaNOApto = getAsuntoDarDiplomaNOApto($idcurso);
								$textoDiplomaNOApto = getTextoDarDiplomaNOApto($idusuario, $idcurso);
														
								$text = urlencode($textoDiplomaNOApto);
								$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
								 ?>	
								<br><div hidden="hidden" id="iframeDiplomaNOApto" class="bloque-lateral comunicacion">	
									<br><br><label>Está otorgando al usuario <b>calificación NO APTO</b>. El usuario recibirá el siguiente email automático:</label>
									<br>
									<input checked value="checkboxIframeDiplomaNOApto" type="checkbox" id="checkboxIframeDiplomaNOApto" name="checkboxIframeDiplomaNOApto"> 
									<span>Enviar email automático. Si no desea que lo reciba, desmarque esta opción.</span>
									<br>	
									<br>	
									<br>	
									<div class="alert alert-info">
										<p><strong>Asunto:</strong> <?=$asuntoDiplomaNOApto?></p>
										<p><strong>Mensaje:</strong></p>
										<iframe srcdoc="<?=htmlentities($textoemailenvio)?>"></iframe>
									</div>
								</div> 
								
								
							<?
							
							
							
							
							$asuntoEspera = getAsuntoListaDeEsperaAInscrito($idcurso);
							$textoEspera = getTextoListaDeEsperaAInscrito($idusuario, $idcurso);
													
							$text = urlencode($textoEspera);
							$textoemailenvio = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
							 ?>	
							<br><div hidden="hidden" id="iframeEspera" class="bloque-lateral comunicacion">	
								<br><br><label>Está cambiando al usuario de <b>lista de espera a inscrito</b>. El usuario recibirá el siguiente email automático:</label>
								<br>
								<input checked value="checkboxIframeEspera" type="checkbox" id="checkboxIframeEspera" name="checkboxIframeEspera"> 
								<span>Enviar email automático. Si no desea que lo reciba, desmarque esta opción.</span>
								<br>	
								<br>	
								<br>	
								<div class="alert alert-info">
									<p><strong>Asunto:</strong> <?=$asuntoEspera?></p>
									<p><strong>Mensaje:</strong></p>
									<iframe srcdoc="<?=htmlentities($textoemailenvio)?>"></iframe>
								</div>
							</div>
							
							<? 
							
							
							$asuntoBaja = getAsuntoDarDeBaja($idcurso);
							$textoBaja = getTextoDarDeBaja($idusuario, $idcurso);
							$text = urlencode($textoBaja);
							$textoemailenvio2 = file_get_contents("https://www.activatie.org/web/plantillaemail.php?plantilla&texto=".$text);
							 ?>	
							<br><div  hidden="hidden" id="iframeBaja" class="bloque-lateral comunicacion">	
								<br><br><label>Está dando de <b>baja</b> al usuario en el curso. El usuario recibirá el siguiente email automático:</label>	
								<br>	
								<input checked value="checkboxIframeBaja" type="checkbox" id="checkboxIframeBaja" name="checkboxIframeBaja"> 
								<span>Enviar email automático. Si no desea que lo reciba, desmarque esta opción.</span>
								<br>	
								<br>	
								<br>	
								<div class="alert alert-info">
									<p><strong>Asunto:</strong> <?=$asuntoBaja?></p>
									<p><strong>Mensaje:</strong></p>
									<iframe srcdoc="<?=htmlentities($textoemailenvio2)?>"></iframe>
								</div>
							</div>
						
							<? if ($cambioMultipleDiploma){ ?>
								<input id="estadoDiploma" type="hidden" id="estadoDiploma" name="estadoDiploma" value="1"  />
							<? } ?>
							<? if ($cambioMultipleFinAcceso){ ?>
								<input id="cambiarFinAcceso" type="hidden" id="cambiarFinAcceso" name="cambiarFinAcceso" value="1"  />
							<? } ?>
							<input id="estadoAntiguo" type="hidden" id="estadoAntiguo" name="estadoAntiguo" value="<?=$estado?>"  />
							<input id="esperaAntiguo" type="hidden" id="esperaAntiguo" name="esperaAntiguo" value="<?=$espera?>"  />
							<input id="diplomaAntiguo" type="hidden" id="diplomaAntiguo" name="diplomaAntiguo" value="<?=$diploma?>"  />
							<input id="pagado" type="hidden" id="pagado" name="pagado" value="<?=$pagado?>"  />
							
							<?=$postsUsuarios?>
							<!--<div class="control-group">
								<label class="control-label" for="inppagado">Notificar estado por email:</label>
									<div class="controls">
										<select name="envioemail" class="input-large" >
											<option class="input-large" value="0" selected="selected">NO</option>
											<option class="input-large" value="1">SI</option>
										</select>
									</div>
							</div>-->							
						</fieldset>
						<div class="form-actions">
							<? if ($titulo1=="editar") { $textboton="Guardar cambios";} else{ $textboton="Guardar";}?>
							<button type="submit" class="btn btn-primary btn-large"><?=$textboton?></button>
						</div>
						</form>
						
					<? } ?>
					
				</div>
				<!--fin pagina blog-->
				<div class="clearfix"></div>
			</div>
			<!--fin grid-8 contenido-principal-->
			<!--Abajo pantilla2-->
	<?
include("plantillaweb02admin.php"); 
?>