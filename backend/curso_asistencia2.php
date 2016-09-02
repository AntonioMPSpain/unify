<?
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
		$_SESSION[esterror]="No dispone de permisos";	
		header("Location: index.php?salir=true"); 
		exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql=" (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[esterror]="No dispone de permisos";	
		header("Location: index.php?salir=true"); 
		exit();
	}
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[esterror]="No dispone de permisos";	
		header("Location: index.php?salir=true"); 
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
		$_SESSION[esterror]="No dispone de permisos";	
		header("Location: index.php?salir=true"); 
		exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////
include("_funciones.php"); 
include("_cone.php"); 
$safe="Usuarios en Cursos";


if (isset($_GET['cursodual'])){
	$getcursodual="&cursodual";
}


////////////Datos del curso ///////////////////////
$idcurso=strip_tags($_REQUEST['idcurso']); 
if (($idcurso<>'')){
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;");// or die (pg_error());
	if ($result) {
		$row = pg_fetch_array($result);
		$nombrecurso=$row["nombre"];
		$modalidad = $row["modalidad"];
		$imagen=$row["imagen"];
	}else{
		$_SESSION[esterror]="Error en curso 2-1";	
		header("Location: index.php?salir=true&est=ko"); 
		exit();
	}
}else{
	$_SESSION[esterror]="Error en curso 2-2";	
	header("Location: index.php?salir=true&est=ko"); 
	exit();
}
////////////Datos de la relacion curso con usuario para sacar el dia y horas ///////////////////////
$idcursohorario=strip_tags($_REQUEST['idcursohorario']); 
if (($idcursohorario<>'')){
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT * FROM curso_horario WHERE id='$idcursohorario' AND borrado=0;");// or die (mysql_error());  
	if ($result2) {
		$row2= pg_fetch_array($result2);						
		$fecha=cambiaf_a_normal($row2["fecha"]);	
		$hora=$row2["hora"];	
		$horafin=$row2["horafin"];	
	}else{
		$_SESSION[esterror]="Error en curso 2-12";	
		header("Location: index.php?salir=true&est=ko"); 
		exit();
	}
}else{
	$_SESSION[esterror]="Error en curso 2-22";	
	header("Location: index.php?salir=true&est=ko"); 
	exit();
}
//acciones en esta pagina
$accion=strip_tags($_REQUEST['accion']);

if ($accion=="guardarm"){

	$numero=$_POST["numero"];
	$count = count($numero);
	//ponemos todos a 0
	$sql="UPDATE curso_horario_asistencia SET estado=0 WHERE idcursohorario='$idcursohorario' ";
	$link=iConectarse();
	$result=pg_query($link,$sql) ;//or die (pg_error());  
	//ahora ponemos a 1 los que queden marcados
	for ($i = 0; $i < $count; $i++) {
		//echo $numero[$i];
		$sql="UPDATE curso_horario_asistencia SET estado=1 WHERE id='$numero[$i]' AND idcursohorario='$idcursohorario' ";
		$result=pg_query($link,$sql) ;//or die (pg_error());  
   }
	$_SESSION[esterror]="Se ha guardado correctamente";
	header("Location: curso_asistencia.php?idcurso=$idcurso$getcursodual"); 
	exit();

}


$texto=strip_tags($_POST['texto']);
$titulo1="curso";
$titulo2="gestión";
include("plantillaweb01admin.php");

?>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=($nombrecurso);?></h2>
		<div class="bloque-lateral acciones">		
			<p>
				<a href="curso_asistencia.php?idcurso=<?=$idcurso.$getcursodual?>" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> 
			</p>
		</div>
		<div class="mensaje">
			<p>
				Fecha: <strong><?=$fecha?></strong><br />
				Inicio: <strong><?=$hora?></strong> h.<br />
				Fin: <strong><?=$horafin?></strong> h.
			</p>
		</div>		
		<br />
					<!--fin acciones-->
<h3>Listado de asistencias</h3>
		<form  class="form-horizontal" action="curso_asistencia2.php?accion=guardarm&idcurso=<?=$idcurso?>&idcursohorario=<?=$idcursohorario.$getcursodual?>" enctype="multipart/form-data" method="post">
		<table border="1" width="98%" style="text-align:center;">
		<tr>
			<th>&nbsp;</th>
			<th>DNI</th>
			<th>NOMBRE</th>
			<th>APELLIDOS</th>
			<th>ASISTENCIA</th>	
		</tr>
	<?
	if ($modalidad==2){ ?>
		
		<tr>
		<td>Presencial</td>
		</tr> 
	<? }	
$campo=strip_tags($_GET['campo']);
if ($campo=="nif"){
	$sqlcampo="nif";
}elseif ($campo=="nombre"){
	$sqlcampo="nombre";
}elseif ($campo=="apellidos"){
	$sqlcampo="apellidos";
}elseif ($campo=="idcolegio"){
	$sqlcampo="idcolegio";
}else{
	$sqlcampo="fecha";
}
$sqlcampo="fechahora";
//echo $sqla="SELECT * FROM curso_usuario AS cu, usuario AS u WHERE cd.estado=0 AND cu.nivel<>'3' AND cu.idcurso='$idcurso' AND $sql cu.borrado=0 ORDER BY  apellidos, nombre, id DESC";
$sqla="SELECT * FROM curso_usuario AS cu, usuario AS u WHERE cu.idusuario=u.id AND cu.nivel='5'  AND cu.estado=0 AND cu.espera=0 AND cu.pagado!=0 AND cu.idcurso='$idcurso' AND cu.borrado=0 ORDER BY cu.inscripciononlinepresencial,u.apellidos, u.nombre, cu.id DESC ";
$link=iConectarse();
$result=pg_query($link,$sqla) ;//or die (pg_error());  
$total_registros = pg_num_rows($result); 
$cuantos = $total_registros;
//fin Paginacion 1
$cplazas=0;
$primero=true;
	while($row = pg_fetch_array($result)) { 
		//comprobamos que esta o inserta
		$idcursoalumno=($row[0]);
		$sqlb="SELECT * FROM curso_horario_asistencia WHERE idcursousuario='$idcursoalumno' AND idcursohorario='$idcursohorario' AND borrado=0 ORDER BY id DESC ";
		$linkb=iConectarse();
		$resultb=pg_query($linkb,$sqlb) ;//or die (pg_error());  
		if (pg_num_rows($resultb)>0){
			$rr=pg_fetch_array($resultb);
			$idcua=$rr["id"];
			$estado=$rr["estado"];
		}else{
			$sqli="INSERT INTO curso_horario_asistencia (idcursohorario,idcursousuario) VALUES ('$idcursohorario','$idcursoalumno')";		
			$Query =pg_query($linkb,$sqli);
			// RETURNING Currval('curso_horario_asistencia_seq');" clave pk
			$resultb=pg_query($linkb,"SELECT id FROM curso_horario_asistencia ORDER BY id DESC;") ; 
			$rr=pg_fetch_array($resultb);
			$idcua=$rr["id"];
			$estado=1;
		}
		
		if ($modalidad==2){
			if (($row['inscripciononlinepresencial']==2) && ($primero)){
				$primero=false;
				$cplazas=0;
				?> <td>On-line</td> <?
			}
		}
		?>
		<tr>
		
			<td><?=++$cplazas?></td>
			<?
			// datos
			$nombre=($row['nombre']); //utf8_decode
			$apellidos=($row['apellidos']) ;
			//echo $rowdg['nif'] ;
			$ncolegiado=$row['ncolegiado'] ;
			$dni=$row['nif'] ;
			if ($ncolegiado=='0' ) $ncolegiado="[no colegiado]";
			if ($ncolegiado=='' ) $ncolegiado="[no colegiado]";
			?>
			<td><?=$dni?></td>
			<td><?=$nombre?></td>
			<td><?=$apellidos?></td>
			<td align="center"><input name="numero[]"  type="checkbox" value="<?=$idcua?>" <? if ($estado==1) {?> checked="checked" <? }?>/></td>
		</tr>
		<?
	}?>
		</table>
		<input type="submit" class="btn btn-important" value="Guardar">
		
	
</form>
    <p class="align-center">Total: <?=$total_registros?> usuarios</p>

	</div>
	<!--fin pagina-->
<? 
include("plantillaweb02admin.php");
?>