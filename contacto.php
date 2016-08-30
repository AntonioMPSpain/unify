<?
error_reporting(0);
include("_funciones.php"); 
include("_cone.php"); 
$titulo1="plataforma";
$titulo2="profesional";
$accion=recoge("accion");
$migas = array();
session_start();
$nombre=$_SESSION[nombre];
if($accion=='guardar'){
	setlocale(LC_TIME,"spanish"); 
	$fecha=strftime("%d/%m/%Y-%H:%M");
	$idcolegio=strip_tags($_POST['idcolegio']);
	$email=strip_tags($_POST['email']);
	$telefono=strip_tags($_POST['telefono']);
	$comentario=nl2br(strip_tags($_POST['consulta']));

	$error="";
	if ($email==""){
		$error.="<li><strong>Email</strong> es un campo obligatorio</li>";
	}
	else {
		if (comprobar_email($email)<>1){
			$error.="<li><strong>Email</strong> no válido</li>";
		}
	}
			
	if ($telefono==""){
		$error.="<li><strong>Teléfono</strong> es un campo obligatorio</li>";
	}

	if ($comentario==""){
		$error.="<li><strong>Consulta</strong> es un campo obligatorio</li>";
	}
	
	if ($error==""){
		$consulta = "INSERT INTO contacto (idcolegio,email,telefono,texto) VALUES ('$idcolegio','$email','$telefono','$comentario')";
		$link=conectar();
		pg_query($link,$consulta);
		//echo pg_last_error();
		if ($idcolegio>0){
			$consulta = "SELECT * FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;";
			$link=conectar();
			$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
			if($rowdg= pg_fetch_array($r_datos)) {	
				$para=$rowdg['email'];
			} 
		}
		if (comprobar_email($para)==0) { //si el emial es incorrecto
			$para="info@activatie.org";
		}
		if ($_SERVER) {
			if ( $_SERVER[HTTP_X_FORWARDED_FOR] ) {
	
				$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	
			} elseif ( $_SERVER["HTTP_CLIENT_IP"] ) {
	
				$realip = $_SERVER["HTTP_CLIENT_IP"];
	
			} else {
	
				$realip = $_SERVER["REMOTE_ADDR"];
	
			}
	
		} else {
			if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
				$realip = getenv( 'HTTP_X_FORWARDED_FOR' );
	
			} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
	
				$realip = getenv( 'HTTP_CLIENT_IP' );
	
			} else {
	
				$realip = getenv( 'REMOTE_ADDR' );
			}
	
		}
	
		//$link=Conectarse(); 
		//$Query = mysql_query("INSERT INTO contacto (email,telefono,comentario,fecha) VALUES ('$email','$telefono','$comentario','$fecha')" ,$link);
		$asunto = "Alguién ha contactado con usted desde activatie."; 
		$cuerpo = ' 
		<html> 
		<head> 
		   <title> Contacto activatie</title> 
		</head> 
		<body> 
		<p>E-mail: '.$email.'</p>
		<p>Teléfono: '.$telefono.'</p>
		<p>Comentario: '.$comentario.'</p>
		<p>Fecha: '.$fecha.'</p> 
		<p>IP: '.$realip.'</p> 
		</body> 
		</html> 
		'; 
		//para el envío en formato HTML 
		$cuerpoplano=strip_tags ($cuerpo);
		//creamos un identificador único
		//para indicar que las partes son idénticas
		$uniqueid= uniqid('info@activatie.org');
		 
		//indicamos las cabeceras del correo
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "From: info@activatie.org \r\n";
		$headers .= "Subject: ".$asunto."\r\n";
		//lo importante es indicarle que el Content-Type
		//es multipart/alternative para indicarle que existirá
		//un contenido alternativo
		$headers .= "Content-Type: multipart/alternative;boundary=" . $uniqueid. "\r\n";
		$message = "";
		$message .= "\r\n\r\n--" . $uniqueid. "\r\n";
		$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
		$message .= $cuerpoplano;
		 
		$message .= "\r\n\r\n--" . $uniqueid. "\r\n";
		$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
		$message .= $cuerpo;
		 
		$message .= "\r\n\r\n--" . $uniqueid. "--";
		 
		$titulo    = $asunto;	
		//$para="info@activatie.org";
		$exito=mail($para, $titulo, $message, $headers);	
		$exito=mail("jose@newsisco.com", $titulo, $message, $headers);	
	}
}
$safe="Contacto";
include("plantillaweb01.php"); 
?>
<!--arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<?
	if (!$exito){
		?>
		<div class="mensaje ko">
			<h3>¡Importante!</h3>	
			<ul>
				<li>Para efectuar la consulta, seleccione en el desplegable su Colegio, y si no pertenece a estos colegios, seleccione activatie.</li>
			</ul>
		</div>	
		<?
	}
	?>	
		<h2 class="titulonoticia">Contacto</h2>
		<br />
			<?
			if ($exito){
				?>
				<div class="mensaje ok">
					<h3>¡Aviso!</h3>	
					<ul>
						<li>Su consulta ha quedado registrada. Nos pondremos en contacto lo antes posible.</li>
					</ul>
				</div>		
				<?
			}elseif($error<>""){
				?>
				<div class="mensaje ko">
					<h3>¡Error!</h3>	
					<ul>
						<?=$error?>
					</ul>
				</div>		
				<?
			}
		
		if (($accion=='') || (($accion=='guardar')&&($error!=""))){		// Muestra formulario
		
		?>
		
		<div class="bloque-lateral buscador">		
			<form class="form-horizontal" action="contacto.php?accion=guardar" method="post" enctype="multipart/form-data">
				<fieldset>
		    		
		    		 <div class="control-group">
		    			<label class="control-label"><strong>Colegio:</strong></label>
   	 				<div class="controls">
   	 					<select name="idcolegio" class="input-large" >
							<option class="input-large" value="0">[-seleccione-]</option>
							<?
							// Generar listado 
								$consulta = "SELECT * FROM usuario WHERE nivel=2 AND borrado = 0 ORDER BY nombre;";
								$link=iConectarse(); 
								$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
								while($rowdg= pg_fetch_array($r_datos)) {	
									
									$selected="";
									if ($rowdg['id']==$idcolegio)
										$selected="selected";
									
									?>
									<option <?=$selected?> class="input-large" value="<?=$rowdg['id']?>"><? echo ($rowdg['nombre']); ?></option>
									<? 
								} ?>
						</select>
   	 				</div>
   	 			 </div>
				 <div class="control-group">
					<label class="control-label"><strong>E-mail:</strong></label>
					<div class="controls">
						<input class="input-large" value="<?=$email?>" type="text" id="email" name="email" placeholder="e-mail" />
					</div>
				 </div>
				 <div class="control-group">
					<label class="control-label"><strong>Teléfono:</strong></label>
					<div class="controls">
						<input class="input-large" value="<?=$telefono?>" type="text" id="telefono" name="telefono" placeholder="teléfono" />
					</div>
				 </div>
				 <div class="control-group">
					<label class="control-label"><strong>Consulta:</strong></label>
					<div class="controls">
						<textarea name="consulta"><?=$comentario?></textarea>
					</div>
				 </div>
	    		 <div class="control-group">
   	 				<div class="controls">
							<button class="btn  btn-primary" type="submit">Enviar</button>
   	 				</div>
   	 			 </div>
			    </fieldset>
<!--<a href="#" class="btn btn-primary">editar</a>-->
		    </form>
		</div>

		<? } ?>	
		
		
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
include("plantillaweb02.php"); 
?>
