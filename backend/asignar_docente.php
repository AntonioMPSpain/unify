<?
include("_funciones.php"); 
include("_cone.php"); 
require_once('lib_actv_api.php');
$safe="Asignar Profesor";
//Parametros obligatorios
$idcurso=strip_tags($_GET['idcurso']); 
if ($idcurso==""){
	echo "Error en parametros";
	exit();
}
$idmoodle=strip_tags($_GET['idmoodle']); 
if ($idmoodle==""){
	echo "Error en parametros moodle";
	exit();
}

if (($accion=="buscar")&&($texto<>"")){
	$textobuscar="Todos los colegios"; 			
}
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	echo "Error: aqui no deberia entrar.";
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy profe";
	}else{
		echo "Error de sesion";
		exit();
	}
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		echo "Error de sesion";
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
	echo "Error: aqui no deberia entrar.";
	exit();
}

//Acciones
if($accion=="desasigna"){
	$rol=strip_tags($_GET['rol'])+0;
	$idusuario=strip_tags($_GET['idusuario']);
	$idcurso=strip_tags($_GET['idcurso']);
	$idusuariomoodle=strip_tags($_REQUEST['idusuariomoodle']);
	$idcursomoodle=strip_tags($_REQUEST['idmoodle']);
	$enmoodle=0;
	$est="ko";
	//$rol $rol - int (5 estudiante, 3 profesoreditor, 4 profesor normal) rol que se le da al usuario
	if (($idusuariomoodle=="")&&($idcursomoodle=="")&&($idusuario<>"")&&($idcurso<>"")&&(($rol==4) || ($rol==3))){
			header("Location: index.php?est=ko"); 
			exit();
	}
	//desactivar
	$borrado=1;
	$resultado = matricula_usuario_curso($idcursomoodle,$idusuariomoodle, $rol, 0,0,1); //suspende matricula
	if ($resultado==1){
		$_SESSION[esterror]="Asignación suspendida correctamente.";	
		$sqlacc="UPDATE curso_usuario SET borrado='1', rol='0' WHERE nivel='3' AND idcurso='$idcurso' AND idusuario='$idusuario';";
		//Insertamos profe en esa tabla
		//nivel de seguridad 3
		//rol=5 es ESTUDIANTE --> ROLES de moodle
		//$rol $rol - int (5 estudiante, 3 profesoreditor, 4 profesor normal) rol que se le da al usuario
		$link=iConectarse(); 
		$Query = pg_query($link,$sqlacc);  // or die (mysql_error())
		if	($Query) $est="ok";	
	}else{
		$_SESSION[esterror]="Asignación no suspendida.";	
	}
	header("Location: asignar_docente.php?idcurso=$idcurso&idmoodle=$idmoodle"); 
	exit();
}


if($accion=="asigna"){
	$rol=strip_tags($_REQUEST['rol'])+0;
	$idusuario=strip_tags($_REQUEST['idusuario']);
	$idcurso=strip_tags($_REQUEST['idcurso']);
	$idusuariomoodle=strip_tags($_REQUEST['idusuariomoodle']);
	$idcursomoodle=strip_tags($_REQUEST['idmoodle']);
	$enmoodle=1;
	if (($idusuariomoodle=="")&&($idcursomoodle=="")&&($idusuario<>"")&&($idcurso<>"")){
			header("Location: index.php?est=ko"); 
			exit();
	}
	//comprobamos que no esté ya asignado
	$consulta = "SELECT * FROM curso_usuario WHERE nivel='3' AND  idcurso='$idcurso' AND idusuario='$idusuario' AND borrado = 0;";
	$link=iConectarse(); 
	$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
	if(pg_num_rows($r_datos)==0){ //No esta
		//matricular
		$resultado = matricula_usuario_curso($idcursomoodle,$idusuariomoodle,$rol); //matricula estudiante
		if ($resultado==1){
			$_SESSION[esterror]="Asignado en moodle correctamente.";	
			$sqlacc="INSERT INTO curso_usuario (borrado,enmoodle,idusuario,idcurso,idmoodle,nivel,rol) VALUES ('0','$enmoodle','$idusuario','$idcurso','$idmoodle','3','$rol')";
			//Insertamos profe en esa tabla
			//nivel de seguridad 3
			//rol=5 es ESTUDIANTE --> ROLES de moodle
			//$rol $rol - int (5 estudiante, 3 profesoreditor, 4 profesor normal) rol que se le da al usuario
			$link=iConectarse(); 
			$Query = pg_query($link,$sqlacc);  // or die (mysql_error())
			if	($Query) $est="ok";	
		}else{
			$_SESSION[esterror]="No se ha podido asignar en moodle.";	
			$est="ko";
		}
	}else{
		$_SESSION[esterror]="Ya se encuentra asignado a este curso.";	
	}
	header("Location: asignar_docente.php?idcurso=$idcurso&idmoodle=$idmoodle"); 
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
session_start();
$id=$_SESSION[idcolegio];
$idusuariomoodle=get_iduser_moodle($id);

?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Permisos asignados</h2>
	<a href="asignar_docente.php?enmoodle=1&rol=3&idusuariomoodle=<?=$idusuariomoodle?>&accion=asigna&idusuario=<?=$id?>&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" class="btn btn-primary">Asignar Administrador Colegio</a>

		<? include("_aya_mensaje_session.php"); 
		$_SESSION[error]="";?>
		<div class="bloque-lateral acciones">		
   	 				<p style="color:#000000">
   	 					<span>Curso <strong>"<?=$nombrecurso;?>"</strong></span>
   	 				</p>
		</div>
		<table class="align-center">
		<tr>
			<th>ID/IDMOODLE</th>
			<th>NOMBRE</th>
			<th>ROL</th>
			<th>ACCIÓN</th>	
		</tr>
		<?
		$link=iConectarse(); 
		$result=pg_query($link,"SELECT * FROM curso_usuario WHERE borrado='0' AND nivel='3' AND idcurso='$idcurso' ORDER BY id DESC") ;//or die (pg_error());  
		while($row = pg_fetch_array($result)) { 
			$idusuariomoodle=get_iduser_moodle($row["idusuario"]);
			$idusuario=$row["idusuario"];
				$consulta = "SELECT * FROM usuario WHERE id='$idusuario' AND borrado = 0 ORDER BY id;";
				$link=iConectarse(); 
				$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
				$rowdg= pg_fetch_array($r_datos);
			?>
			<tr>
				<td>
				<?=$row["id"]?>/<?=$idusuariomoodle?></td>
				<td><?=$rowdg["nombre"]." ". $rowdg['apellidos']?></td>
				<td><?
					if ($row["rol"]==3){
						?>EDITOR<?
					}elseif ($row["rol"]==4){
						?>NORMAL<?
					}
					elseif ($row["rol"]==9){
						?>COORDINADOR<?
					}else{
						?>Debe reasignar<?
					}				
					?></td>
				<td>
					<a href="asignar_docente.php?enmoodle=0&rol=<?=$row['rol']?>&idusuariomoodle=<?=$idusuariomoodle?>&accion=desasigna&idusuario=<?=$idusuario;?>&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" class="btn btn-primary">Eliminar asignación</a>
				</td>
			</tr>
			<?
		}?>
		</table>
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<div class="bloque-lateral buscador">		
			<h4>Asignar Profesores/docentes <?=$textobuscar?></h4>
			<form action="asignar_docente.php?accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" method="post">
				<fieldset>
		    		<div class="input-append">
   	 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="profesores a buscar" />
    					<input class="btn" type="submit" value="Buscar profesores/docentes" />
    				</div>		
			    </fieldset>
		    </form>
		</div>
		<!--fin buscador-->
		<!--fin acciones-->
		<table class="align-center">
		<tr>
			<th>ID/IDMOODLE</th>
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
$registros =10;
if (!$pagina) { 
	$inicio = 0; 
	$pagina = 1; 
}else{ 
	$inicio = ($pagina - 1) * $registros; 
} 
$link=iConectarse();
if (($accion=="buscar")&&($texto<>"")){
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND borrado=0 AND ( sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')  OR  sp_asciipp(apellidos) ILIKE sp_asciipp('%$texto%') ) ORDER BY nombre DESC, id DESC") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	$result=pg_query($link,"SELECT * FROM usuario WHERE nivel='3' AND  borrado=0 AND ( sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')  OR  sp_asciipp(apellidos) ILIKE sp_asciipp('%$texto%') ) ORDER BY nombre DESC, id DESC OFFSET $inicio LIMIT $registros;") ;//or die (pg_error());  
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
		$idusuariomoodle=get_iduser_moodle($row["id"]);
		?>
		<tr>
			<td>
			<?=$row["id"]?>/<?=$idusuariomoodle?></td>
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
			<td><? 
				if ($row["confirmado"]==1){
					?><i class="icon-ok-sign" title="Activo">&nbsp;</i><?
				}else{
					?><i class="icon-ban-circle" title="Deshabilitado">&nbsp;</i><?
				}				
				?></td>
			<td>
			<? 
			if ($idusuariomoodle>0){ ?>
					<a href="asignar_docente.php?enmoodle=1&rol=3&idusuariomoodle=<?=$idusuariomoodle?>&accion=asigna&idusuario=<?=$row["id"];?>&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" class="btn btn-primary">Asignar Editor</a>
					<a href="asignar_docente.php?enmoodle=1&rol=4&idusuariomoodle=<?=$idusuariomoodle?>&accion=asigna&idusuario=<?=$row["id"];?>&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" class="btn btn-primary">Asignar Normal</a>
					<a href="asignar_docente.php?enmoodle=1&rol=9&idusuariomoodle=<?=$idusuariomoodle?>&accion=asigna&idusuario=<?=$row["id"];?>&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" class="btn btn-primary">Asignar Coordinador</a>
			<? }?>
			</td>
		</tr>
		<?
	}?>
		</table>

    <p class="align-center">Total <?=$textobuscar?>: <?=$total_registros?> profesores/docentes</p>
			<?
			if($total_paginas > 1) { ?>
				<div class="pagination">
					<ul>
						<li<? if ($pagina==1){?> class="disabled"<? }?>><a href="asignar_docente.php?pagina=<?=(1)?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" title="Ver primeros Resultados">Primeros</a></li><?
						if(($pagina - 1) > 0) { ?>
							<li><a href="asignar_docente.php?pagina=<?=($pagina-1)?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" title="Ver anteriores Resultados">Anteriores</a></li>
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
									<li><a href="asignar_docente.php?pagina=<?=$i?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>"><?=$i?></a></li>
									<?
								}
							}	
						}
						if(($pagina + 1)<=$total_paginas) { ?>
							 <li><a href="asignar_docente.php?pagina=<?=($pagina+1)?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" title="Ver siguientes Resultados">Siguientes</a></li>
							<?
						} ?>
						<li<? if ($pagina==$total_paginas){?> class="disabled"<? }?>><a href="asignar_docente.php?pagina=<?=$total_paginas?>&texto=<?=$texto?>&accion=buscar&idcurso=<?=$idcurso?>&idmoodle=<?=$idmoodle?>" title="Ver &uacute;ltimos resultados">&Uacute;ltimos</a></li>
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