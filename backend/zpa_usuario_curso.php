<?
//error_reporting(-1);
/*
    $id_curso $id_curso - int identificador del curso en moodle
    $id_usuario $id_usuario - int identificador del usuario en moodle
    $rol $rol - int (5 estudiante, 3 profesoreditor, 4 profesor normal) rol que se le da al usuario
    $f_inicio $f_inicio - timestamp (opcional) fecha inicio de la matricula/permisos
    $f_fin $f_fin - timestamp (opcional) fecha fin de la matricula/permisos
    $suspendido $suspendido - int opcional (1 suspendido, 0 activo)

*/

require_once('lib_actv_api.php');
include("_funciones.php"); 
include("_cone.php"); 
include_once "a_facturas.php";
include_once "a_curso_plazas_libres.php";
include_once "a_api_emails.php";
include_once ("_conemoo.php");

$safe="Gestión de Usuarios en Cursos";
$accion=strip_tags($_GET['accion']); 
$textobusqueda=strip_tags($_REQUEST['textobusqueda']); 
$est=strip_tags($_GET['est']);
$est_texto=strip_tags($_GET['est_texto']);
$est_texto2=strip_tags($_GET['est_texto2']);
$titulo1="formación ";
$titulo2="administración";
$idcurso=strip_tags($_REQUEST['idcurso']); 
//$modalidad=strip_tags($_REQUEST['modalidad']); 
if ($idcurso=="") { //Alumno
	echo "Error: idc.";
	exit();
}
$linka=iConectarse(); 
$rowcurso=pg_query($linka,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
$curso= pg_fetch_array($rowcurso);
$idcursomoodle=$curso["idmoodle"];
$fecha_fin=$curso["fecha_fin"];
$plazas=$curso["plazas"];
$plazaso=$curso["plazaso"];
$modalidad=$curso["modalidad"];
$plazopermanente=$curso["plazopermanente"];
$plazasperma=$curso["plazasperma"];
if (isset($_GET['cursodual'])){
	$cursodual=1;
	$getcursodual="&cursodual";
	$libres = getPlazasLibresOnline($idcurso);
}
else{

	$cursodual=0;
	$getcursodual="";
	if ($modalidad==2){
		$libres = getPlazasLibresPresencial($idcurso);
	}
	else{
		$libres = getPlazasLibres($idcurso);
	}
}
switch($modalidad) { 
  case "0": $plazas=$plazaso; break; //" on-line ";
  case "1": $plazas; break; //" presencial ";
  case "2": if ($cursodual==1) $plazas=$plazaso; else $plazas; break; // " presencial y on-line ";
  case "3": $plazas=$plazasperma; break; //" permanente ";
}

$linkmoo = conectarmoo();

////////// Filtros de nivel por usuario //////////////////////
//include("_seguridadfiltro.php"); //sale $ssql y $sql
session_start();
if ($_SESSION[nivel]==4) { //Alumno
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
}elseif ($_SESSION[nivel]==3) { //Profe 
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
}elseif ($_SESSION[nivel]==5) { // Directivo
	$idcolegio=strip_tags($_SESSION[idcolegio]);
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
		
		$sql = "SELECT * FROM curso WHERE id='$idcurso' AND idcolegio='$idcolegio'";
		$result = posgre_query($sql);
		if ($row = pg_fetch_array($result)){
			
		}
		else{
			$_SESSION[esterror]="No puede acceder a los inscritos de este curso";	
			header("Location: index.php");
			exit();
		}
		
		
		
	}else{
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$accion=$_GET['accion'];
$est=$_REQUEST['est'];
if($accion=='guardarm'){
	$id=$_REQUEST['id'];
	if ((id=="")){
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
	}
	$link=iConectarse();  
	$Query = pg_query($link,"UPDATE curso_usuario SET borrado='1' WHERE  id='$id';") ;//or die (mysql_error()); 
	$_SESSION[esterror]="Guardado";
	header("Location: zona-privada_admin_usuario_curso.php?idcurso=$idcurso&modalidad=$modalidad"); 
	exit();
}


if (isset($_REQUEST['datos'])){

	$datos = $_REQUEST['datos'];
	if ($datos=="inscritos"){
		if ($modalidad==2){
			if ($cursodual==1){	// Saca modo online
				$sql = "SELECT * FROM usuario WHERE borrado=0 AND id IN (SELECT idusuario FROM curso_usuario WHERE espera=0 AND estado=0 AND (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ORDER BY fechahora)";
		
			}
			else{ // Presencial
				$sql = "SELECT * FROM usuario WHERE borrado=0 AND id IN (SELECT idusuario FROM curso_usuario WHERE espera=0 AND  estado=0 AND (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ORDER BY fechahora)";
			}
		}else{
			$sql = "SELECT * FROM usuario WHERE borrado=0 AND id IN (SELECT idusuario FROM curso_usuario WHERE espera=0 AND estado=0 AND (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ORDER BY fechahora)" ;
		}
	}
	elseif ($datos=="espera"){
		if ($modalidad==2){
			if ($cursodual==1){	// Saca modo online
				$sql = "SELECT * FROM usuario WHERE borrado=0 AND id IN (SELECT idusuario FROM curso_usuario WHERE espera=1 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ORDER BY  fechahora)" ;//or die (pg_error());  
			}
			else{				// Presencial
				$sql = "SELECT * FROM usuario WHERE borrado=0 AND id IN (SELECT idusuario FROM curso_usuario WHERE espera=1 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ORDER BY fechahora)" ;//or die (pg_error());  
			}
		}else{
			$sql = "SELECT * FROM usuario WHERE borrado=0 AND id IN (SELECT idusuario FROM curso_usuario WHERE espera=1 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ORDER BY fechahora )" ;//or die (pg_error());  	
		}
	}
	elseif ($datos=="bajas"){
		if ($modalidad==2){
			if ($cursodual==1){	// Saca modo online
				$sql = "SELECT * FROM usuario WHERE borrado=0 AND id IN (SELECT idusuario FROM curso_usuario WHERE estado<>0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ORDER BY fechahora)" ;//or die (pg_error());  
			}
			else{				// Presencial
				$sql = "SELECT * FROM usuario WHERE borrado=0 AND id IN (SELECT idusuario FROM curso_usuario WHERE estado<>0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ORDER BY fechahora)" ;//or die (pg_error());  
			}
		}else{
			$sql = "SELECT * FROM usuario WHERE borrado=0 AND id IN (SELECT idusuario FROM curso_usuario WHERE estado<>0 AND modalidad<>2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ORDER BY fechahora)" ;//or die (pg_error());  
		}
	}

	require_once dirname(__FILE__) . '/../librerias/PHPExcel/Classes/PHPExcel.php';
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);
			
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', "NIF");
	$objPHPExcel->getActiveSheet()->SetCellValue('B1', "NOMBRE");
	$objPHPExcel->getActiveSheet()->SetCellValue('C1', "APELLIDOS");
	$objPHPExcel->getActiveSheet()->SetCellValue('D1', "EMAIL");
	$objPHPExcel->getActiveSheet()->SetCellValue('E1', "TELÉFONO");
	$objPHPExcel->getActiveSheet()->SetCellValue('F1', "TELÉFONO 2");
	
	$rowCount = 2;
	$result = posgre_query($sql);
	while($row = pg_fetch_array($result)){
	
		$nif = $row['nif'];
		$nombre = $row['nombre'];
		$apellidos = $row['apellidos'];
		$email = $row['email'];
		$telefono = $row['telefono'];
		$telefono2 = $row['telefono2'];
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $nif);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nombre);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $apellidos);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $email);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $telefono);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $telefono2);
		$rowCount++;
		
	}
	
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$nombre = "curso-".$idcurso."-usuarios-".$datos."-".time();
	$objWriter->save('./files/'.$nombre.'.xls');
	
	$path = './files/'.$nombre.'.xls';
	ob_clean();
	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.basename($path).'"');
	header("Content-Transfer-Encoding: binary");
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header("Content-Type: application/force-download");
	header("Content-Type: application/download");
	header("Content-Length: ".filesize($path));
	readfile($path);

}

/*
if($accion=='editamoodle'){
	$idusuariomoodle=strip_tags($_REQUEST['idusuariomoodle']);
	$idcursomoodle=strip_tags($_REQUEST['idcursomoodle']);
	$idusuario=strip_tags($_REQUEST['idusuario']);
	$enmoodle=strip_tags($_REQUEST['enmoodle']);
	if (($idusuariomoodle=="")||($idcursomoodle=="")){
		$_SESSION[esterror]="Su sesion ha expirado";	
		header("Location: index.php?error=true");
		exit();
	}
	if ($enmoodle==1){ //activar
		//comprobar usuario y curso 
		//matricular
		$resultado = matricula_usuario_curso($idcursomoodle,$idusuariomoodle,5); //matricula estudiante
		if ($resultado==1){
			$_SESSION[esterror]="Matriculado en moodle correctamente.";	
		}else{
			$_SESSION[esterror]="No se ha podido matricular en moodle.";	
		}
	}else{ //desactivar
		$enmoodle=0;
		$resultado = matricula_usuario_curso($idcursomoodle,$idusuariomoodle, 5, 0,0,1); //suspende matricula estudiante
		if ($resultado==1){
			$_SESSION[esterror]="Matricula suspendida correctamente.";	
		}else{
			$_SESSION[esterror]="Matricula no suspendida.";	
		}
	}
	
	$ssql="UPDATE curso_usuario SET enmoodle = '$enmoodle' WHERE borrado=0 AND idcurso ='$idcurso' AND idusuario='$idusuario';";	
	$link=iConectarse(); 
	$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
	header("Location: zpa_usuario_curso.php?idcurso=$idcurso&modalidad=$modalidad$getcursodual"); 
	exit();
}
*/

/** Cambiar usuario a pagado o no pagado  **/
if 	((isset($_GET['idcurso']))&&(isset($_GET['idusuario']))&&(isset($_GET['setpago']))){
	
	$iduser =  $_GET['idusuario'];
	$idcur = $_GET['idcurso'];
	
	if ($_GET['setpago']==1){
		$formapago = $_GET['formapago'];
		$pago=1;
		if ($formapago==0){
			//generarFactura($iduser, 1, $idcur, 0, $formapago);
		}
		
				
		if ($modalidad==3){
			$hoy = date();
			$plazopermanente=$plazopermanente+1;
			$fechalimitepermanente = date('Y-m-d', strtotime($hoy. ' + '.$plazopermanente.' days'));
			$fechalimitepermanente = "'".$fechalimitepermanente."'";	
			$result = posgre_query("UPDATE curso_usuario SET fechalimitepermanente=$fechalimitepermanente WHERE idcurso='$idcur' AND idusuario='$iduser' AND borrado='0'");
	
		}
		
		
		enviarEmailPagoTransferenciaValidado($iduser, $idcur);
	
	}
	else{
		$pago=0;
	}
	
	$result = posgre_query("UPDATE curso_usuario SET pagado='$pago' WHERE idcurso='$idcur' AND idusuario='$iduser' AND borrado='0'");
	header ("Location: zpa_usuario_curso.php?idcurso=$idcur$getcursodual");
	exit();
}


/** Cambiar usuario a devuelto o no devuelto  **/
/*
if 	((isset($_GET['idcurso']))&&(isset($_GET['idusuario']))&&(isset($_GET['setdevolucion']))){
	
	$iduser =  $_GET['idusuario'];
	$idcur = $_GET['idcurso'];
	
	if ($_GET['setdevolucion']==1){
		$formapago = $_GET['formapago'];
		$devolucion=1;
		$r = generarFactura($iduser, 1, $idcur, 1);
	}
	else{
		$devolucion=0;
		$invoice = getInvoiceNumber($iduser, 1, $idcur, 1);
		anularFactura($invoice);
	}
	
	$result = posgre_query("UPDATE curso_usuario SET devolucion='$devolucion' WHERE idcurso='$idcur' AND idusuario='$iduser' AND borrado='0'");
	header ("Location: zpa_usuario_curso.php?idcurso=$idcur$getcursodual");
	exit();
}
*/

$textomodalidad="";
if ($modalidad==2){
	if ($cursodual==1){	// Saca modo online
		$textomodalidad=". MODO ONLINE";
	}
	else{				// Presencial
		$textomodalidad=". MODO PRESENCIAL";
	}
}
include("plantillaweb01admin.php"); 
$texto=strip_tags($_POST['texto']);
?>
<script>
$(function() {
	$("form input[type=submit]").click(function() {
		$("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
		var val = $(this).attr("clicked", "true");
	});
});

$(function() {
    $('#form_comunicaciones').submit(function() {
 
		var val = $("input[type=submit][clicked=true]").attr("name");
		var idusuario = $("input[type=submit][clicked=true]").attr("id");
		
		if (val=="cambiarEstado"){
			document.form_comunicaciones.action = "zona-privada_admin_usuario_curso2.php?idcurso=<?=$idcurso.$getcursodual?>";
		}
		else if (val=="cambiarEstadoDiploma"){
			document.form_comunicaciones.action = "zona-privada_admin_usuario_curso2.php?estadoDiploma&idcurso=<?=$idcurso.$getcursodual?>";
		}
		else if (val=="cambiarFinAcceso"){
			document.form_comunicaciones.action = "zona-privada_admin_usuario_curso2.php?cambiarFinAcceso&idcurso=<?=$idcurso.$getcursodual?>";
		}
		else if (val=="generarFacturasDomiciliacion"){
			document.form_comunicaciones.action = "zona-privada_admin_usuario_curso_facturas_domi.php?idcurso=<?=$idcurso.$getcursodual?>";
		}
		else if (val=="botonfile"){
			console.log(val);
			document.form_comunicaciones.enctype= "multipart/form-data";
			document.form_comunicaciones.action = "admin_archivos.php?accionpdf=inserta&resguardo&id="+idusuario+"&idcurso=<?=$idcurso?>&idusuario="+idusuario+"<?=$getcursodual?>";

		}
		else{
			document.form_comunicaciones.action = "z_usuario_curso_comunicacion.php?idcurso=<?=$idcurso.$getcursodual?>";
		}

		return true; // return false to cancel form action
    });
});
</script>
<?
include("_aya_mensaje_session.php"); 
$_SESSION[error]="";

?>
	<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Alumnos [<?=$curso["id"]?> <?=$curso["nombre"];?>]<?=$textomodalidad?></h2>
		<br />
		
	<div class="bloque-lateral buscador">
		<h4>Buscar alumno</h4>
		<form action="zpa_usuario_curso.php?accion=buscar&idcurso=<?=$idcurso.$getcursodual?>" method="post" enctype="multipart/form-data" >
			<fieldset>
				<div class="input-append">(nombre,apellidos, nif o email)
				<input type="text" class="span5" id="terminobusqueda" name="textobusqueda" placeholder="búsqueda" value="<?=$textobusqueda?>" />
					<input class="btn" type="submit" value="Buscar" />
	
				</div>		
			</fieldset>
		</form>
	</div>	
	<br>

	<!--Inicio form para envios masivos, recoge el checkbox de cada usuario para enviarles emails o SMS-->
	<form action="z_usuario_curso_comunicacion.php?idcurso=<?=$idcurso.$getcursodual?>" id="form_comunicaciones" name="form_comunicaciones" method="post">

	<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
	<!--Acciones-->
	<div class="bloque-lateral acciones">  Acciones masivas: 		
		<p>
			<input class="btn btn-success" type="submit" name="envioGenerico" value="Envío Email">
			<input disabled class="btn btn-success" type="submit" name="envioGenerico" value="Envío SMS">
			<a class="btn btn-success" href='curso_asistencia.php?v=zpa&idcurso=<?=$idcurso.$getcursodual?>'>Asistencias</a>
			<input class="btn btn-success" type="submit" name="cambiarEstado" value="Cambiar estado inscripción">
			<input class="btn btn-success" type="submit" name="cambiarEstadoDiploma" value="Cambiar estado diploma">
			<input class="btn btn-success" type="submit" name="cambiarFinAcceso" value="Cambiar fecha fin de acceso">
			
			<!--<input class="btn btn-success" type="submit" name="generarFacturasDomiciliacion" value="Generar facturas domiciliación">-->
		</p>
	</div>
	<!--fin acciones-->
	<? } ?>
		<br />
		<? 
		
		$orden=strip_tags($_GET['orden']);
		if($orden=="DESC"){
			$sqlorden="DESC";
		}else{
			$orden="ASC";
			$sqlorden="";
		}
		
		$sqlcampo="CU.fechahora";
		$campo=strip_tags($_GET['campo']);
		$checkedfinacceso="";
		$stylefinacceso=" style='display:none;' ";
		if ($campo<>""){
			if ($campo=="apellidos"){
				$sqlcampo = "U.apellidos";
			}
			elseif ($campo=="modopago"){
				$sqlcampo = "CU.tipoinscripcion";
			}
			elseif ($campo=="fechafinacceso"){
				
				$stylefinacceso="  ";
				$checkedfinacceso=" checked ";
				$sqlcampo = "CU.fechalimitepermanente";
			}
		}
		
		if ($modalidad==3){
			
			$stylefinacceso="  ";
			$checkedfinacceso=" checked ";
		}
			
		$sqlbusqueda="";
		if (($accion=="buscar")&&($textobusqueda<>"")){
			$texto=strval($textobusqueda);
			$sqlbusqueda=" AND CU.idusuario IN (SELECT id FROM usuario WHERE sp_asciipp(nombre) ILIKE sp_asciipp('%$textobusqueda%')  OR  sp_asciipp(apellidos) ILIKE sp_asciipp('%$textobusqueda%') OR  sp_asciipp(nif) ILIKE sp_asciipp('%$textobusqueda%') OR  sp_asciipp(email) ILIKE sp_asciipp('%$textobusqueda%') )";
		}
		
		?>		

<script> 


	$(document).ready(function(){
		$("#header1").on( "click", function() {
			$('.table_1').toggle(0); //muestro mediante id
		 });
	});
	
	$(document).ready(function(){
		$("#header2").on( "click", function() {
			$('.table_2').toggle(0); //muestro mediante id
		 });
	});
	
	$(document).ready(function(){
		$("#header3").on( "click", function() {
			$('.table_3').toggle(0); //muestro mediante id
		 });
	});
	
	$(document).ready(function(){
		$('#checknombre1').change(function() {
			if($(this).is(":checked")) {
				$(".thnombre1").css("display", "table-cell");
			}
			else{
				$(".thnombre1").css("display", "none");
			}
    });
	});
	
	$(document).ready(function(){
		$('#checkapellidos1').change(function() {
			if($(this).is(":checked")) {
				$(".thapellidos1").css("display", "table-cell");
			}
			else{
				$(".thapellidos1").css("display", "none");
			}
    });
	});
	
	$(document).ready(function(){
		$('#checkemail1').change(function() {
			if($(this).is(":checked")) {
				$(".themail1").css("display", "table-cell");
			}
			else{
				$(".themail1").css("display", "none");
			}
    });
	});	
	
	$(document).ready(function(){
		$('#checkmovil1').change(function() {
			if($(this).is(":checked")) {
				$(".thmovil1").css("display", "table-cell");
			}
			else{
				$(".thmovil1").css("display", "none");
			}
    });
	});	
	
	$(document).ready(function(){
		$('#checkcolegio1').change(function() {
			if($(this).is(":checked")) {
				$(".thcolegio1").css("display", "table-cell");
			}
			else{
				$(".thcolegio1").css("display", "none");
			}
    });
	});

	$(document).ready(function(){
		$('#checktelefono21').change(function() {
			if($(this).is(":checked")) {
				$(".thtelefono21").css("display", "table-cell");
			}
			else{
				$(".thtelefono21").css("display", "none");
			}
    });
	});	

	$(document).ready(function(){
		$('#checkprecio1').change(function() {
			if($(this).is(":checked")) {
				$(".thprecio1").css("display", "table-cell");
			}
			else{
				$(".thprecio1").css("display", "none");
			}
    });
	});	
	
	$(document).ready(function(){
		$('#checkfecha1').change(function() {
			if($(this).is(":checked")) {
				$(".thfecha1").css("display", "table-cell");
			}
			else{
				$(".thfecha1").css("display", "none");
			}
    });
	});	
	
	$(document).ready(function(){
		$('#checkmodopago1').change(function() {
			if($(this).is(":checked")) {
				$(".thmodopago1").css("display", "table-cell");
			}
			else{
				$(".thmodopago1").css("display", "none");
			}
    });
	});	
	
	$(document).ready(function(){
		$('#checkresguardo1').change(function() {
			if($(this).is(":checked")) {
				$(".thresguardo1").css("display", "table-cell");
			}
			else{
				$(".thresguardo1").css("display", "none");
			}
    });
	});	
		
	$(document).ready(function(){
		$('#checkpago1').change(function() {
			if($(this).is(":checked")) {
				$(".thpago1").css("display", "table-cell");
			}
			else{
				$(".thpago1").css("display", "none");
			}
    });
	});
		
	$(document).ready(function(){
		$('#checkasistencia1').change(function() {
			if($(this).is(":checked")) {
				$(".thasistencia1").css("display", "table-cell");
			}
			else{
				$(".thasistencia1").css("display", "none");
			}
    });
	});
		
	$(document).ready(function(){
		$('#checknota1').change(function() {
			if($(this).is(":checked")) {
				$(".thnota1").css("display", "table-cell");
			}
			else{
				$(".thnota1").css("display", "none");
			}
    });
	});
		
	$(document).ready(function(){
		$('#checkdiploma1').change(function() {
			if($(this).is(":checked")) {
				$(".thdiploma1").css("display", "table-cell");
			}
			else{
				$(".thdiploma1").css("display", "none");
			}
    });
	});
	
	$(document).ready(function(){
		$('#checkfinacceso1').change(function() {
			if($(this).is(":checked")) {
				$(".thfechafinacceso1").css("display", "table-cell");
			}
			else{
				$(".thfechafinacceso1").css("display", "none");
			}
    });
	});
</script>	

<br>
<div>
	<input checked type="checkbox" id="checknombre1" name="checknombre1" value="checknombre1">NOMBRE&nbsp;
	<input checked type="checkbox" id="checkapellidos1" name="checkapellidos1" value="checkapellidos1">APELLIDOS&nbsp;
	<input type="checkbox" id="checkemail1" name="checkemail1" value="checkemail1">EMAIL&nbsp;
	<input type="checkbox" id="checkmovil1" name="checkmovil1" value="checkmovil1">MÓVIL&nbsp;
	<input type="checkbox" id="checktelefono21" name="checktelefono21" value="checktelefono21">TELÉFONO 2&nbsp;
	<input checked type="checkbox" id="checkcolegio1" name="checkcolegio1" value="checkcolegio1">COLEGIO&nbsp;
	<input checked type="checkbox" id="checkfecha1" name="checkfecha1" value="checkfecha1">FECHA INSCRIPCIÓN&nbsp;
	<input checked type="checkbox" id="checkprecio1" name="checkprecio1" value="checkprecio1">PRECIO&nbsp;
	<input checked type="checkbox" id="checkmodopago1" name="checkmodopago1" value="checkmodopago1">MODO PAGO&nbsp;
	<input checked type="checkbox" id="checkresguardo1" name="checkresguardo1" value="checkresguardo1">RESGUARDO&nbsp;
	<input checked type="checkbox" id="checkpago1" name="checkpago1" value="checkpago1">PAGO&nbsp;
	<input type="checkbox" id="checkasistencia1" name="checkasistencia1" value="checkasistencia1">ASISTENCIA&nbsp;
	<input type="checkbox" id="checknota1" name="checknota1" value="checknota1">NOTA&nbsp;
	<input type="checkbox" id="checkdiploma1" name="checkdiploma1" value="checkdiploma1">DIPLOMA&nbsp;
	<input <?=$checkedfinacceso?> type="checkbox" id="checkfinacceso1" name="checkfinacceso1" value="checkfinacceso1">FIN ACCESO&nbsp;
	
</div>
<br>	
<h2 id="header1" style="cursor:pointer;">Inscritos [Plazas libres: <?=$libres?>, Plazas totales: <?=$plazas?>]</h2>
		

		<table id="table_1" class="table_1 align-center">
		<tr>
			<th><input type="checkbox" id="chck_1" title="Envios genéricos" onclick="selectAll(1)"></th>
			<th class="thnombre1">NOMBRE</th>
			<th class="thapellidos1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=apellidos&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">APELLIDOS</a></th>
			<th style="display:none;" class="themail1">EMAIL</th>	
			<th style="display:none;" class="thmovil1">MÓVIL</th>	
			<th style="display:none;" class="thtelefono21">TELÉFONO 2</th>	
			<th class="thcolegio1">COLEGIO</th>
			<th class="thfecha1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=fechahora&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA INSCRIPCIÓN</a></th>	
			<th class="thprecio1">PRECIO</th>	
			<th class="thmodopago1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=modopago&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">MODO PAGO</a></th>
			<th class="thresguardo1">RESGUARDO</th>
			<th width='10%' class="thpago1">PAGO</th>		
			<th style="display:none;" class="thasistencia1">ASISTENCIA</th>	
			<th style="display:none;" class="thnota1">NOTA</th>	
			<th style="display:none;" class="thdiploma1">DIPLOMA</th>
			<th <?=$stylefinacceso?> class="thfechafinacceso1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=fechafinacceso&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA FIN ACCESO</a></th>
			<th width='10%'>ACCIÓN</th>	
		</tr>
	<?
	 
$link=iConectarse();
if ($modalidad==2){
	if ($cursodual==1){	// Saca modo online
		$result=pg_query($link,"SELECT *, CU.id as cursousuarioid FROM curso_usuario CU, usuario U WHERE CU.idusuario=U.id AND CU.espera=0 AND CU.estado=0 AND (CU.modalidad=2) AND CU.inscripciononlinepresencial=2 AND CU.nivel<>'3' AND CU.idcurso='$idcurso' AND CU.borrado=0 $sqlbusqueda ORDER BY $sqlcampo $sqlorden, CU.id DESC ") ;//or die (pg_error());  

	}
	else{				// Presencial
		$result=pg_query($link,"SELECT *, CU.id as cursousuarioid FROM curso_usuario CU, usuario U WHERE CU.idusuario=U.id AND CU.espera=0 AND  CU.estado=0 AND (CU.modalidad=2) AND CU.inscripciononlinepresencial=1 AND CU.nivel<>'3' AND CU.idcurso='$idcurso' AND CU.borrado=0 $sqlbusqueda ORDER BY $sqlcampo $sqlorden, CU.id DESC ") ;//or die (pg_error());  
	}
	$plazas=$plazaso;
	$total_registros = pg_num_rows($result); 
}else{
	$sql = "SELECT *, CU.id as cursousuarioid FROM curso_usuario CU, usuario U WHERE CU.idusuario=U.id AND CU.espera=0 AND CU.estado=0 AND (CU.modalidad<>2) AND CU.nivel<>'3' AND CU.idcurso='$idcurso' AND CU.borrado=0 $sqlbusqueda ORDER BY $sqlcampo $sqlorden, CU.id DESC";
	$result=pg_query($link,$sql) ;//or die (pg_error()); 
	$total_registros = pg_num_rows($result); 
}
$cuantos = $total_registros;
//fin Paginacion 1
	$cplazas=0;
	$fechafinacceso="";
	while(($row = pg_fetch_array($result))&&($plazas>=$cplazas)) { 
		$idcursousuario = $row['cursousuarioid'];
		$idusuario=$row["idusuario"];
		$fechafinacceso=($row["fechalimitepermanente"]);
		
		if ($fechafinacceso==""){
			$fechafinacceso = ($fecha_fin);
		}
		
		$hoy = date('Y-m-d');
		$margen = date('Y-m-d', strtotime($fechafinacceso. ' + 30 days'));
		
		if ($hoy>$margen){
			$bgcolorfinacceso = "#FFD2C6";	// rojo
		}
		elseif(($hoy<=$margen)&&($hoy>$fechafinacceso)){
			$bgcolorfinacceso = "#FAFAD2"; // amarillo
		}
		elseif($hoy<=$fechafinacceso){
			$bgcolorfinacceso = "#A8FFAE"; // verde
		}
		
		if (($row["estado"]==0)){
			if ($plazas>$cplazas){
				$cplazas++;
			}else{
				
			}
		}else{ 
			$bgcolor="#FF8080";
		}
		
		if ($row['pagado']==1 || $row['precio']==0){
			$bgcolor="#A8FFAE";
		}
		else{
			$bgcolor="#FFD2C6";
		}
		
		/** DIPLOMA **/
		$diploma = $row['diploma'];
		if ($diploma==1){
			$bgcolordiploma="#A8FFAE";
		}
		else{
			$bgcolordiploma="#FFD2C6";
		}
		/****/
		
		/** ASISTENCIA **/
		$resulta=posgre_query("SELECT * FROM curso_horario_asistencia WHERE idcursohorario IN (SELECT id FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0) AND estado=0 AND idcursousuario='$idcursousuario' AND borrado=0;") ;
		$cuantos1=pg_num_rows($resulta);
		$result2=posgre_query("SELECT * FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0;");// or die (mysql_error());  
		$cuantos2=pg_num_rows($result2);
		$asistencia =(100-(($cuantos1*100)/$cuantos2));
		
		if ($asistencia<80){
			$bgcolorasistencia="#FFD2C6";
		}
		else{
			$bgcolorasistencia="#A8FFAE";
		}
		/****/
		
		/** NOTA **/
		
		$nota = "-";
		$idusuariomoodle = get_iduser_moodle($idusuario);
		$sql = "SELECT finalgrade FROM mdl_grade_grades WHERE finalgrade IS NOT NULL AND userid='$idusuariomoodle' AND itemid IN (SELECT id FROM mdl_grade_items WHERE courseid='$idcursomoodle' AND sortorder='1')";
		$resultm2 = pg_query($linkmoo, $sql);
		if ($rowm2 = pg_fetch_array($resultm2)){
			$nota = number_format($rowm2['finalgrade'],2,'.','');
		}
		
		if ($nota>=50){
			$bgcolornota="#A8FFAE";
		}
		else{
			$bgcolornota="#FFD2C6";
		}
		
		/** **/
		
		/** CARGOS **/
		
		$cargo1 = $row['cargo1'];
		$cargo2 = $row['cargo2'];
		$cargo1color="#FFD2C6";
		$cargo2color="#FFD2C6";
		
		if ($cargo1==1){
			$cargo1color="#A8FFAE";
		}
		if ($cargo2==1){
			$cargo2color="#A8FFAE";
		}
		
		
		/** **/
		
		$tipoinscripcion = $row['tipoinscripcion'];
		$pago="";
		if ($tipoinscripcion==1)
			$pago = "Tarjeta";			
		elseif ($tipoinscripcion==0)
			$pago = "Transferencia";	
		elseif ($tipoinscripcion==2)
			$pago = "Domiciliación";
			
		// Genera
		$consulta = "SELECT * FROM usuario WHERE id='$idusuario' ORDER BY id;";
		$link=iConectarse(); 
		$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
		if($rowdg= pg_fetch_array($r_datos)) {
			$borrado = $rowdg['borrado'];	
			$nombre=$rowdg['nombre'];
			$apellidos=$rowdg['apellidos'] ;
			$email=$rowdg['email'] ;
			$telefono=$rowdg['telefono'] ;
			$telefono2=$rowdg['telefono2'] ;
			$idcolegio=$rowdg['idcolegio'];
			$tipousuario=$rowdg['tipodeusuario'];
			$link2=iConectarse(); 
			$r_datos2=pg_query($link2,"SELECT nombre FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;");// or die (mysql_error());  
			if($rowdg2= pg_fetch_array($r_datos2)) {	
				$organismo=$rowdg2['nombre'];
			}else{
				$organismo="Sin colegiar";
			}
			
		}else{
			echo "No VINCULADO";
			$apellidos="";
			$organismo="";
			
		}?>
		
		<tr>
			<td bgcolor="<?=$bgcolor?>">
  			<input type="checkbox" title="Envios genéricos" name="EM_<?=$row['idusuario']?>" id="EM_<?=$row['idusuario']?>">
			</td>
			<td class="thnombre1" bgcolor="<?=$bgcolor?>"><?=$nombre?></td>
			<td class="thapellidos1" bgcolor="<?=$bgcolor?>"><?=$apellidos?></td>
			<td class="themail1" style="display:none;" bgcolor="<?=$bgcolor?>"><?=$email?></td>
			<td class="thmovil1" style="display:none;" bgcolor="<?=$bgcolor?>"><?=$telefono?></td>
			<td class="thtelefono21" style="display:none;" bgcolor="<?=$bgcolor?>"><?=$telefono2?></td>
			<td class="thcolegio1" bgcolor="<?=$bgcolor?>"><?=$organismo?></td>
			<td class="thfecha1" bgcolor="<?=$bgcolor?>"><?=cambiaf_a_normal($row["fechahora"]);?></td>
			<td class="thprecio1" bgcolor="<?=$bgcolor?>"><?=$row["precio"];?></td>
			<td class="thmodopago1" bgcolor="<?=$bgcolor?>"><?=$pago;?>
			
				<? if ($tipoinscripcion==2){ ?>
					
					<table>
					<tr>
					<td bgcolor="<?=$cargo1color?>">cargo 1</td>
					<td bgcolor="<?=$cargo2color?>">cargo 2</td>
					</tr>
					</table>
					
				<? } ?>
			</td>
			
			
						
			<td class="thresguardo1" bgcolor="<?=$bgcolor?>">
			
			<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
			<?
			
			if ($tipoinscripcion==2){
			
				$relleno2='';
				$sql = "SELECT * FROM usuario WHERE id='$idusuario'";
				$resultfc = posgre_query($sql);
				if ($rowfc = pg_fetch_array($resultfc)){
					$relleno2 = $rowfc['domiciliacionvalida'];
					
					if ($relleno2==1){
						?> <i class="icon-ok-sign" title="Domiciliación validada">&nbsp;</i> &nbsp;<a class="btn btn-primary" href="zona-privada_usuario_domiciliacion.php?idusuario=<?=$idusuario?>&volver=zpa&idcurso=<?=$idcurso.$getcursodual?>">ver domiciliación</a>
							<?
					}
					else{
						?> <i class="icon-ban-circle" title="Domiciliación no validada">&nbsp;</i> <?
						
						$result2 = posgre_query("SELECT id,archivo,nombre FROM archivo WHERE idusuario='$idusuario' AND borrado=0 AND autorizacionbancaria=1 ORDER BY fecha DESC") ;//or die (mysql_error());  

						$disabledboton="";
						if($row2= pg_fetch_array($result2)) {

						}
						else{
							$disabledboton=" disabled ";
						}
						?>
						&nbsp;<a <?=$disabledboton?> class="btn btn-primary" href="zona-privada_usuario_domiciliacion.php?idusuario=<?=$idusuario?>&volver=zpa&idcurso=<?=$idcurso.$getcursodual?>">validar domiciliación</a>
						<?
					}
				}
			}
			elseif ($tipoinscripcion==0){
				
				$result2 = posgre_query("SELECT id,archivo,nombre FROM archivo WHERE idcurso='$idcurso' AND idusuario='$idusuario' AND borrado=0 ORDER BY fecha DESC") ;//or die (mysql_error());  
				
				if($row2= pg_fetch_array($result2)) {								
							if ($row2["archivo"]<>""){
								$nombrear="Resguardo";
								
								?>
								<span class="actions"> <?=ucfirst($nombrear)?> &middot; 
									<a href="descarga.php?documento=<?=$row2["archivo"]?>" ><i class="icon-zoom-in"></i> Ver</a> &middot; 
									<? if ($pagado==0){ ?> <a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_archivos.php?resguardo&accionpdf=borrar&idcurso=<?=$idcurso?>&id=<?=$row2["id"]?>&idpdf=<?=$row2["id"].$getcursodual?>"><i class="icon-trash"></i> Borrar</a> <? } ?>
								</span>								
							<? }
				}
				
				else {
					
					?>
						<input id="userfile<?=$idusuario?>" name="userfile<?=$idusuario?>" type="file" />
						<input id="<?=$idusuario?>" name="botonfile" TYPE="submit" value="Guardar" class="btn btn-primary">
					<?
				 }
			} ?>
			
			<? } ?>
			</td>
			
			
			<td class="thpago1" bgcolor="<?=$bgcolor?>"><? 
			if ($row["pagado"]==1){
				?>
				<i class="icon-ok-sign" title="Pagado">&nbsp;</i><br>
				<a href="zpa_usuario_curso.php?setpago=0&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario.$getcursodual?>"  onclick="return confirm('&iquest;Est&aacute; seguro de cancelar el pago? \n\n')" class="btn btn-primary">Cancelar Pago</a> 
				<?
				
			}elseif ($row["pagado"]==0){
				?>
				<i class="icon-ban-circle" title="No pagado">&nbsp;</i><br>
				
				<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
				<a href="zpa_usuario_curso.php?formapago=<?=$tipoinscripcion?>&setpago=1&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario.$getcursodual?>" onclick="return confirmar('&iquest;Est&aacute; seguro de validar? Compruebe si el precio es correcto, si no cambielo ANTES en editar matricula. Se enviara email indicando al usuario la confirmacion\n\n')"  class="btn btn-primary">Validar</a>
						
				<?
				}
			}				
			?>
			</td>
			<td class="thasistencia1" style="display:none;" bgcolor="<?=$bgcolorasistencia?>"><?=$asistencia?>%</td>
			<td class="thnota1" style="display:none;" bgcolor="<?=$bgcolornota?>"><?=$nota?></td>
			<td class="thdiploma1" style="display:none;" bgcolor="<?=$bgcolordiploma?>">
			<? if ($diploma==1){ ?>
			
				<i class="icon-ok-sign" title="APTO">&nbsp;</i><br>
			<?
			}
			elseif ($diploma==0){
			?>
				<i title="Sin calificar">-</i><br>
			<?
			}
			else{ ?>
				<i class="icon-ban-circle" title="No APTO">&nbsp;</i><br>
			<? } ?>
			</td>
			<td <?=$stylefinacceso?> class="thfechafinacceso1" bgcolor="<?=$bgcolorfinacceso?>"><?=cambiaf_a_normal($fechafinacceso);?></td>
			
			<td bgcolor="<?=$bgcolor?>">
			
			<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
				<? if ($borrado==1) { echo "<b>¡USUARIO DADO DE BAJA EN ACTIVATIE!</b><br><br>"; } ?>
				<a href="zona-privada_admin_usuario_curso2.php?enmoodle=<?=$enmoodle?>&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario?>&idcursomoodle=<?=$idcursomoodle?>&idusuariomoodle=<?=$idusuario?>&modalidad=<?=$modalidad.$getcursodual?>" class="btn btn-primary">Editar matricula</a>
			<? } ?>
			</td>
			
			 
				
		</tr>
		<?
	}
	
	$plazasOcupadas = $cplazas;
	
	?>
	</table>
	
	<? if ((($_SESSION[nivel]==1)||($_SESSION[nivel]==2))&&($total_registros>0)){
	?>
	<a href="zpa_usuario_curso.php?datos=inscritos&idcurso=<?=$idcurso.$getcursodual?>" class="table_1 btn btn-primary">Descargar Excel Datos Personales</a>
	<? }?>
	
	<br>
    <br><p>Total: <?=$total_registros?> usuarios</p>
	
	
	

<h2 id="header2" style="cursor:pointer;">Lista de espera</h2>


		<table id="table_2" class="table_2 align-center">
		<tr>
			<th><input type="checkbox" id="chck_2" title="Envios genéricos" onclick="selectAll(2)"></th>
			<th class="thnombre1">NOMBRE</th>
			<th class="thapellidos1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=apellidos&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">APELLIDOS</a></th>
			<th style="display:none;" class="themail1">EMAIL</th>	
			<th style="display:none;" class="thmovil1">MÓVIL</th>	
			<th style="display:none;" class="thtelefono21">TELÉFONO 2</th>	
			<th class="thcolegio1">COLEGIO</th>
			<th class="thfecha1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=fechahora&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA INSCRIPCIÓN</a></th>	
			<th class="thprecio1">PRECIO</th>	
			<th class="thmodopago1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=modopago&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">MODO PAGO</a></th>
			<th class="thresguardo1">RESGUARDO / DOMICILIACIÓN</th>
			<th width='10%' class="thpago1">PAGO</th>		
			<th style="display:none;" class="thasistencia1">ASISTENCIA</th>	
			<th style="display:none;" class="thnota1">NOTA</th>	
			<th style="display:none;" class="thdiploma1">DIPLOMA</th>
			<th <?=$stylefinacceso?> class="thfechafinacceso1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=fechafinacceso&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA FIN ACCESO</a></th>			
			<th width='10%'>ACCIÓN</th>	
		</tr>



	<?

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
	 
$link=iConectarse();
if ($modalidad==2){
	
	if ($cursodual==1){	// Saca modo online
		$result=pg_query($link,"SELECT * FROM curso_usuario CU WHERE espera=1 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0   $sqlbusqueda ORDER BY  fechahora, $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
	}
	else{				// Presencial
		$result=pg_query($link,"SELECT * FROM curso_usuario CU WHERE espera=1 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0   $sqlbusqueda ORDER BY fechahora, $sqlcampo $sqlorden, id DESC ") ;//or die (pg_error());  
	}
	
	$plazas=$plazaso;
	$total_registros = pg_num_rows($result); 
}else{
	$result=pg_query($link,"SELECT * FROM curso_usuario CU WHERE espera=1 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0   $sqlbusqueda ORDER BY fechahora, $sqlcampo $sqlorden, id DESC ") ;//or die (pg_error());  
	$total_registros = pg_num_rows($result); 
	
}

$cuantos = $total_registros;
//fin Paginacion 1
	$cplazas=0;
	
	$fechafinacceso="";
	while(($row = pg_fetch_array($result))&&($plazas>=$cplazas)) { 
		$idcursousuario = $row['id'];
		$idusuario=$row["idusuario"];
		$fechafinacceso=($row["fechalimitepermanente"]);
		
		if ($fechafinacceso==""){
			$fechafinacceso = ($fecha_fin);
		}
				
		$hoy = date('Y-m-d');
		$margen = date('Y-m-d', strtotime($fechafinacceso. ' + 15 days'));
		
		if ($hoy>$margen){
			$bgcolorfinacceso = "#FFD2C6";	// rojo
		}
		elseif(($hoy<=$margen)&&($hoy>$fechafinacceso)){
			$bgcolorfinacceso = "#FAFAD2"; // amarillo
		}
		elseif($hoy<=$fechafinacceso){
			$bgcolorfinacceso = "#A8FFAE"; // verde
		}
		
		if (($row["estado"]==0)){
			if ($plazas>$cplazas){
				$cplazas++;
			}else{
				
			}
		}else{ 
			$bgcolor="#FF8080";
		}
		
		if ($row['pagado']==1 || $row['precio']==0){
			$bgcolor="#A8FFAE";
		}
		else{
			$bgcolor="#FFD2C6";
		}
		
		/** DIPLOMA **/
		$diploma = $row['diploma'];
		if ($diploma==1){
			$bgcolordiploma="#A8FFAE";
		}
		else{
			$bgcolordiploma="#FFD2C6";
		}
		/****/
		
		/** ASISTENCIA **/
		$resulta=posgre_query("SELECT * FROM curso_horario_asistencia WHERE idcursohorario IN (SELECT id FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0) AND estado=0 AND idcursousuario='$idcursousuario' AND borrado=0;") ;
		$cuantos1=pg_num_rows($resulta);
		$result2=posgre_query("SELECT * FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0;");// or die (mysql_error());  
		$cuantos2=pg_num_rows($result2);
		$asistencia =(100-(($cuantos1*100)/$cuantos2));
		
		if ($asistencia<80){
			$bgcolorasistencia="#FFD2C6";
		}
		else{
			$bgcolorasistencia="#A8FFAE";
		}
		/****/
		
		/** NOTA **/
		
		$nota = "-";
		$idusuariomoodle = get_iduser_moodle($idusuario);
		$sql = "SELECT finalgrade FROM mdl_grade_grades WHERE finalgrade IS NOT NULL AND userid='$idusuariomoodle' AND itemid IN (SELECT id FROM mdl_grade_items WHERE courseid='$idcursomoodle' AND sortorder='1')";
		$resultm2 = pg_query($linkmoo, $sql);
		if ($rowm2 = pg_fetch_array($resultm2)){
			$nota = number_format($rowm2['finalgrade'],2,'.','');
		}
		
		if ($nota>=50){
			$bgcolornota="#A8FFAE";
		}
		else{
			$bgcolornota="#FFD2C6";
		}
		
		/** **/
		
		/** CARGOS **/
		
		$cargo1 = $row['cargo1'];
		$cargo2 = $row['cargo2'];
		$cargo1color="#FFD2C6";
		$cargo2color="#FFD2C6";
		
		if ($cargo1==1){
			$cargo1color="#A8FFAE";
		}
		if ($cargo2==1){
			$cargo2color="#A8FFAE";
		}
		
		
		/** **/
		
		$tipoinscripcion = $row['tipoinscripcion'];
		$pago="";
		if ($tipoinscripcion==1)
			$pago = "Tarjeta";			
		elseif ($tipoinscripcion==0)
			$pago = "Transferencia";	
		elseif ($tipoinscripcion==2)
			$pago = "Domiciliación";
			
		// Genera
		$consulta = "SELECT * FROM usuario WHERE id='$idusuario' ORDER BY id;";
		$link=iConectarse(); 
		$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
		if($rowdg= pg_fetch_array($r_datos)) {
			$borrado = $rowdg['borrado'];	
			$nombre=$rowdg['nombre'];
			$apellidos=$rowdg['apellidos'] ;
			$email=$rowdg['email'] ;
			$telefono=$rowdg['telefono'] ;
			$telefono2=$rowdg['telefono2'] ;
			$idcolegio=$rowdg['idcolegio'];
			$tipousuario=$rowdg['tipodeusuario'];
			$link2=iConectarse(); 
			$r_datos2=pg_query($link2,"SELECT nombre FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;");// or die (mysql_error());  
			if($rowdg2= pg_fetch_array($r_datos2)) {	
				$organismo=$rowdg2['nombre'];
			}else{
				$organismo="Sin colegiar";
			}
			
		}else{
			echo "No VINCULADO";
			$apellidos="";
			$organismo="";
			
		}?>
		
		<tr>
			<td bgcolor="<?=$bgcolor?>">
  			<input type="checkbox" title="Envios genéricos" name="EM_<?=$row['idusuario']?>" id="EM_<?=$row['idusuario']?>">
			</td>
			<td class="thnombre1" bgcolor="<?=$bgcolor?>"><?=$nombre?></td>
			<td class="thapellidos1" bgcolor="<?=$bgcolor?>"><?=$apellidos?></td>
			<td class="themail1" style="display:none;" bgcolor="<?=$bgcolor?>"><?=$email?></td>
			<td class="thmovil1" style="display:none;" bgcolor="<?=$bgcolor?>"><?=$telefono?></td>
			<td class="thtelefono21" style="display:none;" bgcolor="<?=$bgcolor?>"><?=$telefono2?></td>
			<td class="thcolegio1" bgcolor="<?=$bgcolor?>"><?=$organismo?></td>
			<td class="thfecha1" bgcolor="<?=$bgcolor?>"><?=cambiaf_a_normal($row["fechahora"]);?></td>
			<td class="thprecio1" bgcolor="<?=$bgcolor?>"><?=$row["precio"];?></td>
			<td class="thmodopago1" bgcolor="<?=$bgcolor?>"><?=$pago;?>
			
				<? if ($tipoinscripcion==2){ ?>
					
					<table>
					<tr>
					<td bgcolor="<?=$cargo1color?>">cargo 1</td>
					<td bgcolor="<?=$cargo2color?>">cargo 2</td>
					</tr>
					</table>
					
				<? } ?>
			</td>
			
			
						
			<td class="thresguardo1" bgcolor="<?=$bgcolor?>">
			
			<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
			<?
			
			if ($tipoinscripcion==2){
			
				$relleno2='';
				$sql = "SELECT * FROM usuario WHERE id='$idusuario'";
				$resultfc = posgre_query($sql);
				if ($rowfc = pg_fetch_array($resultfc)){
					$relleno2 = $rowfc['domiciliacionvalida'];
					
					if ($relleno2==1){
						?> <i class="icon-ok-sign" title="Domiciliación validada">&nbsp;</i> &nbsp;<a class="btn btn-primary" href="zona-privada_usuario_domiciliacion.php?idusuario=<?=$idusuario?>&volver=zpa&idcurso=<?=$idcurso.$getcursodual?>">ver domiciliación</a>
							<?
					}
					else{
						?> <i class="icon-ban-circle" title="Domiciliación no validada">&nbsp;</i> <?
						
						$result2 = posgre_query("SELECT id,archivo,nombre FROM archivo WHERE idusuario='$idusuario' AND borrado=0 AND autorizacionbancaria=1 ORDER BY fecha DESC") ;//or die (mysql_error());  

						$disabledboton="";
						if($row2= pg_fetch_array($result2)) {

						}
						else{
							$disabledboton=" disabled ";
						}
						?>
						&nbsp;<a <?=$disabledboton?> class="btn btn-primary" href="zona-privada_usuario_domiciliacion.php?idusuario=<?=$idusuario?>&volver=zpa&idcurso=<?=$idcurso.$getcursodual?>">validar domiciliación</a>
						<?
					}
				}
			}
			elseif ($tipoinscripcion==0){
				
				$result2 = posgre_query("SELECT id,archivo,nombre FROM archivo WHERE idcurso='$idcurso' AND idusuario='$idusuario' AND borrado=0 ORDER BY fecha DESC") ;//or die (mysql_error());  
				
				if($row2= pg_fetch_array($result2)) {								
							if ($row2["archivo"]<>""){
								if (trim($row2["nombre"])==""){
									$nombrear="Documento";
								}else{
									$nombrear=$row2["nombre"];
								}
								?>
								<span class="actions"> <?=ucfirst($nombrear)?> &middot; 
									<a href="descarga.php?documento=<?=$row2["archivo"]?>" ><i class="icon-zoom-in"></i> Ver</a> &middot; 
									<? if ($pagado==0){ ?> <a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_archivos.php?resguardo&accionpdf=borrar&idcurso=<?=$idcurso?>&id=<?=$row2["id"]?>&idpdf=<?=$row2["id"].$getcursodual?>"><i class="icon-trash"></i> Borrar</a> <? } ?>
								</span>								
							<? }
				}
				
				else {
					
					?>
						<input id="userfile<?=$idusuario?>" name="userfile<?=$idusuario?>" type="file" />
						<input id="<?=$idusuario?>" name="botonfile" TYPE="submit" value="Guardar" class="btn btn-primary">
					<?
				 }
			} ?>
			
			<? } ?>
			</td>
			
			
			<td class="thpago1" bgcolor="<?=$bgcolor?>"><? 
			if ($row["pagado"]==1){
				?>
				<i class="icon-ok-sign" title="Pagado">&nbsp;</i><br>
				<a href="zpa_usuario_curso.php?setpago=0&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario.$getcursodual?>"  onclick="return confirm('&iquest;Est&aacute; seguro de cancelar el pago? \n\n')" class="btn btn-primary">Cancelar Pago</a> 
				<?
				
			}elseif ($row["pagado"]==0){
				?>
				<i class="icon-ban-circle" title="No pagado">&nbsp;</i><br>
				
				<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
				<a href="zpa_usuario_curso.php?formapago=<?=$tipoinscripcion?>&setpago=1&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario.$getcursodual?>" onclick="return confirmar('&iquest;Est&aacute; seguro de validar? Compruebe si el precio es correcto, si no cambielo ANTES en editar matricula. Se enviara email indicando al usuario la confirmacion\n\n')"  class="btn btn-primary">Validar</a>
						
				<?
				}
			}				
			?>
			</td>
			<td class="thasistencia1" style="display:none;" bgcolor="<?=$bgcolorasistencia?>"><?=$asistencia?>%</td>
			<td class="thnota1" style="display:none;" bgcolor="<?=$bgcolornota?>"><?=$nota?></td>
			<td class="thdiploma1" style="display:none;" bgcolor="<?=$bgcolordiploma?>">
			<? if ($diploma==1){ ?>
			
				<i class="icon-ok-sign" title="APTO">&nbsp;</i><br>
			<?
			}
			elseif ($diploma==0){
			?>
				<i title="Sin calificar">-</i><br>
			<?
			}
			else{ ?>
				<i class="icon-ban-circle" title="No APTO">&nbsp;</i><br>
			<? } ?>
			</td>
			<td <?=$stylefinacceso?> class="thfechafinacceso1" bgcolor="<?=$bgcolorfinacceso?>"><?=cambiaf_a_normal($fechafinacceso);?></td>
			
			<td bgcolor="<?=$bgcolor?>">
			
			<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
				<? if ($borrado==1) { echo "<b>¡USUARIO DADO DE BAJA EN ACTIVATIE!</b><br><br>"; } ?>
				<a href="zona-privada_admin_usuario_curso2.php?enmoodle=<?=$enmoodle?>&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario?>&idcursomoodle=<?=$idcursomoodle?>&idusuariomoodle=<?=$idusuario?>&modalidad=<?=$modalidad.$getcursodual?>" class="btn btn-primary">Editar matricula</a>
			<? } ?>
			</td>
			
			 
				
		</tr>
		<?
	}
	
	$plazasOcupadas = $cplazas;
	
	?>
	</table>
	
	<? if ((($_SESSION[nivel]==1)||($_SESSION[nivel]==2))&&($total_registros>0)){
	?>
	<a href="zpa_usuario_curso.php?datos=espera&idcurso=<?=$idcurso.$getcursodual?>" class="table_2 btn btn-primary">Descargar Excel Datos Personales</a>
	<? }?>
	
	<br>
    <br><p>Total: <?=$total_registros?> usuarios</p>
	

<h2 id="header3" style="cursor:pointer;">Bajas</h2>
		<table id="table_3" class="table_3 align-center">
		<tr>
			<th><input type="checkbox" id="chck_3" title="Envios genéricos" onclick="selectAll(3)"></th>
			<th class="thnombre1">NOMBRE</th>
			<th class="thapellidos1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=apellidos&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">APELLIDOS</a></th>
			<th style="display:none;" class="themail1">EMAIL</th>	
			<th style="display:none;" class="thmovil1">MÓVIL</th>	
			<th style="display:none;" class="thtelefono21">TELÉFONO 2</th>	
			<th class="thcolegio1">COLEGIO</th>
			<th class="thfecha1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=fechahora&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA INSCRIPCIÓN</a></th>
			<th class="thprecio1">PRECIO</th>	
			<th class="thmodopago1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=modopago&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">MODO PAGO</a></th>
			<th class="thresguardo1">RESGUARDO / DOMICILIACIÓN</th>
			<th width='10%' class="thpago1">PAGO</th>		
			<th style="display:none;" class="thasistencia1">ASISTENCIA</th>	
			<th style="display:none;" class="thnota1">NOTA</th>	
			<th style="display:none;" class="thdiploma1">DIPLOMA</th>
			<th <?=$stylefinacceso?> class="thfechafinacceso1"><a href="zpa_usuario_curso.php?idcurso=<?=$idcurso.$getcursodual?>&campo=fechafinacceso&orden=<? if ($orden=="ASC"){ echo "DESC"; }else{ echo "ASC"; }?>">FECHA FIN ACCESO</a></th>		
			<th width='10%'>ACCIÓN</th>	
		</tr>

<? 
$link=iConectarse();
if ($modalidad==2){

	if ($cursodual==1){	// Saca modo online
		$result=pg_query($link,"SELECT * FROM curso_usuario CU WHERE estado<>0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0   $sqlbusqueda ORDER BY fechahora, $sqlcampo $sqlorden, id DESC  ") ;//or die (pg_error());  
	}
	else{				// Presencial
		$result=pg_query($link,"SELECT * FROM curso_usuario CU WHERE estado<>0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0   $sqlbusqueda ORDER BY fechahora, $sqlcampo $sqlorden, id DESC  ") ;//or die (pg_error());  
		echo pg_last_error();
	}
	$plazas=$plazaso;
	$total_registros = pg_num_rows($result); 
}else{
		$result=pg_query($link,"SELECT * FROM curso_usuario CU WHERE estado<>0 AND modalidad<>2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  $sqlbusqueda ORDER BY fechahora, $sqlcampo $sqlorden, id DESC") ;//or die (pg_error());  
		$total_registros = pg_num_rows($result); 
}

$cuantos = $total_registros;
//fin Paginacion 1
	$cplazas=0;
	$fechafinacceso="";
	while(($row = pg_fetch_array($result))&&($plazas>=$cplazas)) { 
		$idcursousuario = $row['id'];
		$idusuario=$row["idusuario"];
		$fechafinacceso=($row["fechalimitepermanente"]);
		
		if ($fechafinacceso==""){
			$fechafinacceso = ($fecha_fin);
		}
		
		$hoy = date('Y-m-d');
		$margen = date('Y-m-d', strtotime($fechafinacceso. ' + 15 days'));
		
		if ($hoy>$margen){
			$bgcolorfinacceso = "#FFD2C6";	// rojo
		}
		elseif(($hoy<=$margen)&&($hoy>$fechafinacceso)){
			$bgcolorfinacceso = "#FAFAD2"; // amarillo
		}
		elseif($hoy<=$fechafinacceso){
			$bgcolorfinacceso = "#A8FFAE"; // verde
		}
		
		
		if (($row["estado"]==0)){
			if ($plazas>$cplazas){
				$cplazas++;
			}else{
				
			}
		}else{ 
			$bgcolor="#FF8080";
		}
		
		if ($row['pagado']==1 || $row['precio']==0){
			$bgcolor="#A8FFAE";
		}
		else{
			$bgcolor="#FFD2C6";
		}
		
		/** DIPLOMA **/
		$diploma = $row['diploma'];
		if ($diploma==1){
			$bgcolordiploma="#A8FFAE";
		}
		else{
			$bgcolordiploma="#FFD2C6";
		}
		/****/
		
		/** ASISTENCIA **/
		$resulta=posgre_query("SELECT * FROM curso_horario_asistencia WHERE idcursohorario IN (SELECT id FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0) AND estado=0 AND idcursousuario='$idcursousuario' AND borrado=0;") ;
		$cuantos1=pg_num_rows($resulta);
		$result2=posgre_query("SELECT * FROM curso_horario WHERE idcurso='$idcurso' AND borrado=0;");// or die (mysql_error());  
		$cuantos2=pg_num_rows($result2);
		$asistencia =(100-(($cuantos1*100)/$cuantos2));
		
		if ($asistencia<80){
			$bgcolorasistencia="#FFD2C6";
		}
		else{
			$bgcolorasistencia="#A8FFAE";
		}
		/****/
		
		/** NOTA **/
		
		$nota = "-";
		$idusuariomoodle = get_iduser_moodle($idusuario);
		$sql = "SELECT finalgrade FROM mdl_grade_grades WHERE finalgrade IS NOT NULL AND userid='$idusuariomoodle' AND itemid IN (SELECT id FROM mdl_grade_items WHERE courseid='$idcursomoodle' AND sortorder='1')";
		$resultm2 = pg_query($linkmoo, $sql);
		if ($rowm2 = pg_fetch_array($resultm2)){
			$nota = number_format($rowm2['finalgrade'],2,'.','');
		}
		
		if ($nota>=50){
			$bgcolornota="#A8FFAE";
		}
		else{
			$bgcolornota="#FFD2C6";
		}
		
		/** **/
		
		/** CARGOS **/
		
		$cargo1 = $row['cargo1'];
		$cargo2 = $row['cargo2'];
		$cargo1color="#FFD2C6";
		$cargo2color="#FFD2C6";
		
		if ($cargo1==1){
			$cargo1color="#A8FFAE";
		}
		if ($cargo2==1){
			$cargo2color="#A8FFAE";
		}
		
		
		/** **/
		
		$tipoinscripcion = $row['tipoinscripcion'];
		$pago="";
		if ($tipoinscripcion==1)
			$pago = "Tarjeta";			
		elseif ($tipoinscripcion==0)
			$pago = "Transferencia";	
		elseif ($tipoinscripcion==2)
			$pago = "Domiciliación";
			
		// Genera
		$consulta = "SELECT * FROM usuario WHERE id='$idusuario' ORDER BY id;";
		$link=iConectarse(); 
		$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
		if($rowdg= pg_fetch_array($r_datos)) {
			$borrado = $rowdg['borrado'];	
			$nombre=$rowdg['nombre'];
			$apellidos=$rowdg['apellidos'] ;
			$email=$rowdg['email'] ;
			$telefono=$rowdg['telefono'] ;
			$telefono2=$rowdg['telefono2'] ;
			$idcolegio=$rowdg['idcolegio'];
			$tipousuario=$rowdg['tipodeusuario'];
			$link2=iConectarse(); 
			$r_datos2=pg_query($link2,"SELECT nombre FROM usuario WHERE id='$idcolegio' AND borrado = 0 ORDER BY id;");// or die (mysql_error());  
			if($rowdg2= pg_fetch_array($r_datos2)) {	
				$organismo=$rowdg2['nombre'];
			}else{
				$organismo="Sin colegiar";
			}
			
		}else{
			echo "No VINCULADO";
			$apellidos="";
			$organismo="";
			
		}?>
		
		<tr>
			<td bgcolor="<?=$bgcolor?>">
  			<input type="checkbox" title="Envios genéricos" name="EM_<?=$row['idusuario']?>" id="EM_<?=$row['idusuario']?>">
			</td>
			<td class="thnombre1" bgcolor="<?=$bgcolor?>"><?=$nombre?></td>
			<td class="thapellidos1" bgcolor="<?=$bgcolor?>"><?=$apellidos?></td>
			<td class="themail1" style="display:none;" bgcolor="<?=$bgcolor?>"><?=$email?></td>
			<td class="thmovil1" style="display:none;" bgcolor="<?=$bgcolor?>"><?=$telefono?></td>
			<td class="thtelefono21" style="display:none;" bgcolor="<?=$bgcolor?>"><?=$telefono2?></td>
			<td class="thcolegio1" bgcolor="<?=$bgcolor?>"><?=$organismo?></td>
			<td class="thfecha1" bgcolor="<?=$bgcolor?>"><?=cambiaf_a_normal($row["fechahora"]);?></td>
			<td class="thprecio1" bgcolor="<?=$bgcolor?>"><?=$row["precio"];?></td>
			<td class="thmodopago1" bgcolor="<?=$bgcolor?>"><?=$pago;?>
			
				<? if ($tipoinscripcion==2){ ?>
					
					<table>
					<tr>
					<td bgcolor="<?=$cargo1color?>">cargo 1</td>
					<td bgcolor="<?=$cargo2color?>">cargo 2</td>
					</tr>
					</table>
					
				<? } ?>
			</td>
			
			
						
			<td class="thresguardo1" bgcolor="<?=$bgcolor?>">
			
			<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
			<?
			
			if ($tipoinscripcion==2){
			
				$relleno2='';
				$sql = "SELECT * FROM usuario WHERE id='$idusuario'";
				$resultfc = posgre_query($sql);
				if ($rowfc = pg_fetch_array($resultfc)){
					$relleno2 = $rowfc['domiciliacionvalida'];
					
					if ($relleno2==1){
						?> <i class="icon-ok-sign" title="Domiciliación validada">&nbsp;</i> &nbsp;<a class="btn btn-primary" href="zona-privada_usuario_domiciliacion.php?idusuario=<?=$idusuario?>&volver=zpa&idcurso=<?=$idcurso.$getcursodual?>">ver domiciliación</a>
							<?
					}
					else{
						?> <i class="icon-ban-circle" title="Domiciliación no validada">&nbsp;</i> <?
						
						$result2 = posgre_query("SELECT id,archivo,nombre FROM archivo WHERE idusuario='$idusuario' AND borrado=0 AND autorizacionbancaria=1 ORDER BY fecha DESC") ;//or die (mysql_error());  

						$disabledboton="";
						if($row2= pg_fetch_array($result2)) {

						}
						else{
							$disabledboton=" disabled ";
						}
						?>
						&nbsp;<a <?=$disabledboton?> class="btn btn-primary" href="zona-privada_usuario_domiciliacion.php?idusuario=<?=$idusuario?>&volver=zpa&idcurso=<?=$idcurso.$getcursodual?>">validar domiciliación</a>
						<?
					}
				}
			}
			elseif ($tipoinscripcion==0){
				
				$result2 = posgre_query("SELECT id,archivo,nombre FROM archivo WHERE idcurso='$idcurso' AND idusuario='$idusuario' AND borrado=0 ORDER BY fecha DESC") ;//or die (mysql_error());  
				
				if($row2= pg_fetch_array($result2)) {								
							if ($row2["archivo"]<>""){
								if (trim($row2["nombre"])==""){
									$nombrear="Documento";
								}else{
									$nombrear=$row2["nombre"];
								}
								?>
								<span class="actions"> <?=ucfirst($nombrear)?> &middot; 
									<a href="descarga.php?documento=<?=$row2["archivo"]?>" ><i class="icon-zoom-in"></i> Ver</a> &middot; 
									<? if ($pagado==0){ ?> <a onclick="return confirmar('&iquest;Eliminar elemento?')" href="admin_archivos.php?resguardo&accionpdf=borrar&idcurso=<?=$idcurso?>&id=<?=$row2["id"]?>&idpdf=<?=$row2["id"].$getcursodual?>"><i class="icon-trash"></i> Borrar</a> <? } ?>
								</span>								
							<? }
				}
				
				else {
					
					?>
						<input id="userfile<?=$idusuario?>" name="userfile<?=$idusuario?>" type="file" />
						<input id="<?=$idusuario?>" name="botonfile" TYPE="submit" value="Guardar" class="btn btn-primary">
					<?
				 }
			} ?>
			
			<? } ?>
			</td>
			
			
			<td class="thpago1" bgcolor="<?=$bgcolor?>"><? 
			if ($row["pagado"]==1){
				?>
				<i class="icon-ok-sign" title="Pagado">&nbsp;</i><br>
				<a href="zpa_usuario_curso.php?setpago=0&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario.$getcursodual?>"  onclick="return confirm('&iquest;Est&aacute; seguro de cancelar el pago? \n\n')" class="btn btn-primary">Cancelar Pago</a> 
				<?
				
			}elseif ($row["pagado"]==0){
				?>
				<i class="icon-ban-circle" title="No pagado">&nbsp;</i><br>
				
				<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
				<a href="zpa_usuario_curso.php?formapago=<?=$tipoinscripcion?>&setpago=1&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario.$getcursodual?>" onclick="return confirmar('&iquest;Est&aacute; seguro de validar? Compruebe si el precio es correcto, si no cambielo ANTES en editar matricula. Se enviara email indicando al usuario la confirmacion\n\n')"  class="btn btn-primary">Validar</a>
						
				<?
				}
			}				
			?>
			</td>
			<td class="thasistencia1" style="display:none;" bgcolor="<?=$bgcolorasistencia?>"><?=$asistencia?>%</td>
			<td class="thnota1" style="display:none;" bgcolor="<?=$bgcolornota?>"><?=$nota?></td>
			<td class="thdiploma1" style="display:none;" bgcolor="<?=$bgcolordiploma?>">
			<? if ($diploma==1){ ?>
			
				<i class="icon-ok-sign" title="APTO">&nbsp;</i><br>
			<?
			}
			elseif ($diploma==0){
			?>
				<i title="Sin calificar">-</i><br>
			<?
			}
			else{ ?>
				<i class="icon-ban-circle" title="No APTO">&nbsp;</i><br>
			<? } ?>
			</td>
			<td <?=$stylefinacceso?> class="thfechafinacceso1" bgcolor="<?=$bgcolorfinacceso?>"><?=cambiaf_a_normal($fechafinacceso);?></td>
			<td bgcolor="<?=$bgcolor?>">
			
			<? if (($_SESSION[nivel]==1) || ($_SESSION[nivel]==2)){ ?>
				<? if ($borrado==1) { echo "<b>¡USUARIO DADO DE BAJA EN ACTIVATIE!</b><br><br>"; } ?>
				<a href="zona-privada_admin_usuario_curso2.php?enmoodle=<?=$enmoodle?>&idcurso=<?=$idcurso?>&idusuario=<?=$idusuario?>&idcursomoodle=<?=$idcursomoodle?>&idusuariomoodle=<?=$idusuario?>&modalidad=<?=$modalidad.$getcursodual?>" class="btn btn-primary">Editar matricula</a>
			<? } ?>
			</td>
			
			 
				
		</tr>
		<?
	}
	
	$plazasOcupadas = $cplazas;
	
	?>
	</table>
	
	<? if ((($_SESSION[nivel]==1)||($_SESSION[nivel]==2))&&($total_registros>0)){
	?>
	<a href="zpa_usuario_curso.php?datos=bajas&idcurso=<?=$idcurso.$getcursodual?>" class="table_3 btn btn-primary">Descargar Excel Datos Personales</a>
	<? }?>
	
	<br>
    <br><p>Total: <?=$total_registros?> usuarios</p>



	</form>


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