<?
session_start();
include("_funciones.php"); 
include("_cone.php"); 
$titulo1="formación ";
$titulo2="administración";
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	echo "Error: aqui no deberia entrar.";
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	echo "Error: profe aqui no deberia entrar.";
	exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idusuario=strip_tags($_SESSION[idcolegio]);
		$sql="  (idusuario='$idusuario') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		echo "Error de sesion2";
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sql="";
	$idusuario=1;
}else{
	echo "Error: aqui no deberia entrar.";
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$id=trim($_REQUEST['id']);
$textobusqueda=trim($_REQUEST['textobusqueda']);
$accion=trim($_GET['accion']);
if ($id<>''){
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM emailhistorial WHERE $sql id='$id';");// or die (mysql_error());
	$row = pg_fetch_array($result);
	$texto=$row["texto"];
	$asunto=$row["asunto"];
	$fecha=$row["fecha"];
	$para=$row["usuarios"];
	$idcolegio=$row["idusuario"];
	//sacamos datos del estado del envio
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM email_cron WHERE idemail='$id';");// or die (mysql_error());
	$cuantos=pg_num_rows ($result);	
	
	$_SESSION['textoEmail']=$texto;	
	
}else{
	$_SESSION[error]="Error: no puede ver la ficha de envio";	
	$est="ko";
	header("Location: zona-privada_admin_comunicaciones_5_historico-de-envios.php?est=ko"); 
	exit();
}
include("plantillaweb01admin.php"); 
?>
<!------------Arriba pantilla1---------->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
		<h2 class="titulonoticia">Comunicaciones</h2>
		<br>
		<div class="bloque-lateral acciones">		
			<p><strong>Acciones:</strong>
				<a class="btn btn-success" href="zona-privada_admin_comunicaciones_5_historico-de-envios.php">Volver <i class="icon-calendar"></i></a> <br />
			</p>
		</div>
			<!--fin acciones-->
		<div class="comunicacion">		
			<h3>Datos del envío</h3>
			<h4>Resumen:</h4>
			<br />
				<ul>
					<li>Enviado el <strong><?
									$fe=cambiaf_a_normal_todo($fecha);
									echo substr($fe, 0, -10);  					
									?></strong></li>
					<li>Enviado por <strong><?
									// Genera
										$idcolegio=$row["idusuario"];
										$consulta = "SELECT * FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;";
										$link=iConectarse(); 
										$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
										if($rowdg= pg_fetch_array($r_datos)) {	
											echo $rowdg['nombre'];
										}else{
											echo "No definido";
										}?></strong></li>
					<li>Enviado a <? if ($cuantos>0) echo "<strong>".$cuantos."</strong>";?> usuarios 
					<?
					$idcolegio=$row["idusuario"];
	
					if (($para=="todos")) { //Todos los usuarios del sistema
						$paratexto="Todos";
					}
					if (($para=="registrados")) { //todos los inscritos a un curso
						$paratexto="Registrados web";
					}
					if (($para=="suscritos")) {
						$paratexto="Suscritos web";
					}
					if (($para=="colegiados")) {
						$paratexto="Colegiados";
					}
					if (($para=="nocolegiados")) {
						$paratexto="No colegiados";
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
						$paratexto="Colegiados de ".$row2["nombre"];
					}
					
					if (($para>0)) {
						$paratexto = "Envío manual";
					}
						
					?>
					<strong>(<?=$paratexto?>)</strong> </li>
					<!--<li><strong><i class="icon-warning-sign"></i> Se encontraron Y errores <a href="#errores">[Ver detalles]</a></strong></li>					
					<li><strong><i class="icon-ok"></i> Z mensajes enviados satisfactoriamente</strong></li>-->	
				</ul>   	 				
			<br />
			<h4>Email:</h4>	 				
			<br />
			<ul>
				<li><strong>Asunto:</strong> <?=$row["asunto"]?></li>
				<li><strong>Texto:</strong></li>
				<!--Dentro del IFRAME deberás mostrar el mensaje en HTML con la plantilla correspondiente,
				es decir, en el iframe se muestra el mensaje tal y como se va a enviar.
				De esta forma es más fácil que vean exactamente cómo quedará.-->
				<iframe src="plantillaemail.php?plantilla&idemail=<?=$id?>"></iframe>
			</ul>	
			<br />
			
			<div class="bloque-lateral buscador">	
			<br />	
			<div class="bloque-lateral buscador">		
			<h4>Buscador de usuarios(Apellidos o Email)</h4>
			<form action="zona-privada_admin_comunicaciones_6_ficha-de-envio.php?id=<?=$id?>&accion=buscar" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="textobusqueda" placeholder="Usuario a buscar" value="<?=$textobusqueda?>" />
    				<input class="btn" type="submit" value="Buscar" />

   				</div>		
			    </fieldset>
		    </form>
			<br />
			</div>
	
			
			<h4>Usuarios:</h4>	 
			<table>
			<tr>
				<th>Nombre y apellidos</th>
				<th>Email</th>
				<th>Estado</th>
				<th>Fecha envío</th>
				<th>Acción</th>
			</tr>	
			
			<?
			$pagina=strip_tags($_GET['pagina']);
			$registros = 100;
			if (!$pagina) { 
			$inicio = 0; 
			$pagina = 1; 
			}else{ 
			$inicio = ($pagina - 1) * $registros; 
			} 

			if (($accion=="buscar")&&($textobusqueda<>"")){
				$result=posgre_query("SELECT * FROM email_cron EC,usuario U WHERE  (( U.id=EC.idusuario AND(sp_asciipp(U.email) ILIKE sp_asciipp('%$textobusqueda%') OR sp_asciipp(U.apellidos) ILIKE sp_asciipp('%$textobusqueda%'))) ) AND EC.idemail='$id';");// or die (mysql_error());
				$total_registros = pg_num_rows($result); 
				$result=posgre_query("SELECT * FROM email_cron EC,usuario U WHERE  (( U.id=EC.idusuario AND (sp_asciipp(U.email) ILIKE sp_asciipp('%$textobusqueda%') OR sp_asciipp(U.apellidos) ILIKE sp_asciipp('%$textobusqueda%'))) ) AND EC.idemail='$id'  ORDER BY fechaenvio LIMIT $registros OFFSET $inicio;;");// or die (mysql_error());
				$total_paginas = ceil($total_registros / $registros); 
			}
			else{
			
				$result=posgre_query("SELECT * FROM email_cron WHERE idemail='$id';");// or die (mysql_error());
				$total_registros = pg_num_rows($result); 
				$result=pg_query($link,"SELECT * FROM email_cron WHERE idemail='$id' ORDER BY fechaenvio  LIMIT $registros OFFSET $inicio;");// or die (mysql_error()); 
				$total_paginas = ceil($total_registros / $registros); 
			
			}
			while ($row = pg_fetch_array($result)){
				$idusuario = $row['idusuario'];
				$exito = $row['exito'];
				$fechahora = $row['fechaenvio'];
				if ($exito){
					$estado="Enviado";
				}
				else{
					$estado="NO enviado";
				}
				
				if ($idusuario=="-1"){
					$nombre = "Suscrito Web";
					$email = $row['correo'];
				}
				else{
					$sql = "SELECT * FROM usuario WHERE id='$idusuario'";
					$result2 = posgre_query($sql);
					if ($row2 = pg_fetch_array($result2)){
						$nombre = $row2['nombre']. " " . $row2['apellidos'];
						$email = $row2['email'];
					}
					
				}
				?> 
				<tr>
				<td><?=$nombre;?></td>
				<td><?=$email;?></td>
				<td><?=$estado;?></td>
				<td><?=$fechahora;?></td>
				<td></td>
				</tr>
				<?
				
			}
			?>
			
			</table>
			<!--
		<div id="errores" class="alert alert-error">
		<h3>Errores:</h3>
			<ol>
				<li><strong>Manolo García (mangr23@gmail.com)</strong>: E-mail no existe.</li>						
				<li><strong>Fulanito Pérez (fulper21@hotmail.com)</strong>: El servidor devolvió el mensaje.</li>
				<li><strong>Maria Majada (mamaj@eat.es)</strong>: Correo lleno.</li>				
				<li>...</li>				
				<li><strong>Aitor Men (aitor@aemet.es)</strong>: Error desconocido.</li>				
			</ol>   
		</div>
		<!--fin errores-->
		
		</div>
		
		
		<p class="align-center">Total: <?=$total_registros?> usuarios</p>
		
<? 
if($total_paginas > 1) { ?>
	<div class="pagination">
		<ul>
			<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="zona-privada_admin_comunicaciones_6_ficha-de-envio.php?id=<?=$id?>&pagina=<?=(1)?>" title="Ver primeros Resultados">Primeros</a></li><?
			if(($pagina - 1) > 0) { ?>
				<li><a href="zona-privada_admin_comunicaciones_6_ficha-de-envio.php?id=<?=$id?>&pagina=<?=($pagina-1)?>" title="Ver anteriores Resultados">Anteriores</a></li>
				<?
			}
			$a=$pagina-2;
			$b=$pagina+2;
			for ($i=1; $i<=$total_paginas; $i++){ 
				if ($pagina == $i) {
					?><li class="disabled"><a><?=$pagina?></a></li><?
				} else {
					if (($a<$i)&&($b>$i)){
						?>
						<li><a href="zona-privada_admin_comunicaciones_6_ficha-de-envio.php?id=<?=$id?>&pagina=<?=$i?>"><?=$i?></a></li>
						<?
					}
				}	
			}
			if(($pagina + 1)<=$total_paginas) { ?>
				 <li><a href="zona-privada_admin_comunicaciones_6_ficha-de-envio.php?id=<?=$id?>&pagina=<?=($pagina+1)?>" title="Ver siguientes Resultados">Siguientes</a></li>
				<?
			} ?>
			<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="zona-privada_admin_comunicaciones_6_ficha-de-envio.php?id=<?=$id?>&pagina=<?=$total_paginas?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
		</ul>
	</div>
	<?
}?>
<!--FIN PAGINADOR-->
		
		
		
		
		<!--fin comunicacion-->
		<div id="volverarriba">
			<hr />
			<a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php" title="Volver al listado histórico de mensajes">Regresar al histórico de mensajes <i class="icon-circle-arrow-left"></i></a> · 				
			
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
