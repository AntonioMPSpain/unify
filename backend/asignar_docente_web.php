<?
include("_funciones.php"); 
include("_cone.php"); 
$safe="Asignar Profesor";
//Parametros obligatorios
$idcurso=strip_tags($_GET['idcurso']); 
if ($idcurso==""){
	$_SESSION[error]="Parametros incorrectos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}

if (($accion=="buscar")&&($texto<>"")){
	$textobuscar="Todos los colegios"; 			
}
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[error]="No dispone de permisos";	
		header("Location: index.php?error=true&est=ko#2");
		exit();
	}
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[error]="No dispone de permisos";	
		header("Location: index.php?error=true&est=ko#2");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#2");
	exit();
}


if($accion=="entregadocumentacion"){
	$iddocenteweb = $_REQUEST['iddocenteweb'];
	$sql = "UPDATE curso_docente_web SET fecharevisiondocumentacion='NOW()', revisiondocumentacion='1' WHERE id='$iddocenteweb'";
	posgre_query($sql);
	

}

if($accion=="cancelarentregadocumentacion"){
	$iddocenteweb = $_REQUEST['iddocenteweb'];
	$sql = "UPDATE curso_docente_web SET revisiondocumentacion='0' WHERE id='$iddocenteweb'";
	posgre_query($sql);
}



if($accion=="asignarweb"){
	$_SESSION[error]="No asignado";
	$idusuario=strip_tags($_GET['idusuario']); 
	if ($idusuario<>''){
		$link=iConectarse(); 
		$Query = pg_query($link,"INSERT INTO curso_docente_web (idcurso,idusuario) VALUES ('$idcurso','$idusuario');" );// or die (mysql_error()); 
		$_SESSION[error]="Asignado";
	}
	header("Location: asignar_docente_web.php?idcurso=$idcurso"); 
	exit();
}elseif($accion=='desasignarweb'){
	$_SESSION[error]="No eliminado";
	$id=strip_tags($_GET['id']); 
	if ($id<>''){
		$link=iConectarse(); 
		$Query = pg_query($link,"UPDATE curso_docente_web SET borrado=1 WHERE id='$id';" );// or die (mysql_error()); 
		$_SESSION[error]="eliminado";
	}
	header("Location: asignar_docente_web.php?idcurso=$idcurso"); 
	exit();
}



$titulo1="formación ";
$titulo2="administración";
include("plantillaweb01admin.php"); 
$texto=strip_tags($_POST['texto']);
//Datos del curso y el profe asignado
$link=iConectarse(); 
$result=pg_query($link,"SELECT * FROM curso WHERE id='$idcurso' ORDER BY id DESC") ;//or die (pg_error());  
$row = pg_fetch_array($result);
//$iddocente=$row["iddocente"];
$nombrecurso=$row["nombre"];
?>
<!--Arriba plantilla1-->

	<script>
	$(document).ready(function() {
		
		$('.checkboxdocumentacion').change(function() {
			if($(this).is(":checked")) {
				
				
				var returnVal = confirm("Confirmar entrega documentación?");
				
				if (returnVal) {
					var iddocenteweb = $(this).attr("name");
					window.location.href="asignar_docente_web.php?iddocenteweb="+iddocenteweb+"&accion=entregadocumentacion&idcurso="+<?=$idcurso?>;
				}

			}			
			else{
				var returnVal = confirm("Cancelar confirmación entrega documentación?");
			
				if (returnVal) {
					var iddocenteweb = $(this).attr("name");
					window.location.href="asignar_docente_web.php?iddocenteweb="+iddocenteweb+"&accion=cancelarentregadocumentacion&idcurso="+<?=$idcurso?>;
				}
			}
		});
	});
	</script>

	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Profesores/Docentes mostrados en ficha del curso </h2>
	<br />
	<div class="bloque-lateral acciones">		
		<p><strong>Acciones:</strong>
			<a href="curso_alta.php?accion=editar&id=<?=$idcurso?>" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> 
		</p>
	</div>
	<!--fin acciones-->
		<? include("_aya_mensaje_session.php"); 
		$_SESSION[error]="";?>
		<div class="bloque-lateral acciones">		
   	 				<p style="color:#000000">
   	 					<span>Curso <strong>"<?=$nombrecurso;?>"</strong></span>
   	 				</p>
		</div>
		<table class="align-center">
		<tr>
			<th>NOMBRE</th>
			<th>REVISIÓN DOCUMENTACIÓN POR DIRECCIÓN</th>
			<th>ACCIÓN</th>	
		</tr>
		<?
		$link=iConectarse(); 
		$consulta = "SELECT c.id AS id,u.nombre AS nombre, u.apellidos AS apellidos, u.id as idprofesor,c.revisiondocumentacion, c.fecharevisiondocumentacion FROM curso_docente_web AS c,usuario AS u WHERE c.idusuario=u.id AND c.idcurso='$idcurso' AND c.borrado = 0 AND u.borrado = 0 ORDER BY u.apellidos;";
		$result=pg_query($link,$consulta) ;//or die (pg_error());  
		while($row = pg_fetch_array($result)) { 
		
			$checked = "";
			$divfecha = "";
			if ($row['revisiondocumentacion']==1){
				$checked= " checked ";
				$divfecha = "<span style='font-size:10px;'>Aprobado por dirección</span><br>".cambiaf_a_normal($row['fecharevisiondocumentacion']);
			}
			?>
			<tr>
				<td><?=$row["nombre"]." ". $row['apellidos']?></td>
				<td><input <?=$checked?> class="checkboxdocumentacion" name="<?=$row['id']?>" value="" type="checkbox"><?=$divfecha?></td>
				<td>	
				
					<a href="sc_noconformidad.php?idcurso=<?=$idcurso?>&idprofesor=<?=$row["idprofesor"]?>" class="btn btn-primary">Crear no conformidad</a>
					<a href="asignar_docente_web.php?accion=desasignarweb&idcurso=<?=$idcurso?>&id=<?=$row["id"]?>" class="btn btn-primary">Eliminar asignación</a>
				</td>
			</tr>
			<?
		}?>
		</table>
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<div class="bloque-lateral buscador">		
			<h4>Asignar Profesores/Docentes <?=$textobuscar?></h4>
			<form action="asignar_docente_web.php?accion=buscar&idcurso=<?=$idcurso?>" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				(nombre, apellidos) <input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="búsqueda" />
    					<input class="btn" type="submit" value="Buscar profesores/docentes" />
    				</div>		
			    </fieldset>
		    </form>
		</div>
		<!--fin buscador-->
		<!--fin acciones-->
		<table class="align-center">
		<tr>
			<th>NOMBRE</th>
			<th>ORGANISMO</th>
			<th>ESTADO</th>	
			<th>ACCIÓN</th>	
		</tr>
	<? 

//Filtros de areas, etiquetas...
$ide_p=strip_tags($_GET['ide_p']); //Etiqueta Provincia
if ($ide_p<>""){
	$sql=" (idetiqueta_provincia='$ide_p') AND ";
	$textoh1="Filtro de etiqueta Provincia";
}

//Paginacion 1
$pagina=strip_tags($_GET['pagina']);
$registros =30;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
} 
$link=iConectarse();
if (($accion=="buscar")&&($texto<>"")){
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND borrado=0 AND (sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%') OR sp_asciipp(apellidos) ILIKE sp_asciipp('%$texto%')) ORDER BY nombre DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND  borrado=0 AND (sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%') OR sp_asciipp(apellidos) ILIKE sp_asciipp('%$texto%')) ORDER BY nombre DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		 
}else{
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND $sql borrado=0 ORDER BY nombre DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND $sql borrado=0 ORDER BY nombre DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
	$total_paginas = ceil($total_registros / $registros); 		
}	
$cuantos = $total_registros;
//fin Paginacion 1
	while($row = pg_fetch_array($result)) { 
		?>
		<tr>
			<td><?=$row["nombre"]." ". $row['apellidos']?></td>
			<td><?
				// Genera
				$idcolegio=$row["idcolegio"];
					$consulta = "SELECT * FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;";
					$link=iConectarse(); 
					$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
					if($rowdg= pg_fetch_array($r_datos)) {	
						echo $rowdg['nombre'];
					}else{
						echo "No asignado";
					}
					?></td>
			<? 
				if ($row["confirmado"]==1){
					?><td><i class="icon-ok-sign" title="Activo">&nbsp;</i></td>
					<td>
						<a href="asignar_docente_web.php?accion=asignarweb&idusuario=<?=$row["id"];?>&idcurso=<?=$idcurso?>" class="btn btn-primary">Asignar</a>
					</td>					
					<?
				}else{
					?><td><i class="icon-ban-circle" title="Deshabilitado">&nbsp;</i></td>
					<td>&nbsp;
					</td>
					<?
				}				
				?>
		</tr>
		<?
	}?>
		</table>

    <p class="align-center">Total <?=$textobuscar?>: <?=$total_registros?> profesores/docentes</p>
			<?
			if($total_paginas > 1) { ?>
				<div class="pagination">
					<ul>
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="asignar_docente_web.php?pagina=<?=(1)?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="asignar_docente_web.php?pagina=<?=($pagina-1)?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="asignar_docente_web.php?pagina=<?=$i?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="asignar_docente_web.php?pagina=<?=($pagina+1)?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="asignar_docente_web.php?pagina=<?=$total_paginas?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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