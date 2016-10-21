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

$est=($_REQUEST['est']);
$idcurso=($_REQUEST['idcurso']);
if (($idcurso<>'')){
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;");// or die (pg_error());
	if ($result) {
		$row = pg_fetch_array($result);
		$nombre=$row["nombre"];
		$imagen=$row["imagen"];
	}else{
		$_SESSION[error]="Error en curso 2-1";	
		header("Location: index.php?salir=true&est=ko"); 
		exit();
	}
}else{
	$_SESSION[error]="Error en curso 2-2";	
	header("Location: index.php?salir=true&est=ko"); 
	exit();
}


$accion=strip_tags($_GET['accion']);
if($accion=='borrar'){
	$id=strip_tags($_GET['id']); 			//optativos pero obligatorio para eliminar archivos
	$_SESSION[esterror]="No se ha eliminado";
	if (($id<>"")){ // Parametro optativo
		$link=iConectarse();
		$Query = pg_query($link,"UPDATE curso_horario SET borrado=1 WHERE id='$id' AND idcurso='$idcurso';");
		$_SESSION[esterror]="Se ha eliminado";
	}		
	header("Location: curso_horarios.php?idcurso=$idcurso"); 
	exit();
}
function dameFecha($fecha,$dia)
{   list($day,$mon,$year) = explode('/',$fecha);
    return date('d/m/Y',mktime(0,0,0,$mon,$day+$dia,$year));       
}
if($accion=='inserta'){
	$link=iConectarse(); 
		$dias = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado',);
		$fecha_formu=$_POST['fi'];
		$fecha_formu2=$_POST['ff'];
		if ($fecha_formu==""){
			echo "Parametros de entrada incorrectos ff";
			exit();
		}
		
		if ($fecha_formu2==""){
			$fecha_formu2=$fecha_formu;
		}
		
		$f2_f1=compara_fechas($fecha_formu2,$fecha_formu);
		/*echo $f2_f1; exit();
		if ($f2_f1==0){
			$fe=cambiaf_a_mysql($fecha_formu);
		    $Query =pg_query($link,"INSERT INTO curso_horario (hora,fecha,idcurso,idusuario) VALUES ('$hora','$fe','$idcurso','$idcolegio')");// or die ("2".mysql_error()); 
		}else{*/
		
			while ($f2_f1>=0) {// Mientras fecha2>fecha1 inserta en agenda y suma 7 a fecha1
			
			
			
				$hora=0;
				$fea=cambiaf_a_mysql($fecha_formu);
				$dia = date('w', strtotime($fea)); //saca dia lunes,martes...
				if ($dia==1){
					$hora=$_POST['h1'];
					$horafin=$_POST['h1fin']; 
				}
				if ($dia==2){
					$hora=$_POST['h2'];
					$horafin=$_POST['h2fin'];
				}
				if ($dia==3){
					$hora=$_POST['h3'];
					$horafin=$_POST['h3fin'];
				}
				if ($dia==4){
					$hora=$_POST['h4'];
					$horafin=$_POST['h4fin'];
				}
				if ($dia==5){
					$hora=$_POST['h5'];
					$horafin=$_POST['h5fin'];
				}
				if ($dia==6){
					$hora=$_POST['h6'];
					$horafin=$_POST['h6fin'];
				}
				if ($dia==0){
					$hora=$_POST['h7'];
					$horafin=$_POST['h7fin'];
				}
				
				if ($hora<>""){
					$sqli="INSERT INTO curso_horario (hora,horafin,fecha,idcurso,idusuario,diasemana) VALUES ('$hora','$horafin','$fea','$idcurso','$idcolegio','$dia')";		
					$Query =pg_query($link,$sqli);
				}
				//Sumamos 7 y ponemos la fecha en formato correcto
				
				
				$fecha_formu=dameFecha($fecha_formu,1);//suma 1 dia				
				$f2_f1=compara_fechas($fecha_formu2,$fecha_formu);		
			}
		//}
	$_SESSION[esterror]="Se ha guardado correctamente.";	
	header("Location: curso_horarios.php?idcurso=$idcurso"); 
	exit();
} // FIN 

$migas = array();
$migas[] = array('zona-privada_admin_cursos_1.php', 'Gestión de Cursos');
$titulo1="curso";
$titulo2="gestión";
include("plantillaweb01admin.php");
?>
<script language="javascript">
	function confirmar ( mensaje ) {
		return confirm( mensaje );
	}
</script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" /> 
<link rel="stylesheet" href="jquery.timepicker.css" /> 
<script src="https://code.jquery.com/jquery-1.9.1.js"></script> 
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="jquery.timepicker.js"></script>

<script> 
$(function() { 
	$("#fecha1").datepicker( { dateFormat: "dd/mm/yy", dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ], dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ], firstDay: 1, gotoCurrent: true, monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Deciembre" ] } ); 
	$("#fecha2").datepicker( { dateFormat: "dd/mm/yy", dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ], dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ], firstDay: 1, gotoCurrent: true, monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Deciembre" ] } ); 
}); 
$(function() {
$('#time1').timepicker({ 'timeFormat': 'H:i' });
$('#time1fin').timepicker({ 'timeFormat': 'H:i' });
$('#time2').timepicker({ 'timeFormat': 'H:i' });
$('#time2fin').timepicker({ 'timeFormat': 'H:i' });
$('#time3').timepicker({ 'timeFormat': 'H:i' });
$('#time3fin').timepicker({ 'timeFormat': 'H:i' });
$('#time4').timepicker({ 'timeFormat': 'H:i' });
$('#time4fin').timepicker({ 'timeFormat': 'H:i' });
$('#time5').timepicker({ 'timeFormat': 'H:i' });
$('#time5fin').timepicker({ 'timeFormat': 'H:i' });
$('#time6').timepicker({ 'timeFormat': 'H:i' });
$('#time6fin').timepicker({ 'timeFormat': 'H:i' });
$('#time7').timepicker({ 'timeFormat': 'H:i' });
$('#time7fin').timepicker({ 'timeFormat': 'H:i' });
}); 
</script> 

<!--Arriba -->
<div class="grid-9 contenido-principal">
<div class="clearfix"></div>
<div class="pagina blog">
	<h2 class="titulonoticia">Horarios y fechas de curso</h2>
	<br />
	<div class="bloque-lateral acciones">		
		<p><strong>Acciones:</strong>
			<a href="curso_alta.php?accion=editar&id=<?=$idcurso?>" class="btn btn-success" type="button">Volver <i class="icon-circle-arrow-left"></i></a> 
		</p>
	</div>
	<!--fin acciones-->
	<? include("_aya_mensaje_session.php"); ?>
	
	<br><p>- Seleccione un rango de fechas(p.e: 1 mes completo) y rellene los días de la semana que se realiza el curso(p.e: Martes de 17:00 a 19:00)<br>
	- Si solo quiere añadir horario de una fecha no complete Fecha Fin y complete el horario en el día correspondiente a esa fecha<br>
	- La Hora Fin es opcional</p>
<form  class="form-horizontal" action="curso_horarios.php?accion=inserta&idcurso=<?=$idcurso?>" enctype="multipart/form-data" method="post">
<div class="row">
	<div class="grid-6 formulario-horario">
		<div class="control-group">
			<label class="control-label" for="fecha1">Fecha Inicio:</label>
			<div class="controls">
			<input type="text"  class="input-small" id="fecha1" name="fi"  placeholder="00/00/0000">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fecha2">Fecha Fin:</label>
			<div class="controls">
			<input type="text" class="input-small" id="fecha2" name="ff"  placeholder="00/00/0000">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="time1">Lunes</label>
			<div class="controls">
			Inicio: <input type="text" class="input-mini" id="time1" name="h1"  placeholder="00:00">
			Fin: <input type="text" class="input-mini" id="time1fin" name="h1fin"  placeholder="00:00">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="time2">Martes</label>
			<div class="controls">
			Inicio: <input type="text"  class="input-mini" id="time2" name="h2"  placeholder="00:00">
			Fin: <input type="text"  class="input-mini" id="time2fin" name="h2fin"  placeholder="00:00">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="time3">Miércoles</label>
			<div class="controls">
			Inicio: <input type="text" class="input-mini" id="time3" name="h3" placeholder="00:00">
			Fin: <input type="text" class="input-mini" id="time3fin" name="h3fin" placeholder="00:00">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="time4">Jueves</label>
			<div class="controls">
			Inicio: <input type="text"  class="input-mini" id="time4" name="h4"  placeholder="00:00">
			Fin: <input type="text"  class="input-mini" id="time4fin" name="h4fin"  placeholder="00:00">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="time5">Viernes</label>
			<div class="controls">
			Inicio: <input type="text" class="input-mini" id="time5" name="h5" placeholder="00:00">
			Fin: <input type="text" class="input-mini" id="time5fin" name="h5fin" placeholder="00:00">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="time6">Sábado:</label>
			<div class="controls">
			Inicio: <input type="text" class="input-mini" id="time6" name="h6" placeholder="00:00">
			Fin: <input type="text" class="input-mini" id="time6fin" name="h6fin" placeholder="00:00">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="time7">Domingo:</label>
			<div class="controls">
			Inicio: <input type="text" class="input-mini" id="time7" name="h7" placeholder="00:00">
			Fin: <input type="text" class="input-mini" id="time7fin" name="h7fin" placeholder="00:00">
			</div>
		</div>		
		<hr />
		<div class="control-group">
			<div class="controls">
					<button type="submit" class="btn btn-important">Añadir Horario</button>
			</div>
		</div>
	</div>
	<!--fin grid-6-->
	<div class="grid-6">
		<table class="align-center">
		<tr>
			<th>FECHA</th>
			<th>HORA INICIO</th>
			<th>HORA FIN</th>
			<th>ACCIÓN</th>	
		</tr>
	<h3>Horarios y Fechas:</h3>
	<?
	$link2=iConectarse(); 
	$result2=pg_query($link2,"SELECT * FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0 ORDER BY fecha; ");// or die (mysql_error());  
	if ($result2){
		while($row2= pg_fetch_array($result2)) {								
			?>
		<tr>
			<td><?=cambiaf_a_normal($row2["fecha"])?></td>
			<td><?=$row2["hora"]?></td>
			<td><?=$row2["horafin"]?></td>
			<td>
				<a href="curso_horarios_hora.php?idcurso=<?=$idcurso?>&id=<?=$row2["id"]?>" class="btn btn-primary">editar</a>
				<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_horarios.php?accion=borrar&id=<?=$row2["id"]?>&idcurso=<?=$idcurso?>" class="btn btn-primary">eliminar</a>
			</td>
		</tr>
			<? 
		}
	}?>
		</table>
	</div>
	<!--fin grid-6-->
<div class="clearfix"></div>
</div>
<!--fin row-->
</form>
						
	
							
 </div>
<!--fin pagina blog-->
<div class="clearfix"></div>
</div>
<!--fin grid-8 contenido-principal-->
<? 
include("plantillaweb02admin.php");
?>