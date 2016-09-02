<?
$safe="Mi cuenta";
include("_cone.php");
include("_funciones.php");
$c_directorio_img = "/var/www/web";
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
		$_SESSION[esterror]="No dispone de permisos";	
		header("Location: index.php?salir=true"); 
		exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
		$_SESSION[esterror]="No dispone de permisos";	
		header("Location: index.php?salir=true"); 
		exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$ssql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[esterror]="Su usuario de Colegio no está dado de alta en moodle. Debe asignarle permiso el administrador general.";	
		header("Location: index.php?salir=true"); 
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$idcolegio=0;
	$ssql="";
}else{
		$_SESSION[esterror]="No dispone de permisos";	
		header("Location: index.php?salir=true"); 
		exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$idcurso=($_REQUEST['idcurso']);

if (isset($_GET['cursodual'])){
	$getcursodual="&cursodual";
}

$v=$_GET['v'];

if ($v="zpa"){
	$linkvolver = "zpa_usuario_curso.php?idcurso=$idcurso".$getcursodual;
}
else{
	$linkvolver = "zona-privada_admin_cursos_1.php";
}

$est=($_REQUEST['est']);
if (($idcurso<>'')){
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;");// or die (pg_error());
	
	if ($result) {
		$row = pg_fetch_array($result);
		$nombrecurso=$row["nombre"];
		$imagen=$row["imagen"];
		$modalidad=$row["modalidad"];
	}else{
		$_SESSION[error]="Error en curso 2-1";	
		header("Location: index.php?salir=true&est=ko&2"); 
		exit();
	}
}else{
	$_SESSION[error]="Error en curso 2-2";	
	header("Location: index.php?salir=true&est=ko&1"); 
	exit();
}

$titulo1="curso";
$titulo2="gestión";
include("plantillaweb01admin.php");
?>
<script language="javascript">
	function confirmar ( mensaje ) {
		return confirm( mensaje );
	}
</script>

<!--Arriba -->
<div class="grid-9 contenido-principal">
<div class="clearfix"></div>
<div class="pagina blog">
	<div class="bloque-lateral acciones">		
		<p>
			<a href="<?=$linkvolver?>" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> 
		</p>
	</div>
	<h2 class="titulonoticia">Asistencias: <?=$nombrecurso?></h2>
	<br />
	<? include("_aya_mensaje_session.php"); ?>
	<div class="grid-12">
		<table class="align-center">
		<tr>
			<th>&nbsp;</th>
			<th>FECHA</th>
			<th>HORA INICIO</th>
			<th>HORA FIN</th>
			<th>ACCIÓN</th>	
		</tr>
	<h3>Sesiones</h3>
	<?
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT * FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0 ORDER BY fecha;");// or die (mysql_error());  
	$i=1;
	if ($result2){
		while($row2= pg_fetch_array($result2)) {								
			$fecha = cambiaf_a_normal($row2["fecha"]);
			?>
			
		<tr>
			<td><?=$i?></td>
			<td><?=$fecha?></td>
			<td><?=$row2["hora"]?></td>
			<td><?=$row2["horafin"]?></td>
			<td>
				<a href="curso_asistencia2.php?idcurso=<?=$idcurso?>&idcursohorario=<?=$row2["id"].$getcursodual?>" class="btn btn-primary">gestionar asistencias</a>
				
			<? if (($modalidad==2)||($modalidad==1)) { ?>	
				<? /* <!--<a href="#"  onClick="javascript:window.open('informe-usuario_curso_firma.php?idcurso=<?=$row["id"]?>&modo=pdf', 'noimporta', 'width=30, height=30, scrollbars=NO')" class="btn btn-primary">hoja de firmas presencial (pdf)</a>--> */ ?>
				<a class="btn btn-primary" target="_blank" href="informe-usuario_curso_firma.php?ses=<?=$i?>&fec=<?=$fecha?>&idcurso=<?=$row["id"]?>">hoja de firmas presencial</a>
			<? } ?>
			</td>
		</tr>
			<? 
			$i++;
		}
	}?>
		</table>
	</div>
	<!--fin grid-6-->
<div class="clearfix"></div>
						
<h3>Asistencias</h3>
<h4>Presencial</h4>
<br>
<a class="btn btn-primary" href="curso_asistencia_pdf.php?modalidad=<?=$modalidad?>&tipomodalidad=1&idcurso=<?=$idcurso?>">Descargar en PDF</a>
			

		<table border="1" width="98%" style="text-align:center;">
		<tr>
			<th>&nbsp;</th>
			<th>DNI</th>
			<th>NOMBRE</th>
			<th>APELLIDOS</th>
			<th>ASISTENCIA</th>	
			<!--<th>DIPLOMA<br />(>=80%)</th>-->
		</tr>
	<?

if ($modalidad==2){ ?>

<? }	
	
//echo $sqla="SELECT * FROM curso_usuario AS cu, usuario AS u WHERE cd.estado=0 AND cu.nivel<>'3' AND cu.idcurso='$idcurso' AND $sql cu.borrado=0 ORDER BY  apellidos, nombre, id DESC";
$sqla="SELECT * FROM curso_usuario AS cu, usuario AS u WHERE cu.idusuario=u.id AND cu.nivel='5'  AND cu.estado=0 AND cu.espera=0 AND cu.pagado!=0  AND cu.idcurso='$idcurso' AND cu.borrado=0 ORDER BY cu.inscripciononlinepresencial,u.apellidos, u.nombre, cu.id DESC ";
$link=iConectarse();
$result=pg_query($link,$sqla) ;//or die (pg_error());  
$total_registros = pg_num_rows($result); 
$cuantos = $total_registros;
$primero=true;
//fin Paginacion 1
	$cplazas=0;
	while($row = pg_fetch_array($result)) { 
	
		if ($modalidad==2){
			if (($row['inscripciononlinepresencial']==2) && ($primero)){
				$primero=false;
				$cplazas=0;
				?> </tr></table>
				<h4>On-line</h4>
				<br>
				<a class="btn btn-primary" href="curso_asistencia_pdf.php?modalidad=<?=$modalidad?>&tipomodalidad=2&idcurso=<?=$idcurso?>">Descargar en PDF</a>
				<table border="1" width="98%" style="text-align:center;">
				<tr>
					<th>&nbsp;</th>
					<th>DNI</th>
					<th>NOMBRE</th>
					<th>APELLIDOS</th>
					<th>ASISTENCIA</th>	
					<!--<th>DIPLOMA<br />(>=80%)</th>-->
				</tr>
				 <?
			}
		}
		?>
		<tr>
			<td><?=++$cplazas?></td>
			<?
				// Genera
				$idusuario=$row["idusuario"];
				$modalidad=$row["modalidad"];
				$dni=$row["nif"];
						$nombre=($row['nombre']); //utf8_decode
						$apellidos=($row['apellidos']) ;
						//echo $rowdg['nif'] ;
						$idcolegio=$row['idcolegio'];
			?>
			<td><?=$dni?></td>
			<td><?=$nombre?></td>
			<td><?=$apellidos?></td>
			<td align="center">
			<?
			$idcursousuario=($row[0]);
			$linka=iConectarse(); 
			$resulta=pg_query($linka,"SELECT * FROM curso_horario_asistencia WHERE idcursohorario IN (SELECT id FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0) AND estado=0 AND idcursousuario='$idcursousuario' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
			$cuantos1=pg_num_rows($resulta);
		
			$link2=iConectarse(); 
			$result2=pg_query($link2,"SELECT * FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0;");// or die (mysql_error());  
			$cuantos2=pg_num_rows($result2);
			$cuantos=(100-(($cuantos1*100)/$cuantos2));
			$cuantos = number_format($cuantos, 2, '.', '');
			echo $cuantos;
			?>%
			</td>
			<!--<td align="center"><? if ($cuantos>=80){ echo "SI"; }else{ echo "NO"; }?></td>-->
		</tr>
		<?
	}?>
		</table>

    <p class="align-center">Total: <?=$total_registros?> usuarios</p>	
							
 </div>
<!--fin pagina blog-->
<div class="clearfix"></div>
</div>
<!--fin grid-8 contenido-principal-->
<? 
include("plantillaweb02admin.php");
?>