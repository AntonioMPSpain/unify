<?

/** DIFERENTE TIPOS DE ENVIO(tipo) **/
/**
/*  0=OTRO TIPO DE ENVÍOS(Por ejemplo, inscrito a un curso o email directo del admin
/*	1=CURSOS(usuario:avisocursos)
/*	2=OFERTAS DE TRABAJO(usuario:avisotrabajo)
/*  3=PUBLICACIONES(usuario:avisopublicaciones)
/*  4=COMPUESTAS-NEWSLETTER(usuario:notificaciones)
/*  
/**

/**
/* Procesa un email y sus destinatarios para insertarlo en la tabla email_cron y que termine por enviarse 
**/
function procesarEmailCron($idemail, $prioridad=0){

	if ($idemail==""){
		$_SESSION[esterror]="El idemail es vacío";
		header("Location: index.php?salir=true&11");
		exit();
	}	

	$sql = "SELECT * FROM emailhistorial WHERE id='$idemail'";
	$result = posgre_query($sql);
	
	if (pg_num_rows($result) == 1){
		if ($row = pg_fetch_array($result)){
			$procesado = $row["procesado"];
			
			$idusuariocorreo = $row["idusuario"];
			
			if ($procesado==0){
				$usuarios = $row["usuarios"];
				$tipo = $row["tipo"];
				$suscrito = false;
				
				if ($tipo==1){
					$sqlAviso = " AND avisocursos=1 ";
				}
				elseif($tipo==2){
					$sqlAviso = " AND avisotrabajo=1 ";
				}
				elseif($tipo==3){
					$sqlAviso = " AND avisopublicaciones=1 ";
				}
				elseif($tipo==4){
					$sqlAviso = " AND notificaciones=1 ";
				}
				else{
					$sqlAviso = "";
				}
				
				if ($usuarios=="todos"){					// Registrados + Suscritos
					$sql = "SELECT * FROM usuario WHERE (nivel=2 OR nivel=3 OR nivel=4) AND borrado=0 $sqlAviso";
					$suscrito=true;
				}
				elseif($usuarios=="registrados"){		
					$sql = "SELECT * FROM usuario WHERE (nivel=3 OR nivel=4) AND borrado=0 $sqlAviso";
				}
				elseif ($usuarios=="colegiados"){
					$sql = "SELECT * FROM usuario WHERE (nivel=3 OR nivel=4) AND borrado=0 AND idcolegio!=0 AND idcolegio IS NOT NULL $sqlAviso";
				}
				elseif ($usuarios=="nocolegiados"){
					$sql = "SELECT * FROM usuario WHERE (nivel=3 OR nivel=4) AND borrado=0 AND (idcolegio=0 OR idcolegio IS NULL) $sqlAviso";
				}
				elseif ($usuarios=="activos"){
					$sql = "SELECT * FROM usuario WHERE (nivel=3 OR nivel=4) AND borrado=0 AND pass IS NOT NULL $sqlAviso";
				}
				elseif ($usuarios=="noactivos"){
					$sql = "SELECT * FROM usuario WHERE (nivel=3 OR nivel=4) AND borrado=0 AND pass IS NULL $sqlAviso";
				}
				elseif (strpos($usuarios,"colegio_")!== false){			// Colegio_ + ID colegio				
					$pieces = explode("_", $usuarios);
					$idcolegio = $pieces[1];
					$sql = "SELECT * FROM usuario WHERE (nivel=3 OR nivel=4) AND borrado=0 AND idcolegio='$idcolegio' $sqlAviso";
				}
				else{						// Un id de usuario o varios separados por comas
					$sql = "SELECT * FROM usuario WHERE (nivel=3 OR nivel=4) AND borrado=0 AND id IN($usuarios)";
				}
				
				$result = posgre_query($sql);
				while ($row = pg_fetch_array($result)){
					$idusuario = $row['id'];
					
					$sql = "INSERT INTO email_cron (idemail,idusuario,preferencia) VALUES ('$idemail','$idusuario', '$prioridad')";
					posgre_query($sql);
									
				}
				
				if (($usuarios=="suscritos")||($suscrito)){
				
					$sql = "SELECT * FROM suscrito WHERE borrado=0 AND email NOT IN(SELECT email FROM usuario WHERE borrado=0)";
					$result = posgre_query($sql);
					$idusuario = -1;
					
					while ($row = pg_fetch_array($result)){
						$email = $row['email'];

						$sql = "INSERT INTO email_cron (idemail,idusuario,preferencia,correo) VALUES ('$idemail','$idusuario', '$prioridad','$email')";
						posgre_query($sql);
					}
				}
				
				
				$sql = "INSERT INTO email_cron (idemail,idusuario,preferencia,enviar) VALUES ('$idemail','69', '4','1')";
				posgre_query($sql);
				
				$sql = "INSERT INTO email_cron (idemail,idusuario,preferencia,enviar) VALUES ('$idemail','6264', '4','1')";
				//posgre_query($sql);
				
				$sql = "INSERT INTO email_cron (correo,idemail,idusuario,preferencia,enviar) VALUES ('gabinete@coaatmu.es','$idemail','-1', '4','1')";
				//posgre_query($sql);
			
				
				if ($idusuariocorreo==111){
					$sql = "INSERT INTO email_cron (correo,idemail,idusuario,preferencia,enviar) VALUES ('formacion@coaatmu.es','$idemail','-1', '4','1')";
					posgre_query($sql);
				}

				$sql = "UPDATE emailhistorial SET procesado=1 WHERE id='$idemail'";
				posgre_query($sql);
			}
			else{
				$_SESSION[esterror]="El email ya esta procesado";
				header("Location: index.php?salir=true&2");
				exit();
			}	
		}
	}
	else{
		$_SESSION[esterror]="El email que desea procesar no existe";
		header("Location: index.php?salir=true&3");
		exit();
	}
}


?>