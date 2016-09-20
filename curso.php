<?
include("_funciones.php");  
include("_cone.php"); 
include_once "a_curso_plazas_libres.php";
include_once "a_api_emails.php";
//$c_email="info@tuedificioenforma.es";
//$c_web="tuedificioenforma.es";  
$estaccion="";
$c_directorio_img = "/var/www/web";
session_start();

$accion=strip_tags(cleanInput($_GET['accion'])); 
$id=cleanInput(trim(strip_tags($_GET['id']))+0);

if ($accion=="consulta"){
	
	$_SESSION[esterror]="Fallo al enviar la consulta";
	$idcolegio = $_REQUEST['idcolegio'];
	if (($id>0)&&($idcolegio>0)){
		$nombre = $_REQUEST['nombre'];
		$email = $_REQUEST['email'];
		$consulta = $_REQUEST['consulta'];
		
		$sql = "SELECT nombre FROM curso WHERE id='$id'";
		$result = posgre_query($sql);
		$row = pg_fetch_array($result);
		$nombrecurso = $row["nombre"];
		
		$para = getEmail($idcolegio);
		$asunto = "[activatie] Tiene una consulta en una actividad";
		$cuerpo = "";
		$cuerpo .= "Actividad: ".$nombrecurso."<br>";
		$cuerpo .= "Usuario: ".$nombre."<br>";
		$cuerpo .= "Email: ".$email."<br>";
		$cuerpo .= "Consulta: ".$consulta."<br>";
		if (enviarEmail($para, $asunto ,$cuerpo)){
			$_SESSION[esterror]="Consulta enviada correctamente. En breves nos pondremos en contacto.";
		}	
	}
	header("Location: curso.php?id=$id");
	exit();
}



$est=strip_tags($_GET['est']);
if (ctype_digit($id)&&($id>0)) { //consiste completamente de dígitos.
	//if ($id==""){
	//echo "No parametros";
	//exit();
}
$hoybd= date("Y-m-d");
$link=conectar();
$result=pg_query($link,"SELECT * FROM curso WHERE borrado=0 AND id='$id' ORDER BY fecha_fin_publicacion DESC, id DESC");// or die (mysql_error());  
if(pg_num_rows($result)!=0) {
	$row=pg_fetch_array($result);
	//Plazas del curso para el tip de inscripcion
	$plazas=$row["plazas"];
	$plazaso=$row["plazaso"];
	$idcolegio=$row["idcolegio"];
	$plazasperma=$row["plazasperma"];
	$video=$row["video"];
	$modalidad=$row["modalidad"];
	$fecha_fin_inscripcion=$row["fecha_fin_publicacion"];					
	$privado = $row["privado"];	
	
	/** Precio tachados **/
	$preciotachadoc=recortardecimales($row["preciotachadoc"], "."); if ($preciotachadoc=="") $preciotachadoc=0;
	$preciotachadon=recortardecimales($row["preciotachadon"], "."); if ($preciotachadon=="") $preciotachadon=0;
	$preciotachadooc=recortardecimales($row["preciotachadooc"], "."); if ($preciotachadooc=="") $preciotachadooc=0;
	$preciotachadoon=recortardecimales($row["preciotachadoon"], "."); if ($preciotachadoon=="") $preciotachadoon=0;
	$preciotachadopc=recortardecimales($row["preciotachadopc"], "."); if ($preciotachadopc=="") $preciotachadopc=0;
	$preciotachadopn=recortardecimales($row["preciotachadopn"], "."); if ($preciotachadopn=="") $preciotachadopn=0;
	/** **/
	
	if ($modalidad==2){
		$plazasLibresPresencial = getPlazasLibresPresencial($id);
		$plazasLibresOnline = getPlazasLibresOnline($id);
	}
	else{
		$plazasLibres = getPlazasLibres($id);
	}

	$privadotexto = "";
	if ($privado==1){
		$privadotexto = " row blog-item privado ";
	}
	
	$safe=$row["nombre"];
	$titulo1="actividad";
	$titulo2="formativa";
	include("plantillaweb01.php");
	//include("conweb.php"); ?>
<!--Arriba-->
<div class="grid-8 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina blog <?=$privadotexto?>">
	
	<? include("_aya_mensaje_session.php"); ?>
	<h2 class="titulonoticia"><?=$row["nombre"]?></h2>
	
	<!--<p class="fechanoticia"><i class="icon-calendar"></i>Fin de inscripción <? /*=cambiaf_a_normal($row["fecha_fin_inscripcion"])*/?></p>-->
	 <?
	if (($video<>"")) {
		?>
			<p><?=$video?></p>
		<? 
	}else {
		$destino=$c_directorio_img."/imagen/".$row["imagen2"];
		if(file_exists($destino)&&($row["imagen2"]<>"")){ //comprobamos que existe la foto
			?>
			<p><a href="imagen/<?=$row["imagen2"]?>" rel="shadowbox"><img src="imagen/<?=$row["imagen2"]?>"  alt="<?=$row["nombre"]?>" /></a></p>
			<?
		}
	}
	
	
	if ($row["modalidad"]==0) {
		$textomodalidad=" On-line ";
		$linkmodalidad = "formacion.php";
	} 
	if ($row["modalidad"]==1) {
		$textomodalidad=" Presencial ";
		$linkmodalidad = "formacion.php";
		
	}
	if ($row["modalidad"]==2) {
		$textomodalidad=" Presencial y on-line ";
		$linkmodalidad = "formacion.php";
		
	}
	if ($row["modalidad"]==3) {
		$textomodalidad=" Permanente ";
		$linkmodalidad = "formacion.php?m=permanente";
	}
	
	$fecha_fin_publicacion = $row['fecha_fin_publicacion'];
	$hoy = date('Y-m-d');
	if ($fecha_fin_publicacion < $hoy){
		$cerrado=true;
	}
	else{
		$cerrado=false;
	}
	
	
	?>
	<div class="breadcrumb post-details grids detallescurso">
		<div class="grid-7">	
			<dl>
				<dt class="curso-tipo"><i class="icon-file"></i> Tipo:</dt>
				<dd><a href="#"><? 
				if ($row["tipo"]==0) echo " Curso "; 
				if ($row["tipo"]==1) echo " Curso universitario "; 
				if ($row["tipo"]==2) echo " Taller "; 
				if ($row["tipo"]==3) echo " Seminario "; 
				if ($row["tipo"]==4) echo " Jornada "; 
				?></a></dd>
				<dt class="curso-modalidad"><i class="icon-edit"></i> Modalidad:</dt>
				<dd><a href="<?=$linkmodalidad?>"><?=$textomodalidad?></a></dd>	
				<? if ($row["fecha_inicio"]<>""){ ?>
					<dt class="curso-fechas"><i class="icon-calendar"></i> Fecha inicio:</dt>
					<dd><?=cambiaf_a_normal($row["fecha_inicio"])?></dd>		
				<? }?>
				<? if ($row["fecha_fin_publicacion"]<>""){ ?>
					<dt class="curso-fechas"><i class="icon-calendar"></i> Fin de inscripción:</dt>
					<dd><?=cambiaf_a_normal($row["fecha_fin_publicacion"])?></dd>							
				<? }?>
				
				<? if ($modalidad==3){ ?> 
					<dt class="curso-duracion"><i class="icon-time"></i> Plazo de realización:</dt>
					<dd><?=$row["plazopermanente"]?> días </dd>									
				
				<? } ?>
				
				<dt class="curso-duracion"><i class="icon-time"></i> Duración:</dt>
				<dd><?=$row["duracion"]?> horas <? if ($row["duracionminutos"]>0) echo "y ".$row["duracionminutos"]." minutos" ?></dd>									
				
				
			</dl>
		</div>
		<!--fin detalles-left-->	
		<div class="grid-5">	
			<dl>
				<? if ($modalidad==2){ ?>
					<dt class="curso-estado"><i class="icon-asterisk"></i> Estado online:</dt>
					<dd><? 
					$textolistaesperaonline="";
					$textolistaesperapresencial="";
					
					if (($plazasLibresOnline>0)&&(!$cerrado)) {
						?><span">Plazas Libres</span> <i class="icon-ok"></i><?
					}else{
						if ($cerrado){
							
							?><span">Cerrado</span><i class="icon-remove"></i><? 
						}
						else{
							$textolistaesperaonline=" EN LISTA DE ESPERA";
							?><span">Plazas Agotadas</span><i class="icon-remove"></i><? 
						}
						
					}?></dd>
					
					<dt class="curso-estado"><i class="icon-asterisk"></i> Estado presencial:</dt>
					<dd><? 
					if (($plazasLibresPresencial>0)&&(!$cerrado)) {
						?><span">Plazas Libres</span> <i class="icon-ok"></i><?
					}else{
						
						if ($cerrado){
							
							?><span">Cerrado</span><i class="icon-remove"></i><? 
						}
						else{
							
							$textolistaesperapresencial=" EN LISTA DE ESPERA";
							?><span>Plazas Agotadas</span> <i class="icon-remove"></i><? 
						}
						
					}?></dd>
				<? 
				} 
				else {
					$textolistaespera="";
					?>
					<dt class="curso-estado"><i class="icon-asterisk"></i> Estado:</dt>
					<dd><? 
					if (($plazasLibres>0)&&(!$cerrado)) {
						?><span">Plazas Libres</span> <i class="icon-ok"></i><?
					}else{
						if ($cerrado){
							
							?><span">Cerrado</span><i class="icon-remove"></i><? 
						}
						else{
							$textolistaespera=" EN LISTA DE ESPERA";
							?><span">Plazas Agotadas</span> <i class="icon-remove"></i><? 
						}
					}?></dd>
					<? 
				} 
				
				
				?>
				
				
				<? /*
				<dt class="curso-organizador"><i class="icon-briefcase"></i> Organizador:</dt>
				<dd><a href="#"><abbr title="<?
									// Genera
									$idcolegio=$row["idcolegio"];
										$consulta = "SELECT * FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;";
										$link=conectar(); 
										$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
										if($rowdg= pg_fetch_array($r_datos)) {	
											echo $rowdg['nombre'];
										} 
								
								?>"><?=$rowdg['nombre'];?></abbr></a></dd> 
				*/ ?>
				<? if ($row["lugar"]<>""){ ?>
					<dt class="curso-duracion"><i class="icon-time"></i> Lugar:</dt>
					<dd><?=$row["lugar"]?></dd>		
				<? }?>		
				<?
				
				$link2=iConectarse(); 
				$result2=pg_query($link2,"SELECT id,archivo,nombre FROM archivo WHERE padre='$id' AND borrado='0' AND programa=1;");// or die (mysql_error());  
				
				if (($result2)&&(pg_num_rows($result2)!=0)) {
							while($row2= pg_fetch_array($result2)) {
									$archivo = $c_directorio_img."/files/".$row2["archivo"];
									//--------------------------------------------------------------------------------------
									if(file_exists($archivo)&&($row2["archivo"]<>"")){
										$extension = filetype ($archivo);
										$exte = explode(".",$row2["archivo"]);
										$extension=$exte[1];
										?> 
										
										<dt class="curso-descarga"><i class="icon-download"></i> <a href="descarga.php?documento=<?=$row2["archivo"]?>">Descargar programa</a></dt>
										<dd></dd>
										
										<?
									}
							}
							?>
			
					<!--fin adjuntosnoticia-->
					<?
				}?>
		

				<dt class="curso-consulta"><i class="icon-comment"></i> <a href="#comentarios">¡Consúltanos!</a></dt>
				<dd></dd>				
			</dl>
		</div>
		<!--fin detalles right-->
		
	
		<div class="clearfix"></div>

		
		
		
		<ul>				
			<? 
		    $linket=iConectarse(); 
		    $resultet=pg_query($linket,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$id' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
			if (pg_num_rows($resultet)>0){?>
				<li class="post-tags"><span>Áreas:</span>
					<ul>
					<? 
						while($rowet = pg_fetch_array($resultet)) { 
							?>
							<li>
								<!--<a href="formacion.php?idetiqueta=<?=$rowet["id"]?>" class="tag" title="clic para ver cursos con etiqueta <?=$rowet["texto"]?>">-->
								<span class="arrow"></span>
								<span class="text"><?=$rowet["texto"]?></span>
								<span class="end"></span>
								<!--</a>-->
							</li> 
							<? 
						}?>
					</ul>
				</li>
				<!--fin post-tags áreas-->
			<? }?>	
		</ul>
			
		
		
		
		<div class="post-details grids detallescurso precio-inscripcion">
			<? if(($data[nivel] == '4')||($data[nivel] == '3')) { // Alumno o Profe y logueado 
				$idusuario=$_SESSION[idusuario];


			} 
			$idusuario=$_SESSION[idusuario];
			$privado = $row['privado'];
			if (($row["modalidad"]==2)&&($row['estado']==2)){ //online y presencial
				if ($fecha_fin_inscripcion>=$hoybd){
				?>
				<div class="post-details grids detallescurso precio-inscripcion">
		
					<div class="grid-7"><h4 style="vertical-align:sub">PRECIO ON-LINE</h4>
						<dl>
							<?
							$preciopromocion=false;
							$precioco = recortardecimales($row['precioco'],".");
							 if ($precioco!=""){ 
								$preciotexto="";
								if ($precioco==0){
									$preciotexto="Gratuito";
								}
								elseif ($precioco>0){
									$preciotexto=$precioco."€";
								}
								
								if ($preciotachadooc==0){
									$preciotachadotexto="";
								}
								else{
									$preciopromocion=true;
									$preciotachadotexto=" <strike>".$preciotachadooc."€</strike>*";
								}
							?>
								<dt class="curso-precio"><i class="icon-shopping-cart"></i> Colegiado:</dt>
								<dd><?=$preciotexto;?><?=$preciotachadotexto;?></dd>
							<? 
							} 
							
							if ($privado!=1){			// Privado(solo colegiados)
							
								$preciono = recortardecimales($row['preciono'],".");
								 if ($preciono!=""){ 
									$preciotexto="";
									if ($preciono==0){
										$preciotexto="Gratuito";
									}
									elseif ($preciono>0){
										$preciotexto=$preciono."€";
									}
									
									if ($preciotachadoon==0){
										$preciotachadotexto="";
									}
									else{
										$preciopromocion=true;
										$preciotachadotexto=" <strike>".$preciotachadoon."€</strike>*";
									}
									
								?>
									<dt class="curso-precio"><i class="icon-shopping-cart"></i> No colegiado:</dt>
									<dd><?=$preciotexto;?><?=$preciotachadotexto;?></dd>
								<? 
								} 
							}
							?>
							
							
							
							
							
							
						</dl>
						<? 
						
						if ($preciopromocion){ 
							?> *Precio en promoción <br><?
					 	} 
						
						if ($row['estado']==2) { //estado del curso
							$idalumno=$_SESSION[idusuario]; 
							if (($_SESSION[nivel]==4)&&($idalumno<>"")) { //Alumno
								$sql=" idusuario='$idalumno' AND ";
								$link=iConectarse();
								$result=pg_query($link,"SELECT * FROM curso_usuario WHERE nivel='5' AND idcurso='$id' AND $sql borrado=0 ORDER BY fechahora DESC, id DESC") ;//or die (pg_error());  
								if(pg_num_rows($result)==0) {
									?>
										<?
										if ($row["modalidad"]==2){
											?>
											<a href="prescripcion.php?id=<?=$row['id'];?>&inscripcion=2"  class="btn btn-important">INSCRIPCIÓN ON-LINE<?=$textolistaesperaonline?></a>
											<?
										}
										?>
									<? 
								}else{
									$rowcu = pg_fetch_array($result);
									$estado=($rowcu["estado"]); //estado de la inscripcion
									$espera=($rowcu["espera"]); //estado de la inscripcion
									?>
									<div class="grid-12">	
										<div>Usted ya se encuentra <? if (($estado==0)&&($espera==0)){ ?> MATRICULADO <? }else{ ?> INSCRITO <? }?></div>
										<a href="curso_inscritos.php?idcurso=<?=$row['id'];?>" class="btn btn-important">Lista de matriculados</a>
									</div>
									<? 
								}					
							}else{
								?>
									<?
									if ($row["modalidad"]==2){
										?>
										<a href="prescripcion.php?id=<?=$row['id'];?>&inscripcion=2"  class="btn btn-important">INSCRIPCIÓN ON-LINE<?=$textolistaesperaonline?></a>
										<?
									}
									?>
								<? 
							}
						}?>
					</div>
					
					<div class="grid-5"><h4	style="vertical-align:sub">PRECIO PRESENCIAL</h4>
						<dl>
							<?
							$preciopromocion=false;
							$precioco = recortardecimales($row['precioc'],".");
							 if ($precioco!=""){ 
								$preciotexto="";
								if ($precioco==0){
									$preciotexto="Gratuito";
								}
								elseif ($precioco>0){
									$preciotexto=$precioco."€";
								}
								
								if ($preciotachadoc==0){
									$preciotachadotexto="";
								}
								else{
									$preciopromocion=true;
									$preciotachadotexto=" <strike>".$preciotachadoc."€</strike>*";
								}
							?>
								<dt class="curso-precio"><i class="icon-shopping-cart"></i> Colegiado:</dt>
								<dd><?=$preciotexto;?><?=$preciotachadotexto;?></dd>
							<? 
							} 
							
							if ($privado!=1){			// Privado(solo colegiados)
								
								 $preciono=recortardecimales($row['precion'],".");
								 if ($preciono!=""){ 
									$preciotexto="";
									if ($preciono==0){
										$preciotexto="Gratuito";
									}
									elseif ($preciono>0){
										$preciotexto=$preciono."€";
									}
									
									if ($preciotachadon==0){
										$preciotachadotexto="";
									}
									else{
										$preciopromocion=true;
										$preciotachadotexto=" <strike>".$preciotachadon."€</strike>*";
									}
								
								?>
									<dt class="curso-precio"><i class="icon-shopping-cart"></i> No colegiado:</dt>
									<dd><?=$preciotexto;?><?=$preciotachadotexto;?></dd>
								<? 
								} 
							}
							
							?>
						</dl>
						<? 
						
						if ($preciopromocion){ 
							?> *Precio en promoción <br><?
					 	} 
						if ($row['estado']==2) { //estado del curso
							$idalumno=$_SESSION[idusuario]; 
							if (($_SESSION[nivel]==4)&&($idalumno<>"")) { //Alumno
								$sql=" idusuario='$idalumno' AND ";
								$link=iConectarse();
								$result=pg_query($link,"SELECT * FROM curso_usuario WHERE nivel='5' AND idcurso='$id' AND $sql borrado=0 ORDER BY fechahora DESC, id DESC") ;//or die (pg_error());  
								if(pg_num_rows($result)==0) {
									?>
										<a href="prescripcion.php?id=<?=$row['id'];?>&inscripcion=1"  class="btn btn-important">INSCRIPCIÓN PRESENCIAL<?=$textolistaesperapresencial?></a>
									<? 
								}else{
									$rowcu = pg_fetch_array($result);
									$estado=($rowcu["estado"]); //estado de la inscripcion
									$espera=($rowcu["espera"]); //estado de la inscripcion
									/* ?>
									<div class="grid-12">	
										<div>Usted ya se encuentra <? if (($estado==0)&&($espera==0)){ ?> MATRICULADO <? }else{ ?> INSCRITO <? }?></div>
										<a href="curso_inscritos.php?idcurso=<?=$row['id'];?>" class="btn btn-important">Lista de matriculados</a>
									<? */
								}					
							}else{
								?>
									<a href="prescripcion.php?id=<?=$row['id'];?>&inscripcion=1"  class="btn btn-important">INSCRIPCIÓN PRESENCIAL<?=$textolistaesperapresencial?></a>
								<? 
							}
						}?>						
					</div>
				
				
		
		
				</div>
				<!--fin precio-inscripción-->
				<?
				
				}//fin de $fecha_fin_inscripcion>hoybd
			}elseif (($row["modalidad"]==3)&&($row['estado']==2)){  //permanente
				if ($fecha_fin_inscripcion>=$hoybd){
				?>
				<div class="post-details grids detallescurso precio-inscripcion">
		
					<div class="grid-7"><h4 style="vertical-align:sub">PRECIO PERMANENTE</h4>
						<dl>
							<?
							$preciopromocion=false;
							$precioco = recortardecimales($row['preciocp'],".");
							 if ($precioco!=""){ 
								$preciotexto="";
								if ($precioco==0){
									$preciotexto="Gratuito";
								}
								elseif ($precioco>0){
									$preciotexto=$precioco."€";
								}
								
								if ($preciotachadopc==0){
									$preciotachadotexto="";
								}
								else{
									$preciopromocion=true;
									$preciotachadotexto=" <strike>".$preciotachadopc."€</strike>*";
								}								
							
							?>
								<dt class="curso-precio"><i class="icon-shopping-cart"></i> Colegiado:</dt>
								<dd><?=$preciotexto;?><?=$preciotachadotexto?></dd>
							<? 
							} 
							
							if ($privado!=1){			// Privado(solo colegiados)
								
								$preciono = recortardecimales($row['precionp'],".");
								 if ($preciono!=""){ 
									$preciotexto="";
									if ($preciono==0){
										$preciotexto="Gratuito";
									}
									elseif ($preciono>0){
										$preciotexto=$preciono."€";
									}
									
									if ($preciotachadopn==0){
										$preciotachadotexto="";
									}
									else{
										$preciopromocion=true;
										$preciotachadotexto=" <strike>".$preciotachadopn."€</strike>*";
									}	
								?>
								
									<dt class="curso-precio"><i class="icon-shopping-cart"></i> No colegiado:</dt>
									<dd><?=$preciotexto;?><?=$preciotachadotexto?></dd>
								<? 
								} 
							}
							?>
						</dl>
						<? 
						
						if ($preciopromocion){ 
							?> *Precio en promoción <br><?
					 	}
						 
						if ($row['estado']==2) { //estado del curso
							$idalumno=$_SESSION[idusuario]; 
							if (($_SESSION[nivel]==4)&&($idalumno<>"")) { //Alumno
								$sql=" idusuario='$idalumno' AND ";
								$link=iConectarse();
								$result=pg_query($link,"SELECT * FROM curso_usuario WHERE nivel='5' AND idcurso='$id' AND $sql borrado=0 ORDER BY fechahora DESC, id DESC") ;//or die (pg_error());  
								if(pg_num_rows($result)==0) {
									?>
										<?
										if ($row["modalidad"]==3){
											?>
											<a href="prescripcion.php?id=<?=$row['id'];?>"  class="btn btn-important">INSCRIPCIÓN<?=$textolistaespera?></a>
											<?
										}
										?>
									<? 
								}else{
									$rowcu = pg_fetch_array($result);
									$estado=($rowcu["estado"]); //estado de la inscripcion
									$espera=($rowcu["espera"]); //estado de la inscripcion
									?>
									<div class="grid-12">	
										<div>Usted ya se encuentra <? if (($estado==0)&&($espera==0)){ ?> MATRICULADO <? }else{ ?> INSCRITO <? }?></div>
										<a href="curso_inscritos.php?idcurso=<?=$row['id'];?>" class="btn btn-important">Lista de matriculados</a>
									</div>
									<? 
								}					
							}else{
								?>
									<?
									if ($row["modalidad"]==3){
										?>
										<a href="prescripcion.php?id=<?=$row['id'];?>"  class="btn btn-important">INSCRIPCIÓN<?=$textolistaespera?></a>
										<?
									}
									?>
								<? 
							}
						}?>
					</div>

		
				</div>
				<!--fin precio-inscripción-->
				<?
				}
			}elseif (($row["modalidad"]==0)&&($row['estado']==2)){  //online
				if ($fecha_fin_inscripcion>=$hoybd){
				?>
				<div class="post-details grids detallescurso precio-inscripcion">
		
					<div class="grid-7"><h4 style="vertical-align:sub">PRECIO ONLINE</h4>
						<dl>
							<?
							$preciopromocion=false;
							$precioco = recortardecimales($row['precioco'],".");
							 if ($precioco!=""){ 
								$preciotexto="";
								if ($precioco==0){
									$preciotexto="Gratuito";
								}
								elseif ($precioco>0){
									$preciotexto=$precioco."€";
								}
								if ($preciotachadooc==0){
									$preciotachadotexto="";
								}
								else{
									$preciopromocion=true;
									$preciotachadotexto=" <strike>".$preciotachadooc."€</strike>*";
								}
							?>
								<dt class="curso-precio"><i class="icon-shopping-cart"></i> Colegiado:</dt>
								<dd><?=$preciotexto;?><?=$preciotachadotexto;?></dd>
							<? 
							} 
							
							if ($privado!=1){			// Privado(solo colegiados)
								
								$preciono = recortardecimales($row['preciono'],".");
								 if ($preciono!=""){ 
									$preciotexto="";
									if ($preciono==0){
										$preciotexto="Gratuito";
									}
									elseif ($preciono>0){
										$preciotexto=$preciono."€";
									}
									
									if ($preciotachadoon==0){
										$preciotachadotexto="";
									}
									else{
										$preciopromocion=true;
										$preciotachadotexto=" <strike>".$preciotachadoon."€</strike>*";
									}
								?>
									<dt class="curso-precio"><i class="icon-shopping-cart"></i> No colegiado:</dt>
									<dd><?=$preciotexto;?><?=$preciotachadotexto;?></dd>
								<? 
								} 
							}
							?>
						</dl>
						<? 
						if ($preciopromocion){ 
							?> *Precio en promoción <br><?
					 	} 
						if ($row['estado']==2) { //estado del curso
							$idalumno=$_SESSION[idusuario]; 
							if (($_SESSION[nivel]==4)&&($idalumno<>"")) { //Alumno
								$sql=" idusuario='$idalumno' AND ";
								$link=iConectarse();
								$result=pg_query($link,"SELECT * FROM curso_usuario WHERE nivel='5' AND idcurso='$id' AND $sql borrado=0 ORDER BY fechahora DESC, id DESC") ;//or die (pg_error());  
								if(pg_num_rows($result)==0) {
									?>
										<?
										if ($row["modalidad"]==0){
											?>
											<a href="prescripcion.php?id=<?=$row['id'];?>"  class="btn btn-important">INSCRIPCIÓN<?=$textolistaespera?></a>
											<?
										}
										?>
									<? 
								}else{
									$rowcu = pg_fetch_array($result);
									$estado=($rowcu["estado"]); //estado de la inscripcion
									$espera=($rowcu["espera"]); //estado de la inscripcion
									?>
									<div class="grid-12">	
										<div>Usted ya se encuentra <? if (($estado==0)&&($espera==0)){ ?> MATRICULADO <? }else{ ?> INSCRITO <? }?></div>
										<a href="curso_inscritos.php?idcurso=<?=$row['id'];?>" class="btn btn-important">Lista de matriculados</a>
									</div>
									<? 
								}					
							}else{
								?>
									<?
									if ($row["modalidad"]==0){
										?>
										<a href="prescripcion.php?id=<?=$row['id'];?>"  class="btn btn-important">INSCRIPCIÓN<?=$textolistaespera?></a>
										<?
									}
									?>
								<? 
							}
						}?>
					</div>

		
				</div>
				<!--fin precio-inscripción-->
				<?
				}//fin de $fecha_fin_inscripcion>hoybd
			}else{// presencial
				if($row['estado']==2){
				if ($fecha_fin_inscripcion>=$hoybd){
				?>
					<div class="grid-12">			
						<? 				
						//<div class="grid-7"><h4 style="vertical-align:sub">ON-LINE</h4>?>
						<dl>
								<?
								$preciopromocion=false;
								$precioco = recortardecimales($row['precioc'],".");
								 if ($precioco!=""){ 
									$preciotexto="";
									if ($precioco==0){
										$preciotexto="Gratuito";
									}
									elseif ($precioco>0){
										$preciotexto=$precioco."€";
									}
									
									if ($preciotachadoc==0){
										$preciotachadotexto="";
									}
									else{
										$preciopromocion=true;
										$preciotachadotexto=" <strike>".$preciotachadoc."€</strike>*";
									}
									
								?>
									<dt class="curso-precio"><i class="icon-shopping-cart"></i> Colegiado:</dt>
									<dd><?=$preciotexto;?><?=$preciotachadotexto?></dd>
								<? 
								} 
								
								if ($privado!=1){			// Privado(solo colegiados)
									
									$preciono = recortardecimales($row['precion'],".");
									 if ($preciono!=""){ 
										$preciotexto="";
										if ($preciono==0){
											$preciotexto="Gratuito";
										}
										elseif ($preciono>0){
											$preciotexto=$preciono."€";
										}
										
										if ($preciotachadon==0){
											$preciotachadotexto="";
										}
										else{
											$preciopromocion=true;
											$preciotachadotexto=" <strike>".$preciotachadon."€</strike>*";
										}
									?>
										<dt class="curso-precio"><i class="icon-shopping-cart"></i> No colegiado:</dt>
										<dd><?=$preciotexto;?><?=$preciotachadotexto?></dd>
									<? 
									} 
								}
								?>
						</dl>	
							<? 
							if ($preciopromocion){ 
								?> *Precio en promoción <br><?
					 		} 
							if ($row['estado']==2) { //estado del curso
								$idalumno=$_SESSION[idusuario]; 
								if (($_SESSION[nivel]==4)&&($idalumno<>"")) { //Alumno
									$sql=" idusuario='$idalumno' AND ";
									$link=iConectarse();
									$result=pg_query($link,"SELECT * FROM curso_usuario WHERE nivel='5' AND idcurso='$id' AND $sql borrado=0 ORDER BY fechahora DESC, id DESC") ;//or die (pg_error());  
									if(pg_num_rows($result)==0) {
										?>
											<a href="prescripcion.php?id=<?=$row['id'];?>"  class="btn btn-important">INSCRIPCIÓN<?=$textolistaespera?></a>
										<? 
									}else{
										$rowcu = pg_fetch_array($result);
										$estado=($rowcu["estado"]); //estado de la inscripcion
										$espera=($rowcu["espera"]); //estado de la inscripcion
										?>
										<div class="grid-12">	
											<div>Usted ya se encuentra <? if (($estado==0)&&($espera==0)){ ?> MATRICULADO <? }else{ ?> INSCRITO <? }?></div>
											<a href="curso_inscritos.php?idcurso=<?=$row['id'];?>" class="btn btn-important">Lista de matriculados</a>
										<? 
									}					
								}else{
									?>
										<a href="prescripcion.php?id=<?=$row['id'];?>"  class="btn btn-important">INSCRIPCIÓN<?=$textolistaespera?></a>
									<? 
								}
							}?>						
					</div>
					<? 
				} 
				}//fin de $fecha_fin_inscripcion>hoybd
			}//fin else de modalidad //Por seguridad. Me lo cambiarán
			?>
					

		</div>
		<!--fin detallescurso precio-inscripción-->		
	</div> <!-- fin <div class="breadcrumb post-details grids detallescurso"> -->
	<!--fin detalles curso-->	
	<br />
	<div class="cuerponoticia">
		<h4>Presentación</h4>
		<?=($row["presentacion"]);?>
	</div>

	<? 
	
	if ($row["ponentes"]<>"0") { 
		$masponentes=($row["ponentes"]);
	}	
	
	
	// Genera
		$consultadg = "SELECT u.experiencia AS experiencia,u.imagen AS imagen2, u.nombre AS nombre, u.apellidos AS apellidos FROM curso_docente_web AS c,usuario AS u WHERE c.idusuario=u.id AND c.idcurso='$id' AND c.borrado = 0 AND u.borrado = 0 ORDER BY u.apellidos;";
		$linkdg=iConectarse(); 
		$r_datosdg=pg_query($linkdg,$consultadg);// or die (mysql_error());  
		if(pg_num_rows($r_datosdg)!=0){
			?><br />
			<div class="cuerponoticia">
			<h4>Ponentes</h4><br><?
			while($rowdg = pg_fetch_array($r_datosdg)) { 
				?><div class="grid-3 colegiado-pics"><?
					$c_directorio_img = "/var/www/web";
					$imagen2=$rowdg["imagen2"];
					$nombre=$rowdg["nombre"];
					$apellidos=$rowdg["apellidos"];
					$experiencia=$rowdg["experiencia"];
					$destino =$c_directorio_img."/imagen/".$imagen2;
					if(file_exists($destino)&&($imagen2<>"")){ //comprobamos que existe la foto
						?>
						<img src="imagen/<?=$imagen2?>"  alt="<?=$nombre?>" title="<?=$nombre?>" />
						<?
					}else{
						?>
						<img src="img/person.jpg"  alt="<?=$nombre?>" title="<?=$nombre?>" />
						<?
					
					}
				?></div>
					<div class="grid-9 colegiado-form"><strong><?
						echo $rowdg['nombre']." ".$rowdg['apellidos']."</strong><br />".$experiencia;
				?></div><br /><div class="clearfix"></div><hr /><?
			}
			echo $masponentes;
			?></div><?
		}
	?>
	
	<? if ($row["horariosyfechas"]<>"0") { ?>
		<br />
		<div class="cuerponoticia">
			<h4>Horarios y fechas</h4>
			<?=($row["horariosyfechas"])?>
		</div>
	<? }?>

	
	 <?
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT id,nombre,codigo FROM video WHERE padre='$id' AND borrado=0;");// or die (mysql_error());  
	if (($result2)&&(pg_num_rows($result2)!=0)) {
		?>
		<div id="videonoticia">
			<h3>Vídeo relacionado</h3>
			<?
				while($row2= pg_fetch_array($result2)) {								
					if ($row2["id"]<>""){?>
							<? //=$row2["nombre"]?> 
								<div style="width:550px; height:400px"  class="videocontainer">
									<?=$row2["codigo"]?>											
								</div>
					<? }?>
					<? 
				}?>
		</div>
		<!--fin videonoticia-->
		<?
	}?>
	
	<?
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT * FROM curso_horario WHERE idcurso='$id' AND borrado=0 ORDER BY fecha;");// or die (mysql_error());  
	if (pg_num_rows($result2)>0){
		?>			
		<br />
			<div class="cuerponoticia">
				<h4>Fecha y hora</h4>
				<ul>
				<?
				while($row2= pg_fetch_array($result2)) {								
					$horafin = $row2["horafin"];
					$textofin = "";
					if ($horafin<>""){
						$textofin = " - $horafin";
					}
					
					?>
							<li>
								<span class="actions">
								<?=cambiaf_a_normal($row2["fecha"])?> <?=$row2["hora"].$textofin?> 
								</span>
							</li>									
					<? 
				}
				?>
				</ul>
			</div>
		<?
	}?>
	
	<? if ($row["observaciones"]<>"") { ?>
		<br />
		<div class="cuerponoticia">
			<h4>Observaciones</h4><br>
			<?=($row["observaciones"])?>
		</div>
	<? }?>
	
	
	<?
	$link2=iConectarse(); 
	//$result2=pg_query($link2,"SELECT id,archivo,nombre FROM archivo WHERE padre='$id' AND borrado='0' AND (programa!=1 OR programa IS NULL);");// or die (mysql_error());  
	$result2=pg_query($link2,"SELECT id,archivo,nombre,programa FROM archivo WHERE padre='$id' AND borrado='0' ;");// or die (mysql_error());  
	if ($result2) {
		?>
		<div id="adjuntosnoticia">
			<br><legend>Archivos adjuntos</legend>
				<ul class="archivos"><? 
				while($row2= pg_fetch_array($result2)) {	
						$archivo = $c_directorio_img."/files/".$row2["archivo"];
						//--------------------------------------------------------------------------------------
						if(file_exists($archivo)&&($row2["archivo"]<>"")){
							$extension = filetype ($archivo);
							$exte = explode(".",$row2["archivo"]);
							$extension=$exte[1];
							$programa = $row2['programa'];
							
							if ($programa==1){
								
								?> 
								<li><a href="descarga.php?documento=<?=$row2["archivo"]?>"><? echo "Programa"; ?></a></li> 
								<?
							}
							else{
								?> 
								<li><a href="descarga.php?documento=<?=$row2["archivo"]?>"><? if($row2["nombre"]<>"") { echo $row2["nombre"]; }else{ echo"Documento"; }?></a><span class="detalles"> (<?=$extension?>,  <?=tamano_archivo(filesize($archivo))?> )</span></li> 
								<?
							}
						}
				}
				?> <li><a href="descarga.php?documento=Cómo es la formación en activatie_0216.pdf">Cómo es la formación en activatie</a><span class="detalles"></span></li> 
							
				
				
				</ul>
		</div>
		<!--fin adjuntosnoticia-->
		<?
	}?>
	
	
	<?
	
	
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT comentario,id,foto FROM foto WHERE padre='$id' AND borrado=0;");// or die (mysql_error());  
	if (($result2)&&(pg_num_rows($result2)!=0)) {
		?>
		<div id="minigaleriaimg">
			<h3>Imágenes asociadas</h3>
			<div id="gallery">
			<!--por cada fila se crea un contenedor GRIDS que alberga 3 imágenes, dentro de un grid-4 cada una -->
			 <!--si hay una fila que tiene menos de 3 imágenes, los grids-4 se han de poner igualmente, aunque vacíos.-->
				<?
				$cuantos=0;
				while($row2= pg_fetch_array($result2)) {								
					$destino =$c_directorio_img."/imagen/".$row2["foto"];
					if (($row2["foto"]<>"")&&(file_exists($destino))){
						if ($cuantos==0){		
							?> 
							<div class="grids">
							<?
						}?>
						<div class="grid-4">
							<a href="imagen/<?=$row2["foto"]?>" title="<?=$row2["comentario"]?>"  rel="shadowbox[galery]"><img src="imagen/<?=$row2["foto"]?>" alt="<?=$row2["comentario"]?>" title="<?=$row2["comentario"]?>"  /></a> 
						</div>
						<?
						$cuantos++;
						if ($cuantos==3){	
							$cuantos=0;	
							?> 
							</div>
							 <!--fin grids - fin fila 1-->
							<?
						}
					 }?>
					<? 
				}
				if ($cuantos==1) { ?><div class="grid-4"> </div><div class="grid-4"> </div></div><? }
				if ($cuantos==2) { ?><div class="grid-4"> </div></div><? }
				?>
			</div>
		</div>
		<!-- FIN MINIGALERA-->
		<?
	}?>
	<hr />

	<?
	
	$nombre="";
	if (isset($_SESSION['nombre'])){
		$nombre = $_SESSION['nombre'];
	}
	
	$apellidos="";
	if (isset($_SESSION['apellidos'])){
		$apellidos = $_SESSION['apellidos'];
	}
	
	$nombre = $nombre . " " . $apellidos;
	
	$email="";
	if (isset($_SESSION['email'])){
		$email = $_SESSION['email'];
	}
	
	?>
	
	
	<br>
	<form id="comentarios" action="curso.php?accion=consulta&idcolegio=<?=$idcolegio?>&id=<?=$id?>" method="post" class="form-horizontal">
		<fieldset>
		<legend>¿Tienes dudas?, Consúltanos... </legend>


			<p>Emplea el siguiente formulario para trasladarnos cualquier pregunta, duda o comentario sobre la actividad. Estaremos encantados de ayudarte.</p>

			<div class="control-group">
				<label class="control-label" for="inputName">Nombre y Apellidos</label>
			 <div class="controls">
				<input required type="text" id="inputName" placeholder="Nombre" name="nombre" class="input-xlarge" value="<?=$nombre?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="inputEmail">Correo electrónico</label>
			 <div class="controls">
				<input required type="text" id="inputEmail" placeholder="correo@electronico.com" name="email" class="input-xlarge" value="<?=$email?>">
				</div>
			</div>

				<div class="control-group">
				<label class="control-label" for="inputEmail">Consulta</label>
			 <div class="controls">
				<textarea required rows="6" name="consulta"  class="input-xlarge"></textarea>
				</div>
			</div>


		</fieldset>


		<div class="form-actions">
		<button type="submit" class="btn btn-primary btn-large">Enviar consulta</button>
		</div>

	</form>






	<!--Aquí debe ir el código de los botones para compartir en redes sociales.-->
	<!--<img  src="img/social-networks.png" alt=" " >-->
	<div class="clearfix"></div><br />	
	<?
		$link=conectar();
		$rsv = pg_query($link,"SELECT * FROM visto WHERE idcurso='$id'");
		$cv= pg_num_rows($rsv); 
	?>
	<ul class="breadcrumb">
		<li><i class="icon-eye-open"></i> Visto <?=$cv?> veces </li>
		<li class="readmore"><a href="formacion.php"><i class="icon-chevron-left"></i> Volver a Cursos</a></li>
	</ul>
				</div>
				<!--fin pagina blog-->
				<div class="clearfix"></div>
			</div>
			<!--fin grid-8 contenido-principal-->
 	<?
	include("_visto.php"); 
}else{
	$_SESSION[error]="Acceso no permitido";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}
include("plantillaweb02.php"); 
?>