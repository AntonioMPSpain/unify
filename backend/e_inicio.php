<?

include("_funciones.php"); 
include("_cone.php");
$safe="Gestión de Encuestas";

////////// Filtros de nivel por usuario //////////////////////
session_start();

if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$idusuario = $idcolegio;
		$sqlcolegio="";
		$sqlcolegio = " AND idcurso IN (SELECT id FROM curso WHERE idcolegio='$idcolegio') ";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
}
else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario ////////////////////// 


$accion = $_REQUEST['accion'];

if ($accion=="eliminar"){
	$id = $_REQUEST['id'];
	if ($id<>""){
		$sql = "UPDATE encuestas SET borrado=1 WHERE id='$id' AND borrado=0 $sqlcolegio";
		posgre_query($sql);
	}
	
	header("Location: e_inicio.php");
	exit();
}


if ($accion=="cerrar"){
	$id = $_REQUEST['id'];
	if ($id<>""){
		$sql = "UPDATE encuestas SET estado=0 WHERE id='$id' AND borrado=0 $sqlcolegio";
		posgre_query($sql);
	}
	
	header("Location: e_inicio.php");
	exit();
}

if ($accion=="abrir"){
	$id = $_REQUEST['id'];
	if ($id<>""){
		$sql = "UPDATE encuestas SET estado=1 WHERE id='$id' AND borrado=0 $sqlcolegio";
		posgre_query($sql);
	}
	
	header("Location: e_inicio.php");
	exit();
}

$titulo1="gesti&oacute;n";
$titulo2="encuestas";
include("plantillaweb01admin.php");
?>
				
			<h2 class="titulonoticia"><?=$safe?></h2>
			<br>
			<div class="bloque-lateral acciones">		
				<p>
					<a href="e_duplicar.php?idduplicar=1" class="btn btn-success">Generar encuesta (plantilla por defecto) <i class="icon-plus"></i><br><span style="font-size:9px;">Relacionada con un curso obligatoriamente<br>Aparece en "Mis cursos" del usuario<br>Sistema de calidad</span></a>
					
					<a href="e_nueva.php" class="btn btn-success">Nueva encuesta vacía <i class="icon-plus"></i><span style="font-size:9px;"><br>Encuesta global o de un curso<br>Se debe compartir link para acceso</span></a>
			
					<a href="e_informe.php" class="btn btn-success">Informe Sistema de Calidad <i class="icon-book"></i></a>
				</p>
			</div>
			<p class="paginacion" style="margin: 0px;"></p>
			<table class="align-center" border="0" cellpadding="0" cellspacing="0">
				<tbody><tr>
					<th>Nombre</th>
					<th>Fecha creación</th>
					<th>Ref.</th>
					<th>Nombre Curso</th>
					<th>Estado</th>
					<th>Respondida</th>
					<th width='20%'>Acciones</th>
				</tr>
				<? 
				$result=pg_query("SELECT * FROM encuestas WHERE borrado=0 AND id NOT IN(1) $sqlcolegio ORDER BY fechacreacion DESC"); 
				 while($row = pg_fetch_array($result)) { 
				 	$id = $row['id'];
				 	$nombre = $row['nombre'];
					$estado = $row['estado'];
					$idcurso = $row['idcurso'];
					$token = $row['tokenacceso'];
					$fechacreacion = $row['fechacreacion'];
					
					$nombrecurso="";
					$sql = "SELECT * FROM curso WHERE id='$idcurso'";
					$result2 = pg_query($sql);
					if ($row2 = pg_fetch_array($result2)){
						$nombrecurso = $row2['nombre'];
						$id_categoria_moodle = $row2["id_categoria_moodle"];
						$idorganizador=$row2['idcolegio'];
						if ($idorganizador==$idcolegio){
							$cursopropio=1;
						}
						else{
							$cursopropio=0;
						}
					}
					$textoestado="";
					if ($estado==1){
						$textoestado="Abierta";
						$disabledestado=" disabled ";
						$linkeliminar="#";
					}
					else {
						$textoestado="Cerrado";
						$disabledestado="";
						$linkeliminar="e_inicio.php?accion=eliminar&id=".$row["id"];
					}
				?>
				<tr>
					<td><?=$nombre?></td>
					<td><?=cambiaf_a_normal($fechacreacion)?></td>
					<td><? if ($idcurso<>0) { ?><?=$idcurso?>/<?=$id_categoria_moodle?><? } else echo 'Global';?></td>
					<td><?=$nombrecurso?></td>
					<td><?=$textoestado?></td>
					<td>
					
						<?
							$sql3 = "SELECT idusuario FROM encuestas_respuestas WHERE borrado=0 AND idopcion IN (SELECT id FROM encuestas_opciones WHERE idpregunta IN (SELECT id FROM encuestas_preguntas WHERE idencuesta IN (SELECT id FROM encuestas WHERE borrado=0 AND id='$id'))) GROUP BY idusuario";
							$result3 = posgre_query($sql3);
							$numusuarios = pg_num_rows($result3);
							
							$result3=posgre_query("SELECT * FROM curso_usuario WHERE nivel='5' AND idcurso='$idcurso' AND borrado=0 AND estado=0 AND espera=0 AND (precio=0 OR pagado=1) ORDER BY (SELECT id FROM curso WHERE borrado=0 AND curso_usuario.id=curso.id ORDER BY fecha_inicio DESC)") ;
							$numinscritos = pg_num_rows($result3);
						
							if ($idcurso<>0){
								$textorespondido = $numusuarios."/".$numinscritos;
							}
							else{
								$textorespondido = $numusuarios;
							}
						
						?>
						<?=$textorespondido?>
					</td>
					<td>
						
						<a href="encuesta.php?id=<?=$id;?>&t=<?=$token?>" class="btn btn-primary">ver</a>
						<a href="e_resultados.php?id=<?=$row["id"];?>" class="btn btn-primary">resultados</a>
						<br>
						
						<? if (($cursopropio==1)||($_SESSION['nivel']==1)){ ?>
							<a href="e_nueva.php?id=<?=$id;?>" class="btn btn-primary">editar</a>
						<? } ?>
						
						<a href="e_duplicar.php?idduplicar=<?=$id;?>" class="btn btn-primary">duplicar</a>	
						
						<? if (($cursopropio==1)||($_SESSION['nivel']==1)){ 
							 if ($estado==1) { ?>
								<a href="e_inicio.php?accion=cerrar&id=<?=$id;?>" class="btn btn-primary">cerrar</a>
							
							<? }
							else { ?>
								<a href="e_inicio.php?accion=abrir&id=<?=$id;?>" class="btn btn-primary">abrir</a>		
					
							<? } ?>
							
						<? } ?>	
						
						<? if (($cursopropio==1)||($_SESSION['nivel']==1)){  ?>						
							<a <?=$disabledestado?> onclick="return confirm('&iquest;Desea eliminar la encuesta?')" href="<?=$linkeliminar?>" class="btn btn-primary">eliminar</a>		
						
						<? } ?>		

					</td>
				</tr>
				<? }?>
			</tbody>
			</table>
	<br />
<?
include("plantillaweb02admin.php"); 
?>
