<?
include("_funciones.php"); 
include("_cone.php");
include("_seguridadfiltro.php"); 
$accion=strip_tags($_GET['accion']); 
$texto=strip_tags($_POST['texto']);

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
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idusuario='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		echo "Error de sesion2";
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$idcolegio=1;
	$sql = "";
}else{
	echo "Error: aqui no deberia entrar.";
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

if ($accion=="eliminar"){

	$idemail=strip_tags($_GET['id']);
	if ($idemail<>""){
		$sql = "UPDATE emailhistorial SET borrado=1 WHERE id='$idemail'";
		posgre_query($sql);
					
		$_SESSION[esterror]="Email eliminado correctamente.";	
	
		header("Location: zona-privada_admin_comunicaciones_5_historico-de-envios.php");
		exit();
	}
}

if ($accion=="enviar"){

	$idemail=strip_tags($_GET['id']);
	if ($idemail<>""){
	
		$sql = "SELECT * FROM emailhistorial WHERE id='$idemail'";
		$result = posgre_query($sql);
		if ($row = pg_fetch_array($result)){

			$sql = "UPDATE email_cron SET enviar=1 WHERE idemail='$idemail'";
			posgre_query($sql);

			$sql = "UPDATE emailhistorial SET enviado=1 WHERE id='$idemail'";
			posgre_query($sql);
			
			$_SESSION[esterror]="Envío de emails iniciado correctamente.";	
	
			header("Location: zona-privada_admin_comunicaciones_5_historico-de-envios.php");
			exit();
	
		
		}

	}
}



/*
if($accion=='eliminar'){
	$id=strip_tags($_GET['id']);
	if ($id<>''){
		$link=iConectarse(); 
		$Query = pg_query($link,"UPDATE email SET borrado='1' WHERE  $sql id='$id';" );// or die (mysql_error()); 
		if ($Query){
			$_SESSION[error]="Eliminado correctamente";	
			$est="ok";
		}else{
			$_SESSION[error]="Error al eliminar";	
			$est="ko";
		}
	}else{
		header("Location: index.php?salir=true&i");
		exit();
	}
}
*/
$titulo1="formación ";
$titulo2="administración";

include("plantillaweb01admin.php"); 
?><!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
		<h2 class="titulonoticia">Comunicaciones. Histórico de envíos</h2>
		<? /*		<div class="mensaje ko">
					<h3>Disculpe las molestias, nos encontramos modificando esta sección. MailChimp API</h3>	
				</div>		
			*/?>
		<br />
		<div class="bloque-lateral acciones">		
   	 				<p><strong>Acciones:</strong>
   	 					<a href="zona-privada_admin_comunicaciones_1.php" class="btn btn-success" type="button">Nuevo <i class="icon-plus"></i></a> |
   	 					<a href="zona-privada_admin_cursos_1_email.php" class="btn btn-success" type="button">Nuevo CURSO <i class="icon-plus"></i></a> |
   	 					<a href="zona-privada_admin_comunicaciones_varios_cursos.php" class="btn btn-success" type="button">Nuevo VARIOS CURSOS <i class="icon-plus"></i></a> |
   	 					<a href="zona-privada_admin_cursos_1_publicacion.php" class="btn btn-success" type="button">Nueva PUBLICACIÓN <i class="icon-plus"></i></a> |
   	 					<a href="zona-privada_admin_cursos_1_trabajo.php" class="btn btn-success" type="button">Nueva OFERTAS DE TRABAJO <i class="icon-plus"></i></a> | 
						<?
						if ($_SESSION[nivel]==1){
							?>
   	 						<a href="zona-privada_admin_comunicaciones_newsletter.php" class="btn btn-success" type="button">Newsletter <i class="icon-plus"></i></a> 
							<?
						}
						?>
   	 				</p>
		</div>
		<!--fin acciones-->
		<? include("_aya_mensaje_session.php"); ?>
		<br />
		<div class="bloque-lateral buscador">		
			<h4>Histórico de mensajes enviados</h4>
			<div class="bloque-lateral buscador">		
			<h4>Buscar mensajes</h4>
			<form action="zona-privada_admin_comunicaciones_5_historico-de-envios.php?accion=buscar" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="Mensajes a buscar" value="<?=$texto?>" />
    				<input class="btn" type="submit" value="Buscar" />

   				</div>		
			    </fieldset>
		    </form>
		</div>
		</div>
		<!--fin buscador-->
<table class="align-center">

<tr>
	<th>ID</th>
	<th>FECHA HORA</th>
	<th>ASUNTO</th>
	<th>PARA</th>
	<th>TIPO</th>
	<th>ENVIADO POR</th>
	<th>ACCIÓN</th>	
</tr>

<?      
//Paginacion 1
$pagina=strip_tags($_GET['pagina']);
$registros = 30;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
} 

$link=iConectarse();
if (($accion=="buscar")&&($texto<>"")){
	$result=pg_query($link,"SELECT * FROM emailhistorial WHERE  $sql dominio=1  AND borrado=0 AND sp_asciipp(asunto) ILIKE sp_asciipp('%$texto%') ORDER BY fecha DESC, id DESC");// or die (mysql_error()); 
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM emailhistorial WHERE  $sql dominio=1  AND borrado=0 AND sp_asciipp(asunto) ILIKE sp_asciipp('%$texto%') ORDER BY fecha DESC, id DESC LIMIT $registros OFFSET $inicio;");// or die (mysql_error()); 
	$total_paginas = ceil($total_registros / $registros); 	
}
else{
	$result=pg_query($link,"SELECT * FROM emailhistorial WHERE $sql dominio=1  AND borrado=0 ORDER BY fecha DESC, id DESC");// or die (mysql_error()); 
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM emailhistorial WHERE $sql dominio=1  AND borrado=0 ORDER BY fecha DESC, id DESC LIMIT $registros OFFSET $inicio;");// or die (mysql_error()); 
	$total_paginas = ceil($total_registros / $registros); 
}

$bgcolor="#DDECFF";
$cc=0;
while($row = pg_fetch_array($result)) {
	++$cc;
	
	$procesado = $row['procesado'];
	
	//Procesado o NO procesado
	if ($procesado==1){
		$bgcolor="fondo_normal";
	}else{
		$bgcolor="fondo_rojo";
	}
	
	$idemail=$row["id"];
	$para=$row["usuarios"];
	$idcolegio=$row["idusuario"];
	$tipo=$row["tipo"];
	$enviado=$row["enviado"];
	
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
	
	if ($tipo=="0"){
		$tipotexto="Circular";
	}	
	if ($tipo=="1"){
		$tipotexto="Curso";
	}
	if ($tipo=="2"){
		$tipotexto="Oferta de trabajo";
	}
	if ($tipo=="3"){
		$tipotexto="Publicación";
	}
	if ($tipo=="4"){
		$tipotexto="Newsletter";
	}
	
	if (strpos($para,"colegio_")!== false){			// Colegio_ + ID colegio				
		$pieces = explode("_", $para);
		$idcolegio2 = $pieces[1];
		$link2=iConectarse(); 
		$result2=pg_query($link2,"SELECT * FROM usuario WHERE id='$idcolegio2' AND nivel='2' AND borrado=0 ORDER BY nombre DESC, id DESC") ;//or die (pg_error());  
		$row2 = pg_fetch_array($result2);
		$paratexto="Colegiados de ".$row2["nombre"];
	}
	
	if (($para>0)) {
		$paratexto = "Envío manual";
	}	
	
	?><tr>
	<td><?=$row["id"]?></td>
	<td><?
		$fe=cambiaf_a_normal_todo($row["fecha"]);
		echo substr($fe, 0, -10);  					
	?></td>
	<td align="left"><?=$row["asunto"]?></td>
	<td class="<?=$bgcolor?>" align="center"><strong><?=$paratexto?></strong></td>
	<td class="<?=$bgcolor?>" align="center"><strong><?=$tipotexto?></strong></td>
	<td align="center">
	<?
	// Genera
		$consulta = "SELECT * FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;";
		$link=iConectarse(); 
		$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
		if($rowdg= pg_fetch_array($r_datos)) {	
			echo $rowdg['nombre'];
		}else{
			echo "Admin";
		}?></td>

	<td class="<?=$bgcolor?>" align="center">
		<a href="zona-privada_admin_comunicaciones_6_ficha-de-envio.php?id=<?=$row["id"]?>" class="btn btn-primary">ver</a>
		<?
		if ($enviado==0){
		?>
			<a onclick="return confirmar('&iquest;Seguro que desea enviar el email?')"  href="zona-privada_admin_comunicaciones_5_historico-de-envios.php?accion=enviar&id=<?=$row["id"]?>" class="btn btn-primary">enviar</a>
			<!--<a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php?id=<?=$row["id"]?>" class="btn btn-primary">editar</a>-->
			<a onclick="return confirmar('&iquest;Seguro que desea eliminar el email?')"  href="zona-privada_admin_comunicaciones_5_historico-de-envios.php?accion=eliminar&id=<?=$row["id"]?>" class="btn btn-primary">eliminar</a>
		<?
		}
		?>
	</td>
	</tr>
	<? 
	
	
} //fin while
pg_free_result($result); 
pg_close($link); 
//session_destroy();
?> 
</table>

<p class="align-center">Total: <?=$total_registros?> mensajes</p>
			
<? 
if($total_paginas > 1) { ?>
	<div class="pagination">
		<ul>
			<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php?pagina=<?=(1)?>&texto=<?=$texto?>&actividad=<?=$actividad?>&tp=<?=$tipopubli?>" title="Ver primeros Resultados">Primeros</a></li><?
			if(($pagina - 1) > 0) { ?>
				<li><a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php?pagina=<?=($pagina-1)?>&texto=<?=$texto?>&actividad=<?=$actividad?>&tp=<?=$tipopubli?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
						<li><a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php?pagina=<?=$i?>&texto=<?=$texto?>&actividad=<?=$actividad?>&tp=<?=$tipopubli?>"><?=$i?></a></li>
						<?
					}
				}	
			}
			if(($pagina + 1)<=$total_paginas) { ?>
				 <li><a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php?pagina=<?=($pagina+1)?>&texto=<?=$texto?>&actividad=<?=$actividad?>&tp=<?=$tipopubli?>" title="Ver siguientes Resultados">Siguientes</a></li>
				<?
			} ?>
			<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php?pagina=<?=$total_paginas?>&texto=<?=$texto?>&actividad=<?=$actividad?>&tp=<?=$tipopubli?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
		</ul>
	</div>
	<?
}?>
<!--FIN PAGINADOR-->
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
